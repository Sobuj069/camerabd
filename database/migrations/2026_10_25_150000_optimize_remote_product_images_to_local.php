<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mapping = [
            1 => ['images' => 'p1_img0_IMG_0580_JPG.webp'],
            2 => ['images' => 'p2_img0_IMG_1362.webp'],
            3 => ['images' => 'p3_img0_EDF302AC-C748-4E05-875A-699DB326AA9D.webp'],
            4 => ['images' => 'p4_img0_at-m86tr-01-500x500-1.webp'],
            5 => ['images' => 'p5_img0_at-m82tr-02-500x500-1.webp'],
            6 => ['images' => 'p6_img0_at-m82tr-03-500x500-1.webp'],
            7 => ['images' => 'p7_img0_IMG_7756.webp'],
            8 => ['images' => 'p8_img0_at-s81tr-3-500x500-1.webp'],
            9 => ['images' => 'p9_img0_at-m44r-3-500x500-1.webp'],
            10 => ['images' => 'p10_img0_IMG_7736.webp'],
            11 => ['images' => 'p11_img0_IMG_7734.webp'],
            12 => ['images' => 'p12_img0_IMG_7727.avif'],
            13 => ['images' => 'p13_img0_IMG_7723.avif'],
            14 => ['images' => 'p14_img0_digital-camera-M26-Digital-Camera-3.webp'],
            15 => ['thumbnail' => 'p15_thumb_Mobile-Phone-Telescope-Lens-10.webp', 'images' => 'p15_img0_Mobile-Phone-Telescope-Lens-10.webp'],
            16 => ['images' => 'p16_img0_03c8fda2e8ab68e71f66e9166b0036fc.webp'],
            17 => ['images' => 'p17_img0_Mobile-Phone-Telescope-Lens-6.webp'],
            18 => ['images' => 'p18_img0_IMG_7674-1.webp'],
            19 => ['images' => 'p19_img0_Mobile-Phone-Telescope-Lens-9.webp'],
            189 => ['images' => 'p189_img0_WhatsApp-Image-2026-08-16-at-00_41_07.webp'],
            190 => ['images' => 'p190_img0_WhatsApp-Image-2026-08-15-at-14_40_41-2.webp'],
        ];

        foreach ($mapping as $id => $data) {
            DB::table('products')->where('id', $id)->update($data);
        }

        \Illuminate\Support\Facades\Cache::forget('frontend_homepage_sections_v2');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible not needed
    }
};
