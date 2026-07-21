<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\StockMovementContext;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('a direct product stock edit outside any context logs a default adjustment attributed to the current user', function () {
    $user = User::factory()->create();
    actingAs($user);

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);

    $product->update(['stock' => 15]);

    $movement = StockMovement::where('product_id', $product->id)->latest('id')->first();

    expect($movement)->not->toBeNull();
    expect($movement->type)->toBe(StockMovementType::Adjustment);
    expect($movement->changed_by)->toBe($user->id);
    expect($movement->quantity_before)->toBe(20);
    expect($movement->quantity_after)->toBe(15);
    expect($movement->quantity_delta)->toBe(-5);
    expect($movement->tenant_id)->toBe(Tenancy::id());
});

test('a stock change declared inside StockMovementContext::run uses the declared type/reason/changed_by', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

    StockMovementContext::run([
        'type' => StockMovementType::Sale,
        'reason' => 'Order #TEST-1',
        'changed_by' => $user->id,
    ], function () use ($product) {
        $product->decrement('stock', 3);
    });

    $movement = StockMovement::where('product_id', $product->id)->latest('id')->first();

    expect($movement->type)->toBe(StockMovementType::Sale);
    expect($movement->reason)->toBe('Order #TEST-1');
    expect($movement->changed_by)->toBe($user->id);
    expect($movement->quantity_after)->toBe(7);
});

test('a variant stock update logs its own movement and resyncs the parent product stock', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);

    $variantA = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 100,
        'stock' => 10,
        'is_active' => true,
    ]);
    $variantB = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Large'],
        'price' => 120,
        'stock' => 5,
        'is_active' => true,
    ]);

    StockMovementContext::run([
        'type' => StockMovementType::Adjustment,
        'reason' => 'Recount',
    ], function () use ($variantA) {
        $variantA->update(['stock' => 4]);
    });

    $movement = StockMovement::where('product_attribute_id', $variantA->id)->latest('id')->first();

    expect($movement)->not->toBeNull();
    expect($movement->quantity_before)->toBe(10);
    expect($movement->quantity_after)->toBe(4);

    $product->refresh();
    expect($product->stock)->toBe(4 + $variantB->fresh()->stock);
});
