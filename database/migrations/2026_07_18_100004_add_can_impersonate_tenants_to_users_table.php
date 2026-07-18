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
        Schema::table('users', function (Blueprint $table) {
            // Platform staff (tenant_id === null) can never hold a Spatie role/permission
            // under teams mode (model_has_roles.tenant_id is non-nullable), so this
            // capability is gated by a plain column instead — see the
            // 'impersonate tenants' gate in AppServiceProvider.
            $table->boolean('can_impersonate_tenants')->default(false)->after('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_impersonate_tenants');
        });
    }
};
