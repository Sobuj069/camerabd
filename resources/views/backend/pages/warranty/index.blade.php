@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Warranty List'))

@if (check_permission('warranty.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Warranty') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    {{-- Search Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form action="{{ route('warranty.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search Warranty...') }}" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="feather icon-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('warranty.index') }}" class="btn btn-outline-secondary">
                                                <i class="feather icon-x"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Warranty Name') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Description / Terms') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warranties as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($warranties->currentPage() - 1) * $warranties->perPage() + $loop->iteration }}</td>
                                        <td class="font-weight-bold text-dark">{{ $data->name }}</td>
                                        <td>
                                            <span class="badge badge-info px-2 py-1" style="font-size: 13px;">
                                                <i class="feather icon-clock mr-1"></i>
                                                {{ $data->duration }} {{ $data->period }}{{ $data->duration > 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td>{{ $data->description ? \Illuminate\Support\Str::limit($data->description, 50) : '-' }}</td>
                                        <td>
                                            @if($data->status == 1)
                                                <span class="badge badge-success px-2 py-1">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton{{ $data->id }}" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $data->id }}">
                                                    @if (check_permission('warranty.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('warranty.destroy'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#deleteModal-{{ $data->id }}"
                                                            class="dropdown-item text-danger">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-danger py-4">{{ __('No Warranty Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Render Edit & Delete Modals --}}
                    @foreach ($warranties as $data)
                        {{-- Edit Modal --}}
                        <form action="{{ route('warranty.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Warranty') }}" id="{{ $data->id }}">
                                <div class="row">
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Warranty Name *') }}</label>
                                        <input type="text" class="form-control" name="name" placeholder="{{ __('e.g., 1 Year Warranty, 6 Months Replacement') }}"
                                            required value="{{ $data->name }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Duration (Value) *') }}</label>
                                        <input type="number" class="form-control" name="duration" min="1" step="1"
                                            placeholder="{{ __('e.g., 1, 6, 30') }}" required value="{{ $data->duration }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Period (Unit) *') }}</label>
                                        <select class="form-control" name="period" required>
                                            <option value="Day" {{ $data->period == 'Day' ? 'selected' : '' }}>{{ __('Day(s)') }}</option>
                                            <option value="Month" {{ $data->period == 'Month' ? 'selected' : '' }}>{{ __('Month(s)') }}</option>
                                            <option value="Year" {{ $data->period == 'Year' ? 'selected' : '' }}>{{ __('Year(s)') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Description / Terms') }}</label>
                                        <textarea class="form-control" name="description" rows="3" placeholder="{{ __('Enter warranty terms, conditions or policy details') }}">{{ $data->description }}</textarea>
                                    </div>
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Status') }}</label>
                                        <select class="form-control" name="status">
                                            <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- Delete Modal --}}
                        <form action="{{ route('warranty.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Warranty') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center mt-3">
                        {{ $warranties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('warranty.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add New Warranty') }}">
            <div class="row">
                <div class="mb-3 col-md-12 text-left">
                    <label class="form-label font-weight-bold">{{ __('Warranty Name *') }}</label>
                    <input type="text" class="form-control" name="name" placeholder="{{ __('e.g., 1 Year Official Warranty, 6 Months Replacement, 30 Days Warranty') }}" required>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Duration (Value) *') }}</label>
                    <input type="number" class="form-control" name="duration" min="1" step="1" placeholder="{{ __('e.g., 1, 6, 30') }}" value="1" required>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Period (Unit) *') }}</label>
                    <select class="form-control" name="period" required>
                        <option value="Year" selected>{{ __('Year(s)') }}</option>
                        <option value="Month">{{ __('Month(s)') }}</option>
                        <option value="Day">{{ __('Day(s)') }}</option>
                    </select>
                </div>
                <div class="mb-3 col-md-12 text-left">
                    <label class="form-label font-weight-bold">{{ __('Description / Terms') }}</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="{{ __('Enter warranty terms, conditions or policy details') }}"></textarea>
                </div>
                <div class="mb-3 col-md-12 text-left">
                    <label class="form-label font-weight-bold">{{ __('Status') }}</label>
                    <select class="form-control" name="status">
                        <option value="1" selected>{{ __('Active') }}</option>
                        <option value="0">{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>
        </x-add-modal>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.select2').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%',
                        allowClear: true
                    });
                });
            });
        });
    </script>
@endpush
