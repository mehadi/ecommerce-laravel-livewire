<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Real permission checks (as opposed to the hasRole()-only short-circuit that
// lets actingAsAdmin() work without seeding) need the actual permission rows
// to exist, or Spatie's hasPermissionTo() throws PermissionDoesNotExist rather
// than returning false. Running the real seeder is more realistic than
// hand-rolling permission rows anyway — it's exactly what a real tenant has.
beforeEach(function () {
    (new RolesPermissionsSeeder)->run();
});

if (! function_exists('actingAsCashier')) {
    function actingAsCashier(): User
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        return $cashier;
    }
}

test('a cashier can reach the POS gate but not the backoffice admin gate', function () {
    $cashier = actingAsCashier();

    expect($cashier->can('access pos'))->toBeTrue();
    expect($cashier->can('access admin'))->toBeFalse();
});

test('a cashier cannot process refunds, force-close shifts, or manage POS registers/settings', function () {
    $cashier = actingAsCashier();

    expect($cashier->can('process pos refunds'))->toBeFalse();
    expect($cashier->can('force close pos shift'))->toBeFalse();
    expect($cashier->can('manage pos registers'))->toBeFalse();
    expect($cashier->can('manage pos settings'))->toBeFalse();
});

test('a cashier can process sales, hold sales, and manage their own cash drawer', function () {
    $cashier = actingAsCashier();

    expect($cashier->can('process pos sales'))->toBeTrue();
    expect($cashier->can('hold pos sales'))->toBeTrue();
    expect($cashier->can('manage cash drawer'))->toBeTrue();
    expect($cashier->can('open pos shift'))->toBeTrue();
    expect($cashier->can('close pos shift'))->toBeTrue();
});

test('a manager has POS access including refunds and registers, but not tenant-wide POS settings', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    expect($manager->can('access pos'))->toBeTrue();
    expect($manager->can('process pos refunds'))->toBeTrue();
    expect($manager->can('manage pos registers'))->toBeTrue();
    expect($manager->can('manage pos settings'))->toBeFalse();
});

test('an admin has full POS access including refunds, registers, and settings', function () {
    $admin = actingAsAdmin();

    expect($admin->can('access pos'))->toBeTrue();
    expect($admin->can('process pos refunds'))->toBeTrue();
    expect($admin->can('manage pos registers'))->toBeTrue();
    expect($admin->can('manage pos settings'))->toBeTrue();
});

test('a plain user with no role cannot reach the POS gate', function () {
    $user = User::factory()->create();

    expect($user->can('access pos'))->toBeFalse();
});
