<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\PosSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function posLine(Product $product, int $quantity, ?int $productAttributeId = null): array
{
    return [
        'product_id' => $product->id,
        'product_attribute_id' => $productAttributeId,
        'product_name' => $product->name_en,
        'attribute_data' => null,
        'quantity' => $quantity,
        'unit_price' => (float) $product->price,
    ];
}

test('checkout creates an order with items and payments, and decrements warehouse stock', function () {
    actingAs(User::factory()->create());

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10, 'price' => 100]);
    $warehouse = Warehouse::default();
    WarehouseStock::findOrCreateFor($warehouse->id, $product->id, null)->update(['stock' => 10]);

    $order = app(PosSaleService::class)->checkout([
        'order' => [
            'channel' => 'pos',
            'customer_name' => 'Walk-in',
            'subtotal' => 200,
            'total' => 200,
        ],
        'lines' => [posLine($product, 2)],
        'payments' => [
            ['method' => 'cash', 'amount' => 200, 'change_given' => 0],
        ],
        'warehouse' => $warehouse,
    ]);

    expect($order->channel)->toBe('pos');
    expect($order->items)->toHaveCount(1);
    expect($order->items->first()->stock_deducted)->toBeTrue();
    expect($order->payments)->toHaveCount(1);
    expect(OrderPayment::where('order_id', $order->id)->sum('amount'))->toEqual(200.0);

    $stock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first();
    expect($stock->stock)->toBe(8);

    $movement = StockMovement::where('product_id', $product->id)->where('type', StockMovementType::Sale->value)->first();
    expect($movement)->not->toBeNull();
    expect($movement->reason)->toContain($order->order_number);
});

test('checkout deducts the specific variant when a line has a product_attribute_id', function () {
    actingAs(User::factory()->create());

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);
    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Large'],
        'price' => 150,
        'stock' => 5,
        'is_active' => true,
    ]);
    $warehouse = Warehouse::default();
    WarehouseStock::findOrCreateFor($warehouse->id, $product->id, $variant->id)->update(['stock' => 5]);

    app(PosSaleService::class)->checkout([
        'order' => ['channel' => 'pos', 'customer_name' => 'Walk-in', 'subtotal' => 150, 'total' => 150],
        'lines' => [posLine($product, 1, $variant->id)],
        'payments' => [['method' => 'cash', 'amount' => 150]],
        'warehouse' => $warehouse,
    ]);

    $variantStock = WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_attribute_id', $variant->id)->first();
    expect($variantStock->stock)->toBe(4);
});

test('checkout throws and creates nothing when a line is oversold', function () {
    actingAs(User::factory()->create());

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 1]);
    $warehouse = Warehouse::default();
    WarehouseStock::findOrCreateFor($warehouse->id, $product->id, null)->update(['stock' => 1]);

    expect(fn () => app(PosSaleService::class)->checkout([
        'order' => ['channel' => 'pos', 'customer_name' => 'Walk-in', 'subtotal' => 500, 'total' => 500],
        'lines' => [posLine($product, 5)],
        'payments' => [['method' => 'cash', 'amount' => 500]],
        'warehouse' => $warehouse,
    ]))->toThrow(RuntimeException::class);

    expect(Order::count())->toBe(0);
    expect(WarehouseStock::where('product_id', $product->id)->first()->stock)->toBe(1);
});

test('a skip_stock line records the sale without touching inventory', function () {
    actingAs(User::factory()->create());

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
    $warehouse = Warehouse::default();

    $order = app(PosSaleService::class)->checkout([
        'order' => ['channel' => 'admin_manual', 'customer_name' => 'Jane', 'subtotal' => 100, 'total' => 100],
        'lines' => [array_merge(posLine($product, 1), ['skip_stock' => true])],
        'payments' => [],
        'warehouse' => $warehouse,
    ]);

    $item = $order->items->first();
    expect($item->stock_deducted)->toBeFalse();
    expect($item->warehouse_id)->toBeNull();
    expect($product->fresh()->stock)->toBe(10);
    expect(StockMovement::where('product_id', $product->id)->exists())->toBeFalse();
});
