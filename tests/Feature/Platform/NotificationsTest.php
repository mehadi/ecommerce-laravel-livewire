<?php

declare(strict_types=1);

use App\Console\Commands\NotifyTenantsOfExpiringTrials;
use App\Livewire\Admin\Billing\Index as AdminBillingIndex;
use App\Livewire\Platform\Tenants\Show as TenantsShow;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantReactivated;
use App\Notifications\TenantSuspended;
use App\Notifications\TrialEndingSoon;
use App\Notifications\UpgradeRequestApproved;
use App\Notifications\UpgradeRequestRejected;
use App\Notifications\UpgradeRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function platformStaff(): User
{
    return User::factory()->create(['tenant_id' => null]);
}

test('requesting an upgrade notifies platform staff, not the requesting tenant', function () {
    Notification::fake();

    // Reuses tests/Pest.php's shared 'default' tenant (already bound as the
    // current tenant + permissions team), so actingAsAdmin()'s 'admin' role
    // assignment lands in the right scope for Gate::authorize('access admin').
    $tenant = Tenant::first();
    $currentPlan = Plan::create(['name' => 'Starter', 'slug' => 'starter']);
    $desiredPlan = Plan::create(['name' => 'Growth', 'slug' => 'growth']);
    $tenant->update(['plan_id' => $currentPlan->id]);

    $tenantAdmin = actingAsAdmin();
    $staff = platformStaff();

    Livewire::actingAs($tenantAdmin)
        ->test(AdminBillingIndex::class)
        ->set('desiredPlanId', $desiredPlan->id)
        ->call('requestUpgrade')
        ->assertHasNoErrors();

    Notification::assertSentTo($staff, UpgradeRequestSubmitted::class);
    Notification::assertNotSentTo($tenantAdmin, UpgradeRequestSubmitted::class);

    expect($tenant->fresh()->desired_plan_id)->toBe($desiredPlan->id);
});

test('approving an upgrade notifies the tenant owner and swaps the plan', function () {
    Notification::fake();

    $platformAdmin = platformStaff();
    $currentPlan = Plan::create(['name' => 'Starter', 'slug' => 'starter-2']);
    $desiredPlan = Plan::create(['name' => 'Growth', 'slug' => 'growth-2']);

    $tenant = Tenant::create(['name' => 'Acme', 'slug' => 'acme-approve', 'status' => 'active']);
    $owner = User::factory()->create(['tenant_id' => $tenant->id]);
    $tenant->update([
        'owner_user_id' => $owner->id,
        'plan_id' => $currentPlan->id,
        'desired_plan_id' => $desiredPlan->id,
        'upgrade_requested_at' => now(),
    ]);

    Livewire::actingAs($platformAdmin)
        ->test(TenantsShow::class, ['tenant' => $tenant])
        ->call('approveUpgrade')
        ->assertHasNoErrors();

    Notification::assertSentTo($owner, UpgradeRequestApproved::class);

    $tenant->refresh();
    expect($tenant->plan_id)->toBe($desiredPlan->id)
        ->and($tenant->desired_plan_id)->toBeNull()
        ->and($tenant->upgrade_requested_at)->toBeNull();
});

test('rejecting an upgrade notifies the tenant owner and clears the request', function () {
    Notification::fake();

    $platformAdmin = platformStaff();
    $desiredPlan = Plan::create(['name' => 'Growth', 'slug' => 'growth-3']);

    $tenant = Tenant::create(['name' => 'Acme', 'slug' => 'acme-reject', 'status' => 'active']);
    $owner = User::factory()->create(['tenant_id' => $tenant->id]);
    $tenant->update([
        'owner_user_id' => $owner->id,
        'desired_plan_id' => $desiredPlan->id,
        'upgrade_requested_at' => now(),
    ]);

    Livewire::actingAs($platformAdmin)
        ->test(TenantsShow::class, ['tenant' => $tenant])
        ->call('rejectUpgrade')
        ->assertHasNoErrors();

    Notification::assertSentTo($owner, UpgradeRequestRejected::class);

    $tenant->refresh();
    expect($tenant->desired_plan_id)->toBeNull()
        ->and($tenant->upgrade_requested_at)->toBeNull();
});

test('suspending a tenant notifies its owner with the reason', function () {
    Notification::fake();

    $platformAdmin = platformStaff();
    $tenant = Tenant::create(['name' => 'Acme', 'slug' => 'acme-suspend', 'status' => 'active']);
    $owner = User::factory()->create(['tenant_id' => $tenant->id]);
    $tenant->update(['owner_user_id' => $owner->id]);

    Livewire::actingAs($platformAdmin)
        ->test(TenantsShow::class, ['tenant' => $tenant])
        ->set('suspendReason', 'Unpaid invoice for 30 days')
        ->call('suspend')
        ->assertHasNoErrors();

    Notification::assertSentTo($owner, TenantSuspended::class);
    expect($tenant->fresh()->status)->toBe('suspended');
});

test('reactivating a tenant notifies its owner', function () {
    Notification::fake();

    $platformAdmin = platformStaff();
    $tenant = Tenant::create(['name' => 'Acme', 'slug' => 'acme-reactivate', 'status' => 'suspended']);
    $owner = User::factory()->create(['tenant_id' => $tenant->id]);
    $tenant->update(['owner_user_id' => $owner->id]);

    Livewire::actingAs($platformAdmin)
        ->test(TenantsShow::class, ['tenant' => $tenant])
        ->call('reactivate')
        ->assertHasNoErrors();

    Notification::assertSentTo($owner, TenantReactivated::class);
    expect($tenant->fresh()->status)->toBe('active');
});

test('the trial-ending sweep notifies only tenants inside the window and does not repeat', function () {
    Notification::fake();

    $endingSoon = Tenant::create([
        'name' => 'Ending Soon',
        'slug' => 'ending-soon',
        'status' => 'active',
        'trial_ends_at' => now()->addDays(2),
    ]);
    $endingSoonOwner = User::factory()->create(['tenant_id' => $endingSoon->id]);
    $endingSoon->update(['owner_user_id' => $endingSoonOwner->id]);

    $farFuture = Tenant::create([
        'name' => 'Far Future',
        'slug' => 'far-future',
        'status' => 'active',
        'trial_ends_at' => now()->addDays(10),
    ]);
    $farFutureOwner = User::factory()->create(['tenant_id' => $farFuture->id]);
    $farFuture->update(['owner_user_id' => $farFutureOwner->id]);

    $this->artisan(NotifyTenantsOfExpiringTrials::class)->assertSuccessful();

    Notification::assertSentTo($endingSoonOwner, TrialEndingSoon::class);
    Notification::assertNotSentTo($farFutureOwner, TrialEndingSoon::class);
    expect($endingSoon->fresh()->trial_ending_notified_at)->not->toBeNull();

    // Running it again must not re-notify an already-notified tenant.
    Notification::fake();
    $this->artisan(NotifyTenantsOfExpiringTrials::class)->assertSuccessful();
    Notification::assertNotSentTo($endingSoonOwner, TrialEndingSoon::class);
});

test('extending a trial re-arms the trial-ending reminder', function () {
    $platformAdmin = platformStaff();
    $tenant = Tenant::create([
        'name' => 'Acme',
        'slug' => 'acme-trial-extend',
        'status' => 'active',
        'trial_ends_at' => now()->addDay(),
        'trial_ending_notified_at' => now(),
    ]);

    Livewire::actingAs($platformAdmin)
        ->test(TenantsShow::class, ['tenant' => $tenant])
        ->set('trial_ends_at', now()->addDays(30)->format('Y-m-d'))
        ->call('updateSubscription')
        ->assertHasNoErrors();

    expect($tenant->fresh()->trial_ending_notified_at)->toBeNull();
});
