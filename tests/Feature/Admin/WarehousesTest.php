<?php

declare(strict_types=1);

use App\Livewire\Admin\Warehouses\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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
