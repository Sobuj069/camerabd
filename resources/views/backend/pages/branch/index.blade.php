@extends('backend.layouts.master')
@section('section-title', __('Branch'))
@section('page-title', __('Branch List'))
@if (check_permission('branch.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Branch') }}
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            .h-hide {
                display: none;
            }
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Badge') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Schedule') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branchs as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td class="font-weight-bold text-left pl-3">{{ $data->name }}</td>
                                        <td>
                                            <span class="badge badge-success px-2 py-1">{{ $data->badge ?: 'Open Daily' }}</span>
                                        </td>
                                        <td>{{ $data->phone }}</td>
                                        <td class="small">{{ $data->schedule ?: '-' }}</td>
                                        <td class="small text-left">{{ $data->address }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('branch.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('branch.destroy'))
                                                        @if ($data->id != 1)
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
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($branchs as $data)
                        <form action="{{ route('branch.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Branch / Store') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Branch / Store Name *') }}</label>
                                    <input type="text" class="form-control" name="name" value="{{ $data->name }}" required placeholder="{{ __('e.g. Baitul Mukarram Flagship') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Phone Numbers *') }}</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $data->phone }}" required placeholder="{{ __('e.g. +880-1917-256-756 / +880-1712-445-566') }}">
                                    <small class="text-muted">{{ __('Separate multiple phone numbers with a slash (/). The first number will be used for direct click-to-call.') }}</small>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Opening Hours / Schedule') }}</label>
                                    <input type="text" class="form-control" name="schedule" value="{{ $data->schedule }}" placeholder="{{ __('e.g. Sat – Thu: 10:00 AM – 8:30 PM (Friday Closed)') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Status Badge') }}</label>
                                    <input type="text" class="form-control" name="badge" value="{{ $data->badge ?: 'Open Daily' }}" placeholder="{{ __('e.g. Open Daily, Open Today, Flagship') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Store Highlights & Services') }}</label>
                                    <input type="text" class="form-control" name="highlights" value="{{ $data->highlights }}" placeholder="{{ __('e.g. Live Studio Hands-on Lab & Sensor Cleaning Available') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Google Maps / Directions URL') }}</label>
                                    <input type="text" class="form-control" name="map_link" value="{{ $data->map_link }}" placeholder="{{ __('e.g. https://maps.google.com/?q=Baitul+Mukarram+Dhaka') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Shop & Floor Details') }}</label>
                                    <input type="text" class="form-control" name="shop_name" value="{{ $data->shop_name }}" placeholder="{{ __('e.g. Shop 55/A, Block # 3 Baitul Mukarram East Gate') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Full Address') }}</label>
                                    <input type="text" class="form-control" name="address" value="{{ $data->address }}" placeholder="{{ __('e.g. Shop 55/A, Block # 3 Baitul Mukarram East Gate, Dhaka 1000') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Email (Optional)') }}</label>
                                    <input type="email" class="form-control" name="email" value="{{ $data->email }}" placeholder="{{ __('Enter Email') }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('branch.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Branch') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('branch.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <x-add-modal title="{{ __('Add Branch / Store') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Branch / Store Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('e.g. Baitul Mukarram Flagship') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Phone Numbers *') }}</label>
                <input type="text" class="form-control" name="phone" placeholder="{{ __('e.g. +880-1917-256-756 / +880-1712-445-566') }}" required>
                <small class="text-muted">{{ __('Separate multiple phone numbers with a slash (/). The first number will be used for direct click-to-call.') }}</small>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Opening Hours / Schedule') }}</label>
                <input type="text" class="form-control" name="schedule" placeholder="{{ __('e.g. Sat – Thu: 10:00 AM – 8:30 PM (Friday Closed)') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Status Badge') }}</label>
                <input type="text" class="form-control" name="badge" value="Open Daily" placeholder="{{ __('e.g. Open Daily, Open Today, Flagship') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Store Highlights & Services') }}</label>
                <input type="text" class="form-control" name="highlights" placeholder="{{ __('e.g. Live Studio Hands-on Lab & Sensor Cleaning Available') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Google Maps / Directions URL') }}</label>
                <input type="text" class="form-control" name="map_link" placeholder="{{ __('e.g. https://maps.google.com/?q=Baitul+Mukarram+Dhaka') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Shop & Floor Details') }}</label>
                <input type="text" class="form-control" name="shop_name" placeholder="{{ __('e.g. Shop 55/A, Block # 3 Baitul Mukarram East Gate') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Full Address') }}</label>
                <input type="text" class="form-control" name="address" placeholder="{{ __('e.g. Shop 55/A, Block # 3 Baitul Mukarram East Gate, Dhaka 1000') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Email (Optional)') }}</label>
                <input type="email" class="form-control" name="email" placeholder="{{ __('Enter Email') }}">
            </div>
        </x-add-modal>
    </form>

@endsection
