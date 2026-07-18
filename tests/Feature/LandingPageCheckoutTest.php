<?php

declare(strict_types=1);

use App\Livewire\LandingPage;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('buy now button shows checkout form', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->assertSet('showCheckout', true)
        ->assertSet('cart.'.$product->id.'.id', $product->id)
        ->assertSet('cart.'.$product->id.'.quantity', 1);
});

test('buy now validates product is in stock', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 0,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->assertSet('showCheckout', false);
});

test('place order creates order with city selection', function () {
    $city = City::factory()->create([
        'name' => 'Dhaka',
        'is_active' => true,
    ]);

    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerEmail', 'john@example.com')
        ->set('customerPhone', '01814444444')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->set('shippingPostalCode', '1200')
        ->call('placeOrder')
        ->assertSet('showOrderConfirmation', true)
        ->assertSet('showCheckout', false);

    $this->assertDatabaseHas('orders', [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '01814444444',
        'shipping_address' => '123 Main Street',
        'shipping_city' => 'Dhaka',
        'shipping_postal_code' => '1200',
        'status' => 'pending',
        'payment_method' => 'cod',
    ]);

    $order = Order::where('customer_email', 'john@example.com')->first();
    expect($order)->not->toBeNull();
    expect($order->items)->toHaveCount(1);
    expect($order->items->first()->product_id)->toBe($product->id);
});

test('place order works without email', function () {
    $city = City::factory()->create(['is_active' => true]);
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerPhone', '01814444444')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->call('placeOrder')
        ->assertSet('showOrderConfirmation', true);

    $this->assertDatabaseHas('orders', [
        'customer_name' => 'John Doe',
        'customer_email' => '',
        'customer_phone' => '01814444444',
    ]);
});

test('place order validates required fields', function () {
    $city = City::factory()->create(['is_active' => true]);
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->call('placeOrder')
        ->assertHasErrors([
            'customerName' => 'required',
            'customerPhone' => 'required',
            'shippingAddress' => 'required',
            'shippingCityId' => 'required',
        ]);
});

test('place order validates city exists', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerEmail', 'john@example.com')
        ->set('customerPhone', '+8801712345678')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', 99999)
        ->call('placeOrder')
        ->assertHasErrors(['shippingCityId' => 'exists']);
});

test('place order validates email format when provided', function () {
    $city = City::factory()->create(['is_active' => true]);
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerEmail', 'invalid-email')
        ->set('customerPhone', '01814444444')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->call('placeOrder')
        ->assertHasErrors(['customerEmail' => 'email']);
});

test('place order validates phone format', function () {
    $city = City::factory()->create(['is_active' => true]);
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerPhone', '1234567890')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->call('placeOrder')
        ->assertHasErrors(['customerPhone']);

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerPhone', '0181444444')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->call('placeOrder')
        ->assertHasErrors(['customerPhone']);
});

test('place order decrements product stock', function () {
    $city = City::factory()->create(['is_active' => true]);
    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    $initialStock = $product->stock;

    Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->set('quantity', 3)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerEmail', 'john@example.com')
        ->set('customerPhone', '01814444444')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->call('placeOrder');

    $product->refresh();
    expect($product->stock)->toBe($initialStock - 3);
});

test('cities are loaded for checkout form', function () {
    City::factory()->count(5)->create(['is_active' => true]);
    City::factory()->create(['is_active' => false]);

    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
    ]);

    $component = Livewire::test(LandingPage::class)
        ->set('productId', $product->id);

    $cities = $component->get('cities');
    expect($cities)->toHaveCount(5);
});

test('order confirmation shows order details', function () {
    $city = City::factory()->create([
        'name' => 'Dhaka',
        'is_active' => true,
    ]);

    $product = Product::factory()->create([
        'is_active' => true,
        'stock' => 10,
        'price' => 1000.00,
    ]);

    $component = Livewire::test(LandingPage::class)
        ->set('productId', $product->id)
        ->call('buyNow')
        ->set('customerName', 'John Doe')
        ->set('customerEmail', 'john@example.com')
        ->set('customerPhone', '01814444444')
        ->set('shippingAddress', '123 Main Street')
        ->set('shippingCityId', $city->id)
        ->call('placeOrder');

    expect($component->get('showOrderConfirmation'))->toBeTrue();
    expect($component->get('order'))->not->toBeNull();
    expect($component->get('order')->customer_name)->toBe('John Doe');
    expect($component->get('order')->order_number)->not->toBeNull();
});
