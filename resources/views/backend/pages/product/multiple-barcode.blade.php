@extends('backend.layouts.master')
@section('section-title', 'Product')
@section('page-title', 'Product List')
@if (check_permission('product.create'))
    @section('action-button')
        <a href="{{ route('product.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            Add Product
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {
            @page {
                size: auto;
            }

            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                font-family: Roboto, sans-serif;
            }

            .print_area {
                position: absolute;
                top: 0;
                width: 100%;
            }

            .print_area * {
                visibility: visible !important;
            }
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Column Width Fixes */
        .sl-col { width: 60px !important; }
        .products-col { text-align: left !important; padding-left: 15px !important; }
        .variations-col { width: 150px !important; }
        .qty-col { width: 130px !important; }
        .action-col { width: 60px !important; }

        .table td, .table th {
            vertical-align: middle !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <h2 class="pb-2 text-slate-800 dark:text-slate-100">{{ __('Multiple Barcode Print') }}</h2>
                    <div class="form-row">  
                        {{-- Product --}}
                        <div class="mb-1 col-md-12 text-center">
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text barcod_style" id="basic-addon1"><i
                                            class="fa fa-barcode"></i></span>
                                </div>
                                <input type="text" id="product_search" class="form-control" placeholder="Type & Barcode"
                                    aria-label="Type & Barcode" onkeydown="return event.keyCode !== 13" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsives mt-1">
                        <form action="{{ route('multiple.barcode-print') }}" method="POST" target="_blank">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="responsive-table-wrapper responsive-table">
                                        <table class="table table-bordered text-center responsive-table">
                                            <thead>
                                                <tr style="background: #000ce2; color: white;">
                                                    <th class="sl-col">#SL</th>
                                                    <th class="products-col">{{ __('Product') }}</th>
                                                    <th class="variations-col">{{ __('Barcode') }}</th>
                                                    @if (env('APP_SC') == 'yes')
                                                    <th class="variations-col">{{ __('Variation') }}</th>
                                                    @endif
                                                    <th class="qty-col">{{ __('Quantity') }}</th>
                                                    <th class="action-col">
                                                        <i class="fa fa-trash"></i>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 text-center" style="padding-bottom: 15px; border-bottom: 1px solid #eee;">
                                    <h6 class="mb-3">{{ __('Barcode Settings') }}</h6>
                                    <label class="mr-3"><input type="checkbox" name="b_company" checked> {{ __('Company') }}</label>
                                    <label class="mr-3"><input type="checkbox" name="b_category" checked> {{ __('Category') }}</label>
                                    <label class="mr-3"><input type="checkbox" name="b_name" checked> {{ __('Name') }}</label>
                                    <label class="mr-3"><input type="checkbox" name="b_variation" checked> {{ __('Variation') }}</label>
                                    <label><input type="checkbox" name="b_price" checked> {{ __('Price') }}</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn save_btn">
                                    <i class="mr-2 feather icon-printer"></i> {{ __('Print Barcodes') }}
                                </button> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bar_code_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="barcode-page">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success delete" onclick="print_barcode()"><i
                            class="fa fa-print"></i>
                        Print</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };
    </script>
    <script>
        $(document).ready(function() {
            let count = 0;
            let db_pid_array = []; // Track added product IDs to prevent duplication

            // Function to toggle the visibility of the Submit and Reset buttons
            function toggleSubmitAndResetButtons() {
                if ($("#table_body tr").length > 0) {
                    $(".submitAndReset").show();
                } else {
                    $(".submitAndReset").hide();
                }
            }

            // Autocomplete for product search
            $("#product_search").autocomplete({
                source: function(req, res) {
                    let url = "{{ route('product-search') }}";
                    $.get(url, {
                        req: req.term
                    }, (data) => {
                        res(
                            $.map(data, (item) => {
                                return {
                                    id: item.id,
                                    value: item.name + " - " + item.barcode,
                                    price: item.selling_price,
                                };
                            })
                        );
                    });
                },
                select: function(event, ui) {
                    if ($("#supplier").val() === "") {
                        iziToast.warning({
                            title: "Please Select Supplier First!",
                            position: "topRight",
                        });
                        return false;
                    }

                    if (db_pid_array.includes(ui.item.id)) {
                        iziToast.warning({
                            title: "Product already added.",
                            position: "topRight",
                        });
                        return false;
                    }

                    $(this).val(ui.item.value);
                    let url = "{{ route('sc-search-product-id', 'my_id') }}".replace(
                        "my_id",
                        ui.item.id
                    );
                    $.get(url, (data) => {
                        // Iterate over variations
                        if (data.variations && data.variations.length > 0) {
                            data.variations.forEach((variation) => {
                                count++;

                                let row = `
                                    <tr>
                                        <td class="align-middle">${count}</td>
                                        <td class="align-middle text-left pl-3">
                                            ${data.product.name}
                                            <input type="hidden" name="products[]" value="${data.product.id}">
                                        </td>
                                        <td class="align-middle font-weight-bold">
                                            ${data.product.barcode || ''}
                                        </td>
                                        @if (env('APP_SC') == 'yes')
                                        <td class="align-middle">
                                            ${variation.size || ''}${variation.color ? ' - ' + variation.color : ''}
                                            <input type="hidden" value="${variation.id}" name="variation[]">
                                        </td>
                                        @else
                                        <input type="hidden" value="${variation.id}" name="variation[]">
                                        @endif
                                        <td class="align-middle">
                                            <input type="number" value="1" class="form-control text-center mx-auto qty" style="max-width: 100px;" name="qty[]" onkeydown="return event.keyCode !== 190" min="1">
                                        </td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-danger btn-sm remove shadow-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                                $("#table_body").append(row);
                            });
                        } else {
                            count++;
                            let row = `
                                <tr>
                                    <td class="align-middle">${count}</td>
                                    <td class="align-middle text-left pl-3">
                                        ${data.product.name} 
                                        <input type="hidden" name="products[]" value="${data.product.id}">
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        ${data.product.barcode || ''}
                                    </td>
                                    @if (env('APP_SC') == 'yes')
                                    <td class="align-middle text-muted">
                                        No Variation
                                        <input type="hidden" value="" name="variation[]">
                                    </td>
                                    @else
                                    <input type="hidden" value="" name="variation[]">
                                    @endif
                                    <td class="align-middle">
                                        <input type="number" value="1" class="form-control text-center mx-auto qty" style="max-width: 100px;" name="qty[]" onkeydown="return event.keyCode !== 190" min="1">
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-danger btn-sm remove shadow-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                            $("#table_body").append(row);
                        }

                        db_pid_array.push(ui.item.id);
                        toggleSubmitAndResetButtons(); // Show buttons after adding a product
                    });

                    $(this).val("");
                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length === 1) {
                        ui.item = ui.content[0];
                        $(this).data("ui-autocomplete")._trigger("select", "autocompleteselect", ui);
                        $(this).autocomplete("close");
                    }
                },
                minLength: 0,
            });

            // Remove row from the table
            $(document).on("click", ".remove", function() {
                let row = $(this).closest("tr");
                let productId = row.find("input[name='products[]']").val();
                row.remove();

                // Remove product ID from array if no other rows exist for it
                if ($(`input[name='products[]'][value="${productId}"]`).length === 0) {
                    db_pid_array = db_pid_array.filter((id) => parseInt(id) !== parseInt(productId));
                }

                toggleSubmitAndResetButtons(); // Hide buttons if table is empty
                updateGrandTotal(); // Recalculate grand total after removing a row
            });


            // Update subtotal and Grand Total on quantity or rate change
            $(document).on("input", ".main_qty, .rate", function() {
                let row = $(this).closest("tr");
                let qty = parseFloat(row.find(".main_qty").val()) || 0;
                let rate = parseFloat(row.find(".rate").val()) || 0;
                let subtotal = qty * rate;

                row.find(".sub_total").text(subtotal.toFixed(2) +
                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}");
                row.find(".subtotal_input").val(subtotal.toFixed(2));

                updateGrandTotal();
            });

            $(document).on("input", ".main_qty, .sub_qty, .rate", function() {
                let row = $(this).closest("tr");
                let mainQty = parseFloat(row.find(".main_qty").val()) || 0;
                let subQty = parseFloat(row.find(".sub_qty").val()) || 0;
                let rate = parseFloat(row.find(".rate").val()) || 0;
                let hasSubUnit = row.find(".has_sub_unit").val() === "true";
                let conversion = parseFloat(row.find(".conversion").val()) || 1;

                let totalQty = mainQty;
                if (hasSubUnit) {
                    totalQty += subQty / conversion; // Convert sub_qty to main unit equivalent
                }

                let subtotal = totalQty * rate;

                row.find(".sub_total").text(subtotal.toFixed(2) +
                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}");
                row.find(".subtotal_input").val(subtotal.toFixed(2));

                updateGrandTotal();
            });

            // Function to calculate and update the Grand Total, Payable Amount, and Due Amount
            function updateGrandTotal() {
                let grandTotal = 0;
                $(".subtotal_input").each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });

                $("input[name='estimated_amount']").val(grandTotal.toFixed(2));
                updatePayableAmount(); // Update Payable Amount when Grand Total changes
            }

            // Function to calculate and update the Payable Amount
            function updatePayableAmount() {
                let estimatedAmount = parseFloat($("input[name='estimated_amount']").val()) || 0;
                let discountAmount = parseFloat($("input[name='discount_amount']").val()) || 0;

                // Ensure discount does not exceed the estimated amount
                if (discountAmount > estimatedAmount) {
                    discountAmount = estimatedAmount;
                    $("input[name='discount_amount']").val(discountAmount.toFixed(2));
                }

                let payableAmount = estimatedAmount - discountAmount;
                $("input[name='total_amount']").val(payableAmount.toFixed(2));
                $("input[name='paid_amount']").val(payableAmount.toFixed(2));
                updateDueAmount(); // Update Due Amount whenever the Payable Amount changes
            }

            // Function to calculate and update the Due Amount
            function updateDueAmount() {
                let payableAmount = parseFloat($("input[name='total_amount']").val()) || 0;
                let paidAmount = parseFloat($("input[name='paid_amount']").val()) || 0;

                let dueAmount = payableAmount - paidAmount;
                if (dueAmount < 0) dueAmount = 0; // Prevent negative due amounts

                $("input[name='due_amount']").val(dueAmount.toFixed(2));
            }

            // Update Payable Amount on Discount Amount change
            $(document).on("input", "input[name='discount_amount']", function() {
                updatePayableAmount();
            });

            // Update Due Amount on Paid Amount change
            $(document).on("input", "input[name='paid_amount']", function() {
                updateDueAmount();
            });

            // Handle Reset button click
            $(".reset_button").on("click", function() {
                $("#table_body").empty();
                db_pid_array = [];
                $("input[name='estimated_amount']").val("0");
                $("input[name='discount_amount']").val("0");
                $("input[name='total_amount']").val("0");
                $("input[name='paid_amount']").val("0");
                $("input[name='due_amount']").val("0");
                toggleSubmitAndResetButtons();
            });
        });
    </script>
@endpush
