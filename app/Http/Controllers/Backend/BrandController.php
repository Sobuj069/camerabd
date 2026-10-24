<?php

namespace App\Http\Controllers\Backend;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\BranchBrand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    public function index()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if (session()->has('branch_filter_id')) {
                $brandIds = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $brands = Brand::withCount('products')->whereIn('id', $brandIds)->orderBy('id', 'desc')->paginate(20);
            } else {
                $brands = Brand::withCount('products')->orderBy('id', 'desc')->paginate(20);
            }
        } else {
            $brandIds = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');
            $brands = Brand::withCount('products')->whereIn('id', $brandIds)->orderBy('id', 'desc')->paginate(20);
        }
        $brandIds = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');

        $branchs = Branch::all();
        return view('backend.pages.brand.index', compact('brands', 'branchs', 'filterBranchId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'branch_id' => auth()->user()->branch_id == 1 ? 'required|array' : 'nullable',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'brand_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/brands');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image->move($path, $imageName);
            $brand->image = $imageName;
        }

        DB::transaction(function () use ($request, $brand) {
            if ($brand->save()) {
                if (auth()->user()->branch_id == 1 && $request->branch_id) {
                    foreach ($request->branch_id as $key => $branch_id) {
                        $branch_brand = new BranchBrand();
                        $branch_brand->brand_id = $brand->id;
                        $branch_brand->branch_id = $branch_id;
                        $branch_brand->save();
                    }
                } else {
                    $branch_brand = new BranchBrand();
                    $branch_brand->brand_id = $brand->id;
                    $branch_brand->branch_id = auth()->user()->branch_id;
                    $branch_brand->save();
                }
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Brand created successfully'),
                'brand' => $brand,
            ]);
        }

        session()->flash('success', __('Brand created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        if ($request->hasFile('image')) {
            if (!empty($brand->image) && File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
            $image = $request->file('image');
            $imageName = 'brand_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/brands');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image->move($path, $imageName);
            $brand->image = $imageName;
        }

        DB::transaction(function () use ($request, $brand) {
            if ($brand->save()) {
                if ($request->branch_id != null) {
                    $existingBranchIds = BranchBrand::where('brand_id', $brand->id)
                        ->pluck('branch_id')
                        ->toArray();

                    foreach ($request->branch_id as $branch_id) {
                        if (!in_array($branch_id, $existingBranchIds)) {
                            BranchBrand::create([
                                'brand_id' => $brand->id,
                                'branch_id' => $branch_id,
                            ]);
                        }
                    }
                    BranchBrand::where('brand_id', $brand->id)
                        ->whereNotIn('branch_id', $request->branch_id)
                        ->delete();
                }
            }
        });

        $brand->save();
        session()->flash('success', __('Brand updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);

        // Security Guard: Prevent deletion if products exist under this brand
        $productCount = Product::where('brand_id', $id)->count();
        if ($productCount > 0) {
            session()->flash('error', __("Cannot delete brand ':name'! There are :count products assigned to this brand. Please reassign or delete the products first to prevent data loss.", [
                'name' => $brand->name,
                'count' => $productCount,
            ]));
            return back();
        }

        if (!empty($brand->image) && File::exists(public_path('uploads/brands/' . $brand->image))) {
            File::delete(public_path('uploads/brands/' . $brand->image));
        }

        BranchBrand::where('brand_id', $brand->id)->delete();
        $brand->delete();
        session()->flash('success', __('Brand deleted successfully'));
        return back();
    }
}
