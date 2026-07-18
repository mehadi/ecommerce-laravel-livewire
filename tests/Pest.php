<?php

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

uses(TestCase::class)->in('Unit');

// Every Feature test's HTTP requests default to Host: localhost. Since routes/web.php
// wraps the storefront/dashboard/admin routes in tenant-resolution middleware, every
// such test needs *some* tenant bound or it 404s. Map localhost -> one shared test
// tenant, and set it as the current permissions team, so both HTTP-level requests
// (resolved by App\Http\Middleware\ResolveTenant) and direct role/permission checks
// in test code see a consistent tenant. Guarded by hasTable so it's a no-op for any
// Feature test that (unusually) doesn't use RefreshDatabase / has no tenants table.
uses(TestCase::class)->beforeEach(function () {
    if (! \Illuminate\Support\Facades\Schema::hasTable('tenants')) {
        return;
    }

    // Reuses the same tenant slug DefaultTenantSeeder creates, so tests that assume a
    // pre-seeded 'admin'/'super admin' role already exists (a holdover from when roles
    // weren't team-scoped) keep working against a real seeded dev database, while also
    // being self-sufficient (firstOrCreate) for a genuinely fresh/CI database.
    $tenant = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);

    Domain::firstOrCreate(['domain' => 'localhost'], ['tenant_id' => $tenant->id, 'verified_at' => now()]);

    app()->instance('currentTenant', $tenant);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
})->in('Feature');

if (! function_exists('actingAsAdmin')) {
    function actingAsAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }
}
