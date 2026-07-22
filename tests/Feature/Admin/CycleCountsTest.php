<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Livewire\Admin\CycleCounts\Count;
use App\Livewire\Admin\CycleCounts\Index;
use App\Models\Category;
use App\Models\CycleCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

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

test('a user without cycle count permissions cannot view the index or open the count screen', function () {
    // Spatie's hasPermissionTo()/checkPermissionTo() throws PermissionDoesNotExist if the
    // permission row itself isn't registered yet — seed the rows so this exercises "denied
    // because the user lacks the permission", not "denied because it was never created".
    foreach (['view cycle counts', 'create cycle counts', 'complete cycle counts'] as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouse = Warehouse::default();

    $cycleCount = CycleCount::create(['warehouse_id' => $warehouse->id, 'status' => 'pending', 'scope' => 'all']);
    $item = $cycleCount->items()->create(['product_id' => $product->id, 'expected_quantity' => 20]);

    Livewire::actingAs($user)->test(Index::class)->assertStatus(403);
    Livewire::actingAs($user)->test(Count::class, ['cycleCount' => $cycleCount])->assertStatus(403);

    expect(CycleCount::count())->toBe(1);
    expect($item->fresh()->counted_quantity)->toBeNull();
});

test('a user who can view but not create/complete cycle counts is denied those actions', function () {
    foreach (['view cycle counts', 'create cycle counts', 'complete cycle counts'] as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $user = User::factory()->create();
    $user->givePermissionTo('view cycle counts');

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouse = Warehouse::default();

    $cycleCount = CycleCount::create(['warehouse_id' => $warehouse->id, 'status' => 'pending', 'scope' => 'all']);
    $item = $cycleCount->items()->create(['product_id' => $product->id, 'expected_quantity' => 20]);

    Livewire::actingAs($user)->test(Index::class)->call('createCycleCount')->assertStatus(403);
    Livewire::actingAs($user)
        ->test(Count::class, ['cycleCount' => $cycleCount])
        ->set("countedQuantities.{$item->id}", 5)
        ->call('saveProgress')
        ->assertStatus(403);
    Livewire::actingAs($user)
        ->test(Count::class, ['cycleCount' => $cycleCount])
        ->set("countedQuantities.{$item->id}", 5)
        ->call('completeCount')
        ->assertStatus(403);

    expect(CycleCount::count())->toBe(1);
    expect($item->fresh()->counted_quantity)->toBeNull();
});

test('a completed cycle count cannot be reopened to re-mutate warehouse stock', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);
    $warehouse = Warehouse::default();

    $cycleCount = CycleCount::create(['warehouse_id' => $warehouse->id, 'status' => 'pending', 'scope' => 'all']);
    $item = $cycleCount->items()->create(['product_id' => $product->id, 'expected_quantity' => 20]);

    Livewire::actingAs($admin)
        ->test(Count::class, ['cycleCount' => $cycleCount])
        ->set("countedQuantities.{$item->id}", 17)
        ->call('completeCount');

    expect($cycleCount->fresh()->status)->toBe('completed');
    expect($product->fresh()->stock)->toBe(17);

    // Someone bumps the warehouse stock back up after the count completed.
    WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first()->update(['stock' => 50]);

    // Reopening the already-completed count and re-submitting a different
    // quantity must not touch stock or the item again.
    Livewire::actingAs($admin)
        ->test(Count::class, ['cycleCount' => $cycleCount->fresh()])
        ->set("countedQuantities.{$item->id}", 5)
        ->call('saveProgress');

    expect($item->fresh()->counted_quantity)->toBe(17);
    expect($product->fresh()->stock)->toBe(50);

    Livewire::actingAs($admin)
        ->test(Count::class, ['cycleCount' => $cycleCount->fresh()])
        ->set("countedQuantities.{$item->id}", 5)
        ->call('completeCount');

    expect($item->fresh()->counted_quantity)->toBe(17);
    expect($product->fresh()->stock)->toBe(50);
    expect($cycleCount->fresh()->status)->toBe('completed');
});
