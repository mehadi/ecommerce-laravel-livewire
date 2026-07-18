<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasShoppingCart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryPage extends Component
{
    use HasShoppingCart, WithPagination;

    /** Allowed values for the "products per page" control. */
    public const PER_PAGE_OPTIONS = [6, 12, 24, 48];

    /** Allowed values for the "grid columns" control. */
    public const COLUMN_OPTIONS = [2, 3, 4];

    public Category $category;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'featured')]
    public string $sort = 'featured';

    #[Url(except: '')]
    public string $minPrice = '';

    #[Url(except: '')]
    public string $maxPrice = '';

    #[Url(except: false)]
    public bool $inStockOnly = false;

    #[Url(except: 6)]
    public int $perPage = 6;

    #[Url(except: 3)]
    public int $columns = 3;

    public bool $showFilters = false;

    public function mount(Category $category): void
    {
        abort_unless($category->is_active, 404);

        $this->category = $category;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatedInStockOnly(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(int $value): void
    {
        if (! in_array($value, self::PER_PAGE_OPTIONS, true)) {
            $this->perPage = 6;
        }

        $this->resetPage();
    }

    public function updatedColumns(int $value): void
    {
        if (! in_array($value, self::COLUMN_OPTIONS, true)) {
            $this->columns = 3;
        }
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'sort', 'minPrice', 'maxPrice', 'inStockOnly');
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
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
        return Product::where('is_active', true)
            ->whereIn('category_id', $this->categoryIds)
            ->count();
    }

    #[Computed]
    public function priceBounds(): array
    {
        $range = Product::where('is_active', true)
            ->whereIn('category_id', $this->categoryIds)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min' => $range && $range->min_price !== null ? (int) floor((float) $range->min_price) : 0,
            'max' => $range && $range->max_price !== null ? (int) ceil((float) $range->max_price) : 0,
        ];
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

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name_en', 'ilike', '%'.$this->search.'%')
                    ->orWhere('name_bn', 'ilike', '%'.$this->search.'%');
            });
        }

        // Effective price: cheapest active variant if the product has attributes, else the base price.
        $effectivePrice = 'COALESCE((SELECT MIN(pa.price) FROM product_attributes pa WHERE pa.product_id = products.id AND pa.is_active = true), products.price)';

        if ($this->minPrice !== '' && is_numeric($this->minPrice)) {
            $query->whereRaw("$effectivePrice >= ?", [(float) $this->minPrice]);
        }

        if ($this->maxPrice !== '' && is_numeric($this->maxPrice)) {
            $query->whereRaw("$effectivePrice <= ?", [(float) $this->maxPrice]);
        }

        if ($this->inStockOnly) {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereDoesntHave('productAttributes')->where('stock', '>', 0);
                })->orWhereHas('productAttributes', fn ($q2) => $q2->where('stock', '>', 0));
            });
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('is_featured')->orderBy('order'),
        };

        $perPage = in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : 6;

        return $query->paginate($perPage)->withQueryString();
    }

    public function render()
    {
        $siteName = Setting::get('site_name', config('app.name'));

        return view('livewire.category-page')
            ->layout('components.layouts.public', [
                'title' => $this->category->name.' - '.$siteName,
                'metaDescription' => Str::limit($this->category->description ?: __('Shop :category at :site.', ['category' => $this->category->name, 'site' => $siteName]), 160),
                'showNavigation' => true,
                'showFooter' => true,
                'showCookieConsent' => true,
            ]);
    }
}
