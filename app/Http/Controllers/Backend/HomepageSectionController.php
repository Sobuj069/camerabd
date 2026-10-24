<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomepageSectionController extends Controller
{
    /**
     * Store a newly created homepage section.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'badge' => 'nullable|string|max:100',
            'tag' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'accent_color' => 'nullable|string|max:50',
            'style_theme' => 'nullable|string|in:grid,flash_deal,showcase',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ]);

        $baseKey = Str::slug($request->name, '_');
        if (empty($baseKey)) {
            $baseKey = 'section_' . time();
        }

        $key = $baseKey;
        $counter = 1;
        while (HomepageSection::where('key', $key)->exists()) {
            $key = $baseKey . '_' . $counter;
            $counter++;
        }

        $maxOrder = HomepageSection::max('order') ?? 0;

        $section = HomepageSection::create([
            'key' => $key,
            'name' => $request->name,
            'badge' => $request->badge ?: 'Special',
            'tag' => $request->tag ?: 'FEATURED',
            'subtitle' => $request->subtitle ?: '',
            'icon' => $request->icon ?: '✨',
            'accent_color' => $request->accent_color ?: '#006241',
            'style_theme' => $request->style_theme ?: 'grid',
            'order' => $request->filled('order') ? (int)$request->order : ($maxOrder + 1),
            'status' => $request->has('status') ? (int)$request->status : 1,
            'is_system' => 0,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Homepage section ":name" created successfully!', ['name' => $section->name]),
                'section' => $section,
            ]);
        }

        session()->flash('success', __('Homepage section created successfully!'));
        return back();
    }

    /**
     * Update the specified homepage section.
     */
    public function update(Request $request, $id)
    {
        $section = HomepageSection::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'badge' => 'nullable|string|max:100',
            'tag' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'accent_color' => 'nullable|string|max:50',
            'style_theme' => 'nullable|string|in:grid,flash_deal,showcase',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ]);

        $section->name = $request->name;
        $section->badge = $request->badge;
        $section->tag = $request->tag;
        $section->subtitle = $request->subtitle;
        if ($request->filled('icon')) $section->icon = $request->icon;
        if ($request->filled('accent_color')) $section->accent_color = $request->accent_color;
        if ($request->filled('style_theme')) $section->style_theme = $request->style_theme;
        if ($request->filled('order')) $section->order = (int)$request->order;
        if ($request->has('status')) $section->status = (int)$request->status;

        $section->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Homepage section ":name" updated successfully!', ['name' => $section->name]),
                'section' => $section,
            ]);
        }

        session()->flash('success', __('Homepage section updated successfully!'));
        return back();
    }

    /**
     * Toggle active status of a section.
     */
    public function toggleStatus(Request $request, $id)
    {
        $section = HomepageSection::findOrFail($id);
        $section->status = $section->status ? 0 : 1;
        $section->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => (int)$section->status,
                'message' => $section->status 
                    ? __('Section ":name" is now ACTIVE on website.', ['name' => $section->name])
                    : __('Section ":name" is now INACTIVE/HIDDEN from website.', ['name' => $section->name]),
            ]);
        }

        session()->flash('success', __('Section status updated successfully!'));
        return back();
    }

    /**
     * Remove custom homepage section.
     */
    public function destroy(Request $request, $id)
    {
        $section = HomepageSection::findOrFail($id);

        if ($section->is_system) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Default system sections cannot be deleted, but you can change their title or deactivate them.'),
                ], 422);
            }
            session()->flash('error', __('Default system sections cannot be deleted.'));
            return back();
        }

        $name = $section->name;
        $section->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Section ":name" deleted successfully!', ['name' => $name]),
            ]);
        }

        session()->flash('success', __('Section deleted successfully!'));
        return back();
    }

    /**
     * Reorder sections.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($request->order as $index => $id) {
            HomepageSection::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Section order updated successfully!'),
        ]);
    }
}
