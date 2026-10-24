<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $query = Warranty::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('duration', 'LIKE', "%{$search}%")
                  ->orWhere('period', 'LIKE', "%{$search}%");
            });
        }

        $warranties = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());

        return view('backend.pages.warranty.index', compact('warranties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:warranties,name',
            'duration'    => 'required|integer|min:1',
            'period'      => 'required|in:Day,Month,Year',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:0,1',
        ]);

        $warranty = new Warranty();
        $warranty->name        = $request->name;
        $warranty->duration    = (int) $request->duration;
        $warranty->period      = $request->period;
        $warranty->description = $request->description;
        $warranty->status      = $request->has('status') ? (int) $request->status : 1;
        $warranty->created_by  = Auth::id();
        $warranty->save();

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => __('Warranty created successfully'),
                'warranty' => $warranty,
            ]);
        }

        session()->flash('success', __('Warranty created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:warranties,name,' . $id,
            'duration'    => 'required|integer|min:1',
            'period'      => 'required|in:Day,Month,Year',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:0,1',
        ]);

        $warranty = Warranty::findOrFail($id);
        $warranty->name        = $request->name;
        $warranty->duration    = (int) $request->duration;
        $warranty->period      = $request->period;
        $warranty->description = $request->description;
        $warranty->status      = $request->has('status') ? (int) $request->status : 1;
        $warranty->save();

        // Also sync products attached to this warranty if needed
        Product::where('warranty_id', $warranty->id)->update([
            'warranty_value' => $warranty->duration,
            'warranty_unit'  => $warranty->period,
        ]);

        session()->flash('success', __('Warranty updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $warranty = Warranty::findOrFail($id);

        // Reset products using this warranty
        Product::where('warranty_id', $warranty->id)->update([
            'warranty_id'    => null,
            'has_warranty'   => 0,
            'warranty_value' => null,
            'warranty_unit'  => null,
        ]);

        $warranty->delete();

        session()->flash('success', __('Warranty deleted successfully'));
        return back();
    }
}
