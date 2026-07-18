<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Settings\WebsiteSettings;
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

Route::get('dashboard', App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
        Route::get('website', WebsiteSettings::class)->name('website.index');

        // Products
        Route::get('products', App\Livewire\Admin\Products\Index::class)->name('products.index');
        Route::get('products/create', App\Livewire\Admin\Products\Create::class)->name('products.create');
        Route::get('products/{product}/edit', App\Livewire\Admin\Products\Create::class)->name('products.edit')->where('product', '[0-9]+');

        // Attributes
        Route::get('attributes', App\Livewire\Admin\Attributes\Index::class)->name('attributes.index');

        // Categories
        Route::get('categories', App\Livewire\Admin\Categories\Index::class)->name('categories.index');

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
