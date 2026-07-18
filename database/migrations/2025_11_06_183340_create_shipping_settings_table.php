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
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->string('type')->default('flat'); // flat, weight, city
            $table->decimal('flat_rate', 10, 2)->nullable(); // For flat shipping
            $table->decimal('base_weight_kg', 8, 2)->default(1.00); // Base weight threshold
            $table->decimal('base_rate', 10, 2)->nullable(); // Base rate for weight/city shipping
            $table->decimal('per_kg_rate', 10, 2)->nullable(); // Rate per KG beyond base weight
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
