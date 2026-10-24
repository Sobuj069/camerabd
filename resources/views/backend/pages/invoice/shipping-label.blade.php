@php
    /*
    |--------------------------------------------------------------------------
    | BARCODE
    |--------------------------------------------------------------------------
    */
    $barcodeVal = !empty($data->invoice_no)
        ? (string) $data->invoice_no
        : (string) $data->id;

    $barcodeBase64 = '';

    try {
        $genPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $barcodeBase64 = 'data:image/png;base64,' . base64_encode(
            $genPNG->getBarcode(
                $barcodeVal,
                $genPNG::TYPE_CODE_128,
                2.5,
                38
            )
        );
    } catch (\Exception $e) {
        $barcodeBase64 = '';
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */
    $products = \App\Models\InvoiceItem::where('invoice_id', $data->id)->get();

    $productCount = $products->count();


    /*
    |--------------------------------------------------------------------------
    | FULL LABEL DYNAMIC SCALE
    |--------------------------------------------------------------------------
    |
    | 5 products = 100% / Normal
    |
    | কম product হলে পুরো label বড় হবে
    | বেশি product হলে পুরো label ছোট হবে
    |
    |--------------------------------------------------------------------------
    */

    if ($productCount <= 1) {

        $labelScale = 1.18;

    } elseif ($productCount <= 3) {

        $labelScale = 1.10;

    } elseif ($productCount <= 5) {

        // NORMAL
        $labelScale = 1.00;

    } elseif ($productCount <= 7) {

        $labelScale = 0.94;

    } elseif ($productCount <= 9) {

        $labelScale = 0.88;

    } elseif ($productCount <= 12) {

        $labelScale = 0.82;

    } elseif ($productCount <= 16) {

        $labelScale = 0.76;

    } elseif ($productCount <= 20) {

        $labelScale = 0.70;

    } else {

        $labelScale = 0.64;
    }


    /*
    |--------------------------------------------------------------------------
    | FONT SIZE HELPER
    |--------------------------------------------------------------------------
    */
    $fs = function ($size) use ($labelScale) {
        return round($size * $labelScale, 2);
    };


    /*
    |--------------------------------------------------------------------------
    | SPACE / PADDING HELPER
    |--------------------------------------------------------------------------
    */
    $sp = function ($size) use ($labelScale) {
        return round($size * $labelScale, 2);
    };


    /*
    |--------------------------------------------------------------------------
    | BARCODE HEIGHT
    |--------------------------------------------------------------------------
    */
    $barcodeHeight = round(32 * $labelScale, 2);


    /*
    |--------------------------------------------------------------------------
    | MAIN PADDING
    |--------------------------------------------------------------------------
    */
    $labelPaddingTopBottom = round(2.5 * $labelScale, 2);
    $labelPaddingLeftRight = round(3.5 * $labelScale, 2);
@endphp


<div
    id="{{ $prefix ?? 'printableArea' }}-{{ $data->id }}"
    class="shipping-label-card print_part2"
    style="
        width: 75mm;
        height: 100mm;
        max-width: 75mm;
        max-height: 100mm;

        margin: 0 auto;

        padding:
            {{ $labelPaddingTopBottom }}mm
            {{ $labelPaddingLeftRight }}mm;

        font-family:
            'Segoe UI',
            Roboto,
            Helvetica,
            Arial,
            sans-serif;

        background: #ffffff;
        color: #000000;

        box-sizing: border-box;

        border: 0;
        border-radius: 4px;

        overflow: hidden;

        display: flex;
        flex-direction: column;
        justify-content: space-between;
    "
>

    {{-- ============================================================= --}}
    {{-- MAIN CONTENT --}}
    {{-- ============================================================= --}}

    <div>

        {{-- ============================================================= --}}
        {{-- HEADER --}}
        {{-- ============================================================= --}}

        <div
            style="
                display: flex;
                justify-content: space-between;
                align-items: flex-start;

                border-bottom:
                    {{ $sp(1.5) }}px
                    solid
                    #000;

                padding-bottom:
                    {{ $sp(3) }}px;

                margin-bottom:
                    {{ $sp(4) }}px;
            "
        >

            {{-- COMPANY --}}
            <div style="max-width: 58%;">

                <div
                    style="
                        font-size: {{ $fs(14) }}px;

                        font-weight: 900;

                        color: #000;

                        text-transform: uppercase;

                        line-height: 1.1;

                        letter-spacing: {{ $sp(-0.2) }}px;

                        overflow-wrap: anywhere;

                        word-break: break-word;
                    "
                >
                    {{ empty(get_setting('com_name')) ? 'FAST IT' : get_setting('com_name') }}
                </div>


                @if (!empty(get_setting('com_phone')))

                    <div
                        style="
                            font-size: {{ $fs(9.5) }}px;

                            font-weight: 700;

                            color: #333;

                            margin-top:
                                {{ $sp(1) }}px;
                        "
                    >
                        📞 {{ get_setting('com_phone') }}
                    </div>

                @endif

            </div>


            {{-- COURIER + DATE --}}
            <div style="text-align: right;">

                <span
                    style="
                        display: inline-block;

                        background: #000;

                        color: #fff;

                        font-size: {{ $fs(10) }}px;

                        font-weight: 800;

                        text-transform: uppercase;

                        padding:
                            {{ $sp(1.5) }}px
                            {{ $sp(6) }}px;

                        border-radius:
                            {{ $sp(3) }}px;

                        letter-spacing:
                            {{ $sp(0.3) }}px;

                        white-space: nowrap;
                    "
                >
                    {{ strtoupper($data->courier_type ?: 'STEADFAST') }}
                </span>


                <div
                    style="
                        font-size: {{ $fs(9.5) }}px;

                        font-weight: 700;

                        color: #333;

                        margin-top:
                            {{ $sp(1) }}px;
                    "
                >
                    {{ $data->date }}
                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- BARCODE --}}
        {{-- ============================================================= --}}

        @if (!empty($barcodeBase64))

            <div
                style="
                    text-align: center;

                    margin-bottom:
                        {{ $sp(4) }}px;

                    padding:
                        {{ $sp(2) }}px
                        0;

                    border-bottom:
                        {{ $sp(1) }}px
                        dashed
                        #e4e4e7;
                "
            >

                <img
                    src="{{ $barcodeBase64 }}"
                    alt="Barcode {{ $barcodeVal }}"
                    style="
                        height: {{ $barcodeHeight }}px;

                        width: 98%;

                        max-width: 100%;

                        display: block;

                        margin: 0 auto;

                        object-fit: fill;
                    "
                >

            </div>

        @endif


        {{-- ============================================================= --}}
        {{-- CUSTOMER INFORMATION --}}
        {{-- ============================================================= --}}

        <div
            style="
                text-align: left;

                margin-bottom:
                    {{ $sp(1) }}px;
            "
        >

            <div class="row">

                {{-- CUSTOMER NAME + PHONE --}}
                <div class="col-7">

                    <div
                        style="
                            font-size: {{ $fs(7.5) }}px;

                            font-weight: 600;

                            color: #52525b;

                            text-transform: uppercase;

                            line-height: 1.1;
                        "
                    >
                        📍 Ship To / Recipient:
                    </div>


                    {{-- CUSTOMER NAME --}}
                    <div
                        style="
                            font-size: {{ $fs(9) }}px;

                            font-weight: 600;

                            color: #000;

                            line-height: 1.15;

                            overflow-wrap: anywhere;

                            word-break: break-word;
                        "
                    >
                        {{ $data->customer?->name ?? 'N/A' }}
                    </div>


                    {{-- PHONE --}}
                    <div
                        style="
                            font-size: {{ $fs(9) }}px;

                            font-weight: 600;

                            color: #000;

                            margin:
                                {{ $sp(1) }}px
                                0;

                            line-height: 1.1;
                        "
                    >
                        📱 {{ $data->customer?->phone ?? 'N/A' }}
                    </div>

                </div>


                {{-- INVOICE --}}
                <div class="col-5">

                    <div style="text-align: right;">

                        <div
                            style="
                                font-size: {{ $fs(8) }}px;

                                font-weight: 600;

                                color: #52525b;

                                text-transform: uppercase;
                            "
                        >
                            Consignment ID
                        </div>


                        <div
                            style="
                                font-size: {{ $fs(9) }}px;

                                font-weight: 600;

                                color: #000;

                                overflow-wrap: anywhere;
                            "
                        >
                            #{{ $data->invoice_no }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- CUSTOMER ADDRESS --}}
        {{-- ============================================================= --}}

        <div
            style="
                text-align: left;

                padding-bottom:
                    {{ $sp(1) }}px;

                margin-bottom:
                    {{ $sp(1) }}px;
            "
        >

            <div
                style="
                    font-size: {{ $fs(7.5) }}px;

                    font-weight: 600;

                    color: #18181b;

                    line-height: 1.15;

                    overflow-wrap: anywhere;

                    word-break: break-word;
                "
            >
                🏠 {{ $data->customer?->address ?? 'N/A' }}
            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- COD BOX --}}
        {{-- ============================================================= --}}

        <div
            style="
                display: flex;

                justify-content: space-between;

                align-items: center;

                margin-bottom:
                    {{ $sp(2) }}px;

                padding:
                    {{ $sp(3) }}px
                    {{ $sp(2) }}px;

                background: #f4f4f5;

                border:
                    {{ $sp(1) }}px
                    solid
                    #000;

                border-radius:
                    {{ $sp(4) }}px;

                gap:
                    {{ $sp(2) }}px;
            "
        >

            {{-- COD LABEL --}}
            <div
                style="
                    font-size: {{ $fs(9.5) }}px;

                    font-weight: 800;

                    text-transform: uppercase;

                    color: #333;

                    line-height: 1.1;

                    flex: 1;
                "
            >
                💵 Cash on Delivery (COD):
            </div>


            {{-- COD AMOUNT --}}
            <div
                style="
                    font-size: {{ $fs(16) }}px;

                    font-weight: 900;

                    color: #000;

                    white-space: nowrap;

                    text-align: right;
                "
            >
                TK {{ number_format($data->total_due, 2) }}
            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- PRODUCT TABLE --}}
        {{-- ============================================================= --}}

        <div
            style="
                margin-bottom:
                    {{ $sp(2) }}px;

                text-align: left;
            "
        >

            <table
                style="
                    width: 100%;

                    border-collapse: collapse;

                    font-size: {{ $fs(10) }}px;

                    text-align: left;

                    color: #000;

                    table-layout: fixed;
                "
            >

                {{-- TABLE HEADER --}}
                <thead>

                    <tr
                        style="
                            border-bottom:
                                {{ $sp(1) }}px
                                solid
                                #000;

                            color: #333;
                        "
                    >

                        <th
                            style="
                                padding:
                                    {{ $sp(1.5) }}px
                                    {{ $sp(1) }}px;

                                width: 8%;

                                font-weight: 800;
                            "
                        >
                            #
                        </th>


                        <th
                            style="
                                padding:
                                    {{ $sp(1.5) }}px
                                    {{ $sp(1) }}px;

                                width: 72%;

                                font-weight: 800;
                            "
                        >
                            Item
                        </th>


                        <th
                            style="
                                padding:
                                    {{ $sp(1.5) }}px
                                    {{ $sp(1) }}px;

                                width: 20%;

                                text-align: right;

                                font-weight: 800;
                            "
                        >
                            Qty
                        </th>

                    </tr>

                </thead>


                {{-- TABLE BODY --}}
                <tbody>

                    @php
                        $sl = 1;
                    @endphp


                    @foreach ($products as $item)

                        {{-- ================================================= --}}
                        {{-- QTY --}}
                        {{-- ================================================= --}}

                        @php

                            if ($item->product?->unit?->related_unit == null) {

                                $qty =
                                    $item->main_qty .
                                    ' ' .
                                    ($item->product?->unit?->name ?? 'pcs');

                            } else {

                                $sub_qty = $item->sub_qty ?? 0;

                                if (
                                    $item->main_qty == 0 &&
                                    $sub_qty == 0
                                ) {

                                    $qty =
                                        '0 ' .
                                        ($item->product?->unit?->name ?? 'pcs');

                                } else {

                                    $qty = '';

                                    if ($item->main_qty > 0) {

                                        $qty .=
                                            $item->main_qty .
                                            ' ' .
                                            ($item->product?->unit?->name ?? 'pcs');
                                    }

                                    if (
                                        $item->main_qty > 0 &&
                                        $sub_qty > 0
                                    ) {

                                        $qty .= ' - ';
                                    }

                                    if ($sub_qty > 0) {

                                        $qty .=
                                            $sub_qty .
                                            ' ' .
                                            (
                                                $item->product?->unit?->related_unit?->name
                                                ?? ''
                                            );
                                    }
                                }
                            }

                        @endphp


                        {{-- ================================================= --}}
                        {{-- PRODUCT ROW --}}
                        {{-- ================================================= --}}

                        <tr
                            style="
                                border-bottom:
                                    {{ $sp(0.5) }}px
                                    solid
                                    #e4e4e7;
                            "
                        >

                            {{-- SERIAL --}}
                            <td
                                style="
                                    padding:
                                        {{ $sp(1.5) }}px
                                        {{ $sp(1) }}px;

                                    vertical-align: top;

                                    font-size: {{ $fs(7) }}px;

                                    font-weight: 600;

                                    color: #52525b;

                                    line-height: 1.1;
                                "
                            >
                                {{ $sl++ }}
                            </td>


                            {{-- PRODUCT --}}
                            <td
                                style="
                                    padding:
                                        {{ $sp(1.5) }}px
                                        {{ $sp(1) }}px;

                                    vertical-align: top;

                                    line-height: 1.15;

                                    overflow-wrap: anywhere;

                                    word-break: break-word;
                                "
                            >

                                {{-- PRODUCT NAME --}}
                                <strong
                                    style="
                                        font-size: {{ $fs(7.5) }}px;

                                        color: #000;

                                        font-weight: 700;

                                        line-height: 1.15;
                                    "
                                >
                                    {{ $item->product?->name ?? 'Product' }}
                                </strong>


                                {{-- VARIATION --}}
                                @if ($item->product_variation_id != null)

                                    <span
                                        style="
                                            font-size: {{ $fs(7) }}px;

                                            font-weight: 600;

                                            color: #52525b;
                                        "
                                    >
                                        (
                                        {{ $item->product_variation?->size?->size }}
                                        -
                                        {{ $item->product_variation?->color?->color }}
                                        )
                                    </span>

                                @endif


                                {{-- IMEI --}}
                                @if (
                                    env('APP_IMEI') == 'yes' &&
                                    !empty($item->imei)
                                )

                                    <div
                                        style="
                                            font-size: {{ $fs(7.5) }}px;

                                            font-weight: 600;

                                            color: #52525b;

                                            line-height: 1.1;

                                            overflow-wrap: anywhere;

                                            word-break: break-word;
                                        "
                                    >
                                        IMEI:
                                        {{ str_replace(',', ', ', $item->imei) }}
                                    </div>

                                @endif

                            </td>


                            {{-- QTY --}}
                            <td
                                style="
                                    padding:
                                        {{ $sp(1.5) }}px
                                        {{ $sp(1) }}px;

                                    font-size: {{ $fs(7) }}px;

                                    vertical-align: top;

                                    text-align: right;

                                    font-weight: 600;

                                    color: #000;

                                    line-height: 1.1;

                                    overflow-wrap: anywhere;

                                    word-break: break-word;
                                "
                            >
                                {{ $qty }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- FOOTER --}}
    {{-- ============================================================= --}}

    <div
        style="
            padding-top:
                {{ $sp(2) }}px;

            text-align: left;

            font-size: {{ $fs(8.5) }}px;

            font-weight: 600;

            color: #52525b;

            line-height: 1.2;
        "
    >

        {{-- NOTE --}}
        @if (!empty($data->note))

            <div
                style="
                    font-size: {{ $fs(6.5) }}px;

                    font-weight: 700;

                    color: #b91c1c;

                    margin-top: 0;

                    background: #fef2f2;

                    padding:
                        {{ $sp(1) }}px
                        {{ $sp(1) }}px;

                    border-radius:
                        {{ $sp(3) }}px;

                    line-height: 1.15;

                    overflow-wrap: anywhere;

                    word-break: break-word;
                "
            >

                <strong>Note :</strong>

                {{ $data->note }}

            </div>

        @endif


        {{-- RETURN POLICY --}}
        <div
            style="
                border-top:
                    {{ $sp(1) }}px
                    dashed
                    #a1a1aa;

                font-size: {{ $fs(7.5) }}px;

                text-align: center;

                margin-top:
                    {{ $sp(2) }}px;

                padding-top:
                    {{ $sp(1) }}px;

                line-height: 1.15;
            "
        >
            NO Return / NO Exchange without checking before delivery man leaves
        </div>


        {{-- THANK YOU --}}
        <div
            style="
                font-size: {{ $fs(7.5) }}px;

                font-weight: 800;

                color: #000;

                text-align: center;

                line-height: 1.15;

                margin-top:
                    {{ $sp(1) }}px;
            "
        >
            Thank you for keeping trust on us
        </div>

    </div>

</div>