@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Supplier Report'))

@push('css')
<style>
    .report-summary-card {
        border-radius: 12px;
        padding: 16px 20px;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
    }
    .report-summary-card:hover {
        transform: translateY(-2px);
    }
    .report-summary-card .label { 
        font-size: 11px; 
        opacity: 0.9; 
        font-weight: 700; 
        letter-spacing: 0.5px; 
        text-transform: uppercase; 
    }
    .report-summary-card .value { 
        font-size: 20px; 
        font-weight: 800; 
        margin-top: 4px; 
    }
    .bg-inv      { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
    .bg-paid     { background: linear-gradient(135deg, #059669, #10b981); }
    .bg-due      { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .bg-grand    { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .bg-qty      { background: linear-gradient(135deg, #0284c7, #06b6d4); }
    .bg-return   { background: linear-gradient(135deg, #e11d48, #f43f5e); }
    .bg-stock-qty { background: linear-gradient(135deg, #0284c7, #38bdf8); }
    .bg-stock-val { background: linear-gradient(135deg, #0d9488, #14b8a6); }
    
    .section-title { 
        font-size: 14px; 
        font-weight: 700; 
        color: #1e293b; 
        border-left: 4px solid #4f46e5; 
        padding-left: 10px; 
        margin: 20px 0 12px; 
    }
    .sup-info-table td { 
        padding: 6px 12px; 
        font-size: 13px; 
    }
    .sup-info-table td:first-child { 
        font-weight: 700; 
        color: #64748b; 
        width: 170px; 
    }
    .badge-branch { 
        background: #e0e7ff; 
        color: #4338ca; 
        padding: 3px 10px; 
        border-radius: 20px; 
        font-size: 11px; 
        font-weight: 700; 
    }
    .badge-prod {
        background: #f1f5f9;
        color: #334155;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
    }
    table.report-tbl th { 
        font-size: 12px; 
        background: #1e1b4b; 
        color: #fff; 
        vertical-align: middle;
        font-weight: 600;
        white-space: nowrap;
    }
    table.report-tbl td { 
        font-size: 13px; 
        vertical-align: middle; 
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 18px;
    }
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        background: transparent;
    }

    @media print {
        .no-print, .no-print * { display: none !important; }
        body { background: #fff !important; }
        .card { border: none !important; box-shadow: none !important; margin-bottom: 10px !important; }
        table th, table td { color: #000 !important; font-size: 11px !important; padding: 4px !important; }
        table.report-tbl th { background: #e2e8f0 !important; color: #000 !important; }
        .report-summary-card { color: #000 !important; border: 1px solid #cbd5e1 !important; background: none !important; box-shadow: none !important; padding: 8px !important; }
        .report-summary-card .value { font-size: 14px !important; }
        .tab-pane { display: block !important; opacity: 1 !important; visibility: visible !important; }
        .tab-content > .tab-pane { display: block !important; }
        .nav-tabs { display: none !important; }
        #print-header { display: block !important; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">

        {{-- Filter Card --}}
        <div class="card card-body card_style mb-3 no-print">
            <form action="{{ route('report.supplier-report') }}" method="GET">
                <div class="form-row align-items-end">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="font-weight-bold text-muted small mb-1">{{ __('Select Supplier') }}</label>
                        <select name="supplier_id" class="select2 form-control">
                            <option value="">-- {{ __('All Suppliers / Choose Supplier') }} --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $supplier_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} @if($s->phone) ({{ $s->phone }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="font-weight-bold text-muted small mb-1">{{ __('Filter by Product (Optional)') }}</label>
                        <select name="product_id" class="select2 form-control">
                            <option value="">-- {{ __('All Products') }} --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ $product_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} @if($p->barcode) - [{{ $p->barcode }}] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 mb-3">
                        <label class="font-weight-bold text-muted small mb-1">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" class="form-control" style="height:38px !important;" value="{{ $sdate }}">
                    </div>

                    <div class="col-md-2 col-sm-6 mb-3">
                        <label class="font-weight-bold text-muted small mb-1">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" class="form-control" style="height:38px !important;" value="{{ $edate }}">
                    </div>

                    <div class="col-md-2 col-sm-6 mb-3 d-flex" style="gap:5px;">
                        <button type="submit" class="btn add_list_btn flex-grow-1" style="height:38px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa fa-search mr-1"></i> {{ __('Generate') }}
                        </button>
                        <a href="{{ route('report.supplier-report') }}" class="btn add_list_btn_reset" style="height:38px; width:38px; display:flex; align-items:center; justify-content:center;" title="{{ __('Reset') }}">
                            <i class="feather icon-refresh-cw"></i>
                        </a>
                        <button type="button" onclick="window.print()" class="btn add_list_btn" style="height:38px; width:38px; display:flex; align-items:center; justify-content:center;" title="{{ __('Print') }}">
                            <i class="feather icon-printer"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Company Header (Print only) --}}
        <div class="text-center mb-3" style="display:none;" id="print-header">
            <h2 style="font-size:22px; font-weight:bold; margin-bottom:2px;">{{ get_setting('com_name') ?? config('app.name') }}</h2>
            <p style="margin-bottom:2px; font-size:12px;">{{ get_setting('com_address') }} | Phone: {{ get_setting('com_phone') }} | Email: {{ get_setting('com_email') }}</p>
            <h4 style="font-size:16px; font-weight:bold; margin-top:8px; text-transform:uppercase; text-decoration:underline;">
                {{ __('Supplier Complete History & Purchase Report') }}
            </h4>
            @if($sdate || $edate)
                <small class="text-muted">{{ __('Period') }}: {{ $sdate ?? 'Start' }} to {{ $edate ?? 'Today' }}</small>
            @endif
            <hr style="margin: 10px 0;">
        </div>

        @if($supplier)

        {{-- Supplier Info Card --}}
        <div class="card card_style mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title my-0">{{ __('Supplier Profile') }}</div>
                    @if($supplier->branch)
                        <span class="badge-branch"><i class="feather icon-map-pin mr-1"></i>{{ $supplier->branch->name }}</span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="sup-info-table">
                            <tr><td>{{ __('Supplier Name') }}</td><td>: <strong>{{ $supplier->name }}</strong></td></tr>
                            <tr><td>{{ __('Phone Number') }}</td><td>: {{ $supplier->phone ?? '—' }}</td></tr>
                            <tr><td>{{ __('Email Address') }}</td><td>: {{ $supplier->email ?? '—' }}</td></tr>
                            <tr><td>{{ __('Address') }}</td><td>: {{ $supplier->address ?? '—' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="sup-info-table">
                            <tr><td>{{ __('Opening Due') }}</td><td>: <span class="text-danger font-weight-bold">{{ number_format($supplier->due_amount ?? 0, 2) }} {{ get_setting('com_currency') }}</span></td></tr>
                            <tr><td>{{ __('Opening Advance') }}</td><td>: <span class="text-success font-weight-bold">{{ number_format($supplier->advance_amount ?? 0, 2) }} {{ get_setting('com_currency') }}</span></td></tr>
                            <tr><td>{{ __('Adjusted Opening Balance') }}</td><td>: <strong class="{{ $open_balance >= 0 ? 'text-danger' : 'text-success' }}">{{ number_format(abs($open_balance), 2) }} {{ get_setting('com_currency') }} {{ $open_balance >= 0 ? '(Payable)' : '(Advance)' }}</strong></td></tr>
                            <tr><td>{{ __('Total Invoices') }}</td><td>: <strong>{{ $purchases->count() }} {{ __('Bill(s)') }}</strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-inv">
                    <div class="label">{{ __('Total Purchases') }}</div>
                    <div class="value">{{ number_format($total_purchase_amount, 2) }} {{ get_setting('com_currency') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-paid">
                    <div class="label">{{ __('Total Paid') }}</div>
                    <div class="value">{{ number_format($total_paid, 2) }} {{ get_setting('com_currency') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-due">
                    <div class="label">{{ __('Purchase Due') }}</div>
                    <div class="value">{{ number_format($total_due, 2) }} {{ get_setting('com_currency') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-grand">
                    <div class="label">{{ __('Net Outstanding Due') }}</div>
                    <div class="value">{{ number_format($grand_due, 2) }} {{ get_setting('com_currency') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-qty">
                    <div class="label">{{ __('Total Qty Supplied') }}</div>
                    <div class="value">{{ number_format($total_items_qty, 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-return">
                    <div class="label">{{ __('Total Returns') }}</div>
                    <div class="value">{{ number_format($total_return_amount, 2) }} {{ get_setting('com_currency') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-stock-qty">
                    <div class="label">{{ __('Supplier Products Present Stock') }}</div>
                    <div class="value">{{ number_format($total_present_stock_qty ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-2">
                <div class="report-summary-card bg-stock-val">
                    <div class="label">{{ __('Present Stock Value / Amount') }}</div>
                    <div class="value">{{ number_format($total_present_stock_amount ?? 0, 2) }} {{ get_setting('com_currency') }}</div>
                </div>
            </div>
        </div>

        {{-- Nav Tabs for Sections --}}
        <ul class="nav nav-tabs mb-3 no-print" id="supplierReportTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="items-tab" data-toggle="tab" href="#items-pane" role="tab">
                    <i class="feather icon-list mr-1"></i> {{ __('Product Purchase History (Item Details)') }} ({{ $purchaseItems->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="summary-tab" data-toggle="tab" href="#summary-pane" role="tab">
                    <i class="feather icon-pie-chart mr-1"></i> {{ __('Product Summary') }} ({{ $productSummary->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices-pane" role="tab">
                    <i class="feather icon-file-text mr-1"></i> {{ __('Purchase Invoices') }} ({{ $purchases->count() }})
                </a>
            </li>
            @if($returnPurchases->count() > 0 || $returnItems->count() > 0)
            <li class="nav-item">
                <a class="nav-link" id="returns-tab" data-toggle="tab" href="#returns-pane" role="tab">
                    <i class="feather icon-corner-up-left mr-1"></i> {{ __('Purchase Returns') }} ({{ $returnPurchases->count() }})
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="ledger-tab" data-toggle="tab" href="#ledger-pane" role="tab">
                    <i class="feather icon-book mr-1"></i> {{ __('Payment & Ledger History') }} ({{ $transactions->count() }})
                </a>
            </li>
        </ul>

        <div class="tab-content" id="supplierReportTabContent">

            {{-- 1. Product Purchase History (Item-by-Item A to Z) --}}
            <div class="tab-pane fade show active" id="items-pane" role="tabpanel">
                <div class="card card_style mb-3">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span>{{ __('Product-wise Purchase History (A to Z Item Breakdown)') }}</span>
                            <span class="badge badge-primary font-weight-normal">{{ $purchaseItems->count() }} {{ __('Records') }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered report-tbl">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Purchase No') }}</th>
                                        <th>{{ __('Product Name') }}</th>
                                        <th>{{ __('Category / Brand') }}</th>
                                        <th>{{ __('Variation / Specs') }}</th>
                                        <th class="text-center">{{ __('Supplied Qty') }}</th>
                                        <th class="text-right">{{ __('Unit Rate') }}</th>
                                        <th class="text-right">{{ __('Subtotal') }}</th>
                                        <th class="text-center" style="background: #312e81;">{{ __('Present Stock') }}</th>
                                        <th class="text-right" style="background: #312e81;">{{ __('Stock Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseItems as $i => $item)
                                    @php
                                        $prod = $item->product;
                                        $itemStockQty = (float) ($item->stock_qty ?? 0);
                                        $itemStockText = $itemStockQty . ' ' . ($prod?->unit?->name ?? 'pcs');
                                        $itemStockAmount = $itemStockQty * (float)$item->rate;
                                    @endphp
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $item->date ? date('Y-m-d', strtotime($item->date)) : ($item->purchase?->date ?? '—') }}</td>
                                        <td>
                                            <strong>#{{ $item->purchase?->purchase_no ?? 'N/A' }}</strong>
                                            @if($item->purchase?->supplier_invoice_no)
                                                <br><small class="text-muted">Ref: {{ $item->purchase->supplier_invoice_no }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->product?->name ?? '—' }}</strong>
                                            @if($item->product?->barcode)
                                                <br><span class="badge-prod"><i class="feather icon-tag mr-1"></i>{{ $item->product->barcode }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $item->product?->category?->name ?? '—' }}</span>
                                            @if($item->product?->brand)
                                                <br><small class="text-muted">{{ $item->product->brand->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->product_variation)
                                                <span class="badge badge-info">{{ $item->product_variation->variation_name ?? $item->product_variation->name }}</span>
                                            @elseif($item->size || $item->color)
                                                {{ $item->size?->name }} {{ $item->color?->name }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            {{ $item->main_qty }} {{ $item->product?->unit?->name ?? '' }}
                                            @if($item->sub_qty > 0)
                                                <br><small class="text-muted">+ {{ $item->sub_qty }} {{ $item->product?->unit?->related_unit?->name ?? 'sub' }}</small>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($item->subtotal, 2) }}</td>
                                        <td class="text-center font-weight-bold">
                                            @if($itemStockQty > 0)
                                                <span class="badge badge-info px-2 py-1 shadow-sm" style="font-size: 12px;"><i class="feather icon-box mr-1"></i>{{ $itemStockText }}</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1" style="font-size: 12px;">{{ $itemStockText }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bold {{ $itemStockAmount > 0 ? 'text-primary' : 'text-muted' }}">
                                            {{ number_format($itemStockAmount, 2) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="11" class="text-center text-muted py-4">{{ __('No product purchase records found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                                @if($purchaseItems->count() > 0)
                                <tfoot>
                                    <tr style="background:#f1f5f9; font-weight:bold;">
                                        <td colspan="6" class="text-right">{{ __('Total') }}:</td>
                                        <td class="text-center text-primary">{{ number_format($purchaseItems->sum('main_qty'), 2) }}</td>
                                        <td></td>
                                        <td class="text-right text-success">{{ number_format($purchaseItems->sum('subtotal'), 2) }} {{ get_setting('com_currency') }}</td>
                                        <td class="text-center text-info font-weight-bold">{{ number_format($purchaseItems->sum('stock_qty'), 2) }}</td>
                                        <td class="text-right text-primary font-weight-bold">{{ number_format($purchaseItems->sum(function($it) { return (float)$it->stock_qty * (float)$it->rate; }), 2) }} {{ get_setting('com_currency') }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Product Aggregated Summary --}}
            <div class="tab-pane fade" id="summary-pane" role="tabpanel">
                <div class="card card_style mb-3">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span>{{ __('Product-wise Aggregated Summary') }}</span>
                            <span class="badge badge-info font-weight-normal">{{ $productSummary->count() }} {{ __('Distinct Products') }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered report-tbl">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>{{ __('Product Name') }}</th>
                                        <th>{{ __('Barcode') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th class="text-center">{{ __('Orders Count') }}</th>
                                        <th class="text-center">{{ __('Total Supplied Qty') }}</th>
                                        <th class="text-right">{{ __('Avg Purchase Rate') }}</th>
                                        <th class="text-right">{{ __('Total Amount') }}</th>
                                        <th class="text-center" style="background: #312e81;">{{ __('Present Stock') }}</th>
                                        <th class="text-right" style="background: #312e81;">{{ __('Stock Amount') }}</th>
                                        <th class="text-center">{{ __('Last Purchase Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productSummary as $i => $p)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td><strong>{{ $p->product?->name ?? '—' }}</strong></td>
                                        <td><code>{{ $p->product?->barcode ?? '—' }}</code></td>
                                        <td>{{ $p->product?->category?->name ?? '—' }}</td>
                                        <td class="text-center"><span class="badge badge-secondary">{{ $p->order_count }}</span></td>
                                        <td class="text-center font-weight-bold">{{ number_format($p->total_main_qty, 2) }} {{ $p->product?->unit?->name ?? '' }}</td>
                                        <td class="text-right">{{ number_format($p->avg_rate, 2) }}</td>
                                        <td class="text-right font-weight-bold text-success">{{ number_format($p->total_amount, 2) }}</td>
                                        <td class="text-center font-weight-bold">
                                            @if($p->present_stock_qty > 0)
                                                <span class="badge badge-info px-2 py-1 shadow-sm" style="font-size: 12px;"><i class="feather icon-box mr-1"></i>{{ $p->present_stock_text }}</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1" style="font-size: 12px;">{{ $p->present_stock_text }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bold {{ $p->present_stock_amount > 0 ? 'text-primary' : 'text-muted' }}">
                                            {{ number_format($p->present_stock_amount, 2) }}
                                        </td>
                                        <td class="text-center">{{ $p->last_purchase_date ?? '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="11" class="text-center text-muted py-4">{{ __('No product summary data found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                                @if($productSummary->count() > 0)
                                <tfoot>
                                    <tr style="background:#f1f5f9; font-weight:bold;">
                                        <td colspan="5" class="text-right">{{ __('Grand Total') }}:</td>
                                        <td class="text-center text-primary">{{ number_format($productSummary->sum('total_main_qty'), 2) }}</td>
                                        <td></td>
                                        <td class="text-right text-success">{{ number_format($productSummary->sum('total_amount'), 2) }} {{ get_setting('com_currency') }}</td>
                                        <td class="text-center text-info font-weight-bold">{{ number_format($productSummary->sum('present_stock_qty'), 2) }}</td>
                                        <td class="text-right text-primary font-weight-bold">{{ number_format($productSummary->sum('present_stock_amount'), 2) }} {{ get_setting('com_currency') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Purchase Invoices List --}}
            <div class="tab-pane fade" id="invoices-pane" role="tabpanel">
                <div class="card card_style mb-3">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span>{{ __('Purchase Invoices & Orders') }}</span>
                            <span class="badge badge-secondary font-weight-normal">{{ $purchases->count() }} {{ __('Invoices') }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered report-tbl">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Purchase No') }}</th>
                                        <th>{{ __('Supplier Ref No') }}</th>
                                        <th class="text-center">{{ __('Items') }}</th>
                                        <th class="text-right">{{ __('Total Amount') }}</th>
                                        <th class="text-right">{{ __('Discount') }}</th>
                                        <th class="text-right">{{ __('Paid') }}</th>
                                        <th class="text-right">{{ __('Due') }}</th>
                                        <th>{{ __('Created By') }}</th>
                                        <th>{{ __('Note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $i => $pur)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $pur->date }}</td>
                                        <td><strong>#{{ $pur->purchase_no }}</strong></td>
                                        <td>{{ $pur->supplier_invoice_no ?? '—' }}</td>
                                        <td class="text-center"><span class="badge badge-light">{{ $pur->purchaseItems->count() }}</span></td>
                                        <td class="text-right font-weight-bold">{{ number_format($pur->total_amount, 2) }}</td>
                                        <td class="text-right text-muted">{{ number_format($pur->discount_amount ?? 0, 2) }}</td>
                                        <td class="text-right text-success font-weight-bold">{{ number_format($pur->total_paid, 2) }}</td>
                                        <td class="text-right font-weight-bold {{ $pur->total_due > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ number_format($pur->total_due, 2) }}
                                        </td>
                                        <td><small>{{ $pur->user?->name ?? '—' }}</small></td>
                                        <td><small class="text-muted">{{ $pur->note ?? '—' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="11" class="text-center text-muted py-4">{{ __('No purchase invoices found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                                @if($purchases->count() > 0)
                                <tfoot>
                                    <tr style="background:#f1f5f9; font-weight:bold;">
                                        <td colspan="5" class="text-right">{{ __('Total') }}:</td>
                                        <td class="text-right text-dark">{{ number_format($total_purchase_amount, 2) }}</td>
                                        <td class="text-right text-muted">{{ number_format($purchases->sum('discount_amount'), 2) }}</td>
                                        <td class="text-right text-success">{{ number_format($total_paid, 2) }}</td>
                                        <td class="text-right text-danger">{{ number_format($total_due, 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Purchase Returns --}}
            @if($returnPurchases->count() > 0 || $returnItems->count() > 0)
            <div class="tab-pane fade" id="returns-pane" role="tabpanel">
                <div class="card card_style mb-3">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span>{{ __('Purchase Returns') }}</span>
                            <span class="badge badge-danger font-weight-normal">{{ $returnPurchases->count() }} {{ __('Return Vouchers') }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered report-tbl">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Return Purchase ID') }}</th>
                                        <th>{{ __('Original Purchase Ref') }}</th>
                                        <th class="text-right">{{ __('Estimated Amount') }}</th>
                                        <th class="text-right">{{ __('Discount') }}</th>
                                        <th class="text-right">{{ __('Total Return Amount') }}</th>
                                        <th>{{ __('Created By') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($returnPurchases as $i => $ret)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $ret->date ?? ($ret->created_at ? $ret->created_at->format('Y-m-d') : '—') }}</td>
                                        <td><strong>#{{ $ret->id }}</strong></td>
                                        <td>{{ $ret->purchase_id ? '#'.$ret->purchase_id : '—' }}</td>
                                        <td class="text-right">{{ number_format($ret->estimated_amount, 2) }}</td>
                                        <td class="text-right text-muted">{{ number_format($ret->discount_amount, 2) }}</td>
                                        <td class="text-right font-weight-bold text-danger">{{ number_format($ret->total_return, 2) }}</td>
                                        <td><small>{{ $ret->user?->name ?? '—' }}</small></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">{{ __('No return records found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                                @if($returnPurchases->count() > 0)
                                <tfoot>
                                    <tr style="background:#f1f5f9; font-weight:bold;">
                                        <td colspan="6" class="text-right">{{ __('Total Returns') }}:</td>
                                        <td class="text-right text-danger">{{ number_format($total_return_amount, 2) }} {{ get_setting('com_currency') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 5. Payment & Ledger Transactions --}}
            <div class="tab-pane fade" id="ledger-pane" role="tabpanel">
                <div class="card card_style mb-3">
                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <span>{{ __('Supplier Ledger & Transactions') }}</span>
                            <span class="badge badge-primary font-weight-normal">{{ $transactions->count() }} {{ __('Transactions') }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered report-tbl">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Type / Particulars') }}</th>
                                        <th>{{ __('Purchase Ref') }}</th>
                                        <th class="text-right">{{ __('Debit (Payment / Adv)') }}</th>
                                        <th class="text-right">{{ __('Credit (Purchase Bill)') }}</th>
                                        <th class="text-right">{{ __('Running Balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $runningBalance = $previous_balance ?: ($open_balance ?? 0); 
                                    @endphp
                                    @if($opening_due > 0 || $opening_advance > 0)
                                    <tr style="background:#fef3c7;">
                                        <td colspan="4"><strong>{{ __('Opening Balance (Initial)') }}</strong></td>
                                        <td class="text-right text-success">{{ $opening_advance > 0 ? number_format($opening_advance, 2) : '—' }}</td>
                                        <td class="text-right text-danger">{{ $opening_due > 0 ? number_format($opening_due, 2) : '—' }}</td>
                                        <td class="text-right font-weight-bold {{ $open_balance > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format(abs($open_balance), 2) }} {{ $open_balance > 0 ? '(Dr)' : ($open_balance < 0 ? '(Cr)' : '') }}
                                        </td>
                                    </tr>
                                    @endif

                                    @forelse($transactions as $i => $tr)
                                    @php
                                        $runningBalance = $runningBalance + ($tr->credit ?? 0) - ($tr->debit ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $tr->date }}</td>
                                        <td>{{ $tr->transaction_type }}</td>
                                        <td>{{ $tr->purchase ? '#'.$tr->purchase->purchase_no : ($tr->invoice ? '#'.$tr->invoice->invoice_no : '—') }}</td>
                                        <td class="text-right text-success font-weight-bold">
                                            {{ $tr->debit > 0 ? number_format($tr->debit, 2) : '—' }}
                                        </td>
                                        <td class="text-right text-danger font-weight-bold">
                                            {{ $tr->credit > 0 ? number_format($tr->credit, 2) : '—' }}
                                        </td>
                                        <td class="text-right font-weight-bold {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format(abs($runningBalance), 2) }}
                                            {{ $runningBalance > 0 ? '(Payable)' : ($runningBalance < 0 ? '(Advance)' : '') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No ledger transactions found.') }}</td></tr>
                                    @endforelse
                                </tbody>
                                @if($transactions->count() > 0 || $open_balance != 0)
                                <tfoot>
                                    <tr style="background:#1e1b4b; color:#fff; font-weight:bold;">
                                        <td colspan="4" class="text-right text-white">{{ __('Total & Closing Balance') }}:</td>
                                        <td class="text-right text-success">{{ number_format($transactions->sum('debit') + $opening_advance, 2) }}</td>
                                        <td class="text-right text-warning">{{ number_format($transactions->sum('credit') + $opening_due, 2) }}</td>
                                        <td class="text-right text-white">{{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? '(Payable)' : '(Advance)' }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @else

        {{-- All Suppliers Overview List --}}
        <div class="card card_style mb-3">
            <div class="card-body">
                <div class="section-title d-flex justify-content-between align-items-center mb-3">
                    <span>{{ __('All Suppliers Overview & Summary Report') }}</span>
                    <span class="badge badge-primary font-weight-normal">{{ $allSuppliersSummary->count() }} {{ __('Suppliers') }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered report-tbl">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>{{ __('Supplier Name & Contact') }}</th>
                                <th>{{ __('Branch') }}</th>
                                <th class="text-center">{{ __('Total Orders') }}</th>
                                <th class="text-center">{{ __('Total Qty Supplied') }}</th>
                                <th class="text-right">{{ __('Total Purchases') }}</th>
                                <th class="text-right">{{ __('Total Paid') }}</th>
                                <th class="text-right">{{ __('Purchase Due') }}</th>
                                <th class="text-right">{{ __('Opening Bal') }}</th>
                                <th class="text-right">{{ __('Returns') }}</th>
                                <th class="text-right">{{ __('Net Due') }}</th>
                                <th class="text-center no-print" style="width:130px;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandPurchases = 0;
                                $grandPaid = 0;
                                $grandDue = 0;
                                $grandOpen = 0;
                                $grandReturns = 0;
                                $grandNet = 0;
                                $grandQty = 0;
                            @endphp
                            @forelse($allSuppliersSummary as $i => $row)
                            @php
                                $grandPurchases += $row->total_amount;
                                $grandPaid += $row->total_paid;
                                $grandDue += $row->total_due;
                                $grandOpen += $row->open_balance;
                                $grandReturns += $row->return_amount;
                                $grandNet += $row->net_due;
                                $grandQty += $row->total_items_qty;
                            @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>
                                    <strong>{{ $row->supplier->name }}</strong>
                                    @if($row->supplier->phone)
                                        <br><small class="text-muted"><i class="feather icon-phone mr-1"></i>{{ $row->supplier->phone }}</small>
                                    @endif
                                </td>
                                <td><span class="badge-branch">{{ $row->supplier->branch?->name ?? 'Main' }}</span></td>
                                <td class="text-center"><span class="badge badge-secondary">{{ $row->purchase_count }}</span></td>
                                <td class="text-center font-weight-bold">{{ number_format($row->total_items_qty, 2) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($row->total_amount, 2) }}</td>
                                <td class="text-right text-success font-weight-bold">{{ number_format($row->total_paid, 2) }}</td>
                                <td class="text-right font-weight-bold {{ $row->total_due > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ number_format($row->total_due, 2) }}
                                </td>
                                <td class="text-right text-muted">{{ number_format($row->open_balance, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($row->return_amount, 2) }}</td>
                                <td class="text-right font-weight-bold {{ $row->net_due > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($row->net_due, 2) }}
                                </td>
                                <td class="text-center no-print">
                                    <a href="{{ route('report.supplier-report', ['supplier_id' => $row->supplier->id, 'start_date' => $sdate, 'end_date' => $edate]) }}" class="btn add_list_btn btn-sm">
                                        <i class="feather icon-file-text mr-1"></i> {{ __('Full Report') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="12" class="text-center text-muted py-4">{{ __('No suppliers found.') }}</td></tr>
                            @endforelse
                        </tbody>
                        @if($allSuppliersSummary->count() > 0)
                        <tfoot>
                            <tr style="background:#f1f5f9; font-weight:bold;">
                                <td colspan="4" class="text-right">{{ __('Total') }} ({{ $allSuppliersSummary->count() }}):</td>
                                <td class="text-center text-primary">{{ number_format($grandQty, 2) }}</td>
                                <td class="text-right text-dark">{{ number_format($grandPurchases, 2) }}</td>
                                <td class="text-right text-success">{{ number_format($grandPaid, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($grandDue, 2) }}</td>
                                <td class="text-right text-muted">{{ number_format($grandOpen, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($grandReturns, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($grandNet, 2) }} {{ get_setting('com_currency') }}</td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @endif

    </div>
</div>
@endsection

@push('js')
<script>
    // Ensure all tab panes are shown when printing
    window.onbeforeprint = function() {
        document.querySelectorAll('.tab-pane').forEach(function(el) {
            el.classList.add('show', 'active');
        });
        document.getElementById('print-header') && (document.getElementById('print-header').style.display = 'block');
    };
    window.onafterprint = function() {
        document.getElementById('print-header') && (document.getElementById('print-header').style.display = 'none');
        // Restore active tab
        var activeTab = document.querySelector('.nav-tabs .nav-link.active');
        if (activeTab) {
            var target = activeTab.getAttribute('href');
            document.querySelectorAll('.tab-pane').forEach(function(el) {
                if ('#' + el.id !== target) {
                    el.classList.remove('show', 'active');
                }
            });
        }
    };
</script>
@endpush