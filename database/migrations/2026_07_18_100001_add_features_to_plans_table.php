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
        Schema::table('plans', function (Blueprint $table) {
            // Admin-togglable capability flags (e.g. coupons_enabled, landing_pages_enabled).
            // Unlike max_products/max_admin_users/max_custom_domains (null = unlimited),
            // an absent key here means the feature is OFF — see Plan::hasFeature().
            $table->json('features')->nullable()->after('max_custom_domains');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
