<?php

namespace App\Livewire;

use App\Livewire\Concerns\FiltersProductGrid;
use App\Livewire\Concerns\HasShoppingCart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryPage extends Component
{
    use FiltersProductGrid, HasShoppingCart, WithPagination;

    public Category $category;

    public function mount(Category $category): void
    {
        abort_unless($category->is_active, 404);

        $this->category = $category;
    }

    public function clearFilters(): void
    {
        $this->reset(...$this->commonFilterProperties());
        $this->resetPage();
    }

    /**
     * The category plus every descendant category id, so a parent category
     * page also surfaces products filed under its subcategories.
     */
    #[Computed]
    public function categoryIds(): array
    {
        return Cache::remember(Tenancy::cacheKey('category.'.$this->category->id.'.subtree_ids'), 1800, function () {
            $all = Category::where('is_active', true)->get(['id', 'parent_id']);

            $ids = [$this->category->id];
            $queue = [$this->category->id];

            while ($queue) {
                $parentId = array_shift($queue);
                foreach ($all->where('parent_id', $parentId) as $child) {
                    $ids[] = $child->id;
                    $queue[] = $child->id;
                }
            }

            return $ids;
        });
    }

    #[Computed]
    public function subcategories()
    {
        return Cache::remember(Tenancy::cacheKey('category.'.$this->category->id.'.subcategories'), 1800, function () {
            return Category::where('parent_id', $this->category->id)
                ->where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('order')
                ->get();
        });
    }

    #[Computed]
    public function siblingCategories()
    {
        return Cache::remember(Tenancy::cacheKey('category.'.$this->category->id.'.siblings'), 1800, function () {
            if ($this->category->parent_id === null) {
                return collect();
            }

            return Category::where('parent_id', $this->category->parent_id)
                ->where('id', '!=', $this->category->id)
                ->where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('order')
                ->get();
        });
    }

    #[Computed]
    public function totalProductCount(): int
    {
        return Cache::remember(Tenancy::cacheKey('category.'.$this->category->id.'.total_product_count'), 1800, function () {
            return Product::where('is_active', true)
                ->whereIn('category_id', $this->categoryIds)
                ->count();
        });
    }

    #[Computed]
    public function priceBounds(): array
    {
        return Cache::remember(Tenancy::cacheKey('category.'.$this->category->id.'.price_bounds'), 1800, function () {
            $range = Product::where('is_active', true)
                ->whereIn('category_id', $this->categoryIds)
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();

            return [
                'min' => $range && $range->min_price !== null ? (int) floor((float) $range->min_price) : 0,
                'max' => $range && $range->max_price !== null ? (int) ceil((float) $range->max_price) : 0,
            ];
        });
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return ($this->minPrice !== '' ? 1 : 0)
            + ($this->maxPrice !== '' ? 1 : 0)
            + ($this->inStockOnly ? 1 : 0);
    }

    #[Computed]
    public function products()
    {
        $query = Product::where('is_active', true)
            ->whereIn('category_id', $this->categoryIds)
            ->with(['category', 'productAttributes']);

        // Effective price: cheapest active variant if the product has attributes, else the base price.
        $effectivePrice = 'COALESCE((SELECT MIN(pa.price) FROM product_attributes pa WHERE pa.product_id = products.id AND pa.is_active = true), products.price)';

        $this->applyCommonProductFilters($query, $effectivePrice);
        $this->applyProductSort($query);

        return $query->paginate($this->normalizedPerPage())->withQueryString();
    }

    public function render()
    {
        $siteName = Setting::get('site_name', config('app.name'));

        return view('livewire.category-page')
            ->layout('components.layouts.public', [
                'title' => $this->category->name.' - '.$siteName,
                'metaDescription' => Str::limit($this->category->description ?: __('Shop :category at :site.', ['category' => $this->category->name, 'site' => $siteName]), 160),
                'ogImage' => $this->category->image,
                'showNavigation' => true,
                'showFooter' => true,
                'showCookieConsent' => true,
            ]);
    }
}
