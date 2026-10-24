@extends('backend.layouts.master')
@section('page-title', 'Invoice Print')
@push('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

        body {
            font-family: 'Roboto', 'Segoe UI', Arial, sans-serif;
            color: #000;
            background: #f1f5f9;
        }

        .invoice-contentbar {
            margin: 30px auto;
            padding: 10px;
        }

        .invoice-wrapper {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            padding: 25px 30px;
            border: 1px solid #d1d5db;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            color: #000;
        }

        /* ── HEADER ── */
        .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            border-bottom: 1.5px solid #d1d5db;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        .header-left-logo {
            flex: 0 0 22%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .header-left-logo img {
            max-height: 120px;
            max-width: auto;
            object-fit: left;
        }

        .header-middle-info {
            flex: 0 0 44%;
            padding: 0 15px;
            border-right: 1px solid #d1d5db;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }

        .header-middle-info .comp-title {
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 3px;
            color: #000;
            letter-spacing: 0.5px;
        }

        .header-right-meta {
            flex: 0 0 34%;
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            font-size: 12px;
            line-height: 1.45;
            color: #000;
            padding-left: 10px;
        }

        .header-right-meta .barcode-container {
            margin-bottom: 4px;
        }

        .header-right-meta .meta-lines {
            text-align: right;
        }

        /* ── TITLE ── */
        .inv-main-title {
            text-align: center;
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 10px 0 16px 0;
            color: #000;
        }

        /* ── CUSTOMER & ADDRESS ── */
        .inv-customer-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
            font-size: 12px;
            line-height: 1.4;
        }

        .cust-left {
            flex: 0 0 50%;
        }

        .cust-left .cust-label {
            font-size: 12px;
            color: #000;
            margin-bottom: 2px;
        }

        .cust-left .cust-name {
            font-size: 15px;
            font-weight: 800;
            color: #000;
        }

        .cust-left .cust-phone {
            font-size: 11px;
            color: #333;
            margin-top: 1px;
        }

        .cust-right {
            flex: 0 0 45%;
            text-align: left;
        }

        .cust-right .addr-label {
            font-size: 13px;
            font-weight: 800;
            color: #000;
            margin-bottom: 2px;
        }

        .cust-right .addr-text {
            font-size: 12px;
            color: #333;
        }

        /* ── SALES TABLE ── */
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .sales-table th {
            background-color: #0088cc !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0088cc !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sales-table td {
            border: 1px solid #94a3b8 !important;
            padding: 6px 8px;
            vertical-align: middle;
            color: #000;
        }

        .sales-table .item-name {
            font-weight: 700;
            color: #000;
        }

        .sales-table .item-sub {
            font-size: 11px;
            color: #222;
            margin-top: 2px;
            font-weight: 500;
        }

        .sales-table .summary-label {
            text-align: right;
            font-weight: 700;
            padding: 5px 8px;
            color: #000;
        }

        .sales-table .summary-val {
            text-align: right;
            font-weight: 700;
            padding: 5px 8px;
            color: #000;
        }

        /* ── WORDS & TERMS ── */
        .inwords-block {
            font-size: 12px;
            margin-top: 6px;
            color: #000;
        }

        .terms-block {
            margin-top: 12px;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
        }

        .terms-block .terms-title {
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .terms-block ul {
            margin: 0;
            padding-left: 14px;
            list-style-type: disc;
            font-size: 10.5px;
            line-height: 1.45;
        }

        /* ── FOOTER SIGNATURES ── */
        .inv-footer-signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding: 0 5px;
        }

        .sig-block {
            text-align: center;
            width: 170px;
        }

        .sig-line {
            border-top: 1.5px solid #000;
            margin-bottom: 5px;
        }

        .sig-label {
            font-size: 13px;
            font-weight: 700;
            color: #000;
        }

        .qr-block {
            text-align: center;
        }

        .qr-block img {
            width: 85px;
            height: 85px;
            display: inline-block;
        }

        .print-btn-bar {
            display: flex;
            justify-content: center;
            margin: 20px auto;
            max-width: 300px;
        }

        @media print {
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .invoice-contentbar {
                margin: 0 !important;
                padding: 0 !important;
            }

            .invoice-wrapper {
                border: none !important;
                padding: 10px 15px !important;
                margin: 0 auto !important;
                max-width: 100% !important;
                box-shadow: none !important;
            }

            .print_hidden, .print-btn-bar {
                display: none !important;
            }

            .sales-table th {
                background-color: #0088cc !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
@endpush

@section('invoice')
    @if(empty($invoice))
        <div class="container mt-5">
            <div class="alert alert-danger text-center">
                <h4>Invoice not found or invalid.</h4>
            </div>
        </div>
    @else
    @php
        /* ── Amount in Words ── */
        $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                 'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                 'Seventeen','Eighteen','Nineteen'];
        $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

        $cvtH = function(int $n) use ($ones, $tens): string {
            $r = '';
            if ($n >= 100) { $r .= $ones[intval($n/100)].' Hundred '; $n %= 100; }
            if ($n >= 20)  { $r .= $tens[intval($n/10)].' '; $n %= 10; }
            if ($n > 0)    { $r .= $ones[$n].' '; }
            return $r;
        };

        $num = (int) $invoice->total_amount;
        $inWords = '';
        if ($num == 0) {
            $inWords = 'Zero';
        } else {
            $t = $num;
            if ($t >= 10000000) { $inWords .= $cvtH(intval($t/10000000)).'Crore '; $t %= 10000000; }
            if ($t >= 100000)   { $inWords .= $cvtH(intval($t/100000)).'Lakh '; $t %= 100000; }
            if ($t >= 1000)     { $inWords .= $cvtH(intval($t/1000)).'Thousand '; $t %= 1000; }
            if ($t > 0)         { $inWords .= $cvtH($t); }
        }
        $inWords = ucwords(strtolower(trim($inWords))).' Taka Only';

        /* ── Branch / Company Info ── */
        $branch = null;
        if (get_setting('inv_details') == 'branch') {
            $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
        }
        $shopName    = $branch ? $branch->shop_name    : (get_setting('com_name')    ?: 'CAMERA BD');
        $shopAddress = $branch ? $branch->address      : (get_setting('com_address') ?: 'Jamuna Future Park, Level # 4, Block # C, Shop # 4C-012B1, Dhaka-1229');
        $shopPhone   = $branch ? $branch->phone        : (get_setting('com_phone')   ?: '01783315319');
        $shopMobile  = $branch ? $branch->mobile       : (get_setting('com_mobile')  ?: '01342225228');
        $shopEmail   = $branch ? $branch->email        : (get_setting('com_email')   ?: 'camerabd.shop@gmail.com');
        $shopWeb     = get_setting('com_web') ?: 'https://www.facebook.com/masudoffbd';
        $shopLogo    = ($branch && !empty($branch->logo)) ? asset('uploads/logo/'.$branch->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/'.get_setting('system_logo')) : url('backend/images/fastLogo.jpeg'));

        $currency = get_setting('com_currency') ?: 'Tk.';

        /* ── Barcode Generation ── */
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeString = $invoice->unique_id ?? $invoice->invoice_no ?? (string)$invoice->id;
        try {
            $barcodeBase64 = base64_encode($generator->getBarcode($barcodeString, $generator::TYPE_CODE_128));
        } catch (\Exception $e) {
            $barcodeBase64 = null;
        }

        /* ── Formatting unique invoice code ── */
        $formattedInvoiceNo = $invoice->unique_id ?: ('SEL-CBD-' . date('dmy', strtotime($invoice->date)) . '-' . str_pad($invoice->invoice_no, 3, '0', STR_PAD_LEFT));
    @endphp

    <div class="invoice-contentbar">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div id="print-area" class="invoice-wrapper">

                    {{-- ══ HEADER (3 Columns) ══ --}}
                    <div class="inv-header">
                        <!-- Left Logo -->
                        <div class="header-left-logo">
                            <img src="{{ $shopLogo }}" alt="{{ $shopName }}">
                        </div>

                        <!-- Middle Company Info -->
                        <div class="header-middle-info">
                            <div class="comp-title">{{ $shopName }}</div>
                            <div>{!! nl2br(e($shopAddress)) !!}</div>
                            <div><strong>Phone:</strong> {{ $shopPhone }}</div>
                            @if(!empty($shopMobile))
                                <div><strong>Mobile:</strong> {{ $shopMobile }}</div>
                            @endif
                            <div><strong>Email:</strong> {{ $shopEmail }}</div>
                            @if(!empty($shopWeb))
                                <div><strong>Web:</strong> {{ $shopWeb }}</div>
                            @endif
                        </div>

                        <!-- Right Barcode & Meta -->
                        <div class="header-right-meta">
                            <div class="barcode-container">
                                @if($barcodeBase64)
                                    <img src="data:image/png;base64,{{ $barcodeBase64 }}" style="height: 38px; max-width: 210px;" alt="Barcode">
                                @endif
                            </div>
                            <div class="meta-lines">
                                <div><strong>Invoice Nº:</strong> {{ $formattedInvoiceNo }}</div>
                                <div><strong>InvSL:</strong> #{{ str_pad($invoice->invoice_no ?? $invoice->id, 3, '0', STR_PAD_LEFT) }}</div>
                                <div><strong>User:</strong> {{ $invoice->user?->name ?? 'Masud Hawlader' }}</div>
                                <div><strong>Date:</strong> {{ date('d M Y', strtotime($invoice->date)) }} @if(!empty($invoice->created_at))<span style="font-size: 11px; color: #475569;">({{ date('h:i A', strtotime($invoice->created_at)) }})</span>@endif</div>
                            </div>
                        </div>
                    </div>

                    {{-- ══ TITLE ══ --}}
                    <div class="inv-main-title">SALES INVOICE</div>

                    {{-- ══ CUSTOMER & ADDRESS ══ --}}
                    <div class="inv-customer-block">
                        <div class="cust-left">
                            <div class="cust-label">Customer</div>
                            <div class="cust-name">{{ $invoice->customer?->name ?? 'Walk-In Customer' }}</div>
                            @if(!empty($invoice->customer?->phone) && $invoice->customer_id != 1)
                                <div class="cust-phone"><strong>Phone:</strong> {{ $invoice->customer->phone }}</div>
                            @endif
                        </div>
                        <div class="cust-right">
                            <div class="addr-label">Address</div>
                            <div class="addr-text">{{ $invoice->customer?->address ?? '' }}</div>
                        </div>
                    </div>

                    {{-- ══ PRODUCTS TABLE ══ --}}
                    <table class="sales-table">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">Nº</th>
                                <th style="width: 46%; text-align: left;">DESCRIPTION(CODE)</th>
                                <th style="width: 14%; text-align: right;">PRICE</th>
                                <th style="width: 12%; text-align: center;">QTY</th>
                                <th style="width: 8%; text-align: center;">DIS</th>
                                <th style="width: 15%; text-align: right;">LINE TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $calcSubTotal = 0;
                            @endphp
                            @foreach ($invoice->invoiceItems as $item)
                                @php
                                    $lineSub = (float)($item->subtotal ?? ($item->rate * $item->main_qty));
                                    $calcSubTotal += $lineSub;

                                    $subParts = [];
                                    if (!empty($item->imei)) {
                                        $imeiList = array_filter(array_map('trim', explode("\n", $item->imei)));
                                        if (!empty($imeiList)) {
                                            $subParts[] = 'SL#' . implode(', ', $imeiList);
                                        }
                                    }

                                    // Warranty text
                                    $wVal = $item->warranty_value;
                                    $wUnit = $item->warranty_unit;
                                    if (empty($wVal) && $item->product) {
                                        if ($item->product->warranty) {
                                            $wVal = $item->product->warranty->duration;
                                            $wUnit = $item->product->warranty->period;
                                        } elseif (!empty($item->product->warranty_value)) {
                                            $wVal = $item->product->warranty_value;
                                            $wUnit = $item->product->warranty_unit ?: 'Month';
                                        }
                                    }
                                    if (!empty($wVal)) {
                                        $subParts[] = $wVal . ' ' . $wUnit . ' warranty.';
                                    }
                                @endphp
                                <tr>
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="item-name">
                                            {{ $item->product?->name }}
                                            @if($item->product_variation)
                                                ({{ $item->product_variation->size?->size }} {{ $item->product_variation->color?->color }})
                                            @endif
                                        </div>
                                        @if(!empty($subParts))
                                            <div class="item-sub">{{ implode(', ', $subParts) }}</div>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                                    <td style="text-align: center;">{{ number_format($item->main_qty, 2) }} {{ $item->product?->unit?->name ?: 'Pcs' }}</td>
                                    <td style="text-align: center;">
                                        {{ !empty($item->product_discount) && (float)$item->product_discount > 0 ? number_format((float)$item->product_discount, 2) : '' }}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($lineSub, 2) }}</td>
                                </tr>
                            @endforeach

                            {{-- ══ SUMMARY ROWS (Same styling as screenshot) ══ --}}
                            @php
                                $invDiscount = (float)($invoice->discount_amount ?? $invoice->discount ?? 0);
                                $previousDue = (float)($invoice->previous_due ?? 0);
                            @endphp

                            <tr>
                                <td colspan="3" style="background: #fff;"></td>
                                <td colspan="2" class="summary-label">Sub Total</td>
                                <td class="summary-val">{{ number_format($calcSubTotal, 2) }}</td>
                            </tr>

                            @if($invDiscount > 0)
                            <tr>
                                <td colspan="3" style="background: #fff;"></td>
                                <td colspan="2" class="summary-label">Discount</td>
                                <td class="summary-val">{{ number_format($invDiscount, 2) }}</td>
                            </tr>
                            @endif

                            @if($previousDue > 0)
                            <tr>
                                <td colspan="3" style="background: #fff;"></td>
                                <td colspan="2" class="summary-label">Previous Due</td>
                                <td class="summary-val">{{ number_format($previousDue, 2) }}</td>
                            </tr>
                            @endif

                            <tr>
                                <td colspan="3" style="background: #fff;"></td>
                                <td colspan="2" class="summary-label">Gross Total</td>
                                <td class="summary-val">{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>

                            <tr>
                                <td colspan="3" style="background: #fff;"></td>
                                <td colspan="2" class="summary-label">Paid Amount</td>
                                <td class="summary-val">{{ number_format($invoice->total_paid, 2) }}</td>
                            </tr>

                            <tr>
                                <td colspan="3" style="background: #fff;"></td>
                                <td colspan="2" class="summary-label">Outstanding</td>
                                <td class="summary-val">{{ number_format($invoice->total_due, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- ══ AMOUNT IN WORDS ══ --}}
                    <div class="inwords-block">
                        <strong>Amount in words:</strong> {{ $inWords }}
                    </div>

                    @if(!empty($invoice->note))
                    <div class="invoice-note-block" style="margin-top: 8px; margin-bottom: 8px; padding: 6px 10px; background: #f8fafc; border-left: 3px solid #0088cc; font-size: 11px; border-radius: 2px;">
                        <strong>Note:</strong> {{ $invoice->note }}
                    </div>
                    @endif

                    {{-- ══ TERMS & CONDITIONS ══ --}}
                    <div class="terms-block">
                        <div class="terms-title">Terms & Conditions:</div>
                        <ul>
                            <li>এই ইনভয়েস পেপার ছাড়া ওয়ারেন্টি প্রযোজ্য হবে না । একবার পণ্য বিক্রি হলে তা ফেরত নেওয়া হবে না ।</li>
                            <li>ব্যাটারি, চার্জার, USB কেবল, AB কেবল, সফটওয়্যার সমস্যা, পানি লাগা, আগুনে পোড়া, শারীরিক ক্ষতি, নষ্ট (junk) আইটেম এবং সব ধরনের এক্সটার্নাল এক্সেসরিজ—এগুলোর ক্ষেত্রে ওয়ারেন্টি প্রযোজ্য নয় ।</li>
                            <li>পণ্যের নিয়ম অনুযায়ী ওয়ারেন্টি সংক্রান্ত সমস্যা সমাধানে ন্যূনতম ১৫ কার্যদিবস সময় লাগবে ।</li>
                            <li>ফাঙ্গাস ও ধুলাবালির জন্য কোনো ওয়ারেন্টি নেই ।</li>
                            <li>ড্রোন ওয়ারেন্টি নীতিমালা প্রতিটি ড্রোন প্রোডাক্টের ক্ষেত্রে শুধুমাত্র সার্ভিস ওয়ারেন্টি প্রযোজ্য হবে। কোনো ধরনের পার্টস ওয়ারেন্টি প্রযোজ্য নয়। ওয়ারেন্টির আওতায় শুধুমাত্র প্রোডাক্টের সার্ভিস সংক্রান্ত সমস্যা সমাধান করা হবে। </li>
                        </ul>
                    </div>

                    {{-- ══ INSTALLMENT SCHEDULE (If applicable) ══ --}}
                    @if ($invoice->installment && $invoice->installment->schedules->count() > 0)
                        <div style="margin-top: 15px;">
                            <h4 style="font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 6px; border-bottom: 1px solid #000; padding-bottom: 2px;">Payment Schedule</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;" class="table-bordered text-center">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="border: 1px solid #000; padding: 4px; font-weight: bold;">Installment #</th>
                                        <th style="border: 1px solid #000; padding: 4px; font-weight: bold;">Due Date</th>
                                        <th style="border: 1px solid #000; padding: 4px; font-weight: bold;">Scheduled Amount</th>
                                        <th style="border: 1px solid #000; padding: 4px; font-weight: bold;">Paid Amount</th>
                                        <th style="border: 1px solid #000; padding: 4px; font-weight: bold;">Paid Date</th>
                                        <th style="border: 1px solid #000; padding: 4px; font-weight: bold;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->installment->schedules as $sched)
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 4px; font-weight: bold;">#{{ $sched->installment_no }}</td>
                                            <td style="border: 1px solid #000; padding: 4px;">{{ $sched->due_date }}</td>
                                            <td style="border: 1px solid #000; padding: 4px;">Tk {{ number_format($sched->amount, 2) }}</td>
                                            <td style="border: 1px solid #000; padding: 4px;">{{ $sched->status == 'paid' ? 'Tk '.number_format($sched->paid_amount, 2) : '-' }}</td>
                                            <td style="border: 1px solid #000; padding: 4px;">{{ $sched->paid_date ?? '-' }}</td>
                                            <td style="border: 1px solid #000; padding: 4px; font-weight: bold;">{{ strtoupper($sched->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- ══ FOOTER SIGNATURES & QR CODE ══ --}}
                    <div class="inv-footer-signatures">
                        <div class="sig-block">
                            <div class="sig-line"></div>
                            <div class="sig-label">Customer Signature</div>
                        </div>

                        <div class="qr-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode(route('invoice.print', $invoice->id)) }}" alt="QR Code">
                        </div>

                        <div class="sig-block">
                            <div class="sig-line"></div>
                            <div class="sig-label">Authorized Signature</div>
                        </div>
                    </div>

                </div>{{-- /invoice-wrapper --}}

                <div class="print-btn-bar print_hidden">
                    <button class="btn btn-primary btn-lg shadow px-4 font-bold" onclick="print_receipt('print-area')" style="background-color: #0088cc; border: none; border-radius: 8px;">
                        <i class="fa fa-print mr-2"></i> Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('js')
    <script>
        localStorage.removeItem('pos-items');

        function print_receipt(divName) {
            window.print();
        }

        @if(env('APP_AUTO_PRINT') == 'yes')
        $(document).ready(function() {
            window.print();
            window.location.href = "{{ route('invoice.create') }}";
        });
        @endif
    </script>
@endpush
