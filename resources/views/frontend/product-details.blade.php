@extends('frontend.layouts.master')

@section('title', ($product['name'] ?? 'Product Details') . ' - ' . ($siteName ?? 'CAMERA BD'))

@push('styles')
<style>
  .product-rich-description {
    color: inherit;
    line-height: 1.75;
  }
  .product-rich-description img {
    max-width: 100% !important;
    height: auto !important;
    border-radius: 0.75rem;
    margin: 1rem 0;
    display: inline-block;
  }
  .product-rich-description table {
    width: 100% !important;
    border-collapse: collapse;
    margin: 1rem 0;
  }
  .product-rich-description table td, .product-rich-description table th {
    border: 1px solid rgba(148, 163, 184, 0.2);
    padding: 0.5rem 0.75rem;
  }
  .product-rich-description ul {
    list-style-type: disc !important;
    margin-left: 1.5rem !important;
    margin-bottom: 1rem !important;
  }
  .product-rich-description ol {
    list-style-type: decimal !important;
    margin-left: 1.5rem !important;
    margin-bottom: 1rem !important;
  }
  .product-rich-description p {
    margin-bottom: 0.75rem;
  }
  .product-rich-description h1, .product-rich-description h2, .product-rich-description h3, .product-rich-description h4, .product-rich-description h5, .product-rich-description h6 {
    font-weight: 700;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
  }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-12 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-6 flex-wrap">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <a href="{{ url('/') }}#all-categories" class="hover:text-primary transition-colors">{{ $product['category'] ?? 'Gear' }}</a>
    <span>/</span>
    <span class="text-on-surface font-semibold truncate">{{ $product['name'] ?? 'Product' }}</span>
  </nav>

  <!-- Product Main Section (2 Columns on Desktop) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 bg-surface-card rounded-3xl p-5 md:p-8 border border-surface-border shadow-xs">
    
    <!-- LEFT COLUMN: Image Gallery -->
    <div class="lg:col-span-6 flex flex-col gap-4">
      <!-- Main Featured Image -->
      <div class="relative bg-surface-base rounded-2xl p-6 md:p-10 aspect-square flex items-center justify-center border border-surface-border overflow-hidden group">
        @if(!empty($product['discount']))
          <span class="absolute top-4 left-4 z-10 bg-rec-red-base text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-sm">
            -{{ $product['discount'] }}% Off
          </span>
        @endif
        
        <button onclick="toggleWishlist(this, '{{ addslashes($product['name']) }}', {{ (float)$product['price'] }}, '{{ addslashes($product['images'][0] ?? '') }}', '{{ $product['slug'] ?? ($product['id'] ?? '') }}', '{{ $product['id'] ?? '' }}', '{{ addslashes($product['specs']['Brand'] ?? 'CAMERA BD') }}')" class="wishlist-btn absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/90 shadow-sm flex items-center justify-center text-text-muted hover:text-red-500 transition-colors cursor-pointer" data-wishlist-product="{{ addslashes($product['name']) }}" data-wishlist-id="{{ $product['id'] ?? '' }}">
          <span class="material-symbols-outlined text-[22px]">favorite_border</span>
        </button>

        <img id="main-product-img" src="{{ $product['images'][0] ?? 'https://via.placeholder.com/600' }}" alt="{{ $product['name'] }}" class="max-h-full max-w-full object-contain transition-all duration-500 group-hover:scale-105" fetchpriority="high" loading="eager" decoding="async" />
      </div>

      <!-- Thumbnail Selector Carousel -->
      @if(isset($product['images']) && count($product['images']) > 1)
        <div class="flex items-center gap-3 overflow-x-auto no-scrollbar py-1">
          @foreach($product['images'] as $index => $img)
            <button onclick="changeMainImage('{{ $img }}', this)" class="thumbnail-btn w-20 h-20 rounded-xl p-2 bg-surface-base border-2 {{ $index === 0 ? 'border-primary' : 'border-surface-border' }} hover:border-primary shrink-0 transition-all">
              <img src="{{ $img }}" alt="Thumbnail {{ $index+1 }}" class="w-full h-full object-contain" loading="lazy" decoding="async" />
            </button>
          @endforeach
        </div>
      @endif

      <!-- Quantity and Action Buttons (Add to Cart & Order Now) -->
      <div class="space-y-3 pt-3">
        <div class="flex items-center gap-3">
          <!-- Quantity Counter -->
          <div class="flex items-center border border-surface-border bg-surface-container rounded-xl p-1 shrink-0">
            <button type="button" onclick="decrementQty()" class="w-8 h-8 rounded-lg bg-surface-card flex items-center justify-center text-on-surface hover:bg-primary hover:text-white transition-colors font-bold select-none cursor-pointer">-</button>
            <input type="number" value="1" min="1" max="10" class="product-qty-input w-12 text-center text-sm font-bold bg-transparent focus:outline-none" oninput="syncQty(this.value)" />
            <button type="button" onclick="incrementQty()" class="w-8 h-8 rounded-lg bg-surface-card flex items-center justify-center text-on-surface hover:bg-primary hover:text-white transition-colors font-bold select-none cursor-pointer">+</button>
          </div>

          <!-- Add to Cart -->
          <button type="button" onclick="handleAddToCart()" class="flex-1 bg-primary hover:bg-primary-hover text-white font-bold text-sm py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
            <span>Add to Cart</span>
          </button>
        </div>

        <!-- Direct Order Now & Hotline -->
        <div class="flex gap-2">
          <button type="button" onclick="handleBuyNow()" class="flex-1 bg-rec-red-base hover:bg-rec-red-hover text-white font-bold text-sm py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 text-center cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">bolt</span>
            <span>Order Now</span>
          </button>
          <a href="tel:{{ $whatsappDigits ?? '8801917256756' }}" class="px-4 py-3 bg-surface-container hover:bg-surface-container-low border border-surface-border rounded-xl text-xs font-bold text-on-surface flex items-center justify-center gap-1.5 transition-colors shrink-0">
            <span class="material-symbols-outlined text-primary text-[18px]">support_agent</span>
            <span>Call Hotline</span>
          </a>
        </div>
      </div>

      <!-- Official Guarantee Perks (Moved Below Action Buttons) -->
      <div class="grid grid-cols-2 gap-3 pt-3 border-t border-surface-border">
        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low border border-surface-border">
          <span class="material-symbols-outlined text-primary text-[24px]">verified</span>
          <div>
            <h5 class="text-xs font-bold text-on-surface">100% Genuine</h5>
            <p class="text-[11px] text-text-muted">Official Channel Warranty</p>
          </div>
        </div>
        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low border border-surface-border">
          <span class="material-symbols-outlined text-primary text-[24px]">local_shipping</span>
          <div>
            <h5 class="text-xs font-bold text-on-surface">Express Delivery</h5>
            <p class="text-[11px] text-text-muted">1-3 Days Nationwide</p>
          </div>
        </div>
        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low border border-surface-border">
          <span class="material-symbols-outlined text-primary text-[24px]">credit_card</span>
          <div>
            <h5 class="text-xs font-bold text-on-surface">0% EMI Available</h5>
            <p class="text-[11px] text-text-muted">36 Partner Banks</p>
          </div>
        </div>
        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low border border-surface-border">
          <span class="material-symbols-outlined text-primary text-[24px]">published_with_changes</span>
          <div>
            <h5 class="text-xs font-bold text-on-surface">7 Days Replacement</h5>
            <p class="text-[11px] text-text-muted">For Hardware Defects</p>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN: Product Details & Purchase Controls -->
    <div class="lg:col-span-6 flex flex-col justify-between">
      <div>
        <!-- Brand & SKU -->
        <div class="flex items-center justify-between gap-2 mb-2">
          <span class="bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
            {{ $product['brand'] ?? 'Official Brand' }}
          </span>
          <span class="text-xs text-text-muted font-medium">SKU: {{ $product['sku'] ?? 'CB-PRO-001' }}</span>
        </div>

        <!-- Product Name -->
        <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-text-display mb-3 leading-tight">
          {{ $product['name'] }}
        </h1>

        <!-- Rating & Stock Info -->
        <div class="flex items-center gap-4 text-xs mb-4 pb-4 border-b border-surface-border flex-wrap">
          <div class="flex items-center gap-1 text-amber-500 font-bold">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
            <span>{{ $product['rating'] ?? 5.0 }}</span>
            <span class="text-text-muted font-normal">({{ $product['reviews_count'] ?? 10 }} Customer Reviews)</span>
          </div>
          <span class="text-surface-border">|</span>
          <div class="flex items-center gap-1 text-stock-available font-bold">
            <span class="w-2 h-2 rounded-full bg-stock-available animate-pulse"></span>
            <span>In Stock (Baitul Mukarram & Bashundhara City)</span>
          </div>
        </div>

        <!-- Price Block -->
        <div class="bg-surface-container-low p-4 rounded-2xl border border-surface-border mb-6">
          <div class="flex items-baseline gap-3 flex-wrap">
            <span class="text-2xl md:text-3xl font-extrabold text-primary">
              ৳ {{ number_format((float)($product['price'] ?? 0)) }} TK
            </span>
            @if(!empty($product['old_price']))
              <span class="text-base text-text-muted line-through">
                ৳ {{ number_format($product['old_price']) }} TK
              </span>
              <span class="bg-rec-red-base/10 text-rec-red-base text-xs font-bold px-2 py-0.5 rounded-full">
                Save ৳ {{ number_format($product['old_price'] - $product['price']) }}
              </span>
            @endif
          </div>

          <!-- EMI Breakdown -->
          <div class="mt-2.5 pt-2.5 border-t border-surface-border/60 flex items-center justify-between text-xs">
            <span class="text-text-muted flex items-center gap-1">
              <span class="material-symbols-outlined text-[16px] text-primary">payments</span>
              EMI from <strong class="text-on-surface font-bold">৳ {{ number_format(round($product['price'] / 36)) }}/month</strong> (Up to 36 Months)
            </span>
            <a href="#emi-plans" class="text-primary hover:underline font-bold">View EMI Plans</a>
          </div>
        </div>

        <!-- Key Features Bullet List -->
        <div class="mb-6">
          <h4 class="text-xs uppercase font-extrabold text-text-muted tracking-wider mb-2">Key Highlights:</h4>
          <ul class="space-y-1.5 text-xs text-on-surface">
            @foreach(($product['features'] ?? []) as $feat)
              <li class="flex items-start gap-2">
                <span class="material-symbols-outlined text-primary text-[16px] shrink-0 mt-0.5">check_circle</span>
                <span>{{ $feat }}</span>
              </li>
            @endforeach
          </ul>
        </div>

        <!-- Variant Selector Example -->
        <div class="mb-6">
          <label class="block text-xs uppercase font-extrabold text-text-muted tracking-wider mb-2">Configuration / Package:</label>
          <div class="grid grid-cols-2 gap-2.5">
            <button class="package-btn p-3 rounded-xl border-2 border-primary bg-primary/5 text-left text-xs transition-all">
              <span class="font-bold block text-on-surface">Standard Package</span>
              <span class="text-primary font-bold">৳ {{ number_format((float)($product['price'] ?? 0)) }} TK</span>
            </button>
            <button class="package-btn p-3 rounded-xl border border-surface-border hover:border-primary text-left text-xs transition-all">
              <span class="font-bold block text-on-surface">Creator Pro Kit (+ Battery/Bag)</span>
              <span class="text-text-muted">৳ {{ number_format(((float)($product['price'] ?? 0)) + 8500) }} TK</span>
            </button>
          </div>
        </div>

        <!-- Desktop Action Buttons (Right Column) -->
        <div class="hidden lg:block space-y-3 pt-2">
          <div class="flex items-center gap-3">
            <!-- Quantity Counter -->
            <div class="flex items-center border border-surface-border bg-surface-container rounded-xl p-1 shrink-0">
              <button type="button" onclick="decrementQty()" class="w-8 h-8 rounded-lg bg-surface-card flex items-center justify-center text-on-surface hover:bg-primary hover:text-white transition-colors font-bold select-none cursor-pointer">-</button>
              <input type="number" value="1" min="1" max="10" class="product-qty-input w-12 text-center text-sm font-bold bg-transparent focus:outline-none" oninput="syncQty(this.value)" />
              <button type="button" onclick="incrementQty()" class="w-8 h-8 rounded-lg bg-surface-card flex items-center justify-center text-on-surface hover:bg-primary hover:text-white transition-colors font-bold select-none cursor-pointer">+</button>
            </div>

            <!-- Add to Cart -->
            <button type="button" onclick="handleAddToCart()" class="flex-1 bg-primary hover:bg-primary-hover text-white font-bold text-sm py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
              <span>Add to Cart</span>
            </button>
          </div>

          <!-- Direct Order Now & Hotline -->
          <div class="flex gap-2">
            <button type="button" onclick="handleBuyNow()" class="flex-1 bg-rec-red-base hover:bg-rec-red-hover text-white font-bold text-sm py-3 px-4 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 text-center cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">bolt</span>
              <span>Order Now</span>
            </button>
            <a href="tel:{{ $whatsappDigits ?? '8801917256756' }}" class="px-4 py-3 bg-surface-container hover:bg-surface-container-low border border-surface-border rounded-xl text-xs font-bold text-on-surface flex items-center justify-center gap-1.5 transition-colors">
              <span class="material-symbols-outlined text-primary text-[18px]">support_agent</span>
              <span>Call Hotline</span>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Detailed Tabs: Description, Specifications, In the Box, Reviews -->
  <div class="mt-10 bg-surface-card rounded-3xl p-6 md:p-8 border border-surface-border shadow-xs">
    <!-- Tab Nav Buttons -->
    <div class="flex items-center gap-2 border-b border-surface-border pb-3 overflow-x-auto no-scrollbar">
      <button onclick="switchTab('overview', this)" class="tab-btn active px-4 py-2 text-xs font-bold rounded-xl bg-primary text-white transition-all">
        Description & Details
      </button>
      <button onclick="switchTab('specs', this)" class="tab-btn px-4 py-2 text-xs font-bold rounded-xl text-text-muted hover:text-primary transition-all">
        Technical Specifications
      </button>
      <button onclick="switchTab('box', this)" class="tab-btn px-4 py-2 text-xs font-bold rounded-xl text-text-muted hover:text-primary transition-all">
        What's In The Box
      </button>
      <button onclick="switchTab('reviews', this)" class="tab-btn px-4 py-2 text-xs font-bold rounded-xl text-text-muted hover:text-primary transition-all">
        Customer Reviews ({{ $product['reviews_count'] ?? 38 }})
      </button>
    </div>

    <!-- Tab 1: Description & Overview -->
    <div id="tab-overview" class="tab-content pt-6">
      <h3 class="text-base font-bold text-text-display mb-4">Product Description & Features</h3>
      @if(!empty($product['description']))
        <div class="product-rich-description text-on-surface text-sm leading-relaxed mb-6">
          {!! $product['description'] !!}
        </div>
      @else
        <p class="text-sm text-text-muted leading-relaxed mb-4">
          {{ $product['overview'] ?? 'High quality professional cinema equipment.' }}
        </p>
      @endif

      @if(isset($product['features']) && count($product['features']) > 0)
        <div class="mt-6 pt-4 border-t border-surface-border">
          <h4 class="text-xs uppercase font-extrabold text-primary tracking-wider mb-3">Key Highlights & Advantages:</h4>
          <ul class="space-y-2 text-xs text-on-surface">
            @foreach($product['features'] as $feat)
              <li class="flex items-start gap-2">
                <span class="material-symbols-outlined text-primary text-[16px] shrink-0 mt-0.5">check_circle</span>
                <span>{{ $feat }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    <!-- Tab 2: Specifications -->
    <div id="tab-specs" class="tab-content pt-6 hidden">
      <h3 class="text-base font-bold text-text-display mb-4">Detailed Specifications</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach(($product['specs'] ?? []) as $key => $val)
          <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low border border-surface-border text-xs">
            <span class="font-bold text-text-muted">{{ $key }}</span>
            <span class="font-semibold text-on-surface text-right">{{ $val }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Tab 3: In the Box -->
    <div id="tab-box" class="tab-content pt-6 hidden">
      <h3 class="text-base font-bold text-text-display mb-3">Included in the Box</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
        @foreach(($product['box_contents'] ?? ['Main Body', 'Battery Pack', 'Cables', 'Warranty Card']) as $item)
          <div class="flex items-center gap-2 p-3 bg-surface-container-low rounded-xl border border-surface-border text-xs">
            <span class="material-symbols-outlined text-primary text-[18px]">inventory_2</span>
            <span class="font-semibold text-on-surface">{{ $item }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Tab 4: Reviews -->
    <div id="tab-reviews" class="tab-content pt-6 hidden">
      <div class="flex flex-col md:flex-row gap-8 items-center justify-between pb-6 border-b border-surface-border">
        <div class="text-center md:text-left">
          <div class="text-4xl font-extrabold text-primary">{{ $product['rating'] ?? 4.9 }}</div>
          <div class="flex text-amber-500 text-lg justify-center md:justify-start my-1">★★★★★</div>
          <span class="text-xs text-text-muted">Based on {{ $product['reviews_count'] ?? 38 }} verified buyers</span>
        </div>
        <button onclick="showToast('Review submission form opened', 'info')" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 px-5 rounded-xl transition-all">
          Write a Review
        </button>
      </div>

      <!-- Sample Verified Reviews -->
      <div class="space-y-4 pt-6">
        <div class="p-4 bg-surface-container-low rounded-2xl border border-surface-border">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <span class="w-8 h-8 rounded-full bg-primary/20 text-primary font-bold flex items-center justify-center text-xs">T</span>
              <div>
                <span class="text-xs font-bold text-on-surface block">Tanvir Hasan</span>
                <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-0.5"><span class="material-symbols-outlined text-[12px]">verified</span> Verified Buyer (Dhaka)</span>
              </div>
            </div>
            <span class="text-[11px] text-text-muted">2 days ago</span>
          </div>
          <div class="flex text-amber-500 text-xs mb-1">★★★★★</div>
          <p class="text-xs text-on-surface leading-relaxed">
            "Authentic product with official warranty card from {{ $siteName ?? 'CAMERA BD' }}. Delivered to Dhanmondi within 24 hours. The 4K 10-bit color profile is incredible!"
          </p>
        </div>
      </div>
    </div>

  </div>

  <!-- Related Gear Recommendations -->
  @if(isset($related) && count($related) > 0)
    <div class="mt-12">
      <div class="flex items-center justify-between mb-6">
        <div>
          <span class="text-xs text-primary font-bold uppercase tracking-wider block">Frequently Paired</span>
          <h3 class="text-xl font-bold text-text-display">Recommended Production Gear</h3>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($related as $rel)
          <div class="bg-surface-card rounded-2xl p-4 border border-surface-border flex flex-col justify-between hover:shadow-md transition-all group">
            <div>
              <a href="{{ url('/product-details/' . $rel['slug']) }}" class="block aspect-square p-2 bg-surface-base rounded-xl mb-3 overflow-hidden">
                <img src="{{ $rel['images'][0] ?? '' }}" alt="{{ $rel['name'] }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async" />
              </a>
              <span class="text-[10px] text-primary font-bold uppercase">{{ $rel['brand'] }}</span>
              <h4 class="text-xs font-bold text-text-display line-clamp-2 mt-0.5 mb-1 group-hover:text-primary transition-colors">
                <a href="{{ url('/product-details/' . $rel['slug']) }}">{{ $rel['name'] }}</a>
              </h4>
            </div>
            <div class="pt-2 border-t border-surface-border">
              <span class="text-sm font-bold text-primary block">৳ {{ number_format($rel['price']) }} TK</span>
              <button onclick="addToCart('{{ $rel['name'] }}', {{ $rel['price'] }}, '{{ $rel['images'][0] ?? '' }}')" class="w-full mt-2 bg-surface-container hover:bg-primary hover:text-white text-on-surface text-xs font-bold py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[14px]">shopping_cart</span> Add
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
  function changeMainImage(url, btn) {
    const mainImg = document.getElementById('main-product-img');
    if (mainImg) {
      mainImg.style.opacity = '0';
      setTimeout(() => {
        mainImg.src = url;
        mainImg.style.opacity = '1';
      }, 150);
    }
    document.querySelectorAll('.thumbnail-btn').forEach(b => {
      b.classList.remove('border-primary');
      b.classList.add('border-surface-border');
    });
    btn.classList.add('border-primary');
    btn.classList.remove('border-surface-border');
  }

  function syncQty(val) {
    const parsed = parseInt(val) || 1;
    document.querySelectorAll('.product-qty-input').forEach(input => {
      input.value = Math.max(1, Math.min(10, parsed));
    });
  }

  function incrementQty() {
    const first = document.querySelector('.product-qty-input');
    const cur = parseInt(first?.value || 1);
    if (cur < 10) {
      syncQty(cur + 1);
    }
  }

  function decrementQty() {
    const first = document.querySelector('.product-qty-input');
    const cur = parseInt(first?.value || 1);
    if (cur > 1) {
      syncQty(cur - 1);
    }
  }

  function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
      b.classList.remove('bg-primary', 'text-white', 'active');
      b.classList.add('text-text-muted');
    });
    const target = document.getElementById('tab-' + tabId);
    if (target) target.classList.remove('hidden');
    btn.classList.add('bg-primary', 'text-white', 'active');
    btn.classList.remove('text-text-muted');
  }

  function handleAddToCart() {
    const qtyInput = document.querySelector('.product-qty-input');
    const qty = parseInt(qtyInput?.value || 1);
    if (window.CameraCart) {
      CameraCart.add({
        id: '{{ $product['id'] ?? null }}',
        name: '{{ addslashes($product['name']) }}',
        price: {{ $product['price'] }},
        image: '{{ $product['images'][0] ?? '' }}',
        barcode: '{{ $product['sku'] ?? '' }}',
        url: window.location.href,
        qty: qty
      }, true);
    }
  }

  function handleBuyNow() {
    const qtyInput = document.querySelector('.product-qty-input');
    const qty = parseInt(qtyInput?.value || 1);
    if (window.CameraCart) {
      CameraCart.add({
        id: '{{ $product['id'] ?? null }}',
        name: '{{ addslashes($product['name']) }}',
        price: {{ $product['price'] }},
        image: '{{ $product['images'][0] ?? '' }}',
        barcode: '{{ $product['sku'] ?? '' }}',
        url: window.location.href,
        qty: qty
      }, false);
    }
    window.location.href = "{{ route('frontend.checkout') }}";
  }

  // GA4 / GTM DataLayer ViewItem Event Tracking
  @php
    $dlProductItem = [
      'item_id' => (string)($product['id'] ?? ($product['sku'] ?? '')),
      'item_name' => $product['name'] ?? '',
      'affiliation' => $siteName ?? 'CAMERA BD',
      'currency' => 'BDT',
      'item_brand' => $product['brand'] ?? 'CAMERA BD',
      'item_category' => $product['category'] ?? 'Gear',
      'item_category2' => $product['subcategory'] ?? '',
      'price' => (float)($product['price'] ?? 0),
      'quantity' => 1
    ];

    $dlRelatedItems = [];
    if (!empty($related) && is_array($related)) {
      $itemIdxCounter = 1;
      foreach (array_slice($related, 0, 4) as $idx => $rel) {
        $dlRelatedItems[] = [
          'item_id' => (string)($rel['id'] ?? ($rel['sku'] ?? '')),
          'item_name' => $rel['name'] ?? '',
          'affiliation' => $siteName ?? 'CAMERA BD',
          'currency' => 'BDT',
          'index' => $itemIdxCounter++,
          'item_brand' => $rel['brand'] ?? 'CAMERA BD',
          'item_category' => $rel['category'] ?? 'Gear',
          'price' => (float)($rel['price'] ?? 0),
          'quantity' => 1
        ];
      }
    }
  @endphp

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.pushToDataLayer === 'function') {
      window.pushToDataLayer('view_item', {
        currency: 'BDT',
        value: {{ (float)($product['price'] ?? 0) }},
        items: [{!! json_encode($dlProductItem, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}]
      });

      @if(!empty($dlRelatedItems))
      window.pushToDataLayer('view_item_list', {
        item_list_id: 'related_products',
        item_list_name: 'Frequently Paired Products',
        items: {!! json_encode($dlRelatedItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
      });
      @endif
    }
  });

  // Meta Pixel ViewContent Event Tracking
  document.addEventListener('DOMContentLoaded', function() {
    try {
      if (typeof fbq === 'function') {
        fbq('track', 'ViewContent', {
          content_name: '{{ addslashes($product["name"] ?? "") }}',
          content_category: '{{ addslashes($product["category"] ?? "Gear") }}',
          content_ids: ['{{ (string)($product["id"] ?? ($product["sku"] ?? "")) }}'],
          content_type: 'product',
          value: {{ (float)($product["price"] ?? 0) }},
          currency: 'BDT'
        });
      }
    } catch(err) {
      console.warn('Meta Pixel ViewContent error:', err);
    }
  });
</script>
@endpush
