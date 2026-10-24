<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branchs = Branch::orderBy('id', 'desc')->get();
        return view('backend.pages.branch.index', compact('branchs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'shop_name' => 'nullable|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:100',
            'highlights' => 'nullable|string|max:500',
            'map_link' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $branch = new Branch();
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->shop_name = $request->shop_name;
        $branch->schedule = $request->schedule;
        $branch->badge = $request->badge ?: 'Open Daily';
        $branch->highlights = $request->highlights;
        $branch->map_link = $request->map_link;
        $branch->icon = $request->icon ?: 'storefront';
        $branch->created_by = auth()->user()->id;
        $image = $request->file('logo');
        $branch->save();
        if ($image) {
            $imgName = date('YmdHi') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/logo/', $imgName);
            $branch->logo = $imgName;
            $branch->save();
        }

        session()->flash('success', __('Branch created successfully'));
        logActivity('Create Branch', "Branch '{$branch->name}' created", $branch);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'shop_name' => 'nullable|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:100',
            'highlights' => 'nullable|string|max:500',
            'map_link' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->shop_name = $request->shop_name;
        $branch->schedule = $request->schedule;
        $branch->badge = $request->badge ?: 'Open Daily';
        $branch->highlights = $request->highlights;
        $branch->map_link = $request->map_link;
        $branch->icon = $request->icon ?: 'storefront';
        $image = $request->file('logo');
        $branch->save();
        if ($image) {
            $imgName = date('YmdHi') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/logo/', $imgName);
            $branch->logo = $imgName;
            $branch->save();
        }

        session()->flash('success', __('Branch updated successfully'));
        logActivity('Update Branch', "Branch '{$branch->name}' updated", $branch);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::find($id);
        logActivity('Delete Branch', "Branch '{$branch->name}' deleted", $branch);
        $branch->delete();
        session()->flash('success', __('Branch deleted successfully'));
        return back();
    }

    // public function switch(Request $request)
    // {
    //     $request->validate([
    //         'branch_id' => 'required|exists:branches,id'
    //     ]);

    //     Session::put('branch_id', $request->branch_id);

    //     return back()->with('success', 'Branch switched successfully!');
    // }

    public function switch(Request $request)
    {
        if (!check_permission('switch.branch')) {
            session()->flash('error', __('You do not have permission to switch branches.'));
            return back();
        }

        if ($request->branch_id) {
            session(['branch_filter_id' => $request->branch_id]);
        } else {
            session()->forget('branch_filter_id');
        }
        return back(); // আগের পেইজে ফেরত যাবে
    }
}
