<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive only — existing storefront/admin-manual order creation is
 * unaffected. `channel` gets a column-level default so every pre-existing row
 * backfills to 'storefront' as part of adding the column, no separate data
 * migration needed (mirrors how `low_stock_threshold` was added nullable).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('channel')->default('storefront')->after('customer_id'); // 'storefront'|'pos'|'admin_manual'
            $table->foreignId('register_id')->nullable()->after('channel')->constrained('pos_registers')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->after('register_id')->constrained('pos_shifts')->nullOnDelete();

            $table->index(['channel', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['channel', 'created_at']);
            $table->dropConstrainedForeignId('shift_id');
            $table->dropConstrainedForeignId('register_id');
            $table->dropColumn('channel');
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
