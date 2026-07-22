<?php

declare(strict_types=1);

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

test('super admin can create a permission', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('createPermission')
        ->assertSet('showModal', true)
        ->set('name', 'create.products')
        ->call('savePermission')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect(Permission::where('name', 'create.products')->exists())->toBeTrue();
});

test('super admin can edit a permission', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    $permission = Permission::create(['name' => 'create.products', 'guard_name' => 'web']);

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('editPermission', $permission)
        ->assertSet('name', 'create.products')
        ->set('name', 'edit.products')
        ->call('savePermission')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $permission->refresh();
    expect($permission->name)->toBe('edit.products');
});

test('super admin can delete a permission', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('deletePermission', $permission->id)
        ->assertHasNoErrors();

    expect(Permission::where('id', $permission->id)->exists())->toBeFalse();
});

test('permission name is required', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('createPermission')
        ->set('name', '')
        ->call('savePermission')
        ->assertHasErrors(['name']);
});

test('permission name must be unique', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    Permission::create(['name' => 'create.products', 'guard_name' => 'web']);

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('createPermission')
        ->set('name', 'create.products')
        ->call('savePermission')
        ->assertHasErrors(['name']);
});

test('tenant admin cannot create a permission', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('name', 'create.products')
        ->call('savePermission')
        ->assertForbidden();

    expect(Permission::where('name', 'create.products')->exists())->toBeFalse();
});

test('tenant admin cannot rename a permission', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'create.products', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editPermission', $permission)
        ->set('name', 'edit.products')
        ->call('savePermission')
        ->assertForbidden();

    expect($permission->refresh()->name)->toBe('create.products');
});

test('tenant admin cannot delete a permission', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('deletePermission', $permission->id)
        ->assertForbidden();

    expect(Permission::where('id', $permission->id)->exists())->toBeTrue();
});

test('tenant admin does not see mutation controls on the permissions index', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertDontSee(__('Add New Permission'))
        ->assertDontSeeHtml('wire:click="editPermission(');
});

test('super admin cannot rename a permission that is hardcoded into a Gate closure', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    $permission = Permission::firstOrCreate(['name' => 'view products', 'guard_name' => 'web']);

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('editPermission', $permission)
        ->set('name', 'view catalog')
        ->call('savePermission');

    expect($permission->refresh()->name)->toBe('view products');
});

test('super admin cannot delete a permission that is hardcoded into a Gate closure', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']));

    $permission = Permission::firstOrCreate(['name' => 'manage pos settings', 'guard_name' => 'web']);

    Livewire::actingAs($superAdmin)
        ->test(Index::class)
        ->call('deletePermission', $permission->id);

    expect(Permission::where('id', $permission->id)->exists())->toBeTrue();
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
    Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    $manager->assignRole('manager');

    actingAs($manager)
        ->get(route('admin.roles.index'))
        ->assertForbidden();

    actingAs($manager)
        ->get(route('admin.permissions.index'))
        ->assertForbidden();
});
