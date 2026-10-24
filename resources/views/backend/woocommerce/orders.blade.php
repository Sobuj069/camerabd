@extends('backend.layouts.master')

@section('page-title', 'WooCommerce Order List')

@push('css')
<style>
/* ==========================================================================
   DYNAMIC DUAL THEME (Seamless Light & Dark Mode Support)
   ========================================================================== */

/* 1. Default Light Mode Theme Variables */
:root {
    --wc-bg: #f8fafc;
    --wc-surface: #ffffff;
    --wc-surface-hover: #f1f5f9;
    --wc-surface-alt: #faf5ff;
    --wc-border: #e2e8f0;
    --wc-border-focus: #a855f7;
    --wc-purple: #7c3aed;
    --wc-purple-light: #8b5cf6;
    --wc-purple-glow: rgba(124, 58, 237, 0.25);
    --wc-green: #10b981;
    --wc-cyan: #0284c7;
    --wc-text-heading: #0f172a;
    --wc-text-body: #334155;
    --wc-text-muted: #64748b;
    --wc-input-bg: #ffffff;
    --wc-input-border: #cbd5e1;
    --wc-input-text: #0f172a;
    --wc-table-th-bg: #f8fafc;
    --wc-table-th-text: #475569;
    --wc-table-border: #e2e8f0;
    --wc-sku-bg: #f1f5f9;
    --wc-sku-text: #1e293b;
    --wc-sku-border: #cbd5e1;
    --wc-circle-bg: #e2e8f0;
    --wc-btn-outline-bg: #ffffff;
    --wc-btn-outline-border: #cbd5e1;
    --wc-btn-outline-text: #334155;
    --wc-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}

/* 2. Dark Mode Theme Variables (Activated by body.dark-theme / html.dark-theme) */
body.dark-theme,
html.dark-theme {
    --wc-bg: #0d0a1a !important;
    --wc-surface: #161228 !important;
    --wc-surface-hover: #201938 !important;
    --wc-surface-alt: #1c1533 !important;
    --wc-border: #282044 !important;
    --wc-border-focus: #a855f7 !important;
    --wc-purple: #a855f7 !important;
    --wc-purple-light: #c084fc !important;
    --wc-purple-glow: rgba(168, 85, 247, 0.4) !important;
    --wc-green: #10b981 !important;
    --wc-cyan: #38bdf8 !important;
    --wc-text-heading: #ffffff !important;
    --wc-text-body: #cbd5e1 !important;
    --wc-text-muted: #94a3b8 !important;
    --wc-input-bg: #1c1735 !important;
    --wc-input-border: #282044 !important;
    --wc-input-text: #ffffff !important;
    --wc-table-th-bg: #120e22 !important;
    --wc-table-th-text: #a78bfa !important;
    --wc-table-border: #231b3c !important;
    --wc-sku-bg: #251c44 !important;
    --wc-sku-text: #ffffff !important;
    --wc-sku-border: #3c2e6b !important;
    --wc-circle-bg: #251c44 !important;
    --wc-btn-outline-bg: #1e1838 !important;
    --wc-btn-outline-border: #312656 !important;
    --wc-btn-outline-text: #e2e8f0 !important;
    --wc-shadow: 0 4px 18px rgba(0, 0, 0, 0.4) !important;
}

/* Base Wrapper */
.wc-theme-wrapper {
    background-color: var(--wc-bg) !important;
    color: var(--wc-text-body) !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    transition: background-color 0.25s ease, color 0.25s ease !important;
}

/* Cards */
.wc-card-custom {
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 12px !important;
    box-shadow: var(--wc-shadow) !important;
    transition: all 0.25s ease !important;
}

.wc-table-card {
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: var(--wc-shadow) !important;
    transition: all 0.25s ease !important;
}

/* Status Slider Bar */
.wc-status-bar {
    background: var(--wc-surface) !important;
    border-radius: 12px !important;
    padding: 10px 14px !important;
    border: 1px solid var(--wc-border) !important;
    box-shadow: var(--wc-shadow) !important;
}
.status-tabs-container {
    display: flex !important;
    gap: 8px !important;
    overflow-x: auto !important;
    scroll-behavior: smooth !important;
    white-space: nowrap !important;
    scrollbar-width: none !important;
    padding: 2px 0 !important;
}
.status-tabs-container::-webkit-scrollbar { display: none !important; }
.status-pill-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 6px 14px !important;
    border-radius: 20px !important;
    background: var(--wc-btn-outline-bg) !important;
    color: var(--wc-text-body) !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    border: 1px solid var(--wc-btn-outline-border) !important;
}
.status-pill-item:hover {
    background: var(--wc-surface-hover) !important;
    color: var(--wc-text-heading) !important;
    transform: translateY(-1px) !important;
}
.status-pill-item.active {
    background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%) !important;
    color: #ffffff !important;
    border-color: #a855f7 !important;
    box-shadow: 0 3px 12px var(--wc-purple-glow) !important;
}
.status-pill-item .status-badge {
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 2px 8px !important;
    border-radius: 10px !important;
    background: rgba(0, 0, 0, 0.12) !important;
    color: var(--wc-text-heading) !important;
}
.status-pill-item.active .status-badge {
    background: #ffffff !important;
    color: #7c3aed !important;
}
.scroll-btn-wc {
    border-radius: 50% !important;
    width: 32px !important;
    height: 32px !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: var(--wc-btn-outline-bg) !important;
    color: var(--wc-text-body) !important;
    border: 1px solid var(--wc-btn-outline-border) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}
.scroll-btn-wc:hover {
    background: var(--wc-surface-hover) !important;
    color: var(--wc-text-heading) !important;
}

/* Inputs & Form Controls */
.input-group-custom {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
}
.input-group-custom .input-icon {
    position: absolute !important;
    left: 12px !important;
    color: var(--wc-text-muted) !important;
    z-index: 5 !important;
}
.form-control-custom {
    background: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    color: var(--wc-input-text) !important;
    border-radius: 8px !important;
    padding: 9px 12px 9px 36px !important;
    width: 100% !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    transition: all 0.2s !important;
}
.form-control-custom:focus {
    background: var(--wc-input-bg) !important;
    border-color: var(--wc-purple) !important;
    outline: none !important;
    box-shadow: 0 0 0 3px var(--wc-purple-glow) !important;
    color: var(--wc-input-text) !important;
}
.form-select-custom {
    background-color: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    color: var(--wc-input-text) !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    width: 100% !important;
    transition: all 0.2s !important;
}
.form-select-custom:focus {
    background-color: var(--wc-input-bg) !important;
    border-color: var(--wc-purple) !important;
    outline: none !important;
    color: var(--wc-input-text) !important;
}

/* Buttons */
.btn-purple-gradient {
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%) !important;
    color: #ffffff !important;
    border: 1px solid #7c3aed !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 8px var(--wc-purple-glow) !important;
    transition: all 0.2s !important;
}
.btn-purple-gradient:hover {
    background: linear-gradient(135deg, #6d28d9 0%, #7e22ce 100%) !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
}
.btn-outline-custom {
    background: var(--wc-btn-outline-bg) !important;
    border: 1px solid var(--wc-btn-outline-border) !important;
    color: var(--wc-btn-outline-text) !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    transition: all 0.2s !important;
}
.btn-outline-custom:hover {
    background: var(--wc-surface-hover) !important;
    color: var(--wc-text-heading) !important;
    border-color: var(--wc-purple) !important;
}

/* Dropdown Menu */
.dropdown-menu {
    border-radius: 10px !important;
    padding: 6px 0 !important;
}
.dropdown-menu.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}
.dropdown-item {
    padding: 8px 16px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    transition: background 0.15s ease !important;
}
.dropdown-item:hover {
    background-color: var(--wc-surface-hover) !important;
    color: var(--wc-purple) !important;
}

/* Clean, Modern, Reliable Custom Checkbox */
.wc-checkbox {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    width: 20px !important;
    min-width: 20px !important;
    height: 20px !important;
    border-radius: 5px !important;
    background-color: var(--wc-input-bg) !important;
    border: 2px solid var(--wc-input-border) !important;
    cursor: pointer !important;
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    vertical-align: middle !important;
    transition: all 0.15s ease-in-out !important;
    outline: none !important;
    padding: 0 !important;
    margin: 0 !important;
}
.wc-checkbox:hover {
    border-color: var(--wc-purple) !important;
    box-shadow: 0 0 0 2px var(--wc-purple-glow) !important;
}
.wc-checkbox:checked {
    background-color: #7c3aed !important;
    border-color: #7c3aed !important;
}
.wc-checkbox:checked::after {
    content: '' !important;
    position: absolute !important;
    left: 6px !important;
    top: 2px !important;
    width: 5px !important;
    height: 10px !important;
    border: solid #ffffff !important;
    border-width: 0 2.5px 2.5px 0 !important;
    transform: rotate(45deg) !important;
    display: block !important;
}

/* ==========================================================================
   DYNAMIC TABLE STYLES
   ========================================================================== */
.table-responsive {
    background: var(--wc-surface) !important;
    border-radius: 12px !important;
}
.wc-dynamic-table,
table.wc-dynamic-table {
    background-color: var(--wc-surface) !important;
    color: var(--wc-text-body) !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    width: 100% !important;
    margin-bottom: 0 !important;
}
.wc-dynamic-table > :not(caption) > * > * {
    background-color: transparent !important;
    box-shadow: none !important;
}
.wc-dynamic-table thead,
.wc-dynamic-table thead tr {
    background-color: var(--wc-table-th-bg) !important;
}
.wc-dynamic-table thead th {
    background-color: var(--wc-table-th-bg) !important;
    color: var(--wc-table-th-text) !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    border-bottom: 1px solid var(--wc-table-border) !important;
    border-top: none !important;
    padding: 14px 12px !important;
    white-space: nowrap !important;
}
.wc-dynamic-table tbody tr {
    background-color: var(--wc-surface) !important;
    border-bottom: 1px solid var(--wc-table-border) !important;
    transition: background-color 0.15s ease !important;
}
.wc-dynamic-table tbody tr:hover {
    background-color: var(--wc-surface-hover) !important;
}
.wc-dynamic-table tbody td {
    background-color: transparent !important;
    color: var(--wc-text-body) !important;
    border-bottom: 1px solid var(--wc-table-border) !important;
    border-top: none !important;
    padding: 14px 12px !important;
    vertical-align: middle !important;
}

/* Typography & Cell Details */
.wc-order-date {
    color: var(--wc-text-heading) !important;
    font-weight: 700 !important;
    font-size: 13px !important;
}
.wc-order-id {
    color: var(--wc-text-muted) !important;
    font-size: 11px !important;
    font-weight: 600 !important;
}
.phone-link {
    color: var(--wc-cyan) !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    text-decoration: none !important;
}
.phone-link:hover {
    text-decoration: underline !important;
}
.customer-name-text {
    color: var(--wc-text-heading) !important;
    font-weight: 700 !important;
    font-size: 13px !important;
}
.address-snippet {
    color: var(--wc-text-muted) !important;
    font-size: 11px !important;
    line-height: 1.4 !important;
}
.note-time-text {
    color: var(--wc-purple) !important;
    font-weight: 700 !important;
    font-size: 11px !important;
}
.note-content-text {
    color: var(--wc-text-body) !important;
    font-size: 12px !important;
}
.product-price-accent {
    color: var(--wc-purple) !important;
    font-weight: 700 !important;
    font-size: 12px !important;
}
.whatsapp-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #25d366 !important;
    font-size: 16px !important;
    transition: transform 0.2s !important;
}
.whatsapp-btn:hover {
    transform: scale(1.25) !important;
    color: #16a34a !important;
}
.btn-copy-mini {
    background: transparent !important;
    border: none !important;
    color: var(--wc-text-muted) !important;
    font-size: 12px !important;
    cursor: pointer !important;
    padding: 0 4px !important;
}
.btn-copy-mini:hover { color: var(--wc-purple) !important; }

/* Product Item Box */
.product-thumb-box {
    width: 46px !important;
    height: 46px !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}
.product-thumb {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
.sku-badge {
    background: var(--wc-sku-bg) !important;
    color: var(--wc-sku-text) !important;
    border: 1px solid var(--wc-sku-border) !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
}

/* Circular Success Rate Gauge */
.circle-progress-wrapper {
    width: 34px !important;
    height: 34px !important;
    position: relative !important;
    flex-shrink: 0 !important;
}
.circular-chart-svg {
    display: block !important;
    width: 100% !important;
    height: 100% !important;
}
.circular-chart-svg .circle-bg {
    fill: none !important;
    stroke: var(--wc-circle-bg) !important;
    stroke-width: 3.5 !important;
}
.circular-chart-svg .circle-bar {
    fill: none !important;
    stroke: #10b981 !important;
    stroke-width: 3.5 !important;
    stroke-linecap: round !important;
}
.rating-text-meta {
    font-size: 11px !important;
    line-height: 1.3 !important;
}

.btn-tag-add {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    background: var(--wc-btn-outline-bg) !important;
    border: 1px dashed var(--wc-border) !important;
    color: var(--wc-text-muted) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
}
.btn-tag-add:hover {
    background: var(--wc-surface-hover) !important;
    color: var(--wc-text-heading) !important;
    border-color: var(--wc-purple) !important;
}

.site-badge {
    background: var(--wc-sku-bg) !important;
    border: 1px solid var(--wc-border) !important;
    color: var(--wc-text-heading) !important;
    font-size: 11px !important;
    padding: 3px 10px !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
}

.btn-open-order {
    background: var(--wc-btn-outline-bg) !important;
    border: 1px solid var(--wc-purple) !important;
    color: var(--wc-purple) !important;
    border-radius: 8px !important;
    padding: 6px 16px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    transition: all 0.2s !important;
    box-shadow: 0 2px 6px var(--wc-purple-glow) !important;
}
.btn-open-order:hover {
    background: var(--wc-purple) !important;
    color: #ffffff !important;
    border-color: var(--wc-purple) !important;
    box-shadow: 0 4px 12px var(--wc-purple-glow) !important;
    transform: translateY(-1px) !important;
}

.btn-page-nav {
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    color: var(--wc-text-body) !important;
    border-radius: 6px !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.2s !important;
}
.btn-page-nav:hover:not(.disabled) {
    background: var(--wc-surface-hover) !important;
    color: var(--wc-text-heading) !important;
}
.btn-page-nav.disabled {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
}
.page-current-indicator {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--wc-text-heading) !important;
    padding: 0 6px !important;
}
.border-top-dynamic {
    border-top: 1px solid var(--wc-border) !important;
}
.text-purple { color: var(--wc-purple) !important; }
.border-left-purple { border-left: 4px solid var(--wc-purple) !important; }
</style>
@endpush

@section('content')
<div class="wc-theme-wrapper py-3">
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--wc-text-heading);">
                <i class="fa fa-shopping-bag text-purple me-2"></i> WooCommerce Orders
            </h4>
            <span class="badge rounded-pill px-3 py-1 font-weight-bold" style="background: var(--wc-btn-outline-bg); color: var(--wc-text-heading); border: 1px solid var(--wc-border);">
                {{ $orders->total() }} Total
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('invoice.online.sale') }}" class="btn btn-outline-custom btn-sm">
                <i class="fa fa-globe text-info me-1"></i> Online Sale List
            </a>
            <a href="{{ route('woocommerce.scanner') }}" class="btn btn-outline-custom btn-sm text-purple">
                <i class="fa fa-barcode me-1"></i> Scan To Update
            </a>
        </div>
    </div>

    @if(session('error') || session('message') || session('success'))
        @php
            $isError = session('error') || session('alert-type') == 'error';
            $msg = session('error') ?: session('message') ?: session('success');
            $alertClass = $isError ? 'alert-danger' : 'alert-success';
            $icon = $isError ? 'fa-exclamation-triangle text-danger' : 'fa-check-circle text-success';
        @endphp
        <div class="alert {{ $alertClass }} alert-dismissible fade show mb-3 p-3 rounded-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa {{ $icon }} fa-lg me-2"></i>
                <div style="font-size: 14px; line-height: 1.5;">{!! nl2br(e($msg)) !!}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $statusList = [
            'Processing' => ['label' => 'Processing', 'icon' => '🟡'],
            'Good But No Response' => ['label' => 'Good But No Response', 'icon' => '👍'],
            'No Response' => ['label' => 'No Response', 'icon' => '🚫'],
            'Advance Payment' => ['label' => 'Advance Payment', 'icon' => '📝'],
            'On Hold' => ['label' => 'On Hold', 'icon' => '⏸️'],
            'Approved' => ['label' => 'Approved', 'icon' => '✔️'],
            'Cancelled' => ['label' => 'Cancelled', 'icon' => '❌'],
            'ALL' => ['label' => 'ALL', 'icon' => '📋'],
        ];
        $currentStatus = request('WooCommerceStatus', $statusFilter ?? 'Processing');
    @endphp

    <!-- WooCommerce Status Header Pills Bar (Slider) -->
    <div class="wc-status-bar mb-3">
        <div class="d-flex align-items-center position-relative">
            <button type="button" class="scroll-btn-wc me-2" onclick="scrollWcStatusTabs(-200)" title="Scroll Left">
                <i class="fa fa-chevron-left"></i>
            </button>
            
            <div class="status-tabs-wrapper flex-grow-1 overflow-hidden">
                <div class="status-tabs-container" id="wcStatusTabsContainer">
                    @foreach($statusList as $statusKey => $statusData)
                        @php
                            $isActive = ($currentStatus === $statusKey);
                            $count = $statusCounts[$statusKey] ?? 0;
                            $queryParams = array_merge(request()->query(), ['WooCommerceStatus' => $statusKey]);
                            $url = route('woocommerce.orders.list', $queryParams);
                        @endphp
                        <a href="{{ $url }}" class="status-pill-item {{ $isActive ? 'active' : '' }}" data-status="{{ $statusKey }}">
                            <span class="status-icon">{{ $statusData['icon'] }}</span>
                            <span class="status-label">{{ $statusData['label'] }}</span>
                            <span class="status-badge">{{ $count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <button type="button" class="scroll-btn-wc ms-2" onclick="scrollWcStatusTabs(200)" title="Scroll Right">
                <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Top Filter Bar & Controls -->
    <div class="wc-card-custom mb-3 p-3">
        <form action="{{ route('woocommerce.orders.list') }}" method="GET" id="wcOrderSearchForm" class="row g-2 align-items-center">
            <div class="col-lg-5 col-md-6">
                <div class="input-group-custom">
                    <span class="input-icon"><i class="fa fa-search"></i></span>
                    <input type="text" name="search" id="wcSearchInput" class="form-control-custom" 
                           value="{{ request('search') }}" 
                           placeholder="Filter orders by ID, Phone, Customer, SKU..." 
                           autocomplete="off">
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <select name="WooCommerceStatus" class="form-select-custom">
                    @foreach($statusList as $sKey => $sData)
                        <option value="{{ $sKey }}" {{ $currentStatus == $sKey ? 'selected' : '' }}>
                            {{ $sData['icon'] }} {{ $sKey === 'ALL' ? 'ALL (All Statuses)' : $sData['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-custom"><i class="fa fa-filter me-1"></i> Filter</button>
                @if(request()->filled('search') || (request()->filled('WooCommerceStatus') && request('WooCommerceStatus') !== 'Processing'))
                    <a href="{{ route('woocommerce.orders.list') }}" class="btn btn-outline-custom text-muted"><i class="fa fa-times me-1"></i> Clear</a>
                @endif
                <div class="dropdown ms-auto" id="wcBulkDropdownWrapper" style="position: relative;">
                    <button class="btn btn-outline-custom dropdown-toggle" type="button" id="btnBulkActionsDropdown" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-layer-group me-1"></i> Bulk Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border" aria-labelledby="btnBulkActionsDropdown" style="background: var(--wc-surface); border-color: var(--wc-border); min-width: 220px; z-index: 1060; right: 0; left: auto;">
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" style="color: var(--wc-text-heading); cursor: pointer;" href="javascript:void(0)" onclick="triggerBulkCourierSubmit()">
                                <i class="fa fa-paper-plane me-2 text-primary"></i> Send Selected to Courier
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" style="color: var(--wc-text-heading); cursor: pointer;" href="javascript:void(0)" onclick="selectAllOrdersToggle()">
                                <i class="fa fa-check-square me-2 text-info"></i> Select / Unselect All
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" style="color: var(--wc-text-heading); cursor: pointer;" href="javascript:void(0)" onclick="pushStatusesToWooCommerce()">
                                <i class="fa fa-cloud-upload-alt me-2 text-success"></i> Push Status to WooCommerce
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1" style="border-color: var(--wc-border);"></li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" style="color: var(--wc-text-heading); cursor: pointer;" href="javascript:void(0)" onclick="manualSyncOrders()">
                                <i class="fa fa-sync me-2 text-purple"></i> Sync New Orders
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Courier Action Form -->
    <form id="bulkCourierForm" action="{{ route('woocommerce.orders.sendToOnline') }}" method="POST">
        @csrf
        <input type="hidden" name="select_all_matching" id="selectAllMatchingInput" value="0">
        <input type="hidden" name="all_matching_ids" id="allMatchingIdsInput" value="{{ implode(',', $allMatchingIds ?? []) }}">
        
        <div class="wc-card-custom mb-3 p-3 border-left-purple" style="background: var(--wc-surface-alt) !important;">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small font-weight-bold mb-1" style="color: var(--wc-text-heading);">
                        <i class="fa fa-truck text-purple me-1"></i> Courier Service:
                    </label>
                    @php
                        $activeCouriers = getActiveCouriers();
                    @endphp
                    <select name="courier_type" class="form-select-custom" required>
                        @foreach($activeCouriers as $cKey => $cLabel)
                            <option value="{{ $cKey }}" {{ $loop->first ? 'selected' : '' }}>{{ $cLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small font-weight-bold mb-1" style="color: var(--wc-text-heading);">
                        <i class="fa fa-boxes text-info me-1"></i> Stock Transfer Mode:
                    </label>
                    <select name="stock_mode" class="form-select-custom" required>
                        <option value="without_manage" selected>Without Manage Stock (Direct Transfer)</option>
                        <option value="with_manage">Manage Stock (Check Inventory First)</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 pb-2">
                    <div class="form-check mb-0">
                        <input class="form-check-input wc-checkbox" type="checkbox" name="send_to_courier_api" id="chkSendApi" value="1">
                        <label class="form-check-label small font-weight-bold ms-1" for="chkSendApi" style="color: var(--wc-text-heading);">
                            Send to Courier API
                        </label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-lg-end">
                    <button type="submit" id="btn-submit-courier" class="btn btn-purple-gradient px-3 font-weight-bold w-100 w-lg-auto">
                        <i class="fa fa-paper-plane me-1"></i> Send Selected Orders
                    </button>
                </div>
            </div>
        </div>

        <!-- Select All Across Pages Banner -->
        <div id="selectAllPagesBanner" class="alert py-2 px-3 mb-2 shadow-sm d-none align-items-center justify-content-between" style="border-radius: 8px; font-size: 13px; background: var(--wc-surface-alt); border: 1.5px solid #10b981; color: var(--wc-text-heading);">
            <div class="d-flex align-items-center gap-2">
                <i class="fa fa-check-circle text-success fs-5"></i>
                <span id="selectAllBannerText">
                    All <strong class="text-success" id="totalOrdersCountText">{{ $orders->total() }}</strong> orders across all pages are selected.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 font-weight-bold" id="btnClearAllPages" onclick="clearAllOrdersSelection()" style="border-radius: 6px; font-size: 12px;">
                    <i class="fa fa-times me-1"></i> Clear selection
                </button>
            </div>
        </div>

        <!-- Orders Table with Dynamic Theme matching screenshot -->
        <div class="wc-table-card">
            <div class="table-responsive">
                <table class="table wc-dynamic-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="45" class="ps-3 wc-check-cell" style="cursor: pointer;">
                                <input type="checkbox" id="selectAllOrders" class="wc-checkbox" title="Select All">
                            </th>
                            <th>Created At <i class="fa fa-sort ms-1 small text-muted"></i></th>
                            <th>Customer</th>
                            <th>Note</th>
                            <th>Order Items</th>
                            <th>Success Rate <i class="fa fa-sort ms-1 small text-muted"></i></th>
                            <th>Tags</th>
                            <th>Site</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key => $order)
                            @php
                                $custPhone = $order->customer->phone ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
                                $waPhone = str_starts_with($cleanPhone, '88') ? $cleanPhone : ('88' . $cleanPhone);
                                $itemCount = $order->invoiceItems->count();
                                $firstItem = $order->invoiceItems->first();
                                $product = $firstItem?->product;
                                
                                $productImage = $product?->images;
                                if ($productImage && !str_starts_with($productImage, 'http')) {
                                    $productImage = asset($productImage);
                                }
                                if (!$productImage) {
                                    $productImage = asset('backend/images/no_images.png');
                                }

                                $sku = $product?->barcode ?: ($product?->woocommerce_product_id ?: ($firstItem?->new_details ?: '12930'));
                                $itemPrice = $firstItem?->rate ?? $order->total_amount;
                                $itemQty = (int)($firstItem?->main_qty ?? 1);

                                // Dynamic customer success rate & stats
                                $stat = $customerStats[$order->id] ?? [
                                    'total' => 1,
                                    'delivered' => 1,
                                    'cancelled' => 0,
                                    'rate' => 100,
                                    'rating' => 80
                                ];

                                $successRate = (int)($stat['rate'] ?? 100);
                                $deliveredCount = (int)($stat['delivered'] ?? 1);
                                $totalOrdersCount = (int)($stat['total'] ?? 1);
                                $ratingScore = (int)($stat['rating'] ?? 80);

                                if ($successRate >= 80) {
                                    $rateColor = '#10b981';
                                    $rateTextClass = 'text-success fw-bold';
                                } elseif ($successRate >= 50) {
                                    $rateColor = '#eab308';
                                    $rateTextClass = 'text-warning fw-bold';
                                } else {
                                    $rateColor = '#ef4444';
                                    $rateTextClass = 'text-danger fw-bold';
                                }

                                // Relative time
                                $timeDiff = $order->created_at ? $order->created_at->diffForHumans() : 'Recently';
                                $formattedDate = date('d M, h:i a', strtotime($order->created_at ?: $order->date));
                                $orderDisplayId = $order->woocommerce_order_id ?: ($order->invoice_no ?: $order->id);
                            @endphp
                            <tr class="wc-row" data-order-id="{{ $order->id }}">
                                <td class="ps-3 wc-check-cell" style="cursor: pointer;">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="wc-checkbox order-select-chk">
                                </td>
                                <td>
                                    <div class="wc-order-date">{{ $formattedDate }}</div>
                                    <div class="wc-order-id">ID: {{ $orderDisplayId }}</div>
                                </td>
                                <td>
                                    <div class="customer-cell">
                                        @if(!empty($custPhone))
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <a href="tel:{{ $custPhone }}" class="phone-link d-flex align-items-center" title="Call">
                                                    <i class="fa fa-phone-alt me-1 small"></i> {{ $custPhone }}
                                                </a>
                                                <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="whatsapp-btn" title="Chat on WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <i class="fa fa-user text-muted small"></i>
                                            <span class="customer-name-text">{{ $order->customer->name ?? 'Customer' }}</span>
                                            <button type="button" class="btn-copy-mini" onclick="copyToClipboard('{{ addslashes($order->customer->name ?? '') }}')" title="Copy Name">
                                                <i class="fa fa-clone"></i>
                                            </button>
                                        </div>
                                        @if(!empty($order->customer->address))
                                            <div class="address-snippet text-truncate" style="max-width: 220px;" title="{{ $order->customer->address }}">
                                                <i class="fa fa-map-marker-alt text-danger me-1"></i> {{ $order->customer->address }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="note-cell">
                                        @php
                                            $cleanOrderNote = trim(preg_replace([
                                                '/^Imported from WooCommerce[^\n]*\r?\n?/mi',
                                                '/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Status changed[^\n]*\r?\n?/mi',
                                                '/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Order approved[^\n]*\r?\n?/mi',
                                                '/^\d{1,2} [A-Za-z]+, \d{1,2}:\d{2} [AP]M: Scanned & updated[^\n]*\r?\n?/mi'
                                            ], '', $order->note ?? ''));
                                        @endphp
                                        <div class="note-time-text">Updated {{ $timeDiff }}</div>
                                        <div class="note-content-text text-truncate" style="max-width: 170px;">
                                            {{ $cleanOrderNote ? Str::limit($cleanOrderNote, 35) : '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="product-thumb-box">
                                            <img src="{{ $productImage }}" alt="Product" class="product-thumb" onerror="this.src='{{ asset('backend/images/no_images.png') }}'">
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="sku-badge">{{ $sku }}</span>
                                                <span class="small fw-bold" style="color: var(--wc-text-heading);">{{ $itemQty }}x</span>
                                            </div>
                                            <div class="product-price-accent mt-1">৳{{ number_format($itemPrice) }}</div>
                                            @if($itemCount > 1)
                                                <small class="text-muted">+{{ $itemCount - 1 }} more</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <!-- Dynamic Success Rate Gauge -->
                                    <div class="d-flex align-items-center gap-2 rating-gauge-box" 
                                         onclick="openCustomerFraudModal('{{ $custPhone }}', '{{ e($order->customer->name ?? 'Customer') }}')" 
                                         style="cursor: pointer;" title="Click for courier history profile">
                                        <div class="circle-progress-wrapper">
                                            <svg viewBox="0 0 36 36" class="circular-chart-svg">
                                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                <path class="circle-bar" style="stroke: {{ $rateColor }} !important;" stroke-dasharray="{{ $successRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                            </svg>
                                        </div>
                                        <div class="rating-text-meta">
                                            <div class="{{ $rateTextClass }}">Success: {{ $successRate }}%</div>
                                            <div class="text-muted small">Order: {{ $deliveredCount }}/{{ $totalOrdersCount }}</div>
                                            <div class="text-muted small">Rating: {{ $ratingScore }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn-tag-add" title="Add Tag">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                                <td>
                                    <span class="site-badge">Main</span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('woocommerce.orders.details', $order->id) }}" class="btn-open-order">
                                        Open <i class="fa fa-external-link-alt ms-1 small"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa fa-shopping-bag fa-3x mb-3 text-secondary d-block"></i>
                                    <h5 class="fw-bold" style="color: var(--wc-text-heading);">No WooCommerce Orders Found</h5>
                                    <p class="small mb-0 text-muted">Click "Sync New Orders" to fetch the latest orders from WooCommerce.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modern Pagination Footer -->
            <div class="wc-pagination-footer d-flex flex-wrap justify-content-between align-items-center p-3 border-top-dynamic" style="background: var(--wc-surface);">
                <div class="text-muted small font-weight-bold">
                    Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }} (Total {{ $orders->total() }} orders)
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small font-weight-bold">Rows per page</span>
                        <select class="form-select-custom form-select-sm" style="width: 75px;" onchange="changePerPage(this.value)">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ (!request('per_page') || request('per_page') == 20) ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="pagination-buttons d-flex align-items-center gap-1">
                        @if($orders->onFirstPage())
                            <button type="button" class="btn-page-nav disabled"><i class="fa fa-chevron-left"></i> Previous</button>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="btn-page-nav"><i class="fa fa-chevron-left"></i> Previous</a>
                        @endif

                        <span class="page-current-indicator">Page {{ $orders->currentPage() }}</span>

                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="btn-page-nav">Next <i class="fa fa-chevron-right"></i></a>
                        @else
                            <button type="button" class="btn-page-nav disabled">Next <i class="fa fa-chevron-right"></i></button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Customer Courier Fraud Details Modal -->
<div class="modal fade" id="customerFraudModal" tabindex="-1" role="dialog" aria-labelledby="customerFraudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden; background: var(--wc-surface); border: 1px solid var(--wc-border);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 16px 20px;">
                <h5 class="modal-title text-white font-weight-bold d-flex align-items-center" id="customerFraudModalLabel">
                    <i class="fa fa-shield-alt me-2 text-info"></i> {{ __('Customer Courier Rating & Fraud Profile') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: var(--wc-bg);">
                <!-- Top Customer Summary Card -->
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3 border shadow-sm" style="background: var(--wc-surface); border-color: var(--wc-border) !important;">
                    <div>
                        <h6 class="font-weight-bold mb-1" style="color: var(--wc-text-heading);" id="cfm_cust_name">-</h6>
                        <div class="text-muted small"><i class="fa fa-phone me-1 text-success"></i> <span id="cfm_cust_phone" class="font-weight-bold" style="color: var(--wc-text-heading);">-</span></div>
                    </div>
                    <div class="text-end">
                        <span id="cfm_risk_badge" class="badge rounded-pill px-3 py-2" style="font-size: 13px;">-</span>
                        <div class="small font-weight-bold mt-1 text-muted">{{ __('Delivery Success Rate') }}: <span id="cfm_success_rate" class="text-success fw-bold">100%</span></div>
                    </div>
                </div>

                <!-- Warning Alert Box -->
                <div id="cfm_warning_alert" class="alert alert-danger shadow-sm mb-3" style="display: none; border-left: 5px solid #dc3545; border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                        <div id="cfm_warning_text" class="font-weight-bold"></div>
                    </div>
                </div>

                <!-- Courier Source Breakdown Table -->
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-2 border" style="background: var(--wc-surface); border-color: var(--wc-border) !important;">
                    <div class="card-header font-weight-bold border-bottom py-2" style="background: var(--wc-table-th-bg); color: var(--wc-text-heading); border-color: var(--wc-border) !important;">
                        <i class="fa fa-truck text-purple me-1"></i> {{ __('Courier Delivery History (Steadfast, Pathao & POS)') }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center" style="font-size: 13px;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--wc-border); color: var(--wc-table-th-text);">
                                    <th class="text-start">{{ __('Courier Source') }}</th>
                                    <th>{{ __('Total Parcels') }}</th>
                                    <th>{{ __('Delivered') }}</th>
                                    <th>{{ __('Cancelled / Returned') }}</th>
                                    <th>{{ __('Success Rate') }}</th>
                                </tr>
                            </thead>
                            <tbody id="cfm_sources_tbody">
                                <tr><td colspan="5" class="text-muted py-3">{{ __('Loading rating data...') }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 border-top" style="background: var(--wc-table-th-bg); border-color: var(--wc-border) !important;">
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        alert("Copied to clipboard: " + text);
    });
}

function triggerBulkCourierSubmit() {
    $('#wcBulkDropdownWrapper .dropdown-menu').removeClass('show');
    var count = $('.order-select-chk:checked').length;
    if (count === 0) {
        alert("অনুগ্রহ করে কুরিয়ার বা অনলাইন সেল লিস্টে পাঠানোর জন্য অন্তত একটি অর্ডার সিলেক্ট করুন। (Please select at least 1 order)");
        return;
    }
    $('#bulkCourierForm').submit();
}

var totalMatchingOrders = {{ (int)($orders->total() ?? 0) }};

function updateSelectedCountUI() {
    var isAllMatching = $('#selectAllMatchingInput').val() === '1';
    var checkedOnPage = $('.order-select-chk:checked').length;
    var totalOnPage = $('.order-select-chk').length;

    if (isAllMatching) {
        $('#btn-submit-courier').html('<i class="fa fa-paper-plane me-1"></i> Send Selected Orders (' + totalMatchingOrders + ')');
        $('#selectAllPagesBanner').removeClass('d-none').addClass('d-flex');
        $('#selectAllBannerText').html('All <strong class="text-success">' + totalMatchingOrders + '</strong> orders across all pages are selected.');
        $('#selectAllOrders').prop('checked', true);
    } else if (checkedOnPage > 0) {
        $('#btn-submit-courier').html('<i class="fa fa-paper-plane me-1"></i> Send Selected Orders (' + checkedOnPage + ')');
        $('#selectAllPagesBanner').addClass('d-none').removeClass('d-flex');
        $('#selectAllOrders').prop('checked', totalOnPage > 0 && checkedOnPage === totalOnPage);
    } else {
        $('#btn-submit-courier').html('<i class="fa fa-paper-plane me-1"></i> Send Selected Orders');
        $('#selectAllPagesBanner').addClass('d-none').removeClass('d-flex');
        $('#selectAllOrders').prop('checked', false);
    }
}

function clearAllOrdersSelection() {
    $('#selectAllMatchingInput').val('0');
    $('#selectAllOrders').prop('checked', false);
    $('.order-select-chk').prop('checked', false);
    updateSelectedCountUI();
}

function selectAllOrdersToggle() {
    $('#wcBulkDropdownWrapper .dropdown-menu').removeClass('show');
    var isChecked = !$('#selectAllOrders').is(':checked');
    $('#selectAllOrders').prop('checked', isChecked).trigger('change');
}

// Universal Dropdown Toggle Handler
$(document).on('click', '#btnBulkActionsDropdown', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $menu = $(this).siblings('.dropdown-menu');
    $('.dropdown-menu').not($menu).removeClass('show');
    $menu.toggleClass('show');
});

$(document).on('click', function(e) {
    if (!$(e.target).closest('#wcBulkDropdownWrapper').length) {
        $('#wcBulkDropdownWrapper .dropdown-menu').removeClass('show');
    }
});

function changePerPage(val) {
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

function scrollWcStatusTabs(offset) {
    const container = document.getElementById('wcStatusTabsContainer');
    if (container) {
        container.scrollBy({ left: offset, behavior: 'smooth' });
    }
}

function openCustomerFraudModal(phone, custName) {
    if (!phone) {
        alert("Phone number is required to check courier rating.");
        return;
    }

    $('#cfm_cust_name').text(custName || 'Customer');
    $('#cfm_cust_phone').text(phone);
    $('#cfm_risk_badge').text('Checking...').removeClass().addClass('badge rounded-pill bg-secondary px-3 py-2');
    $('#cfm_warning_alert').hide();
    $('#cfm_sources_tbody').html('<tr><td colspan="5" class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-purple"></i><br><span class="text-muted small mt-2 d-inline-block">Checking Courier Rating Profile...</span></td></tr>');
    $('#customerFraudModal').modal('show');

    var url = "{{ route('customer.fraud-check', ':phone') }}".replace(':phone', encodeURIComponent(phone));
    $.get(url, function(fc) {
        if (!fc || !fc.success) {
            $('#cfm_sources_tbody').html('<tr><td colspan="5" class="text-danger py-3">Failed to fetch courier rating data</td></tr>');
            return;
        }

        $('#cfm_cust_name').text(fc.customer_name || custName);
        $('#cfm_cust_phone').text(fc.phone || phone);
        $('#cfm_risk_badge').text(fc.risk_label).removeClass().addClass('badge rounded-pill px-3 py-2 ' + (fc.badge_class || 'bg-secondary'));
        if (fc.badge_bg) $('#cfm_risk_badge').css('background-color', fc.badge_bg).css('color', '#fff');

        $('#cfm_success_rate').text(fc.overall_success_rate + '%');

        if (fc.warning_message) {
            $('#cfm_warning_text').text(fc.warning_message);
            $('#cfm_warning_alert').show();
        } else {
            $('#cfm_warning_alert').hide();
        }

        var tbody = '';
        var sources = fc.sources || {};
        var sourceNames = {
            'internal': 'Internal POS Orders',
            'steadfast': 'Steadfast Courier API',
            'pathao': 'Pathao Courier API',
            'redx': 'RedX Courier',
            'carrybee': 'CarryBee Courier',
            'courier_api': 'Aggregator Courier API'
        };

        $.each(sources, function(key, s) {
            var total = s.total_orders || s.total_parcels || 0;
            var delivered = s.delivered || 0;
            var cancelled = s.returned || s.cancelled || 0;
            var rate = (s.success_rate !== null && s.success_rate !== undefined) ? s.success_rate + '%' : '-';
            var name = sourceNames[key] || key.toUpperCase();

            var badgeClass = s.success_rate >= 80 ? 'text-success fw-bold' : (s.success_rate >= 50 ? 'text-warning fw-bold' : (s.success_rate !== null ? 'text-danger fw-bold' : 'text-muted'));

            tbody += '<tr>' +
                '<td class="text-start font-weight-bold" style="color: var(--wc-text-heading);">' + name + '</td>' +
                '<td><span class="badge border px-2 py-1" style="background: var(--wc-btn-outline-bg); color: var(--wc-text-heading); border-color: var(--wc-border) !important;">' + total + '</span></td>' +
                '<td><span class="badge bg-success px-2 py-1">' + delivered + '</span></td>' +
                '<td><span class="badge bg-danger px-2 py-1">' + cancelled + '</span></td>' +
                '<td class="' + badgeClass + '">' + rate + '</td>' +
            '</tr>';
        });

        $('#cfm_sources_tbody').html(tbody);
    }).fail(function() {
        $('#cfm_sources_tbody').html('<tr><td colspan="5" class="text-danger py-3">Error connecting to Fraud Checker API</td></tr>');
    });
}

// Expose sync functions globally
window.manualSyncOrders = function() {
    var btn = $('#btnBulkActionsDropdown');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Syncing...');
    $.ajax({
        url: "{{ route('woocommerce.sync.orders') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", limit: 50 },
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
            alert(res.message);
            location.reload();
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
            alert("Error syncing orders from WooCommerce.");
        }
    });
};

window.pushStatusesToWooCommerce = function() {
    var selectedIds = [];
    $('.order-select-chk:checked').each(function() {
        selectedIds.push($(this).val());
    });

    var msg = selectedIds.length > 0 
        ? "Push status for " + selectedIds.length + " selected order(s) to WooCommerce store?" 
        : "Push status of all recent orders to WooCommerce store?";
    
    if (!confirm(msg)) return;

    var btn = $('#btnBulkActionsDropdown');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Pushing Status...');
    
    $.ajax({
        url: "{{ route('woocommerce.orders.pushStatuses') }}",
        type: "POST",
        data: { 
            _token: "{{ csrf_token() }}", 
            order_ids: selectedIds.join(',') 
        },
        success: function(res) {
            if (res.success) {
                btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
                alert(res.message || "WooCommerce status sync completed successfully!");
                location.reload();
                return;
            }

            // If server-to-server was challenged by CDN/WAF, seamlessly push directly from browser
            if (res.fallback_needed && res.credentials && res.items && res.items.length > 0) {
                btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Browser Direct Syncing (' + res.items.length + ')...');
                var creds = res.credentials;
                var promises = res.items.map(function(item) {
                    var endpoint = creds.url + '/wp-json/wc/v3/orders/' + item.id + '?consumer_key=' + encodeURIComponent(creds.consumer_key) + '&consumer_secret=' + encodeURIComponent(creds.consumer_secret);
                    return fetch(endpoint, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status: item.status })
                    }).then(function(r) { return r.json(); });
                });

                Promise.all(promises).then(function(results) {
                    btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
                    alert("Successfully synced " + results.length + " order status(es) directly to WooCommerce!");
                    location.reload();
                }).catch(function(err) {
                    btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
                    alert("WooCommerce update error: " + (err.message || "Failed"));
                });
                return;
            }

            btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
            alert(res.message || "Failed to update WooCommerce statuses.");
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Bulk Actions');
            alert("Error syncing statuses to WooCommerce: " + (xhr.responseJSON?.message || xhr.statusText));
        }
    });
};

$(document).ready(function() {
    // Select all checkbox
    $(document).on('change', '#selectAllOrders', function() {
        var isChecked = $(this).is(':checked');
        $('.order-select-chk').prop('checked', isChecked);
        if (isChecked) {
            $('#selectAllMatchingInput').val('1');
        } else {
            $('#selectAllMatchingInput').val('0');
        }
        updateSelectedCountUI();
    });

    // Row checkbox change updates state
    $(document).on('change', '.order-select-chk', function() {
        $('#selectAllMatchingInput').val('0');
        updateSelectedCountUI();
    });

    // Allow clicking anywhere inside the check cell to toggle checkbox
    $(document).on('click', '.wc-check-cell', function(e) {
        if (!$(e.target).is('input[type="checkbox"]')) {
            var $chk = $(this).find('input[type="checkbox"]');
            if ($chk.length) {
                $chk.prop('checked', !$chk.prop('checked')).trigger('change');
            }
        }
    });

    $('#bulkCourierForm').on('submit', function(e) {
        var isAllMatching = $('#selectAllMatchingInput').val() === '1';
        var checkedCount = $('.order-select-chk:checked').length;
        if (!isAllMatching && checkedCount === 0) {
            e.preventDefault();
            alert("অনুগ্রহ করে কুরিয়ার বা অনলাইন সেল লিস্টে পাঠানোর জন্য অন্তত একটি অর্ডার সিলেক্ট করুন।");
            return false;
        }
        var countText = isAllMatching ? totalMatchingOrders : checkedCount;
        $('#btn-submit-courier').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Sending ' + countText + ' order(s)...');
    });

    // Silent background auto-sync
    var autoSyncEnabled = {{ !empty($credentials['auto_sync']) ? 'true' : 'false' }};
    var autoSyncIntervalMin = {{ (int)($credentials['auto_sync_interval'] ?? 2) }};
    var isBackgroundSyncing = false;

    if (autoSyncEnabled) {
        setInterval(function() {
            if (isBackgroundSyncing) return;
            isBackgroundSyncing = true;

            $.ajax({
                url: "{{ route('woocommerce.sync.orders') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", limit: 50 },
                success: function(res) {
                    isBackgroundSyncing = false;
                    if (res.imported && res.imported > 0) {
                        location.reload();
                    }
                },
                error: function() {
                    isBackgroundSyncing = false;
                }
            });
        }, Math.max(1, autoSyncIntervalMin) * 60 * 1000);
    }
});
</script>
@endsection
