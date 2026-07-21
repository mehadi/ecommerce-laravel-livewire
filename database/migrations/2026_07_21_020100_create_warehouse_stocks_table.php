<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('stock')->default(0);
            $table->integer('reserved')->default(0);
            $table->timestamps();

            // Uniqueness of [warehouse_id, product_id, product_attribute_id] is enforced
            // at the application layer (firstOrCreate) rather than a DB constraint,
            // since a nullable column in a composite unique index behaves
            // inconsistently across database engines.
            $table->index(['warehouse_id', 'product_id']);
            $table->index(['warehouse_id', 'product_attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_stocks');
    }
};
