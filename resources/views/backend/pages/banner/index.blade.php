@extends('backend.layouts.master')
@section('section-title', __('Banners & Sliders'))
@section('page-title', __('Frontend Banner Management'))

@section('action-button')
    <a href="{{ route('banner.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add New Banner') }}
    </a>
@endsection

@section('content')
    {{-- Summary / Filter Cards --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('banner.index') }}" class="card text-decoration-none border-0 shadow-sm rounded-lg {{ empty($position) ? 'bg-primary text-white' : 'bg-white text-dark' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">{{ __('All Banners') }}</div>
                        <div class="h4 font-weight-bold mb-0">{{ $totalBanners }}</div>
                    </div>
                    <div class="rounded-circle p-2 {{ empty($position) ? 'bg-white text-primary' : 'bg-light text-primary' }}">
                        <i class="feather icon-image" style="font-size: 20px;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('banner.index', ['position' => 'main_hero']) }}" class="card text-decoration-none border-0 shadow-sm rounded-lg {{ $position === 'main_hero' ? 'bg-primary text-white' : 'bg-white text-dark' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">{{ __('Main Hero Billboard') }}</div>
                        <div class="h4 font-weight-bold mb-0">{{ $mainHeroCount }}</div>
                    </div>
                    <div class="rounded-circle p-2 {{ $position === 'main_hero' ? 'bg-white text-primary' : 'bg-light text-success' }}">
                        <i class="feather icon-monitor" style="font-size: 20px;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('banner.index', ['position' => 'side_top']) }}" class="card text-decoration-none border-0 shadow-sm rounded-lg {{ $position === 'side_top' ? 'bg-primary text-white' : 'bg-white text-dark' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">{{ __('Side Top Banner') }}</div>
                        <div class="h4 font-weight-bold mb-0">{{ $sideTopCount }}</div>
                    </div>
                    <div class="rounded-circle p-2 {{ $position === 'side_top' ? 'bg-white text-primary' : 'bg-light text-info' }}">
                        <i class="feather icon-sidebar" style="font-size: 20px;"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ route('banner.index', ['position' => 'side_bottom']) }}" class="card text-decoration-none border-0 shadow-sm rounded-lg {{ $position === 'side_bottom' ? 'bg-primary text-white' : 'bg-white text-dark' }}">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">{{ __('Side Bottom Banner') }}</div>
                        <div class="h4 font-weight-bold mb-0">{{ $sideBottomCount }}</div>
                    </div>
                    <div class="rounded-circle p-2 {{ $position === 'side_bottom' ? 'bg-white text-primary' : 'bg-light text-warning' }}">
                        <i class="feather icon-layout" style="font-size: 20px;"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="m-0 font-weight-bold text-dark">
                        <i class="feather icon-layers mr-2 text-primary"></i>
                        {{ __('Active Banners List') }} 
                        @if($position)
                            <span class="badge badge-secondary font-weight-normal text-capitalize ml-2">{{ str_replace('_', ' ', $position) }}</span>
                        @endif
                    </h5>
                    <a href="{{ route('banner.create') }}" class="btn btn-sm btn-primary">
                        <i class="feather icon-plus mr-1"></i> {{ __('Add Banner') }}
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="header_bg text-white">
                                <tr>
                                    <th class="header_style_left py-3" style="width: 60px;">{{ __('#SL') }}</th>
                                    <th class="py-3" style="width: 140px;">{{ __('Preview') }}</th>
                                    <th class="py-3 text-left">{{ __('Banner Details') }}</th>
                                    <th class="py-3">{{ __('Position') }}</th>
                                    <th class="py-3" style="width: 80px;">{{ __('Order') }}</th>
                                    <th class="py-3" style="width: 100px;">{{ __('Status') }}</th>
                                    <th class="header_style_right py-3" style="width: 130px;">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banners as $banner)
                                    <tr>
                                        <td class="table_data_style_left font-weight-bold text-muted">
                                            {{ $banners->firstItem() ? ($banners->firstItem() + $loop->index) : ($loop->index + 1) }}
                                        </td>
                                        <td>
                                            <div class="rounded-lg overflow-hidden border bg-light p-1 shadow-2xs d-inline-block" style="max-width: 120px;">
                                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" 
                                                     class="img-fluid rounded" 
                                                     style="height: 55px; width: 110px; object-fit: cover;">
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="font-weight-bold text-dark text-base">{{ $banner->title ?: __('(No Title)') }}</div>
                                            @if($banner->subtitle)
                                                <div class="text-xs text-muted text-truncate" style="max-width: 320px;">{{ $banner->subtitle }}</div>
                                            @endif
                                            @if($banner->link)
                                                <a href="{{ $banner->link }}" target="_blank" class="text-xs text-primary d-inline-flex align-items-center mt-1">
                                                    <i class="feather icon-link mr-1"></i>
                                                    <span class="text-truncate" style="max-width: 250px;">{{ $banner->link }}</span>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($banner->position === 'main_hero')
                                                <span class="badge badge-success px-2.5 py-1.5 font-weight-bold text-xs">
                                                    <i class="feather icon-monitor mr-1"></i> {{ __('Main Hero Billboard') }}
                                                </span>
                                            @elseif($banner->position === 'side_top')
                                                <span class="badge badge-info px-2.5 py-1.5 font-weight-bold text-xs">
                                                    <i class="feather icon-sidebar mr-1"></i> {{ __('Side Top Promo') }}
                                                </span>
                                            @elseif($banner->position === 'side_bottom')
                                                <span class="badge badge-warning px-2.5 py-1.5 font-weight-bold text-xs">
                                                    <i class="feather icon-layout mr-1"></i> {{ __('Side Bottom Promo') }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary px-2.5 py-1.5 font-weight-bold text-xs">
                                                    <i class="feather icon-image mr-1"></i> {{ __('Middle Promo') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light border text-dark font-weight-bold px-2 py-1">{{ $banner->order }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('banner.toggle-status', $banner->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $banner->status ? 'btn-outline-success' : 'btn-outline-secondary' }} px-2.5 py-1 rounded-pill font-weight-bold" title="Click to Toggle">
                                                    <i class="feather {{ $banner->status ? 'icon-check-circle' : 'icon-slash' }} mr-1"></i>
                                                    {{ $banner->status ? __('Active') : __('Inactive') }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <a href="{{ route('banner.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary p-1.5 rounded" title="Edit Banner">
                                                    <i class="feather icon-edit" style="font-size: 15px;"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger p-1.5 rounded" data-toggle="modal" data-target="#deleteModal-{{ $banner->id }}" title="Delete Banner">
                                                    <i class="feather icon-trash-2" style="font-size: 15px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Delete Confirmation Modal --}}
                                    <div class="modal fade" id="deleteModal-{{ $banner->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title font-weight-bold"><i class="feather icon-alert-triangle mr-2"></i> {{ __('Confirm Delete') }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center p-4">
                                                    <div class="mb-3">
                                                        <img src="{{ $banner->image_url }}" alt="Preview" class="img-fluid rounded border" style="max-height: 90px;">
                                                    </div>
                                                    <h6 class="font-weight-bold text-dark">{{ $banner->title ?: __('Selected Banner') }}</h6>
                                                    <p class="text-muted text-sm mb-0">{{ __('Are you sure you want to delete this banner? This action cannot be undone.') }}</p>
                                                </div>
                                                <div class="modal-footer bg-light justify-content-between">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <form action="{{ route('banner.destroy', $banner->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete Banner') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="mb-2">
                                                <i class="feather icon-image text-slate-300" style="font-size: 40px;"></i>
                                            </div>
                                            <h6 class="font-weight-bold text-dark">{{ __('No Banners Found') }}</h6>
                                            <p class="text-xs text-muted mb-3">{{ __('Add banners to showcase on the homepage hero and promo sections.') }}</p>
                                            <a href="{{ route('banner.create') }}" class="btn btn-primary btn-sm">
                                                <i class="feather icon-plus mr-1"></i> {{ __('Add Your First Banner') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($banners->hasPages())
                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                        {{ $banners->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
