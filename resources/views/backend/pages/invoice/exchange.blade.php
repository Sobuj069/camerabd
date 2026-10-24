@extends('backend.layouts.master')
@section('page-title', 'Invoice Exchange')

@push('css')
    <style>
        .invoice-contentbar {
            margin: 0 !important;
            padding: 20px;
            margin-bottom: 60px;
        }

        .cart-container {
            padding-top: 5px !important;
        }

        .table-responsive {
            margin-bottom: 4px !important;
        }

        .footerpos {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }

        .footerpos .footerpos_left {
            background-color: #000ce2 !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 12px 10px; 
            border-top-left-radius: 35px;
            border-bottom-left-radius: 35px;
        }

        .footerpos .footerpos_left div {
            font-size: 25px;
            color: #fff;
        }

        .footerpos_right {
            background-color: #00a65a !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 12px 10px;
            border-top-right-radius: 35px;
            border-bottom-right-radius: 35px;
        }

        .footerpos_right div {
            font-size: 25px;
            color: #fff;
            cursor: pointer;
        }

        .productcss {
            border: 1px solid #DDD;
            cursor: pointer;
            padding-bottom: 30px;
            height: 262px;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* ======= DARK MODE OVERRIDES ======= */
        body.dark-theme .rightbar,
        body.dark-theme #containerbar,
        body.dark-theme .invoice-contentbar {
            background-color: #0d1220 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme .invoice-contentbar .card.card_top, 
        body.dark-theme .invoice-contentbar .card {
            background-color: #121829 !important;
            border: 1px solid #1e293b !important;
        }

        body.dark-theme .cart-search-header,
        body.dark-theme .cart-head,
        body.dark-theme .cart-container {
            background: transparent !important;
        }

        body.dark-theme .form-control {
            background-color: #1a2035 !important;
            border-color: #2e3856 !important;
            color: #ffffff !important;
        }

        body.dark-theme .form-control:focus {
            background-color: #1a2035 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        body.dark-theme .table tbody td input.form-control,
        body.dark-theme .table tbody td select.form-control,
        body.dark-theme .table tbody td textarea.form-control {
            color: #f8fafc !important;
            background-color: #1e293b !important;
            border-color: #475569 !important;
        }

        body.dark-theme .table tbody td input.form-control[readonly],
        body.dark-theme .table tbody td input.form-control[disabled],
        body.dark-theme .table tbody td textarea.form-control[readonly],
        body.dark-theme .table tbody td textarea.form-control[disabled] {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #38bdf8 !important;
        }

        body.dark-theme .table tbody td .input-group-text {
            background-color: #334155 !important;
            color: #cbd5e1 !important;
            border-color: #475569 !important;
        }

        body.dark-theme tbody tr {
            background: #2d3748 !important;
            color: #e2e8f0 !important;
        }

        body.dark-theme tbody tr td {
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }

        body.dark-theme .table-striped tbody tr:nth-of-type(odd) {
            background: #263044 !important;
        }

        body.dark-theme .bg-light-primary {
            background-color: #182235 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme .bg-light-primary td {
            color: #f1f5f9 !important;
        }

        body.dark-theme .bg-light-primary .form-control {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #38bdf8 !important;
        }

        body.dark-theme .text-dark {
            color: #f1f5f9 !important;
        }

        body.dark-theme .input-group-prepend .input-group-text {
            background-color: #1e293b !important;
            border-color: #2e3856 !important;
            color: #cbd5e1 !important;
        }

        body.dark-theme .input-group-prepend .barcod_style {
            background-color: #1e293b !important;
            border-color: #2e3856 !important;
            color: #cbd5e1 !important;
        }

        body.dark-theme .table tfoot tr {
            background-color: #121829 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme .table tfoot tr td {
            color: #f1f5f9 !important;
            border-color: #1e293b !important;
        }

        body.dark-theme .footerpos .footerpos_left {
            background-color: #1c2b5c !important;
        }

        body.dark-theme .footerpos_right {
            background-color: #0a693c !important;
        }

        .table tbody tr td,
        .table tbody tr td:first-child,
        .table tbody tr td:last-child,
        .table_data_style_left,
        .table_data_style_right {
            border-radius: 0px !important;
        }
    </style>
@endpush

@section('invoice')
    <div class="invoice-contentbar">
        <div class="row">
            <!-- Start col -->
            <div class="col-md-12">
                <div class="card card_top">
                    <form action="{{ route('invoice.ex.update') }}" id="" method="POST">
                        @csrf
                        <div class="cart-container">
                            <div class="cart-head">
                                <div class="input-group text-dark">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text barcod_style" id="basic-addon1">Selling Date :</span>
                                    </div>
                                    <input type="date" class="form-control" id="" readonly value="{{ $invoice->date }}"
                                        name="date">
                                </div>
                                <hr>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                class="fa fa-barcode"></i></span>
                                    </div>
                                    <input type="text" id="product_search" class="form-control"
                                        placeholder="Type & Barcode" aria-label="Type & Barcode"
                                        onkeydown="return event.keyCode !== 13" autocomplete="off">
                                </div>
                            </div>
                            <div class="cart-head">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center">
                                        <thead class="header_bg text-white">
                                            <tr class="">
                                                <th width="17%" class="header_style_left">Product</th>
                                                <th width="45%">Quantity</th>
                                                <th width="19%">Rate</th>
                                                {{-- <th width="19%">Discount</th> --}}
                                                <th width="19%">Total</th>
                                                <th class="header_style_right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @php
                                                $invoiceItem = App\Models\InvoiceItem::where(
                                                    'invoice_id',
                                                    $invoice->id,
                                                )->get();
                                            @endphp
                                                <input type="hidden" name="id" value="{{ $invoice->id }}">
                                            @forelse ($invoiceItem as $item)
                                                <tr>
                                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                                    <td class="table_data_style_left">
                                                        {{ $item->product?->name }} - {{ $item->product?->barcode }}
                                                        <input type="hidden" value="{{ $item->product?->id }}" name="product_id[]" />
                                                    </td>
                                                    <td style="width:250px">
                                                        <div class="form-row">
                                                            @if ($item->product->unit->related_unit == null)
                                                                {{-- ONLY MAIN UNIT --}}
                                                                <input type="text" class="has_sub_unit" hidden
                                                                    value="false">
                                                                <label class="ml-2 mt-2  mr-2"
                                                                    style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>

                                                                <input type="number" value="{{ $item->main_qty }}"
                                                                    class="form-control col main_qty"
                                                                    name="main_qty[]"
                                                                    data-value="{{ product_stock($item->product) }}"
                                                                    data-related="{{ $item->product->unit->related_value }}"
                                                                    onkeydown="return event.keyCode !== 190" min="1">
                                                                <input type="hidden" value="{{ $item->sub_qty }}"
                                                                    class="form-control col sub_qty mr-1"
                                                                    name="sub_qty[]"
                                                                    onkeydown="return event.keyCode !== 190" min="0"
                                                                    max="{{ $item->product->unit->related_value - 1 }}">
                                                            @else
                                                                {{-- HAS SUB UNIT --}}
                                                                <input type="text" class="has_sub_unit" hidden
                                                                    value="true">
                                                                <input type="text" class="conversion" hidden
                                                                    value="{{ $item->product->unit->related_value }}">

                                                                <label class="mr-1 ml-1"
                                                                    style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>
                                                                <input type="number" value="{{ $item->main_qty }}"
                                                                    class="form-control col main_qty mr-1"
                                                                    name="main_qty[]"
                                                                    data-value="{{ product_stock($item->product) }}"
                                                                    data-related="{{ $item->product->unit->related_value }}"
                                                                    onkeydown="return event.keyCode !== 190" min="0">

                                                                <label class="mr-1"
                                                                    style="padding-top: 5px;">{{ $item->product->unit->related_unit->name }}:</label>
                                                                <input type="number" value="{{ $item->sub_qty }}"
                                                                    class="form-control col sub_qty mr-1"
                                                                    name="sub_qty[]"
                                                                    onkeydown="return event.keyCode !== 190" min="0"
                                                                    max="{{ $item->product->unit->related_value - 1 }}">
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" style="min-width: 100px;"
                                                            class="form-control rate"
                                                            name="rate[]" value="{{ $item->rate }}" />
                                                    </td>
                                                    <td>
                                                        <input type="number" style="min-width: 100px;" readonly
                                                            name="sub_total[]"
                                                            class="form-control sub_total"
                                                            value="{{ $item->subtotal }}" />
                                                    </td>
                                                    <td class="table_data_style_right">
                                                        <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-undo text-danger"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Invoice Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light-primary">
                                                <td colspan="3" class="text-right fw-bold">Total</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="estimated_amount"
                                                        value="0" class="form-control estimated_amount" readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr> 
                                            {{-- Discount --}}
                                            <tr class="bg-light-primary">
                                                <td colspan="3" class="text-right fw-bold">Discount</td>
                                                <td>
                                                    @php
                                                        $ex_discount = $item->invoice->discount;
                                                        if (is_numeric($ex_discount) && ($item->invoice->discount_amount && (str_contains($item->invoice->discount_amount, '%') || !is_numeric($item->invoice->discount_amount)))) {
                                                            $ex_discount = $item->invoice->discount_amount;
                                                        }
                                                        $ex_discount = ($ex_discount == 0.00) ? '0' : $ex_discount;
                                                    @endphp
                                                    <input type="text" class="form-control discount_amount"
                                                        name="discount_amount" placeholder="0%" value="{{ $ex_discount }}">

                                                    <input type="hidden" class="form-control discount"
                                                        name="discount" placeholder="0%">
                                                </td>
                                                <td class="text-right"></td>
                                            <tr class="bg-blue-600 text-white font-black">
                                                <td colspan="3" class="text-right py-3">{{ __('Payable Amount') }}</td>
                                                <td colspan="1" class="text-center">
                                                    <div id="grand_total" class="text-lg">0.00</div>
                                                    <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                        
                                             {{-- Bank Account Selection & Multi-Account Split for Exchange --}}
                                             <tr class="bg-light-primary border-top">
                                                 <td colspan="5" class="p-3 text-left">
                                                     <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                         <div>
                                                             <strong class="text-dark font-size-14">
                                                                 <i class="feather icon-credit-card mr-1 text-primary"></i> {{ __('Exchange Settlement & Bank Accounts') }}
                                                             </strong>
                                                             <small class="text-muted d-block">{{ __('Select single bank account or record exchange payments across multiple bank accounts.') }}</small>
                                                         </div>
                                                         <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                             <label class="btn btn-sm btn-outline-primary {{ !$hasMultipleBanks ? 'active' : '' }}" id="lbl_ex_single_account" style="cursor: pointer; font-weight: 600;">
                                                                 <input type="radio" name="payment_type" id="ex_payment_type_single" value="pos" {{ !$hasMultipleBanks ? 'checked' : '' }}>
                                                                 <i class="feather icon-user mr-1"></i> {{ __('Single Account') }}
                                                             </label>
                                                             <label class="btn btn-sm btn-outline-primary {{ $hasMultipleBanks ? 'active' : '' }}" id="lbl_ex_multi_account" style="cursor: pointer; font-weight: 600;">
                                                                 <input type="radio" name="payment_type" id="ex_payment_type_multi" value="checking" {{ $hasMultipleBanks ? 'checked' : '' }}>
                                                                 <i class="feather icon-layers mr-1"></i> {{ __('Multiple Bank Accounts') }}
                                                             </label>
                                                         </div>
                                                     </div>

                                                     {{-- Single Bank Selection --}}
                                                     <div id="ex_single_bank_wrap" class="mt-2" style="{{ $hasMultipleBanks ? 'display: none;' : '' }}">
                                                         <div class="row align-items-center">
                                                             <div class="col-md-6">
                                                                 <label class="font-weight-bold text-muted small mb-1">{{ __('Select Deposit Bank Account') }}</label>
                                                                 <select name="bank_id" id="ex_bank_id" class="form-control" style="width: 100%;">
                                                                     @foreach($bank_accounts as $ba)
                                                                         <option value="{{ $ba->id }}" {{ ($primaryBankId == $ba->id) ? 'selected' : '' }}>
                                                                             {{ $ba->bank_name }} ({{ $ba->account_number ?? 'Default' }})
                                                                         </option>
                                                                     @endforeach
                                                                 </select>
                                                             </div>
                                                         </div>
                                                     </div>

                                                     {{-- Multiple Bank Accounts Allocation --}}
                                                     <div id="ex_multi_bank_wrap" class="mt-2" style="{{ !$hasMultipleBanks ? 'display: none;' : '' }}">
                                                         <div class="alert alert-info py-2 px-3 small font-weight-bold mb-3" style="background-color: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
                                                             <i class="feather icon-info mr-1"></i> {{ __('Enter amounts collected across individual bank / cash accounts. Total Paid will automatically synchronize.') }}
                                                         </div>
                                                         <div class="row">
                                                             @foreach($bank_accounts as $ba)
                                                                 @php
                                                                     $allocatedVal = $existingBankAllocations[$ba->id] ?? 0;
                                                                 @endphp
                                                                 <div class="col-md-6 mb-2">
                                                                     <div class="p-2 border rounded bg-white shadow-xs d-flex align-items-center justify-content-between" style="border-color: #cbd5e1 !important;">
                                                                         <div class="pr-2">
                                                                             <strong class="d-block text-dark font-size-13">{{ $ba->bank_name }}</strong>
                                                                             <small class="text-muted">{{ $ba->account_number ?? 'General Account' }}</small>
                                                                         </div>
                                                                         <div style="width: 175px;">
                                                                              <div class="input-group input-group-sm">
                                                                                  <div class="input-group-prepend"><span class="input-group-text bg-light font-weight-bold" style="border-color: #cbd5e1;">৳</span></div>
                                                                                  <input type="number" step="any" min="0" 
                                                                                      name="amounts[{{ $ba->id }}]" 
                                                                                      value="{{ (float)$allocatedVal > 0 ? (float)$allocatedVal : 0 }}" 
                                                                                      class="form-control text-right font-weight-bold ex-multi-bank-input" 
                                                                                      data-bank-id="{{ $ba->id }}" 
                                                                                      placeholder="0.00"
                                                                                      style="border-color: #cbd5e1; font-size: 13px;">
                                                                                  <div class="input-group-append">
                                                                                      <button type="button" class="btn btn-outline-primary btn-fill-ex-bank px-2 font-weight-bold" 
                                                                                              data-bank-id="{{ $ba->id }}" 
                                                                                              title="{{ __('Auto-fill remaining balance into this account') }}" 
                                                                                              style="font-size: 11px; border-color: #cbd5e1;">
                                                                                          {{ __('Fill') }}
                                                                                      </button>
                                                                                  </div>
                                                                              </div>
                                                                          </div>
                                                                     </div>
                                                                 </div>
                                                             @endforeach
                                                         </div>
                                                     </div>
                                                 </td>
                                             </tr>

                                             <tr class="bg-light-primary">
                                                 <td colspan="3" class="text-right fw-bold" style="color: #2563eb;">{{ __('Previously Paid on Invoice') }}</td>
                                                 <td colspan="1">
                                                     <input type="text" readonly value="৳ {{ number_format($invoice->total_paid ?? 0, 2) }}" class="form-control text-center font-weight-bold" style="font-size: 14px; color: #1d4ed8; background-color: #eff6ff; border: 1px solid #bfdbfe;">
                                                 </td>
                                                 <td></td>
                                             </tr>

                                             <tr class="bg-light-primary">
                                                 <td colspan="3" class="text-right fw-bold">
                                                     {{ __('Total Paid Amount') }}
                                                     <button type="button" class="btn btn-xs btn-outline-primary ml-2 py-0 px-1" id="ex_btn_pay_full" style="font-size: 11px;">{{ __('Pay Full') }}</button>
                                                     <button type="button" class="btn btn-xs btn-outline-secondary ml-1 py-0 px-1" id="ex_btn_pay_prev" style="font-size: 11px;">{{ __('Keep Previous') }}</button>
                                                 </td>
                                                 <td colspan="1">
                                                     <input type="number" step="any" id="ex_total_paid" name="total_paid"
                                                         value="{{ $invoice->total_paid ?? 0 }}" class="form-control text-center font-weight-bold" style="font-size: 15px;">
                                                 </td>
                                                 <td></td>
                                             </tr>

                                             <tr class="bg-light-primary">
                                                 <td colspan="3" class="text-right fw-bold" style="color: #dc2626;">
                                                     {{ __('Due Amount (If Payable > Paid)') }}
                                                 </td>
                                                 <td colspan="1">
                                                     <input type="number" step="any" id="ex_total_due" name="total_due"
                                                         value="0.00" class="form-control text-center text-danger font-weight-bold" style="font-size: 15px; background-color: #fef2f2; border: 1px solid #fca5a5;" readonly>
                                                 </td>
                                                 <td></td>
                                             </tr>

                                             <tr class="bg-light-primary">
                                                 <td colspan="3" class="text-right fw-bold" style="color: #16a34a;">
                                                     {{ __('Refund / Change (If Paid > Payable)') }}
                                                 </td>
                                                 <td colspan="1">
                                                     <input type="number" step="any" id="ex_change_amount" name="change_amount"
                                                         value="0.00" class="form-control text-center text-success font-weight-bold" style="font-size: 15px; background-color: #f0fdf4; border: 1px solid #86efac;" readonly>
                                                 </td>
                                                 <td></td>
                                             </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <footer class="footerpos mt-1 px-1 flex justify-end">
                                    <button class="w-auto border-0 focus:outline-none transition-all duration-300 hover:scale-[1.03] active:scale-[0.98]">
                                        <div class="footerpos_right text-center !rounded-[5px] px-8 py-2.5 flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all duration-300 bg-emerald-600 hover:bg-emerald-500">
                                            <input type="hidden" name="customer_id" id="" value="{{$item->invoice->customer_id}}">
                                            <i class="feather icon-refresh-cw text-lg"></i>
                                            <span class="text-base font-bold uppercase tracking-wider"> {{ __('Update') }} </span>
                                        </div>
                                    </button>
                                </footer>
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
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };
    </script>
    <script>
        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        $('#product_search').blur();

        var localData = localStorage.getItem('exchng-items') ? JSON.parse(localStorage.getItem('exchng-items')) : [];

        function showList() {
            if (localData.length <= 0) {
                $("#tbody");
            } else {
                localData.forEach((item, index) => {
                    domPrepend(item, index);
                });
            }
        }

        showList();
        estimatedAmount();

        var cartList = [];

        // Helper Functions
        function empty_field_check(placeholder) {
            // console.log(typeof placeholder);
            if (typeof placeholder == NaN) {
                placeholder = 0;
            } else if (placeholder == null) {
                placeholder = 0;
            } else if (placeholder.trim() == "") {
                placeholder = 0;
            } else if (placeholder == 'null') {
                placeholder = 0;
            }
            return placeholder;
        }

        function to_sub_unit(main_val, sub_val, related_by, has_sub_unit) {
            if (has_sub_unit == 'true') {
                return (main_val * related_by) + sub_val;
            }
            return main_val;


        }

        function convert_to_main_and_sub(quantity, has_sub_unit, related_by) {
            var main_qty = 0;
            var main_qty_as_sub = 0;
            var sub_qty = 0;

            main_qty = parseInt(quantity);

            if (has_sub_unit == "true" && quantity != 0 && related_by != 0) {
                main_qty = parseInt(quantity / related_by);
                main_qty_as_sub = main_qty * related_by;
                sub_qty = quantity - main_qty_as_sub;
            }

            return {
                'main_qty': main_qty,
                'sub_qty': sub_qty
            };
        }

        function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount) {
            let sub_unit_price = 0;
            if (has_sub_unit == "true" && related_by != 0) {
                sub_unit_price = parseFloat(unit_price / related_by);
            }
            let main_price = main_qty * unit_price;
            let sub_price = sub_qty * sub_unit_price;
            let total = parseFloat(main_price + sub_price);
            // console.log(discount);
            // discount = total*discount/100;

            let discountAmount = 0;
            if ((typeof discount === 'string' || discount instanceof String) && discount.includes("%")) {
                let removed_percent_discount = discount.replace('%', '');
                discount = parseFloat(removed_percent_discount);
                discountAmount = Math.round(total * (discount / 100));
                console.log(discountAmount);
            } else {
                discountAmount = parseFloat(discount);
            }

            // Apply discount (Assuming discount is a flat amount)
            total -= discountAmount;

            return total.toFixed(2);
        }
        $(document).on('keyup change', '.product_discount', function(e) {
            let discount = $(this).val();
            // let discount = parseFloat($(this).val());
            // console.log(discount);
            let row = $(this).closest('tr');
            let main_qty = parseInt(row.find('.main_qty').val()) || 0;
            let sub_qty = parseInt(row.find('.sub_qty').val()) || 0;
            let unit_price = parseFloat(row.find('.rate').val()) || 0;
            let related_by = parseInt(row.find('.main_qty').attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            let sub_total = calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount);
            row.find('.sub_total').val(sub_total);
            estimatedAmount();
        });

        // Manage Addition and Removal from LocalStorage
        function pExist(pid) {
            let ldata = localStorage.getItem('exchng-items') ? JSON.parse(localStorage.getItem('exchng-items')) : [];
            return ldata.some(function(el) {
                return el.product.id === pid
            });
        }

        function storedata(data) {
            if (localStorage.getItem('exchng-items') != null) {
                cartList = JSON.parse(localStorage.getItem('exchng-items'))
                cartList.push(data);
            } else {
                cartList.push(data);
            }
            localStorage.setItem('exchng-items', JSON.stringify(cartList));
        }

        function addProductToCard(data) {
            storedata(data);
            var x = 0;
            domPrepend(data, x++);
            estimatedAmount();
        }

        // Search Product
        $("#product_search").autocomplete({
            source: function(req, res) {
                let url = "{{ route('product-search') }}";
                $.get(url, {
                    req: req.term
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            value: item.name + " " + item.barcode,
                            price: item.selling_price
                        }
                    })); // end res

                });
            },
            select: function(event, ui) {

                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
                    // console.log(product);
                    // check stock
                    if (data.stock_qty <= 0) {
                        iziToast.warning({
                            title: "This product is Stock out. Please Purchases the Product.",
                            position: "topRight",
                        });
                        return false;
                    }


                    if (pExist(data.product.id) == true) {
                        iziToast.warning({
                            title: "Please Increase the quantity.",
                            position: "topRight",
                        });
                    } else {
                        addProductToCard(data);
                    }
                });

                $(this).val('');

                return false;
            },
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');

                }
            },
            minLength: 0
        });

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                // check stock
                if (data.stock_qty <= 0) {
                    iziToast.warning({
                        title: "This product is Stock out. Please Purchases the Product.",
                        position: "topRight",
                    });
                    return false;
                }

                if (pExist(data.product.id) == true) {
                    iziToast.warning({
                        title: "Please Increase the quantity.",
                        position: "topRight",
                    });
                } else {
                    addProductToCard(data);
                }
            }); // Load Data to cart

        });


        $(document).on('click', '.remove-btn', function() {
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.removeItem('exchng-items');
            localStorage.setItem('exchng-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
        });

        $("#clearList").on('click', function() {
            localStorage.removeItem('exchng-items');
            $("#tbody").html(empty);
            estimatedAmount();
        });



        function domPrepend(data = null, index = null) {
            var name = data.product.name;
            var quantity_data = '';
            // console.log(data);
            if (data.product.unit.related_unit == null) {
                // alert("NO SUB UNIT");
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="false">
                        <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                        <input type="number" value="1" class="form-control col main_qty" name="main_qty[]" 
                        data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                        onkeydown="return event.keyCode !== 190" min="0">

                        <input type="hidden" value="0" class="form-control col sub_qty mr-1" name="sub_qty[]"  
                        min="0" max="">
                        `;
            } else {
                // alert("SUB UNIT");
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="true">
                        <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">

                        <label class="mr-1 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                        <input type="number" value="1" class="form-control col main_qty mr-1" name="main_qty[]" 
                        data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                        onkeydown="return event.keyCode !== 190" min="0">

                        <label class="mr-1" style="padding-top: 5px;">${data.product.unit.related_unit.name}:</label>
                        <input type="number" value="0" class="form-control col sub_qty mr-1" name="sub_qty[]"  
                        onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value-1}">`;
            }

            let dom = `
                <tr id="tbody_tr">
                    <td class="table_data_style_left">
                    ${data.product.name + " - " + data.product.barcode}
                    
                    <input type="hidden" value="${data.product.id}" name="product_id[]" />
                    </td>
                    <td>
                        <div class="form-row" style="min-width: 100px;">
                            ${quantity_data}
                        </div>
                    </td>
                    <td>
                    <input type="number" style="min-width: 100px;" value="${data.product.selling_price}" class="form-control rate" name="rate[]" />
                    </td>
                    
                    <td>
                    <input type="number" style="min-width: 100px;" readonly name="sub_total[]" class="form-control sub_total" value="${data.product.selling_price}"/>
                    </td>
                    <td class="table_data_style_right">
                    <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
            $("#tbody").prepend(dom);
        }

        function handle_change(obj) {
            var main_val = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val()));
            var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
            var pro_discount = parseInt(empty_field_check(obj.parents('tr').find('.product_discount').val()));
            let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related')));
            var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
            let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);
            let stock = obj.parents('tr').find('.main_qty').attr('data-value');

            // alert(converted_sub);

            if (stock < converted_sub) {
                // put the max stock
                var converted;
                if (has_sub_unit == "true") {
                    converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                    obj.parents('tr').find('.main_qty').val(converted.main_qty);
                    obj.parents('tr').find('.sub_qty').val(converted.sub_qty);
                } else {
                    converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                    obj.parents('tr').find('.main_qty').val(converted.main_qty);
                }

                let price = obj.parents('tr').find('.rate').val();
                price = parseFloat(price);

                let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit, pro_discount);

                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();

                iziToast.warning({
                    title: "Not Enough Stock.",
                    position: "topRight",
                });
            } else {
                let price = obj.parents('tr').find('.rate').val();
                price = parseFloat(price);
                let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit, pro_discount);
                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();
            }
        }

        // main_qty
        $(document).on('keyup change', '.main_qty', function(e) {
            handle_change($(this));
        });

        //sub_qty change
        $(document).on('keyup change', '.sub_qty', function(e) {
            handle_change($(this));
        });

        // rate change
        $(document).on('keyup change', '.rate', function(e) {
            handle_change($(this));
            return;
        });

        //estimatedAmount function
        function estimatedAmount() {
            var sum = 0;

            $(".sub_total").each(function() {
                var value = $(this).val();
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });
            $("input[name='estimated_amount']").val(sum);
            totalCalculate();
        }

        // Other Calculations - discount
        $(document).on(
            "keyup change",
            "input[name='discount_amount']",
            function() {
                totalCalculate();
            }
        );


        function totalCalculate() {
            let discount = $(".discount_amount").val();
            let estimated_amount = parseFloat(
                $("input[name='estimated_amount']").val()
            );

            discount = empty_field_check(discount);


            let discountAmount = 0;
            if ((typeof discount === 'string' || discount instanceof String) && discount.includes("%")) {
                let removed_percent_discount = discount.replace('%', '');
                discount = parseFloat(removed_percent_discount);
                discountAmount = Math.round($(".estimated_amount").val() * (discount / 100));
            } else {
                discountAmount = parseFloat(discount);
            }

            let total_amount = estimated_amount - discountAmount;

            $("#grand_total").text(total_amount.toFixed(2));
            $(".sub_total").text(estimated_amount.toFixed(2));
            $(".discount_amount").text(discountAmount.toFixed(2));
            $(".discount").val(discountAmount.toFixed(2));
            $(".payable_amount").text(total_amount.toFixed(2));
            let pay = $("#payable_amount").val(total_amount.toFixed(2));
            recalcExchangePayment();
        }

        // Recalculate exchange payment settlements
        function recalcExchangePayment() {
            let payable = parseFloat($("#payable_amount").val()) || 0;
            let paid = parseFloat($("#ex_total_paid").val()) || 0;
            let due = payable - paid;
            let change = paid - payable;
            
            if (due < 0) due = 0;
            if (change < 0) change = 0;
            
            $("#ex_total_due").val(due.toFixed(2));
            $("#ex_change_amount").val(change.toFixed(2));
        }

        $(document).on("change", "input[name=\"payment_type\"]", function() {
            let pType = $(this).val();
            if (pType === "checking") {
                $("#ex_single_bank_wrap").slideUp(200);
                $("#ex_multi_bank_wrap").slideDown(200);
                $("#lbl_ex_single_account").removeClass("active");
                $("#lbl_ex_multi_account").addClass("active");
                
                let currentSum = 0;
                $(".ex-multi-bank-input").each(function() { currentSum += parseFloat($(this).val()) || 0; });
                if (currentSum === 0) {
                    let totalPaid = parseFloat($("#ex_total_paid").val()) || 0;
                    if (totalPaid > 0) {
                        let primaryId = $("#ex_bank_id").val() || "1";
                        let $targetInput = $(".ex-multi-bank-input[data-bank-id=\"" + primaryId + "\"]");
                        if ($targetInput.length) {
                            $targetInput.val(totalPaid.toFixed(2));
                        } else {
                            $(".ex-multi-bank-input").first().val(totalPaid.toFixed(2));
                        }
                    }
                }
            } else {
                $("#ex_multi_bank_wrap").slideUp(200);
                $("#ex_single_bank_wrap").slideDown(200);
                $("#lbl_ex_multi_account").removeClass("active");
                $("#lbl_ex_single_account").addClass("active");
            }
        });

        // Helper: Auto-adjust exchange multi-bank inputs
        function autoAdjustExchangeMultiBankInputs($activeInput, inputClass, totalBillSelector) {
            let grandTotal = parseFloat($(totalBillSelector).val() || $(totalBillSelector).text()) || 0;
            if (grandTotal < 0) grandTotal = 0;

            let activeVal = parseFloat($activeInput.val());
            if (isNaN(activeVal) || activeVal < 0) {
                activeVal = 0;
            }

            if (activeVal > grandTotal && grandTotal > 0) {
                activeVal = grandTotal;
                $activeInput.val(activeVal.toFixed(2));
            }

            let otherSum = 0;
            let otherInputs = [];
            $(inputClass).not($activeInput).each(function() {
                let v = parseFloat($(this).val()) || 0;
                if (v > 0) {
                    otherInputs.push({ $el: $(this), val: v });
                    otherSum += v;
                }
            });

            let totalCombined = activeVal + otherSum;
            let excess = totalCombined - grandTotal;

            if (excess > 0.001 && otherInputs.length > 0) {
                for (let i = 0; i < otherInputs.length; i++) {
                    let item = otherInputs[i];
                    let deduct = Math.min(item.val, excess);
                    let newVal = item.val - deduct;
                    item.$el.val(newVal > 0.001 ? newVal.toFixed(2) : "0");
                    excess -= deduct;
                    if (excess <= 0.001) break;
                }
            }

            let finalSum = 0;
            $(inputClass).each(function() {
                finalSum += parseFloat($(this).val()) || 0;
            });

            return finalSum;
        }

        $(document).on("input keyup change", ".ex-multi-bank-input", function() {
            let sum = autoAdjustExchangeMultiBankInputs($(this), ".ex-multi-bank-input", "#payable_amount");
            $("#ex_total_paid").val(sum.toFixed(2));
            recalcExchangePayment();
        });

        $(document).on("click", ".btn-fill-ex-bank", function(e) {
            e.preventDefault();
            let bankId = $(this).data("bank-id");
            let $target = $(".ex-multi-bank-input[data-bank-id=\"" + bankId + "\"]");
            let grandTotal = parseFloat($("#payable_amount").val()) || 0;
            
            let otherSum = 0;
            $(".ex-multi-bank-input").not($target).each(function() {
                otherSum += parseFloat($(this).val()) || 0;
            });
            
            let remaining = grandTotal - otherSum;
            if (remaining < 0) remaining = 0;
            
            $target.val(remaining.toFixed(2));
            $target.trigger("input");
        });

        $(document).on("input keyup change", "#ex_total_paid", function() {
            recalcExchangePayment();
        });

        $(document).on("click", "#ex_btn_pay_full", function() {
            let payable = parseFloat($("#payable_amount").val()) || 0;
            $("#ex_total_paid").val(payable.toFixed(2));
            let isChecking = $("input[name=\"payment_type\"]:checked").val() === "checking";
            if (isChecking) {
                $(".ex-multi-bank-input").val("0.00");
                let primaryId = $("#ex_bank_id").val() || "1";
                let $targetInput = $(".ex-multi-bank-input[data-bank-id=\"" + primaryId + "\"]");
                if ($targetInput.length) {
                    $targetInput.val(payable.toFixed(2));
                } else {
                    $(".ex-multi-bank-input").first().val(payable.toFixed(2));
                }
            }
            recalcExchangePayment();
        });

        $(document).on("click", "#ex_btn_pay_prev", function() {
            let initialPaid = {{ (float)($invoice->total_paid ?? 0) }};
            $("#ex_total_paid").val(initialPaid.toFixed(2));
            let isChecking = $("input[name=\"payment_type\"]:checked").val() === "checking";
            if (isChecking) {
                $(".ex-multi-bank-input").val("0.00");
                let primaryId = $("#ex_bank_id").val() || "1";
                let $targetInput = $(".ex-multi-bank-input[data-bank-id=\"" + primaryId + "\"]");
                if ($targetInput.length) {
                    $targetInput.val(initialPaid.toFixed(2));
                } else {
                    $(".ex-multi-bank-input").first().val(initialPaid.toFixed(2));
                }
            }
            recalcExchangePayment();
        });

        // ===================order modal===================
        //payment_modal_btn
        $("#payment_modal_btn").on("click", function() {
            //date
            var date = $("#date").val();
            if (date == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a date.",
                    position: "topRight",
                });
                return false;
            }
            //customer_id
            var customer_id = $("#customer_id").val();
            if (customer_id == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a customer.",
                    position: "topRight",
                });
                return false;
            }
            //order_modal_obj
            if ($.trim($('.name').val()) == '') {
                iziToast.warning({
                    title: "Please select at least one product",
                    position: "topRight",
                });
                return false;
            }
            // count of tr in cart_list table
            var count = $("#tbody").find("tr#tbody_tr").length;
            $(".total_item").text(count);
            //get customer name from id="customer_id"
            var customer_name = $("#customer_id").find("option:selected").text();
            $("#payment_modal").find("#customer_name").text(customer_name);
            var customer_id = $("#customer_id").val();
            $("#payment_modal").find("input[name=customer_id]").val(customer_id);
            //show payment_modal
            $("#payment_modal").modal("show");
        });

        //.full_pay_btn
        $(".full_pay_btn").on("click", function() {
            var payable_amount = $("#grand_total").text();
            payable_amount = parseFloat(payable_amount);
            payable_amount = payable_amount.toFixed(2);

            $(".pay_amount").val(payable_amount);
            $("#paid_amount").val(payable_amount);
            $(".paid_amount").text(payable_amount);
            $("#due_amount").val('0.00');
            $(".due_amount").text('0.00');
        });

        //.full_due_btn
        $(".full_due_btn").on("click", function() {
            var payable_amount = $("#grand_total").text();
            payable_amount = parseFloat(payable_amount);
            payable_amount = payable_amount.toFixed(2);

            $(".pay_amount").val('0.00');
            $("#due_amount").val(payable_amount);
            $(".due_amount").text(payable_amount);
            $("#paid_amount").val('0.00');
            $(".paid_amount").text('0.00');
        });

        // Product Search
        $(document).on("change", "#getProductsByCat", function() {
            var cat_id = $(this).val();
            $.ajax({
                url: "{{ route('posProducts') }}",
                type: "GET",
                data: {
                    cat_id: cat_id
                },
                success: function(data) {
                    $("#products").html(data);
                    // console.log(data)
                },
                error: function() {
                    alert('Error !');
                }
            });
        });

        $("#checkout").on("click", function() {
            localStorage.clear();
        });
    </script>
@endpush
