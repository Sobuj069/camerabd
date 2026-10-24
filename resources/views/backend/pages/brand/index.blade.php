@extends('backend.layouts.master')
@section('section-title', __('Brand'))
@section('page-title', __('Brand List'))

@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Brand') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Logo') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $data)
                                    @php
                                        $branch_id = session('branch_filter_id', auth()->user()->branch_id);
                                        if (auth()->user()->branch_id == 1 && !session('branch_filter_id')) {
                                            $branch_brand = App\Models\BranchBrand::where('brand_id', $data->id)->get();
                                        } else {
                                            $branch_brand = App\Models\BranchBrand::where('brand_id', $data->id)
                                            ->where('branch_id', auth()->user()->branch_id)
                                                ->get();
                                        }
                                        $count_cat = App\Models\Product::where('brand_id', $data->id)->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $brands->firstItem() ? ($brands->firstItem() + $loop->index) : ($loop->index + 1) }}</td>
                                        <td>
                                            @if(!empty($data->image_url))
                                                <img src="{{ $data->image_url }}" alt="{{ $data->name }}" class="rounded shadow-xs p-1 bg-white" style="width: 60px; height: 36px; object-fit: contain; border: 1px solid #e2e8f0;">
                                            @else
                                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded text-muted font-weight-bold px-2 py-1" style="border: 1px dashed #cbd5e1; font-size: 11px;">
                                                    <i class="feather icon-image text-muted mr-1"></i> {{ __('No Logo') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-left pl-3">
                                            {{ $data->name }}
                                            <span class="badge {{ $count_cat > 0 ? 'badge-info' : 'badge-light' }} ml-1" style="font-size: 11px;">
                                                {{ $count_cat }} {{ __('Products') }}
                                            </span>
                                        </td>
                                        <td>
                                            @foreach ($branch_brand as $branch)
                                                <span class="badge badge-primary">{{ $branch->branch?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                    {{-- edit --}}
                                                    @if (check_permission('brand.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('brand.destroy'))
                                                        @if ($count_cat > 0)
                                                            <a href="javascript:void(0)" class="dropdown-item text-muted" style="cursor: not-allowed; opacity: 0.7;" title="{{ __('Cannot delete: :count products are assigned to this brand', ['count' => $count_cat]) }}" onclick="alert('{{ __('Cannot delete brand \':name\'! There are :count products assigned to this brand. Please reassign or delete the products first to prevent data loss.', ['name' => addslashes($data->name), 'count' => $count_cat]) }}'); return false;">
                                                                <i class="feather icon-lock text-warning"></i> {{ __('Delete (Locked)') }}
                                                            </a>
                                                        @else
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
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($brands as $data)
                        @php
                            $selectedBranchIds = \App\Models\BranchBrand::where(
                                'brand_id',
                                $data->id,
                            )->pluck('branch_id');
                        @endphp
                        {{-- edit modal  --}}
                        <form action="{{ route('brand.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Brand') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Brand Logo / Image') }}</label>
                                    @if(!empty($data->image_url))
                                        <div class="mb-2 d-flex align-items-center">
                                            <img src="{{ $data->image_url }}" alt="{{ $data->name }}" class="rounded shadow-xs p-1 bg-white" style="max-height: 48px; max-width: 100px; object-fit: contain; border: 1px solid #e2e8f0;">
                                            <small class="text-muted ml-2">{{ __('Current Logo') }}</small>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <small class="text-muted d-block mt-1">{{ __('Leave blank to keep existing image. Recommended: PNG/SVG/WEBP or JPG, Max 4MB') }}</small>
                                </div>
                                @if (auth()->user()->branch_id == 1)
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                        <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                                            @foreach ($branchs as $branch_item)
                                                <option value="{{ $branch_item->id }}"{{ in_array($branch_item->id, $selectedBranchIds->toArray()) ? 'selected' : '' }}>{{ $branch_item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('brand.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Brand') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('brand.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <x-add-modal title="{{ __('Add Brand') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Brand Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Brand Name') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Brand Logo / Image') }}</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="text-muted d-block mt-1">{{ __('Recommended: PNG/SVG/WEBP or JPG, Max 4MB. If not provided, a default brand card placeholder will be used.') }}</small>
            </div>
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12 text-left">
                    <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                    <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                        @foreach ($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </x-add-modal>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for ALL Bootstrap modals on this page
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.select2').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select Option',
                        allowClear: true
                    });
                });
            });
        });
    </script>
@endpush
