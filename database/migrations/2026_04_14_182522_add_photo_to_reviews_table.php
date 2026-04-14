<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('body');
            $table->string('photo_alt')->nullable()->after('photo');
            $table->boolean('photo_analysed')->default(false)->after('photo_alt');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['photo', 'photo_alt', 'photo_analysed']);
        });
    }
};
