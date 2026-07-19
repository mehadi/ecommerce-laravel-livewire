@php
    use App\Models\Category;
    use App\Models\NavigationItem;
    use App\Models\Setting;
    use App\Support\NavbarVariants;
    use App\Support\Tenancy;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;

    $headerVariant = NavbarVariants::resolve(Setting::get('storefront_header_variant'));

    $siteName = Setting::get('site_name', config('app.name'));
    $siteLogo = Setting::get('site_logo');

    // Get navigation items from cache or database with children
    $navigationItems = Cache::remember(Tenancy::cacheKey('navigation.items.active'), 3600, function () {
        return NavigationItem::with('children')
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    });

    // Selected storefront categories, shared by every header variant that shows a category strip.
    $categories = Cache::remember(Tenancy::cacheKey('categories.navigation'), 3600, function () {
        $selectedCategoryIdsQuery = DB::table('navigation_categories');

        if (Tenancy::check()) {
            $selectedCategoryIdsQuery->where('tenant_id', Tenancy::id());
        }

        $selectedCategoryIds = $selectedCategoryIdsQuery
            ->orderBy('order')
            ->pluck('category_id')
            ->toArray();

        if (empty($selectedCategoryIds)) {
            return collect([]);
        }

        return Category::whereIn('id', $selectedCategoryIds)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($category) use ($selectedCategoryIds) {
                return array_search($category->id, $selectedCategoryIds);
            });
    });

    // Get cart from session
    $cart = session()->get('cart', []);
    $cartItemCount = 0;
    foreach ($cart as $item) {
        $cartItemCount += $item['quantity'] ?? 0;
    }
@endphp

@include('components.public.navbars.'.$headerVariant)
