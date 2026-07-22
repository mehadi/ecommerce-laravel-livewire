<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductBatch;
use App\Models\Testimonial;
use App\Models\WarehouseStock;
use App\Observers\OrderObserver;
use App\Observers\ProductAttributeObserver;
use App\Observers\ProductBatchObserver;
use App\Observers\ProductObserver;
use App\Observers\WarehouseStockObserver;
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
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        ProductAttribute::observe(ProductAttributeObserver::class);
        WarehouseStock::observe(WarehouseStockObserver::class);
        ProductBatch::observe(ProductBatchObserver::class);

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

        // Inventory gates (mirrors the 'manage users' shape so seeded manager/admin
        // roles keep working even though tests' actingAsAdmin() helper never runs
        // RolesPermissionsSeeder)
        Gate::define('view inventory', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('view inventory');
        });

        Gate::define('adjust stock', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('adjust stock');
        });

        Gate::define('manage inventory settings', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('manage inventory settings');
        });

        // Product catalog gates (same shape as the inventory gates above).
        // These back the seeded 'view/create/edit/delete products' permissions,
        // which the Roles UI exposes but nothing enforced until now.
        // checkPermissionTo (not hasPermissionTo) so an unseeded permission
        // reads as "denied" instead of throwing PermissionDoesNotExist.
        Gate::define('view products', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('view products') === true;
        });

        Gate::define('create products', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('create products') === true;
        });

        Gate::define('edit products', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('edit products') === true;
        });

        Gate::define('delete products', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('delete products') === true;
        });

        // Category gates (same shape as the product catalog gates above).
        // Categories underpins Products, Navigation, LandingPages, and Sections,
        // so its own mutating actions need the same enforcement.
        Gate::define('view categories', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('view categories') === true;
        });

        Gate::define('create categories', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('create categories') === true;
        });

        Gate::define('edit categories', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('edit categories') === true;
        });

        Gate::define('delete categories', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('delete categories') === true;
        });

        // Warehouse gates (same shape as the product catalog gates above).
        // Warehouses is the foundational table every stock-related module
        // (inventory, transfers, purchase orders, cycle counts, POS) reads
        // from, so its own CRUD needs the same enforcement.
        Gate::define('view warehouses', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('view warehouses') === true;
        });

        Gate::define('create warehouses', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('create warehouses') === true;
        });

        Gate::define('edit warehouses', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('edit warehouses') === true;
        });

        Gate::define('delete warehouses', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('delete warehouses') === true;
        });

        // Supplier gates (same shape as the warehouse gates above). Backs the
        // seeded 'view/create/edit/delete suppliers' permissions.
        Gate::define('view suppliers', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('view suppliers') === true;
        });

        Gate::define('create suppliers', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('create suppliers') === true;
        });

        Gate::define('edit suppliers', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('edit suppliers') === true;
        });

        Gate::define('delete suppliers', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('delete suppliers') === true;
        });

        // Cycle count gates (same shape as the warehouse/supplier gates above).
        // Backs the seeded 'view/create/complete cycle counts' permissions —
        // 'complete cycle counts' also covers saving in-progress counts, since
        // that's the only mutating permission the counting screen has.
        Gate::define('view cycle counts', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('view cycle counts') === true;
        });

        Gate::define('create cycle counts', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('create cycle counts') === true;
        });

        Gate::define('complete cycle counts', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->checkPermissionTo('complete cycle counts') === true;
        });

        // POS gates. 'access pos' (and the cashier-tier actions below it) include
        // the 'cashier' role explicitly, since a cashier account intentionally
        // cannot reach 'access admin' — POS routes are gated separately from the
        // rest of the backoffice (see routes/web.php's 'pos' group).
        Gate::define('access pos', function ($user) {
            return $user->tenant_id !== null && (
                $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier')
                || $user->hasPermissionTo('access pos')
            );
        });

        Gate::define('process pos sales', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('process pos sales');
        });

        Gate::define('apply pos discounts', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('apply pos discounts');
        });

        Gate::define('hold pos sales', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('hold pos sales');
        });

        Gate::define('void pos sale line', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('void pos sale line');
        });

        Gate::define('open pos shift', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('open pos shift');
        });

        Gate::define('close pos shift', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('close pos shift');
        });

        Gate::define('manage cash drawer', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('cashier') || $user->hasPermissionTo('manage cash drawer');
        });

        // Manager-and-above-only POS actions — deliberately excludes 'cashier'.
        Gate::define('void pos sale', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('void pos sale');
        });

        Gate::define('process pos refunds', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('process pos refunds');
        });

        Gate::define('force close pos shift', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('force close pos shift');
        });

        Gate::define('view pos reports', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('view pos reports');
        });

        Gate::define('manage pos registers', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasRole('manager') || $user->hasPermissionTo('manage pos registers');
        });

        // Admin/super-admin only — tenant-wide POS settings.
        Gate::define('manage pos settings', function ($user) {
            return $user->hasRole('super admin') || $user->hasRole('admin') || $user->hasPermissionTo('manage pos settings');
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
