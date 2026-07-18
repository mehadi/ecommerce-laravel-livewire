<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\Testimonial;
use App\Observers\ProductVariationCombinationObserver;
use App\Observers\ProductVariationObserver;
use App\Support\Tenancy;
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

        // Define admin gate (tenant-scoped: hasRole() is implicitly scoped to the
        // current tenant via spatie/laravel-permission teams mode)
        Gate::define('access admin', function ($user) {
            return $user->tenant_id !== null
                && ($user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager'));
        });

        // Platform staff (no tenant_id) managing the SaaS itself, not any single store.
        // A tenant_id of null already uniquely identifies platform staff — no role check
        // here, since spatie's teams pivot (model_has_roles.tenant_id) is NOT NULL and a
        // role can never actually be assigned while no tenant/team is bound.
        Gate::define('access platform', function ($user) {
            return $user->tenant_id === null;
        });

        // Restricted to senior platform staff (a plain column, not a Spatie
        // permission — see the can_impersonate_tenants migration for why).
        Gate::define('impersonate tenants', function ($user) {
            return $user->tenant_id === null && $user->can_impersonate_tenants;
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

        // Cache featured products (scoped per tenant so one tenant's featured
        // products/categories/testimonials never leak into another's storefront)
        if (! app()->runningInConsole() && Tenancy::check()) {
            Cache::remember(Tenancy::cacheKey('products.featured'), 3600, function () {
                return Product::where('is_active', true)
                    ->where('is_featured', true)
                    ->orderBy('order')
                    ->get();
            });

            Cache::remember(Tenancy::cacheKey('categories.all'), 3600, function () {
                return Category::where('is_active', true)
                    ->orderBy('order')
                    ->get();
            });

            Cache::remember(Tenancy::cacheKey('testimonials.active'), 3600, function () {
                return Testimonial::where('is_active', true)
                    ->orderBy('order')
                    ->limit(6)
                    ->get();
            });
        }
    }
}
