<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\Tenancy;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesPage extends Component
{
    use WithPagination;

    /** Fallback "grid columns" choices, used only when no admin setting is configured. */
    public const COLUMN_OPTIONS = [4, 6, 8, 10];

    /** Fallback "categories per page" choices, used only when no admin setting is configured. */
    public const PER_PAGE_OPTIONS = [12, 24, 48];

    #[Url(except: '')]
    public string $search = '';

    // Nullable + `except: null` rather than a hardcoded literal default: a
    // PHP property default must be a compile-time constant, so it can't read
    // the admin-configured Setting default directly. null is the "nothing in
    // the URL" sentinel that mount() below resolves to the real configured
    // default — a fixed literal here would permanently win over the admin's
    // chosen default on every fresh visit as long as it happened to still be
    // a member of the configured options list.
    #[Url(except: null)]
    public ?int $columns = null;

    #[Url(except: null)]
    public ?int $perPage = null;

    public function mount(): void
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

    public function updatedColumns(int $value): void
    {
        if (! in_array($value, self::columnOptions(), true)) {
            $this->columns = self::defaultColumns();
        }
    }

    public function updatedPerPage(int $value): void
    {
        if (! in_array($value, self::perPageOptions(), true)) {
            $this->perPage = self::defaultPerPage();
        }

        $this->resetPage();
    }

    /** Admin-configurable "grid columns" choices (Setting::categories_columns_options), falling back to COLUMN_OPTIONS. */
    public static function columnOptions(): array
    {
        return self::parseOptionsSetting('categories_columns_options') ?: self::COLUMN_OPTIONS;
    }

    public static function defaultColumns(): int
    {
        $options = self::columnOptions();
        $default = (int) Setting::get('categories_columns_default', (string) $options[0]);

        return in_array($default, $options, true) ? $default : $options[0];
    }

    /** Admin-configurable "categories per page" choices (Setting::categories_per_page_options), falling back to PER_PAGE_OPTIONS. */
    public static function perPageOptions(): array
    {
        return self::parseOptionsSetting('categories_per_page_options') ?: self::PER_PAGE_OPTIONS;
    }

    public static function defaultPerPage(): int
    {
        $options = self::perPageOptions();
        $default = (int) Setting::get('categories_per_page_default', (string) $options[0]);

        return in_array($default, $options, true) ? $default : $options[0];
    }

    /** Decode a JSON-encoded list of positive integers stored in Settings, e.g. "[4,6,8,10]". */
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
     * Top-level active categories with an active-product count aggregated
     * across each category's full subtree (so a purely organizational parent
     * with all its products filed under children still shows the real total).
     */
    #[Computed]
    public function categories()
    {
        $cards = Cache::remember(Tenancy::cacheKey('categories.index.cards'), 1800, function () {
            $all = Category::where('is_active', true)
                ->orderBy('order')
                ->get(['id', 'parent_id', 'name_en', 'name_bn', 'slug', 'description_en', 'description_bn', 'image', 'order']);

            $productCounts = Product::where('is_active', true)
                ->whereIn('category_id', $all->pluck('id'))
                ->selectRaw('category_id, count(*) as aggregate')
                ->groupBy('category_id')
                ->pluck('aggregate', 'category_id');

            $subtreeCount = function (int $categoryId) use (&$subtreeCount, $all, $productCounts) {
                $count = (int) ($productCounts[$categoryId] ?? 0);
                foreach ($all->where('parent_id', $categoryId) as $child) {
                    $count += $subtreeCount($child->id);
                }

                return $count;
            };

            return $all->where('parent_id', null)
                ->values()
                ->map(function ($category) use ($all, $subtreeCount) {
                    return [
                        'category' => $category,
                        'productCount' => $subtreeCount($category->id),
                        'subcategories' => $all->where('parent_id', $category->id)->values(),
                    ];
                });
        });

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $cards = $cards->filter(fn ($card) => str_contains(mb_strtolower($card['category']->name), $needle));
        }

        $cards = $cards->values();

        $perPage = $this->perPage !== null && in_array($this->perPage, self::perPageOptions(), true)
            ? $this->perPage
            : self::defaultPerPage();
        $page = $this->getPage();

        return new LengthAwarePaginator(
            $cards->forPage($page, $perPage)->values(),
            $cards->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }

    /** Responsive grid-column ceiling derived from the admin/user-selected column count. */
    #[Computed]
    public function gridColsClass(): string
    {
        $max = max(2, min(12, $this->columns));
        $lg = max(2, (int) round($max * 0.7));
        $sm = max(1, (int) round($max * 0.4));

        return "grid-cols-1 sm:grid-cols-{$sm} lg:grid-cols-{$lg} xl:grid-cols-{$max}";
    }

    public function render()
    {
        $siteName = Setting::get('site_name', config('app.name'));

        return view('livewire.categories-page')
            ->layout('components.layouts.public', [
                'title' => __('Categories').' - '.$siteName,
                'metaDescription' => Str::limit(__('Browse all product categories at :site.', ['site' => $siteName]), 160),
                'showNavigation' => true,
                'showFooter' => true,
                'showCookieConsent' => true,
            ]);
    }
}
