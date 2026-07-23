<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasShoppingCart;
use App\Models\Category;
use App\Models\LandingPageSection;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The tenant storefront homepage (`GET /`) — a multi-product store front:
 * hero, featured categories, featured products, testimonials, shop CTA.
 *
 * Campaign/product funnel pages live on `/lp/{slug}` (App\Livewire\LandingPage),
 * which is a single-product page with its own buy-now flow.
 */
class HomePage extends Component
{
    use HasShoppingCart;

    #[Computed]
    public function getHeroSectionProperty(): ?LandingPageSection
    {
        return Cache::remember(Tenancy::cacheKey('landing.sections.hero'), 3600, function () {
            return LandingPageSection::where('type', 'hero')
                ->where('is_active', true)
                ->orderBy('order')
                ->first();
        });
    }

    /**
     * The product spotlighted inside the hero visual. Purely presentational —
     * the homepage has no single-product buy flow.
     */
    #[Computed]
    public function getHeroProductProperty(): ?Product
    {
        return Cache::remember(Tenancy::cacheKey('home.hero_product'), 3600, function () {
            return Product::where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('order')
                ->first();
        });
    }

    /**
     * A second, distinct product for the hero's "spotlight" card -- shares the
     * heroProduct's ordering but excludes it so the two cards never repeat.
     */
    #[Computed]
    public function getHeroSpotlightProductProperty(): ?Product
    {
        return Cache::remember(Tenancy::cacheKey('home.hero_spotlight_product'), 3600, function () {
            return Product::where('is_active', true)
                ->when($this->heroProduct, fn ($query) => $query->where('id', '!=', $this->heroProduct->id))
                ->whereNotNull('primary_image')
                ->orderByDesc('is_featured')
                ->orderBy('order')
                ->first();
        });
    }

    #[Computed]
    public function getFeaturedProductsProperty()
    {
        return Cache::remember(Tenancy::cacheKey('landing.featured_products'), 3600, function () {
            return Product::where('is_active', true)
                ->where('is_featured', true)
                ->with(['category', 'productAttributes'])
                ->orderBy('order')
                ->limit(8)
                ->get();
        });
    }

    #[Computed]
    public function getFeaturedCategoriesProperty()
    {
        return Cache::remember(Tenancy::cacheKey('landing.featured_categories'), 3600, function () {
            return Category::where('is_active', true)
                ->whereHas('products', fn ($q) => $q->where('is_active', true))
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('order')
                ->limit(6)
                ->get();
        });
    }

    #[Computed]
    public function getTestimonialsProperty()
    {
        return Cache::remember(Tenancy::cacheKey('testimonials.active'), 3600, function () {
            return Testimonial::where('is_active', true)
                ->orderBy('order')
                ->limit(6)
                ->get();
        });
    }

    #[Computed]
    public function getFaqsProperty()
    {
        return Cache::remember(Tenancy::cacheKey('landing.sections.faq'), 3600, function () {
            return LandingPageSection::where('type', 'faq')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    #[Computed]
    public function getSiteNameProperty(): string
    {
        return Setting::get('site_name', config('app.name'));
    }

    #[Computed]
    public function getSocialFacebookProperty(): string
    {
        return Setting::get('social_facebook', '#');
    }

    #[Computed]
    public function getSocialInstagramProperty(): string
    {
        return Setting::get('social_instagram', '#');
    }

    #[Computed]
    public function getSocialTwitterProperty(): string
    {
        return Setting::get('social_twitter', '#');
    }

    public function render()
    {
        return view('livewire.home-page')
            ->layout('components.layouts.public', [
                'title' => $this->siteName,
                'showNavigation' => true,
                'showFooter' => true,
                'showCookieConsent' => true,
            ]);
    }
}
