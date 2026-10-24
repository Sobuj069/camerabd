<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            border-radius: 8px;
        }
        .header-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #102d28;
            text-transform: uppercase;
        }
        .items-table {
            margin-top: 25px;
        }
        .items-table th {
            background: #f4f6f8;
            border: 1px solid #ddd;
            padding: 8px;
            font-weight: bold;
            text-align: left;
        }
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row th, .total-row td {
            font-weight: bold;
            font-size: 14px;
            background: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            background: #e2e8f0;
        }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #006241; color: #fff; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer;">
            🖨️ Print Invoice
        </button>
        <button onclick="window.close()" style="background: #64748b; color: #fff; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <div class="title">{{ get_setting('com_name') ?: 'CAMERA BD' }}</div>
                    <div style="font-size: 11px; color: #666; margin-top: 4px;">
                        {{ get_setting('com_address') ?: 'Jamuna Future park , Level-4 , Block-c , Shop No 012B1' }}<br>
                        Phone: {{ get_setting('com_phone') ?: '01352146344' }} | Email: {{ get_setting('com_email') ?: 'camerabd.shop@gmail.com' }}
                    </div>
                </td>
                <td class="text-right">
                    <h2 style="margin: 0; color: #dc2626;">WEBSITE INVOICE</h2>
                    <p style="margin: 4px 0;"><strong>Order #:</strong> {{ $order->order_no }}</p>
                    <p style="margin: 4px 0;"><strong>Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>
                    <p style="margin: 4px 0;"><span class="badge">{{ strtoupper($order->order_status) }}</span></p>
                </td>
            </tr>
        </table>

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <strong style="color: #006241;">DELIVERY TO (CUSTOMER):</strong><br>
                    <strong>Name:</strong> {{ $order->customer_name }}<br>
                    <strong>Phone:</strong> {{ $order->phone }}<br>
                    @if($order->email)
                        <strong>Email:</strong> {{ $order->email }}<br>
                    @endif
                    <strong>Address:</strong> {{ $order->delivery_address }}<br>
                    @if($order->city)
                        <strong>City:</strong> {{ $order->city }} {{ $order->division ? '(' . $order->division . ')' : '' }}<br>
                    @endif
                </td>
                <td style="width: 45%;">
                    <strong style="color: #006241;">PAYMENT & DELIVERY DETAILS:</strong><br>
                    <strong>Payment Method:</strong> {{ $order->payment_method }}<br>
                    <strong>Payment Status:</strong> {{ strtoupper($order->payment_status) }}<br>
                    <strong>Delivery Option:</strong> {{ ucwords(str_replace('_', ' ', $order->delivery_method)) }}<br>
                    @if($order->order_notes)
                        <strong style="color: #dc2626;">Customer Note:</strong> {{ $order->order_notes }}<br>
                    @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 55%;">Product Description</th>
                    <th class="text-center" style="width: 15%;">Unit Price</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product_barcode)
                                <br><small style="color: #666;">SKU / Barcode: {{ $item->product_barcode }}</small>
                            @endif
                        </td>
                        <td class="text-center">৳ {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                        <td class="text-right font-weight-bold">৳ {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">৳ {{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right"><strong>Shipping Cost:</strong></td>
                    <td class="text-right">৳ {{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                    <tr>
                        <td colspan="4" class="text-right"><strong>Discount:</strong></td>
                        <td class="text-right">- ৳ {{ number_format($order->discount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4" class="text-right">GRAND TOTAL:</td>
                    <td class="text-right" style="color: #006241; font-size: 16px;">৳ {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px dashed #ccc; font-size: 11px; text-align: center; color: #777;">
            Thank you for shopping with {{ get_setting('com_name') ?: 'CAMERA BD' }}! For warranty & support, visit our store or call hotline: {{ get_setting('com_phone') ?: '01352146344' }}.
        </div>
    </div>
</body>
</html>
