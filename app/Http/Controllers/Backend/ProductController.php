<?php

namespace App\Http\Controllers\Backend;

use App\Models\Unit;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\BranchBrand;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use App\Models\BusinessSetting;
use App\Models\ProductVariation;
use App\Models\Warranty;
use App\Models\HomepageSection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $data['product_id'] = $request->product_id;
        $data['category_id'] = $request->category_id;
        $data['sub_category_id'] = $request->sub_category_id;
        $data['barcode'] = $request->barcode;
        $activeSection = $request->get('section', 'all');
        $data['activeSection'] = $activeSection;
        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();
        
        $homepageSections = HomepageSection::orderBy('order', 'asc')->get();
        $data['homepageSections'] = $homepageSections;

        $query = Product::with(['brand', 'category', 'subCategory', 'unit.related_unit', 'homepageSections'])->where('is_service', 0);

        // Filter by Homepage Section if selected
        if ($activeSection !== 'all') {
            $sec = $homepageSections->firstWhere('key', $activeSection) ?? HomepageSection::find($activeSection);
            if ($sec) {
                $query->where(function($q) use ($sec) {
                    $q->whereHas('homepageSections', function($sub) use ($sec) {
                        $sub->where('homepage_sections.id', $sec->id);
                    });
                    if ($sec->key === 'flash_deal') $q->orWhere('is_flash_deal', 1);
                    elseif ($sec->key === 'popular') $q->orWhere('is_popular', 1);
                    elseif ($sec->key === 'hollyland') $q->orWhere('is_hollyland', 1);
                    elseif ($sec->key === 'essential') $q->orWhere('is_essential', 1);
                });
                $data['activeSectionModel'] = $sec;
            }
        }

        // Section counts for tabs
        $data['totalProductsCount'] = Product::where('is_service', 0)->count();
        $sectionCounts = [];
        foreach ($homepageSections as $hSec) {
            $count = Product::where('is_service', 0)->where(function($q) use ($hSec) {
                $q->whereHas('homepageSections', function($sub) use ($hSec) {
                    $sub->where('homepage_sections.id', $hSec->id);
                });
                if ($hSec->key === 'flash_deal') $q->orWhere('is_flash_deal', 1);
                elseif ($hSec->key === 'popular') $q->orWhere('is_popular', 1);
                elseif ($hSec->key === 'hollyland') $q->orWhere('is_hollyland', 1);
                elseif ($hSec->key === 'essential') $q->orWhere('is_essential', 1);
            })->count();
            $sectionCounts[$hSec->key] = $count;
            $hSec->products_count = $count;
        }
        $data['sectionCounts'] = $sectionCounts;
        $data['flashDealCount'] = $sectionCounts['flash_deal'] ?? 0;
        $data['popularCount'] = $sectionCounts['popular'] ?? 0;
        $data['hollylandCount'] = $sectionCounts['hollyland'] ?? 0;
        $data['essentialCount'] = $sectionCounts['essential'] ?? 0;

        // All active products for quick section assignment modal
        $data['allProductsList'] = Product::with('homepageSections')->where('is_service', 0)->orderBy('name', 'asc')->get(['id', 'name', 'selling_price', 'barcode', 'is_flash_deal', 'is_popular', 'is_hollyland', 'is_essential']);

        if ($userBranchId == 1) {
            if ($filterBranchId && empty($request->barcode) && empty($request->product_id) && $activeSection === 'all') {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $query->whereIn('id', $productIds);
                $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->orderBy('id', 'desc')->paginate(20);
                $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
            } else {
                $data['produc'] = Product::where('is_service', 0)->orderBy('id', 'desc')->paginate(20);
                $data['categories'] = Category::orderBy('id', 'desc')->get();
            }
        } else {
            $categoryds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->orderBy('id', 'desc')->paginate(20);
            $query->whereIn('id', $productIds)->orderBy('id', 'desc')->paginate(20);
            $query->whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
            $query->whereIn('id', $productIds)->count();
        }

        $data['com'] = BusinessSetting::where('id', 6)->first();

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }
        if ($request->barcode != null) {
            $barcodeVal = $request->barcode;
            $query->where(function($q) use ($barcodeVal) {
                $q->where('barcode', $barcodeVal)
                  ->orWhere(function($subQ) use ($barcodeVal) {
                      $subQ->where('barcode', '!=', '')
                           ->whereNotNull('barcode')
                           ->whereRaw('? LIKE CONCAT(barcode, "%")', [$barcodeVal]);
                  });
            });
        }
        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->sub_category_id != null) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->category_id != null && $request->product_id != null) {
            $query->where('category_id', $request->category_id)->where('id', $request->product_id);
        }
        $data['products'] = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(15)->appends($request->all());

        return view('backend.pages.product.index', $data);
    }

    public function create()
    {

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $brandids = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $data['products'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->paginate(20);
                $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
                $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
                $data['brands'] = Brand::whereIn('id', $brandids)->orderBy('id', 'desc')->get();
            } else {
                $data['allProduct'] = Product::orderBy('id', 'desc')->get();
                $data['products']  = Product::orderBy('id', 'desc')->paginate(20);
                $data['categories'] = Category::orderBy('id', 'desc')->get();
                $data['brands'] = Brand::orderBy('id', 'desc')->get();
            }
        } else {
            $categoryds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $brandids = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['products']  = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->paginate(20);
            $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
            $data['brands'] = Brand::whereIn('id', $brandids)->orderBy('id', 'desc')->get();
        }

        // $brands = Brand::all();
        // $categories = Category::all();
        $data['subCategories'] = SubCategory::orderBy('name', 'asc')->get();
        $variations = Variation::all();
        $sizes = ProductSize::all();
        $colors = ProductColor::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        $allBranch = Branch::get();
        $warranties = Warranty::where('status', 1)->orderBy('name', 'asc')->get();
        return view('backend.pages.product.create', $data, compact('allBranch', 'sizes', 'colors', 'variations', 'units', 'suppliers', 'warranties'));
    }

    function generateUniqueBarcode()
    {
        do {
            // always 6 digit (leading zero সহ)
            $barcode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $exists = \App\Models\Product::where('barcode', $barcode)->exists();
        } while ($exists);

        return $barcode;
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate(
            [
                'name' => 'required',
                'barcode' => 'nullable|unique:products',
                'category_id' => 'required',
                'unit_id' => 'required',
                'purchase_price' => 'required',
                'selling_price' => 'required',
                'status' => 'required',
            ],
            [
                'barcode.unique' => 'The product barcode has already been taken !',
            ]
        );

        $product = new Product();
        $product->name = $request->name;

        // Auto-generate / unique slug
        $baseSlug = !empty($request->slug) ? \Illuminate\Support\Str::slug($request->slug) : \Illuminate\Support\Str::slug($request->name);
        $baseSlug = $baseSlug ?: 'product';
        $finalSlug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $finalSlug)->exists()) {
            $finalSlug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $product->slug = $finalSlug;
        $product->date = date('Y-m-d');
        if (empty($request->barcode)) {
            $product->barcode = $this->generateUniqueBarcode();
        } else {
            $product->barcode = $request->barcode;
        }

        $product->is_service = 0;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id;
        $product->main_qty = $request->main_qty;
        $product->has_serial = $request->has_serial;
        $product->purchase_price = $request->purchase_price !== null ? (float) $request->purchase_price : 0;
        $dis_selling_price = $request->dis_selling_price !== null ? (float) $request->dis_selling_price : 0;
        $product->dis_selling_price = $dis_selling_price;
        $discount = $request->discount;
        if (!empty($discount) && str_contains($discount, '%')) {
            $percent = (float) str_replace('%', '', $discount);
            $discount = ($dis_selling_price * $percent) / 100;
        } elseif ($discount !== null && $discount !== '') {
            $discount = (float) $discount;
        } else {
            $discount = 0;
        }
        $product->discount = $discount;
        $product->selling_price = $request->selling_price !== null ? (float) $request->selling_price : ($dis_selling_price - $discount);
        $product->status = $request->status;
        $product->description = $request->description;
        $product->imei = $request->imei;

        if ($request->filled('warranty_id')) {
            $warranty = Warranty::find($request->warranty_id);
            if ($warranty) {
                $product->warranty_id = $warranty->id;
                $product->has_warranty = 1;
                $product->warranty_value = $warranty->duration;
                $product->warranty_unit = $warranty->period;
            } else {
                $product->warranty_id = null;
                $product->has_warranty = 0;
                $product->warranty_value = null;
                $product->warranty_unit = null;
            }
        } elseif ($request->has_warranty == 1) {
            $product->warranty_id = null;
            $product->has_warranty = 1;
            $product->warranty_value = $request->warranty_value ?? null;
            $product->warranty_unit = $request->warranty_unit ?? 'Month';
        } else {
            $product->warranty_id = null;
            $product->has_warranty = 0;
            $product->warranty_value = null;
            $product->warranty_unit = null;
        }

        $product->is_flash_deal = $request->has('is_flash_deal') ? 1 : 0;
        $product->is_popular = $request->has('is_popular') ? 1 : 0;
        $product->is_hollyland = $request->has('is_hollyland') ? 1 : 0;
        $product->is_essential = $request->has('is_essential') ? 1 : 0;
        $product->show_on_website = $request->has('show_on_website') ? ($request->show_on_website ? 1 : 0) : 1;

        $product->created_by = Auth::user()->id;

        if ($product->save()) {
            if ($request->size != null || $request->color != null) {
                $sizes = $request->size ?? [null];
                $colors = $request->color ?? [null];
                foreach ($sizes as $size) {
                    foreach ($colors as $color) {
                        $product->product_variations()->create([
                            'product_id' => $product->id,
                            'size_id' => $size,
                            'color_id' => $color,
                        ]);
                    }
                }
            }
        }

        // 1. Handle Dedicated Thumbnail / Main Cover Image
        $thumbnailName = null;
        if ($request->hasFile('thumbnail')) {
            $thumbFile = $request->file('thumbnail');
            if ($thumbFile && $thumbFile->isValid()) {
                $thumbnailName = 'thumb_' . date('YmdHis') . '_' . uniqid() . '.' . $thumbFile->getClientOriginalExtension();
                $thumbFile->move(public_path('uploads/products/'), $thumbnailName);
            }
        }

        // 2. Handle Additional Gallery Images (Multiple)
        $uploadedGalleryImages = [];
        $galleryInput = $request->file('gallery_images') ?? $request->file('images');
        if ($galleryInput) {
            $files = is_array($galleryInput) ? $galleryInput : [$galleryInput];
            foreach ($files as $index => $file) {
                if ($file && $file->isValid()) {
                    $imgName = 'gallery_' . date('YmdHis') . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products/'), $imgName);
                    $uploadedGalleryImages[] = $imgName;
                }
            }
        }

        // Fallback: If no dedicated thumbnail was chosen, but gallery images were uploaded, use 1st gallery photo as thumbnail
        if (empty($thumbnailName) && !empty($uploadedGalleryImages)) {
            $thumbnailName = $uploadedGalleryImages[0];
        }

        $product->thumbnail = $thumbnailName;
        if (!empty($uploadedGalleryImages)) {
            $product->images = json_encode(array_values($uploadedGalleryImages));
        } else {
            $product->images = null;
        }

        DB::transaction(function () use ($request, $product) {
            if (auth()->user()->branch_id == 1) {
                foreach ($request->branch_id as $branch_id) {
                    // Ensure the category exists in the branch
                    $categoryExists = DB::table('branch_categories')
                        ->where('branch_id', $branch_id)
                        ->where('category_id', $product->category_id)
                        ->exists();

                    if (!$categoryExists) {
                        DB::table('branch_categories')->insert([
                            'branch_id' => $branch_id,
                            'category_id' => $product->category_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Ensure the brand exists in the branch
                    if ($product->brand_id) {
                        $brandExists = DB::table('branch_brands')
                            ->where('branch_id', $branch_id)
                            ->where('brand_id', $product->brand_id)
                            ->exists();

                        if (!$brandExists) {
                            DB::table('branch_brands')->insert([
                                'branch_id' => $branch_id,
                                'brand_id' => $product->brand_id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    // Save product to the branch
                    BranchProduct::create([
                        'product_id' => $product->id,
                        'branch_id' => $branch_id,
                    ]);
                }
            } else {
                $branch_id = auth()->user()->branch_id;

                // Ensure the category exists in the user's branch
                $categoryExists = DB::table('branch_categories')
                    ->where('branch_id', $branch_id)
                    ->where('category_id', $product->category_id)
                    ->exists();

                if (!$categoryExists) {
                    DB::table('branch_categories')->insert([
                        'branch_id' => $branch_id,
                        'category_id' => $product->category_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Ensure the brand exists in the user's branch
                if ($product->brand_id) {
                    $brandExists = DB::table('branch_brands')
                        ->where('branch_id', $branch_id)
                        ->where('brand_id', $product->brand_id)
                        ->exists();

                    if (!$brandExists) {
                        DB::table('branch_brands')->insert([
                            'branch_id' => $branch_id,
                            'brand_id' => $product->brand_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Save product to the user's branch
                BranchProduct::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch_id,
                ]);
            }
        });

        $product->save();
        $product->load('category', 'brand');
        session()->flash('success', __('Product created successfully!'));
        logActivity('Create Product', "Product '{$product->name}' created", $product);
        return Redirect()->route('product.index');
    }

    public function edit(string $id)
    {
        $data['brands'] = Brand::all();
        $data['categories'] = Category::all();
        $data['subCategories'] = SubCategory::orderBy('name', 'asc')->get();
        $data['sizes'] = ProductSize::all();
        $data['colors'] = ProductColor::all();
        $data['units'] = Unit::all();
        $data['allBranch'] = Branch::get();
        $selectedVariations = ProductVariation::where('product_id', $id)->get();
        $data['selectedColorIds'] = $selectedVariations->pluck('color_id')->unique()->toArray();
        $data['selectedSizeIds'] = $selectedVariations->pluck('size_id')->unique()->toArray();
        
        $lockedSizeIds = [];
        $lockedColorIds = [];
        foreach ($selectedVariations as $pv) {
            if ($pv->hasTransactions()) {
                if ($pv->size_id) {
                    $lockedSizeIds[] = $pv->size_id;
                }
                if ($pv->color_id) {
                    $lockedColorIds[] = $pv->color_id;
                }
            }
        }
        $data['lockedSizeIds'] = array_unique($lockedSizeIds);
        $data['lockedColorIds'] = array_unique($lockedColorIds);
        $data['warranties'] = Warranty::where('status', 1)->orderBy('name', 'asc')->get();

        $data['data'] = Product::with(['brand', 'category', 'unit.related_unit', 'warranty'])->where('id', $id)->first();
        return view('backend.pages.product.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'barcode' => 'nullable|unique:products,barcode,' . $id,
            'category_id' => 'required',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'status' => 'required',
        ], [
            'barcode.unique' => 'The product barcode has already been taken !',
        ]);

        $product = Product::findorfail($id);

        if ($request->has('unit_id') && $request->unit_id != $product->unit_id) {
            if ($product->hasTransactions()) {
                session()->flash('error', __('Cannot change product unit because this product already has transaction history.'));
                return back();
            }
        }

        // Check if any variation being deleted has transactions/stock
        $existing = ProductVariation::where('product_id', $product->id)->get();
        $sizes = $request->size ?? [];
        $colors = $request->color ?? [];

        // Ensure locked/transaction size & color IDs are preserved even if omitted by browser due to disabled attribute
        foreach ($existing as $old) {
            if ($old->hasTransactions()) {
                if ($old->size_id && !in_array($old->size_id, $sizes)) {
                    $sizes[] = (string)$old->size_id;
                }
                if ($old->color_id && !in_array($old->color_id, $colors)) {
                    $colors[] = (string)$old->color_id;
                }
            }
        }

        $new = collect();
        if (!empty($sizes) && !empty($colors)) {
            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $new->push([$size, $color]);
                }
            }
        } elseif (!empty($sizes) && empty($colors)) {
            foreach ($sizes as $size) {
                $new->push([$size, null]);
            }
        } elseif (empty($sizes) && !empty($colors)) {
            foreach ($colors as $color) {
                $new->push([null, $color]);
            }
        }

        $cannotDelete = [];
        foreach ($existing as $old) {
            $exists = $new->contains(function ($v) use ($old) {
                return $v[0] == $old->size_id && $v[1] == $old->color_id;
            });

            if (!$exists && $old->hasTransactions()) {
                $sizeName = $old->size->size ?? '';
                $colorName = $old->color->color ?? '';
                $comb = [];
                if ($sizeName) $comb[] = "Size: $sizeName";
                if ($colorName) $comb[] = "Color: $colorName";
                $cannotDelete[] = implode(', ', $comb) ?: 'Default Variation';
            }
        }

        if (!empty($cannotDelete)) {
            session()->flash('error', __('Cannot remove variation(s) (:variations) because they have stock or purchase history.', ['variations' => implode(' | ', $cannotDelete)]));
            return back();
        }
        $product->name = $request->name;

        // Auto-generate / unique slug
        $baseSlug = !empty($request->slug) ? \Illuminate\Support\Str::slug($request->slug) : \Illuminate\Support\Str::slug($request->name);
        $baseSlug = $baseSlug ?: 'product';
        $finalSlug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $finalSlug)->where('id', '!=', $product->id)->exists()) {
            $finalSlug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $product->slug = $finalSlug;
        $numberBarcode = rand(000000, 999999);
        if ($product->barcode == NULL) {
            $product->barcode = $numberBarcode;
        } else {
            $product->barcode = $request->barcode;
        }
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id ?? $product->unit_id;
        $product->main_qty = $request->main_qty;
        $product->purchase_price = $request->purchase_price !== null ? (float) $request->purchase_price : 0;
        $discount = $request->discount;
        $dis_selling_price = $request->dis_selling_price !== null ? (float) $request->dis_selling_price : 0;
        if (!empty($discount) && str_contains($discount, '%')) {
            $percent = (float) str_replace('%', '', $discount);
            $discount = ($dis_selling_price * $percent) / 100;
        } elseif ($discount !== null && $discount !== '') {
            $discount = (float) $discount;
        } else {
            $discount = 0;
        }
        $product->discount = $discount;
        $product->dis_selling_price = $dis_selling_price;
        $product->selling_price = $request->selling_price !== null ? (float) $request->selling_price : ($dis_selling_price - $discount);
        $product->status = $request->status;
        $product->description = $request->description;
        $product->imei = $request->imei;

        if ($request->filled('warranty_id')) {
            $warranty = Warranty::find($request->warranty_id);
            if ($warranty) {
                $product->warranty_id = $warranty->id;
                $product->has_warranty = 1;
                $product->warranty_value = $warranty->duration;
                $product->warranty_unit = $warranty->period;
            } else {
                $product->warranty_id = null;
                $product->has_warranty = 0;
                $product->warranty_value = null;
                $product->warranty_unit = null;
            }
        } elseif ($request->has_warranty == 1) {
            $product->warranty_id = null;
            $product->has_warranty = 1;
            $product->warranty_value = $request->warranty_value ?? null;
            $product->warranty_unit = $request->warranty_unit ?? 'Month';
        } else {
            $product->warranty_id = null;
            $product->has_warranty = 0;
            $product->warranty_value = null;
            $product->warranty_unit = null;
        }

        $product->is_flash_deal = $request->has('is_flash_deal') ? 1 : 0;
        $product->is_popular = $request->has('is_popular') ? 1 : 0;
        $product->is_hollyland = $request->has('is_hollyland') ? 1 : 0;
        $product->is_essential = $request->has('is_essential') ? 1 : 0;
        $product->show_on_website = $request->has('show_on_website') ? 1 : 0;

        if ($product->save()) {

            $sizes = $request->size ?? [];
            $colors = $request->color ?? [];

            $existing = ProductVariation::where('product_id', $product->id)->get();

            foreach ($existing as $old) {
                if ($old->hasTransactions()) {
                    if ($old->size_id && !in_array($old->size_id, $sizes)) {
                        $sizes[] = (string)$old->size_id;
                    }
                    if ($old->color_id && !in_array($old->color_id, $colors)) {
                        $colors[] = (string)$old->color_id;
                    }
                }
            }

            $new = collect();

            // CASE 1: Size + Color Both Provided
            if (!empty($sizes) && !empty($colors)) {
                foreach ($sizes as $size) {
                    foreach ($colors as $color) {
                        $new->push([$size, $color]);

                        ProductVariation::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'size_id' => $size,
                                'color_id' => $color
                            ],
                            []
                        );
                    }
                }
            }

            // CASE 2: Only Size Provided
            if (!empty($sizes) && empty($colors)) {
                foreach ($sizes as $size) {
                    $new->push([$size, null]);

                    ProductVariation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size_id' => $size,
                            'color_id' => null
                        ],
                        []
                    );
                }
            }

            // CASE 3: Only Color Provided
            if (empty($sizes) && !empty($colors)) {
                foreach ($colors as $color) {
                    $new->push([null, $color]);

                    ProductVariation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size_id' => null,
                            'color_id' => $color
                        ],
                        []
                    );
                }
            }

            // DELETE MISSING VARIATIONS
            foreach ($existing as $old) {
                $exists = $new->contains(function ($v) use ($old) {
                    return $v[0] == $old->size_id && $v[1] == $old->color_id;
                });

                if (!$exists) {
                    $old->delete();
                }
            }
        }

        // if ($product->save()) {
        //     $newSizes = $request->size ?? [];
        //     $newColors = $request->color ?? [];

        //     $desiredCombinations = [];
        //     foreach ($newSizes as $sizeId) {
        //         foreach ($newColors as $colorId) {
        //             $desiredCombinations[] = ['size_id' => $sizeId, 'color_id' => $colorId];
        //         }
        //     }
        //     $existingCombinations = $product->product_variations()->get(['id', 'size_id', 'color_id']);
        //     $existingMap = [];
        //     foreach ($existingCombinations as $variation) {
        //         $existingMap[$variation->size_id . '-' . $variation->color_id] = $variation->id;
        //     }
        //     foreach ($desiredCombinations as $combo) {
        //         $key = $combo['size_id'] . '-' . $combo['color_id'];
        //         if (!isset($existingMap[$key])) {
        //             $product->product_variations()->create($combo);
        //         } else {
        //             unset($existingMap[$key]);
        //         }
        //     }
        //     if (!empty($existingMap)) {
        //         $product->product_variations()->whereIn('id', array_values($existingMap))->delete();
        //     }
        // }


        DB::transaction(function () use ($request, $product) {
            if ($product->save()) {
                if (auth()->user()->branch_id == 1) {
                    $selectedBranchIds = $request->branch_id ?? [];
                    BranchProduct::where('product_id', $product->id)
                        ->whereNotIn('branch_id', $selectedBranchIds)
                        ->delete();
                    foreach ($selectedBranchIds as $branch_id) {
                        BranchProduct::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'branch_id' => $branch_id,
                            ]
                        );
                    }
                } else {
                    $branch_id = auth()->user()->branch_id;
                    BranchProduct::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'branch_id' => $branch_id,
                        ]
                    );
                }
            }
        });

        // 1. Handle Thumbnail (Cover Image)
        $currentThumbnail = $product->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $thumbFile = $request->file('thumbnail');
            if ($thumbFile && $thumbFile->isValid()) {
                // Delete previous local thumbnail file if it was replaced
                if (!empty($currentThumbnail) && !str_starts_with($currentThumbnail, 'http')) {
                    $oldThumbPath = public_path('uploads/products/' . $currentThumbnail);
                    if (file_exists($oldThumbPath) && is_file($oldThumbPath)) {
                        @unlink($oldThumbPath);
                    }
                }
                $newThumbName = 'thumb_' . date('YmdHis') . '_' . uniqid() . '.' . $thumbFile->getClientOriginalExtension();
                $thumbFile->move(public_path('uploads/products/'), $newThumbName);
                $product->thumbnail = $newThumbName;
            }
        } elseif ($request->has('remove_thumbnail') && $request->remove_thumbnail == '1') {
            if (!empty($currentThumbnail) && !str_starts_with($currentThumbnail, 'http')) {
                $oldThumbPath = public_path('uploads/products/' . $currentThumbnail);
                if (file_exists($oldThumbPath) && is_file($oldThumbPath)) {
                    @unlink($oldThumbPath);
                }
            }
            $product->thumbnail = null;
        } elseif ($request->filled('old_thumbnail')) {
            $product->thumbnail = $request->old_thumbnail;
        }

        // 2. Handle Retained Existing Gallery Images
        $existingGalleryImages = [];
        $oldGalleryInput = $request->old_gallery_images ?? $request->old_images;
        if ($oldGalleryInput) {
            if (is_array($oldGalleryInput)) {
                $existingGalleryImages = array_values(array_filter($oldGalleryInput));
            } elseif (is_string($oldGalleryInput)) {
                $decoded = json_decode($oldGalleryInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $existingGalleryImages = array_values(array_filter($decoded));
                } else {
                    $existingGalleryImages = array_filter([$oldGalleryInput]);
                }
            }
        }

        // Clean up removed old local gallery images
        $originalRawImages = $product->raw_images;
        foreach ($originalRawImages as $rawImg) {
            if (!in_array($rawImg, $existingGalleryImages) && $rawImg !== $product->thumbnail && !str_starts_with($rawImg, 'http')) {
                $filePath = public_path('uploads/products/' . $rawImg);
                if (file_exists($filePath) && is_file($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        // 3. Handle Newly Uploaded Gallery Images
        $newUploadedImages = [];
        $galleryInput = $request->file('gallery_images') ?? $request->file('images');
        if ($galleryInput) {
            $files = is_array($galleryInput) ? $galleryInput : [$galleryInput];
            foreach ($files as $index => $file) {
                if ($file && $file->isValid()) {
                    $imgName = 'gallery_' . date('YmdHis') . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products/'), $imgName);
                    $newUploadedImages[] = $imgName;
                }
            }
        }

        $finalGalleryImages = array_merge($existingGalleryImages, $newUploadedImages);

        // Fallback: If thumbnail is still null, but gallery images exist, set 1st gallery photo as thumbnail
        if (empty($product->thumbnail) && !empty($finalGalleryImages)) {
            $product->thumbnail = $finalGalleryImages[0];
        }

        if (!empty($finalGalleryImages)) {
            $product->images = json_encode(array_values($finalGalleryImages));
        } else {
            $product->images = null;
        }
        
        $product->save();
        $product->load('category', 'brand');
        logActivity('Update Product', "Product '{$product->name}' updated", $product);

        session()->flash('success', __('Product update successfully!'));
        return Redirect()->route('product.index');
    }

    public function destroy(string $id)
    {
        $product = Product::findorfail($id);

        if ($product->hasTransactions()) {
            session()->flash('error', __('Cannot delete product because it has purchase or sales transaction history.'));
            return back();
        }
        
        // Capture info BEFORE delete
        $product->load('category', 'brand');
        logActivity('Delete Product', "Product '{$product->name}' deleted", $product);

        // Delete thumbnail file if exists
        if (!empty($product->thumbnail) && !str_starts_with($product->thumbnail, 'http')) {
            $thumbPath = public_path('uploads/products/' . $product->thumbnail);
            if (file_exists($thumbPath) && is_file($thumbPath)) {
                @unlink($thumbPath);
            }
        }

        // Delete gallery images
        $rawImages = $product->raw_images;
        foreach ($rawImages as $rawImg) {
            if (!empty($rawImg) && !str_starts_with($rawImg, 'http') && $rawImg !== $product->thumbnail) {
                $filePath = public_path('uploads/products/' . $rawImg);
                if (file_exists($filePath) && is_file($filePath)) {
                    @unlink($filePath);
                }
            }
        }
        $product->delete();
        session()->flash('success', __('Successfully Product Delete!'));
        return back();
    }

    public function multipleBarcode()
    {
        return view('backend.pages.product.multiple-barcode');
    }

    public function print(Request $request)
    {
        $productIds = $request->products;   // product id array
        $variationIds = $request->variation; // variation id array
        $qtys = $request->qty;              // qty array

        $settings = [
            'b_company' => $request->has('b_company'),
            'b_category' => $request->has('b_category'),
            'b_name' => $request->has('b_name'),
            'b_variation' => $request->has('b_variation'),
            'b_price' => $request->has('b_price'),
        ];

        $printItems = [];

        foreach ($productIds as $index => $productId) {
            $product = Product::find($productId);
            $variationId = $variationIds[$index] ?? null;
            $qty = (int)($qtys[$index] ?? 1);
            $company = get_setting('com_name');
            $currency = get_setting('com_currency');
            // Variation info load
            $variation = null;
            if ($variationId) {
                $variation = \App\Models\ProductVariation::find($variationId);
            }

            $printItems[] = [
                'company'   => $company,
                'currency'   => $currency,
                'product'   => $product,
                'variation' => $variation,
                'qty'       => $qty,
            ];
        }
        return view('backend.pages.product.print-barcode', compact('printItems', 'settings'));
    }


    public function search(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['category_id'] = $request->category_id;

        if ($request->product_id != null) {
            $data['products'] = Product::with(['brand', 'category', 'unit.related_unit'])
                ->where('id', $request->product_id)
                ->paginate(10);
        }
        if ($request->category_id != null) {
            $data['products'] = Product::with(['brand', 'category', 'unit.related_unit'])
                ->where('category_id', $request->category_id)
                ->paginate(10);
        }
        return view('backend.pages.product.index', $data);
    }

    public function updateDescription(Request $request, $id)
    {
        $request->validate([
            'description' => 'nullable|string'
        ]);

        $product = Product::findOrFail($id);
        $product->description = $request->description;
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Product description updated successfully'),
            'description' => $product->description
        ]);
    }

    /**
     * Toggle a specific Homepage Section on/off for a single product.
     */
    public function toggleHomepageSection(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $sectionKey = $request->section;

        $section = HomepageSection::where('key', $sectionKey)->orWhere('id', $sectionKey)->first();

        if (!$section) {
            return response()->json(['success' => false, 'message' => __('Invalid section selected')], 400);
        }

        $isAttached = DB::table('homepage_section_products')
            ->where('homepage_section_id', $section->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($isAttached) {
            DB::table('homepage_section_products')
                ->where('homepage_section_id', $section->id)
                ->where('product_id', $product->id)
                ->delete();
            $status = false;
        } else {
            DB::table('homepage_section_products')->insert([
                'homepage_section_id' => $section->id,
                'product_id' => $product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $status = true;
        }

        // Backward compatibility sync for system boolean columns
        if ($section->key === 'flash_deal') {
            $product->is_flash_deal = $status ? 1 : 0;
        } elseif ($section->key === 'popular') {
            $product->is_popular = $status ? 1 : 0;
        } elseif ($section->key === 'hollyland') {
            $product->is_hollyland = $status ? 1 : 0;
        } elseif ($section->key === 'essential') {
            $product->is_essential = $status ? 1 : 0;
        }
        $product->save();

        // Compute updated counts
        $allSections = HomepageSection::orderBy('order', 'asc')->get();
        $counts = [];
        foreach ($allSections as $s) {
            $counts[$s->key] = DB::table('homepage_section_products')->where('homepage_section_id', $s->id)->count();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'section' => $section->key,
                'section_id' => $section->id,
                'section_name' => $section->name,
                'message' => $status ? __("Product added to :name", ['name' => $section->name]) : __("Product removed from :name", ['name' => $section->name]),
                'total_products_count' => Product::where('is_service', 0)->count(),
                'section_counts' => $counts,
                'flash_deal_count' => $counts['flash_deal'] ?? 0,
                'popular_count' => $counts['popular'] ?? 0,
                'hollyland_count' => $counts['hollyland'] ?? 0,
                'essential_count' => $counts['essential'] ?? 0,
            ]);
        }

        session()->flash('success', $status ? "Product added to {$section->name}" : "Product removed from {$section->name}");
        return back();
    }

    /**
     * Bulk assign or remove multiple products to/from a homepage section.
     */
    public function bulkAssignHomepageSection(Request $request)
    {
        $request->validate([
            'section' => 'required|string',
            'action' => 'required|in:add,remove',
        ]);

        $productIds = $request->product_ids ?? [];

        // If user selected all products in store / matching filter
        if ($request->filled('select_all') && $request->select_all == 1) {
            $q = Product::where('is_service', 0);
            if ($request->category_id) $q->where('category_id', $request->category_id);
            if ($request->sub_category_id) $q->where('sub_category_id', $request->sub_category_id);
            if ($request->barcode) {
                $b = $request->barcode;
                $q->where(function($sub) use ($b) {
                    $sub->where('barcode', $b)->orWhere('barcode', 'LIKE', $b . '%');
                });
            }
            $productIds = $q->pluck('id')->toArray();
        }

        if (empty($productIds)) {
            return response()->json(['success' => false, 'message' => __('No products found to update.')], 400);
        }

        if ($request->section === 'all' && $request->action === 'remove') {
            DB::table('homepage_section_products')->whereIn('product_id', $productIds)->delete();
            Product::whereIn('id', $productIds)->update([
                'is_flash_deal' => 0,
                'is_popular' => 0,
                'is_hollyland' => 0,
                'is_essential' => 0,
            ]);
        } else {
            $section = HomepageSection::where('key', $request->section)->orWhere('id', $request->section)->first();
            if (!$section) {
                return response()->json(['success' => false, 'message' => __('Invalid section selected')], 400);
            }

            if ($request->action === 'add') {
                foreach ($productIds as $pId) {
                    $exists = DB::table('homepage_section_products')
                        ->where('homepage_section_id', $section->id)
                        ->where('product_id', $pId)
                        ->exists();
                    if (!$exists) {
                        DB::table('homepage_section_products')->insert([
                            'homepage_section_id' => $section->id,
                            'product_id' => $pId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                if ($section->key === 'flash_deal') Product::whereIn('id', $productIds)->update(['is_flash_deal' => 1]);
                elseif ($section->key === 'popular') Product::whereIn('id', $productIds)->update(['is_popular' => 1]);
                elseif ($section->key === 'hollyland') Product::whereIn('id', $productIds)->update(['is_hollyland' => 1]);
                elseif ($section->key === 'essential') Product::whereIn('id', $productIds)->update(['is_essential' => 1]);
            } else {
                DB::table('homepage_section_products')
                    ->where('homepage_section_id', $section->id)
                    ->whereIn('product_id', $productIds)
                    ->delete();

                if ($section->key === 'flash_deal') Product::whereIn('id', $productIds)->update(['is_flash_deal' => 0]);
                elseif ($section->key === 'popular') Product::whereIn('id', $productIds)->update(['is_popular' => 0]);
                elseif ($section->key === 'hollyland') Product::whereIn('id', $productIds)->update(['is_hollyland' => 0]);
                elseif ($section->key === 'essential') Product::whereIn('id', $productIds)->update(['is_essential' => 0]);
            }
        }

        $allSections = HomepageSection::orderBy('order', 'asc')->get();
        $counts = [];
        foreach ($allSections as $s) {
            $counts[$s->key] = DB::table('homepage_section_products')->where('homepage_section_id', $s->id)->count();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Homepage section updated for selected products successfully'),
                'total_updated' => count($productIds),
                'section_counts' => $counts,
                'flash_deal_count' => $counts['flash_deal'] ?? 0,
                'popular_count' => $counts['popular'] ?? 0,
                'hollyland_count' => $counts['hollyland'] ?? 0,
                'essential_count' => $counts['essential'] ?? 0,
            ]);
        }

        session()->flash('success', __('Homepage section products updated successfully'));
        return back();
    }

    /**
     * Save Flash Deal Offer End Date, Title, and Badge settings dynamically.
     */
    public function saveFlashDealSettings(Request $request)
    {
        $request->validate([
            'flash_deal_end_date' => 'required|string',
            'flash_deal_title' => 'nullable|string|max:255',
            'flash_deal_badge' => 'nullable|string|max:255',
        ]);

        $settings = [
            'flash_deal_end_date' => $request->flash_deal_end_date,
            'flash_deal_title' => $request->flash_deal_title ?: 'Broadcast & Studio Flash',
            'flash_deal_badge' => $request->flash_deal_badge ?: 'Limited Studio Drops',
        ];

        foreach ($settings as $type => $value) {
            $setting = BusinessSetting::where('type', $type)->first();
            if ($setting) {
                $setting->value = $value;
                $setting->save();
            } else {
                BusinessSetting::create(['type' => $type, 'value' => $value]);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('business_settings');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Flash deal timer settings saved successfully'),
                'settings' => $settings,
            ]);
        }

        session()->flash('success', __('Flash deal timer settings saved successfully'));
        return back();
    }

    /**
     * Save Hollyland Audio Showcase section and banner settings dynamically.
     */
    public function saveHollylandSettings(Request $request)
    {
        $request->validate([
            'hollyland_section_title' => 'nullable|string|max:255',
            'hollyland_section_badge' => 'nullable|string|max:255',
            'hollyland_section_tag' => 'nullable|string|max:255',
            'hollyland_section_view_all_link' => 'nullable|string|max:500',
            'hollyland_banner_brand' => 'nullable|string|max:255',
            'hollyland_banner_title' => 'nullable|string|max:255',
            'hollyland_banner_subtitle' => 'nullable|string|max:255',
            'hollyland_banner_features' => 'nullable|string|max:500',
            'hollyland_banner_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'hollyland_banner_image_url' => 'nullable|string|max:1000',
            'hollyland_banner_bottom_text' => 'nullable|string|max:255',
            'hollyland_banner_link' => 'nullable|string|max:500',
        ]);

        $settings = [
            'hollyland_section_title' => $request->hollyland_section_title ?: 'Hollyland Microphone',
            'hollyland_section_badge' => $request->hollyland_section_badge ?: 'FEATURED',
            'hollyland_section_tag' => $request->hollyland_section_tag ?: 'SHOWCASE',
            'hollyland_section_view_all_link' => $request->hollyland_section_view_all_link ?: '',
            'hollyland_banner_brand' => $request->hollyland_banner_brand ?: 'HOLLYLAND',
            'hollyland_banner_title' => $request->hollyland_banner_title ?: 'PURE SOUND. ZERO LIMITS.',
            'hollyland_banner_subtitle' => $request->hollyland_banner_subtitle ?: 'Wireless Audio for Modern Creators',
            'hollyland_banner_features' => $request->hollyland_banner_features ?: 'Wireless • Clear • Reliable • Portable',
            'hollyland_banner_bottom_text' => $request->hollyland_banner_bottom_text ?: 'Capture What Matters',
            'hollyland_banner_link' => $request->hollyland_banner_link ?: '',
        ];

        // Handle Image upload or URL
        if ($request->hasFile('hollyland_banner_image_file')) {
            $image = $request->file('hollyland_banner_image_file');
            $ext = strtolower($image->getClientOriginalExtension());
            $imgName = 'hollyland_banner_' . date('YmdHis') . '_' . uniqid() . '.' . ($ext ?: 'png');
            $destPath = public_path('uploads/settings/');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $image->move($destPath, $imgName);
            $settings['hollyland_banner_image'] = 'uploads/settings/' . $imgName;
        } elseif ($request->filled('hollyland_banner_image_url')) {
            $settings['hollyland_banner_image'] = $request->hollyland_banner_image_url;
        }

        foreach ($settings as $type => $value) {
            $setting = BusinessSetting::where('type', $type)->first();
            if ($setting) {
                $setting->value = $value;
                $setting->save();
            } else {
                BusinessSetting::create(['type' => $type, 'value' => $value]);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('business_settings');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Hollyland showcase settings saved successfully'),
                'settings' => $settings,
            ]);
        }

        session()->flash('success', __('Hollyland showcase settings saved successfully'));
        return back();
    }

    /**
     * Toggle single product show_on_website visibility.
     */
    public function toggleWebsiteVisibility(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->show_on_website = $request->has('show_on_website') ? ($request->show_on_website ? 1 : 0) : ($product->show_on_website ? 0 : 1);
        $product->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'show_on_website' => (int) $product->show_on_website,
                'message' => $product->show_on_website 
                    ? __('Product is now visible on the website.') 
                    : __('Product is now hidden from the website.'),
            ]);
        }

        session()->flash('success', __('Product website visibility updated successfully.'));
        return back();
    }

    /**
     * Bulk toggle products show_on_website visibility.
     */
    public function bulkToggleWebsiteVisibility(Request $request)
    {
        $request->validate([
            'show_on_website' => 'required|in:0,1',
        ]);

        $status = (int) $request->show_on_website;
        $productIds = $request->product_ids ?? [];

        // If user selected all products in store / matching filter
        if ($request->filled('select_all') && $request->select_all == 1) {
            $q = Product::where('is_service', 0);
            if ($request->category_id) $q->where('category_id', $request->category_id);
            if ($request->sub_category_id) $q->where('sub_category_id', $request->sub_category_id);
            if ($request->barcode) {
                $b = $request->barcode;
                $q->where(function($sub) use ($b) {
                    $sub->where('barcode', $b)->orWhere('barcode', 'LIKE', $b . '%');
                });
            }
            $productIds = $q->pluck('id')->toArray();
        }

        if (empty($productIds)) {
            return response()->json(['success' => false, 'message' => __('No products found to update.')], 400);
        }

        Product::whereIn('id', $productIds)->update(['show_on_website' => $status]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $status == 1
                    ? __('Selected products are now visible on the website.')
                    : __('Selected products are now hidden from the website.'),
            ]);
        }

        session()->flash('success', __('Selected products website visibility updated.'));
        return back();
    }
}