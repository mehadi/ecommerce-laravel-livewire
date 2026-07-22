<?php

declare(strict_types=1);

use App\Livewire\Admin\Products\Create;
use App\Livewire\Admin\Products\Index;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\CycleCount;
use App\Models\CycleCountItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

test('admin can create a product with rich descriptions intact', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();

    $richTextEn = '<h2>Daily Glow Ritual</h2><ul><li>Boosts radiance</li><li>Locks in hydration</li></ul>';
    $richTextBn = '<p><strong>ত্বকের উজ্জ্বলতা</strong> ও আর্দ্রতা ধরে রাখে।</p>';

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('category_id', $category->id)
        ->set('name_en', 'Radiant Serum Concentrate')
        ->set('name_bn', 'রেডিয়ান্ট সিরাম কনসেনট্রেট')
        ->set('description_en', $richTextEn)
        ->set('description_bn', $richTextBn)
        ->set('price', 1299.50)
        ->set('compare_at_price', 1499.00)
        ->set('sku', 'RDS-001')
        ->set('stock', 25)
        ->set('order', 3)
        ->set('is_active', true)
        ->set('is_featured', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('sku', 'RDS-001')->first();

    expect($product)->not->toBeNull();
    expect($product)
        ->description_en->toBe($richTextEn)
        ->description_bn->toBe($richTextBn)
        ->is_featured->toBeTrue();
});

test('admin can update product rich descriptions without stripping markup', function () {
    $admin = actingAsAdmin();
    $category = Category::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name_en' => 'Hydrating Elixir',
        'description_en' => '<p>Original description</p>',
        'description_bn' => '<p>মূল বর্ণনা</p>',
        'sku' => 'HYD-123',
        'price' => 990,
        'compare_at_price' => 1200,
        'stock' => 10,
        'is_featured' => false,
    ]);

    $updatedEn = '<h3>New Glow Formula</h3><p><strong>Clinically proven</strong> to boost luminosity in 7 days.</p>';
    $updatedBn = '<p><em>দ্রুত ফলাফল</em> এবং ত্বকের দীপ্তি বাড়ায়।</p>';

    Livewire::actingAs($admin)
        ->test(Create::class, ['product' => $product->fresh()])
        ->set('name_en', 'Hydrating Elixir Plus')
        ->set('description_en', $updatedEn)
        ->set('description_bn', $updatedBn)
        ->set('is_featured', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product->refresh();

    expect($product)
        ->name_en->toBe('Hydrating Elixir Plus')
        ->description_en->toBe($updatedEn)
        ->description_bn->toBe($updatedBn)
        ->is_featured->toBeTrue();
});

test('a product can be created with zero stock', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name_en', 'Preorder Only Item')
        ->set('price', 500)
        ->set('stock', 0)
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::where('name_en', 'Preorder Only Item')->first())
        ->not->toBeNull()
        ->stock->toBe(0);
});

test('a batch-tracked product can be created without entering stock', function () {
    $admin = actingAsAdmin();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name_en', 'Batch Tracked Serum')
        ->set('price', 750)
        ->set('tracks_batches', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::where('name_en', 'Batch Tracked Serum')->first())
        ->not->toBeNull()
        ->tracks_batches->toBeTrue()
        ->stock->toBe(0);
});

test('product and variant barcodes persist through the form', function () {
    $admin = actingAsAdmin();

    $product = Product::factory()->create(['sku' => 'BAR-01', 'price' => 100, 'stock' => 5]);

    ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'L'],
        'price' => 120,
        'stock' => 3,
    ]);

    Livewire::actingAs($admin)
        ->test(Create::class, ['product' => $product->fresh()])
        ->set('barcode', '4006381333931')
        ->set('productAttributes.0.barcode', '4006381333948')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh())
        ->barcode->toBe('4006381333931');

    expect($product->fresh()->productAttributes->first())
        ->barcode->toBe('4006381333948');
});

test('editing a product with variants keeps existing variant IDs stable', function () {
    $admin = actingAsAdmin();

    $product = Product::factory()->create(['price' => 100, 'stock' => 0]);

    $variantSmall = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'S'],
        'price' => 90,
        'stock' => 4,
        'sku' => 'VAR-S',
    ]);
    $variantLarge = ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'L'],
        'price' => 110,
        'stock' => 6,
        'sku' => 'VAR-L',
    ]);

    Livewire::actingAs($admin)
        ->test(Create::class, ['product' => $product->fresh()])
        ->set('name_en', $product->name_en.' Updated')
        ->set('productAttributes.0.price', 95)
        ->call('save')
        ->assertHasNoErrors();

    // The old implementation deleted and recreated every variant on edit,
    // which cascaded away warehouse stock rows and stock-movement history.
    $freshIds = $product->fresh()->productAttributes->pluck('id')->sort()->values()->all();

    expect($freshIds)->toBe(collect([$variantSmall->id, $variantLarge->id])->sort()->values()->all());
    expect($variantSmall->fresh()->price)->toEqual(95);
    expect($variantLarge->fresh()->sku)->toBe('VAR-L');
});

test('toggling attribute values preserves already-entered variant pricing', function () {
    $admin = actingAsAdmin();

    $size = Attribute::create(['name' => 'Size', 'slug' => 'size', 'type' => 'select', 'is_active' => true, 'order' => 1]);
    $small = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'S', 'display_value' => 'Small', 'is_active' => true, 'order' => 1]);
    $large = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'L', 'display_value' => 'Large', 'is_active' => true, 'order' => 2]);

    $component = Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name_en', 'Variant Test Tee')
        ->call('toggleAttributeValue', $size->id, $small->id)
        ->set('productAttributes.0.price', 123.45)
        ->set('productAttributes.0.stock', 7)
        ->call('toggleAttributeValue', $size->id, $large->id);

    $rows = collect($component->get('productAttributes'));

    expect($rows)->toHaveCount(2);

    $smallRow = $rows->first(fn ($row) => ($row['attribute_data']['Size'] ?? null) === 'Small');

    expect($smallRow['price'])->toEqual(123.45);
    expect($smallRow['stock'])->toEqual(7);
});

test('duplicate SKU within the tenant is rejected', function () {
    $admin = actingAsAdmin();

    Product::factory()->create(['sku' => 'TAKEN-SKU']);

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('name_en', 'Another Product')
        ->set('price', 10)
        ->set('stock', 1)
        ->set('sku', 'TAKEN-SKU')
        ->call('save')
        ->assertHasErrors(['sku']);
});

test('duplicating a product creates an inactive draft copy with variants', function () {
    $admin = actingAsAdmin();

    $product = Product::factory()->create([
        'name_en' => 'Original Tee',
        'sku' => 'ORIG-1',
        'barcode' => '1234567890128',
        'price' => 100,
        'stock' => 0,
        'is_active' => true,
    ]);

    ProductAttribute::create([
        'product_id' => $product->id,
        'attribute_data' => ['Size' => 'M'],
        'price' => 105,
        'stock' => 9,
        'sku' => 'ORIG-1-M',
    ]);

    Livewire::actingAs($admin)
        ->test(Create::class, ['product' => $product->fresh()])
        ->call('duplicate')
        ->assertHasNoErrors();

    $copy = Product::where('name_en', 'Original Tee (Copy)')->first();

    expect($copy)->not->toBeNull()
        ->is_active->toBeFalse()
        ->sku->toBeNull()
        ->barcode->toBeNull();

    $copyVariant = $copy->productAttributes->first();

    expect($copyVariant)->not->toBeNull()
        ->stock->toBe(0)
        ->sku->toBeNull();
    expect($copyVariant->attribute_data)->toBe(['Size' => 'M']);
});

test('a user without product permissions cannot open the product form', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->assertForbidden();
});

test('rich text attachments persist to public disk', function () {
    Storage::fake('public');

    $admin = actingAsAdmin();

    $component = Livewire::actingAs($admin)
        ->test(Create::class);

    // PNG, not JPG — the container's GD build lacks imagejpeg().
    $upload = UploadedFile::fake()->image('editor-attachment.png', 640, 640);

    $component->set('pendingAttachments', [$upload]);

    $component->call('storePendingAttachment');

    $payload = data_get($component->effects, 'returns.0');

    expect($payload)
        ->toBeArray()
        ->and($payload)
        ->toHaveKeys(['url', 'path']);

    Storage::disk('public')->assertExists($payload['path']);

    expect($component->instance()->pendingAttachments)->toBe([]);
});

test('admin can bulk delete selected products', function () {
    $admin = actingAsAdmin();

    $toDelete = Product::factory()->count(2)->create();
    $toKeep = Product::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('selectedItems', $toDelete->pluck('id')->toArray())
        ->call('bulkDelete')
        ->assertHasNoErrors();

    expect(Product::whereIn('id', $toDelete->pluck('id'))->count())->toBe(0);
    expect(Product::find($toKeep->id))->not->toBeNull();
});

test('bulk deleting a product referenced elsewhere does not destroy its images first', function () {
    Storage::fake('public');
    $admin = actingAsAdmin();

    $warehouse = Warehouse::create([
        'name' => 'Main',
        'code' => 'MAIN',
        'is_default' => true,
        'is_active' => true,
    ]);

    $referenced = Product::factory()->create([
        'primary_image' => 'products/referenced.jpg',
    ]);
    Storage::disk('public')->put($referenced->primary_image, 'fake-image-content');

    $cycleCount = CycleCount::create([
        'warehouse_id' => $warehouse->id,
        'status' => 'pending',
        'scope' => 'all',
    ]);
    CycleCountItem::create([
        'cycle_count_id' => $cycleCount->id,
        'product_id' => $referenced->id,
        'expected_quantity' => 5,
    ]);

    $deletable = Product::factory()->create([
        'primary_image' => 'products/deletable.jpg',
    ]);
    Storage::disk('public')->put($deletable->primary_image, 'fake-image-content');

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('selectedItems', [$referenced->id, $deletable->id])
        ->call('bulkDelete')
        ->assertHasNoErrors();

    // The referenced product can't be deleted at the DB level (it's still
    // pointed to by a cycle count item), so its row and images must survive
    // — regardless of how Livewire's test harness surfaces the flashed
    // error message (see PosTerminalTest for the same convention).
    expect(Product::find($referenced->id))->not->toBeNull();
    Storage::disk('public')->assertExists($referenced->primary_image);

    // ...while the unrelated, actually-deletable product still gets removed.
    expect(Product::find($deletable->id))->toBeNull();
    Storage::disk('public')->assertMissing($deletable->primary_image);
});

test('non-image rich text attachments are rejected', function () {
    Storage::fake('public');

    $admin = actingAsAdmin();

    $component = Livewire::actingAs($admin)
        ->test(Create::class);

    $upload = UploadedFile::fake()->create('payload.svg', 4, 'image/svg+xml');

    $component->set('pendingAttachments', [$upload]);

    $component->call('storePendingAttachment')
        ->assertHasErrors(['pendingAttachments.0']);

    expect($component->instance()->pendingAttachments)->toBe([]);
});

test('total value stat sums price times stock, including per-variant totals', function () {
    $admin = actingAsAdmin();

    // Simple product: 10 * 5 = 50.
    Product::factory()->create(['price' => 10, 'stock' => 5, 'is_active' => true]);

    // Attribute-tracked product: synced price is the min *active* variant
    // price (20), synced stock is the sum of every variant's stock whether
    // active or not (3 + 2 = 5) — so the correct contribution is 20 * 5 =
    // 100, not (price alone) 20.
    $variantProduct = Product::factory()->create(['price' => 0, 'stock' => 0, 'is_active' => true]);
    ProductAttribute::create([
        'product_id' => $variantProduct->id,
        'attribute_data' => ['Size' => 'Small'],
        'price' => 20,
        'stock' => 3,
        'is_active' => true,
    ]);
    ProductAttribute::create([
        'product_id' => $variantProduct->id,
        'attribute_data' => ['Size' => 'Large'],
        'price' => 30,
        'stock' => 2,
        'is_active' => false,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertViewHas('stats', function ($stats) {
            return (float) $stats['total_value'] === 150.0;
        });
});

test('low-stock badge uses the per-product/tenant threshold, not a hardcoded 10', function () {
    Setting::set('low_stock_threshold', '3');
    $admin = actingAsAdmin();

    // Stock of 8 is above the old hardcoded ≤10 badge trigger's replacement
    // value here, but still within this product's own override threshold.
    $product = Product::factory()->create([
        'stock' => 8,
        'low_stock_threshold' => 10,
        'is_active' => true,
    ]);

    // Sanity check: with the tenant default of 3 (not this product's
    // override of 10), stock of 8 would NOT be considered low.
    expect($product->fresh()->isLowStock())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->assertSee('8 '.__('left'));

    // A product relying on the tenant default (no override) at the same
    // stock level is correctly NOT flagged, proving the threshold isn't a
    // hardcoded constant shared by every row.
    $defaultThresholdProduct = Product::factory()->create([
        'stock' => 8,
        'low_stock_threshold' => null,
        'is_active' => true,
    ]);

    expect($defaultThresholdProduct->fresh()->isLowStock())->toBeFalse();
});

test('low stock filter matches per-product thresholds via SQL, not a full in-PHP scan', function () {
    Setting::set('low_stock_threshold', '10');
    $admin = actingAsAdmin();

    // Below the tenant default threshold (10) — low stock.
    $lowStock = Product::factory()->create(['stock' => 5, 'low_stock_threshold' => null, 'is_active' => true]);
    // Above the tenant default, but within its own override — low stock.
    $overrideLowStock = Product::factory()->create(['stock' => 40, 'low_stock_threshold' => 50, 'is_active' => true]);
    // Comfortably above any threshold — not low stock.
    $healthy = Product::factory()->create(['stock' => 500, 'low_stock_threshold' => null, 'is_active' => true]);
    // Zero stock is "out of stock", not "low stock".
    $outOfStock = Product::factory()->create(['stock' => 0, 'low_stock_threshold' => null, 'is_active' => true]);

    $component = Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('filterStock', 'low_stock');

    $ids = $component->viewData('products')->pluck('id');

    expect($ids)->toContain($lowStock->id)
        ->toContain($overrideLowStock->id)
        ->not->toContain($healthy->id)
        ->not->toContain($outOfStock->id);
});

test('category_id and default_supplier_id validation is scoped to the current tenant', function () {
    $tenant1 = Tenant::firstOrCreate(['slug' => 'default'], ['name' => 'Default Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);
    $admin1 = actingAsAdmin();

    $tenant2 = Tenant::create(['slug' => 'second-store', 'name' => 'Second Store', 'status' => 'active']);
    app()->instance('currentTenant', $tenant2);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant2->id);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $otherCategory = Category::factory()->create();
    $otherSupplier = Supplier::create(['name' => 'Other Tenant Supplier', 'is_active' => true]);

    // Back to tenant1: referencing tenant2's category/supplier IDs must be
    // rejected instead of silently attaching cross-tenant data (IDOR).
    app()->instance('currentTenant', $tenant1);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant1->id);

    Livewire::actingAs($admin1)
        ->test(Create::class)
        ->set('name_en', 'Cross Tenant Attempt')
        ->set('price', 10)
        ->set('stock', 1)
        ->set('category_id', $otherCategory->id)
        ->set('default_supplier_id', $otherSupplier->id)
        ->call('save')
        ->assertHasErrors(['category_id', 'default_supplier_id']);

    expect(Product::where('name_en', 'Cross Tenant Attempt')->exists())->toBeFalse();

    // A category/supplier that genuinely belongs to tenant1 still validates fine.
    $ownCategory = Category::factory()->create();
    $ownSupplier = Supplier::create(['name' => 'Own Tenant Supplier', 'is_active' => true]);

    Livewire::actingAs($admin1)
        ->test(Create::class)
        ->set('name_en', 'Same Tenant Product')
        ->set('price', 10)
        ->set('stock', 1)
        ->set('category_id', $ownCategory->id)
        ->set('default_supplier_id', $ownSupplier->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::where('name_en', 'Same Tenant Product')->exists())->toBeTrue();
});
