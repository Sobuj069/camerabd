@extends('backend.layouts.master')
@section('section-title', __('Role Management'))
@section('page-title', __('Update Role & Permission'))
@section('action-button')
    <a href="{{ route('roles-permission.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Role & Permission List') }}
    </a>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form class="needs-validation" action="{{ route('roles-permission.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-3"></div>
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom01" class="form-label font-weight-bold">
                                    {{ __('Role Name *') }}
                                </label>
                                <input type="text" class="form-control" id="validationCustom01"
                                    placeholder="{{ __('Enter Role Name') }}" name="name" required value="{{ $role->name }}">
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="card" style="border: 1px solid #000ce2;">
                            <div class="p-2" style="background: #000ce2;color:white">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h4 class="card-title text-white mb-0">{{ __('Permission') }}</h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input class="custom-control-input" type="checkbox" id="select_all_permissions">
                                            <label class="custom-control-label font-weight-bold text-white cursor-pointer" for="select_all_permissions">
                                                {{ __('Select All Permissions') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $sortedRouteList = collect($routeList)->sortByDesc(function ($permissions) {
                                            return count($permissions);
                                        });
                                    @endphp
                                    @foreach ($sortedRouteList as $key => $value)
                                        <div class="col-md-3 mb-4">
                                            <div class="card card_top h-100" style="border: 1px solid #000ce2;">
                                                <div style="background: #292d75ff;color:white;" class="p-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input text-white cursor-pointer" style="background: white;color:white;border:white;" type="checkbox"
                                                            id="{{ $key }}"
                                                            onclick="$('.{{ $key }}').prop('checked', this.checked);">
                                                        <label class="custom-control-label font-weight-bold text-white cursor-pointer"
                                                            for="{{ $key }}">
                                                            {{ str_replace('-', ' ', str_replace('_', ' ', ucfirst($key))) }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @foreach ($routeList[$key] as $item => $val)
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input {{ $key }} cursor-pointer permission_checkbox"
                                                                type="checkbox" id="{{ $key }}{{ $item }}"
                                                                name="permission[{{ $key }}][]"
                                                                value="{{ $item }}" {{ $val == 1 ? 'checked' : '' }}>
                                                            <label class="custom-control-label font-weight-bold cursor-pointer"
                                                                for="{{ $key }}{{ $item }}">
                                                                {{ str_replace('-', ' ', str_replace('_', ' ', ucfirst($item))) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer mt-3">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn save_btn">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Select All functionality
            $('#select_all_permissions').on('change', function() {
                $('.permission_checkbox, .custom-control-input').prop('checked', this.checked);
            });

            // Update individual group checkboxes based on their children
            $('.permission_checkbox').on('change', function() {
                var groupClass = $(this).attr('class').split(' ')[1];
                var allChecked = $('.' + groupClass + ':checked').length === $('.' + groupClass).length;
                $('#' + groupClass).prop('checked', allChecked);
            });
        });
    </script>
@endpush
