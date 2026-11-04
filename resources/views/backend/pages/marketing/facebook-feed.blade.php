@extends('backend.layouts.master')
@section('section-title', __('Marketing'))
@section('page-title', __('Facebook & Instagram Product Feed'))

@section('action-button')
    <a href="{{ $feedUrl }}" target="_blank" class="btn btn-outline-primary" style="margin-right: 8px;">
        <i class="feather icon-external-link mr-1"></i> {{ __('View XML Feed') }}
    </a>
    <form action="{{ route('facebook.feed.clear-cache') }}" method="POST" style="display: inline-block;">
        @csrf
        <button type="submit" class="btn btn-warning">
            <i class="feather icon-refresh-cw mr-1"></i> {{ __('Clear Cache & Regenerate') }}
        </button>
    </form>
@endsection

@section('content')
<div class="row">
    <!-- Top Highlights & Feed URL Box -->
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="feather icon-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card m-b-30 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="mb-1 font-weight-bold text-dark" style="font-size: 20px;">
                            <i class="feather icon-rss text-primary mr-2"></i> {{ __('Meta / Facebook Commerce Catalog Feed') }}
                        </h4>
                        <p class="text-muted mb-0" style="font-size: 13.5px;">
                            {{ __('Dynamic XML Product Feed strictly compatible with Facebook Commerce Manager, Meta Catalog Ads, and Instagram Shopping.') }}
                        </p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; border-radius: 20px;">
                            <i class="feather icon-check mr-1"></i> {{ __('Real-Time Dynamic Sync Active') }}
                        </span>
                        @if($isCached)
                            <span class="badge badge-info px-3 py-2 font-weight-bold ml-1" style="font-size: 12px; border-radius: 20px;">
                                <i class="feather icon-zap mr-1"></i> {{ __('Cached for Fast Meta Crawling') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-3 mb-3 bg-light rounded" style="border: 1px solid #cbd5e1; border-radius: 10px;">
                    <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">
                        <i class="feather icon-link mr-1 text-primary"></i> {{ __('Your Facebook Product Feed URL (XML)') }}
                    </label>
                    <div class="input-group">
                        <input type="text" id="feed-url-input" class="form-control font-weight-bold text-primary bg-white" value="{{ $feedUrl }}" readonly style="font-size: 14px; height: 46px; border-radius: 8px 0 0 8px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary px-4 font-weight-bold" type="button" onclick="copyFeedUrl('feed-url-input')" style="border-radius: 0 8px 8px 0;">
                                <i class="feather icon-copy mr-1"></i> {{ __('Copy URL') }}
                            </button>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="feather icon-info mr-1 text-info"></i> {{ __('Paste this URL into Facebook Commerce Manager > Catalog > Data Sources > Data feeds (Scheduled Feed).') }}
                    </small>
                </div>

                <!-- Secondary Formats -->
                <div class="d-flex flex-wrap gap-2 align-items-center text-muted" style="font-size: 13px;">
                    <span class="font-weight-bold mr-2 text-dark">{{ __('Alternative Links:') }}</span>
                    <a href="{{ $feedUrl }}?refresh=1" target="_blank" class="mr-3 text-primary">
                        <i class="feather icon-refresh-cw mr-1"></i> {{ __('Live Uncached XML') }}
                    </a>
                    <a href="{{ $csvUrl }}" target="_blank" class="text-success">
                        <i class="feather icon-file-text mr-1"></i> {{ __('Download CSV Feed') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="card m-b-30 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary mr-3" style="width: 50px; height: 50px; background: rgba(30, 41, 59, 0.08);">
                    <i class="feather icon-box text-primary" style="font-size: 24px;"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 font-weight-bold" style="font-size: 12px; text-transform: uppercase;">{{ __('Total in Feed') }}</h6>
                    <h4 class="mb-0 font-weight-bold text-dark">{{ $totalActive }} {{ __('Products') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card m-b-30 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1);">
                    <i class="feather icon-check-circle text-success" style="font-size: 24px;"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 font-weight-bold" style="font-size: 12px; text-transform: uppercase;">{{ __('In Stock') }}</h6>
                    <h4 class="mb-0 font-weight-bold text-success">{{ $inStock }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card m-b-30 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(239, 68, 68, 0.1);">
                    <i class="feather icon-alert-triangle text-danger" style="font-size: 24px;"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 font-weight-bold" style="font-size: 12px; text-transform: uppercase;">{{ __('Out of Stock') }}</h6>
                    <h4 class="mb-0 font-weight-bold text-danger">{{ $outOfStock }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card m-b-30 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1);">
                    <i class="feather icon-percent text-warning" style="font-size: 24px;"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 font-weight-bold" style="font-size: 12px; text-transform: uppercase;">{{ __('On Sale / Discounted') }}</h6>
                    <h4 class="mb-0 font-weight-bold text-warning">{{ $discounted }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Step-by-Step Facebook Catalog Setup Guide -->
    <div class="col-lg-12">
        <div class="card m-b-30 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0;">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 font-weight-bold text-dark">
                    <i class="feather icon-help-circle text-primary mr-1"></i> {{ __('How to Connect this Feed to Facebook Commerce Manager (Step-by-Step)') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 rounded h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <span class="badge badge-primary mr-1">1</span> {{ __('Facebook Commerce Manager-এ প্রবেশ করুন') }}
                            </h6>
                            <p class="text-muted mb-2" style="font-size: 13.5px;">
                                প্রথমে আপনার ব্রাউজারে <a href="https://business.facebook.com/commerce" target="_blank" class="font-weight-bold text-primary">Facebook Commerce Manager</a> ওপেন করুন এবং আপনার Business Account সিলেক্ট করুন।
                            </p>
                            <h6 class="font-weight-bold text-primary mb-2 mt-3">
                                <span class="badge badge-primary mr-1">2</span> {{ __('Catalog > Data Sources-এ যান') }}
                            </h6>
                            <p class="text-muted mb-0" style="font-size: 13.5px;">
                                বাম পাশের মেনু থেকে <strong>Catalog</strong> &gt; <strong>Data Sources</strong> অপশনে ক্লিক করুন। এরপর <strong>Data feeds</strong> (বা <i>Add Items</i> &gt; <i>Data feed</i>) সিলেক্ট করুন।
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 rounded h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <span class="badge badge-primary mr-1">3</span> {{ __('Scheduled Feed সিলেক্ট করুন এবং URL দিন') }}
                            </h6>
                            <p class="text-muted mb-2" style="font-size: 13.5px;">
                                আপলোড অপশনে <strong>Set a schedule</strong> সিলেক্ট করুন। উপরের বক্সে থাকা <code>{{ $feedUrl }}</code> কপি করে সেখানে পেস্ট করুন।
                            </p>
                            <h6 class="font-weight-bold text-primary mb-2 mt-3">
                                <span class="badge badge-primary mr-1">4</span> {{ __('শিডিউল এবং কারেন্সি সেট করুন') }}
                            </h6>
                            <p class="text-muted mb-0" style="font-size: 13.5px;">
                                <strong>Frequency:</strong> প্রতি ১ ঘণ্টা (Hourly) অথবা প্রতিদিন (Daily) দিন। <strong>Default Currency:</strong> <code>BDT</code> (Bangladeshi Taka) সিলেক্ট করুন। এরপর <strong>Save Feed and Upload</strong>-এ ক্লিক করলেই সব প্রোডাক্ট অটোমেটিক ফেবু ক্যাটালগে সিঙ্ক হয়ে যাবে!
                            </p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mb-0 mt-2" style="border-radius: 8px;">
                    <div class="d-flex">
                        <i class="feather icon-shield mr-2 mt-1" style="font-size: 18px;"></i>
                        <div style="font-size: 13px;">
                            <strong>Dynamic Product Ads (DPA) Compatibility:</strong> 
                            এই ফিডের প্রতিটি প্রোডাক্টের <code>&lt;g:id&gt;</code> ওয়েবসাইট মেটা পিক্সেল (Meta Pixel) এবং Conversions API (CAPI)-এর <code>content_ids</code>-এর সাথে ১০০% ম্যাচ করে তৈরি করা হয়েছে। ফলে Dynamic Retargeting Ads, Catalog Sales Ads, এবং Instagram Shopping-এ ১০০% ক্যাটালগ ম্যাচ রেট পাওয়া যাবে।
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Preview Table -->
    <div class="col-lg-12">
        <div class="card m-b-30 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold text-dark">
                    <i class="feather icon-eye text-primary mr-1"></i> {{ __('Feed Catalog Live Preview (First 12 Products)') }}
                </h5>
                <span class="text-muted" style="font-size: 13px;">{{ __('Real-time database reflection') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 13.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 70px;">{{ __('g:id') }}</th>
                                <th style="width: 70px;">{{ __('Image') }}</th>
                                <th>{{ __('Product Title (g:title)') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Brand') }}</th>
                                <th>{{ __('Price (g:price)') }}</th>
                                <th>{{ __('Sale Price') }}</th>
                                <th>{{ __('Stock Status') }}</th>
                                <th class="text-right">{{ __('Link') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($previewProducts as $item)
                                @php
                                    $sPrice = (float)($item->selling_price ?? 0);
                                    $dPrice = (float)($item->dis_selling_price ?? 0);
                                    $isDiscount = ($dPrice > 0 && $dPrice > $sPrice);
                                    $regPrice = $isDiscount ? $dPrice : $sPrice;
                                    $salePrice = $isDiscount ? $sPrice : null;
                                    $inStock = ($item->main_qty === null || (float)$item->main_qty > 0);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge badge-secondary font-weight-bold">{{ $item->id }}</span>
                                    </td>
                                    <td>
                                        <img src="{{ $item->thumbnail_url ?: asset('backend/images/no_images.png') }}" alt="{{ $item->name }}" style="width: 44px; height: 44px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff;">
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-dark">{{ $item->name }}</span>
                                        @if($item->barcode)
                                            <small class="text-muted d-block">SKU: {{ $item->barcode }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->category?->name ?? '-' }}</td>
                                    <td>{{ $item->brand?->name ?? 'CAMERA BD' }}</td>
                                    <td>
                                        <span class="font-weight-bold {{ $isDiscount ? 'text-muted' : 'text-dark' }}" style="{{ $isDiscount ? 'text-decoration: line-through;' : '' }}">
                                            ৳ {{ number_format($regPrice) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($salePrice)
                                            <span class="font-weight-bold text-danger">
                                                ৳ {{ number_format($salePrice) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inStock)
                                            <span class="badge badge-success px-2 py-1 font-weight-bold">{{ __('in stock') }}</span>
                                        @else
                                            <span class="badge badge-danger px-2 py-1 font-weight-bold">{{ __('out of stock') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ url('/product-details/' . ($item->slug ?: $item->id)) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Product Page">
                                            <i class="feather icon-external-link"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        {{ __('No products found in feed.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyFeedUrl(inputId) {
    var copyText = document.getElementById(inputId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        if (typeof toastr !== 'undefined') {
            toastr.success('Feed URL copied to clipboard!');
        } else {
            alert('Feed URL copied to clipboard: ' + copyText.value);
        }
    });
}
</script>
@endsection
