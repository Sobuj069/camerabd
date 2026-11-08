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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'is_free_delivery')) {
            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('is_free_delivery')->default(0)->after('status')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'is_free_delivery')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('is_free_delivery');
            });
        }
    }
};
