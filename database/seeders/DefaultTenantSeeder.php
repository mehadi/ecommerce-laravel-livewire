<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DefaultTenantSeeder extends Seeder
{
    /**
     * Creates the single dev tenant that owns all of the existing demo data, and binds
     * it into the container so every seeder that runs after this one (via DatabaseSeeder)
     * automatically tenant-scopes what it creates — Eloquent creates get tenant_id
     * auto-stamped by the BelongsToTenant trait, and spatie/laravel-permission Role
     * creates get team_id auto-stamped via setPermissionsTeamId(). Reachable locally at
     * "{slug}.{a CENTRAL_DOMAINS entry}", e.g. default.localhost, with no Domain row
     * needed (subdomain resolution matches on Tenant::slug directly).
     */
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'default'],
            ['name' => 'Default Store', 'status' => 'active']
        );

        app()->instance('currentTenant', $tenant);
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

        // Backfill rows written before any tenant existed (currently just the
        // navbar_components defaults inserted directly by their create-table migration).
        foreach ($this->tenantOwnedTables() as $table) {
            \Illuminate\Support\Facades\DB::table($table)
                ->whereNull('tenant_id')
                ->update(['tenant_id' => $tenant->id]);
        }
    }

    /**
     * @return list<string>
     */
    protected function tenantOwnedTables(): array
    {
        return [
            'products',
            'product_attributes', 'attributes', 'attribute_values', 'categories', 'orders', 'order_items',
            'coupons', 'testimonials', 'navbar_components', 'navigation_items', 'navigation_categories',
            'landing_pages', 'landing_page_sections', 'user_dashboard_preferences', 'settings',
            'shipping_settings', 'shipping_city_rates',
        ];
    }
}
