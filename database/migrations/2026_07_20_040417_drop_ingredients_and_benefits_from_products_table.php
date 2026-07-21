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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['ingredients_en', 'ingredients_bn', 'benefits_en', 'benefits_bn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('ingredients_en')->nullable()->after('description_bn');
            $table->text('ingredients_bn')->nullable()->after('ingredients_en');
            $table->text('benefits_en')->nullable()->after('ingredients_bn');
            $table->text('benefits_bn')->nullable()->after('benefits_en');
        });
    }
};
