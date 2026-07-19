<?php

declare(strict_types=1);

use App\Livewire\HomePage;
use App\Livewire\LandingPage;
use App\Models\Category;
use App\Models\LandingPageConfig;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

// `localhost` is a central domain, so `GET /` there serves the platform
// marketing page. The tenant storefront home must be requested on a tenant
// host — Pest.php maps the shared test tenant to slug `default`.
const TENANT_HOME = 'http://default.localhost/';

test('storefront homepage uses the HomePage component, not the landing page funnel', function () {
    get(TENANT_HOME)
        ->assertOk()
        ->assertSeeLivewire(HomePage::class);
});

test('homepage shows featured products and categories', function () {
    $category = Category::factory()->create(['is_active' => true, 'name_en' => 'Honey Collection']);
    Product::factory()->create([
        'is_active' => true,
        'is_featured' => true,
        'name_en' => 'Wild Forest Honey',
        'category_id' => $category->id,
        'stock' => 5,
    ]);

    get(TENANT_HOME)
        ->assertOk()
        ->assertSee('Featured Products')
        ->assertSee('Wild Forest Honey')
        ->assertSee('Honey Collection');
});

test('homepage has no single-product funnel sections', function () {
    Product::factory()->create([
        'is_active' => true,
        'is_featured' => true,
        'stock' => 5,
    ]);

    // The product-details buy section (id="product") and its quantity picker
    // belong to /lp/{slug} campaign pages only.
    get(TENANT_HOME)
        ->assertOk()
        ->assertDontSee('id="product-quantity"', false);
});

test('visitors can quick add a product to the cart from the homepage', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'is_featured' => true,
        'stock' => 10,
        'price' => 500.00,
    ]);

    Livewire::test(HomePage::class)
        ->call('quickAddToCart', $product->id)
        ->assertSet('cart.'.$product->id.'.id', $product->id)
        ->assertSet('cart.'.$product->id.'.quantity', 1)
        ->assertSet('showCart', true);
});

test('landing pages on /lp/{slug} keep the product funnel and drop storefront sections', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'name_en' => 'Campaign Product',
        'stock' => 10,
    ]);

    Product::factory()->create([
        'is_active' => true,
        'is_featured' => true,
        'name_en' => 'Some Other Featured Product',
        'stock' => 10,
    ]);

    LandingPageConfig::create([
        'name' => 'Summer Campaign',
        'slug' => 'summer-campaign',
        'product_id' => $product->id,
        'is_active' => true,
        'config' => [],
    ]);

    get('http://default.localhost/lp/summer-campaign')
        ->assertOk()
        ->assertSeeLivewire(LandingPage::class)
        ->assertSee('Campaign Product')
        ->assertDontSee('Featured Products');
});
