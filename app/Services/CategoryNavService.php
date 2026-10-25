<?php

namespace App\Services;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Str;

class CategoryNavService
{
    /**
     * Get categories with their REAL subcategories strictly from Database.
     * Only categories and subcategories that exist in DB will appear.
     * If a category has no subcategories in DB, subcategories array will be empty.
     */
    public static function getMegaMenuCategories()
    {
        return \Illuminate\Support\Facades\Cache::remember('mega_menu_categories_v2', 86400, function () {
            // 1. Fetch all real categories with their subcategories from database
            try {
                $dbCategories = Category::with('subCategories')
                    ->orderBy('is_top', 'desc')
                    ->orderBy('top_order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            } catch (\Throwable $e) {
                $dbCategories = collect();
            }

            $categoriesList = [];

            foreach ($dbCategories as $cat) {
                $rawName = trim($cat->name);
                $normKey = strtolower($rawName);
                $slug = Str::slug($rawName);

                // Clean, professional title formatting for UI
                $displayName = ucwords(strtolower($rawName));
                if ($normKey === 'accessoris') $displayName = 'Accessories';
                elseif ($normKey === 'battry') $displayName = 'Battery';
                elseif ($normKey === 'pockt camera') $displayName = 'Pocket Camera';
                elseif ($normKey === 'trypod') $displayName = 'Tripod';
                elseif ($normKey === 'walike talike') $displayName = 'Walkie Talkie';
                elseif ($normKey === 'nickband') $displayName = 'Neckband';
                elseif ($normKey === 'sound box') $displayName = 'Sound Box';
                elseif ($normKey === 'powerbank') $displayName = 'Power Bank';

                $icon = self::resolveCategoryIcon($rawName);

                $subList = [];

                // ONLY populate subcategories if they actually exist in the database!
                if ($cat->subCategories && $cat->subCategories->isNotEmpty()) {
                    foreach ($cat->subCategories as $dbSub) {
                        $subSlug = Str::slug($dbSub->name);
                        $subList[] = [
                            'id' => $dbSub->id,
                            'name' => $dbSub->name,
                            'slug' => $subSlug,
                            'icon' => self::resolveCategoryIcon($dbSub->name),
                            'icon_color' => '#006241',
                            'image' => null,
                            'url' => route('frontend.subcategory', [$slug, $subSlug]),
                        ];
                    }
                }

                $imageUrl = $cat->image_url ?: null;

                $categoriesList[$slug] = [
                    'id' => $cat->id,
                    'name' => $displayName,
                    'raw_name' => $rawName,
                    'slug' => $slug,
                    'image' => $imageUrl,
                    'has_custom_image' => !empty($cat->image_url),
                    'icon' => $icon,
                    'icon_color' => '#006241',
                    'is_top' => (bool) $cat->is_top,
                    'top_order' => (int) $cat->top_order,
                    'view_all_url' => route('frontend.category', $slug),
                    'subcategories' => $subList,
                ];
            }

            return array_values($categoriesList);
        });
    }

    /**
     * Resolve fallback image for category based on its name.
     */
    public static function resolveFallbackImage($name)
    {
        return null;
    }

    /**
     * Intelligently resolve Material Symbol icon based on category/subcategory name.
     */
    public static function resolveCategoryIcon($name)
    {
        $n = strtolower($name);
        if (str_contains($n, 'digital camera') || str_contains($n, 'body camera') || str_contains($n, 'cinema') || str_contains($n, 'dslr') || str_contains($n, 'mirrorless') || str_contains($n, 'print camera')) {
            return 'photo_camera';
        }
        if (str_contains($n, 'action') || str_contains($n, 'pocket camera') || str_contains($n, 'pockt')) {
            return 'videocam';
        }
        if (str_contains($n, 'drone') || str_contains($n, 'aerial')) {
            return 'flight';
        }
        if (str_contains($n, 'mic') || str_contains($n, 'audio') || str_contains($n, 'sound') || str_contains($n, 'voice')) {
            return 'mic';
        }
        if (str_contains($n, 'gimbal') || str_contains($n, 'stabiliz')) {
            return 'screen_rotation';
        }
        if (str_contains($n, 'lens') || str_contains($n, 'optics')) {
            return 'camera';
        }
        if (str_contains($n, 'light') || str_contains($n, 'flash') || str_contains($n, 'softbox')) {
            return 'lightbulb';
        }
        if (str_contains($n, 'tripod') || str_contains($n, 'trypod') || str_contains($n, 'monopod') || str_contains($n, 'stand')) {
            return 'camera_indoor';
        }
        if (str_contains($n, 'bag') || str_contains($n, 'backpack') || str_contains($n, 'case') || str_contains($n, 'pouch')) {
            return 'shopping_bag';
        }
        if (str_contains($n, 'battery') || str_contains($n, 'battry') || str_contains($n, 'powerbank') || str_contains($n, 'power')) {
            return 'battery_charging_full';
        }
        if (str_contains($n, 'charger') || str_contains($n, 'adapter') || str_contains($n, 'cable')) {
            return 'power';
        }
        if (str_contains($n, 'memory') || str_contains($n, 'card') || str_contains($n, 'sd') || str_contains($n, 'ssd')) {
            return 'sd_card';
        }
        if (str_contains($n, 'airpod') || str_contains($n, 'earphone') || str_contains($n, 'headphone') || str_contains($n, 'nickband')) {
            return 'headphones';
        }
        if (str_contains($n, 'speaker') || str_contains($n, 'sound box') || str_contains($n, 'soundbox')) {
            return 'speaker';
        }
        if (str_contains($n, 'walkie') || str_contains($n, 'walike') || str_contains($n, 'talkie') || str_contains($n, 'intercom')) {
            return 'cell_tower';
        }
        if (str_contains($n, 'switcher') || str_contains($n, 'monitor') || str_contains($n, 'display')) {
            return 'live_tv';
        }
        if (str_contains($n, 'game') || str_contains($n, 'console') || str_contains($n, 'gaming')) {
            return 'sports_esports';
        }
        if (str_contains($n, 'fan')) {
            return 'mode_fan';
        }
        if (str_contains($n, 'accessor') || str_contains($n, 'accessoris')) {
            return 'extension';
        }
        return 'category';
    }
}
