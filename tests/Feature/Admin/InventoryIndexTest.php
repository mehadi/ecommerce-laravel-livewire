<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Livewire\Admin\Inventory\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

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

test('the inventory stats aggregate stock and buying-price value via SQL, including per-variant products', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();

    // Simple product: 10 units at a buying price of 5 = 50.
    Product::factory()->create(['category_id' => $category->id, 'stock' => 10, 'buying_price' => 5]);

    // Attribute-tracked product: syncPriceAndStock() never syncs buying_price
    // onto the parent, so its own (stale) buying_price column must NOT be used —
    // the value has to come from summing each variant's own buying_price * stock
    // (3 * 20) + (2 * 30) = 120.
    $variantProduct = Product::factory()->create(['category_id' => $category->id, 'stock' => 0, 'buying_price' => 999]);
    ProductAttribute::create([
        'product_id' => $variantProduct->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 10,
        'buying_price' => 20,
        'stock' => 3,
        'is_active' => true,
    ]);
    ProductAttribute::create([
        'product_id' => $variantProduct->id,
        'attribute_data' => ['Size' => 'Large'],
        'price' => 15,
        'buying_price' => 30,
        'stock' => 2,
        'is_active' => true,
    ]);

    $stats = Livewire::actingAs($admin)->test(Index::class)->viewData('stats');

    expect((float) $stats['total_value'])->toBe(170.0); // 50 + 120
    expect($stats['total_skus'])->toBe(2);
    expect($stats['total_units'])->toBe(15); // 10 + (3 + 2)
});

test('adjustWarehouseId validation is scoped to the current tenant', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);
    $admin = actingAsAdmin();

    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    $otherTenantWarehouse = Warehouse::create(['name' => 'Other Tenant Warehouse', 'code' => 'OTHER', 'is_active' => true]);

    // Back to tenant1: referencing tenant2's warehouse id must be rejected
    // instead of silently adjusting stock against another tenant's warehouse (IDOR).
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustWarehouseId', $otherTenantWarehouse->id)
        ->set('adjustMode', 'add')
        ->set('adjustQuantity', 5)
        ->set('adjustReason', 'Cross tenant attempt')
        ->call('adjustStock')
        ->assertHasErrors(['adjustWarehouseId']);

    expect(WarehouseStock::where('warehouse_id', $otherTenantWarehouse->id)->where('product_id', $product->id)->exists())->toBeFalse();
});

test('recomputing ABC classes only affects the current tenant, not every tenant on the platform', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);
    $admin = actingAsAdmin();

    $category1 = Category::factory()->create();
    $tenant1Product = Product::factory()->create(['category_id' => $category1->id, 'stock' => 5]);

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    $category2 = Category::factory()->create();
    $tenant2Product = Product::factory()->create(['category_id' => $category2->id, 'stock' => 5]);

    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('recomputeAbcClasses');

    expect($tenant1Product->fresh()->abc_class)->not->toBeNull();
    expect($tenant2Product->fresh()->abc_class)->toBeNull();
});

test('the Adjust Stock modal shows the current stock for the selected warehouse, not the tenant-wide total', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);

    $secondWarehouse = Warehouse::create(['name' => 'Overflow Storage', 'code' => 'OVERFLOW', 'is_active' => true]);
    WarehouseStock::findOrCreateFor($secondWarehouse->id, $product->id, null)->update(['stock' => 4]);

    // Tenant-wide total is now 10 (main) + 4 (overflow) = 14, but the caption
    // for the selected warehouse should show only that warehouse's figure.
    expect($product->fresh()->stock)->toBe(14);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('openAdjustModal', $product->id)
        ->set('adjustWarehouseId', $secondWarehouse->id)
        ->assertSet('adjustCurrentStock', 4)
        ->assertSee('Current stock: 4')
        ->assertDontSee('Current stock: 14');
});

test('the warehouse filter shows that warehouse\'s own stock figure, not the tenant-wide aggregate', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Multi Warehouse Item',
        'stock' => 10,
    ]);

    $secondWarehouse = Warehouse::create(['name' => 'Overflow Storage', 'code' => 'OVERFLOW', 'is_active' => true]);
    // A deliberately distinctive number so it can't be confused with any other
    // figure on the page (thresholds, pagination options, stat totals, etc).
    WarehouseStock::findOrCreateFor($secondWarehouse->id, $product->id, null)->update(['stock' => 77]);

    expect($product->fresh()->stock)->toBe(87); // tenant-wide total across both warehouses

    // Pulls the Stock column's number out of the product's own row (the first
    // "<number> units" span rendered after its name), independent of the
    // tenant-wide stat cards above the table which legitimately still show 87.
    $stockShownInRow = function (string $html) {
        $afterName = substr($html, strpos($html, 'Multi Warehouse Item'));
        preg_match('/font-medium text-zinc-900 dark:text-white">(\d+)<\/span>\s*<span class="text-xs text-zinc-500 dark:text-zinc-400">\s*units/', $afterName, $matches);

        return $matches[1] ?? null;
    };

    // Tenant-wide (no warehouse filter): the row shows the full 87 units.
    $unfiltered = Livewire::actingAs($admin)->test(Index::class)->html();
    expect($stockShownInRow($unfiltered))->toBe('87');

    // Filtered to just the second warehouse: the row must show only the 77
    // units held there, not the 87-unit tenant-wide aggregate.
    $filtered = Livewire::actingAs($admin)->test(Index::class)->set('filterWarehouse', $secondWarehouse->id)->html();
    expect($stockShownInRow($filtered))->toBe('77');
});
