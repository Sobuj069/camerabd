@extends('backend.layouts.master')

@section('content')
<div class="setting-page">
    <div class="setting-tabs">
        <button onclick="switchTab('business')" id="tab-btn-business" class="tab-btn active-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="16" x="4" y="4" rx="2"/><path d="M9 22V2h6v20"/><path d="M8 12h8"/></svg>
            {{ __('Identity & branding') }}
        </button>
        <button onclick="switchTab('operational')" id="tab-btn-operational" class="tab-btn inactive-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ __('Operational') }}
        </button>
        <button onclick="switchTab('legal')" id="tab-btn-legal" class="tab-btn inactive-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            {{ __('Legal & Policies') }}
        </button>
    </div> 

    <div class="setting-card">

        {{-- Identity Tab --}}
        <div id="tab-business" class="tab-content block">
            <form method="POST" action="{{ route('setting.update') }}" enctype="multipart/form-data">
                @csrf

                <p class="section-label">{{ __('Branding assets') }}</p>

                <div class="upload-row flex flex-col md:flex-row gap-6 md:gap-8">
                    <div class="upload-item">
                        <div class="upload-preview">
                            <img id="image_icon" src="{{ !empty(get_setting('system_icon')) ? url('uploads/logo/' . get_setting('system_icon')) : url('backend/images/favicon.png') }}">
                        </div>
                        <div class="upload-info">
                            <div class="upload-name">{{ __('System icon') }}</div>
                            <div class="upload-hint">{{ __('32×32 recommended') }}</div>
                            <label class="upload-btn">{{ __('Change icon') }}
                                <input type="file" name="system_icon" style="display:none" onchange="readURLIcon(this);">
                            </label>
                        </div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-preview wide">
                            <img id="image_logo" src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/no_images.png') }}">
                        </div>
                        <div class="upload-info">
                            <div class="upload-name">{{ __('System logo') }}</div>
                            <div class="upload-hint">{{ __('SVG or PNG, wide format') }}</div>
                            <label class="upload-btn">{{ __('Upload logo') }}
                                <input type="file" name="system_logo" style="display:none" onchange="readURLLogo(this);">
                            </label>
                        </div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-preview wide">
                            <img id="image_login_bg" src="{{ !empty(get_setting('login_bg')) ? url('uploads/logo/' . get_setting('login_bg')) : url('backend/images/multishop_bg.png') }}">
                        </div>
                        <div class="upload-info">
                            <div class="upload-name">{{ __('Login Background') }}</div>
                            <div class="upload-hint">{{ __('1920×1080 recommended') }}</div>
                            <label class="upload-btn">{{ __('Upload BG') }}
                                <input type="file" name="login_bg" style="display:none" onchange="readURLLoginBg(this);">
                            </label>
                        </div>
                    </div>
                </div>

                <p class="section-label">{{ __('Store details') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Store name') }}</label>
                        <input type="hidden" name="types[]" value="com_name">
                        <input type="text" name="com_name" value="{{ get_setting('com_name') }}" placeholder="{{ __('e.g. My Store') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Email') }}</label>
                        <input type="hidden" name="types[]" value="com_email">
                        <input type="email" name="com_email" value="{{ get_setting('com_email') }}" placeholder="{{ __('contact@store.com') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Phone') }}</label>
                        <input type="hidden" name="types[]" value="com_phone">
                        <input type="text" name="com_phone" value="{{ get_setting('com_phone') }}" placeholder="+880...">
                    </div>
                    <div class="field">
                        <label>{{ __('Currency') }}</label>
                        <input type="hidden" name="types[]" value="com_currency">
                        <input type="text" name="com_currency" value="{{ get_setting('com_currency') }}" placeholder="{{ __('BDT') }}">
                    </div>
                    <div class="field col-span-1 md:col-span-2">
                        <label>{{ __('Address') }}</label>
                        <input type="hidden" name="types[]" value="com_address">
                        <textarea name="com_address" rows="2" placeholder="{{ __('Street, city, country') }}">{{ get_setting('com_address') }}</textarea>
                    </div>
                </div>

                <div class="footer-row">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Save identity') }}</span>
                        <span class="btn-loader hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Operational Tab --}}
        <div id="tab-operational" class="tab-content hidden">
            <form method="POST" action="{{ route('setting.update') }}">
                @csrf

                <p class="section-label">{{ __('Print & display options') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Barcode style') }}</label>
                        <input type="hidden" name="types[]" value="pro_barcode">
                        <select name="pro_barcode" class="select2">
                            <option value="a4" {{ get_setting('pro_barcode') == 'a4' ? 'selected' : '' }}>{{ __('A4 sheet') }}</option>
                            <option value="single" {{ get_setting('pro_barcode') == 'single' ? 'selected' : '' }}>{{ __('Single thermal') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Invoice branding') }}</label>
                        <input type="hidden" name="types[]" value="inv_logo">
                        <select name="inv_logo" class="select2">
                            <option value="name" {{ get_setting('inv_logo') == 'name' ? 'selected' : '' }}>{{ __('Name only') }}</option>
                            <option value="logo" {{ get_setting('inv_logo') == 'logo' ? 'selected' : '' }}>{{ __('Logo only') }}</option>
                            <option value="both" {{ get_setting('inv_logo') == 'both' ? 'selected' : '' }}>{{ __('Both') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Invoice layout') }}</label>
                        <input type="hidden" name="types[]" value="inv_design">
                        <select name="inv_design" class="select2">
                            <option value="a4" {{ get_setting('inv_design') == 'a4' ? 'selected' : '' }}>{{ __('Standard A4') }}</option>
                            <option value="a5" {{ get_setting('inv_design') == 'a5' ? 'selected' : '' }}>{{ __('Standard A5') }}</option>
                            <option value="pos" {{ get_setting('inv_design') == 'pos' ? 'selected' : '' }}>{{ __('POS 80mm') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Stock detail') }}</label>
                        <input type="hidden" name="types[]" value="inv_details">
                        <select name="inv_details" class="select2">
                            <option value="single" {{ get_setting('inv_details') == 'single' ? 'selected' : '' }}>{{ __('Single shop') }}</option>
                            <option value="branch" {{ get_setting('inv_details') == 'branch' ? 'selected' : '' }}>{{ __('Branch wise') }}</option>
                        </select>
                    </div>
                </div>

                @if(env('APP_COURIER_FRAUD_CHECK') == 'yes' && auth()->check() && auth()->user()->isSuperAdmin())
                <p class="section-label mt-6">{{ __('Steadfast Courier API Settings') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Steadfast API Key') }}</label>
                        <input type="hidden" name="types[]" value="steadfast_api_key">
                        <input type="text" name="steadfast_api_key" value="{{ get_setting('steadfast_api_key') ?: env('STEADFAST_API_KEY') }}" placeholder="{{ __('API Key') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Steadfast Secret Key') }}</label>
                        <input type="hidden" name="types[]" value="steadfast_secret_key">
                        <input type="password" name="steadfast_secret_key" value="{{ get_setting('steadfast_secret_key') ?: env('STEADFAST_SECRET_KEY') }}" placeholder="{{ __('Secret Key') }}">
                    </div>
                </div>

                <p class="section-label mt-6">{{ __('Pathao Courier API Settings') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Pathao Client ID') }}</label>
                        <input type="hidden" name="types[]" value="pathao_client_id">
                        <input type="text" name="pathao_client_id" value="{{ get_setting('pathao_client_id') ?: (env('PATHAO_CLIENT_ID') ?: 'jnegkRrewZ') }}" placeholder="{{ __('Client ID') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Pathao Client Secret') }}</label>
                        <input type="hidden" name="types[]" value="pathao_client_secret">
                        <input type="password" name="pathao_client_secret" value="{{ get_setting('pathao_client_secret') ?: env('PATHAO_CLIENT_SECRET') }}" placeholder="{{ __('Client Secret') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Pathao Username / Email') }}</label>
                        <input type="hidden" name="types[]" value="pathao_username">
                        <input type="text" name="pathao_username" value="{{ get_setting('pathao_username') ?: env('PATHAO_USERNAME') }}" placeholder="{{ __('Registered Email or Phone') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Pathao Password') }}</label>
                        <input type="hidden" name="types[]" value="pathao_password">
                        <input type="password" name="pathao_password" value="{{ get_setting('pathao_password') ?: env('PATHAO_PASSWORD') }}" placeholder="{{ __('Account Password') }}">
                    </div>
                    <div class="field col-span-1 md:col-span-2">
                        <label>{{ __('Pathao Secret Token (Optional)') }}</label>
                        <input type="hidden" name="types[]" value="pathao_secret_token">
                        <input type="text" name="pathao_secret_token" value="{{ get_setting('pathao_secret_token') ?: env('PATHAO_SECRET_TOKEN') }}" placeholder="{{ __('Secret Bearer Token if issued directly') }}">
                    </div>
                </div>

                <p class="section-label mt-6">{{ __('Custom Courier Fraud Checker API Settings') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Fraud API Base URL') }}</label>
                        <input type="hidden" name="types[]" value="courier_fraud_api_url">
                        <input type="text" name="courier_fraud_api_url" value="{{ get_setting('courier_fraud_api_url') }}" placeholder="{{ __('e.g. https://api.fraudchecker.xyz') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Fraud API Key') }}</label>
                        <input type="hidden" name="types[]" value="courier_fraud_api_key">
                        <input type="password" name="courier_fraud_api_key" value="{{ get_setting('courier_fraud_api_key') }}" placeholder="{{ __('API Authorization Key') }}">
                    </div>
                </div>
                @endif

                <p class="section-label mt-6">{{ __('Meta Pixel & Conversions API (Server-Side Tracking)') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Meta Pixel / Dataset ID') }}</label>
                        <input type="hidden" name="types[]" value="meta_pixel_id">
                        <input type="text" name="meta_pixel_id" value="{{ get_setting('meta_pixel_id') ?: '28625456847108675' }}" placeholder="28625456847108675">
                        <small class="text-muted">{{ __('Found in Meta Events Manager > Datasets') }}</small>
                    </div>

                    <div class="field">
                        <label>{{ __('CAPI Test Event Code (Optional for Test Events Tab)') }}</label>
                        <input type="hidden" name="types[]" value="meta_capi_test_event_code">
                        <input type="text" name="meta_capi_test_event_code" value="{{ get_setting('meta_capi_test_event_code') }}" placeholder="{{ __('e.g. TEST12345 (Leave empty for Live Production)') }}">
                        <small class="text-muted">{{ __('From Events Manager > Test Events tab') }}</small>
                    </div>

                    <div class="field col-span-1 md:col-span-2">
                        <label>{{ __('Meta Conversions API (CAPI) Access Token') }}</label>
                        <input type="hidden" name="types[]" value="meta_capi_access_token">
                        <textarea name="meta_capi_access_token" rows="3" placeholder="{{ __('EAAG... (Generate from Events Manager > Settings > Conversions API > Generate Access Token)') }}">{{ get_setting('meta_capi_access_token') }}</textarea>
                        <small class="text-muted">{{ __('Required for Server-Side conversion events to reach Meta with 100% bypass of ad-blockers and iOS privacy restrictions.') }}</small>
                    </div>
                </div>

                <p class="section-label mt-6">{{ __('Google Tag Manager & DataLayer (GA4 / E-commerce Tracking)') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Google Tag Manager (GTM) Container ID') }}</label>
                        <input type="hidden" name="types[]" value="gtm_id">
                        <input type="text" name="gtm_id" value="{{ get_setting('gtm_id') ?: env('GTM_ID') }}" placeholder="GTM-XXXXXXX">
                        <small class="text-muted">{{ __('e.g. GTM-XXXXXXX. Window DataLayer is always automatically enabled across all pages.') }}</small>
                    </div>

                    <div class="field">
                        <label>{{ __('Google Analytics 4 (GA4) Measurement ID (Optional)') }}</label>
                        <input type="hidden" name="types[]" value="ga4_measurement_id">
                        <input type="text" name="ga4_measurement_id" value="{{ get_setting('ga4_measurement_id') ?: env('GA4_MEASUREMENT_ID') }}" placeholder="G-XXXXXXXXXX">
                        <small class="text-muted">{{ __('Optional if already configured via Google Tag Manager.') }}</small>
                    </div>
                </div>

                <div class="footer-row">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Apply settings') }}</span>
                        <span class="btn-loader hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Legal & Policies Tab --}}
        <div id="tab-legal" class="tab-content hidden">
            <form method="POST" action="{{ route('setting.update') }}">
                @csrf

                <p class="section-label">{{ __('Business Legal Registration') }}</p>
                <div class="field-grid grid grid-cols-1 gap-5">
                    <div class="field">
                        <label>{{ __('Govt. Trade License & BIN Information') }}</label>
                        <input type="hidden" name="types[]" value="com_trade_license">
                        <input type="text" name="com_trade_license" value="{{ get_setting('com_trade_license') ?: 'Govt. Trade License No: TRAD/DNCC/023910/2021 | BIN: 001239845-0101' }}" placeholder="{{ __('e.g. Govt. Trade License No: TRAD/DNCC/023910/2021 | BIN: 001239845-0101') }}">
                        <small class="text-muted">{{ __('This text appears at the bottom of the frontend footer on all pages.') }}</small>
                    </div>
                </div>

                <p class="section-label mt-6">{{ __('Frontend Policy Documents Content') }}</p>
                <p class="text-xs text-muted mb-4">{{ __('Leave empty to use official standard defaults, or customize with your own terms & policy content.') }}</p>

                <div class="field-grid grid grid-cols-1 gap-6">
                    <!-- Privacy Policy -->
                    <div class="field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-weight-bold">{{ __('Privacy Policy Content') }}</label>
                            <a href="{{ route('frontend.privacy-policy') }}" target="_blank" class="text-xs text-primary font-weight-bold flex items-center gap-1">
                                <span>{{ __('View Page') }}</span> &rarr;
                            </a>
                        </div>
                        <input type="hidden" name="types[]" value="privacy_policy_content">
                        <textarea name="privacy_policy_content" rows="6" class="form-control" placeholder="{{ __('Write Privacy Policy details here...') }}">{{ get_setting('privacy_policy_content') }}</textarea>
                    </div>

                    <!-- Terms of Service -->
                    <div class="field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-weight-bold">{{ __('Terms of Service Content') }}</label>
                            <a href="{{ route('frontend.terms-of-service') }}" target="_blank" class="text-xs text-primary font-weight-bold flex items-center gap-1">
                                <span>{{ __('View Page') }}</span> &rarr;
                            </a>
                        </div>
                        <input type="hidden" name="types[]" value="terms_service_content">
                        <textarea name="terms_service_content" rows="6" class="form-control" placeholder="{{ __('Write Terms of Service & sales conditions here...') }}">{{ get_setting('terms_service_content') }}</textarea>
                    </div>

                    <!-- Warranty Terms -->
                    <div class="field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-weight-bold">{{ __('Official Warranty Terms & Policy Content') }}</label>
                            <a href="{{ route('frontend.warranty-terms') }}" target="_blank" class="text-xs text-primary font-weight-bold flex items-center gap-1">
                                <span>{{ __('View Page') }}</span> &rarr;
                            </a>
                        </div>
                        <input type="hidden" name="types[]" value="warranty_terms_content">
                        <textarea name="warranty_terms_content" rows="6" class="form-control" placeholder="{{ __('Write official brand warranty coverage, sensor lab guidelines, and claim rules here...') }}">{{ get_setting('warranty_terms_content') }}</textarea>
                    </div>

                    <!-- Site Map Intro / Notes -->
                    <div class="field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-weight-bold">{{ __('Site Map & Directory Introduction') }}</label>
                            <a href="{{ route('frontend.sitemap') }}" target="_blank" class="text-xs text-primary font-weight-bold flex items-center gap-1">
                                <span>{{ __('View Page') }}</span> &rarr;
                            </a>
                        </div>
                        <input type="hidden" name="types[]" value="sitemap_note_content">
                        <textarea name="sitemap_note_content" rows="3" class="form-control" placeholder="{{ __('Introductory note for the Site Map and Catalog Directory...') }}">{{ get_setting('sitemap_note_content') }}</textarea>
                    </div>
                </div>

                <div class="footer-row mt-6">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Save Legal & Policies') }}</span>
                        <span class="btn-loader hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('js')
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(function(c) {
            c.classList.add('hidden');
            c.classList.remove('block');
        });
        document.getElementById('tab-' + tabId).classList.replace('hidden', 'block');

        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('active-tab');
            b.classList.add('inactive-tab');
        });
        document.getElementById('tab-btn-' + tabId).classList.add('active-tab');
        document.getElementById('tab-btn-' + tabId).classList.remove('inactive-tab');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var urlParams = new URLSearchParams(window.location.search);
        var tab = urlParams.get('tab');
        if (tab && document.getElementById('tab-' + tab)) {
            switchTab(tab);
        }
    });

    function readURLIcon(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_icon').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function readURLLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_logo').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function readURLLoginBg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_login_bg').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
