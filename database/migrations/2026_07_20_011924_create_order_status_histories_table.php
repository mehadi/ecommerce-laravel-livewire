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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['order_id', 'changed_at']);
        });

        // Backfill one row per pre-existing order using its current status/updated_at
        // as an approximation — this is "time since the last status change we know
        // of", the same accuracy the app already had via updated_at, not a
        // regression. Genuine multi-status transition history only starts
        // accumulating from here forward via OrderObserver; past transitions can't
        // be reconstructed retroactively (no timestamps were ever recorded for
        // them), so reports built on this table treat a single history row per
        // order as "no observed transition yet" rather than real fulfillment data.
        DB::table('orders')->orderBy('id')->chunkById(500, function ($orders) {
            $now = now();

            $rows = $orders->map(fn ($order) => [
                'tenant_id' => $order->tenant_id,
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by' => null,
                'changed_at' => $order->updated_at ?? $order->created_at ?? $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (! empty($rows)) {
                DB::table('order_status_histories')->insert($rows);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
