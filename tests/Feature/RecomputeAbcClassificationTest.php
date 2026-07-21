<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

test('products are classified A/B/C by their share of total revenue', function () {
    $tenant = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant);

    $category = Category::factory()->create();

    // One dominant seller (~90% of revenue), one modest seller, one that never sold.
    $bestSeller = Product::factory()->create(['category_id' => $category->id, 'name_en' => 'Best Seller']);
    $moderateSeller = Product::factory()->create(['category_id' => $category->id, 'name_en' => 'Moderate Seller']);
    $neverSold = Product::factory()->create(['category_id' => $category->id, 'name_en' => 'Never Sold']);

    $order = Order::create([
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '01700000000',
        'shipping_address' => '123 Main Street',
        'subtotal' => 1000,
        'total' => 1000,
        'payment_method' => 'cod',
        'status' => 'delivered',
    ]);

    // Best seller = 75% of total revenue (cumulative <= 80% → class A);
    // moderate seller pushes cumulative to 100% (> 95% → class C).
    $order->orderItems()->create([
        'product_id' => $bestSeller->id,
        'product_name' => $bestSeller->name_en,
        'price' => 750,
        'quantity' => 1,
        'subtotal' => 750,
    ]);

    $order->orderItems()->create([
        'product_id' => $moderateSeller->id,
        'product_name' => $moderateSeller->name_en,
        'price' => 250,
        'quantity' => 1,
        'subtotal' => 250,
    ]);

    artisan('inventory:recompute-abc')->assertSuccessful();

    expect($bestSeller->fresh()->abc_class)->toBe('A');
    expect($moderateSeller->fresh()->abc_class)->toBe('C');
    expect($neverSold->fresh()->abc_class)->toBe('C');
});

test('the command is a no-op when a tenant has no revenue at all', function () {
    $tenant = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant);

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    artisan('inventory:recompute-abc')->assertSuccessful();

    expect($product->fresh()->abc_class)->toBe('C');
});
