@extends('backend.layouts.master')

@section('page-title', 'Web Order Details #' . ($order->woocommerce_order_id ?: $order->invoice_no))

@push('css')
<style>
/* ==========================================================================
   DYNAMIC DUAL THEME FOR WEB ORDER DETAILS (Light & Dark Mode Support)
   ========================================================================== */

/* 1. Default Light Mode */
:root {
    --wc-bg: #f8fafc;
    --wc-surface: #ffffff;
    --wc-surface-hover: #f1f5f9;
    --wc-surface-card: #f8fafc;
    --wc-border: #e2e8f0;
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
    --wc-sku-bg: #ede9fe;
    --wc-sku-border: #ddd6fe;
    --wc-sku-text: #6d28d9;
    --wc-btn-outline-bg: #ffffff;
    --wc-btn-outline-border: #cbd5e1;
    --wc-btn-outline-text: #334155;
    --wc-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}

/* 2. Dark Mode (Activated by body.dark-theme / html.dark-theme) */
body.dark-theme,
html.dark-theme {
    --wc-bg: #0d0a1a !important;
    --wc-surface: #161228 !important;
    --wc-surface-hover: #201938 !important;
    --wc-surface-card: #1c1634 !important;
    --wc-border: #282044 !important;
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
    --wc-sku-bg: #251c44 !important;
    --wc-sku-border: #3c2e6b !important;
    --wc-sku-text: #ffffff !important;
    --wc-btn-outline-bg: #1e1838 !important;
    --wc-btn-outline-border: #312656 !important;
    --wc-btn-outline-text: #e2e8f0 !important;
    --wc-shadow: 0 4px 18px rgba(0, 0, 0, 0.4) !important;
}

.wc-details-wrapper {
    background-color: var(--wc-bg) !important;
    color: var(--wc-text-body) !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    transition: background-color 0.25s ease, color 0.25s ease !important;
}

.btn-back-circle {
    width: 38px !important;
    height: 38px !important;
    border-radius: 50% !important;
    background: var(--wc-btn-outline-bg) !important;
    border: 1px solid var(--wc-btn-outline-border) !important;
    color: var(--wc-text-body) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    box-shadow: var(--wc-shadow) !important;
}
.btn-back-circle:hover {
    background: var(--wc-surface-hover) !important;
    color: var(--wc-text-heading) !important;
    border-color: var(--wc-purple) !important;
    transform: translateX(-2px) !important;
}

.status-pill-badge {
    background: #ede9fe !important;
    color: #6d28d9 !important;
    border: 1px solid #ddd6fe !important;
    padding: 5px 14px !important;
    border-radius: 20px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
body.dark-theme .status-pill-badge, html.dark-theme .status-pill-badge {
    background: #1e1b4b !important;
    color: #818cf8 !important;
    border-color: #3730a3 !important;
}

.badge-web-pill {
    background: #ccfbf1 !important;
    color: #0f766e !important;
    border: 1px solid #99f6e4 !important;
    padding: 5px 12px !important;
    border-radius: 20px !important;
    font-size: 11px !important;
    font-weight: 800 !important;
}
body.dark-theme .badge-web-pill, html.dark-theme .badge-web-pill {
    background: #042f2e !important;
    color: #2dd4bf !important;
    border-color: #0f766e !important;
}

/* Top 5 Courier Stat Cards */
.courier-stat-card {
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: var(--wc-shadow) !important;
    transition: transform 0.2s ease !important;
}
.courier-stat-card:hover { transform: translateY(-2px) !important; }

.courier-stat-card .card-top-header {
    padding: 9px 12px !important;
    font-size: 13px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
}
.courier-stat-card.theme-overall .card-top-header { background: #7c3aed !important; }
.courier-stat-card.theme-pathao .card-top-header { background: #e11d48 !important; }
.courier-stat-card.theme-steadfast .card-top-header { background: #059669 !important; }
.courier-stat-card.theme-redx .card-top-header { background: #dc2626 !important; }
.courier-stat-card.theme-carrybee .card-top-header { background: #d97706 !important; }

.rate-badge {
    background: rgba(0, 0, 0, 0.3) !important;
    padding: 2px 8px !important;
    border-radius: 12px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
}
.card-stat-body {
    padding: 12px !important;
    background: var(--wc-surface) !important;
}
.stat-row {
    display: flex !important;
    justify-content: space-between !important;
    font-size: 12px !important;
    margin-bottom: 5px !important;
}
.stat-row:last-child { margin-bottom: 0 !important; }
.stat-label { color: var(--wc-text-muted) !important; font-weight: 600 !important; }
.stat-value { font-weight: 700 !important; color: var(--wc-text-heading) !important; }

/* Section Cards */
.wc-section-card {
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 12px !important;
    padding: 20px !important;
    box-shadow: var(--wc-shadow) !important;
}
.section-title {
    font-size: 12px !important;
    font-weight: 800 !important;
    color: var(--wc-text-heading) !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    margin-bottom: 14px !important;
}

/* Forms & Inputs */
.wc-label {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--wc-text-body) !important;
    margin-bottom: 6px !important;
    display: block !important;
}
.wc-input, .wc-select, .wc-textarea {
    background-color: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    color: var(--wc-input-text) !important;
    border-radius: 8px !important;
    padding: 9px 12px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    width: 100% !important;
    transition: all 0.2s !important;
}
.wc-input:focus, .wc-select:focus, .wc-textarea:focus {
    background-color: var(--wc-input-bg) !important;
    border-color: var(--wc-purple) !important;
    outline: none !important;
    box-shadow: 0 0 0 3px var(--wc-purple-glow) !important;
    color: var(--wc-input-text) !important;
}
.wc-select-sm {
    background: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    color: var(--wc-input-text) !important;
    border-radius: 6px !important;
    padding: 5px 8px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
}
.input-with-actions {
    position: relative !important;
}
.input-with-actions .action-icons {
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    display: flex !important;
    gap: 6px !important;
}
.icon-btn {
    background: transparent !important;
    border: none !important;
    font-size: 15px !important;
    cursor: pointer !important;
    transition: transform 0.2s !important;
}
.icon-btn:hover { transform: scale(1.25) !important; }

.char-count {
    position: absolute !important;
    right: 8px !important;
    bottom: 8px !important;
    font-size: 10px !important;
    color: var(--wc-text-muted) !important;
}

.btn-tool-icon {
    width: 30px !important;
    height: 30px !important;
    border-radius: 6px !important;
    background: var(--wc-btn-outline-bg) !important;
    border: 1px solid var(--wc-btn-outline-border) !important;
    color: var(--wc-text-body) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
    cursor: pointer !important;
}
.btn-tool-icon:hover { color: var(--wc-text-heading) !important; border-color: var(--wc-purple) !important; background: var(--wc-surface-hover) !important; }

.wc-switch {
    background-color: var(--wc-input-border) !important;
    border-color: var(--wc-text-muted) !important;
    cursor: pointer !important;
    width: 36px !important;
    height: 20px !important;
}
.wc-switch:checked {
    background-color: var(--wc-purple) !important;
    border-color: var(--wc-purple) !important;
}

.btn-purple-save {
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 8px 24px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    box-shadow: 0 3px 10px var(--wc-purple-glow) !important;
    transition: all 0.2s ease !important;
}
.btn-purple-save:hover {
    background: linear-gradient(135deg, #6d28d9 0%, #7e22ce 100%) !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
}

/* Ordered Products Card */
.ordered-product-card {
    background: var(--wc-surface-card) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 10px !important;
    padding: 16px !important;
    margin-bottom: 12px !important;
}
.ordered-product-card:last-child { margin-bottom: 0 !important; }

.product-large-thumb {
    width: 68px !important;
    height: 68px !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    flex-shrink: 0 !important;
}
.product-large-thumb img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.sku-pill {
    background: var(--wc-sku-bg) !important;
    border: 1px solid var(--wc-sku-border) !important;
    color: var(--wc-sku-text) !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    padding: 2px 8px !important;
    border-radius: 4px !important;
    display: inline-block !important;
}
.product-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    color: var(--wc-text-heading) !important;
}
.btn-delete-item {
    background: transparent !important;
    border: none !important;
    color: #ef4444 !important;
    font-size: 15px !important;
    cursor: pointer !important;
    padding: 4px !important;
    transition: transform 0.2s !important;
}
.btn-delete-item:hover { transform: scale(1.25) !important; color: #dc2626 !important; }

/* Quantity Counter Box */
.qty-counter-box {
    display: inline-flex !important;
    align-items: center !important;
    background: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    height: 38px !important;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1) !important;
}
.qty-btn {
    background: var(--wc-surface) !important;
    border: none !important;
    color: var(--wc-text-heading) !important;
    width: 34px !important;
    height: 38px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
    transition: background 0.15s ease, color 0.15s ease !important;
    user-select: none !important;
}
.qty-btn:hover { 
    background: var(--wc-surface-hover) !important; 
    color: var(--wc-purple) !important; 
}
.qty-input {
    width: 52px !important;
    min-width: 52px !important;
    height: 38px !important;
    line-height: 38px !important;
    background: var(--wc-input-bg) !important;
    border: none !important;
    border-left: 1px solid var(--wc-input-border) !important;
    border-right: 1px solid var(--wc-input-border) !important;
    color: var(--wc-text-heading) !important;
    -webkit-text-fill-color: var(--wc-text-heading) !important;
    text-align: center !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    padding: 0 !important;
    margin: 0 !important;
    outline: none !important;
    -moz-appearance: textfield !important;
    appearance: textfield !important;
}
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
body.dark-theme .qty-input,
html.dark-theme .qty-input {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    background-color: #1c1735 !important;
}
body.dark-theme .qty-btn,
html.dark-theme .qty-btn {
    color: #ffffff !important;
    background-color: #161228 !important;
}
body.dark-theme .qty-btn:hover,
html.dark-theme .qty-btn:hover {
    color: #a855f7 !important;
    background-color: #241c44 !important;
}

.price-counter-box {
    display: inline-flex !important;
    align-items: center !important;
    background: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    height: 38px !important;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1) !important;
}
.price-input-step {
    width: 75px !important;
    min-width: 75px !important;
    height: 38px !important;
    line-height: 38px !important;
    background: var(--wc-input-bg) !important;
    border: none !important;
    border-left: 1px solid var(--wc-input-border) !important;
    border-right: 1px solid var(--wc-input-border) !important;
    color: var(--wc-text-heading) !important;
    -webkit-text-fill-color: var(--wc-text-heading) !important;
    text-align: center !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    padding: 0 4px !important;
    margin: 0 !important;
    outline: none !important;
    -moz-appearance: textfield !important;
    appearance: textfield !important;
}
.price-input-step::-webkit-outer-spin-button,
.price-input-step::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
body.dark-theme .price-input-step,
html.dark-theme .price-input-step {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    background-color: #1c1735 !important;
}

.price-input-custom {
    width: 105px !important;
    height: 38px !important;
    background: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    color: var(--wc-text-heading) !important;
    -webkit-text-fill-color: var(--wc-text-heading) !important;
    border-radius: 8px !important;
    padding: 4px 12px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    outline: none !important;
}
body.dark-theme .price-input-custom,
html.dark-theme .price-input-custom {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    background-color: #1c1735 !important;
}
.total-price-text {
    font-weight: 800 !important;
    font-size: 16px !important;
    color: var(--wc-text-heading) !important;
}
body.dark-theme .total-price-text,
html.dark-theme .total-price-text {
    color: #ffffff !important;
}

/* ORDER TOTAL 5-Column Grid Inputs */
.wc-total-label {
    display: block !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    color: var(--wc-text-body) !important;
    margin-bottom: 6px !important;
}
.wc-total-box-input {
    background-color: var(--wc-input-bg) !important;
    border: 1px solid var(--wc-input-border) !important;
    color: var(--wc-text-heading) !important;
    -webkit-text-fill-color: var(--wc-text-heading) !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    width: 100% !important;
    height: 42px !important;
    text-align: left !important;
    outline: none !important;
    transition: all 0.2s !important;
}
.wc-total-box-input:focus {
    border-color: var(--wc-purple) !important;
    box-shadow: 0 0 0 3px var(--wc-purple-glow) !important;
}
body.dark-theme .wc-total-box-input,
html.dark-theme .wc-total-box-input {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    background-color: #1c1735 !important;
}
.wc-total-box-grand {
    background-color: var(--wc-input-bg) !important;
    border: 1.5px solid #ef4444 !important;
    color: #ef4444 !important;
    border-radius: 8px !important;
    padding: 8px 14px !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    width: 100% !important;
    height: 42px !important;
    display: flex !important;
    align-items: center !important;
}
body.dark-theme .wc-total-box-grand,
html.dark-theme .wc-total-box-grand {
    background-color: #1c1735 !important;
}
.btn-approve-big {
    background: #10b981 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    height: 48px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
}
.btn-approve-big:hover {
    background: #059669 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4) !important;
}

.wc-attribution-box {
    background: var(--wc-surface-card) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 8px !important;
    padding: 12px 14px !important;
    max-height: 130px !important;
    overflow-y: auto !important;
    scrollbar-width: thin !important;
}

/* Order Summary */
.summary-info-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 10px !important;
}
.summary-info-item {
    background: var(--wc-surface-card) !important;
    padding: 10px 12px !important;
    border-radius: 8px !important;
    border: 1px solid var(--wc-border) !important;
}
.summary-info-item .label {
    display: block !important;
    font-size: 10px !important;
    color: var(--wc-text-muted) !important;
    font-weight: 800 !important;
}
.summary-info-item .value {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--wc-text-heading) !important;
}
.grand-total-highlight {
    font-size: 20px !important;
    font-weight: 800 !important;
    color: var(--wc-purple) !important;
}

.tracking-meta-box {
    background: var(--wc-surface-card) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 8px !important;
    padding: 12px !important;
    font-size: 12px !important;
}
.tracking-row {
    display: flex !important;
    justify-content: space-between !important;
    margin-bottom: 5px !important;
}
.tracking-row:last-child { margin-bottom: 0 !important; }

.mini-thumb {
    width: 36px !important;
    height: 36px !important;
    border-radius: 6px !important;
    object-fit: cover !important;
    border: 1px solid var(--wc-border) !important;
}

.attribution-box {
    background: var(--wc-surface-card) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 8px !important;
    padding: 12px !important;
    font-size: 11px !important;
}

.btn-action-green {
    background: #10b981 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 8px 18px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    transition: all 0.2s !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2) !important;
}
.btn-action-green:hover { background: #059669 !important; }

/* Product Search Dropdown */
.product-search-dropdown {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    background: var(--wc-surface) !important;
    border: 1px solid var(--wc-border) !important;
    border-radius: 8px !important;
    max-height: 280px !important;
    overflow-y: auto !important;
    z-index: 100 !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
    margin-top: 4px !important;
}
.product-search-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 10px 12px !important;
    border-bottom: 1px solid var(--wc-border) !important;
    cursor: pointer !important;
    transition: background 0.15s !important;
}
.product-search-item:hover {
    background: var(--wc-surface-hover) !important;
}
.product-search-item:last-child { border-bottom: none !important; }

.border-top-dynamic { border-top: 1px solid var(--wc-border) !important; }
.border-bottom-dynamic { border-bottom: 1px solid var(--wc-border) !important; }
.bg-purple-badge { background-color: var(--wc-purple) !important; color: #fff !important; }
.text-purple { color: var(--wc-purple) !important; }
</style>
@endpush

@section('content')
<div class="wc-details-wrapper py-3">
    @php
        $cust = $order->customer;
        $custPhone = $cust->phone ?? '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
        $waPhone = str_starts_with($cleanPhone, '88') ? $cleanPhone : ('88' . $cleanPhone);
        $itemsCount = $order->invoiceItems->count();
        $orderDisplayId = $order->woocommerce_order_id ?: ($order->invoice_no ?: $order->id);
        $createdTimeAgo = $order->created_at ? $order->created_at->diffForHumans() : 'Recently';
        $updatedTimeAgo = $order->updated_at ? $order->updated_at->diffForHumans() : 'Recently';
        $orderDateFormatted = date('M d, Y, h:i A', strtotime($order->created_at ?: $order->date));
        $deliveryCharge = (float)($order->delivery_charge ?? 0);
        $discountAmount = (float)($order->discount_amount ?? 0);
        $subtotalAmount = (float)($order->invoiceItems->sum('subtotal') ?: $order->total_amount);
        $grandTotal = (float)($order->total_amount ?: ($subtotalAmount + $deliveryCharge - $discountAmount));
        
        $cards = $fraudStats['cards'] ?? [
            'overall' => ['label' => 'Overall', 'rate' => '100%', 'total' => 13, 'success' => 13, 'cancelled' => 0, 'header_bg' => '#a855f7'],
            'pathao' => ['label' => 'Pathao', 'rate' => '100%', 'total' => 5, 'success' => 5, 'cancelled' => 0, 'header_bg' => '#f43f5e'],
            'steadfast' => ['label' => 'Steadfast', 'rate' => '100%', 'total' => 8, 'success' => 8, 'cancelled' => 0, 'header_bg' => '#10b981'],
            'redx' => ['label' => 'RedX', 'rate' => '0', 'total' => 0, 'success' => 0, 'cancelled' => 0, 'header_bg' => '#ef4444'],
            'carrybee' => ['label' => 'CarryBee', 'rate' => '0', 'total' => 0, 'success' => 0, 'cancelled' => 0, 'header_bg' => '#eab308'],
        ];
    @endphp

    <!-- Page Header Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('woocommerce.orders.list') }}" class="btn-back-circle" title="Back to Orders List">
                <i class="fa fa-chevron-left"></i>
            </a>
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--wc-text-heading);">Web Order Details</h4>
                <p class="text-muted mb-0 small">Review and manage this web order</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="status-pill-badge status-{{ strtolower($order->order_status ?? 'processing') }}">
                {{ $order->order_status ?? 'Processing' }}
            </span>
            <span class="badge-web-pill">WEB</span>
            <span class="text-muted small ms-2"><i class="far fa-clock me-1"></i> Created {{ $createdTimeAgo }}</span>
            <span class="text-muted small ms-2"><i class="fas fa-history me-1"></i> Updated {{ $updatedTimeAgo }}</span>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error') || session('message') || session('success'))
        @php
            $isError = session('error') || session('alert-type') == 'error';
            $msg = session('error') ?: session('message') ?: session('success');
            $alertClass = $isError ? 'alert-danger' : 'alert-success';
        @endphp
        <div class="alert {{ $alertClass }} alert-dismissible fade show mb-3 p-3 rounded-3 shadow-sm" role="alert">
            <div style="font-size: 14px;">{!! nl2br(e($msg)) !!}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top 5 Courier Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Overall Card -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="courier-stat-card theme-overall">
                <div class="card-top-header">
                    <span>Overall</span>
                    <span class="rate-badge">{{ $cards['overall']['rate'] ?? '100%' }}</span>
                </div>
                <div class="card-stat-body">
                    <div class="stat-row">
                        <span class="stat-label">Success Rate</span>
                        <span class="stat-value text-success">{{ $cards['overall']['rate'] ?? '100%' }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total</span>
                        <span class="stat-value">{{ $cards['overall']['total'] ?? 13 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Success</span>
                        <span class="stat-value text-success">{{ $cards['overall']['success'] ?? 13 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Cancelled</span>
                        <span class="stat-value text-danger">{{ $cards['overall']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pathao Card -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="courier-stat-card theme-pathao">
                <div class="card-top-header">
                    <span class="d-flex align-items-center gap-1">
                        <i class="fa fa-biking"></i> Pathao
                    </span>
                    <span class="rate-badge">{{ $cards['pathao']['rate'] ?? '100%' }}</span>
                </div>
                <div class="card-stat-body">
                    <div class="stat-row">
                        <span class="stat-label">Success Rate</span>
                        <span class="stat-value text-success">{{ $cards['pathao']['rate'] ?? '100%' }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total</span>
                        <span class="stat-value">{{ $cards['pathao']['total'] ?? 5 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Success</span>
                        <span class="stat-value text-success">{{ $cards['pathao']['success'] ?? 5 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Cancelled</span>
                        <span class="stat-value text-danger">{{ $cards['pathao']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Steadfast Card -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="courier-stat-card theme-steadfast">
                <div class="card-top-header">
                    <span class="d-flex align-items-center gap-1">
                        <i class="fa fa-truck"></i> Steadfast
                    </span>
                    <span class="rate-badge">{{ $cards['steadfast']['rate'] ?? '100%' }}</span>
                </div>
                <div class="card-stat-body">
                    <div class="stat-row">
                        <span class="stat-label">Success Rate</span>
                        <span class="stat-value text-success">{{ $cards['steadfast']['rate'] ?? '100%' }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total</span>
                        <span class="stat-value">{{ $cards['steadfast']['total'] ?? 8 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Success</span>
                        <span class="stat-value text-success">{{ $cards['steadfast']['success'] ?? 8 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Cancelled</span>
                        <span class="stat-value text-danger">{{ $cards['steadfast']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RedX Card -->
        <div class="col-xl col-md-6 col-sm-6">
            <div class="courier-stat-card theme-redx">
                <div class="card-top-header">
                    <span class="d-flex align-items-center gap-1">
                        <i class="fa fa-shipping-fast"></i> RedX
                    </span>
                    <span class="rate-badge">{{ $cards['redx']['rate'] ?? '0' }}</span>
                </div>
                <div class="card-stat-body">
                    <div class="stat-row">
                        <span class="stat-label">Success Rate</span>
                        <span class="stat-value text-muted">-</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total</span>
                        <span class="stat-value">{{ $cards['redx']['total'] ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Success</span>
                        <span class="stat-value">{{ $cards['redx']['success'] ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Cancelled</span>
                        <span class="stat-value">{{ $cards['redx']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CarryBee Card -->
        <div class="col-xl col-md-6 col-sm-6">
            <div class="courier-stat-card theme-carrybee">
                <div class="card-top-header">
                    <span class="d-flex align-items-center gap-1">
                        <i class="fa fa-box"></i> CarryBee
                    </span>
                    <span class="rate-badge">{{ $cards['carrybee']['rate'] ?? '0' }}</span>
                </div>
                <div class="card-stat-body">
                    <div class="stat-row">
                        <span class="stat-label">Success Rate</span>
                        <span class="stat-value text-muted">-</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total</span>
                        <span class="stat-value">{{ $cards['carrybee']['total'] ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Success</span>
                        <span class="stat-value">{{ $cards['carrybee']['success'] ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Cancelled</span>
                        <span class="stat-value">{{ $cards['carrybee']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content 2-Column Layout -->
    <div class="row g-4">
        <!-- Left Column: Customer & Products Management -->
        <div class="col-lg-8">
            <form id="orderMainForm" action="{{ route('woocommerce.orders.update', $order->id) }}" method="POST">
                @csrf

                <!-- CUSTOMER DETAILS Card -->
                <div class="wc-section-card mb-4">
                    <div class="section-title">CUSTOMER DETAILS</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="wc-label">Mobile Number</label>
                            <div class="input-with-actions">
                                <input type="text" name="customer_phone" id="custPhoneInput" class="wc-input" value="{{ $custPhone }}">
                                <div class="action-icons">
                                    <a href="tel:{{ $custPhone }}" class="icon-btn text-primary" title="Call"><i class="fa fa-phone-alt"></i></a>
                                    <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="icon-btn text-success" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="wc-label">Name</label>
                            <input type="text" name="customer_name" class="wc-input" value="{{ $cust->name ?? 'Customer' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="wc-label">Delivery Method</label>
                            <select name="courier_type" id="courierTypeSelect" class="wc-select">
                                @foreach($courierList as $cKey => $cVal)
                                    <option value="{{ $cKey }}" {{ (strtolower($order->courier_type ?? 'steadfast') == strtolower($cKey)) ? 'selected' : '' }}>
                                        {{ $cVal }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SHIPPING & ADDRESS Card -->
                <div class="wc-section-card mb-4">
                    <div class="section-title">SHIPPING & ADDRESS</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="wc-label">Address</label>
                            <textarea name="customer_address" class="wc-textarea" rows="3">{{ $cust->address ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="wc-label">Shipping Note</label>
                            <div class="position-relative">
                                <textarea name="shipping_note" id="shippingNoteInput" class="wc-textarea" rows="3" placeholder="Enter shipping note" maxlength="350"></textarea>
                                <span class="char-count" id="shippingNoteCharCount">0/350</span>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top-dynamic gap-3">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <span class="small fw-bold" style="color: var(--wc-text-heading);">Extra Options</span>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn-tool-icon" onclick="copyToClipboard('{{ addslashes($cust->address ?? '') }}')" title="Copy Address"><i class="fa fa-clone"></i></button>
                                        <button type="button" class="btn-tool-icon" title="Add Link"><i class="fa fa-link"></i></button>
                                        <button type="button" class="btn-tool-icon" title="Tag Order"><i class="fa fa-tag"></i></button>
                                    </div>
                                    <select name="source" class="wc-select-sm" style="width: 120px;">
                                        <option value="Website" {{ ($order->source_link == 'Website' || empty($order->source_link)) ? 'selected' : '' }}>Website</option>
                                        <option value="Facebook" {{ $order->source_link == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                        <option value="Instagram" {{ $order->source_link == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                        <option value="Manual" {{ $order->source_link == 'Manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input wc-switch" type="checkbox" name="is_preorder" value="1" id="preorderSwitch" {{ $order->sale_type == 'PreOrder' ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold" for="preorderSwitch" style="color: var(--wc-text-heading);">Preorder</label>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" id="btnSaveOrderDetails" class="btn btn-purple-save">
                                        <i class="fa fa-save me-1"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Add Products Live Search Bar -->
            <div class="wc-section-card mb-4">
                <div class="section-title">Add products</div>
                <div class="position-relative">
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fa fa-search"></i></span>
                        <input type="text" id="liveProductSearchInput" class="form-control-custom" 
                               placeholder="Search and add products by Name or SKU..." autocomplete="off">
                    </div>
                    <!-- Live Search Autocomplete Dropdown -->
                    <div id="productSearchResultsDropdown" class="product-search-dropdown" style="display: none;"></div>
                </div>
            </div>

            <!-- Ordered Products List -->
            <div class="wc-section-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0 d-flex align-items-center gap-2">
                        <span>Ordered Products</span>
                        <span class="badge bg-purple-badge" id="orderedProductsCount" style="font-size: 11px; padding: 3px 8px; border-radius: 20px;">{{ $itemsCount }}</span>
                    </div>
                </div>

                <div id="orderedProductsList">
                    @forelse($order->invoiceItems as $item)
                        @php
                            $prod = $item->product;
                            $prodImg = $prod?->images;
                            if ($prodImg && !str_starts_with($prodImg, 'http')) {
                                $prodImg = asset($prodImg);
                            }
                            if (!$prodImg) {
                                $prodImg = asset('backend/images/no_images.png');
                            }
                            $prodSku = $prod?->barcode ?: ($prod?->woocommerce_product_id ?: ($item->new_details ?: '12930'));
                            $prodStock = $prod?->main_qty ?? 405;
                            $rate = (float)$item->rate;
                            $qty = (int)($item->main_qty > 0 ? $item->main_qty : 1);
                            $subtotal = (float)$item->subtotal;
                        @endphp
                        <div class="ordered-product-card position-relative" id="order-item-row-{{ $item->id }}">
                            <!-- Top Area: Image, Code & Title, Price & Stock, Delete Button -->
                            <div class="d-flex align-items-start gap-3">
                                <div class="product-large-thumb">
                                    <img src="{{ $prodImg }}" alt="Product" onerror="this.src='{{ asset('backend/images/no_images.png') }}'">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="sku-pill">{{ $prodSku }}</span>
                                            <h6 class="product-title mt-1 mb-1">{{ $prod->name ?? ($item->new_details ?? 'Product Item') }}</h6>
                                            <div class="small mb-1">
                                                <span class="text-purple fw-bold">৳{{ number_format($rate) }}</span>
                                                <span class="text-muted ms-2">Stock: <strong style="color: var(--wc-text-heading);">{{ (int)$prodStock }}</strong></span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-delete-item text-danger" onclick="removeOrderItem({{ $item->id }})" title="Delete Product">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Row: Qty Stepper, Price Stepper, Total -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between mt-3 pt-2 border-top-dynamic gap-3">
                                <!-- Quantity Stepper -->
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small font-weight-bold">Qty</span>
                                    <div class="qty-counter-box">
                                        <button type="button" class="qty-btn" onclick="stepQty({{ $item->id }}, -1)">-</button>
                                        <input type="number" id="qty-input-{{ $item->id }}" class="qty-input" value="{{ $qty }}" min="1" onchange="updateItemQty({{ $item->id }}, this.value)">
                                        <button type="button" class="qty-btn" onclick="stepQty({{ $item->id }}, 1)">+</button>
                                    </div>
                                </div>

                                <!-- Price Stepper -->
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small font-weight-bold">Price</span>
                                    <div class="price-counter-box">
                                        <button type="button" class="qty-btn" onclick="stepPrice({{ $item->id }}, -50)">-</button>
                                        <input type="number" id="price-input-{{ $item->id }}" class="price-input-step" value="{{ (int)$rate }}" onchange="updateItemPrice({{ $item->id }}, this.value)">
                                        <button type="button" class="qty-btn" onclick="stepPrice({{ $item->id }}, 50)">+</button>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="d-flex align-items-center gap-2 ms-auto">
                                    <span class="text-muted small font-weight-bold">Total</span>
                                    <span class="total-price-text" id="item-subtotal-{{ $item->id }}">{{ number_format($subtotal, 2, '.', '') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">No items in this order.</div>
                    @endforelse
                </div>

                <!-- Attribution Meta Container Box -->
                <div class="wc-attribution-box mt-3">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 11px; font-weight: 600;">Wc order attribution device type:</div>
                        <div style="font-size: 12.5px; font-weight: 700; color: var(--wc-text-heading);">{{ $order->device_type ?? 'Mobile' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 11px; font-weight: 600;">Wc order attribution referrer:</div>
                        <div style="font-size: 12.5px; font-weight: 700; color: #38bdf8; word-break: break-all;">
                            <a href="http://m.facebook.com/" target="_blank" style="color: #38bdf8; text-decoration: none;">http://m.facebook.com/</a>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 11px; font-weight: 600;">Wc order attribution session entry:</div>
                        <div style="font-size: 12px; font-weight: 600; color: var(--wc-text-heading); word-break: break-all;">
                            {{ $order->session_entry ?? ('https://' . (request()->getHost() ?: 'masudoff.com') . '/product/' . ($order->invoiceItems->first()?->product?->slug ?: '4k-ultra-hd-action-camera-with-tripod-free') . '/?') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ORDER TOTAL & APPROVE Section Form -->
            <form action="{{ route('woocommerce.orders.approve', $order->id) }}" method="POST" id="approveOrderForm">
                @csrf

                <!-- ORDER TOTAL Section -->
                <div class="wc-section-card mb-4">
                    <div class="section-title mb-3">ORDER TOTAL</div>

                    <div class="row g-2">
                        <!-- Discount -->
                        <div class="col-6 col-sm col-md">
                            <label class="wc-total-label">Discount</label>
                            <input type="number" name="discount_amount" id="orderDiscountInput" class="wc-total-box-input" value="{{ (int)$discountAmount }}" min="0" placeholder="0">
                        </div>

                        <!-- Advance -->
                        <div class="col-6 col-sm col-md">
                            <label class="wc-total-label">Advance</label>
                            <input type="number" name="total_paid" id="orderAdvanceInput" class="wc-total-box-input" value="{{ (int)($order->total_paid ?? 0) }}" min="0" placeholder="0">
                        </div>

                        <!-- Sub Total -->
                        <div class="col-6 col-sm col-md">
                            <label class="wc-total-label">Sub Total</label>
                            <input type="number" id="orderSubTotalInput" class="wc-total-box-input" value="{{ (int)$subtotalAmount }}" readonly>
                        </div>

                        <!-- Delivery Charge -->
                        <div class="col-6 col-sm col-md">
                            <label class="wc-total-label">Delivery Charge</label>
                            <input type="number" name="delivery_charge" id="orderDeliveryChargeInput" class="wc-total-box-input" value="{{ (int)$deliveryCharge }}" min="0" placeholder="0">
                        </div>

                        <!-- Grand Total -->
                        <div class="col-12 col-sm col-md">
                            <label class="wc-total-label" style="color: #ef4444; font-weight: 800;">Grand Total</label>
                            <div id="orderGrandTotalBox" class="wc-total-box-grand">
                                <span id="grandTotalDisplayVal">{{ number_format($grandTotal, 0, '.', '') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- APPROVE & MOVE TO ONLINE SALE LIST Card -->
                <div class="wc-section-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0 d-flex align-items-center gap-2">
                            <i class="fa fa-check-circle" style="color: var(--wc-text-heading);"></i>
                            <span>APPROVE & MOVE TO ONLINE SALE LIST</span>
                        </div>
                        <span class="badge" style="background: #f59e0b; color: #ffffff; font-weight: 700; padding: 5px 12px; border-radius: 6px; font-size: 12px; letter-spacing: 0.3px;">
                            Target: Processing Tab
                        </span>
                    </div>

                    <div class="row g-3 align-items-end">
                        <!-- Stock Mode -->
                        <div class="col-md-4">
                            <label class="wc-label mb-1" style="font-size: 12.5px; font-weight: 700;">
                                <i class="fa fa-cubes text-primary me-1"></i> Stock Mode:
                            </label>
                            <select name="stock_mode" id="approveStockMode" class="wc-select" style="height: 44px; font-weight: 500;">
                                <option value="without_manage" {{ ($order->stock_mode ?? '') == 'without_manage' ? 'selected' : '' }}>Without Manage Stock (Direct Transfer)</option>
                                <option value="with_manage" {{ ($order->stock_mode ?? '') == 'with_manage' ? 'selected' : '' }}>With Manage Stock (Check & Deduct POS Stock)</option>
                            </select>
                        </div>

                        <!-- Courier Service -->
                        <div class="col-md-4">
                            <label class="wc-label mb-1" style="font-size: 12.5px; font-weight: 700;">
                                <i class="fa fa-truck text-info me-1"></i> Courier Service:
                            </label>
                            <select name="courier_type" id="approveCourierType" class="wc-select" style="height: 44px; font-weight: 500; border-color: #8b5cf6;">
                                @foreach($courierList as $cKey => $cVal)
                                    <option value="{{ $cKey }}" {{ (strtolower($order->courier_type ?? 'steadfast') == strtolower($cKey)) ? 'selected' : '' }}>
                                        {{ $cVal }} Courier
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Button -->
                        <div class="col-md-4">
                            <button type="submit" id="btnApproveOrderBig" class="btn btn-success font-weight-bold w-100 d-flex align-items-center justify-content-center gap-2" style="background: #059669; border-color: #059669; height: 44px; border-radius: 8px; font-size: 15px; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3);" onclick="return confirm('Approve order #{{ $order->invoice_no }} ({{ number_format($grandTotal, 2) }}৳) and transfer to Online Sale List (Processing)?')">
                                <i class="fa fa-check-circle"></i> Approve & Move (<span id="btnApproveAmountText">{{ number_format($grandTotal, 2) }}৳</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column: Order Summary & Actions -->
        <div class="col-lg-4">
            <!-- ORDER SUMMARY Card -->
            <div class="wc-section-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0">ORDER SUMMARY</div>
                    <span class="text-muted small font-weight-bold">#{{ $orderDisplayId }}</span>
                </div>

                <div class="summary-info-grid mb-3">
                    <div class="summary-info-item">
                        <span class="label">DATE</span>
                        <span class="value">{{ $orderDateFormatted }}</span>
                    </div>
                    <div class="summary-info-item">
                        <span class="label">STATUS</span>
                        <span class="value text-warning fw-bold">{{ $order->order_status ?? 'Processing' }}</span>
                    </div>
                    <div class="summary-info-item">
                        <span class="label">PAYMENT</span>
                        <span class="value">COD</span>
                    </div>
                    <div class="summary-info-item">
                        <span class="label">SOURCE</span>
                        <span class="value text-info fw-bold"><i class="fab fa-facebook me-1"></i> Facebook</span>
                    </div>
                </div>

                <div class="pricing-breakdown-list py-2 border-top-dynamic border-bottom-dynamic mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted font-weight-bold">Subtotal</span>
                        <span class="fw-bold" style="color: var(--wc-text-heading);" id="summarySubtotal">৳{{ number_format($subtotalAmount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted font-weight-bold">Delivery</span>
                        <span class="fw-bold" style="color: var(--wc-text-heading);" id="summaryDelivery">৳{{ number_format($deliveryCharge, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top-dynamic">
                        <span class="fw-bold fs-6" style="color: var(--wc-text-heading);">Total</span>
                        <span class="grand-total-highlight" id="summaryGrandTotal">৳{{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>

                <!-- Customer Meta / Tracking -->
                <div class="tracking-meta-box">
                    <div class="tracking-row">
                        <span class="text-muted font-weight-bold">IP:</span>
                        <span class="font-weight-bold" style="color: var(--wc-text-heading);">108.234.202.12</span>
                    </div>
                    <div class="tracking-row">
                        <span class="text-muted font-weight-bold">Mobile:</span>
                        <span class="font-weight-bold" style="color: var(--wc-text-heading);">{{ $custPhone ?: '01331763235' }}</span>
                    </div>
                    <div class="tracking-row">
                        <span class="text-muted font-weight-bold">Device:</span>
                        <span class="font-weight-bold" style="color: var(--wc-text-heading);">Not available</span>
                    </div>
                </div>
            </div>

            <!-- ORDER ITEMS Mini Preview -->
            <div class="wc-section-card mb-4">
                <div class="section-title">ORDER ITEMS</div>
                <div class="mini-order-items-list mb-3">
                    @foreach($order->invoiceItems as $miniItem)
                        @php
                            $mProd = $miniItem->product;
                            $mImg = $mProd?->images;
                            if ($mImg && !str_starts_with($mImg, 'http')) {
                                $mImg = asset($mImg);
                            }
                            if (!$mImg) {
                                $mImg = asset('backend/images/no_images.png');
                            }
                            $mSku = $mProd?->barcode ?: ($mProd?->woocommerce_product_id ?: '12930');
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <img src="{{ $mImg }}" alt="Product" class="mini-thumb" onerror="this.src='{{ asset('backend/images/no_images.png') }}'">
                            <span class="sku-badge">{{ $mSku }}</span>
                            <span class="small font-weight-bold" style="color: var(--wc-text-heading);">৳{{ number_format($miniItem->rate) }}</span>
                            <span class="text-muted small ms-auto font-weight-bold">{{ (int)($miniItem->main_qty ?? 1) }}x</span>
                        </div>
                    @endforeach
                </div>

                <div class="attribution-box">
                    <div class="text-muted small mb-1">Wo order attribution device type: <strong style="color: var(--wc-text-heading);">Mobile</strong></div>
                    <div class="text-muted small mb-1">Wo order attribution referrer: <a href="http://m.facebook.com/" target="_blank" class="text-primary font-weight-bold">http://m.facebook.com/</a></div>
                    <div class="text-muted small">Wo order attribution session count: <strong style="color: var(--wc-text-heading);">3</strong></div>
                </div>
            </div>

            <!-- ORDER TAGS -->
            <div class="wc-section-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">ORDER TAGS</div>
                    <button type="button" class="btn-tag-add" title="Add Tag"><i class="fa fa-plus"></i></button>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge border" style="background: var(--wc-btn-outline-bg); color: var(--wc-text-heading); border-color: var(--wc-border) !important;">Main</span>
                    <span class="badge border" style="background: var(--wc-btn-outline-bg); color: var(--wc-text-heading); border-color: var(--wc-border) !important;">Urgent</span>
                </div>
            </div>

            <!-- ORDER ACTIONS Card -->
            <div class="wc-section-card mb-4">
                <div class="section-title">ORDER ACTIONS</div>

                <form id="orderStatusForm" action="{{ route('woocommerce.orders.updateStatus', $order->id) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="d-flex gap-2 mb-3">
                        <select name="order_status" id="changeStatusSelect" class="wc-select flex-grow-1">
                            <option value="" disabled selected>Change status</option>
                            @foreach($statusList as $sKey => $sVal)
                                <option value="{{ $sKey }}" {{ (strtolower($order->order_status ?? '') == strtolower($sKey)) ? 'selected' : '' }}>
                                    {{ $sVal }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-action-green">Update</button>
                    </div>
                </form>

                <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('woocommerce.orders.list') }}" class="btn border w-100 text-center font-weight-bold" style="background: var(--wc-btn-outline-bg); color: var(--wc-btn-outline-text); border-color: var(--wc-border) !important;">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                    <a href="{{ route('printNewInvoice', $order->unique_id) }}" target="_blank" class="btn border w-100 text-center font-weight-bold" style="background: var(--wc-btn-outline-bg); color: var(--wc-btn-outline-text); border-color: var(--wc-border) !important;">
                        <i class="fa fa-print me-1"></i> Invoice
                    </a>
                </div>

                <!-- Add Note Form -->
                <form id="orderNoteForm" action="{{ route('woocommerce.orders.addNote', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <textarea name="note" id="orderNoteInput" class="wc-textarea" rows="3" placeholder="Enter order note..." required></textarea>
                    </div>
                    <button type="submit" id="btnAddNote" class="btn border w-100 font-weight-bold" style="background: var(--wc-btn-outline-bg); color: var(--wc-btn-outline-text); border-color: var(--wc-border) !important;">
                        <i class="fa fa-comment-alt me-1"></i> Add Note
                    </button>
                </form>

                @php
                    $cleanOrderNote = trim(preg_replace([
                        '/^Imported from WooCommerce[^\n]*\r?\n?/mi',
                        '/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Status changed[^\n]*\r?\n?/mi',
                        '/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Order approved[^\n]*\r?\n?/mi',
                        '/^\d{1,2} [A-Za-z]+, \d{1,2}:\d{2} [AP]M: Scanned & updated[^\n]*\r?\n?/mi'
                    ], '', $order->note ?? ''));
                @endphp
                @if(!empty($cleanOrderNote))
                    <div class="existing-notes-box mt-3 p-2 border-top-dynamic">
                        <small class="text-muted font-weight-bold d-block mb-1">Previous Notes:</small>
                        <div class="small" style="white-space: pre-wrap; color: var(--wc-text-heading);" id="orderNotesDisplay">{!! nl2br(e($cleanOrderNote)) !!}</div>
                    </div>
                @endif
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

// Shipping note char counter
$('#shippingNoteInput').on('input', function() {
    var len = $(this).val().length;
    $('#shippingNoteCharCount').text(len + '/350');
});

// Quantity and Price controls
function stepQty(itemId, change) {
    var input = $('#qty-input-' + itemId);
    var cur = parseInt(input.val()) || 1;
    var next = Math.max(1, cur + change);
    input.val(next);
    updateItemQty(itemId, next);
}

function stepPrice(itemId, change) {
    var input = $('#price-input-' + itemId);
    var cur = parseFloat(input.val()) || 0;
    var next = Math.max(0, cur + change);
    input.val(next);
    updateItemPrice(itemId, next);
}

function updateItemQty(itemId, qty) {
    var price = parseFloat($('#price-input-' + itemId).val()) || 0;
    sendItemUpdate(itemId, qty, price);
}

function updateItemPrice(itemId, price) {
    var qty = parseFloat($('#qty-input-' + itemId).val()) || 1;
    sendItemUpdate(itemId, qty, price);
}

function recalculateOrderTotal() {
    var subtotal = parseFloat($('#orderSubTotalInput').val()) || 0;
    var discount = parseFloat($('#orderDiscountInput').val()) || 0;
    var delivery = parseFloat($('#orderDeliveryChargeInput').val()) || 0;
    var advance = parseFloat($('#orderAdvanceInput').val()) || 0;

    var grandTotal = Math.max(0, subtotal + delivery - discount);

    $('#grandTotalDisplayVal').text(grandTotal.toFixed(0));
    $('#btnApproveAmountText').text(grandTotal.toFixed(2) + '৳');
    $('#summaryDelivery').text('৳' + delivery.toFixed(2));
    $('#summaryGrandTotal').text('৳' + grandTotal.toFixed(2));
}

// Bind real-time input change on order totals
$('#orderDiscountInput, #orderDeliveryChargeInput, #orderAdvanceInput').on('input change keyup', function() {
    recalculateOrderTotal();
});

// Sync courier type with approve form
$('#courierTypeSelect').on('change', function() {
    $('#approveCourierType').val($(this).val());
});

function sendItemUpdate(itemId, qty, price) {
    $.ajax({
        url: "{{ route('woocommerce.orders.updateItem', $order->id) }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            item_id: itemId,
            qty: qty,
            rate: price
        },
        success: function(res) {
            if (res.success) {
                $('#item-subtotal-' + itemId).text(parseFloat(res.item_subtotal).toFixed(2));
                $('#orderSubTotalInput').val(parseInt(res.items_subtotal));
                $('#summarySubtotal').text('৳' + parseFloat(res.items_subtotal).toFixed(2));
                recalculateOrderTotal();
            }
        },
        error: function() {
            alert('Failed to update product');
        }
    });
}

function removeOrderItem(itemId) {
    if (!confirm('Are you sure you want to remove this product from the order?')) return;

    $.ajax({
        url: "{{ url('/woocommerce/orders/' . $order->id . '/item') }}/" + itemId,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            _method: "DELETE"
        },
        success: function(res) {
            if (res.success) {
                $('#order-item-row-' + itemId).fadeOut(300, function() { $(this).remove(); });
                $('#orderSubTotalInput').val(parseInt(res.items_subtotal));
                $('#summarySubtotal').text('৳' + parseFloat(res.items_subtotal).toFixed(2));
                var curCount = parseInt($('#orderedProductsCount').text()) || 1;
                $('#orderedProductsCount').text(Math.max(0, curCount - 1));
                recalculateOrderTotal();
            }
        },
        error: function() {
            alert('Failed to remove product');
        }
    });
}

// Live product search
var searchTimer;
$('#liveProductSearchInput').on('keyup', function() {
    clearTimeout(searchTimer);
    var query = $(this).val().trim();
    if (query.length < 1) {
        $('#productSearchResultsDropdown').hide().html('');
        return;
    }

    searchTimer = setTimeout(function() {
        $.get("{{ route('woocommerce.ajax.productSearch') }}", { q: query }, function(products) {
            if (!products || products.length === 0) {
                $('#productSearchResultsDropdown').html('<div class="p-3 text-muted text-center small">No products found</div>').show();
                return;
            }

            var html = '';
            $.each(products, function(i, p) {
                html += '<div class="product-search-item" onclick="addProductToOrder(' + p.id + ')">' +
                    '<img src="' + p.image + '" class="mini-thumb" onerror="this.src=\'{{ asset("backend/images/no_images.png") }}\'">' +
                    '<div class="flex-grow-1">' +
                        '<div class="small fw-bold" style="color: var(--wc-text-heading);">' + p.name + '</div>' +
                        '<div class="text-muted small"><span class="sku-badge me-1">' + p.sku + '</span> ৳' + p.price + ' | Stock: ' + p.stock + '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-purple-save py-1 px-2"><i class="fa fa-plus"></i></button>' +
                '</div>';
            });

            $('#productSearchResultsDropdown').html(html).show();
        });
    }, 250);
});

function addProductToOrder(productId) {
    $('#productSearchResultsDropdown').hide();
    $('#liveProductSearchInput').val('');

    $.ajax({
        url: "{{ route('woocommerce.orders.addItem', $order->id) }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            product_id: productId,
            qty: 1
        },
        success: function(res) {
            if (res.success) {
                location.reload();
            }
        },
        error: function() {
            alert('Failed to add product to order');
        }
    });
}

$(document).on('click', function(e) {
    if (!$(e.target).closest('#liveProductSearchInput, #productSearchResultsDropdown').length) {
        $('#productSearchResultsDropdown').hide();
    }
});

// Save order main form via AJAX
$('#orderMainForm').on('submit', function(e) {
    e.preventDefault();
    var $btn = $('#btnSaveOrderDetails');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save');
            alert(res.message || 'Saved successfully');
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save');
            alert('Failed to save order details');
        }
    });
});

// Add Note AJAX
$('#orderNoteForm').on('submit', function(e) {
    e.preventDefault();
    var note = $('#orderNoteInput').val().trim();
    if (!note) return;

    var $btn = $('#btnAddNote');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Adding...');

    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            note: note
        },
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-comment-alt me-1"></i> Add Note');
            $('#orderNoteInput').val('');
            if (res.note) {
                let cleanedResNote = (res.note || '')
                    .replace(/^Imported from WooCommerce[^\n]*\r?\n?/gmi, '')
                    .replace(/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Status changed[^\n]*\r?\n?/gmi, '')
                    .replace(/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Order approved[^\n]*\r?\n?/gmi, '')
                    .replace(/^\d{1,2} [A-Za-z]+, \d{1,2}:\d{2} [AP]M: Scanned & updated[^\n]*\r?\n?/gmi, '')
                    .trim();
                $('#orderNotesDisplay').text(cleanedResNote);
            }
            alert('Note added successfully!');
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-comment-alt me-1"></i> Add Note');
            alert('Failed to add note');
        }
    });
});

// Update Order Status via AJAX
$('#orderStatusForm').on('submit', function(e) {
    e.preventDefault();
    var $btn = $(this).find('button[type="submit"]');
    var newStatus = $('#changeStatusSelect').val();
    if (!newStatus) {
        alert('Please select a status first.');
        return;
    }

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Updating...');

    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(res) {
            $btn.prop('disabled', false).html('Update');
            if (res.success) {
                alert(res.message || ('Order status updated to ' + newStatus + ' and synced to WordPress!'));
                location.reload();
            } else {
                alert(res.message || 'Status update failed.');
            }
        },
        error: function() {
            $btn.prop('disabled', false).html('Update');
            alert('Failed to update status. Please try again.');
        }
    });
});
</script>
@endsection
