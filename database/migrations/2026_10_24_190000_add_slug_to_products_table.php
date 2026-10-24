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
        if (!Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name')->index();
            });
        }

        // Auto-generate slugs for existing products without a slug
        try {
            $products = \App\Models\Product::whereNull('slug')->orWhere('slug', '')->get();
            foreach ($products as $p) {
                $base = \Illuminate\Support\Str::slug($p->name) ?: ('product-' . $p->id);
                $slug = $base;
                $counter = 1;
                while (\App\Models\Product::where('slug', $slug)->where('id', '!=', $p->id)->exists()) {
                    $slug = "{$base}-{$counter}";
                    $counter++;
                }
                $p->slug = $slug;
                $p->save();
            }
        } catch (\Throwable $e) {
            // Silently continue if tables or models are not yet booted during fresh install
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
