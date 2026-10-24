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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_flash_deal')->default(0)->index()->after('status');
            $table->boolean('is_popular')->default(0)->index()->after('is_flash_deal');
            $table->boolean('is_hollyland')->default(0)->index()->after('is_popular');
            $table->boolean('is_essential')->default(0)->index()->after('is_hollyland');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_flash_deal', 'is_popular', 'is_hollyland', 'is_essential']);
        });
    }
};
