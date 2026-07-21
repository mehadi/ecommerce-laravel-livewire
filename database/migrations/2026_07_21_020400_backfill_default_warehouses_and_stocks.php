<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * For every existing tenant: create its default "Main Warehouse", seed one
 * warehouse_stocks row per existing product/product_attribute from the
 * current (denormalized) stock value, and point existing stock_movements/
 * order_items rows at that warehouse. This is what keeps every current
 * single-warehouse tenant's behavior unchanged once the application code
 * starts reading/writing through WarehouseStock instead of products.stock
 * directly.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tenantIds = DB::table('tenants')->pluck('id');

        foreach ($tenantIds as $tenantId) {
            $warehouseId = DB::table('warehouses')->insertGetId([
                'tenant_id' => $tenantId,
                'name' => 'Main Warehouse',
                'code' => 'MAIN',
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('products')
                ->where('tenant_id', $tenantId)
                ->orderBy('id')
                ->chunkById(200, function ($products) use ($warehouseId, $tenantId) {
                    $rows = $products->map(fn ($product) => [
                        'tenant_id' => $tenantId,
                        'warehouse_id' => $warehouseId,
                        'product_id' => $product->id,
                        'product_attribute_id' => null,
                        'stock' => $product->stock,
                        'reserved' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->all();

                    DB::table('warehouse_stocks')->insert($rows);
                });

            DB::table('product_attributes')
                ->where('tenant_id', $tenantId)
                ->orderBy('id')
                ->chunkById(200, function ($attributes) use ($warehouseId, $tenantId) {
                    $rows = $attributes->map(fn ($attribute) => [
                        'tenant_id' => $tenantId,
                        'warehouse_id' => $warehouseId,
                        'product_id' => $attribute->product_id,
                        'product_attribute_id' => $attribute->id,
                        'stock' => $attribute->stock,
                        'reserved' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->all();

                    DB::table('warehouse_stocks')->insert($rows);
                });

            DB::table('stock_movements')->where('tenant_id', $tenantId)->update(['warehouse_id' => $warehouseId]);
            DB::table('order_items')->where('tenant_id', $tenantId)->update(['warehouse_id' => $warehouseId]);
        }
    }

    public function down(): void
    {
        // Schema-only rollback: clears the backfilled warehouse_id pointers and
        // removes the warehouse_stocks rows this migration created. The
        // warehouses/warehouse_stocks tables themselves are dropped by their
        // own create-migrations' down().
        DB::table('stock_movements')->update(['warehouse_id' => null]);
        DB::table('order_items')->update(['warehouse_id' => null]);
        DB::table('warehouse_stocks')->delete();
        DB::table('warehouses')->where('code', 'MAIN')->delete();
    }
};
