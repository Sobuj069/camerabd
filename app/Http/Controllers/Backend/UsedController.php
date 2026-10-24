<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UsedProduct;
use App\Models\Used;
use App\Models\UsedItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['damages'] = Used::with('usedItems')->where('status', 1)->get();
        $data['products'] = UsedProduct::orderBy('created_at', 'asc')->get();

        if ($request->product_id != null) {
            $data['damages'] = Used::with(['usedItems' => function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            }])
                ->whereHas('usedItems', function ($query) use ($request) {
                    $query->where('product_id', $request->product_id);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }


        if ($request->start_date != NULL && $request->end_date != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_date)->toDateString();
            $sdate = $data['sdate'];
            $edate = $data['edate'];
            $damages = Used::orderBy('created_at', 'DESC')->whereBetween('date', [$sdate, $edate]);
            $data['damages'] = $damages->get();
        }

        return view('backend.pages.usedProduct.used.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = UsedProduct::orderBy('id', 'DESC')->get();
        return view('backend.pages.usedProduct.used.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date' => 'required',
            'product_id' => 'required',
            'main_qty' => 'required',
        ]);

        $damage = new Used();
        $damage->date = $request->date;
        $damage->created_by = auth()->user()->id;
        $damage->total_amount = $request->payable_amount;
        DB::transaction(function () use ($request, $damage) {
            if ($damage->save()) {
                foreach ($request->product_id as $key => $product) {
                    $damageItem = new UsedItem();
                    $damageItem->date = $request->date;
                    $damageItem->used_id = $damage->id;
                    $damageItem->product_id = $product;
                    $damageItem->main_qty = $request->main_qty[$key];
                    $damageItem->sub_qty = $request->sub_qty[$key];
                    $damageItem->rate = $request->new_rate[$key];
                    $damageItem->subtotal = $request->sub_total[$key];
                    $damageItem->save();
                }
            }
        });

        session()->flash('success', 'Used Created Successfully');
        return redirect()->route('used.index');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $damage = Used::find($id);

        $damageItem = UsedItem::where('used_id', $id)->get();
        foreach ($damageItem as $item) {
            $item->delete();
        }
        $damage->delete();

        session()->flash('success', 'Used Product deleted successfully');
        return back();
    }
    // public function usedStock(Request $request)
    // {
    //     $data['product_id'] = $request->product_id;
    //     $data['keyword'] = $request->search_keyword;
    //     $data['category_id'] = $request->category_id;
    //     $data['brand_id'] = $request->brand_id;
    //     //get all stock with supplier and user
    //     $data['products'] = UsedProduct::with('unit.related_unit')
    //         ->orderBy('created_at', 'DESC')
    //         ->paginate(20);
    //     $data['totalPrice'] = UsedProduct::all()->sum(function ($product) {
    //         // dd(product_stock_balance($product));
    //         return product_stock_balance($product) * $product->purchase_price;
    //     });

    //     if ($request->product_id != null) {
    //         $data['products'] = UsedProduct::with('unit.related_unit')->where('id', $request->product_id)
    //             ->orderBy('created_at', 'DESC')
    //             ->paginate(20)
    //             ->appends([
    //                 'product_id' => $request->product_id,
    //             ]);
    //     }

    //     if ($request->category_id != null) {
    //         $data['products'] = UsedProduct::with('unit.related_unit')->where('category_id', $request->category_id)
    //             ->orderBy('created_at', 'DESC')
    //             ->paginate(20)
    //             ->appends([
    //                 'category_id' => $request->category_id,
    //             ]);
    //     }


    //     if ($request->brand_id != null) {
    //         $data['products'] = UsedProduct::with('unit.related_unit')->where('brand_id', $request->brand_id)
    //             ->orderBy('created_at', 'DESC')
    //             ->paginate(20)
    //             ->appends([
    //                 'brand_id' => $request->brand_id,
    //             ]);
    //     }


    //     if ($request->search_keyword != null) {
    //         $data['products'] = UsedProduct::with('unit.related_unit')->where('name', 'like', '%' . $request->search_keyword . '%')
    //             ->orWhere('barcode', $request->search_keyword)
    //             ->orderBy('created_at', 'DESC')
    //             ->paginate(20)
    //             ->appends([
    //                 'search_keyword' => $request->search_keyword,
    //             ]);
    //     }

    //     dd($data);
    //     return view('backend.pages.usedProduct.used_stock', $data);
    // }
}
