<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $banners = [
            1 => 'banner_pocket4p.webp',
            2 => 'banner_fx30.webp',
            3 => 'banner_neo2.webp',
            4 => 'banner_z30.webp',
        ];

        foreach ($banners as $id => $img) {
            DB::table('banners')->where('id', $id)->update([
                'image' => $img,
                'updated_at' => now(),
            ]);
        }

        // Also update any banners whose image still starts with lh3.googleusercontent
        DB::table('banners')
            ->where('image', 'LIKE', '%googleusercontent%')
            ->where('position', 'main_hero')
            ->update(['image' => 'banner_pocket4p.webp']);

        DB::table('banners')
            ->where('image', 'LIKE', '%googleusercontent%')
            ->where('position', 'side_top')
            ->update(['image' => 'banner_neo2.webp']);

        DB::table('banners')
            ->where('image', 'LIKE', '%googleusercontent%')
            ->where('position', 'side_bottom')
            ->update(['image' => 'banner_z30.webp']);

        \Illuminate\Support\Facades\Cache::forget('frontend_homepage_sections_v2');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
