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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('schedule')->nullable()->after('address');
            $table->string('badge')->nullable()->after('schedule');
            $table->string('highlights')->nullable()->after('badge');
            $table->string('map_link')->nullable()->after('highlights');
            $table->string('icon')->nullable()->default('storefront')->after('map_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['schedule', 'badge', 'highlights', 'map_link', 'icon']);
        });
    }
};
