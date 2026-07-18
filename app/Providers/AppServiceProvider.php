<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\Testimonial;
use App\Observers\ProductVariationCombinationObserver;
use App\Observers\ProductVariationObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        ProductVariationCombination::observe(ProductVariationCombinationObserver::class);
        ProductVariation::observe(ProductVariationObserver::class);

        // Define admin gate
        Gate::define('access admin', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager');
        });

        // Define gate for managing roles and permissions (super admin and admin only)
        Gate::define('manage roles', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin');
        });

        Gate::define('manage permissions', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin');
        });

        // Define gate for managing users (super admin and admin can manage, others can view if they have permission)
        Gate::define('manage users', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasPermissionTo('view users');
        });

        // Cache featured products
        if (! app()->runningInConsole()) {
            Cache::remember('products.featured', 3600, function () {
                return Product::where('is_active', true)
                    ->where('is_featured', true)
                    ->orderBy('order')
                    ->get();
            });

            Cache::remember('categories.all', 3600, function () {
                return Category::where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });

            Cache::remember('testimonials.active', 3600, function () {
                return Testimonial::where('is_active', true)
                    ->orderBy('order')
                    ->limit(6)
                    ->get();
            });
        }
    }
}
