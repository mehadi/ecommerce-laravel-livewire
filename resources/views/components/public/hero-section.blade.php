@props([
    'heroSection',
    'product',
    'customTitle' => null,
    'customContent' => null,
    'customBadgeText' => null,
    'socialFacebook' => null,
    'socialInstagram' => null,
    'socialTwitter' => null,
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

    $heroVariant = HeroVariants::resolve(Setting::get('storefront_hero_variant'));

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
    $heroBadge = ($customBadgeText || $heroSection) ? ($customBadgeText ?: __('100% Natural & Premium Quality')) : null;
    $heroOrderCountLabel = $heroExtras['orderCount'] > 999 ? number_format($heroExtras['orderCount'] / 1000, 1).'K+' : $heroExtras['orderCount'].'+';
@endphp

@if($heroSection || $customTitle || $product)
    @include('components.public.heroes.'.$heroVariant)
@endif
