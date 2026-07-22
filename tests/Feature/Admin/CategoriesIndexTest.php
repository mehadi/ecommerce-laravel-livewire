<?php

declare(strict_types=1);

use App\Livewire\Admin\Categories\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

test('a user without category permissions cannot save, delete, or duplicate categories', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name_en', 'Nope')
        ->set('slug', 'nope')
        ->call('save')
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('delete', $category->id)
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('duplicate', $category->id)
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('toggleStatus', $category->id)
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('selectedItems', [$category->id])
        ->call('bulkDelete')
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('selectedItems', [$category->id])
        ->call('bulkToggleStatus')
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('updateCategoryParent', $category->id, null)
        ->assertForbidden();

    expect(Category::find($category->id))->not->toBeNull();
});

test('slug uniqueness is scoped per tenant, not global', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);
    $admin1 = actingAsAdmin();

    Livewire::actingAs($admin1)
        ->test(Index::class)
        ->set('name_en', 'Shoes')
        ->set('slug', 'shoes')
        ->call('save')
        ->assertHasNoErrors();

    expect(Category::where('slug', 'shoes')->where('tenant_id', $tenant1->id)->exists())->toBeTrue();

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant2->id);

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin2 = User::factory()->create();
    $admin2->assignRole('admin');

    // Same slug, different tenant: must be allowed, since uniqueness is (tenant_id, slug).
    Livewire::actingAs($admin2)
        ->test(Index::class)
        ->set('name_en', 'Shoes')
        ->set('slug', 'shoes')
        ->call('save')
        ->assertHasNoErrors();

    expect(Category::where('slug', 'shoes')->where('tenant_id', $tenant2->id)->exists())->toBeTrue();
    expect(DB::table('categories')->where('slug', 'shoes')->count())->toBe(2);

    // But within the same tenant, the slug is still rejected as taken.
    Livewire::actingAs($admin2)
        ->test(Index::class)
        ->set('name_en', 'Shoes Again')
        ->set('slug', 'shoes')
        ->call('save')
        ->assertHasErrors('slug');
});

test('bulk toggle status flips each category own state instead of forcing one status on all', function () {
    $admin = actingAsAdmin();

    $active = Category::factory()->create(['is_active' => true]);
    $inactive = Category::factory()->create(['is_active' => false]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('selectedItems', [$active->id, $inactive->id])
        ->call('bulkToggleStatus')
        ->assertHasNoErrors();

    expect($active->fresh()->is_active)->toBeFalse();
    expect($inactive->fresh()->is_active)->toBeTrue();
});

test('removing a category image only deletes the file from storage on save, not on click', function () {
    Storage::fake('public');
    $admin = actingAsAdmin();

    $path = 'categories/original.jpg';
    Storage::disk('public')->put($path, 'fake-image-contents');

    $category = Category::factory()->create(['image' => $path]);

    $component = Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openModal', $category->id)
        ->call('removeImage');

    // Clicking remove only flips component state — the file must still exist.
    Storage::disk('public')->assertExists($path);
    expect(Category::find($category->id)->image)->toBe($path);

    $component->call('save')->assertHasNoErrors();

    // Only now, once the change is actually persisted, is the file removed.
    Storage::disk('public')->assertMissing($path);
    expect(Category::find($category->id)->image)->toBeNull();
});

test('cancelling the modal after removing an image leaves storage and the database untouched', function () {
    Storage::fake('public');
    $admin = actingAsAdmin();

    $path = 'categories/keep-me.jpg';
    Storage::disk('public')->put($path, 'fake-image-contents');

    $category = Category::factory()->create(['image' => $path]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openModal', $category->id)
        ->call('removeImage')
        ->call('closeModal');

    Storage::disk('public')->assertExists($path);
    expect(Category::find($category->id)->image)->toBe($path);
});

test('mutating a category invalidates every storefront cache derived from category data', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create(['is_active' => true]);

    Cache::put(Tenancy::cacheKey('categories.all'), 'stale', 3600);
    Cache::put(Tenancy::cacheKey('categories.index.cards'), 'stale', 3600);
    Cache::put(Tenancy::cacheKey('landing.featured_categories'), 'stale', 3600);
    Cache::put(Tenancy::cacheKey('shop.categories'), 'stale', 3600);
    Cache::put(Tenancy::cacheKey("category.{$category->id}.subtree_ids"), 'stale', 3600);
    Cache::put(Tenancy::cacheKey("category.{$category->id}.subcategories"), 'stale', 3600);
    Cache::put(Tenancy::cacheKey("category.{$category->id}.siblings"), 'stale', 3600);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('toggleStatus', $category->id)
        ->assertHasNoErrors();

    expect(Cache::has(Tenancy::cacheKey('categories.all')))->toBeFalse();
    expect(Cache::has(Tenancy::cacheKey('categories.index.cards')))->toBeFalse();
    expect(Cache::has(Tenancy::cacheKey('landing.featured_categories')))->toBeFalse();
    expect(Cache::has(Tenancy::cacheKey('shop.categories')))->toBeFalse();
    expect(Cache::has(Tenancy::cacheKey("category.{$category->id}.subtree_ids")))->toBeFalse();
    expect(Cache::has(Tenancy::cacheKey("category.{$category->id}.subcategories")))->toBeFalse();
    expect(Cache::has(Tenancy::cacheKey("category.{$category->id}.siblings")))->toBeFalse();
});

test('deleting a category with assigned products warns and leaves the products uncategorized', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $products = Product::factory()->count(2)->create(['category_id' => $category->id]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $category->id)
        ->assertHasNoErrors();

    // The category is gone, but its products survive with category_id nulled out
    // (products.category_id is nullOnDelete) rather than being deleted themselves.
    expect(Category::find($category->id))->toBeNull();

    foreach ($products as $product) {
        expect($product->fresh())
            ->not->toBeNull()
            ->category_id->toBeNull();
    }
});
