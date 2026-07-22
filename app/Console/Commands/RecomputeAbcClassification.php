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
    protected $signature = 'inventory:recompute-abc {--tenant= : Only recompute this tenant ID, instead of every tenant on the platform}';

    protected $description = "Recompute each tenant's product ABC classification from total revenue contribution.";

    public function handle(): int
    {
        if ($tenantId = $this->option('tenant')) {
            $tenant = Tenant::find($tenantId);

            if (! $tenant) {
                $this->error("Tenant #{$tenantId} not found.");

                return self::FAILURE;
            }

            $this->recomputeForTenant($tenant);
            $this->info("Recomputed ABC classes for tenant #{$tenant->id} ({$tenant->name}).");

            return self::SUCCESS;
        }

        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');

            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->recomputeForTenant($tenant);
            $this->info("Recomputed ABC classes for tenant #{$tenant->id} ({$tenant->name}).");
        }

        return self::SUCCESS;
    }

    /**
     * Swaps the bound tenant only for the duration of the recompute, restoring
     * whatever was bound before (rather than unconditionally forgetting it) so
     * this is safe to call for a single tenant from inside an already-tenant-
     * scoped web request, not just from the console's all-tenants loop.
     */
    private function recomputeForTenant(Tenant $tenant): void
    {
        $previousTenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        app()->instance('currentTenant', $tenant);

        try {
            $this->recomputeForCurrentTenant();
        } finally {
            if ($previousTenant !== null) {
                app()->instance('currentTenant', $previousTenant);
            } else {
                app()->forgetInstance('currentTenant');
            }
        }
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
