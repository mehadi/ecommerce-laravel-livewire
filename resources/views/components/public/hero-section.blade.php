@props([
    'heroSection',
    'product',
    'customTitle' => null,
    'customContent' => null,
    'customBadgeText' => null,
    'socialFacebook' => null,
    'socialInstagram' => null,
    'socialTwitter' => null,
    // Fallback CTA copy/targets when the tenant hasn't configured their own.
    // The landing-page funnel keeps the buy-focused defaults; the storefront
    // homepage passes shop-focused ones.
    'defaultPrimaryCtaLabel' => null,
    'defaultPrimaryCtaUrl' => null,
    'defaultSecondaryCtaLabel' => null,
    'defaultSecondaryCtaUrl' => null,
])

@php
    use App\Models\Attribute;
    use App\Models\Category;
    use App\Models\Order;
    use App\Models\Product;
    use App\Models\Setting;
    use App\Models\Testimonial;
    use App\Support\HeroVariants;
    use App\Support\Tenancy;
    use Illuminate\Support\Facades\Cache;

    $heroSettings = Setting::getMany([
        'storefront_hero_variant',
        'hero_badge_text',
        'hero_primary_cta_label',
        'hero_primary_cta_url',
        'hero_secondary_cta_label',
        'hero_secondary_cta_url',
        'hero_show_stats',
    ]);

    $heroVariant = HeroVariants::resolve($heroSettings['storefront_hero_variant']);

    $heroImage = ($heroSection && $heroSection->image) ? $heroSection->image : $product?->primary_image;

    $heroExtras = Cache::remember(Tenancy::cacheKey('landing.hero.extras.'.($product?->id ?? 'none')), 1800, function () use ($product) {
        $weightValues = Attribute::where('slug', 'weight')->with('activeValues')->first()?->activeValues ?? collect();

        $spotlightProduct = Product::where('is_active', true)
            ->when($product, fn ($query) => $query->where('id', '!=', $product->id))
            ->whereNotNull('primary_image')
            ->orderByDesc('is_featured')
            ->orderBy('order')
            ->first();

        $spotlightCategory = Category::where('is_active', true)
            ->whereNotNull('image')
            ->orderBy('order')
            ->first();

        $recentProducts = Product::where('is_active', true)
            ->whereNotNull('primary_image')
            ->latest()
            ->take(3)
            ->get(['id', 'primary_image', 'name_en', 'name_bn']);

        return [
            'weightValues' => $weightValues->take(6),
            'spotlightProduct' => $spotlightProduct,
            'spotlightCategory' => $spotlightCategory,
            'recentProducts' => $recentProducts,
            'productCount' => Product::where('is_active', true)->count(),
            'orderCount' => Order::count(),
            'testimonialCount' => Testimonial::active()->count(),
            'avgRating' => round((float) Testimonial::active()->avg('rating'), 1),
            'spotlightTestimonial' => Testimonial::active()->ordered()->first(),
        ];
    });

    $heroTitle = $customTitle ?: ($heroSection?->title ?? '');
    $heroContent = $customContent ?: ($heroSection?->content ?? '');
    // Landing-page config override wins, then the tenant's hero settings,
    // then the stock badge (only when a hero section exists at all).
    $heroBadge = $customBadgeText
        ?: ($heroSettings['hero_badge_text']
        ?: ($heroSection ? __('100% Natural & Premium Quality') : null));
    $heroPrimaryCtaLabel = $heroSettings['hero_primary_cta_label'] ?: ($defaultPrimaryCtaLabel ?? __('Order Now'));
    $heroPrimaryCtaUrl = $heroSettings['hero_primary_cta_url'] ?: ($defaultPrimaryCtaUrl ?? '#product');
    $heroSecondaryCtaLabel = $heroSettings['hero_secondary_cta_label'] ?: ($defaultSecondaryCtaLabel ?? __('Browse Shop'));
    $heroSecondaryCtaUrl = $heroSettings['hero_secondary_cta_url'] ?: ($defaultSecondaryCtaUrl ?? '/shop');
    // wire:navigate only works for same-app paths; skip it for anchors/external URLs.
    $heroPrimaryCtaNavigate = str_starts_with($heroPrimaryCtaUrl, '/');
    $heroSecondaryCtaNavigate = str_starts_with($heroSecondaryCtaUrl, '/');
    $heroShowStats = ($heroSettings['hero_show_stats'] ?? '1') !== '0';
    $heroOrderCountLabel = $heroExtras['orderCount'] > 999 ? number_format($heroExtras['orderCount'] / 1000, 1).'K+' : $heroExtras['orderCount'].'+';
@endphp

@if($heroSection || $customTitle || $product)
    @include('components.public.heroes.'.$heroVariant)
@endif
