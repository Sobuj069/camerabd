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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'is_top')) {
                $table->tinyInteger('is_top')->default(0)->after('image');
            }
            if (!Schema::hasColumn('categories', 'top_order')) {
                $table->integer('top_order')->default(0)->after('is_top');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'is_top')) {
                $table->dropColumn('is_top');
            }
            if (Schema::hasColumn('categories', 'top_order')) {
                $table->dropColumn('top_order');
            }
        });
    }
};
