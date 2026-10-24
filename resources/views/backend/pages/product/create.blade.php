@extends('backend.layouts.master')
@section('section-title', __('Product'))
@section('page-title', __('Add Product'))

@push('css')
@endpush

@if (check_permission('product.update'))
    @section('action-button')
        <a href="{{ route('product.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            {{ __('All Product') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div>
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('product.store') }}"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            {{-- Product name --}}
                            <div class="mt-2 col-md-6">
                                <label for="name" class="form-label fw-bold">{{ __('Name *') }}</label>
                                <input type="text" onfocus="this.select()" autofocus class="form-control" name="name"
                                    placeholder="{{ __('Enter Name') }}">
                                <div class="errors">{{ $errors->has('name') ? $errors->first('name') : '' }}</div>
                            </div>

                            {{-- Product barcode  --}}
                            <div class="mt-2 col-md-6">
                                <label for="barcode" class="form-label fw-bold">{{ __('Barcode') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Enter Barcode') }}" name="barcode">
                                <div class="errors">{{ $errors->has('barcode') ? $errors->first('barcode') : '' }}</div>
                            </div>

                            {{-- Category --}}
                            <div class="mt-2 col-md-6">
                                <div class="row align-items-end">
                                    <div class="col-10 pr-0">
                                        <label for="unit_id" class="form-label fw-bold mb-1">{{ __('Category *') }}</label>
                                        <select class="select2" name="category_id" id="categoryId">
                                            <option selected value="">{{ __('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="errors text-danger text-[10px]">
                                            {{ $errors->has('category_id') ? $errors->first('category_id') : '' }}</div>
                                    </div>
                                    <div class="col-2 pl-1">
                                        <button data-toggle="modal" type="button" data-target="#addModal1"
                                            class="btn extra_btn mb-0 shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- Sub Category --}}
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="sub_category_id" class="form-label fw-bold mb-1">{{ __('Sub Category') }}</label>
                                            <select class="select2" name="sub_category_id" id="subCategoryId">
                                                <option selected value="">{{ __('Select Sub Category') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <a href="{{ route('sub-category.index') }}" target="_blank" type="button" class="btn extra_btn mb-0 shadow-sm">
                                                <i class="feather icon-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- Brand --}}
                            <div class="mt-2 col-md-6">
                                <div class="row align-items-end">
                                    <div class="col-10 pr-0">
                                        <label for="brand_id" class="form-label fw-bold mb-1">{{ __('Brand') }}</label>
                                        <select class="select2" name="brand_id" id="brandId">
                                            <option selected value="">{{ __('Select Brand') }}</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2 pl-1">
                                        <button data-toggle="modal" type="button" data-target="#addModal"
                                            class="btn extra_btn mb-0 shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Unit --}}
                            <div class="mt-2 col-md-3 ">
                                <label for="unit_id" class="form-label fw-bold">{{ __('Main Unit') }}</label>
                                <select class="select2 main_unit" name="unit_id">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Unit --}}
                            <div class="mt-2 col-md-3 ">
                                <label for="unit_id" class="form-label fw-bold">{{ __('Sub Unit') }}</label>
                                <select name="sub_unit_id" id="" class="form-control sub_unit">
                                    <option value="">{{ __('No Related Unit Found') }}</option>
                                </select>
                            </div>

                            {{-- Unit --}}
                            <div class="mt-2 col-md-6 ">
                                <div class="form-group">
                                    <label for="">{{ __('Low Stock Quantity') }}</label>
                                    <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                        <input type="text" name="main_qty" value="" class="form-control col"
                                            placeholder="{{ __('Pcs') }}">
                                    </div>
                                </div>
                            </div>
                            {{-- variation --}}
                            @if (env('APP_SC') == 'yes')
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="size" class="form-label fw-bold mb-1">{{ __('Size') }} </label>
                                            <select class="select2 product_size" name="size[]" multiple data-placeholder="{{ __('Select Size') }}">
                                                <option value=""></option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->size }}</option>
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
                                            <label for="color" class="form-label fw-bold mb-1">{{ __('Color') }} </label>
                                            <select class="select2 product_color" name="color[]" multiple data-placeholder="{{ __('Select Color') }}">
                                                <option value=""></option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}"> {{ $color->color }}</option>
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
                                <label for="purchase_price" class="form-label fw-bold">{{ __('Purchase Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="purchase_price" id="purchase_price" placeholder="0.00">
                                <div class="errors">
                                    {{ $errors->has('purchase_price') ? $errors->first('purchase_price') : '' }}
                                </div>
                            </div>
                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Sale Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="dis_selling_price" id="selling_price" placeholder="0.00">
                            </div>

                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Discount') }}</label>
                                <input type="text" class="form-control" name="discount" id="discount" placeholder="e.g. 10 or 10%">
                            </div>

                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Discount Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="selling_price" id="discount_price"
                                    readonly placeholder="0.00">

                                <div id="price_error" style="color:red; display:none;">
                                    {{ __('Sale price cannot be below purchase price') }}
                                </div>
                            </div>
                            {{-- status --}}
                            <div class="col-md-6 mt-2">
                                <label for="status" class="form-label fw-bold">{{ __('Status *') }}</label>
                                <select class="select2" name="status">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactive') }}</option>
                                </select>
                                <div class="errors">{{ $errors->has('status') ? $errors->first('status') : '' }}</div>
                            </div>

                            {{-- Show on Website --}}
                            <div class="col-md-6 mt-2">
                                <label class="form-label fw-bold d-block">{{ __('Show on Website (ওয়েবসাইটে প্রদর্শন)') }}</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" class="custom-control-input" id="show_on_website" name="show_on_website" value="1" {{ old('show_on_website', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="show_on_website">{{ __('Display this product on Website Frontend (Default: Checked)') }}</label>
                                </div>
                            </div>
                            @if (env('APP_IMEI') == 'yes')
                            <div class="mt-2 col-md-6  ">
                                <label for="imei" class="form-label fw-bold">{{ __('Has IMEI') }}</label>
                                <select class="select2" name="imei">
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                            </div>
                            @endif
                            <div class="mt-2 col-md-6">
                                <label for="warranty_id" class="form-label fw-bold">{{ __('Warranty') }}</label>
                                <select class="select2" name="warranty_id" id="warranty_id">
                                    <option value="">{{ __('No Warranty / Select Warranty') }}</option>
                                    @if(isset($warranties))
                                        @foreach($warranties as $warranty)
                                            <option value="{{ $warranty->id }}" {{ old('warranty_id') == $warranty->id ? 'selected' : '' }}>
                                                {{ $warranty->name }} ({{ $warranty->duration }} {{ $warranty->period }}{{ $warranty->duration > 1 ? 's' : '' }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                             <div class="mt-2 col-md-12">
                                 <label for="description" class="form-label fw-bold">{{ __('Description') }}</label>
                                 <textarea class="form-control summernote" id="description" name="description" rows="4"></textarea>
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
                                                    <input type="checkbox" class="custom-control-input" id="is_flash_deal" name="is_flash_deal" value="1" {{ old('is_flash_deal') ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_flash_deal">
                                                        ⚡ {{ __('Broadcast & Studio Flash') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_popular" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_popular">
                                                        🔥 {{ __('Popular Cinema & Creator Gear') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_hollyland" name="is_hollyland" value="1" {{ old('is_hollyland') ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="is_hollyland">
                                                        🎙️ {{ __('Hollyland Microphone') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox p-2 rounded" style="background: #fff; border: 1px solid #e2e8f0;">
                                                    <input type="checkbox" class="custom-control-input" id="is_essential" name="is_essential" value="1" {{ old('is_essential') ? 'checked' : '' }}>
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
                                                <input class="form-control-file d-none" type="file" name="thumbnail" id="product_thumbnail" accept="image/*">
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <label for="product_thumbnail" class="btn btn-sm btn-outline-warning text-dark mb-0 shadow-sm cursor-pointer" style="cursor:pointer; font-weight: 600;">
                                                        <i class="feather icon-upload-cloud mr-1"></i> {{ __('Select Thumbnail') }}
                                                    </label>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ml-2 d-none" id="clear_thumbnail_btn">
                                                        <i class="feather icon-trash-2 mr-1"></i> {{ __('Remove') }}
                                                    </button>
                                                </div>

                                                <div id="thumbnail_preview_box" class="border rounded p-2 text-center bg-white" style="min-height: 150px; display: flex; align-items: center; justify-content: center; position: relative;">
                                                    <div id="no_thumbnail_placeholder" class="text-muted py-2">
                                                        <img src="{{ asset('backend/images/no_images.png') }}" style="max-height: 80px; opacity: 0.6;" alt="No thumbnail">
                                                        <p class="small mt-1 mb-0 text-muted">{{ __('No thumbnail chosen yet') }}</p>
                                                    </div>
                                                    <div id="active_thumbnail_preview" class="d-none w-100">
                                                        <img id="thumbnail_img_elem" src="" class="img-fluid rounded mb-1" style="max-height: 130px; object-fit: contain;">
                                                        <div class="mt-1">
                                                            <small class="text-truncate d-block font-11 text-dark font-weight-bold" id="thumbnail_name_elem"></small>
                                                            <small class="text-muted font-10" id="thumbnail_size_elem"></small>
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
                                                <span class="badge badge-info text-white font-11" id="selected-gallery-count">{{ __('0 selected') }}</span>
                                            </div>
                                            <div class="card-body p-3">
                                                <small class="text-muted d-block mb-2">
                                                    {{ __('Upload multiple extra photos (angles, box, accessories) for product details slide gallery.') }}
                                                </small>
                                                <input class="form-control-file d-none" type="file" name="gallery_images[]" id="product_gallery_images" multiple accept="image/*">
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                                    <label for="product_gallery_images" class="btn btn-sm btn-outline-primary mb-0 shadow-sm cursor-pointer" style="cursor:pointer; font-weight: 600;">
                                                        <i class="feather icon-plus-circle mr-1"></i> {{ __('Select Gallery Photos') }}
                                                    </label>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ml-2 d-none" id="clear_all_gallery_btn">
                                                        <i class="feather icon-trash-2 mr-1"></i> {{ __('Clear All') }}
                                                    </button>
                                                </div>

                                                <div id="gallery_preview_container" class="row" style="min-height: 150px; max-height: 260px; overflow-y: auto;">
                                                    <div class="col-12 text-center text-muted py-4" id="no_gallery_placeholder">
                                                        <img src="{{ asset('backend/images/no_images.png') }}" style="max-height: 60px; opacity: 0.5;" alt="No gallery">
                                                        <p class="small mt-1 mb-0">{{ __('No additional gallery images chosen') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (auth()->user()->branch_id == 1)
                                <div class="mt-2 col-md-12">
                                    <label class="form-label fw-bold">{{ __('Branch *') }}</label>

                                    {{-- All Select Checkbox --}}
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="select_all_branches" checked>
                                        <label class="form-check-label fw-bold" for="select_all_branches">{{ __('Select All') }}</label>
                                    </div>

                                    {{-- Branch Checkboxes --}}
                                    <div id="branch_checkbox_list">
                                        @foreach ($allBranch as $branch)
                                            <div class="form-check">
                                                <input class="form-check-input branch-checkbox" type="checkbox"
                                                    name="branch_id[]" value="{{ $branch->id }}"
                                                    id="branch_{{ $branch->id }}" checked>
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
                                <button id="submitBtn" class="btn save_btn" type="submit"> {{ __('Save') }} </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    {{-- Add Category Modal --}}
    <form action="#" id="categoryForm" method="POST">
        @csrf
        <x-another-modal title="{{ __('Add Category') }}" sizeClass="modal-md">
            <x-input label="{{ __('Category Name *') }}" type="text" name="name" placeholder="{{ __('Enter Category Name') }}" required />
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12">
                    <label class="form-label fw-bold mb-1">{{ __('Branch *') }}</label>
                    <select name="branch_id[]" class="form-control select2" multiple style="width: 100%">
                        @foreach ($allBranch as $branch_item)
                            <option value="{{ $branch_item->id }}">{{ $branch_item->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </x-another-modal>
    </form>

    {{-- Add Brand Modal --}}
    <form action="#" id="brandForm" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Brand') }}" sizeClass="modal-md">
            <x-input label="{{ __('Brand Name *') }}" type="text" name="name" placeholder="{{ __('Enter Brand Name') }}" required />
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12">
                    <label class="form-label fw-bold mb-1">{{ __('Branch *') }}</label>
                    <select name="branch_id[]" class="form-control select2" multiple style="width: 100%">
                        @foreach ($allBranch as $branch_item)
                            <option value="{{ $branch_item->id }}">{{ $branch_item->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </x-add-modal>
    </form>
    <div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addSizeForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Size') }}</h5>
                        <!-- ✅ Close button in header -->
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
        // toastr compatibility wrapper for ToastMagic
        if (typeof toastr === 'undefined') {
            window.toastr = {
                success: function(msg) {
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.success(msg);
                    } else if (typeof ToastMagic !== 'undefined') {
                        (new ToastMagic()).success(msg);
                    } else {
                        console.log('Success:', msg);
                    }
                },
                error: function(msg) {
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.error(msg);
                    } else if (typeof ToastMagic !== 'undefined') {
                        (new ToastMagic()).error(msg);
                    } else {
                        console.error('Error:', msg);
                    }
                }
            };
        }

        $(document).ready(function() {

            $('#addColorForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('color.ajaxStore') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {

                        // Modal hide
                        $('#addColorModal').modal('hide');
                        $('#addColorForm')[0].reset();
                        $('#colorError').text('');
                        if (response.color) {
                            // Create new option
                            var newOption = new Option(response.color.color_name, response.color
                                .id, false, false);

                            // Append to select2
                            $('select.product_color').append(newOption);

                            // ✅ Get current selected values
                            var currentValues = $('select.product_color').val() || [];

                            // ✅ Add new value to selected values array
                            currentValues.push(response.color.id.toString());

                            // ✅ Set multiple values
                            $('select.product_color').val(currentValues).trigger('change');

                            // Refresh select2
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
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#addSizeForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('size.ajaxStore') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {

                        // Modal hide
                        $('#addSizeModal').modal('hide');
                        $('#addSizeForm')[0].reset();
                        $('#sizeError').text('');

                        // ✅ Dynamically add new option to select2 WITHOUT page reload
                        if (response.size) {
                            // Create new option
                            var newOption = new Option(response.size.size_name, response.size
                                .id, false, false);

                            // Append to select2
                            $('select.product_size').append(newOption);

                            // ✅ Get current selected values
                            var currentValues = $('select.product_size').val() || [];

                            // ✅ Add new value to selected values array
                            currentValues.push(response.size.id.toString());

                            // ✅ Set multiple values
                            $('select.product_size').val(currentValues).trigger('change');

                            // Refresh select2
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


    {{-- JavaScript --}}
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
        $('#product_thumbnail').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#thumbnail_img_elem').attr('src', evt.target.result);
                    $('#thumbnail_name_elem').text(file.name);
                    $('#thumbnail_size_elem').text('(' + (file.size / 1024).toFixed(1) + ' KB)');
                    $('#no_thumbnail_placeholder').addClass('d-none');
                    $('#active_thumbnail_preview').removeClass('d-none');
                    $('#clear_thumbnail_btn').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        $('#clear_thumbnail_btn').on('click', function() {
            document.getElementById('product_thumbnail').value = '';
            $('#thumbnail_img_elem').attr('src', '');
            $('#thumbnail_name_elem').text('');
            $('#thumbnail_size_elem').text('');
            $('#active_thumbnail_preview').addClass('d-none');
            $('#no_thumbnail_placeholder').removeClass('d-none');
            $(this).addClass('d-none');
        });

        // ----------------- 2. ADDITIONAL GALLERY HANDLER -----------------
        let galleryDt = new DataTransfer();

        $('#product_gallery_images').on('change', function(e) {
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                galleryDt.items.add(files[i]);
            }
            this.files = galleryDt.files;
            renderGalleryPreviews();
        });

        function removeGalleryImageByIndex(index) {
            const newDt = new DataTransfer();
            const files = galleryDt.files;
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    newDt.items.add(files[i]);
                }
            }
            galleryDt = newDt;
            document.getElementById('product_gallery_images').files = galleryDt.files;
            renderGalleryPreviews();
        }

        $('#clear_all_gallery_btn').on('click', function() {
            galleryDt = new DataTransfer();
            document.getElementById('product_gallery_images').files = galleryDt.files;
            renderGalleryPreviews();
        });

        function renderGalleryPreviews() {
            const files = galleryDt.files;
            const container = $('#gallery_preview_container');
            const countBadge = $('#selected-gallery-count');
            const clearBtn = $('#clear_all_gallery_btn');

            container.empty();

            if (files.length === 0) {
                container.html(`
                    <div class="col-12 text-center text-muted py-4" id="no_gallery_placeholder">
                        <img src="{{ asset('backend/images/no_images.png') }}" style="max-height: 60px; opacity: 0.5;" alt="No gallery">
                        <p class="small mt-1 mb-0">{{ __('No additional gallery images chosen') }}</p>
                    </div>
                `);
                countBadge.text('{{ __("0 selected") }}');
                clearBtn.addClass('d-none');
                return;
            }

            countBadge.text(files.length + ' {{ __("selected") }}');
            clearBtn.removeClass('d-none');

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                const cardCol = $(`
                    <div class="col-6 col-sm-4 col-md-3 mb-3" data-index="${i}">
                        <div class="card h-100 border shadow-xs position-relative overflow-hidden" style="border-radius: 8px;">
                            <span class="badge badge-secondary position-absolute" style="top: 4px; left: 4px; z-index: 2; font-size: 9px;">#${i + 1}</span>
                            <button type="button" class="btn btn-danger btn-sm position-absolute rounded-circle p-0" 
                                onclick="removeGalleryImageByIndex(${i})" 
                                style="top: 4px; right: 4px; z-index: 2; width: 22px; height: 22px; line-height: 20px; font-size: 11px;" title="{{ __('Remove') }}">
                                <i class="feather icon-x"></i>
                            </button>
                            <div style="height: 90px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img id="gallery_preview_thumb_${i}" src="" class="img-fluid" style="max-height: 90px; max-width: 100%; object-fit: contain;">
                            </div>
                            <div class="card-footer p-1 text-center bg-white border-top">
                                <small class="text-truncate d-block font-11 text-muted" title="${file.name}">${file.name}</small>
                                <small class="text-muted font-10">(${(file.size / 1024).toFixed(1)} KB)</small>
                            </div>
                        </div>
                    </div>
                `);

                container.append(cardCol);

                (function(idx) {
                    reader.onload = function(e) {
                        $('#gallery_preview_thumb_' + idx).attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                })(i);
            }
        }
    </script>
    <script>
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
                    $('#price_error').show(); // Show error message
                    $('#submitBtn').attr('disabled', true); // Disable submit button
                } else {
                    $('#price_error').hide(); // Hide error message
                    $('#submitBtn').attr('disabled', false); // Enable submit button
                }
            });
        });
    </script>
    <script>
        //category modal ajax code
        $(document).ready(function() {
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: "{{ route('category.store') }}", // Define the route for submission
                    method: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        $('#addModal1').modal('hide');
                        $('#categoryForm')[0].reset();
                        // Reset and reload the dropdown with Select2 re-initialization
                        if ($('#categoryId').data('select2')) {
                            $('#categoryId').select2('destroy');
                        }
                        $('#categoryId').load(location.href + ' #categoryId>*', function() {
                            $('#categoryId').select2({ width: '100%' });
                            toastr.success(response.message || 'Category created successfully');
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors && errors.name) {
                                toastr.error(errors.name[0]);
                            } else {
                                toastr.error('Validation failed!');
                            }
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        //Brand modal ajax code
        $(document).ready(function() {
            $('#brandForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: "{{ route('brand.store') }}", // Define the route for submission
                    method: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        $('#addModal').modal('hide');
                        $('#brandForm')[0].reset();
                        // Reset and reload the dropdown with Select2 re-initialization
                        if ($('#brandId').data('select2')) {
                            $('#brandId').select2('destroy');
                        }
                        $('#brandId').load(location.href + ' #brandId>*', function() {
                            $('#brandId').select2({ width: '100%' });
                            toastr.success(response.message || 'Brand created successfully');
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors && errors.name) {
                                toastr.error(errors.name[0]);
                            } else {
                                toastr.error('Validation failed!');
                            }
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $('.main_unit').change(function() {
            $('.sub_unit').html('<option value="">{{ __('No Related Unit Found') }}</option>');
            var main_unit_id = $(this).find(':selected').val();
            var main_unit_text = $(this).find(':selected').text();

            let url = "{{ route('product-unit', 'my_id') }}".replace('my_id', main_unit_id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (data) {
                        var sub_value = '<option value="">{{ __('Select Unit') }}</option><option value="' + data
                            .related_unit_id + '">' + data.related_unit.name + '</option>';
                        $('.sub_unit').html(sub_value);

                        // Opening Stock
                        var opening_stock = "";
                        opening_stock +=
                            `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    } else {
                        $('.sub_unit').html('<option value="" selected>{{ __('No Related Unit Found') }}</option>');
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

            // Fix Select2 focus and dropdown issue inside Bootstrap modals
            $('#addModal, #addModal1').on('shown.bs.modal', function () {
                $(this).find('.select2').select2({
                    dropdownParent: $(this),
                    width: '100%',
                    placeholder: 'Select Branches',
                    allowClear: true
                });
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
                                $subSelect.empty().append('<option value="">{{ __("Select Sub Category") }}</option>');
                                $.each(data, function(key, value) {
                                    $subSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                                $subSelect.trigger('change');
                            }
                        });
                    } else {
                        $subSelect.empty().append('<option value="">{{ __("Select Sub Category") }}</option>').trigger('change');
                    }
                }
            });
        });
    </script>
@endpush
