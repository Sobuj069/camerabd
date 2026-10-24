@extends('backend.layouts.master')
@section('section-title', __('Stock'))
@section('page-title', __('Product Stock'))
@section('action-button')
    <a href="" class="btn add_list_btn " onclick="window.print()">{{ __('Print') }}</a>
@endsection
@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            .h-hide {
                display: none;
            }
        }


        table .table_vari_bg tr {
            background: #000ce2 !important;
            color: rgb(255, 255, 255);
            font-weight: 800;
        }

        th,
        td {
            /* border: 1px solid black; Border for table cells */
            /* padding: 10px; */
            /* text-align: center; */
        }
    </style>

    <style>
        /* Style for the main table rows (striped) */
        .table-striped tbody tr:nth-child(odd) {
            background-color: #dcdcdc;
            /* Light gray for odd rows */
        }

        .table-striped tbody tr:nth-child(even) {
            background-color: #ffffff;
            /* White for even rows */
        }

        /* Style for the nested variation table */
        .table-striped tbody tr:nth-child(odd) table tbody tr {
            background-color: #dcdcdc;
            /* Match odd row color */
        }

        .table-striped tbody tr:nth-child(even) table tbody tr {
            background-color: #ffffff;
            /* Match even row color */
        }

        /* Optional: Add borders to differentiate nested table */
        .table-striped table {
            width: 100%;
            border: 1px solid #ddd;
        }

        .table-striped table th,
        .table-striped table td {
            padding: 5px;
            text-align: left;
        }
    </style>
@endpush
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-header ">
                  
                    @php
                        // $brands = App\Models\Brand::get();
                        // $categories = App\Models\Category::get();
                    @endphp
                    <form action="{{ route('report.stock') }}" method="GET">
                        <div class="form-row align-items-end">
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Search Keyword') }}</label>
                                <input type="text" class="form-control" id="search_keyword" name="search_keyword"
                                    value="{{ $keyword }}" placeholder="{{ __('Barcode or Name') }}" style="height: 38px !important;">
                            </div>
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Product') }}</label>
                                <select class="select2" name="product_id" id="product_id">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($produc as $product)
                                        <option value="{{ $product->id }}" {{ $product_id == $product->id ? 'SELECTED' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Category') }}</label>
                                <select name="category_id" id="category_id" class="select2">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'SELECTED' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="col-md-2 col-12 mb-3">
                                    <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Sub Category') }}</label>
                                    <select name="sub_category_id" id="sub_category_id" class="select2">
                                        <option value="">{{ __('Select Sub Category') }}</option>
                                        @foreach ($subCategories ?? [] as $subCat)
                                            <option value="{{ $subCat->id }}" {{ ($sub_category_id ?? '') == $subCat->id ? 'SELECTED' : '' }}>
                                                {{ $subCat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Brand') }}</label>
                                <select name="brand_id" id="brand_id" class="select2">
                                    <option value="">{{ __('Select Brand') }}</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $brand_id == $brand->id ? 'SELECTED' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('report.stock') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                        <i class="feather icon-refresh-cw mr-1"></i> {{ __('Reset') }}
                                    </a>
                                    <a href="#" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" onclick="window.print()">
                                        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th style="width: 25%">{{ __('Product') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    @if (env('APP_SUB_CATEGORY') == 'yes')
                                        <th>{{ __('Sub Category') }}</th>
                                    @endif
                                    <th>{{ __('Stock In') }}</th>
                                    <th>{{ __('Stock Transfer') }}</th>
                                    <th>{{ __('Stock Receive') }}</th>
                                    <th>{{ __('Stock Out') }}</th>
                                    <th>{{ __('Sale Return') }}</th>
                                    <th>{{ __('Purchase Return') }}</th>
                                    <th>{{ __('Damage') }}</th>
                                    <th>{{ __('Adjust In') }}</th>
                                    <th>{{ __('Adjust Out') }}</th>
                                    <th class="header_style_right">{{ __('Available Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $row_purchase = 0;
                                    $row_sale = 0;
                                    $row_sale_ret = 0;
                                    $row_pur_ret = 0;
                                    $row_damage = 0;
                                    $row_available = 0;
                                    $row_stock_qty = 0;
                                    $row_adjust_in_total = 0;
                                    $row_adjust_out_total = 0;
                                @endphp
                                @forelse($products as $data)
                                    @php
                                        $purchased_qty = purchased_qty($data);
                                        $invoiced_qty = invoiced_qty($data);
                                        $stock_qty = product_stock($data);
                                        $stock_transfer_qty = stock_transfer_qty($data);
                                        $stock_receive_qty = stock_receive_qty($data);
                                        $returned_qty = returned_qty($data);
                                        $damaged_qty = damaged_qty($data);
                                        $adjust_in = adjust_in($data);
                                        $adjust_out = adjust_out($data);
                                        $return_pur_qty = return_pur_qty($data);
                                        // dd($stock_qty);

                                        $row_purchase += (float) $purchased_qty;
                                        $row_sale     += (float) $invoiced_qty;
                                        $row_sale_ret += (float) $returned_qty;
                                        $row_pur_ret  += (float) $return_pur_qty;
                                        $row_damage   += (float) $damaged_qty;
                                        $row_adjust_in_total   += (float) $adjust_in;
                                        $row_adjust_out_total   += (float) $adjust_out;
                                        
                                        $row_stock_qty = $row_purchase - $row_sale + $row_adjust_in_total - $row_adjust_out_total - $row_pur_ret - $row_damage ;
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $products->firstItem() ? ($products->firstItem() + $loop->index) : ($loop->index + 1) }}</td>
                                        <td>{{ $data->name }} - {{ $data->barcode }}</td>
                                        <td>
                                            <span class="badge badge-secondary cursor-pointer btn-view-category" 
                                                  data-category-name="{{ $data->category->name }}"
                                                  data-product-name="{{ $data->name }} - {{ $data->barcode }}"
                                                  style="cursor: pointer; padding: 5px 8px; font-size: 11px;">
                                                {{ $data->category->name }}
                                            </span>
                                        </td>
                                        @if (env('APP_SUB_CATEGORY') == 'yes')
                                            <td>
                                                <span class="badge badge-info" style="padding: 5px 8px; font-size: 11px;">
                                                    {{ $data->subCategory?->name ?? '-' }}
                                                </span>
                                            </td>
                                        @endif
                                        <td>{{ $purchased_qty }}</td>
                                        <td>
                                            {{ $stock_transfer_qty }}
                                            @php $allTransferOutImeis = []; @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $transOutQuery = App\Models\TransferItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $transOutQuery->where('from_branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $transOutQuery->where('from_branch_id', $userBranchId);
                                                    }
                                                    
                                                    $transOutItems = $transOutQuery->pluck('imei')->toArray();
                                                    foreach ($transOutItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allTransferOutImeis = array_merge($allTransferOutImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allTransferOutImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Stock Transfer') }}"
                                                            data-imeis="{{ json_encode(array_values($allTransferOutImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allTransferOutImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $stock_receive_qty }}
                                            @php $allTransferInImeis = []; @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $transInQuery = App\Models\TransferItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $transInQuery->where('to_branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $transInQuery->where('to_branch_id', $userBranchId);
                                                    }
                                                    
                                                    $transInItems = $transInQuery->pluck('imei')->toArray();
                                                    foreach ($transInItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allTransferInImeis = array_merge($allTransferInImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allTransferInImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Stock Receive') }}"
                                                            data-imeis="{{ json_encode(array_values($allTransferInImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allTransferInImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $invoiced_qty }}
                                            @php
                                                $allSoldImeis = [];
                                            @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $invQuery = App\Models\InvoiceItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $invQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $invQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $invoiceItems = $invQuery->pluck('imei')->toArray();
                                                    foreach ($invoiceItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allSoldImeis = array_merge($allSoldImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allSoldImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Stock Out') }}"
                                                            data-imeis="{{ json_encode(array_values($allSoldImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allSoldImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $returned_qty }}
                                            @php
                                                $allSaleReturnImeis = [];
                                            @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $rtnQuery = App\Models\ReturnItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $rtnQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $rtnQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $returnItems = $rtnQuery->pluck('imei')->toArray();
                                                    foreach ($returnItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allSaleReturnImeis = array_merge($allSaleReturnImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allSaleReturnImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Sale Return') }}"
                                                            data-imeis="{{ json_encode(array_values($allSaleReturnImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allSaleReturnImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                {{ $return_pur_qty }}
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $rtnQuery = App\Models\ReturnPurchaseItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $rtnQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $rtnQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $returnPurchaseItems = $rtnQuery->pluck('imei')->toArray();
                                                    $allReturnImeis = [];
                                                    foreach ($returnPurchaseItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allReturnImeis = array_merge($allReturnImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allReturnImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Purchase Return') }}"
                                                            data-imeis="{{ json_encode(array_values($allReturnImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allReturnImeis) }})
                                                    </button>
                                                @endif
                                            @else
                                                {{ $return_pur_qty }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $damaged_qty }}
                                            @php
                                                $allDamagedImeis = [];
                                            @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $dmgQuery = App\Models\DamageItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $dmgQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $dmgQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $damageItems = $dmgQuery->pluck('imei')->toArray();
                                                    foreach ($damageItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allDamagedImeis = array_merge($allDamagedImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allDamagedImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Damage') }}"
                                                            data-imeis="{{ json_encode(array_values($allDamagedImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allDamagedImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $adjust_in }}
                                            @php $allAdjustInImeis = []; @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $adjInQuery = App\Models\AdjustStockItem::where('product_id', $data->id)
                                                        ->where('stock_status', 1)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $adjInQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $adjInQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $adjInItems = $adjInQuery->pluck('imei')->toArray();
                                                    foreach ($adjInItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allAdjustInImeis = array_merge($allAdjustInImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allAdjustInImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Adjust In') }}"
                                                            data-imeis="{{ json_encode(array_values($allAdjustInImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allAdjustInImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $adjust_out }}
                                            @php $allAdjustOutImeis = []; @endphp
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $adjOutQuery = App\Models\AdjustStockItem::where('product_id', $data->id)
                                                        ->where('stock_status', 0)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $adjOutQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $adjOutQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $adjOutItems = $adjOutQuery->pluck('imei')->toArray();
                                                    foreach ($adjOutItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allAdjustOutImeis = array_merge($allAdjustOutImeis, $imeiArray);
                                                    }
                                                @endphp
                                                @if (count($allAdjustOutImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Adjust Out') }}"
                                                            data-imeis="{{ json_encode(array_values($allAdjustOutImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($allAdjustOutImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            {{-- @dd($data->); --}}
 
                                            @php
                                                $available_calc = (float) $purchased_qty - (float) $invoiced_qty - (float) $return_pur_qty - (float) $damaged_qty + (float) $adjust_in - (float) $adjust_out;
                                            @endphp
                                            @if ($data->product_variations->count() > 0)
                                                @php
                                                    $var_total = 0;
                                                    foreach ($data->product_variations as $variation) {
                                                        $var_total += variation_stock($variation->id);
                                                    }
                                                @endphp
                                                {{ $var_total }} {{ __('Pics') }}
                                                <div class="d-print-none">
                                                    <button type="button" class="btn btn-xs btn-primary btn-view-variations mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}"
                                                            data-category="{{ $data->category->name }}"
                                                            data-variations="{{ json_encode($data->product_variations->map(function($v) {
                                                                return [
                                                                    'size' => $v->size?->size ?? 'N/A',
                                                                    'color' => $v->color?->color ?? 'N/A',
                                                                    'stock' => variation_stock($v->id)
                                                                ];
                                                            })) }}"
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #007bff; border-color: #007bff; color: #fff; cursor: pointer;">
                                                        <i class="fa fa-list"></i> {{ __('Variations') }}
                                                    </button>
                                                </div>
                                                <div class="mt-2 text-left d-none d-print-block" style="font-size: 11px; background: #f8f9fa; padding: 5px; border-radius: 4px; border: 1px solid #ddd; min-width: 150px;">
                                                    @foreach ($data->product_variations as $v)
                                                        <div class="d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eee; padding: 2px 0;">
                                                            <span style="margin-right: 10px;">{{ $v->size?->size ?? 'N/A' }} - {{ $v->color?->color ?? 'N/A' }}</span>
                                                            <strong>{{ variation_stock($v->id) }}</strong>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $stock_qty }}
                                            @endif
 
                                            @if ($data->imei == 1 || $data->imei == '1')
                                                @php
                                                    $userBranchId = auth()->user()->branch_id;
                                                    $filterBranchId = session('branch_filter_id');
                                                    $purQuery = App\Models\PurchaseItem::where('product_id', $data->id)
                                                        ->whereNotNull('imei')
                                                        ->where('imei', '!=', '');
                                                    
                                                    if ($userBranchId == 1) {
                                                        if ($filterBranchId) {
                                                            $purQuery->where('branch_id', $filterBranchId);
                                                        }
                                                    } else {
                                                        $purQuery->where('branch_id', $userBranchId);
                                                    }
                                                    
                                                    $purchaseItems = $purQuery->pluck('imei')->toArray();
                                                    $allImeis = [];
                                                    foreach ($purchaseItems as $imeiStr) {
                                                        $imeiArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $imeiStr))));
                                                        $allImeis = array_merge($allImeis, $imeiArray);
                                                    }
 
                                                    // Merge Adjust In IMEIs into all received IMEIs
                                                    $allImeis = array_merge($allImeis, $allAdjustInImeis ?? []);

                                                    $out = array_merge($allSoldImeis ?? [], $allReturnImeis ?? [], $allDamagedImeis ?? [], $allTransferOutImeis ?? [], $allAdjustOutImeis ?? []);
                                                    // Subtract items that came back from customer to make them available again
                                                    $actuallyOut = array_diff($out, $allSaleReturnImeis ?? []);
                                                    $availableImeis = array_diff($allImeis, $actuallyOut);
                                                @endphp
                                                @if (count($availableImeis) > 0)
                                                    <br>
                                                    <button type="button" class="btn btn-xs btn-info btn-view-imeis mt-1" 
                                                            data-product="{{ $data->name }} - {{ $data->barcode }}" 
                                                            data-column="{{ __('Available Stock') }}"
                                                            data-imeis="{{ json_encode(array_values($availableImeis)) }}" 
                                                            style="padding: 1px 5px; font-size: 10px; border-radius: 3px; background-color: #17a2b8; border-color: #17a2b8; color: #fff;">
                                                        <i class="fa fa-barcode"></i> {{ __('IMEI') }} ({{ count($availableImeis) }})
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="header_bg text-white">
                                    <td colspan="{{ env('APP_SUB_CATEGORY') == 'yes' ? 4 : 3 }}"><strong class="text-white"> {{ __('Page Total') }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_purchase) }}</strong></td>
                                    <td><strong class="text-white">0</strong></td>
                                    <td><strong class="text-white">0</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_sale) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_sale_ret) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_pur_ret) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_damage) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_adjust_in_total) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_adjust_out_total) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($row_stock_qty) }}</strong></td>
                                </tr>
                                <tr class="header_bg text-white">
                                    <td colspan="{{ env('APP_SUB_CATEGORY') == 'yes' ? 4 : 3 }}"><strong class="text-white">{{ __('Total') }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_purchase) }}</strong></td>
                                    <td><strong class="text-white">0</strong></td>
                                    <td><strong class="text-white">0</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_sale) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_sale_ret) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_pur_ret) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_damage) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_adjust_in_total) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_adjust_out_total) }}</strong></td>
                                    <td><strong class="text-white">{{ number_format($grand_stock_qty) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="pagination justify-content-center">
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View IMEI Modal -->
    <div class="modal fade" id="viewImeiModal" tabindex="-1" role="dialog" aria-labelledby="viewImeiModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content card_style">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="viewImeiModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label class="font-weight-bold mb-2" id="viewImeiListLabel"></label>
                        <div id="viewImeiList" class="imei-list-container">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; font-weight: 500;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Variations Modal -->
    <div class="modal fade" id="viewVariationsModal" tabindex="-1" role="dialog" aria-labelledby="viewVariationsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content card_style">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="viewVariationsModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <div class="mb-3">
                        <span class="badge badge-info" id="viewVariationsCategory" style="font-size: 12px; padding: 6px 10px;"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Size') }}</th>
                                    <th>{{ __('Color') }}</th>
                                    <th class="text-right">{{ __('Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody id="viewVariationsList">
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="2" class="text-left">{{ __('Total Qty =') }}</td>
                                    <td class="text-right" id="viewVariationsTotal"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; font-weight: 500;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Category Modal -->
    <div class="modal fade" id="viewCategoryModal" tabindex="-1" role="dialog" aria-labelledby="viewCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content card_style">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="viewCategoryModalLabel">{{ __('Category Details') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label class="font-weight-bold mb-1">{{ __('Product:') }}</label>
                        <p id="viewCategoryProduct" class="text-muted mb-3" style="font-size: 14px; font-weight: 500;"></p>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold mb-1">{{ __('Category Name:') }}</label>
                        <h4 class="text-info font-weight-bold mb-0" id="viewCategoryName"></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; font-weight: 500;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // $(doucment).ready(function(){
        $('#resetBtn').click(function() {
            $("#product_id option:first").prop("selected", true).change();
            $("#category_id option:first").prop("selected", true).change();
            $("#brand_id option:first").prop("selected", true).change();
            $("#search_keyword").val('');
        });
        // });

        $(document).on('click', '.btn-view-imeis', function() {
            let title = $(this).data('product');
            let column = $(this).data('column');
            let imeis = $(this).data('imeis') || [];
            
            $('#viewImeiModalLabel').text(title);
            $('#viewImeiListLabel').text(column + ' IMEIs (' + imeis.length + '):');
            
            let html = '<ol class="imei-ol-list" style="padding-left: 20px;">';
            imeis.forEach(function(imei) {
                html += '<li class="mb-1">' + imei + '</li>';
            });
            html += '</ol>';
            if (imeis.length === 0) {
                html = '<div class="text-muted text-center">No IMEIs available</div>';
            }
            $('#viewImeiList').html(html);
            $('#viewImeiModal').modal('show');
        });

        $(document).on('click', '.btn-view-variations', function() {
            let title = $(this).data('product');
            let category = $(this).data('category');
            let variations = $(this).data('variations') || [];
            
            $('#viewVariationsModalLabel').text(title);
            $('#viewVariationsCategory').text('{{ __('Category:') }} ' + category);
            
            let html = '';
            let total = 0;
            variations.forEach(function(v) {
                total += parseFloat(v.stock) || 0;
                html += '<tr>' +
                        '<td>' + v.size + '</td>' +
                        '<td>' + v.color + '</td>' +
                        '<td class="text-right">' + v.stock + ' Pics</td>' +
                        '</tr>';
            });
            $('#viewVariationsList').html(html);
            $('#viewVariationsTotal').text(total + ' Pics');
            $('#viewVariationsModal').modal('show');
        });

        $(document).on('click', '.btn-view-category', function() {
            let categoryName = $(this).data('category-name');
            let productName = $(this).data('product-name');
            
            $('#viewCategoryProduct').text(productName);
            $('#viewCategoryName').text(categoryName);
            $('#viewCategoryModal').modal('show');
        });
    </script>
@endsection
