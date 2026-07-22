<?php

declare(strict_types=1);

use App\Livewire\Admin\Users\Index;
use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('admin can assign the admin role to a user', function () {
    (new RolesPermissionsSeeder)->run();
    $admin = actingAsAdmin();

    $adminRole = Role::where('name', 'admin')->firstOrFail();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openModal')
        ->set('name', 'New Admin')
        ->set('email', 'new-admin@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('selectedRoles', [(string) $adminRole->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $user = User::where('email', 'new-admin@example.com')->firstOrFail();
    expect($user->hasRole('admin'))->toBeTrue();
});

test('a manager without manage roles permission cannot grant the admin role when creating a user', function () {
    (new RolesPermissionsSeeder)->run();

    $adminRole = Role::where('name', 'admin')->firstOrFail();
    $managerRole = Role::where('name', 'manager')->firstOrFail();

    $manager = User::factory()->create();
    $manager->assignRole($managerRole);

    Livewire::actingAs($manager)
        ->test(Index::class)
        ->call('openModal')
        ->set('name', 'Sneaky Escalation')
        ->set('email', 'sneaky@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('selectedRoles', [(string) $adminRole->id])
        ->call('save')
        ->assertForbidden();

    expect(User::where('email', 'sneaky@example.com')->exists())->toBeFalse();
});

test('a manager without manage roles permission cannot grant the admin role when editing a user', function () {
    (new RolesPermissionsSeeder)->run();

    $adminRole = Role::where('name', 'admin')->firstOrFail();
    $managerRole = Role::where('name', 'manager')->firstOrFail();

    $manager = User::factory()->create();
    $manager->assignRole($managerRole);

    $target = User::factory()->create();

    Livewire::actingAs($manager)
        ->test(Index::class)
        ->call('openModal', $target->id)
        ->set('selectedRoles', [(string) $adminRole->id])
        ->call('save')
        ->assertForbidden();

    expect($target->fresh()->hasRole('admin'))->toBeFalse();
});

test('a manager without manage roles permission can still assign a non-privileged role', function () {
    (new RolesPermissionsSeeder)->run();

    $managerRole = Role::where('name', 'manager')->firstOrFail();
    $editorRole = Role::where('name', 'editor')->firstOrFail();

    $manager = User::factory()->create();
    $manager->assignRole($managerRole);

    $target = User::factory()->create();

    Livewire::actingAs($manager)
        ->test(Index::class)
        ->call('openModal', $target->id)
        ->set('selectedRoles', [(string) $editorRole->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect($target->fresh()->hasRole('editor'))->toBeTrue();
});

test('a user with an open pos shift cannot be hard-deleted', function () {
    (new RolesPermissionsSeeder)->run();
    $admin = actingAsAdmin();

    $cashier = User::factory()->create();
    $register = PosRegister::create([
        'name' => 'Till', 'code' => 'T1', 'warehouse_id' => Warehouse::default()->id, 'is_active' => true,
    ]);
    PosShift::create([
        'register_id' => $register->id, 'opened_by' => $cashier->id, 'opening_cash' => 100,
        'status' => 'open', 'opened_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $cashier->id);

    expect(User::where('id', $cashier->id)->exists())->toBeTrue();
});

test('a user without pos history can still be deleted', function () {
    (new RolesPermissionsSeeder)->run();
    $admin = actingAsAdmin();

    $plainUser = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $plainUser->id);

    expect(User::where('id', $plainUser->id)->exists())->toBeFalse();
});
