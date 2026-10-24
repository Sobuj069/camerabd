@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('WooCommerce Order Report'))

@push('css')
    <style>
        @media print {
            .no-print, #h-hide, .main-header, .sidebar-wrapper, nav, .btn, form {
                display: none !important;
            }
            body {
                background: #fff !important;
                color: #000 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .table-bordered th, .table-bordered td {
                border: 1px solid #000 !important;
                color: #000 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <!-- Header & Action Buttons -->
            <div class="d-flex align-items-center justify-between flex-wrap mb-3" id="h-hide">
                <div>
                    <h4 class="font-weight-bold text-dark mb-1">
                        <i class="fa fa-shopping-cart text-purple me-2"></i> {{ __('WooCommerce Order & Stock Mode Report') }}
                    </h4>
                    <p class="text-muted small mb-0">
                        {{ __('Product-wise sales breakdown transferred with Manage Stock vs Without Manage Stock & Returns') }}
                    </p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fa fa-print me-1"></i> {{ __('Print Report') }}
                    </button>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row mb-4" id="h-hide">
                <div class="col-md col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3" style="border-left: 4px solid #8b5cf6 !important;">
                        <div class="d-flex align-items-center justify-between">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold d-block">{{ __('Total Web Orders') }}</span>
                                <h3 class="font-weight-extrabold text-dark mb-0 mt-1">{{ number_format($totalOrdersCount) }}</h3>
                            </div>
                            <div class="p-3 bg-purple-light rounded-circle text-purple">
                                <i class="fa fa-shopping-bag fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3" style="border-left: 4px solid #10b981 !important;">
                        <div class="d-flex align-items-center justify-between">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold d-block">{{ __('Total Sold Qty') }}</span>
                                <h3 class="font-weight-extrabold text-success mb-0 mt-1">{{ number_format($totalSoldQty) }} Pcs</h3>
                            </div>
                            <div class="p-3 bg-emerald-light rounded-circle text-success">
                                <i class="fa fa-boxes fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3" style="border-left: 4px solid #3b82f6 !important;">
                        <div class="d-flex align-items-center justify-between">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold d-block">{{ __('With Manage Stock') }}</span>
                                <h3 class="font-weight-extrabold text-primary mb-0 mt-1">{{ number_format($totalWithManageQty) }} Pcs</h3>
                            </div>
                            <div class="p-3 bg-blue-light rounded-circle text-primary">
                                <i class="fa fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3" style="border-left: 4px solid #f59e0b !important;">
                        <div class="d-flex align-items-center justify-between">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold d-block">{{ __('Without Manage Stock') }}</span>
                                <h3 class="font-weight-extrabold text-warning mb-0 mt-1">{{ number_format($totalWithoutManageQty) }} Pcs</h3>
                            </div>
                            <div class="p-3 bg-amber-light rounded-circle text-warning">
                                <i class="fa fa-flash fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md col-sm-6 mb-2">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3" style="border-left: 4px solid #ef4444 !important;">
                        <div class="d-flex align-items-center justify-between">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold d-block">{{ __('Returned Qty') }}</span>
                                <h3 class="font-weight-extrabold text-danger mb-0 mt-1">{{ number_format($totalReturnedQty) }} Pcs</h3>
                            </div>
                            <div class="p-3 bg-rose-light rounded-circle text-danger">
                                <i class="fa fa-rotate-ccw fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Date Filter Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4" id="h-hide">
                <div class="card-body p-3">
                    <form action="{{ route('report.woocommerce') }}" method="GET">
                        <div class="form-row align-items-end">
                            <div class="col-md-3 col-12 mb-2">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Search Product / Code') }}</label>
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Product Name or Barcode...') }}" value="{{ request('search') }}">
                            </div>

                            <div class="col-md-3 col-12 mb-2">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-3 col-12 mb-2">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('End Date') }}</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>

                            <div class="col-md-3 col-12 mb-2 d-flex gap-2">
                                <button type="submit" class="btn btn-purple font-weight-bold flex-fill" style="background-color: #8b5cf6; color: #fff;">
                                    <i class="fa fa-filter me-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.woocommerce') }}" class="btn btn-outline-secondary font-weight-bold flex-fill">
                                    <i class="fa fa-refresh me-1"></i> {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Table Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered mb-0 align-middle">
                            <thead class="bg-light text-dark font-weight-bold">
                                <tr style="font-size: 13px;">
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>{{ __('Product Details') }}</th>
                                    <th class="text-center" style="width: 130px;">{{ __('Available Stock') }}</th>
                                    <th class="text-center" style="width: 110px;">{{ __('Total Orders') }}</th>
                                    <th class="text-center" style="width: 110px;">{{ __('Total Sold Qty') }}</th>
                                    <th class="text-center bg-success text-white" style="width: 150px;">
                                        <i class="fa fa-check-circle me-1"></i> {{ __('With Manage Stock') }}
                                    </th>
                                    <th class="text-center bg-warning text-dark" style="width: 160px;">
                                        <i class="fa fa-bolt me-1"></i> {{ __('Without Manage Stock') }}
                                    </th>
                                    <th class="text-center bg-danger text-white" style="width: 140px;">
                                        <i class="fa fa-rotate-ccw me-1"></i> {{ __('Returned Qty') }}
                                    </th>
                                    <th class="text-center bg-secondary text-white" style="width: 140px;">
                                        <i class="fa fa-clock-o me-1"></i> {{ __('Pending Orders') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productStats as $pid => $stat)
                                    @php
                                        $product = $stat['product'];
                                        $stockClass = $stat['current_stock'] <= 0 ? 'bg-danger text-white' : 'bg-info text-white';
                                    @endphp
                                    <tr style="font-size: 13px;">
                                        <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-extrabold text-dark" style="font-size: 14px;">
                                                {{ $product->name }}
                                            </div>
                                            <div class="text-muted small mt-0.5">
                                                <span class="badge bg-light text-dark border font-weight-bold me-2">Code: {{ $product->barcode ?? 'N/A' }}</span>
                                                @if($product->brand)
                                                    <span class="text-secondary font-weight-bold">Brand: {{ $product->brand->name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            <span class="badge {{ $stockClass }} px-2.5 py-1" style="font-size: 12px;">
                                                {{ number_format($stat['current_stock'], 0) }} Pcs
                                            </span>
                                        </td>
                                        <td class="text-center font-weight-extrabold text-dark" style="font-size: 14px;">
                                            {{ number_format($stat['total_orders']) }} {{ __('Orders') }}
                                        </td>
                                        <td class="text-center font-weight-extrabold text-purple" style="font-size: 14px; color: #8b5cf6;">
                                            {{ number_format($stat['total_qty']) }} Pcs
                                        </td>
                                        <td class="text-center font-weight-extrabold text-success" style="font-size: 14px; background-color: #f0fdf4;">
                                            @if($stat['with_manage_qty'] > 0)
                                                <span class="badge bg-success text-white px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                                                    {{ number_format($stat['with_manage_qty']) }} Pcs
                                                </span>
                                            @else
                                                <span class="text-muted">0 Pcs</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-extrabold text-warning" style="font-size: 14px; background-color: #fffbeb;">
                                            @if($stat['without_manage_qty'] > 0)
                                                <span class="badge bg-warning text-dark px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                                                    {{ number_format($stat['without_manage_qty']) }} Pcs
                                                </span>
                                            @else
                                                <span class="text-muted">0 Pcs</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-extrabold text-danger" style="font-size: 14px; background-color: #fef2f2;">
                                            @if($stat['return_qty'] > 0)
                                                <span class="badge bg-danger text-white px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                                                    {{ number_format($stat['return_qty']) }} Pcs
                                                </span>
                                            @else
                                                <span class="text-muted">0 Pcs</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-extrabold text-secondary" style="font-size: 14px; background-color: #f8fafc;">
                                            @if($stat['untransferred_qty'] > 0)
                                                <span class="badge bg-secondary text-white px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                                                    {{ number_format($stat['untransferred_qty']) }} Pcs
                                                </span>
                                            @else
                                                <span class="text-muted">0 Pcs</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted font-weight-bold">
                                            <i class="fa fa-info-circle me-1"></i> {{ __('No WooCommerce product sales records found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($productStats) > 0)
                                <tfoot class="bg-light font-weight-bold" style="font-size: 13px;">
                                    <tr>
                                        <td colspan="3" class="text-end font-weight-black uppercase">{{ __('Total') }}:</td>
                                        <td class="text-center font-weight-black">{{ number_format($totalOrdersCount) }} Orders</td>
                                        <td class="text-center font-weight-black text-purple" style="color: #8b5cf6;">{{ number_format($totalSoldQty) }} Pcs</td>
                                        <td class="text-center font-weight-black text-success">{{ number_format($totalWithManageQty) }} Pcs</td>
                                        <td class="text-center font-weight-black text-warning">{{ number_format($totalWithoutManageQty) }} Pcs</td>
                                        <td class="text-center font-weight-black text-danger">{{ number_format($totalReturnedQty) }} Pcs</td>
                                        <td class="text-center font-weight-black text-secondary">{{ number_format($totalUntransferredQty) }} Pcs</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
