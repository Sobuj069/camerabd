<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\WebsiteOrder;
use App\Models\WebsiteOrderItem;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Branch;
use App\Services\CategoryNavService;
use App\Services\MetaCapiService;

class FrontendController extends Controller
{
    /**
     * Get mock catalog of products for rich demo & fallback
     */
    private function getCatalog()
    {
        return [
            'sony-fx30' => [
                'id' => 1,
                'slug' => 'sony-fx30',
                'name' => 'Sony FX30 Cinema Line Super 35 Camera',
                'category' => 'Cinema Cameras',
                'brand' => 'Sony',
                'sku' => 'SONY-FX30-ILME',
                'price' => 194999,
                'old_price' => 210000,
                'discount' => 7,
                'rating' => 4.9,
                'reviews_count' => 38,
                'in_stock' => true,
                'stock_branches' => ['Baitul Mukarram Flagship', 'Bashundhara City Level 6'],
                'warranty' => '3 Years Official Service Warranty + 1 Year International',
                'images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1VlkIaGWo4pZtCfRCtGHkIOYCB_lwbnbvypPczrc0Zep_5j_I3I0x0272WYzgzQnR6mFOG1JpYO3uIg0xlKUhkOIXtcA6Qp2Zme48SvdFxUfa3pDyf7UYGMTt_0crdEY4IWr-fMPJE4HKv1wR8h0D3M29za-nyxKsI1baXPZpnRPPdiRDAor_1aq5t6z3orCGyAOsGo5fPLDHnPZOYuMkEEseZJU08eG_N6Eob7mwbRTy3H7IVw2Qc-BwY',
                    'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=800&auto=format&fit=crop&q=80'
                ],
                'overview' => 'The Sony FX30 captures stunning visuals with 6K oversampling for sharp 4K Super 35 recording. Designed specifically for independent filmmakers and broadcast content creators, it features dual native ISO (800 / 2500), S-Cinetone, 10-bit 4:2:2 internal recording, and 5-axis in-body image stabilization.',
                'features' => [
                    '26.1MP APS-C / Super 35 Exmor R BSI CMOS Sensor',
                    'UHD 4K up to 120p | 10-Bit 4:2:2 XAVC S-I / XAVC HS',
                    'Dual Base ISO (800 / 2500) for clean low-light capture',
                    'S-Cinetone, S-Log3, HLG Film Look Profiles',
                    'Active Cooling Fan for unlimited 4K 60p recording without overheating',
                    'Real-time Eye AF & Fast Hybrid Autofocus (495 phase-detect points)',
                    'Dual CFexpress Type A / SD UHS-II Card Slots'
                ],
                'specs' => [
                    'Sensor Type' => 'APS-C (23.3 x 15.5 mm) Super 35 CMOS',
                    'Lens Mount' => 'Sony E-Mount',
                    'Video Formats' => 'XAVC HS / XAVC S / XAVC S-I 4:2:2 10-Bit',
                    'Max Resolution' => '4K (3840 x 2160) up to 120 fps',
                    'Audio Inputs' => '3.5mm Stereo Mic In, Optional XLR Top Handle',
                    'Video Output' => 'Full-Size HDMI Type-A',
                    'Battery Life' => 'NP-FZ100 (Approx 115 min recording)',
                    'Weight' => '562 g (Body Only with Battery)'
                ],
                'box_contents' => [
                    'Sony FX30 Cinema Camera Body',
                    'Sony NP-FZ100 Rechargeable Lithium-Ion Battery',
                    'Sony AC Adaptor & Power Cord',
                    'Sony ALC-B1EM Body Cap',
                    'Multi Interface Shoe Cap',
                    'USB Type-A to Type-C Cable (USB 3.2)',
                    'Official Warranty Card & User Manual'
                ]
            ],
            'dji-osmo-pocket-4p' => [
                'id' => 2,
                'slug' => 'dji-osmo-pocket-4p',
                'name' => 'DJI Osmo Pocket 4P Creator Combo',
                'category' => 'Gimbals & Action',
                'brand' => 'DJI',
                'sku' => 'DJI-POCKET4P-COMBO',
                'price' => 74990,
                'old_price' => 79990,
                'discount' => 6,
                'rating' => 4.95,
                'reviews_count' => 62,
                'in_stock' => true,
                'stock_branches' => ['Baitul Mukarram Flagship', 'Bashundhara City Level 6'],
                'warranty' => '1 Year Official DJI Replacement Warranty',
                'images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1Vqtovg_xZlDnZaVLXZXohSSKxOzwNsqMMehT2gASqjyOfoGneJMZFqoH9IPecShYtpwgk9u7piXxWXoxpDO3um1RY2TiwiV07r6eOYuayiVh2wtWy2mFCC7cRlyTcyGO5Oow-9tFRAkoIMevpq-fd1cgrFpY2jHKfVeiNgh_iTom9zUqW3Ma_1mf2SEbP2PjUTxpPZkHYWb_ZrFZhOob0QRha2GFauumTzaWXmI3sUn8DY_-RRBUeP5Kg',
                    'https://images.unsplash.com/photo-1508614589041-895b88991e3e?w=800&auto=format&fit=crop&q=80'
                ],
                'overview' => 'DJI Osmo Pocket 4P brings revolutionary 1-inch Dual-Lens stabilization with 4K/120fps recording, 2-inch rotatable OLED touchscreen, and 3-axis mechanical gimbal stabilization. Perfect for vloggers, travel filmmakers, and live streaming.',
                'features' => [
                    '1-inch CMOS Sensor with 4K/120fps capability',
                    '2-inch Rotatable OLED Touchscreen with fast horizontal/vertical orientation',
                    '3-Axis Mechanical Gimbal Stabilization with ActiveTrack 6.0',
                    'Full-Pixel Fast Focusing with Product Showcase Mode',
                    'D-Log M 10-bit color profile for 1 billion colors',
                    'DJI Mic 2 Wireless Transmitter included in Creator Combo'
                ],
                'specs' => [
                    'Sensor' => '1-inch CMOS Sensor',
                    'Max Video' => '4K (16:9): 3840x2160 @ 120fps',
                    'Lens' => '20mm equivalent, f/2.0 aperture',
                    'Stabilization' => '3-Axis Mechanical Gimbal',
                    'Battery' => '1300 mAh (166 mins runtime, 80% charge in 16 min)',
                    'Weight' => '179 g'
                ],
                'box_contents' => [
                    'DJI Osmo Pocket 4P',
                    'DJI Mic 2 Transmitter with Windscreen & Magnet',
                    'Battery Handle Extension',
                    'Mini Tripod with 1/4" Thread',
                    'Wide-Angle Lens Converter',
                    'Protective Storage Case & Wrist Strap'
                ]
            ],
            'hollyland-lark-m2' => [
                'id' => 3,
                'slug' => 'hollyland-lark-m2',
                'name' => 'Hollyland LARK M2 DUO 2-Person Wireless Microphone',
                'category' => 'Wireless Audio',
                'brand' => 'Hollyland',
                'sku' => 'HL-LARKM2-DUO',
                'price' => 10499,
                'old_price' => 11000,
                'discount' => 5,
                'rating' => 5.0,
                'reviews_count' => 45,
                'in_stock' => true,
                'stock_branches' => ['Baitul Mukarram Flagship', 'Bashundhara City Level 6'],
                'warranty' => '2 Years Official Hollyland Replacement Warranty',
                'images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1X-nc7VZD9tYHp6qutS95cjA-Xk5pOOzJwdae3Vi5yg75ZaanA4-bPFLwz-qoo-JoQi5TkDwO71GgUsiHVOVo5x8y535Jg4E3WEA_VzQl9ZAdT5TVIR5gEoikhv-Z2gFvZvSkGbsxQIifYqIgqsF8YhYzvl6tPc6smePHKqrg_ApXKYK0N-E5a5oijUQjPNmBCEz6Eh9i35JKinBfJI9ZCuzzIm1KilnSKr1gZmzV4bCV9tLBN_Y3w1T5o',
                    'https://lh3.googleusercontent.com/aida/AEtjO1VgxG8LbldPbmAC2p7GjkhsXZezPqdGbUe4_Qljpk1PmGB53zj3k9Tqucd0LkXB8XsWiVwCy-ANr4g6SzMy-JILbubzySN0uGtFrktwdHWSyxJKHkUAxgn34B4RB0lVT21D7sM7Img80dDn3bjeqPZykIb-R9rcpvP9RpZVl4htyNv568a1ADFg4FT5xj0CPlHDBTn5Ya0r2uvBYV2aQPtderGP88qABO3JU_aXu8YhYzgk7gsepJmk6mQ'
                ],
                'overview' => 'Hollyland LARK M2 is an ultra-lightweight, coin-sized 9g button wireless microphone system engineered for pristine 48kHz/24-bit Hi-Fi sound, Environmental Noise Cancellation (ENC), and up to 40 hours of battery life with charging case.',
                'features' => [
                    'Only 9g Button Size Transmitter – Lightest in its class',
                    '48kHz/24-bit Studio Grade Hi-Fi Audio Capture',
                    'One-Click Yellow ENC (Environmental Noise Cancellation)',
                    'Up to 300m (1000ft) Ultra-Long Range Wireless Transmission',
                    'Universal Compatibility (Camera, iPhone/Lightning, USB-C, PC)',
                    '40-Hour Total Battery Life with Compact Charging Case'
                ],
                'specs' => [
                    'Audio Quality' => '48kHz / 24-bit Hi-Fi',
                    'Signal-to-Noise Ratio' => '> 70 dB',
                    'Max SPL' => '115 dB SPL',
                    'Wireless Range' => 'Up to 300m Line of Sight',
                    'Battery Life' => '10 Hours per TX, 40 Hours total with Case',
                    'Weight' => '9g (TX), 14.8g (Camera RX)'
                ],
                'box_contents' => [
                    '2x Hollyland LARK M2 Button Transmitters',
                    '1x Camera Receiver (3.5mm TRS)',
                    '1x USB-C Mobile Receiver',
                    '1x Lightning iOS Receiver',
                    '1x Wireless Charging Case',
                    '2x Furry Windscreens, 2x Magnetic Clips, 2x Silicone Necklaces',
                    '3.5mm TRS to TRS & TRS to TRRS Cables'
                ]
            ],
            'hollyland-lark-a1-duo' => [
                'id' => 4,
                'slug' => 'hollyland-lark-a1-duo',
                'name' => 'Hollyland LARK A1 Duo 2-Person Wireless Microphone System',
                'category' => 'Wireless Audio',
                'brand' => 'Hollyland',
                'sku' => 'HL-LARKA1-DUO',
                'price' => 5199,
                'old_price' => 6000,
                'discount' => 13,
                'rating' => 5.0,
                'reviews_count' => 24,
                'in_stock' => true,
                'stock_branches' => ['Baitul Mukarram Flagship', 'Bashundhara City Level 6'],
                'warranty' => '2 Years Official Warranty',
                'images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1XfQAISMRxlwh3cRyS9KXfszcuI4qtAFy_2nh2DqmJbf1h9fIqhykPXfmvCQPC7aAs7sD-uZZLkJlCoUht1hYo0qIbn69xOpn2u7GyeKxYEclHmMzw_0XIcGZWyRMQ9fIi06tPoJvPMfMAX1AkCrYahcdfYa0CwM0hrSgbhHX7i8N4NNpyTatjOsoC6vj6ETcEjtqGdgXkhfW4kMX-Qs5oVf0HgZib8aeU_OxB6aZMTaG3RO-u4OKD6i3U'
                ],
                'overview' => 'Compact and affordable 2-person wireless microphone setup with digital noise reduction, plug & play simplicity, and clear audio transmission.',
                'features' => [
                    '2-Person Dual Channel Recording',
                    'Active AI Noise Cancellation',
                    'Plug and Play with Type-C / Lightning Smartphones & Cameras',
                    '18 Hours Battery Life with Case'
                ],
                'specs' => [
                    'Range' => '100m Line of Sight',
                    'Sampling' => '48kHz / 16-bit',
                    'Battery' => 'Up to 6 hours continuous on single charge'
                ],
                'box_contents' => [
                    '2x Transmitters',
                    '1x Receiver',
                    '1x Charging Case',
                    'Cables & Wind Muff'
                ]
            ],
            'dji-rs-5' => [
                'id' => 5,
                'slug' => 'dji-rs-5',
                'name' => 'DJI RS 5 3-Axis Gimbal Combo Kit',
                'category' => 'Gimbals & Stabilizers',
                'brand' => 'DJI',
                'sku' => 'DJI-RS5-COMBO',
                'price' => 54990,
                'old_price' => 58000,
                'discount' => 5,
                'rating' => 4.9,
                'reviews_count' => 19,
                'in_stock' => true,
                'stock_branches' => ['Baitul Mukarram Flagship'],
                'warranty' => '1 Year Official Warranty',
                'images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1UtwKYg6iZpMl5Fh535es5hvtVBj6b1J_gMRxIUJq70aHGREGvG8S8hHmMvJo3K6dh6OkcyvqZf9ZdrroGSyDV0UoPKAsNQNv1vXa_AIHpSzNA6UFVFi7BdXB0hXxLVKDfYOJ14c9GH9ehBwDeY0ZWokAVvKOoqR4NOj-c5cRHNmIY4O5KEKxj_ECnSFkJblKF5p9ROOClIvXKIS_yct4Dm4BgbKp4o50pNnPe30Z2sys-iwVTrbqw0_AI'
                ],
                'overview' => 'Engineered for cinema cameras with 4.5kg payload, LiDAR autofocus tracking support, automated axis locks, and 12-hour high-capacity battery grip.',
                'features' => [
                    '4.5kg Tested Payload Capacity for Cinema Rigs',
                    'Next-Gen Automated Axis Locks for Instant Setup',
                    'Teflon-Coated Carbon Fiber Axis Arms',
                    'Bluetooth Wireless Camera Shutter Control'
                ],
                'specs' => [
                    'Payload' => '4.5 kg (10 lbs)',
                    'Battery' => 'BG30 Grip (12h Runtime)',
                    'Weight' => '1.3 kg'
                ],
                'box_contents' => [
                    'DJI RS 5 Gimbal Body',
                    'BG30 Battery Grip',
                    'Quick-Release Plate (Arca-Swiss/Manfrotto)',
                    'Focus Motor Combo with Rod Mount',
                    'Carrying Case'
                ]
            ],
            'datavideo-hs-3200' => [
                'id' => 6,
                'slug' => 'datavideo-hs-3200',
                'name' => 'Datavideo HS-3200 12-Channel Full HD Portable Video Switcher Studio',
                'category' => 'Video Switchers',
                'brand' => 'Datavideo',
                'sku' => 'DATA-HS3200-STU',
                'price' => 1150000,
                'old_price' => 1290000,
                'discount' => 11,
                'rating' => 5.0,
                'reviews_count' => 14,
                'in_stock' => true,
                'stock_branches' => ['Baitul Mukarram Flagship (Demo Ready)'],
                'warranty' => '3 Years Official Importer Warranty with On-Site Training',
                'images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1UBDPEziCcfUcrZsVf0ztZ5r7q_PN_MC51xHqhHZ8fE9iYi_CLXiYp1-zXZihrrkt0z0zvXAiWGqXsqR80S9uScTtSgQAfQIYufte0A_cmBmS7INxkujstyrdcDTCmTKSjgUMi74qE-O7I2YRPobLot4y97ayawdrOkd-lWKufzVH5lFi4JqWKUCjFwtKnm0A6KHxzuNgN4Ykrf-eSDnBgKmmxOnvfiPphUB4yuOYuwrRhj96o2ZLUjh_A'
                ],
                'overview' => 'The Datavideo HS-3200 is an all-in-one 12-channel hand-carry broadcast switcher with built-in 17.3" Full HD monitor, H.264 streaming encoder, and SD card recorder for live events and broadcast television.',
                'features' => [
                    '12 Video Inputs: 8x 3G-SDI & 4x HDMI',
                    'Built-in 17.3" Full HD 1080p Monitor',
                    'Dual Streaming Encoder (RTMP / RTSP to YouTube, Facebook, CDN)',
                    'Built-in SD Card H.264 MP4 Broadcast Recorder',
                    'Chroma Key, Luma Key, DSK, PiP, Virtual Studio Support'
                ],
                'specs' => [
                    'Inputs' => '8x SDI, 4x HDMI',
                    'Outputs' => '6x SDI, 3x HDMI (Assignable Multiview/Program)',
                    'Display' => '17.3" TFT LCD 1920x1080',
                    'Power' => 'DC 12V / 100-240V AC Adapter'
                ],
                'box_contents' => [
                    'Datavideo HS-3200 Main Unit in Heavy-Duty Flight Case',
                    'Power Adapter & Power Cord',
                    'Tally Light Interface Cable',
                    'User Manual & Calibration Certificate'
                ]
            ]
        ];
    }

    /**
     * Frontend Home Page
     */
    public function index()
    {
        return view('frontend.index');
    }

    /**
     * Search Products Page
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        $categorySlug = trim($request->get('category', 'all'));
        $sort = $request->get('sort', 'latest');

        $query = Product::with(['category', 'brand'])->where('is_service', 0)->where('show_on_website', 1);

        $brandParam = trim($request->get('brand', ''));
        if (!empty($brandParam) && $brandParam !== 'all') {
            if (is_numeric($brandParam)) {
                $query->where('brand_id', $brandParam);
            } else {
                $brandObj = Brand::where('slug', $brandParam)->orWhere('name', 'LIKE', "%{$brandParam}%")->first();
                if ($brandObj) {
                    $query->where('brand_id', $brandObj->id);
                    if (empty($q)) {
                        $q = $brandObj->name;
                    }
                }
            }
        }

        if (!empty($q) && empty($brandParam)) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('barcode', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%");
            });
        }

        if (!empty($categorySlug) && $categorySlug !== 'all') {
            if (is_numeric($categorySlug)) {
                $query->where('category_id', $categorySlug);
            } else {
                $category = Category::where('slug', $categorySlug)->orWhere('name', 'LIKE', "%{$categorySlug}%")->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
        }

        switch ($sort) {
            case 'price_low':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest('id');
                break;
        }

        $products = $query->paginate(24)->appends($request->query());
        $totalCount = $products->total();

        $navCategories = CategoryNavService::getMegaMenuCategories();

        return view('frontend.search', compact('products', 'q', 'categorySlug', 'sort', 'totalCount', 'navCategories'));
    }

    /**
     * Live AJAX Search API
     */
    public function liveSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        $categorySlug = trim($request->get('category', 'all'));

        if (empty($q) || strlen($q) < 1) {
            return response()->json(['results' => [], 'total' => 0, 'query' => $q]);
        }

        $query = Product::with(['category', 'brand'])
            ->where('is_service', 0)
            ->where('show_on_website', 1)
            ->where(function($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('barcode', 'LIKE', "%{$q}%");
            });

        if (!empty($categorySlug) && $categorySlug !== 'all') {
            if (is_numeric($categorySlug)) {
                $query->where('category_id', $categorySlug);
            } else {
                $category = Category::where('slug', $categorySlug)->orWhere('name', 'LIKE', "%{$categorySlug}%")->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
        }

        $totalMatches = $query->count();
        $products = $query->limit(8)->get();

        $results = $products->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'category' => $p->category?->name ?? 'Gear',
                'price' => (float)$p->selling_price,
                'formatted_price' => '৳ ' . number_format($p->selling_price, 0),
                'old_price' => $p->dis_selling_price ? ('৳ ' . number_format($p->dis_selling_price, 0)) : null,
                'image' => $p->featured_image,
                'has_real_image' => $p->has_real_image,
                'url' => route('frontend.product.details', $p->id)
            ];
        });

        return response()->json([
            'results' => $results,
            'total' => $totalMatches,
            'query' => $q,
            'view_all_url' => route('frontend.search', ['q' => $q, 'category' => $categorySlug])
        ]);
    }

    /**
     * Format a database Product model into a rich unified array structure.
     */
    private function formatDbProduct($dbProduct)
    {
        if (!$dbProduct) {
            return null;
        }

        $price = (float)($dbProduct->selling_price ?? 0);
        $oldPrice = (float)($dbProduct->dis_selling_price > 0 ? $dbProduct->dis_selling_price : 0);
        $discount = 0;
        if ($dbProduct->discount > 0) {
            $discount = (float)$dbProduct->discount;
        } elseif ($oldPrice > $price) {
            $discount = round((($oldPrice - $price) / $oldPrice) * 100);
        }

        $gallery = $dbProduct->gallery_images;
        if (empty($gallery)) {
            $gallery = [$dbProduct->thumbnail_url ?: asset('backend/images/no_images.png')];
        }

        $rawDescription = $dbProduct->description ?: '';
        $overview = $rawDescription ? strip_tags($rawDescription) : 'Professional production and camera equipment with official warranty from CAMERA BD.';
        if (strlen($overview) > 600) {
            $overview = substr($overview, 0, 600) . '...';
        }

        $warrantyStr = 'Official Importer Warranty';
        if ($dbProduct->warranty && $dbProduct->warranty->name) {
            $warrantyStr = $dbProduct->warranty->name;
        } elseif ($dbProduct->has_warranty && $dbProduct->warranty_value) {
            $warrantyStr = $dbProduct->warranty_value . ' ' . ($dbProduct->warranty_unit ?? 'Month') . ' Warranty';
        }

        return [
            'id' => $dbProduct->id,
            'slug' => !empty($dbProduct->slug) ? $dbProduct->slug : (\Illuminate\Support\Str::slug($dbProduct->name) ?: (string)$dbProduct->id),
            'name' => $dbProduct->name,
            'category' => $dbProduct->category?->name ?? 'Cinema & Gear',
            'brand' => $dbProduct->brand?->name ?? 'Official Brand',
            'sku' => $dbProduct->barcode ?? ('CB-' . $dbProduct->id),
            'price' => $price,
            'old_price' => $oldPrice > $price ? $oldPrice : null,
            'discount' => $discount,
            'rating' => 4.9,
            'reviews_count' => 15 + ($dbProduct->id % 25),
            'in_stock' => true,
            'stock_branches' => ['Baitul Mukarram Flagship', 'Bashundhara City Branch'],
            'warranty' => $warrantyStr,
            'images' => $gallery,
            'description' => $rawDescription,
            'overview' => $overview,
            'features' => [
                '100% Genuine and Official Channel Importer Product',
                'High quality components & durable chassis',
                'Full kit and official packaging included',
                'Fast express delivery and store pickup across Bangladesh'
            ],
            'specs' => [
                'Category' => $dbProduct->category?->name ?? 'General Gear',
                'Brand' => $dbProduct->brand?->name ?? 'Official Brand',
                'Model / Barcode' => $dbProduct->barcode ?? ('CB-' . $dbProduct->id),
                'Warranty' => $warrantyStr,
            ],
            'box_contents' => [
                $dbProduct->name . ' Unit',
                'Standard Accessories & Cables',
                'Warranty Documentation & Guide'
            ]
        ];
    }

    /**
     * Product Details Page
     */
    public function productDetails($slug = 'sony-fx30')
    {
        $catalog = $this->getCatalog();
        $product = null;
        $related = [];

        // Try to find in database first (by slug, ID, barcode, or matching name)
        $dbProduct = null;
        if (!empty($slug)) {
            $dbProduct = Product::with(['brand', 'category', 'subCategory', 'warranty'])
                ->where('is_service', 0)
                ->where('show_on_website', 1)
                ->where('slug', $slug)
                ->first();
        }

        if (!$dbProduct && is_numeric($slug)) {
            $dbProduct = Product::with(['brand', 'category', 'subCategory', 'warranty'])
                ->where('is_service', 0)
                ->where('show_on_website', 1)
                ->find($slug);
        }

        if (!$dbProduct) {
            $dbProduct = Product::with(['brand', 'category', 'subCategory', 'warranty'])
                ->where('is_service', 0)
                ->where('show_on_website', 1)
                ->where(function($q) use ($slug) {
                    $clean = str_replace('-', ' ', $slug);
                    $q->where('barcode', $slug)
                      ->orWhere('name', $clean)
                      ->orWhere('name', 'LIKE', '%' . $clean . '%');
                })
                ->first();
        }

        if ($dbProduct) {
            // URL normalization: if accessed by numeric ID, redirect to slug URL so top URL always shows slug
            $canonicalSlug = !empty($dbProduct->slug) ? $dbProduct->slug : \Illuminate\Support\Str::slug($dbProduct->name);
            if (!empty($canonicalSlug) && $slug !== $canonicalSlug && is_numeric($slug)) {
                return redirect()->route('frontend.product.details', $canonicalSlug, 301);
            }

            $product = $this->formatDbProduct($dbProduct);

            // Fetch related products from DB
            $relatedQuery = Product::with(['brand', 'category', 'subCategory', 'warranty'])
                ->where('is_service', 0)
                ->where('show_on_website', 1)
                ->where('id', '!=', $dbProduct->id);

            if ($dbProduct->category_id) {
                $relatedQuery->where('category_id', $dbProduct->category_id);
            }

            $relatedDb = $relatedQuery->latest('id')->take(4)->get();
            if ($relatedDb->isNotEmpty()) {
                $related = $relatedDb->map(function($p) {
                    return $this->formatDbProduct($p);
                })->filter()->values()->toArray();
            }
        } elseif (isset($catalog[$slug])) {
            $product = $catalog[$slug];
        } else {
            // Find closest or default to Sony FX30
            $product = $catalog['sony-fx30'];
            $product['name'] = ucwords(str_replace('-', ' ', $slug));
        }

        if (empty($related)) {
            $related = array_filter($catalog, function($item) use ($product) {
                return ($item['slug'] ?? '') !== ($product['slug'] ?? '');
            });
            $related = array_slice($related, 0, 4);
        }

        return view('frontend.product-details', compact('product', 'related'));
    }

    /**
     * Shopping Cart Page
     */
    public function cart()
    {
        return view('frontend.cart');
    }

    /**
     * Checkout Page
     */
    public function checkout()
    {
        return view('frontend.checkout');
    }

    /**
     * Process Checkout POST
     */
    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'delivery_address' => 'required|string|max:500',
            'delivery_zone' => 'nullable|string|in:inside_dhaka,outside_dhaka',
            'city' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'division' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'order_notes' => 'nullable|string|max:1000',
            'delivery_method' => 'nullable|string|max:100',
            'shipping_cost' => 'nullable|numeric|min:0',
            'cart_items' => 'nullable|string',
        ]);

        // -------------------------------------------------------------
        // 24-Hour Single Order Policy (Default: Disabled)
        // Set 'enable_24h_order_restriction' to 1 in Settings if ever needed
        // -------------------------------------------------------------
        $is24hOrderRestrictionEnabled = function_exists('get_setting') && (get_setting('enable_24h_order_restriction') === '1' || get_setting('enable_24h_order_restriction') === 'true');

        if ($is24hOrderRestrictionEnabled) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
            $phoneSuffix = strlen($cleanPhone) >= 10 ? substr($cleanPhone, -10) : $cleanPhone;
            $twentyFourHoursAgo = now()->subHours(24);

            $existingOrder = WebsiteOrder::where('created_at', '>=', $twentyFourHoursAgo)
                ->where(function($q) use ($phoneSuffix, $cleanPhone) {
                    $q->where('phone', 'like', "%{$phoneSuffix}")
                      ->orWhere('phone', $cleanPhone);
                })
                ->latest('id')
                ->first();

            $existingOrderNo = $existingOrder ? $existingOrder->order_no : null;

            if (!$existingOrder) {
                $existingInvoice = Invoice::where('created_at', '>=', $twentyFourHoursAgo)
                    ->whereHas('customer', function($q) use ($phoneSuffix, $cleanPhone) {
                        $q->where('phone', 'like', "%{$phoneSuffix}")
                          ->orWhere('phone', $cleanPhone);
                    })
                    ->latest('id')
                    ->first();
                if ($existingInvoice) {
                    $existingOrderNo = $existingInvoice->invoice_no;
                }
            }

            if ($existingOrderNo) {
                $cacheKey = 'order_attempts_' . $phoneSuffix;
                $attempts = (int) Cache::get($cacheKey, 0) + 1;
                Cache::put($cacheKey, $attempts, now()->addHours(24));

                $rawPhone = function_exists('get_setting') ? get_setting('com_phone') : null;
                $digits = preg_replace('/[^0-9]/', '', $rawPhone ?: '01352146344');
                $whatsappDigits = str_starts_with($digits, '880') ? $digits : (str_starts_with($digits, '0') ? ('88' . $digits) : ('880' . $digits));
                $siteName = function_exists('get_setting') && get_setting('com_name') ? get_setting('com_name') : 'CAMERA BD';

                $waMessage = "Hello {$siteName}! I have already placed Order #{$existingOrderNo} within the last 24 hours, and I want to order additional products or need assistance.";
                $whatsappUrl = "https://wa.me/{$whatsappDigits}?text=" . urlencode($waMessage);

                if ($attempts >= 3) {
                    // 3rd attempt within 24h: Redirect immediately to WhatsApp
                    return redirect()->away($whatsappUrl);
                }

                $remaining = 3 - $attempts;
                $errorMsg = "আপনি গত ২৪ ঘণ্টার মধ্যে ইতিমধ্যে একটি অর্ডার প্লেস করেছেন (অর্ডার নং: #{$existingOrderNo})। একজন গ্রাহক ২৪ ঘণ্টায় কেবল ১টি অনলাইন অর্ডার করতে পারবেন। জরুরি প্রয়োজনে WhatsApp-এ যোগাযোগ করুন। (আর {$remaining} বার চেষ্টা করলে সরাসরি WhatsApp-এ রিডাইরেক্ট হবে)";

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMsg)
                    ->with('whatsapp_url', $whatsappUrl)
                    ->with('existing_order_no', $existingOrderNo)
                    ->with('attempts_count', $attempts);
            }
        }

        $cartItems = [];
        if ($request->filled('cart_items')) {
            $decoded = json_decode($request->input('cart_items'), true);
            if (is_array($decoded)) {
                $cartItems = $decoded;
            }
        }

        // Generate unique Order Number: CB-YYYYMMDD-XXXX
        $todayStr = date('Ymd');
        $randomSuffix = strtoupper(substr(uniqid(), -4));
        $orderNo = 'CB-' . $todayStr . '-' . $randomSuffix;

        // Calculate pricing
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $unitPrice = floatval($item['price'] ?? 0);
            $qty = intval($item['quantity'] ?? $item['qty'] ?? 1);
            if ($qty < 1) $qty = 1;
            $subtotal += ($unitPrice * $qty);
        }

        $deliveryZone = $request->input('delivery_zone', 'inside_dhaka');
        $shippingCost = floatval($request->input('shipping_cost', 0));
        if ($shippingCost <= 0) {
            $shippingCost = ($deliveryZone === 'outside_dhaka') ? 150.00 : 100.00;
        }

        $deliveryMethod = $request->input('delivery_method');
        if (empty($deliveryMethod)) {
            $deliveryMethod = ($deliveryZone === 'outside_dhaka' || $shippingCost == 150)
                ? 'Courier Delivery (Outside Dhaka)'
                : 'Express Home Delivery (Inside Dhaka)';
        }

        $city = $request->input('city');
        if (empty($city)) {
            $city = ($deliveryZone === 'outside_dhaka' || $shippingCost == 150) ? 'Outside Dhaka' : 'Dhaka';
        }

        $division = $request->input('division');
        if (empty($division)) {
            $division = ($deliveryZone === 'outside_dhaka' || $shippingCost == 150) ? 'Outside Dhaka' : 'Dhaka Division';
        }

        $discount = floatval($request->input('discount', 0));
        $totalAmount = max(0, $subtotal + $shippingCost - $discount);

        // Find or create Customer in customers table if exists
        $customerId = null;
        try {
            if (class_exists(\App\Models\Customer::class)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
                $customer = Customer::where('phone', 'like', '%' . substr($cleanPhone, -10) . '%')->first();
                if (!$customer) {
                    $branch = Branch::where('status', 1)->where('id', '!=', 1)->first() ?? Branch::first();
                    $customer = Customer::create([
                        'name' => $validated['customer_name'],
                        'phone' => $validated['phone'],
                        'email' => $request->input('email'),
                        'address' => $validated['delivery_address'],
                        'branch_id' => $branch?->id ?? 2,
                    ]);
                }
                $customerId = $customer?->id;
            }
        } catch (\Exception $e) {
            // Non-blocking
        }

        // Default Store Branch
        $defaultBranch = Branch::where('status', 1)->where('id', '!=', 1)->first() ?? Branch::first();
        $branchId = $defaultBranch?->id ?? 2;

        DB::beginTransaction();
        try {
            $order = WebsiteOrder::create([
                'order_no' => $orderNo,
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'email' => $request->input('email'),
                'division' => $division,
                'city' => $city,
                'delivery_address' => $validated['delivery_address'],
                'order_notes' => $request->input('order_notes'),
                'delivery_method' => $deliveryMethod,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->input('payment_method', 'Cash On Delivery'),
                'payment_status' => 'unpaid',
                'order_status' => 'Processing',
                'status' => 'processing',
                'customer_id' => $customerId,
                'branch_id' => $branchId,
            ]);

            foreach ($cartItems as $item) {
                $unitPrice = floatval($item['price'] ?? 0);
                $qty = intval($item['quantity'] ?? $item['qty'] ?? 1);
                if ($qty < 1) $qty = 1;
                $lineTotal = $unitPrice * $qty;

                $productId = null;
                if (!empty($item['id']) && is_numeric($item['id']) && Product::where('id', intval($item['id']))->exists()) {
                    $productId = intval($item['id']);
                }

                if (!$productId && !empty($item['barcode'])) {
                    $productId = Product::where('barcode', $item['barcode'])->orWhere('sku', $item['barcode'])->value('id');
                }

                if (!$productId && !empty($item['name'])) {
                    $trimmedName = trim($item['name']);
                    $productId = Product::where('name', $trimmedName)
                        ->orWhereRaw('LOWER(TRIM(name)) = ?', [strtolower($trimmedName)])
                        ->orWhere('name', 'LIKE', '%' . $trimmedName . '%')
                        ->value('id');
                }

                WebsiteOrderItem::create([
                    'website_order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item['name'] ?? 'Product',
                    'product_barcode' => $item['barcode'] ?? ($item['sku'] ?? null),
                    'product_image' => $item['image'] ?? null,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'total_price' => $lineTotal,
                ]);
            }

            DB::commit();

            // Note: Meta Conversions API Purchase Event will be dispatched ONLY when Admin approves the order in Website Orders (Software)

            return redirect()->route('frontend.order-confirmation', ['order_number' => $order->order_no])
                ->with([
                    'success' => 'Your order has been placed successfully!',
                    'order_id' => $order->id,
                    'just_placed_order' => $order->order_no,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    /**
     * Order Confirmation Page
     */
    public function orderConfirmation(Request $request, $order_number = null)
    {
        $order = null;
        if ($order_number) {
            $order = WebsiteOrder::with('items')->where('order_no', $order_number)->first();
        }

        if (!$order && $request->session()->has('order_id')) {
            $order = WebsiteOrder::with('items')->find($request->session()->get('order_id'));
        }

        if (!$order) {
            return redirect()->route('frontend');
        }

        $orderNumber = $order->order_no;
        $orderData = [
            'customer_name' => $order->customer_name,
            'phone' => $order->phone,
            'email' => $order->email,
            'division' => $order->division,
            'city' => $order->city,
            'delivery_address' => $order->delivery_address,
            'payment_method' => $order->payment_method,
            'delivery_method' => $order->delivery_method,
            'shipping_cost' => $order->shipping_cost,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'total_amount' => $order->total_amount,
            'created_at' => $order->created_at,
        ];

        $orderItems = $order->items;

        // Security check: Only trigger Meta Purchase Pixel on initial order completion redirect
        $isFreshOrder = $request->session()->has('just_placed_order') && $request->session()->get('just_placed_order') === $order->order_no;

        $isApproved = ($order->status === 'approved' || strtolower($order->order_status ?? '') === 'approved');
        $approvedOrderDl = null;
        if ($isApproved) {
            $approvedOrderDl = \App\Http\Controllers\Backend\WebsiteOrderController::buildOrderDataLayerPayload($order);
        }

        return view('frontend.order-confirmation', compact('order', 'orderNumber', 'orderData', 'orderItems', 'isFreshOrder', 'isApproved', 'approvedOrderDl'));
    }

    /**
     * Check Order Status API for real-time frontend DataLayer event dispatch
     */
    public function checkOrderStatus($order_number)
    {
        $order = WebsiteOrder::with(['items.product.brand'])->where('order_no', $order_number)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $isApproved = ($order->status === 'approved' || strtolower($order->order_status ?? '') === 'approved');
        $dataLayer = null;
        if ($isApproved) {
            $dataLayer = \App\Http\Controllers\Backend\WebsiteOrderController::buildOrderDataLayerPayload($order);
        }

        return response()->json([
            'success' => true,
            'order_no' => $order->order_no,
            'status' => $order->status,
            'order_status' => $order->order_status,
            'is_approved' => $isApproved,
            'order_datalayer' => $dataLayer,
        ]);
    }

    /**
     * Wishlist Page
     */
    public function wishlist()
    {
        return view('frontend.wishlist');
    }

    /**
     * AJAX Endpoint to dispatch Meta CAPI events from browser
     */
    public function trackCapiEvent(Request $request)
    {
        $eventName = $request->input('event_name');
        if (empty($eventName)) {
            return response()->json(['success' => false, 'message' => 'Event name required'], 400);
        }

        // Hard block: Purchase event can NEVER be dispatched from client/browser AJAX
        if (strtolower($eventName) === 'purchase') {
            return response()->json([
                'success' => false,
                'message' => 'Purchase events are strictly reserved for Admin Order Approval via software backend.',
            ], 403);
        }

        $eventId = $request->input('event_id');
        $customData = $request->input('custom_data', []);
        $userData = $request->input('user_data', []);

        $result = MetaCapiService::sendEvent(
            $eventName,
            $userData,
            $customData,
            $eventId,
            $request->input('source_url', $request->header('referer') ?: url('/')),
            $request
        );

        return response()->json($result);
    }

    /**
     * Category Schema & Metadata
     */
    private function getCategoryData()
    {
        return [
            'cameras' => [
                'name' => 'Cinema & Digital Cameras',
                'slug' => 'cameras',
                'badge' => 'Cinema & Mirrorless',
                'description' => 'Explore Bangladesh’s largest official collection of Super 35, Full Frame 4K/8K cinema bodies, and broadcast video systems with authorized warranty.',
                'subcategories' => [
                    ['name' => 'Cinema Cameras', 'slug' => 'cinema-cameras', 'count' => 14, 'description' => 'Professional Super 35 & Full Frame Cinema line camera bodies.'],
                    ['name' => 'Mirrorless Bodies', 'slug' => 'mirrorless-bodies', 'count' => 20, 'description' => 'Hybrid photo and 4K/6K video mirrorless camera systems.'],
                    ['name' => 'Action & 360 VR', 'slug' => 'action-360', 'count' => 8, 'description' => 'Waterproof action cameras, pocket gimbals, and 360-degree VR capture.'],
                    ['name' => 'Vlogging & Point-and-Shoot', 'slug' => 'vlogging', 'count' => 6, 'description' => 'Compact creator cameras with fast autofocus and built-in stabilization.']
                ]
            ],
            'audio' => [
                'name' => 'Pro Audio & Wireless Microphones',
                'slug' => 'audio',
                'badge' => 'Hollyland & Broadcast Audio',
                'description' => 'Studio-grade wireless microphones, Lavalier kits, shotgun mics, and field audio recorders for crystal clear voice capture.',
                'subcategories' => [
                    ['name' => 'Wireless Mic Systems', 'slug' => 'wireless-mics', 'count' => 16, 'description' => 'Dual-channel wireless clip-on microphones for cameras and phones.'],
                    ['name' => 'Shotgun & On-Camera Mics', 'slug' => 'shotgun-mics', 'count' => 9, 'description' => 'Directional shotgun microphones with shock mounts.'],
                    ['name' => 'Studio Condensers & USB', 'slug' => 'studio-condensers', 'count' => 7, 'description' => 'XLR and USB studio vocal microphones for podcasts and voiceover.'],
                    ['name' => 'Audio Recorders & Mixers', 'slug' => 'audio-recorders', 'count' => 5, 'description' => 'Multi-track 32-bit float field audio recorders.']
                ]
            ],
            'gimbals' => [
                'name' => 'Gimbals & Stabilization',
                'slug' => 'gimbals',
                'badge' => 'DJI & Motorized Stabilizers',
                'description' => '3-axis motorized gimbals, carbon fiber tripods, motorized sliders, and heavy-duty camera rigs.',
                'subcategories' => [
                    ['name' => 'Handheld Camera Gimbals', 'slug' => 'handheld-gimbals', 'count' => 12, 'description' => '3-axis stabilizers supporting mirrorless and cinema cameras.'],
                    ['name' => 'Smartphone Gimbals', 'slug' => 'smartphone-gimbals', 'count' => 6, 'description' => 'Compact foldable mobile stabilizers with AI face tracking.'],
                    ['name' => 'Tripods & Monopods', 'slug' => 'tripods-monopods', 'count' => 15, 'description' => 'Fluid video head tripods and heavy-duty aluminum legs.']
                ]
            ],
            'switchers' => [
                'name' => 'Broadcast & Video Switchers',
                'slug' => 'switchers',
                'badge' => 'Datavideo & Live Streaming',
                'description' => 'Multi-camera HDMI/SDI video switchers, wireless transmission systems, and PTZ broadcast studios.',
                'subcategories' => [
                    ['name' => 'Live Streaming Switchers', 'slug' => 'live-switchers', 'count' => 8, 'description' => 'Multi-input hardware switchers with built-in streaming encoder.'],
                    ['name' => 'Wireless Video Transmitters', 'slug' => 'wireless-transmitters', 'count' => 10, 'description' => 'Zero latency 4K HDMI/SDI transmission systems.'],
                    ['name' => 'Field Monitors', 'slug' => 'field-monitors', 'count' => 11, 'description' => 'High-brightness on-camera field monitors with LUT support.']
                ]
            ]
        ];
    }

    /**
     * Apply common catalog filters, sorting, and pagination to a Product query.
     */
    private function applyCatalogFiltersAndPaginate(Request $request, $baseQuery, $perPage = 12)
    {
        // 1. Calculate price bounds on unfiltered base query
        $boundQuery = clone $baseQuery;
        $priceMinBound = (int)($boundQuery->min('selling_price') ?? 0);
        $priceMaxBound = (int)($boundQuery->max('selling_price') ?? 100000);
        if ($priceMaxBound <= $priceMinBound) {
            $priceMaxBound = $priceMinBound + 10000;
        }

        // 2. Discover available brands with product counts in this category scope
        $brandCountsQuery = (clone $baseQuery)
            ->selectRaw('brand_id, count(*) as count')
            ->whereNotNull('brand_id')
            ->groupBy('brand_id')
            ->pluck('count', 'brand_id');

        $availableBrands = collect();
        if ($brandCountsQuery->isNotEmpty()) {
            $availableBrands = Brand::whereIn('id', $brandCountsQuery->keys())
                ->get()
                ->map(function($b) use ($brandCountsQuery) {
                    $b->products_count = $brandCountsQuery[$b->id] ?? 0;
                    return $b;
                })
                ->sortByDesc('products_count')
                ->values();
        }

        // 3. Apply request filters to query
        $filteredQuery = clone $baseQuery;

        // Brand filter
        $selectedBrands = (array)$request->input('brand', []);
        $selectedBrands = array_filter($selectedBrands, fn($b) => $b !== '' && $b !== null);
        if (!empty($selectedBrands)) {
            $filteredQuery->where(function($bq) use ($selectedBrands) {
                $bq->whereIn('brand_id', $selectedBrands)
                   ->orWhereHas('brand', function($sub) use ($selectedBrands) {
                       $sub->whereIn('name', $selectedBrands);
                   });
            });
        }

        // Subcategory filter (if present)
        $selectedSubCats = (array)$request->input('sub_category', []);
        $selectedSubCats = array_filter($selectedSubCats, fn($s) => $s !== '' && $s !== null);
        if (!empty($selectedSubCats)) {
            $filteredQuery->where(function($sq) use ($selectedSubCats) {
                $sq->whereIn('sub_category_id', $selectedSubCats)
                   ->orWhereHas('subCategory', function($sub) use ($selectedSubCats) {
                       $sub->whereIn('name', $selectedSubCats);
                   });
            });
        }

        // Price filter
        if ($request->filled('max_price')) {
            $filteredQuery->where('selling_price', '<=', (float)$request->input('max_price'));
        }
        if ($request->filled('min_price')) {
            $filteredQuery->where('selling_price', '>=', (float)$request->input('min_price'));
        }

        // Stock status filter
        $selectedStock = $request->input('stock', 'all');
        if ($selectedStock === 'in_stock') {
            $filteredQuery->where('main_qty', '>', 0);
        } elseif ($selectedStock === 'pre_order') {
            $filteredQuery->where(function($sq) {
                $sq->whereNull('main_qty')->orWhere('main_qty', '<=', 0);
            });
        }

        // Sorting
        $selectedSort = $request->input('sort', 'featured');
        switch ($selectedSort) {
            case 'price_low':
                $filteredQuery->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $filteredQuery->orderBy('selling_price', 'desc');
                break;
            case 'name':
                $filteredQuery->orderBy('name', 'asc');
                break;
            case 'newest':
            case 'featured':
            default:
                $filteredQuery->latest('id');
                break;
        }

        // 4. Paginate and transform
        $paginated = $filteredQuery->paginate($perPage)->withQueryString();
        $products = $paginated->through(fn($p) => $this->formatDbProduct($p));

        return [
            'products' => $products,
            'availableBrands' => $availableBrands,
            'priceMinBound' => $priceMinBound,
            'priceMaxBound' => $priceMaxBound,
            'selectedBrands' => $selectedBrands,
            'selectedStock' => $selectedStock,
            'selectedSort' => $selectedSort,
            'selectedMaxPrice' => $request->input('max_price', $priceMaxBound),
        ];
    }

    /**
     * All Categories / Main Categories Index
     */
    public function allCategories(Request $request)
    {
        $navCategories = CategoryNavService::getMegaMenuCategories();

        $baseQuery = Product::with(['brand', 'category', 'subCategory', 'warranty'])
            ->where('is_service', 0)
            ->where('show_on_website', 1);

        $filterData = $this->applyCatalogFiltersAndPaginate($request, $baseQuery, 12);

        $currentCategory = [
            'name' => 'All Production Gear Categories',
            'slug' => 'all',
            'badge' => 'Official Departments',
            'description' => 'Browse our complete catalog of cinema cameras, wireless audio, video switchers, gimbals, lenses, and broadcast accessories.',
            'subcategories' => array_map(function($c) {
                return [
                    'name' => $c['name'],
                    'slug' => $c['slug'],
                    'count' => count($c['subcategories'] ?? []),
                    'icon' => $c['icon'] ?? 'category',
                ];
            }, $navCategories)
        ];

        return view('frontend.category', array_merge(['currentCategory' => $currentCategory], $filterData));
    }

    /**
     * Category Page
     */
    public function category(Request $request, $slug = 'cameras')
    {
        $slug = $slug ?: 'cameras';
        $navCategories = CategoryNavService::getMegaMenuCategories();
        
        // Find matching category in nav service or DB
        $found = null;
        foreach ($navCategories as $cat) {
            if ($cat['slug'] === $slug || str_contains($cat['slug'], $slug) || str_contains($slug, $cat['slug'])) {
                $found = $cat;
                break;
            }
        }

        $dbCat = null;
        if (is_numeric($slug)) {
            $dbCat = Category::with('subCategories')->find($slug);
        } else {
            $cleanSlugName = str_replace('-', ' ', $slug);
            $dbCat = Category::with('subCategories')
                ->where('name', 'LIKE', $cleanSlugName)
                ->orWhere('name', 'LIKE', "%{$cleanSlugName}%")
                ->first();
        }

        if ($dbCat) {
            $subCatsList = [];
            if ($dbCat->subCategories && $dbCat->subCategories->isNotEmpty()) {
                foreach ($dbCat->subCategories as $s) {
                    $subCatsList[] = [
                        'id' => $s->id,
                        'name' => $s->name,
                        'slug' => \Illuminate\Support\Str::slug($s->name),
                        'count' => Product::where('is_service', 0)->where('show_on_website', 1)->where('sub_category_id', $s->id)->count()
                    ];
                }
            } elseif ($found && !empty($found['subcategories'])) {
                $subCatsList = $found['subcategories'];
            }

            $currentCategory = [
                'id' => $dbCat->id,
                'name' => ucwords(strtolower($dbCat->name)),
                'slug' => \Illuminate\Support\Str::slug($dbCat->name),
                'badge' => 'Official Category',
                'description' => 'Explore authentic ' . ucwords(strtolower($dbCat->name)) . ' with official warranty and express delivery across Bangladesh.',
                'subcategories' => $subCatsList,
            ];
        } elseif ($found) {
            $currentCategory = [
                'name' => $found['name'],
                'slug' => $found['slug'],
                'badge' => 'Official Category',
                'description' => 'Explore authentic ' . $found['name'] . ' with official warranty and express delivery across Bangladesh.',
                'subcategories' => $found['subcategories'] ?? [],
            ];
        } else {
            $categoryData = $this->getCategoryData();
            if (isset($categoryData[$slug])) {
                $currentCategory = $categoryData[$slug];
            } else {
                $currentCategory = [
                    'name' => ucwords(str_replace('-', ' ', $slug)),
                    'slug' => $slug,
                    'badge' => 'Special Collection',
                    'description' => 'Explore selected cinema gear, lenses, and accessories in Bangladesh.',
                    'subcategories' => []
                ];
            }
        }

        // Query database products for this category
        $baseQuery = Product::with(['brand', 'category', 'subCategory', 'warranty'])->where('is_service', 0)->where('show_on_website', 1);
        if ($dbCat) {
            $baseQuery->where('category_id', $dbCat->id);
        } elseif ($found && !empty($found['id'])) {
            $baseQuery->where('category_id', $found['id']);
        } else {
            $cleanName = str_replace('-', ' ', $slug);
            $baseQuery->where(function($q) use ($cleanName) {
                $q->whereHas('category', function($cq) use ($cleanName) {
                    $cq->where('name', 'LIKE', "%{$cleanName}%");
                })->orWhere('name', 'LIKE', "%{$cleanName}%");
            });
        }

        $filterData = $this->applyCatalogFiltersAndPaginate($request, $baseQuery, 12);

        return view('frontend.category', array_merge(['currentCategory' => $currentCategory], $filterData));
    }

    /**
     * Subcategory Page
     */
    public function subcategory(Request $request, $category_slug, $subcategory_slug)
    {
        $navCategories = CategoryNavService::getMegaMenuCategories();
        
        $foundCat = null;
        foreach ($navCategories as $cat) {
            if ($cat['slug'] === $category_slug || str_contains($cat['slug'], $category_slug) || str_contains($category_slug, $cat['slug'])) {
                $foundCat = $cat;
                break;
            }
        }

        if ($foundCat) {
            $currentCategory = [
                'name' => $foundCat['name'],
                'slug' => $foundCat['slug'],
                'subcategories' => $foundCat['subcategories'] ?? []
            ];
        } else {
            $currentCategory = [
                'name' => ucwords(str_replace('-', ' ', $category_slug)),
                'slug' => $category_slug,
                'subcategories' => []
            ];
        }

        // Find subcategory
        $currentSubcategory = null;
        $dbSubCatId = null;
        if (!empty($currentCategory['subcategories'])) {
            foreach ($currentCategory['subcategories'] as $sub) {
                if ($sub['slug'] === $subcategory_slug || str_contains($sub['slug'], $subcategory_slug) || str_contains($subcategory_slug, $sub['slug'])) {
                    $currentSubcategory = $sub;
                    $dbSubCatId = $sub['id'] ?? null;
                    break;
                }
            }
        }

        if (!$currentSubcategory) {
            $cleanSubName = str_replace('-', ' ', $subcategory_slug);
            $dbSub = \App\Models\SubCategory::where('name', 'LIKE', "%{$cleanSubName}%")->first();
            if ($dbSub) {
                $dbSubCatId = $dbSub->id;
                $currentSubcategory = [
                    'id' => $dbSub->id,
                    'name' => $dbSub->name,
                    'slug' => \Illuminate\Support\Str::slug($dbSub->name),
                    'description' => 'Explore authentic gear with warranty and express delivery across Bangladesh.'
                ];
            } else {
                $currentSubcategory = [
                    'name' => ucwords(str_replace('-', ' ', $subcategory_slug)),
                    'slug' => $subcategory_slug,
                    'description' => 'Explore authentic gear with warranty and express delivery across Bangladesh.'
                ];
            }
        }

        // Query database products
        $baseQuery = Product::with(['brand', 'category', 'subCategory', 'warranty'])->where('is_service', 0)->where('show_on_website', 1);
        if ($dbSubCatId) {
            $baseQuery->where('sub_category_id', $dbSubCatId);
        } else {
            $cleanSubName = str_replace('-', ' ', $subcategory_slug);
            $baseQuery->where(function($q) use ($cleanSubName) {
                $q->whereHas('subCategory', function($sq) use ($cleanSubName) {
                    $sq->where('name', 'LIKE', "%{$cleanSubName}%");
                })->orWhere('name', 'LIKE', "%{$cleanSubName}%");
            });
        }

        $filterData = $this->applyCatalogFiltersAndPaginate($request, $baseQuery, 12);

        return view('frontend.subcategory', array_merge([
            'currentCategory' => $currentCategory,
            'currentSubcategory' => $currentSubcategory
        ], $filterData));
    }

    /**
     * Privacy Policy Page
     */
    public function privacyPolicy()
    {
        $pageType = 'privacy';
        $pageTitle = 'Privacy Policy';
        $metaDescription = 'Privacy Policy and Data Protection practices of ' . (get_setting('com_name') ?: 'CAMERA BD') . '.';
        $content = get_setting('privacy_policy_content');

        return view('frontend.legal', compact('pageType', 'pageTitle', 'metaDescription', 'content'));
    }

    /**
     * Terms of Service Page
     */
    public function termsOfService()
    {
        $pageType = 'terms';
        $pageTitle = 'Terms of Service';
        $metaDescription = 'Terms and Conditions for purchasing cameras, cine gear, and optical equipment from ' . (get_setting('com_name') ?: 'CAMERA BD') . '.';
        $content = get_setting('terms_service_content');

        return view('frontend.legal', compact('pageType', 'pageTitle', 'metaDescription', 'content'));
    }

    /**
     * Warranty Terms Page
     */
    public function warrantyTerms()
    {
        $pageType = 'warranty';
        $pageTitle = 'Official Warranty Terms & Policy';
        $metaDescription = 'Official Brand Warranty Policy, Sensor Lab Care, and Claim Guidelines of ' . (get_setting('com_name') ?: 'CAMERA BD') . '.';
        $content = get_setting('warranty_terms_content');

        return view('frontend.legal', compact('pageType', 'pageTitle', 'metaDescription', 'content'));
    }

    /**
     * Site Map Page
     */
    public function siteMap()
    {
        $pageType = 'sitemap';
        $pageTitle = 'Site Map & Catalog Index';
        $metaDescription = 'Explore all categories, cinema gear, brands, and store outlets of ' . (get_setting('com_name') ?: 'CAMERA BD') . '.';
        $content = get_setting('sitemap_note_content');

        $categories = Category::with('subcategories')->orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        $branches = Branch::withoutGlobalScopes()->where('status', 1)->where('id', '!=', 1)->orderBy('id', 'asc')->get();

        return view('frontend.legal', compact('pageType', 'pageTitle', 'metaDescription', 'content', 'categories', 'brands', 'branches'));
    }
}
