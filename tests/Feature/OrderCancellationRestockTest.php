<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

if (! function_exists('makePendingOrder')) {
    function makePendingOrder(): Order
    {
        return Order::create([
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '01700000000',
            'shipping_address' => '123 Main Street',
            'subtotal' => 0,
            'total' => 0,
            'payment_method' => 'cod',
            'status' => 'pending',
        ]);
    }
}

test('cancelling an order restores stock, logs a return movement, and flips stock_deducted off', function () {
    $admin = User::factory()->create();
    actingAs($admin);

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 15]);

    $order = makePendingOrder();
    $item = $order->orderItems()->create([
        'product_id' => $product->id,
        'product_name' => $product->name_en,
        'price' => 100,
        'quantity' => 5,
        'subtotal' => 500,
        'stock_deducted' => true,
    ]);

    $order->update(['status' => 'cancelled']);

    $product->refresh();
    $item->refresh();

    expect($product->stock)->toBe(20);
    expect($item->stock_deducted)->toBeFalse();

    $returnMovement = StockMovement::where('product_id', $product->id)
        ->where('type', StockMovementType::Return->value)
        ->latest('id')
        ->first();

    expect($returnMovement)->not->toBeNull();
    expect($returnMovement->quantity_delta)->toBe(5);
    expect($returnMovement->reason)->toContain($order->order_number);
});

test('restocking targets the specific variant when the order item has one', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);

    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 100,
        'stock' => 8,
        'is_active' => true,
    ]);

    $order = makePendingOrder();
    $order->orderItems()->create([
        'product_id' => $product->id,
        'product_attribute_id' => $variant->id,
        'product_name' => $product->name_en,
        'price' => 100,
        'quantity' => 2,
        'subtotal' => 200,
        'stock_deducted' => true,
    ]);

    $order->update(['status' => 'cancelled']);

    expect($variant->fresh()->stock)->toBe(10);

    $movement = StockMovement::where('product_attribute_id', $variant->id)
        ->where('type', StockMovementType::Return->value)
        ->first();

    expect($movement)->not->toBeNull();
    expect($movement->quantity_delta)->toBe(2);
});

test('an order item that never had stock deducted is skipped on cancellation', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

    $order = makePendingOrder();
    $item = $order->orderItems()->create([
        'product_id' => $product->id,
        'product_name' => $product->name_en,
        'price' => 100,
        'quantity' => 3,
        'subtotal' => 300,
        'stock_deducted' => false,
    ]);

    $order->update(['status' => 'cancelled']);

    expect($product->fresh()->stock)->toBe(10);
    expect($item->fresh()->stock_deducted)->toBeFalse();
    expect(StockMovement::where('product_id', $product->id)->exists())->toBeFalse();
});

test('a second cancellation transition does not double-restock', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 15]);

    $order = makePendingOrder();
    $order->orderItems()->create([
        'product_id' => $product->id,
        'product_name' => $product->name_en,
        'price' => 100,
        'quantity' => 5,
        'subtotal' => 500,
        'stock_deducted' => true,
    ]);

    $order->update(['status' => 'cancelled']);
    expect($product->fresh()->stock)->toBe(20);

    // Simulate the order being reopened and cancelled again — the guard is
    // `stock_deducted`, already flipped false by the first cancellation, so
    // this second transition into 'cancelled' must not restock again.
    $order->update(['status' => 'pending']);
    $order->update(['status' => 'cancelled']);

    expect($product->fresh()->stock)->toBe(20);

    $returnMovementCount = StockMovement::where('product_id', $product->id)
        ->where('type', StockMovementType::Return->value)
        ->count();

    expect($returnMovementCount)->toBe(1);
});
