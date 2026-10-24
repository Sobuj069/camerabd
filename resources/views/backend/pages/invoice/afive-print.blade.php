@extends('backend.layouts.master')
@section('page-title', 'Invoice Print')
@push('css')
    <style rel="stylesheet">
        hr {
            margin: 0px;
            margin-bottom: 5px;
            margin-top: 5px;
            border: 1px dashed #000;
        }
        .page-footer hr {
            margin: 2px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature p {
            margin-top: -10px;
        }
        address {
            margin-bottom: 0px;
        }
        .invoice-header address {
            text-align: center;
            font-size: 14px;
        }
        .logo_n_name {
            text-align: center;
        }
        .logo_n_name img {
            max-width: 50%;
        }
        .logo_n_name {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .logo_n_name img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }
        .logo_n_name h2 {
            text-align: center;
            margin-top: 5px;
        }
        .footer-note {
            margin-top: 20px;
        }

        /* ===== টেবিলের প্যাডিং কমানো ===== */
        .order-details th,
        .order-details td {
            padding: 4px 6px !important;
            font-size: 13px;
        }

        /* ===== সারাংশ অংশ ডান দিকে সারিবদ্ধ ===== */
        .summary-section {
            margin-top: 10px;
            padding: 0 5px;
            text-align: right;
            font-size: 14px;
        }
        .summary-section p {
            margin: 2px 0;
            border: none !important;
        }
        .summary-section strong {
            font-weight: 700;
            margin-right: 10px;
        }

        @media print {
            @page {
                size: A5;
                margin: 10mm;
            }
            html, body {
                width: 148mm;
                height: 210mm;
                font-size: 12px;
            }
            .print_hidden {
                display: none !important;
            }
            .card.print {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
            }
            .invoice-contentbar {
                margin: 0 !important;
                padding: 0 !important;
            }
            .order-details th,
            .order-details td {
                padding: 3px 4px !important;
                font-size: 11px;
            }
            .order-details th,
            .order-details td,
            .order-details .product-total-row td {
                border: 1px solid #000 !important;
            }
            .summary-section p {
                border: none !important;
                font-size: 12px;
            }
            #print-area > h2 {
                font-size: 28px !important;
                font-weight: 700 !important;
                text-align: center !important;
                color: #000 !important;
                margin: 0 0 5px 0 !important;
                display: block !important;
            }
        }
    </style>
@endpush

@php
    // ========== Helper function for formatting quantity ==========
    function format_qty($value) {
        if (!is_numeric($value)) return $value;
        if (floor($value) == $value) {
            return (int)$value;
        }
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
@endphp

@section('invoice')
    <div class="invoice-contentbar">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-7 card card-body print">
                        <div id="print-area">
                            <div class="invoice-header row">
                                @if (!empty($invoice->shop_id) && $invoice->shop)
                                    <address class="col-12">
                                        <div class="logo_n_name" style="text-align:center;">
                                            @php
                                                $sysLogo = !empty($invoice->shop->logo) ? asset('uploads/shop/' . $invoice->shop->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg'));
                                            @endphp
                                            @if ($invoice->shop->logo)
                                                <img src="{{ $sysLogo }}" style="max-height:55px; width:auto; display:block; margin:0 auto 5px auto;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0; text-align:center; font-size: 20px;">{{ $invoice->shop->name }}</h2>
                                            @else
                                                <h2 style="font-weight: bold; margin-bottom:0; text-align:center; font-size: 22px;">{{ $invoice->shop->name }}</h2>
                                            @endif
                                        </div>
                                        @if($invoice->shop->address) {{ $invoice->shop->address }}<br> @endif
                                        @if($invoice->shop->phone) Phone : {{ $invoice->shop->phone }} @endif
                                        @if($invoice->shop->email) Email : {{ $invoice->shop->email }} @endif
                                        <br>
                                    </address>
                                @elseif (get_setting('inv_details') == 'branch')
                                    @php
                                        $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                    @endphp
                                    <address class="col-12">
                                        <div class="logo_n_name" style="text-align:center;">
                                            @if (get_setting('inv_logo') == 'logo')
                                                <img src="{{ asset('uploads/logo/' . $branch->logo) }}" style="width:150px; height:50px; display:block; margin:0 auto;" alt="logo">
                                            @elseif (get_setting('inv_logo') == 'name')
                                                <h2 style="font-weight: bold; margin-bottom:0; text-align:center;">{{ $branch->shop_name }}</h2>
                                            @else
                                                <img src="{{ asset('uploads/logo/' . $branch->logo) }}" style="width:150px; height:50px; display:block; margin:0 auto;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0; text-align:center;">{{ $branch->shop_name }}</h2>
                                            @endif
                                        </div>
                                        {{ $branch->address }}<br>
                                        Phone : {{ $branch->phone }} Email : {{ $branch->email }}<br>
                                    </address>
                                @else
                                    <address class="col-12">
                                        <div class="logo_n_name">
                                            @if (get_setting('inv_logo') == 'logo')
                                                <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}" style="width:150px; height:50px;" alt="logo">
                                            @elseif (get_setting('inv_logo') == 'name')
                                                <h2 style="font-weight: bold; margin-bottom:0;">{{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                            @else
                                                <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}" style="width:150px; height:50px;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0;">{{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                            @endif
                                        </div>
                                        {{ empty(get_setting('com_address')) ? 'Suite: 807,Shah Ali Plaza, Mirpur-10, Dhaka-1216.' : get_setting('com_address') }}<br>
                                        Phone : {{ empty(get_setting('com_phone')) ? '01784-159071' : get_setting('com_phone') }} Email : {{ empty(get_setting('com_email')) ? 'fastitbd00@gamil.com' : get_setting('com_email') }}<br>
                                    </address>
                                @endif
                            </div>

                            <h2 style="font-weight: bold; margin-bottom:0;text-align:center;font-size:28px;">Invoice</h2>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin: 5px 0; font-size: 14px; font-weight: 500;">
                                <span><strong>Invoice ID:</strong> {{ $invoice->invoice_no }}</span>
                                <span><strong>Date & Time:</strong> {{ $invoice->created_at->format('d/m/Y h:i A') }}</span>
                            </div>

                            <!-- প্রোডাক্ট টেবিল (শুধু প্রোডাক্ট ও তার মোট পর্যন্ত) -->
                            <table class="table table-bordered order-details text-black">
                                <tr>
                                    <th>SL</th>
                                    <th>Product</th>
                                    <th>MRP</th>
                                    <th>Qty</th>
                                    <th>Total Value</th>
                                    <th>Total Disc</th>
                                    <th>Net Pay</th>
                                </tr>
                                @php
                                    $total_qty = 0;
                                    $total_value = 0;
                                    $total_discount = 0;
                                    $total_net_pay = 0;
                                @endphp
                                @foreach ($invoice->invoiceItems as $key => $item)
                                    @php
                                        $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                        if ($product->is_service == 0) {
                                            if ($product->unit->related_unit == null) {
                                                $qty = format_qty($item->main_qty) . ' ' . $product->unit->name;
                                            } else {
                                                $sub_qty = $item->sub_qty ?? 0;
                                                $qty = format_qty($item->main_qty) . ' ' . $product->unit->name . ' ' . format_qty($sub_qty) . ' ' . $product->unit->related_unit->name;
                                            }
                                        } else {
                                            $qty = format_qty($item->main_qty) . ' pcs';
                                        }
                                        $total_qty += (float)$item->main_qty;
                                        $total_value += (float)($item->main_qty * $item->rate);
                                        if (is_numeric($item->product_discount)) {
                                            $total_discount += (float)$item->product_discount;
                                        }
                                        $total_net_pay += (float)$item->subtotal;
                                    @endphp
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td style="width:30%;">
                                            {{ $item->product?->name }}
                                            @if($item->is_return == 1) <span class="badge bg-danger">Return</span> @endif
                                            @if(env('APP_SC')=='yes' && $item->product_variation_id != null)
                                                ({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})
                                            @endif
                                            @if($item->warranty_value)
                                                <br><small><strong>Warranty:</strong> {{ $item->warranty_value }} {{ $item->warranty_unit }}{{ $item->warranty_value > 1 ? 's' : '' }}</small>
                                            @endif
                                            @if($item->product_discount > 0)
                                                <br><small><strong>Discount:</strong> 
                                                @if(str_contains($item->product_discount, '%')) {{ $item->product_discount }} @else {{ number_format((float)$item->product_discount, 2) }} {{ empty(get_setting('com_currency')) ? '' : get_setting('com_currency') }} @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $item->rate }}</td>
                                        <td><small>{{ $qty }}</small></td>
                                        <td>{{ number_format($item->main_qty * $item->rate, 2) }}</td>
                                        <td>{{ $item->product_discount }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <!-- প্রোডাক্টের মোট (বর্ডার থাকবে) -->
                                <tr class="product-total-row" style="font-weight:700; background:#f2f2f2;">
                                    <td colspan="2" class="text-right">Total :</td>
                                    <td></td>
                                    <td>{{ format_qty($total_qty) }}</td>
                                    <td>{{ number_format($total_value, 2) }}</td>
                                    <td>{{ number_format($total_discount, 2) }}</td>
                                    <td>{{ number_format($total_net_pay, 2) }}</td>
                                </tr>
                            </table>

                            <!-- সারাংশ অংশ (টেবিলের বাইরে, ডান দিকে সারিবদ্ধ) -->
                            <div class="summary-section">
                                @php
                                    $cash = App\Models\BankTransaction::where('invoice_id', $invoice->id)->get();
                                @endphp
                                <p><strong>Total Value :</strong> {{ $invoice->estimated_amount }}</p>
                                @foreach($cash as $bank)
                                    <p><strong>{{ $bank->bank_account?->bank_name ?? 'Cash' }} ({{ $bank->date ?: date('Y-m-d', strtotime($invoice->created_at)) }}) :</strong> {{ $bank->trans_type == 'withdraw' ? '-' : '' }}{{ number_format($bank->amount, 2) }}</p>
                                @endforeach
                                @php
                                    $displayPreviousDue = (float) $invoice->previous_due;
                                    if ($displayPreviousDue <= 0 && $invoice->customer_id && $invoice->customer_id != 1) {
                                        $priorInvDue = (float) \App\Models\Invoice::where('customer_id', $invoice->customer_id)
                                            ->where('id', '<', $invoice->id)
                                            ->sum('total_due');
                                        $custObj = \App\Models\Customer::find($invoice->customer_id);
                                        $openBal = $custObj ? open_balance_customer($invoice->customer_id, $custObj->due_amount) : 0;
                                        $displayPreviousDue = $priorInvDue + $openBal;
                                    }
                                @endphp
                                <p><strong>Due :</strong> {{ number_format($invoice->total_due, 2) }}</p>
                                <p><strong>Previous Due :</strong> {{ number_format($displayPreviousDue, 2) }}</p>
                                <p><strong>Total Due :</strong> {{ number_format($invoice->total_due + $displayPreviousDue, 2) }}</p>
                            </div>

                            @if($invoice->note)
                                <div style="margin-top: 10px; font-size: 13px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                    <strong>Note:</strong> {{ $invoice->note }}
                                </div>
                            @endif

                            <!-- ফুটার নোট -->
                            <div class="footer-note mt-3">
                                <p class="note">
                                    <ol style="list-style-type: decimal !important; padding-left: 15px !important; margin:0;">
                                        <li>দয়া করে মূল্য পরিশোধ করে পণ্য বুঝে নিন।</li>
                                        <li>কোন প্রোডাক্ট রিটার্ন করলে কোন ধরনের অফার প্রযোজ্য হবে না।</li>
                                        <li>আমাদের সিল যুক্ত পণ্যের কোন সমস্যা থাকলে আমরা সমাধান করে দিবো।</li>
                                        <li>ডেলিভারি সম্পর্কিত কোনো অভিযোগ বা পরামর্শ থাকলে এই নম্বর এ (01329655700) এ যোগাযোগ করুন।</li>
                                    </ol>
                                </p>
                                <h6 class="mt-5 text-center">Thank you for Shopping at {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h6>
                                <h6 class="text-center">Developed By Fast iT Ltd</h6>
                            </div>
                        </div>
                        <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        localStorage.removeItem('pos-items');
        function print_receipt(divName) {
            let printDoc = $('#' + divName).html();
            let originalContents = $('body').html();
            $("body").html(printDoc);
            window.print();
            $('body').html(originalContents);
        }
        @if(env('APP_AUTO_PRINT') == 'yes')
        $(document).ready(function() {
            print_receipt('print-area');
            window.location.href = "{{ route('invoice.create') }}";
        });
        @endif
    </script>
@endpush