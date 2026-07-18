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
        // Global (non-tenant-scoped) key-value store for platform-operator settings.
        // Deliberately not tenant-scoped: App\Models\Setting's TenantScope applies no
        // filter at all when no tenant is resolved (which is always true on Platform
        // routes), so reusing it here would read/write across every tenant's settings.
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
