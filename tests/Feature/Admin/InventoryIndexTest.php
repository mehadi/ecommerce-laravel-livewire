<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Livewire\Admin\Inventory\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('a user without any admin-panel role cannot open the Inventory component', function () {
    // Spatie's hasPermissionTo() throws PermissionDoesNotExist if the permission
    // row itself isn't registered yet — seed it (as RolesPermissionsSeeder does
    // in every real deployment) so this test exercises "denied because the user
    // lacks it", not "denied because the permission was never created".
    Permission::firstOrCreate(['name' => 'view inventory', 'guard_name' => 'web']);

    $user = User::factory()->create();

    // Laravel's exception handler renders a mount()-time AuthorizationException
    // as a 403 response rather than letting it bubble up as a PHP exception here.
    Livewire::actingAs($user)->test(Index::class)->assertStatus(403);
});

test('an admin can search, filter by stock status, and sort the inventory list', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();

    $inStock = Product::factory()->create(['category_id' => $category->id, 'name_en' => 'Glow Serum', 'stock' => 50]);
    $outOfStock = Product::factory()->create(['category_id' => $category->id, 'name_en' => 'Matte Lipstick', 'stock' => 0]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee('Glow Serum')
        ->assertSee('Matte Lipstick')
        ->set('search', 'Glow')
        ->assertSee('Glow Serum')
        ->assertDontSee('Matte Lipstick')
        ->set('search', '')
        ->set('filterStock', 'out_of_stock')
        ->assertDontSee('Glow Serum')
        ->assertSee('Matte Lipstick');
});

test('an admin can adjust stock for a simple product and the movement is logged', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustMode', 'add')
        ->set('adjustQuantity', 5)
        ->set('adjustReason', 'Supplier restock')
        ->call('adjustStock')
        ->assertSet('showAdjustModal', false);

    $product->refresh();
    expect($product->stock)->toBe(15);

    $movement = StockMovement::where('product_id', $product->id)->latest('id')->first();
    expect($movement->quantity_before)->toBe(10);
    expect($movement->quantity_after)->toBe(15);
    expect($movement->reason)->toBe('Supplier restock');
    expect($movement->changed_by)->toBe($admin->id);
});

test('an admin can adjust stock for individual variants of a product', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 0]);

    $variant = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 100,
        'stock' => 10,
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set("variantQuantities.{$variant->id}", 3)
        ->set('adjustReason', 'Damaged units removed')
        ->call('adjustStock');

    expect($variant->fresh()->stock)->toBe(3);
    expect($product->fresh()->stock)->toBe(3);

    $movement = StockMovement::where('product_attribute_id', $variant->id)->latest('id')->first();
    expect($movement->quantity_before)->toBe(10);
    expect($movement->quantity_after)->toBe(3);
});

test('an admin can reserve and release stock without changing the physical stock count', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 20]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustMode', 'reserve')
        ->set('adjustQuantity', 8)
        ->set('adjustReason', 'Wholesale hold')
        ->call('adjustStock');

    $warehouseStock = WarehouseStock::where('product_id', $product->id)->whereNull('product_attribute_id')->first();
    expect($warehouseStock->stock)->toBe(20);
    expect($warehouseStock->reserved)->toBe(8);
    expect($product->fresh()->getSyncedAvailable())->toBe(12);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustMode', 'release')
        ->set('adjustQuantity', 3)
        ->set('adjustReason', 'Wholesale order cancelled')
        ->call('adjustStock');

    expect($warehouseStock->fresh()->reserved)->toBe(5);

    $reservationMovements = StockMovement::where('product_id', $product->id)
        ->where('type', StockMovementType::Reservation->value)
        ->count();
    expect($reservationMovements)->toBe(2);
});

test('reserving more than the physical stock caps at the available quantity', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 5]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustMode', 'reserve')
        ->set('adjustQuantity', 999)
        ->set('adjustReason', 'Attempted over-reservation')
        ->call('adjustStock');

    $warehouseStock = WarehouseStock::where('product_id', $product->id)->whereNull('product_attribute_id')->first();
    expect($warehouseStock->reserved)->toBe(5);
});

test('an admin can adjust stock in a specific warehouse when more than one exists', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

    $mainWarehouse = Warehouse::default();
    $secondWarehouse = Warehouse::create(['name' => 'Overflow Storage', 'code' => 'OVERFLOW', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustWarehouseId', $secondWarehouse->id)
        ->set('adjustMode', 'add')
        ->set('adjustQuantity', 6)
        ->set('adjustReason', 'New stock at overflow location')
        ->call('adjustStock');

    $mainStock = WarehouseStock::where('warehouse_id', $mainWarehouse->id)->where('product_id', $product->id)->first();
    $secondStock = WarehouseStock::where('warehouse_id', $secondWarehouse->id)->where('product_id', $product->id)->first();

    expect($mainStock->stock)->toBe(10);
    expect($secondStock->stock)->toBe(6);
    expect($product->fresh()->stock)->toBe(16);
});

test('an admin can update the default low stock threshold', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openThresholdModal')
        ->set('lowStockThresholdSetting', 25)
        ->call('saveThreshold')
        ->assertSet('showThresholdModal', false);

    expect(Setting::get('low_stock_threshold'))->toBe('25');
});
