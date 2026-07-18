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
        Schema::create('landing_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // hero, features, testimonials, faq, etc.
            $table->string('title_en')->nullable();
            $table->string('title_bn')->nullable();
            $table->text('content_en')->nullable();
            $table->text('content_bn')->nullable();
            $table->json('data')->nullable(); // For flexible JSON storage
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_sections');
    }
};
