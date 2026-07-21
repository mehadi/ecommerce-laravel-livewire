<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Laravel's schema builder can't express a partial unique index (needed
 * because `product_attribute_id` is nullable and Postgres treats every NULL
 * as distinct in an ordinary composite unique index), so this is raw SQL.
 * Two indexes: one for simple products (attribute_id IS NULL), one for
 * variants (attribute_id IS NOT NULL) — together they guarantee at most one
 * WarehouseStock row per warehouse+sellable-unit, closing the race where two
 * concurrent `createOrFirst()` calls for a never-before-stocked product could
 * otherwise insert duplicate rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            'create unique index warehouse_stocks_warehouse_product_unique on warehouse_stocks (warehouse_id, product_id) where product_attribute_id is null'
        );

        DB::statement(
            'create unique index warehouse_stocks_warehouse_attribute_unique on warehouse_stocks (warehouse_id, product_attribute_id) where product_attribute_id is not null'
        );
    }

    public function down(): void
    {
        DB::statement('drop index if exists warehouse_stocks_warehouse_product_unique');
        DB::statement('drop index if exists warehouse_stocks_warehouse_attribute_unique');
    }
};
