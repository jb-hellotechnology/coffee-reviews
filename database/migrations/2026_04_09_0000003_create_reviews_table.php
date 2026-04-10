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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('venue_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('ai_analysed')->default(false);
            $table->json('ai_tags')->nullable();
            $table->boolean('verified')->default(true);
            $table->timestamps();

            $table->index(['venue_id', 'verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
