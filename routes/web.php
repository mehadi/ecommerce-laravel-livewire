<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/change-language/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'bn'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }

    return redirect()->back();
})->name('change-language');

Route::get('/', App\Livewire\LandingPage::class)->name('home');
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
            Route::get('contact', \App\Livewire\Admin\WebsiteSettings\Contact::class)->name('contact');
            Route::get('social', \App\Livewire\Admin\WebsiteSettings\Social::class)->name('social');
            Route::get('analytics', \App\Livewire\Admin\WebsiteSettings\Analytics::class)->name('analytics');
            Route::get('verification', \App\Livewire\Admin\WebsiteSettings\Verification::class)->name('verification');
            Route::get('seo', \App\Livewire\Admin\WebsiteSettings\Seo::class)->name('seo');
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

        // Roles
        Route::get('roles', App\Livewire\Admin\Roles\Index::class)->name('roles.index');
        Route::get('roles/create', App\Livewire\Admin\Roles\Create::class)->name('roles.create');
        Route::get('roles/{role}/edit', App\Livewire\Admin\Roles\Edit::class)->name('roles.edit');

        // Permissions
        Route::get('permissions', App\Livewire\Admin\Permissions\Index::class)->name('permissions.index');
        Route::get('permissions/create', App\Livewire\Admin\Permissions\Create::class)->name('permissions.create');
        Route::get('permissions/{permission}/edit', App\Livewire\Admin\Permissions\Edit::class)->name('permissions.edit');

        // Users
        Route::get('users', App\Livewire\Admin\Users\Index::class)->name('users.index');
    });
});
