@extends('backend.layouts.master')

@section('page-title', 'WooCommerce Integration')

@push('css')
<style>
    .pulse-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulse-green {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
    .auto-sync-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1.5px solid #a7f3d0 !important;
    }
    body.dark-theme .auto-sync-card {
        background: linear-gradient(135deg, #062419 0%, #0d2e24 100%) !important;
        border: 1.5px solid #059669 !important;
    }
    .badge-timer {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 1px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold">
                <i class="fa fa-shopping-cart me-2"></i>WooCommerce Integration & Automatic Sync
            </h4>
            <p class="text-muted mb-0">Connect your website (masudoff.com) and automatically synchronize Product & Order lists seamlessly.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('woocommerce.orders.list') }}" class="btn btn-outline-primary">
                <i class="fa fa-list-alt me-1"></i> View Order List
            </a>
            <button id="btn-test-conn" class="btn btn-outline-info">
                <i class="fa fa-plug me-1"></i> Test Connection
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-0 border-start border-primary border-4 rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase font-weight-bold">Synced Products</span>
                        <h3 class="mb-0 mt-1 font-weight-bold text-primary" id="synced-products-count">{{ $syncedProductsCount }}</h3>
                        <small class="text-muted">Out of <span id="total-products-count">{{ $totalProductsCount }}</span> POS products</small>
                    </div>
                    <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa fa-box fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-0 border-start border-success border-4 rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase font-weight-bold">Synced Orders</span>
                        <h3 class="mb-0 mt-1 font-weight-bold text-success" id="synced-orders-count">{{ $syncedOrdersCount }}</h3>
                        <small class="text-muted">Out of <span id="total-orders-count">{{ $totalInvoicesCount }}</span> POS invoices</small>
                    </div>
                    <div class="bg-success text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa fa-receipt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-light h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span id="conn-status-badge" class="badge bg-warning text-dark fs-6 px-3 py-2">
                                <i class="fa fa-circle-notch fa-spin me-1"></i> Ready to Connect
                            </span>
                        </div>
                        <div>
                            <strong class="d-block">API Target Website:</strong>
                            <span class="text-muted">{{ $credentials['url'] }}</span>
                        </div>
                    </div>
                    <div>
                        <small id="last-sync-time-top" class="text-muted d-block text-end">
                            <i class="fa fa-history me-1"></i> Last Synced: <strong class="text-dark">{{ $credentials['last_synced_at'] ? date('d M, h:i A', strtotime($credentials['last_synced_at'])) : 'Never' }}</strong>
                        </small>
                    </div>
                </div>
                <div id="conn-status-msg" class="mt-2 small text-muted">Click "Test Connection" to check live WooCommerce status.</div>
            </div>
        </div>
    </div>

    <!-- Automated Real-Time Auto-Sync Engine Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card auto-sync-card shadow-sm rounded-3 p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <!-- Left: Status & Switch -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-switch fs-5 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="auto-sync-engine-toggle" {{ $credentials['auto_sync'] ? 'checked' : '' }} style="cursor: pointer; width: 3.2rem; height: 1.7rem;">
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-dot" id="auto-sync-pulse-dot"></span>
                                <h6 class="mb-0 font-weight-bold text-success" id="auto-sync-heading">
                                    {{ $credentials['auto_sync'] ? 'স্বয়ংক্রিয় সিঙ্ক চালু আছে (Auto-Sync ACTIVE)' : 'স্বয়ংক্রিয় সিঙ্ক বন্ধ (Auto-Sync PAUSED)' }}
                                </h6>
                            </div>
                            <small class="text-muted" id="auto-sync-desc">নির্দিষ্ট সময় পর পর ব্রাউজার নিজে থেকে ব্যাকগ্রাউন্ডে নতুন Order এবং Product ফেচ করবে।</small>
                        </div>
                    </div>

                    <!-- Middle: Controls (Interval & Scope) -->
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center bg-white px-2 py-1 rounded-2 border shadow-sm">
                            <span class="small font-weight-bold me-2 text-muted"><i class="fa fa-clock text-primary me-1"></i>Interval:</span>
                            <select id="auto-sync-interval-select" class="form-select form-select-sm border-0 bg-transparent fw-bold text-primary" style="width: auto; cursor: pointer;">
                                <option value="1" {{ ($credentials['auto_sync_interval'] ?? 2) == 1 ? 'selected' : '' }}>Every 1 Minute</option>
                                <option value="2" {{ ($credentials['auto_sync_interval'] ?? 2) == 2 ? 'selected' : '' }}>Every 2 Minutes</option>
                                <option value="3" {{ ($credentials['auto_sync_interval'] ?? 2) == 3 ? 'selected' : '' }}>Every 3 Minutes</option>
                                <option value="5" {{ ($credentials['auto_sync_interval'] ?? 2) == 5 ? 'selected' : '' }}>Every 5 Minutes</option>
                                <option value="10" {{ ($credentials['auto_sync_interval'] ?? 2) == 10 ? 'selected' : '' }}>Every 10 Minutes</option>
                                <option value="15" {{ ($credentials['auto_sync_interval'] ?? 2) == 15 ? 'selected' : '' }}>Every 15 Minutes</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center bg-white px-2 py-1 rounded-2 border shadow-sm">
                            <span class="small font-weight-bold me-2 text-muted"><i class="fa fa-filter text-success me-1"></i>Sync Scope:</span>
                            <select id="auto-sync-scope-select" class="form-select form-select-sm border-0 bg-transparent fw-bold text-success" style="width: auto; cursor: pointer;">
                                <option value="all" {{ ($credentials['auto_sync_scope'] ?? 'all') == 'all' ? 'selected' : '' }}>Both (Orders & Products)</option>
                                <option value="orders" {{ ($credentials['auto_sync_scope'] ?? 'all') == 'orders' ? 'selected' : '' }}>Orders Only</option>
                                <option value="products" {{ ($credentials['auto_sync_scope'] ?? 'all') == 'products' ? 'selected' : '' }}>Products Only</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right: Countdown & Quick Action -->
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white border rounded-2 px-3 py-1 text-center shadow-sm" id="countdown-wrapper">
                            <small class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase;">Next Sync In</small>
                            <span id="auto-sync-countdown" class="badge-timer text-success">02:00</span>
                        </div>
                        <button id="btn-sync-all-now" class="btn btn-success px-3 fw-bold shadow-sm" title="Trigger Instant Sync">
                            <i class="fa fa-sync me-1"></i> Sync Now
                        </button>
                    </div>
                </div>

                <!-- Live Ticker & Activity Status -->
                <div class="mt-2 pt-2 border-top border-success-subtle d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                    <div id="auto-sync-live-status" class="text-dark">
                        <i class="fa fa-info-circle text-success me-1"></i> Auto-sync engine is ready. Next periodic sync will catch all new web orders & products automatically.
                    </div>
                    <div id="auto-sync-last-log" class="text-muted fst-italic"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Section: Manual Sync Actions & Credentials Form -->
    <div class="row">
        <!-- Sync Actions Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom font-weight-bold d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-sync text-primary me-2"></i>Manual Sync Actions</span>
                    <span class="badge bg-info text-dark">Live API v3</span>
                </div>
                <div class="card-body">
                    <!-- Sync Both Option -->
                    <div class="p-3 mb-3 border border-2 border-primary rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-primary"><i class="fa fa-bolt me-2 text-warning"></i>Sync All (Products & Orders)</h6>
                                <p class="text-muted small mb-0">Fetch both new orders and latest products in a single synchronized action.</p>
                            </div>
                            <button id="btn-manual-sync-all" class="btn btn-outline-primary px-3">
                                <i class="fa fa-sync me-1"></i> Sync All
                            </button>
                        </div>
                    </div>

                    <!-- Sync Products Option -->
                    <div class="p-3 mb-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-dark"><i class="fa fa-cubes me-2 text-primary"></i>Sync Product List</h6>
                                <p class="text-muted small mb-0">Fetch products, prices, stock, images & categories from WooCommerce into POS.</p>
                            </div>
                            <button id="btn-sync-products" class="btn btn-primary px-4">
                                <i class="fa fa-download me-1"></i> Sync Products
                            </button>
                        </div>
                        <div id="product-sync-log" class="small mt-2" style="display: none;"></div>
                    </div>

                    <!-- Sync Orders Option -->
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-dark"><i class="fa fa-shopping-bag me-2 text-success"></i>Sync Order List</h6>
                                <p class="text-muted small mb-0">Import customer web orders and convert them into POS Invoices automatically.</p>
                            </div>
                            <button id="btn-sync-orders" class="btn btn-success px-4">
                                <i class="fa fa-download me-1"></i> Sync Orders
                            </button>
                        </div>
                        <div id="order-sync-log" class="small mt-2" style="display: none;"></div>
                    </div>

                    <!-- Usage Tips -->
                    <div class="mt-3 p-3 bg-white border border-info rounded-3">
                        <h6 class="text-info font-weight-bold mb-2"><i class="fa fa-lightbulb me-1"></i>Automatic Sync Features:</h6>
                        <ul class="mb-0 text-muted small ps-3">
                            <li><strong>Auto-Sync Engine</strong> runs periodically in the background as long as this dashboard or POS tab is open.</li>
                            <li>Product SKUs or WooCommerce Product IDs are matched automatically to avoid duplicates.</li>
                            <li>Web Orders automatically register customers in the POS system if they don't already exist.</li>
                            <li>Server Cron Command: <code>php artisan woocommerce:sync</code> runs automatically every 5 minutes on server scheduler.</li>
                        </ul>
                    </div>

                    <!-- Instant Real-time Webhook Setup -->
                    <div class="mt-3 p-3 bg-light border border-success rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-success font-weight-bold mb-0">
                                <i class="fa fa-bolt me-1"></i>Instant Real-Time Webhook (Recommended)
                            </h6>
                            <span class="badge bg-success">100% Real-time</span>
                        </div>
                        <p class="text-muted small mb-2">To bypass cPanel firewall limits and receive orders instantly when placed on WordPress, add this Webhook in WooCommerce:</p>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control font-monospace" id="wcWebhookUrlInput" value="{{ url('/api/woocommerce/webhook') }}" readonly style="background: #fff; font-size: 11px;">
                            <button class="btn btn-outline-success font-weight-bold" type="button" onclick="navigator.clipboard.writeText(document.getElementById('wcWebhookUrlInput').value); alert('Webhook Delivery URL copied to clipboard!');">
                                <i class="fa fa-copy me-1"></i> Copy URL
                            </button>
                        </div>
                        <small class="text-muted d-block" style="font-size: 11px;">
                            <strong>Setup in WordPress:</strong> <em>WooCommerce &gt; Settings &gt; Advanced &gt; Webhooks &gt; Add Webhook</em>.<br>
                            Set <strong>Topic</strong>: <code>Order created</code> | <strong>Delivery URL</strong>: Paste the URL above | <strong>Status</strong>: <code>Active</code>.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Credentials Settings Form -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom font-weight-bold">
                    <i class="fa fa-key text-warning me-2"></i>WooCommerce API Settings & Preferences
                </div>
                <div class="card-body">
                    <form action="{{ route('woocommerce.settings.save') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Website URL</label>
                            <input type="url" name="url" class="form-control" value="{{ old('url', $credentials['url']) }}" required placeholder="https://masudoff.com">
                            <small class="text-muted">Include https:// without trailing slash.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Consumer Key</label>
                            <input type="text" name="consumer_key" class="form-control" value="{{ old('consumer_key', $credentials['consumer_key']) }}" required placeholder="ck_xxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Consumer Secret</label>
                            <input type="text" name="consumer_secret" class="form-control" value="{{ old('consumer_secret', $credentials['consumer_secret']) }}" required placeholder="cs_xxxxxxxx">
                        </div>

                        <hr class="my-3 text-muted">

                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="auto_sync" id="auto_sync_form" value="1" {{ $credentials['auto_sync'] ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-bold" for="auto_sync_form">Enable Automatic Background Sync</label>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small">Default Auto-Sync Interval</label>
                                <select name="auto_sync_interval" class="form-select">
                                    <option value="1" {{ ($credentials['auto_sync_interval'] ?? 2) == 1 ? 'selected' : '' }}>1 Minute</option>
                                    <option value="2" {{ ($credentials['auto_sync_interval'] ?? 2) == 2 ? 'selected' : '' }}>2 Minutes (Recommended)</option>
                                    <option value="3" {{ ($credentials['auto_sync_interval'] ?? 2) == 3 ? 'selected' : '' }}>3 Minutes</option>
                                    <option value="5" {{ ($credentials['auto_sync_interval'] ?? 2) == 5 ? 'selected' : '' }}>5 Minutes</option>
                                    <option value="10" {{ ($credentials['auto_sync_interval'] ?? 2) == 10 ? 'selected' : '' }}>10 Minutes</option>
                                    <option value="15" {{ ($credentials['auto_sync_interval'] ?? 2) == 15 ? 'selected' : '' }}>15 Minutes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small">Default Sync Scope</label>
                                <select name="auto_sync_scope" class="form-select">
                                    <option value="all" {{ ($credentials['auto_sync_scope'] ?? 'all') == 'all' ? 'selected' : '' }}>Both (Orders & Products)</option>
                                    <option value="orders" {{ ($credentials['auto_sync_scope'] ?? 'all') == 'orders' ? 'selected' : '' }}>Orders Only</option>
                                    <option value="products" {{ ($credentials['auto_sync_scope'] ?? 'all') == 'products' ? 'selected' : '' }}>Products Only</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-save me-1"></i> Save API Credentials & Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document.body).ready(function() {
    // ==========================================
    // 1. Audio Notification Beep (Synthesized Web Audio API)
    // ==========================================
    function playOrderBeep() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            osc.frequency.setValueAtTime(880, ctx.currentTime + 0.15); // A5
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
            osc.start();
            osc.stop(ctx.currentTime + 0.4);
        } catch(e) {}
    }

    // ==========================================
    // 2. State & Auto-Sync Engine Variables
    // ==========================================
    var autoSyncState = {
        enabled: $('#auto-sync-engine-toggle').is(':checked'),
        intervalMinutes: parseInt($('#auto-sync-interval-select').val()) || 2,
        scope: $('#auto-sync-scope-select').val() || 'all',
        remainingSeconds: (parseInt($('#auto-sync-interval-select').val()) || 2) * 60,
        isSyncing: false,
        timerId: null
    };

    function updateUiState() {
        if (autoSyncState.enabled) {
            $('#auto-sync-heading').text('স্বয়ংক্রিয় সিঙ্ক চালু আছে (Auto-Sync ACTIVE)').removeClass('text-danger text-muted').addClass('text-success');
            $('#auto-sync-pulse-dot').show();
            $('#countdown-wrapper').removeClass('opacity-50');
            $('#auto-sync-countdown').removeClass('text-muted').addClass('text-success');
        } else {
            $('#auto-sync-heading').text('স্বয়ংক্রিয় সিঙ্ক বন্ধ (Auto-Sync PAUSED)').removeClass('text-success').addClass('text-muted');
            $('#auto-sync-pulse-dot').hide();
            $('#countdown-wrapper').addClass('opacity-50');
            $('#auto-sync-countdown').removeClass('text-success').addClass('text-muted').text('--:--');
        }
    }

    function formatTime(sec) {
        var m = Math.floor(sec / 60);
        var s = sec % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    // ==========================================
    // 3. Execution of Automated Sync
    // ==========================================
    function performAutoSync(isManual) {
        if (autoSyncState.isSyncing) return;
        autoSyncState.isSyncing = true;

        var $btnSyncAll = $('#btn-sync-all-now');
        var origBtnHtml = $btnSyncAll.html();
        $btnSyncAll.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Syncing...');

        $('#auto-sync-live-status').html('<span class="text-primary font-weight-bold"><i class="fa fa-spinner fa-spin me-1"></i> WooCommerce API থেকে ডাটা সিঙ্ক হচ্ছে... (Fetching latest data from masudoff.com)</span>');

        $.ajax({
            url: "{{ route('woocommerce.auto.sync') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                scope: autoSyncState.scope,
                product_limit: 100,
                order_limit: 50
            },
            timeout: 45000,
            success: function(res) {
                autoSyncState.isSyncing = false;
                $btnSyncAll.prop('disabled', false).html(origBtnHtml);

                if (res.success) {
                    // Update stats counters dynamically
                    if (res.syncedProductsCount !== undefined) {
                        $('#synced-products-count').text(res.syncedProductsCount);
                    }
                    if (res.syncedOrdersCount !== undefined) {
                        $('#synced-orders-count').text(res.syncedOrdersCount);
                    }
                    if (res.totalProductsCount !== undefined) {
                        $('#total-products-count').text(res.totalProductsCount);
                    }
                    if (res.totalInvoicesCount !== undefined) {
                        $('#total-orders-count').text(res.totalInvoicesCount);
                    }

                    // Format result summary
                    var ordersSummary = "";
                    if (res.orders) {
                        if (res.orders.imported > 0) {
                            ordersSummary = '<span class="badge bg-success me-1">+' + res.orders.imported + ' New Order(s) Imported</span>';
                            playOrderBeep();
                        } else {
                            ordersSummary = '<span class="text-muted">Orders checked (' + res.orders.total + ' total, no new)</span>';
                        }
                    }

                    var prodsSummary = "";
                    if (res.products) {
                        if (res.products.created > 0 || res.products.updated > 0) {
                            prodsSummary = '<span class="badge bg-primary me-1">Products: +' + res.products.created + ' created, ' + res.products.updated + ' updated</span>';
                        } else {
                            prodsSummary = '<span class="text-muted">Products checked (' + res.products.total + ' total)</span>';
                        }
                    }

                    var nowTime = new Date().toLocaleTimeString();
                    $('#auto-sync-live-status').html('<span class="text-success font-weight-bold"><i class="fa fa-check-circle me-1"></i> সফলভাবে সিঙ্ক সম্পন্ন হয়েছে! ' + ordersSummary + ' ' + prodsSummary + '</span>');
                    $('#auto-sync-last-log').html('Last auto-sync at ' + nowTime);
                    $('#last-sync-time-top strong').text(nowTime);

                } else {
                    $('#auto-sync-live-status').html('<span class="text-danger font-weight-bold"><i class="fa fa-exclamation-triangle me-1"></i> Sync Warning: ' + (res.message || 'Error occurred during sync.') + '</span>');
                }

                // Reset timer countdown
                autoSyncState.remainingSeconds = autoSyncState.intervalMinutes * 60;
                $('#auto-sync-countdown').text(formatTime(autoSyncState.remainingSeconds));
            },
            error: function(err) {
                autoSyncState.isSyncing = false;
                $btnSyncAll.prop('disabled', false).html(origBtnHtml);
                $('#auto-sync-live-status').html('<span class="text-danger"><i class="fa fa-times-circle me-1"></i> Auto-sync network or server error. Will retry on next cycle.</span>');
                autoSyncState.remainingSeconds = autoSyncState.intervalMinutes * 60;
                $('#auto-sync-countdown').text(formatTime(autoSyncState.remainingSeconds));
            }
        });
    }

    // ==========================================
    // 4. Timer Interval Loop (1 second tick)
    // ==========================================
    function startTimerLoop() {
        if (autoSyncState.timerId) clearInterval(autoSyncState.timerId);

        autoSyncState.timerId = setInterval(function() {
            if (!autoSyncState.enabled) return;

            if (autoSyncState.remainingSeconds > 0) {
                autoSyncState.remainingSeconds--;
                $('#auto-sync-countdown').text(formatTime(autoSyncState.remainingSeconds));
            } else {
                $('#auto-sync-countdown').text('00:00');
                performAutoSync(false);
            }
        }, 1000);
    }

    // Init UI and Timer
    updateUiState();
    startTimerLoop();

    // Event Handlers for Auto Sync Controls
    $('#auto-sync-engine-toggle').on('change', function() {
        autoSyncState.enabled = $(this).is(':checked');
        $('#auto_sync_form').prop('checked', autoSyncState.enabled);
        updateUiState();
        if (autoSyncState.enabled) {
            autoSyncState.remainingSeconds = autoSyncState.intervalMinutes * 60;
            $('#auto-sync-countdown').text(formatTime(autoSyncState.remainingSeconds));
        }
    });

    $('#auto-sync-interval-select').on('change', function() {
        autoSyncState.intervalMinutes = parseInt($(this).val()) || 2;
        autoSyncState.remainingSeconds = autoSyncState.intervalMinutes * 60;
        $('#auto-sync-countdown').text(formatTime(autoSyncState.remainingSeconds));
    });

    $('#auto-sync-scope-select').on('change', function() {
        autoSyncState.scope = $(this).val() || 'all';
    });

    $('#btn-sync-all-now, #btn-manual-sync-all').on('click', function() {
        performAutoSync(true);
    });

    // ==========================================
    // 5. Test Connection Handler
    // ==========================================
    $('#btn-test-conn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Testing...');
        $('#conn-status-badge').removeClass('bg-success bg-danger bg-warning').addClass('bg-warning text-dark').html('<i class="fa fa-circle-notch fa-spin me-1"></i> Testing Connection...');

        $.ajax({
            url: "{{ route('woocommerce.test') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-plug me-1"></i> Test Connection');
                if (res.success) {
                    $('#conn-status-badge').removeClass('bg-warning bg-danger').addClass('bg-success text-white').html('<i class="fa fa-check-circle me-1"></i> Connected');
                    $('#conn-status-msg').removeClass('text-danger').addClass('text-success').text(res.message);
                } else {
                    $('#conn-status-badge').removeClass('bg-warning bg-success').addClass('bg-danger text-white').html('<i class="fa fa-times-circle me-1"></i> Failed');
                    $('#conn-status-msg').removeClass('text-success').addClass('text-danger').text(res.message);
                }
            },
            error: function(err) {
                $btn.prop('disabled', false).html('<i class="fa fa-plug me-1"></i> Test Connection');
                $('#conn-status-badge').removeClass('bg-warning bg-success').addClass('bg-danger text-white').html('<i class="fa fa-times-circle me-1"></i> Error');
                $('#conn-status-msg').removeClass('text-success').addClass('text-danger').text("Server error during test connection.");
            }
        });
    });

    // ==========================================
    // 6. Manual Sync Products
    // ==========================================
    $('#btn-sync-products').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Syncing...');
        $('#product-sync-log').show().removeClass('text-danger text-success').addClass('text-info').html('<i class="fa fa-spinner fa-spin me-1"></i> Fetching products from WooCommerce...');

        $.ajax({
            url: "{{ route('woocommerce.sync.products') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", limit: 100 },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-download me-1"></i> Sync Products');
                if (res.success) {
                    $('#product-sync-log').removeClass('text-info text-danger').addClass('text-success').html('<i class="fa fa-check me-1"></i> ' + res.message);
                    if (res.total !== undefined) {
                        $('#synced-products-count').text(parseInt($('#synced-products-count').text()) + (res.created || 0));
                    }
                } else {
                    $('#product-sync-log').removeClass('text-info text-success').addClass('text-danger').html('<i class="fa fa-exclamation-triangle me-1"></i> ' + res.message);
                }
            },
            error: function(err) {
                $btn.prop('disabled', false).html('<i class="fa fa-download me-1"></i> Sync Products');
                $('#product-sync-log').removeClass('text-info text-success').addClass('text-danger').html('<i class="fa fa-times me-1"></i> Error syncing products.');
            }
        });
    });

    // ==========================================
    // 7. Manual Sync Orders
    // ==========================================
    $('#btn-sync-orders').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Syncing...');
        $('#order-sync-log').show().removeClass('text-danger text-success').addClass('text-info').html('<i class="fa fa-spinner fa-spin me-1"></i> Fetching orders from WooCommerce...');

        $.ajax({
            url: "{{ route('woocommerce.sync.orders') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", limit: 50 },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-download me-1"></i> Sync Orders');
                if (res.success) {
                    $('#order-sync-log').removeClass('text-info text-danger').addClass('text-success').html('<i class="fa fa-check me-1"></i> ' + res.message);
                    if (res.imported > 0) {
                        playOrderBeep();
                        $('#synced-orders-count').text(parseInt($('#synced-orders-count').text()) + res.imported);
                    }
                } else {
                    $('#order-sync-log').removeClass('text-info text-success').addClass('text-danger').html('<i class="fa fa-exclamation-triangle me-1"></i> ' + res.message);
                }
            },
            error: function(err) {
                $btn.prop('disabled', false).html('<i class="fa fa-download me-1"></i> Sync Orders');
                $('#order-sync-log').removeClass('text-info text-success').addClass('text-danger').html('<i class="fa fa-times me-1"></i> Error syncing orders.');
            }
        });
    });
});
</script>
@endsection
