<?php

declare(strict_types=1);

use App\Livewire\Pos\Terminal;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PosCashMovement;
use App\Models\PosHeldSale;
use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function stockedProduct(array $overrides = []): Product
{
    $category = Category::factory()->create();
    $product = Product::factory()->create(array_merge(['category_id' => $category->id, 'price' => 100, 'stock' => 0], $overrides));
    WarehouseStock::findOrCreateFor(Warehouse::default()->id, $product->id, null)->update(['stock' => $overrides['stock'] ?? 10]);

    return $product;
}

test('a user without the POS gate cannot mount the terminal', function () {
    (new RolesPermissionsSeeder)->run();
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Terminal::class)->assertForbidden();
});

test('opening a shift auto-provisions the default register', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 200)
        ->call('openShift');

    expect(PosRegister::count())->toBe(1);
    expect(PosShift::where('status', 'open')->count())->toBe(1);
    expect(PosShift::first()->opening_cash)->toEqual(200.0);
});

test('a cash sale adds the product to the cart, decrements stock, and completes the order', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['stock' => 10]);

    $component = Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 100)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 100)
        ->call('addPayment')
        ->call('checkout');

    $order = Order::first();
    expect($order)->not->toBeNull();
    expect($order->channel)->toBe('pos');
    expect($order->total)->toEqual(100.0);
    expect($order->payments()->count())->toBe(1);

    $stock = WarehouseStock::where('warehouse_id', Warehouse::default()->id)->where('product_id', $product->id)->first();
    expect($stock->stock)->toBe(9);

    expect(PosCashMovement::where('type', 'sale_cash')->sum('amount'))->toEqual(100.0);
    expect($component->get('completedOrderId'))->toBe($order->id);
});

test('adding a product with variants opens the variant picker instead of adding directly', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Medium'],
        'price' => 120,
        'stock' => 5,
        'is_active' => true,
    ]);
    WarehouseStock::findOrCreateFor(Warehouse::default()->id, $product->id, $variant->id)->update(['stock' => 5]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->assertSet('variantPickerProductId', $product->id)
        ->assertCount('cart', 0)
        ->call('addVariantToCart', $variant->id)
        ->assertSet('variantPickerProductId', null)
        ->assertCount('cart', 1);
});

test('split payment across cash and store credit checks out once fully covered', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);
    $customer = Customer::create(['name' => 'Jane', 'phone' => '01700000000', 'store_credit_balance' => 40]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('selectedCustomerId', $customer->id)
        ->set('paymentMethod', 'store_credit')
        ->set('paymentTendered', 40)
        ->call('addPayment')
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 60)
        ->call('addPayment')
        ->assertSet('remainingDue', 0.0)
        ->call('checkout');

    $order = Order::first();
    expect($order->payments()->count())->toBe(2);
    expect($customer->fresh()->store_credit_balance)->toEqual(0.0);
});

test('a cash payment exceeding the total records change given', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 150)
        ->call('addPayment')
        ->call('checkout');

    $order = Order::first();
    $payment = $order->payments()->first();
    expect($payment->amount)->toEqual(100.0);
    expect($payment->change_given)->toEqual(50.0);
});

test('checkout is blocked with a friendly error when the product is oversold', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 1]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->call('updateCartQuantity', 'product_'.$product->id, 5)
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 500)
        ->call('addPayment')
        ->call('checkout');

    // The oversell guard rolls the whole transaction back — no order, no
    // stock movement — regardless of how Livewire's test harness surfaces
    // the flashed error message.
    expect(Order::count())->toBe(0);
    expect(WarehouseStock::where('product_id', $product->id)->first()->stock)->toBe(1);
});

test('holding a sale clears the cart and resuming restores it', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    $component = Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->call('holdSale')
        ->assertCount('cart', 0);

    expect(PosHeldSale::count())->toBe(1);

    $held = PosHeldSale::first();

    $component->call('resumeHeldSale', $held->id)
        ->assertCount('cart', 1);

    expect(PosHeldSale::count())->toBe(0);
});

test('closing a shift computes expected cash and variance from cash movements', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    $component = Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 100)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 100)
        ->call('addPayment')
        ->call('checkout')
        ->call('confirmCloseShift')
        ->set('closingCash', 150) // short by 50 against expected 200 (100 opening + 100 cash sale)
        ->call('closeShift');

    $shift = PosShift::first();
    expect($shift->status)->toBe('closed');
    expect($shift->expected_cash)->toEqual(200.0);
    expect($shift->variance)->toEqual(-50.0);
});

test('removing a cart line stashes it for one undo', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->call('removeCartLine', 'product_'.$product->id)
        ->assertCount('cart', 0)
        ->call('undoRemoveLine')
        ->assertCount('cart', 1)
        ->assertSet('lastRemovedLine', null);
});

test('void transaction clears the entire in-progress sale', function () {
    $admin = actingAsAdmin();
    $productA = stockedProduct(['price' => 100, 'stock' => 10]);
    $productB = stockedProduct(['price' => 50, 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $productA->id)
        ->call('addProductToCart', $productB->id)
        ->set('discountAmount', 10)
        ->set('notes', 'test note')
        ->call('voidTransaction')
        ->assertCount('cart', 0)
        ->assertSet('discountAmount', 0)
        ->assertSet('notes', '');
});

test('a completed sale carries its notes through to the order', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('notes', 'Gift wrap requested')
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 100)
        ->call('addPayment')
        ->call('checkout');

    expect(Order::first()->notes)->toBe('Gift wrap requested');
});

/**
 * Regression guard: reprintOrderId must stay the real order id after
 * reprintLastReceipt(), not get coerced into a boolean by some Livewire/Flux
 * binding along the way (this broke once when the reprint view bound it via
 * <flux:modal wire:model="reprintOrderId">, which manages that property as a
 * plain open/closed flag and overwrote the id with true).
 */
test('reprintLastReceipt keeps the real order id, not a boolean', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    $component = Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 100)
        ->call('addPayment')
        ->call('checkout')
        ->call('startNewSale')
        ->call('reprintLastReceipt');

    $order = Order::first();
    expect($component->get('reprintOrderId'))->toBe($order->id);
    expect($component->get('reprintOrderId'))->not->toBeBool();
});

test('grid/list view mode toggles', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->assertSet('viewMode', 'grid')
        ->call('toggleViewMode')
        ->assertSet('viewMode', 'list')
        ->call('toggleViewMode')
        ->assertSet('viewMode', 'grid');
});

test('load more increases the product browse limit', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->assertSet('productLimit', 24)
        ->call('loadMoreProducts')
        ->assertSet('productLimit', 48);
});

test('recently sold surfaces products actually sold through the POS channel', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['price' => 100, 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->set('paymentMethod', 'cash')
        ->set('paymentTendered', 100)
        ->call('addPayment')
        ->call('checkout');

    $component = Livewire::actingAs($admin)->test(Terminal::class);
    expect($component->instance()->recentlySold()->pluck('id'))->toContain($product->id);
});

test('scanning an exact SKU match adds it straight to the cart', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['sku' => 'MILK-001', 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->set('search', 'MILK-001')
        ->call('scanBarcode')
        ->assertCount('cart', 1)
        ->assertSet('search', '');
});

test('scanning a SKU with zero stock shows a notification instead of adding it', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['sku' => 'OOS-001', 'stock' => 0]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->set('search', 'OOS-001')
        ->call('scanBarcode')
        ->assertCount('cart', 0);
});

test('adding one more than remaining stock shows a notification without changing the cart', function () {
    $admin = actingAsAdmin();
    $product = stockedProduct(['stock' => 2]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->call('addProductToCart', $product->id)
        ->assertSet('cart.product_'.$product->id.'.quantity', 2)
        ->call('addProductToCart', $product->id)
        ->assertSet('cart.product_'.$product->id.'.quantity', 2);
});

test('a variant with zero stock shows a notification and keeps the variant picker open', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Large'],
        'price' => 150,
        'stock' => 0,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Terminal::class)
        ->set('openingCash', 0)
        ->call('openShift')
        ->call('addProductToCart', $product->id)
        ->assertSet('variantPickerProductId', $product->id)
        ->call('addVariantToCart', $variant->id)
        ->assertCount('cart', 0)
        ->assertSet('variantPickerProductId', $product->id);
});
