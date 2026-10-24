<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Bulk Shipping Labels Print') }} ({{ $invoices->count() }})</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/feather-icons/feather.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .labels-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .single-label-wrapper {
            background: #ffffff;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 6px;
            overflow: hidden;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .labels-container {
                max-width: 100% !important;
                margin: 0 !important;
            }

            .single-label-wrapper {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .single-label-wrapper:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="feather icon-printer text-primary mr-1"></i> {{ __('Bulk Shipping Labels') }} ({{ $invoices->count() }} {{ __('Invoices') }})
            </h5>
            <small class="text-muted">{{ __('Ready for thermal / standard printing') }}</small>
        </div>
        <div>
            <button type="button" class="btn btn-primary font-weight-bold px-4 shadow-sm" onclick="window.print()">
                <i class="feather icon-printer mr-1"></i> {{ __('Print All Labels') }}
            </button>
            <button type="button" class="btn btn-outline-secondary font-weight-bold ml-2" onclick="window.close()">
                {{ __('Close') }}
            </button>
        </div>
    </div>

    <div class="labels-container">
        @foreach ($invoices as $data)
            <div class="single-label-wrapper">
                @include('backend.pages.invoice.shipping-label', ['data' => $data, 'prefix' => 'bulkPrintArea'])
            </div>
        @endforeach
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
