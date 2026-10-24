@extends('backend.layouts.master')

@section('title', 'Logistics Scanner Console')

@section('content')
<style>
:root {
    --sc-bg: #0d0a1a;
    --sc-surface: #161228;
    --sc-surface-alt: #1e1935;
    --sc-surface-hover: #262042;
    --sc-border: #2d264f;
    --sc-purple: #9333ea;
    --sc-purple-light: #a855f7;
    --sc-purple-glow: rgba(147, 51, 234, 0.25);
    --sc-text-heading: #f8fafc;
    --sc-text-body: #94a3b8;
    --sc-text-muted: #64748b;
    --sc-input-bg: #130f24;
    --sc-input-border: #3b3366;
    --sc-success-bg: rgba(16, 185, 129, 0.12);
    --sc-success-border: rgba(16, 185, 129, 0.35);
    --sc-success-text: #10b981;
    --sc-danger-bg: rgba(239, 68, 68, 0.12);
    --sc-danger-border: rgba(239, 68, 68, 0.35);
    --sc-danger-text: #ef4444;
    --sc-warning-bg: rgba(234, 179, 8, 0.12);
    --sc-warning-border: rgba(234, 179, 8, 0.35);
    --sc-warning-text: #eab308;
}

/* Light Theme Variables */
body:not(.dark-theme) {
    --sc-bg: #f8fafc;
    --sc-surface: #ffffff;
    --sc-surface-alt: #f1f5f9;
    --sc-surface-hover: #e2e8f0;
    --sc-border: #e2e8f0;
    --sc-text-heading: #0f172a;
    --sc-text-body: #334155;
    --sc-text-muted: #64748b;
    --sc-input-bg: #ffffff;
    --sc-input-border: #cbd5e1;
    --sc-purple-glow: rgba(147, 51, 234, 0.12);
}

.sc-container {
    background: transparent;
    color: var(--sc-text-body);
    font-family: inherit;
}

/* Header */
.sc-header-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--sc-text-heading);
    letter-spacing: -0.02em;
    margin-bottom: 2px;
}
.sc-header-subtitle {
    font-size: 13.5px;
    color: var(--sc-text-muted);
}

/* Top Buttons */
.sc-btn-top {
    background: var(--sc-surface);
    border: 1px solid var(--sc-border);
    color: var(--sc-text-heading);
    font-size: 13px;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 10px;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.sc-btn-top:hover {
    background: var(--sc-surface-hover);
    color: var(--sc-purple-light);
    border-color: var(--sc-purple);
}
.sc-btn-export {
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
    color: #ffffff !important;
    border: 1px solid #7c3aed;
    box-shadow: 0 4px 12px var(--sc-purple-glow);
}
.sc-btn-export:hover {
    background: linear-gradient(135deg, #6d28d9 0%, #7e22ce 100%);
    transform: translateY(-1px);
}

/* Navigation Tabs */
.sc-nav-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid var(--sc-border);
    padding-bottom: 0;
    margin-bottom: 20px;
}
.sc-nav-tab-btn {
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    color: var(--sc-text-muted);
    font-size: 14.5px;
    font-weight: 600;
    padding: 10px 20px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: -2px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.sc-nav-tab-btn:hover {
    color: var(--sc-text-heading);
}
.sc-nav-tab-btn.active {
    color: var(--sc-purple-light);
    border-bottom-color: var(--sc-purple);
}

/* Main Cards */
.sc-card {
    background: var(--sc-surface);
    border: 1px solid var(--sc-border);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    height: 100%;
}
.sc-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--sc-text-heading);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
}

/* Mode Switchers */
.sc-mode-btn-group {
    display: inline-flex;
    gap: 8px;
    margin-bottom: 20px;
}
.sc-mode-btn {
    background: var(--sc-surface-alt);
    border: 1px solid var(--sc-border);
    color: var(--sc-text-heading);
    font-size: 13px;
    font-weight: 600;
    padding: 9px 18px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.sc-mode-btn.active {
    background: #3b1d60;
    border-color: #9333ea;
    color: #ffffff;
    box-shadow: 0 0 0 2px var(--sc-purple-glow);
}
body:not(.dark-theme) .sc-mode-btn.active {
    background: #f3e8ff;
    border-color: #9333ea;
    color: #7c3aed;
}

/* Huge Scanner Input Box */
.sc-big-input-card {
    background: var(--sc-surface-alt);
    border: 2px dashed var(--sc-input-border);
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    margin-bottom: 20px;
    transition: all 0.2s;
}
.sc-big-input-card.focused {
    border-color: var(--sc-purple);
    border-style: solid;
    box-shadow: 0 0 0 4px var(--sc-purple-glow);
}
.sc-big-input-label {
    font-size: 13px;
    color: var(--sc-text-muted);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.sc-big-input {
    width: 100%;
    background: var(--sc-input-bg);
    border: 1.5px solid var(--sc-input-border);
    border-radius: 10px;
    color: var(--sc-text-heading);
    font-size: 18px;
    font-weight: 600;
    padding: 16px 20px;
    text-align: center;
    letter-spacing: 0.5px;
    transition: all 0.2s;
    outline: none;
}
.sc-big-input:focus {
    border-color: var(--sc-purple);
    box-shadow: 0 0 0 4px var(--sc-purple-glow);
}
.sc-big-input::placeholder {
    color: var(--sc-text-muted);
    font-weight: 500;
    font-size: 15px;
}

/* Manual Override Bar */
.sc-manual-bar {
    background: var(--sc-surface-alt);
    border: 1px solid var(--sc-border);
    border-radius: 10px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.sc-manual-label {
    font-size: 12px;
    font-weight: 800;
    color: var(--sc-purple-light);
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.sc-manual-input {
    flex: 1;
    background: var(--sc-input-bg);
    border: 1px solid var(--sc-input-border);
    border-radius: 8px;
    color: var(--sc-text-heading);
    font-size: 14px;
    padding: 8px 14px;
    outline: none;
}
.sc-manual-input:focus {
    border-color: var(--sc-purple);
}
.sc-manual-btn {
    background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);
    border: none;
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
    padding: 8px 18px;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.sc-manual-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--sc-purple-glow);
}

/* Instructions Card */
.sc-instructions-card {
    background: var(--sc-surface-alt);
    border: 1px solid var(--sc-border);
    border-radius: 10px;
    padding: 16px 20px;
    font-size: 12.5px;
    color: var(--sc-text-muted);
}
.sc-instructions-card h6 {
    font-size: 13px;
    font-weight: 700;
    color: var(--sc-text-heading);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sc-instructions-card ul {
    margin-bottom: 0;
    padding-left: 18px;
}
.sc-instructions-card li {
    margin-bottom: 4px;
}

/* Summary Cards */
.sc-summary-title {
    font-size: 12px;
    font-weight: 800;
    color: var(--sc-text-muted);
    letter-spacing: 0.5px;
    margin-bottom: 14px;
}
.sc-stat-card {
    background: var(--sc-surface-alt);
    border: 1px solid var(--sc-border);
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s;
}
.sc-stat-card.total {
    border-left: 4px solid var(--sc-purple);
}
.sc-stat-card.success {
    border-left: 4px solid var(--sc-success-text);
    background: var(--sc-success-bg);
    border-color: var(--sc-success-border);
}
.sc-stat-card.failed {
    border-left: 4px solid var(--sc-danger-text);
    background: var(--sc-danger-bg);
    border-color: var(--sc-danger-border);
}
.sc-stat-card.duplicate {
    border-left: 4px solid var(--sc-warning-text);
    background: var(--sc-warning-bg);
    border-color: var(--sc-warning-border);
}
.sc-stat-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--sc-text-muted);
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.sc-stat-value {
    font-size: 26px;
    font-weight: 800;
    color: var(--sc-text-heading);
    line-height: 1;
}
.sc-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.sc-stat-card.total .sc-stat-icon { background: var(--sc-purple-glow); color: var(--sc-purple-light); }
.sc-stat-card.success .sc-stat-icon { background: rgba(16, 185, 129, 0.2); color: var(--sc-success-text); }
.sc-stat-card.failed .sc-stat-icon { background: rgba(239, 68, 68, 0.2); color: var(--sc-danger-text); }
.sc-stat-card.duplicate .sc-stat-icon { background: rgba(234, 179, 8, 0.2); color: var(--sc-warning-text); }

/* Live History Table */
.sc-history-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.sc-history-table th {
    background: var(--sc-surface-alt);
    color: var(--sc-text-muted);
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--sc-border);
}
.sc-history-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--sc-border);
    color: var(--sc-text-heading);
    font-size: 13.5px;
    vertical-align: middle;
}
.sc-history-table tbody tr:hover td {
    background: var(--sc-surface-hover);
}

/* Badge Classes */
.sc-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.sc-badge-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.sc-badge-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
.sc-badge-warning { background: rgba(234, 179, 8, 0.15); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.3); }
.sc-badge-purple { background: rgba(147, 51, 234, 0.15); color: #a855f7; border: 1px solid rgba(147, 51, 234, 0.3); }

/* Status Transition Arrow */
.sc-transition {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
}
.sc-transition .prev { color: var(--sc-text-muted); }
.sc-transition .arrow { color: var(--sc-purple-light); }
.sc-transition .next { color: var(--sc-text-heading); font-weight: 700; }

/* Camera Modal / Viewfinder */
#reader {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 15px;
}
</style>

<div class="sc-container py-3">
    <!-- Top Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="sc-header-title">{{ __('Logistics Scanner Console') }}</h1>
            <p class="sc-header-subtitle mb-0">{{ __('Scan order barcodes or invoices to perform high-speed state changes.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="sc-btn-top" data-bs-toggle="modal" data-bs-target="#scannerGuideModal" data-toggle="modal" data-target="#scannerGuideModal">
                <i class="fa fa-book-open text-muted"></i> {{ __('Guide') }}
            </button>
            <a href="#scanHistorySection" class="sc-btn-top">
                <i class="fa fa-history text-muted"></i> {{ __('Scan History') }}
            </a>
            <button type="button" class="sc-btn-top sc-btn-export" onclick="exportScanResults()">
                <i class="fa fa-file-export"></i> {{ __('Export Result') }}
            </button>
        </div>
    </div>

    <!-- Mode Selector Navigation Tabs -->
    <div class="sc-nav-tabs">
        <button type="button" class="sc-nav-tab-btn {{ $mode === 'shipping' ? 'active' : '' }}" onclick="switchScannerMode('shipping')">
            <i class="fa fa-truck me-1"></i> {{ __('Scan To Shipping') }}
        </button>
        <button type="button" class="sc-nav-tab-btn {{ $mode === 'return' ? 'active' : '' }}" onclick="switchScannerMode('return')">
            <i class="fa fa-undo me-1"></i> {{ __('Scan To Return') }}
        </button>
        <button type="button" class="sc-nav-tab-btn {{ $mode === 'rts' ? 'active' : '' }}" onclick="switchScannerMode('rts')">
            <i class="fa fa-box me-1"></i> {{ __('Scan To RTS') }}
        </button>
    </div>

    <!-- Scanner Row -->
    <div class="row g-4 mb-4">
        <!-- Left: Scanner Controls & Input -->
        <div class="col-lg-8">
            <div class="sc-card">
                <div class="sc-card-title">
                    <i class="fa fa-qrcode text-purple"></i>
                    <span id="currentModeHeader">
                        @if($mode === 'shipping') Scan To Shipping Scanner Input
                        @elseif($mode === 'return') Scan To Return Scanner Input
                        @else Scan To RTS Scanner Input
                        @endif
                    </span>
                </div>

                <!-- Input Type Switcher -->
                <div class="sc-mode-btn-group">
                    <button type="button" class="sc-mode-btn active" id="btnModeKeyboard" onclick="setScanInputMode('keyboard')">
                        <i class="fa fa-keyboard"></i> Keyboard scanner
                    </button>
                    <button type="button" class="sc-mode-btn" id="btnModeCamera" onclick="setScanInputMode('camera')">
                        <i class="fa fa-camera"></i> Use phone camera
                    </button>
                </div>

                <!-- Camera Container (Hidden by default) -->
                <div id="cameraScannerWrapper" style="display: none;" class="mb-3">
                    <div id="reader"></div>
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="stopCameraScanner()">
                        <i class="fa fa-stop me-1"></i> Stop Camera Scanner
                    </button>
                </div>

                <!-- Big Scanner Input Box -->
                <div class="sc-big-input-card focused" id="bigInputCard" onclick="$('#barcodeInput').focus()">
                    <div class="sc-big-input-label">
                        <i class="fa fa-expand"></i> Click Input to activate scanner or type manually
                    </div>
                    <input type="text" id="barcodeInput" class="sc-big-input" placeholder="⛶ Scan or type invoice number and press Enter..." autofocus autocomplete="off">
                </div>

                <!-- Manual Override Bar -->
                <form id="manualScanForm" onsubmit="handleManualSubmit(event)">
                    <div class="sc-manual-bar">
                        <span class="sc-manual-label">MANUAL OVERRIDE:</span>
                        <input type="text" id="manualOverrideInput" class="sc-manual-input" placeholder="Enter invoice # manually...">
                        <button type="submit" class="sc-manual-btn">
                            <i class="fa fa-paper-plane"></i> Submit
                        </button>
                    </div>
                </form>

                <!-- Scanner Instructions -->
                <div class="sc-instructions-card">
                    <h6><i class="fa fa-info-circle text-info"></i> Scanner Instructions & Shortcuts</h6>
                    <ul>
                        <li>For barcode hardware scanners, keep this page open and focused. No input click is necessary.</li>
                        <li>Toggle between camera scan and keyboard scan modes using the selector buttons above.</li>
                        <li>High-speed continuous scan mode is enabled. Audio sound chimes automatically on each scan.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right: Scan Session Summary -->
        <div class="col-lg-4">
            <div class="sc-card">
                <div class="sc-summary-title">SCAN SESSION SUMMARY</div>

                <div class="sc-stat-card total">
                    <div>
                        <div class="sc-stat-label">TOTAL SCANS</div>
                        <div class="sc-stat-value" id="statTotalScans">0</div>
                    </div>
                    <div class="sc-stat-icon">
                        <i class="fa fa-barcode"></i>
                    </div>
                </div>

                <div class="sc-stat-card success">
                    <div>
                        <div class="sc-stat-label" style="color: var(--sc-success-text);">SUCCESSFUL</div>
                        <div class="sc-stat-value" style="color: var(--sc-success-text);" id="statSuccessfulScans">0</div>
                    </div>
                    <div class="sc-stat-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>

                <div class="sc-stat-card failed">
                    <div>
                        <div class="sc-stat-label" style="color: var(--sc-danger-text);">FAILED</div>
                        <div class="sc-stat-value" style="color: var(--sc-danger-text);" id="statFailedScans">0</div>
                    </div>
                    <div class="sc-stat-icon">
                        <i class="fa fa-times-circle"></i>
                    </div>
                </div>

                <div class="sc-stat-card duplicate">
                    <div>
                        <div class="sc-stat-label" style="color: var(--sc-warning-text);">DUPLICATE / ALREADY UPDATED</div>
                        <div class="sc-stat-value" style="color: var(--sc-warning-text);" id="statDuplicateScans">0</div>
                    </div>
                    <div class="sc-stat-icon">
                        <i class="fa fa-copy"></i>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top" style="border-color: var(--sc-border) !important;">
                    <a href="{{ route('woocommerce.orders.list') }}" class="btn btn-outline-custom w-100 py-2 font-weight-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="fa fa-list"></i> View In Order List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Scanned Items History Stream -->
    <div class="sc-card" id="scanHistorySection">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="sc-card-title mb-0">
                <i class="fa fa-history text-purple"></i> {{ __('Live Scanned Orders Activity Stream') }}
            </div>
            <button type="button" class="btn btn-sm btn-outline-custom" onclick="clearSessionHistory()">
                <i class="fa fa-trash me-1"></i> Clear List
            </button>
        </div>

        <div class="table-responsive">
            <table class="sc-history-table">
                <thead>
                    <tr>
                        <th>Order / Invoice #</th>
                        <th>Customer</th>
                        <th>Status Transition</th>
                        <th>Courier</th>
                        <th>Amount</th>
                        <th>Time</th>
                        <th class="text-end">Result</th>
                    </tr>
                </thead>
                <tbody id="scanHistoryTbody">
                    <tr id="emptyHistoryRow">
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa fa-barcode fa-3x mb-2 d-block text-purple" style="opacity: 0.5;"></i>
                            Ready to scan! Please scan an invoice barcode or enter invoice number above.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Guide Modal -->
<div class="modal fade" id="scannerGuideModal" tabindex="-1" aria-labelledby="scannerGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--sc-surface); border: 1px solid var(--sc-border); color: var(--sc-text-heading);">
            <div class="modal-header" style="border-bottom-color: var(--sc-border);">
                <h5 class="modal-title font-weight-bold" id="scannerGuideModalLabel"><i class="fa fa-book-open text-purple me-2"></i> Logistics Scanner Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: 13.5px; color: var(--sc-text-body); line-height: 1.6;">
                <p class="font-weight-bold text-purple mb-2">How to use the High-Speed Scanner:</p>
                <ol class="ps-3 mb-3">
                    <li class="mb-1"><strong>Choose Mode:</strong> Select <code>Scan To Shipping</code>, <code>Scan To Return</code>, or <code>Scan To RTS</code> from the top tabs.</li>
                    <li class="mb-1"><strong>Point & Scan:</strong> Use your barcode gun to scan printed shipping labels, invoice numbers, or WooCommerce Order IDs.</li>
                    <li class="mb-1"><strong>Instant Feedback:</strong> The system automatically verifies the invoice, changes the status in database, plays an audio sound, and records it in the live activity stream.</li>
                    <li class="mb-1"><strong>Camera Mode:</strong> Click <em>Use phone camera</em> if you want to scan via smartphone camera or webcam.</li>
                </ol>
            </div>
            <div class="modal-footer" style="border-top-color: var(--sc-border);">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

<!-- HTML5-QRCode Scanner Library for Phone Camera -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
var currentMode = "{{ $mode }}"; // 'shipping', 'return', 'rts'
var html5QrCode = null;
var scannedSessionList = [];
var scanCounts = {
    total: 0,
    success: 0,
    failed: 0,
    duplicate: 0
};

// Web Audio API Synthesizer for Audio Feedback
var audioCtx = null;
function getAudioContext() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    return audioCtx;
}

function playSound(type) {
    try {
        var ctx = getAudioContext();
        if (ctx.state === 'suspended') {
            ctx.resume();
        }

        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'success') {
            // High cheerful chime
            osc.frequency.setValueAtTime(800, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.12);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.15);
        } else if (type === 'duplicate') {
            // Double warning beep
            osc.frequency.setValueAtTime(520, ctx.currentTime);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.18);
        } else if (type === 'failed') {
            // Low error buzz
            osc.frequency.setValueAtTime(220, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(140, ctx.currentTime + 0.25);
            gain.gain.setValueAtTime(0.4, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.25);
        }
    } catch(e) {
        console.log("Audio play error", e);
    }
}

function switchScannerMode(mode) {
    currentMode = mode;
    $('.sc-nav-tab-btn').removeClass('active');
    $('button[onclick="switchScannerMode(\'' + mode + '\')"]').addClass('active');

    var titles = {
        'shipping': 'Scan To Shipping Scanner Input',
        'return': 'Scan To Return Scanner Input',
        'rts': 'Scan To RTS Scanner Input'
    };
    $('#currentModeHeader').text(titles[mode] || 'Scanner Input');
    $('#barcodeInput').focus();
}

function setScanInputMode(mode) {
    if (mode === 'keyboard') {
        $('#btnModeKeyboard').addClass('active');
        $('#btnModeCamera').removeClass('active');
        $('#cameraScannerWrapper').hide();
        stopCameraScanner();
        $('#barcodeInput').focus();
    } else {
        $('#btnModeCamera').addClass('active');
        $('#btnModeKeyboard').removeClass('active');
        $('#cameraScannerWrapper').show();
        startCameraScanner();
    }
}

function startCameraScanner() {
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
    }
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        function(decodedText) {
            executeScan(decodedText);
        },
        function(error) {}
    ).catch(function(err) {
        alert("Camera permission denied or camera not found: " + err);
        setScanInputMode('keyboard');
    });
}

function stopCameraScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(function() {
            html5QrCode.clear();
        }).catch(function(err) {});
    }
}

function executeScan(barcode) {
    barcode = (barcode || '').trim();
    if (!barcode) return;

    $('#barcodeInput').val('');
    $('#manualOverrideInput').val('');

    $.ajax({
        url: "{{ route('woocommerce.scanner.process') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            barcode: barcode,
            mode: currentMode
        },
        success: function(res) {
            scanCounts.total++;
            $('#statTotalScans').text(scanCounts.total);

            if (res.status === 'updated') {
                scanCounts.success++;
                $('#statSuccessfulScans').text(scanCounts.success);
                playSound('success');
                appendHistoryRow(res, 'SUCCESSFUL', 'sc-badge-success');
            } else if (res.status === 'duplicate') {
                scanCounts.duplicate++;
                $('#statDuplicateScans').text(scanCounts.duplicate);
                playSound('duplicate');
                appendHistoryRow(res, 'DUPLICATE', 'sc-badge-warning');
            } else {
                scanCounts.failed++;
                $('#statFailedScans').text(scanCounts.failed);
                playSound('failed');
                appendHistoryRow({
                    invoice_no: barcode,
                    customer: 'Not Found',
                    prev_status: '-',
                    new_status: currentMode.toUpperCase(),
                    courier: '-',
                    amount: 0,
                    time: new Date().toLocaleTimeString()
                }, 'NOT FOUND', 'sc-badge-danger');
            }
        },
        error: function(xhr) {
            scanCounts.total++;
            scanCounts.failed++;
            $('#statTotalScans').text(scanCounts.total);
            $('#statFailedScans').text(scanCounts.failed);
            playSound('failed');
            appendHistoryRow({
                invoice_no: barcode,
                customer: 'Network Error',
                prev_status: '-',
                new_status: currentMode.toUpperCase(),
                courier: '-',
                amount: 0,
                time: new Date().toLocaleTimeString()
            }, 'FAILED', 'sc-badge-danger');
        },
        complete: function() {
            $('#barcodeInput').focus();
        }
    });
}

function appendHistoryRow(data, badgeText, badgeClass) {
    $('#emptyHistoryRow').hide();
    scannedSessionList.unshift(data);

    var prevSt = data.prev_status || 'Processing';
    var nextSt = data.new_status || currentMode.toUpperCase();

    var rowHtml = '<tr>' +
        '<td class="font-weight-bold text-purple">#' + data.invoice_no + '</td>' +
        '<td>' + (data.customer || '-') + (data.phone && data.phone !== '-' ? ('<br><small class="text-muted">' + data.phone + '</small>') : '') + '</td>' +
        '<td>' +
            '<div class="sc-transition">' +
                '<span class="prev">' + prevSt + '</span>' +
                '<i class="fa fa-arrow-right arrow small"></i>' +
                '<span class="next">' + nextSt + '</span>' +
            '</div>' +
        '</td>' +
        '<td>' + (data.courier || 'Steadfast') + '</td>' +
        '<td>৳' + (data.amount ? Number(data.amount).toLocaleString() : '0') + '</td>' +
        '<td class="text-muted small">' + (data.time || 'Just now') + '</td>' +
        '<td class="text-end"><span class="sc-badge ' + badgeClass + '">' + badgeText + '</span></td>' +
    '</tr>';

    $('#scanHistoryTbody').prepend(rowHtml);
}

function handleManualSubmit(e) {
    e.preventDefault();
    var val = $('#manualOverrideInput').val();
    if (val) {
        executeScan(val);
    }
}

function clearSessionHistory() {
    if (confirm("Are you sure you want to clear this scan session history?")) {
        scannedSessionList = [];
        scanCounts = { total: 0, success: 0, failed: 0, duplicate: 0 };
        $('#statTotalScans').text(0);
        $('#statSuccessfulScans').text(0);
        $('#statFailedScans').text(0);
        $('#statDuplicateScans').text(0);
        $('#scanHistoryTbody').html('<tr id="emptyHistoryRow"><td colspan="7" class="text-center py-5 text-muted"><i class="fa fa-barcode fa-3x mb-2 d-block text-purple" style="opacity: 0.5;"></i>Ready to scan! Please scan an invoice barcode or enter invoice number above.</td></tr>');
    }
}

function exportScanResults() {
    if (scannedSessionList.length === 0) {
        alert("No scanned items in current session to export.");
        return;
    }

    var csvContent = "data:text/csv;charset=utf-8,Invoice No,Customer,Phone,Previous Status,New Status,Courier,Amount,Time\n";
    scannedSessionList.forEach(function(row) {
        csvContent += '"' + (row.invoice_no || '') + '","' + (row.customer || '') + '","' + (row.phone || '') + '","' + (row.prev_status || '') + '","' + (row.new_status || '') + '","' + (row.courier || '') + '","' + (row.amount || 0) + '","' + (row.time || '') + '"\n';
    });

    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "logistics_scan_results_" + currentMode + "_" + new Date().toISOString().slice(0,10) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

$(document).ready(function() {
    // Keep focus on barcode input
    $('#barcodeInput').focus();

    $('#barcodeInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var val = $(this).val();
            if (val) {
                executeScan(val);
            }
        }
    });

    // Auto-focus on click anywhere outside inputs
    $(document).on('click', function(e) {
        if (!$(e.target).is('input, button, select, a, textarea') && !$('#cameraScannerWrapper').is(':visible')) {
            $('#barcodeInput').focus();
        }
    });
});
</script>
@endsection
