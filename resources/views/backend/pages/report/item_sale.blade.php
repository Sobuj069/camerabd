@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Item Sale Report'))

@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            #h-hide {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body card_style mb-2" style="margin-top: -5px;" id="h-hide">
                <form action="{{ route('report.item-sale') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Category') }}</label>
                            <select class="select2" name="category_id">
                                <option selected value="">{{ __('Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (env('APP_SUB_CATEGORY') == 'yes')
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Sub Category') }}</label>
                                <select class="select2" name="sub_category_id">
                                    <option selected value="">{{ __('Select Sub Category') }}</option>
                                    @foreach ($subCategories ?? [] as $subCat)
                                        <option value="{{ $subCat->id }}" {{ request('sub_category_id') == $subCat->id ? 'selected' : '' }}>
                                            {{ $subCat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Product') }}</label>
                            <select class="select2" name="product_id">
                                <option selected value="">{{ __('Select Product') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        @isset($oneProduct) @if ($product->id == $oneProduct->id) selected @endif @endisset>
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" style="height: 38px !important;"
                                value="{{ isset($sdate) ? date('Y-m-d', strtotime($sdate)) : '' }}">
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control" style="height: 38px !important;"
                                value="{{ isset($edate) ? date('Y-m-d', strtotime($edate)) : '' }}">
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.item-sale') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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
            <div class="card m-b-30 print_area card_style">
                <div class="card-header">
                    <h4 style="text-align: center; font-weight:bold; margin-top:25px;font-size:30px">{{ __('Sale Report') }}
                        ({{ isset($sdate) ? date('m-d-Y', strtotime($sdate)) : '' }} -
                        {{ isset($edate) ? date('m-d-Y', strtotime($edate)) : '' }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (isset($invoiceItem))
                            <table class="table">
                                <thead>
                                    <tr class="header_bg">
                                        <th class="header_style_left">{{ __('SL.') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Invoice No') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        @if (env('APP_SUB_CATEGORY') == 'yes')
                                            <th>{{ __('Sub Category') }}</th>
                                        @endif
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Unit Price') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th class="header_style_right">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_discount = 0;
                                        $total_amount = 0;
                                        $total_qty = 0;
                                    @endphp
                                    @forelse($invoiceItem as $key => $item)
                                        @php
                                        $product = App\Models\Product::where('id', $item->product_id)
                                        ->with('unit.related_unit')
                                        ->first();
                                        if ($product->is_service == 0) {
                                            if ($product->unit->related_unit == null) {
                                                // Sub unit নাই → normal show
                                                $qty = $item->main_qty . ' ' . $product->unit->name;
                                            } else {
                                                // Sub unit আছে → সব convert হবে main unit এ
                                                $sub_qty = $item->sub_qty ?? 0;
                                                $related_value = $product->unit->related_value; // ১ main unit = কত sub unit
                                                // main_qty + sub_qty convert to main unit
                                                $total_main = $item->main_qty + $sub_qty / $related_value;
                                                // চূড়ান্ত show → শুধু main unit এ
                                                $qty = $total_main . ' ' . $product->unit->name;
                                            }
                                        } else {
                                            $qty = $item->main_qty . ' pcs';
                                        }
                                            $total_discount += $item->product_discount;
                                            $total_amount += $item->subtotal;
                                            $total_qty += $item->main_qty;
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ date('m-d-Y', strtotime($item->invoice->date)) }}</td>
                                            <td>{{ $item->invoice->invoice_no }}</td>
                                            <td>{{ $item->product?->category?->name }}</td>
                                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                                <td>{{ $item->product?->subCategory?->name ?? '-' }}</td>
                                            @endif
                                            <td>
                                                {{ $item->product->name }}
                                                @if (env('APP_IMEI') == 'yes' && !empty($item->imei))
                                                    <br>
                                                    IMEI: {{ str_replace(',', ', ', $item->imei) }}
                                                @endif
                                            </td>
                                            <td>{{ $qty }}</td>
                                            <td>{{ $item->rate }}</td>
                                            <td>{{ number_format((float) $item->product_discount, 2) }}</td>
                                            <td>{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-danger no_data_style">{{ __('No Data Found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfooter>
                                    <tr class="header_bg" style="font-size: 20px; font-width: 700; font-family:sans-serif">
                                        <td class="header_style_left" colspan="3"></td>
                                        <td colspan="{{ env('APP_SUB_CATEGORY') == 'yes' ? 3 : 2 }}"> <strong class="text-white"> {{ __('Total:') }} </strong></td>
                                        <td class="text-white" colspan="1">{{ $total_qty }} pcs</td>
                                        <td class="text-white" colspan="1"></td>
                                        <td class="text-white" colspan="1">{{ number_format($total_discount, 2) }}    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="header_style_right text-white text-left" colspan="1"><strong>
                                                {{ number_format($total_amount, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </strong></td>
                                    </tr>
                                </tfooter>
                            </table>
                        @else
                            <div class="col-md-12" style="padding-bottom: 30px;">
                                <div class="alert alert-danger text-right" role="alert"> {{ __('Please Select Start and End Month') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
