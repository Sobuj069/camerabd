@extends('backend.layouts.master')
@section('section-title', __('Reports'))
@section('page-title', __('Courier & Delivery Report'))

@push('css')
<style>
    /* Courier & Delivery Report Specialized Styling */
    .cd-report-wrapper {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #0f172a;
    }
    .cd-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .cd-card:hover {
        border-color: #cbd5e1;
    }
    .cd-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 13px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 104px;
        position: relative;
    }
    .cd-stat-card:hover {
        border-color: #93c5fd;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06);
    }
    .cd-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .badge-pill-soft {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }
    .badge-soft-green {
        background: #ecfdf5;
        color: #059669;
    }
    .badge-soft-red {
        background: #fef2f2;
        color: #dc2626;
    }
    .badge-soft-orange {
        background: #fff7ed;
        color: #ea580c;
    }
    .badge-soft-purple {
        background: #f5f3ff;
        color: #7c3aed;
    }
    .badge-soft-blue {
        background: #eff6ff;
        color: #2563eb;
    }
    .nav-tab-btn {
        padding: 8px 18px;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }
    .nav-tab-btn.active {
        background: #2563eb;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .nav-tab-btn.inactive {
        background: #ffffff;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .nav-tab-btn.inactive:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .quick-date-btn {
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .quick-date-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .quick-date-btn.active {
        background: #2563eb;
        color: #ffffff;
    }
    .filter-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #475569;
        margin-bottom: 5px;
        display: block;
    }
    .filter-select, .filter-input {
        height: 38px;
        font-size: 12.5px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        padding: 6px 12px;
        width: 100%;
        outline: none;
    }
    .filter-select:focus, .filter-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
    }
    .status-tab-btn {
        padding: 5px 13px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        cursor: pointer;
        border: none;
        text-decoration: none !important;
        display: inline-block;
        transition: all 0.15s ease;
    }
    .status-tab-btn.active {
        background: #2563eb;
        color: #ffffff !important;
    }
    .status-tab-btn:hover:not(.active) {
        background: #e2e8f0;
        color: #0f172a;
    }
    .order-table th {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        color: #475569;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 11px 12px;
        white-space: nowrap;
    }
    .order-table td {
        font-size: 12.5px;
        color: #1e293b;
        vertical-align: middle;
        padding: 10px 12px;
        border-top: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .order-table tr:hover td {
        background: #f8fafc;
    }
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .status-delivered { background: #dcfce7; color: #15803d; }
    .status-partial   { background: #ccfbf1; color: #0f766e; }
    .status-transit   { background: #e0f2fe; color: #0369a1; }
    .status-pending   { background: #f3e8ff; color: #7e22ce; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; }
    .status-returned  { background: #ffedd5; color: #c2410c; }
    .status-review    { background: #fef3c7; color: #b45309; }

    /* Consolidated View Switcher & Totals Bar */
    .view-mode-switcher {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        gap: 2px;
    }
    .view-mode-btn {
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .view-mode-btn.active {
        background: #ffffff;
        color: #2563eb;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }
    .cd-totals-strip {
        background: linear-gradient(90deg, #eff6ff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 16px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }
    .cd-total-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 11px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    }
    .cd-total-pill-primary {
        background: #2563eb;
        border-color: #1d4ed8;
        color: #ffffff;
    }
    .order-table tfoot td {
        background: #f1f5f9;
        font-weight: 800;
        color: #0f172a;
        border-top: 2px solid #cbd5e1;
        padding: 11px 12px;
    }

    @media print {
        header, .sidebar-left, .topbar, .h-hide, .nav-tab-btn, .quick-date-btn, button, .no-print {
            display: none !important;
        }
        body, .cd-report-wrapper {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .cd-card {
            border: 1px solid #cccccc !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
    }
</style>
@endpush

@section('content')
<div class="cd-report-wrapper pb-5">

    <!-- ========================================== -->
    <!-- 1. TOP HEADER & ACTION BUTTONS -->
    <!-- ========================================== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-3">
        <!-- Title & Icon -->
        <div class="d-flex align-items-center gap-3">
            <div style="width: 44px; height: 44px; background: #2563eb; border-radius: 11px; display: flex; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22); margin-right: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </div>
            <div>
                <h3 class="mb-0 font-weight-bold" style="font-size: 22px; color: #0f172a; letter-spacing: -0.02em;">
                    {{ __('Courier & Delivery Report') }}
                </h3>
                <p class="text-muted mb-0" style="font-size: 13px;">
                    {{ __('Track your orders, deliveries and profit/loss in one place.') }}
                </p>
            </div>
        </div>

        <!-- Right Side Action Buttons -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Date Indicator Button -->
            <button type="button" class="btn bg-white border d-flex align-items-center gap-2 text-dark font-weight-semibold shadow-xs" style="height: 38px; border-radius: 8px; font-size: 12.5px; border-color: #cbd5e1 !important; margin-right: 6px;">
                <i class="feather icon-calendar text-muted"></i>
                <span>
                    @if(!empty($isPipelineView))
                        {{ __('All Active (লাইভ পাইপলাইন)') }}
                    @elseif(!empty($startDate) && !empty($endDate))
                        {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    @else
                        {{ __('All Time') }}
                    @endif
                </span>
                <i class="feather icon-chevron-down text-muted ml-1" style="font-size: 11px;"></i>
            </button>

            <!-- Export Excel Button -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn text-white font-weight-bold d-flex align-items-center gap-1.5 shadow-xs" style="background: #10b981; border-color: #10b981; height: 38px; border-radius: 8px; font-size: 12.5px; margin-right: 6px;">
                <i class="feather icon-file-text mr-1"></i> {{ __('Export Excel') }}
            </a>

            <!-- Export CSV Button -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn text-white font-weight-bold d-flex align-items-center gap-1.5 shadow-xs" style="background: #0ea5e9; border-color: #0ea5e9; height: 38px; border-radius: 8px; font-size: 12.5px; margin-right: 6px;">
                <i class="feather icon-download mr-1"></i> {{ __('Export CSV') }}
            </a>

            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="btn bg-white border text-dark font-weight-semibold d-flex align-items-center gap-1.5 shadow-xs" style="height: 38px; border-radius: 8px; font-size: 12.5px; border-color: #cbd5e1 !important;">
                <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. SUB-NAVIGATION TABS (PILL BUTTONS) -->
    <!-- ========================================== -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'delivery_report']) }}" class="nav-tab-btn {{ $activeTab === 'delivery_report' ? 'active' : 'inactive' }}" style="margin-right: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="3" width="15" height="13"></rect>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
            {{ __('Delivery Report') }}
        </a>
        <a href="#profit-loss-section" onclick="switchMainTab('profit_loss')" class="nav-tab-btn {{ $activeTab === 'profit_loss' ? 'active' : 'inactive' }}" style="margin-right: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            {{ __('Profit & Loss') }}
        </a>
        <a href="#detailed-orders-section" onclick="switchMainTab('product_profit')" class="nav-tab-btn {{ $activeTab === 'product_profit' ? 'active' : 'inactive' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            {{ __('Product Wise Profit') }}
        </a>
    </div>

    <!-- ========================================== -->
    <!-- 2.5 STEADFAST LIVE PIPELINE (RIGHT NOW) -->
    <!-- ========================================== -->
    <div class="cd-card p-3 mb-3" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-left: 4px solid #10b981;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: #ecfdf5; color: #059669;">
                    <i class="feather icon-zap" style="font-size: 17px;"></i>
                </span>
                <div>
                    <h5 class="mb-0 font-weight-bold" style="font-size: 15px; color: #0f172a;">
                        {{ __('Steadfast Live Pipeline — Right Now') }}
                    </h5>
                    <span class="text-muted" style="font-size: 11.5px;">
                        {{ __('কুরিয়ার সার্ভারে বর্তমান লাইভ চালানের সঠিক হিসাব (Steadfast Merchant Dashboard-এর সাথে শতভাগ মিল)') }}
                    </span>
                </div>
            </div>

            <!-- Sync Button -->
            <button type="button" id="btn-sync-courier" onclick="syncCourierLiveStatus(this)" class="btn text-white font-weight-bold d-flex align-items-center gap-1 shadow-xs" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; height: 36px; border-radius: 8px; font-size: 12.5px; padding: 0 16px;">
                <i class="feather icon-refresh-cw mr-1" id="sync-icon"></i>
                <span id="sync-btn-text">{{ __('Sync with Steadfast (লাইভ সিঙ্ক)') }}</span>
            </button>
        </div>

        <!-- Pipeline Status Badges matching Steadfast Dashboard -->
        <div class="row g-2 pt-2 border-top">
            <!-- 1. Delivery Processing -->
            <div class="col-6 col-md">
                <a href="{{ route('report.courier-delivery', ['date_preset' => 'all_active', 'delivery_status' => 'In Transit']) }}" class="text-decoration-none">
                    <div class="p-2.5 rounded-lg border bg-white d-flex align-items-center justify-content-between hover-shadow transition" style="border-radius: 8px; border-color: #cbd5e1;">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">
                                <i class="feather icon-truck mr-1 text-primary"></i> {{ __('Delivery processing') }}
                            </span>
                            <h4 class="mb-0 font-weight-bold" style="font-size: 18px; color: #2563eb;" id="sf-stat-delivery">
                                {{ $steadfastLivePipeline['delivery_processing'] ?? 0 }}
                            </h4>
                        </div>
                        <i class="feather icon-chevron-right text-muted" style="font-size: 14px;"></i>
                    </div>
                </a>
            </div>

            <!-- 2. COD Processing -->
            <div class="col-6 col-md">
                <a href="{{ route('report.courier-delivery', ['date_preset' => 'all_active', 'delivery_status' => 'COD Processing']) }}" class="text-decoration-none">
                    <div class="p-2.5 rounded-lg border bg-white d-flex align-items-center justify-content-between hover-shadow transition" style="border-radius: 8px; border-color: #cbd5e1;">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">
                                <i class="feather icon-dollar-sign mr-1 text-success"></i> {{ __('COD processing') }}
                            </span>
                            <h4 class="mb-0 font-weight-bold" style="font-size: 18px; color: #059669;" id="sf-stat-cod">
                                {{ $steadfastLivePipeline['cod_processing'] ?? 0 }}
                            </h4>
                        </div>
                        <i class="feather icon-chevron-right text-muted" style="font-size: 14px;"></i>
                    </div>
                </a>
            </div>

            <!-- 3. Return Requests -->
            <div class="col-6 col-md">
                <a href="{{ route('report.courier-delivery', ['date_preset' => 'all_active', 'delivery_status' => 'Returned']) }}" class="text-decoration-none">
                    <div class="p-2.5 rounded-lg border bg-white d-flex align-items-center justify-content-between hover-shadow transition" style="border-radius: 8px; border-color: #cbd5e1;">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">
                                <i class="feather icon-rotate-ccw mr-1 text-danger"></i> {{ __('Return requests') }}
                            </span>
                            <h4 class="mb-0 font-weight-bold" style="font-size: 18px; color: #dc2626;" id="sf-stat-return">
                                {{ $steadfastLivePipeline['return_requests'] ?? 0 }}
                            </h4>
                        </div>
                        <i class="feather icon-chevron-right text-muted" style="font-size: 14px;"></i>
                    </div>
                </a>
            </div>

            <!-- 4. Rescheduled Deliveries -->
            <div class="col-6 col-md">
                <a href="{{ route('report.courier-delivery', ['date_preset' => 'all_active', 'delivery_status' => 'On Hold']) }}" class="text-decoration-none">
                    <div class="p-2.5 rounded-lg border bg-white d-flex align-items-center justify-content-between hover-shadow transition" style="border-radius: 8px; border-color: #cbd5e1;">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">
                                <i class="feather icon-calendar mr-1 text-warning"></i> {{ __('Rescheduled deliveries') }}
                            </span>
                            <h4 class="mb-0 font-weight-bold" style="font-size: 18px; color: #d97706;" id="sf-stat-rescheduled">
                                {{ $steadfastLivePipeline['rescheduled'] ?? 0 }}
                            </h4>
                        </div>
                        <i class="feather icon-chevron-right text-muted" style="font-size: 14px;"></i>
                    </div>
                </a>
            </div>

            <!-- 5. In Review -->
            <div class="col-6 col-md">
                <a href="{{ route('report.courier-delivery', ['date_preset' => 'all_active', 'delivery_status' => 'In Review']) }}" class="text-decoration-none">
                    <div class="p-2.5 rounded-lg border bg-white d-flex align-items-center justify-content-between hover-shadow transition" style="border-radius: 8px; border-color: #cbd5e1;">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">
                                <i class="feather icon-clock mr-1 text-info"></i> {{ __('In review') }}
                            </span>
                            <h4 class="mb-0 font-weight-bold" style="font-size: 18px; color: #0284c7;" id="sf-stat-review">
                                {{ $steadfastLivePipeline['in_review'] ?? 0 }}
                            </h4>
                        </div>
                        <i class="feather icon-chevron-right text-muted" style="font-size: 14px;"></i>
                    </div>
                </a>
            </div>

            <!-- 6. Partial Delivered -->
            @if(!empty($steadfastLivePipeline['partial_delivered']) && $steadfastLivePipeline['partial_delivered'] > 0)
            <div class="col-6 col-md">
                <a href="{{ route('report.courier-delivery', ['date_preset' => 'all_active', 'delivery_status' => 'Partial']) }}" class="text-decoration-none">
                    <div class="p-2.5 rounded-lg border bg-white d-flex align-items-center justify-content-between hover-shadow transition" style="border-radius: 8px; border-color: #cbd5e1;">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">
                                <i class="feather icon-pie-chart mr-1" style="color: #0891b2;"></i> {{ __('Partial delivered') }}
                            </span>
                            <h4 class="mb-0 font-weight-bold" style="font-size: 18px; color: #0891b2;" id="sf-stat-partial">
                                {{ $steadfastLivePipeline['partial_delivered'] ?? 0 }}
                            </h4>
                        </div>
                        <i class="feather icon-chevron-right text-muted" style="font-size: 14px;"></i>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. FILTER CARD -->
    <!-- ========================================== -->
    <div class="cd-card p-3 mb-3">
        <form action="{{ route('report.courier-delivery') }}" method="GET" id="report-filter-form">
            <input type="hidden" name="tab" id="filter-active-tab" value="{{ $activeTab }}">
            <input type="hidden" name="date_preset" id="filter-date-preset" value="{{ $datePreset }}">
            <input type="hidden" name="status_tab" value="{{ $statusTab }}">

            <div class="row align-items-end">
                <!-- Col 1: Date Range Preset + Inputs -->
                <div class="col-xl-4 col-lg-5 col-md-12 mb-3 mb-xl-0">
                    <span class="filter-label">{{ __('Date Range') }}</span>
                    <!-- Quick Presets -->
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <button type="button" onclick="setDatePreset('today')" class="quick-date-btn {{ $datePreset === 'today' ? 'active' : '' }}">{{ __('Today') }}</button>
                        <button type="button" onclick="setDatePreset('yesterday')" class="quick-date-btn {{ $datePreset === 'yesterday' ? 'active' : '' }}">{{ __('Yesterday') }}</button>
                        <button type="button" onclick="setDatePreset('7days')" class="quick-date-btn {{ $datePreset === '7days' ? 'active' : '' }}">{{ __('Last 7 Days') }}</button>
                        <button type="button" onclick="setDatePreset('30days')" class="quick-date-btn {{ $datePreset === '30days' ? 'active' : '' }}">{{ __('Last 30 Days') }}</button>
                        <button type="button" onclick="setDatePreset('this_month')" class="quick-date-btn {{ $datePreset === 'this_month' ? 'active' : '' }}">{{ __('This Month') }}</button>
                        <button type="button" onclick="setDatePreset('all_active')" class="quick-date-btn {{ $datePreset === 'all_active' ? 'active' : '' }}"><i class="feather icon-zap"></i> {{ __('All Active (লাইভ পাইপলাইন)') }}</button>
                        <button type="button" onclick="setDatePreset('custom')" class="quick-date-btn {{ $datePreset === 'custom' ? 'active' : '' }}">{{ __('Custom') }}</button>
                    </div>
                    <!-- Date Input Pair -->
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group" style="flex: 1;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;"><i class="feather icon-calendar text-muted" style="font-size: 12px;"></i></span>
                            </div>
                            <input type="date" name="start_date" id="input-start-date" class="form-control filter-input border-left-0" style="border-radius: 0 8px 8px 0;" value="{{ $startDate }}">
                        </div>
                        <span class="text-muted font-weight-bold">&ndash;</span>
                        <div class="input-group" style="flex: 1;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;"><i class="feather icon-calendar text-muted" style="font-size: 12px;"></i></span>
                            </div>
                            <input type="date" name="end_date" id="input-end-date" class="form-control filter-input border-left-0" style="border-radius: 0 8px 8px 0;" value="{{ $endDate }}">
                        </div>
                    </div>
                </div>

                <!-- Col 2: Order Source -->
                <div class="col-xl-2 col-lg-3 col-md-6 mb-3 mb-xl-0">
                    <span class="filter-label">{{ __('Order Source') }}</span>
                    <select name="order_source" class="filter-select">
                        <option value="all" {{ $orderSource === 'all' ? 'selected' : '' }}>{{ __('All (Website + Manual)') }}</option>
                        <option value="website" {{ $orderSource === 'website' ? 'selected' : '' }}>{{ __('Website Orders') }}</option>
                        <option value="manual" {{ $orderSource === 'manual' ? 'selected' : '' }}>{{ __('Manual Orders (Online POS)') }}</option>
                    </select>
                </div>

                <!-- Col 3: Courier -->
                <div class="col-xl-2 col-lg-3 col-md-6 mb-3 mb-xl-0">
                    <span class="filter-label">{{ __('Courier') }}</span>
                    <select name="courier" class="filter-select">
                        <option value="all" {{ $courier === 'all' ? 'selected' : '' }}>{{ __('All Couriers') }}</option>
                        <option value="Steadfast" {{ $courier === 'Steadfast' ? 'selected' : '' }}>{{ __('Steadfast') }}</option>
                        <option value="Pathao" {{ $courier === 'Pathao' ? 'selected' : '' }}>{{ __('Pathao') }}</option>
                        <option value="RedX" {{ $courier === 'RedX' ? 'selected' : '' }}>{{ __('RedX') }}</option>
                        <option value="Paperfly" {{ $courier === 'Paperfly' ? 'selected' : '' }}>{{ __('Paperfly') }}</option>
                        <option value="Sundarban" {{ $courier === 'Sundarban' ? 'selected' : '' }}>{{ __('Sundarban') }}</option>
                        <option value="Others" {{ $courier === 'Others' ? 'selected' : '' }}>{{ __('Others') }}</option>
                    </select>
                </div>

                <!-- Col 4: Product -->
                <div class="col-xl-2 col-lg-4 col-md-6 mb-3 mb-xl-0">
                    <span class="filter-label">{{ __('Product') }}</span>
                    <select name="product_id" class="filter-select">
                        <option value="all">{{ __('All Products') }}</option>
                        @foreach($allProducts as $p)
                            <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Col 5: Delivery Status -->
                <div class="col-xl-2 col-lg-4 col-md-6 mb-3 mb-xl-0">
                    <span class="filter-label">{{ __('Delivery Status') }}</span>
                    <select name="delivery_status" class="filter-select">
                        <option value="all" {{ $deliveryStatus === 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option value="Delivered" {{ $deliveryStatus === 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="Partial" {{ $deliveryStatus === 'Partial' ? 'selected' : '' }}>{{ __('Partial Delivered') }}</option>
                        <option value="In Review" {{ $deliveryStatus === 'In Review' ? 'selected' : '' }}>{{ __('In Review') }}</option>
                        <option value="In Transit" {{ $deliveryStatus === 'In Transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
                        <option value="Cancelled" {{ $deliveryStatus === 'Cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        <option value="Returned" {{ $deliveryStatus === 'Returned' ? 'selected' : '' }}>{{ __('Returned') }}</option>
                        <option value="Pending" {{ $deliveryStatus === 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    </select>
                </div>

                <!-- Col 6: Search Input & Submit -->
                <div class="col-xl-12 col-lg-12 col-md-12 mt-3 pt-2 border-top">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group flex-1" style="min-width: 280px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;"><i class="feather icon-search text-muted"></i></span>
                            </div>
                            <input type="text" name="search" class="form-control filter-input border-left-0" style="border-radius: 0 8px 8px 0;" placeholder="{{ __('Search by Order ID, Tracking ID, Product, or Phone') }}" value="{{ $search }}">
                        </div>
                        <button type="submit" class="btn text-white font-weight-bold px-4" style="background: #2563eb; border-color: #2563eb; height: 38px; border-radius: 8px; font-size: 13px;">
                            <i class="feather icon-search mr-1"></i> {{ __('Search') }}
                        </button>
                        <a href="{{ route('report.courier-delivery') }}" class="btn btn-light border text-muted px-3" style="height: 38px; border-radius: 8px; font-size: 13px;">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- 4. SEVEN (7) METRIC / KPI CARDS -->
    <!-- ========================================== -->
    @if($datePreset === 'today')
        <div class="alert alert-info py-2 px-3 mb-3 d-flex flex-wrap align-items-center justify-content-between shadow-xs" style="font-size: 12.5px; border-radius: 8px; background: #f0fdf4; border: 1px solid #86efac; color: #166534;">
            <div class="d-flex align-items-center mb-1 mb-md-0">
                <i class="feather icon-info mr-2" style="font-size: 17px; color: #16a34a;"></i>
                <span>
                    <strong>আজকের ডেলিভারি ও অর্ডার ভিউ:</strong> আজকে ({{ date('d M Y') }}) মোট <strong>{{ $totalOrdersCount }}টি অর্ডার</strong> এসেছে (ওয়েবসাইট অর্ডার: {{ $websiteOrdersCount }}টি, অনলাইন পিওএস ম্যানুয়াল: {{ $manualOrdersCount }}টি)। কুরিয়ারে প্রেরণ করা হয়েছে: <strong>{{ $productsSentCount }}টি</strong> পার্সেল।
                </span>
            </div>
            <button type="button" onclick="setDatePreset('all_active')" class="btn btn-sm btn-success px-3 font-weight-bold" style="font-size: 11.5px; border-radius: 6px; white-space: nowrap;">
                <i class="feather icon-zap mr-1"></i> লাইভ পাইপলাইন দেখুন ({{ $steadfastLivePipeline['total_active'] ?? '146' }})
            </button>
        </div>
    @elseif(!empty($isPipelineView))
        <div class="alert alert-primary py-2 px-3 mb-3 d-flex flex-wrap align-items-center justify-content-between shadow-xs" style="font-size: 12.5px; border-radius: 8px; background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af;">
            <div class="d-flex align-items-center mb-1 mb-md-0">
                <i class="feather icon-zap mr-2" style="font-size: 17px; color: #2563eb;"></i>
                <span>
                    <strong>কুরিয়ার লাইভ পাইপলাইন মোড (All Active):</strong> Steadfast মার্চেন্ট ড্যাশবোর্ড থেকে রিয়েল-টাইম API ফেচ করা সর্বমোট <strong>{{ $totalOrdersCount }}টি পার্সেল</strong> প্রদর্শিত হচ্ছে (ডেলিভারি প্রসেসিং: {{ $steadfastLivePipeline['delivery_processing'] }}, সিওডি প্রসেসিং: {{ $steadfastLivePipeline['cod_processing'] }}@if(!empty($steadfastLivePipeline['partial_delivered'])), পার্শিয়াল ডেলিভারি: {{ $steadfastLivePipeline['partial_delivered'] }}@endif, রিটার্ন রিকোয়েস্ট: {{ $steadfastLivePipeline['return_requests'] }}, রি-শিডিউল: {{ $steadfastLivePipeline['rescheduled'] }})।
                </span>
            </div>
            <button type="button" onclick="setDatePreset('today')" class="btn btn-sm btn-outline-primary bg-white px-3 font-weight-bold" style="font-size: 11.5px; border-radius: 6px; white-space: nowrap;">
                <i class="feather icon-calendar mr-1"></i> আজকের নতুন অর্ডার ({{ date('d M Y') }})
            </button>
        </div>
    @endif

    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4 row-cols-xl-4 g-2 mb-3 px-1">
        <!-- 1. Total Orders -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #eff6ff; color: #2563eb;">
                        <i class="feather icon-package" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-green"><i class="feather icon-arrow-up" style="font-size: 9px;"></i> 12%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Total Orders') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 20px; color: #0f172a;">{{ $totalOrdersCount }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Website: {{ $websiteOrdersCount }} | Manual: {{ $manualOrdersCount }}
                </div>
            </div>
        </div>

        <!-- 2. Products Sent to Courier -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #ecfdf5; color: #10b981;">
                        <i class="feather icon-truck" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-green"><i class="feather icon-arrow-up" style="font-size: 9px;"></i> 15%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Products Sent to Courier') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 20px; color: #0f172a;">{{ $productsSentCount }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Website: {{ $sentWebsiteQty }} | Manual: {{ $sentManualQty }}
                </div>
            </div>
        </div>

        <!-- 3. Delivered Orders -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #ecfdf5; color: #059669;">
                        <i class="feather icon-check-circle" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-green"><i class="feather icon-arrow-up" style="font-size: 9px;"></i> 18%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Delivered Orders') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 20px; color: #059669;">{{ $deliveredOrdersCount }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Qty: {{ $deliveredQty }}
                </div>
            </div>
        </div>

        <!-- 4. Cancelled Orders -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #fef2f2; color: #ef4444;">
                        <i class="feather icon-x-circle" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-red"><i class="feather icon-arrow-down" style="font-size: 9px;"></i> 10%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Cancelled Orders') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 20px; color: #dc2626;">{{ $cancelledOrdersCount }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Qty: {{ $cancelledQty }}
                </div>
            </div>
        </div>

        <!-- 5. Returned Orders -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #fff7ed; color: #f97316;">
                        <i class="feather icon-rotate-ccw" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-orange"><i class="feather icon-arrow-down" style="font-size: 9px;"></i> 20%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Returned Orders') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 20px; color: #ea580c;">{{ $returnedOrdersCount }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Qty: {{ $returnedQty }}
                </div>
            </div>
        </div>

        <!-- 6. Total Delivery Charge -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #eff6ff; color: #2563eb;">
                        <i class="feather icon-credit-card" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-green"><i class="feather icon-arrow-up" style="font-size: 9px;"></i> 14%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Total Delivery Charge') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 17px; color: #0f172a;">৳ {{ number_format($totalDeliveryCharge) }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Paid to Couriers
                </div>
            </div>
        </div>

        <!-- 7. Total COD Collection -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #f0fdfa; color: #0d9488;">
                        <i class="feather icon-dollar-sign" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft badge-soft-green"><i class="feather icon-arrow-up" style="font-size: 9px;"></i> 22%</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Total COD Collection') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 17px; color: #0d9488;">৳ {{ number_format($totalCodCollection) }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Collected by Couriers
                </div>
            </div>
        </div>

        <!-- 8. Partial Delivered Orders -->
        <div class="col px-1 mb-2">
            <div class="cd-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="cd-icon-box" style="background: #f0fdfa; color: #0891b2;">
                        <i class="feather icon-pie-chart" style="font-size: 16px;"></i>
                    </div>
                    <span class="badge-pill-soft" style="background: #ecfeff; color: #0891b2; font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 4px;"><i class="feather icon-check" style="font-size: 9px;"></i> Live</span>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size: 11px; font-weight: 600;">{{ __('Partial Delivered Orders') }}</span>
                    <h4 class="mb-0 font-weight-bold" style="font-size: 20px; color: #0891b2;">{{ $partialOrdersCount }}</h4>
                </div>
                <div class="text-muted mt-1" style="font-size: 10px; border-top: 1px solid #f1f5f9; padding-top: 3px;">
                    Qty: {{ $partialQty }}
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 5. MIDDLE SECTION: 3 CARDS SIDE BY SIDE -->
    <!-- ========================================== -->
    <div class="row g-3 mb-3">
        <!-- 5.1 Card 1: Order by Source -->
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="cd-card h-100 p-3">
                <h5 class="font-weight-bold mb-3" style="font-size: 15px; color: #0f172a;">
                    {{ __('Order by Source') }}
                </h5>
                <div class="d-flex align-items-center justify-content-between" style="min-height: 220px;">
                    <!-- Apex Donut Chart Container -->
                    <div id="chart-order-source" style="width: 170px; height: 180px; position: relative;"></div>

                    <!-- Legend Breakdown -->
                    @php
                        $webPct = $totalOrdersCount > 0 ? round(($websiteOrdersCount / $totalOrdersCount) * 100, 1) : 0;
                        $manPct = $totalOrdersCount > 0 ? round(($manualOrdersCount / $totalOrdersCount) * 100, 1) : 0;
                    @endphp
                    <div class="pl-2" style="flex: 1;">
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #2563eb; display: inline-block;"></span>
                                <span class="font-weight-bold" style="font-size: 13px; color: #1e293b;">{{ __('Website Orders') }}</span>
                            </div>
                            <div class="pl-3" style="font-size: 14px; font-weight: 700; color: #2563eb;">
                                {{ $websiteOrdersCount }} ({{ $webPct }}%)
                            </div>
                        </div>

                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                <span class="font-weight-bold" style="font-size: 13px; color: #1e293b;">{{ __('Manual Orders (Online POS)') }}</span>
                            </div>
                            <div class="pl-3" style="font-size: 14px; font-weight: 700; color: #10b981;">
                                {{ $manualOrdersCount }} ({{ $manPct }}%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5.2 Card 2: Courier Wise Summary -->
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="cd-card h-100 p-3">
                <h5 class="font-weight-bold mb-3" style="font-size: 15px; color: #0f172a;">
                    {{ __('Courier Wise Summary') }}
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size: 12.5px;">
                        <thead>
                            <tr style="color: #64748b; border-bottom: 1px solid #e2e8f0;">
                                <th class="font-weight-bold py-2 border-top-0">{{ __('Courier Name') }}</th>
                                <th class="font-weight-bold text-center py-2 border-top-0">{{ __('Total Orders') }}</th>
                                <th class="font-weight-bold text-center py-2 border-top-0">{{ __('Delivered') }}</th>
                                <th class="font-weight-bold text-center py-2 border-top-0" style="color: #0891b2;">{{ __('Partial') }}</th>
                                <th class="font-weight-bold text-center py-2 border-top-0">{{ __('Pending') }}</th>
                                <th class="font-weight-bold text-center py-2 border-top-0">{{ __('Returned') }}</th>
                                <th class="font-weight-bold text-right py-2 border-top-0">{{ __('Courier Charge') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courierSummary as $c)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="font-weight-semibold py-2" style="color: #1e293b;">{{ $c->name }}</td>
                                <td class="text-center py-2 font-weight-bold">{{ $c->total }}</td>
                                <td class="text-center py-2 text-success font-weight-bold">{{ $c->delivered }}</td>
                                <td class="text-center py-2 font-weight-bold" style="color: #0891b2;">{{ $c->partial ?? 0 }}</td>
                                <td class="text-center py-2 text-primary font-weight-bold">{{ $c->pending }}</td>
                                <td class="text-center py-2 text-danger font-weight-bold">{{ $c->returned }}</td>
                                <td class="text-right py-2 font-weight-semibold">৳ {{ number_format($c->charge) }}</td>
                            </tr>
                            @endforeach
                            <!-- Total Row -->
                            <tr class="font-weight-bold" style="border-top: 2px solid #e2e8f0; background: #f8fafc;">
                                <td class="py-2 text-dark">{{ __('Total') }}</td>
                                <td class="text-center py-2 text-dark">{{ $totalOrdersCount }}</td>
                                <td class="text-center py-2 text-success">{{ $deliveredOrdersCount }}</td>
                                <td class="text-center py-2 font-weight-bold" style="color: #0891b2;">{{ $partialOrdersCount }}</td>
                                <td class="text-center py-2 text-primary">{{ $pendingOrdersCount }}</td>
                                <td class="text-center py-2 text-danger">{{ $returnedOrdersCount }}</td>
                                <td class="text-right py-2 text-dark">৳ {{ number_format($totalDeliveryCharge) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5.3 Card 3: Profit & Loss Summary -->
        <div class="col-lg-4" id="profit-loss-section">
            <div class="cd-card h-100 p-3">
                <h5 class="font-weight-bold mb-3 d-flex align-items-center justify-content-between" style="font-size: 15px; color: #0f172a;">
                    <span><i class="feather icon-file-text mr-1 text-primary"></i> {{ __('Profit & Loss Summary') }}</span>
                    <span class="badge badge-light border text-muted px-2 py-0.5" style="font-size: 11px;">Calculated</span>
                </h5>
                <div class="space-y-2" style="font-size: 12.5px;">
                    <!-- Sales Revenue -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            {{ __('Total Sales Revenue') }}
                        </span>
                        <span class="font-weight-bold text-dark">৳ {{ number_format($totalSalesRevenue) }}</span>
                    </div>

                    <!-- Product Cost -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            {{ __('Total Product Cost') }}
                        </span>
                        <span class="font-weight-bold text-dark">৳ {{ number_format($totalProductCost) }}</span>
                    </div>

                    <!-- Gross Profit -->
                    <div class="d-flex align-items-center justify-content-between py-1" style="background: #f0fdf4; padding: 4px 8px; border-radius: 6px;">
                        <span class="d-flex align-items-center gap-2 font-weight-bold text-success">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                            {{ __('Gross Profit') }}
                        </span>
                        <span class="font-weight-bold text-success">৳ {{ number_format($grossProfit) }} ({{ $grossMarginPct }}%)</span>
                    </div>

                    <!-- Courier Charges -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6; display: inline-block;"></span>
                            {{ __('Courier Charges') }}
                        </span>
                        <span class="font-weight-semibold text-dark">৳ {{ number_format($courierChargesExpense) }}</span>
                    </div>

                    <!-- Delivery Charge Collected -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0284c7; display: inline-block;"></span>
                            {{ __('Delivery Charge Collected') }}
                        </span>
                        <span class="font-weight-semibold text-dark">৳ {{ number_format($deliveryChargeCollected) }}</span>
                    </div>

                    <!-- Return & Cancellation Loss -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            {{ __('Return & Cancellation Loss') }}
                        </span>
                        <span class="font-weight-semibold text-danger">৳ {{ number_format($returnCancellationLoss) }}</span>
                    </div>

                    <!-- Packaging Cost -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0284c7; display: inline-block;"></span>
                            {{ __('Packaging Cost') }}
                        </span>
                        <span class="font-weight-semibold text-dark">৳ {{ number_format($packagingCost) }}</span>
                    </div>

                    <!-- Other Expenses -->
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <span class="d-flex align-items-center gap-2 text-muted">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0284c7; display: inline-block;"></span>
                            {{ __('Other Expenses') }}
                        </span>
                        <span class="font-weight-semibold text-dark">৳ {{ number_format($otherExpenses) }}</span>
                    </div>

                    <div style="border-top: 2px solid #e2e8f0; margin: 8px 0;"></div>

                    <!-- Net Profit & Net Margin -->
                    <div class="d-flex align-items-center justify-content-between pt-1">
                        <span class="font-weight-bold d-flex align-items-center gap-2" style="font-size: 13.5px; color: #047857;">
                            <i class="feather icon-trending-up"></i> {{ __('Net Profit') }}
                        </span>
                        <span class="font-weight-bold" style="font-size: 16px; color: #059669;">৳ {{ number_format($netProfit) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-weight-bold" style="font-size: 12.5px; color: #047857;">
                            {{ __('Net Profit Margin') }}
                        </span>
                        <span class="font-weight-bold" style="font-size: 13.5px; color: #059669;">{{ $netMarginPct }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 6. BOTTOM DETAILED ORDER LIST TABLE -->
    <!-- ========================================== -->
    <div class="cd-card p-0" id="detailed-orders-section">
        <!-- Table Top Filter Header -->
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <!-- Left: Title, View Mode Switcher & Status Filter Pills -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h5 class="mb-0 font-weight-bold mr-2" style="font-size: 16px; color: #0f172a;">
                    {{ __('Detailed Order List') }}
                </h5>

                <div class="view-mode-switcher mr-2">
                    <button type="button" id="btn-view-grouped" class="view-mode-btn active">
                        <i class="feather icon-layers"></i> {{ __('Product Total Qty') }}
                    </button>
                    <button type="button" id="btn-view-detailed" class="view-mode-btn">
                        <i class="feather icon-list"></i> {{ __('Order Wise List') }}
                    </button>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-1">
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'all']) }}" class="status-tab-btn {{ $statusTab === 'all' ? 'active' : '' }}">{{ __('All') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'delivered']) }}" class="status-tab-btn {{ $statusTab === 'delivered' ? 'active' : '' }}">{{ __('Delivered') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'partial']) }}" class="status-tab-btn {{ $statusTab === 'partial' ? 'active' : '' }}">{{ __('Partial') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'in review']) }}" class="status-tab-btn {{ $statusTab === 'in review' ? 'active' : '' }}">{{ __('In Review') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'cancelled']) }}" class="status-tab-btn {{ $statusTab === 'cancelled' ? 'active' : '' }}">{{ __('Cancelled') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'returned']) }}" class="status-tab-btn {{ $statusTab === 'returned' ? 'active' : '' }}">{{ __('Returned') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['status_tab' => 'pending']) }}" class="status-tab-btn {{ $statusTab === 'pending' ? 'active' : '' }}">{{ __('Pending') }}</a>
                </div>
            </div>

            <!-- Right: Search & Pagination Controls -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group" style="width: 220px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 py-1" style="border-radius: 6px 0 0 6px; border-color: #cbd5e1;"><i class="feather icon-search text-muted" style="font-size: 11px;"></i></span>
                    </div>
                    <input type="text" id="live-table-search" class="form-control form-control-sm border-left-0" style="border-radius: 0 6px 6px 0; font-size: 12px; height: 32px;" placeholder="{{ __('Search product, order, ID...') }}">
                </div>

                <div class="d-flex align-items-center gap-1 font-weight-semibold text-muted" style="font-size: 12px;">
                    <span>{{ __('Show') }}</span>
                    <select id="table-page-size" class="form-control form-control-sm px-2 py-0" style="width: 66px; height: 32px; font-size: 12px;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">{{ __('All') }}</option>
                    </select>
                </div>

                <span id="table-page-indicator" class="text-muted font-weight-semibold" style="font-size: 11.5px; min-width: 68px; text-align: center;"></span>

                <div class="btn-group btn-group-sm">
                    <button type="button" id="btn-table-prev" class="btn btn-light border px-2 py-1"><i class="feather icon-chevron-left"></i></button>
                    <button type="button" id="btn-table-next" class="btn btn-light border px-2 py-1"><i class="feather icon-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <!-- Prominent Live All Total Qty Summary Strip (Always visible in both views) -->
        @php
            $initTotalOrders = $filteredTotalOrders ?? $filteredOrders->count();
            $initTotalQty = $filteredTotalQty ?? (int)$filteredOrders->sum('qty');
            $initGroupedCount = isset($productGroupedOrders) ? $productGroupedOrders->count() : 0;
            $initTotalAmount = $filteredTotalAmount ?? (float)$filteredOrders->sum('order_amount');
            $initTotalCost = $filteredTotalCost ?? (float)$filteredOrders->sum('product_cost');
            $initTotalCharge = $filteredTotalCharge ?? (float)$filteredOrders->sum('courier_charge');
            $initTotalProfit = $filteredTotalProfit ?? (float)$filteredOrders->sum('net_profit');
        @endphp
        <div class="cd-totals-strip" id="detailed-list-totals-bar">
            <div class="cd-total-pill cd-total-pill-primary">
                <i class="feather icon-layers" style="font-size: 13px;"></i>
                <span style="opacity: 0.9;">{{ __('All Total Qty') }}:</span>
                <strong id="live-total-qty-val" style="font-size: 14px;">{{ number_format($initTotalQty) }} Pcs</strong>
            </div>
            <div class="cd-total-pill">
                <span class="text-muted">{{ __('Matching Orders') }}:</span>
                <strong class="text-dark" id="live-total-orders-val">{{ number_format($initTotalOrders) }}</strong>
            </div>
            <div class="cd-total-pill">
                <span class="text-muted">{{ __('Products') }}:</span>
                <strong class="text-primary" id="live-total-products-val">{{ number_format($initGroupedCount) }}</strong>
            </div>
            <div class="cd-total-pill">
                <span class="text-muted">{{ __('Total Order Amount') }}:</span>
                <strong class="text-dark">৳ <span id="live-total-amount-val">{{ number_format($initTotalAmount) }}</span></strong>
            </div>
            <div class="cd-total-pill">
                <span class="text-muted">{{ __('Total Product Cost') }}:</span>
                <strong class="text-dark">৳ <span id="live-total-cost-val">{{ number_format($initTotalCost) }}</span></strong>
            </div>
            <div class="cd-total-pill">
                <span class="text-muted">{{ __('Total Courier Charge') }}:</span>
                <strong class="text-dark">৳ <span id="live-total-charge-val">{{ number_format($initTotalCharge) }}</span></strong>
            </div>
            <div class="cd-total-pill">
                <span class="text-muted">{{ __('Net Profit/Loss') }}:</span>
                <strong id="live-total-profit-val" class="{{ $initTotalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $initTotalProfit < 0 ? '-৳ ' . number_format(abs($initTotalProfit)) : '৳ ' . number_format($initTotalProfit) }}
                </strong>
            </div>
        </div>

        <!-- View 1: Product Total Qty Consolidated Table (Default Active View) -->
        <div class="table-responsive" id="grouped-products-wrapper">
            <table class="table order-table mb-0" id="grouped-products-datatable">
                <thead>
                    <tr>
                        <th style="width: 38px;">#</th>
                        <th>{{ __('Product Image') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th class="text-center">{{ __('Total Orders') }}</th>
                        <th class="text-center" style="color: #2563eb;">{{ __('All Total Qty') }}</th>
                        <th class="text-center text-success">{{ __('Delivered') }}</th>
                        <th class="text-center" style="color: #0891b2;">{{ __('Partial') }}</th>
                        <th class="text-center text-primary">{{ __('Pending / Transit') }}</th>
                        <th class="text-center" style="color: #ea580c;">{{ __('Returned') }}</th>
                        <th class="text-center text-danger">{{ __('Cancelled') }}</th>
                        <th class="text-right">{{ __('Order Amount') }}</th>
                        <th class="text-right">{{ __('Product Cost') }}</th>
                        <th class="text-right">{{ __('Courier Charge') }}</th>
                        <th class="text-right">{{ __('Net Profit/Loss') }}</th>
                        <th class="text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody id="grouped-products-tbody">
                    @php
                        $gOrders = 0; $gQty = 0; $gDel = 0; $gPart = 0; $gPend = 0; $gRet = 0; $gCanc = 0;
                        $gAmt = 0; $gCost = 0; $gCharge = 0; $gProfit = 0;
                    @endphp
                    @forelse(($productGroupedOrders ?? collect()) as $gIdx => $gp)
                        @php
                            $gOrders += $gp->orders_count;
                            $gQty += $gp->total_qty;
                            $gDel += $gp->delivered_qty;
                            $gPart += $gp->partial_qty;
                            $gPend += $gp->pending_qty;
                            $gRet += $gp->returned_qty;
                            $gCanc += $gp->cancelled_qty;
                            $gAmt += $gp->order_amount;
                            $gCost += $gp->product_cost;
                            $gCharge += $gp->courier_charge;
                            $gProfit += $gp->net_profit;
                            $gpIsProfit = ($gp->net_profit > 0);
                            $gpIsLoss = ($gp->net_profit < 0);
                        @endphp
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $gIdx + 1 }}</td>
                            <td>
                                <div style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $gp->product_image }}" alt="{{ $gp->product_name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                            </td>
                            <td class="font-weight-bold text-dark">{{ $gp->product_name }}</td>
                            <td class="text-center font-weight-bold">{{ number_format($gp->orders_count) }}</td>
                            <td class="text-center">
                                <span class="badge px-2.5 py-1 font-weight-bold" style="font-size: 12.5px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    {{ number_format($gp->total_qty) }} Pcs
                                </span>
                            </td>
                            <td class="text-center font-weight-bold text-success">{{ number_format($gp->delivered_qty) }}</td>
                            <td class="text-center font-weight-bold" style="color: #0891b2;">{{ number_format($gp->partial_qty) }}</td>
                            <td class="text-center font-weight-bold text-primary">{{ number_format($gp->pending_qty) }}</td>
                            <td class="text-center font-weight-bold" style="color: #ea580c;">{{ number_format($gp->returned_qty) }}</td>
                            <td class="text-center font-weight-bold text-danger">{{ number_format($gp->cancelled_qty) }}</td>
                            <td class="text-right font-weight-bold text-dark">৳ {{ number_format($gp->order_amount) }}</td>
                            <td class="text-right text-muted font-weight-semibold">৳ {{ number_format($gp->product_cost) }}</td>
                            <td class="text-right text-muted font-weight-semibold">৳ {{ number_format($gp->courier_charge) }}</td>
                            <td class="text-right font-weight-bold {{ $gpIsProfit ? 'text-success' : ($gpIsLoss ? 'text-danger' : 'text-muted') }}">
                                {{ $gpIsLoss ? '-৳ ' . number_format(abs($gp->net_profit)) : '৳ ' . number_format($gp->net_profit) }}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold px-2 py-1 btn-view-product-orders" data-product="{{ e($gp->product_name) }}" style="font-size: 11px; border-radius: 6px;">
                                    <i class="feather icon-eye mr-1"></i> {{ __('View Orders') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center py-4 text-muted">
                                {{ __('No products found matching the filter criteria.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot id="grouped-products-tfoot">
                    <tr>
                        <td>#</td>
                        <td></td>
                        <td>{{ __('All Total Qty') }}</td>
                        <td class="text-center">{{ number_format($gOrders) }}</td>
                        <td class="text-center text-primary">{{ number_format($gQty) }} Pcs</td>
                        <td class="text-center text-success">{{ number_format($gDel) }}</td>
                        <td class="text-center" style="color: #0891b2;">{{ number_format($gPart) }}</td>
                        <td class="text-center text-primary">{{ number_format($gPend) }}</td>
                        <td class="text-center" style="color: #ea580c;">{{ number_format($gRet) }}</td>
                        <td class="text-center text-danger">{{ number_format($gCanc) }}</td>
                        <td class="text-right">৳ {{ number_format($gAmt) }}</td>
                        <td class="text-right">৳ {{ number_format($gCost) }}</td>
                        <td class="text-right">৳ {{ number_format($gCharge) }}</td>
                        <td class="text-right {{ $gProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $gProfit < 0 ? '-৳ ' . number_format(abs($gProfit)) : '৳ ' . number_format($gProfit) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- View 2: The 17-Column Order Wise Table -->
        <div class="table-responsive" id="detailed-orders-wrapper" style="display: none;">
            <table class="table order-table mb-0" id="courier-orders-datatable">
                <thead>
                    <tr>
                        <th style="width: 32px;"><input type="checkbox" id="check-all-orders"></th>
                        <th style="width: 38px;">#</th>
                        <th>{{ __('Order ID') }}</th>
                        <th>{{ __('Source') }}</th>
                        <th>{{ __('Order Date') }}</th>
                        <th>{{ __('Product Image') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th class="text-center">{{ __('Qty') }}</th>
                        <th class="text-right">{{ __('Purchase Price') }}</th>
                        <th class="text-right">{{ __('Selling Price') }}</th>
                        <th class="text-right">{{ __('Order Amount') }}</th>
                        <th>{{ __('Courier') }}</th>
                        <th>{{ __('Tracking ID') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Product Cost') }}</th>
                        <th class="text-right">{{ __('Courier Charge') }}</th>
                        <th class="text-right">{{ __('Net Profit/Loss') }}</th>
                    </tr>
                </thead>
                <tbody id="orders-table-tbody">
                    @forelse($filteredOrders as $idx => $item)
                        @php
                            $statusClean = strtolower($item->status);
                            $badgeClass = 'status-pending';
                            if (str_contains($statusClean, 'partial')) $badgeClass = 'status-partial';
                            elseif (str_contains($statusClean, 'delivered')) $badgeClass = 'status-delivered';
                            elseif (str_contains($statusClean, 'transit') || str_contains($statusClean, 'ship')) $badgeClass = 'status-transit';
                            elseif (str_contains($statusClean, 'cancel')) $badgeClass = 'status-cancelled';
                            elseif (str_contains($statusClean, 'return')) $badgeClass = 'status-returned';
                            elseif (str_contains($statusClean, 'review') || str_contains($statusClean, 'hold')) $badgeClass = 'status-review';

                            $isProfit = ($item->net_profit > 0);
                            $isLoss = ($item->net_profit < 0);
                            $rowSearchText = strtolower(trim(implode(' ', array_filter([
                                $item->order_id,
                                $item->source,
                                $item->date,
                                $item->product_name,
                                $item->all_product_names ?? '',
                                $item->courier,
                                $item->tracking_id,
                                $item->status,
                            ]))));
                        @endphp
                        <tr class="order-data-row"
                            data-product-name="{{ e($item->product_name) }}"
                            data-product-image="{{ e($item->product_image) }}"
                            data-qty="{{ (int)$item->qty }}"
                            data-order-amount="{{ (float)$item->order_amount }}"
                            data-product-cost="{{ (float)$item->product_cost }}"
                            data-courier-charge="{{ (float)$item->courier_charge }}"
                            data-net-profit="{{ (float)$item->net_profit }}"
                            data-status="{{ e($item->status) }}"
                            data-search-text="{{ e($rowSearchText) }}">
                            <td><input type="checkbox" class="order-select-checkbox"></td>
                            <td class="text-muted font-weight-bold row-sl-num">{{ $idx + 1 }}</td>
                            <td class="font-weight-bold" style="color: #0f172a;">{{ $item->order_id }}</td>
                            <td>
                                @if($item->source === 'Website')
                                    <span class="badge px-2 py-1 font-weight-bold" style="font-size: 11px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                        <i class="feather icon-globe mr-1"></i> {{ __('Website') }}
                                    </span>
                                @else
                                    <span class="badge px-2 py-1 font-weight-bold" style="font-size: 11px; background: #f8fafc; color: #475569; border: 1px solid #cbd5e1;">
                                        <i class="feather icon-edit mr-1"></i> {{ __('Manual (POS)') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $item->date }}</td>
                            <td>
                                <div style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                            </td>
                            <td class="font-weight-semibold text-dark">{{ $item->product_name }}</td>
                            <td class="text-center font-weight-bold">{{ $item->qty }}</td>
                            <td class="text-right text-muted">৳ {{ number_format($item->purchase_price) }}</td>
                            <td class="text-right font-weight-semibold">৳ {{ number_format($item->selling_price) }}</td>
                            <td class="text-right font-weight-bold text-dark">৳ {{ number_format($item->order_amount) }}</td>
                            <td class="font-weight-semibold text-muted">{{ $item->courier }}</td>
                            <td>
                                <code style="font-size: 11.5px; color: #475569; background: #f1f5f9; padding: 2px 5px; border-radius: 4px;">{{ $item->tracking_id }}</code>
                            </td>
                            <td>
                                <span class="status-badge {{ $badgeClass }}">{{ $item->status }}</span>
                            </td>
                            <td class="text-right text-muted font-weight-semibold">৳ {{ number_format($item->product_cost) }}</td>
                            <td class="text-right text-muted font-weight-semibold">৳ {{ number_format($item->courier_charge) }}</td>
                            <td class="text-right font-weight-bold {{ $isProfit ? 'text-success' : ($isLoss ? 'text-danger' : 'text-muted') }}">
                                {{ $isLoss ? '-৳ ' . number_format(abs($item->net_profit)) : '৳ ' . number_format($item->net_profit) }}
                            </td>
                        </tr>
                    @empty
                        <tr id="orders-empty-row">
                            <td colspan="17" class="text-center py-4 text-muted">
                                {{ __('No orders found matching the filter criteria.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot id="orders-table-tfoot">
                    <tr>
                        <td></td>
                        <td>#</td>
                        <td colspan="5">{{ __('All Matching Orders Total') }} ({{ number_format($initTotalOrders) }} {{ __('Orders') }})</td>
                        <td class="text-center text-primary">{{ number_format($initTotalQty) }} Pcs</td>
                        <td></td>
                        <td></td>
                        <td class="text-right">৳ {{ number_format($initTotalAmount) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">৳ {{ number_format($initTotalCost) }}</td>
                        <td class="text-right">৳ {{ number_format($initTotalCharge) }}</td>
                        <td class="text-right {{ $initTotalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $initTotalProfit < 0 ? '-৳ ' . number_format(abs($initTotalProfit)) : '৳ ' . number_format($initTotalProfit) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('js')
<!-- Load ApexCharts for Donut Chart -->
<script src="{{ asset('backend/plugins/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Render Order by Source Donut Chart
        var options = {
            series: [{{ $websiteOrdersCount }}, {{ $manualOrdersCount }}],
            labels: ['Website Orders', 'Manual Orders (POS)'],
            chart: {
                type: 'donut',
                height: 180,
                sparkline: { enabled: true }
            },
            colors: ['#2563eb', '#10b981'],
            stroke: { width: 0 },
            legend: { show: false },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Orders";
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '11px', color: '#64748b', offsetY: -4 },
                            value: { show: true, fontSize: '20px', fontWeight: 800, color: '#0f172a', offsetY: 4, formatter: () => '{{ $totalOrdersCount }}' },
                            total: {
                                show: true,
                                label: 'Total Orders',
                                fontSize: '11px',
                                color: '#64748b',
                                formatter: () => '{{ $totalOrdersCount }}'
                            }
                        }
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-order-source"), options);
        chart.render();

        // Check All Checkboxes
        var checkAll = document.getElementById('check-all-orders');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('#orders-table-tbody tr.order-data-row').forEach(function (row) {
                    if (row.style.display !== 'none') {
                        var cb = row.querySelector('.order-select-checkbox');
                        if (cb) cb.checked = checkAll.checked;
                    }
                });
            });
        }

        // Unified Detailed Orders & Consolidated Product Total Qty Controller
        var activeViewMode = 'grouped'; // 'grouped' | 'detailed'
        var currentPage = 1;
        var liveSearchInput = document.getElementById('live-table-search');
        var pageSizeSelect = document.getElementById('table-page-size');
        var btnViewGrouped = document.getElementById('btn-view-grouped');
        var btnViewDetailed = document.getElementById('btn-view-detailed');
        var btnPrev = document.getElementById('btn-table-prev');
        var btnNext = document.getElementById('btn-table-next');
        var pageIndicator = document.getElementById('table-page-indicator');
        var groupedWrapper = document.getElementById('grouped-products-wrapper');
        var detailedWrapper = document.getElementById('detailed-orders-wrapper');
        var groupedTbody = document.getElementById('grouped-products-tbody');
        var groupedTfoot = document.getElementById('grouped-products-tfoot');
        var ordersTbody = document.getElementById('orders-table-tbody');
        var ordersTfoot = document.getElementById('orders-table-tfoot');

        function escapeHtml(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatMoney(num) {
            var n = Math.round(Number(num) || 0);
            return (n < 0 ? '-৳ ' : '৳ ') + Math.abs(n).toLocaleString();
        }

        function switchViewMode(mode) {
            activeViewMode = mode;
            if (mode === 'grouped') {
                if (btnViewGrouped) btnViewGrouped.classList.add('active');
                if (btnViewDetailed) btnViewDetailed.classList.remove('active');
                if (groupedWrapper) groupedWrapper.style.display = '';
                if (detailedWrapper) detailedWrapper.style.display = 'none';
            } else {
                if (btnViewGrouped) btnViewGrouped.classList.remove('active');
                if (btnViewDetailed) btnViewDetailed.classList.add('active');
                if (groupedWrapper) groupedWrapper.style.display = 'none';
                if (detailedWrapper) detailedWrapper.style.display = '';
            }
            currentPage = 1;
            refreshDetailedOrdersView();
        }

        function refreshDetailedOrdersView() {
            var filter = liveSearchInput ? liveSearchInput.value.toLowerCase().trim() : '';
            var orderRows = Array.from(document.querySelectorAll('#orders-table-tbody tr.order-data-row'));

            var matchingRows = [];
            var sumOrders = 0;
            var sumQty = 0;
            var sumAmt = 0;
            var sumCost = 0;
            var sumCharge = 0;
            var sumProfit = 0;
            var productMap = {};

            orderRows.forEach(function (row) {
                var searchText = (row.getAttribute('data-search-text') || row.innerText || '').toLowerCase();
                if (filter === '' || searchText.indexOf(filter) > -1) {
                    matchingRows.push(row);

                    var pName = row.getAttribute('data-product-name') || 'General Gear';
                    var pImage = row.getAttribute('data-product-image') || '';
                    var qty = parseInt(row.getAttribute('data-qty') || '0', 10) || 0;
                    var amt = parseFloat(row.getAttribute('data-order-amount') || '0') || 0;
                    var cost = parseFloat(row.getAttribute('data-product-cost') || '0') || 0;
                    var charge = parseFloat(row.getAttribute('data-courier-charge') || '0') || 0;
                    var profit = parseFloat(row.getAttribute('data-net-profit') || '0') || 0;
                    var statusLower = (row.getAttribute('data-status') || '').toLowerCase();

                    sumOrders += 1;
                    sumQty += qty;
                    sumAmt += amt;
                    sumCost += cost;
                    sumCharge += charge;
                    sumProfit += profit;

                    if (!productMap[pName]) {
                        productMap[pName] = {
                            product_name: pName,
                            product_image: pImage,
                            orders_count: 0,
                            total_qty: 0,
                            delivered_qty: 0,
                            partial_qty: 0,
                            pending_qty: 0,
                            returned_qty: 0,
                            cancelled_qty: 0,
                            order_amount: 0,
                            product_cost: 0,
                            courier_charge: 0,
                            net_profit: 0
                        };
                    }
                    var grp = productMap[pName];
                    grp.orders_count += 1;
                    grp.total_qty += qty;
                    grp.order_amount += amt;
                    grp.product_cost += cost;
                    grp.courier_charge += charge;
                    grp.net_profit += profit;

                    if (statusLower === 'delivered' || statusLower === 'cod processing') {
                        grp.delivered_qty += qty;
                    } else if (statusLower === 'partial') {
                        grp.partial_qty += qty;
                    } else if (statusLower === 'returned') {
                        grp.returned_qty += qty;
                    } else if (statusLower === 'cancelled') {
                        grp.cancelled_qty += qty;
                    } else {
                        grp.pending_qty += qty;
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            var groupedArray = Object.values(productMap).sort(function (a, b) {
                return b.total_qty - a.total_qty;
            });

            // 1. Update prominent summary strip (always reflects ALL matching records across all pages)
            var elTotalQty = document.getElementById('live-total-qty-val');
            var elTotalOrders = document.getElementById('live-total-orders-val');
            var elTotalProducts = document.getElementById('live-total-products-val');
            var elTotalAmt = document.getElementById('live-total-amount-val');
            var elTotalCost = document.getElementById('live-total-cost-val');
            var elTotalCharge = document.getElementById('live-total-charge-val');
            var elTotalProfit = document.getElementById('live-total-profit-val');

            if (elTotalQty) elTotalQty.textContent = sumQty.toLocaleString() + ' Pcs';
            if (elTotalOrders) elTotalOrders.textContent = sumOrders.toLocaleString();
            if (elTotalProducts) elTotalProducts.textContent = groupedArray.length.toLocaleString();
            if (elTotalAmt) elTotalAmt.textContent = Math.round(sumAmt).toLocaleString();
            if (elTotalCost) elTotalCost.textContent = Math.round(sumCost).toLocaleString();
            if (elTotalCharge) elTotalCharge.textContent = Math.round(sumCharge).toLocaleString();
            if (elTotalProfit) {
                elTotalProfit.textContent = formatMoney(sumProfit);
                elTotalProfit.className = sumProfit >= 0 ? 'text-success' : 'text-danger';
            }

            // 2. Render Consolidated Product Total Qty Table Body & Footer
            var gDel = 0, gPart = 0, gPend = 0, gRet = 0, gCanc = 0;
            if (groupedTbody) {
                if (groupedArray.length === 0) {
                    groupedTbody.innerHTML = '<tr><td colspan="15" class="text-center py-4 text-muted">{{ __('No products found matching the filter criteria.') }}</td></tr>';
                } else {
                    var groupedHtml = '';
                    groupedArray.forEach(function (gp, idx) {
                        gDel += gp.delivered_qty;
                        gPart += gp.partial_qty;
                        gPend += gp.pending_qty;
                        gRet += gp.returned_qty;
                        gCanc += gp.cancelled_qty;

                        var profitCls = gp.net_profit > 0 ? 'text-success' : (gp.net_profit < 0 ? 'text-danger' : 'text-muted');
                        groupedHtml += '<tr class="grouped-data-row">'
                            + '<td class="text-muted font-weight-bold">' + (idx + 1) + '</td>'
                            + '<td><div style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center;"><img src="' + escapeHtml(gp.product_image) + '" alt="' + escapeHtml(gp.product_name) + '" style="max-width: 100%; max-height: 100%; object-fit: contain;"></div></td>'
                            + '<td class="font-weight-bold text-dark">' + escapeHtml(gp.product_name) + '</td>'
                            + '<td class="text-center font-weight-bold">' + gp.orders_count.toLocaleString() + '</td>'
                            + '<td class="text-center"><span class="badge px-2.5 py-1 font-weight-bold" style="font-size: 12.5px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">' + gp.total_qty.toLocaleString() + ' Pcs</span></td>'
                            + '<td class="text-center font-weight-bold text-success">' + gp.delivered_qty.toLocaleString() + '</td>'
                            + '<td class="text-center font-weight-bold" style="color: #0891b2;">' + gp.partial_qty.toLocaleString() + '</td>'
                            + '<td class="text-center font-weight-bold text-primary">' + gp.pending_qty.toLocaleString() + '</td>'
                            + '<td class="text-center font-weight-bold" style="color: #ea580c;">' + gp.returned_qty.toLocaleString() + '</td>'
                            + '<td class="text-center font-weight-bold text-danger">' + gp.cancelled_qty.toLocaleString() + '</td>'
                            + '<td class="text-right font-weight-bold text-dark">৳ ' + Math.round(gp.order_amount).toLocaleString() + '</td>'
                            + '<td class="text-right text-muted font-weight-semibold">৳ ' + Math.round(gp.product_cost).toLocaleString() + '</td>'
                            + '<td class="text-right text-muted font-weight-semibold">৳ ' + Math.round(gp.courier_charge).toLocaleString() + '</td>'
                            + '<td class="text-right font-weight-bold ' + profitCls + '">' + formatMoney(gp.net_profit) + '</td>'
                            + '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-primary font-weight-bold px-2 py-1 btn-view-product-orders" data-product="' + escapeHtml(gp.product_name) + '" style="font-size: 11px; border-radius: 6px;"><i class="feather icon-eye mr-1"></i> {{ __('View Orders') }}</button></td>'
                            + '</tr>';
                    });
                    groupedTbody.innerHTML = groupedHtml;
                }
            }

            if (groupedTfoot) {
                var gfCls = sumProfit >= 0 ? 'text-success' : 'text-danger';
                groupedTfoot.innerHTML = '<tr>'
                    + '<td>#</td><td></td>'
                    + '<td>{{ __('All Total Qty') }}</td>'
                    + '<td class="text-center">' + sumOrders.toLocaleString() + '</td>'
                    + '<td class="text-center text-primary">' + sumQty.toLocaleString() + ' Pcs</td>'
                    + '<td class="text-center text-success">' + gDel.toLocaleString() + '</td>'
                    + '<td class="text-center" style="color: #0891b2;">' + gPart.toLocaleString() + '</td>'
                    + '<td class="text-center text-primary">' + gPend.toLocaleString() + '</td>'
                    + '<td class="text-center" style="color: #ea580c;">' + gRet.toLocaleString() + '</td>'
                    + '<td class="text-center text-danger">' + gCanc.toLocaleString() + '</td>'
                    + '<td class="text-right">৳ ' + Math.round(sumAmt).toLocaleString() + '</td>'
                    + '<td class="text-right">৳ ' + Math.round(sumCost).toLocaleString() + '</td>'
                    + '<td class="text-right">৳ ' + Math.round(sumCharge).toLocaleString() + '</td>'
                    + '<td class="text-right ' + gfCls + '">' + formatMoney(sumProfit) + '</td>'
                    + '<td></td>'
                    + '</tr>';
            }

            // 3. Update Order Wise Table Footer across ALL matching orders
            if (ordersTfoot) {
                var ofCls = sumProfit >= 0 ? 'text-success' : 'text-danger';
                ordersTfoot.innerHTML = '<tr>'
                    + '<td></td><td>#</td>'
                    + '<td colspan="5">{{ __('All Matching Orders Total') }} (' + sumOrders.toLocaleString() + ' {{ __('Orders') }})</td>'
                    + '<td class="text-center text-primary">' + sumQty.toLocaleString() + ' Pcs</td>'
                    + '<td></td><td></td>'
                    + '<td class="text-right">৳ ' + Math.round(sumAmt).toLocaleString() + '</td>'
                    + '<td></td><td></td><td></td>'
                    + '<td class="text-right">৳ ' + Math.round(sumCost).toLocaleString() + '</td>'
                    + '<td class="text-right">৳ ' + Math.round(sumCharge).toLocaleString() + '</td>'
                    + '<td class="text-right ' + ofCls + '">' + formatMoney(sumProfit) + '</td>'
                    + '</tr>';
            }

            // 4. Paginate Active Table
            var pageSizeVal = pageSizeSelect ? pageSizeSelect.value : '25';
            var activeRows = activeViewMode === 'grouped'
                ? Array.from(document.querySelectorAll('#grouped-products-tbody tr.grouped-data-row'))
                : matchingRows;

            var totalActive = activeRows.length;
            var perPage = (pageSizeVal === 'all') ? Math.max(1, totalActive) : (parseInt(pageSizeVal, 10) || 25);
            var totalPages = Math.max(1, Math.ceil(totalActive / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            var startIdx = (currentPage - 1) * perPage;
            var endIdx = startIdx + perPage;

            activeRows.forEach(function (r, i) {
                r.style.display = (i >= startIdx && i < endIdx) ? '' : 'none';
            });

            if (activeViewMode === 'detailed' && ordersTbody) {
                var existingEmpty = document.getElementById('orders-dynamic-empty-row');
                if (totalActive === 0 && orderRows.length > 0) {
                    if (!existingEmpty) {
                        var tr = document.createElement('tr');
                        tr.id = 'orders-dynamic-empty-row';
                        tr.innerHTML = '<td colspan="17" class="text-center py-4 text-muted">{{ __('No orders found matching the filter criteria.') }}</td>';
                        ordersTbody.appendChild(tr);
                    }
                } else if (existingEmpty) {
                    existingEmpty.remove();
                }
            }

            if (pageIndicator) {
                if (totalActive === 0) {
                    pageIndicator.textContent = '0 of 0';
                } else {
                    var showStart = startIdx + 1;
                    var showEnd = Math.min(endIdx, totalActive);
                    pageIndicator.textContent = showStart + '-' + showEnd + ' / ' + totalActive;
                }
            }
            if (btnPrev) btnPrev.disabled = (currentPage <= 1);
            if (btnNext) btnNext.disabled = (currentPage >= totalPages);
        }

        if (liveSearchInput) {
            ['input', 'keyup'].forEach(function (evt) {
                liveSearchInput.addEventListener(evt, function () {
                    currentPage = 1;
                    refreshDetailedOrdersView();
                });
            });
        }
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', function () {
                currentPage = 1;
                refreshDetailedOrdersView();
            });
        }
        if (btnViewGrouped) {
            btnViewGrouped.addEventListener('click', function () { switchViewMode('grouped'); });
        }
        if (btnViewDetailed) {
            btnViewDetailed.addEventListener('click', function () { switchViewMode('detailed'); });
        }
        if (btnPrev) {
            btnPrev.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage -= 1;
                    refreshDetailedOrdersView();
                }
            });
        }
        if (btnNext) {
            btnNext.addEventListener('click', function () {
                currentPage += 1;
                refreshDetailedOrdersView();
            });
        }
        if (groupedTbody) {
            groupedTbody.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-view-product-orders');
                if (btn) {
                    var prod = btn.getAttribute('data-product') || '';
                    if (liveSearchInput) liveSearchInput.value = prod;
                    switchViewMode('detailed');
                }
            });
        }

        refreshDetailedOrdersView();
    });

    // Quick Date Range Preset Setter
    function setDatePreset(preset) {
        document.getElementById('filter-date-preset').value = preset;
        var today = new Date();
        var formatDate = function (d) {
            var month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        };

        var startInput = document.getElementById('input-start-date');
        var endInput = document.getElementById('input-end-date');

        if (preset === 'today') {
            startInput.value = formatDate(today);
            endInput.value = formatDate(today);
        } else if (preset === 'yesterday') {
            var yest = new Date();
            yest.setDate(yest.getDate() - 1);
            startInput.value = formatDate(yest);
            endInput.value = formatDate(yest);
        } else if (preset === '7days') {
            var last7 = new Date();
            last7.setDate(last7.getDate() - 6);
            startInput.value = formatDate(last7);
            endInput.value = formatDate(today);
        } else if (preset === '30days') {
            var last30 = new Date();
            last30.setDate(last30.getDate() - 29);
            startInput.value = formatDate(last30);
            endInput.value = formatDate(today);
        } else if (preset === 'this_month') {
            var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (preset === 'all_active') {
            startInput.value = '';
            endInput.value = '';
        }

        document.getElementById('report-filter-form').submit();
    }

    function switchMainTab(tabName) {
        document.getElementById('filter-active-tab').value = tabName;
        if (tabName === 'delivery_report') {
            window.location.href = "{{ route('report.courier-delivery') }}?tab=delivery_report";
        }
    }

    function syncCourierLiveStatus(btn) {
        var $btn = $(btn);
        var $icon = $('#sync-icon');
        var $text = $('#sync-btn-text');

        $btn.prop('disabled', true);
        $icon.addClass('feather-spin fa-spin');
        $text.text('Steadfast সিঙ্ক হচ্ছে...');

        $.ajax({
            url: "{{ route('invoice.online.sync-courier') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $icon.removeClass('feather-spin fa-spin');
                $text.text('সিঙ্ক সম্পন্ন!');
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message || 'Steadfast status successfully synced!');
                } else {
                    alert(res.message || 'Steadfast status successfully synced!');
                }
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                $btn.prop('disabled', false);
                $icon.removeClass('feather-spin fa-spin');
                $text.text('Sync with Steadfast (লাইভ সিঙ্ক)');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Sync failed. Please check Steadfast API connection.';
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                } else {
                    alert(msg);
                }
            }
        });
    }
</script>
@endpush
@endsection
