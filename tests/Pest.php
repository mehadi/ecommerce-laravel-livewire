<?php

use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

uses(TestCase::class)->in('Unit');

// Every Feature test's HTTP requests hit Host: default.localhost (see TestCase::$baseUrl),
// which App\Http\Middleware\ResolveTenant resolves via the "{slug}.{central domain}"
// subdomain scheme — the same path real tenant traffic takes. Seed the matching 'default'
// tenant and set it as the current permissions team, so both HTTP-level requests and
// direct role/permission checks in test code (Livewire::test(), which never goes through
// HTTP middleware) see a consistent tenant. Guarded by hasTable so it's a no-op for any
// Feature test that (unusually) doesn't use RefreshDatabase / has no tenants table.
uses(TestCase::class)->beforeEach(function () {
    // Settings are memoized in a static PHP array in front of the cache store
    // (see App\Models\Setting/PlatformSetting) for performance. RefreshDatabase
    // rolls back each test's DB changes but doesn't touch that cache, so without
    // this reset a value committed in one test can leak into the next.
    \Illuminate\Support\Facades\Cache::flush();
    \App\Models\Setting::flushCache();
    \App\Models\PlatformSetting::flushCache();

    if (! \Illuminate\Support\Facades\Schema::hasTable('tenants')) {
        return;
    }

    // Reuses the same tenant slug DefaultTenantSeeder creates, so tests that assume a
    // pre-seeded 'admin'/'super admin' role already exists (a holdover from when roles
    // weren't team-scoped) keep working against a real seeded dev database, while also
    // being self-sufficient (firstOrCreate) for a genuinely fresh/CI database.
    $tenant = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);

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
