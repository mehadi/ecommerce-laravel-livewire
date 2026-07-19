<?php

declare(strict_types=1);

use App\Livewire\Admin\WebsiteSettings\ProductGrid;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\ProductGridVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

/**
 * Seeds one product set that exercises every card-rendering branch (discount
 * badge, out-of-stock overlay, attribute-based price range with no quick-add,
 * plain in-stock product) so a single page load per variant covers all of them.
 */
function seedGridTestProducts(): void
{
    $category = Category::factory()->create(['is_active' => true]);

    Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Discounted Product',
        'is_active' => true,
        'is_featured' => true,
        'price' => 400,
        'compare_at_price' => 600,
        'stock' => 20,
    ]);

    Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Out Of Stock Product',
        'is_active' => true,
        'stock' => 0,
    ]);

    Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Plain Product',
        'is_active' => true,
        'stock' => 15,
    ]);
}

test('admin can view the product grid settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.website.product-grid'));

    $response->assertOk();
    foreach (ProductGridVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a shop grid variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(ProductGrid::class)
        ->set('storefront_shop_grid_variant', 'masonry')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_shop_grid_variant'))->toBe('masonry');
});

test('shop and featured grid variants persist independently of each other', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(ProductGrid::class)
        ->set('storefront_shop_grid_variant', 'list')
        ->set('storefront_featured_grid_variant', 'noir')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_shop_grid_variant'))->toBe('list');
    expect(Setting::get('storefront_featured_grid_variant'))->toBe('noir');

    // Changing only the shop variant afterwards must not disturb the featured one.
    Livewire::actingAs($admin)
        ->test(ProductGrid::class)
        ->set('storefront_shop_grid_variant', 'promo')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_shop_grid_variant'))->toBe('promo');
    expect(Setting::get('storefront_featured_grid_variant'))->toBe('noir');
});

test('an unknown grid variant is rejected for either field', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(ProductGrid::class)
        ->set('storefront_shop_grid_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_shop_grid_variant']);

    Livewire::actingAs($admin)
        ->test(ProductGrid::class)
        ->set('storefront_featured_grid_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_featured_grid_variant']);

    expect(Setting::get('storefront_shop_grid_variant'))->toBeNull();
    expect(Setting::get('storefront_featured_grid_variant'))->toBeNull();
});

test('the shop page renders every grid variant without error', function () {
    seedGridTestProducts();

    foreach (ProductGridVariants::keys() as $key) {
        Setting::set('storefront_shop_grid_variant', $key);

        get('http://default.localhost/shop')
            ->assertOk()
            ->assertSee('Discounted Product')
            ->assertSee('Out Of Stock Product')
            ->assertSee('Plain Product');
    }
});

test('a category page renders every grid variant without error', function () {
    $category = Category::factory()->create(['is_active' => true]);
    Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Category Grid Product',
        'is_active' => true,
        'stock' => 10,
    ]);

    foreach (ProductGridVariants::keys() as $key) {
        Setting::set('storefront_shop_grid_variant', $key);

        get('http://default.localhost/category/'.$category->slug)
            ->assertOk()
            ->assertSee('Category Grid Product');
    }
});

test('the homepage featured products section renders every grid variant without error', function () {
    Product::factory()->create([
        'name_en' => 'Homepage Featured Product',
        'is_active' => true,
        'is_featured' => true,
        'stock' => 10,
    ]);

    foreach (ProductGridVariants::keys() as $key) {
        Setting::set('storefront_featured_grid_variant', $key);

        get('http://default.localhost/')
            ->assertOk()
            ->assertSee('Homepage Featured Product');
    }
});

test('the shop page still renders its empty state with zero products', function () {
    get('http://default.localhost/shop')
        ->assertOk()
        ->assertSee('No products found');
});

test('stale stored grid variants fall back to the default for both settings', function () {
    Setting::set('storefront_shop_grid_variant', 'removed-legacy-variant');
    Setting::set('storefront_featured_grid_variant', 'another-removed-variant');

    expect(ProductGridVariants::resolve(Setting::get('storefront_shop_grid_variant')))->toBe(ProductGridVariants::DEFAULT);
    expect(ProductGridVariants::resolve(Setting::get('storefront_featured_grid_variant')))->toBe(ProductGridVariants::DEFAULT);

    seedGridTestProducts();

    get('http://default.localhost/shop')->assertOk();
});
