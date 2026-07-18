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
        Schema::table('user_dashboard_preferences', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['user_id', 'card_key']);

            // Add new unique constraint that includes card_type
            $table->unique(['user_id', 'card_key', 'card_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_dashboard_preferences', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['user_id', 'card_key', 'card_type']);

            // Restore the old unique constraint
            $table->unique(['user_id', 'card_key']);
        });
    }
};
