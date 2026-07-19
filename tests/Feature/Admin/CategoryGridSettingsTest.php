<?php

declare(strict_types=1);

use App\Livewire\Admin\WebsiteSettings\CategoryGrid;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\CategoryGridVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

/**
 * Seeds a category set that exercises every card-rendering branch (a parent
 * with an image and subcategories, an image-less parent, and products for a
 * non-zero count) so a single page load per variant covers all of them.
 */
function seedCategoryGridTestData(): void
{
    $withChildren = Category::factory()->create([
        'name_en' => 'Parent With Children',
        'is_active' => true,
        'image' => 'categories/example.jpg',
    ]);

    foreach (range(1, 5) as $i) {
        Category::factory()->create([
            'name_en' => 'Child Category '.$i,
            'parent_id' => $withChildren->id,
            'is_active' => true,
        ]);
    }

    $plain = Category::factory()->create([
        'name_en' => 'Plain Parent Category',
        'is_active' => true,
        'image' => null,
    ]);

    Product::factory()->create([
        'category_id' => $plain->id,
        'is_active' => true,
        'stock' => 10,
    ]);
}

test('admin can view the category grid settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.website.category-grid'));

    $response->assertOk();
    foreach (CategoryGridVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a category grid variant and it persists', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(CategoryGrid::class)
        ->set('storefront_category_grid_variant', 'overlay')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_category_grid_variant'))->toBe('overlay');
});

test('an unknown category grid variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(CategoryGrid::class)
        ->set('storefront_category_grid_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_category_grid_variant']);

    expect(Setting::get('storefront_category_grid_variant'))->toBeNull();
});

test('the categories page renders every grid variant without error', function () {
    seedCategoryGridTestData();

    foreach (CategoryGridVariants::keys() as $key) {
        Setting::set('storefront_category_grid_variant', $key);

        get('http://default.localhost/categories')
            ->assertOk()
            ->assertSee('Parent With Children')
            ->assertSee('Plain Parent Category');
    }
});

test('the categories page still renders its empty state with zero categories', function () {
    get('http://default.localhost/categories')
        ->assertOk()
        ->assertSee('No categories found');
});

test('a stale stored category grid variant falls back to the default', function () {
    Setting::set('storefront_category_grid_variant', 'removed-legacy-variant');

    expect(CategoryGridVariants::resolve(Setting::get('storefront_category_grid_variant')))->toBe(CategoryGridVariants::DEFAULT);

    seedCategoryGridTestData();

    get('http://default.localhost/categories')->assertOk();
});

test('the grid columns picker is hidden for variants with a fixed layout', function () {
    seedCategoryGridTestData();

    Setting::set('storefront_category_grid_variant', 'cards');
    get('http://default.localhost/categories')->assertOk()->assertSee('Grid columns');

    Setting::set('storefront_category_grid_variant', 'list');
    get('http://default.localhost/categories')->assertOk()->assertDontSee('Grid columns');
});
