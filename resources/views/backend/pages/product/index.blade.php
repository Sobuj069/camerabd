@extends('backend.layouts.master')
@section('section-title', __('Product'))
@section('page-title', __('Product List'))
@if (check_permission('product.create'))
    @section('action-button')
        <a href="{{ route('product.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Product') }}
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {
            @page {
                size: auto;
            }

            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                font-family: Roboto, sans-serif;
            }

            .print_area {
                position: absolute;
                top: 0;
                width: 100%;
            }

            .print_area * {
                visibility: visible !important;
            }
        }
        #barcode-page svg, .print_area svg {
            max-width: 100% !important;
            height: 26px !important;
            display: block !important;
            margin: 0 auto !important;
            color-adjust: exact !important;
            -webkit-print-color-adjust: exact !important;
        }
        /* Homepage section enhancements */
        .section-toggle-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            white-space: nowrap;
        }
        .section-toggle-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.18);
        }
        .section-toggle-btn.active-section {
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        }
        .section-toggle-btn .remove-x {
            display: inline-block;
            font-weight: bold;
            margin-left: 3px;
            font-size: 13px;
            line-height: 1;
        }
        tr.product-selected-row {
            background-color: #f0fdf4 !important;
        }
        #bulkActionBar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3) !important;
            position: sticky;
            top: 15px;
            z-index: 999;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <!-- Homepage Section Filter Tabs -->
                    <div class="mb-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between p-3 rounded shadow-xs mb-3" style="background: linear-gradient(135deg, #102d28 0%, #0d3b32 100%); color: #fff;">
                            <div>
                                <h5 class="mb-1 text-white font-weight-bold d-flex align-items-center">
                                    <i class="feather icon-layout mr-2 text-warning"></i> {{ __('Homepage Sections Management') }}
                                </h5>
                                <p class="mb-0 text-white-50 small">{{ __('Click on a section tab to filter assigned products, or use the quick toggle buttons in the table.') }}</p>
                            </div>
                            <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
                                <button type="button" class="btn btn-success font-weight-bold shadow-sm" data-toggle="modal" data-target="#addNewSectionModal">
                                    <i class="feather icon-plus mr-1"></i> {{ __('+ Add New Section') }}
                                </button>
                                <button type="button" class="btn btn-info font-weight-bold shadow-sm" data-toggle="modal" data-target="#manageSectionsModal">
                                    <i class="feather icon-settings mr-1"></i> {{ __('Manage All Sections') }}
                                </button>
                                <button type="button" class="btn btn-danger font-weight-bold shadow-sm" data-toggle="modal" data-target="#flashDealSettingsModal">
                                    <i class="feather icon-clock mr-1"></i> {{ __('Flash Timer') }}
                                </button>
                                <button type="button" class="btn text-white font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #00897b 0%, #004d40 100%); border: none;" data-toggle="modal" data-target="#hollylandSettingsModal">
                                    <i class="feather icon-mic mr-1"></i> {{ __('Hollyland Settings') }}
                                </button>
                                <button type="button" class="btn btn-warning font-weight-bold shadow-sm" data-toggle="modal" data-target="#bulkAssignSectionModal">
                                    <i class="feather icon-check-square mr-1"></i> {{ __('Bulk Assign') }}
                                </button>
                            </div>
                        </div>

                        <!-- Section Nav Pills -->
                        <div class="p-2 bg-white rounded border shadow-xs d-flex flex-wrap align-items-center" style="gap: 6px;">
                            <a href="{{ route('product.index', array_merge(request()->except(['section', 'page']), ['section' => 'all'])) }}"
                               class="btn btn-sm {{ ($activeSection ?? 'all') === 'all' ? 'btn-dark' : 'btn-outline-secondary' }} font-weight-bold">
                                <i class="feather icon-grid mr-1"></i> {{ __('All Products') }}
                                <span class="badge {{ ($activeSection ?? 'all') === 'all' ? 'badge-light text-dark' : 'badge-secondary' }} ml-1" id="tabCountAll">{{ $totalProductsCount ?? 0 }}</span>
                            </a>

                            @if(isset($homepageSections) && count($homepageSections) > 0)
                                @foreach($homepageSections as $sec)
                                    @php
                                        $secKeyOrId = $sec->key ?: (string)$sec->id;
                                        $isActive = ($activeSection ?? '') == $secKeyOrId;
                                        $cnt = $sectionCounts[$secKeyOrId] ?? ($sec->products_count ?? 0);
                                        $accentColor = $sec->accent_color ?: ($sec->key === 'flash_deal' ? '#ef4444' : ($sec->key === 'popular' ? '#3b82f6' : ($sec->key === 'hollyland' ? '#10b981' : '#06b6d4')));
                                    @endphp
                                    <a href="{{ route('product.index', array_merge(request()->except(['section', 'page']), ['section' => $secKeyOrId])) }}"
                                       class="btn btn-sm font-weight-bold {{ $isActive ? 'text-white' : 'btn-outline-secondary' }}"
                                       style="{{ $isActive ? 'background-color: ' . $accentColor . '; border-color: ' . $accentColor . ';' : '' }}">
                                        @if($sec->key === 'flash_deal')
                                            ⚡
                                        @elseif($sec->key === 'popular')
                                            🔥
                                        @elseif($sec->key === 'hollyland')
                                            🎙️
                                        @elseif($sec->key === 'essential')
                                            🔌
                                        @else
                                            <span class="feather icon-tag mr-1"></span>
                                        @endif
                                        {{ $sec->name }}
                                        <span class="badge {{ $isActive ? 'badge-light text-dark' : 'badge-secondary' }} ml-1" id="tabCount_{{ $secKeyOrId }}">{{ $cnt }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>

                        @php
                            $activeSecObj = isset($homepageSections) ? $homepageSections->first(function($item) use ($activeSection) {
                                return ($item->key && $item->key === $activeSection) || ((string)$item->id === (string)$activeSection);
                            }) : null;
                        @endphp

                        @if(($activeSection ?? '') === 'flash_deal')
                            @php
                                $curEndDate = get_setting('flash_deal_end_date');
                                $curTitle = get_setting('flash_deal_title') ?: ($activeSecObj->name ?? 'Broadcast & Studio Flash');
                                $curBadge = get_setting('flash_deal_badge') ?: ($activeSecObj->badge ?? 'Limited Studio Drops');
                            @endphp
                            <div class="alert alert-danger d-flex flex-wrap align-items-center justify-content-between p-3 rounded shadow-xs mt-3 mb-1" style="background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b;">
                                <div>
                                    <h6 class="font-weight-bold mb-1 d-flex align-items-center" style="color: #991b1b;">
                                        <i class="feather icon-clock mr-2"></i> {{ __('Flash Deal Live Timer & Offer Settings') }}
                                    </h6>
                                    <p class="mb-0 small" style="color: #4b5563;">
                                        <strong>{{ __('Offer Ends At') }}:</strong> 
                                        <span class="badge badge-danger px-2 py-1" style="font-size: 13px;">{{ $curEndDate ? date('d M, Y - h:i A', strtotime($curEndDate)) : __('Not Configured Yet (Default: 7 Days)') }}</span>
                                        <span class="mx-2">|</span>
                                        <strong>{{ __('Title') }}:</strong> {{ $curTitle }}
                                        <span class="mx-2">|</span>
                                        <strong>{{ __('Badge') }}:</strong> {{ $curBadge }}
                                    </p>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <button type="button" class="btn btn-sm btn-danger font-weight-bold shadow-xs" data-toggle="modal" data-target="#flashDealSettingsModal">
                                        <i class="feather icon-edit mr-1"></i> {{ __('Change Timer / Date') }}
                                    </button>
                                </div>
                            </div>
                        @elseif(($activeSection ?? '') === 'hollyland')
                            @php
                                $hlSecTitle = get_setting('hollyland_section_title') ?: ($activeSecObj->name ?? 'Hollyland Microphone');
                                $hlBanBrand = get_setting('hollyland_banner_brand') ?: 'HOLLYLAND';
                                $hlBanTitle = get_setting('hollyland_banner_title') ?: 'PURE SOUND. ZERO LIMITS.';
                            @endphp
                            <div class="alert d-flex flex-wrap align-items-center justify-content-between p-3 rounded shadow-xs mt-3 mb-1" style="background-color: #f0faf7; border: 1px solid #bce8dd; color: #006a61;">
                                <div>
                                    <h6 class="font-weight-bold mb-1 d-flex align-items-center" style="color: #006a61;">
                                        <i class="feather icon-mic mr-2"></i> {{ __('Hollyland Audio Showcase & Banner Settings') }}
                                    </h6>
                                    <p class="mb-0 small" style="color: #374151;">
                                        <strong>{{ __('Section Title') }}:</strong> {{ $hlSecTitle }}
                                        <span class="mx-2">|</span>
                                        <strong>{{ __('Brand') }}:</strong> {{ $hlBanBrand }}
                                        <span class="mx-2">|</span>
                                        <strong>{{ __('Headline') }}:</strong> {{ $hlBanTitle }}
                                    </p>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <button type="button" class="btn btn-sm text-white font-weight-bold shadow-xs" style="background: #006a61;" data-toggle="modal" data-target="#hollylandSettingsModal">
                                        <i class="feather icon-edit mr-1"></i> {{ __('Edit Showcase Banner & Titles') }}
                                    </button>
                                </div>
                            </div>
                        @elseif($activeSecObj)
                            <div class="alert d-flex flex-wrap align-items-center justify-content-between p-3 rounded shadow-xs mt-3 mb-1" style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid {{ $activeSecObj->accent_color ?: '#006a61' }};">
                                <div>
                                    <h6 class="font-weight-bold mb-1 d-flex align-items-center" style="color: #1e293b;">
                                        <span class="mr-2" style="color: {{ $activeSecObj->accent_color ?: '#006a61' }};">●</span>
                                        {{ $activeSecObj->name }}
                                        <span class="badge ml-2 text-white font-weight-bold" style="background-color: {{ $activeSecObj->accent_color ?: '#006a61' }}; font-size: 11px;">
                                            {{ $activeSecObj->badge ?: 'Active Section' }}
                                        </span>
                                    </h6>
                                    <p class="mb-0 small text-muted">
                                        {{ $activeSecObj->subtitle ?: __('Products assigned here will appear in this section on the website homepage.') }}
                                    </p>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold edit-section-btn" 
                                            data-id="{{ $activeSecObj->id }}" 
                                            data-name="{{ $activeSecObj->name }}" 
                                            data-badge="{{ $activeSecObj->badge }}" 
                                            data-tag="{{ $activeSecObj->tag }}" 
                                            data-subtitle="{{ $activeSecObj->subtitle }}" 
                                            data-icon="{{ $activeSecObj->icon }}" 
                                            data-accent_color="{{ $activeSecObj->accent_color }}" 
                                            data-is_system="{{ $activeSecObj->is_system ? 1 : 0 }}">
                                        <i class="feather icon-edit mr-1"></i> {{ __('Edit Section Info') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('product.index') }}" method="GET" class="mb-2">
                        <input type="hidden" name="section" value="{{ $activeSection ?? 'all' }}">
                        @php
                            $categories = App\Models\Category::get();
                            $produc = App\Models\Product::get();
                        @endphp
                        <div class="row align-items-center">

                            <div class="col-md-3 mt-1">
                                <input type="text" class="form-control" name="barcode" placeholder="{{ __('Enter Barcode') }}"
                                    value="{{ $barcode ?? '' }}" />
                            </div>

                            <div class="col-md-3 mt-1">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('All Product') }}</option>
                                    @foreach ($produc as $item)
                                        <option value="{{ $item->id }}"{{ ($product_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-{{ env('APP_SUB_CATEGORY') == 'yes' ? '2' : '3' }} mt-1">
                                <select name="category_id" id="" class="select2">
                                    <option value="">{{ __('All Category') }}</option>
                                    @foreach ($categories as $item)
                                        <option
                                            value="{{ $item->id }}"{{ ($category_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="col-md-2 mt-1">
                                    <select name="sub_category_id" id="" class="select2">
                                        <option value="">{{ __('All Sub Category') }}</option>
                                        @foreach ($subCategories ?? [] as $subItem)
                                            <option
                                                value="{{ $subItem->id }}"{{ ($sub_category_id ?? '') == $subItem->id ? 'selected' : '' }}>
                                                {{ $subItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-{{ env('APP_SUB_CATEGORY') == 'yes' ? '2' : '3' }} mt-1 d-flex align-items-center" style="gap: 5px;">
                                <button type="submit" class="btn add_list_btn px-3">{{ __('Filter') }}</button>
                                <a href="{{ route('product.index') }}" class="btn add_list_btn_reset px-3">{{ __('Reset') }}</a>
                                <button type="button" class="btn add_list_btn ml-auto px-2" onclick="window.print()" title="{{ __('Print') }}"><i class="fa fa-print"></i></button>
                            </div>
                        </div>
                    </form>

                    <!-- Sticky Bulk Action Toolbar (Appears when 1+ rows selected) -->
                    <div id="bulkActionBar" class="card shadow-lg border-0 mb-3" style="display: none;">
                        <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                <span class="badge badge-warning px-3 py-2 text-dark font-weight-bold shadow-xs" id="bulkSelectedBadge" style="font-size: 13px;">
                                    <i class="feather icon-check-square mr-1"></i> <span id="selectedProductsCount">0</span> {{ __('Selected') }}
                                </span>
                                <span id="allStoreSelectedNotice" class="badge badge-info px-2.5 py-2 font-weight-bold text-white shadow-xs" style="display: none; font-size: 13px;">
                                    <i class="feather icon-layers mr-1"></i> {{ __('ALL :count Products in Store Selected', ['count' => $totalProductsCount ?? 0]) }}
                                </span>

                                @if($activeSecObj)
                                    <button type="button" class="btn btn-sm btn-danger font-weight-bold shadow-xs quick-bulk-remove-current-btn" data-section="{{ $activeSecObj->key ?: $activeSecObj->id }}">
                                        <i class="feather icon-trash-2 mr-1"></i> {{ __('Remove Selected from') }} {{ $activeSecObj->name }}
                                    </button>
                                @endif

                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-danger dropdown-toggle font-weight-bold text-white shadow-xs" type="button" id="bulkRemoveDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-color: #ef4444;">
                                        <i class="feather icon-minus-circle mr-1 text-danger"></i> {{ __('Remove from Section...') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-dark shadow" aria-labelledby="bulkRemoveDropdown" style="background: #1e293b; border: 1px solid #334155; max-height: 280px; overflow-y: auto;">
                                        @if(isset($homepageSections))
                                            @foreach($homepageSections as $sec)
                                                <a class="dropdown-item text-white py-2 bulk-section-action-btn" href="javascript:void(0)" data-action="remove" data-section="{{ $sec->key ?: $sec->id }}">
                                                    @if($sec->key === 'flash_deal') ⚡ @elseif($sec->key === 'popular') 🔥 @elseif($sec->key === 'hollyland') 🎙️ @elseif($sec->key === 'essential') 🔌 @else <span class="feather icon-tag mr-1"></span> @endif
                                                    {{ $sec->name }}
                                                </a>
                                            @endforeach
                                        @endif
                                        <div class="dropdown-divider" style="border-color: #334155;"></div>
                                        <a class="dropdown-item text-danger font-weight-bold py-2 bulk-section-action-btn" href="javascript:void(0)" data-action="remove" data-section="all">
                                            ❌ {{ __('Remove from ALL Homepage Sections') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-success dropdown-toggle font-weight-bold text-white shadow-xs" type="button" id="bulkAddDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-color: #22c55e;">
                                        <i class="feather icon-plus-circle mr-1 text-success"></i> {{ __('Add to Section...') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-dark shadow" aria-labelledby="bulkAddDropdown" style="background: #1e293b; border: 1px solid #334155; max-height: 280px; overflow-y: auto;">
                                        @if(isset($homepageSections))
                                            @foreach($homepageSections as $sec)
                                                <a class="dropdown-item text-white py-2 bulk-section-action-btn" href="javascript:void(0)" data-action="add" data-section="{{ $sec->key ?: $sec->id }}">
                                                    @if($sec->key === 'flash_deal') ⚡ @elseif($sec->key === 'popular') 🔥 @elseif($sec->key === 'hollyland') 🎙️ @elseif($sec->key === 'essential') 🔌 @else <span class="feather icon-tag mr-1"></span> @endif
                                                    {{ $sec->name }}
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="btn-group ml-1">
                                    <button type="button" class="btn btn-sm btn-outline-success font-weight-bold bulk-website-action-btn" data-status="1" title="{{ __('Show selected products on website') }}">
                                        <i class="feather icon-eye mr-1"></i> {{ __('Show on Website') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold text-white bulk-website-action-btn" data-status="0" title="{{ __('Hide selected products from website') }}">
                                        <i class="feather icon-eye-off mr-1"></i> {{ __('Hide from Website') }}
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <button type="button" class="btn btn-sm btn-secondary font-weight-bold" id="clearBulkSelectionBtn">
                                    <i class="feather icon-x mr-1"></i> {{ __('Deselect All') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left text-center" style="width: 40px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkAllProducts" style="cursor: pointer;">
                                            <label class="custom-control-label" for="checkAllProducts" style="cursor: pointer;"></label>
                                        </div>
                                    </th>
                                    <th>{{ __('#SL') }}</th>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Barcode') }}</th>
                                    @if (env('APP_IMEI') == 'yes')
                                        <th>{{ __('IMEI') }}</th>
                                    @endif
                                    <th>{{ __('Category') }}</th>
                                    @if (env('APP_SUB_CATEGORY') == 'yes')
                                        <th>{{ __('Sub Category') }}</th>
                                    @endif
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="text-center">{{ __('Website') }}</th>
                                    <th>{{ __('Homepage Sections') }}</th>
                                    <th></th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $data)
                                    @php
                                        $count_inv = App\Models\InvoiceItem::where('product_id', $data->id)->count();
                                        $count_pur = App\Models\PurchaseItem::where('product_id', $data->id)->count();

                                        $userBranchId = auth()->user()->branch_id;
                                        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                        if ($userBranchId == 1) {
                                            if ($filterBranchId) {
                                                $branch_product = App\Models\BranchProduct::where(
                                                    'branch_id',
                                                    $filterBranchId,
                                                )
                                                    ->where('product_id', $data->id)
                                                    ->get();
                                            } else {
                                                $branch_product = App\Models\BranchProduct::where(
                                                    'product_id',
                                                    $data->id,
                                                )->get();
                                            }
                                        } else {
                                            $branch_product = App\Models\BranchProduct::where('product_id', $data->id)
                                                ->where('branch_id', $userBranchId)
                                                ->get();
                                        }
                                    @endphp
                                    <tr id="product_row_{{ $data->id }}">
                                        <td class="table_data_style_left text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input product-select-checkbox" id="product_cb_{{ $data->id }}" value="{{ $data->id }}"
                                                       data-name="{{ $data->name }}"
                                                       data-flash="{{ $data->is_flash_deal }}" data-popular="{{ $data->is_popular }}" data-hollyland="{{ $data->is_hollyland }}" data-essential="{{ $data->is_essential }}">
                                                <label class="custom-control-label" for="product_cb_{{ $data->id }}" style="cursor: pointer;"></label>
                                            </div>
                                        </td>
                                        <td>{{ $products->firstItem() ? ($products->firstItem() + $loop->index) : ($loop->index + 1) }}</td>
                                        <td>
                                            <div class="position-relative d-inline-block">
                                                <img src="{{ $data->featured_image }}" class="rounded border shadow-xs" style="width: 44px; height: 44px; object-fit: contain; background: #fff;" alt="{{ $data->name }}">
                                                @if(count($data->gallery_images) > 1)
                                                    <span class="badge badge-pill badge-primary position-absolute" style="bottom: -5px; right: -5px; font-size: 9px; padding: 2px 5px;" title="{{ count($data->gallery_images) }} {{ __('photos') }}">
                                                        +{{ count($data->gallery_images) - 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @foreach ($branch_product as $branch)
                                                <span class="badge badge-primary">{{ $branch->branch?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            {{ $data->name }}
                                            @if (env('APP_SC') == 'yes')
                                                @if (count($data->product_variations) > 0)
                                                    <div class="mt-2">
                                                        <select name="variation[]" class="form-control variation_select"
                                                            data-product-id="{{ $data->id }}" style="padding-right: 30px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; min-width: 120px; font-size: 13px; height: 32px; padding-top: 4px; padding-bottom: 4px;">
                                                            <option value="">{{ __('Select Variation') }}</option>
                                                            @foreach ($data->product_variations as $variation)
                                                                @if ($data->id == $variation->product_id)
                                                                    <option data-variation_id="{{ $variation->id }}"
                                                                        value="{{ $variation->size?->size }}-{{ $variation->color?->color }}">
                                                                        {{ $variation->size?->size }}-{{ $variation->color?->color }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $data->barcode }}</td>
                                        @if (env('APP_IMEI') == 'yes')
                                        <td>
                                            @if ($data->imei == '1')
                                                <span class="badge badge-success">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>{{ $data->category?->name }}</td>
                                        @if (env('APP_SUB_CATEGORY') == 'yes')
                                            <td>{{ $data->subCategory?->name ?? '-' }}</td>
                                        @endif
                                        <td>{{ $data->purchase_price }}</td>
                                        <td>{{ $data->selling_price }}</td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <div class="custom-control custom-switch d-inline-block" title="{{ $data->show_on_website ? __('Visible on Website (Click to hide)') : __('Hidden from Website (Click to show)') }}">
                                                <input type="checkbox" class="custom-control-input toggle-website-switch" id="web_switch_{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->show_on_website ? 'checked' : '' }} style="cursor: pointer;">
                                                <label class="custom-control-label" for="web_switch_{{ $data->id }}" style="cursor: pointer;"></label>
                                            </div>
                                            <div>
                                                <span class="badge {{ $data->show_on_website ? 'badge-success' : 'badge-secondary' }} website-badge-{{ $data->id }}" style="font-size: 10px; padding: 2px 6px;">
                                                    {{ $data->show_on_website ? __('Active') : __('Hidden') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $assignedSecIds = $data->homepageSections ? $data->homepageSections->pluck('id')->toArray() : [];
                                            @endphp
                                            <div class="d-flex flex-wrap" style="gap: 3px; max-width: 220px;">
                                                @if(isset($homepageSections))
                                                    @foreach($homepageSections as $sec)
                                                        @php
                                                            $secKeyOrId = $sec->key ?: (string)$sec->id;
                                                            $isAssigned = in_array($sec->id, $assignedSecIds) || 
                                                                ($sec->key === 'flash_deal' && $data->is_flash_deal) ||
                                                                ($sec->key === 'popular' && $data->is_popular) ||
                                                                ($sec->key === 'hollyland' && $data->is_hollyland) ||
                                                                ($sec->key === 'essential' && $data->is_essential);
                                                            
                                                            $color = $sec->accent_color ?: ($sec->key === 'flash_deal' ? '#ef4444' : ($sec->key === 'popular' ? '#3b82f6' : ($sec->key === 'hollyland' ? '#10b981' : '#06b6d4')));
                                                        @endphp
                                                        <button type="button" 
                                                            class="btn btn-xs section-toggle-btn {{ $isAssigned ? 'text-white font-weight-bold active-section' : 'btn-outline-secondary text-muted' }}" 
                                                            data-id="{{ $data->id }}" 
                                                            data-section="{{ $secKeyOrId }}" 
                                                            data-accent="{{ $color }}"
                                                            data-name="{{ $sec->name }}"
                                                            title="{{ $isAssigned ? __('Click to remove from :sec', ['sec' => $sec->name]) : __('Click to add to :sec', ['sec' => $sec->name]) }}" 
                                                            style="padding: 2px 6px; font-size: 11px; border-radius: 4px; {{ $isAssigned ? 'background-color: ' . $color . '; border-color: ' . $color . ';' : '' }}">
                                                            @if($sec->key === 'flash_deal') ⚡ Flash @elseif($sec->key === 'popular') 🔥 Popular @elseif($sec->key === 'hollyland') 🎙️ Hollyland @elseif($sec->key === 'essential') 🔌 Essential @else {{ $sec->name }} @endif
                                                            @if($isAssigned)<span class="remove-x">&times;</span>@endif
                                                        </button>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-primary-rgba generated_barcode"
                                                data-name="{{ $data->name }}" data-category="{{ $data->category?->name }}" data-code="{{ $data->barcode }}"
                                                data-id="{{ $data->id }}" data-dis_price="{{ $data->dis_selling_price }}" data-discount="{{ $data->discount }}" data-price="{{ $data->selling_price }}">
                                                <i class="fa fa-barcode"></i>
                                            </a>
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if (check_permission('product.edit'))
                                                        <a href="{{ route('product.edit', $data->id) }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('product.destroy'))
                                                        @if ($count_inv < 1 && $count_pur < 1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        {{ $products->onEachSide(1)->links() }}
                    </div>

                    @foreach ($products as $data)
                        @if (check_permission('product.destroy'))
                            <form action="{{ route('product.destroy', $data->id) }}" method="POST">
                                @csrf
                                <x-delete-modal title="{{ __('Product') }}" id="{{ $data->id }}" />
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bar_code_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Barcode Settings') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center" style="padding-bottom: 15px; border-bottom: 1px solid #eee;">
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_company" checked> {{ __('Company') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_category"> {{ __('Category') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_name" checked> {{ __('Name') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_variation" checked> {{ __('Variation') }}</label>
                        <label><input type="checkbox" class="barcode_setting" data-target=".b_price" checked> {{ __('Price') }}</label>
                    </div>
                    <div id="barcode-page">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancel_btn" id="modal_cancel">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn save_btn delete" onclick="print_barcode()"><i
                            class="fa fa-print mr-1"></i>
                        {{ __('Print') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Assign Homepage Section Modal -->
    <div class="modal fade" id="bulkAssignSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('product.bulk-assign-homepage-section') }}" method="POST">
                    @csrf
                    <div class="modal-header" style="background: linear-gradient(135deg, #102d28 0%, #0d3b32 100%); color: #fff;">
                        <h5 class="modal-title text-white">
                            <i class="feather icon-check-square mr-2"></i> {{ __('Homepage Sections Bulk Assign') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">{{ __('Target Homepage Section') }} <span class="text-danger">*</span></label>
                                <select name="section" class="form-control" required id="modal_target_section">
                                    @if(isset($homepageSections))
                                        @foreach($homepageSections as $sec)
                                            <option value="{{ $sec->key ?: $sec->id }}" {{ ($activeSection ?? '') == ($sec->key ?: $sec->id) ? 'selected' : '' }}>
                                                @if($sec->key === 'flash_deal') ⚡ @elseif($sec->key === 'popular') 🔥 @elseif($sec->key === 'hollyland') 🎙️ @elseif($sec->key === 'essential') 🔌 @else 🏷️ @endif
                                                {{ $sec->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                    <option value="all">❌ {{ __('All Homepage Sections (Remove Only)') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">{{ __('Action') }} <span class="text-danger">*</span></label>
                                <select name="action" class="form-control" required>
                                    <option value="add">➕ {{ __('Add Selected Products to Section') }}</option>
                                    <option value="remove">➖ {{ __('Remove Selected Products from Section') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="font-weight-bold mb-0">{{ __('Select Products') }} <span class="text-danger">*</span></label>
                                <div class="small">
                                    <a href="javascript:void(0)" id="selectAllModalProducts" class="text-primary mr-2 font-weight-bold">{{ __('Select All Filtered') }}</a>
                                    <a href="javascript:void(0)" id="deselectAllModalProducts" class="text-muted">{{ __('Deselect All') }}</a>
                                </div>
                            </div>
                            <input type="text" id="modalProductSearch" class="form-control form-control-sm mb-2" placeholder="{{ __('Type product name or barcode to filter list...') }}">
                            
                            <div class="border rounded p-2" style="max-height: 280px; overflow-y: auto; background: #fdfdfd;" id="modalProductListContainer">
                                @if(isset($allProductsList) && count($allProductsList) > 0)
                                    @foreach($allProductsList as $pItem)
                                        <div class="custom-control custom-checkbox modal-product-item py-1 border-bottom" data-search="{{ strtolower($pItem->name) }} {{ strtolower($pItem->barcode ?? '') }}">
                                            <input type="checkbox" name="product_ids[]" value="{{ $pItem->id }}" class="custom-control-input modal-p-checkbox" id="modal_p_{{ $pItem->id }}">
                                            <label class="custom-control-label d-flex justify-content-between align-items-center cursor-pointer" for="modal_p_{{ $pItem->id }}">
                                                <span class="font-weight-bold text-dark">{{ $pItem->name }}</span>
                                                <span class="text-muted small">৳ {{ number_format($pItem->selling_price, 2) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted text-center my-3">{{ __('No products found') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-success font-weight-bold">
                            <i class="feather icon-save mr-1"></i> {{ __('Save Section Products') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add New Homepage Section Modal -->
    <div class="modal fade" id="addNewSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('homepage-sections.store') }}" method="POST">
                    @csrf
                    <div class="modal-header" style="background: linear-gradient(135deg, #102d28 0%, #0d3b32 100%); color: #fff;">
                        <h5 class="modal-title text-white font-weight-bold">
                            <i class="feather icon-plus-circle mr-2 text-warning"></i> {{ __('Create New Homepage Section') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Section Name / Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g. DJI & Action Cameras" required>
                            <small class="text-muted">{{ __('This title will be displayed directly as the section heading on the website homepage.') }}</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">{{ __('Badge / Tag') }}</label>
                                <input type="text" name="badge" class="form-control" placeholder="e.g. Creator's Choice">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">{{ __('Accent Color') }}</label>
                                <div class="input-group">
                                    <input type="color" name="accent_color" class="form-control" style="height: 38px; width: 60px; padding: 2px;" value="#006a61">
                                    <input type="text" class="form-control" value="#006a61" onchange="$(this).prev().val(this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Subtitle / Short Description') }}</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="e.g. Best high-performance action cameras and stabilizers">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success font-weight-bold">
                            <i class="feather icon-check mr-1"></i> {{ __('Create Section') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage All Homepage Sections Modal -->
    <div class="modal fade" id="manageSectionsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="feather icon-layout mr-2 text-warning"></i> {{ __('Manage All Homepage Sections') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Section Name') }}</th>
                                    <th>{{ __('Badge') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($homepageSections))
                                    @foreach($homepageSections as $sec)
                                        <tr>
                                            <td class="font-weight-bold text-dark">
                                                <span class="d-inline-block rounded-circle mr-1" style="width: 10px; height: 10px; background-color: {{ $sec->accent_color ?: '#006a61' }};"></span>
                                                {{ $sec->name }}
                                            </td>
                                            <td>
                                                <span class="badge px-2 py-1 text-white font-weight-bold" style="background-color: {{ $sec->accent_color ?: '#006a61' }};">
                                                    {{ $sec->badge ?: 'None' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($sec->is_system)
                                                    <span class="badge badge-secondary">{{ __('System') }}</span>
                                                @else
                                                    <span class="badge badge-primary">{{ __('Custom') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input section-status-switch" id="sec_status_{{ $sec->id }}" data-id="{{ $sec->id }}" {{ $sec->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="sec_status_{{ $sec->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-section-btn"
                                                        data-id="{{ $sec->id }}"
                                                        data-name="{{ $sec->name }}"
                                                        data-badge="{{ $sec->badge }}"
                                                        data-tag="{{ $sec->tag }}"
                                                        data-subtitle="{{ $sec->subtitle }}"
                                                        data-icon="{{ $sec->icon }}"
                                                        data-accent_color="{{ $sec->accent_color }}"
                                                        data-is_system="{{ $sec->is_system ? 1 : 0 }}"
                                                        title="{{ __('Edit Section') }}">
                                                    <i class="feather icon-edit"></i>
                                                </button>
                                                @if(!$sec->is_system)
                                                    <form action="{{ route('homepage-sections.destroy', $sec->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this section? Assigned products will not be deleted.') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete Section') }}">
                                                            <i class="feather icon-trash-2"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-success font-weight-bold" data-dismiss="modal" data-toggle="modal" data-target="#addNewSectionModal">
                        <i class="feather icon-plus mr-1"></i> {{ __('+ Add New Section') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Homepage Section Modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="editSectionForm" method="POST" action="">
                    @csrf
                    <div class="modal-header" style="background: linear-gradient(135deg, #102d28 0%, #0d3b32 100%); color: #fff;">
                        <h5 class="modal-title text-white font-weight-bold">
                            <i class="feather icon-edit mr-2"></i> {{ __('Edit Homepage Section') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Section Name / Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_sec_name" class="form-control form-control-lg" required>
                            <small class="text-muted">{{ __('This is the title displayed for the section on the website homepage.') }}</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">{{ __('Badge / Tag') }}</label>
                                <input type="text" name="badge" id="edit_sec_badge" class="form-control" placeholder="e.g. Top Rated">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark">{{ __('Accent Color') }}</label>
                                <div class="input-group">
                                    <input type="color" name="accent_color" id="edit_sec_accent_color" class="form-control" style="height: 38px; width: 60px; padding: 2px;">
                                    <input type="text" id="edit_sec_accent_hex" class="form-control" onchange="$('#edit_sec_accent_color').val(this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Subtitle / Short Description') }}</label>
                            <input type="text" name="subtitle" id="edit_sec_subtitle" class="form-control" placeholder="e.g. High quality gear for professionals">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">
                            <i class="feather icon-save mr-1"></i> {{ __('Update Section') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Flash Deal Timer & Settings Modal -->
    <div class="modal fade" id="flashDealSettingsModal" tabindex="-1" role="dialog" aria-labelledby="flashDealSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold text-white" id="flashDealSettingsModalLabel">
                        <i class="feather icon-clock mr-1"></i> {{ __('Flash Deal Offer Timer & Title Settings') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('product.save-flash-deal-settings') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 text-left">
                        @php
                            $curEndDate = get_setting('flash_deal_end_date');
                            $formattedValue = $curEndDate ? date('Y-m-d\TH:i', strtotime($curEndDate)) : date('Y-m-d\TH:i', strtotime('+7 days 23:59:59'));
                            $curTitle = get_setting('flash_deal_title') ?: 'Broadcast & Studio Flash';
                            $curBadge = get_setting('flash_deal_badge') ?: 'Limited Studio Drops';
                        @endphp
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Flash Deal End Date & Time *') }}</label>
                            <input type="datetime-local" name="flash_deal_end_date" class="form-control form-control-lg" value="{{ $formattedValue }}" required>
                            <small class="text-muted d-block mt-1">{{ __('The countdown timer on the frontend homepage will dynamically count down to this exact date and time.') }}</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Section Heading Title') }}</label>
                            <input type="text" name="flash_deal_title" class="form-control" value="{{ $curTitle }}" placeholder="e.g. Broadcast & Studio Flash">
                            <small class="text-muted">{{ __('Section title displayed above the products.') }}</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Section Badge / Tag') }}</label>
                            <input type="text" name="flash_deal_badge" class="form-control" value="{{ $curBadge }}" placeholder="e.g. Limited Studio Drops">
                            <small class="text-muted">{{ __('Small badge shown with the pulsing dot.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger font-weight-bold">
                            <i class="feather icon-save mr-1"></i> {{ __('Save Timer Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hollyland Showcase Banner & Settings Modal -->
    <div class="modal fade" id="hollylandSettingsModal" tabindex="-1" role="dialog" aria-labelledby="hollylandSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #006a61 0%, #102d28 100%);">
                    <h5 class="modal-title font-weight-bold text-white" id="hollylandSettingsModalLabel">
                        <i class="feather icon-sliders mr-2"></i> {{ __('Hollyland Showcase Banner & Section Settings') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('product.save-hollyland-settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 text-left">
                        @php
                            $hlSecTitle = get_setting('hollyland_section_title') ?: 'Hollyland Microphone';
                            $hlSecBadge = get_setting('hollyland_section_badge') ?: 'FEATURED';
                            $hlSecTag = get_setting('hollyland_section_tag') ?: 'SHOWCASE';
                            $hlSecViewAll = get_setting('hollyland_section_view_all_link') ?: '';

                            $hlBanBrand = get_setting('hollyland_banner_brand') ?: 'HOLLYLAND';
                            $hlBanTitle = get_setting('hollyland_banner_title') ?: 'PURE SOUND. ZERO LIMITS.';
                            $hlBanSubtitle = get_setting('hollyland_banner_subtitle') ?: 'Wireless Audio for Modern Creators';
                            $hlBanFeatures = get_setting('hollyland_banner_features') ?: 'Wireless • Clear • Reliable • Portable';
                            $hlBanBottom = get_setting('hollyland_banner_bottom_text') ?: 'Capture What Matters';
                            $hlBanLink = get_setting('hollyland_banner_link') ?: '';
                            $hlBanImage = get_setting('hollyland_banner_image');
                            
                            $hlPreviewSrc = $hlBanImage 
                                ? ((str_starts_with($hlBanImage, 'http://') || str_starts_with($hlBanImage, 'https://')) ? $hlBanImage : asset($hlBanImage))
                                : asset('uploads/banners/banner_hollyland.webp');
                        @endphp

                        <!-- Section 1: Header Bar Settings -->
                        <div class="card mb-4 border" style="border-color: #c6eae1 !important; background-color: #f8fdfc;">
                            <div class="card-header py-2 px-3 text-white font-weight-bold" style="background-color: #006a61; font-size: 13px;">
                                <i class="feather icon-layout mr-1"></i> {{ __('1. Section Header & Badges') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Section Title') }}</label>
                                        <input type="text" name="hollyland_section_title" class="form-control form-control-sm" value="{{ $hlSecTitle }}" placeholder="e.g. Hollyland Microphone">
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Left Badge') }}</label>
                                        <input type="text" name="hollyland_section_badge" class="form-control form-control-sm" value="{{ $hlSecBadge }}" placeholder="e.g. FEATURED">
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Right Tag') }}</label>
                                        <input type="text" name="hollyland_section_tag" class="form-control form-control-sm" value="{{ $hlSecTag }}" placeholder="e.g. SHOWCASE">
                                    </div>
                                    <div class="col-md-12 form-group mb-0">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('"View All" Button Custom URL (Optional)') }}</label>
                                        <input type="text" name="hollyland_section_view_all_link" class="form-control form-control-sm" value="{{ $hlSecViewAll }}" placeholder="Leave blank to use default wireless-mics subcategory page">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Left Vertical Poster Banner Content -->
                        <div class="card mb-4 border" style="border-color: #1e293b !important; background-color: #f8fafc;">
                            <div class="card-header py-2 px-3 text-white font-weight-bold" style="background-color: #1e293b; font-size: 13px;">
                                <i class="feather icon-sidebar mr-1"></i> {{ __('2. Left Vertical Showcase Banner Card') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Banner Brand Name') }}</label>
                                        <input type="text" name="hollyland_banner_brand" class="form-control form-control-sm" value="{{ $hlBanBrand }}" placeholder="e.g. HOLLYLAND">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Banner Headline Title') }}</label>
                                        <input type="text" name="hollyland_banner_title" class="form-control form-control-sm" value="{{ $hlBanTitle }}" placeholder="e.g. PURE SOUND. ZERO LIMITS.">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Banner Subtitle / Tagline') }}</label>
                                        <input type="text" name="hollyland_banner_subtitle" class="form-control form-control-sm" value="{{ $hlBanSubtitle }}" placeholder="e.g. Wireless Audio for Modern Creators">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Banner Features / Bullet Points') }}</label>
                                        <input type="text" name="hollyland_banner_features" class="form-control form-control-sm" value="{{ $hlBanFeatures }}" placeholder="e.g. Wireless • Clear • Reliable • Portable">
                                        <small class="text-muted">{{ __('Separate items with bullets (•), commas, or pipes.') }}</small>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Banner Bottom Script / Quote Text') }}</label>
                                        <input type="text" name="hollyland_banner_bottom_text" class="form-control form-control-sm" value="{{ $hlBanBottom }}" placeholder="e.g. Capture What Matters">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">{{ __('Banner Target Link URL (Optional)') }}</label>
                                        <input type="text" name="hollyland_banner_link" class="form-control form-control-sm" value="{{ $hlBanLink }}" placeholder="e.g. /category/audio/wireless-mics">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Banner Showcase Image -->
                        <div class="card mb-0 border" style="border-color: #006a61 !important; background-color: #f0faf7;">
                            <div class="card-header py-2 px-3 text-white font-weight-bold" style="background-color: #006a61; font-size: 13px;">
                                <i class="feather icon-image mr-1"></i> {{ __('3. Showcase Product Image') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center mb-3 mb-md-0">
                                        <div class="p-3 bg-dark rounded border shadow-inner d-inline-block">
                                            <img id="hollylandBannerPreview" src="{{ $hlPreviewSrc }}" alt="Preview" style="max-height: 120px; max-width: 140px; object-fit: contain;">
                                        </div>
                                        <small class="d-block text-muted mt-1">{{ __('Current Banner Visual') }}</small>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold text-dark small mb-1">{{ __('Upload New Banner Image') }}</label>
                                            <input type="file" name="hollyland_banner_image_file" class="form-control-file border rounded p-1 bg-white" accept="image/*" onchange="previewHollylandImage(this)">
                                            <small class="text-muted d-block">{{ __('Recommended: PNG with transparent background, ~400x400px (Max: 5MB)') }}</small>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold text-dark small mb-1">{{ __('Or Image External URL') }}</label>
                                            <input type="text" name="hollyland_banner_image_url" class="form-control form-control-sm" value="{{ str_starts_with($hlBanImage ?? '', 'http') ? $hlBanImage : '' }}" placeholder="https://..." oninput="previewHollylandUrl(this.value)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn font-weight-bold text-white" style="background: #006a61;">
                            <i class="feather icon-save mr-1"></i> {{ __('Save Hollyland Settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const currentActiveSection = "{{ $activeSection ?? 'all' }}";
            const totalProductsInStore = {{ $totalProductsCount ?? 0 }};
            let isAllStoreSelected = false;

            // Update Bulk Action Bar visibility and count
            function updateBulkActionBar() {
                const checkedCount = $('.product-select-checkbox:checked').length;
                const totalCheckboxes = $('.product-select-checkbox').length;

                if (isAllStoreSelected) {
                    $('#selectedProductsCount').text(totalProductsInStore);
                    $('#allStoreSelectedNotice').show();
                    $('#bulkActionBar').slideDown(200);
                    $('.product-select-checkbox').each(function() {
                        $(this).prop('checked', true).closest('tr').addClass('product-selected-row');
                    });
                    $('#checkAllProducts').prop('checked', true).prop('indeterminate', false);
                    return;
                }

                $('#allStoreSelectedNotice').hide();
                $('#selectedProductsCount').text(checkedCount);

                if (checkedCount > 0) {
                    $('#bulkActionBar').slideDown(200);
                } else {
                    $('#bulkActionBar').slideUp(200);
                    $('#checkAllProducts').prop('checked', false);
                }

                // Highlight selected rows
                $('.product-select-checkbox').each(function() {
                    if ($(this).is(':checked')) {
                        $(this).closest('tr').addClass('product-selected-row');
                    } else {
                        $(this).closest('tr').removeClass('product-selected-row');
                    }
                });

                // Update Master Checkbox state
                if (totalCheckboxes > 0 && checkedCount === totalCheckboxes) {
                    $('#checkAllProducts').prop('checked', true).prop('indeterminate', false);
                } else if (checkedCount > 0 && checkedCount < totalCheckboxes) {
                    $('#checkAllProducts').prop('checked', false).prop('indeterminate', true);
                } else {
                    $('#checkAllProducts').prop('checked', false).prop('indeterminate', false);
                }
            }

            // Master Checkbox click: select ALL products across all pages/filter
            $('#checkAllProducts').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.product-select-checkbox').prop('checked', isChecked);
                isAllStoreSelected = isChecked;
                updateBulkActionBar();
            });

            // Individual Row Checkbox change: switches to page selection
            $(document).on('change', '.product-select-checkbox', function() {
                isAllStoreSelected = false;
                updateBulkActionBar();
            });

            // Clear Selection
            $('#clearBulkSelectionBtn').on('click', function() {
                isAllStoreSelected = false;
                $('.product-select-checkbox').prop('checked', false);
                $('#checkAllProducts').prop('checked', false);
                updateBulkActionBar();
            });

            // Helper: update tab count badges
            function updateTabCounts(res) {
                if (res.total_products_count !== undefined) $('#tabCountAll').text(res.total_products_count);
                if (res.flash_deal_count !== undefined) $('#tabCountFlashDeal').text(res.flash_deal_count);
                if (res.popular_count !== undefined) $('#tabCountPopular').text(res.popular_count);
                if (res.hollyland_count !== undefined) $('#tabCountHollyland').text(res.hollyland_count);
                if (res.essential_count !== undefined) $('#tabCountEssential').text(res.essential_count);
            }

            // Bulk action execution
            function performBulkSectionAction(section, action) {
                const selectedIds = [];
                $('.product-select-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0 && !isAllStoreSelected) {
                    if (typeof toastr !== 'undefined') toastr.warning('{{ __("Please select at least one product.") }}');
                    return;
                }

                const countToUpdate = isAllStoreSelected ? totalProductsInStore : selectedIds.length;
                const actionText = action === 'remove' ? 'remove' : 'add';
                const sectionName = section === 'flash_deal' ? '⚡ Broadcast & Studio Flash' :
                                    (section === 'popular' ? '🔥 Popular Cinema & Creator Gear' :
                                    (section === 'hollyland' ? '🎙️ Hollyland Microphone' :
                                    (section === 'essential' ? '🔌 Studio Essentials & Power' : 'ALL Homepage Sections')));

                const confirmMsg = `Are you sure you want to ${actionText} ${countToUpdate} product(s) ${action === 'remove' ? 'from' : 'to'} ${sectionName}?`;
                if (!confirm(confirmMsg)) return;

                $('#bulkActionBar button').prop('disabled', true).css('opacity', '0.6');

                $.ajax({
                    url: "{{ route('product.bulk-assign-homepage-section') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_ids: selectedIds,
                        select_all: isAllStoreSelected ? 1 : 0,
                        category_id: "{{ request('category_id') ?? '' }}",
                        sub_category_id: "{{ request('sub_category_id') ?? '' }}",
                        barcode: "{{ request('barcode') ?? '' }}",
                        section: section,
                        action: action
                    },
                    success: function(res) {
                        $('#bulkActionBar button').prop('disabled', false).css('opacity', '1');
                        if (res.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(res.message);
                            }

                            updateTabCounts(res);

                            // If we're filtering by a section and products were removed from it, fade out those rows
                            if (action === 'remove' && (currentActiveSection === String(section) || section === 'all')) {
                                selectedIds.forEach(id => {
                                    const row = $(`#product_row_${id}`);
                                    row.fadeOut(300, function() {
                                        $(this).remove();
                                        if ($('#datatable-buttons tbody tr').length === 0) {
                                            $('#datatable-buttons tbody').html('<tr><td colspan="100%" class="text-center no_data_style text-danger">{{ __("No Data Available") }}</td></tr>');
                                        }
                                    });
                                });
                            } else {
                                selectedIds.forEach(id => {
                                    const row = $(`#product_row_${id}`);
                                    if (section === 'all' && action === 'remove') {
                                        row.find('.section-toggle-btn').each(function() {
                                            const sName = $(this).data('name') || $(this).data('section');
                                            $(this).removeClass('text-white font-weight-bold active-section')
                                                   .addClass('btn-outline-secondary text-muted')
                                                   .css({'background-color': '', 'border-color': ''})
                                                   .html(sName);
                                        });
                                    } else {
                                        const btn = row.find(`.section-toggle-btn[data-section="${section}"]`);
                                        if (btn.length) {
                                            const sName = btn.data('name') || section;
                                            const accent = btn.data('accent') || '#006a61';
                                            if (action === 'add') {
                                                btn.removeClass('btn-outline-secondary text-muted')
                                                   .addClass('text-white font-weight-bold active-section')
                                                   .css({'background-color': accent, 'border-color': accent})
                                                   .html(sName + ' <span class="remove-x">&times;</span>');
                                            } else {
                                                btn.removeClass('text-white font-weight-bold active-section')
                                                   .addClass('btn-outline-secondary text-muted')
                                                   .css({'background-color': '', 'border-color': ''})
                                                   .html(sName);
                                            }
                                        }
                                    }
                                });
                            }

                            // Reset selection
                            isAllStoreSelected = false;
                            $('.product-select-checkbox').prop('checked', false);
                            $('#checkAllProducts').prop('checked', false);
                            $('tr').removeClass('product-selected-row');
                            updateBulkActionBar();
                        }
                    },
                    error: function() {
                        $('#bulkActionBar button').prop('disabled', false).css('opacity', '1');
                        alert('{{ __("Failed to perform bulk section action.") }}');
                    }
                });
            }

            // Quick remove from current section button
            $(document).on('click', '.quick-bulk-remove-current-btn', function() {
                const section = $(this).data('section');
                performBulkSectionAction(section, 'remove');
            });

            // Dropdown Bulk actions
            $(document).on('click', '.bulk-section-action-btn', function() {
                const section = $(this).data('section');
                const action = $(this).data('action');
                performBulkSectionAction(section, action);
            });

            // Single Homepage Section Live AJAX Toggle Button
            $(document).on('click', '.section-toggle-btn', function(e) {
                e.preventDefault();
                const btn = $(this);
                const productId = btn.data('id');
                const section = btn.data('section');
                const accentColor = btn.data('accent') || '#006a61';
                const sectionName = btn.data('name') || section;
                const row = $(`#product_row_${productId}`);
                
                btn.prop('disabled', true).css('opacity', '0.6');
                
                $.ajax({
                    url: "{{ url('/product/toggle-homepage-section') }}/" + productId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        section: section
                    },
                    success: function(res) {
                        btn.prop('disabled', false).css('opacity', '1');
                        if (res.success) {
                            // If currently filtered by this section and it was removed, fade out row
                            if (!res.status && currentActiveSection === String(section)) {
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                    if ($('#datatable-buttons tbody tr').length === 0) {
                                        $('#datatable-buttons tbody').html('<tr><td colspan="100%" class="text-center no_data_style text-danger">{{ __("No Data Available") }}</td></tr>');
                                    }
                                });
                            } else {
                                if (res.status) {
                                    btn.removeClass('btn-outline-secondary text-muted')
                                       .addClass('text-white font-weight-bold active-section')
                                       .css({'background-color': accentColor, 'border-color': accentColor})
                                       .html(sectionName + ' <span class="remove-x">&times;</span>');
                                    btn.attr('title', '{{ __("Click to remove from section") }}');
                                } else {
                                    btn.removeClass('text-white font-weight-bold active-section')
                                       .addClass('btn-outline-secondary text-muted')
                                       .css({'background-color': '', 'border-color': ''})
                                       .html(sectionName);
                                    btn.attr('title', '{{ __("Click to add to section") }}');
                                }
                            }

                            // Show notification
                            if (typeof toastr !== 'undefined') {
                                toastr.success(res.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).css('opacity', '1');
                        alert('{{ __("Error toggling section status.") }}');
                    }
                });
            });

            // Edit section modal trigger
            $(document).on('click', '.edit-section-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const badge = $(this).data('badge') || '';
                const subtitle = $(this).data('subtitle') || '';
                const accent = $(this).data('accent_color') || '#006a61';

                $('#edit_sec_name').val(name);
                $('#edit_sec_badge').val(badge);
                $('#edit_sec_subtitle').val(subtitle);
                $('#edit_sec_accent_color').val(accent);
                $('#edit_sec_accent_hex').val(accent);

                $('#editSectionForm').attr('action', "{{ url('/homepage-sections/update') }}/" + id);
                $('#manageSectionsModal').modal('hide');
                $('#editSectionModal').modal('show');
            });

            // Section active/inactive toggle
            $(document).on('change', '.section-status-switch', function() {
                const switchEl = $(this);
                const id = switchEl.data('id');

                $.ajax({
                    url: "{{ url('/homepage-sections/toggle-status') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success && typeof toastr !== 'undefined') {
                            toastr.success(res.message);
                        }
                    },
                    error: function() {
                        alert('{{ __("Failed to update section status.") }}');
                    }
                });
            });

            // Single Website visibility toggle
            $(document).on('change', '.toggle-website-switch', function() {
                const switchEl = $(this);
                const productId = switchEl.data('id');
                const isChecked = switchEl.is(':checked') ? 1 : 0;
                const badge = $(`.website-badge-${productId}`);

                switchEl.prop('disabled', true);

                $.ajax({
                    url: "{{ url('/product/toggle-website-visibility') }}/" + productId,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        show_on_website: isChecked
                    },
                    success: function(res) {
                        switchEl.prop('disabled', false);
                        if (res.success) {
                            if (res.show_on_website) {
                                switchEl.prop('checked', true);
                                badge.removeClass('badge-secondary').addClass('badge-success').text('{{ __("Active") }}');
                            } else {
                                switchEl.prop('checked', false);
                                badge.removeClass('badge-success').addClass('badge-secondary').text('{{ __("Hidden") }}');
                            }
                            if (typeof toastr !== 'undefined') {
                                toastr.success(res.message);
                            }
                        }
                    },
                    error: function() {
                        switchEl.prop('disabled', false);
                        switchEl.prop('checked', !isChecked);
                        alert('{{ __("Failed to update website visibility.") }}');
                    }
                });
            });

            // Bulk Website visibility toggle
            $(document).on('click', '.bulk-website-action-btn', function() {
                const status = $(this).data('status');
                const selectedIds = [];
                $('.product-select-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0 && !isAllStoreSelected) {
                    if (typeof toastr !== 'undefined') toastr.warning('{{ __("Please select at least one product.") }}');
                    return;
                }

                const countToUpdate = isAllStoreSelected ? totalProductsInStore : selectedIds.length;
                const statusText = status == 1 ? '{{ __("SHOW on website") }}' : '{{ __("HIDE from website") }}';
                if (!confirm(`Are you sure you want to ${statusText} for ${countToUpdate} product(s)?`)) return;

                $('#bulkActionBar button').prop('disabled', true).css('opacity', '0.6');

                $.ajax({
                    url: "{{ route('product.bulk-toggle-website-visibility') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_ids: selectedIds,
                        select_all: isAllStoreSelected ? 1 : 0,
                        category_id: "{{ request('category_id') ?? '' }}",
                        sub_category_id: "{{ request('sub_category_id') ?? '' }}",
                        barcode: "{{ request('barcode') ?? '' }}",
                        show_on_website: status
                    },
                    success: function(res) {
                        $('#bulkActionBar button').prop('disabled', false).css('opacity', '1');
                        if (res.success) {
                            if (typeof toastr !== 'undefined') toastr.success(res.message);

                            if (isAllStoreSelected) {
                                $('.toggle-website-switch').prop('checked', status == 1);
                                if (status == 1) {
                                    $('[class*="website-badge-"]').removeClass('badge-secondary').addClass('badge-success').text('{{ __("Active") }}');
                                } else {
                                    $('[class*="website-badge-"]').removeClass('badge-success').addClass('badge-secondary').text('{{ __("Hidden") }}');
                                }
                            } else {
                                selectedIds.forEach(id => {
                                    const sw = $(`#web_switch_${id}`);
                                    const bd = $(`.website-badge-${id}`);
                                    if (status == 1) {
                                        sw.prop('checked', true);
                                        bd.removeClass('badge-secondary').addClass('badge-success').text('{{ __("Active") }}');
                                    } else {
                                        sw.prop('checked', false);
                                        bd.removeClass('badge-success').addClass('badge-secondary').text('{{ __("Hidden") }}');
                                    }
                                });
                            }

                            // Reset selection
                            isAllStoreSelected = false;
                            $('.product-select-checkbox').prop('checked', false);
                            $('#checkAllProducts').prop('checked', false);
                            $('tr').removeClass('product-selected-row');
                            updateBulkActionBar();
                        }
                    },
                    error: function() {
                        $('#bulkActionBar button').prop('disabled', false).css('opacity', '1');
                        alert('{{ __("Failed to update website visibility.") }}');
                    }
                });
            });

            // Modal Filter Search
            $('#modalProductSearch').on('keyup', function() {
                const searchVal = $(this).val().toLowerCase().trim();
                $('.modal-product-item').each(function() {
                    const itemText = $(this).data('search');
                    if (!searchVal || itemText.indexOf(searchVal) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Select / Deselect All
            $('#selectAllModalProducts').on('click', function() {
                $('.modal-product-item:visible .modal-p-checkbox').prop('checked', true);
            });
            $('#deselectAllModalProducts').on('click', function() {
                $('.modal-p-checkbox').prop('checked', false);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Update barcode variation for the correct row
            function getVariationForRow(button) {
                const row = $(button).closest('tr');
                const select = row.find('.variation_select');
                const variation = select.val() || '';
                const variation_id = select.find('option:selected').data('variation_id') || '';
                return {
                    variation,
                    variation_id
                };
            }

            // Barcode button click
            $(document).on('click', '.generated_barcode', function() {
                const name = $(this).data('name');
                const category = $(this).data('category');
                const discount = $(this).data('discount');
                const dis_price = $(this).data('dis_price');
                const price = $(this).data('price');
                const code = $(this).data('code');
                const {
                    variation,
                    variation_id
                } = getVariationForRow(this);
                const full_code = code + "" + variation_id;
                const pro_barcode = "{{ get_setting('pro_barcode') }}" || 'single';
                const company = "{{ $com?->value ?? get_setting('com_name') }}";

                const formattedPrice = new Intl.NumberFormat('en-US', {
                    useGrouping: true,
                    minimumFractionDigits: 2
                }).format(price);
                const formattedDisPrice = new Intl.NumberFormat('en-US', {
                    useGrouping: true,
                    minimumFractionDigits: 2
                }).format(dis_price);
                
                let priceHtml = '';

                if (discount > 0) {
                
                    priceHtml = `
                    <p style="margin-bottom:2px; line-height:9px; margin-top:5px; font-size: 12px; color:black;">
                        <strong>{{ __('Price') }}: <del> ${formattedDisPrice} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</del></strong>
                    </p>
                    <p style="margin-bottom:2px; line-height:9px; margin-top:5px; font-size: 12px; color:black;">
                        <strong>{{ __('Discount Price') }}: ${formattedPrice} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                    </p>
                    `;
                
                } else {
                
                    priceHtml = `
                    <p style="margin-bottom:2px; line-height:9px; margin-top:5px; font-size: 12px; color:black;">
                        <strong>{{ __('Price') }}: ${formattedPrice} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                    </p>
                    `;
                
                }

                $("#barcode-page").html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                $("#bar_code_modal").modal('show');

                const url = "{{ route('product-barcode', 'value') }}".replace('value', full_code);

                $.get(url, (data) => {
                    let barcode = '';

                    if (pro_barcode === 'a4') {
                        barcode +=
                            `<div class="text-center p-4 print_area" id="barcode"><table class="table table-bordered">`;
                        for (let i = 0; i < 10; i++) {
                            barcode += `<tr>`;
                            for (let j = 0; j < 3; j++) {
                                barcode += `<td>
                            <p class="b_company" style="margin-bottom:1px; line-height:9px; margin-top:5px; font-size: 11px; color:black;"><strong>${company}</strong></p>
                            <p class="b_category" style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 9px; color:black;"><strong>${category}</strong></p>
                            <div style="text-align: center; margin: 0;">${data}</div>
                            <p class="b_code" style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${full_code}</strong></p>
                            <p class="b_name" style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${name}</strong></p>
                            <p class="b_variation" style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${variation}</strong></p>
                            <div class="b_price">${priceHtml}</div>
                            
                        </td>`;
                            }
                            barcode += `</tr>`;
                        }
                        barcode += `</table></div>`;
                    } else {
                        barcode += `<div class="text-center p-4 print_area" id="barcode" style="padding:0 !important">
                    <table class="table" style="margin-bottom:0 !important;">
                        <tr>
                            <td style="border-top:0 !important;padding:0;">
                                <p class="b_company" style="font-family: Arial, sans-serif;line-height:9px;margin-bottom:1px; font-size: 13px;margin-top:5px; font-weight: 500; text-transform: uppercase; color: black;"><strong>${company}</strong></p>
                                 <p class="b_category" style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 9px; color:black;"><strong>${category}</strong></p>
                                <div style="text-align: center; margin: 0;">${data}</div>
                                <p class="b_code" style="margin-bottom:0px; line-height:9px; margin-top:2px; font-size: 10px; color:black;"><strong>${full_code.split('').join(' ')}</strong></p>
                                <p class="b_name" style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 9px; color:black;"><strong>${name}</strong></p>
                                <p class="b_variation" style="margin-bottom:2px; line-height:9px; margin-top:-2px; font-size: 9px; color:black;"><strong>${variation}</strong></p>
                                <div class="b_price">${priceHtml}</div>
                                
                            </td>
                        </tr>
                    </table>
                </div>`;
                    }

                    $("#barcode-page").html(barcode);
                    $('.barcode_setting').trigger('change');
                }).fail(() => {
                    $("#barcode-page").html('<div class="text-center text-danger p-4">{{ __("Failed to generate barcode.") }}</div>');
                });
            });
            
            $(document).on('change', '.barcode_setting', function() {
                var target = $(this).data('target');
                if ($(this).is(':checked')) {
                    $(target).show();
                } else {
                    $(target).hide();
                }
            });

            // Reset barcode modal on cancel
            $(document).on('click', '#modal_cancel', function() {
                $("#bar_code_modal").modal('hide');
                $('.variation_select').val('');
            });

        });
    </script>

    <script>
        //  Print Barcode
        function print_barcode(id) {
            $("#bar_code_modal").modal('hide');
            $(".modal-backdrop").remove();
            $(".modal").css('display', 'none');

            let printContent = $("#barcode-page").html();
            let printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Barcode Print</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0px;
                            text-align: center;
                        }
                        .print_area {
                            width: 100%;
                            text-align: center;
                        }
                        svg {
                            max-width: 100% !important;
                            height: 26px !important;
                            display: block !important;
                            margin: 0 auto !important;
                            color-adjust: exact !important;
                            -webkit-print-color-adjust: exact !important;
                        }
                        table {
                            width: 100%;
                            text-align: center;
                        }
                        table td, table th {
                            text-align: center !important;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
                </html>
            `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
        }

        // Live image preview functions for Hollyland modal
        function previewHollylandImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#hollylandBannerPreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function previewHollylandUrl(url) {
            if (url && url.trim().length > 8) {
                $('#hollylandBannerPreview').attr('src', url.trim());
            }
        }
    </script>
@endpush
