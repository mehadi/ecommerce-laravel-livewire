<?php

declare(strict_types=1);

use App\Livewire\Admin\StockTransfers\Create;
use App\Livewire\Admin\StockTransfers\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('an admin can create a stock transfer between two warehouses', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);

    $main = Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('from_warehouse_id', $main->id)
        ->set('to_warehouse_id', $second->id)
        ->set('items.0.product_id', $product->id)
        ->set('items.0.quantity', 5)
        ->call('save')
        ->assertRedirect(route('admin.stock-transfers.index'));

    $transfer = StockTransfer::first();
    expect($transfer)->not->toBeNull();
    expect($transfer->status)->toBe('pending');
    expect($transfer->items)->toHaveCount(1);
});

test('receiving a transfer moves stock from the source to the destination warehouse', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);

    $main = Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    $transfer = StockTransfer::create([
        'from_warehouse_id' => $main->id,
        'to_warehouse_id' => $second->id,
        'status' => 'pending',
    ]);
    $transfer->items()->create(['product_id' => $product->id, 'quantity' => 8]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $transfer->id)
        ->call('receiveTransfer');

    $mainStock = WarehouseStock::where('warehouse_id', $main->id)->where('product_id', $product->id)->first();
    $secondStock = WarehouseStock::where('warehouse_id', $second->id)->where('product_id', $product->id)->first();

    expect($mainStock->stock)->toBe(12);
    expect($secondStock->stock)->toBe(8);
    expect($transfer->fresh()->status)->toBe('received');
});

test('a variant-based transfer moves the correct attribute stock', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);
    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 100,
        'stock' => 15,
        'is_active' => true,
    ]);

    $main = Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    $transfer = StockTransfer::create([
        'from_warehouse_id' => $main->id,
        'to_warehouse_id' => $second->id,
        'status' => 'pending',
    ]);
    $transfer->items()->create(['product_id' => $product->id, 'product_attribute_id' => $variant->id, 'quantity' => 6]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $transfer->id)
        ->call('receiveTransfer');

    $mainStock = WarehouseStock::where('warehouse_id', $main->id)->where('product_attribute_id', $variant->id)->first();
    $secondStock = WarehouseStock::where('warehouse_id', $second->id)->where('product_attribute_id', $variant->id)->first();

    expect($mainStock->stock)->toBe(9);
    expect($secondStock->stock)->toBe(6);
    expect($variant->fresh()->stock)->toBe(15);
});

test('a cancelled transfer cannot be received and a received transfer cannot be cancelled', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
    $main = Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    $transfer = StockTransfer::create([
        'from_warehouse_id' => $main->id,
        'to_warehouse_id' => $second->id,
        'status' => 'pending',
    ]);
    $transfer->items()->create(['product_id' => $product->id, 'quantity' => 3]);

    Livewire::actingAs($admin)->test(Index::class)->call('cancelTransfer', $transfer->id);
    expect($transfer->fresh()->status)->toBe('cancelled');

    // Attempting to receive an already-cancelled transfer must not move any stock.
    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openReceiveModal', $transfer->id)
        ->call('receiveTransfer');

    expect($transfer->fresh()->status)->toBe('cancelled');
    expect(WarehouseStock::where('warehouse_id', $second->id)->where('product_id', $product->id)->exists())->toBeFalse();
});
