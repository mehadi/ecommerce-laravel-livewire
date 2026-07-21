<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Support\StockMovementContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Warehouse::default() lazily creates exactly one default warehouse per tenant', function () {
    expect(Warehouse::count())->toBe(0);

    $first = Warehouse::default();
    $second = Warehouse::default();

    expect($first->id)->toBe($second->id);
    expect(Warehouse::count())->toBe(1);
    expect($first->code)->toBe('MAIN');
    expect($first->is_default)->toBeTrue();
});

test('a product created with initial stock gets a matching WarehouseStock row', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 40]);

    $warehouseStock = WarehouseStock::where('product_id', $product->id)->whereNull('product_attribute_id')->first();

    expect($warehouseStock)->not->toBeNull();
    expect($warehouseStock->stock)->toBe(40);
    expect($warehouseStock->warehouse_id)->toBe(Warehouse::default()->id);
});

test('a product attribute created with initial stock gets a matching WarehouseStock row and resyncs the parent', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);

    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 100,
        'stock' => 12,
        'is_active' => true,
    ]);

    $warehouseStock = WarehouseStock::where('product_attribute_id', $variant->id)->first();

    expect($warehouseStock)->not->toBeNull();
    expect($warehouseStock->stock)->toBe(12);
    expect($product->fresh()->stock)->toBe(12);
});

test('updating a WarehouseStock row logs a movement and resyncs the parent product', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouseStock = WarehouseStock::where('product_id', $product->id)->whereNull('product_attribute_id')->first();

    StockMovementContext::run([
        'type' => StockMovementType::Adjustment,
        'reason' => 'Recount',
    ], function () use ($warehouseStock) {
        $warehouseStock->update(['stock' => 25]);
    });

    expect($product->fresh()->stock)->toBe(25);

    $movement = StockMovement::where('product_id', $product->id)->latest('id')->first();
    expect($movement->type)->toBe(StockMovementType::Adjustment);
    expect($movement->warehouse_id)->toBe($warehouseStock->warehouse_id);
    expect($movement->quantity_before)->toBe(20);
    expect($movement->quantity_after)->toBe(25);
});

test('changing the reserved counter logs a Reservation movement without touching stock', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouseStock = WarehouseStock::where('product_id', $product->id)->whereNull('product_attribute_id')->first();

    $warehouseStock->update(['reserved' => 5]);

    expect($product->fresh()->stock)->toBe(20);

    $movement = StockMovement::where('product_id', $product->id)
        ->where('type', StockMovementType::Reservation->value)
        ->latest('id')
        ->first();

    expect($movement)->not->toBeNull();
    expect($movement->quantity_before)->toBe(0);
    expect($movement->quantity_after)->toBe(5);
});

test('findOrCreateFor does not create duplicate rows for the same warehouse and product', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);
    $warehouse = Warehouse::default();

    $first = WarehouseStock::findOrCreateFor($warehouse->id, $product->id, null);
    $second = WarehouseStock::findOrCreateFor($warehouse->id, $product->id, null);

    expect($first->id)->toBe($second->id);
    expect(WarehouseStock::where('product_id', $product->id)->whereNull('product_attribute_id')->count())->toBe(1);
});
