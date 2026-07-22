<?php

declare(strict_types=1);

use App\Livewire\Admin\Warehouses\Index;
use App\Models\Category;
use App\Models\CycleCount;
use App\Models\PosRegister;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\PurchaseOrder;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

test('an admin can create a warehouse', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createWarehouse')
        ->set('name', 'Overflow Storage')
        ->set('code', 'OVERFLOW')
        ->set('city', 'Chattogram')
        ->call('storeWarehouse')
        ->assertSet('showModal', false);

    expect(Warehouse::where('code', 'OVERFLOW')->exists())->toBeTrue();
});

test('an admin can edit a warehouse', function () {
    $admin = actingAsAdmin();
    $warehouse = Warehouse::create(['name' => 'Old Name', 'code' => 'OLD', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editWarehouse', $warehouse->id)
        ->set('name', 'New Name')
        ->call('updateWarehouse');

    expect($warehouse->fresh()->name)->toBe('New Name');
});

test('setting a warehouse as default unsets the previous default', function () {
    $admin = actingAsAdmin();
    $main = Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('setDefault', $second->id);

    expect($second->fresh()->is_default)->toBeTrue();
    expect($main->fresh()->is_default)->toBeFalse();
});

test('the default warehouse cannot be deleted', function () {
    $admin = actingAsAdmin();
    $main = Warehouse::default();

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $main->id);

    expect(Warehouse::find($main->id))->not->toBeNull();
});

test('a warehouse still holding stock cannot be deleted', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);
    WarehouseStock::findOrCreateFor($second->id, $product->id, null)->update(['stock' => 5]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->not->toBeNull();
});

test('an empty non-default warehouse can be deleted', function () {
    $admin = actingAsAdmin();
    Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->toBeNull();
});

test('a warehouse referenced by a stock transfer cannot be deleted', function () {
    $admin = actingAsAdmin();
    $main = Warehouse::default();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    StockTransfer::create([
        'from_warehouse_id' => $main->id,
        'to_warehouse_id' => $second->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->not->toBeNull();
});

test('a warehouse referenced by a purchase order cannot be deleted', function () {
    $admin = actingAsAdmin();
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    PurchaseOrder::create(['supplier_id' => $supplier->id, 'warehouse_id' => $second->id, 'status' => 'ordered']);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->not->toBeNull();
});

test('a warehouse referenced by a cycle count cannot be deleted', function () {
    $admin = actingAsAdmin();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    CycleCount::create(['warehouse_id' => $second->id, 'status' => 'pending', 'scope' => 'all']);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->not->toBeNull();
});

test('a warehouse referenced by a product batch cannot be deleted', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    ProductBatch::create([
        'warehouse_id' => $second->id,
        'product_id' => $product->id,
        'batch_number' => 'LOT-001',
        'quantity' => 10,
        'received_at' => now(),
    ]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->not->toBeNull();
});

test('a warehouse with a pos register cannot be deleted (no silent cascade)', function () {
    $admin = actingAsAdmin();
    $second = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    $register = PosRegister::create(['name' => 'Till', 'code' => 'T1', 'warehouse_id' => $second->id, 'is_active' => true]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteWarehouse', $second->id);

    expect(Warehouse::find($second->id))->not->toBeNull();
    expect(PosRegister::find($register->id))->not->toBeNull();
});

test('a user without warehouse permissions cannot create, update, set default, or delete a warehouse', function () {
    // Spatie's hasPermissionTo() throws PermissionDoesNotExist if the permission
    // row itself isn't registered yet — seed the rows so this exercises "denied
    // because the user lacks the permission", not "denied because it was never created".
    foreach (['view warehouses', 'create warehouses', 'edit warehouses', 'delete warehouses'] as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $user = User::factory()->create();
    $warehouse = Warehouse::create(['name' => 'Overflow', 'code' => 'OVERFLOW', 'is_active' => true]);

    Livewire::actingAs($user)->test(Index::class)->call('createWarehouse')->assertStatus(403);
    Livewire::actingAs($user)->test(Index::class)->call('storeWarehouse')->assertStatus(403);
    Livewire::actingAs($user)->test(Index::class)->call('updateWarehouse')->assertStatus(403);
    Livewire::actingAs($user)->test(Index::class)->call('setDefault', $warehouse->id)->assertStatus(403);
    Livewire::actingAs($user)->test(Index::class)->call('deleteWarehouse', $warehouse->id)->assertStatus(403);

    expect(Warehouse::find($warehouse->id))->not->toBeNull();
});

test('the same warehouse code can be reused across different tenants', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);

    $admin1 = actingAsAdmin();

    Livewire::actingAs($admin1)
        ->test(Index::class)
        ->call('createWarehouse')
        ->set('name', 'Main Warehouse')
        ->set('code', 'MAIN')
        ->call('storeWarehouse')
        ->assertSet('showModal', false)
        ->assertHasNoErrors();

    expect(Warehouse::where('code', 'MAIN')->where('tenant_id', $tenant1->id)->exists())->toBeTrue();

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant2->id);

    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin2 = User::factory()->create();
    $admin2->assignRole('admin');

    Livewire::actingAs($admin2)
        ->test(Index::class)
        ->call('createWarehouse')
        ->set('name', 'Main Warehouse')
        ->set('code', 'MAIN')
        ->call('storeWarehouse')
        ->assertSet('showModal', false)
        ->assertHasNoErrors();

    expect(Warehouse::where('code', 'MAIN')->where('tenant_id', $tenant2->id)->exists())->toBeTrue();
    expect(DB::table('warehouses')->where('code', 'MAIN')->count())->toBe(2);
});
