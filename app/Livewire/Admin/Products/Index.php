<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $filterCategory = '';

    public $filterStock = '';

    public $filterFeatured = '';

    public $perPage = 15;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $selectedItems = [];

    public $selectAll = false;

    protected $queryString = ['search', 'filterStatus', 'filterCategory', 'filterStock', 'filterFeatured', 'perPage', 'sortField', 'sortDirection'];

    public function mount(): void
    {
        Gate::authorize('view products');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStock(): void
    {
        $this->resetPage();
    }

    public function updatingFilterFeatured(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedItems = $this->getProductsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = false;
    }

    public function toggleStatus($productId): void
    {
        Gate::authorize('edit products');

        $product = Product::findOrFail($productId);
        $product->update(['is_active' => ! $product->is_active]);
        $this->clearCache();
        session()->flash('message', __('Product status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$productId]);
    }

    public function toggleFeatured($productId): void
    {
        Gate::authorize('edit products');

        $product = Product::findOrFail($productId);
        $product->update(['is_featured' => ! $product->is_featured]);
        $this->clearCache();
        session()->flash('message', __('Product featured status updated successfully.'));
        $this->selectedItems = array_diff($this->selectedItems, [$productId]);
    }

    public function bulkToggleStatus(): void
    {
        Gate::authorize('edit products');

        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one product.'));

            return;
        }

        $products = Product::whereIn('id', $this->selectedItems)->get();
        $newStatus = $products->first()->is_active ? false : true;

        foreach ($products as $product) {
            $product->update(['is_active' => $newStatus]);
        }

        $this->clearCache();
        session()->flash('message', __(':count product(s) status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkToggleFeatured(): void
    {
        Gate::authorize('edit products');

        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one product.'));

            return;
        }

        $products = Product::whereIn('id', $this->selectedItems)->get();
        $newStatus = $products->first()->is_featured ? false : true;

        foreach ($products as $product) {
            $product->update(['is_featured' => $newStatus]);
        }

        $this->clearCache();
        session()->flash('message', __(':count product(s) featured status updated successfully.', ['count' => count($this->selectedItems)]));
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        Gate::authorize('delete products');

        if (empty($this->selectedItems)) {
            session()->flash('error', __('Please select at least one product.'));

            return;
        }

        $products = Product::whereIn('id', $this->selectedItems)->get();

        $deletedCount = 0;
        $blockedNames = [];

        foreach ($products as $product) {
            if (! $this->deleteProductRow($product)) {
                $blockedNames[] = $product->name_en;

                continue;
            }

            $deletedCount++;
        }

        $this->clearCache();

        if ($deletedCount > 0) {
            session()->flash('message', __(':count product(s) deleted successfully.', ['count' => $deletedCount]));
        }

        if (! empty($blockedNames)) {
            session()->flash('error', __('Could not delete :names — still referenced by purchase orders, stock transfers, or cycle counts.', ['names' => implode(', ', $blockedNames)]));
        }

        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function deleteProduct($productId): void
    {
        Gate::authorize('delete products');

        $product = Product::findOrFail($productId);

        if (! $this->deleteProductRow($product)) {
            session()->flash('error', __('Could not delete :name — it is still referenced by a purchase order, stock transfer, or cycle count.', ['name' => $product->name_en]));

            return;
        }

        $this->clearCache();
        session()->flash('message', __('Product deleted successfully.'));
    }

    /**
     * Delete a single product row, only removing its stored images once the
     * row is actually gone. Products still referenced by tables that don't
     * cascade (purchase order items, stock transfer items, cycle count
     * items) fail their FK constraint on delete; returning false here lets
     * callers skip those instead of losing images for a product that never
     * actually got deleted.
     */
    protected function deleteProductRow(Product $product): bool
    {
        try {
            $product->delete();
        } catch (QueryException) {
            return false;
        }

        if ($product->primary_image) {
            Storage::disk('public')->delete($product->primary_image);
        }
        if ($product->gallery_images) {
            foreach ($product->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        return true;
    }

    public function delete(Product $product): void
    {
        $this->deleteProduct($product->id);
    }

    public function duplicateProduct($productId): void
    {
        Gate::authorize('create products');

        if (! Tenancy::current()?->canAddProduct()) {
            session()->flash('error', __('Your plan\'s product limit has been reached. Upgrade your plan to add more products.'));

            return;
        }

        Product::findOrFail($productId)->duplicate();

        $this->clearCache();
        session()->flash('message', __('Product duplicated as an inactive draft.'));
    }

    protected function clearCache(): void
    {
        Cache::forget(Tenancy::cacheKey('products.featured'));
    }

    protected function getProductsQuery()
    {
        return Product::query()
            ->with(['category', 'productAttributes'])
            ->when($this->search, function ($query) {
                $query->where('name_en', 'like', '%'.$this->search.'%')
                    ->orWhere('name_bn', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterCategory !== '', function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterStock !== '', function ($query) {
                if ($this->filterStock === 'in_stock') {
                    // For products with attributes, check product attributes stock
                    $query->where(function ($q) {
                        $q->whereHas('productAttributes', function ($subQ) {
                            $subQ->selectRaw('product_id, SUM(stock) as total_stock')
                                ->groupBy('product_id')
                                ->havingRaw('SUM(stock) > 0');
                        })->orWhere(function ($subQ) {
                            $subQ->whereDoesntHave('productAttributes')
                                ->where('stock', '>', 0);
                        });
                    });
                } elseif ($this->filterStock === 'low_stock') {
                    // Threshold is per-product (override) or per-tenant (default); expressed
                    // in SQL via effectiveThresholdExpr() so this stays a set of aggregate
                    // queries instead of hydrating the whole catalog into PHP (see
                    // stockValueQuery() below, shared with render()'s stat cards).
                    $stockExpr = $this->effectiveStockExpr();
                    $thresholdExpr = $this->effectiveThresholdExpr();

                    $lowStockIds = $this->stockValueQuery()
                        ->whereRaw("({$stockExpr}) > 0")
                        ->whereRaw("({$stockExpr}) <= ({$thresholdExpr})")
                        ->pluck('products.id');

                    $query->whereIn('id', $lowStockIds);
                } elseif ($this->filterStock === 'out_of_stock') {
                    $query->where(function ($q) {
                        $q->whereHas('productAttributes', function ($subQ) {
                            $subQ->selectRaw('product_id, SUM(stock) as total_stock')
                                ->groupBy('product_id')
                                ->havingRaw('SUM(stock) <= 0');
                        })->orWhere(function ($subQ) {
                            $subQ->whereDoesntHave('productAttributes')
                                ->where('stock', '<=', 0);
                        });
                    });
                }
            })
            ->when($this->filterFeatured !== '', function ($query) {
                if ($this->filterFeatured === 'featured') {
                    $query->where('is_featured', true);
                } elseif ($this->filterFeatured === 'not_featured') {
                    $query->where('is_featured', false);
                }
            });
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Stock/value stats (total value, low-stock, out-of-stock counts) are computed
        // in SQL via stockValueQuery() rather than hydrating every product + variant
        // into PHP — see that method's docblock for the effective stock/price/threshold
        // logic being mirrored from Product::isLowStock()/getSyncedStock()/getSyncedPrice().
        $stockExpr = $this->effectiveStockExpr();
        $priceExpr = $this->effectivePriceExpr();
        $thresholdExpr = $this->effectiveThresholdExpr();

        $stockValueStats = $this->stockValueQuery()
            ->selectRaw("
                COALESCE(SUM(({$priceExpr}) * ({$stockExpr})), 0) as total_value,
                COALESCE(SUM(CASE WHEN ({$stockExpr}) <= 0 THEN 1 ELSE 0 END), 0) as out_of_stock,
                COALESCE(SUM(CASE WHEN ({$stockExpr}) > 0 AND ({$stockExpr}) <= ({$thresholdExpr}) THEN 1 ELSE 0 END), 0) as low_stock
            ")
            ->first();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'featured' => Product::where('is_featured', true)->count(),
            'low_stock' => (int) ($stockValueStats->low_stock ?? 0),
            'out_of_stock' => (int) ($stockValueStats->out_of_stock ?? 0),
            'total_value' => (float) ($stockValueStats->total_value ?? 0),
        ];

        $categories = Category::orderBy('name_en')->get();

        return view('livewire.admin.products.index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => $categories,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Products'),
        ]);
    }

    /**
     * Base query builder (one row per current-tenant product, left-joined to a
     * per-product aggregate of its variants) that effectiveStockExpr()/
     * effectivePriceExpr()/effectiveThresholdExpr() build SQL expressions
     * against. Shared by the low-stock filter and render()'s stat cards so
     * both compute stock/value the same way without loading every product
     * (and its variants) into PHP.
     *
     * Raw query builder, not Eloquent, so Product's TenantScope global scope
     * doesn't apply automatically — scoped explicitly below instead.
     */
    protected function stockValueQuery(): QueryBuilder
    {
        $variantAgg = DB::table('product_attributes')
            ->select('product_id')
            ->selectRaw('SUM(stock) as variant_stock')
            ->selectRaw('MIN(CASE WHEN is_active THEN price END) as variant_min_price')
            ->selectRaw('COUNT(*) as variant_count')
            ->groupBy('product_id');

        return DB::table('products')
            ->where('products.tenant_id', Tenancy::id())
            ->leftJoinSub($variantAgg, 'variant_agg', 'variant_agg.product_id', '=', 'products.id');
    }

    /**
     * Mirrors Product::getSyncedStock(): sum of every variant's stock
     * (active or not) when the product has any variants, else the product's
     * own stock column.
     */
    protected function effectiveStockExpr(): string
    {
        return 'CASE WHEN variant_agg.variant_count > 0 THEN COALESCE(variant_agg.variant_stock, 0) ELSE products.stock END';
    }

    /**
     * Mirrors Product::getSyncedPrice(): cheapest active variant price when
     * the product has any variants (falling back to the product's own price
     * if none of its variants are active/priced), else the product's own price.
     */
    protected function effectivePriceExpr(): string
    {
        return 'CASE WHEN variant_agg.variant_count > 0 THEN COALESCE(variant_agg.variant_min_price, products.price) ELSE products.price END';
    }

    /**
     * Mirrors Product::lowStockThreshold(): the product's own override if
     * set, else the tenant's configured default. That default is a single
     * scalar for the whole tenant (not per-row), so it's safe to resolve once
     * and inline as a literal int rather than a bound parameter.
     */
    protected function effectiveThresholdExpr(): string
    {
        $defaultThreshold = (int) Setting::get('low_stock_threshold', '10');

        return "COALESCE(products.low_stock_threshold, {$defaultThreshold})";
    }
}
