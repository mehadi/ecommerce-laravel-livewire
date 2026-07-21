<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Append-only cash-drawer audit ledger — same idiom as stock_movements
 * (one dedicated table per domain, `type` + `changed_by`/`created_by` +
 * amount, rather than a generic polymorphic activity log).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('shift_id')->constrained('pos_shifts')->cascadeOnDelete();
            $table->string('type'); // 'cash_in'|'cash_out'|'sale_cash'|'refund_cash'
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['shift_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_cash_movements');
    }
};
