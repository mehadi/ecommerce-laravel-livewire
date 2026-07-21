<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Livewire\Admin\CycleCounts\Count;
use App\Livewire\Admin\CycleCounts\Index;
use App\Models\Category;
use App\Models\CycleCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('an admin can create a full physical inventory cycle count', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    Product::factory()->count(3)->create(['category_id' => $category->id, 'stock' => 10]);
    $warehouse = Warehouse::default();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openCreateModal')
        ->set('warehouse_id', $warehouse->id)
        ->set('scope', 'all')
        ->call('createCycleCount');

    $cycleCount = CycleCount::first();
    expect($cycleCount)->not->toBeNull();
    expect($cycleCount->status)->toBe('pending');
    expect($cycleCount->items)->toHaveCount(3);
});

test('an admin can create a cycle count scoped to one category', function () {
    $admin = actingAsAdmin();
    $categoryA = Category::factory()->create();
    $categoryB = Category::factory()->create();
    Product::factory()->create(['category_id' => $categoryA->id, 'stock' => 10]);
    Product::factory()->create(['category_id' => $categoryB->id, 'stock' => 10]);
    $warehouse = Warehouse::default();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openCreateModal')
        ->set('warehouse_id', $warehouse->id)
        ->set('scope', 'category')
        ->set('filterCategoryId', $categoryA->id)
        ->call('createCycleCount');

    $cycleCount = CycleCount::first();
    expect($cycleCount->items)->toHaveCount(1);
});

test('completing a cycle count reconciles discrepancies and logs cycle count movements', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouse = Warehouse::default();

    $cycleCount = CycleCount::create(['warehouse_id' => $warehouse->id, 'status' => 'pending', 'scope' => 'all']);
    $item = $cycleCount->items()->create(['product_id' => $product->id, 'expected_quantity' => 20]);

    Livewire::actingAs($admin)
        ->test(Count::class, ['cycleCount' => $cycleCount])
        ->set("countedQuantities.{$item->id}", 17)
        ->call('completeCount')
        ->assertRedirect(route('admin.cycle-counts.index'));

    expect($cycleCount->fresh()->status)->toBe('completed');
    expect($item->fresh()->counted_quantity)->toBe(17);
    expect($product->fresh()->stock)->toBe(17);

    $movement = StockMovement::where('product_id', $product->id)
        ->where('type', StockMovementType::CycleCount->value)
        ->first();
    expect($movement)->not->toBeNull();
    expect($movement->quantity_delta)->toBe(-3);
});

test('a matching count does not create a stock movement', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouse = Warehouse::default();

    $cycleCount = CycleCount::create(['warehouse_id' => $warehouse->id, 'status' => 'pending', 'scope' => 'all']);
    $item = $cycleCount->items()->create(['product_id' => $product->id, 'expected_quantity' => 20]);

    Livewire::actingAs($admin)
        ->test(Count::class, ['cycleCount' => $cycleCount])
        ->set("countedQuantities.{$item->id}", 20)
        ->call('completeCount');

    expect(StockMovement::where('product_id', $product->id)
        ->where('type', StockMovementType::CycleCount->value)
        ->exists())->toBeFalse();
});

test('saving progress marks the count as in progress without completing it', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouse = Warehouse::default();

    $cycleCount = CycleCount::create(['warehouse_id' => $warehouse->id, 'status' => 'pending', 'scope' => 'all']);
    $item = $cycleCount->items()->create(['product_id' => $product->id, 'expected_quantity' => 20]);

    Livewire::actingAs($admin)
        ->test(Count::class, ['cycleCount' => $cycleCount])
        ->set("countedQuantities.{$item->id}", 19)
        ->call('saveProgress');

    expect($cycleCount->fresh()->status)->toBe('in_progress');
    expect($product->fresh()->stock)->toBe(20); // unchanged until completeCount()
});
