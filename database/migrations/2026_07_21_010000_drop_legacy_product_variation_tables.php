<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops the legacy ProductVariation/ProductVariationOption/ProductVariationCombination
 * system, superseded same-day by ProductAttribute back in November 2025. Confirmed
 * dead: no Livewire component, Blade view, route, job, factory, or test references
 * any of these three tables, and neither them nor ProductAttribute ever received
 * seed data, so there is nothing to migrate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variation_options');
        Schema::dropIfExists('product_variation_combinations');
        Schema::dropIfExists('product_variations');
    }

    public function down(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_variation_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variation_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->json('variation_data');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('buying_price', 10, 2)->nullable();
            $table->string('sku')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('sku');
        });
    }
};
