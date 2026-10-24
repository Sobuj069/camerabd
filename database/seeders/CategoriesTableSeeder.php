<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Seed only the essential default category.
     * Demo categories from JSON have been removed.
     *
     * @return void
     */
    public function run()
    {
        // Insert only a default "General" category
        DB::table('categories')->insertOrIgnore([
            [
                'name'       => 'General',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
