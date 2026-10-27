@extends('frontend.layouts.master')

@section('title', 'Secure Checkout - ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-16 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-6">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <a href="{{ route('frontend.cart') }}" class="hover:text-primary transition-colors">Cart</a>
    <span>/</span>
    <span class="text-on-surface font-semibold">Checkout</span>
  </nav>

  <div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-bold text-text-display">Checkout & Delivery Information</h1>
    <p class="text-xs text-text-muted mt-1">Provide your delivery address and choose your preferred payment method</p>
  </div>

  @if(session('error'))
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
      <div class="flex items-start sm:items-center gap-2.5">
        <span class="material-symbols-outlined text-[22px] text-amber-600 shrink-0">warning</span>
        <span class="leading-relaxed">{{ session('error') }}</span>
      </div>
      @if(session('whatsapp_url'))
        <a href="{{ session('whatsapp_url') }}" target="_blank" class="px-4 py-2.5 bg-[#25D366] hover:bg-[#20ba5a] text-white font-bold rounded-xl flex items-center gap-1.5 shadow-xs whitespace-nowrap shrink-0 transition-transform active:scale-95 text-xs">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.978-.275-.1-.476-.15-.676.15-.2.301-.776.978-.952 1.179-.175.2-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.894-.798-1.498-1.784-1.674-2.085-.175-.301-.019-.464.132-.614.136-.135.301-.351.452-.527.15-.175.2-.301.3-.501.1-.2.05-.376-.025-.526-.075-.15-.676-1.63-1.002-2.233-.25-.6-.5-.526-.676-.526-.175 0-.376-.025-.576-.025-.2 0-.526.075-.802.376-.276.301-1.053 1.028-1.053 2.508 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.141 4.544.718.31 1.279.496 1.716.635.721.23 1.377.197 1.896.12.578-.087 1.78-.727 2.03-1.43.25-.702.25-1.303.175-1.43-.075-.125-.276-.2-.576-.35zM12.04 21.78c-1.74 0-3.41-.46-4.88-1.33l-.35-.21-3.63.95.97-3.54-.23-.37a9.78 9.78 0 0 1-1.5-5.24c0-5.43 4.42-9.85 9.85-9.85 2.63 0 5.1 1.02 6.96 2.88a9.8 9.8 0 0 1 2.88 6.97c0 5.43-4.42 9.84-9.85 9.84zm0-17.84a7.99 7.99 0 0 0-8 8c0 1.58.46 3.1 1.33 4.41l.21.32-.57 2.08 2.13-.56.31.18c1.28.76 2.76 1.17 4.29 1.17 4.41 0 8-3.59 8-8s-3.59-8-8-8z"/></svg>
          <span>Chat on WhatsApp</span>
        </a>
      @endif
    </div>
  @endif

  <form action="{{ route('frontend.process-checkout') }}" method="POST" id="checkout-form" data-fbq-ignore="true">
    @csrf
    
    <!-- Hidden payload inputs for Cart, Shipping, Discount -->
    <input type="hidden" name="cart_items" id="cart-items-input" value="[]" />
    <input type="hidden" name="shipping_cost" id="shipping-cost-input" value="0" />
    <input type="hidden" name="discount" id="discount-input" value="0" />
    <input type="hidden" name="order_no" id="order-no-input" value="{{ $orderNo ?? ('CB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))) }}" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- LEFT COLUMN: Customer & Delivery Details (7 Cols) -->
      <div class="lg:col-span-7 space-y-5">
        
        <!-- Section 1: Customer Details & Address -->
        <div class="bg-surface-card rounded-3xl p-5 md:p-6 border border-surface-border shadow-xs">
          <div class="flex items-center gap-2.5 pb-3 border-b border-surface-border mb-4">
            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">1</div>
            <h2 class="text-base font-bold text-text-display">Customer & Delivery Information</h2>
          </div>

          <div class="space-y-4 text-xs">
            <!-- Full Name -->
            <div>
              <label class="block font-bold text-on-surface mb-1.5">
                Full Name / আপনার নাম <span class="text-primary font-bold">*</span>
              </label>
              <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="আপনার নাম লিখুন (e.g. Tanvir Ahmed)" class="w-full bg-surface-container rounded-xl p-3.5 border border-surface-border text-xs focus:outline-none focus:border-primary font-medium" />
            </div>

            <!-- Phone Number -->
            <div>
              <label class="block font-bold text-on-surface mb-1.5">
                Mobile Number / মোবাইল নাম্বার <span class="text-primary font-bold">*</span>
              </label>
              <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="01XXXXXXXXX" class="w-full bg-surface-container rounded-xl p-3.5 border border-surface-border text-xs focus:outline-none focus:border-primary font-medium" />
            </div>

            <!-- Delivery Address -->
            <div>
              <label class="block font-bold text-on-surface mb-1.5">
                Full Address / সম্পূর্ণ ডেলিভারি ঠিকানা <span class="text-primary font-bold">*</span>
              </label>
              <textarea name="delivery_address" id="address-input" rows="3" required placeholder="বাসা নং, রোড নং, এলাকা, থানা ও জেলার নাম লিখুন..." class="w-full bg-surface-container rounded-xl p-3.5 border border-surface-border text-xs focus:outline-none focus:border-primary font-medium">{{ old('delivery_address') }}</textarea>
            </div>

            <!-- Hidden consistency fields for Backend & Courier Processing -->
            <input type="hidden" name="city" id="city-input" value="{{ old('city', 'Dhaka') }}" />
            <input type="hidden" name="division" id="division-input" value="{{ old('division', 'Dhaka Division') }}" />
            <input type="hidden" name="email" value="" />
            <input type="hidden" name="order_notes" value="" />
          </div>
        </div>

        <!-- Section 2: Delivery Area & Charge -->
        <div class="bg-surface-card rounded-3xl p-5 md:p-6 border border-surface-border shadow-xs">
          <div class="flex items-center gap-2.5 pb-3 border-b border-surface-border mb-4">
            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">2</div>
            <h2 class="text-base font-bold text-text-display">ডেলিভারি এলাকা ও চার্জ (Delivery Charge)</h2>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <!-- Inside Dhaka: ৳ 100 -->
            <label id="zone-card-inside" onclick="setDeliveryZone('inside_dhaka')" class="relative flex items-center justify-between p-4 rounded-2xl border-2 border-primary bg-primary/5 cursor-pointer transition-all">
              <div class="flex items-center gap-3">
                <input type="radio" name="delivery_zone" value="inside_dhaka" checked class="w-4 h-4 text-primary focus:ring-primary pointer-events-none" />
                <div>
                  <span class="font-extrabold text-on-surface text-sm block">Inside Dhaka (ঢাকা সিটি)</span>
                  <span class="text-text-muted text-[11px]">১-২ দিনে দ্রুত হোম ডেলিভারি</span>
                </div>
              </div>
              <span class="font-extrabold text-primary text-sm whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-primary/20 shadow-xs">৳ 100 TK</span>
            </label>

            <!-- Outside Dhaka: ৳ 150 -->
            <label id="zone-card-outside" onclick="setDeliveryZone('outside_dhaka')" class="relative flex items-center justify-between p-4 rounded-2xl border border-surface-border bg-surface-container/60 cursor-pointer transition-all hover:border-primary">
              <div class="flex items-center gap-3">
                <input type="radio" name="delivery_zone" value="outside_dhaka" class="w-4 h-4 text-primary focus:ring-primary pointer-events-none" />
                <div>
                  <span class="font-extrabold text-on-surface text-sm block">Outside Dhaka (ঢাকার বাইরে)</span>
                  <span class="text-text-muted text-[11px]">সারা বাংলাদেশে কুরিয়ার ডেলিভারি</span>
                </div>
              </div>
              <span class="font-extrabold text-on-surface text-sm whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-surface-border shadow-xs">৳ 150 TK</span>
            </label>
          </div>

          <input type="hidden" name="delivery_method" id="delivery-method-input" value="Express Home Delivery (Inside Dhaka)" />
          <input type="hidden" name="payment_method" value="Cash On Delivery" />
        </div>

        <!-- Section 3: Cash On Delivery Badge -->
        <div class="bg-surface-card rounded-2xl p-4 border border-surface-border shadow-xs flex items-center justify-between gap-3 text-xs">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[22px]">payments</span>
            </div>
            <div>
              <span class="font-bold text-on-surface text-xs block">Cash On Delivery (ক্যাশ অন ডেলিভারি)</span>
              <span class="text-text-muted text-[11px]">পণ্য হাতে পেয়ে চেক করে সম্পূর্ণ মূল্য পরিশোধ করুন</span>
            </div>
          </div>
          <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg whitespace-nowrap">Cash On Delivery</span>
        </div>

      </div>

      <!-- RIGHT COLUMN: Order Review & Submit (5 Cols) -->
      <div class="lg:col-span-5 bg-surface-card rounded-3xl p-5 md:p-6 border border-surface-border shadow-xs sticky top-28 space-y-5">
        
        <h3 class="text-base font-bold text-text-display pb-3 border-b border-surface-border">
          Order Summary (<span id="checkout-item-count">0 Items</span>)
        </h3>

        <!-- Dynamic Mini items list -->
        <div class="space-y-3 max-h-64 overflow-y-auto pr-1 divide-y divide-surface-border/50" id="checkout-items-list">
          <!-- Rendered dynamically by renderCheckoutSummary() -->
        </div>

        <!-- Calculations -->
        <div class="space-y-2.5 pt-4 border-t border-surface-border text-xs">
          <div class="flex items-center justify-between text-text-muted">
            <span>Subtotal:</span>
            <span class="font-bold text-on-surface" id="checkout-subtotal">৳ 0 TK</span>
          </div>
          <div class="flex items-center justify-between text-text-muted">
            <span>Shipping Charge:</span>
            <span class="font-bold text-on-surface" id="checkout-shipping">৳ 100 TK</span>
          </div>
          <div class="flex items-center justify-between text-text-muted">
            <span>Estimated VAT/AIT:</span>
            <span class="text-on-surface font-medium">Included</span>
          </div>
          <div class="pt-3 border-t border-surface-border flex items-baseline justify-between">
            <span class="text-sm font-bold text-text-display">Payable Amount:</span>
            <span class="text-xl font-extrabold text-primary" id="checkout-total">৳ 0 TK</span>
          </div>
        </div>

        <!-- Terms Agreement -->
        <div class="pt-2">
          <label class="flex items-start gap-2 text-[11px] text-text-muted cursor-pointer">
            <input type="checkbox" checked required class="mt-0.5 rounded text-primary focus:ring-primary" />
            <span>I have read and agree to the <a href="#terms" class="text-primary underline">Terms & Conditions</a> and warranty policy of {{ $siteName ?? 'CAMERA BD' }}.</span>
          </label>
        </div>

        <!-- Confirm & Place Order Button -->
        <button type="submit" id="submit-order-btn" data-no-pixel="true" data-prevent-automatic-events="true" data-fbq-ignore="true" class="fb-no-track fbq-ignore _fbq_ignore w-full bg-primary hover:bg-primary-hover text-white font-extrabold text-base py-4 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
          <span class="material-symbols-outlined text-[20px]">check_circle</span>
          <span>Confirm & Place Order</span>
        </button>

        <!-- Guarantee Icons -->
        <div class="p-3 bg-surface-container rounded-xl border border-surface-border text-[11px] text-text-muted space-y-1">
          <div class="flex items-center gap-1.5 text-stock-available font-bold">
            <span class="material-symbols-outlined text-[15px]">verified</span> 100% Genuine Importer Product
          </div>
          <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[15px] text-primary">speed</span> Dispatch within 24 Hours
          </div>
        </div>

      </div>

    </div>
  </form>

</div>
@endsection

@push('scripts')
<script>
  let currentShippingCost = 100;
  let currentZone = 'inside_dhaka';

  function setDeliveryZone(zone, triggerFromUser = true) {
    currentZone = zone;
    const cardInside = document.getElementById('zone-card-inside');
    const cardOutside = document.getElementById('zone-card-outside');
    const radioInside = document.querySelector('input[name="delivery_zone"][value="inside_dhaka"]');
    const radioOutside = document.querySelector('input[name="delivery_zone"][value="outside_dhaka"]');
    const shipEl = document.getElementById('checkout-shipping');
    const deliveryMethodInput = document.getElementById('delivery-method-input');
    const cityInput = document.getElementById('city-input');
    const divisionInput = document.getElementById('division-input');

    if (zone === 'outside_dhaka') {
      currentShippingCost = 150;
      if (radioOutside) radioOutside.checked = true;
      if (radioInside) radioInside.checked = false;
      if (cardOutside) {
        cardOutside.className = 'relative flex items-center justify-between p-4 rounded-2xl border-2 border-primary bg-primary/5 cursor-pointer transition-all';
        const badge = cardOutside.querySelector('span:last-child');
        if (badge) badge.className = 'font-extrabold text-primary text-sm whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-primary/20 shadow-xs';
      }
      if (cardInside) {
        cardInside.className = 'relative flex items-center justify-between p-4 rounded-2xl border border-surface-border bg-surface-container/60 cursor-pointer transition-all hover:border-primary';
        const badge = cardInside.querySelector('span:last-child');
        if (badge) badge.className = 'font-extrabold text-on-surface text-sm whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-surface-border shadow-xs';
      }
      if (shipEl) shipEl.textContent = '৳ 150 TK';
      if (deliveryMethodInput) deliveryMethodInput.value = 'Courier Delivery (Outside Dhaka)';
      if (cityInput) cityInput.value = 'Outside Dhaka';
      if (divisionInput) divisionInput.value = 'Outside Dhaka';
    } else {
      currentShippingCost = 100;
      if (radioInside) radioInside.checked = true;
      if (radioOutside) radioOutside.checked = false;
      if (cardInside) {
        cardInside.className = 'relative flex items-center justify-between p-4 rounded-2xl border-2 border-primary bg-primary/5 cursor-pointer transition-all';
        const badge = cardInside.querySelector('span:last-child');
        if (badge) badge.className = 'font-extrabold text-primary text-sm whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-primary/20 shadow-xs';
      }
      if (cardOutside) {
        cardOutside.className = 'relative flex items-center justify-between p-4 rounded-2xl border border-surface-border bg-surface-container/60 cursor-pointer transition-all hover:border-primary';
        const badge = cardOutside.querySelector('span:last-child');
        if (badge) badge.className = 'font-extrabold text-on-surface text-sm whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-surface-border shadow-xs';
      }
      if (shipEl) shipEl.textContent = '৳ 100 TK';
      if (deliveryMethodInput) deliveryMethodInput.value = 'Express Home Delivery (Inside Dhaka)';
      if (cityInput) cityInput.value = 'Dhaka';
      if (divisionInput) divisionInput.value = 'Dhaka Division';
    }

    renderCheckoutSummary();

    if (triggerFromUser) {
      trackDataLayerAddShippingInfo();
      pushCustomerInfoToDataLayer('customer_info_updated');
    }
  }

  function autoDetectDeliveryZoneFromAddress() {
    const addressInput = document.getElementById('address-input');
    if (!addressInput) return;
    const text = addressInput.value.toLowerCase().trim();
    if (!text || text.length < 3) return;

    const outsideKeywords = [
      'chittagong', 'chattogram', 'sylhet', 'rajshahi', 'khulna', 'barishal', 'barisal',
      'rangpur', 'mymensingh', 'cumilla', 'comilla', 'bogra', 'bogura', 'jessore', 'jashore',
      'cox', 'cox\'s bazar', 'noakhali', 'feni', 'dinajpur', 'tangail', 'brahmanbaria',
      'kushtia', 'pabna', 'sirajganj', 'jamalpur', 'faridpur', 'naogaon', 'habiganj',
      'moulvibazar', 'sunamganj', 'patuakhali', 'bhola', 'chandpur', 'lakshmipur', 'kishoreganj',
      'netrokona', 'sherpur', 'narsingdi', 'manikganj', 'munshiganj', 'gopalganj', 'madaripur',
      'shariatpur', 'rajbari', 'magura', 'narail', 'bagerhat', 'satkhira', 'chuadanga',
      'meherpur', 'jhinaidah', 'jhenaidah', 'joypurhat', 'chapainawabganj', 'nawabganj',
      'kurigram', 'gaibandha', 'lalmonirhat', 'nilphamari', 'panchagarh', 'thakurgaon',
      'barguna', 'jhalokati', 'pirojpur', 'bandarban', 'khagrachhari', 'rangamati',
      'চট্টগ্রাম', 'সিলেট', 'রাজশাহী', 'খুলনা', 'বরিশাল', 'রংপুর', 'ময়মনসিংহ', 'কুমিল্লা', 'বগুড়া', 'যশোর', 'কক্সবাজার'
    ];

    const isDhakaExplicit = text.includes('dhaka') || text.includes('ঢাকা') || text.includes('uttara') || text.includes('mirpur') || text.includes('gulshan') || text.includes('banani') || text.includes('dhanmondi') || text.includes('motijheel') || text.includes('mohammadpur') || text.includes('badda') || text.includes('উত্তরা') || text.includes('মিরপুর') || text.includes('গুলশান') || text.includes('ধানমন্ডি');

    const matchesOutside = outsideKeywords.some(w => text.includes(w));
    if (matchesOutside && !isDhakaExplicit) {
      if (currentZone !== 'outside_dhaka') {
        setDeliveryZone('outside_dhaka', false);
      }
    }
  }

  function renderCheckoutSummary() {
    const items = window.CameraCart ? CameraCart.get() : [];
    const count = window.CameraCart ? CameraCart.getCount() : 0;
    const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;

    const listEl = document.getElementById('checkout-items-list');
    const countEl = document.getElementById('checkout-item-count');
    const subtotalEl = document.getElementById('checkout-subtotal');
    const totalEl = document.getElementById('checkout-total');
    const cartInput = document.getElementById('cart-items-input');
    const shippingInput = document.getElementById('shipping-cost-input');
    const submitBtn = document.getElementById('submit-order-btn');

    if (countEl) countEl.textContent = `${count} Item${count > 1 ? 's' : ''}`;
    if (subtotalEl) subtotalEl.textContent = `৳ ${Math.round(subtotal).toLocaleString('en-US')} TK`;

    // Set hidden inputs
    if (cartInput) cartInput.value = JSON.stringify(items);
    if (shippingInput) shippingInput.value = currentShippingCost;

    const grandTotal = subtotal + currentShippingCost;
    if (totalEl) totalEl.textContent = `৳ ${Math.round(grandTotal).toLocaleString('en-US')} TK`;

    if (!items || items.length === 0) {
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'pointer-events-none');
      }
      if (listEl) {
        listEl.innerHTML = `
          <div class="py-8 text-center text-xs text-text-muted">
            <span class="material-symbols-outlined text-[36px] text-slate-300 block mb-1">shopping_bag</span>
            <p class="font-bold text-slate-700">No items in your cart</p>
            <a href="{{ url('/') }}" class="text-primary font-bold hover:underline mt-2 inline-block">Start shopping now →</a>
          </div>
        `;
      }
      return;
    }

    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.classList.remove('opacity-50', 'pointer-events-none');
    }

    if (listEl) {
      let html = '';
      items.forEach(item => {
        const unitPrice = parseFloat(item.price) || 0;
        const qty = parseInt(item.qty) || 1;
        const lineTotal = unitPrice * qty;

        const imgHtml = item.image
          ? `<img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-contain bg-surface-base rounded-lg p-1 border border-surface-border shrink-0" />`
          : `<div class="w-12 h-12 rounded-lg bg-surface-base border border-surface-border flex items-center justify-center text-primary shrink-0"><span class="material-symbols-outlined text-[20px]">videocam</span></div>`;

        html += `
          <div class="flex items-center gap-3 text-xs pt-2.5 first:pt-0">
            ${imgHtml}
            <div class="flex-1 min-w-0">
              <h4 class="font-bold text-on-surface truncate">${item.name}</h4>
              <span class="text-text-muted text-[11px]">Qty: ${qty} × ৳ ${Math.round(unitPrice).toLocaleString('en-US')}</span>
            </div>
            <span class="font-bold text-primary whitespace-nowrap">৳ ${Math.round(lineTotal).toLocaleString('en-US')}</span>
          </div>
        `;
      });
      listEl.innerHTML = html;
    }
  }

  // Real-time Address Input for Smart Zone Detection
  const addressInput = document.getElementById('address-input');
  if (addressInput) {
    addressInput.addEventListener('input', autoDetectDeliveryZoneFromAddress);
  }

  // -------------------------------------------------------------
  // DataLayer Customer Data Extractor & Dispatcher
  // -------------------------------------------------------------
  function getCustomerData() {
    const nameEl = document.querySelector('input[name="customer_name"]');
    const phoneEl = document.querySelector('input[name="phone"]');
    const emailEl = document.querySelector('input[name="email"]');
    const divisionEl = document.querySelector('select[name="division"]');
    const cityEl = document.querySelector('input[name="city"]');
    const addressEl = document.querySelector('textarea[name="delivery_address"]');
    const notesEl = document.querySelector('input[name="order_notes"]');

    const fullName = (nameEl ? nameEl.value : '').trim();
    const phone = (phoneEl ? phoneEl.value : '').trim();
    const email = (emailEl ? emailEl.value : '').trim();
    const division = (divisionEl ? divisionEl.value : '').trim();
    const city = (cityEl ? cityEl.value : '').trim();
    const address = (addressEl ? addressEl.value : '').trim();
    const notes = (notesEl ? notesEl.value : '').trim();

    // Split First & Last Name
    let firstName = fullName;
    let lastName = '';
    if (fullName.includes(' ')) {
      const parts = fullName.split(' ');
      firstName = parts[0];
      lastName = parts.slice(1).join(' ');
    }

    // Format phone (+88 for BD)
    let formattedPhone = phone;
    const digits = phone.replace(/[^0-9]/g, '');
    if (digits.length === 11 && digits.startsWith('01')) {
      formattedPhone = '+88' + digits;
    } else if (digits.length === 13 && digits.startsWith('8801')) {
      formattedPhone = '+' + digits;
    }

    const customerObj = {
      name: fullName,
      customer_name: fullName,
      first_name: firstName,
      last_name: lastName,
      phone: phone,
      customer_phone: phone,
      phone_number: formattedPhone,
      email: email,
      customer_email: email,
      division: division,
      customer_division: division,
      city: city,
      customer_city: city,
      district: city,
      address: address,
      customer_address: address,
      delivery_address: address,
      street: address,
      country: 'Bangladesh',
      country_code: 'BD',
      order_notes: notes
    };

    const userDataObj = {
      email: email || undefined,
      phone_number: formattedPhone || undefined,
      address: {
        first_name: firstName || undefined,
        last_name: lastName || undefined,
        street: address || undefined,
        city: city || undefined,
        region: division || undefined,
        country: 'BD'
      }
    };

    return {
      customer: customerObj,
      customer_info: customerObj,
      user_data: userDataObj,
      customer_name: fullName,
      customer_phone: phone,
      customer_email: email,
      customer_city: city,
      customer_division: division,
      customer_address: address
    };
  }

  // Real-time Push to DataLayer when customer inputs change
  let customerInfoDebounceTimer = null;
  function pushCustomerInfoToDataLayer(triggerEvent) {
    try {
      const cust = getCustomerData();
      if (!cust.customer_name && !cust.customer_phone && !cust.customer_email && !cust.customer_address) {
        return;
      }

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        event: triggerEvent || 'customer_info_updated',
        customer: cust.customer,
        customer_info: cust.customer_info,
        user_data: cust.user_data,
        customer_name: cust.customer_name,
        customer_phone: cust.customer_phone,
        customer_email: cust.customer_email,
        customer_city: cust.customer_city,
        customer_division: cust.customer_division,
        customer_address: cust.customer_address
      });
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('[DataLayer Customer Info Pushed]', cust);
      }
    } catch(err) {
      console.warn('[DataLayer] Customer Info push error:', err);
    }
  }

  // Attach event listeners to all customer input fields
  function setupCustomerInputListeners() {
    const selector = 'input[name="customer_name"], input[name="phone"], textarea[name="delivery_address"], input[name="delivery_zone"]';
    const inputs = document.querySelectorAll(selector);

    inputs.forEach(function(input) {
      input.addEventListener('input', function() {
        clearTimeout(customerInfoDebounceTimer);
        customerInfoDebounceTimer = setTimeout(function() {
          pushCustomerInfoToDataLayer('customer_info_updated');
        }, 500);
      });

      input.addEventListener('change', function() {
        pushCustomerInfoToDataLayer('customer_info_updated');
        trackDataLayerAddShippingInfo();
      });

      input.addEventListener('blur', function() {
        pushCustomerInfoToDataLayer('customer_info_updated');
      });
    });
  }

  // Track add_shipping_info with customer info
  let lastTrackedShippingTier = '';
  function trackDataLayerAddShippingInfo() {
    try {
      if (typeof window.pushToDataLayer !== 'function') return;
      const items = window.CameraCart ? CameraCart.get() : [];
      if (!items || items.length === 0) return;

      const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;
      const grandTotal = subtotal + currentShippingCost;
      const cust = getCustomerData();
      const deliveryMethodInput = document.getElementById('delivery-method-input');
      const shippingTier = deliveryMethodInput ? deliveryMethodInput.value : (currentShippingCost === 100 ? 'Express Home Delivery (Inside Dhaka)' : 'Courier Delivery (Outside Dhaka)');

      if (lastTrackedShippingTier === shippingTier && !cust.customer_name && !cust.customer_phone) return;
      lastTrackedShippingTier = shippingTier;

      const dlCheckoutItems = items.map(function(item, idx) {
        return {
          item_id: String(item.id || item.barcode || item.name),
          item_name: item.name || 'Cinema Product',
          affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
          currency: 'BDT',
          price: parseFloat(item.price) || 0,
          quantity: parseInt(item.qty) || 1,
          index: idx + 1
        };
      });

      window.pushToDataLayer('add_shipping_info', {
        currency: 'BDT',
        value: parseFloat(grandTotal) || 0,
        shipping_tier: shippingTier,
        items: dlCheckoutItems
      }, {
        customer: cust.customer,
        customer_info: cust.customer_info,
        user_data: cust.user_data,
        customer_name: cust.customer_name,
        customer_phone: cust.customer_phone,
        customer_email: cust.customer_email,
        customer_city: cust.customer_city,
        customer_division: cust.customer_division,
        customer_address: cust.customer_address
      });
    } catch(e) {
      console.warn('[DataLayer] AddShippingInfo error:', e);
    }
  }

  // Form submit check with strict double-click guard & DataLayer Purchase tracking
  let isCheckoutSubmitting = false;
  const checkoutFormEl = document.getElementById('checkout-form');
  const submitBtnEl = document.getElementById('submit-order-btn');

  if (submitBtnEl) {
    submitBtnEl.addEventListener('click', function(e) {
      if (isCheckoutSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
      }
    });
  }

  // Reset lock if page restored from cache (e.g. back button)
  window.addEventListener('pageshow', function() {
    isCheckoutSubmitting = false;
    if (submitBtnEl) {
      submitBtnEl.style.pointerEvents = 'auto';
      submitBtnEl.classList.remove('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
    }
  });

  if (checkoutFormEl) {
    checkoutFormEl.addEventListener('submit', function(e) {
      if (isCheckoutSubmitting) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
      }

      const items = window.CameraCart ? CameraCart.get() : [];
      if (!items || items.length === 0) {
        e.preventDefault();
        alert('Your cart is empty! Please add products before checking out.');
        return false;
      }

      // Check HTML5 validity before locking form
      if (checkoutFormEl.checkValidity && !checkoutFormEl.checkValidity()) {
        return true;
      }

      // Set submitting flag immediately to prevent double submissions from fast clicks
      isCheckoutSubmitting = true;

      // Visual loading state without synchronous disabled=true (disabling synchronously can cancel form POST in mobile WebKit/Chrome)
      if (submitBtnEl) {
        submitBtnEl.style.pointerEvents = 'none';
        submitBtnEl.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
        submitBtnEl.innerHTML = `
          <span class="inline-block animate-spin mr-1">⏳</span>
          <span>Processing Order... Please wait</span>
        `;
        setTimeout(function() {
          try { submitBtnEl.disabled = true; } catch(e) {}
        }, 300);
      }

      const cartInput = document.getElementById('cart-items-input');
      if (cartInput) {
        cartInput.value = JSON.stringify(items);
      }
      const shippingInput = document.getElementById('shipping-cost-input');
      if (shippingInput) {
        shippingInput.value = currentShippingCost;
      }

      const orderNoInput = document.getElementById('order-no-input');
      const orderKey = orderNoInput ? orderNoInput.value : ('CB-' + Date.now());

      // Push purchase, checkout_submit and add_payment_info to DataLayer before submission
      try {
        const cust = getCustomerData();
        const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;
        const grandTotal = subtotal + currentShippingCost;
        const dlCheckoutItems = items.map(function(item, idx) {
          return {
            item_id: String(item.id || item.barcode || item.name),
            item_name: item.name || 'Cinema Product',
            affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
            currency: 'BDT',
            price: parseFloat(item.price) || 0,
            quantity: parseInt(item.qty) || 1,
            index: idx + 1
          };
        });

        // 1. Mark dispatched in localStorage to prevent duplicate firing on confirmation page
        try {
          localStorage.setItem('cb_dispatched_order_' + orderKey, Date.now());
        } catch(e) {}

        // 2. Dispatch Purchase Event to DataLayer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null }); // Clear previous ecommerce object per Google standard
        window.dataLayer.push({
          event: 'purchase',
          order_id: orderKey,
          transaction_id: orderKey,
          value: parseFloat(grandTotal) || 0,
          currency: 'BDT',
          shipping: currentShippingCost,
          tax: 0,
          customer_name: cust.customer_name,
          customer_phone: cust.customer_phone,
          phone_number: cust.customer_phone,
          customer_email: cust.customer_email,
          customer_city: cust.customer_city,
          customer_division: cust.customer_division,
          customer_address: cust.customer_address,
          customer: cust.customer,
          customer_info: cust.customer_info,
          user_data: cust.user_data,
          items: dlCheckoutItems,
          ecommerce: {
            transaction_id: orderKey,
            value: parseFloat(grandTotal) || 0,
            currency: 'BDT',
            shipping: currentShippingCost,
            tax: 0,
            items: dlCheckoutItems
          }
        });

        // 3. Direct Meta Pixel (Browser) Purchase Event Dispatch ONLY when GTM is not active
        if (!window._hasGtm && typeof window.fbq === 'function') {
          var fbContents = [];
          var fbContentIds = [];
          dlCheckoutItems.forEach(function(item) {
            fbContentIds.push(item.item_id);
            fbContents.push({
              id: item.item_id,
              quantity: item.quantity,
              item_price: item.price
            });
          });
          window.fbq('track', 'Purchase', {
            content_type: 'product',
            contents: fbContents,
            content_ids: fbContentIds,
            value: parseFloat(grandTotal) || 0,
            currency: 'BDT',
            num_items: fbContents.length
          }, {
            eventID: orderKey
          });
        }

        console.log('%c✅ [DataLayer] "purchase" event fired on checkout submit! Order: #' + orderKey, 'background: #10b981; color: white; font-weight: bold; padding: 6px 12px; border-radius: 4px;');

        // Also push add_payment_info and checkout_submit for full funnel tracking
        if (typeof window.pushToDataLayer === 'function') {
          window.pushToDataLayer('add_payment_info', {
            currency: 'BDT',
            value: parseFloat(grandTotal) || 0,
            payment_type: 'Cash On Delivery',
            items: dlCheckoutItems
          }, {
            customer: cust.customer,
            customer_info: cust.customer_info,
            user_data: cust.user_data,
            customer_name: cust.customer_name,
            customer_phone: cust.customer_phone,
            customer_email: cust.customer_email,
            customer_city: cust.customer_city,
            customer_division: cust.customer_division,
            customer_address: cust.customer_address
          });

          window.pushToDataLayer('checkout_submit', {
            currency: 'BDT',
            value: parseFloat(grandTotal) || 0,
            shipping: currentShippingCost,
            payment_type: 'Cash On Delivery',
            items: dlCheckoutItems
          }, {
            customer: cust.customer,
            customer_info: cust.customer_info,
            user_data: cust.user_data,
            customer_name: cust.customer_name,
            customer_phone: cust.customer_phone,
            customer_email: cust.customer_email,
            customer_city: cust.customer_city,
            customer_division: cust.customer_division,
            customer_address: cust.customer_address
          });
        }
      } catch(err) {
        console.warn('[DataLayer] Checkout submit tracking error:', err);
      }
    });
  }

  // Listen for live cart events
  window.addEventListener('cameracart:updated', function() {
    renderCheckoutSummary();
  });

  // GA4 / GTM DataLayer BeginCheckout Event Tracking with Customer Data
  function trackDataLayerBeginCheckout() {
    try {
      if (typeof window.pushToDataLayer !== 'function') return;
      const items = window.CameraCart ? CameraCart.get() : [];
      if (!items || items.length === 0) return;

      const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;
      const cust = getCustomerData();
      const dlCheckoutItems = items.map(function(item, idx) {
        return {
          item_id: String(item.id || item.barcode || item.name),
          item_name: item.name || 'Cinema Product',
          affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
          currency: 'BDT',
          price: parseFloat(item.price) || 0,
          quantity: parseInt(item.qty) || 1,
          index: idx + 1
        };
      });

      window.pushToDataLayer('begin_checkout', {
        currency: 'BDT',
        value: parseFloat(subtotal) || 0,
        items: dlCheckoutItems
      }, {
        customer: cust.customer,
        customer_info: cust.customer_info,
        user_data: cust.user_data,
        customer_name: cust.customer_name,
        customer_phone: cust.customer_phone,
        customer_email: cust.customer_email,
        customer_city: cust.customer_city,
        customer_division: cust.customer_division,
        customer_address: cust.customer_address
      });
    } catch(e) {
      console.warn('[DataLayer] BeginCheckout error:', e);
    }
  }

  // Meta Pixel InitiateCheckout Event Tracking (Browser + Server CAPI)
  function trackMetaPixelInitiateCheckout() {
    try {
      const items = window.CameraCart ? CameraCart.get() : [];
      if (!items || items.length === 0) {
        return; // Do not fire InitiateCheckout if cart is empty
      }

      const subtotal = window.CameraCart ? CameraCart.getSubtotal() : 0;
      const count = window.CameraCart ? CameraCart.getCount() : 0;

      const contentIds = items.map(i => String(i.id || i.barcode || i.name)).filter(Boolean);
      const contents = items.map(i => ({
        id: String(i.id || i.barcode || i.name),
        quantity: parseInt(i.qty) || 1,
        item_price: parseFloat(i.price) || 0
      }));

      const eventId = 'initiate_checkout_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
      const customPayload = {
        content_name: 'Checkout Page',
        content_category: 'E-commerce Checkout',
        content_ids: contentIds.length > 0 ? contentIds : ['CHECKOUT'],
        contents: contents,
        content_type: 'product',
        value: parseFloat(subtotal) || 0,
        num_items: count || 1,
        currency: 'BDT'
      };

      // Direct Meta Pixel & CAPI (Only if GTM is not active, otherwise GTM handles it via begin_checkout DataLayer)
      if (!window._hasGtm) {
        if (typeof fbq === 'function') {
          fbq('track', 'InitiateCheckout', customPayload, { eventID: eventId });
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        fetch('{{ route("meta-capi.track") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            event_name: 'InitiateCheckout',
            event_id: eventId,
            custom_data: customPayload,
            source_url: window.location.href
          })
        }).catch(e => console.debug('CAPI async notice:', e));
      }

    } catch(err) {
      console.warn('Meta Pixel InitiateCheckout error:', err);
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    setDeliveryZone('inside_dhaka', false);
    renderCheckoutSummary();
    setupCustomerInputListeners();
    pushCustomerInfoToDataLayer('customer_info_updated');
    trackDataLayerBeginCheckout();
    trackMetaPixelInitiateCheckout();
  });
</script>
@endpush

