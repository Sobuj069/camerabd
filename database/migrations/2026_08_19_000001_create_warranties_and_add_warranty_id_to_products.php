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
        if (!Schema::hasTable('warranties')) {
            Schema::create('warranties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('duration');
                $table->string('period')->default('Month'); // Day, Month, Year
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'warranty_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('warranty_id')->nullable()->after('has_warranty');
                $table->foreign('warranty_id')->references('id')->on('warranties')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'warranty_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['warranty_id']);
                $table->dropColumn('warranty_id');
            });
        }

        Schema::dropIfExists('warranties');
    }
};
