<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexed but not unique, mirroring this table's existing `sku` column —
 * variant barcodes aren't given a DB-level uniqueness guarantee here (same
 * tradeoff already accepted for `sku`); the POS product search flags an
 * ambiguous multi-match rather than relying on a constraint.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attributes', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('sku')->index();
        });
    }

    public function down(): void
    {
        Schema::table('product_attributes', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};
