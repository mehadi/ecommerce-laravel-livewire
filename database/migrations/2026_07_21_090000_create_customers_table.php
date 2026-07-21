<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('store_credit_balance', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // NULL phone rows don't collide with each other (standard unique-index
            // NULL semantics on both Postgres and the sqlite test DB), so guest/quick
            // customers created without a phone number are unaffected — only two
            // customers sharing the same non-null phone within a tenant is rejected.
            $table->unique(['tenant_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
