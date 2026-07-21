<?php

declare(strict_types=1);

use App\Livewire\Admin\Orders\Index;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('a manually created admin order deducts stock from the default warehouse when only one exists', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10, 'price' => 100]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('customer_name', 'Jane Doe')
        ->set('customer_email', 'jane@example.com')
        ->set('customer_phone', '01700000000')
        ->set('shipping_address', '123 Main Street')
        ->set('orderItems', [[
            'product_id' => $product->id,
            'product_name' => $product->name_en,
            'price' => 100,
            'quantity' => 4,
            'subtotal' => 400,
        ]])
        ->call('createOrder');

    $warehouseStock = WarehouseStock::where('warehouse_id', Warehouse::default()->id)
        ->where('product_id', $product->id)
        ->whereNull('product_attribute_id')
        ->first();

    expect($warehouseStock->stock)->toBe(6);
    expect($product->fresh()->stock)->toBe(6);
});

test('an admin can pick which warehouse fulfills a manually created order when more than one exists', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10, 'price' => 100]);

    $mainWarehouse = Warehouse::default();
    $secondWarehouse = Warehouse::create(['name' => 'Overflow Storage', 'code' => 'OVERFLOW', 'is_active' => true]);

    // Give the second warehouse its own stock for this product so the
    // deduction is unambiguous.
    WarehouseStock::findOrCreateFor($secondWarehouse->id, $product->id, null)->update(['stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('customer_name', 'Jane Doe')
        ->set('customer_email', 'jane@example.com')
        ->set('customer_phone', '01700000000')
        ->set('shipping_address', '123 Main Street')
        ->set('selectedWarehouseId', $secondWarehouse->id)
        ->set('orderItems', [[
            'product_id' => $product->id,
            'product_name' => $product->name_en,
            'price' => 100,
            'quantity' => 4,
            'subtotal' => 400,
        ]])
        ->call('createOrder');

    $mainStock = WarehouseStock::where('warehouse_id', $mainWarehouse->id)->where('product_id', $product->id)->first();
    $secondStock = WarehouseStock::where('warehouse_id', $secondWarehouse->id)->where('product_id', $product->id)->first();

    expect($mainStock->stock)->toBe(10);
    expect($secondStock->stock)->toBe(6);

    $orderItem = OrderItem::where('product_id', $product->id)->latest('id')->first();
    expect($orderItem->warehouse_id)->toBe($secondWarehouse->id);
});
