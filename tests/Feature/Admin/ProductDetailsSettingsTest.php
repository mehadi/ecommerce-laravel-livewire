<?php

declare(strict_types=1);

use App\Livewire\Admin\WebsiteSettings\ProductDetails;
use App\Models\Product;
use App\Models\Setting;
use App\Support\ProductDetailsVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('admin can view the product details settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.website.product-details'));

    $response->assertOk();
    foreach (ProductDetailsVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a product details variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(ProductDetails::class)
        ->set('storefront_product_details_variant', 'editorial')
        ->call('update')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_product_details_variant'))->toBe('editorial');
});

test('an unknown product details variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(ProductDetails::class)
        ->set('storefront_product_details_variant', 'not-a-real-variant')
        ->call('update')
        ->assertHasErrors(['storefront_product_details_variant']);

    expect(Setting::get('storefront_product_details_variant'))->toBeNull();
});

test('the product page renders every product details variant without errors', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'name_en' => 'Product Details Variant Test Item',
        'stock' => 10,
        'price' => 250.00,
    ]);

    foreach (ProductDetailsVariants::keys() as $key) {
        Setting::set('storefront_product_details_variant', $key);
        Cache::flush();

        get('http://default.localhost/product/'.$product->id)
            ->assertOk()
            ->assertSee('Product Details Variant Test Item')
            ->assertSee(__('Add to Cart'));
    }
});

test('a stale stored product details variant falls back to the default', function () {
    $product = Product::factory()->create(['is_active' => true]);
    Setting::set('storefront_product_details_variant', 'removed-legacy-variant');

    get('http://default.localhost/product/'.$product->id)->assertOk();

    expect(ProductDetailsVariants::resolve(Setting::get('storefront_product_details_variant')))->toBe(ProductDetailsVariants::DEFAULT);
});
