@extends('backend.layouts.master')

@section('title', 'Search Orders')

@section('content')
<style>
:root {
    --sr-bg: #0d0a1a;
    --sr-surface: #161228;
    --sr-surface-alt: #1e1935;
    --sr-surface-hover: #262042;
    --sr-border: #2d264f;
    --sr-purple: #9333ea;
    --sr-purple-light: #a855f7;
    --sr-purple-glow: rgba(147, 51, 234, 0.25);
    --sr-text-heading: #f8fafc;
    --sr-text-body: #94a3b8;
    --sr-text-muted: #64748b;
    --sr-input-bg: #130f24;
    --sr-input-border: #3b3366;
}

body:not(.dark-theme) {
    --sr-bg: #f8fafc;
    --sr-surface: #ffffff;
    --sr-surface-alt: #f1f5f9;
    --sr-surface-hover: #e2e8f0;
    --sr-border: #e2e8f0;
    --sr-text-heading: #0f172a;
    --sr-text-body: #334155;
    --sr-text-muted: #64748b;
    --sr-input-bg: #ffffff;
    --sr-input-border: #cbd5e1;
    --sr-purple-glow: rgba(147, 51, 234, 0.12);
}

.sr-container {
    background: transparent;
    color: var(--sr-text-body);
}

/* Header */
.sr-header-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--sr-text-heading);
    letter-spacing: -0.02em;
    margin-bottom: 2px;
}
.sr-header-subtitle {
    font-size: 13.5px;
    color: var(--sr-text-muted);
}

.sr-btn-guide {
    background: var(--sr-surface);
    border: 1px solid var(--sr-border);
    color: var(--sr-text-heading);
    font-size: 13px;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 10px;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.sr-btn-guide:hover {
    background: var(--sr-surface-hover);
    color: var(--sr-purple-light);
    border-color: var(--sr-purple);
}

/* Search Card matching screenshot */
.sr-search-card {
    background: var(--sr-surface);
    border: 1px solid var(--sr-border);
    border-radius: 14px;
    padding: 20px 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
}

/* Filter Field Tabs */
.sr-field-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    border-bottom: 1px solid var(--sr-border);
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.sr-field-tab {
    background: transparent;
    border: none;
    color: var(--sr-text-muted);
    font-size: 13.5px;
    font-weight: 600;
    padding: 4px 2px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    text-decoration: none;
}
.sr-field-tab:hover {
    color: var(--sr-text-heading);
}
.sr-field-tab.active {
    color: var(--sr-purple-light);
}
.sr-field-tab.active::after {
    content: '';
    position: absolute;
    bottom: -13px;
    left: 0;
    right: 0;
    height: 2.5px;
    background: #9333ea;
    border-radius: 2px;
}

/* Glowing Search Input Bar */
.sr-search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.sr-search-icon {
    position: absolute;
    left: 18px;
    color: var(--sr-text-muted);
    font-size: 17px;
    pointer-events: none;
    transition: color 0.2s;
}
.sr-search-input {
    width: 100%;
    background: var(--sr-input-bg);
    border: 1.5px solid var(--sr-input-border);
    border-radius: 10px;
    color: var(--sr-text-heading);
    font-size: 15px;
    font-weight: 500;
    padding: 14px 45px 14px 48px;
    transition: all 0.2s;
    outline: none;
}
.sr-search-input:focus {
    border-color: #9333ea;
    box-shadow: 0 0 0 4px var(--sr-purple-glow);
}
.sr-search-input:focus + .sr-search-icon {
    color: var(--sr-purple-light);
}
.sr-search-input::placeholder {
    color: var(--sr-text-muted);
}
.sr-search-clear-btn {
    position: absolute;
    right: 16px;
    background: transparent;
    border: none;
    color: var(--sr-text-muted);
    cursor: pointer;
    font-size: 14px;
    padding: 4px 6px;
}
.sr-search-clear-btn:hover {
    color: var(--sr-text-heading);
}

/* Live Instant Dropdown */
.sr-live-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--sr-surface);
    border: 1px solid var(--sr-border);
    border-radius: 12px;
    margin-top: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    z-index: 1050;
    max-height: 420px;
    overflow-y: auto;
    display: none;
}
.sr-live-item {
    padding: 12px 18px;
    border-bottom: 1px solid var(--sr-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
    color: var(--sr-text-body);
    transition: background 0.15s;
}
.sr-live-item:last-child {
    border-bottom: none;
}
.sr-live-item:hover {
    background: var(--sr-surface-hover);
    color: var(--sr-text-heading);
}

/* Result Table Styles */
.sr-table-card {
    background: var(--sr-surface);
    border: 1px solid var(--sr-border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}
.sr-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.sr-table th {
    background: var(--sr-surface-alt);
    color: var(--sr-text-muted);
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--sr-border);
}
.sr-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--sr-border);
    color: var(--sr-text-heading);
    font-size: 13.5px;
    vertical-align: middle;
}
.sr-table tbody tr:hover td {
    background: var(--sr-surface-hover);
}

/* Product thumb & badge */
.product-thumb-box {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    background: var(--sr-surface-alt);
    border: 1px solid var(--sr-border);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.product-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.sku-badge {
    background: rgba(147, 51, 234, 0.15);
    color: #a855f7;
    font-weight: 700;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
}
.btn-open-order {
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
    color: #ffffff !important;
    border: 1px solid #7c3aed;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
}
.btn-open-order:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px var(--sr-purple-glow);
}
.circular-chart-svg {
    width: 38px;
    height: 38px;
}
.circle-bg {
    fill: none;
    stroke: var(--sr-border);
    stroke-width: 3.5;
}
.circle-bar {
    fill: none;
    stroke-width: 3.5;
    stroke-linecap: round;
}
</style>

<div class="sr-container py-3">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="sr-header-title">{{ __('Search Orders') }}</h1>
            <p class="sr-header-subtitle mb-0">{{ __('Search by Invoice, Phone Number, Customer Name, Courier Tracking ID, or Payment Transaction') }}</p>
        </div>
        <div>
            <button type="button" class="sr-btn-guide" data-bs-toggle="modal" data-bs-target="#searchGuideModal" data-toggle="modal" data-target="#searchGuideModal">
                <i class="fa fa-book-open text-muted"></i> {{ __('Guide') }}
            </button>
        </div>
    </div>

    <!-- Main Search Card matching user mockup -->
    <div class="sr-search-card">
        <!-- Field Tabs Bar -->
        <div class="sr-field-tabs">
            <a href="javascript:void(0)" class="sr-field-tab {{ ($field ?? 'all') === 'all' ? 'active' : '' }}" onclick="selectSearchField('all', 'Search by invoice, phone, name, courier tracking ID, or payment transaction...')">
                {{ __('All Fields') }}
            </a>
            <a href="javascript:void(0)" class="sr-field-tab {{ ($field ?? '') === 'invoice' ? 'active' : '' }}" onclick="selectSearchField('invoice', 'Search by Invoice #, WooCommerce Order ID, or Unique ID...')">
                {{ __('Invoice') }}
            </a>
            <a href="javascript:void(0)" class="sr-field-tab {{ ($field ?? '') === 'mobile' ? 'active' : '' }}" onclick="selectSearchField('mobile', 'Search by Customer Mobile / Phone Number...')">
                {{ __('Mobile Number') }}
            </a>
            <a href="javascript:void(0)" class="sr-field-tab {{ ($field ?? '') === 'name' ? 'active' : '' }}" onclick="selectSearchField('name', 'Search by Customer Name...')">
                {{ __('Customer Name') }}
            </a>
            <a href="javascript:void(0)" class="sr-field-tab {{ ($field ?? '') === 'courier' ? 'active' : '' }}" onclick="selectSearchField('courier', 'Search by Courier Tracking ID / Consignment ID...')">
                {{ __('Courier Tracking ID') }}
            </a>
            <a href="javascript:void(0)" class="sr-field-tab {{ ($field ?? '') === 'transaction' ? 'active' : '' }}" onclick="selectSearchField('transaction', 'Search by Payment Reference / Transaction ID...')">
                {{ __('Payment Transaction') }}
            </a>
        </div>

        <!-- Search Form -->
        <form id="orderSearchForm" action="{{ route('woocommerce.search') }}" method="GET">
            <input type="hidden" name="field" id="searchFieldInput" value="{{ $field ?? 'all' }}">
            
            <div class="sr-search-input-wrapper">
                <i class="fa fa-search sr-search-icon"></i>
                <input type="text" 
                       name="q" 
                       id="mainSearchInput" 
                       class="sr-search-input" 
                       value="{{ $queryText ?? '' }}" 
                       placeholder="{{ ($field ?? 'all') === 'all' ? 'Search by invoice, phone, name, courier tracking ID, or payment transaction...' : 'Search by ' . ucfirst($field ?? 'all') . '...' }}" 
                       autofocus 
                       autocomplete="off">
                @if(!empty($queryText))
                    <a href="{{ route('woocommerce.search') }}" class="sr-search-clear-btn" title="Clear Search">
                        <i class="fa fa-times"></i>
                    </a>
                @endif

                <!-- Instant Live Search Dropdown -->
                <div class="sr-live-dropdown" id="liveSearchDropdown"></div>
            </div>
        </form>
    </div>

    <!-- Search Results Section -->
    @if($hasSearched)
        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
            <div class="fw-bold" style="color: var(--sr-text-heading); font-size: 15px;">
                <i class="fa fa-search text-purple me-1"></i> Found {{ $orders->total() }} order(s) for "<span class="text-purple">{{ $queryText }}</span>"
            </div>
            @if($orders->total() > 0)
                <div class="text-muted small">Showing {{ $orders->firstItem() }} - {{ $orders->lastItem() }} of {{ $orders->total() }}</div>
            @endif
        </div>

        @if($orders->count() > 0)
            <div class="sr-table-card mb-4">
                <div class="table-responsive">
                    <table class="sr-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order / Date</th>
                                <th>Customer</th>
                                <th>Note</th>
                                <th>Order Items</th>
                                <th>Status</th>
                                <th>Success Rate</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $custPhone = $order->customer->phone ?? '';
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
                                    $waPhone = str_starts_with($cleanPhone, '88') ? $cleanPhone : ('88' . $cleanPhone);
                                    $itemCount = $order->invoiceItems->count();
                                    $firstItem = $order->invoiceItems->first();
                                    $product = $firstItem?->product;
                                    
                                    $productImage = $product?->images ?: 'backend/images/no_images.png';
                                    if (!str_starts_with($productImage, 'http')) {
                                        $productImage = asset($productImage);
                                    }

                                    $sku = $product?->barcode ?: ($product?->woocommerce_product_id ?: ($firstItem?->new_details ?: '12930'));
                                    $itemPrice = $firstItem?->rate ?? $order->total_amount;
                                    $itemQty = (int)($firstItem?->main_qty ?? 1);

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

                                    $formattedDate = date('d M, h:i a', strtotime($order->created_at ?: $order->date));
                                    $orderDisplayId = $order->woocommerce_order_id ?: ($order->invoice_no ?: $order->id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: var(--sr-text-heading);">{{ $formattedDate }}</div>
                                        <div class="text-purple small fw-bold">ID: #{{ $orderDisplayId }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold" style="color: var(--sr-text-heading);">{{ $order->customer->name ?? 'Customer' }}</div>
                                        @if(!empty($custPhone))
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <a href="tel:{{ $custPhone }}" class="text-info small text-decoration-none"><i class="fa fa-phone-alt me-1"></i>{{ $custPhone }}</a>
                                                <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="text-success small"><i class="fab fa-whatsapp"></i></a>
                                            </div>
                                        @endif
                                        @if(!empty($order->customer->address))
                                            <div class="text-muted small text-truncate mt-1" style="max-width: 200px;">{{ $order->customer->address }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-muted small" style="max-width: 150px;">
                                            @php
                                                $cleanSearchNote = trim(preg_replace([
                                                    '/^Imported from WooCommerce[^\n]*\r?\n?/mi',
                                                    '/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Status changed[^\n]*\r?\n?/mi',
                                                    '/^\[\d+ [A-Za-z]+, \d+:\d+ [AP]M\] Order approved[^\n]*\r?\n?/mi',
                                                    '/^\d{1,2} [A-Za-z]+, \d{1,2}:\d{2} [AP]M: Scanned & updated[^\n]*\r?\n?/mi'
                                                ], '', $order->note ?? ''));
                                            @endphp
                                            {{ $cleanSearchNote ? Str::limit($cleanSearchNote, 30) : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="product-thumb-box">
                                                <img src="{{ $productImage }}" alt="Product" class="product-thumb" onerror="this.src='{{ asset('backend/images/no_images.png') }}'">
                                            </div>
                                            <div>
                                                <span class="sku-badge">{{ $sku }}</span>
                                                <span class="small fw-bold ms-1" style="color: var(--sr-text-heading);">{{ $itemQty }}x</span>
                                                <div class="text-success fw-bold small mt-1">৳{{ number_format($itemPrice) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-purple px-2 py-1">{{ $order->order_status ?: 'Processing' }}</span>
                                        @if($order->courier_type)
                                            <div class="text-muted small mt-1"><i class="fa fa-truck me-1"></i>{{ $order->courier_type }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 36px; height: 36px;">
                                                <svg viewBox="0 0 36 36" class="circular-chart-svg">
                                                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                    <path class="circle-bar" style="stroke: {{ $rateColor }} !important;" stroke-dasharray="{{ $successRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="{{ $rateTextClass }} small">{{ $successRate }}%</div>
                                                <div class="text-muted small" style="font-size: 11px;">{{ $deliveredCount }}/{{ $totalOrdersCount }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('woocommerce.orders.details', $order->id) }}" class="btn-open-order">
                                            Open <i class="fa fa-external-link-alt ms-1 small"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @else
            <div class="sr-table-card p-5 text-center">
                <i class="fa fa-search-minus fa-3x text-muted mb-3 d-block"></i>
                <h5 class="fw-bold" style="color: var(--sr-text-heading);">No orders found matching "{{ $queryText }}"</h5>
                <p class="text-muted small mb-0">Please check for typos or try searching with a different field (e.g. mobile number, invoice #, or customer name).</p>
            </div>
        @endif
    @else
        <!-- Empty State Prompt -->
        <div class="sr-table-card p-5 text-center">
            <div class="mb-3">
                <span class="d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; border-radius: 50%; background: var(--sr-surface-alt); border: 2px dashed var(--sr-border);">
                    <i class="fa fa-search fa-2x text-purple"></i>
                </span>
            </div>
            <h5 class="fw-bold mb-2" style="color: var(--sr-text-heading);">Ready to Search Orders</h5>
            <p class="text-muted small mx-auto" style="max-width: 500px;">
                Enter an <strong>Invoice #</strong>, <strong>Customer Phone Number</strong>, <strong>Name</strong>, or <strong>Courier Tracking ID</strong> in the search bar above to instantly find any order.
            </p>
        </div>
    @endif
</div>

<!-- Search Guide Modal -->
<div class="modal fade" id="searchGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--sr-surface); border: 1px solid var(--sr-border); color: var(--sr-text-heading);">
            <div class="modal-header" style="border-bottom-color: var(--sr-border);">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-book-open text-purple me-2"></i> Search Orders Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: 13.5px; color: var(--sr-text-body); line-height: 1.6;">
                <p class="fw-bold text-purple mb-2">High-Speed Search Capabilities:</p>
                <ul class="ps-3 mb-0">
                    <li class="mb-2"><strong>All Fields:</strong> Universal search across invoices, customer phone, name, products, notes, and courier data.</li>
                    <li class="mb-2"><strong>Mobile Number:</strong> Direct lookup by 11-digit or partial customer mobile number.</li>
                    <li class="mb-2"><strong>Invoice:</strong> Fast lookup by WooCommerce Order ID (e.g. <code>#15800</code>) or POS Invoice ID.</li>
                    <li class="mb-2"><strong>Courier Tracking ID:</strong> Instant tracking by Steadfast, Pathao, RedX, or CarryBee consignment number.</li>
                </ul>
            </div>
            <div class="modal-footer" style="border-top-color: var(--sr-border);">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

<script>
function selectSearchField(fieldName, placeholderText) {
    $('#searchFieldInput').val(fieldName);
    $('.sr-field-tab').removeClass('active');
    $('a[onclick*="\'' + fieldName + '\'"]').addClass('active');
    $('#mainSearchInput').attr('placeholder', placeholderText).focus();
}

// Live debounced Ajax suggestions
var searchTimer = null;
$('#mainSearchInput').on('input', function() {
    var val = $(this).val().trim();
    var field = $('#searchFieldInput').val();

    clearTimeout(searchTimer);
    if (val.length < 2) {
        $('#liveSearchDropdown').hide().html('');
        return;
    }

    searchTimer = setTimeout(function() {
        $.ajax({
            url: "{{ route('woocommerce.search.ajax') }}",
            type: "GET",
            data: { q: val, field: field },
            success: function(res) {
                if (res.data && res.data.length > 0) {
                    var html = '';
                    res.data.forEach(function(item) {
                        html += '<a href="' + item.details_url + '" class="sr-live-item">' +
                            '<div class="d-flex align-items-center gap-3">' +
                                '<img src="' + item.product_image + '" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover;" onerror="this.src=\'{{ asset("backend/images/no_images.png") }}\'">' +
                                '<div>' +
                                    '<div class="fw-bold text-purple">#' + item.invoice_no + ' - ' + item.customer_name + '</div>' +
                                    '<div class="text-muted small">' + item.customer_phone + ' | ৳' + Number(item.total_amount).toLocaleString() + '</div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="text-end">' +
                                '<span class="badge bg-purple">' + item.status + '</span>' +
                                '<div class="text-muted small mt-1">' + item.date + '</div>' +
                            '</div>' +
                        '</a>';
                    });
                    $('#liveSearchDropdown').html(html).show();
                } else {
                    $('#liveSearchDropdown').hide().html('');
                }
            }
        });
    }, 250);
});

// Close live dropdown on outside click
$(document).on('click', function(e) {
    if (!$(e.target).closest('.sr-search-input-wrapper').length) {
        $('#liveSearchDropdown').hide();
    }
});
</script>
@endsection
