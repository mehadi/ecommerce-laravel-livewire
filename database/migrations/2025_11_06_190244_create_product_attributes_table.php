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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->json('attribute_data'); // {"Color": "Red", "Size": "Large", "Weight": "2.5"}
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('buying_price', 10, 2)->nullable();
            $table->string('sku')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('weight_kg', 8, 2)->nullable(); // Weight from attribute or override
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Note: PostgreSQL doesn't support unique constraints on JSON columns directly
            // Consider adding application-level validation for uniqueness
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
