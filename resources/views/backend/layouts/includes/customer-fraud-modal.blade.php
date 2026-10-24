<!-- Customer Courier Rating & Fraud Profile Modal -->
<div class="modal fade" id="customerFraudModal" tabindex="-1" role="dialog" aria-labelledby="customerFraudModalLabel" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 820px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <!-- Modal Header -->
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 16px 22px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div class="d-flex align-items-center">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(6, 182, 212, 0.15); display: flex; align-items: center; justify-content: center; margin-right: 12px; border: 1px solid rgba(6, 182, 212, 0.3);">
                        <i class="feather icon-shield" style="color: #06b6d4; font-size: 18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white font-weight-bold mb-0" id="customerFraudModalLabel" style="font-size: 16px;">
                            {{ __('Customer Courier Rating & Fraud Profile') }}
                        </h5>
                        <div class="text-muted small" style="color: #94a3b8 !important; font-size: 11px;">
                            {{ __('Real-time delivery success & courier history inspection') }}
                        </div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.8; font-size: 24px; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" style="background: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">
                <!-- Top Customer Summary Card -->
                <div class="p-3 rounded-lg mb-3 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="feather icon-user text-muted mr-1" style="font-size: 14px;"></i>
                                <h6 class="font-weight-bold mb-0 text-dark" id="cfm_cust_name" style="font-size: 16px;">-</h6>
                            </div>
                            <div class="d-flex align-items-center mt-1 flex-wrap" style="gap: 8px;">
                                <span class="text-muted small"><i class="feather icon-phone text-success mr-1"></i> <span id="cfm_cust_phone" class="font-weight-bold text-dark">-</span></span>
                                <a id="cfm_whatsapp_btn" href="#" target="_blank" class="badge badge-success py-1 px-2 text-white" style="font-size: 11px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border-radius: 10px;">
                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                </a>
                                <a id="cfm_call_btn" href="#" class="badge badge-info py-1 px-2 text-white" style="font-size: 11px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border-radius: 10px;">
                                    <i class="feather icon-phone-call"></i> Call
                                </a>
                            </div>
                        </div>

                        <div class="text-right">
                            <span id="cfm_risk_badge" class="badge rounded-pill px-3 py-2 shadow-sm" style="font-size: 12px; font-weight: 700;">Checking...</span>
                            <div class="small font-weight-bold mt-1 text-muted">
                                Overall Delivery: <span id="cfm_success_rate" class="text-success font-weight-bold" style="font-size: 17px;">-</span>
                            </div>
                            <div class="text-muted" style="font-size: 11.5px;" id="cfm_parcels_summary">Parcels: 0 | Delivered: 0 | Cancelled: 0</div>
                        </div>
                    </div>
                </div>

                <!-- Warning Alert Box -->
                <div id="cfm_warning_alert" class="alert alert-danger shadow-sm mb-3" style="display: none; border-left: 4px solid #ef4444; border-radius: 10px; background: #fef2f2; border-color: #fee2e2;">
                    <div class="d-flex align-items-start">
                        <i class="feather icon-alert-triangle mr-2 text-danger" style="font-size: 20px; line-height: 1.2;"></i>
                        <div id="cfm_warning_text" class="font-weight-bold text-danger" style="font-size: 13px;"></div>
                    </div>
                </div>

                <!-- Courier-Wise Rating Mini Cards -->
                <div class="mb-3">
                    <label class="font-weight-bold text-dark mb-2 d-flex align-items-center" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569 !important;">
                        <i class="feather icon-truck mr-1 text-primary"></i> Courier Partners Rating Overview
                    </label>
                    <div class="row" id="cfm_courier_cards_container" style="margin-left: -5px; margin-right: -5px;">
                        <!-- Cards populated dynamically -->
                    </div>
                </div>

                <!-- Courier Delivery History Breakdown Table -->
                <div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-2" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div class="card-header font-weight-bold py-2 px-3 d-flex justify-content-between align-items-center" style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
                        <span style="font-size: 13px; color: #1e293b;"><i class="feather icon-layers mr-1 text-info"></i> Courier-Wise History (Steadfast, Pathao, RedX, CarryBee & POS)</span>
                        <span class="badge badge-light border text-muted" style="font-size: 11px;">Breakdown</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center" style="font-size: 12.5px;">
                            <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <tr style="color: #64748b; font-weight: 700; font-size: 11.5px; text-transform: uppercase;">
                                    <th class="text-left" style="padding: 10px 14px;">Courier Source</th>
                                    <th>Total Parcels</th>
                                    <th>Delivered</th>
                                    <th>Cancelled / Returned</th>
                                    <th>Success Rate</th>
                                    <th>Reliability Status</th>
                                </tr>
                            </thead>
                            <tbody id="cfm_sources_tbody">
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="text-muted small mt-2">Checking courier ratings & delivery profile...</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-2 px-3" style="background: #f1f5f9; border-top: 1px solid #e2e8f0; border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-sm btn-outline-primary" id="cfm_refresh_btn" onclick="refreshCurrentCustomerFraud()">
                    <i class="feather icon-refresh-cw mr-1"></i> Re-check Rating
                </button>
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold px-3" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var cfmCurrentPhone = '';
var cfmCurrentName = '';

function openCustomerFraudModal(phone, custName) {
    if (!phone) {
        alert("Phone number is required to check courier rating.");
        return;
    }

    cfmCurrentPhone = phone;
    cfmCurrentName = custName || 'Customer';

    // Format phone for links
    var cleanPhone = phone.replace(/[^0-9]/g, '');
    var waPhone = cleanPhone.startsWith('0') ? '88' + cleanPhone : cleanPhone;

    // Reset Modal UI
    $('#cfm_cust_name').text(custName || 'Customer');
    $('#cfm_cust_phone').text(phone);
    $('#cfm_whatsapp_btn').attr('href', 'https://wa.me/' + waPhone);
    $('#cfm_call_btn').attr('href', 'tel:' + phone);
    $('#cfm_risk_badge').text('Checking...').removeClass().addClass('badge rounded-pill bg-secondary px-3 py-2 text-white');
    $('#cfm_success_rate').text('...');
    $('#cfm_parcels_summary').text('Loading delivery statistics...');
    $('#cfm_warning_alert').hide();

    // Loader in table and cards
    $('#cfm_courier_cards_container').html(`
        <div class="col-12 text-center py-3 text-muted">
            <span class="spinner-border spinner-border-sm text-primary mr-1"></span> Loading courier partner ratings...
        </div>
    `);

    $('#cfm_sources_tbody').html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <i class="feather icon-loader fa-spin text-primary" style="font-size: 24px;"></i>
                <div class="text-muted small mt-2">Checking courier ratings & delivery history...</div>
            </td>
        </tr>
    `);

    $('#customerFraudModal').modal('show');

    // Fetch live fraud and rating check
    loadCustomerFraudData(phone, custName);
}

function refreshCurrentCustomerFraud() {
    if (cfmCurrentPhone) {
        var btn = $('#cfm_refresh_btn');
        btn.prop('disabled', true).html('<i class="feather icon-loader fa-spin mr-1"></i> Refreshing...');
        loadCustomerFraudData(cfmCurrentPhone, cfmCurrentName, function() {
            btn.prop('disabled', false).html('<i class="feather icon-refresh-cw mr-1"></i> Re-check Rating');
        });
    }
}

function loadCustomerFraudData(phone, custName, onDone) {
    var url = "{{ url('/customer/fraud-check') }}/" + encodeURIComponent(phone);

    $.get(url, function(fc) {
        if (!fc || !fc.success) {
            $('#cfm_sources_tbody').html('<tr><td colspan="6" class="text-danger py-3">Failed to fetch courier rating data.</td></tr>');
            if (onDone) onDone();
            return;
        }

        // Customer Info
        $('#cfm_cust_name').text(fc.customer_name || custName || 'Customer');
        $('#cfm_cust_phone').text(fc.phone || phone);

        // Risk Badge
        var badgeClass = fc.badge_class || 'badge-secondary';
        var badgeBg = fc.badge_bg || '#64748b';
        $('#cfm_risk_badge').text(fc.risk_label || 'Safe').removeClass().addClass('badge rounded-pill px-3 py-2 text-white ' + badgeClass).css('background-color', badgeBg);

        // Overall Success Rate
        var overallRate = fc.overall_success_rate !== undefined ? fc.overall_success_rate : 100;
        var rateColor = overallRate >= 70 ? '#10b981' : (overallRate >= 40 ? '#f59e0b' : '#ef4444');
        $('#cfm_success_rate').text(overallRate + '%').css('color', rateColor);

        var total = fc.total_parcels || 0;
        var delivered = fc.total_delivered || 0;
        var cancelled = fc.total_cancelled || 0;
        $('#cfm_parcels_summary').html(`Total Orders: <strong>${total}</strong> | Delivered: <strong class="text-success">${delivered}</strong> | Cancelled: <strong class="text-danger">${cancelled}</strong>`);

        // Warning Alert Box
        if (fc.warning_message || cancelled >= 1) {
            var warnMsg = fc.warning_message || `⚠️ সতর্কতা: এই গ্রাহকের ${cancelled} টি পার্সেল রিটার্ন/বাতিল হয়েছে! ডেলিভারি পাঠানোর পূর্বে নিশ্চিত হয়ে নিন।`;
            $('#cfm_warning_text').text(warnMsg);
            $('#cfm_warning_alert').show();
        } else {
            $('#cfm_warning_alert').hide();
        }

        // Render Courier Cards & Table
        renderCourierRatingDetails(fc.sources || {});

        if (onDone) onDone();
    }).fail(function() {
        $('#cfm_sources_tbody').html('<tr><td colspan="6" class="text-danger py-3">Error connecting to Courier Rating service.</td></tr>');
        if (onDone) onDone();
    });
}

function renderCourierRatingDetails(sources) {
    var courierConfig = {
        'steadfast': { name: 'Steadfast Courier', icon: 'feather icon-send', color: '#0ea5e9', bg: '#f0f9ff' },
        'pathao': { name: 'Pathao Courier', icon: 'feather icon-navigation', color: '#ef4444', bg: '#fef2f2' },
        'redx': { name: 'RedX Courier', icon: 'feather icon-box', color: '#e11d48', bg: '#fff1f2' },
        'carrybee': { name: 'CarryBee Courier', icon: 'feather icon-truck', color: '#f59e0b', bg: '#fffbeb' },
        'internal': { name: 'Internal POS Orders', icon: 'feather icon-home', color: '#8b5cf6', bg: '#f5f3ff' },
        'courier_api': { name: 'Aggregator Courier API', icon: 'feather icon-globe', color: '#10b981', bg: '#f0fdf4' }
    };

    var cardsHtml = '';
    var tbodyHtml = '';

    $.each(sources, function(key, s) {
        var cfg = courierConfig[key] || { name: key.toUpperCase(), icon: 'feather icon-package', color: '#64748b', bg: '#f8fafc' };
        var total = s.total_parcels !== undefined ? s.total_parcels : (s.total_orders || 0);
        var delivered = s.delivered || 0;
        var cancelled = s.cancelled !== undefined ? s.cancelled : (s.returned || 0);
        var rate = (s.success_rate !== null && s.success_rate !== undefined) ? s.success_rate : null;
        var rateText = rate !== null ? rate + '%' : (total === 0 ? 'N/A' : '0%');

        var rateColor = rate !== null ? (rate >= 70 ? '#10b981' : (rate >= 40 ? '#f59e0b' : '#ef4444')) : '#94a3b8';
        var statusBadge = '';

        if (total === 0) {
            statusBadge = '<span class="badge badge-light border text-muted" style="font-size:11px;">No Parcels</span>';
        } else if (rate >= 80) {
            statusBadge = '<span class="badge badge-success" style="font-size:11px; background:#10b981;">Excellent (Safe)</span>';
        } else if (rate >= 50) {
            statusBadge = '<span class="badge badge-warning text-white" style="font-size:11px; background:#f59e0b;">Moderate Risk</span>';
        } else {
            statusBadge = '<span class="badge badge-danger" style="font-size:11px; background:#ef4444;">High Returns</span>';
        }

        // Mini Card HTML (For top couriers: steadfast, pathao, redx, carrybee, internal)
        if (['steadfast', 'pathao', 'redx', 'carrybee', 'internal'].indexOf(key) !== -1) {
            cardsHtml += `
                <div class="col-md-4 col-sm-6 mb-2" style="padding-left: 5px; padding-right: 5px;">
                    <div class="p-2 rounded-lg border shadow-xs" style="background: ${cfg.bg}; border-color: #e2e8f0 !important; border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold" style="font-size: 12px; color: #1e293b;">
                                <i class="${cfg.icon} mr-1" style="color: ${cfg.color};"></i> ${cfg.name}
                            </span>
                            <span class="font-weight-bold" style="font-size: 13px; color: ${rateColor};">${rateText}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
                            <span>Total: <strong>${total}</strong></span>
                            <span>Delivered: <strong class="text-success">${delivered}</strong></span>
                            <span>Cancel: <strong class="text-danger">${cancelled}</strong></span>
                        </div>
                        <div class="progress mt-1" style="height: 4px; background: #e2e8f0; border-radius: 4px;">
                            <div class="progress-bar" style="width: ${rate !== null ? rate : (total > 0 ? 0 : 0)}%; background-color: ${rateColor};"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Table Row HTML
        tbodyHtml += `
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td class="text-left font-weight-bold" style="color: #1e293b; padding: 9px 14px;">
                    <i class="${cfg.icon} mr-1" style="color: ${cfg.color};"></i> ${cfg.name}
                </td>
                <td><span class="badge badge-light border px-2 py-1 font-weight-bold" style="font-size: 12px;">${total}</span></td>
                <td><span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 12px; background: #10b981;">${delivered}</span></td>
                <td><span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 12px; background: #ef4444;">${cancelled}</span></td>
                <td><strong style="color: ${rateColor}; font-size: 13px;">${rateText}</strong></td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });

    $('#cfm_courier_cards_container').html(cardsHtml);
    $('#cfm_sources_tbody').html(tbodyHtml);
}

// Global aliases so any button calling checkCustomerFraud works seamlessly
window.openCustomerFraudModal = openCustomerFraudModal;
window.checkCustomerFraud = openCustomerFraudModal;
window.checkCustomerFraudHistory = openCustomerFraudModal;
</script>
