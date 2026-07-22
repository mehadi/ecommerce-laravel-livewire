<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class SuggestedReorders extends Component
{
    public function mount(): void
    {
        Gate::authorize('view inventory');
    }

    /**
     * Low-stock/out-of-stock products grouped by their default supplier
     * (a "No Supplier Assigned" bucket first for anything unassigned), each
     * with a naive suggested reorder quantity (bring stock up to 2x the
     * product's low-stock threshold) — a human-reviewed starting point, not
     * a demand forecast.
     *
     * Filtered at the SQL level against `products.stock` (kept in sync with
     * the sum of variant/warehouse stock by Product::syncPriceAndStock())
     * rather than loading the entire catalog into memory to filter it.
     */
    protected function flaggedProducts()
    {
        $defaultThreshold = (int) Setting::get('low_stock_threshold', '10');

        return Product::with(['productAttributes', 'defaultSupplier'])
            ->where(function ($query) use ($defaultThreshold) {
                $query->where('stock', '<=', 0)
                    ->orWhereRaw('stock > 0 AND stock <= COALESCE(low_stock_threshold, ?)', [$defaultThreshold]);
            })
            ->get();
    }

    public function createPurchaseOrder($supplierId): void
    {
        Gate::authorize('view inventory');

        $products = $this->flaggedProducts()->filter(fn (Product $p) => (string) $p->default_supplier_id === (string) $supplierId);

        session()->flash('prefill_po_supplier_id', $supplierId);
        session()->flash('prefill_po_items', $products->map(fn (Product $product) => [
            'product_id' => $product->id,
            'quantity_ordered' => max(1, ($product->lowStockThreshold() * 2) - $product->getSyncedStock()),
        ])->values()->all());

        $this->redirect(route('admin.purchase-orders.create'));
    }

    public function render()
    {
        $flagged = $this->flaggedProducts();

        $grouped = $flagged->groupBy('default_supplier_id')->map(function ($products, $supplierId) {
            return [
                'supplier' => $supplierId ? $products->first()->defaultSupplier : null,
                'products' => $products,
            ];
        })->sortBy(fn ($group) => $group['supplier']?->name ?? '');

        return view('livewire.admin.inventory.suggested-reorders', [
            'groups' => $grouped,
        ])->layout('components.layouts.app', [
            'title' => __('Suggested Reorders'),
        ]);
    }
}
