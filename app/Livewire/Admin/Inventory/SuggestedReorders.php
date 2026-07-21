<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
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
     */
    protected function flaggedProducts()
    {
        return Product::with(['productAttributes', 'defaultSupplier'])
            ->get()
            ->filter(fn (Product $product) => $product->isLowStock() || $product->getSyncedStock() <= 0);
    }

    public function createPurchaseOrder($supplierId): void
    {
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
