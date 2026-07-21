<?php

declare(strict_types=1);

use App\Livewire\Admin\Inventory\Index;
use App\Livewire\Admin\PurchaseOrders\Index as PurchaseOrdersIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('an admin can add a new batch for a batch-tracked product via the Adjust modal', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0, 'tracks_batches' => true]);
    $warehouse = Warehouse::default();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('batchRows.0.batch_number', 'LOT-001')
        ->set('batchRows.0.quantity', 40)
        ->set('batchRows.0.expires_at', now()->addDays(20)->format('Y-m-d'))
        ->set('adjustReason', 'Initial batch receipt')
        ->call('adjustStock');

    expect(ProductBatch::where('product_id', $product->id)->where('batch_number', 'LOT-001')->exists())->toBeTrue();
    expect($product->fresh()->stock)->toBe(40);

    $warehouseStock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first();
    expect($warehouseStock->stock)->toBe(40);
});

test('an admin can edit an existing batch quantity via the Adjust modal', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0, 'tracks_batches' => true]);
    $warehouse = Warehouse::default();

    $batch = ProductBatch::create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'batch_number' => 'LOT-002',
        'quantity' => 25,
        'received_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('batchRows.0.quantity', 18)
        ->set('adjustReason', 'Damaged units removed')
        ->call('adjustStock');

    expect($batch->fresh()->quantity)->toBe(18);
    expect($product->fresh()->stock)->toBe(18);
});

test('reserve/release still works for a batch-tracked product without touching batches', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0, 'tracks_batches' => true]);
    $warehouse = Warehouse::default();

    ProductBatch::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'batch_number' => 'LOT-003', 'quantity' => 30, 'received_at' => now()]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustMode', 'reserve')
        ->set('adjustQuantity', 10)
        ->set('adjustReason', 'Wholesale hold')
        ->call('adjustStock');

    $warehouseStock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first();
    expect($warehouseStock->stock)->toBe(30);
    expect($warehouseStock->reserved)->toBe(10);
    expect(ProductBatch::where('product_id', $product->id)->sum('quantity'))->toBe(30);
});

test('receiving a purchase order for a batch-tracked product creates a batch instead of a direct stock increment', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0, 'tracks_batches' => true]);
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);
    $warehouse = Warehouse::default();

    $order = PurchaseOrder::create(['supplier_id' => $supplier->id, 'warehouse_id' => $warehouse->id, 'status' => 'ordered']);
    $item = $order->items()->create(['product_id' => $product->id, 'quantity_ordered' => 60]);

    Livewire::actingAs($admin)
        ->test(PurchaseOrdersIndex::class)
        ->call('openReceiveModal', $order->id)
        ->set("receiveBatchNumbers.{$item->id}", 'PO-LOT-1')
        ->set("receiveExpiryDates.{$item->id}", now()->addDays(60)->format('Y-m-d'))
        ->call('receivePurchaseOrder');

    expect(ProductBatch::where('product_id', $product->id)->where('batch_number', 'PO-LOT-1')->exists())->toBeTrue();
    expect($product->fresh()->stock)->toBe(60);
    expect($order->fresh()->status)->toBe('received');
});

test('nextBatchToPick returns the soonest-expiring batch with stock (FEFO)', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0, 'tracks_batches' => true]);
    $warehouse = Warehouse::default();

    ProductBatch::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'batch_number' => 'FAR', 'quantity' => 10, 'expires_at' => now()->addDays(90)]);
    ProductBatch::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'batch_number' => 'NEAR', 'quantity' => 10, 'expires_at' => now()->addDays(5)]);
    ProductBatch::create(['warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'batch_number' => 'EMPTY-BUT-NEARER', 'quantity' => 0, 'expires_at' => now()->addDays(1)]);

    $next = $product->nextBatchToPick($warehouse->id);

    expect($next->batch_number)->toBe('NEAR');
});
