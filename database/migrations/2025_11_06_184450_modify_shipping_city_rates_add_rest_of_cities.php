<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_city_rates', function (Blueprint $table) {
            // Drop existing unique constraint and foreign key
            $table->dropUnique(['city_id']);
        });

        // Drop foreign key separately
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('shipping_city_rates', function (Blueprint $table) {
                $table->dropForeign(['city_id']);
            });
        }

        Schema::table('shipping_city_rates', function (Blueprint $table) {
            // Make city_id nullable for "Rest of All Cities"
            $table->unsignedBigInteger('city_id')->nullable()->change();
        });

        // Re-add foreign key but nullable (SQLite doesn't support this well, so we'll handle it in the model)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('shipping_city_rates', function (Blueprint $table) {
                $table->foreign('city_id')
                    ->references('id')
                    ->on('cities')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('shipping_city_rates', function (Blueprint $table) {
                $table->dropForeign(['city_id']);
            });
        }

        Schema::table('shipping_city_rates', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable(false)->change();
            $table->unique('city_id');
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('shipping_city_rates', function (Blueprint $table) {
                $table->foreign('city_id')
                    ->references('id')
                    ->on('cities')
                    ->onDelete('cascade');
            });
        }
    }
};
