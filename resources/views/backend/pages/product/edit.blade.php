@extends('backend.layouts.master')
@section('section-title', 'Edit Product')
@section('page-title', 'Product Edit')

@push('css')
@endpush

@if (check_permission('product.update'))
    @section('action-button')
        <a href="{{ route('product.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            All Product
        </a>
    @endsection
@endif

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('product.update', $data->id) }}" enctype="multipart/form-data">
                            @csrf
                            @php
                                $selectedBranchIds = \App\Models\BranchProduct::where('product_id', $data->id)->pluck(
                                    'branch_id',
                                );
                            @endphp
                            {{-- Product name --}}
                            <div class="mt-2 col-md-6">
                                <label for="name" class="form-label fw-bold">Product Name *</label>
                                <input type="text" class="form-control" id="product_name" name="name" placeholder="Enter product name"
                                    value="{{ $data->name }}">
                                <div class="errors">{{ $errors->has('name') ? $errors->first('name') : '' }}</div>
                            </div>

                            {{-- Product slug --}}
                            <div class="mt-2 col-md-6">
                                <label for="slug" class="form-label fw-bold">Slug (Product Details URL)</label>
                                <input type="text" class="form-control" id="product_slug" name="slug"
                                    placeholder="e.g. sony-fx30-cinema-camera" value="{{ old('slug', $data->slug) }}">
                                <small class="text-muted text-[11px]">Will appear in URL: /product-details/your-slug</small>
                                <div class="errors">{{ $errors->has('slug') ? $errors->first('slug') : '' }}</div>
                            </div>

                            {{-- Product barcode  --}}
                            <div class="mt-2 col-md-6">
                                <label for="barcode" class="form-label fw-bold">Product Barcode</label>
                                <input type="text" class="form-control" placeholder="Enter product barcode"
                                    name="barcode" value="{{ $data->barcode }}">
                                <div class="errors">{{ $errors->has('barcode') ? $errors->first('barcode') : '' }}</div>
                            </div>

                            {{-- Category --}}
                            <div class="mt-2 col-md-6">
                                <label for="unit_id" class="form-label fw-bold">Category *</label>
                                <select class="select2" name="category_id" id="categoryId">
                                    <option selected value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value={{ $category->id }}
                                            {{ $category->id == $data->category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="errors">{{ $errors->has('category_id') ? $errors->first('category_id') : '' }}
                                </div>
                            </div>

                            {{-- Sub Category --}}
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="mt-2 col-md-6">
                                    <label for="sub_category_id" class="form-label fw-bold">Sub Category</label>
                                    <select class="select2" name="sub_category_id" id="subCategoryId">
                                        <option value="">Select Sub Category</option>
                                        @foreach ($subCategories as $subCat)
                                            <option value="{{ $subCat->id }}" {{ $subCat->id == $data->sub_category_id ? 'selected' : '' }}>
                                                {{ $subCat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Brand --}}
                            <div class="mt-2 col-md-6">
                                <label for="brand_id" class="form-label fw-bold">Brand</label>
                                <select class="select2" name="brand_id">
                                    <option selected value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ $brand->id == $data->brand_id ? 'selected' : '' }}>{{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @php
                                $hasTransactions = $data->hasTransactions();
                            @endphp
                            @if ($data->unit?->related_unit_id == null)
                                {{-- Unit --}}
                                <div class="mt-2 col-md-3">
                                    <label for="unit_id" class="form-label fw-bold">Main Unit</label>
                                    <select @if ($hasTransactions) disabled @endif name="unit_id" class="select2 main_unit"
                                        name="unit_id">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ $unit->id == $data->unit_id ? 'selected' : '' }}>{{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($hasTransactions)
                                        <input type="hidden" name="unit_id" value="{{ $data->unit_id }}">
                                    @endif
                                </div>

                                {{-- Unit --}}
                                <div class="mt-2 col-md-3">
                                    <label for="unit_id" class="form-label fw-bold">Sub Unit</label>
                                    <select name="sub_unit_id" id="" class="form-control sub_unit">
                                        <option value="">No Related Unit Found</option>
                                    </select>
                                </div>


                                {{-- Unit --}}
                                <div class="mt-2 col-md-6">
                                    <div class="form-group">
                                        <label for="">Low Stock Quantity</label>
                                        <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                            <input type="text" name="main_qty" value="{{ $data->main_qty }}"
                                                class="form-control col" placeholder="Pices">
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Unit --}}
                                <div class="mt-2 col-md-3">
                                    <label for="unit_id" class="form-label fw-bold">Main Unit</label>
                                    <select @if ($hasTransactions) disabled @endif  class="select2 main_unit"
                                        name="unit_id">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ $unit->id == $data->unit_id ? 'selected' : '' }}>{{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($hasTransactions)
                                        <input type="hidden" name="unit_id" value="{{ $data->unit_id }}">
                                    @endif
                                </div>

                                {{-- Unit --}}
                                <div class="mt-2 col-md-3">
                                    <label for="unit_id" class="form-label fw-bold">Sub Unit</label>
                                    <select @if ($hasTransactions) disabled @endif name="sub_unit_id"
                                        id="" class="form-control sub_unit">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ $unit->id == $data->unit->related_unit_id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                {{-- Unit --}}
                                <div class="mt-2 col-md-3">
                                    <div class="form-group">
                                        <label for="">Low Stock Quantity</label>
                                        <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                            <input type="text" name="main_qty" value="{{ $data->main_qty }}"
                                                class="form-control col" placeholder="Pices">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 col-md-3">
                                    <div class="form-group">
                                        <label for=""></label>
                                        <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                            <input type="text" name="sub_qty" value="{{ $data->sub_qty }}"
                                                class="form-control col" placeholder="Pices">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (env('APP_SC') == 'yes')
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="size" class="form-label fw-bold mb-1">Size </label>
                                            <select class="select2 product_size" name="size[]" multiple data-placeholder="Select Size">
                                                <option value=""></option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}"
                                                        {{ in_array($size->id, $selectedSizeIds) ? 'selected' : '' }}
                                                        {{ in_array($size->id, $lockedSizeIds ?? []) ? 'disabled' : '' }}>
                                                        {{ $size->size }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <button type="button" class="btn extra_btn mb-0 shadow-sm" data-toggle="modal"
                                                data-target="#addSizeModal">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="errors">{{ $errors->has('size') ? $errors->first('size') : '' }}</div>
                                </div>
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="color" class="form-label fw-bold mb-1">Color </label>
                                            <select class="select2 product_color" name="color[]" multiple data-placeholder="Select Color">
                                                <option value=""></option>
                                                 @foreach ($colors as $color)
                                                     <option value="{{ $color->id }}"
                                                         {{ in_array($color->id, $selectedColorIds) ? 'selected' : '' }}
                                                         {{ in_array($color->id, $lockedColorIds ?? []) ? 'disabled' : '' }}>
                                                         {{ $color->color }}</option>
                                                 @endforeach
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <button type="button" class="btn extra_btn mb-0 shadow-sm" data-toggle="modal"
                                                data-target="#addColorModal">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="errors">{{ $errors->has('color') ? $errors->first('color') : '' }}</div>
                                </div>
                            @endif
                            {{-- Price --}}
                            <div class="mt-2 col-md-6">
                                <label for="purchase_price" class="form-label fw-bold">Purchase Price *</label>
                                <input type="number" step="any" class="form-control" name="purchase_price" id="purchase_price"
                                    value="{{ $data->purchase_price }}">
                                <div class="errors">
                                    {{ $errors->has('purchase_price') ? $errors->first('purchase_price') : '' }}</div>
                            </div>
                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">Sale Price *</label>
                                <input type="number" step="any" class="form-control" value="{{ $data->dis_selling_price ?? 0 }}"
                                    name="dis_selling_price" id="selling_price">
                            </div>

                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">Discount</label>
                                <input type="text" class="form-control" value="{{ $data->discount ?? 0 }}"
                                    name="discount" id="discount" placeholder="e.g. 10 or 10%">
                            </div>

                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">Discount Price *</label>
                                <input type="number" step="any" class="form-control" value="{{ $data->selling_price ?? 0 }}"
                                    name="selling_price" id="discount_price" readonly>

                                <div id="price_error" style="color:red; display:none;">
                                    Sale price cannot be below purchase price
                                </div>
                            </div>
                            {{-- status --}}
                            <div class="col-md-6 mt-2">
                                <label for="status" class="form-label fw-bold">Status *</label>
                                <select class="select2" name="status">
                                    <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Deactive</option>
                                </select>
                                <div class="errors">{{ $errors->has('status') ? $errors->first('status') : '' }}</div>
                            </div>

                            {{-- Show on Website --}}
                            <div class="col-md-6 mt-2">
                                <label class="form-label fw-bold d-block">{{ __('Show on Website (ওয়েবসাইটে প্রদর্শন)') }}</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" class="custom-control-input" id="show_on_website" name="show_on_website" value="1" {{ old('show_on_website', $data->show_on_website) ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="show_on_website">{{ __('Display this product on Website Frontend') }}</label>
                                </div>
                            </div>

                            @if (env('APP_IMEI') == 'yes')
                            <div class="mt-2 col-md-6">
                                <label for="imei" class="form-label fw-bold">Has IMEI</label>
                                <select class="select2" name="imei">
                                    <option value="0" {{ $data->imei == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $data->imei == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            @endif
                            <div class="mt-2 col-md-6">
                                <label for="warranty_id" class="form-label fw-bold">{{ __('Warranty') }}</label>
                                <select class="select2" name="warranty_id" id="warranty_id">
                                    <option value="">{{ __('No Warranty / Select Warranty') }}</option>
                                    @if(isset($warranties))
                                        @foreach($warranties as $warranty)
                                            <option value="{{ $warranty->id }}" {{ (old('warranty_id', $data->warranty_id) == $warranty->id) ? 'selected' : '' }}>
                                                {{ $warranty->name }} ({{ $warranty->duration }} {{ $warranty->period }}{{ $warranty->duration > 1 ? 's' : '' }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                             {{-- Description --}}
                             <div class="mt-2 col-md-12">
                                 <label for="description" class="form-label fw-bold">{{ __('Description') }}</label>
                                 <textarea class="form-control summernote" id="description" name="description" rows="4">{!! $data->description !!}</textarea>
                             </div>

                            {{-- Homepage Display Sections Selection --}}
                            <div class="mt-3 col-md-12">
                                <div class="card border" style="border-radius: 8px; background-color: #f8fafc; border-color: #cbd5e1 !important;">
                                    <div class="card-header py-2 d-flex align-items-center justify-content-between" style="background-color: #f1f5f9; border-bottom: 1px solid #cbd5e1;">
                                        <span class="font-weight-bold text-dark font-13">
                                            <i class="feather icon-layout mr-1 text-primary"></i> {{ __('Homepage Sections Display (হোমপেজ সেকশন নির্বাচন)') }}
                                        </span>
                                        <span class="badge badge-primary font-11">{{ __('Optional Placement') }}</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <small class="text-muted d-block mb-3">
                                            {{ __('Select which homepage section(s) this product should appear in:') }}
                                        </small>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_flash_deal" name="is_flash_deal" value="1" {{ old('is_flash_deal', $data->is_flash_deal ?? 0) ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_flash_deal">
                                                        ⚡ {{ __('Broadcast & Studio Flash') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_popular" name="is_popular" value="1" {{ old('is_popular', $data->is_popular ?? 0) ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_popular">
                                                        🔥 {{ __('Popular Cinema & Creator Gear') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_hollyland" name="is_hollyland" value="1" {{ old('is_hollyland', $data->is_hollyland ?? 0) ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_hollyland">
                                                        🎙️ {{ __('Hollyland Microphone') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_essential" name="is_essential" value="1" {{ old('is_essential', $data->is_essential ?? 0) ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_essential">
                                                        🔌 {{ __('Studio Essentials & Power') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Product Images Section: Thumbnail & Gallery --}}
                            <div class="mt-4 col-md-12">
                                <div class="row">
                                    {{-- 1. Thumbnail / Main Cover Image --}}
                                    <div class="col-md-5 mb-3">
                                        <div class="card h-100 border" style="border-radius: 8px; background-color: #fffdfa; border-color: #ffe0b2 !important;">
                                            <div class="card-header py-2 d-flex align-items-center justify-content-between" style="background-color: #fff8e1; border-bottom: 1px solid #ffe0b2;">
                                                <span class="font-weight-bold text-dark font-13">
                                                    <i class="feather icon-star mr-1 text-warning"></i> {{ __('Main Cover Thumbnail') }}
                                                </span>
                                                <span class="badge badge-warning text-dark font-11">{{ __('Primary Display') }}</span>
                                            </div>
                                            <div class="card-body p-3">
                                                <small class="text-muted d-block mb-2">
                                                    {{ __('Customers will see this photo on category cards, search results, wishlist, cart & POS.') }}
                                                </small>
                                                <input type="hidden" name="old_thumbnail" id="old_thumbnail_val" value="{{ $data->thumbnail }}">
                                                <input class="form-control-file d-none" type="file" name="thumbnail" id="product_thumbnail" accept="image/*">
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <label for="product_thumbnail" class="btn btn-sm btn-outline-warning text-dark mb-0 shadow-sm cursor-pointer" style="cursor:pointer; font-weight: 600;">
                                                        <i class="feather icon-upload-cloud mr-1"></i> {{ __('Change Thumbnail') }}
                                                    </label>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2 d-none" id="reset_thumbnail_btn">
                                                        <i class="feather icon-rotate-ccw mr-1"></i> {{ __('Reset') }}
                                                    </button>
                                                </div>

                                                <div id="thumbnail_preview_box" class="border rounded p-2 text-center bg-white" style="min-height: 150px; display: flex; align-items: center; justify-content: center; position: relative;">
                                                    <div id="current_thumbnail_display" class="w-100">
                                                        <img id="thumbnail_img_elem" src="{{ $data->thumbnail_url }}" class="img-fluid rounded mb-1" style="max-height: 130px; object-fit: contain;">
                                                        <div class="mt-1">
                                                            <small class="text-truncate d-block font-11 text-muted" id="thumbnail_status_label">
                                                                {{ $data->thumbnail ? basename($data->thumbnail) : __('Default Cover') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. Additional Gallery Images (Multiple) --}}
                                    <div class="col-md-7 mb-3">
                                        <div class="card h-100 border" style="border-radius: 8px; background-color: #fcfdfe;">
                                            <div class="card-header py-2 d-flex align-items-center justify-content-between bg-white border-bottom">
                                                <span class="font-weight-bold text-dark font-13">
                                                    <i class="feather icon-image mr-1 text-primary"></i> {{ __('Additional Gallery Images') }}
                                                </span>
                                                <span class="badge badge-info text-white font-11" id="existing_images_count_label">
                                                    {{ count($data->raw_images) }} {{ __('saved') }}
                                                </span>
                                            </div>
                                            <div class="card-body p-3">
                                                {{-- Existing Gallery Images --}}
                                                <div class="mb-3">
                                                    <label class="form-label font-12 font-weight-bold text-muted mb-1">{{ __('Saved Gallery Photos:') }}</label>
                                                    <div class="row" id="existing_images_row" style="max-height: 170px; overflow-y: auto;">
                                                        @forelse($data->raw_images as $index => $img)
                                                            <div class="col-6 col-sm-4 col-md-3 mb-2 existing-image-item" id="existing_img_box_{{ $index }}">
                                                                <div class="card h-100 border shadow-xs position-relative overflow-hidden" style="border-radius: 6px;">
                                                                    <button type="button" class="btn btn-danger btn-sm position-absolute rounded-circle p-0" 
                                                                        onclick="removeExistingImage({{ $index }})" 
                                                                        style="top: 3px; right: 3px; z-index: 2; width: 20px; height: 20px; line-height: 18px; font-size: 10px;" title="{{ __('Delete Image') }}">
                                                                        <i class="feather icon-trash-2"></i>
                                                                    </button>
                                                                    <input type="hidden" name="old_gallery_images[]" value="{{ $img }}" id="old_img_input_{{ $index }}">
                                                                    <div style="height: 75px; background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                                        <img src="{{ $data->formatImageUrl($img) }}" class="img-fluid" style="max-height: 75px; max-width: 100%; object-fit: contain;">
                                                                    </div>
                                                                    <div class="card-footer p-1 text-center bg-white border-top">
                                                                        <small class="text-truncate d-block font-10 text-muted" title="{{ $img }}">{{ basename($img) }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12 text-center text-muted py-2" id="no_existing_images_msg">
                                                                <small class="text-muted font-11">{{ __('No extra gallery photos saved yet.') }}</small>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                {{-- Add New Gallery Photos --}}
                                                <div class="border-top pt-2">
                                                    <input class="form-control-file d-none" type="file" name="gallery_images[]" id="new_product_images" multiple accept="image/*">
                                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                        <label for="new_product_images" class="btn btn-sm btn-outline-primary mb-0 shadow-sm cursor-pointer" style="cursor:pointer; font-weight: 600;">
                                                            <i class="feather icon-plus-circle mr-1"></i> {{ __('Add More Photos') }}
                                                        </label>
                                                        <button type="button" class="btn btn-sm btn-outline-danger ml-2 d-none" id="clear_new_images_btn">
                                                            <i class="feather icon-trash-2 mr-1"></i> {{ __('Clear New') }}
                                                        </button>
                                                        <span class="badge badge-light border text-muted font-10 ml-auto" id="new-selected-count">{{ __('0 new') }}</span>
                                                    </div>

                                                    <div id="new_images_preview_container" class="row mt-2" style="max-height: 150px; overflow-y: auto;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (auth()->user()->branch_id == 1)
                                <div class="mt-2 col-md-12">
                                    <label class="form-label fw-bold">Branch *</label>

                                    {{-- All Select Checkbox --}}
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="select_all_branches">
                                        <label class="form-check-label fw-bold" for="select_all_branches">Select
                                            All</label>
                                    </div>

                                    {{-- Branch Checkboxes --}}
                                    <div id="branch_checkbox_list">
                                        @foreach ($allBranch as $branch)
                                            <div class="form-check">
                                                <input class="form-check-input branch-checkbox" type="checkbox"
                                                    name="branch_id[]" value="{{ $branch->id }}"
                                                    id="branch_{{ $branch->id }}"
                                                    {{ in_array($branch->id, old('branch_id', $selectedBranchIds->toArray() ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="branch_{{ $branch->id }}">
                                                    {{ $branch->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="text-danger">
                                        {{ $errors->has('branch_id') ? $errors->first('branch_id') : '' }}
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3 text-center col-12">
                                <button id="submitBtn" class="btn save_btn" type="submit"> Save </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addSizeForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Size') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label ml-3">{{ __('Size Name') }}</label>
                            <input type="text" class="form-control" name="size_name" id="size_name"
                                placeholder="{{ __('Size Name') }}">
                        </div>
                        <div id="sizeError" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn save_btn">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- color Modal -->
    <div class="modal fade" id="addColorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addColorForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Color') }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label ml-3"> {{ __('Color Name') }} </label>
                            <input type="text" class="form-control" name="color_name" placeholder="{{ __('Color Name') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn save_btn">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('.product_size, .product_color').closest('form').on('submit', function() {
                $('.product_size option, .product_color option').prop('disabled', false);
            });

            $('.product_size, .product_color').select2({
                width: '100%',
                theme: 'default',
                templateResult: function(data) {
                    if (data.selected) {
                        return null;
                    }
                    return data.text;
                }
            }).on('select2:unselecting', function(e) {
                var $option = $(this).find('option[value="' + e.params.args.data.id + '"]');
                if ($option.is(':disabled')) {
                    e.preventDefault();
                }
            });

            function disableSelectedRemoveButtons() {
                $('.product_size, .product_color').each(function() {
                    var $select = $(this);
                    setTimeout(function() {
                        $select.next('.select2-container').find('.select2-selection__choice').each(function() {
                            var $choice = $(this);
                            var select2Data = $choice.data('data');
                            if (select2Data) {
                                var $opt = $select.find('option[value="' + select2Data.id + '"]');
                                if ($opt.is(':disabled')) {
                                    $choice.find('.select2-selection__choice__remove').hide();
                                    $choice.css({
                                        'padding-left': '10px',
                                        'opacity': '0.7',
                                        'cursor': 'not-allowed'
                                    });
                                }
                            } else {
                                var title = $choice.attr('title') || $choice.text().trim();
                                title = title.replace(/^[×x\s]+/, '').trim();

                                $select.find('option').each(function() {
                                    var $opt = $(this);
                                    if ($opt.text().trim() === title && $opt.is(':disabled')) {
                                        $choice.find('.select2-selection__choice__remove').hide();
                                        $choice.css({
                                            'padding-left': '10px',
                                            'opacity': '0.7',
                                            'cursor': 'not-allowed'
                                        });
                                    }
                                });
                            }
                        });
                    }, 50);
                });
            }

            disableSelectedRemoveButtons();
            $('.product_size, .product_color').on('change', disableSelectedRemoveButtons);

            $('#addColorForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('color.ajaxStore') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addColorModal').modal('hide');
                        $('#addColorForm')[0].reset();
                        $('#colorError').text('');
                        if (response.color) {
                            var newOption = new Option(response.color.color_name, response.color.id, false, false);
                            $('select.product_color').append(newOption);
                            var currentValues = $('select.product_color').val() || [];
                            currentValues.push(response.color.id.toString());
                            $('select.product_color').val(currentValues).trigger('change');
                            $('select.product_color').trigger('change.select2');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('#colorError').text(xhr.responseJSON.errors.color_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });

            $('#addSizeForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('size.ajaxStore') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addSizeModal').modal('hide');
                        $('#addSizeForm')[0].reset();
                        $('#sizeError').text('');
                        if (response.size) {
                            var newOption = new Option(response.size.size_name, response.size.id, false, false);
                            $('select.product_size').append(newOption);
                            var currentValues = $('select.product_size').val() || [];
                            currentValues.push(response.size.id.toString());
                            $('select.product_size').val(currentValues).trigger('change');
                            $('select.product_size').trigger('change.select2');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('#sizeError').text(xhr.responseJSON.errors.size_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        document.getElementById('selling_price').addEventListener('input', calculateDiscount);
        document.getElementById('discount').addEventListener('input', calculateDiscount);

        function calculateDiscount() {
            let sale = parseFloat(document.getElementById('selling_price').value) || 0;
            let discountVal = document.getElementById('discount').value.toString();
            let discount = 0;

            if (discountVal.includes('%')) {
                let percent = parseFloat(discountVal.replace('%', '')) || 0;
                discount = (sale * percent) / 100;
            } else {
                discount = parseFloat(discountVal) || 0;
            }

            let finalPrice = sale - discount;
            if (finalPrice < 0) {
                finalPrice = 0;
            }
            document.getElementById('discount_price').value = finalPrice.toFixed(2);
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select_all_branches');
            const checkboxes = document.querySelectorAll('.branch-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });

            // Sync "Select All" if all are manually selected/deselected
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    selectAll.checked = [...checkboxes].every(cb => cb.checked);
                });
            });
        });
    </script>


    <script type="text/javascript">
        // ----------------- 1. THUMBNAIL / MAIN COVER HANDLER -----------------
        const originalThumbnailSrc = "{{ $data->thumbnail_url }}";
        const originalThumbnailLabel = "{{ $data->thumbnail ? basename($data->thumbnail) : __('Default Cover') }}";

        $('#product_thumbnail').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#thumbnail_img_elem').attr('src', evt.target.result);
                    $('#thumbnail_status_label').html('<span class="badge badge-success">{{ __("New Selected:") }} ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</span>');
                    $('#reset_thumbnail_btn').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        $('#reset_thumbnail_btn').on('click', function() {
            document.getElementById('product_thumbnail').value = '';
            $('#thumbnail_img_elem').attr('src', originalThumbnailSrc);
            $('#thumbnail_status_label').text(originalThumbnailLabel);
            $(this).addClass('d-none');
        });

        // ----------------- 2. EXISTING GALLERY REMOVAL -----------------
        function removeExistingImage(index) {
            $('#existing_img_box_' + index).fadeOut(250, function() {
                $(this).remove();
                var remaining = $('.existing-image-item').length;
                $('#existing_images_count_label').text(remaining + ' {{ __("saved") }}');
                if (remaining === 0) {
                    $('#existing_images_row').html(`
                        <div class="col-12 text-center text-muted py-2" id="no_existing_images_msg">
                            <small class="text-muted font-11">{{ __('No extra gallery photos saved.') }}</small>
                        </div>
                    `);
                }
            });
        }

        // ----------------- 3. NEW GALLERY UPLOADER -----------------
        let newDt = new DataTransfer();

        $('#new_product_images').on('change', function(e) {
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                newDt.items.add(files[i]);
            }
            this.files = newDt.files;
            renderNewImagePreviews();
        });

        function removeNewImageByIndex(index) {
            const updatedDt = new DataTransfer();
            const files = newDt.files;
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    updatedDt.items.add(files[i]);
                }
            }
            newDt = updatedDt;
            document.getElementById('new_product_images').files = newDt.files;
            renderNewImagePreviews();
        }

        $('#clear_new_images_btn').on('click', function() {
            newDt = new DataTransfer();
            document.getElementById('new_product_images').files = newDt.files;
            renderNewImagePreviews();
        });

        function renderNewImagePreviews() {
            const files = newDt.files;
            const container = $('#new_images_preview_container');
            const countBadge = $('#new-selected-count');
            const clearBtn = $('#clear_new_images_btn');

            container.empty();

            if (files.length === 0) {
                countBadge.text('{{ __("0 new") }}');
                clearBtn.addClass('d-none');
                return;
            }

            countBadge.text(files.length + ' {{ __("new") }}');
            clearBtn.removeClass('d-none');

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                const cardCol = $(`
                    <div class="col-6 col-sm-4 col-md-3 mb-2" data-index="${i}">
                        <div class="card h-100 border shadow-xs position-relative overflow-hidden" style="border-radius: 6px;">
                            <span class="badge badge-primary position-absolute" style="top: 3px; left: 3px; z-index: 2; font-size: 9px;">{{ __("New") }}</span>
                            <button type="button" class="btn btn-danger btn-sm position-absolute rounded-circle p-0" 
                                onclick="removeNewImageByIndex(${i})" 
                                style="top: 3px; right: 3px; z-index: 2; width: 20px; height: 20px; line-height: 18px; font-size: 10px;" title="{{ __('Remove') }}">
                                <i class="feather icon-x"></i>
                            </button>
                            <div style="height: 75px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img id="new_preview_thumb_${i}" src="" class="img-fluid" style="max-height: 75px; max-width: 100%; object-fit: contain;">
                            </div>
                            <div class="card-footer p-1 text-center bg-white border-top">
                                <small class="text-truncate d-block font-10 text-muted" title="${file.name}">${file.name}</small>
                                <small class="text-muted font-9">(${(file.size / 1024).toFixed(1)} KB)</small>
                            </div>
                        </div>
                    </div>
                `);

                container.append(cardCol);

                (function(idx) {
                    reader.onload = function(e) {
                        $('#new_preview_thumb_' + idx).attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                })(i);
            }
        }
    </script>
    <script>
        $('.main_unit').change(function() {
            $('.sub_unit').html('<option value="">No Related Unit Found</option>');
            var main_unit_id = $(this).find(':selected').val();
            var main_unit_text = $(this).find(':selected').text();

            let url = "{{ route('product-unit', 'my_id') }}".replace('my_id', main_unit_id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (data) {
                        var sub_value = '<option value="">Select Unit</option><option value="' + data
                            .related_unit_id + '">' + data.related_unit.name + '</option>';
                        $('.sub_unit').html(sub_value);

                        // Opening Stock
                        var opening_stock = "";
                        opening_stock +=
                            `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    } else {
                        $('.sub_unit').html('<option value="" selected>No Related Unit Found</option>');
                        // opening Stock

                        var opening_stock =
                            `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    }
                }
            });
        });

        $('.sub_unit').change(function() {
            var sub_unit_id = $(this).find(':selected').val();
            var sub_unit_text = $(this).find(':selected').text();

            var main_unit_id = $('.main_unit').find(':selected').val();
            var main_unit_text = $('.main_unit').find(':selected').text();
            var opening_stock = '';
            if (sub_unit_id == "") {
                opening_stock =
                    `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
            } else {
                opening_stock +=
                    `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}" style="margin-right:5px;">`;
                opening_stock +=
                    `<input type="text" name="sub_qty" value="" class="form-control col" placeholder="${sub_unit_text}">`;
            }

            $('.opening_stocks').html(opening_stock);

        });
    </script>
    <script>
        $(document).ready(function() {
            // Auto slug generator if empty
            $('#product_name').on('input', function() {
                var currentSlug = $('#product_slug').val().trim();
                if (!currentSlug) {
                    var str = $(this).val();
                    var slug = str.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .trim()
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    $('#product_slug').val(slug);
                }
            });

            if ($.fn.summernote) {
                $('#description').summernote({
                    placeholder: '{{ __("Enter Description...") }}',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });
            }
        $(document).ready(function() {
            // Attach input/change event listeners for all price & discount fields
            $('#purchase_price, #selling_price, #discount_price, #discount').on('input change', function() {
                var purchasePrice = parseFloat($('#purchase_price').val()) || 0;
                var sellingPrice = parseFloat($('#discount_price').val());
                if (isNaN(sellingPrice) || sellingPrice === 0) {
                    sellingPrice = parseFloat($('#selling_price').val()) || 0;
                }

                // Check if purchase price is greater than selling price (only if selling price is entered)
                if (sellingPrice > 0 && purchasePrice > sellingPrice) {
                    $('#price_error').show();
                    $('#submitBtn').attr('disabled', true);
                } else {
                    $('#price_error').hide();
                    $('#submitBtn').attr('disabled', false);
                }
            });

            // Dynamic Subcategory population
            $('#categoryId').on('change', function() {
                var category_id = $(this).val();
                var $subSelect = $('#subCategoryId');
                if ($subSelect.length) {
                    if (category_id) {
                        $.ajax({
                            url: "{{ route('sub-category.by-category') }}",
                            type: "GET",
                            data: { category_id: category_id },
                            success: function(data) {
                                $subSelect.empty().append('<option value="">Select Sub Category</option>');
                                $.each(data, function(key, value) {
                                    $subSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                                $subSelect.trigger('change');
                            }
                        });
                    } else {
                        $subSelect.empty().append('<option value="">Select Sub Category</option>').trigger('change');
                    }
                }
            });
        });
    </script>
@endpush
