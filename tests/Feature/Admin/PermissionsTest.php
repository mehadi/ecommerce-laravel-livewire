<?php

declare(strict_types=1);

use App\Livewire\Admin\Permissions\Create;
use App\Livewire\Admin\Permissions\Edit;
use App\Livewire\Admin\Permissions\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('admin can view permissions index page', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin)
        ->get(route('admin.permissions.index'))
        ->assertSuccessful()
        ->assertSeeLivewire('admin.permissions.index');
});

test('non-admin cannot view permissions index page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertForbidden();
});

test('admin can create a permission', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin)
        ->get(route('admin.permissions.create'))
        ->assertSuccessful()
        ->assertSeeLivewire('admin.permissions.create');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', 'create.products')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.permissions.index'));

    expect(Permission::where('name', 'create.products')->exists())->toBeTrue();
});

test('admin can edit a permission', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'create.products', 'guard_name' => 'web']);

    actingAs($admin)
        ->get(route('admin.permissions.edit', $permission))
        ->assertSuccessful()
        ->assertSeeLivewire('admin.permissions.edit');

    Livewire::actingAs($admin)
        ->test(Edit::class, ['permission' => $permission])
        ->set('name', 'edit.products')
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.permissions.index'));

    $permission->refresh();
    expect($permission->name)->toBe('edit.products');
});

test('admin can delete a permission', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('deletePermission', $permission->id)
        ->assertHasNoErrors();

    expect(Permission::where('id', $permission->id)->exists())->toBeFalse();
});

test('permission name is required', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('permission name must be unique', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Permission::create(['name' => 'create.products', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', 'create.products')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('permissions index shows search functionality', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Permission::create(['name' => 'create.products', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit.products', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete.users', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('search', 'products')
        ->assertSee('create.products')
        ->assertSee('edit.products')
        ->assertDontSee('delete.users');
});

test('permissions index shows role counts', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);
    $role1 = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $role2 = Role::create(['name' => 'moderator', 'guard_name' => 'web']);

    $role1->givePermissionTo($permission);
    $role2->givePermissionTo($permission);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee('test.permission')
        ->assertSee('2'); // role count
});

test('manager role cannot access roles and permissions', function () {
    $manager = User::factory()->create();
    $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    $manager->assignRole('manager');

    actingAs($manager)
        ->get(route('admin.roles.index'))
        ->assertForbidden();

    actingAs($manager)
        ->get(route('admin.permissions.index'))
        ->assertForbidden();
});
