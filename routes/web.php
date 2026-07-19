<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Central/platform routes: reachable on the central domain(s) with no tenant resolved
// (config('tenancy.central_domains')). Marketing/signup/platform-admin routes belong here.

// Platform marketing homepage. The tenant group below also registers a `GET /`
// (the tenant's own storefront home) — Laravel route matching is first-match-wins
// on URI regardless of middleware, so this has to be domain-scoped to the central
// domain(s) explicitly, or it would shadow every tenant's homepage.
foreach (config('tenancy.central_domains', []) as $centralDomain) {
    Route::domain($centralDomain)->group(function () {
        Route::get('/', \App\Livewire\Platform\Landing::class)->name('platform.home');
    });
}

Route::get('/change-language/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'bn'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }

    return redirect()->back();
})->name('change-language');

// Called by Caddy's on-demand TLS "ask" directive (internal network only — nginx's
// port 80 is not published to the host, only Caddy's 80/443 are) before it will
// request/renew a Let's Encrypt certificate for an unrecognized domain. Authorizes
// the same two cases App\Http\Middleware\ResolveTenant accepts — a DNS-verified
// custom domain, or a valid "{slug}.{central domain}" free subdomain — for an
// active tenant. Without this gate, anyone pointing DNS at the platform could
// trigger cert issuance for a domain they don't legitimately control.
Route::get('/internal/domains/ask', function (\Illuminate\Http\Request $request) {
    $host = (string) $request->query('domain', '');

    $authorized = $host !== '' && (
        \App\Models\Domain::query()
            ->whereNotNull('verified_at')
            ->where('domain', $host)
            ->whereHas('tenant', fn ($query) => $query->where('status', 'active'))
            ->exists()
        || collect(config('tenancy.central_domains', []))
            ->contains(fn ($central) => str_ends_with($host, '.'.$central)
                && \App\Models\Tenant::query()
                    ->where('slug', substr($host, 0, -strlen('.'.$central)))
                    ->where('status', 'active')
                    ->exists())
    );

    return response($authorized ? 'ok' : 'domain not authorized', $authorized ? 200 : 403);
})->name('internal.domains.ask');

// Platform-staff routes (tenant_id = null users) — the SaaS operator's own admin area,
// reachable on the central domain where no tenant is resolved. Not wrapped in the
// 'tenant' middleware since platform staff have no tenant context by definition.
Route::middleware(['auth', 'can:access platform'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/', \App\Livewire\Platform\Dashboard::class)->name('dashboard');
    Route::get('tenants', \App\Livewire\Platform\Tenants\Index::class)->name('tenants.index');
    Route::get('tenants/{tenant}', \App\Livewire\Platform\Tenants\Show::class)->name('tenants.show');
    Route::get('upgrade-requests', \App\Livewire\Platform\UpgradeRequests\Index::class)->name('upgrade-requests.index');
    Route::get('plans', \App\Livewire\Platform\Plans\Index::class)->name('plans.index');
    Route::get('billing', \App\Livewire\Platform\Billing\Index::class)->name('billing.index');
    Route::get('analytics', \App\Livewire\Platform\Analytics\Index::class)->name('analytics.index');
    Route::get('settings', \App\Livewire\Platform\Settings\Index::class)->name('settings.index');

    // Platform-wide defaults for tenant Website Settings — App\Models\Setting::get()
    // falls back to these PlatformSetting values whenever a tenant leaves a field blank.
    Route::prefix('website-defaults')->name('website-defaults.')->group(function () {
        Route::get('/', \App\Livewire\Platform\WebsiteDefaults\General::class)->name('index');
        Route::get('appearance', \App\Livewire\Platform\WebsiteDefaults\Appearance::class)->name('appearance');
        Route::get('contact', \App\Livewire\Platform\WebsiteDefaults\Contact::class)->name('contact');
        Route::get('social', \App\Livewire\Platform\WebsiteDefaults\Social::class)->name('social');
        Route::get('analytics', \App\Livewire\Platform\WebsiteDefaults\Analytics::class)->name('analytics');
        Route::get('verification', \App\Livewire\Platform\WebsiteDefaults\Verification::class)->name('verification');
        Route::get('seo', \App\Livewire\Platform\WebsiteDefaults\Seo::class)->name('seo');
    });
});

// Stop-impersonating must work regardless of which side of the app (tenant admin
// or platform) is currently mounted, so it's a plain auth-only route rather than
// living inside either the 'tenant' or 'platform.' route groups.
Route::post('/stop-impersonating', [\App\Http\Controllers\ImpersonationController::class, 'stop'])
    ->middleware('auth')->name('impersonation.stop');

// Tenant routes: only reachable once a tenant has been resolved by domain/subdomain
// (see App\Http\Middleware\ResolveTenant + the 'tenant' middleware alias). This guards
// against tenant-scoped global scopes silently no-op'ing (= unscoped, cross-tenant data)
// if these routes were ever hit on the central domain.
Route::middleware(['tenant'])->group(function () {
    // Reached on the TARGET TENANT'S own domain via a short-lived signed link
    // from Platform\Tenants\Show::impersonate() — see
    // App\Http\Controllers\ImpersonationController::enter() for why this can't
    // just be a same-request Auth::login() + redirect from the central domain.
    Route::get('/impersonate/enter/{impersonator}/{user}', [\App\Http\Controllers\ImpersonationController::class, 'enter'])
        ->name('impersonation.enter');

    // Storefront homepage — a multi-product store front. Campaign funnels
    // (single-product landing pages) live on /lp/{slug} below.
    Route::get('/', App\Livewire\HomePage::class)->name('home');
    Route::get('/shop', App\Livewire\ShopPage::class)->name('shop');
    Route::get('/product/{product}', App\Livewire\ProductPage::class)->name('product.show')->where('product', '[0-9]+');
    Route::get('/categories', App\Livewire\CategoriesPage::class)->name('categories.index');
    Route::get('/category/{category:slug}', App\Livewire\CategoryPage::class)->name('category.show');
    Route::get('/lp/{slug}', App\Livewire\LandingPage::class)->name('landing-page');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('dashboard', App\Livewire\Dashboard\Overview::class)->name('dashboard');
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('sales', App\Livewire\Dashboard\Sales::class)->name('sales');
            Route::get('orders', App\Livewire\Dashboard\Orders::class)->name('orders');
            Route::get('customers', App\Livewire\Dashboard\Customers::class)->name('customers');
            Route::get('products', App\Livewire\Dashboard\Products::class)->name('products');
            Route::get('profitability', App\Livewire\Dashboard\Profitability::class)->name('profitability');
            Route::get('inventory', App\Livewire\Dashboard\Inventory::class)->name('inventory');
        });
    });

    Route::middleware(['auth'])->group(function () {
        Route::redirect('settings', 'settings/profile');

        Route::get('settings/profile', Profile::class)->name('profile.edit');
        Route::get('settings/password', Password::class)->name('user-password.edit');
        Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

        Route::get('settings/two-factor', TwoFactor::class)
            ->middleware(
                when(
                    Features::canManageTwoFactorAuthentication()
                        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                    ['password.confirm'],
                    [],
                ),
            )
            ->name('two-factor.show');

        // Admin Routes
        Route::prefix('admin')->name('admin.')->middleware(['can:access admin'])->group(function () {
        // Website Settings
        Route::prefix('website')->name('website.')->group(function () {
            Route::get('/', \App\Livewire\Admin\WebsiteSettings\General::class)->name('index');
            Route::get('appearance', \App\Livewire\Admin\WebsiteSettings\Appearance::class)->name('appearance');
            Route::get('hero', \App\Livewire\Admin\WebsiteSettings\Hero::class)->name('hero');
            Route::get('header', \App\Livewire\Admin\WebsiteSettings\Header::class)->name('header');
            Route::get('product-grid', \App\Livewire\Admin\WebsiteSettings\ProductGrid::class)->name('product-grid');
            Route::get('product-details', \App\Livewire\Admin\WebsiteSettings\ProductDetails::class)->name('product-details');
            Route::get('category-grid', \App\Livewire\Admin\WebsiteSettings\CategoryGrid::class)->name('category-grid');
            Route::get('footer', \App\Livewire\Admin\WebsiteSettings\Footer::class)->name('footer');
            Route::get('contact', \App\Livewire\Admin\WebsiteSettings\Contact::class)->name('contact');
            Route::get('social', \App\Livewire\Admin\WebsiteSettings\Social::class)->name('social');
            Route::get('analytics', \App\Livewire\Admin\WebsiteSettings\Analytics::class)->name('analytics');
            Route::get('verification', \App\Livewire\Admin\WebsiteSettings\Verification::class)->name('verification');
            Route::get('seo', \App\Livewire\Admin\WebsiteSettings\Seo::class)->name('seo');
            Route::get('domains', \App\Livewire\Admin\WebsiteSettings\Domains::class)->name('domains');
            Route::get('localization', \App\Livewire\Admin\WebsiteSettings\Localization::class)->name('localization');
            Route::get('custom-code', \App\Livewire\Admin\WebsiteSettings\CustomCode::class)->name('custom-code');
        });

        // Products
        Route::get('products', App\Livewire\Admin\Products\Index::class)->name('products.index');
        Route::get('products/create', App\Livewire\Admin\Products\Create::class)->name('products.create');
        Route::get('products/{product}/edit', App\Livewire\Admin\Products\Create::class)->name('products.edit')->where('product', '[0-9]+');

        // Attributes
        Route::get('attributes', App\Livewire\Admin\Attributes\Index::class)->name('attributes.index');

        // Categories
        Route::get('categories', App\Livewire\Admin\Categories\Index::class)->name('categories.index');

        // Categories Page Display (grid columns + pagination options for the public /categories page)
        Route::get('categories-display', App\Livewire\Admin\CategoriesDisplay\Index::class)->name('categories-display.index');

        // Orders
        Route::get('orders', App\Livewire\Admin\Orders\Index::class)->name('orders.index');

        // Coupons
        Route::get('coupons', App\Livewire\Admin\Coupons\Index::class)->name('coupons.index');

        // Shipping
        Route::get('shipping', App\Livewire\Admin\Shipping\Index::class)->name('shipping.index');

        // Cart & Checkout
        Route::get('cart-checkout', App\Livewire\Admin\CartCheckout\Index::class)->name('cart-checkout.index');

        // Landing Page Sections
        Route::get('sections', App\Livewire\Admin\Sections\Index::class)->name('sections.index');

        // Testimonials
        Route::get('testimonials', App\Livewire\Admin\Testimonials\Index::class)->name('testimonials.index');

        // Navigation
        Route::get('navigation', App\Livewire\Admin\Navigation\Index::class)->name('navigation.index');

        // Landing Pages
        Route::get('landing-pages', App\Livewire\Admin\LandingPages\Index::class)->name('landing-pages.index');
        Route::get('landing-pages/create', App\Livewire\Admin\LandingPages\Create::class)->name('landing-pages.create');
        Route::get('landing-pages/{landingPage}/edit', App\Livewire\Admin\LandingPages\Edit::class)->name('landing-pages.edit');

        // Roles (modal-based create/edit lives in Roles\Index — no dedicated routed pages)
        Route::get('roles', App\Livewire\Admin\Roles\Index::class)->name('roles.index');

        // Permissions (modal-based create/edit lives in Permissions\Index — no dedicated routed pages)
        Route::get('permissions', App\Livewire\Admin\Permissions\Index::class)->name('permissions.index');

        // Users
        Route::get('users', App\Livewire\Admin\Users\Index::class)->name('users.index');

        // Billing
        Route::get('billing', App\Livewire\Admin\Billing\Index::class)->name('billing.index');
        });
    });
});
