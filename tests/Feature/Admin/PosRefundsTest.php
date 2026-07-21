<?php

declare(strict_types=1);

use App\Livewire\Admin\Pos\Refunds\Index;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderRefund;
use App\Models\PosCashMovement;
use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\PosSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function posSaleForRefund(?Customer $customer = null): Order
{
    $admin = actingAsAdmin();
    actingAs($admin);

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'price' => 100, 'stock' => 0]);
    $warehouse = Warehouse::default();
    WarehouseStock::findOrCreateFor($warehouse->id, $product->id, null)->update(['stock' => 10]);

    $register = PosRegister::create(['name' => 'Till', 'code' => 'T1', 'warehouse_id' => $warehouse->id, 'is_active' => true]);
    $shift = PosShift::create(['register_id' => $register->id, 'opened_by' => $admin->id, 'opening_cash' => 0, 'status' => 'open', 'opened_at' => now()]);

    return app(PosSaleService::class)->checkout([
        'order' => [
            'channel' => 'pos',
            'customer_id' => $customer?->id,
            'register_id' => $register->id,
            'shift_id' => $shift->id,
            'customer_name' => $customer?->name ?? 'Walk-in Customer',
            'status' => 'delivered',
            'subtotal' => 300,
            'total' => 300,
        ],
        'lines' => [[
            'product_id' => $product->id,
            'product_attribute_id' => null,
            'product_name' => $product->name_en,
            'attribute_data' => null,
            'quantity' => 3,
            'unit_price' => 100,
        ]],
        'payments' => [['method' => 'cash', 'amount' => 300, 'reference' => null, 'change_given' => 0]],
        'warehouse' => $warehouse,
    ]);
}

test('a partial cash refund restocks only the refunded quantity and logs a cash movement against the open shift', function () {
    $order = posSaleForRefund();
    $item = $order->items->first();
    $product = $item->product;
    $admin = auth()->user();

    $stockBefore = WarehouseStock::where('product_id', $product->id)->first()->stock;

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openRefundModal', $order->id)
        ->set("refundQuantities.{$item->id}", 1)
        ->set('refundMethod', 'cash')
        ->set('refundReason', 'Customer changed their mind')
        ->call('submitRefund');

    expect(OrderRefund::where('order_id', $order->id)->count())->toBe(1);
    $refund = OrderRefund::first();
    expect($refund->quantity)->toBe(1);
    expect($refund->amount)->toEqual(100.0);

    $stockAfter = WarehouseStock::where('product_id', $product->id)->first()->stock;
    expect($stockAfter)->toBe($stockBefore + 1);

    expect(PosCashMovement::where('type', 'refund_cash')->sum('amount'))->toEqual(100.0);
});

test('a refund cannot exceed the item quantity sold', function () {
    $order = posSaleForRefund();
    $item = $order->items->first();
    $admin = auth()->user();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openRefundModal', $order->id)
        ->set("refundQuantities.{$item->id}", 999)
        ->set('refundMethod', 'cash')
        ->call('submitRefund');

    $refund = OrderRefund::first();
    expect($refund->quantity)->toBe(3); // capped at the quantity actually sold
});

test('a store-credit refund increases the customer balance instead of touching cash', function () {
    $customer = Customer::create(['name' => 'Jane', 'phone' => '01711111111', 'store_credit_balance' => 0]);
    $order = posSaleForRefund($customer);
    $item = $order->items->first();
    $admin = auth()->user();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openRefundModal', $order->id)
        ->set("refundQuantities.{$item->id}", 2)
        ->set('refundMethod', 'store_credit')
        ->call('submitRefund');

    expect($customer->fresh()->store_credit_balance)->toEqual(200.0);
    expect(PosCashMovement::where('type', 'refund_cash')->count())->toBe(0);
});
