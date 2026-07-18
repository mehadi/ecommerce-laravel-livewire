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
        Schema::create('shipping_city_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->decimal('base_rate', 10, 2);
            $table->decimal('per_kg_rate', 10, 2)->default(0);
            $table->decimal('base_weight_kg', 8, 2)->default(1.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_city_rates');
    }
};
