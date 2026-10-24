<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\BranchCategory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $categories = Category::whereIn('id', $categoryIds)->orderBy('is_top', 'desc')->orderBy('top_order', 'asc')->orderBy('id', 'desc')->paginate(20);
            } else {
                $categories = Category::orderBy('is_top', 'desc')->orderBy('top_order', 'asc')->orderBy('id', 'desc')->paginate(20);
            }
        } else {
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $categories = Category::whereIn('id', $categoryIds)->orderBy('is_top', 'desc')->orderBy('top_order', 'asc')->orderBy('id', 'desc')->paginate(20);
        }
        $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');

        $branchs = Branch::all();
        return view('backend.pages.category.index',  compact('categories', 'branchs', 'filterBranchId','categoryIds'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'branch_id' => auth()->user()->branch_id == 1 ? 'required|array' : 'nullable',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->is_top = $request->has('is_top') ? 1 : 0;
        $category->top_order = intval($request->input('top_order', 0));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'cat_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/categories');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image->move($path, $imageName);
            $category->image = $imageName;
        }

        DB::transaction(function () use ($request, $category) {
            if ($category->save()) {
                if(auth()->user()->branch_id == 1 && $request->branch_id){
                    foreach ($request->branch_id as $key => $branch_id) {
                        $branch_category = new BranchCategory();
                        $branch_category->category_id = $category->id;
                        $branch_category->branch_id = $branch_id;
                        $branch_category->save();
                    }
                }else{
                    $branch_category = new BranchCategory();
                    $branch_category->category_id = $category->id;
                    $branch_category->branch_id = auth()->user()->branch_id;
                    $branch_category->save();
                }
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Category created successfully'),
                'category' => $category,
            ]);
        }

        session()->flash('success', __('Category created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'top_order' => 'nullable|integer',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->is_top = $request->has('is_top') ? 1 : 0;
        $category->top_order = intval($request->input('top_order', 0));

        if ($request->hasFile('image')) {
            if (!empty($category->image) && File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
            $image = $request->file('image');
            $imageName = 'cat_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/categories');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $image->move($path, $imageName);
            $category->image = $imageName;
        }

        DB::transaction(function () use ($request, $category) {
            if ($category->save()) {
                if($request->branch_id != null){
                    $existingBranchIds = BranchCategory::where('category_id', $category->id)
                        ->pluck('branch_id')
                        ->toArray();
            
                    foreach ($request->branch_id as $branch_id) {
                        if (!in_array($branch_id, $existingBranchIds)) {
                            BranchCategory::create([
                                'category_id' => $category->id,
                                'branch_id' => $branch_id,
                            ]);
                        }
                    }
                    BranchCategory::where('category_id', $category->id)
                        ->whereNotIn('branch_id', $request->branch_id)
                        ->delete();
                }
            }
        });

        session()->flash('success', __('Category updated successfully'));
        return back();
    }

    /**
     * Quick toggle for Top Category status via AJAX or Form
     */
    public function toggleTop(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->is_top = $category->is_top ? 0 : 1;
        $category->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_top' => $category->is_top,
                'message' => $category->is_top ? __('Marked as Top Category') : __('Removed from Top Categories'),
            ]);
        }

        session()->flash('success', $category->is_top ? __('Marked as Top Category') : __('Removed from Top Categories'));
        return back();
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            // Security Guard: Prevent deletion if products exist under this category
            $productCount = Product::where('category_id', $id)->count();
            if ($productCount > 0) {
                session()->flash('error', __("Cannot delete category ':name'! There are :count products assigned to this category. Please reassign or delete the products first to prevent data loss.", [
                    'name' => $category->name,
                    'count' => $productCount,
                ]));
                return back();
            }

            if (!empty($category->image) && File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }
            $category->delete();
        }
        session()->flash('success', __('Category deleted successfully'));
        return back();
    }
}
