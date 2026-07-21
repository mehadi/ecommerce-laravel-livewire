<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('method'); // 'cash'|'card'|'mobile_banking'|'store_credit'
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable(); // card/mobile reference no. — record-only, no gateway call
            $table->decimal('change_given', 10, 2)->nullable(); // cash tenders only
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
