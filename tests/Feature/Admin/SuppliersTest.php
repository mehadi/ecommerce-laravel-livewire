<?php

declare(strict_types=1);

use App\Livewire\Admin\Suppliers\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('an admin can create a supplier', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createSupplier')
        ->set('name', 'Sundarban Organics Ltd.')
        ->set('email', 'sales@sundarban.example')
        ->set('lead_time_days', 7)
        ->call('storeSupplier')
        ->assertSet('showModal', false);

    expect(Supplier::where('name', 'Sundarban Organics Ltd.')->exists())->toBeTrue();
});

test('an admin can edit a supplier', function () {
    $admin = actingAsAdmin();
    $supplier = Supplier::create(['name' => 'Old Name', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('editSupplier', $supplier->id)
        ->set('name', 'New Name')
        ->call('updateSupplier');

    expect($supplier->fresh()->name)->toBe('New Name');
});

test('a supplier set as a product default supplier cannot be deleted', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'Sole Supplier', 'is_active' => true]);
    Product::factory()->create(['category_id' => $category->id, 'default_supplier_id' => $supplier->id]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteSupplier', $supplier->id);

    expect(Supplier::find($supplier->id))->not->toBeNull();
});

test('an unused supplier can be deleted', function () {
    $admin = actingAsAdmin();
    $supplier = Supplier::create(['name' => 'Unused Supplier', 'is_active' => true]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteSupplier', $supplier->id);

    expect(Supplier::find($supplier->id))->toBeNull();
});

test('a supplier with purchase orders cannot be deleted', function () {
    $admin = actingAsAdmin();
    $warehouse = Warehouse::default();
    $supplier = Supplier::create(['name' => 'Ordered Supplier', 'is_active' => true]);
    PurchaseOrder::create([
        'supplier_id' => $supplier->id,
        'warehouse_id' => $warehouse->id,
        'order_number' => 'PO-0001',
    ]);

    Livewire::actingAs($admin)->test(Index::class)->call('deleteSupplier', $supplier->id);

    expect(Supplier::find($supplier->id))->not->toBeNull();
});

test('a user without supplier permissions cannot create, edit, or delete a supplier', function () {
    $user = User::factory()->create();
    $supplier = Supplier::create(['name' => 'Protected Supplier', 'is_active' => true]);

    Livewire::actingAs($user)->test(Index::class)->call('createSupplier')->assertForbidden();
    Livewire::actingAs($user)->test(Index::class)->call('updateSupplier')->assertForbidden();
    Livewire::actingAs($user)->test(Index::class)->call('deleteSupplier', $supplier->id)->assertForbidden();

    expect(Supplier::find($supplier->id))->not->toBeNull();
});
