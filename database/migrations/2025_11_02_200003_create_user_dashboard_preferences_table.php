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
        Schema::create('user_dashboard_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_key'); // e.g., 'total_revenue', 'total_orders', etc.
            $table->integer('order')->default(0); // Order/position of the card
            $table->boolean('is_visible')->default(true); // Whether card is visible
            $table->string('card_type')->default('metric'); // 'metric' or 'chart'
            $table->string('page')->default('overview'); // dashboard page this preference belongs to
            $table->timestamps();

            $table->unique(['user_id', 'card_key', 'card_type', 'page']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_preferences');
    }
};
