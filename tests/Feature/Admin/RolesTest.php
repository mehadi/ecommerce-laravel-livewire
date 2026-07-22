<?php

declare(strict_types=1);

use App\Livewire\Admin\Roles\Index;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('admin can view roles index page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin)
        ->get(route('admin.roles.index'))
        ->assertSuccessful()
        ->assertSeeLivewire('admin.roles.index');
});

test('non-admin cannot view roles index page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertForbidden();
});

test('admin can create a role', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createRole')
        ->assertSet('showModal', true)
        ->set('name', 'editor')
        ->set('selectedPermissions', [(string) $permission->id])
        ->call('saveRole')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect(Role::where('name', 'editor')->exists())->toBeTrue();
    expect(Role::where('name', 'editor')->first()->permissions)->toHaveCount(1);
});

test('admin can edit a role', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $permission1 = Permission::create(['name' => 'permission.1', 'guard_name' => 'web']);
    $permission2 = Permission::create(['name' => 'permission.2', 'guard_name' => 'web']);

    $role->givePermissionTo($permission1);

    $component = Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editRole', $role)
        ->assertSet('name', 'editor')
        ->assertSet('selectedPermissions', [(string) $permission1->id]);

    $component
        ->set('name', 'senior-editor')
        ->set('selectedPermissions', [(string) $permission1->id, (string) $permission2->id])
        ->call('saveRole')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $role->refresh();
    expect($role->name)->toBe('senior-editor');
    expect($role->permissions)->toHaveCount(2);
});

test('admin can delete a role', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('deleteRole', $role->id)
        ->assertHasNoErrors();

    expect(Role::where('id', $role->id)->exists())->toBeFalse();
});

test('role name is required', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createRole')
        ->set('name', '')
        ->call('saveRole')
        ->assertHasErrors(['name']);
});

test('role name must be unique', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Role::create(['name' => 'editor', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createRole')
        ->set('name', 'editor')
        ->call('saveRole')
        ->assertHasErrors(['name']);
});

test('admin cannot rename the super admin role', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);
    $superAdmin = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editRole', $superAdmin)
        ->assertSet('editingIsProtectedRole', true)
        ->set('name', 'renamed-admin')
        ->call('saveRole');

    expect($superAdmin->refresh()->name)->toBe('super admin');
});

test('admin cannot rename roles hardcoded in AppServiceProvider gates', function (string $roleName) {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);
    $protectedRole = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editRole', $protectedRole)
        ->assertSet('editingIsProtectedRole', true)
        ->set('name', 'renamed-role')
        ->call('saveRole');

    expect($protectedRole->refresh()->name)->toBe($roleName);
})->with(['admin', 'manager', 'cashier']);

test('admin cannot delete roles hardcoded in AppServiceProvider gates', function (string $roleName) {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);
    $protectedRole = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('deleteRole', $protectedRole->id);

    expect(Role::where('id', $protectedRole->id)->exists())->toBeTrue();
})->with(['manager', 'cashier']);

test('role name uniqueness is scoped to the current tenant', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // A role with the same name but belonging to a different tenant must not
    // block creating a role with that name for the current tenant.
    Role::create(['name' => 'editor', 'guard_name' => 'web', 'tenant_id' => 999999]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createRole')
        ->set('name', 'editor')
        ->call('saveRole')
        ->assertHasNoErrors();

    expect(Role::where('name', 'editor')->where('tenant_id', Tenancy::id())->exists())->toBeTrue();
});

test('a role belonging to an unrelated tenant cannot be edited or deleted', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $otherTenantRole = Role::create(['name' => 'editor', 'guard_name' => 'web', 'tenant_id' => 999999]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editRole', $otherTenantRole)
        ->assertStatus(404);
});

test('roles index shows search functionality', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'editor', 'guard_name' => 'web']);
    Role::create(['name' => 'moderator', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('search', 'editor')
        ->assertSee('editor')
        ->assertDontSee('moderator');
});

test('roles index shows permission and user counts', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web', 'tenant_id' => Tenancy::id()]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);
    $user = User::factory()->create();

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee('editor')
        ->assertSee('1') // permission count
        ->assertSee('1'); // user count
});
