<?php

namespace App\Livewire\Concerns;

use App\Models\Setting;
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
    /** Fallback "products per page" choices, used only when no admin setting is configured. */
    public const PER_PAGE_OPTIONS = [6, 12, 24, 48];

    /** Fallback "grid columns" choices, used only when no admin setting is configured. */
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

    // Nullable + `except: null` rather than a hardcoded literal default: a
    // PHP property default must be a compile-time constant, so it can't read
    // the admin-configured Setting default directly. null is the "nothing in
    // the URL" sentinel that mountFiltersProductGrid() below resolves to the
    // real configured default — a fixed literal here would permanently win
    // over the admin's chosen default on every fresh visit as long as it
    // happened to still be a member of the configured options list.
    #[Url(except: null)]
    public ?int $perPage = null;

    #[Url(except: null)]
    public ?int $columns = null;

    public bool $showFilters = false;

    /**
     * Livewire auto-invokes mount{TraitName}() for every trait a component
     * uses (see WithPagination's own bootWithPagination()) — this resolves
     * columns/perPage (still null when absent from the URL) to the admin-
     * configured default, or clamps an out-of-range URL-seeded value back to
     * it, for every page that uses this trait (ShopPage, CategoryPage)
     * without requiring each host component to remember to call it.
     */
    public function mountFiltersProductGrid(): void
    {
        if ($this->columns === null || ! in_array($this->columns, self::columnOptions(), true)) {
            $this->columns = self::defaultColumns();
        }

        if ($this->perPage === null || ! in_array($this->perPage, self::perPageOptions(), true)) {
            $this->perPage = self::defaultPerPage();
        }
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
        if (! in_array($value, self::perPageOptions(), true)) {
            $this->perPage = self::defaultPerPage();
        }

        $this->resetPage();
    }

    public function updatedColumns(int $value): void
    {
        if (! in_array($value, self::columnOptions(), true)) {
            $this->columns = self::defaultColumns();
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
        return $this->perPage !== null && in_array($this->perPage, self::perPageOptions(), true)
            ? $this->perPage
            : self::defaultPerPage();
    }

    /** Admin-configurable "grid columns" choices (Setting::shop_columns_options), falling back to COLUMN_OPTIONS. */
    public static function columnOptions(): array
    {
        return self::parseOptionsSetting('shop_columns_options') ?: self::COLUMN_OPTIONS;
    }

    public static function defaultColumns(): int
    {
        $options = self::columnOptions();
        $default = (int) Setting::get('shop_columns_default', (string) $options[0]);

        return in_array($default, $options, true) ? $default : $options[0];
    }

    /** Admin-configurable "products per page" choices (Setting::shop_per_page_options), falling back to PER_PAGE_OPTIONS. */
    public static function perPageOptions(): array
    {
        return self::parseOptionsSetting('shop_per_page_options') ?: self::PER_PAGE_OPTIONS;
    }

    public static function defaultPerPage(): int
    {
        $options = self::perPageOptions();
        $default = (int) Setting::get('shop_per_page_default', (string) $options[0]);

        return in_array($default, $options, true) ? $default : $options[0];
    }

    /** Decode a JSON-encoded list of positive integers stored in Settings, e.g. "[2,3,4]". */
    private static function parseOptionsSetting(string $key): array
    {
        $decoded = json_decode((string) Setting::get($key, ''), true);

        if (! is_array($decoded) || empty($decoded)) {
            return [];
        }

        $values = array_values(array_unique(array_map('intval', $decoded)));
        $values = array_values(array_filter($values, fn ($v) => $v > 0));
        sort($values);

        return $values;
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
