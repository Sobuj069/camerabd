@extends('backend.layouts.master')
@section('section-title', 'Website Orders List')
@section('page-title', 'Website Orders List')

@push('css')
<style>
    /* Styling to match user screenshots pixel-for-pixel */
    .website-orders-wrapper {
        font-family: inherit;
    }
    
    /* Top Header Bar */
    .order-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
    }
    .order-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }
    .order-header-title i {
        color: #6366f1;
        font-size: 18px;
    }
    .order-total-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .header-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .header-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .header-action-btn.btn-cyan {
        color: #0284c7;
        border-color: #bae6fd;
        background: #f0f9ff;
    }
    .header-action-btn.btn-cyan:hover {
        background: #e0f2fe;
    }
    .header-action-btn.btn-purple {
        color: #7c3aed;
        border-color: #ddd6fe;
        background: #f5f3ff;
    }
    .header-action-btn.btn-purple:hover {
        background: #ede9fe;
    }

    /* Carousel Tabs */
    .status-carousel-container {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        position: relative;
    }
    .carousel-nav-btn {
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
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .carousel-nav-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .status-pills-scroll {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE */
        padding: 4px 2px;
        flex: 1;
    }
    .status-pills-scroll::-webkit-scrollbar {
        display: none;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        font-size: 12.5px;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .status-pill:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .status-pill.active {
        background: #7c3aed;
        color: #ffffff;
        border-color: #7c3aed;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.35);
    }
    .status-pill.active .pill-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
    .pill-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 10px;
    }

    /* Filter Bar */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 14px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }
    .search-input-group {
        flex: 1;
        min-width: 260px;
        position: relative;
    }
    .search-input-group i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-input-group input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .search-input-group input:focus {
        background: #ffffff;
        border-color: #7c3aed;
        outline: none;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
    }
    .filter-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        font-weight: 500;
        background: #ffffff;
        min-width: 150px;
    }
    .btn-filter-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-filter-action:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    /* Date-Wise Filter Bar */
    .active-date-badge {
        background: #ede9fe;
        color: #6d28d9;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .date-filter-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 14px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
    }
    .date-presets-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }
    .date-presets-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-right: 4px;
    }
    .date-preset-btn {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 11px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .date-preset-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .date-preset-btn.active {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #ffffff;
        border-color: #6d28d9;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
    }
    .date-range-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .date-range-sep {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .date-input-modern {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: border-color 0.2s ease;
    }
    .date-input-modern:focus {
        border-color: #7c3aed;
        background: #ffffff;
    }
    .btn-date-apply {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 6px 13px;
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }
    .btn-date-apply:hover {
        opacity: 0.92;
    }
    .btn-date-reset {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 5px 11px;
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-date-reset:hover {
        background: #fee2e2;
        color: #b91c1c;
        text-decoration: none;
    }

    /* Courier & Stock Transfer Bar */
    .dispatch-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #7c3aed;
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .dispatch-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
    }
    .dispatch-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .dispatch-field label {
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }
    .dispatch-select {
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 12.5px;
        font-weight: 500;
        background: #ffffff;
        min-width: 220px;
    }
    .btn-send-orders {
        background: #7c3aed;
        color: #ffffff;
        border: none;
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        transition: all 0.2s ease;
    }
    .btn-send-orders:hover {
        background: #6d28d9;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.4);
    }

    /* Table Styling */
    .orders-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .table-modern {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 14px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12.5px;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background-color: #fafbfc;
    }

    /* Cell details */
    .cell-created-date {
        font-weight: 700;
        color: #1e293b;
        font-size: 12.5px;
    }
    .cell-order-id {
        font-size: 11px;
        color: #64748b;
        font-family: monospace;
        margin-top: 2px;
    }
    .customer-phone-link {
        color: #0284c7;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .customer-phone-link:hover {
        color: #0369a1;
        text-decoration: underline;
    }
    .customer-whatsapp-btn {
        color: #10b981;
        font-size: 14px;
        margin-left: 2px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .customer-whatsapp-btn:hover {
        transform: scale(1.2);
    }
    .customer-name-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 3px;
    }
    .customer-address-row {
        display: flex;
        align-items: flex-start;
        gap: 4px;
        font-size: 11px;
        color: #64748b;
        margin-top: 3px;
        max-width: 250px;
        line-height: 1.3;
    }
    .customer-address-row i {
        color: #ef4444;
        margin-top: 2px;
    }
    .copy-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 2px;
        font-size: 11px;
        transition: color 0.2s;
    }
    .copy-btn:hover {
        color: #64748b;
    }

    /* Note cell */
    .note-cell-updated {
        color: #7c3aed;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .note-text-editable {
        font-size: 12px;
        color: #475569;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 4px;
        display: inline-block;
    }
    .note-text-editable:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    /* Item thumbnail */
    .item-preview-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .item-thumbnail {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        object-fit: cover;
        background: #f8fafc;
    }
    .item-details-box {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .item-code-qty {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
    }
    .item-price {
        font-size: 12px;
        font-weight: 700;
        color: #7c3aed;
    }

    /* Success rate circular progress */
    .success-rate-box {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .success-rate-ring {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
        border-top-color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 800;
        color: #64748b;
    }
    .rate-info-text {
        font-size: 11px;
        line-height: 1.25;
    }
    .rate-percent {
        font-weight: 700;
        color: #ef4444;
    }

    /* Site badge */
    .badge-site {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }

    /* Open action button */
    .btn-open-action {
        border: 1px solid #c4b5fd;
        background: #f5f3ff;
        color: #7c3aed;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-open-action:hover {
        background: #7c3aed;
        color: #ffffff;
    }
    .btn-quick-status {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
    }

    /* Stock indicator badge in details modal */
    .stock-badge-in {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }
    .stock-badge-out {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="website-orders-wrapper">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather icon-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="feather icon-alert-triangle mr-1"></i> {!! nl2br(e(session('error'))) !!}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @php
        $hasDateFilter = !empty($datePreset) && $datePreset !== 'all';
        $hasAnyFilter = $hasDateFilter || !empty($search) || !empty($courierFilter);

        $formattedDateRange = null;
        if ($hasDateFilter) {
            if (!empty($startDate) && !empty($endDate)) {
                $formattedDateRange = ($startDate === $endDate)
                    ? \Carbon\Carbon::parse($startDate)->format('d M Y')
                    : \Carbon\Carbon::parse($startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y');
            } elseif (!empty($startDate)) {
                $formattedDateRange = 'From ' . \Carbon\Carbon::parse($startDate)->format('d M Y');
            } elseif (!empty($endDate)) {
                $formattedDateRange = 'Until ' . \Carbon\Carbon::parse($endDate)->format('d M Y');
            }
        }

        $pillBaseQuery = array_filter([
            'date_preset' => $hasDateFilter ? $datePreset : null,
            'start_date' => ($datePreset === 'custom' && !empty($startDate)) ? $startDate : null,
            'end_date' => ($datePreset === 'custom' && !empty($endDate)) ? $endDate : null,
            'search' => !empty($search) ? $search : null,
            'courier_service' => !empty($courierFilter) ? $courierFilter : null,
        ], fn($v) => $v !== null && $v !== '');

        $datePresets = [
            'all'          => 'All Time',
            'today'        => 'Today',
            'yesterday'    => 'Yesterday',
            'last_7_days'  => 'Last 7 Days',
            'last_30_days' => 'Last 30 Days',
            'this_month'   => 'This Month',
            'last_month'   => 'Last Month',
        ];

        $statusTabs = [
            ['key' => 'processing',       'label' => 'Processing',           'dot' => '#f59e0b', 'option' => '🟡 Processing'],
            ['key' => 'good_no_response', 'label' => 'Good But No Response', 'icon' => '👍',     'option' => '👍 Good But No Response'],
            ['key' => 'no_response',      'label' => 'No Response',          'icon' => '🚫', 'color' => '#ef4444', 'option' => '🚫 No Response'],
            ['key' => 'advance_payment',  'label' => 'Advance Payment',      'icon' => '📝',     'option' => '📝 Advance Payment'],
            ['key' => 'on_hold',          'label' => 'On Hold',              'icon' => '⏸️', 'color' => '#0284c7', 'option' => '⏸️ On Hold'],
            ['key' => 'approved',         'label' => 'Approved',             'icon' => '✔',  'color' => '#10b981', 'option' => '✔ Approved'],
            ['key' => 'delivered',        'label' => 'Delivered',            'icon' => '📦',     'option' => '📦 Delivered'],
            ['key' => 'cancelled',        'label' => 'Cancelled',            'icon' => '❌', 'color' => '#ef4444', 'option' => '❌ Cancelled'],
            ['key' => 'all',              'label' => 'ALL',                  'icon' => '📋',     'option' => '📋 All Orders'],
        ];
    @endphp

    {{-- Top Header Bar --}}
    <div class="order-header-bar">
        <div class="order-header-title">
            <i class="feather icon-shopping-bag"></i>
            <span>{{ __('Website Orders') }}</span>
            <span class="order-total-badge">
                @if($hasAnyFilter)
                    {{ $counts['all'] }} {{ __('Matched') }} <span style="opacity: 0.75; font-weight: 500;">/ {{ $overallTotal }} {{ __('Total') }}</span>
                @else
                    {{ $counts['all'] }} {{ __('Total') }}
                @endif
            </span>
            @if(!empty($formattedDateRange))
                <span class="active-date-badge">
                    <i class="feather icon-calendar"></i>
                    {{ $formattedDateRange }}
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2" style="gap: 10px;">
            <a href="{{ route('invoice.online.sale') }}" class="header-action-btn btn-cyan">
                <i class="feather icon-globe"></i>
                <span>{{ __('Online Sale List') }}</span>
            </a>
            <a href="{{ url('/') }}" target="_blank" class="header-action-btn btn-purple">
                <i class="feather icon-external-link"></i>
                <span>{{ __('Visit Website Store') }}</span>
            </a>
        </div>
    </div>

    {{-- Filter Form Wrapping Date Filter Bar, Status Tabs & Search --}}
    <form action="{{ route('website-orders.index') }}" method="GET" id="filterForm">
        <input type="hidden" name="date_preset" id="filterDatePreset" value="{{ $datePreset ?? 'all' }}">
        @if(!empty($courierFilter))
            <input type="hidden" name="courier_service" value="{{ $courierFilter }}">
        @endif

        {{-- Date-Wise Filter Bar --}}
        <div class="date-filter-bar">
            <div class="date-presets-group">
                <span class="date-presets-label">
                    <i class="feather icon-calendar" style="color: #7c3aed;"></i>
                    {{ __('Date:') }}
                </span>
                @foreach($datePresets as $presetKey => $presetLabel)
                    <button type="button"
                            class="date-preset-btn {{ ($datePreset ?? 'all') === $presetKey ? 'active' : '' }}"
                            onclick="selectDatePreset('{{ $presetKey }}')">
                        {{ __($presetLabel) }}
                    </button>
                @endforeach
            </div>

            <div class="date-range-group">
                <input type="date" name="start_date" id="filterStartDate" class="date-input-modern"
                       value="{{ $startDate ?? '' }}" title="{{ __('Start Date') }}">
                <span class="date-range-sep">{{ __('to') }}</span>
                <input type="date" name="end_date" id="filterEndDate" class="date-input-modern"
                       value="{{ $endDate ?? '' }}" title="{{ __('End Date') }}">
                <button type="submit" class="btn-date-apply">
                    <i class="feather icon-check"></i>
                    <span>{{ __('Apply') }}</span>
                </button>
                @if($hasAnyFilter)
                    <a href="{{ route('website-orders.index') }}" class="btn-date-reset" title="{{ __('Clear all filters') }}">
                        <i class="feather icon-rotate-ccw"></i>
                        <span>{{ __('Reset') }}</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Carousel Status Filter Tabs --}}
        <div class="status-carousel-container">
            <button type="button" class="carousel-nav-btn" id="scrollPillsLeft" title="Scroll Left">
                <i class="feather icon-chevron-left"></i>
            </button>

            <div class="status-pills-scroll" id="pillsScrollTrack">
                @foreach($statusTabs as $tab)
                    <a href="{{ route('website-orders.index', array_merge($pillBaseQuery, ['status' => $tab['key']])) }}"
                       class="status-pill {{ $status === $tab['key'] ? 'active' : '' }}">
                        @if(!empty($tab['dot']))
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:{{ $tab['dot'] }};"></span>
                        @elseif(!empty($tab['color']))
                            <span style="color:{{ $tab['color'] }};">{{ $tab['icon'] }}</span>
                        @else
                            <span>{{ $tab['icon'] }}</span>
                        @endif
                        <span>{{ $tab['label'] }}</span>
                        <span class="pill-count">{{ $counts[$tab['key']] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <button type="button" class="carousel-nav-btn" id="scrollPillsRight" title="Scroll Right">
                <i class="feather icon-chevron-right"></i>
            </button>
        </div>

        {{-- Search & Status Filter Bar --}}
        <div class="filter-card">
            <div class="search-input-group">
                <i class="feather icon-search"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="{{ __('Filter orders by ID, Phone, Customer, SKU...') }}">
            </div>

            <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit();">
                @foreach($statusTabs as $tab)
                    <option value="{{ $tab['key'] }}" {{ $status === $tab['key'] ? 'selected' : '' }}>{{ $tab['option'] }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-filter-action">
                <i class="feather icon-filter"></i>
                <span>{{ __('Filter') }}</span>
            </button>

            <div class="dropdown">
                <button type="button" class="btn-filter-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="feather icon-layers"></i>
                    <span>{{ __('Bulk Actions') }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkAction('approve')">
                        <i class="feather icon-check text-success mr-2"></i> {{ __('Approve Selected to Online Sale') }}
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkAction('good_no_response')">
                        <i class="feather icon-thumbs-up text-warning mr-2"></i> {{ __('Mark Good But No Response') }}
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkAction('no_response')">
                        <i class="feather icon-slash text-danger mr-2"></i> {{ __('Mark No Response') }}
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkAction('on_hold')">
                        <i class="feather icon-pause text-info mr-2"></i> {{ __('Mark On Hold') }}
                    </a>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="performBulkAction('cancelled')">
                        <i class="feather icon-x-circle text-danger mr-2"></i> {{ __('Mark Cancelled') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="performBulkAction('delete')">
                        <i class="feather icon-trash-2 mr-2"></i> {{ __('Delete Selected') }}
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Courier & Stock Transfer Bar --}}
    <div class="dispatch-bar">
        <div class="dispatch-controls">
            <div class="dispatch-field">
                <label><i class="feather icon-truck text-purple"></i> Courier Service:</label>
                <select id="courierServiceSelect" class="dispatch-select">
                    <option value="Steadfast Courier">Steadfast Courier</option>
                    <option value="Pathao Courier">Pathao Courier</option>
                    <option value="RedX Courier">RedX Courier</option>
                </select>
            </div>

            <div class="dispatch-field">
                <label><i class="feather icon-package text-info"></i> Stock Transfer Mode:</label>
                <select id="stockTransferModeSelect" class="dispatch-select">
                    <option value="without_manage" selected>Without Manage Stock (Direct Transfer)</option>
                    <option value="with_manage">Manage Stock (FIFO Deduction)</option>
                </select>
            </div>

            <div class="d-flex align-items-center mt-3">
                <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-size: 13px; font-weight: 600; color: #475569;">
                    <input type="checkbox" id="sendToCourierApiCheckbox" style="width: 16px; height: 16px; accent-color: #7c3aed; margin-right: 6px;">
                    Send to Courier API
                </label>
            </div>
        </div>

        <div>
            <button type="button" class="btn-send-orders" onclick="dispatchSelectedOrders()">
                <i class="feather icon-send"></i>
                <span>Send Selected Orders</span>
            </button>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="orders-table-card">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 38px;">
                            <input type="checkbox" id="selectAllCheckbox" style="cursor: pointer; accent-color: #7c3aed;">
                        </th>
                        <th>CREATED AT ↕</th>
                        <th>CUSTOMER</th>
                        <th>NOTE</th>
                        <th>ORDER ITEMS</th>
                        <th>SUCCESS RATE ↕</th>
                        <th>TAGS</th>
                        <th>SITE</th>
                        <th style="text-align: right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr id="orderRow-{{ $order->id }}">
                            {{-- Checkbox --}}
                            <td>
                                <input type="checkbox" class="order-select-checkbox" value="{{ $order->id }}" style="cursor: pointer; accent-color: #7c3aed;">
                            </td>

                            {{-- Created At & Order ID --}}
                            <td>
                                <div class="cell-created-date">{{ $order->created_at ? $order->created_at->format('d M, h:i a') : 'N/A' }}</div>
                                <div class="cell-order-id">ID: {{ $order->order_no }}</div>
                            </td>

                            {{-- Customer --}}
                            <td>
                                <div>
                                    <a href="tel:{{ $order->customer_phone }}" class="customer-phone-link">
                                        <i class="feather icon-phone" style="color: #0284c7; font-size: 13px;"></i>
                                        <span>{{ $order->customer_phone }}</span>
                                    </a>
                                    @php
                                        $cleanPhone = preg_replace('/[^\d]/', '', $order->customer_phone);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $waPhone = '88' . $cleanPhone;
                                        } else {
                                            $waPhone = $cleanPhone;
                                        }
                                        $waMsg = urlencode("Hello " . $order->customer_name . ", thank you for your order #" . $order->order_no . " at our store.");
                                    @endphp
                                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" class="customer-whatsapp-btn" title="Chat on WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                </div>
                                <div class="mt-1 mb-1">
                                    <button type="button" class="btn btn-xs" style="font-size: 10px; border-radius: 12px; padding: 1.5px 8px; border: 1px solid #06b6d4; color: #0891b2; background: #f0fdfa; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" onclick="openCustomerFraudModal('{{ $order->customer_phone }}', '{{ addslashes($order->customer_name) }}')" title="{{ __('View Courier Delivery Rating & Fraud Check') }}">
                                        <i class="feather icon-shield" style="font-size: 11px;"></i> Courier Rating
                                    </button>
                                </div>
                                <div class="customer-name-row">
                                    <i class="feather icon-user" style="color: #64748b; font-size: 12px;"></i>
                                    <span>{{ $order->customer_name }}</span>
                                    <button type="button" class="copy-btn" onclick="copyCustomerInfo('{{ $order->customer_name }}', '{{ $order->customer_phone }}', '{{ addslashes($order->customer_address) }}')" title="Copy customer info">
                                        <i class="feather icon-copy"></i>
                                    </button>
                                </div>
                                @if($order->customer_address)
                                    <div class="customer-address-row">
                                        <i class="feather icon-map-pin"></i>
                                        <span>{{ $order->customer_address }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Note --}}
                            <td>
                                <div class="note-cell-updated">
                                    {{ $order->note_updated_at ? 'Updated ' . (\Carbon\Carbon::parse($order->note_updated_at)->diffForHumans()) : 'Updated recently' }}
                                </div>
                                <div class="note-text-editable" onclick="editOrderNote({{ $order->id }}, '{{ addslashes($order->note ?: ($order->order_notes ?? '')) }}')" title="Click to edit note">
                                    {{ !empty($order->note) ? Str::limit($order->note, 45) : (!empty($order->order_notes) ? Str::limit($order->order_notes, 45) : '-') }}
                                </div>
                            </td>

                            {{-- Order Items --}}
                            <td>
                                @if($order->items->isNotEmpty())
                                    @php $firstItem = $order->items->first(); @endphp
                                    <div class="item-preview-box" onclick="openOrderModal({{ $order->id }})" style="cursor: pointer;" title="{{ __('Click to view & manage ordered items') }}">
                                        <img src="{{ $firstItem->image_url }}" alt="{{ $firstItem->product_name }}" class="item-thumbnail">
                                        <div class="item-details-box">
                                            <div class="item-code-qty">
                                                <span>{{ $firstItem->product_code ?? ($firstItem->product?->barcode ?? 'N/A') }}</span>
                                                <span class="text-muted ml-1">{{ $firstItem->quantity }}x</span>
                                            </div>
                                            <div class="item-price">
                                                ৳{{ number_format($firstItem->subtotal, 0) }}
                                                @if($order->items->count() > 1)
                                                    <span class="badge badge-light ml-1" style="font-size: 10px;">+{{ $order->items->count() - 1 }} more</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">No items</span>
                                @endif
                            </td>

                            {{-- Success Rate --}}
                            <td>
                                @php
                                    $stats = $order->fraud_stats ?? [];
                                    $rate = $stats['overall_success_rate'] ?? 0;
                                    $parcels = $stats['total_parcels'] ?? 0;
                                    $delivered = $stats['total_delivered'] ?? 0;
                                    $rating = $parcels > 0 ? round(($delivered / $parcels) * 100) : 0;
                                    $color = $rate >= 70 ? '#10b981' : ($rate >= 40 ? '#f59e0b' : '#ef4444');
                                @endphp
                                <div class="success-rate-box clickable-fraud-check" onclick="openCustomerFraudModal('{{ $order->customer_phone }}', '{{ addslashes($order->customer_name) }}')" style="cursor: pointer; transition: transform 0.15s ease;" title="{{ __('Click to view detailed courier rating & delivery history') }}" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                                    <div class="success-rate-ring" style="border-top-color: {{ $color }};">
                                        <span>{{ $rate }}%</span>
                                    </div>
                                    <div class="rate-info-text">
                                        <div class="rate-percent" style="color: {{ $color }};">Success: {{ $rate }}%</div>
                                        <div class="text-muted">Orders: {{ $delivered }}/{{ $parcels }}</div>
                                        <div class="text-muted">Rating: {{ $rate }}% <i class="feather icon-external-link ml-1" style="font-size: 10px; color: #0284c7;"></i></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Tags --}}
                            <td>
                                @if(!empty($order->tags))
                                    <span class="badge badge-secondary" style="font-size: 11px;">{{ $order->tags }}</span>
                                @else
                                    <button type="button" class="btn btn-sm btn-light py-0 px-2 text-muted" style="border-radius: 50%; font-size: 12px;" title="Add tag">+</button>
                                @endif
                            </td>

                            {{-- Site --}}
                            <td>
                                <span class="badge-site">{{ $order->site ?? 'Website' }}</span>
                            </td>

                            {{-- Actions --}}
                            <td style="text-align: right;">
                                <div class="d-inline-flex align-items-center gap-2" style="gap: 6px;">
                                    <a href="{{ route('website-orders.details', $order->id) }}" class="btn-open-action" title="{{ __('Open Order Details Page') }}">
                                        <span>Open</span>
                                        <i class="feather icon-external-link"></i>
                                    </a>

                                    @if($order->status === 'approved')
                                        <select class="btn-quick-status" onchange="changeOrderStatus({{ $order->id }}, this.value)" style="border-color: #10b981; color: #047857; font-weight: 700; background-color: #ecfdf5;">
                                            <option value="approved" selected>✔ Approved</option>
                                            <option value="cancelled" style="color: #ef4444; font-weight: 600;">❌ Cancel Order</option>
                                        </select>
                                    @else
                                        <select class="btn-quick-status" onchange="changeOrderStatus({{ $order->id }}, this.value)">
                                            <option value="processing" {{ in_array($order->status, ['processing', 'pending']) ? 'selected' : '' }}>Processing</option>
                                            <option value="good_no_response" {{ $order->status === 'good_no_response' ? 'selected' : '' }}>Good But No Response</option>
                                            <option value="no_response" {{ $order->status === 'no_response' ? 'selected' : '' }}>No Response</option>
                                            <option value="advance_payment" {{ $order->status === 'advance_payment' ? 'selected' : '' }}>Advance Payment</option>
                                            <option value="on_hold" {{ $order->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                            <option value="approved" {{ $order->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="feather icon-inbox" style="font-size: 38px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                                    <h5>{{ __('No orders found in this status') }}</h5>
                                    <p class="small text-muted">{{ __('Try choosing another status tab or filter.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- Order Details Modal --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold" id="modalOrderTitle">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4" id="orderDetailsModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-purple" role="status"></div>
                    <div class="mt-2 text-muted">Loading order details...</div>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" id="btnModalFullDetails" class="btn btn-outline-primary" style="font-weight: 600;">
                    <i class="feather icon-external-link mr-1"></i> Full Details Page
                </a>
                <button type="button" class="btn btn-primary" id="btnModalApprove" style="background: #7c3aed; border-color: #7c3aed;">
                    <i class="feather icon-check mr-1"></i> Approve to Online Sale
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    // Global orders data passed from server for instant modal view
    const ordersData = @json($orders->items());

    // Scroll buttons for top status pills
    document.getElementById('scrollPillsLeft').addEventListener('click', function() {
        document.getElementById('pillsScrollTrack').scrollBy({ left: -250, behavior: 'smooth' });
    });
    document.getElementById('scrollPillsRight').addEventListener('click', function() {
        document.getElementById('pillsScrollTrack').scrollBy({ left: 250, behavior: 'smooth' });
    });

    // Date-wise quick preset selector
    function selectDatePreset(preset) {
        const presetInput = document.getElementById('filterDatePreset');
        const startInput = document.getElementById('filterStartDate');
        const endInput = document.getElementById('filterEndDate');
        const form = document.getElementById('filterForm');
        if (!presetInput || !form) return;

        presetInput.value = preset;
        if (startInput) startInput.value = '';
        if (endInput) endInput.value = '';
        form.submit();
    }

    (function initCustomDateRangeHandlers() {
        const presetInput = document.getElementById('filterDatePreset');
        const startInput = document.getElementById('filterStartDate');
        const endInput = document.getElementById('filterEndDate');
        if (!presetInput || !startInput || !endInput) return;

        const clearActivePresetButtons = () => {
            document.querySelectorAll('.date-preset-btn').forEach(btn => btn.classList.remove('active'));
        };

        startInput.addEventListener('change', function() {
            clearActivePresetButtons();
            if (this.value) {
                presetInput.value = 'custom';
                if (!endInput.value || endInput.value < this.value) {
                    endInput.value = this.value;
                }
            } else if (!endInput.value) {
                presetInput.value = 'all';
            }
        });

        endInput.addEventListener('change', function() {
            clearActivePresetButtons();
            if (this.value) {
                presetInput.value = 'custom';
                if (!startInput.value || startInput.value > this.value) {
                    startInput.value = this.value;
                }
            } else if (!startInput.value) {
                presetInput.value = 'all';
            }
        });
    })();

    // Select all checkboxes
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.order-select-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function getSelectedOrderIds() {
        const selected = [];
        document.querySelectorAll('.order-select-checkbox:checked').forEach(cb => {
            selected.push(cb.value);
        });
        return selected;
    }

    // Copy customer info
    function copyCustomerInfo(name, phone, address) {
        const text = `Name: ${name}\nPhone: ${phone}\nAddress: ${address}`;
        navigator.clipboard.writeText(text).then(() => {
            if (typeof toastr !== 'undefined') {
                toastr.success('Customer details copied to clipboard!');
            } else {
                alert('Copied to clipboard!');
            }
        });
    }

    // Inline note editing
    function editOrderNote(orderId, currentNote) {
        const newNote = prompt('Edit order note:', currentNote);
        if (newNote !== null) {
            fetch(`{{ url('/website-orders') }}/${orderId}/note`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ note: newNote })
            })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                return ct.includes('application/json') ? await res.json() : { success: false, message: 'Server error (' + res.status + ')' };
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error updating note');
                }
            })
            .catch(err => alert('Network error'));
        }
    }

    // Change status with stock validation
    function changeOrderStatus(orderId, newStatus) {
        const currentOrder = ordersData.find(o => o.id == orderId);
        if (currentOrder && currentOrder.status === 'approved') {
            if (newStatus === 'cancelled') {
                if (!confirm('আপনি কি নিশ্চিত যে এই অনুমোদিত (Approved) অর্ডারটি বাতিল (Cancel) করতে চান? স্টক রিস্টোর করা হবে এবং মেটা (Facebook) পারচেজ ইভেন্ট বাতিল করা হবে।')) {
                    location.reload();
                    return;
                }
            } else {
                alert('অনুমোদিত (Approved) অর্ডার শুধুমাত্র বাতিল (Cancelled) করা যাবে!');
                location.reload();
                return;
            }
        } else if (newStatus === 'approved') {
            const stockModeSelect = document.getElementById('stockTransferModeSelect');
            const stockModeVal = stockModeSelect ? stockModeSelect.value : 'without_manage';
            const isWithManage = stockModeVal === 'with_manage';
            const confirmMsg = isWithManage
                ? 'Are you sure you want to approve this order with MANAGE STOCK? Stock will be checked and deducted via FIFO. (If insufficient, approval will halt).'
                : 'Are you sure you want to approve this order WITHOUT MANAGE STOCK? The order will transfer directly to Online Sale List.';

            if (!confirm(confirmMsg)) {
                location.reload();
                return;
            }
        }

        const currentStockMode = document.getElementById('stockTransferModeSelect') ? document.getElementById('stockTransferModeSelect').value : 'without_manage';

        fetch(`{{ url('/website-orders') }}/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: newStatus,
                stock_mode: currentStockMode
            })
        })
        .then(async res => {
            const ct = res.headers.get('content-type') || '';
            let data = null;
            if (ct.includes('application/json')) {
                data = await res.json();
            } else {
                data = {
                    success: false,
                    message: res.status === 419 
                        ? 'Session expired. Please refresh the page and try again.' 
                        : (res.status === 403 
                            ? 'Permission denied. You do not have permission for this action.' 
                            : 'Server error (' + res.status + '). Please check error log.')
                };
            }
            return { status: res.status, body: data };
        })
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(body.message);
                } else {
                    alert(body.message);
                }
                setTimeout(() => location.reload(), 1500);
            } else {
                alert(body.message || 'Action failed!');
                location.reload();
            }
        })
        .catch(err => {
            alert('Request error: ' + err);
            location.reload();
        });
    }

    // Open order details modal
    function openOrderModal(orderId) {
        const order = ordersData.find(o => o.id == orderId);
        if (!order) return;

        $('#modalOrderTitle').text(`Order Details - ${order.order_no}`);
        $('#btnModalFullDetails').attr('href', `{{ url('/website-orders/details') }}/${order.id}`);
        
        let itemsHtml = '';
        let canApprove = true;
        const hasMultipleItems = order.items && order.items.length > 1;

        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const stockItem = (order.stock_details || []).find(s => s.item_id === item.id);
                const inStock = stockItem ? stockItem.in_stock : true;
                const available = stockItem ? stockItem.available : 'N/A';
                if (!inStock) canApprove = false;

                itemsHtml += `
                    <tr id="modal-item-row-${item.id}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="${item.image_url}" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover;" alt="">
                                <div>
                                    <div class="font-weight-bold">${item.product_name}</div>
                                    <div class="text-muted small">Code: ${item.product_code || 'N/A'} | Qty: ${item.quantity}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            ${inStock ? `<span class="stock-badge-in"><i class="feather icon-check-circle"></i> In Stock (${available})</span>` : `<span class="stock-badge-out"><i class="feather icon-alert-circle"></i> Out of Stock (Available: ${available})</span>`}
                        </td>
                        <td class="text-right font-weight-bold">৳${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td class="text-right font-weight-bold">৳${parseFloat(item.subtotal).toFixed(2)}</td>
                        ${order.status !== 'approved' ? `
                            <td class="text-center">
                                ${hasMultipleItems ? `
                                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeOrderItemFromModal(${order.id}, ${item.id})" title="Remove product from order" style="padding: 3px 10px; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="feather icon-trash-2"></i>
                                        <span>Remove</span>
                                    </button>
                                ` : `<span class="text-muted small" title="Minimum 1 product required">-</span>`}
                            </td>
                        ` : ''}
                    </tr>
                `;
            });
        }

        const deliveryChargeVal = parseFloat(order.shipping_fee !== undefined && order.shipping_fee !== null ? order.shipping_fee : (order.shipping_cost || 0));
        const subtotalVal = parseFloat(order.subtotal || (order.items ? order.items.reduce((acc, i) => acc + parseFloat(i.subtotal || 0), 0) : order.total_amount));

        const modalBody = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small text-uppercase font-weight-bold mb-1">Customer Information</div>
                        <div class="font-weight-bold font-size-16">${order.customer_name}</div>
                        <div><i class="feather icon-phone text-muted mr-1"></i> ${order.customer_phone}</div>
                        <div><i class="feather icon-map-pin text-muted mr-1"></i> ${order.customer_address || 'No address provided'}</div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-xs" style="font-size: 11px; border-radius: 12px; padding: 2px 10px; border: 1px solid #06b6d4; color: #0891b2; background: #f0fdfa; font-weight: 600;" onclick="openCustomerFraudModal('${order.customer_phone}', '${(order.customer_name || '').replace(/'/g, "\\'")}')">
                                <i class="feather icon-shield mr-1"></i> Check Courier Delivery Rating
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small text-uppercase font-weight-bold mb-1">Order Summary</div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Status:</span> 
                            ${order.status === 'approved' 
                                ? '<span class="badge badge-success" style="background: #10b981; font-size: 11px; font-weight: 700; padding: 4px 8px;"><i class="feather icon-check-circle mr-1"></i> Approved (Locked)</span>' 
                                : `<span class="badge badge-info text-capitalize">${order.status}</span>`}
                        </div>
                        <div class="d-flex justify-content-between"><span>Payment Method:</span> <span class="font-weight-bold">${order.payment_method}</span></div>
                        <div class="d-flex justify-content-between"><span>Subtotal:</span> <span class="font-weight-bold" id="modalSubtotalVal">৳${subtotalVal.toFixed(2)}</span></div>
                        <div class="d-flex justify-content-between align-items-center my-1">
                            <span>Delivery Charge:</span>
                            ${order.status !== 'approved' ? `
                                <div class="d-flex align-items-center gap-1" style="width: 130px;">
                                    <span class="mr-1">৳</span>
                                    <input type="number" id="modalDeliveryCharge-${order.id}" class="form-control form-control-sm text-right py-0 px-1" style="height: 26px; font-weight: 700;" value="${deliveryChargeVal}" min="0" onchange="updateModalDeliveryCharge(${order.id}, this.value)">
                                </div>
                            ` : `<span class="font-weight-bold">৳${deliveryChargeVal.toFixed(2)}</span>`}
                        </div>
                        <div class="d-flex justify-content-between border-top pt-1 mt-1"><span>Total Amount:</span> <span class="font-weight-bold text-purple font-size-16" id="modalTotalVal">৳${parseFloat(order.total_amount).toFixed(2)}</span></div>
                    </div>
                </div>
            </div>

            ${!canApprove ? `
                <div class="alert alert-warning mb-3">
                    <i class="feather icon-alert-triangle mr-1"></i>
                    <strong>স্টক ঘাটতি:</strong> এই অর্ডারের কিছু পণ্যের পর্যাপ্ত স্টক নেই। অনলাইন সেল লিস্টে অনুমোদন করতে প্রথমে পণ্যটি পারচেজ (Purchase) করুন।
                </div>
            ` : ''}

            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Stock Check</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Subtotal</th>
                            ${order.status !== 'approved' ? '<th class="text-center" style="width: 70px;">Action</th>' : ''}
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>
            </div>
        `;

        $('#orderDetailsModalBody').html(modalBody);

        const btnApprove = document.getElementById('btnModalApprove');
        if (order.status === 'approved') {
            btnApprove.style.display = 'inline-block';
            btnApprove.className = 'btn btn-outline-danger';
            btnApprove.innerHTML = '<i class="feather icon-x-circle mr-1"></i> Cancel Approved Order';
            btnApprove.onclick = function() {
                changeOrderStatus(order.id, 'cancelled');
            };
        } else {
            btnApprove.style.display = 'inline-block';
            btnApprove.className = 'btn btn-success';
            btnApprove.innerHTML = '<i class="feather icon-check-circle mr-1"></i> Approve to Online Sale';
            btnApprove.onclick = function() {
                changeOrderStatus(order.id, 'approved');
            };
        }

        $('#orderDetailsModal').modal('show');
    }

    function removeOrderItemFromModal(orderId, itemId) {
        const order = ordersData.find(o => o.id == orderId);
        if (!order) return;
        if (order.items && order.items.length <= 1) {
            alert('অর্ডারে কমপক্ষে ১টি প্রোডাক্ট থাকতে হবে। শেষ প্রোডাক্টটি রিমুভ করা যাবে না।');
            return;
        }

        if (!confirm('আপনি কি নিশ্চিত যে এই প্রোডাক্টটি অর্ডার থেকে রিমুভ করতে চান?')) return;

        $.ajax({
            url: "{{ url('/website-orders') }}/" + orderId + "/item/" + itemId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "DELETE"
            },
            success: function(res) {
                if (res.success) {
                    order.items = order.items.filter(i => i.id != itemId);
                    order.subtotal = res.subtotal;
                    order.total_amount = res.grand_total;
                    if (res.shipping_fee !== undefined) {
                        order.shipping_fee = res.shipping_fee;
                        order.shipping_cost = res.shipping_fee;
                    }
                    openOrderModal(orderId);
                    updateTableRowItems(order);
                    alert(res.message || 'Product removed successfully');
                } else {
                    alert(res.message || 'Failed to remove product');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to remove product';
                alert(msg);
            }
        });
    }

    function updateModalDeliveryCharge(orderId, newCharge) {
        const order = ordersData.find(o => o.id == orderId);
        if (!order) return;

        var chargeVal = parseFloat(newCharge);
        if (isNaN(chargeVal) || chargeVal < 0) chargeVal = 0;

        $.ajax({
            url: "{{ url('/website-orders/details') }}/" + orderId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                delivery_charge: chargeVal
            },
            success: function(res) {
                if (res.success) {
                    order.shipping_cost = chargeVal;
                    order.shipping_fee = chargeVal;
                    order.total_amount = res.grand_total !== undefined ? res.grand_total : Math.max(0, (order.subtotal || 0) + chargeVal - (order.discount || 0));
                    $('#modalTotalVal').text('৳' + parseFloat(order.total_amount).toFixed(2));
                }
            },
            error: function() {
                alert('Failed to update delivery charge');
            }
        });
    }

    function updateTableRowItems(order) {
        var $row = $('#orderRow-' + order.id);
        if (!$row.length) return;
        if (order.items && order.items.length > 0) {
            var first = order.items[0];
            var moreBadge = order.items.length > 1 ? `<span class="badge badge-light ml-1" style="font-size: 10px;">+${order.items.length - 1} more</span>` : '';
            var html = `
                <div class="item-preview-box" onclick="openOrderModal(${order.id})" style="cursor: pointer;" title="Click to view & manage ordered items">
                    <img src="${first.image_url}" alt="${first.product_name}" class="item-thumbnail">
                    <div class="item-details-box">
                        <div class="item-code-qty">
                            <span>${first.product_code || 'N/A'}</span>
                            <span class="text-muted ml-1">${first.quantity}x</span>
                        </div>
                        <div class="item-price">
                            ৳${parseFloat(first.subtotal).toFixed(0)}
                            ${moreBadge}
                        </div>
                    </div>
                </div>
            `;
            $row.find('td:nth-child(4)').html(html);
        }
    }

    // Bulk actions
    function performBulkAction(action) {
        const orderIds = getSelectedOrderIds();
        if (orderIds.length === 0) {
            alert('Please select at least one order.');
            return;
        }

        if (action === 'approve') {
            if (!confirm(`Are you sure you want to approve ${orderIds.length} orders? Stock will be checked and deducted for available products.`)) {
                return;
            }
        } else if (action === 'delete') {
            if (!confirm(`Are you sure you want to delete ${orderIds.length} orders?`)) {
                return;
            }
        }

        const stockModeSelect = document.getElementById('stockTransferModeSelect');
        const stockMode = stockModeSelect ? stockModeSelect.value : 'without_manage';

        fetch(`{{ route('website-orders.bulk-action') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                order_ids: orderIds,
                stock_mode: stockMode
            })
        })
        .then(async res => {
            const ct = res.headers.get('content-type') || '';
            return ct.includes('application/json') ? await res.json() : { success: false, message: 'Server error (' + res.status + ')' };
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Bulk action failed');
            }
        })
        .catch(err => alert('Network error'));
    }

    // Send selected orders to courier
    function dispatchSelectedOrders() {
        const orderIds = getSelectedOrderIds();
        if (orderIds.length === 0) {
            alert('Please select at least one order to send to courier.');
            return;
        }

        const courier = document.getElementById('courierServiceSelect').value;
        const sendApi = document.getElementById('sendToCourierApiCheckbox').checked;
        const stockModeSelect = document.getElementById('stockTransferModeSelect');
        const stockMode = stockModeSelect ? stockModeSelect.value : 'without_manage';

        if (!confirm(`Send ${orderIds.length} selected orders to ${courier}?`)) {
            return;
        }

        fetch(`{{ route('website-orders.dispatch-courier') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                order_ids: orderIds,
                courier_service: courier,
                send_to_courier_api: sendApi,
                stock_mode: stockMode
            })
        })
        .then(async res => {
            const ct = res.headers.get('content-type') || '';
            return ct.includes('application/json') ? await res.json() : { success: false, message: 'Server error (' + res.status + ')' };
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Dispatch failed');
            }
        })
        .catch(err => alert('Network error'));
    }
</script>
@endpush
@endsection
