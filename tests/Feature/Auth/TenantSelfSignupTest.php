<?php

use App\Models\PlatformSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\WelcomeTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

/**
 * tests/Pest.php's global beforeEach binds 'currentTenant' to the shared
 * 'default' tenant for EVERY Feature test (via a real Domain row for
 * "localhost"), so most tests default to a tenant context. These tests need
 * the opposite — a request that resolves to NO tenant, as a real request to
 * the platform's central domain would — so they explicitly forget that
 * binding and target 127.0.0.1, which is also a config('tenancy.central_domains')
 * entry but has no seeded Domain row pointing anywhere.
 */
function actingOnCentralDomain(): void
{
    app()->forgetInstance('currentTenant');
}

it('creates a new tenant and owner when registering on the central domain', function () {
    actingOnCentralDomain();

    $response = $this->withHeaders(['Host' => '127.0.0.1'])
        ->post('http://127.0.0.1/register', [
            'store_name' => 'Acme Goods',
            'name' => 'Jane Owner',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $tenant = Tenant::where('slug', 'acme-goods')->first();
    expect($tenant)->not->toBeNull();

    $owner = User::where('email', 'jane@example.com')->first();
    expect($owner)->not->toBeNull()
        ->and($owner->tenant_id)->toBe($tenant->id)
        ->and($tenant->owner_user_id)->toBe($owner->id)
        ->and(Gate::forUser($owner)->allows('access platform'))->toBeFalse();

    // Spatie's teams mode scopes role checks to whatever team is currently active
    // on the PermissionRegistrar — a real subsequent request to this tenant's own
    // domain would have ResolveTenant set that scope; simulate it here.
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
    expect($owner->hasRole('admin'))->toBeTrue();

    $response->assertRedirect($tenant->primaryUrl().'/login?welcome=1');
});

it('sends a welcome notification and starts the default trial period', function () {
    actingOnCentralDomain();
    Notification::fake();
    PlatformSetting::setMany(['default_trial_days' => '14']);

    $this->withHeaders(['Host' => '127.0.0.1'])
        ->post('http://127.0.0.1/register', [
            'store_name' => 'Trial Co',
            'name' => 'Trial Owner',
            'email' => 'trial-owner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $tenant = Tenant::where('slug', 'trial-co')->first();
    $owner = User::where('email', 'trial-owner@example.com')->first();

    Notification::assertSentTo($owner, WelcomeTenant::class);
    expect($tenant->trial_ends_at?->diffInDays(now()->addDays(14)))->toBeLessThanOrEqual(1);
});

it('blocks registration on an existing tenant subdomain', function () {
    $usersBefore = User::count();
    $tenantsBefore = Tenant::count();

    $response = $this->withHeaders(['Host' => 'default.localhost'])
        ->post('http://default.localhost/register', [
            'name' => 'Someone',
            'email' => 'someone@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $response->assertSessionHasErrors('store_name');
    expect(User::count())->toBe($usersBefore);
    expect(Tenant::count())->toBe($tenantsBefore);
});

it('gives duplicate store names unique slugs', function () {
    actingOnCentralDomain();

    $this->withHeaders(['Host' => '127.0.0.1'])->post('http://127.0.0.1/register', [
        'store_name' => 'Acme',
        'name' => 'First Owner',
        'email' => 'first@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Registering the first owner also logs them in (Fortify's own controller
    // behavior); /register sits behind 'guest' middleware, so without logging
    // back out, this second request would just get redirected away as an
    // already-authenticated user instead of ever reaching CreateNewUser.
    auth()->logout();
    actingOnCentralDomain();

    $this->withHeaders(['Host' => '127.0.0.1'])->post('http://127.0.0.1/register', [
        'store_name' => 'Acme',
        'name' => 'Second Owner',
        'email' => 'second@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect(Tenant::where('slug', 'acme')->exists())->toBeTrue();
    expect(Tenant::where('slug', 'acme-2')->exists())->toBeTrue();
});
