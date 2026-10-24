@extends('backend.layouts.master')
@section('section-title', __('Banners & Sliders'))
@section('page-title', __('Add New Banner'))

@section('action-button')
    <a href="{{ route('banner.index') }}" class="btn btn-outline-secondary">
        <i class="mr-1 feather icon-arrow-left"></i>
        {{ __('Back to Banner List') }}
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-xl">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="m-0 font-weight-bold text-dark">
                        <i class="feather icon-plus-circle text-primary mr-2"></i> {{ __('Upload & Configure Banner') }}
                    </h5>
                    <span class="badge badge-light border text-muted">{{ __('Frontend Display') }}</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            {{-- Left Column: Details --}}
                            <div class="col-md-7">
                                {{-- Banner Position --}}
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">
                                        {{ __('Banner Position') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="position" id="bannerPosition" class="form-control @error('position') is-invalid @enderror" required onchange="updateDimensionGuide(this.value)">
                                        <option value="main_hero" {{ old('position') === 'main_hero' ? 'selected' : '' }}>
                                            {{ __('Main Hero Billboard / Slider (Left 8 Columns)') }}
                                        </option>
                                        <option value="side_top" {{ old('position') === 'side_top' ? 'selected' : '' }}>
                                            {{ __('Side Top Promo Banner (Right Top Card)') }}
                                        </option>
                                        <option value="side_bottom" {{ old('position') === 'side_bottom' ? 'selected' : '' }}>
                                            {{ __('Side Bottom Promo Banner (Right Bottom Card)') }}
                                        </option>
                                        <option value="middle_banner" {{ old('position') === 'middle_banner' ? 'selected' : '' }}>
                                            {{ __('Middle Full-Width Promo Banner') }}
                                        </option>
                                    </select>
                                    <small class="form-text text-muted" id="dimensionGuide">
                                        <i class="feather icon-info mr-1 text-primary"></i>
                                        <span id="dimensionText">{{ __('Recommended dimensions: 1200 x 600 px (Aspect ratio ~ 2:1 or 16:9)') }}</span>
                                    </small>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Title --}}
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">{{ __('Banner Title / Heading') }}</label>
                                    <input type="text" name="title" value="{{ old('title') }}" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           placeholder="e.g. DJI Osmo Pocket 4P Creator Combo">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Subtitle / Tagline --}}
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">{{ __('Subtitle / Short Description') }}</label>
                                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" 
                                           class="form-control @error('subtitle') is-invalid @enderror" 
                                           placeholder="e.g. 1-Inch Dual Lens 4K/120fps Stabilization">
                                    @error('subtitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Link / URL --}}
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">{{ __('Redirect Link / Click URL') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light"><i class="feather icon-link"></i></span>
                                        </div>
                                        <input type="text" name="link" value="{{ old('link') }}" 
                                               class="form-control @error('link') is-invalid @enderror" 
                                               placeholder="e.g. /product-details/dji-osmo-pocket-4p or /catalog/drone">
                                    </div>
                                    <small class="form-text text-muted">{{ __('When visitors click this banner, they will be redirected to this link.') }}</small>
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    {{-- Order --}}
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="font-weight-bold text-dark">{{ __('Display Order') }}</label>
                                        <input type="number" name="order" value="{{ old('order', 1) }}" min="0" 
                                               class="form-control @error('order') is-invalid @enderror">
                                        <small class="form-text text-muted">{{ __('Lower number appears first') }}</small>
                                        @error('order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6 form-group mb-3 d-flex flex-col justify-content-center">
                                        <label class="font-weight-bold text-dark mb-2">{{ __('Publication Status') }}</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="status" class="custom-control-input" id="bannerStatus" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-success" for="bannerStatus">{{ __('Active & Visible') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Image Upload & Preview --}}
                            <div class="col-md-5">
                                <div class="card bg-light border p-3 rounded-xl mb-3">
                                    <label class="font-weight-bold text-dark mb-2">
                                        {{ __('Banner Image') }} <span class="text-danger">*</span>
                                    </label>
                                    
                                    {{-- Live Preview Box --}}
                                    <div class="mb-3 rounded-lg overflow-hidden border bg-white d-flex align-items-center justify-content-center text-center p-2" style="min-height: 180px; max-height: 240px;">
                                        <img id="imagePreview" src="https://placehold.co/800x450/f1f5f9/94a3b8?text=Banner+Preview" 
                                             alt="Preview" class="img-fluid rounded" style="max-height: 220px; width: 100%; object-fit: cover;">
                                    </div>

                                    <div class="custom-file mb-2">
                                        <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="bannerImageInput" accept="image/*" required onchange="previewBannerImage(this)">
                                        <label class="custom-file-label" for="bannerImageInput" id="bannerImageLabel">{{ __('Choose Image File...') }}</label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="feather icon-check-circle text-success mr-1"></i> {{ __('Supports: JPG, PNG, WEBP, SVG (Max 8MB)') }}
                                    </small>
                                    @error('image')
                                        <div class="text-danger text-xs mt-1 font-weight-bold">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <a href="{{ route('banner.index') }}" class="btn btn-secondary mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm">
                                <i class="feather icon-upload-cloud mr-1"></i> {{ __('Save & Publish Banner') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewBannerImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                document.getElementById('bannerImageLabel').textContent = input.files[0].name;
            }
        }

        function updateDimensionGuide(position) {
            const guide = document.getElementById('dimensionText');
            if (position === 'main_hero') {
                guide.textContent = "{{ __('Recommended: 1200 x 600 px (Aspect ratio ~ 2:1 or 16:9)') }}";
            } else if (position === 'side_top' || position === 'side_bottom') {
                guide.textContent = "{{ __('Recommended: 600 x 300 px (Aspect ratio ~ 2:1 or 16:9)') }}";
            } else {
                guide.textContent = "{{ __('Recommended: 1400 x 400 px (Wide Panoramic Strip)') }}";
            }
        }
    </script>
@endsection
