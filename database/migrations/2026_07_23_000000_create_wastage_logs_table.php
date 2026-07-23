<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wastage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_batch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wastage_logs');
    }
};
