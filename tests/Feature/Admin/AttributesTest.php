<?php

declare(strict_types=1);

use App\Livewire\Admin\Attributes\Index;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

test('adding a duplicate attribute value returns a validation error instead of a 500', function () {
    $admin = actingAsAdmin();
    $attribute = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);
    AttributeValue::create(['attribute_id' => $attribute->id, 'value' => 'Red']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openValuesModal', $attribute->id)
        ->set('valueName', 'Red')
        ->call('addValue')
        ->assertHasErrors(['valueName' => 'unique']);

    expect(AttributeValue::where('attribute_id', $attribute->id)->where('value', 'Red')->count())->toBe(1);
});

test('a duplicate value for a different attribute is still allowed', function () {
    $admin = actingAsAdmin();
    $color = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);
    $size = Attribute::create(['name' => 'Size', 'slug' => 'size', 'type' => 'text']);
    AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Red']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openValuesModal', $size->id)
        ->set('valueName', 'Red')
        ->call('addValue')
        ->assertHasNoErrors();

    expect(AttributeValue::where('attribute_id', $size->id)->where('value', 'Red')->exists())->toBeTrue();
});

test('attribute search does not leak another tenant\'s rows via the orWhere slug match', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);
    $admin1 = actingAsAdmin();

    Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant2->id);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin2 = User::factory()->create();
    $admin2->assignRole('admin');
    // Other tenant's row whose slug happens to match tenant1's search term.
    Attribute::create(['name' => 'Unrelated', 'slug' => 'color', 'type' => 'text']);

    // Back to tenant1: searching "color" must only ever surface tenant1's own row.
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);

    Livewire::actingAs($admin1)
        ->test(Index::class)
        ->set('search', 'color')
        ->assertViewHas('allAttributes', function ($attributes) use ($tenant1) {
            return $attributes->total() === 1 && $attributes->first()->tenant_id === $tenant1->id;
        });
});

test('attribute slug uniqueness is scoped per tenant, not global', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);
    $admin1 = actingAsAdmin();

    Livewire::actingAs($admin1)
        ->test(Index::class)
        ->set('name', 'Color')
        ->set('slug', 'color')
        ->call('saveAttribute')
        ->assertHasNoErrors();

    expect(Attribute::where('slug', 'color')->where('tenant_id', $tenant1->id)->exists())->toBeTrue();

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant2->id);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin2 = User::factory()->create();
    $admin2->assignRole('admin');

    // Same slug, different tenant: must be allowed, since uniqueness is (tenant_id, slug).
    Livewire::actingAs($admin2)
        ->test(Index::class)
        ->set('name', 'Color')
        ->set('slug', 'color')
        ->call('saveAttribute')
        ->assertHasNoErrors();

    expect(Attribute::where('slug', 'color')->where('tenant_id', $tenant2->id)->exists())->toBeTrue();
    expect(DB::table('attributes')->where('slug', 'color')->count())->toBe(2);

    // But within the same tenant, the slug is still rejected as taken.
    Livewire::actingAs($admin2)
        ->test(Index::class)
        ->set('name', 'Color Again')
        ->set('slug', 'color')
        ->call('saveAttribute')
        ->assertHasErrors('slug');
});

test('an attribute referenced by a live product variant cannot be deleted', function () {
    $admin = actingAsAdmin();
    $attribute = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);
    AttributeValue::create(['attribute_id' => $attribute->id, 'value' => 'Red']);
    $product = Product::factory()->create();
    $product->productAttributes()->create([
        'attribute_data' => ['Color' => 'Red'],
        'price' => 100,
    ]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteAttribute', $attribute->id);

    expect(Attribute::find($attribute->id))->not->toBeNull();
});

test('an attribute referenced by a live product variant cannot be renamed', function () {
    $admin = actingAsAdmin();
    $attribute = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);
    $product = Product::factory()->create();
    $product->productAttributes()->create([
        'attribute_data' => ['Color' => 'Red'],
        'price' => 100,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openEditModal', $attribute->id)
        ->set('name', 'Colour')
        ->set('slug', 'colour')
        ->call('saveAttribute')
        ->assertHasErrors('name');

    expect($attribute->fresh()->name)->toBe('Color');
});

test('an unused attribute can be renamed and deleted', function () {
    $admin = actingAsAdmin();
    $attribute = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openEditModal', $attribute->id)
        ->set('name', 'Colour')
        ->set('slug', 'colour')
        ->call('saveAttribute')
        ->assertHasNoErrors();

    expect($attribute->fresh()->name)->toBe('Colour');

    Livewire::actingAs($admin)->test(Index::class)->call('deleteAttribute', $attribute->id);

    expect(Attribute::find($attribute->id))->toBeNull();
});

test('an attribute value referenced by a live product variant cannot be deleted', function () {
    $admin = actingAsAdmin();
    $attribute = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);
    $value = AttributeValue::create(['attribute_id' => $attribute->id, 'value' => 'Red']);
    $product = Product::factory()->create();
    $product->productAttributes()->create([
        'attribute_data' => ['Color' => 'Red'],
        'price' => 100,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openValuesModal', $attribute->id)
        ->call('deleteValue', $value->id);

    expect(AttributeValue::find($value->id))->not->toBeNull();
});

test('an unused attribute value can be deleted', function () {
    $admin = actingAsAdmin();
    $attribute = Attribute::create(['name' => 'Color', 'slug' => 'color', 'type' => 'text']);
    $value = AttributeValue::create(['attribute_id' => $attribute->id, 'value' => 'Red']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openValuesModal', $attribute->id)
        ->call('deleteValue', $value->id);

    expect(AttributeValue::find($value->id))->toBeNull();
});
