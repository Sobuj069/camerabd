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
        if (!Schema::hasTable('homepage_sections')) {
            Schema::create('homepage_sections', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->string('badge')->nullable();
                $table->string('tag')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('icon')->nullable();
                $table->string('accent_color')->default('#b70011');
                $table->string('style_theme')->default('grid'); // grid, flash_deal, showcase
                $table->integer('order')->default(0);
                $table->boolean('status')->default(1);
                $table->boolean('is_system')->default(0);
                $table->text('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('homepage_section_products')) {
            Schema::create('homepage_section_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('homepage_section_id');
                $table->unsignedBigInteger('product_id');
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->unique(['homepage_section_id', 'product_id'], 'sec_prod_unique');
                $table->foreign('homepage_section_id')->references('id')->on('homepage_sections')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        // Seed default 4 sections if not exists
        $now = now();
        $defaults = [
            [
                'key' => 'flash_deal',
                'name' => 'Broadcast & Studio Flash',
                'badge' => 'Limited Studio Drops',
                'tag' => 'FLASH',
                'subtitle' => 'Special limited-time flash discount offers',
                'icon' => '⚡',
                'accent_color' => '#dc2626',
                'style_theme' => 'flash_deal',
                'order' => 1,
                'status' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'popular',
                'name' => 'Popular Cinema & Creator Gear',
                'badge' => 'Essentials',
                'tag' => 'TRENDING',
                'subtitle' => 'Most popular cinema bodies and creator accessories',
                'icon' => '🔥',
                'accent_color' => '#0d9488',
                'style_theme' => 'grid',
                'order' => 2,
                'status' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'hollyland',
                'name' => 'Hollyland Microphone',
                'badge' => 'FEATURED',
                'tag' => 'SHOWCASE',
                'subtitle' => 'Wireless audio systems and creator microphones',
                'icon' => '🎙️',
                'accent_color' => '#006a61',
                'style_theme' => 'showcase',
                'order' => 3,
                'status' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'essential',
                'name' => 'Studio Essentials & Power',
                'badge' => 'Top Rated Rigging',
                'tag' => 'ESSENTIAL',
                'subtitle' => 'Batteries, rigging, mounts and essential studio supplies',
                'icon' => '🔌',
                'accent_color' => '#0284c7',
                'style_theme' => 'grid',
                'order' => 4,
                'status' => 1,
                'is_system' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($defaults as $secData) {
            $existing = DB::table('homepage_sections')->where('key', $secData['key'])->first();
            if (!$existing) {
                $secId = DB::table('homepage_sections')->insertGetId($secData);
            } else {
                $secId = $existing->id;
            }

            // Sync existing products with boolean flags to pivot table
            $colName = match($secData['key']) {
                'flash_deal' => 'is_flash_deal',
                'popular' => 'is_popular',
                'hollyland' => 'is_hollyland',
                'essential' => 'is_essential',
                default => null
            };

            if ($colName && Schema::hasColumn('products', $colName)) {
                $productIds = DB::table('products')->where($colName, 1)->pluck('id');
                foreach ($productIds as $pId) {
                    $hasPivot = DB::table('homepage_section_products')
                        ->where('homepage_section_id', $secId)
                        ->where('product_id', $pId)
                        ->exists();
                    if (!$hasPivot) {
                        DB::table('homepage_section_products')->insert([
                            'homepage_section_id' => $secId,
                            'product_id' => $pId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_section_products');
        Schema::dropIfExists('homepage_sections');
    }
};
