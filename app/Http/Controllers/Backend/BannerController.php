<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index(Request $request)
    {
        $position = $request->get('position');

        $query = Banner::query();
        if (!empty($position)) {
            $query->where('position', $position);
        }

        $banners = $query->orderBy('position', 'asc')->orderBy('order', 'asc')->orderBy('id', 'desc')->paginate(15);
        $totalBanners = Banner::count();
        $mainHeroCount = Banner::where('position', 'main_hero')->count();
        $sideTopCount = Banner::where('position', 'side_top')->count();
        $sideBottomCount = Banner::where('position', 'side_bottom')->count();
        $middleCount = Banner::where('position', 'middle_banner')->count();

        return view('backend.pages.banner.index', compact(
            'banners',
            'position',
            'totalBanners',
            'mainHeroCount',
            'sideTopCount',
            'sideBottomCount',
            'middleCount'
        ));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('backend.pages.banner.create');
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
            'link' => 'nullable|string|max:1000',
            'position' => 'required|in:main_hero,side_top,side_bottom,middle_banner',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->link = $request->link;
        $banner->position = $request->position;
        $banner->order = $request->order ?? 0;
        $banner->status = $request->has('status') ? 1 : 0;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'banner_' . date('YmdHis') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/banners');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $image->move($uploadPath, $imageName);
            $banner->image = $imageName;
        }

        $banner->save();

        session()->flash('success', __('Banner created successfully!'));
        return redirect()->route('banner.index');
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(string $id)
    {
        $banner = Banner::findOrFail($id);
        return view('backend.pages.banner.edit', compact('banner'));
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
            'link' => 'nullable|string|max:1000',
            'position' => 'required|in:main_hero,side_top,side_bottom,middle_banner',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);

        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->link = $request->link;
        $banner->position = $request->position;
        $banner->order = $request->order ?? 0;
        $banner->status = $request->has('status') ? 1 : 0;

        // Handle Image Upload Replacement
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'banner_' . date('YmdHis') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/banners');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            // Remove old image file if local
            if (!empty($banner->image) && !str_starts_with($banner->image, 'http')) {
                $oldFile = public_path('uploads/banners/' . $banner->image);
                if (File::exists($oldFile)) {
                    File::delete($oldFile);
                }
            }

            $image->move($uploadPath, $imageName);
            $banner->image = $imageName;
        }

        $banner->save();

        session()->flash('success', __('Banner updated successfully!'));
        return redirect()->route('banner.index');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);

        // Delete image file if stored locally
        if (!empty($banner->image) && !str_starts_with($banner->image, 'http')) {
            $oldFile = public_path('uploads/banners/' . $banner->image);
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }

        $banner->delete();

        session()->flash('success', __('Banner deleted successfully!'));
        return redirect()->route('banner.index');
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(string $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = !$banner->status;
        $banner->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $banner->status,
                'message' => __('Banner status updated successfully!'),
            ]);
        }

        session()->flash('success', __('Banner status updated successfully!'));
        return back();
    }
}
