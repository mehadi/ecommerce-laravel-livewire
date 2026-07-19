<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Url;

/**
 * Search/sort/price/stock filtering and grid display prefs shared by every
 * paginated product grid (ShopPage, CategoryPage). Each component still
 * builds and paginates its own `products()` query — this only factors out
 * the identical URL-bound state, its update hooks, and the common WHERE/
 * ORDER BY clauses so the two don't drift out of sync with each other.
 */
trait FiltersProductGrid
{
    /** Allowed values for the "products per page" control. */
    public const PER_PAGE_OPTIONS = [6, 12, 24, 48];

    /** Allowed values for the "grid columns" control. */
    public const COLUMN_OPTIONS = [2, 3, 4];

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

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    /** The URL-bound filter property names common to every product-grid page. */
    protected function commonFilterProperties(): array
    {
        return ['search', 'sort', 'minPrice', 'maxPrice', 'inStockOnly'];
    }

    protected function normalizedPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : 6;
    }

    /**
     * Search/price/stock WHERE clauses shared by every product grid.
     * $effectivePriceSql: raw SQL for "cheapest active variant if the
     * product has attributes, else the base price" — callers build it
     * since it references the products table alias each query uses.
     */
    protected function applyCommonProductFilters($query, string $effectivePriceSql): void
    {
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name_en', 'ilike', '%'.$this->search.'%')
                    ->orWhere('name_bn', 'ilike', '%'.$this->search.'%');
            });
        }

        if ($this->minPrice !== '' && is_numeric($this->minPrice)) {
            $query->whereRaw("$effectivePriceSql >= ?", [(float) $this->minPrice]);
        }

        if ($this->maxPrice !== '' && is_numeric($this->maxPrice)) {
            $query->whereRaw("$effectivePriceSql <= ?", [(float) $this->maxPrice]);
        }

        if ($this->inStockOnly) {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereDoesntHave('productAttributes')->where('stock', '>', 0);
                })->orWhereHas('productAttributes', fn ($q2) => $q2->where('stock', '>', 0));
            });
        }
    }

    protected function applyProductSort($query): void
    {
        match ($this->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('is_featured')->orderBy('order'),
        };
    }
}
