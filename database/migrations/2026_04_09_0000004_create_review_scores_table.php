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
        Schema::create('review_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('review_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('espresso')->unsigned()->nullable();
            $table->tinyInteger('milk_work')->unsigned()->nullable();
            $table->tinyInteger('filter_options')->unsigned()->nullable();
            $table->tinyInteger('bean_sourcing')->unsigned()->nullable();
            $table->tinyInteger('barista_knowledge')->unsigned()->nullable();
            $table->tinyInteger('equipment')->unsigned()->nullable();
            $table->tinyInteger('decaf_available')->unsigned()->nullable();
            $table->tinyInteger('value')->unsigned()->nullable();
            $table->float('overall')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_scores');
    }
};
