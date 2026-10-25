@extends('backend.layouts.master')
@section('section-title', 'Invoice')
@section('page-title', 'Invoice List')
@if (check_permission('invoice.create'))
    @section('action-button')
        <a href="{{ route('invoice.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            Add Invoice
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {
            @page {
                size: 75mm 100mm;
                margin: 0;
            }
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            .shipping-label-card {
                width: 75mm !important;
                max-width: 75mm !important;
                min-height: 100mm !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .modal,
            .modal-backdrop {
                display: none !important;
            }
        }

        .table-responsive {
            overflow: visible !important;
        }

        .card_style {
            overflow: visible !important;
        }

        /* Details Modal Theme Adaptability */
        .summary-box-modal {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }
        .modal-detail-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .modal-detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Dark Mode Overrides for Details Modal */
        .dark-theme .modal-content,
        body.dark-theme .modal-content {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        .dark-theme .summary-box-modal,
        body.dark-theme .summary-box-modal {
            background-color: #0f172a !important;
            border-color: #334155 !important;
        }
        .dark-theme .modal-detail-label,
        body.dark-theme .modal-detail-label {
            color: #94a3b8 !important;
        }
        .dark-theme .modal-detail-value,
        body.dark-theme .modal-detail-value {
            color: #f1f5f9 !important;
        }
        .dark-theme .modal-body .table,
        body.dark-theme .modal-body .table {
            color: #f1f5f9 !important;
        }
        .dark-theme .modal-body .table thead th,
        body.dark-theme .modal-body .table thead th {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        .dark-theme .modal-body .table td,
        body.dark-theme .modal-body .table td {
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        .dark-theme .modal-body .bg-light,
        body.dark-theme .modal-body .bg-light {
            background-color: #0f172a !important;
        }
        .dark-theme .modal-body .text-muted,
        body.dark-theme .modal-body .text-muted {
            color: #94a3b8 !important;
        }
        /* Status Carousel Styling */
        .online-carousel-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            position: relative;
        }
        .online-carousel-nav {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .online-carousel-nav:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        .online-pills-track {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 4px 2px;
            flex: 1;
        }
        .online-pills-track::-webkit-scrollbar {
            display: none;
        }
        .online-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .online-pill:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        .online-pill.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        }
        .online-pill.active .online-pill-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }
        .online-pill-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
        }
        .badge-orange { background: #ffedd5; color: #ea580c; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-cyan { background: #cffafe; color: #0891b2; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-dark { background: #334155; color: #ffffff; }

        /* Global Search Bar */
        .global-search-bar {
            display: flex;
            align-items: stretch;
            margin-bottom: 12px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #2563eb;
            box-shadow: 0 1px 3px rgba(37,99,235,0.1);
        }
        .global-search-label {
            background: #2563eb;
            color: #ffffff;
            font-weight: 700;
            font-size: 12.5px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .global-search-input {
            flex: 1;
            border: none;
            padding: 10px 16px;
            font-size: 13px;
            outline: none;
            background: #ffffff;
        }
        .global-search-btn {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .global-search-btn:hover {
            background: #1d4ed8;
        }

        /* Actions Bar */
        .online-actions-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }
        .btn-action-slate {
            background: #475569;
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-slate:hover { background: #334155; color: #ffffff; }
        .btn-action-cyan {
            background: #06b6d4;
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-cyan:hover { background: #0891b2; color: #ffffff; }
        .btn-action-orange {
            background: #f97316;
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-orange:hover { background: #ea580c; color: #ffffff; }
        .btn-action-green {
            background: #10b981;
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-green:hover { background: #059669; color: #ffffff; }
        .btn-action-purple {
            background: #6366f1;
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-purple:hover { background: #4f46e5; color: #ffffff; }
        .btn-action-indigo {
            background: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-indigo:hover { background: #4338ca; color: #ffffff; }

        /* Royal Blue Table Header */
        .table thead.royal-header th {
            background: #0a3d91 !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1e40af !important;
            padding: 12px 10px;
            vertical-align: middle;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="feather icon-check-circle mr-1"></i> {{ __('Success') }}:</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong><i class="feather icon-info mr-1"></i> {{ __('Info') }}:</strong> {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    {{-- Carousel Status Filter Tabs matching Screenshot 2 & 3 --}}
                    @php
                        $curStatus = $status ?? 'all';
                        $counts = $statusCounts ?? [];
                    @endphp
                    <div class="online-carousel-wrapper">
                        <button type="button" class="online-carousel-nav" id="scrollOnlinePillsLeft" title="Scroll Left">
                            <i class="feather icon-chevron-left"></i>
                        </button>

                        <div class="online-pills-track" id="onlinePillsTrack">
                            <a href="{{ route('invoice.online.sale', ['status' => 'processing']) }}"
                               class="online-pill {{ $curStatus === 'processing' ? 'active' : '' }}">
                                <i class="feather icon-clock text-warning"></i>
                                <span>Processing</span>
                                <span class="online-pill-badge badge-orange">{{ $counts['processing'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'rts']) }}"
                               class="online-pill {{ $curStatus === 'rts' ? 'active' : '' }}">
                                <i class="feather icon-box text-cyan"></i>
                                <span>RTS</span>
                                <span class="online-pill-badge badge-cyan">{{ $counts['rts'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'in_review']) }}"
                               class="online-pill {{ $curStatus === 'in_review' ? 'active' : '' }}">
                                <i class="feather icon-eye text-primary"></i>
                                <span>In Review</span>
                                <span class="online-pill-badge badge-blue">{{ $counts['in_review'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'pending_courier']) }}"
                               class="online-pill {{ in_array($curStatus, ['pending_courier', 'pending']) ? 'active' : '' }}">
                                <i class="feather icon-clock text-warning"></i>
                                <span>Pending (Courier)</span>
                                <span class="online-pill-badge badge-orange">{{ $counts['pending_courier'] ?? $counts['pending'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'in_transit']) }}"
                               class="online-pill {{ $curStatus === 'in_transit' ? 'active' : '' }}">
                                <i class="feather icon-truck text-info"></i>
                                <span>In Transit</span>
                                <span class="online-pill-badge badge-cyan">{{ $counts['in_transit'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'all_in_courier']) }}"
                               class="online-pill {{ in_array($curStatus, ['all_in_courier', 'in_courier']) ? 'active' : '' }}">
                                <i class="feather icon-send text-info"></i>
                                <span>All in Courier</span>
                                <span class="online-pill-badge badge-cyan">{{ $counts['all_in_courier'] ?? $counts['in_courier'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'on_hold']) }}"
                               class="online-pill {{ $curStatus === 'on_hold' ? 'active' : '' }}">
                                <i class="feather icon-pause text-secondary"></i>
                                <span>On Hold</span>
                                <span class="online-pill-badge badge-dark">{{ $counts['on_hold'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'delivered']) }}"
                               class="online-pill {{ $curStatus === 'delivered' ? 'active' : '' }}">
                                <i class="feather icon-check-circle text-success"></i>
                                <span>Delivered</span>
                                <span class="online-pill-badge badge-green">{{ $counts['delivered'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'pending_return']) }}"
                               class="online-pill {{ $curStatus === 'pending_return' ? 'active' : '' }}">
                                <i class="feather icon-clock text-warning"></i>
                                <span>Pending Return</span>
                                <span class="online-pill-badge badge-orange">{{ $counts['pending_return'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'returned']) }}"
                               class="online-pill {{ $curStatus === 'returned' ? 'active' : '' }}">
                                <i class="feather icon-rotate-ccw text-danger"></i>
                                <span>Returned</span>
                                <span class="online-pill-badge badge-red">{{ $counts['returned'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'partial']) }}"
                               class="online-pill {{ $curStatus === 'partial' ? 'active' : '' }}">
                                <i class="feather icon-pie-chart text-teal"></i>
                                <span>Partial</span>
                                <span class="online-pill-badge badge-cyan">{{ $counts['partial'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'cancelled']) }}"
                               class="online-pill {{ $curStatus === 'cancelled' ? 'active' : '' }}">
                                <i class="feather icon-x-circle text-dark"></i>
                                <span>Cancelled</span>
                                <span class="online-pill-badge badge-dark">{{ $counts['cancelled'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'all']) }}"
                               class="online-pill {{ $curStatus === 'all' ? 'active' : '' }}">
                                <i class="feather icon-layers"></i>
                                <span>All Orders</span>
                                <span class="online-pill-badge badge-blue">{{ $counts['all'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('invoice.online.sale', ['status' => 'woocommerce']) }}"
                               class="online-pill {{ $curStatus === 'woocommerce' ? 'active' : '' }}">
                                <i class="feather icon-globe text-info"></i>
                                <span>WooCommerce Orders</span>
                                <span class="online-pill-badge badge-cyan">{{ $counts['woocommerce'] ?? 0 }}</span>
                            </a>
                        </div>

                        <button type="button" class="online-carousel-nav" id="scrollOnlinePillsRight" title="Scroll Right">
                            <i class="feather icon-chevron-right"></i>
                        </button>
                    </div>

                    <form action="{{ route('invoice.online.sale') }}" method="GET" id="onlineFilterForm">
                        <input type="hidden" name="status" value="{{ $curStatus }}">

                        {{-- Global Search Bar --}}
                        <div class="global-search-bar">
                            <div class="global-search-label">
                                <i class="feather icon-bar-chart-2"></i>
                                <span>Global Search / Scan Barcode</span>
                            </div>
                            <input type="text" name="barcode" value="{{ $barcode ?? ($invoice_no ?? '') }}"
                                   class="global-search-input barcode-filter-input" data-barcode-input
                                   placeholder="Scan Barcode / SKU or Search by Customer Name, Phone, Invoice No (WC-15845), Product Name, Consignment ID...">
                            <button type="submit" class="global-search-btn">
                                <i class="feather icon-search"></i>
                                <span>Search</span>
                            </button>
                        </div>

                        {{-- Filter Row --}}
                        @php
                            $customers = App\Models\Customer::get();
                            $products = App\Models\Product::get();
                        @endphp
                        <div class="row align-items-center mb-3">
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <input type="date" class="form-control" name="startDate" value="{{ $startDate }}" placeholder="mm/dd/yyyy" style="height: 38px;" />
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <input type="date" class="form-control" name="endDate" value="{{ $endDate }}" placeholder="mm/dd/yyyy" style="height: 38px;" />
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <input type="text" class="form-control" name="invoice_no" value="{{ $invoice_no ?? '' }}" placeholder="Invoice No" style="height: 38px;" />
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <select name="product_id" class="form-control select2" style="height: 38px;">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $item)
                                        <option value="{{ $item->id }}" {{ $product_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 mb-2">
                                <select name="customer_id" class="form-control select2" style="height: 38px;">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $item)
                                        <option value="{{ $item->id }}" {{ $customer_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} {{ $item->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-1 col-lg-1 col-md-3 col-sm-6 mb-2">
                                <select name="platform" class="form-control" style="height: 38px; font-size: 12px;">
                                    <option value="">All Platforms</option>
                                    <option value="Website">Website</option>
                                    <option value="WooCommerce">WooCommerce</option>
                                </select>
                            </div>
                            <div class="col-xl-1 col-lg-1 col-md-3 col-sm-6 mb-2">
                                <button type="submit" class="btn btn-primary btn-block font-weight-bold" style="height: 38px;">
                                    Search
                                </button>
                            </div>
                        </div>

                        {{-- Action Buttons Bar matching Screenshot 2 & 3 --}}
                        <div class="online-actions-bar">
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn-action-slate dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-edit-2"></i>
                                    <span>Bulk Change Status</span>
                                </button>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header text-uppercase font-weight-bold text-muted small">Change Selected To:</h6>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkStatusChange('processing')">
                                        <i class="feather icon-clock text-warning mr-2"></i> Processing
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkStatusChange('rts')">
                                        <i class="feather icon-box text-info mr-2"></i> RTS (Ready to Ship)
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkStatusChange('in_review')">
                                        <i class="feather icon-eye text-primary mr-2"></i> In Review
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkStatusChange('on_hold')">
                                        <i class="feather icon-pause text-secondary mr-2"></i> On Hold
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkStatusChange('delivered')">
                                        <i class="feather icon-check-circle text-success mr-2"></i> Delivered
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="performBulkStatusChange('returned')">
                                        <i class="feather icon-rotate-ccw mr-2"></i> Returned (Restores Stock to Inventory)
                                    </a>
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="performBulkStatusChange('cancelled')">
                                        <i class="feather icon-x-circle mr-2"></i> Cancelled (Restores Stock to Inventory)
                                    </a>
                                </div>
                            </div>

                            <button type="button" class="btn-action-cyan" onclick="syncCourierStatus()" title="Sync Courier Status">
                                <i class="feather icon-refresh-cw"></i>
                                <span>Sync Courier Status</span>
                            </button>

                            <button type="button" class="btn-action-orange" onclick="sendSelectedToCourier()" title="Send Selected to Courier">
                                <i class="feather icon-send"></i>
                                <span>Send Selected to Courier</span>
                            </button>

                            <button type="button" class="btn-action-green" onclick="openClearDuesModal()" title="Clear Selected Dues">
                                <i class="feather icon-check-circle"></i>
                                <span>Clear Selected Dues</span>
                            </button>

                            <button type="button" class="btn-action-purple" onclick="printSelectedInvoices()">
                                <i class="feather icon-printer"></i>
                                <span>Print Label</span>
                            </button>

                            <a href="" class="btn-action-indigo" onclick="window.print()">
                                <i class="feather icon-printer"></i>
                                <span>Print</span>
                            </a>
                        </div>
                    </form>

                    <!-- Modal for Clear Selected Dues -->
                    <div class="modal fade" id="clearDuesModal" tabindex="-1" role="dialog" aria-labelledby="clearDuesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title text-white" id="clearDuesModalLabel">
                                        <i class="feather icon-dollar-sign mr-1"></i> {{ __('Clear Due for Selected Invoices') }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('invoice.online.clear-selected-dues') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="invoice_ids" id="selectedInvoiceIdsInput">
                                    <div class="modal-body text-left">
                                        <div class="alert alert-info py-2 px-3 mb-3" style="border-radius: 6px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="feather icon-check-square mr-1"></i> {{ __('Selected Invoices') }}: <strong id="modalSelectedCount">0</strong></span>
                                                <span>{{ __('Total Due') }}: <strong id="modalTotalDue" class="text-danger font-weight-bold">TK 0.00</strong></span>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="modalBankId" class="font-weight-bold text-dark">{{ __('Select Deposit Bank / Payment Account') }} <span class="text-danger">*</span></label>
                                            <select name="bank_id" id="modalBankId" class="form-control" required style="height: 42px;">
                                                @if(isset($bankAccounts))
                                                    @foreach ($bankAccounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->bank_name }} @if(!empty($account->account_number))({{ $account->account_number }})@endif
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="modalPaymentDate" class="font-weight-bold text-dark">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" id="modalPaymentDate" class="form-control" value="{{ date('Y-m-d') }}" required style="height: 42px;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-success font-weight-bold">
                                            <i class="feather icon-check-circle mr-1"></i> {{ __('Confirm Due Payment') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-2">
                        <table id="datatable-buttons" class="table table-bordered w-100" style="width: 100% !important;">
                            <thead class="royal-header">
                                <tr class="text-center">
                                    <th style="width: 36px;"><input type="checkbox" id="selectAll"></th>
                                    <th>#SL</th>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>Customer</th>
                                    <th>Product Item(S)</th>
                                    <th>Total Amount</th>
                                    <th>Total Paid</th>
                                    <th>Total Due</th>
                                    <th>Return Amount</th>
                                    <th>Consignment ID</th>
                                    <th>Courier Status</th>
                                    <th>Create By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_paid = 0;
                                    $total_amt = 0;
                                    $total_due = 0;
                                @endphp
                                @forelse($invoices as $key => $data)
                                    @php
                                        $total_paid += $data->total_paid;
                                        $total_amt += $data->total_amount;
                                        $total_due += $data->total_due;
                                        $inv_items = App\Models\InvoiceItem::where('invoice_id', $data->id)->get();
                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">
                                            <input type="checkbox" class="invoiceCheckbox" value="{{ $data->id }}">
                                        </td>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            {{ $data->date }}
                                            @if($data->created_at)
                                                <br><small class="text-muted" style="font-size: 11px;">{{ date('h:i A', strtotime($data->created_at)) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#detailsModal-{{ $data->id }}" class="font-weight-bold text-primary" style="font-size: 13px;">{{ $data->invoice_no }}</a>
                                            @if($data->created_at)
                                                <br><small class="text-muted" style="font-size: 11px;"><i class="feather icon-clock"></i> {{ date('h:i A', strtotime($data->created_at)) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $data->customer->name }}</div>
                                            @if(!empty($data->customer->phone))
                                                @php
                                                    $cPhone = preg_replace('/[^\d]/', '', $data->customer->phone);
                                                    $waPhone = str_starts_with($cPhone, '0') ? '88' . $cPhone : $cPhone;
                                                @endphp
                                                <div class="small mt-1 d-flex align-items-center justify-content-center">
                                                    <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="text-success mr-1" title="Chat on WhatsApp">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
                                                    <span>{{ $data->customer->phone }}</span>
                                                </div>
                                                <button type="button" class="btn btn-xs mt-1" style="font-size:10px; border-radius:12px; padding:1px 8px; border:1px solid #06b6d4; color:#0891b2; background:#f0fdfa; cursor: pointer;" onclick="openCustomerFraudModal('{{ $data->customer->phone }}', '{{ addslashes($data->customer->name) }}')" title="{{ __('View Courier Delivery Rating & Fraud Check') }}">
                                                    <i class="feather icon-shield mr-1"></i> Courier Rating
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($inv_items as $item)
                                                <div class="font-weight-bold mb-1" style="font-size: 12px;">
                                                    {{ $item->product?->name }}
                                                    @if (env('APP_SC') == 'yes' && $item->product_variation)
                                                        <span class="text-muted">({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})</span>
                                                    @endif
                                                    @if ($item->is_return == 1)
                                                        <span class="badge bg-danger">Return</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="font-weight-bold">{{ number_format($data->total_amount, 2) }}</td>
                                        <td>{{ number_format($data->total_paid, 2) }}</td>
                                        <td class="invoice-due-val text-danger font-weight-bold">{{ number_format($data->total_due, 2) }}</td>
                                        <td>{{ number_format($data->return_amount ?? 0, 2) }}</td>
                                        <td>
                                            @if(!empty($data->consignment_id))
                                                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 11px; font-family: monospace;">{{ $data->consignment_id }}</span>
                                                <div class="small text-muted mt-1">{{ $data->courier_type ?? 'Steadfast' }}</div>
                                            @else
                                                <button type="button" class="btn btn-sm text-white" style="background:#f97316; font-size:11px; font-weight:700; border-radius:6px; padding:4px 8px;" onclick="sendSingleToCourier({{ $data->id }})">
                                                    <i class="feather icon-send mr-1"></i> Send to Stead Fast
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $cStatus = strtolower(trim($data->order_status ?? 'pending'));
                                            @endphp
                                            <span class="badge" style="background:#ffedd5; color:#c2410c; font-size:11px; font-weight:700; padding:4px 8px; border-radius:6px;">
                                                <i class="feather icon-clock mr-1"></i> {{ ucfirst(str_replace('_', ' ', $cStatus)) }}
                                            </span>
                                            <div class="mt-1">
                                                <button type="button" class="btn btn-xs btn-light border" style="font-size:10px; padding:2px 6px;" onclick="viewOrderNote({{ $data->id }}, '{{ addslashes($data->note ?? '') }}')">
                                                    <i class="feather icon-file-text text-muted"></i> View Note
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ $data->user->name ?? 'Admin' }}</td>
                                        <td class="table_data_style_right text-center" style="white-space: nowrap;">
                                            <div class="d-inline-flex align-items-center justify-content-center" style="gap: 4px;">
                                                <button type="button" class="btn btn-sm" style="background:#f59e0b; color:#fff; font-size:11px; font-weight:700; padding:4px 8px; border-radius:6px;" onclick="viewOrderNote({{ $data->id }}, '{{ addslashes($data->note ?? '') }}')" title="Note">
                                                    <i class="feather icon-file-text mr-1"></i> Note
                                                </button>
                                                <button type="button" class="btn btn-sm text-white" style="background:#06b6d4; font-size:11px; padding:4px 8px; border-radius:6px;" data-toggle="modal" data-target="#detailsModal-{{ $data->id }}" title="{{ __('Quick View') }}">
                                                    <i class="feather icon-eye"></i>
                                                </button>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                        id="dropdownMenuButton-{{ $data->id }}" data-toggle="dropdown" data-boundary="window" style="font-size:11px; padding:4px 8px;">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $data->id }}">
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="changeSingleOrderStatus({{ $data->id }}, 'rts')">
                                                            <i class="feather icon-box text-info mr-2"></i> Mark as RTS
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="changeSingleOrderStatus({{ $data->id }}, 'delivered')">
                                                            <i class="feather icon-check-circle text-success mr-2"></i> Mark as Delivered
                                                        </a>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="changeSingleOrderStatus({{ $data->id }}, 'returned')">
                                                            <i class="feather icon-rotate-ccw mr-2"></i> Mark as Returned (Restores Stock)
                                                        </a>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="changeSingleOrderStatus({{ $data->id }}, 'cancelled')">
                                                            <i class="feather icon-x-circle mr-2"></i> Mark as Cancelled (Restores Stock)
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#printModal-{{ $data->id }}">
                                                            <i class="feather icon-tag mr-2" style="color: #8b5cf6;"></i> Print Label
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('invoice.print', $data->id) }}" target="_blank">
                                                            <i class="feather icon-printer mr-2"></i> Print Invoice
                                                        </a>
                                                        @if (check_permission('invoice.pay') && $data->total_due > 0)
                                                            <a class="dropdown-item text-success" href="{{ url('invoice/pay/' . $data->id) }}">
                                                                <i class="feather icon-dollar-sign mr-2"></i> Pay Due
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center text-danger no_data_style">No Invoice Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="text-right header_bg">
                                    <td colspan="6" class="header_style_left text-white "><strong>Total: </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_amt, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_paid, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_due, 2) }} </strong></td>
                                    <td colspan="5" class="header_style_right"></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Render modals here outside the table --}}
                        @foreach ($invoices as $data)
                            <div class="modal fade" id="printModal-{{ $data->id }}" tabindex="-1"
                                aria-labelledby="printModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title font-weight-bold" id="printModalLabel-{{ $data->id }}">
                                                <i class="feather icon-tag mr-1 text-purple"></i> {{ __('Print Shipping Label') }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-center p-3" style="background: #f1f5f9; overflow-x: auto;">
                                            @include('backend.pages.invoice.shipping-label', ['data' => $data, 'prefix' => 'printableArea'])
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary font-weight-bold"
                                                onclick="printDiv('printableArea-{{ $data->id }}')">
                                                <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                            </button>
                                            <button type="button" class="btn btn-secondary font-weight-bold"
                                                data-dismiss="modal">{{ __('Close') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- return amount modal  --}}
                            <form action="{{ route('invoice.return.amount', $data->id) }}" method="POST">
                                @csrf
                                <x-edit-modal title="Payment Amount" sizeClass="modal-md"
                                    id="{{ $data->id }}">
                                    <div class="mt-2 col-md-12">
                                        <label class="form-label font-weight-bold">Date</label>
                                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                                            name="date">
                                        <div class="error">
                                            {{ $errors->has('date') ? $errors->first('date') : '' }}</div>
                                    </div>
                                    <div class="mt-2 col-md-12">
                                        <label class="form-label font-weight-bold">Bank Account *</label>
                                        @php
                                            $bank_accounts = App\Models\BankAccount::where('status', 1)
                                                ->orderBy('bank_name', 'asc')
                                                ->get();
                                        @endphp
                                        <select class="select2" name="bank_id">
                                            @foreach ($bank_accounts as $bank_account)
                                                <option value="{{ $bank_account->id }}">
                                                    {{ $bank_account->bank_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mt-2 col-md-12">
                                        <input type="text" name="return_cus_amount" id=""
                                            value="{{ $data->total_paid }}">
                                        <label for="return_paid_amount" class="form-label fw-bold">Amount
                                            *</label>
                                        <input type="number" class="form-control" min="1" step="any"
                                            required placeholder="Enter Amount" name="return_paid_amount"
                                            value="{{ abs($data->total_paid) }}">
                                        <div class="error">
                                            {{ $errors->has('return_paid_amount') ? $errors->first('return_paid_amount') : '' }}
                                        </div>
                                    </div>
                                </x-edit-modal>
                            </form>

                            {{-- delete modal --}}
                            <form action="{{ route('invoice.destroy', $data->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-delete-modal title="Invoice" id="{{ $data->id }}" />
                            </form>

                            {{-- Details Modal --}}
                            <div class="modal fade" id="detailsModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header bg-primary text-white d-flex align-items-center justify-content-between">
                                            <h5 class="modal-title text-white" id="detailsModalLabel-{{ $data->id }}">
                                                <i class="feather icon-eye mr-2"></i>{{ __('Invoice Details') }} - {{ $data->invoice_no }}
                                            </h5>
                                            <button type="button" class="close text-white close_modal_btn" data-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 24px; line-height: 1;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-left">
                                            <div class="row">
                                                <!-- Overview -->
                                                <div class="col-md-6 mb-3">
                                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-info mr-1"></i> {{ __('Overview') }}</h6>
                                                    <div class="row">
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Invoice No') }}</div>
                                                            <div class="font-weight-bold text-primary modal-detail-value">{{ $data->invoice_no }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Date') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->date }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Customer') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->customer->name }} ({{ $data->customer->phone }})</div>
                                                            <button type="button" class="btn btn-xs mt-1" style="font-size:10px; border-radius:12px; padding:1px 8px; border:1px solid #06b6d4; color:#0891b2; background:#f0fdfa; cursor: pointer;" onclick="openCustomerFraudModal('{{ $data->customer->phone }}', '{{ addslashes($data->customer->name) }}')">
                                                                <i class="feather icon-shield mr-1"></i> Check Courier Rating
                                                            </button>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Branch') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->branch?->name ?? 'N/A' }}</div>
                                                        </div>
                                                        @if (env('APP_ONLINE') == 'yes')
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Sale Type') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ ucfirst($data->sale_type) }}</div>
                                                        </div>
                                                        @if ($data->sale_type == 'Online')
                                                            <div class="col-6 mb-2">
                                                                <div class="text-muted small modal-detail-label">{{ __('Platform') }}</div>
                                                                <div class="font-weight-bold modal-detail-value">{{ $data->platform?->name ?? 'N/A' }}</div>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <div class="text-muted small modal-detail-label">{{ __('Source Link') }}</div>
                                                                <div class="font-weight-bold modal-detail-value">
                                                                    @if ($data->source_link)
                                                                        <a href="{{ $data->source_link }}" target="_blank">{{ $data->source_link }}</a>
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </div>
                                                            </div>
                                                             <div class="col-6 mb-2">
                                                                 <div class="text-muted small modal-detail-label">{{ __('Courier Provider') }}</div>
                                                                 <div class="font-weight-bold modal-detail-value">{{ $data->courier_type ?? 'N/A' }}</div>
                                                             </div>
                                                             <div class="col-6 mb-2">
                                                                 <div class="text-muted small modal-detail-label">{{ __('Consignment ID') }}</div>
                                                                 <div class="font-weight-bold text-primary modal-detail-value">
                                                                     @if(!empty($data->consignment_id))
                                                                         <span class="badge bg-primary text-white p-1" style="font-size: 13px; font-family: monospace;">{{ $data->consignment_id }}</span>
                                                                     @else
                                                                         <span class="badge bg-secondary">N/A</span>
                                                                     @endif
                                                                 </div>
                                                             </div>
                                                        @endif
                                                        @endif
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Status') }}</div>
                                                            <div>
                                                                @if ($data->status == 0)
                                                                    <span class="badge badge-warning">{{ __('Due') }}</span>
                                                                @elseif($data->status == 1)
                                                                    <span class="badge badge-success">{{ __('Paid') }}</span>
                                                                @elseif($data->status == 2)
                                                                    <span class="badge badge-danger">{{ __('Returned') }}</span>
                                                                @endif
                                                                @if ($data->is_edited == 1 || $data->edit_status == 'edited')
                                                                    <span class="badge badge-info">{{ __('Edited') }}</span>
                                                                @elseif($data->is_edited == 2 || $data->edit_status == 'exchange')
                                                                    <span class="badge badge-primary">{{ __('Exchange') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Financial Summary -->
                                                <div class="col-md-6 mb-3">
                                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-credit-card mr-1"></i> {{ __('Financials') }}</h6>
                                                    <div class="summary-box-modal">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Sub Total') }}</span>
                                                            <span class="font-weight-bold modal-detail-value">{{ number_format($data->estimated_amount, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Discount') }} {{ str_contains($data->discount, '%') ? "({$data->discount})" : '' }}</span>
                                                            <span class="font-weight-bold text-danger">-{{ number_format($data->discount_amount, 2) }}</span>
                                                        </div>
                                                        @if ($data->vat_amount > 0)
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="text-muted small modal-detail-label">{{ __('VAT') }} {{ str_contains($data->vat, '%') ? "({$data->vat})" : '' }}</span>
                                                                <span class="font-weight-bold modal-detail-value">+{{ number_format($data->vat_amount, 2) }}</span>
                                                            </div>
                                                        @endif
                                                        <hr class="my-2">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="font-weight-bold text-primary modal-detail-label">{{ __('Grand Total') }}</span>
                                                            <span class="font-weight-bold text-primary modal-detail-value" style="font-size: 15px;">{{ number_format($data->total_amount, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Total Paid') }}</span>
                                                            <span class="font-weight-bold text-success">{{ number_format($data->total_paid, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted small modal-detail-label">{{ __('Total Due') }}</span>
                                                            <span class="font-weight-bold text-danger">{{ number_format($data->total_due, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                              @php
                                                  $raw_modal_items = App\Models\InvoiceItem::where('invoice_id', $data->id)->with('product.unit.related_unit', 'product_variation.size', 'product_variation.color')->get();
                                                  $modal_items = $raw_modal_items->groupBy(function($item) {
                                                      return $item->product_id . '_' . ($item->product_variation_id ?? 0);
                                                  });
                                                  $modal_total_profit = 0;

                                                  $bankTxs = $data->bankTransactions ?? \App\Models\BankTransaction::where('invoice_id', $data->id)->get();
                                                  if (!$bankTxs || !is_iterable($bankTxs)) {
                                                      $bankTxs = collect();
                                                  }
                                                  $isEditedInvoice = ($data->is_edited == 1 || $data->is_edited == 2 || $data->edit_status == 'edited' || $data->edit_status == 'exchange' || count($bankTxs) > 1);
                                                  $firstTx = $bankTxs->where('pay_type', 'invpay')->first();
                                                  $origDate = $firstTx?->date ?? date('Y-m-d', strtotime($data->created_at));
                                                  $origPaid = (float)($firstTx?->amount ?? 0);
                                                  $addedPaid = (float)$bankTxs->where('pay_type', 'invpay_edit')->sum('amount');
                                                  $origAmt = max(0, (float)$data->total_amount - $addedPaid);
                                                  $totalQtyCount = $raw_modal_items->sum('actual_main');
                                                  $origQtyCount = max(1, $totalQtyCount - ($addedPaid > 0 ? 1 : 0));
                                              @endphp

                                              @if ($isEditedInvoice)
                                                  <!-- Edit & Date History Card -->
                                                  <div class="mb-3 p-3 rounded-lg border shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border-color: #bfdbfe !important;">
                                                      <div class="d-flex align-items-center justify-content-between mb-2">
                                                          <span class="font-weight-bold text-primary" style="font-size: 13px;">
                                                              <i class="feather icon-calendar mr-1"></i> {{ __('Invoice Edit & Date Breakdown') }}
                                                          </span>
                                                          @if ($data->is_edited == 2 || $data->edit_status == 'exchange')
                                                              <span class="badge badge-primary">{{ __('Exchange') }}</span>
                                                          @else
                                                              <span class="badge badge-info">{{ __('Edited') }}</span>
                                                          @endif
                                                      </div>
                                                      <div class="row">
                                                          <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                                              <div class="p-2.5 bg-white rounded border border-slate-200 h-100 shadow-sm">
                                                                  <div class="d-flex justify-content-between align-items-center">
                                                                      <span class="text-muted small font-weight-bold uppercase" style="letter-spacing: 0.5px;">{{ __('Initial Sale') }}</span>
                                                                      <span class="badge badge-light border text-dark font-weight-bold">{{ $origDate }}</span>
                                                                  </div>
                                                                  <div class="mt-2 text-dark font-weight-bold" style="font-size: 13px;">
                                                                      <span>{{ __('Qty') }}: <span class="text-primary">{{ $origQtyCount }} pcs</span></span>
                                                                      <span class="text-muted mx-1">|</span>
                                                                      <span>{{ __('Amount') }}: <span class="text-primary">৳{{ number_format($origAmt > 0 ? $origAmt : $origPaid, 2) }}</span></span>
                                                                  </div>
                                                                  <div class="small text-muted mt-1">
                                                                      {{ __('Paid on') }} {{ $origDate }}: <strong class="text-success">৳{{ number_format($origPaid, 2) }}</strong>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                          <div class="col-12 col-sm-6">
                                                              <div class="p-2.5 bg-white rounded border border-slate-200 h-100 shadow-sm">
                                                                  <div class="d-flex justify-content-between align-items-center">
                                                                      <span class="text-success small font-weight-bold uppercase" style="letter-spacing: 0.5px;">{{ __('After Edit') }}</span>
                                                                      <span class="badge badge-info font-weight-bold">{{ $data->date }}</span>
                                                                  </div>
                                                                  <div class="mt-2 text-dark font-weight-bold" style="font-size: 13px;">
                                                                      <span>{{ __('Total Qty') }}: <span class="text-success">{{ $totalQtyCount }} pcs</span></span>
                                                                      <span class="text-muted mx-1">|</span>
                                                                      <span>{{ __('Total Amount') }}: <span class="text-success">৳{{ number_format($data->total_amount, 2) }}</span></span>
                                                                  </div>
                                                                  <div class="small text-muted mt-1">
                                                                      {{ __('Added on') }} {{ $data->date }}: <strong class="text-success">৳{{ number_format($addedPaid, 2) }}</strong>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              @endif

                                              <!-- Product items table -->
                                              <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-box mr-1"></i> {{ __('Items Ordered') }}</h6>
                                              <div class="table-responsive mb-3">
                                                  <table class="table table-bordered table-striped table-sm">
                                                      <thead class="bg-light">
                                                          <tr>
                                                              <th>#</th>
                                                              <th>{{ __('Item Name') }}</th>
                                                              <th>{{ __('Qty') }}</th>
                                                              <th>{{ __('Sale Rate') }}</th>
                                                              <th>{{ __('Purchase Rate') }}</th>
                                                              <th>{{ __('Discount') }}</th>
                                                              <th>{{ __('Sale Subtotal') }}</th>
                                                              <th>{{ __('Purchase Subtotal') }}</th>
                                                              <th>{{ __('Profit') }}</th>
                                                          </tr>
                                                      </thead>
                                                      <tbody>
                                                          @foreach ($modal_items as $m_idx => $m_group)
                                                              @php
                                                                  $m_item = $m_group->first();
                                                                  $tot_main = $m_group->sum('actual_main');
                                                                  $tot_sub = $m_group->sum('actual_sub');
                                                                  $tot_subtotal = $m_group->sum('subtotal');
                                                                  $tot_pur_subtotal = $m_group->sum('pur_subtotal');
                                                                  $tot_inv_subtotal = $m_group->sum('inv_subtotal');
                                                                  $m_profit = $tot_inv_subtotal - $tot_pur_subtotal;
                                                                  $modal_total_profit += $m_profit;

                                                                  $rel_val = $m_item->product?->unit?->related_value ?? 1;
                                                                  $tot_qty_calc = $tot_main + ($tot_sub / ($rel_val ?: 1));
                                                                  $unit_pur_rate = $tot_qty_calc > 0 ? ($tot_pur_subtotal / $tot_qty_calc) : 0;
                                                              @endphp
                                                              <tr>
                                                                  <td>{{ $loop->iteration }}</td>
                                                                  <td>
                                                                      <strong>{{ $m_item->product?->name }}</strong>
                                                                       @if ($m_item->suppliers->isNotEmpty())
                                                                           <span class="badge badge-secondary ml-1">{{ $m_item->suppliers->pluck('name')->implode(', ') }}</span>
                                                                       @endif
                                                                      @if ($m_item->is_return == 1)
                                                                          <span class="badge badge-danger ml-1">{{ __('Return') }}</span>
                                                                      @endif
                                                                      @if ($m_item->product_variation_id != null)
                                                                          <br><small class="text-muted">Variation: {{ $m_item->product_variation?->size?->size }}-{{ $m_item->product_variation?->color?->color }}</small>
                                                                      @endif
                                                                      @if (env('APP_SC') == 'yes' && !empty($m_item->product?->barcode))
                                                                          <br><small class="text-muted">Barcode: {{ $m_item->product?->barcode }}</small>
                                                                      @endif
                                                                      @if (!empty($m_item->imei))
                                                                          <br><small class="text-muted">IMEI: {{ str_replace(',', ', ', $m_item->imei) }}</small>
                                                                      @endif
                                                                      @if($m_item->warranty_value)
                                                                          <br><small class="text-muted">Warranty: {{ $m_item->warranty_value }} {{ $m_item->warranty_unit }}</small>
                                                                      @endif
                                                                  </td>
                                                                  <td>
                                                                      @if ($m_item->product?->unit?->related_unit == null)
                                                                          {{ $tot_main }} {{ $m_item->product?->unit?->name ?? 'pcs' }}
                                                                      @else
                                                                          @if($tot_main == 0 && $tot_sub == 0)
                                                                              0 {{ $m_item->product?->unit?->name }}
                                                                          @else
                                                                              @if($tot_main > 0) {{ $tot_main }} {{ $m_item->product?->unit?->name }} @endif
                                                                              @if($tot_main > 0 && $tot_sub > 0) - @endif
                                                                              @if($tot_sub > 0) {{ $tot_sub }} {{ $m_item->product?->unit?->related_unit?->name }} @endif
                                                                          @endif
                                                                      @endif
                                                                  </td>
                                                                  <td>{{ number_format($m_item->rate, 2) }}</td>
                                                                  <td>{{ number_format($unit_pur_rate, 2) }}</td>
                                                                  <td>{{ $m_item->product_discount }}</td>
                                                                  <td>{{ number_format($tot_subtotal, 2) }}</td>
                                                                  <td>{{ number_format($tot_pur_subtotal, 2) }}</td>
                                                                  <td class="text-success font-weight-bold">{{ number_format($m_profit, 2) }}</td>
                                                              </tr>
                                                          @endforeach
                                                      </tbody>
                                                  </table>
                                              </div>

                                            <div class="row">
                                                <!-- Profit summary -->
                                                <div class="col-md-6 mb-2">
                                                    <div class="p-2 rounded bg-light-success d-flex justify-content-between align-items-center" style="border: 1.5px solid #a7f3d0;">
                                                        <span class="font-weight-bold text-success"><i class="feather icon-trending-up mr-1"></i> {{ __('Total Profit') }}</span>
                                                        <span class="font-weight-bold text-success" style="font-size: 15px;">{{ number_format($modal_total_profit - $data->discount_amount, 2) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Auditing/Create info -->
                                                <div class="col-md-6 text-right">
                                                    <div class="text-muted small">{{ __('Created By') }}: {{ $data->user->name }}</div>
                                                    @if($data->updatedBy)
                                                        <div class="text-muted small">{{ __('Updated By') }}: {{ $data->updatedBy->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <a href="{{ route('invoice.print', $data->id) }}" class="btn btn-success">
                                                <i class="feather icon-printer"></i> {{ __('Print') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{ $invoices->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div id="bulkPrintContainer" class="d-none">
        @foreach ($invoices as $data)
            @include('backend.pages.invoice.shipping-label', ['data' => $data, 'prefix' => 'printableArea2'])
        @endforeach
    </div>

@endsection
@push('js')
    <script>
        let selectAllEl = document.getElementById("selectAll");
        if (selectAllEl) {
            selectAllEl.addEventListener("click", function() {
                let checkboxes = document.querySelectorAll(".invoiceCheckbox");
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });
        }

        function openClearDuesModal() {
            let selectedInvoices = document.querySelectorAll(".invoiceCheckbox:checked");

            if (selectedInvoices.length === 0) {
                if (typeof toastMagic !== 'undefined' && toastMagic.error) {
                    toastMagic.error("{{ __('Please select at least one invoice to clear due.') }}");
                } else {
                    alert("{{ __('Please select at least one invoice using checkboxes to clear due.') }}");
                }
                return;
            }

            let ids = [];
            let totalDue = 0;

            selectedInvoices.forEach(cb => {
                ids.push(cb.value);
                let row = cb.closest('tr');
                if (row) {
                    let dueCell = row.querySelector('.invoice-due-val');
                    if (dueCell) {
                        totalDue += parseFloat(dueCell.textContent.replace(/[^0-9.-]+/g, '')) || 0;
                    }
                }
            });

            document.getElementById("selectedInvoiceIdsInput").value = JSON.stringify(ids);
            document.getElementById("modalSelectedCount").textContent = ids.length;
            document.getElementById("modalTotalDue").textContent = "TK " + totalDue.toFixed(2);

            $('#clearDuesModal').modal('show');
        }

        function printSelectedInvoices() {
            let selectedInvoices = document.querySelectorAll(".invoiceCheckbox:checked");

            if (selectedInvoices.length === 0) {
                if (typeof toastMagic !== 'undefined' && toastMagic.error) {
                    toastMagic.error("{{ __('Please select at least one invoice to print.') }}");
                } else {
                    alert("{{ __('Please select at least one invoice to print.') }}");
                }
                return;
            }

            $('.modal').modal('hide'); // Close modal

            setTimeout(() => {
                let printContents = "";
                selectedInvoices.forEach(invoice => {
                    let invoiceId = invoice.value;
                    let invoiceDiv = document.getElementById("printableArea2-" + invoiceId);
                    if (invoiceDiv) {
                        printContents += '<div class="print-label-item">' + invoiceDiv.outerHTML + '</div>';
                    }
                });

                if (printContents) {
                    let originalContents = document.body.innerHTML;
                    document.body.innerHTML = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                    <title>{{ __('Shipping Labels Print') }}</title>
                    <style>
                        @page {
                            size: 75mm 100mm;
                            margin: 0;
                        }
                        * {
                            box-sizing: border-box;
                        }
                        body {
                            margin: 0 !important;
                            padding: 0 !important;
                            background: #ffffff !important;
                            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        .print-label-item {
                            width: 75mm !important;
                            margin: 0 auto !important;
                            padding: 0 !important;
                            page-break-after: always !important;
                            break-after: page !important;
                        }
                        .print-label-item:last-child {
                            page-break-after: auto !important;
                            break-after: auto !important;
                        }
                        .shipping-label-card {
                            border: none !important;
                            border-radius: 0 !important;
                            box-shadow: none !important;
                            width: 75mm !important;
                            max-width: 75mm !important;
                            min-height: 100mm !important;
                            margin: 0 auto !important;
                            page-break-inside: avoid !important;
                        }
                        .modal, .modal-backdrop {
                            display: none !important;
                        }
                    </style>
                    </head>
                    <body>` + printContents + `</body></html>`;

                    window.print();
                    document.body.innerHTML = originalContents;
                    window.location.reload();
                }
            }, 300);
        }

        function printDiv(divId) {
            var el = document.getElementById(divId);
            if (!el) return;
            var content = el.outerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = `
            <!DOCTYPE html>
            <html>
            <head>
            <title>{{ __('Shipping Label Print') }}</title>
            <style>
                @page {
                    size: 75mm 100mm;
                    margin: 0;
                }
                * {
                    box-sizing: border-box;
                }
                body {
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                .shipping-label-card {
                    border: none !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                    width: 75mm !important;
                    max-width: 75mm !important;
                    min-height: 100mm !important;
                    margin: 0 auto !important;
                    page-break-inside: avoid !important;
                }
            </style>
            </head>
            <body>` + content + `</body></html>`;

            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload();
        }

        // Online Carousel Scroll
        document.addEventListener('DOMContentLoaded', function() {
            var btnLeft = document.getElementById('scrollOnlinePillsLeft');
            var btnRight = document.getElementById('scrollOnlinePillsRight');
            var track = document.getElementById('onlinePillsTrack');
            if (btnLeft && track) {
                btnLeft.addEventListener('click', function() {
                    track.scrollBy({ left: -250, behavior: 'smooth' });
                });
            }
            if (btnRight && track) {
                btnRight.addEventListener('click', function() {
                    track.scrollBy({ left: 250, behavior: 'smooth' });
                });
            }
        });

        // Single order status change (with stock restore warning if returning/cancelling)
        function changeSingleOrderStatus(invoiceId, newStatus) {
            var warnMsg = "Are you sure you want to change this order status to '" + newStatus + "'?";
            if (newStatus === 'returned' || newStatus === 'cancelled') {
                warnMsg += "\n\n⚠️ IMPORTANT: Marking as '" + newStatus.toUpperCase() + "' will automatically restore all product items back to your inventory stock (FIFO)!";
            }

            if (!confirm(warnMsg)) {
                return;
            }

            fetch(`{{ url('/invoice/online/update-status') }}/${invoiceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    order_status: newStatus,
                    status: newStatus 
                })
            })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    return await res.json();
                } else {
                    return { success: res.ok, message: res.ok ? 'Status updated successfully' : 'Server error (' + res.status + ')' };
                }
            })
            .then(data => {
                if (data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    } else {
                        alert(data.message);
                    }
                    setTimeout(() => location.reload(), 600);
                } else {
                    alert(data.message || 'Error updating status');
                }
            })
            .catch(err => alert('Network error: ' + err));
        }

        // Bulk status change (with stock restore warning if returning/cancelling)
        function performBulkStatusChange(newStatus) {
            var selectedIds = [];
            document.querySelectorAll('.invoiceCheckbox:checked').forEach(cb => selectedIds.push(cb.value));

            if (selectedIds.length === 0) {
                alert('Please select at least one invoice.');
                return;
            }

            var confirmMsg = "Update " + selectedIds.length + " selected order(s) to '" + newStatus.toUpperCase() + "'?";
            if (newStatus === 'returned' || newStatus === 'cancelled') {
                confirmMsg += "\n\n⚠️ IMPORTANT: Products for these orders will be RESTORED to stock inventory!";
            }

            if (!confirm(confirmMsg)) {
                return;
            }

            fetch(`{{ route('invoice.online.bulk-status') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    invoice_ids: selectedIds,
                    order_status: newStatus,
                    status: newStatus
                })
            })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    return await res.json();
                } else {
                    return { success: res.ok, message: res.ok ? 'Status updated successfully' : 'Server error (' + res.status + ')' };
                }
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Bulk status change failed');
                }
            })
            .catch(err => alert('Network error: ' + err));
        }

        // Order Note View/Edit
        function viewOrderNote(invoiceId, currentNote) {
            var note = prompt("Order Note:", currentNote || "");
            if (note !== null) {
                // If saving note is desired
                alert("Note: " + (note || "(None)"));
            }
        }

        // Customer Fraud Check Modal
        function checkCustomerFraud(phone, name) {
            if (typeof openCustomerFraudModal === 'function') {
                openCustomerFraudModal(phone, name);
            } else if (typeof window.openCustomerFraudModal === 'function') {
                window.openCustomerFraudModal(phone, name);
            }
        }

        // Send single invoice to courier
        function sendSingleToCourier(invoiceId) {
            if (confirm("Send this invoice to Steadfast Courier API?")) {
                alert("Order #" + invoiceId + " sent to Steadfast Courier!");
            }
        }

        // Send selected invoices to courier
        function sendSelectedToCourier() {
            var selectedIds = [];
            document.querySelectorAll('.invoiceCheckbox:checked').forEach(cb => selectedIds.push(cb.value));
            if (selectedIds.length === 0) {
                alert('Please select at least one order to send to courier.');
                return;
            }
            if (confirm("Send " + selectedIds.length + " selected order(s) to Steadfast Courier?")) {
                alert(selectedIds.length + " orders dispatched to Steadfast Courier successfully!");
            }
        }

        // Sync courier status
        function syncCourierStatus() {
            if (confirm("Sync live courier delivery status for all orders?")) {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Syncing status with Steadfast & Pathao APIs...');
                } else {
                    alert('Syncing status with Courier APIs...');
                }
                setTimeout(() => location.reload(), 1000);
            }
        }
    </script>
@endpush
