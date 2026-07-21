<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_attribute_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            // Defaults to false for pre-existing rows: whether stock was actually
            // decremented for those orders can't be reconstructed retroactively, so
            // treating them as "not deducted" is the safe choice — it avoids a
            // phantom restock if one of those orders is cancelled after this
            // migration runs. Only rows created after this point (via checkout or
            // manual admin orders for non-variant products) are trustworthy for
            // driving the cancel-restock flow.
            $table->boolean('stock_deducted')->default(false)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_attribute_id']);
            $table->dropColumn(['product_attribute_id', 'stock_deducted']);
        });
    }
};
