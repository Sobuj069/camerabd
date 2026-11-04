<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use XMLWriter;

class FacebookFeedController extends Controller
{
    /**
     * Cache key for the default XML feed.
     */
    const CACHE_KEY_XML = 'facebook_product_feed_xml';
    const CACHE_KEY_CSV = 'facebook_product_feed_csv';
    const CACHE_TTL_SECONDS = 86400; // 24 hours (auto-invalidated on Product save/delete)

    /**
     * Map store category names to standard Google / Meta Product Taxonomy.
     */
    public static function mapGoogleCategory($categoryName)
    {
        $cat = mb_strtolower(trim($categoryName ?? ''), 'UTF-8');

        if (str_contains($cat, 'drone')) {
            return 'Electronics > Remote Control Toys > Remote Control Drones';
        }
        if (str_contains($cat, 'mic') || str_contains($cat, 'audio') || str_contains($cat, 'sound') || str_contains($cat, 'wireless') || str_contains($cat, 'headphone')) {
            return 'Electronics > Audio > Audio Equipment > Microphones';
        }
        if (str_contains($cat, 'gimbal') || str_contains($cat, 'tripod') || str_contains($cat, 'stabilizer') || str_contains($cat, 'monopod') || str_contains($cat, 'stand')) {
            return 'Cameras & Optics > Camera & Optic Accessories > Tripods & Monopods';
        }
        if (str_contains($cat, 'lens') || str_contains($cat, 'filter') || str_contains($cat, 'optics')) {
            return 'Cameras & Optics > Camera & Optic Accessories > Camera & Video Camera Lenses';
        }
        if (str_contains($cat, 'light') || str_contains($cat, 'studio') || str_contains($cat, 'flash') || str_contains($cat, 'softbox') || str_contains($cat, 'led')) {
            return 'Cameras & Optics > Photography > Studio & Lighting Equipment';
        }
        if (str_contains($cat, 'action camera') || str_contains($cat, 'action')) {
            return 'Cameras & Optics > Cameras > Camcorders';
        }
        if (str_contains($cat, 'pocket camera') || str_contains($cat, 'vlog')) {
            return 'Cameras & Optics > Cameras > Digital Cameras';
        }
        if (str_contains($cat, 'bag') || str_contains($cat, 'case')) {
            return 'Cameras & Optics > Camera & Optic Accessories > Camera Bags & Cases';
        }
        if (str_contains($cat, 'battery') || str_contains($cat, 'charger') || str_contains($cat, 'power')) {
            return 'Electronics > Electronics Accessories > Power > Batteries';
        }
        if (str_contains($cat, 'card') || str_contains($cat, 'memory') || str_contains($cat, 'storage') || str_contains($cat, 'ssd')) {
            return 'Electronics > Computers > Computer Storage Devices > Flash Memory Cards';
        }

        return 'Cameras & Optics > Cameras';
    }

    /**
     * Normalize URL to be fully qualified with current root scheme & host.
     */
    public static function ensureAbsoluteUrl($url)
    {
        if (empty($url)) {
            return asset('backend/images/no_images.png');
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            // If generated locally or behind proxy, replace http://localhost with actual request root if available
            if (request()->hasHeader('host')) {
                $root = request()->schemeAndHttpHost();
                if (str_starts_with($url, 'http://localhost') && !str_starts_with($root, 'http://localhost')) {
                    return str_replace('http://localhost', $root, $url);
                }
            }
            return $url;
        }

        return url($url);
    }

    /**
     * Build base Eloquent query for catalog products.
     */
    protected function getProductQuery(Request $request)
    {
        $query = Product::with(['category', 'subCategory', 'brand', 'warranty'])
            ->where('is_service', 0)
            ->where('show_on_website', 1)
            ->where('status', 1);

        if ($request->filled('category')) {
            $cat = $request->input('category');
            $query->whereHas('category', function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('id', $cat);
                } else {
                    $q->where('slug', $cat)->orWhere('name', 'like', "%{$cat}%");
                }
            });
        }

        if ($request->filled('brand')) {
            $brand = $request->input('brand');
            $query->whereHas('brand', function ($q) use ($brand) {
                if (is_numeric($brand)) {
                    $q->where('id', $brand);
                } else {
                    $q->where('slug', $brand)->orWhere('name', 'like', "%{$brand}%");
                }
            });
        }

        if ($request->filled('stock')) {
            $stock = $request->input('stock');
            if ($stock === 'in_stock') {
                $query->where(function ($q) {
                    $q->whereNull('main_qty')->orWhere('main_qty', '>', 0);
                });
            } elseif ($stock === 'out_of_stock') {
                $query->whereNotNull('main_qty')->where('main_qty', '<=', 0);
            }
        }

        if ($request->filled('limit') && is_numeric($request->input('limit'))) {
            $query->limit((int)$request->input('limit'));
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Public Meta / Facebook Product Catalog XML Feed.
     * Accessible at: /facebook-feed.xml or /feed/facebook-catalog.xml
     */
    public function xmlFeed(Request $request)
    {
        $bypassCache = $request->has('refresh') || $request->has('nocache') || $request->filled('category') || $request->filled('brand') || $request->filled('stock') || $request->filled('limit');

        if (!$bypassCache && Cache::has(self::CACHE_KEY_XML)) {
            $xmlContent = Cache::get(self::CACHE_KEY_XML);
            return response($xmlContent, 200, [
                'Content-Type' => 'application/xml; charset=utf-8',
                'Cache-Control' => 'public, max-age=3600',
                'X-Feed-Source' => 'cache'
            ]);
        }

        $siteName = function_exists('get_setting') && get_setting('system_name') ? get_setting('system_name') : 'CAMERA BD';
        $siteUrl = url('/');

        $products = $this->getProductQuery($request)->get();

        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');
        $xml->startDocument('1.0', 'UTF-8');

        // RSS 2.0 with Google Base namespace
        $xml->startElement('rss');
        $xml->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $xml->writeAttribute('version', '2.0');

        $xml->startElement('channel');
        $xml->writeElement('title', $siteName . ' - Pro Cine & Audio Product Catalog');
        $xml->writeElement('link', $siteUrl);
        $xml->writeElement('description', 'Official Dynamic Facebook & Instagram Product Feed for ' . $siteName);

        foreach ($products as $p) {
            $sellingPrice = (float)($p->selling_price ?? 0);
            $disPrice = (float)($p->dis_selling_price ?? 0);

            if ($disPrice > 0 && $disPrice > $sellingPrice) {
                $regularPrice = $disPrice;
                $salePrice = $sellingPrice;
            } else {
                $regularPrice = $sellingPrice;
                $salePrice = null;
            }

            $brand = !empty($p->brand?->name) ? $p->brand->name : $siteName;
            $catName = !empty($p->category?->name) ? $p->category->name : 'General Gear';
            $subCatName = !empty($p->subCategory?->name) ? $p->subCategory->name : '';
            $productType = $catName . ($subCatName ? ' > ' . $subCatName : '');

            // Description sanitization
            $rawDesc = trim(strip_tags($p->description ?? ''));
            if (empty($rawDesc) || mb_strlen($rawDesc, 'UTF-8') < 15) {
                $desc = "Buy " . $p->name . " at best price in Bangladesh from " . $siteName . ". 100% genuine with official warranty.";
            } else {
                $desc = mb_substr($rawDesc, 0, 4900, 'UTF-8');
            }

            // Availability
            $availability = ($p->main_qty !== null && (float)$p->main_qty <= 0) ? 'out of stock' : 'in stock';

            // Canonical Link
            $productUrl = self::ensureAbsoluteUrl(url('/product-details/' . ($p->slug ?: $p->id)));

            // Primary Image
            $mainImage = self::ensureAbsoluteUrl($p->thumbnail_url ?: asset('backend/images/no_images.png'));

            // Item Start
            $xml->startElement('item');

            // g:id (Matches Meta Pixel & CAPI content_ids for 100% Dynamic Ads match)
            $xml->writeElement('g:id', (string)$p->id);

            // g:title
            $xml->startElement('g:title');
            $xml->writeCdata(mb_substr($p->name, 0, 150, 'UTF-8'));
            $xml->endElement();

            // g:description
            $xml->startElement('g:description');
            $xml->writeCdata($desc);
            $xml->endElement();

            // g:link
            $xml->writeElement('g:link', $productUrl);

            // g:image_link
            $xml->writeElement('g:image_link', $mainImage);

            // Additional Gallery Images (up to 10 images)
            $gallery = $p->gallery_images;
            if (is_array($gallery)) {
                $imgCount = 0;
                foreach ($gallery as $galImg) {
                    $galUrl = self::ensureAbsoluteUrl($galImg);
                    if ($galUrl !== $mainImage && !empty($galUrl) && $imgCount < 10) {
                        $xml->writeElement('g:additional_image_link', $galUrl);
                        $imgCount++;
                    }
                }
            }

            // g:brand
            $xml->startElement('g:brand');
            $xml->writeCdata($brand);
            $xml->endElement();

            // g:condition
            $xml->writeElement('g:condition', 'new');

            // g:availability
            $xml->writeElement('g:availability', $availability);

            // g:price
            $xml->writeElement('g:price', number_format($regularPrice, 2, '.', '') . ' BDT');

            // g:sale_price (only when product has a genuine discount)
            if ($salePrice !== null && $salePrice > 0 && $regularPrice > $salePrice) {
                $xml->writeElement('g:sale_price', number_format($salePrice, 2, '.', '') . ' BDT');
            }

            // g:google_product_category
            $xml->startElement('g:google_product_category');
            $xml->writeCdata(self::mapGoogleCategory($catName));
            $xml->endElement();

            // g:product_type
            $xml->startElement('g:product_type');
            $xml->writeCdata($productType);
            $xml->endElement();

            // MPN / SKU Barcode
            if (!empty($p->barcode)) {
                $xml->writeElement('g:mpn', (string)$p->barcode);
            }

            // Custom Labels for Facebook Ad Targeting & Product Sets
            // Custom Label 0: Stock Status
            $xml->startElement('g:custom_label_0');
            $xml->writeCdata($availability === 'in stock' ? 'In Stock' : 'Out of Stock');
            $xml->endElement();

            // Custom Label 1: Category Name
            $xml->startElement('g:custom_label_1');
            $xml->writeCdata($catName);
            $xml->endElement();

            // Custom Label 2: Brand Name
            $xml->startElement('g:custom_label_2');
            $xml->writeCdata($brand);
            $xml->endElement();

            // Custom Label 3: Promotion Segment
            $dealType = 'Regular';
            if ($p->is_flash_deal) {
                $dealType = 'Flash Deal';
            } elseif ($p->is_popular) {
                $dealType = 'Popular Gear';
            } elseif ($p->is_hollyland) {
                $dealType = 'Hollyland Gear';
            } elseif ($p->is_essential) {
                $dealType = 'Studio Essential';
            }
            $xml->startElement('g:custom_label_3');
            $xml->writeCdata($dealType);
            $xml->endElement();

            // Custom Label 4: Discount Percentage
            $discountLabel = 'No Discount';
            if ($salePrice !== null && $regularPrice > $salePrice) {
                $pct = round((($regularPrice - $salePrice) / $regularPrice) * 100);
                $discountLabel = $pct . '% Off';
            }
            $xml->startElement('g:custom_label_4');
            $xml->writeCdata($discountLabel);
            $xml->endElement();

            // Optional Facebook Product Category
            $xml->startElement('g:fb_product_category');
            $xml->writeCdata(self::mapGoogleCategory($catName));
            $xml->endElement();

            $xml->endElement(); // item
        }

        $xml->endElement(); // channel
        $xml->endElement(); // rss
        $xml->endDocument();

        $output = $xml->outputMemory();

        if (!$bypassCache) {
            Cache::put(self::CACHE_KEY_XML, $output, self::CACHE_TTL_SECONDS);
        }

        return response($output, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
            'X-Feed-Source' => $bypassCache ? 'live' : 'generated'
        ]);
    }

    /**
     * CSV Feed format for manual export / spreadsheet review.
     */
    public function csvFeed(Request $request)
    {
        $products = $this->getProductQuery($request)->get();
        $siteName = function_exists('get_setting') && get_setting('system_name') ? get_setting('system_name') : 'CAMERA BD';

        $handle = fopen('php://memory', 'r+');

        // CSV Header row per Meta Catalog Specification
        fputcsv($handle, [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'sale_price',
            'link',
            'image_link',
            'brand',
            'google_product_category',
            'product_type',
            'mpn',
            'custom_label_0',
            'custom_label_1',
            'custom_label_2',
            'custom_label_3',
            'custom_label_4'
        ]);

        foreach ($products as $p) {
            $sellingPrice = (float)($p->selling_price ?? 0);
            $disPrice = (float)($p->dis_selling_price ?? 0);

            if ($disPrice > 0 && $disPrice > $sellingPrice) {
                $regularPrice = $disPrice;
                $salePrice = $sellingPrice;
            } else {
                $regularPrice = $sellingPrice;
                $salePrice = null;
            }

            $brand = !empty($p->brand?->name) ? $p->brand->name : $siteName;
            $catName = !empty($p->category?->name) ? $p->category->name : 'General Gear';
            $subCatName = !empty($p->subCategory?->name) ? $p->subCategory->name : '';
            $productType = $catName . ($subCatName ? ' > ' . $subCatName : '');

            $rawDesc = trim(strip_tags($p->description ?? ''));
            $desc = empty($rawDesc) ? "Buy {$p->name} at best price in Bangladesh from {$siteName}." : mb_substr($rawDesc, 0, 4900, 'UTF-8');

            $availability = ($p->main_qty !== null && (float)$p->main_qty <= 0) ? 'out of stock' : 'in stock';
            $productUrl = self::ensureAbsoluteUrl(url('/product-details/' . ($p->slug ?: $p->id)));
            $mainImage = self::ensureAbsoluteUrl($p->thumbnail_url ?: asset('backend/images/no_images.png'));

            $dealType = 'Regular';
            if ($p->is_flash_deal) $dealType = 'Flash Deal';
            elseif ($p->is_popular) $dealType = 'Popular Gear';
            elseif ($p->is_hollyland) $dealType = 'Hollyland Gear';
            elseif ($p->is_essential) $dealType = 'Studio Essential';

            $discountLabel = 'No Discount';
            if ($salePrice !== null && $regularPrice > $salePrice) {
                $pct = round((($regularPrice - $salePrice) / $regularPrice) * 100);
                $discountLabel = $pct . '% Off';
            }

            fputcsv($handle, [
                $p->id,
                mb_substr($p->name, 0, 150, 'UTF-8'),
                $desc,
                $availability,
                'new',
                number_format($regularPrice, 2, '.', '') . ' BDT',
                $salePrice ? number_format($salePrice, 2, '.', '') . ' BDT' : '',
                $productUrl,
                $mainImage,
                $brand,
                self::mapGoogleCategory($catName),
                $productType,
                $p->barcode ?? '',
                $availability === 'in stock' ? 'In Stock' : 'Out of Stock',
                $catName,
                $brand,
                $dealType,
                $discountLabel
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'inline; filename="facebook-product-feed.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }

    /**
     * Admin Dashboard: Facebook Product Feed Control Panel.
     * Route: /marketing/facebook-feed (admin access)
     */
    public function adminIndex(Request $request)
    {
        $totalActive = Product::where('is_service', 0)->where('show_on_website', 1)->where('status', 1)->count();
        $inStock = Product::where('is_service', 0)->where('show_on_website', 1)->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('main_qty')->orWhere('main_qty', '>', 0);
            })->count();
        $outOfStock = Product::where('is_service', 0)->where('show_on_website', 1)->where('status', 1)
            ->whereNotNull('main_qty')->where('main_qty', '<=', 0)->count();

        $discounted = Product::where('is_service', 0)->where('show_on_website', 1)->where('status', 1)
            ->where('dis_selling_price', '>', 0)
            ->whereRaw('dis_selling_price > selling_price')
            ->count();

        $categoriesCount = Category::count();
        $brandsCount = Brand::count();

        $previewProducts = Product::with(['category', 'brand'])
            ->where('is_service', 0)
            ->where('show_on_website', 1)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->take(12)
            ->get();

        $feedUrl = url('/facebook-feed.xml');
        $csvUrl = url('/facebook-feed.csv');
        $isCached = Cache::has(self::CACHE_KEY_XML);

        return view('backend.pages.marketing.facebook-feed', compact(
            'totalActive',
            'inStock',
            'outOfStock',
            'discounted',
            'categoriesCount',
            'brandsCount',
            'previewProducts',
            'feedUrl',
            'csvUrl',
            'isCached'
        ));
    }

    /**
     * Clear Feed Cache and force regenerate on next crawl.
     */
    public function clearCache(Request $request)
    {
        Cache::forget(self::CACHE_KEY_XML);
        Cache::forget(self::CACHE_KEY_CSV);

        session()->flash('success', 'Facebook Product Feed cache cleared successfully! Fresh data will be served on next request.');
        return redirect()->back();
    }
}
