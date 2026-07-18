<?php

declare(strict_types=1);

use App\Livewire\Admin\Roles\Create;
use App\Livewire\Admin\Roles\Edit;
use App\Livewire\Admin\Roles\Index;
use App\Models\User;
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
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

    actingAs($admin)
        ->get(route('admin.roles.create'))
        ->assertSuccessful()
        ->assertSeeLivewire('admin.roles.create');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', 'editor')
        ->set('selectedPermissions', [(string) $permission->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.roles.index'));

    expect(Role::where('name', 'editor')->exists())->toBeTrue();
    expect(Role::where('name', 'editor')->first()->permissions)->toHaveCount(1);
});

test('admin can edit a role', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $permission1 = Permission::create(['name' => 'permission.1', 'guard_name' => 'web']);
    $permission2 = Permission::create(['name' => 'permission.2', 'guard_name' => 'web']);

    $role->givePermissionTo($permission1);

    actingAs($admin)
        ->get(route('admin.roles.edit', $role))
        ->assertSuccessful()
        ->assertSeeLivewire('admin.roles.edit');

    $component = Livewire::actingAs($admin)
        ->test(Edit::class, ['role' => $role])
        ->assertSet('name', 'editor')
        ->assertSet('selectedPermissions', [(string) $permission1->id]);

    $component
        ->set('name', 'senior-editor')
        ->set('selectedPermissions', [(string) $permission1->id, (string) $permission2->id])
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.roles.index'));

    $role->refresh();
    expect($role->name)->toBe('senior-editor');
    expect($role->permissions)->toHaveCount(2);
});

test('admin can delete a role', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

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
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('role name must be unique', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Role::create(['name' => 'editor', 'guard_name' => 'web']);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name', 'editor')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('roles index shows search functionality', function () {
    // Ensure admin role exists
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

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
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

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
