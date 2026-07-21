<?php

declare(strict_types=1);

use App\Livewire\Admin\PurchaseOrders\Create;
use App\Livewire\Admin\PurchaseOrders\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('an admin can create a purchase order', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);
    $warehouse = Warehouse::default();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('supplier_id', $supplier->id)
        ->set('warehouse_id', $warehouse->id)
        ->set('items.0.product_id', $product->id)
        ->set('items.0.quantity_ordered', 20)
        ->set('items.0.unit_cost', 15.50)
        ->call('save')
        ->assertRedirect(route('admin.purchase-orders.index'));

    $order = PurchaseOrder::first();
    expect($order)->not->toBeNull();
    expect($order->status)->toBe('ordered');
    expect($order->items)->toHaveCount(1);
    expect((float) $order->items->first()->unit_cost)->toBe(15.50);
});

test('fully receiving a purchase order increments warehouse stock and marks it received', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);
    $warehouse = Warehouse::default();

    $order = PurchaseOrder::create(['supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id, 'status' => 'ordered']);
    $order->items()->create(['product_id' => $product->id, 'quantity_ordered' => 20]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $order->id)
        ->call('receivePurchaseOrder');

    $warehouseStock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first();
    expect($warehouseStock->stock)->toBe(30);
    expect($order->fresh()->status)->toBe('received');
});

test('a partial receipt marks the order partially received and a follow-up receipt completes it', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);
    $warehouse = Warehouse::default();

    $order = PurchaseOrder::create(['supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id, 'status' => 'ordered']);
    $item = $order->items()->create(['product_id' => $product->id, 'quantity_ordered' => 20]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $order->id)
        ->set("receiveQuantities.{$item->id}", 12)
        ->call('receivePurchaseOrder');

    expect($order->fresh()->status)->toBe('partially_received');
    expect($item->fresh()->quantity_received)->toBe(12);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $order->id)
        ->call('receivePurchaseOrder');

    expect($order->fresh()->status)->toBe('received');
    expect($item->fresh()->quantity_received)->toBe(20);

    $warehouseStock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first();
    expect($warehouseStock->stock)->toBe(20);
});

test('a cancelled purchase order cannot be received', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);
    $warehouse = Warehouse::default();

    $order = PurchaseOrder::create(['supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id, 'status' => 'ordered']);
    $order->items()->create(['product_id' => $product->id, 'quantity_ordered' => 5]);

    Livewire::actingAs($admin)->test(Index::class)->call('cancelPurchaseOrder', $order->id);
    expect($order->fresh()->status)->toBe('cancelled');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $order->id)
        ->call('receivePurchaseOrder');

    expect(WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->exists())->toBeFalse();
});
