<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasShoppingCart;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ShopPage extends Component
{
    use HasShoppingCart, WithPagination;

    /** Allowed values for the "products per page" control. */
    public const PER_PAGE_OPTIONS = [6, 12, 24, 48];

    /** Allowed values for the "grid columns" control. */
    public const COLUMN_OPTIONS = [2, 3, 4];

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: null)]
    public ?int $category = null;

    #[Url(except: 'featured')]
    public string $sort = 'featured';

    #[Url(except: '')]
    public string $minPrice = '';

    #[Url(except: '')]
    public string $maxPrice = '';

    #[Url(except: false)]
    public bool $inStockOnly = false;

    #[Url(as: 'attrs', except: [])]
    public array $attributeFilters = [];

    #[Url(except: 6)]
    public int $perPage = 6;

    #[Url(except: 3)]
    public int $columns = 3;

    public bool $showFilters = false;

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

    public function selectCategory(?int $categoryId): void
    {
        $this->category = $categoryId;
        $this->resetPage();
    }

    public function toggleAttributeValue(int $attributeId, string $value): void
    {
        $selected = $this->attributeFilters[$attributeId] ?? [];

        if (in_array($value, $selected, true)) {
            $selected = array_values(array_diff($selected, [$value]));
        } else {
            $selected[] = $value;
        }

        if (empty($selected)) {
            unset($this->attributeFilters[$attributeId]);
        } else {
            $this->attributeFilters[$attributeId] = $selected;
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'sort', 'minPrice', 'maxPrice', 'inStockOnly', 'attributeFilters');
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    #[Computed]
    public function categories()
    {
        return Cache::remember('shop.categories', 1800, function () {
            return Category::whereHas('products', fn ($q) => $q->where('is_active', true))
                ->orderBy('order')
                ->get();
        });
    }

    #[Computed]
    public function filterableAttributes()
    {
        return Cache::remember('shop.filterable_attributes', 1800, function () {
            $usedValuesByName = [];
            foreach (ProductAttribute::where('is_active', true)
                ->whereHas('product', fn ($q) => $q->where('is_active', true))
                ->pluck('attribute_data') as $data) {
                foreach ($data as $name => $value) {
                    $usedValuesByName[$name][$value] = true;
                }
            }

            if (empty($usedValuesByName)) {
                return collect();
            }

            return Attribute::where('is_active', true)
                ->whereIn('name', array_keys($usedValuesByName))
                ->orderBy('order')
                ->with('activeValues')
                ->get()
                ->map(function ($attribute) use ($usedValuesByName) {
                    $used = array_keys($usedValuesByName[$attribute->name] ?? []);
                    $attribute->filterValues = $attribute->activeValues->filter(
                        fn ($v) => in_array($v->value, $used, true) || in_array($v->display_value, $used, true)
                    )->values();

                    return $attribute;
                })
                ->filter(fn ($attribute) => $attribute->filterValues->isNotEmpty())
                ->values();
        });
    }

    #[Computed]
    public function priceBounds(): array
    {
        return Cache::remember('shop.products.price_bounds', 1800, function () {
            $range = Product::where('is_active', true)->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

            return [
                'min' => $range && $range->min_price !== null ? (int) floor((float) $range->min_price) : 0,
                'max' => $range && $range->max_price !== null ? (int) ceil((float) $range->max_price) : 0,
            ];
        });
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return ($this->category !== null ? 1 : 0)
            + ($this->minPrice !== '' ? 1 : 0)
            + ($this->maxPrice !== '' ? 1 : 0)
            + ($this->inStockOnly ? 1 : 0)
            + collect($this->attributeFilters)->filter(fn ($values) => ! empty($values))->count();
    }

    #[Computed]
    public function products()
    {
        $query = Product::where('is_active', true)
            ->with(['category', 'productAttributes']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name_en', 'ilike', '%'.$this->search.'%')
                    ->orWhere('name_bn', 'ilike', '%'.$this->search.'%');
            });
        }

        if ($this->category) {
            $query->where('category_id', $this->category);
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

        foreach ($this->attributeFilters as $attributeId => $values) {
            if (empty($values)) {
                continue;
            }

            $attributeName = $this->filterableAttributes->firstWhere('id', (int) $attributeId)?->name;

            if (! $attributeName) {
                continue;
            }

            $query->whereHas('productAttributes', function ($q) use ($attributeName, $values) {
                $q->where('is_active', true)->where(function ($q2) use ($attributeName, $values) {
                    foreach ($values as $value) {
                        $q2->orWhereRaw('attribute_data->>? = ?', [$attributeName, $value]);
                    }
                });
            });
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('is_featured')->orderBy('order'),
        };

        $perPage = in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : 6;

        return $query->paginate($perPage);
    }

    public function render()
    {
        $siteName = Setting::get('site_name', config('app.name'));

        return view('livewire.shop-page')
            ->layout('components.layouts.public', [
                'title' => __('Shop').' - '.$siteName,
                'metaDescription' => __('Browse our full range of natural, premium quality products at :site.', ['site' => $siteName]),
                'showNavigation' => true,
                'showFooter' => true,
                'showCookieConsent' => true,
            ]);
    }
}
