@php
    /*
    |--------------------------------------------------------------------------
    | BARCODE
    |--------------------------------------------------------------------------
    */
    $barcodeVal = !empty($data->consignment_id)
        ? (string) $data->consignment_id
        : (!empty($data->invoice_no) ? (string) $data->invoice_no : (string) $data->id);

    $barcodeBase64 = '';
    try {
        if (class_exists('\Picqer\Barcode\BarcodeGeneratorPNG')) {
            $genPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeBase64 = 'data:image/png;base64,' . base64_encode(
                $genPNG->getBarcode($barcodeVal, $genPNG::TYPE_CODE_128, 2, 40)
            );
        }
    } catch (\Exception $e) {
        $barcodeBase64 = '';
    }

    $products = \App\Models\InvoiceItem::with(['product.unit', 'productVariation.size', 'productVariation.color'])
        ->where('invoice_id', $data->id)
        ->get();

    $comName = !empty(get_setting('com_name')) ? get_setting('com_name') : 'CAMERA BD';
    $comPhone = !empty(get_setting('com_phone')) ? get_setting('com_phone') : '01352146344';
    $labelDate = !empty($data->date) ? date('Y-m-d', strtotime($data->date)) : date('Y-m-d');
    $consignmentId = !empty($data->consignment_id) ? $data->consignment_id : $data->invoice_no;
@endphp

<div
    id="{{ $prefix ?? 'printableArea' }}-{{ $data->id }}"
    class="shipping-label-card print_part2"
    style="
        width: 75mm;
        min-height: 100mm;
        max-width: 75mm;
        margin: 0 auto;
        padding: 4mm 4mm;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        background: #ffffff;
        color: #000000;
        box-sizing: border-box;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 11px;
        line-height: 1.25;
        text-align: left;
    "
>
    {{-- HEADER: STORE NAME + PHONE (LEFT) | DATE (RIGHT) --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 4px;">
        <div style="max-width: 65%;">
            <div style="font-size: 15px; font-weight: 800; color: #000; text-transform: uppercase; line-height: 1.1; letter-spacing: -0.2px;">
                {{ $comName }}
            </div>
            <div style="font-size: 10.5px; font-weight: 700; color: #111; margin-top: 1px;">
                📞 {{ $comPhone }}
            </div>
        </div>
        <div style="text-align: right; max-width: 35%;">
            <div style="font-size: 11px; font-weight: 700; color: #111; white-space: nowrap;">
                {{ $labelDate }}
            </div>
        </div>
    </div>

    {{-- BARCODE --}}
    @if (!empty($barcodeBase64))
        <div style="text-align: center; margin: 4px 0 5px 0;">
            <img src="{{ $barcodeBase64 }}" alt="Barcode {{ $barcodeVal }}" style="height: 38px; width: 98%; max-width: 100%; display: block; margin: 0 auto; object-fit: fill;">
        </div>
    @endif

    {{-- RECIPIENT & CONSIGNMENT SECTION --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 3px;">
        <div style="max-width: 65%;">
            <div style="font-size: 8.5px; font-weight: 800; color: #333; text-transform: uppercase; letter-spacing: 0.2px; line-height: 1.1;">
                ● SHIP TO / RECIPIENT:
            </div>
            <div style="font-size: 12px; font-weight: 800; color: #000; margin-top: 2px; line-height: 1.15; word-break: break-word;">
                {{ $data->customer?->name ?? 'Customer' }}
            </div>
            <div style="font-size: 10.5px; font-weight: 700; color: #111; margin-top: 1px; line-height: 1.1;">
                📞 {{ $data->customer?->phone ?? 'N/A' }}
            </div>
        </div>
        <div style="text-align: right; max-width: 35%;">
            <div style="font-size: 8px; font-weight: 800; color: #555; text-transform: uppercase; letter-spacing: 0.2px; line-height: 1.1;">
                CONSIGNMENT ID
            </div>
            <div style="font-size: 11px; font-weight: 800; color: #000; margin-top: 2px; line-height: 1.15; word-break: break-all;">
                #{{ $consignmentId }}
            </div>
        </div>
    </div>

    {{-- ADDRESS --}}
    <div style="font-size: 9.5px; font-weight: 600; color: #111; margin: 2px 0 5px 0; line-height: 1.3; word-break: break-word;">
        📍 {{ !empty($data->customer?->address) ? $data->customer->address : 'N/A' }}
    </div>

    {{-- COD BOX --}}
    <div style="display: flex; justify-content: space-between; align-items: center; border: 1.5px solid #000; border-radius: 4px; padding: 4px 6px; margin: 4px 0 6px 0; background: #fff;">
        <div style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: #000; letter-spacing: 0.2px;">
            :: CASH ON DELIVERY (COD):
        </div>
        <div style="font-size: 16px; font-weight: 900; color: #000; white-space: nowrap;">
            TK {{ number_format($data->total_due, 2) }}
        </div>
    </div>

    {{-- PRODUCT LIST TABLE --}}
    <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 8px; table-layout: fixed;">
        <thead>
            <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; color: #000;">
                <th style="padding: 3px 2px; width: 8%; font-weight: 800; text-align: left;">#</th>
                <th style="padding: 3px 2px; width: 70%; font-weight: 800; text-align: left;">Item</th>
                <th style="padding: 3px 2px; width: 22%; font-weight: 800; text-align: right;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = 1; @endphp
            @foreach ($products as $item)
                @php
                    $unitName = $item->product?->unit?->name ?? 'pcs';
                    $qtyDisplay = number_format($item->main_qty ?? 1, 2) . ' ' . $unitName;
                @endphp
                <tr style="border-bottom: 0.5px solid #e5e7eb;">
                    <td style="padding: 2.5px 2px; vertical-align: top; font-weight: 600; color: #333;">
                        {{ $sl++ }}
                    </td>
                    <td style="padding: 2.5px 2px; vertical-align: top; font-weight: 600; color: #000; word-break: break-word; line-height: 1.2;">
                        {{ $item->product?->name ?? 'Product' }}
                        @if ($item->product_variation_id != null)
                            <span style="font-size: 8.5px; color: #444;">
                                ({{ $item->productVariation?->size?->size }} - {{ $item->productVariation?->color?->color }})
                            </span>
                        @endif
                        @if (env('APP_IMEI') == 'yes' && !empty($item->imei))
                            <div style="font-size: 8px; color: #444;">
                                IMEI: {{ str_replace(',', ', ', $item->imei) }}
                            </div>
                        @endif
                    </td>
                    <td style="padding: 2.5px 2px; vertical-align: top; text-align: right; font-weight: 700; color: #000; white-space: nowrap;">
                        {{ $qtyDisplay }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER / DISCLAIMER --}}
    <div style="text-align: center; margin-top: 6px; padding-top: 2px; color: #000;">
        @if (!empty($data->note))
            <div style="font-size: 8px; font-weight: 700; color: #b91c1c; margin-bottom: 2px; word-break: break-word;">
                Note: {{ $data->note }}
            </div>
        @endif
        <div style="font-size: 8px; font-weight: 700; line-height: 1.3;">
            NO Return / NO Exchange without checking before delivery man leaves
        </div>
        <div style="font-size: 8px; font-weight: 800; margin-top: 1px; line-height: 1.3;">
            Thank you for keeping trust on us
        </div>
    </div>
</div>
