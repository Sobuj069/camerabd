@extends('backend.layouts.master')
@section('page-title', __('Dashboard'))
@push('css')
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
       .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        } 
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        .filter-nav .nav-link {
            color: #64748b;
            font-weight: 700;
            padding: 6px 14px;
            font-size: 12.5px;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            white-space: nowrap;
        }
        .filter-nav .nav-link:hover {
            color: #1e293b;
            background-color: rgba(0, 0, 0, 0.04);
        }
        .dark .filter-nav .nav-link:hover {
            color: #f1f5f9;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .filter-nav .nav-link.active {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.35);
        }

        /* Dark Floating Date Filter Dropdown */
        .dash-date-filter-wrapper {
            position: relative;
            display: inline-block;
        }
        .dash-filter-btn {
            background: #0f172a;
            color: #f8fafc;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
            line-height: 1.3;
        }
        .dash-filter-btn:hover {
            background: #1e293b;
            border-color: #475569;
            color: #ffffff;
        }
        .dash-filter-btn .dash-filter-caret {
            font-size: 10px;
            transition: transform 0.2s ease;
            margin-left: 2px;
        }
        .dash-date-filter-wrapper.open .dash-filter-caret {
            transform: rotate(180deg);
        }
        .dash-filter-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: auto;
            width: 245px;
            max-width: calc(100vw - 32px);
            max-height: 380px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 6px;
            z-index: 1050;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #334155 #0f172a;
            box-sizing: border-box;
        }
        @media (max-width: 640px) {
            .dash-filter-dropdown {
                left: 0;
                right: auto;
                width: 245px;
                max-width: calc(100vw - 32px);
            }
        }
        .dash-filter-dropdown::-webkit-scrollbar {
            width: 5px;
        }
        .dash-filter-dropdown::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .dash-filter-dropdown::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        .dash-date-filter-wrapper.open .dash-filter-dropdown {
            display: block;
            animation: dashFilterFadeIn 0.15s ease-out;
        }
        @keyframes dashFilterFadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .dash-filter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #cbd5e1;
            border-radius: 7px;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease;
            margin-bottom: 2px;
        }
        .dash-filter-item:hover {
            background-color: #1e293b;
            color: #ffffff;
        }
        .dash-filter-item.active {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 600;
        }
        .dash-filter-item .check-icon {
            font-size: 11px;
            display: none;
        }
        .dash-filter-item.active .check-icon {
            display: inline-block;
        }
        .dash-filter-section-title {
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            padding: 8px 10px 4px 10px;
            margin-top: 4px;
            border-top: 1px solid #1e293b;
        }
        .dash-filter-custom-trigger {
            padding: 7px 10px;
            font-size: 12.5px;
            font-weight: 500;
            color: #cbd5e1;
            border-radius: 7px;
            cursor: pointer;
            transition: background-color 0.15s ease;
            margin-bottom: 2px;
        }
        .dash-filter-custom-trigger:hover {
            background-color: #1e293b;
            color: #ffffff;
        }
        .dash-filter-custom-trigger .custom-expand-icon {
            font-size: 11px;
            color: #64748b;
            transition: transform 0.2s ease;
        }
        .dash-filter-custom-trigger.expanded .custom-expand-icon {
            transform: rotate(90deg);
        }
        .dash-custom-panel {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 8px 10px;
            margin-top: 6px;
            cursor: default;
        }
        .dash-custom-input-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: block;
        }
        .dash-custom-panel input[type="date"] {
            background: #0f172a !important;
            border: 1px solid #475569 !important;
            color: #f8fafc !important;
            font-size: 12px !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
            width: 100% !important;
            color-scheme: dark;
        }
        .dash-custom-panel input[type="date"]:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25) !important;
        }
        .dash-btn-apply-single,
        .dash-btn-apply-range {
            background: #2563eb !important;
            border: none !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            padding: 5px 10px !important;
            color: #ffffff !important;
        }
        .dash-btn-apply-single:hover,
        .dash-btn-apply-range:hover {
            background: #1d4ed8 !important;
        }

        /* Analytics Graph Cards (Theme Matched) */
        .dash-analytics-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .dash-analytics-card {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .dash-analytics-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }
        .graph-ctrl-select {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            outline: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .dark .graph-ctrl-select {
            background: #0f172a;
            color: #f1f5f9;
            border-color: #475569;
        }
        .graph-ctrl-select:hover, .graph-ctrl-select:focus {
            border-color: #9333ea;
        }
        .earning-tile-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 72px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .dark .earning-tile-btn {
            background: rgba(15, 23, 42, 0.6);
            border-color: #334155;
            color: #94a3b8;
        }
        .earning-tile-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        .dark .earning-tile-btn:hover {
            background: #334155;
            color: #ffffff;
        }
        .earning-tile-btn.active[data-metric="orders"] {
            border-color: #ec4899;
            background: #fdf2f8;
            color: #db2777;
            box-shadow: 0 2px 10px rgba(236, 72, 153, 0.2);
        }
        .dark .earning-tile-btn.active[data-metric="orders"] {
            background: rgba(236, 72, 153, 0.15);
            color: #f472b6;
        }
        .earning-tile-btn.active[data-metric="revenue"] {
            border-color: #3b82f6;
            background: #eff6ff;
            color: #2563eb;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
        }
        .dark .earning-tile-btn.active[data-metric="revenue"] {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }
        .earning-tile-btn.active[data-metric="profit"] {
            border-color: #10b981;
            background: #ecfdf5;
            color: #059669;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.2);
        }
        .dark .earning-tile-btn.active[data-metric="profit"] {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
        .earning-tile-btn.active[data-metric="expense"] {
            border-color: #f59e0b;
            background: #fffbeb;
            color: #d97706;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.2);
        }
        .dark .earning-tile-btn.active[data-metric="expense"] {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }
        .earning-tile-btn i {
            font-size: 17px;
            margin-bottom: 5px;
        }
        .earning-tile-btn span {
            font-size: 11px;
            font-weight: 700;
        }
        .hourly-pill-toggle {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            padding: 3px;
            gap: 4px;
        }
        .dark .hourly-pill-toggle {
            background: #0f172a;
            border-color: #334155;
        }
        .hourly-pill-btn {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s;
        }
        .dark .hourly-pill-btn {
            color: #94a3b8;
        }
        .hourly-pill-btn.active[data-series="today"] {
            background: #9333ea;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(147, 51, 234, 0.35);
        }
        .hourly-pill-btn.active[data-series="yesterday"] {
            background: #f59e0b;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
        }

        /* Channel Card Embedded Tabs & Sub-panes */
        .channel-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            white-space: nowrap;
        }
        .dark .channel-tab-btn {
            color: #94a3b8;
        }
        .channel-tab-btn:hover {
            color: #1e293b;
            background: rgba(0, 0, 0, 0.04);
        }
        .dark .channel-tab-btn:hover {
            color: #f1f5f9;
            background: rgba(255, 255, 255, 0.05);
        }
        .channel-tab-btn.active {
            background: #ffffff;
            color: #7c3aed;
            border-color: #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .dark .channel-tab-btn.active {
            background: #1e293b;
            color: #c084fc;
            border-color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        .web-order-subtab, .woo-order-subtab {
            font-size: 13px;
            font-weight: 800;
            color: #94a3b8;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            position: relative;
            transition: all 0.2s ease;
        }
        .web-order-subtab:hover, .woo-order-subtab:hover {
            color: #64748b;
        }
        .dark .web-order-subtab:hover, .dark .woo-order-subtab:hover {
            color: #cbd5e1;
        }
        .web-order-subtab.active {
            color: #2563eb;
        }
        .dark .web-order-subtab.active {
            color: #60a5fa;
        }
        .web-order-subtab.active::after {
            content: '';
            position: absolute;
            bottom: -9px;
            left: 0;
            right: 0;
            height: 2.5px;
            background: #2563eb;
            border-radius: 2px;
        }
        .dark .web-order-subtab.active::after {
            background: #60a5fa;
        }
        .woo-order-subtab.active {
            color: #9333ea;
        }
        .dark .woo-order-subtab.active {
            color: #c084fc;
        }
        .woo-order-subtab.active::after {
            content: '';
            position: absolute;
            bottom: -9px;
            left: 0;
            right: 0;
            height: 2.5px;
            background: #9333ea;
            border-radius: 2px;
        }
        .dark .woo-order-subtab.active::after {
            background: #c084fc;
        }
        .source-view-toggle-btn {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dark .source-view-toggle-btn {
            color: #94a3b8;
        }
        .source-view-toggle-btn.active {
            background: #7c3aed;
            color: #ffffff;
            box-shadow: 0 1px 4px rgba(124, 58, 237, 0.3);
        }
        .status-legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.15s ease;
        }
        .dark .status-legend-item {
            background: #1e293b;
            border-color: #334155;
        }
        .status-legend-item:hover {
            border-color: #cbd5e1;
            transform: translateX(2px);
        }
        .dark .status-legend-item:hover {
            border-color: #475569;
        }

        /* Responsive Dashboard Grid & Charts */
        .dash-card-row {
            display: flex;
            flex-wrap: wrap;
            margin-left: -12px;
            margin-right: -12px;
        }
        .dash-col-half {
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
            padding-left: 12px;
            padding-right: 12px;
            margin-bottom: 20px;
        }
        .dash-col-7 {
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
            padding-left: 12px;
            padding-right: 12px;
            margin-bottom: 20px;
        }
        .dash-col-5 {
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
            padding-left: 12px;
            padding-right: 12px;
            margin-bottom: 20px;
        }
        .dash-subcard-3 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
            padding-left: 6px;
            padding-right: 6px;
        }

        @media (min-width: 992px) {
            .dash-col-half {
                flex: 0 0 50%;
                max-width: 50%;
                margin-bottom: 0;
            }
            .dash-col-7 {
                flex: 0 0 58.333%;
                max-width: 58.333%;
                margin-bottom: 0;
            }
            .dash-col-5 {
                flex: 0 0 41.666%;
                max-width: 41.666%;
                margin-bottom: 0;
            }
        }

        @media (max-width: 640px) {
            .dash-analytics-card {
                padding: 14px !important;
                border-radius: 14px !important;
                margin-bottom: 16px !important;
            }
            .dash-chart-container {
                height: 210px !important;
            }
            .earning-tile-btn {
                height: 58px !important;
                min-width: 56px !important;
                border-radius: 10px !important;
            }
            .earning-tile-btn i {
                font-size: 14px !important;
                margin-bottom: 2px !important;
            }
            .earning-tile-btn span {
                font-size: 10px !important;
            }
        }

        @media (max-width: 480px) {
            .dash-subcard-3 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 8px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchIdForQuery = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $invoiceQuery = App\Models\Invoice::query();
        $total_return_query = App\Models\ReturnTbl::query();
        $total_purchase_query = App\Models\Purchase::query();
        $return_purchase_query = App\Models\ReturnPurchase::query();
        $baseQuery = App\Models\Invoice::where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id');
        });
        $top_selling_query = App\Models\InvoiceItem::select('product_id', \DB::raw('SUM(main_qty) as total_qty'));

        if ($branchIdForQuery) {
            $invoiceQuery->where('branch_id', $branchIdForQuery);
            $total_return_query->where('branch_id', $branchIdForQuery);
            $total_purchase_query->where('branch_id', $branchIdForQuery);
            $return_purchase_query->where('branch_id', $branchIdForQuery);
            $baseQuery->where(function($q) use ($branchIdForQuery) {
                $q->where('branch_id', $branchIdForQuery)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
            $top_selling_query->where('branch_id', $branchIdForQuery);
        }

        $total_invoice = App\Models\Invoice::getFakeSum($invoiceQuery, 'total_amount');
        $total_return = $total_return_query->sum('total_return');
        $total_purchase = $total_purchase_query->sum('total_amount');
        $return_purchase = $return_purchase_query->sum('total_return');
        $currency = '৳';

        // Order Summary Counts
        $total_orders_count = (clone $baseQuery)->count();
        
        $pending_orders_count = (clone $baseQuery)
            ->whereIn('order_status', ['pending', 'in_review', 'hold', 'unknown', 'processing'])->count();
            
        $cancelled_orders_count = (clone $baseQuery)
            ->whereIn('order_status', ['cancelled', 'cancelled_approval_pending', 'failed'])->count();
            
        $completed_delivered_count = (clone $baseQuery)
            ->whereIn('order_status', ['delivered', 'partial_delivered', 'delivered_approval_pending', 'completed'])->count();
        
        $shipped_orders_count = (clone $baseQuery)
            ->whereNotNull('consignment_id')->where('consignment_id', '!=', '')
            ->whereNotIn('order_status', [
                'pending', 'in_review', 'hold', 'unknown', 
                'delivered', 'partial_delivered', 'delivered_approval_pending', 
                'cancelled', 'cancelled_approval_pending', 
                'returned'
            ])->count();
            
        $courier_pending_count = (clone $baseQuery)
            ->where('order_status', 'Failed to place order')->count();
            
        $returned_orders_count = (clone $baseQuery)
            ->where(function($q) {
                $q->where('status', 2)->orWhereIn('order_status', ['returned']);
            })->count();

        // Top Selling Products Query
        $top_selling_products = $top_selling_query->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->limit(3)
            ->with('product')
            ->get();

        // Chart 1 Data: Manual / Outlet POS Sales
        $manualInvQuery = App\Models\Invoice::query()->where(function($q) {
            $q->whereNull('sale_type')->orWhereNotIn('sale_type', ['Online', 'WooCommerce']);
        })->whereNull('woocommerce_order_id');

        if ($branchIdForQuery) {
            $manualInvQuery->where('branch_id', $branchIdForQuery);
        }

        $manualTotalCount = (clone $manualInvQuery)->count();
        $manualInvIds = (clone $manualInvQuery)->pluck('id');
        $manualTotalQty = (float) App\Models\InvoiceItem::whereIn('invoice_id', $manualInvIds)->sum('main_qty');

        $manualFullPaidCount = (clone $manualInvQuery)->where('total_due', '<=', 0)->count();
        $manualPartialPaidCount = (clone $manualInvQuery)->where('total_paid', '>', 0)->where('total_due', '>', 0)->count();
        $manualDueCount = (clone $manualInvQuery)->where('total_paid', '<=', 0)->where('total_due', '>', 0)->count();

        $manualLabels = ['Full Paid Invoices', 'Partial Paid Invoices', 'Due Invoices'];
        $manualData = [$manualFullPaidCount, $manualPartialPaidCount, $manualDueCount];

        // Chart 2 Data: Online POS Sales (Platform Breakdown)
        $onlineInvQuery = App\Models\Invoice::query()->where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($branchIdForQuery) {
            $onlineInvQuery->where(function($q) use ($branchIdForQuery) {
                $q->where('branch_id', $branchIdForQuery)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
        }

        $onlinePlatformLabels = $online_platforms_chart['labels'] ?? [];
        $onlinePlatformData = $online_platforms_chart['data'] ?? [];
        $onlinePlatformQty = $online_platforms_chart['qtys'] ?? [];
        $wooOrdersCount = $online_platforms_chart['woo_orders'] ?? 0;
        $webOrdersCount = $online_platforms_chart['web_orders'] ?? 0;
        $onlineSalesCount = $online_platforms_chart['total_orders'] ?? (clone $onlineInvQuery)->count();
        $onlineTotalQty = $online_platforms_chart['total_qty'] ?? 0;

        // Status Counts for Online Orders Side Panels
        $statusProcessingCount = $online_platforms_chart['status_counts']['processing'] ?? (clone $onlineInvQuery)->where(function($q) {
            $q->whereNull('order_status')->orWhereIn('order_status', ['Processing', 'processing', 'Pending', 'pending']);
        })->count();

        $statusRtsCount = $online_platforms_chart['status_counts']['rts'] ?? (clone $onlineInvQuery)->where(function($q) {
            $q->whereIn('order_status', ['RTS', 'rts', 'Approved', 'approved']);
        })->count();

        $statusOnHoldCount = $online_platforms_chart['status_counts']['on_hold'] ?? (clone $onlineInvQuery)->where(function($q) {
            $q->whereIn('order_status', ['On Hold', 'on_hold', 'on-hold', 'Hold', 'hold', 'good but no response', 'no response', 'advance payment']);
        })->count();

        $statusInCourierCount = $online_platforms_chart['status_counts']['in_courier'] ?? (clone $onlineInvQuery)->where(function($q) {
            $q->whereNotNull('consignment_id')->orWhereIn('order_status', ['In Courier', 'in_courier', 'shipped', 'Shipped']);
        })->count();

        $statusDeliveredCount = $online_platforms_chart['status_counts']['delivered'] ?? (clone $onlineInvQuery)->where(function($q) {
            $q->whereIn('order_status', ['Delivered', 'delivered', 'completed']);
        })->count();

        $statusCancelledCount = $online_platforms_chart['status_counts']['cancelled'] ?? (clone $onlineInvQuery)->where(function($q) {
            $q->whereIn('order_status', ['Cancelled', 'cancelled', 'failed', 'returned', 'Returned']);
        })->count();
    @endphp

    <div class="">
        <!-- Header & Filter -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                @php
                    $hour = date('H');
                    $greeting = '';
                    if ($hour >= 5 && $hour < 12) {
                        $greeting = 'Good Morning';
                    } elseif ($hour >= 12 && $hour < 17) {
                        $greeting = 'Good Afternoon';
                    } elseif ($hour >= 17 && $hour < 21) {
                        $greeting = 'Good Evening';
                    } else {
                        $greeting = 'Welcome back';
                    }
                @endphp
                <h1 class="text-lg font-extrabold text-slate-800 dark:text-white tracking-tight leading-tight">
                    {{ __($greeting) }}, <span class="text-blue-600 font-black">{{ Auth::user()->name ?? __('Admin') }}</span>
                </h1>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 uppercase tracking-widest">
                    <i class="fa-solid fa-calendar-day text-xs text-slate-400"></i>
                    {{ date('l, d F Y') }}
                </p>
            </div>
            
            <!-- Responsive Filter Nav & Summary Report Controls -->
            <style>
                .hide-scroll::-webkit-scrollbar { display: none; }
                .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }

                .sales-view-tab-btn {
                    padding: 6px 14px;
                    font-size: 12.5px;
                    font-weight: 700;
                    border-radius: 8px;
                    border: 1px solid transparent;
                    color: #64748b;
                    background: transparent;
                    transition: all 0.2s ease;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                }
                .sales-view-tab-btn:hover {
                    color: #1e293b;
                }
                .dark .sales-view-tab-btn:hover {
                    color: #f1f5f9;
                }
                .sales-view-tab-btn.active {
                    background: #ffffff;
                    color: #7c3aed;
                    border-color: #e2e8f0;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
                }
                .dark .sales-view-tab-btn.active {
                    background: #1e293b;
                    color: #c084fc;
                    border-color: #334155;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                }

                .web-order-subtab, .woo-order-subtab {
                    padding: 6px 12px;
                    font-size: 13px;
                    font-weight: 700;
                    color: #64748b;
                    background: transparent;
                    border: none;
                    border-bottom: 2px solid transparent;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }
                .web-order-subtab:hover, .woo-order-subtab:hover {
                    color: #1e293b;
                }
                .dark .web-order-subtab:hover, .dark .woo-order-subtab:hover {
                    color: #f1f5f9;
                }
                .web-order-subtab.active {
                    color: #2563eb;
                    border-bottom-color: #2563eb;
                }
                .dark .web-order-subtab.active {
                    color: #60a5fa;
                    border-bottom-color: #60a5fa;
                }
                .woo-order-subtab.active {
                    color: #9333ea;
                    border-bottom-color: #9333ea;
                }
                .dark .woo-order-subtab.active {
                    color: #c084fc;
                    border-bottom-color: #c084fc;
                }

                .source-view-toggle-btn {
                    padding: 4px 10px;
                    font-size: 11px;
                    font-weight: 700;
                    border-radius: 6px;
                    border: 1px solid transparent;
                    color: #64748b;
                    background: transparent;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .source-view-toggle-btn.active {
                    background: #9333ea;
                    color: #ffffff;
                    box-shadow: 0 1px 3px rgba(147, 51, 234, 0.3);
                }
                .graph-ctrl-select {
                    padding: 4px 10px;
                    font-size: 12px;
                    font-weight: 700;
                    border-radius: 8px;
                    border: 1px solid #e2e8f0;
                    background-color: #ffffff;
                    color: #334155;
                    outline: none;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .dark .graph-ctrl-select {
                    border-color: #334155;
                    background-color: #0f172a;
                    color: #cbd5e1;
                }
                .graph-ctrl-select:focus {
                    border-color: #9333ea;
                }
                .status-legend-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                    font-size: 12px;
                    font-weight: 700;
                    padding: 4px 0;
                }
            </style>
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Custom Date Range & Summary Report Button -->
                <div class="flex items-center gap-1.5 bg-white dark:bg-slate-800 p-1.5 rounded-xl shadow-sm border border-slate-200/90 dark:border-slate-700/80 flex-nowrap">
                    <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-200/70 dark:border-slate-700/70">
                        <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                        <input type="date" id="dashStartDate" value="{{ date('Y-m-d') }}" 
                            class="bg-transparent border-0 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-0 p-0 cursor-pointer w-[110px]">
                        <span class="text-xs text-slate-400 font-bold px-0.5">-</span>
                        <input type="date" id="dashEndDate" value="{{ date('Y-m-d') }}" 
                            class="bg-transparent border-0 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-0 p-0 cursor-pointer w-[110px]">
                    </div>
                    
                    <button type="button" id="btnGenCustomSummary" title="Generate Product Sales Summary Report" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-extrabold transition flex items-center justify-center gap-1.5 shadow-sm whitespace-nowrap cursor-pointer shrink-0">
                        <i class="fas fa-file-invoice"></i> {{ __('Summary Report') }}
                    </button>
                </div>

                <!-- Quick Preset Filter Tabs -->
                <div class="bg-white dark:bg-slate-800 p-1 rounded-xl shadow-sm border border-slate-200/90 dark:border-slate-700/80 flex items-center overflow-x-auto max-w-full hide-scroll">
                    <ul class="flex items-center gap-1 filter-nav m-0 p-0 list-none hide-scroll" id="filterNav">
                        <li><a class="nav-link active" href="#" data-filter="Today">{{ __('Today') }}</a></li>
                        <li><a class="nav-link" href="#" data-filter="Yesterday">{{ __('Yesterday') }}</a></li>
                        <li><a class="nav-link" href="#" data-filter="This-week">{{ __('This Week') }}</a></li>
                        <li><a class="nav-link" href="#" data-filter="This-month">{{ __('This Month') }}</a></li>
                        <li><a class="nav-link" href="#" data-filter="This-year">{{ __('This Year') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Primary Filtered Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <!-- Manual / Outlet POS Sale Card -->
            <a href="{{ route('invoice.index', ['sale_type' => 'Manual']) }}" class="block group">
                <div class="stat-card bg-white dark:bg-slate-800 rounded-lg shadow-sm border-t-4 border-blue-500 p-3 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-blue-600 dark:text-blue-400 flex items-baseline">
                                <span class="text-sm font-medium text-slate-400 mr-1">{{ $currency }}</span>
                                <span id="manualSaleAmount">{{ number_format($data['manual_sale'], 0) }}</span>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1">
                                <span class="filterName">{{ __('Today') }}</span> {{ __('Outlate Sale') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500">
                            <i class="fas fa-store text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 dark:text-slate-500"><span id="manualCount">{{ number_format($data['manual_count']) }}</span> {{ __('Invoices') }}</span>
                        <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $currency }}{{ number_format($total_manual_sale, 0) }}</span>
                    </div>
                </div>
            </a>

            <!-- Online / E-Commerce Sale Card -->
            <a href="{{ route('invoice.online.sale') }}" class="block group">
                <div class="stat-card bg-white dark:bg-slate-800 rounded-lg shadow-sm border-t-4 border-purple-500 p-3 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-purple-600 dark:text-purple-400 flex items-baseline">
                                <span class="text-sm font-medium text-slate-400 mr-1">{{ $currency }}</span>
                                <span id="onlineSaleAmount">{{ number_format($data['online_sale'], 0) }}</span>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1">
                                <span class="filterName">{{ __('Today') }}</span> {{ __('Online Sale') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-purple-50 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-purple-500">
                            <i class="fas fa-globe text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 dark:text-slate-500"><span id="onlineCount">{{ number_format($data['online_count']) }}</span> {{ __('Orders') }}</span>
                        <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $currency }}{{ number_format($total_online_sale, 0) }}</span>
                    </div>
                </div>
            </a>

            <!-- Total Combined Sale Card -->
            <a href="{{ route('invoice.index') }}" class="block group">
                <div class="stat-card bg-white dark:bg-slate-800 rounded-lg shadow-sm border-t-4 border-emerald-500 p-3 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 flex items-baseline">
                                <span class="text-sm font-medium text-slate-400 mr-1">{{ $currency }}</span>
                                <span id="saleAmount">{{ number_format($data['sale'], 0) }}</span>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1">
                                <span class="filterName">{{ __('Today') }}</span> {{ __('Total Sale') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-emerald-500">
                            <i class="fas fa-sack-dollar text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Lifetime Total') }}</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $currency }}{{ number_format($total_invoice, 0) }}</span>
                    </div>
                </div>
            </a>

            <!-- Purchase Card -->
            @if (check_permission('dashboard.dashboard'))
            <a href="{{ route('purchase.index') }}" class="block group">
                <div class="stat-card bg-white dark:bg-slate-800 rounded-lg shadow-sm border-t-4 border-indigo-500 p-3 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-slate-800 dark:text-white flex items-baseline">
                                <span class="text-sm font-medium text-slate-400 mr-1">{{ $currency }}</span>
                                <span id="purchaseAmount">{{ number_format($data['purchase'], 0) }}</span>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1"><span class="filterName">{{ __('Today') }}</span> {{ __('Purchase') }}</p>
                        </div>
                        <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-500">
                            <i class="fas fa-bag-shopping text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Lifetime') }}</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $currency }}{{ number_format($total_pur_amount, 0) }}</span>
                    </div>
                </div>
            </a>
            @endif

            <!-- Expense Card -->
            <a href="{{ route('expense.index') }}" class="block group">
                <div class="stat-card bg-white dark:bg-slate-800 rounded-lg shadow-sm border-t-4 border-orange-500 p-3 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-slate-800 dark:text-white flex items-baseline">
                                <span class="text-sm font-medium text-slate-400 mr-1">{{ $currency }}</span>
                                <span id="expenseAmount">{{ number_format($data['expense'], 0) }}</span>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1"><span class="filterName">{{ __('Today') }}</span> {{ __('Expense') }}</p>
                        </div>
                        <div class="w-8 h-8 bg-orange-50 dark:bg-orange-900/30 rounded-lg flex items-center justify-center text-orange-500">
                            <i class="fas fa-money-bill-transfer text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Operational') }}</span>
                        <span class="text-orange-600 dark:text-orange-400 font-bold">{{ __('Expenses') }}</span>
                    </div>
                </div>
            </a>

            <!-- Profit Card -->
            @if (check_permission('dashboard.dashboard'))
            <a href="{{ route('report.profit-loss') }}" class="block group">
                <div class="stat-card bg-white dark:bg-slate-800 rounded-lg shadow-sm border-t-4 border-fuchsia-500 p-3 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-bold text-slate-800 dark:text-white flex items-baseline">
                                <span class="text-sm font-medium text-slate-400 mr-1">{{ $currency }}</span>
                                <span id="profitAmount">{{ number_format($data['profit'], 0) }}</span>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1"><span class="filterName">{{ __('Today') }}</span> {{ __('Profit') }}</p>
                        </div>
                        <div class="w-8 h-8 bg-fuchsia-50 dark:bg-fuchsia-900/30 rounded-lg flex items-center justify-center text-fuchsia-500">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-slate-50 dark:border-slate-700 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 dark:text-slate-500">{{ __('Net Profit') }}</span>
                        <span class="text-fuchsia-600 dark:text-fuchsia-400 font-bold">{{ __('Growth') }}</span>
                    </div>
                </div>
            </a>
            @endif
        </div>

        <!-- Sales Channel Breakdown: Manual POS vs Online Sales (Side-by-Side) -->
        <div class="dash-card-row mb-4">
            <!-- Left Box: Manual / Outlet POS Sales -->
            <div class="dash-col-half">
                <div class="card h-100 shadow-sm border-0 rounded-2xl" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 50%, #f0fdf4 100%); border: 1.5px solid #bfdbfe !important; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; overflow: visible !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3 flex-wrap gap-2" style="border-color: #dbeafe !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 44px; height: 44px; background-color: #2563eb; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <h4 class="m-0 font-extrabold text-slate-800 dark:text-white" style="font-size: 16px; font-weight: 800; color: #1e293b;">{{ __('Manual / Outlet Sale') }}</h4>
                                    <p class="m-0 text-muted" style="font-size: 12px; color: #64748b;">{{ __('In-store Counter Sales & Walk-in Customers') }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <!-- Date Filter Dropdown for Manual Sale -->
                                <div class="dash-date-filter-wrapper" data-channel="manual">
                                    <button type="button" class="dash-filter-btn" aria-haspopup="true" aria-expanded="false">
                                        <i class="far fa-calendar-alt text-slate-400"></i>
                                        <span class="dash-filter-label">{{ __('Today') }}</span>
                                        <i class="fas fa-chevron-down dash-filter-caret text-slate-400"></i>
                                    </button>

                                    <div class="dash-filter-dropdown shadow-2xl">
                                        <!-- Quick Items -->
                                        <div class="dash-filter-item active" data-filter="Today">
                                            <span>{{ __('Today') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Yesterday">
                                            <span>{{ __('Yesterday') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-7-days">
                                            <span>{{ __('Last 7 days') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-30-days">
                                            <span>{{ __('Last 30 days') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>

                                        <!-- Extended Range -->
                                        <div class="dash-filter-section-title">{{ __('EXTENDED RANGE') }}</div>
                                        <div class="dash-filter-item" data-filter="This-month">
                                            <span>{{ __('This month') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-month">
                                            <span>{{ __('Last month') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="This-year">
                                            <span>{{ __('This year') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-year">
                                            <span>{{ __('Last year') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Lifetime">
                                            <span>{{ __('Lifetime') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>

                                        <!-- Custom -->
                                        <div class="dash-filter-section-title">{{ __('CUSTOM') }}</div>
                                        <div class="dash-filter-custom-trigger" data-type="single">
                                            <div class="d-flex align-items-center justify-content-between w-100 dash-custom-header-row">
                                                <span>{{ __('Select a date') }}</span>
                                                <i class="fas fa-angle-right custom-expand-icon"></i>
                                            </div>
                                            <div class="dash-custom-panel dash-custom-single-panel" style="display: none;">
                                                <input type="date" class="form-control form-control-sm dash-single-input" value="{{ date('Y-m-d') }}">
                                                <button type="button" class="btn btn-sm btn-primary w-100 mt-2 dash-btn-apply-single">{{ __('Apply Date') }}</button>
                                            </div>
                                        </div>

                                        <div class="dash-filter-custom-trigger" data-type="range">
                                            <div class="d-flex align-items-center justify-content-between w-100 dash-custom-header-row">
                                                <span>{{ __('Custom range') }}</span>
                                                <i class="fas fa-angle-right custom-expand-icon"></i>
                                            </div>
                                            <div class="dash-custom-panel dash-custom-range-panel" style="display: none;">
                                                <div class="mb-1">
                                                    <span class="dash-custom-input-label">{{ __('Start Date') }}</span>
                                                    <input type="date" class="form-control form-control-sm dash-range-start-input" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="mb-2">
                                                    <span class="dash-custom-input-label">{{ __('End Date') }}</span>
                                                    <input type="date" class="form-control form-control-sm dash-range-end-input" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary w-100 dash-btn-apply-range">{{ __('Apply Range') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('invoice.index', ['sale_type' => 'Manual']) }}" class="btn btn-sm btn-primary font-bold shadow-sm" style="background-color: #2563eb; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 6px 14px;">
                                    <i class="fas fa-list mr-1"></i> {{ __('View Manual Invoices') }}
                                </a>
                            </div>
                        </div>

                        <!-- 4 Metric Cards in Responsive Grid -->
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        <span class="filterName filterNameManual">{{ __('Today') }}</span> {{ __('Sale') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #2563eb; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600;">{{ $currency }}</span>
                                        <span id="summaryManualSale">{{ number_format($data['manual_sale'], 0) }}</span>
                                    </span>
                                    <span style="font-size: 10.5px; color: #64748b; font-weight: 700; margin-top: 4px; display: block;">
                                        <span id="summaryManualCount">{{ number_format($data['manual_count']) }}</span> {{ __('Invoices') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        {{ __('Lifetime Sales') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #1e293b; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600; color: #94a3b8;">{{ $currency }}</span>
                                        <span>{{ number_format($total_manual_sale, 0) }}</span>
                                    </span>
                                    <span style="font-size: 10.5px; color: #64748b; font-weight: 700; margin-top: 4px; display: block;">
                                        {{ number_format($total_manual_count) }} {{ __('Invoices') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #d1fae5; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        {{ __('Outlet Profit') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #059669; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600;">{{ $currency }}</span>
                                        <span id="summaryManualProfit">{{ number_format($data['manual_profit'] ?? 0, 0) }}</span>
                                    </span>
                                    <span style="font-size: 10px; color: #047857; font-weight: 700; margin-top: 4px; display: block;">
                                        {{ __('Lifetime') }}: {{ $currency }}{{ number_format($total_manual_profit ?? 0, 0) }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #ffe4e6; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #e11d48; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        {{ __('Total Due') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #e11d48; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600; color: #f43f5e;">{{ $currency }}</span>
                                        <span>{{ number_format($total_manual_due, 0) }}</span>
                                    </span>
                                    <span style="font-size: 10.5px; color: #64748b; font-weight: 700; margin-top: 4px; display: block;">
                                        {{ __('Customer Pending') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank & Payment Collection Breakdown -->
                        <div class="mt-3 pt-2 border-top" style="border-color: #dbeafe !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 11.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="fas fa-university text-primary mr-1"></i> {{ __('Bank & Payment Sales') }}
                                </span>
                                <span class="badge" style="background-color: #dbeafe; color: #1d4ed8; font-size: 11px; font-weight: 700; border-radius: 6px; padding: 4px 8px;">
                                    {{ __('Total Collected') }}: {{ $currency }}<span id="bankSalesTotalCollected">{{ number_format($lifetime_bank_sales['total'] ?? 0, 0) }}</span>
                                </span>
                            </div>

                            <div id="bankSalesContainer" class="d-flex flex-column gap-2" style="max-height: 160px; overflow-y: auto;">
                                @forelse(($lifetime_bank_sales['accounts'] ?? []) as $bank)
                                    <div class="bank-account-row" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ str_contains(strtolower($bank['name']), 'cash') ? '#dcfce7' : (str_contains(strtolower($bank['name']), 'bkash') ? '#fce7f3' : (str_contains(strtolower($bank['name']), 'nagad') ? '#ffedd5' : '#e0e7ff')) }}; color: {{ str_contains(strtolower($bank['name']), 'cash') ? '#16a34a' : (str_contains(strtolower($bank['name']), 'bkash') ? '#db2777' : (str_contains(strtolower($bank['name']), 'nagad') ? '#ea580c' : '#4f46e5')) }}; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <i class="fas {{ str_contains(strtolower($bank['name']), 'cash') ? 'fa-money-bill-wave' : (str_contains(strtolower($bank['name']), 'bkash') || str_contains(strtolower($bank['name']), 'nagad') ? 'fa-mobile-alt' : 'fa-building-columns') }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 12.5px; font-weight: 700; color: #1e293b;">
                                                    {{ $bank['name'] }} @if(!empty($bank['account_number'])) <span style="font-size: 11px; color: #64748b; font-weight: 500;">({{ $bank['account_number'] }})</span> @endif
                                                </div>
                                                <div style="font-size: 10.5px; color: #94a3b8; font-weight: 600;">
                                                    <span class="bank-count">{{ $bank['count'] }}</span> {{ __('Invoices') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div style="font-size: 13.5px; font-weight: 800; color: #0f172a;">
                                                {{ $currency }}<span class="bank-amount">{{ number_format($bank['amount'], 0) }}</span>
                                            </div>
                                            <div style="font-size: 10px; font-weight: 700; color: #059669;">
                                                <span class="bank-percent">{{ $bank['percentage'] }}</span>%
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted small text-center py-2">{{ __('No bank sales recorded yet.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2.5 border-top mt-3" style="border-color: #dbeafe !important; font-size: 12px;">
                        <span style="color: #64748b; font-weight: 600;">
                            <i class="fas fa-cash-register text-primary mr-1"></i> {{ __('POS Counter Invoices') }}
                        </span>
                        <a href="{{ route('invoice.create') }}" style="color: #2563eb; font-weight: 800; text-decoration: none;">
                            <i class="fas fa-plus-circle mr-1"></i> {{ __('Open POS Screen') }} &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Box: Online / E-Commerce Sales -->
            <div class="dash-col-half">
                <div class="card h-100 shadow-sm border-0 rounded-2xl" style="background: linear-gradient(135deg, #faf5ff 0%, #ffffff 50%, #fdf2f8 100%); border: 1.5px solid #e9d5ff !important; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; overflow: visible !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3 flex-wrap gap-2" style="border-color: #f3e8ff !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 44px; height: 44px; background-color: #9333ea; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 12px rgba(147,51,234,0.25);">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div>
                                    <h4 class="m-0 font-extrabold text-slate-800 dark:text-white" style="font-size: 16px; font-weight: 800; color: #1e293b;">{{ __('Online / E-Commerce Sale') }}</h4>
                                    <p class="m-0 text-muted" style="font-size: 12px; color: #64748b;">{{ __('Website, WooCommerce, Facebook & Courier Orders') }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <!-- Date Filter Dropdown for Online Sale -->
                                <div class="dash-date-filter-wrapper" data-channel="online">
                                    <button type="button" class="dash-filter-btn" aria-haspopup="true" aria-expanded="false">
                                        <i class="far fa-calendar-alt text-slate-400"></i>
                                        <span class="dash-filter-label">{{ __('Today') }}</span>
                                        <i class="fas fa-chevron-down dash-filter-caret text-slate-400"></i>
                                    </button>

                                    <div class="dash-filter-dropdown shadow-2xl">
                                        <!-- Quick Items -->
                                        <div class="dash-filter-item active" data-filter="Today">
                                            <span>{{ __('Today') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Yesterday">
                                            <span>{{ __('Yesterday') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-7-days">
                                            <span>{{ __('Last 7 days') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-30-days">
                                            <span>{{ __('Last 30 days') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>

                                        <!-- Extended Range -->
                                        <div class="dash-filter-section-title">{{ __('EXTENDED RANGE') }}</div>
                                        <div class="dash-filter-item" data-filter="This-month">
                                            <span>{{ __('This month') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-month">
                                            <span>{{ __('Last month') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="This-year">
                                            <span>{{ __('This year') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Last-year">
                                            <span>{{ __('Last year') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                        <div class="dash-filter-item" data-filter="Lifetime">
                                            <span>{{ __('Lifetime') }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>

                                        <!-- Custom -->
                                        <div class="dash-filter-section-title">{{ __('CUSTOM') }}</div>
                                        <div class="dash-filter-custom-trigger" data-type="single">
                                            <div class="d-flex align-items-center justify-content-between w-100 dash-custom-header-row">
                                                <span>{{ __('Select a date') }}</span>
                                                <i class="fas fa-angle-right custom-expand-icon"></i>
                                            </div>
                                            <div class="dash-custom-panel dash-custom-single-panel" style="display: none;">
                                                <input type="date" class="form-control form-control-sm dash-single-input" value="{{ date('Y-m-d') }}">
                                                <button type="button" class="btn btn-sm btn-primary w-100 mt-2 dash-btn-apply-single">{{ __('Apply Date') }}</button>
                                            </div>
                                        </div>

                                        <div class="dash-filter-custom-trigger" data-type="range">
                                            <div class="d-flex align-items-center justify-content-between w-100 dash-custom-header-row">
                                                <span>{{ __('Custom range') }}</span>
                                                <i class="fas fa-angle-right custom-expand-icon"></i>
                                            </div>
                                            <div class="dash-custom-panel dash-custom-range-panel" style="display: none;">
                                                <div class="mb-1">
                                                    <span class="dash-custom-input-label">{{ __('Start Date') }}</span>
                                                    <input type="date" class="form-control form-control-sm dash-range-start-input" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="mb-2">
                                                    <span class="dash-custom-input-label">{{ __('End Date') }}</span>
                                                    <input type="date" class="form-control form-control-sm dash-range-end-input" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary w-100 dash-btn-apply-range">{{ __('Apply Range') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('website-orders.index') }}" class="btn btn-sm text-white font-bold shadow-sm" style="background-color: #2563eb; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 6px 12px;">
                                    <i class="fas fa-globe mr-1"></i> {{ __('Web Order List') }}
                                </a>
                                <a href="{{ route('woocommerce.orders.list') }}" class="btn btn-sm text-white font-bold shadow-sm" style="background-color: #7c3aed; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 6px 12px;">
                                    <i class="fab fa-wordpress mr-1"></i> {{ __('WooCommerce Orders') }}
                                </a>
                                <a href="{{ route('invoice.online.sale') }}" class="btn btn-sm text-white font-bold shadow-sm" style="background-color: #9333ea; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 6px 14px;">
                                    <i class="fas fa-truck-fast mr-1"></i> {{ __('View Online Sales') }}
                                </a>
                            </div>
                        </div>

                        <!-- 4 Metric Cards in Responsive Grid -->
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        <span class="filterName filterNameOnline">{{ __('Today') }}</span> {{ __('Sale') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #9333ea; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600;">{{ $currency }}</span>
                                        <span id="summaryOnlineSale">{{ number_format($data['online_sale'], 0) }}</span>
                                    </span>
                                    <span style="font-size: 10.5px; color: #64748b; font-weight: 700; margin-top: 4px; display: block;">
                                        <span id="summaryOnlineCount">{{ number_format($data['online_count']) }}</span> {{ __('Orders') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        {{ __('Lifetime Sales') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #1e293b; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600; color: #94a3b8;">{{ $currency }}</span>
                                        <span>{{ number_format($total_online_sale, 0) }}</span>
                                    </span>
                                    <span style="font-size: 10.5px; color: #64748b; font-weight: 700; margin-top: 4px; display: block;">
                                        {{ number_format($total_online_count) }} {{ __('Orders') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #d1fae5; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        {{ __('Online Profit') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #059669; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600;">{{ $currency }}</span>
                                        <span id="summaryOnlineProfit">{{ number_format($data['online_profit'] ?? 0, 0) }}</span>
                                    </span>
                                    <span style="font-size: 10px; color: #047857; font-weight: 700; margin-top: 4px; display: block;">
                                        {{ __('Lifetime') }}: {{ $currency }}{{ number_format($total_online_profit ?? 0, 0) }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div style="background: #ffffff; border: 1px solid #ffe4e6; border-radius: 12px; padding: 12px 8px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                                    <span style="font-size: 10px; font-weight: 800; color: #e11d48; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">
                                        {{ __('Total Due') }}
                                    </span>
                                    <span style="font-size: 16px; font-weight: 900; color: #e11d48; display: block; line-height: 1.2;">
                                        <span style="font-size: 11px; font-weight: 600; color: #f43f5e;">{{ $currency }}</span>
                                        <span>{{ number_format($total_online_due, 0) }}</span>
                                    </span>
                                    <span style="font-size: 10.5px; color: #64748b; font-weight: 700; margin-top: 4px; display: block;">
                                        {{ __('Courier / COD') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Online Profit & Realization Breakdown -->
                        <div class="mt-3 pt-2 border-top" style="border-color: #f3e8ff !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 11.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="fas fa-chart-line text-purple-600 mr-1"></i> {{ __('Online Profit & Realization') }}
                                </span>
                                <span class="badge" style="background-color: #f3e8ff; color: #7e22ce; font-size: 11px; font-weight: 700; border-radius: 6px; padding: 4px 8px;">
                                    {{ __('Net Profit') }}: {{ $currency }}<span id="onlineProfitSummaryTotal">{{ number_format($lifetime_online_profit_overview['total_profit'] ?? ($total_online_profit ?? 0), 0) }}</span>
                                    (<span id="onlineProfitMarginText">{{ $lifetime_online_profit_overview['margin_percent'] ?? 0 }}%</span> Margin)
                                </span>
                            </div>

                            <div id="onlineProfitContainer" class="d-flex flex-column gap-2" style="max-height: 160px; overflow-y: auto;">
                                <!-- Delivered / Realized Profit -->
                                <div class="online-profit-row" style="background: #ffffff; border: 1px solid #d1fae5; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 12.5px; font-weight: 700; color: #1e293b;">
                                                {{ __('Delivered Realized Profit') }}
                                            </div>
                                            <div style="font-size: 10.5px; color: #047857; font-weight: 600;">
                                                <span id="onlineDeliveredCount">{{ number_format($lifetime_online_profit_overview['delivered_count'] ?? 0) }}</span> {{ __('Orders Delivered') }} ({{ $currency }}<span id="onlineDeliveredSales">{{ number_format($lifetime_online_profit_overview['delivered_sales'] ?? 0, 0) }}</span>)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div style="font-size: 13.5px; font-weight: 800; color: #059669;">
                                            {{ $currency }}<span id="onlineDeliveredProfit">{{ number_format($lifetime_online_profit_overview['delivered_profit'] ?? 0, 0) }}</span>
                                        </div>
                                        <div style="font-size: 10px; font-weight: 700; color: #16a34a;">
                                            {{ __('Realized Cash') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- In-Transit / Courier Potential Profit -->
                                <div class="online-profit-row" style="background: #ffffff; border: 1px solid #e9d5ff; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            <i class="fas fa-truck-fast"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 12.5px; font-weight: 700; color: #1e293b;">
                                                {{ __('In-Courier Potential Profit') }}
                                            </div>
                                            <div style="font-size: 10.5px; color: #7e22ce; font-weight: 600;">
                                                <span id="onlineTransitCount">{{ number_format($lifetime_online_profit_overview['transit_count'] ?? 0) }}</span> {{ __('In-Transit / Courier') }} ({{ $currency }}<span id="onlineTransitSales">{{ number_format($lifetime_online_profit_overview['transit_sales'] ?? 0, 0) }}</span>)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div style="font-size: 13.5px; font-weight: 800; color: #9333ea;">
                                            {{ $currency }}<span id="onlineTransitProfit">{{ number_format($lifetime_online_profit_overview['transit_profit'] ?? 0, 0) }}</span>
                                        </div>
                                        <div style="font-size: 10px; font-weight: 700; color: #a855f7;">
                                            {{ __('Expected') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2.5 border-top flex-wrap gap-2" style="border-color: #f3e8ff !important; font-size: 11px;">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span style="padding: 3px 8px; background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 6px; font-weight: 700;">
                                Processing: <span id="statusProcessingCount">{{ $statusProcessingCount }}</span>
                            </span>
                            <span style="padding: 3px 8px; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; border-radius: 6px; font-weight: 700;">
                                RTS: <span id="statusRtsCount">{{ $statusRtsCount }}</span>
                            </span>
                            <span style="padding: 3px 8px; background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 6px; font-weight: 700;">
                                In Courier: <span id="statusInCourierCount">{{ $statusInCourierCount }}</span>
                            </span>
                            <span style="padding: 3px 8px; background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 6px; font-weight: 700;">
                                Delivered: <span id="statusDeliveredCount">{{ $statusDeliveredCount }}</span>
                            </span>
                        </div>
                        <a href="{{ route('invoice.online.sale') }}" style="color: #9333ea; font-weight: 800; text-decoration: none;">
                            <i class="fas fa-arrow-right mr-1"></i> {{ __('Online Management') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (env('APP_ONLINE') == 'yes')
        <!-- Order Summary Section -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden mb-6">
            <div class="p-6">
                <h3 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                    <i class="fas fa-boxes-packing text-[10px]"></i> {{ __('Order Summary') }}
                </h3>
                
                <!-- Row 1 -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="p-4 rounded-lg bg-indigo-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Total Order') }}</span>
                        <span class="text-2xl font-black text-slate-800 dark:text-white mt-2">{{ $total_orders_count }}</span>
                    </div>

                    <div class="p-4 rounded-lg bg-amber-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Pending') }}</span>
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-500 mt-2">{{ $pending_orders_count }}</span>
                    </div>

                    <div class="p-4 rounded-lg bg-rose-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Cancelled') }}</span>
                        <span class="text-2xl font-black text-rose-600 dark:text-rose-500 mt-2">{{ $cancelled_orders_count }}</span>
                    </div>

                    <div class="p-4 rounded-lg bg-emerald-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Delivered') }}</span>
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-500 mt-2">{{ $completed_delivered_count }}</span>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="p-4 rounded-lg bg-blue-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Shipped') }}</span>
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-500 mt-2">{{ $shipped_orders_count }}</span>
                    </div>

                    <div class="p-4 rounded-lg bg-orange-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Courier Failed') }}</span>
                        <span class="text-2xl font-black text-orange-600 dark:text-orange-500 mt-2">{{ $courier_pending_count }}</span>
                    </div>

                    <div class="p-4 rounded-lg bg-red-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Return') }}</span>
                        <span class="text-2xl font-black text-red-600 dark:text-red-500 mt-2">{{ $returned_orders_count }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6">
                <h3 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-[10px]"></i> {{ __('Summary & Operations') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    
                    <!-- Row 1: Purchase -->
                    <!-- Purchase Due -->
                    <a href="{{ route('report.supplier-due') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-500 dark:text-red-400 text-xs shrink-0">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Purchase Due') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $currency }} {{ number_format($total_pur_due, 0) }}</span>
                    </a>

                    <!-- Overall Purchase -->
                    <a href="{{ route('purchase.index') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400 text-xs shrink-0">
                                <i class="fas fa-cart-shopping"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Purchase Amount') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $currency }} {{ number_format($total_pur_amount, 0) }}</span>
                    </a>

                    <!-- Row 2: Sales Overview -->
                    <!-- Manual POS Sale Amount -->
                    <a href="{{ route('invoice.index', ['sale_type' => 'Manual']) }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-blue-500 dark:hover:border-blue-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400 text-xs shrink-0">
                                <i class="fas fa-store"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Manual / Sales Amount') }}</span>
                        </div>
                        <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $currency }} {{ number_format($total_manual_sale, 0) }}</span>
                    </a>

                    <!-- Online POS Sale Amount -->
                    <a href="{{ route('invoice.online.sale') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-purple-500 dark:hover:border-purple-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-500 dark:text-purple-400 text-xs shrink-0">
                                <i class="fas fa-globe"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Online Sales Amount') }}</span>
                        </div>
                        <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $currency }} {{ number_format($total_online_sale, 0) }}</span>
                    </a>

                    <!-- Manual Sales Due -->
                    <a href="{{ route('report.customer-due') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-500 dark:text-rose-400 text-xs shrink-0">
                                <i class="fas fa-hand-holding-dollar"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Manual Sales Due') }}</span>
                        </div>
                        <span class="text-rose-600 dark:text-rose-400 font-bold">{{ $currency }} {{ number_format($total_manual_due, 0) }}</span>
                    </a>

                    <!-- Online Sales Due -->
                    <a href="{{ route('invoice.online.sale') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 dark:text-amber-400 text-xs shrink-0">
                                <i class="fas fa-truck-ramp-box"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Online / COD Due') }}</span>
                        </div>
                        <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $currency }} {{ number_format($total_online_due, 0) }}</span>
                    </a>

                    <!-- Collective Sales -->
                    <a href="{{ route('invoice.index') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500 dark:text-emerald-400 text-xs shrink-0">
                                <i class="fas fa-sack-dollar"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Combined Sales Amount') }}</span>
                        </div>
                        <div class="flex flex-col items-start">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $currency }} {{ number_format($total_sale, 0) }}</span>
                        </div>
                    </a>

                    <!-- Row 3: Stock -->
                    <!-- Stock Valuation (Purchase Rate) -->
                    <a href="{{ route('report.stock') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center text-violet-500 dark:text-violet-400 text-xs shrink-0">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Available Stock Amount (Purchase Rate)') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $currency }} {{ number_format($totalPrice, 0) }}</span>
                    </a>

                    <!-- Stock Valuation (Sale Rate) -->
                    <a href="{{ route('report.stock') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center text-violet-500 dark:text-violet-400 text-xs shrink-0">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Available Stock Amount (Sale Rate)') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $currency }} {{ number_format($available_stock_sale_val ?? 0, 0) }}</span>
                    </a>

                    <!-- Row 4: Returns and Qty -->
                    <!-- Sales Return -->
                    <a href="{{ route('return.sale') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 dark:text-indigo-400 text-xs shrink-0">
                                <i class="fas fa-arrow-rotate-left"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Sales Return') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $currency }} {{ number_format($total_return_amount, 0) }}</span>
                    </a>

                    <!-- Total Invoices / Sale Qty -->
                    <a href="{{ route('invoice.index') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-fuchsia-50 dark:bg-fuchsia-900/30 flex items-center justify-center text-fuchsia-500 dark:text-fuchsia-400 text-xs shrink-0">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Sale Qty') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ number_format($total_invoice_count, 0) }}</span>
                    </a>

                    <!-- Row 5: Others -->
                    <!-- Total Products -->
                    <a href="{{ route('product.index') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 dark:text-amber-400 text-xs shrink-0">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Products') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ number_format($total_product, 0) }}</span>
                    </a>

                    <!-- Active Customers -->
                    <a href="{{ route('customer.index') }}" class="group flex items-center justify-start gap-4 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:border-slate-500 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-500 dark:text-sky-400 text-xs shrink-0">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="text-slate-800 dark:text-white font-semibold text-sm">{{ __('Total Active Customers') }}</span>
                        </div>
                        <span class="text-slate-900 dark:text-white font-bold">{{ number_format($total_customer, 0) }}</span>
                    </a>

                </div>

                <!-- Extra Subtle Note info -->
                <div class="p-2.5 mt-6 bg-slate-100 dark:bg-slate-900/80 rounded-lg border border-slate-200 dark:border-slate-700/50">
                    <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed italic">
                        * {{ __('Data synchronized with real-time inventory and financial records. Last update:') }} {{ date('h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        
        @if (env('APP_ONLINE') == 'yes')
        <!-- Top Selling Products Section -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden mt-6">
            <div class="p-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-4 mb-4">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white tracking-tight">{{ __('Top Selling Products') }}</h3>
                        <p class="text-[12px] font-bold text-slate-400 dark:text-slate-400/80 mt-0.5 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-fire text-amber-500"></i> {{ __('Most Popular Items') }}
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center align-middle mb-0">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300">
                            <tr>
                                <th class="py-3 px-4 text-center font-bold text-xs uppercase tracking-wider">#</th>
                                <th class="py-3 px-4 text-center font-bold text-xs uppercase tracking-wider">{{ __('Image') }}</th>
                                <th class="py-3 px-4 text-left font-bold text-xs uppercase tracking-wider">{{ __('Product Name') }}</th>
                                <th class="py-3 px-4 text-center font-bold text-xs uppercase tracking-wider">{{ __('Stock Qty') }}</th>
                                <th class="py-3 px-4 text-center font-bold text-xs uppercase tracking-wider">{{ __('Sale Qty') }}</th>
                                <th class="py-3 px-4 text-center font-bold text-xs uppercase tracking-wider">{{ __('Selling Price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-800 dark:text-slate-200">
                            @foreach ($top_selling_products as $item)
                                @php
                                    $product = $item->product;
                                    if ($product) {
                                        $purchase = (float) purchased_qty($product);
                                        $sale     = (float) invoiced_qty($product);
                                        $sale_ret = (float) returned_qty($product);
                                        $pur_ret  = (float) return_pur_qty($product);
                                        $damage   = (float) damaged_qty($product);
                                        $adjust_in_total = (float) adjust_in($product);
                                        $adjust_out_total = (float) adjust_out($product);

                                        $stock = $purchase - $sale + $adjust_in_total + $sale_ret - $adjust_out_total - $pur_ret - $damage;
                                    } else {
                                        $stock = 0;
                                    }
                                @endphp
                                <tr>
                                    <td class="py-2.5 px-4 font-bold">{{ $loop->iteration }}</td>
                                    <td class="py-2.5 px-4">
                                        <div class="flex justify-center">
                                            @if ($product && !empty($product->featured_image))
                                                <img src="{{ $product->featured_image }}" class="w-10 h-10 object-contain rounded border border-slate-250 dark:border-slate-700 bg-white" alt="{{ $product->name }}">
                                            @else
                                                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded flex items-center justify-center border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500">
                                                    <i class="fas fa-image text-sm"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-4 text-left">
                                        <span class="font-extrabold text-slate-800 dark:text-white block text-sm">{{ $product?->name ?? __('Unknown Product') }}</span>
                                        <span class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-md mt-1 inline-block uppercase tracking-wider">
                                            Code: {{ $product?->barcode ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-indigo-600 dark:text-indigo-400 text-sm">
                                        {{ number_format($stock, 0) }} pcs
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                        {{ number_format($item->total_qty, 0) }} pcs
                                    </td>
                                    <td class="py-2.5 px-4 font-extrabold text-slate-800 dark:text-white text-sm">
                                        ৳{{ number_format($product?->selling_price ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Sales & Order Analytics Section Header -->
        <div class="mt-8 mb-4 w-full" style="width: 100%; text-align: left;">
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom flex-wrap gap-2 w-full" style="border-color: #e2e8f0; text-align: left;">
                <div style="text-align: left;">
                    <h3 class="m-0 font-extrabold text-slate-800 dark:text-white" style="font-size: 18px; letter-spacing: -0.3px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400"></i> {{ __('Sales & Order Analytics') }}
                    </h3>
                    <p class="m-0 text-slate-500 dark:text-slate-400" style="font-size: 12.5px; margin-top: 2px;">
                        {{ __('Manual Outlet POS Sales, Online Platforms & Web E-Commerce Orders breakdown') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 2 Side-by-Side Analytics Cards -->
        <div class="dash-card-row mb-4">
            <!-- Left Card: Manual / Outlet Sales -->
            <div class="dash-col-half flex flex-col">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/60 overflow-hidden flex flex-col justify-between h-full">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight flex items-center gap-2 m-0">
                                <i class="fas fa-store text-blue-500"></i> {{ __('Manual / Outlet Sales') }}
                            </h3>
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-400/80 mt-0.5 m-0 uppercase tracking-wider">
                                {{ __('Payment & Order Breakdown') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <select id="manualSalesRangeSelect" class="graph-ctrl-select">
                                <option value="Last-30-days" selected>{{ __('30D') }}</option>
                                <option value="Today">{{ __('Today') }}</option>
                                <option value="Yesterday">{{ __('Yesterday') }}</option>
                                <option value="Last-7-days">{{ __('7D') }}</option>
                                <option value="This-month">{{ __('This Month') }}</option>
                                <option value="Last-month">{{ __('Last Month') }}</option>
                                <option value="Lifetime">{{ __('Lifetime') }}</option>
                            </select>
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full border border-blue-200 dark:border-blue-800/60">
                                <i class="fas fa-shopping-bag mr-1"></i> <span id="manualOrdersBadgeCount">{{ number_format($manualTotalCount) }}</span>
                            </span>
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-full border border-emerald-200 dark:border-emerald-800/60">
                                <i class="fas fa-boxes mr-1"></i> <span id="manualOrdersBadgeQty">{{ number_format($manualTotalQty) }}</span> Pcs
                            </span>
                        </div>
                    </div>
                    <div class="p-3 sm:p-4 flex-1 flex flex-col items-center justify-center bg-slate-50/30 dark:bg-slate-900/40">
                        <div style="position: relative; width: 100%; max-width: 185px; height: 180px; min-height: 140px; margin: 5px auto; display: flex; align-items: center; justify-content: center;">
                            <canvas id="manualPosChart"></canvas>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700 w-full text-xs font-bold">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                <span class="text-slate-700 dark:text-slate-300">Paid: <strong id="manualLegendPaid">{{ number_format($manualFullPaidCount) }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                                <span class="text-slate-700 dark:text-slate-300">Partial: <strong id="manualLegendPartial">{{ number_format($manualPartialPaidCount) }}</strong></span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                                <span class="text-slate-700 dark:text-slate-300">Due: <strong id="manualLegendDue">{{ number_format($manualDueCount) }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Online Channels Multi-Tab Card -->
            <div class="dash-col-half flex flex-col">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700/60 overflow-hidden flex flex-col justify-between h-full">
                    <!-- Header with Embedded Tabs & Dynamic Filters -->
                    <div class="p-3 sm:p-4 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 flex-wrap">
                        <!-- Embedded Channel Switcher Tabs -->
                        <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-900/80 p-1 rounded-xl border border-slate-200/80 dark:border-slate-700/80 overflow-x-auto max-w-full">
                            <button type="button" class="channel-tab-btn active" data-tab="platforms">
                                <i class="fas fa-layer-group text-purple-500"></i> {{ __('Platforms') }}
                            </button>
                            <button type="button" class="channel-tab-btn" data-tab="web_orders">
                                <i class="fas fa-globe text-blue-500"></i> {{ __('Website Orders') }}
                            </button>
                            <button type="button" class="channel-tab-btn" data-tab="woo_orders">
                                <i class="fab fa-wordpress text-pink-500"></i> {{ __('WooCommerce Orders') }}
                            </button>
                            <button type="button" class="channel-tab-btn" data-tab="source">
                                <i class="fas fa-chart-pie text-emerald-500"></i> {{ __('Orders by Source') }}
                            </button>
                        </div>

                        <!-- Dynamic Controls based on active tab -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Controls for Tab 1: Platforms -->
                            <div id="controlsTabPlatforms" class="flex items-center gap-2 flex-wrap">
                                <select id="onlinePlatformsRangeSelect" class="graph-ctrl-select">
                                    <option value="Last-30-days" selected>{{ __('30D') }}</option>
                                    <option value="Today">{{ __('Today') }}</option>
                                    <option value="Yesterday">{{ __('Yesterday') }}</option>
                                    <option value="Last-7-days">{{ __('7D') }}</option>
                                    <option value="This-month">{{ __('This Month') }}</option>
                                    <option value="Last-month">{{ __('Last Month') }}</option>
                                    <option value="Lifetime">{{ __('Lifetime') }}</option>
                                </select>
                                <button type="button" id="btnGoToWooOrdersTab" title="Click to view WooCommerce Analytics" class="px-2.5 py-1 bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 text-xs font-black rounded-full border border-purple-300 dark:border-purple-800 hover:bg-purple-200 transition-all shadow-sm flex items-center gap-1 cursor-pointer">
                                    <i class="fab fa-wordpress text-purple-600"></i> {{ __('Woo') }}: <span id="onlineBadgeWooCount">{{ number_format($wooOrdersCount ?? 0) }}</span>
                                </button>
                                <button type="button" id="btnGoToWebOrdersTab" title="Click to view Website Orders Analytics" class="px-2.5 py-1 bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-xs font-black rounded-full border border-blue-300 dark:border-blue-800 hover:bg-blue-200 transition-all shadow-sm flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-globe text-blue-600"></i> {{ __('Web') }}: <span id="onlineBadgeWebCount">{{ number_format($web_orders_count ?? ($webOrdersCount ?? 0)) }}</span>
                                </button>
                                <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-full border border-purple-200 dark:border-purple-800/60">
                                    <i class="fas fa-shopping-bag mr-1"></i> <span id="onlineBadgeOrdersCount">{{ number_format($onlineSalesCount ?? 0) }}</span>
                                </span>
                                <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-full border border-emerald-200 dark:border-emerald-800/60">
                                    <i class="fas fa-boxes mr-1"></i> <span id="onlineBadgeTotalQty">{{ number_format($onlineTotalQty ?? 0) }}</span> Pcs
                                </span>
                            </div>

                            <!-- Controls for Tab 2: Website Orders -->
                            <div id="controlsTabWebOrders" class="flex items-center gap-2 flex-wrap" style="display: none;">
                                <select id="webOrdersRangeSelect" class="graph-ctrl-select">
                                    <option value="Last-30-days" selected>{{ __('30D') }}</option>
                                    <option value="Today">{{ __('Today') }}</option>
                                    <option value="Yesterday">{{ __('Yesterday') }}</option>
                                    <option value="Last-7-days">{{ __('7D') }}</option>
                                    <option value="This-month">{{ __('This Month') }}</option>
                                    <option value="Last-month">{{ __('Last Month') }}</option>
                                    <option value="Lifetime">{{ __('Lifetime') }}</option>
                                </select>
                                <a href="{{ route('website-orders.index') }}" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center gap-1 transition-all">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> {{ __('View List') }}
                                </a>
                            </div>

                            <!-- Controls for Tab 3: WooCommerce Orders -->
                            <div id="controlsTabWooOrders" class="flex items-center gap-2 flex-wrap" style="display: none;">
                                <select id="wooOrdersRangeSelect" class="graph-ctrl-select">
                                    <option value="Last-30-days" selected>{{ __('30D') }}</option>
                                    <option value="Today">{{ __('Today') }}</option>
                                    <option value="Yesterday">{{ __('Yesterday') }}</option>
                                    <option value="Last-7-days">{{ __('7D') }}</option>
                                    <option value="This-month">{{ __('This Month') }}</option>
                                    <option value="Last-month">{{ __('Last Month') }}</option>
                                    <option value="Lifetime">{{ __('Lifetime') }}</option>
                                </select>
                                <a href="{{ route('woocommerce.orders.list') }}" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center gap-1 transition-all">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> {{ __('View List') }}
                                </a>
                            </div>

                            <!-- Controls for Tab 4: Orders by Source -->
                            <div id="controlsTabSource" class="flex items-center gap-2 flex-wrap" style="display: none;">
                                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-900 p-0.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase px-1.5">VIEW</span>
                                    <button type="button" class="source-view-toggle-btn active" id="btnSourceViewChart" data-mode="chart">
                                        {{ __('Chart') }}
                                    </button>
                                    <button type="button" class="source-view-toggle-btn" id="btnSourceViewTable" data-mode="table">
                                        {{ __('Table') }}
                                    </button>
                                </div>
                                <select id="ordersBySourceStatusSelect" class="graph-ctrl-select">
                                    <option value="all" selected>{{ __('All Status') }}</option>
                                    <option value="approved">{{ __('Approved') }}</option>
                                    <option value="processing">{{ __('Processing') }}</option>
                                    <option value="in_courier">{{ __('In Courier') }}</option>
                                    <option value="delivered">{{ __('Delivered') }}</option>
                                    <option value="cancelled">{{ __('Cancelled') }}</option>
                                </select>
                                <select id="ordersBySourceRangeSelect" class="graph-ctrl-select">
                                    <option value="Today" selected>{{ __('Today') }}</option>
                                    <option value="Yesterday">{{ __('Yesterday') }}</option>
                                    <option value="Last-7-days">{{ __('7D') }}</option>
                                    <option value="Last-30-days">{{ __('30D') }}</option>
                                    <option value="This-month">{{ __('This Month') }}</option>
                                    <option value="Lifetime">{{ __('Lifetime') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pane 1: Platforms Breakdown (Donut Chart + 6 Badges + Platform Footer) -->
                    <div id="panePlatforms" class="channel-pane p-3 sm:p-4 flex-1 flex flex-col justify-between bg-slate-50/30 dark:bg-slate-900/40">
                        <div class="flex flex-row items-center justify-between gap-1.5 sm:gap-3 w-full my-auto py-1">
                            <!-- Left Side Status Badges -->
                            <div class="flex flex-col gap-1.5 sm:gap-2 w-[98px] xs:w-[115px] sm:w-[135px] xl:w-[145px] shrink-0">
                                <a href="{{ route('invoice.online.sale', ['status' => 'processing']) }}" class="flex items-center justify-between p-1 sm:p-2 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-amber-400 transition-all">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-amber-500 text-white flex items-center justify-center text-[8px] sm:text-[9px] font-bold shadow-sm shrink-0">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <span class="text-[9.5px] sm:text-[11px] font-extrabold text-slate-700 dark:text-slate-200 truncate">{{ __('Processing') }}</span>
                                    </div>
                                    <span id="onlineStatusProcessing" class="px-1 sm:px-1.5 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">
                                        {{ number_format($statusProcessingCount) }}
                                    </span>
                                </a>

                                <a href="{{ route('invoice.online.sale', ['status' => 'rts']) }}" class="flex items-center justify-between p-1 sm:p-2 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-sky-400 transition-all">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-sky-500 text-white flex items-center justify-center text-[8px] sm:text-[9px] font-bold shadow-sm shrink-0">
                                            <i class="fas fa-box"></i>
                                        </span>
                                        <span class="text-[9.5px] sm:text-[11px] font-extrabold text-slate-700 dark:text-slate-200 truncate">{{ __('RTS') }}</span>
                                    </div>
                                    <span id="onlineStatusRts" class="px-1 sm:px-1.5 py-0.5 bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">
                                        {{ number_format($statusRtsCount) }}
                                    </span>
                                </a>

                                <a href="{{ route('invoice.online.sale', ['status' => 'on_hold']) }}" class="flex items-center justify-between p-1 sm:p-2 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-yellow-400 transition-all">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-yellow-500 text-white flex items-center justify-center text-[8px] sm:text-[9px] font-bold shadow-sm shrink-0">
                                            <i class="fas fa-pause-circle"></i>
                                        </span>
                                        <span class="text-[9.5px] sm:text-[11px] font-extrabold text-slate-700 dark:text-slate-200 truncate">{{ __('On Hold') }}</span>
                                    </div>
                                    <span id="onlineStatusOnHold" class="px-1 sm:px-1.5 py-0.5 bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-300 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">
                                        {{ number_format($statusOnHoldCount) }}
                                    </span>
                                </a>
                            </div>

                            <!-- Center: Donut Chart -->
                            <div class="flex-1 flex justify-center items-center min-w-0 px-1">
                                <div style="position: relative; width: 100%; max-width: 165px; height: 160px; min-height: 110px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="onlinePlatformChart"></canvas>
                                </div>
                            </div>

                            <!-- Right Side Status Badges -->
                            <div class="flex flex-col gap-1.5 sm:gap-2 w-[98px] xs:w-[115px] sm:w-[135px] xl:w-[145px] shrink-0">
                                <a href="{{ route('invoice.online.sale', ['status' => 'in_courier']) }}" class="flex items-center justify-between p-1 sm:p-2 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-blue-400 transition-all">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-blue-500 text-white flex items-center justify-center text-[8px] sm:text-[9px] font-bold shadow-sm shrink-0">
                                            <i class="fas fa-truck"></i>
                                        </span>
                                        <span class="text-[9.5px] sm:text-[11px] font-extrabold text-slate-700 dark:text-slate-200 truncate">{{ __('In Courier') }}</span>
                                    </div>
                                    <span id="onlineStatusInCourier" class="px-1 sm:px-1.5 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">
                                        {{ number_format($statusInCourierCount) }}
                                    </span>
                                </a>

                                <a href="{{ route('invoice.online.sale', ['status' => 'delivered']) }}" class="flex items-center justify-between p-1 sm:p-2 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-emerald-400 transition-all">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-emerald-500 text-white flex items-center justify-center text-[8px] sm:text-[9px] font-bold shadow-sm shrink-0">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <span class="text-[9.5px] sm:text-[11px] font-extrabold text-slate-700 dark:text-slate-200 truncate">{{ __('Delivered') }}</span>
                                    </div>
                                    <span id="onlineStatusDelivered" class="px-1 sm:px-1.5 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">
                                        {{ number_format($statusDeliveredCount) }}
                                    </span>
                                </a>

                                <a href="{{ route('invoice.online.sale', ['status' => 'cancelled']) }}" class="flex items-center justify-between p-1 sm:p-2 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg sm:rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-red-400 transition-all">
                                    <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 rounded-md bg-red-500 text-white flex items-center justify-center text-[8px] sm:text-[9px] font-bold shadow-sm shrink-0">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                        <span class="text-[9.5px] sm:text-[11px] font-extrabold text-slate-700 dark:text-slate-200 truncate">{{ __('Cancelled') }}</span>
                                    </div>
                                    <span id="onlineStatusCancelled" class="px-1 sm:px-1.5 py-0.5 bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">
                                        {{ number_format($statusCancelledCount) }}
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Platform Breakdown Badges Footer -->
                        <div id="onlinePlatformBadgesContainer" class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700 w-full text-[11px] sm:text-xs font-bold">
                            @foreach($onlinePlatformLabels as $idx => $label)
                                <div class="flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ ['#8b5cf6', '#3b82f6', '#10b981', '#a855f7', '#f59e0b', '#ec4899', '#06b6d4', '#64748b'][$idx % 8] }};"></span>
                                    <span class="text-slate-700 dark:text-slate-300 font-extrabold">{{ $label }}:</span>
                                    <span class="text-purple-600 font-extrabold">{{ number_format($onlinePlatformData[$idx] ?? 0) }} {{ __('Orders') }}</span>
                                    <span class="text-emerald-600 font-extrabold">({{ number_format($onlinePlatformQty[$idx] ?? 0) }} Pcs)</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pane 2: Website Orders -->
                    <div id="paneWebOrders" class="channel-pane p-3 sm:p-4 flex-1 flex flex-col justify-between bg-slate-50/30 dark:bg-slate-900/40" style="display: none;">
                        <!-- Sub-tabs: Web Orders vs Incomplete Orders -->
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-4">
                                <button type="button" class="web-order-subtab active" data-type="web_orders">
                                    {{ __('Website Orders') }}
                                </button>
                                <button type="button" class="web-order-subtab" data-type="incomplete_orders">
                                    {{ __('Incomplete Orders') }}
                                </button>
                            </div>
                            <a href="{{ route('website-orders.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                <span>{{ __('Open Manager') }}</span> <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- 3-Column Layout: Left Badges | Center Donut Chart | Right Badges -->
                        @php
                            $webBreakdown = $website_orders_chart['breakdown'] ?? ($web_order_report_chart['breakdown'] ?? []);
                            $webMid = ceil(count($webBreakdown) / 2);
                            $webLeftItems = array_slice($webBreakdown, 0, $webMid);
                            $webRightItems = array_slice($webBreakdown, $webMid);
                        @endphp
                        <div class="flex flex-row items-center justify-between gap-1.5 sm:gap-3 w-full my-auto py-2">
                            <!-- Left Side Status Badges -->
                            <div class="flex flex-col gap-1.5 sm:gap-2 w-[98px] xs:w-[115px] sm:w-[135px] xl:w-[145px] shrink-0" id="webOrdersLegendLeft">
                                @foreach($webLeftItems as $bItem)
                                    <a href="{{ route('website-orders.index', ['status' => $bItem['label']]) }}" class="flex items-center justify-between p-1 sm:p-1.5 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-blue-400 transition-all">
                                        <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                            <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full shrink-0" style="background-color: {{ $bItem['color'] }};"></span>
                                            <span class="text-[9.5px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate" title="{{ $bItem['label'] }}">{{ $bItem['label'] }}</span>
                                        </div>
                                        <span class="px-1 sm:px-1.5 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">{{ $bItem['count'] }}</span>
                                    </a>
                                @endforeach
                            </div>

                            <!-- Center: Donut Chart -->
                            <div class="flex-1 flex flex-col items-center justify-center min-w-0 px-1">
                                <div style="position: relative; width: 100%; max-width: 165px; height: 160px; min-height: 110px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="webOrdersChartCanvas"></canvas>
                                </div>
                                <div class="mt-2 text-center">
                                    <span class="text-[10px] sm:text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('TOTAL ORDERS:') }}</span>
                                    <span id="webOrdersTotalCount" class="text-lg sm:text-xl font-black text-slate-800 dark:text-white mt-0.5 block">
                                        {{ number_format($website_orders_chart['total_orders'] ?? ($web_order_report_chart['total_orders'] ?? 0)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Right Side Status Badges -->
                            <div class="flex flex-col gap-1.5 sm:gap-2 w-[98px] xs:w-[115px] sm:w-[135px] xl:w-[145px] shrink-0" id="webOrdersLegendRight">
                                @foreach($webRightItems as $bItem)
                                    <a href="{{ route('website-orders.index', ['status' => $bItem['label']]) }}" class="flex items-center justify-between p-1 sm:p-1.5 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-blue-400 transition-all">
                                        <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                            <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full shrink-0" style="background-color: {{ $bItem['color'] }};"></span>
                                            <span class="text-[9.5px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate" title="{{ $bItem['label'] }}">{{ $bItem['label'] }}</span>
                                        </div>
                                        <span class="px-1 sm:px-1.5 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">{{ $bItem['count'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Pane 3: WooCommerce Orders -->
                    <div id="paneWooOrders" class="channel-pane p-3 sm:p-4 flex-1 flex flex-col justify-between bg-slate-50/30 dark:bg-slate-900/40" style="display: none;">
                        <!-- Sub-tabs: Woo Orders vs Incomplete Orders -->
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-4">
                                <button type="button" class="woo-order-subtab active" data-type="web_orders">
                                    {{ __('WooCommerce Orders') }}
                                </button>
                                <button type="button" class="woo-order-subtab" data-type="incomplete_orders">
                                    {{ __('Incomplete Orders') }}
                                </button>
                            </div>
                            <a href="{{ route('woocommerce.orders.list') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1">
                                <span>{{ __('Open Manager') }}</span> <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>

                        <!-- 3-Column Layout: Left Badges | Center Donut Chart | Right Badges -->
                        @php
                            $wooBreakdown = $woocommerce_orders_chart['breakdown'] ?? [];
                            $wooMid = ceil(count($wooBreakdown) / 2);
                            $wooLeftItems = array_slice($wooBreakdown, 0, $wooMid);
                            $wooRightItems = array_slice($wooBreakdown, $wooMid);
                        @endphp
                        <div class="flex flex-row items-center justify-between gap-1.5 sm:gap-3 w-full my-auto py-2">
                            <!-- Left Side Status Badges -->
                            <div class="flex flex-col gap-1.5 sm:gap-2 w-[98px] xs:w-[115px] sm:w-[135px] xl:w-[145px] shrink-0" id="wooOrdersLegendLeft">
                                @foreach($wooLeftItems as $bItem)
                                    <a href="{{ route('woocommerce.orders.list', ['WooCommerceStatus' => $bItem['label']]) }}" class="flex items-center justify-between p-1 sm:p-1.5 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-purple-400 transition-all">
                                        <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                            <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full shrink-0" style="background-color: {{ $bItem['color'] }};"></span>
                                            <span class="text-[9.5px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate" title="{{ $bItem['label'] }}">{{ $bItem['label'] }}</span>
                                        </div>
                                        <span class="px-1 sm:px-1.5 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">{{ $bItem['count'] }}</span>
                                    </a>
                                @endforeach
                            </div>

                            <!-- Center: Donut Chart -->
                            <div class="flex-1 flex flex-col items-center justify-center min-w-0 px-1">
                                <div style="position: relative; width: 100%; max-width: 165px; height: 160px; min-height: 110px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="wooOrdersChartCanvas"></canvas>
                                </div>
                                <div class="mt-2 text-center">
                                    <span class="text-[10px] sm:text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('TOTAL ORDERS:') }}</span>
                                    <span id="wooOrdersTotalCount" class="text-lg sm:text-xl font-black text-slate-800 dark:text-white mt-0.5 block">
                                        {{ number_format($woocommerce_orders_chart['total_orders'] ?? 0) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Right Side Status Badges -->
                            <div class="flex flex-col gap-1.5 sm:gap-2 w-[98px] xs:w-[115px] sm:w-[135px] xl:w-[145px] shrink-0" id="wooOrdersLegendRight">
                                @foreach($wooRightItems as $bItem)
                                    <a href="{{ route('woocommerce.orders.list', ['WooCommerceStatus' => $bItem['label']]) }}" class="flex items-center justify-between p-1 sm:p-1.5 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-purple-400 transition-all">
                                        <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                            <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full shrink-0" style="background-color: {{ $bItem['color'] }};"></span>
                                            <span class="text-[9.5px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate" title="{{ $bItem['label'] }}">{{ $bItem['label'] }}</span>
                                        </div>
                                        <span class="px-1 sm:px-1.5 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">{{ $bItem['count'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Pane 3: Orders by Source -->
                    <div id="paneSource" class="channel-pane p-3 sm:p-4 flex-1 flex flex-col justify-between bg-slate-50/30 dark:bg-slate-900/40" style="display: none;">
                        <!-- Container 1: Donut Chart Mode -->
                        <div id="sourceChartContainer" class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 my-auto py-2">
                            <!-- Left: Donut Chart -->
                            <div class="w-full sm:w-1/2 flex flex-col items-center justify-center">
                                <div style="position: relative; width: 100%; max-width: 180px; height: 170px; min-height: 130px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="ordersBySourceChartCanvas"></canvas>
                                </div>
                                <div class="mt-2 text-center">
                                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                        {{ __('Total Orders') }}: <strong id="sourceTotalOrders" class="text-slate-800 dark:text-white">{{ number_format($orders_by_source_chart['total_orders'] ?? 0) }}</strong>
                                    </div>
                                    <div class="text-xs font-bold text-slate-500 mt-0.5">
                                        {{ __('Total Value') }}: <span class="text-pink-500 font-extrabold">৳<span id="sourceTotalValue">{{ $orders_by_source_chart['total_value_formatted'] ?? '0.00' }}</span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Source Items List -->
                            <div class="w-full sm:w-1/2 flex flex-col gap-1.5 sm:gap-2" id="ordersBySourceLegend">
                                @if(isset($orders_by_source_chart['table_rows']))
                                    @foreach($orders_by_source_chart['table_rows'] as $sItem)
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $sItem['color'] }};"></span>
                                                <span class="font-extrabold text-xs text-slate-700 dark:text-slate-200">{{ $sItem['name'] }}</span>
                                                <span class="text-slate-400 text-xs">({{ $sItem['count'] }})</span>
                                            </div>
                                            <div class="text-xs font-black text-pink-600 dark:text-pink-400">
                                                ৳{{ $sItem['value_formatted'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Container 2: Table Mode -->
                        <div id="sourceTableContainer" class="my-auto py-2" style="display: none;">
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-white dark:bg-slate-800 text-slate-500 font-extrabold border-b border-slate-200 dark:border-slate-700">
                                        <tr>
                                            <th class="p-2.5">{{ __('Source') }}</th>
                                            <th class="p-2.5 text-center">{{ __('Orders') }}</th>
                                            <th class="p-2.5 text-center">{{ __('Qty') }}</th>
                                            <th class="p-2.5 text-right">{{ __('Total Amount') }}</th>
                                            <th class="p-2.5 text-right">{{ __('Share') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sourceTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-800/60">
                                        @if(isset($orders_by_source_chart['table_rows']))
                                            @foreach($orders_by_source_chart['table_rows'] as $tItem)
                                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                                    <td class="p-2.5 font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $tItem['color'] }};"></span>
                                                        {{ $tItem['name'] }}
                                                    </td>
                                                    <td class="p-2.5 text-center font-bold">${{ $tItem['count'] }}</td>
                                                    <td class="p-2.5 text-center text-slate-500">{{ (int)$tItem['qty'] }} pcs</td>
                                                    <td class="p-2.5 text-right font-black text-emerald-600 dark:text-emerald-400">৳{{ $tItem['value_formatted'] }}</td>
                                                    <td class="p-2.5 text-right font-bold text-slate-500">{{ $tItem['share'] }}%</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Modern Analytics Graphs Section (Theme Matched) -->
        <div class="my-6">
            <!-- 1. Order Counts Graph -->
            <div class="dash-analytics-card">
                <div class="d-flex justify-content-between align-items-center pb-3 flex-wrap gap-3">
                    <div>
                        <h3 class="m-0 font-extrabold text-slate-800 dark:text-white" style="font-size: 18px; letter-spacing: -0.3px;">
                            <i class="fas fa-chart-simple mr-2 text-purple-600 dark:text-purple-400"></i>{{ __('Order Counts') }}
                        </h3>
                        <p class="m-0 text-slate-500 dark:text-slate-400" style="font-size: 12.5px; margin-top: 2px;">
                            {{ __('Orders created vs sent to courier in the selected range') }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <select id="orderCountsRangeSelect" class="graph-ctrl-select">
                            <option value="Last-30-days" selected>{{ __('Last 30 days') }}</option>
                            <option value="Last-7-days">{{ __('Last 7 days') }}</option>
                            <option value="This-month">{{ __('This month') }}</option>
                            <option value="Last-month">{{ __('Last month') }}</option>
                            <option value="This-year">{{ __('This year') }}</option>
                            <option value="Lifetime">{{ __('Lifetime') }}</option>
                        </select>

                        <div class="d-flex align-items-center gap-2 sm:gap-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/80 rounded-xl px-2.5 sm:px-3.5 py-1.5 sm:py-2">
                            <div class="d-flex flex-column">
                                <span class="text-purple-600 dark:text-purple-400 font-bold" style="font-size: 11px; display: flex; align-items: center; gap: 4px;">
                                    <span class="inline-block w-2 h-2 rounded-sm bg-purple-600 dark:purple-400"></span> {{ __('Created') }}
                                </span>
                                <span id="orderCountsCreatedTotal" class="text-slate-800 dark:text-white" style="font-size: 18px; sm:font-size: 20px; font-weight: 900; line-height: 1.1;">
                                    {{ number_format($order_counts_chart['total_created'] ?? 0) }}
                                </span>
                            </div>
                            <div class="w-px h-7 bg-slate-200 dark:bg-slate-700"></div>
                            <div class="d-flex flex-column">
                                <span class="text-sky-600 dark:text-sky-400 font-bold" style="font-size: 11px; display: flex; align-items: center; gap: 4px;">
                                    <span class="inline-block w-2 h-2 rounded-sm bg-sky-600 dark:bg-sky-400"></span> {{ __('Sent to Courier') }}
                                </span>
                                <span id="orderCountsCourierTotal" class="text-slate-800 dark:text-white" style="font-size: 18px; sm:font-size: 20px; font-weight: 900; line-height: 1.1;">
                                    {{ number_format($order_counts_chart['total_courier'] ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dash-chart-container" style="position: relative; height: 260px; width: 100%; margin-top: 10px;">
                    <canvas id="orderCountsChartCanvas"></canvas>
                </div>
            </div>

            <!-- Row for 2. Earning Reports & 3. Hourly Web Orders -->
            <div class="dash-card-row">
                <!-- 2. Earning Reports Graph -->
                <div class="dash-col-7">
                    <div class="dash-analytics-card h-100 mb-0 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center pb-3 flex-wrap gap-2">
                                <div>
                                    <h3 class="m-0 font-extrabold text-slate-800 dark:text-white" style="font-size: 18px; letter-spacing: -0.3px;">
                                        <i class="fas fa-chart-column mr-2 text-pink-500"></i>{{ __('Earning Reports') }}
                                    </h3>
                                    <p id="earningReportsSubtitle" class="m-0 text-slate-500 dark:text-slate-400" style="font-size: 12px; margin-top: 2px;">
                                        {{ __('Orders overview, month by month') }} &middot; <span id="earningReportsRangeLabel">{{ $earning_reports_chart['range_label'] ?? '' }}</span>
                                    </p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <select id="earningStatusSelect" class="graph-ctrl-select">
                                        <option value="all" selected>{{ __('All Status') }}</option>
                                        <option value="approved">{{ __('Approved / RTS') }}</option>
                                        <option value="delivered">{{ __('Delivered') }}</option>
                                        <option value="processing">{{ __('Processing') }}</option>
                                    </select>
                                    <select id="earningPeriodSelect" class="graph-ctrl-select">
                                        <option value="year" selected>📅 {{ __('Year') }}</option>
                                        <option value="6months">📅 {{ __('Last 6 Months') }}</option>
                                        <option value="12months_full">📅 {{ __('Last 12 Months') }}</option>
                                        <option value="3months">📅 {{ __('Last 3 Months') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Interactive Metric Tiles Row -->
                            <div class="d-flex align-items-center gap-2 my-3 flex-wrap">
                                <div class="earning-tile-btn active" data-metric="orders">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>{{ __('Orders') }}</span>
                                </div>
                                <div class="earning-tile-btn" data-metric="revenue">
                                    <i class="fas fa-chart-line"></i>
                                    <span>{{ __('Revenue') }}</span>
                                </div>
                                <div class="earning-tile-btn" data-metric="profit">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>{{ __('Profit') }}</span>
                                </div>
                                <div class="earning-tile-btn" data-metric="expense">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>{{ __('Ad Spend') }}</span>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-semibold my-2">
                                {{ __('Click a tile to chart it; click another to compare them side by side.') }}
                            </p>

                            <!-- Big Highlighted Metric -->
                            <div class="mb-3">
                                <div id="earningBigMetricValue" style="font-size: 28px; font-weight: 900; color: #ec4899; line-height: 1.1;">
                                    {{ number_format($earning_reports_chart['total_orders'] ?? 0) }}
                                </div>
                                <div id="earningBigMetricLabel" class="text-slate-500 dark:text-slate-400 font-semibold" style="font-size: 12px; margin-top: 2px;">
                                    {{ __('Total orders for the selected range') }}
                                </div>
                            </div>
                        </div>

                        <div class="dash-chart-container" style="position: relative; height: 260px; width: 100%;">
                            <canvas id="earningReportsChartCanvas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 3. Hourly Web Orders Graph -->
                <div class="dash-col-5">
                    <div class="dash-analytics-card h-100 mb-0 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center pb-3 flex-wrap gap-2">
                                <div>
                                    <h3 class="m-0 font-extrabold text-slate-800 dark:text-white" style="font-size: 18px; letter-spacing: -0.3px;">
                                        <i class="fas fa-clock-rotate-left mr-2 text-purple-600 dark:text-purple-400"></i>{{ __('Hourly Web Orders') }}
                                    </h3>
                                    <p id="hourlyOrdersSubtitle" class="m-0 text-slate-500 dark:text-slate-400" style="font-size: 12px; margin-top: 2px;">
                                        {{ $hourly_orders_chart['subtitle'] ?? '' }}
                                    </p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <select id="hourlyChannelSelect" class="graph-ctrl-select">
                                        <option value="web" selected>{{ __('Web Orders') }}</option>
                                        <option value="manual">{{ __('POS Orders') }}</option>
                                        <option value="all">{{ __('All Orders') }}</option>
                                    </select>
                                    <div class="hourly-pill-toggle">
                                        <button type="button" class="hourly-pill-btn active" id="btnHourlyToday" data-series="today">
                                            {{ __('Today') }}
                                        </button>
                                        <button type="button" class="hourly-pill-btn" id="btnHourlyYesterday" data-series="yesterday">
                                            {{ __('Yesterday') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-4 my-2" style="font-size: 11.5px; font-weight: 700;">
                                <div class="d-flex align-items-center gap-1.5 text-purple-600 dark:text-purple-400">
                                    <span style="display: inline-block; width: 14px; height: 3px; background: #9333ea; border-radius: 2px;"></span>
                                    <span>{{ __('Today') }}: <strong id="hourlyTodayTotal" class="text-slate-800 dark:text-white">{{ $hourly_orders_chart['total_today'] ?? 0 }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 text-amber-500">
                                    <span style="display: inline-block; width: 14px; height: 2px; border-top: 2px dashed #f59e0b;"></span>
                                    <span>{{ __('Yesterday') }}: <strong id="hourlyYesterdayTotal" class="text-slate-800 dark:text-white">{{ $hourly_orders_chart['total_yesterday'] ?? 0 }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="dash-chart-container" style="position: relative; height: 260px; width: 100%; margin-top: 10px;">
                            <canvas id="hourlyOrdersChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actionable Insights Grid (Bento Style) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10 animate-slide-up delay-400">            
        </div>
    </div>
    </div>
    </div>  </div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================
            // RIGHT CARD CHANNEL TABS (Platforms / Web Orders / WooCommerce Orders / Source)
            // ==========================================
            $('.channel-tab-btn').on('click', function() {
                $('.channel-tab-btn').removeClass('active');
                $(this).addClass('active');

                const tab = $(this).data('tab');
                $('.channel-pane').hide();
                $('#controlsTabPlatforms, #controlsTabWebOrders, #controlsTabWooOrders, #controlsTabSource').hide();

                if (tab === 'platforms') {
                    $('#panePlatforms').show();
                    $('#controlsTabPlatforms').show();
                    setTimeout(function() {
                        renderOnlinePlatformsChart();
                    }, 30);
                } else if (tab === 'web_orders') {
                    $('#paneWebOrders').show();
                    $('#controlsTabWebOrders').show();
                    setTimeout(function() {
                        renderWebOrdersChart();
                    }, 30);
                } else if (tab === 'woo_orders') {
                    $('#paneWooOrders').show();
                    $('#controlsTabWooOrders').show();
                    setTimeout(function() {
                        renderWooOrdersChart();
                    }, 30);
                } else if (tab === 'source') {
                    $('#paneSource').show();
                    $('#controlsTabSource').show();
                    setTimeout(function() {
                        renderOrdersBySourceChart();
                    }, 30);
                }
            });

            $('#btnGoToWebOrdersTab').on('click', function() {
                $('.channel-tab-btn[data-tab="web_orders"]').click();
            });

            $('#btnGoToWooOrdersTab, #btnGoToWebEcomTab').on('click', function() {
                $('.channel-tab-btn[data-tab="woo_orders"]').click();
            });

            // ==========================================
            // 1. MANUAL POS SALES DONUT CHART
            // ==========================================
            const manualElem = document.getElementById('manualPosChart');
            let manualChartInstance = null;
            let currentManualData = {!! json_encode($manual_sales_chart ?? []) !!};

            if (manualElem) {
                manualChartInstance = new Chart(manualElem.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: currentManualData.labels || {!! json_encode($manualLabels ?? []) !!},
                        datasets: [{
                            data: currentManualData.data || {!! json_encode($manualData ?? []) !!},
                            backgroundColor: currentManualData.colors || ['#10b981', '#f59e0b', '#f43f5e'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            function reloadManualSalesChart(range) {
                $.ajax({
                    url: "{{ route('dashboard.chart.manual-sales') }}",
                    type: "GET",
                    data: { filter: range },
                    success: function(res) {
                        currentManualData = res;
                        $('#manualOrdersBadgeCount').text(Number(res.total_orders || 0).toLocaleString());
                        $('#manualOrdersBadgeQty').text(Number(res.total_qty || 0).toLocaleString());
                        $('#manualLegendPaid').text(Number(res.paid || 0).toLocaleString());
                        $('#manualLegendPartial').text(Number(res.partial || 0).toLocaleString());
                        $('#manualLegendDue').text(Number(res.due || 0).toLocaleString());

                        if (manualChartInstance) {
                            manualChartInstance.data.labels = res.labels || [];
                            manualChartInstance.data.datasets[0].data = res.data || [];
                            manualChartInstance.data.datasets[0].backgroundColor = res.colors || ['#10b981', '#f59e0b', '#f43f5e'];
                            manualChartInstance.update();
                        }
                    },
                    error: function(xhr) {
                        console.error('Manual sales chart filter error:', xhr);
                    }
                });
            }

            $('#manualSalesRangeSelect').on('change', function() {
                reloadManualSalesChart($(this).val());
            });

            // ==========================================
            // 2. ONLINE PLATFORM BREAKDOWN DONUT CHART
            // ==========================================
            let platformChartInstance = null;
            let currentPlatformData = {!! json_encode($online_platforms_chart ?? []) !!};

            function renderOnlinePlatformsChart() {
                const platformElem = document.getElementById('onlinePlatformChart');
                if (!platformElem) return;

                if (platformChartInstance) {
                    platformChartInstance.destroy();
                    platformChartInstance = null;
                }

                const hasData = currentPlatformData.data && currentPlatformData.data.some(v => v > 0);
                const chartLabels = hasData ? (currentPlatformData.labels || {!! json_encode($onlinePlatformLabels ?? []) !!}) : ["No Orders"];
                const chartData = hasData ? (currentPlatformData.data || {!! json_encode($onlinePlatformData ?? []) !!}) : [1];
                const chartColors = hasData ? (currentPlatformData.colors || ['#8b5cf6', '#3b82f6', '#10b981', '#a855f7', '#f59e0b', '#ec4899', '#06b6d4', '#64748b']) : ['#334155'];

                platformChartInstance = new Chart(platformElem.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: hasData,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            renderOnlinePlatformsChart();

            function reloadOnlinePlatformsChart(range) {
                $.ajax({
                    url: "{{ route('dashboard.chart.online-platforms') }}",
                    type: "GET",
                    data: { filter: range },
                    success: function(res) {
                        currentPlatformData = res;
                        $('#onlineBadgeOrdersCount').text(Number(res.total_orders || 0).toLocaleString());
                        $('#onlineBadgeTotalQty').text(Number(res.total_qty || 0).toLocaleString());
                        $('#onlineBadgeWooCount').text(Number(res.woo_orders || 0).toLocaleString());
                        $('#onlineBadgeWebCount').text(Number(res.web_orders || 0).toLocaleString());

                        if (res.status_counts) {
                            $('#onlineStatusProcessing').text(Number(res.status_counts.processing || 0).toLocaleString());
                            $('#onlineStatusRts').text(Number(res.status_counts.rts || 0).toLocaleString());
                            $('#onlineStatusOnHold').text(Number(res.status_counts.on_hold || 0).toLocaleString());
                            $('#onlineStatusInCourier').text(Number(res.status_counts.in_courier || 0).toLocaleString());
                            $('#onlineStatusDelivered').text(Number(res.status_counts.delivered || 0).toLocaleString());
                            $('#onlineStatusCancelled').text(Number(res.status_counts.cancelled || 0).toLocaleString());
                        }

                        if (res.platform_badges) {
                            let badgesHtml = '';
                            res.platform_badges.forEach(function(b) {
                                badgesHtml += `
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: ${b.color};"></span>
                                        <span class="text-slate-700 dark:text-slate-300 font-extrabold">${b.label}:</span>
                                        <span class="text-purple-600 font-extrabold">${Number(b.count).toLocaleString()} {{ __('Orders') }}</span>
                                        <span class="text-emerald-600 font-extrabold">(${Number(b.qty).toLocaleString()} Pcs)</span>
                                    </div>
                                `;
                            });
                            $('#onlinePlatformBadgesContainer').html(badgesHtml);
                        }

                        renderOnlinePlatformsChart();
                    },
                    error: function(xhr) {
                        console.error('Online platforms chart filter error:', xhr);
                    }
                });
            }

            $('#onlinePlatformsRangeSelect').on('change', function() {
                reloadOnlinePlatformsChart($(this).val());
            });

            // ==========================================
            // 3. WEBSITE ORDERS REPORT DONUT CHART
            // ==========================================
            let webOrdersChartInstance = null;
            let currentWebOrdersData = {!! json_encode($website_orders_chart ?? ($web_order_report_chart ?? [])) !!};
            let currentWebOrdersType = 'web_orders';

            function renderWebOrdersChart() {
                const webOrderElem = document.getElementById('webOrdersChartCanvas');
                if (!webOrderElem) return;

                if (webOrdersChartInstance) {
                    webOrdersChartInstance.destroy();
                    webOrdersChartInstance = null;
                }

                const hasData = currentWebOrdersData.data && currentWebOrdersData.data.some(v => v > 0);
                const chartLabels = hasData ? (currentWebOrdersData.labels || []) : ["No Orders"];
                const chartData = hasData ? currentWebOrdersData.data : [1];
                const chartColors = hasData ? (currentWebOrdersData.colors || ['#ec4899', '#3b82f6', '#f97316', '#6366f1', '#84cc16', '#a855f7', '#10b981']) : ['#334155'];

                webOrdersChartInstance = new Chart(webOrderElem.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: hasData,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            function reloadWebOrdersChart(type, range) {
                $.ajax({
                    url: "{{ route('dashboard.chart.web-order-report') }}",
                    type: "GET",
                    data: { type: type, filter: range, source: 'website' },
                    success: function(res) {
                        currentWebOrdersData = res;
                        $('#webOrdersTotalCount').text(Number(res.total_orders || 0).toLocaleString());

                        if (res.breakdown) {
                            let mid = Math.ceil(res.breakdown.length / 2);
                            let leftItems = res.breakdown.slice(0, mid);
                            let rightItems = res.breakdown.slice(mid);

                            let renderBadges = function(items) {
                                let html = '';
                                items.forEach(function(item) {
                                    const encodedStatus = encodeURIComponent(item.label);
                                    html += `
                                        <a href="{{ route('website-orders.index') }}?status=${encodedStatus}" class="flex items-center justify-between p-1 sm:p-1.5 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-blue-400 transition-all">
                                            <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                                <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full shrink-0" style="background-color: ${item.color};"></span>
                                                <span class="text-[9.5px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate" title="${item.label}">${item.label}</span>
                                            </div>
                                            <span class="px-1 sm:px-1.5 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">${item.count}</span>
                                        </a>
                                    `;
                                });
                                return html;
                            };

                            $('#webOrdersLegendLeft').html(renderBadges(leftItems));
                            $('#webOrdersLegendRight').html(renderBadges(rightItems));
                        }

                        renderWebOrdersChart();
                    },
                    error: function(xhr) {
                        console.error('Website order report chart filter error:', xhr);
                    }
                });
            }

            $('.web-order-subtab').on('click', function() {
                $('.web-order-subtab').removeClass('active');
                $(this).addClass('active');
                currentWebOrdersType = $(this).data('type');
                reloadWebOrdersChart(currentWebOrdersType, $('#webOrdersRangeSelect').val());
            });

            $('#webOrdersRangeSelect').on('change', function() {
                reloadWebOrdersChart(currentWebOrdersType, $(this).val());
            });

            // ==========================================
            // 4. WOOCOMMERCE ORDERS REPORT DONUT CHART
            // ==========================================
            let wooOrdersChartInstance = null;
            let currentWooOrdersData = {!! json_encode($woocommerce_orders_chart ?? []) !!};
            let currentWooOrdersType = 'web_orders';

            function renderWooOrdersChart() {
                const wooOrderElem = document.getElementById('wooOrdersChartCanvas');
                if (!wooOrderElem) return;

                if (wooOrdersChartInstance) {
                    wooOrdersChartInstance.destroy();
                    wooOrdersChartInstance = null;
                }

                const hasData = currentWooOrdersData.data && currentWooOrdersData.data.some(v => v > 0);
                const chartLabels = hasData ? (currentWooOrdersData.labels || []) : ["No Orders"];
                const chartData = hasData ? currentWooOrdersData.data : [1];
                const chartColors = hasData ? (currentWooOrdersData.colors || ['#ec4899', '#3b82f6', '#f97316', '#6366f1', '#84cc16', '#a855f7', '#10b981']) : ['#334155'];

                wooOrdersChartInstance = new Chart(wooOrderElem.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: hasData,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            function reloadWooOrdersChart(type, range) {
                $.ajax({
                    url: "{{ route('dashboard.chart.web-order-report') }}",
                    type: "GET",
                    data: { type: type, filter: range, source: 'woocommerce' },
                    success: function(res) {
                        currentWooOrdersData = res;
                        $('#wooOrdersTotalCount').text(Number(res.total_orders || 0).toLocaleString());

                        if (res.breakdown) {
                            let mid = Math.ceil(res.breakdown.length / 2);
                            let leftItems = res.breakdown.slice(0, mid);
                            let rightItems = res.breakdown.slice(mid);

                            let renderBadges = function(items) {
                                let html = '';
                                items.forEach(function(item) {
                                    const encodedStatus = encodeURIComponent(item.label);
                                    html += `
                                        <a href="{{ route('woocommerce.orders.list') }}?WooCommerceStatus=${encodedStatus}" class="flex items-center justify-between p-1 sm:p-1.5 px-1.5 sm:px-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200/80 dark:border-slate-700 shadow-sm hover:border-purple-400 transition-all">
                                            <div class="flex items-center gap-1 sm:gap-1.5 min-w-0">
                                                <span class="w-1.5 sm:w-2 h-1.5 sm:h-2 rounded-full shrink-0" style="background-color: ${item.color};"></span>
                                                <span class="text-[9.5px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate" title="${item.label}">${item.label}</span>
                                            </div>
                                            <span class="px-1 sm:px-1.5 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-[9.5px] sm:text-[11px] font-black rounded shrink-0">${item.count}</span>
                                        </a>
                                    `;
                                });
                                return html;
                            };

                            $('#wooOrdersLegendLeft').html(renderBadges(leftItems));
                            $('#wooOrdersLegendRight').html(renderBadges(rightItems));
                        }

                        renderWooOrdersChart();
                    },
                    error: function(xhr) {
                        console.error('WooCommerce order report chart filter error:', xhr);
                    }
                });
            }

            $('.woo-order-subtab').on('click', function() {
                $('.woo-order-subtab').removeClass('active');
                $(this).addClass('active');
                currentWooOrdersType = $(this).data('type');
                reloadWooOrdersChart(currentWooOrdersType, $('#wooOrdersRangeSelect').val());
            });

            $('#wooOrdersRangeSelect').on('change', function() {
                reloadWooOrdersChart(currentWooOrdersType, $(this).val());
            });

            // ==========================================
            // 4. ORDERS BY SOURCE CHART & TABLE (E-Com)
            // ==========================================
            let ordersBySourceChartInstance = null;
            let currentOrdersBySourceData = {!! json_encode($orders_by_source_chart ?? []) !!};

            function renderOrdersBySourceChart() {
                const ordersBySourceElem = document.getElementById('ordersBySourceChartCanvas');
                if (!ordersBySourceElem) return;

                if (ordersBySourceChartInstance) {
                    ordersBySourceChartInstance.destroy();
                    ordersBySourceChartInstance = null;
                }

                const hasData = currentOrdersBySourceData.counts && currentOrdersBySourceData.counts.some(v => v > 0);
                const chartLabels = hasData ? (currentOrdersBySourceData.labels || []) : ["No Orders"];
                const chartCounts = hasData ? currentOrdersBySourceData.counts : [1];
                const chartColors = hasData ? (currentOrdersBySourceData.colors || ['#ec4899', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#06b6d4']) : ['#334155'];

                ordersBySourceChartInstance = new Chart(ordersBySourceElem.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartCounts,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: hasData,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let count = context.raw || 0;
                                        let val = (currentOrdersBySourceData.values && currentOrdersBySourceData.values[context.dataIndex]) ? currentOrdersBySourceData.values[context.dataIndex] : 0;
                                        return `${label}: ${count} Orders (৳${Number(val).toLocaleString()})`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            function reloadOrdersBySourceChart(status, range) {
                $.ajax({
                    url: "{{ route('dashboard.chart.orders-by-source') }}",
                    type: "GET",
                    data: { status: status, filter: range },
                    success: function(res) {
                        currentOrdersBySourceData = res;
                        $('#sourceTotalOrders').text(Number(res.total_orders || 0).toLocaleString());
                        $('#sourceTotalValue').text(res.total_value_formatted || '0.00');

                        let legendHtml = '';
                        let tableRowsHtml = '';

                        if (res.table_rows) {
                            res.table_rows.forEach(function(row) {
                                legendHtml += `
                                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: ${row.color};"></span>
                                            <span class="font-extrabold text-xs text-slate-700 dark:text-slate-200">${row.name}</span>
                                            <span class="text-slate-400 text-xs">(${row.count})</span>
                                        </div>
                                        <div class="text-xs font-black text-pink-600 dark:text-pink-400">
                                            ৳${row.value_formatted}
                                        </div>
                                    </div>
                                `;

                                tableRowsHtml += `
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                        <td class="p-2.5 font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full" style="background-color: ${row.color};"></span>
                                            ${row.name}
                                        </td>
                                        <td class="p-2.5 text-center font-bold">${row.count}</td>
                                        <td class="p-2.5 text-center text-slate-500">${parseInt(row.qty || 0)} pcs</td>
                                        <td class="p-2.5 text-right font-black text-emerald-600 dark:text-emerald-400">৳${row.value_formatted}</td>
                                        <td class="p-2.5 text-right font-bold text-slate-500">${row.share}%</td>
                                    </tr>
                                `;
                            });
                        }

                        $('#ordersBySourceLegend').html(legendHtml);
                        $('#sourceTableBody').html(tableRowsHtml);

                        renderOrdersBySourceChart();
                    },
                    error: function(xhr) {
                        console.error('Orders by source chart filter error:', xhr);
                    }
                });
            }

            $('.source-view-toggle-btn').on('click', function() {
                $('.source-view-toggle-btn').removeClass('active');
                $(this).addClass('active');

                const mode = $(this).data('mode');
                if (mode === 'chart') {
                    $('#sourceChartContainer').show();
                    $('#sourceTableContainer').hide();
                    setTimeout(function() {
                        renderOrdersBySourceChart();
                    }, 30);
                } else if (mode === 'table') {
                    $('#sourceChartContainer').hide();
                    $('#sourceTableContainer').show();
                }
            });

            $('#ordersBySourceStatusSelect, #ordersBySourceRangeSelect').on('change', function() {
                const status = $('#ordersBySourceStatusSelect').val();
                const range = $('#ordersBySourceRangeSelect').val();
                reloadOrdersBySourceChart(status, range);
            });

            // ==========================================
            // 3 NEW ANALYTICS GRAPHS (Theme Matched)
            // ==========================================

            const chartGridColor = 'rgba(148, 163, 184, 0.15)';
            const chartTooltipConfig = {
                backgroundColor: '#0f172a',
                titleColor: '#ffffff',
                bodyColor: '#cbd5e1',
                borderColor: '#334155',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 8
            };

            // 1. Order Counts Chart
            const orderCountsCanvas = document.getElementById('orderCountsChartCanvas');
            let orderCountsChartInstance = null;
            let currentOrderCountsData = {!! json_encode($order_counts_chart ?? []) !!};

            if (orderCountsCanvas && currentOrderCountsData) {
                orderCountsChartInstance = new Chart(orderCountsCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: currentOrderCountsData.labels || [],
                        datasets: [{
                            label: "{{ __('Orders Created') }}",
                            data: currentOrderCountsData.created_data || [],
                            backgroundColor: '#a855f7',
                            hoverBackgroundColor: '#9333ea',
                            borderRadius: 6,
                            barPercentage: 0.65,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...chartTooltipConfig,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        let idx = context.dataIndex;
                                        let created = context.raw || 0;
                                        let courier = (currentOrderCountsData.courier_data && currentOrderCountsData.courier_data[idx] !== undefined) ? currentOrderCountsData.courier_data[idx] : 0;
                                        return [
                                            `Created: ${created}`,
                                            `Sent to Courier: ${courier}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: '#64748b', font: { size: 10.5, weight: 'bold' }, maxRotation: 0 }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: chartGridColor, drawBorder: false },
                                ticks: { color: '#64748b', font: { size: 10 }, precision: 0 }
                            }
                        }
                    }
                });
            }

            $('#orderCountsRangeSelect').on('change', function() {
                const range = $(this).val();
                $.ajax({
                    url: "{{ route('dashboard.chart.order-counts') }}",
                    type: "GET",
                    data: { filter: range },
                    success: function(res) {
                        currentOrderCountsData = res;
                        $('#orderCountsCreatedTotal').text(Number(res.total_created || 0).toLocaleString());
                        $('#orderCountsCourierTotal').text(Number(res.total_courier || 0).toLocaleString());

                        if (orderCountsChartInstance) {
                            orderCountsChartInstance.data.labels = res.labels || [];
                            orderCountsChartInstance.data.datasets[0].data = res.created_data || [];
                            orderCountsChartInstance.update();
                        }
                    },
                    error: function(xhr) {
                        console.error('Order counts chart error:', xhr);
                    }
                });
            });

            // 2. Earning Reports Chart
            const earningCanvas = document.getElementById('earningReportsChartCanvas');
            let earningChartInstance = null;
            let currentEarningData = {!! json_encode($earning_reports_chart ?? []) !!};
            let activeEarningMetric = 'orders';

            function renderEarningChartMetric() {
                if (!earningChartInstance || !currentEarningData) return;

                let datasetData = [];
                let barColor = '#ec4899';
                let hoverColor = '#db2777';
                let metricLabel = "{{ __('Orders') }}";
                let bigVal = '0';
                let bigLabel = "{{ __('Total orders for the selected range') }}";

                if (activeEarningMetric === 'orders') {
                    datasetData = currentEarningData.orders || [];
                    barColor = '#ec4899';
                    hoverColor = '#db2777';
                    metricLabel = "{{ __('Orders') }}";
                    bigVal = Number(currentEarningData.total_orders || 0).toLocaleString();
                    bigLabel = "{{ __('Total orders for the selected range') }}";
                    $('#earningBigMetricValue').css('color', '#ec4899');
                } else if (activeEarningMetric === 'revenue') {
                    datasetData = currentEarningData.revenue || [];
                    barColor = '#3b82f6';
                    hoverColor = '#2563eb';
                    metricLabel = "{{ __('Revenue') }}";
                    bigVal = '{{ $currency }} ' + Number(currentEarningData.total_revenue || 0).toLocaleString();
                    bigLabel = "{{ __('Total revenue for the selected range') }}";
                    $('#earningBigMetricValue').css('color', '#3b82f6');
                } else if (activeEarningMetric === 'profit') {
                    datasetData = currentEarningData.profit || [];
                    barColor = '#10b981';
                    hoverColor = '#059669';
                    metricLabel = "{{ __('Profit') }}";
                    bigVal = '{{ $currency }} ' + Number(currentEarningData.total_profit || 0).toLocaleString();
                    bigLabel = "{{ __('Total net profit for the selected range') }}";
                    $('#earningBigMetricValue').css('color', '#10b981');
                } else if (activeEarningMetric === 'expense') {
                    datasetData = currentEarningData.expense || [];
                    barColor = '#f59e0b';
                    hoverColor = '#d97706';
                    metricLabel = "{{ __('Ad Spend / Expense') }}";
                    bigVal = '{{ $currency }} ' + Number(currentEarningData.total_expense || 0).toLocaleString();
                    bigLabel = "{{ __('Total expenses for the selected range') }}";
                    $('#earningBigMetricValue').css('color', '#f59e0b');
                }

                $('#earningBigMetricValue').text(bigVal);
                $('#earningBigMetricLabel').text(bigLabel);

                earningChartInstance.data.labels = currentEarningData.labels || [];
                earningChartInstance.data.datasets[0].label = metricLabel;
                earningChartInstance.data.datasets[0].data = datasetData;
                earningChartInstance.data.datasets[0].backgroundColor = barColor;
                earningChartInstance.data.datasets[0].hoverBackgroundColor = hoverColor;
                earningChartInstance.update();
            }

            if (earningCanvas && currentEarningData) {
                earningChartInstance = new Chart(earningCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: currentEarningData.labels || [],
                        datasets: [{
                            label: "{{ __('Orders') }}",
                            data: currentEarningData.orders || [],
                            backgroundColor: '#ec4899',
                            hoverBackgroundColor: '#db2777',
                            borderRadius: 6,
                            barPercentage: 0.5,
                            categoryPercentage: 0.7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...chartTooltipConfig,
                                callbacks: {
                                    label: function(context) {
                                        let val = context.raw || 0;
                                        if (activeEarningMetric === 'revenue' || activeEarningMetric === 'profit' || activeEarningMetric === 'expense') {
                                            return `${context.dataset.label}: {{ $currency }} ${Number(val).toLocaleString()}`;
                                        }
                                        return `${context.dataset.label}: ${Number(val).toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: '#64748b', font: { size: 11, weight: 'bold' } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: chartGridColor, drawBorder: false },
                                ticks: {
                                    color: '#64748b',
                                    font: { size: 10 },
                                    callback: function(value) {
                                        if (value >= 1000) return (value / 1000) + 'k';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Click tile handler
            $(document).on('click', '.earning-tile-btn', function() {
                $('.earning-tile-btn').removeClass('active');
                $(this).addClass('active');
                activeEarningMetric = $(this).data('metric');
                renderEarningChartMetric();
            });

            // Filter changes for Earning Reports
            function reloadEarningReportsChart() {
                const status = $('#earningStatusSelect').val();
                const period = $('#earningPeriodSelect').val();

                $.ajax({
                    url: "{{ route('dashboard.chart.earning-reports') }}",
                    type: "GET",
                    data: { status: status, period: period },
                    success: function(res) {
                        currentEarningData = res;
                        $('#earningReportsRangeLabel').text(res.range_label || '');
                        renderEarningChartMetric();
                    },
                    error: function(xhr) {
                        console.error('Earning reports chart error:', xhr);
                    }
                });
            }

            $('#earningStatusSelect, #earningPeriodSelect').on('change', function() {
                reloadEarningReportsChart();
            });

            // 3. Hourly Web Orders Chart
            const hourlyCanvas = document.getElementById('hourlyOrdersChartCanvas');
            let hourlyChartInstance = null;
            let currentHourlyData = {!! json_encode($hourly_orders_chart ?? []) !!};

            if (hourlyCanvas && currentHourlyData) {
                hourlyChartInstance = new Chart(hourlyCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: currentHourlyData.labels || [],
                        datasets: [
                            {
                                label: "{{ __('Today') }}",
                                data: currentHourlyData.today || [],
                                borderColor: '#9333ea',
                                backgroundColor: 'rgba(147, 51, 234, 0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointRadius: 2.5,
                                pointBackgroundColor: '#9333ea',
                                pointHoverRadius: 6
                            },
                            {
                                label: "{{ __('Yesterday') }}",
                                data: currentHourlyData.yesterday || [],
                                borderColor: '#f59e0b',
                                borderDash: [4, 4],
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 0,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...chartTooltipConfig,
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: {
                                    color: '#64748b',
                                    font: { size: 9.5, weight: 'bold' },
                                    maxRotation: 0,
                                    callback: function(val, index) {
                                        return index % 2 === 0 ? this.getLabelForValue(val) : '';
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: chartGridColor, drawBorder: false },
                                ticks: { color: '#64748b', font: { size: 10 }, precision: 0 }
                            }
                        }
                    }
                });
            }

            $('#hourlyChannelSelect').on('change', function() {
                const ch = $(this).val();
                $.ajax({
                    url: "{{ route('dashboard.chart.hourly-orders') }}",
                    type: "GET",
                    data: { channel: ch },
                    success: function(res) {
                        currentHourlyData = res;
                        $('#hourlyOrdersSubtitle').text(res.subtitle || '');
                        $('#hourlyTodayTotal').text(res.total_today || 0);
                        $('#hourlyYesterdayTotal').text(res.total_yesterday || 0);

                        if (hourlyChartInstance) {
                            hourlyChartInstance.data.datasets[0].data = res.today || [];
                            hourlyChartInstance.data.datasets[1].data = res.yesterday || [];
                            hourlyChartInstance.update();
                        }
                    },
                    error: function(xhr) {
                        console.error('Hourly orders chart error:', xhr);
                    }
                });
            });

            // Toggle pill buttons for Today / Yesterday line in hourly chart
            $('#btnHourlyToday').on('click', function() {
                if (hourlyChartInstance) {
                    const isHidden = hourlyChartInstance.getDatasetMeta(0).hidden;
                    hourlyChartInstance.getDatasetMeta(0).hidden = !isHidden;
                    hourlyChartInstance.update();
                    $(this).toggleClass('active', isHidden);
                }
            });

            $('#btnHourlyYesterday').on('click', function() {
                if (hourlyChartInstance) {
                    const isHidden = hourlyChartInstance.getDatasetMeta(1).hidden;
                    hourlyChartInstance.getDatasetMeta(1).hidden = !isHidden;
                    hourlyChartInstance.update();
                    $(this).toggleClass('active', isHidden);
                }
            });
        });

        $(document).ready(function() {
            const filterTranslations = {
                'Today': "{{ __('Today') }}",
                'Yesterday': "{{ __('Yesterday') }}",
                'Last-7-days': "{{ __('Last 7 days') }}",
                'Last-30-days': "{{ __('Last 30 days') }}",
                'This-week': "{{ __('This Week') }}",
                'This-month': "{{ __('This Month') }}",
                'Last-month': "{{ __('Last Month') }}",
                'This-year': "{{ __('This Year') }}",
                'Last-year': "{{ __('Last Year') }}",
                'Lifetime': "{{ __('Lifetime') }}"
            };

            // Toggle dropdown menus
            $(document).on('click', '.dash-filter-btn', function(e) {
                e.stopPropagation();
                const wrapper = $(this).closest('.dash-date-filter-wrapper');
                const wasOpen = wrapper.hasClass('open');
                $('.dash-date-filter-wrapper').removeClass('open');
                if (!wasOpen) {
                    wrapper.addClass('open');
                }
            });

            // Close dropdowns on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dash-date-filter-wrapper').length) {
                    $('.dash-date-filter-wrapper').removeClass('open');
                }
            });

            // Expand / collapse custom subpanels
            $(document).on('click', '.dash-custom-header-row', function(e) {
                e.stopPropagation();
                const trigger = $(this).closest('.dash-filter-custom-trigger');
                const panel = trigger.find('.dash-custom-panel');
                const isExpanded = trigger.hasClass('expanded');

                trigger.siblings('.dash-filter-custom-trigger').removeClass('expanded').find('.dash-custom-panel').slideUp(150);

                if (isExpanded) {
                    trigger.removeClass('expanded');
                    panel.slideUp(150);
                } else {
                    trigger.addClass('expanded');
                    panel.slideDown(150);
                }
            });

            // Stop click bubbling inside custom inputs
            $(document).on('click', '.dash-custom-panel', function(e) {
                e.stopPropagation();
            });

            // Handle dropdown preset item clicks
            $(document).on('click', '.dash-filter-item', function(e) {
                e.stopPropagation();
                const item = $(this);
                const wrapper = item.closest('.dash-date-filter-wrapper');
                const channel = wrapper.data('channel'); // 'manual' or 'online'
                const filterVal = item.data('filter');
                const labelText = filterTranslations[filterVal] || item.find('span').text().trim();

                wrapper.find('.dash-filter-item').removeClass('active');
                item.addClass('active');
                wrapper.find('.dash-filter-label').text(labelText);
                wrapper.find('.dash-filter-custom-trigger').removeClass('expanded').find('.dash-custom-panel').hide();
                wrapper.removeClass('open');

                if (channel === 'manual') {
                    $('.filterNameManual').text(labelText);
                } else if (channel === 'online') {
                    $('.filterNameOnline').text(labelText);
                }

                applyDashboardFilter({ filter: filterVal, channel: channel });
            });

            // Handle Single Date Apply
            $(document).on('click', '.dash-btn-apply-single', function(e) {
                e.stopPropagation();
                const btn = $(this);
                const wrapper = btn.closest('.dash-date-filter-wrapper');
                const channel = wrapper.data('channel');
                const dateVal = wrapper.find('.dash-single-input').val();

                if (!dateVal) {
                    alert('দয়া করে তারিখ নির্বাচন করুন!');
                    return;
                }

                wrapper.find('.dash-filter-item').removeClass('active');
                wrapper.find('.dash-filter-label').text(dateVal);
                wrapper.removeClass('open');

                if (channel === 'manual') {
                    $('.filterNameManual').text(dateVal);
                } else if (channel === 'online') {
                    $('.filterNameOnline').text(dateVal);
                }

                applyDashboardFilter({ filter: 'single_date', start_date: dateVal, channel: channel });
            });

            // Handle Custom Range Apply
            $(document).on('click', '.dash-btn-apply-range', function(e) {
                e.stopPropagation();
                const btn = $(this);
                const wrapper = btn.closest('.dash-date-filter-wrapper');
                const channel = wrapper.data('channel');
                const sDate = wrapper.find('.dash-range-start-input').val();
                const eDate = wrapper.find('.dash-range-end-input').val();

                if (!sDate || !eDate) {
                    alert('দয়া করে শুরু ও শেষের তারিখ নির্বাচন করুন!');
                    return;
                }

                wrapper.find('.dash-filter-item').removeClass('active');
                const rangeLabel = sDate + ' to ' + eDate;
                wrapper.find('.dash-filter-label').text(rangeLabel);
                wrapper.removeClass('open');

                if (channel === 'manual') {
                    $('.filterNameManual').text(rangeLabel);
                } else if (channel === 'online') {
                    $('.filterNameOnline').text(rangeLabel);
                }

                applyDashboardFilter({ filter: 'custom', start_date: sDate, end_date: eDate, channel: channel });
            });

            // Top Filter Nav clicks
            $('#filterNav .nav-link').click(function(e) {
                e.preventDefault(); 
                $('#filterNav .nav-link').removeClass('active');
                $(this).addClass('active');

                let selectedFilter = $(this).data('filter');
                let translated = filterTranslations[selectedFilter] || selectedFilter;
                $('.filterName').text(translated);
                $('.dash-filter-label').text(translated);

                applyDashboardFilter({ filter: selectedFilter });
            });

            function applyDashboardFilter(params) {
                $.ajax({
                    url: "{{ route('dashboard.filter') }}", 
                    type: "GET",
                    data: params,
                    success: function(response) {
                        // Update Manual Sales
                        if (!params.channel || params.channel === 'manual') {
                            $('#manualSaleAmount').text(Number(response.manual_sale || 0).toLocaleString());
                            $('#manualCount').text(Number(response.manual_count || 0).toLocaleString());
                            $('#summaryManualSale').text(Number(response.manual_sale || 0).toLocaleString());
                            $('#summaryManualCount').text(Number(response.manual_count || 0).toLocaleString());
                            $('#summaryManualProfit').text(Number(response.manual_profit || 0).toLocaleString());

                            if (response.bank_sales && response.bank_sales.accounts) {
                                $('#bankSalesTotalCollected').text(Number(response.bank_sales.total || 0).toLocaleString());
                                let bankHtml = '';
                                response.bank_sales.accounts.forEach(function(bank) {
                                    let nameLower = (bank.name || '').toLowerCase();
                                    let isCash = nameLower.includes('cash');
                                    let isBkash = nameLower.includes('bkash');
                                    let isNagad = nameLower.includes('nagad');
                                    let bg = isCash ? '#dcfce7' : (isBkash ? '#fce7f3' : (isNagad ? '#ffedd5' : '#e0e7ff'));
                                    let color = isCash ? '#16a34a' : (isBkash ? '#db2777' : (isNagad ? '#ea580c' : '#4f46e5'));
                                    let icon = isCash ? 'fa-money-bill-wave' : ((isBkash || isNagad) ? 'fa-mobile-alt' : 'fa-building-columns');

                                    bankHtml += `
                                        <div class="bank-account-row" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 32px; height: 32px; border-radius: 8px; background: ${bg}; color: ${color}; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                    <i class="fas ${icon}"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 12.5px; font-weight: 700; color: #1e293b;">
                                                        ${bank.name} ${bank.account_number ? `<span style="font-size: 11px; color: #64748b; font-weight: 500;">(${bank.account_number})</span>` : ''}
                                                    </div>
                                                    <div style="font-size: 10.5px; color: #94a3b8; font-weight: 600;">
                                                        <span class="bank-count">${bank.count}</span> {{ __('Invoices') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div style="font-size: 13.5px; font-weight: 800; color: #0f172a;">
                                                    {{ $currency }}<span class="bank-amount">${Number(bank.amount || 0).toLocaleString()}</span>
                                                </div>
                                                <div style="font-size: 10px; font-weight: 700; color: #059669;">
                                                    <span class="bank-percent">${bank.percentage}</span>%
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                                if (response.bank_sales.accounts.length === 0) {
                                    bankHtml = `<div class="text-muted small text-center py-2">{{ __('No bank sales recorded for this period.') }}</div>`;
                                }
                                $('#bankSalesContainer').html(bankHtml);
                            }
                        }

                        // Update Online Sales
                        if (!params.channel || params.channel === 'online') {
                            $('#onlineSaleAmount').text(Number(response.online_sale || 0).toLocaleString());
                            $('#onlineCount').text(Number(response.online_count || 0).toLocaleString());
                            $('#summaryOnlineSale').text(Number(response.online_sale || 0).toLocaleString());
                            $('#summaryOnlineCount').text(Number(response.online_count || 0).toLocaleString());
                            $('#summaryOnlineProfit').text(Number(response.online_profit || 0).toLocaleString());

                            if (response.online_profit_overview) {
                                const ov = response.online_profit_overview;
                                $('#onlineProfitSummaryTotal').text(Number(ov.total_profit || 0).toLocaleString());
                                $('#onlineProfitMarginText').text((ov.margin_percent || 0) + '%');
                                $('#onlineDeliveredProfit').text(Number(ov.delivered_profit || 0).toLocaleString());
                                $('#onlineDeliveredCount').text(Number(ov.delivered_count || 0).toLocaleString());
                                $('#onlineDeliveredSales').text(Number(ov.delivered_sales || 0).toLocaleString());
                                $('#onlineTransitProfit').text(Number(ov.transit_profit || 0).toLocaleString());
                                $('#onlineTransitCount').text(Number(ov.transit_count || 0).toLocaleString());
                                $('#onlineTransitSales').text(Number(ov.transit_sales || 0).toLocaleString());
                            }

                            if (response.status_processing !== undefined) {
                                $('#statusProcessingCount').text(response.status_processing);
                            }
                            if (response.status_rts !== undefined) {
                                $('#statusRtsCount').text(response.status_rts);
                            }
                            if (response.status_in_courier !== undefined) {
                                $('#statusInCourierCount').text(response.status_in_courier);
                            }
                            if (response.status_delivered !== undefined) {
                                $('#statusDeliveredCount').text(response.status_delivered);
                            }
                        }

                        // Top overview stats
                        if (!params.channel) {
                            $('#saleAmount').text(Number(response.sale || 0).toLocaleString());
                            $('#purchaseAmount').text(Number(response.purchase || 0).toLocaleString());
                            $('#expenseAmount').text(Number(response.expense || 0).toLocaleString());
                            $('#profitAmount').text(Number(response.profit || 0).toLocaleString());
                        }
                    },
                    error: function(xhr) {
                        console.error('Dashboard filter AJAX error:', xhr);
                    }
                });
            }

            // Open Sales Summary Report directly in a new page/tab
            $('#btnGenCustomSummary').click(function() {
                const sDate = $('#dashStartDate').val();
                const eDate = $('#dashEndDate').val();
                if(!sDate || !eDate) {
                    alert('দয়া করে তারিখ নির্বাচন করুন!');
                    return;
                }
                const printUrl = `{{ route('dashboard.product-sales-summary.print') }}?start_date=${encodeURIComponent(sDate)}&end_date=${encodeURIComponent(eDate)}`;
                window.open(printUrl, '_blank');
            });

            // Trigger Chart.js resize on window resize to ensure full mobile responsiveness
            let dashChartResizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(dashChartResizeTimer);
                dashChartResizeTimer = setTimeout(function() {
                    if (typeof manualChartInstance !== 'undefined' && manualChartInstance) manualChartInstance.resize();
                    if (typeof platformChartInstance !== 'undefined' && platformChartInstance) platformChartInstance.resize();
                    if (typeof webOrderChartInstance !== 'undefined' && webOrderChartInstance) webOrderChartInstance.resize();
                    if (typeof ordersBySourceChartInstance !== 'undefined' && ordersBySourceChartInstance) ordersBySourceChartInstance.resize();
                    if (typeof orderCountsChartInstance !== 'undefined' && orderCountsChartInstance) orderCountsChartInstance.resize();
                    if (typeof earningChartInstance !== 'undefined' && earningChartInstance) earningChartInstance.resize();
                    if (typeof hourlyChartInstance !== 'undefined' && hourlyChartInstance) hourlyChartInstance.resize();
                }, 100);
            });
        });
    </script>
@endpush
