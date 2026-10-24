@extends('frontend.layouts.master')

@section('title', 'Shopping Cart - ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-16 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-6">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <span class="text-on-surface font-semibold">Shopping Cart</span>
  </nav>

  <!-- Page Heading -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl lg:text-3xl font-bold text-text-display">Your Production Gear Bag</h1>
      <p class="text-xs text-text-muted mt-1">Review your selected cinema equipment and calculate shipping</p>
    </div>
    <a href="{{ url('/') }}" class="hidden sm:flex items-center gap-1 text-xs text-primary font-bold hover:underline">
      <span class="material-symbols-outlined text-[16px]">arrow_back</span> Continue Shopping
    </a>
  </div>

  <!-- Free Shipping Progress Tracker -->
  <div class="bg-surface-card rounded-2xl p-4 border border-surface-border mb-8 shadow-xs">
    <div class="flex items-center justify-between text-xs font-bold text-on-surface mb-2">
      <span class="flex items-center gap-1.5 text-primary">
        <span class="material-symbols-outlined text-[18px]">local_shipping</span>
        <span>Express Delivery Across Bangladesh</span>
      </span>
      <span class="text-stock-available font-extrabold">AUTHENTIC GEAR GUARANTEE</span>
    </div>
    <div class="w-full h-2.5 bg-surface-container rounded-full overflow-hidden">
      <div class="h-full bg-primary rounded-full transition-all duration-500" style="width: 100%;"></div>
    </div>
  </div>

  <!-- Main 2-Column Cart Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" id="cart-main-container">
    
    <!-- LEFT COLUMN: Cart Items Table (8 Cols) -->
    <div class="lg:col-span-8 bg-surface-card rounded-3xl p-5 md:p-6 border border-surface-border shadow-xs">
      
      <!-- Table Header (Desktop) -->
      <div class="hidden md:grid grid-cols-12 gap-4 pb-4 border-b border-surface-border text-xs font-bold text-text-muted uppercase">
        <div class="col-span-6">Product Details</div>
        <div class="col-span-2 text-center">Unit Price</div>
        <div class="col-span-2 text-center">Quantity</div>
        <div class="col-span-2 text-right">Subtotal</div>
      </div>

      <!-- Dynamic Item Rows -->
      <div class="divide-y divide-surface-border" id="cart-table-body">
        <!-- Rendered by renderCartPage() -->
      </div>

      <!-- Cart Bottom Actions: Voucher & Clear -->
      <div class="pt-6 border-t border-surface-border flex flex-col sm:flex-row items-center justify-between gap-4 mt-2">
        <!-- Coupon Form -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <input id="coupon-input" type="text" placeholder="Enter Promo Code (e.g. CINE10)" class="bg-surface-container px-3.5 py-2.5 rounded-xl border border-surface-border text-xs focus:outline-none focus:border-primary uppercase font-bold" />
          <button onclick="applyCoupon()" class="bg-surface-container-low hover:bg-primary hover:text-white text-on-surface border border-surface-border text-xs font-bold px-4 py-2.5 rounded-xl transition-all">
            Apply
          </button>
        </div>

        <button onclick="CameraCart.clear()" class="text-xs text-text-muted hover:text-rec-red-base font-bold flex items-center gap-1 transition-colors">
          <span class="material-symbols-outlined text-[16px]">remove_shopping_cart</span> Clear Production Bag
        </button>
      </div>

    </div>

    <!-- RIGHT COLUMN: Order Summary & Checkout (4 Cols) -->
    <div class="lg:col-span-4 bg-surface-card rounded-3xl p-5 md:p-6 border border-surface-border shadow-xs sticky top-28 space-y-5">
      
      <h3 class="text-base font-bold text-text-display pb-3 border-b border-surface-border">
        Order Summary
      </h3>

      <!-- Calculations -->
      <div class="space-y-3 text-xs">
        <div class="flex items-center justify-between text-text-muted">
          <span>Subtotal (<span id="cart-summary-item-count">0 Items</span>):</span>
          <span id="cart-summary-subtotal" class="font-bold text-on-surface">৳ 0 TK</span>
        </div>
        
        <div class="flex items-center justify-between text-text-muted">
          <span>Delivery Fee:</span>
          <span class="text-stock-available font-bold" id="cart-summary-delivery">Calculated at Checkout</span>
        </div>

        <div id="discount-row" class="flex items-center justify-between text-emerald-600 hidden font-bold">
          <span>Promo Voucher Discount:</span>
          <span id="discount-val">- ৳ ২,০০০ TK</span>
        </div>

        <div class="flex items-center justify-between text-text-muted">
          <span>Official VAT / AIT:</span>
          <span class="text-on-surface font-medium">Included</span>
        </div>

        <div class="pt-3 border-t border-surface-border flex items-baseline justify-between">
          <span class="text-sm font-bold text-text-display">Total Amount:</span>
          <span id="cart-grand-total" class="text-xl font-extrabold text-primary">৳ 0 TK</span>
        </div>
      </div>

      <!-- EMI Callout -->
      <div class="p-3 bg-surface-container rounded-xl border border-surface-border text-[11px] text-text-muted flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[18px]">credit_card</span>
        <span>EMI available up to 36 months with all leading banks</span>
      </div>

      <!-- Checkout Button -->
      <a id="checkout-btn" href="{{ route('frontend.checkout') }}" class="w-full bg-primary hover:bg-primary-hover text-white font-bold text-sm py-3.5 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 text-center active:scale-95">
        <span class="material-symbols-outlined text-[18px]">lock</span>
        <span>Proceed to Secure Checkout</span>
      </a>

      <!-- Security Guarantees -->
      <div class="pt-4 border-t border-surface-border space-y-2 text-[11px] text-text-muted">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-stock-available text-[16px]">verified_user</span>
          <span>100% Genuine Importer Warranty</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[16px]">inventory_2</span>
          <span>Official Packaging with Warranty Card</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[16px]">support_agent</span>
          <span>Hotline Support: {{ $sitePhoneFormatted ?? '+880-1917-256-756' }}</span>
        </div>
      </div>

    </div>

  </div>

</div>
@endsection

@push('scripts')
<script>
  let appliedDiscount = 0;

  function renderCartPage() {
    const tableBody = document.getElementById('cart-table-body');
    const items = window.CameraCart ? CameraCart.get() : [];
    const count = window.CameraCart ? CameraCart.getCount() : 0;
    const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;

    const countEl = document.getElementById('cart-summary-item-count');
    if (countEl) countEl.textContent = `${count} Item${count > 1 ? 's' : ''}`;

    const subtotalEl = document.getElementById('cart-summary-subtotal');
    if (subtotalEl) subtotalEl.textContent = `৳ ${Math.round(subtotal).toLocaleString('en-US')} TK`;

    const grandTotal = Math.max(0, subtotal - appliedDiscount);
    const grandTotalEl = document.getElementById('cart-grand-total');
    if (grandTotalEl) grandTotalEl.textContent = `৳ ${Math.round(grandTotal).toLocaleString('en-US')} TK`;

    const checkoutBtn = document.getElementById('checkout-btn');

    if (!items || items.length === 0) {
      if (checkoutBtn) {
        checkoutBtn.classList.add('opacity-50', 'pointer-events-none');
      }
      tableBody.innerHTML = `
        <div class="py-16 text-center text-text-muted">
          <span class="material-symbols-outlined text-[54px] text-slate-300 block mb-3">shopping_bag</span>
          <h3 class="text-base font-bold text-slate-800">Your shopping cart is empty</h3>
          <p class="text-xs text-slate-500 mt-1 mb-5">Explore our cinema cameras, wireless audio, gimbals and more!</p>
          <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Explore Cinema Gear</span>
          </a>
        </div>
      `;
      return;
    }

    if (checkoutBtn) {
      checkoutBtn.classList.remove('opacity-50', 'pointer-events-none');
    }

    let html = '';
    items.forEach((item, idx) => {
      const unitPrice = parseFloat(item.price) || 0;
      const qty = parseInt(item.qty) || 1;
      const lineTotal = unitPrice * qty;

      const imgHtml = item.image
        ? `<img src="${item.image}" alt="${item.name}" class="max-h-full max-w-full object-contain" />`
        : `<span class="material-symbols-outlined text-primary text-[28px]">videocam</span>`;

      html += `
        <div class="cart-item-row py-4 flex flex-col md:grid md:grid-cols-12 gap-4 items-center">
          <div class="col-span-6 flex items-center gap-3 w-full">
            <a href="${item.url || '#'}" class="w-16 h-16 md:w-20 md:h-20 bg-surface-base rounded-xl p-2 shrink-0 border border-surface-border flex items-center justify-center">
              ${imgHtml}
            </a>
            <div class="min-w-0 flex-1">
              ${item.barcode ? `<span class="text-[10px] text-primary font-bold uppercase block">Code: ${item.barcode}</span>` : ''}
              <h3 class="text-xs md:text-sm font-bold text-text-display truncate">
                <a href="${item.url || '#'}" class="hover:text-primary transition-colors">${item.name}</a>
              </h3>
              <span class="text-[11px] text-text-muted block">Warranty: Official Warranty Included</span>
              <button onclick="CameraCart.remove(${idx})" class="text-[11px] text-rec-red-base hover:underline font-bold mt-1 flex items-center gap-0.5">
                <span class="material-symbols-outlined text-[14px]">delete</span> Remove
              </button>
            </div>
          </div>
          
          <div class="col-span-2 text-center text-xs font-bold text-on-surface">
            ৳ ${Math.round(unitPrice).toLocaleString('en-US')} TK
          </div>

          <div class="col-span-2 flex justify-center">
            <div class="flex items-center border border-surface-border bg-surface-container rounded-lg p-0.5">
              <button onclick="CameraCart.updateQty(${idx}, -1)" class="w-6 h-6 rounded bg-surface-card flex items-center justify-center text-xs font-bold hover:bg-primary hover:text-white transition-colors">-</button>
              <input type="text" value="${qty}" readonly class="w-8 text-center text-xs font-bold bg-transparent" />
              <button onclick="CameraCart.updateQty(${idx}, 1)" class="w-6 h-6 rounded bg-surface-card flex items-center justify-center text-xs font-bold hover:bg-primary hover:text-white transition-colors">+</button>
            </div>
          </div>

          <div class="col-span-2 text-right text-xs md:text-sm font-bold text-primary item-line-total">
            ৳ ${Math.round(lineTotal).toLocaleString('en-US')} TK
          </div>
        </div>
      `;
    });

    tableBody.innerHTML = html;
  }

  function applyCoupon() {
    const code = document.getElementById('coupon-input').value.trim().toUpperCase();
    if (code === 'CINE10' || code === 'CAMERABD' || code === 'CAMERABAZAR') {
      appliedDiscount = 2000;
      document.getElementById('discount-row').classList.remove('hidden');
      document.getElementById('discount-val').textContent = `- ৳ ${appliedDiscount.toLocaleString('en-US')} TK`;
      renderCartPage();
      showToast('Promo voucher applied: ৳ 2,000 discount!', 'success');
    } else {
      showToast('Invalid voucher code. Try CINE10', 'error');
    }
  }

  // GA4 / GTM DataLayer ViewCart Event
  function trackDataLayerViewCart() {
    if (typeof window.pushToDataLayer !== 'function') return;
    const items = window.CameraCart ? CameraCart.get() : [];
    if (!items || items.length === 0) return;

    const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;
    const dlCartItems = items.map(function(item, idx) {
      return {
        item_id: String(item.id || item.barcode || item.sku || item.name),
        item_name: item.name || 'Cinema Product',
        affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
        currency: 'BDT',
        price: parseFloat(item.price) || 0,
        quantity: parseInt(item.qty) || 1,
        index: idx + 1
      };
    });

    window.pushToDataLayer('view_cart', {
      currency: 'BDT',
      value: parseFloat(subtotal) || 0,
      items: dlCartItems
    });
  }

  // Listen to live cart updates
  window.addEventListener('cameracart:updated', function() {
    renderCartPage();
  });

  document.addEventListener('DOMContentLoaded', function() {
    trackDataLayerViewCart();
    renderCartPage();
  });
</script>
@endpush

