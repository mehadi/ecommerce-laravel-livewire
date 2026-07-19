<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Postgres does not automatically index foreignId()->constrained() columns
     * (only the referenced side gets one), so these FKs — filtered/joined on
     * every shop/category/attribute-filter query — were doing sequential scans.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('product_attributes', function (Blueprint $table) {
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });

        Schema::table('product_attributes', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });
    }
};
