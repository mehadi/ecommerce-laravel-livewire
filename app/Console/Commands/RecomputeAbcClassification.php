<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Console\Command;

/**
 * Classifies each tenant's products into ABC classes by their share of
 * total historical revenue: A = top contributors up to 80% of cumulative
 * revenue, B = the next 15% (up to 95%), C = the remainder (including
 * never-sold products). A simple, explainable heuristic — not a demand
 * forecast — meant to help prioritize which SKUs deserve closer attention
 * (cycle counting, reorder review) as a catalog grows.
 */
class RecomputeAbcClassification extends Command
{
    protected $signature = 'inventory:recompute-abc';

    protected $description = "Recompute each tenant's product ABC classification from total revenue contribution.";

    public function handle(): int
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');

            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            app()->instance('currentTenant', $tenant);
            $this->recomputeForCurrentTenant();
            $this->info("Recomputed ABC classes for tenant #{$tenant->id} ({$tenant->name}).");
        }

        app()->forgetInstance('currentTenant');

        return self::SUCCESS;
    }

    private function recomputeForCurrentTenant(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        $revenueByProduct = OrderItem::query()
            ->selectRaw('product_id, SUM(subtotal) as revenue')
            ->groupBy('product_id')
            ->pluck('revenue', 'product_id');

        $totalRevenue = (float) $revenueByProduct->sum();

        if ($totalRevenue <= 0) {
            Product::query()->update(['abc_class' => 'C']);

            return;
        }

        $ranked = $products->sortByDesc(fn (Product $product) => (float) ($revenueByProduct[$product->id] ?? 0));

        $cumulativeRevenue = 0;

        foreach ($ranked as $product) {
            $cumulativeRevenue += (float) ($revenueByProduct[$product->id] ?? 0);
            $cumulativePercent = ($cumulativeRevenue / $totalRevenue) * 100;

            $class = match (true) {
                $cumulativePercent <= 80 => 'A',
                $cumulativePercent <= 95 => 'B',
                default => 'C',
            };

            $product->update(['abc_class' => $class]);
        }
    }
}
