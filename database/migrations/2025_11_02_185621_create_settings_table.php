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
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        } else {
            // Table exists but may be missing columns, add them if needed
            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'key')) {
                    $table->string('key')->unique()->after('id');
                }
                if (! Schema::hasColumn('settings', 'value')) {
                    $table->text('value')->nullable()->after('key');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
