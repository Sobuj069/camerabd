<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('business_settings')->insertOrIgnore(array(
            0 =>
            array(
                'id' => 1,
                'type' => 'system_icon',
                'value' => '20260416102540_454.png',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            1 =>
            array(
                'id' => 2,
                'type' => 'system_logo',
                'value' => '20260713003518_826.jpg',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            2 =>
            array(
                'id' => 3,
                'type' => 'pro_barcode',
                'value' => 'single',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            3 =>
            array(
                'id' => 4,
                'type' => 'inv_logo',
                'value' => '20260713003518_826.jpg',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            4 =>
            array(
                'id' => 5,
                'type' => 'inv_design',
                'value' => '',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            5 =>
            array(
                'id' => 6,
                'type' => 'com_name',
                'value' => 'Fast iT',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            6 =>
            array(
                'id' => 7,
                'type' => 'com_email',
                'value' => 'fastit.com.bd@gmail.com',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            7 =>
            array(
                'id' => 8,
                'type' => 'com_phone',
                'value' => '01901166580',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            8 =>
            array(
                'id' => 9,
                'type' => 'com_address',
                'value' => 'Suite-807, Floor-7th, Shah Ali Plaza, Mirpur-1216, Dhaka, Bangladesh',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
            9 =>
            array(
                'id' => 10,
                'type' => 'com_currency',
                'value' => 'BDT',
                'created_at' => '2023-12-19 18:37:48',
                'updated_at' => '2023-12-19 18:37:48',
            ),
        ));
    }
}
