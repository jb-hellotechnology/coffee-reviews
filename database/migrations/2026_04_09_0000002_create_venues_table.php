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
        Schema::create('venues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('roaster_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('suggested_by')->nullable()->constrained('users')->nullOnDelete();$table->string('google_place_id')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address');
            $table->string('city');
            $table->string('postcode')->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->json('opening_hours')->nullable();
            $table->float('coffee_score')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index('city');
            $table->index('coffee_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
