<?php

declare(strict_types=1);

use App\Livewire\Admin\CartCheckout\Index;
use App\Livewire\HomePage;
use App\Models\Product;
use App\Models\Setting;
use App\Support\CartVariants;
use App\Support\CheckoutVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('admin can view the cart & checkout settings page with all variants listed', function () {
    $admin = actingAsAdmin();

    $response = actingAs($admin)->get(route('admin.cart-checkout.index'));

    $response->assertOk();
    foreach (CartVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
    foreach (CheckoutVariants::all() as $variant) {
        $response->assertSee($variant['name']);
    }
});

test('admin can select a cart variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('storefront_cart_variant', 'sidebar')
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_cart_variant'))->toBe('sidebar');
});

test('admin can select a checkout variant and it persists per tenant', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('storefront_checkout_variant', 'split')
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::get('storefront_checkout_variant'))->toBe('split');
});

test('an unknown cart variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('storefront_cart_variant', 'not-a-real-variant')
        ->call('save')
        ->assertHasErrors(['storefront_cart_variant']);

    expect(Setting::get('storefront_cart_variant'))->toBeNull();
});

test('an unknown checkout variant is rejected', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('storefront_checkout_variant', 'not-a-real-variant')
        ->call('save')
        ->assertHasErrors(['storefront_checkout_variant']);

    expect(Setting::get('storefront_checkout_variant'))->toBeNull();
});

test('the storefront renders every cart variant without errors', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'is_featured' => true,
        'name_en' => 'Cart Variant Test Product',
        'stock' => 10,
        'price' => 500.00,
    ]);

    foreach (CartVariants::keys() as $key) {
        Setting::set('storefront_cart_variant', $key);
        Cache::flush();

        Livewire::test(HomePage::class)
            ->call('quickAddToCart', $product->id)
            ->assertSet('showCart', true)
            ->assertSee('Cart Variant Test Product');
    }
});

test('the storefront renders every checkout variant without errors', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'is_featured' => true,
        'name_en' => 'Checkout Variant Test Product',
        'stock' => 10,
        'price' => 500.00,
    ]);

    foreach (CheckoutVariants::keys() as $key) {
        Setting::set('storefront_checkout_variant', $key);
        Cache::flush();

        Livewire::test(HomePage::class)
            ->call('quickAddToCart', $product->id)
            ->set('showCheckout', true)
            ->assertSee('Checkout Variant Test Product')
            ->assertSee(__('Place Order'));
    }
});

test('a stale stored cart variant falls back to the default', function () {
    Setting::set('storefront_cart_variant', 'removed-legacy-variant');

    get('http://default.localhost/')->assertOk();

    expect(CartVariants::resolve(Setting::get('storefront_cart_variant')))->toBe(CartVariants::DEFAULT);
});

test('a stale stored checkout variant falls back to the default', function () {
    Setting::set('storefront_checkout_variant', 'removed-legacy-variant');

    get('http://default.localhost/')->assertOk();

    expect(CheckoutVariants::resolve(Setting::get('storefront_checkout_variant')))->toBe(CheckoutVariants::DEFAULT);
});
