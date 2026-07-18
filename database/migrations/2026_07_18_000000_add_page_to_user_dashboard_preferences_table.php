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
        Schema::table('user_dashboard_preferences', function (Blueprint $table) {
            $table->string('page')->default('overview')->after('card_type');
        });

        // Existing rows are pure UI-layout preference data with no reliable way
        // to infer which of the new 5 pages they belonged to. It's safe to
        // truncate — createDefaultPreferences() lazily repopulates per user
        // on their next visit to each page.
        DB::table('user_dashboard_preferences')->truncate();

        Schema::table('user_dashboard_preferences', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['user_id', 'card_key', 'card_type']);

            // Add new unique constraint that includes page
            $table->unique(['user_id', 'card_key', 'card_type', 'page']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_dashboard_preferences', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['user_id', 'card_key', 'card_type', 'page']);

            // Restore the old unique constraint
            $table->unique(['user_id', 'card_key', 'card_type']);
        });

        DB::table('user_dashboard_preferences')->truncate();

        Schema::table('user_dashboard_preferences', function (Blueprint $table) {
            $table->dropColumn('page');
        });
    }
};
