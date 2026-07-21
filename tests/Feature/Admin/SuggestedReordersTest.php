<?php

declare(strict_types=1);

use App\Livewire\Admin\Inventory\SuggestedReorders;
use App\Livewire\Admin\PurchaseOrders\Create;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('low stock and out of stock products are grouped by their default supplier', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);

    $lowStock = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Low Stock Item',
        'stock' => 3,
        'low_stock_threshold' => 10,
        'default_supplier_id' => $supplier->id,
    ]);

    $healthyStock = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Healthy Stock Item',
        'stock' => 100,
        'low_stock_threshold' => 10,
        'default_supplier_id' => $supplier->id,
    ]);

    $unassigned = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Unassigned Low Stock Item',
        'stock' => 0,
        'low_stock_threshold' => 10,
    ]);

    Livewire::actingAs($admin)
        ->test(SuggestedReorders::class)
        ->assertSee('Sundarban Organics')
        ->assertSee('Low Stock Item')
        ->assertDontSee('Healthy Stock Item')
        ->assertSee('No Supplier Assigned')
        ->assertSee('Unassigned Low Stock Item');
});

test('creating a purchase order from suggested reorders prefills the supplier and items', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'Sundarban Organics', 'is_active' => true]);

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'stock' => 2,
        'low_stock_threshold' => 10,
        'default_supplier_id' => $supplier->id,
    ]);

    Livewire::actingAs($admin)
        ->test(SuggestedReorders::class)
        ->call('createPurchaseOrder', $supplier->id)
        ->assertRedirect(route('admin.purchase-orders.create'));

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->assertSet('supplier_id', $supplier->id)
        ->assertSet('items.0.product_id', $product->id)
        ->assertSet('items.0.quantity_ordered', 18); // (10 * 2) - 2
});
