<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class CategoriesPage extends Component
{
    #[Url(except: '')]
    public string $search = '';

    /**
     * Top-level active categories with an active-product count aggregated
     * across each category's full subtree (so a purely organizational parent
     * with all its products filed under children still shows the real total).
     */
    #[Computed]
    public function categories()
    {
        $cards = Cache::remember('categories.index.cards', 1800, function () {
            $all = Category::where('is_active', true)
                ->orderBy('order')
                ->get(['id', 'parent_id', 'name_en', 'name_bn', 'slug', 'image', 'order']);

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

        return $cards->values();
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
