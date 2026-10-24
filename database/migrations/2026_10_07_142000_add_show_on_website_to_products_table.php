<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'show_on_website')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('show_on_website')->default(1)->index()->after('is_essential');
            });

            DB::table('products')->whereNull('show_on_website')->orWhere('show_on_website', 0)->update(['show_on_website' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'show_on_website')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('show_on_website');
            });
        }
    }
};
