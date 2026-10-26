@extends('frontend.layouts.master')

@section('title', 'Order Confirmation - ' . ($orderNumber ?? ($siteName ?? 'CAMERA BD')))

@section('content')
<div class="max-w-4xl mx-auto px-4 lg:px-6 pt-6 lg:pt-8 pb-16 w-full">

  <!-- Success Hero Banner -->
  <div class="bg-surface-card rounded-3xl p-6 md:p-10 border border-surface-border shadow-xs text-center mb-8">
    <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xs">
      <span class="material-symbols-outlined text-[36px] md:text-[44px]">check_circle</span>
    </div>
    
    <span class="inline-block bg-primary/10 text-primary text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
      Order Successfully Placed
    </span>
    <h1 class="text-2xl md:text-3xl font-extrabold text-text-display mb-2">
      Thank You for Your Order!
    </h1>
    <p class="text-xs md:text-sm text-text-muted max-w-lg mx-auto leading-relaxed">
      We have received your gear order and our warehouse engineers are currently inspecting & calibrating your equipment for dispatch.
    </p>

    <!-- Quick Meta Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-surface-border text-left">
      <div class="p-3 bg-surface-container-low rounded-xl border border-surface-border">
        <span class="text-[10px] text-text-muted uppercase font-bold block">Order Number</span>
        <strong class="text-xs font-extrabold text-primary">{{ $orderNumber ?? 'CB-' . date('Ymd') . '-8942' }}</strong>
      </div>
      <div class="p-3 bg-surface-container-low rounded-xl border border-surface-border">
        <span class="text-[10px] text-text-muted uppercase font-bold block">Order Date</span>
        <strong class="text-xs font-bold text-on-surface">
          @if(isset($order) && $order->created_at)
            {{ $order->created_at->format('d M, Y h:i A') }}
          @else
            {{ date('d M, Y') }}
          @endif
        </strong>
      </div>
      <div class="p-3 bg-surface-container-low rounded-xl border border-surface-border">
        <span class="text-[10px] text-text-muted uppercase font-bold block">Payment Method</span>
        <strong class="text-xs font-bold text-on-surface">{{ $orderData['payment_method'] ?? 'Cash On Delivery' }}</strong>
      </div>
      <div class="p-3 bg-surface-container-low rounded-xl border border-surface-border">
        <span class="text-[10px] text-text-muted uppercase font-bold block">Order Status</span>
        <strong class="text-xs font-extrabold text-amber-600 uppercase">
          {{ $order->order_status ?? 'Pending' }}
        </strong>
      </div>
    </div>
  </div>

  <!-- Order Details & Receipt Card -->
  <div class="bg-surface-card rounded-3xl p-6 md:p-8 border border-surface-border shadow-xs space-y-6 print-area">
    
    <!-- Customer & Shipping Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-surface-border">
      <div>
        <h3 class="text-xs font-extrabold text-text-muted uppercase tracking-wider mb-2 flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px] text-primary">person</span>
          <span>Customer & Contact</span>
        </h3>
        <p class="text-sm font-bold text-on-surface">{{ $orderData['customer_name'] ?? 'Customer' }}</p>
        <p class="text-xs text-text-muted mt-0.5"><strong class="text-on-surface">Phone:</strong> {{ $orderData['phone'] ?? 'N/A' }}</p>
        @if(!empty($orderData['email']))
          <p class="text-xs text-text-muted"><strong class="text-on-surface">Email:</strong> {{ $orderData['email'] }}</p>
        @endif
      </div>

      <div>
        <h3 class="text-xs font-extrabold text-text-muted uppercase tracking-wider mb-2 flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px] text-primary">location_on</span>
          <span>Delivery Address</span>
        </h3>
        <p class="text-xs font-bold text-on-surface leading-relaxed">
          {{ $orderData['delivery_address'] ?? 'N/A' }}
        </p>
        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
          <span class="inline-block bg-surface-container text-primary font-bold text-[10px] px-2 py-0.5 rounded">
            {{ $orderData['city'] ?? 'Dhaka' }}
          </span>
          @if(!empty($orderData['division']))
            <span class="inline-block bg-surface-container-low text-text-muted font-bold text-[10px] px-2 py-0.5 rounded">
              {{ $orderData['division'] }}
            </span>
          @endif
        </div>
      </div>
    </div>

    <!-- Itemized List -->
    <div>
      <h3 class="text-xs font-extrabold text-text-muted uppercase tracking-wider mb-4">
        Ordered Gear Checklist ({{ count($orderItems) ?: 1 }} Item{{ count($orderItems) > 1 ? 's' : '' }})
      </h3>

      <div class="divide-y divide-surface-border">
        @if(isset($orderItems) && count($orderItems) > 0)
          @foreach($orderItems as $item)
            <div class="py-3 flex items-center justify-between gap-4 text-xs">
              <div class="flex items-center gap-3">
                @if($item->product_image)
                  <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-contain bg-surface-base rounded-lg p-1 border border-surface-border" />
                @else
                  <div class="w-12 h-12 bg-surface-base rounded-lg border border-surface-border flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[20px]">videocam</span>
                  </div>
                @endif
                <div>
                  <h4 class="font-bold text-on-surface">{{ $item->product_name }}</h4>
                  <span class="text-text-muted text-[11px]">
                    Qty: {{ $item->quantity }} × ৳ {{ number_format($item->unit_price, 2) }}
                    @if($item->product_barcode)
                      • Code: {{ $item->product_barcode }}
                    @endif
                  </span>
                </div>
              </div>
              <span class="font-bold text-primary whitespace-nowrap">৳ {{ number_format($item->total_price, 2) }} TK</span>
            </div>
          @endforeach
        @else
          <!-- Fallback items if none stored -->
          <div class="py-3 flex items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-surface-base rounded-lg border border-surface-border flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
              </div>
              <div>
                <h4 class="font-bold text-on-surface">Standard Cinema & Audio Gear Order</h4>
                <span class="text-text-muted text-[11px]">Qty: 1 × ৳ {{ number_format($orderData['subtotal'] ?? 0, 2) }}</span>
              </div>
            </div>
            <span class="font-bold text-primary">৳ {{ number_format($orderData['subtotal'] ?? 0, 2) }} TK</span>
          </div>
        @endif
      </div>
    </div>

    <!-- Final Price Total -->
    <div class="pt-4 border-t border-surface-border bg-surface-container-low p-4 rounded-2xl text-xs space-y-2">
      <div class="flex items-center justify-between text-text-muted">
        <span>Subtotal:</span>
        <span class="font-bold text-on-surface">৳ {{ number_format($orderData['subtotal'] ?? 0, 2) }} TK</span>
      </div>
      <div class="flex items-center justify-between text-text-muted">
        <span>Delivery Fee ({{ ucwords(str_replace('_', ' ', $orderData['delivery_method'] ?? 'Express Delivery')) }}):</span>
        <span class="{{ ($orderData['shipping_cost'] ?? 0) > 0 ? 'font-bold text-on-surface' : 'text-stock-available font-bold' }}">
          {{ ($orderData['shipping_cost'] ?? 0) > 0 ? ('৳ ' . number_format($orderData['shipping_cost'], 2) . ' TK') : 'FREE' }}
        </span>
      </div>
      @if(!empty($orderData['discount']) && $orderData['discount'] > 0)
        <div class="flex items-center justify-between text-emerald-600 font-bold">
          <span>Discount Applied:</span>
          <span>- ৳ {{ number_format($orderData['discount'], 2) }} TK</span>
        </div>
      @endif
      <div class="pt-2 border-t border-surface-border flex items-baseline justify-between">
        <span class="text-sm font-bold text-text-display">Total Payable Amount:</span>
        <span class="text-xl font-extrabold text-primary">৳ {{ number_format($orderData['total_amount'] ?? 0, 2) }} TK</span>
      </div>
    </div>

    <!-- Action Buttons: Print, Track, Continue -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 no-print">
      <button onclick="window.print()" class="w-full sm:w-auto bg-surface-container hover:bg-surface-container-low text-on-surface border border-surface-border font-bold text-xs py-3 px-5 rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-xs">
        <span class="material-symbols-outlined text-[16px]">print</span>
        <span>Print Invoice Receipt</span>
      </button>

      <div class="flex items-center gap-3 w-full sm:w-auto">
        <a href="{{ url('/') }}" class="flex-1 sm:flex-initial border border-primary text-primary hover:bg-primary/5 font-bold text-xs py-3 px-5 rounded-xl transition-colors text-center">
          Return to Shop
        </a>
        <a href="tel:{{ $whatsappDigits ?? '8801917256756' }}" class="flex-1 sm:flex-initial bg-primary hover:bg-primary-hover text-white font-bold text-xs py-3 px-5 rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-sm text-center">
          <span class="material-symbols-outlined text-[16px]">support_agent</span>
          <span>Order Support</span>
        </a>
      </div>
    </div>

  </div>

</div>
@endsection

@push('scripts')
<script>
  // GA4 / GTM DataLayer Purchase & Customer Info Event Tracking
  @php
    $dlOrderItems = [];
    if (isset($orderItems) && count($orderItems) > 0) {
      foreach ($orderItems as $idx => $oi) {
        $dlOrderItems[] = [
          'item_id' => (string)($oi->product_id ?? ($oi->product_barcode ?? $oi->product_name)),
          'item_name' => $oi->product_name ?? 'Product',
          'affiliation' => $siteName ?? 'CAMERA BD',
          'currency' => 'BDT',
          'price' => (float)($oi->unit_price ?? 0),
          'quantity' => (int)($oi->quantity ?? 1),
          'index' => $idx + 1
        ];
      }
    } else {
      $dlOrderItems[] = [
        'item_id' => 'ORD-' . ($orderNumber ?? '1001'),
        'item_name' => 'Standard Gear Order',
        'affiliation' => $siteName ?? 'CAMERA BD',
        'currency' => 'BDT',
        'price' => (float)($orderData['subtotal'] ?? ($orderData['total_amount'] ?? 0)),
        'quantity' => 1,
        'index' => 1
      ];
    }

    $cName = (string)($orderData['customer_name'] ?? '');
    $cPhone = (string)($orderData['phone'] ?? '');
    $cEmail = (string)($orderData['email'] ?? '');
    $cCity = (string)($orderData['city'] ?? '');
    $cDivision = (string)($orderData['division'] ?? '');
    $cAddress = (string)($orderData['delivery_address'] ?? '');

    $nameParts = explode(' ', trim($cName));
    $cFirstName = $nameParts[0] ?? '';
    $cLastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

    $rawDigits = preg_replace('/[^0-9]/', '', $cPhone);
    $fmtPhone = $cPhone;
    if (strlen($rawDigits) === 11 && str_starts_with($rawDigits, '01')) {
      $fmtPhone = '+88' . $rawDigits;
    } elseif (strlen($rawDigits) === 13 && str_starts_with($rawDigits, '8801')) {
      $fmtPhone = '+' . $rawDigits;
    }

    $customerData = [
      'name' => $cName,
      'customer_name' => $cName,
      'first_name' => $cFirstName,
      'last_name' => $cLastName,
      'phone' => $cPhone,
      'customer_phone' => $cPhone,
      'phone_number' => $fmtPhone,
      'email' => $cEmail,
      'customer_email' => $cEmail,
      'city' => $cCity,
      'customer_city' => $cCity,
      'district' => $cCity,
      'division' => $cDivision,
      'customer_division' => $cDivision,
      'address' => $cAddress,
      'customer_address' => $cAddress,
      'delivery_address' => $cAddress,
      'street' => $cAddress,
      'country' => 'Bangladesh',
      'country_code' => 'BD'
    ];

    $userData = [
      'email' => !empty($cEmail) ? $cEmail : null,
      'phone_number' => !empty($fmtPhone) ? $fmtPhone : null,
      'address' => [
        'first_name' => !empty($cFirstName) ? $cFirstName : null,
        'last_name' => !empty($cLastName) ? $cLastName : null,
        'street' => !empty($cAddress) ? $cAddress : null,
        'city' => !empty($cCity) ? $cCity : null,
        'region' => !empty($cDivision) ? $cDivision : null,
        'postal_code' => null,
        'country' => 'BD'
      ]
    ];
  @endphp
  document.addEventListener('DOMContentLoaded', function() {
    window.dataLayer = window.dataLayer || [];

    // User Data Push for Google Tag Manager Enhanced Conversions
    window.dataLayer.push({
      event: 'user_data',
      customer: {!! json_encode($customerData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!},
      customer_info: {!! json_encode($customerData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!},
      user_data: {!! json_encode($userData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
    });

    // Deduplication tracker to guarantee strictly ONE single "purchase" event on Approval
    window._dispatchedApprovedOrders = window._dispatchedApprovedOrders || {};

    // Function to dispatch purchase into dataLayer with full customer data & approved status
    function dispatchApprovedOrderConfirm(payload) {
      if (!payload) return;
      var orderKey = String(payload.order_id || payload.transaction_id || '{{ $orderNumber }}');
      if (window._dispatchedApprovedOrders[orderKey]) {
        console.log('[DataLayer] "purchase" already dispatched for Order #' + orderKey + ', skipping duplicate.');
        return;
      }
      window._dispatchedApprovedOrders[orderKey] = true;

      try {
        window.dataLayer = window.dataLayer || [];
        // Clear previous ecommerce object per Google standard
        window.dataLayer.push({ ecommerce: null });

        var ev = Object.assign({}, payload, {
          event: 'purchase',
          status: 'approved',
          order_status: 'Approved'
        });

        // Strictly ONE single event push
        window.dataLayer.push(ev);

        console.log('%c✅ [DataLayer] Single "purchase" event fired! Status: APPROVED', 'background: #10b981; color: white; font-weight: bold; padding: 6px 12px; border-radius: 4px;', ev);
      } catch(err) {
        console.warn('[DataLayer] Error firing purchase:', err);
      }
    }

    @if(!empty($isApproved) && !empty($approvedOrderDl))
      // Order is already Approved: fire immediately
      dispatchApprovedOrderConfirm({!! json_encode($approvedOrderDl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!});
    @else
      // Order is currently Pending: poll status in real time.
      // The instant Admin clicks Approve in Website Orders, fire "purchase" without manual page reload!
      (function() {
        var pollTimer = null;
        var pollAttempts = 0;
        var maxAttempts = 120; // 120 * 2.5s = 5 minutes
        pollTimer = setInterval(function() {
          pollAttempts++;
          if (pollAttempts > maxAttempts) {
            clearInterval(pollTimer);
            return;
          }
          fetch('{{ route("frontend.order-status-check", $orderNumber) }}')
            .then(function(r) { return r.json(); })
            .then(function(res) {
              if (res && res.success && res.is_approved && res.order_datalayer) {
                clearInterval(pollTimer);
                dispatchApprovedOrderConfirm(res.order_datalayer);
              }
            })
            .catch(function(e) {});
        }, 2500);
      })();
    @endif
    // Clean cart in localStorage after successful order placement
    try {
      localStorage.removeItem('cb_cart');
      if (window.CameraCart) {
        CameraCart.clearState();
      }
    } catch(e) {
      console.warn('Cart clean error:', e);
    }

    // Note: Meta Pixel Purchase event is strictly triggered via Server CAPI upon Admin Approval in Website Orders
  });
</script>
<style>
@media print {
  body * {
    visibility: hidden;
  }
  .print-area, .print-area * {
    visibility: visible;
  }
  .print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    border: none !important;
    box-shadow: none !important;
  }
  .no-print {
    display: none !important;
  }
}
</style>
@endpush

