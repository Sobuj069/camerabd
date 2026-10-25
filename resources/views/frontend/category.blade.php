@extends('frontend.layouts.master')

@section('title', ($currentCategory['name'] ?? 'Shop by Category') . ' - ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-16 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-5 flex-wrap">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <a href="{{ route('frontend.categories') }}" class="hover:text-primary transition-colors">Categories</a>
    <span>/</span>
    <span class="text-on-surface font-semibold">{{ $currentCategory['name'] ?? 'All Products' }}</span>
  </nav>

  <!-- Category Banner & Quick Subcategory Strip -->
  <div class="bg-surface-card rounded-3xl p-6 lg:p-8 border border-surface-border shadow-xs mb-8 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <span class="bg-primary/10 text-primary text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-full tracking-wider mb-2 inline-block">
        {{ $currentCategory['badge'] ?? 'Official Department' }}
      </span>
      <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-text-display mb-2">
        {{ $currentCategory['name'] ?? 'Cinema & Digital Cameras' }}
      </h1>
      <p class="text-xs md:text-sm text-text-muted leading-relaxed mb-5">
        {{ $currentCategory['description'] ?? 'Explore Bangladesh’s largest official collection of 4K/8K cinema bodies, broadcast lenses, audio transmission systems, and studio equipment with genuine importer warranty.' }}
      </p>

      <!-- Subcategory Quick Pills -->
      @if(!empty($currentCategory['subcategories']))
        <div class="flex items-center gap-2 flex-wrap">
          @foreach($currentCategory['subcategories'] as $sub)
            <a href="{{ route('frontend.subcategory', ['category_slug' => $currentCategory['slug'], 'subcategory_slug' => $sub['slug']]) }}" class="bg-surface-container hover:bg-primary hover:text-white text-on-surface text-xs font-bold px-3.5 py-2 rounded-xl transition-all border border-surface-border flex items-center gap-1.5 shadow-xs">
              <span>{{ $sub['name'] }}</span>
              <span class="bg-white/30 text-current text-[10px] px-1.5 py-0.2 rounded-full">{{ $sub['count'] ?? 8 }}</span>
            </a>
          @endforeach
        </div>
      @endif
    </div>

    <div class="hidden lg:block absolute right-8 top-1/2 -translate-y-1/2 w-64 h-64 opacity-20 pointer-events-none">
      <span class="material-symbols-outlined text-[200px] text-primary">videocam</span>
    </div>
  </div>

  <!-- Main 2-Column Shop Layout (Filter Sidebar + Product Grid) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- LEFT COLUMN: Filter Sidebar (Desktop 3.5 Cols) -->
    <aside class="hidden lg:block lg:col-span-3 bg-surface-card rounded-3xl p-5 border border-surface-border shadow-xs sticky top-28 space-y-6">
      
      <!-- Filter Header -->
      <div class="flex items-center justify-between pb-3 border-b border-surface-border">
        <h3 class="text-sm font-bold text-text-display flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary text-[18px]">tune</span>
          <span>Filter Gear</span>
        </h3>
        <button onclick="resetFilters()" class="text-[11px] text-rec-red-base hover:underline font-bold">
          Reset All
        </button>
      </div>

      <!-- Filter 1: Subcategories -->
      @if(!empty($currentCategory['subcategories']))
      <div>
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Subcategories</h4>
        <div class="space-y-2 text-xs">
          @foreach($currentCategory['subcategories'] as $sub)
            <label class="flex items-center justify-between text-text-muted hover:text-on-surface cursor-pointer">
              <span class="flex items-center gap-2">
                <input type="checkbox" checked class="rounded text-primary focus:ring-primary filter-checkbox" />
                <span>{{ $sub['name'] }}</span>
              </span>
              <span class="text-[10px] text-text-muted bg-surface-container px-1.5 py-0.5 rounded font-bold">{{ $sub['count'] ?? '' }}</span>
            </label>
          @endforeach
        </div>
      </div>
      @endif

      <!-- Filter 2: Brands -->
      <div class="pt-4 border-t border-surface-border">
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Brands</h4>
        <div class="space-y-2 text-xs">
          @foreach(['Sony Official' => 18, 'DJI Pro' => 12, 'Hollyland' => 14, 'Canon' => 9, 'FUJIFILM' => 7, 'Datavideo' => 6, 'Sigma Art' => 11] as $brand => $cnt)
            <label class="flex items-center justify-between text-text-muted hover:text-on-surface cursor-pointer">
              <span class="flex items-center gap-2">
                <input type="checkbox" class="rounded text-primary focus:ring-primary filter-checkbox" />
                <span>{{ $brand }}</span>
              </span>
              <span class="text-[10px] text-text-muted bg-surface-container px-1.5 py-0.5 rounded font-bold">{{ $cnt }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <!-- Filter 3: Price Range -->
      <div class="pt-4 border-t border-surface-border">
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Price Range (BDT)</h4>
        <div class="space-y-3">
          <input type="range" min="5000" max="1200000" step="5000" value="750000" class="w-full accent-primary cursor-pointer" oninput="updatePriceLabel(this.value)" />
          <div class="flex items-center justify-between text-xs font-bold text-on-surface">
            <span>৳ ৫,০০০</span>
            <span id="price-range-label" class="text-primary font-extrabold">Max: ৳ ৭,৫০,০০০</span>
          </div>
        </div>
      </div>

      <!-- Filter 4: Availability -->
      <div class="pt-4 border-t border-surface-border">
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Stock Status</h4>
        <div class="space-y-2 text-xs">
          <label class="flex items-center gap-2 text-text-muted cursor-pointer">
            <input type="checkbox" checked class="rounded text-primary focus:ring-primary filter-checkbox" />
            <span class="text-stock-available font-bold flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-stock-available"></span> In Stock (2 Outlets)
            </span>
          </label>
          <label class="flex items-center gap-2 text-text-muted cursor-pointer">
            <input type="checkbox" class="rounded text-primary focus:ring-primary filter-checkbox" />
            <span>Pre-Booking Ready</span>
          </label>
        </div>
      </div>

    </aside>

    <!-- RIGHT COLUMN: Products Grid & Sorting (Desktop 9 Cols) -->
    <div class="lg:col-span-9 space-y-6">
      
      <!-- Top Control Bar (Items Count + Sort Dropdown + Mobile Filter Trigger) -->
      <div class="bg-surface-card rounded-2xl p-4 border border-surface-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <div class="flex items-center gap-3">
          <!-- Mobile Filter Toggle Button -->
          <button onclick="toggleMobileFilter()" class="lg:hidden bg-primary text-white text-xs font-bold px-3.5 py-2 rounded-xl flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">tune</span>
            <span>Filters</span>
          </button>
          
          <span class="text-xs text-text-muted font-medium">
            Showing <strong class="text-on-surface font-bold">{{ count($products ?? []) }}</strong> gear items found
          </span>
        </div>

        <!-- Sort By Dropdown -->
        <div class="flex items-center gap-2 self-end sm:self-auto text-xs">
          <label class="text-text-muted font-bold">Sort By:</label>
          <select class="bg-surface-container-low rounded-xl px-3 py-2 border border-surface-border text-on-surface text-xs font-bold focus:outline-none focus:border-primary cursor-pointer" onchange="sortProducts(this.value)">
            <option value="featured">Featured & Popular</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="rating">Highest Customer Rating</option>
            <option value="newest">Newest Arrivals</option>
          </select>
        </div>

      </div>

      <!-- Products Grid (3 Columns on Desktop, 2 on Tablet/Mobile) -->
      <div id="product-list-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-5">
        
        @foreach(($products ?? []) as $prod)
          <div class="product-item-card bg-surface-card rounded-3xl p-3.5 sm:p-4 border border-surface-border flex flex-col justify-between hover:shadow-lg hover:border-primary/40 transition-all duration-300 group">
            <div>
              <!-- Top Row: Badge + Wishlist Heart -->
              <div class="flex items-center justify-between min-h-[24px] mb-2">
                @if(!empty($prod['discount']))
                  <span class="bg-rec-red-base text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                    -{{ $prod['discount'] }}% Off
                  </span>
                @else
                  <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">
                    Official
                  </span>
                @endif

                <button onclick="toggleWishlist(this, '{{ addslashes($prod['name']) }}', {{ (float)$prod['price'] }}, '{{ addslashes($prod['images'][0] ?? '') }}', '{{ $prod['slug'] ?? ($prod['id'] ?? '') }}', '{{ $prod['id'] ?? '' }}', '{{ addslashes($prod['brand'] ?? 'CAMERA BD') }}')" class="wishlist-btn text-text-muted hover:text-red-500 transition-colors cursor-pointer" title="Wishlist" data-wishlist-product="{{ addslashes($prod['name']) }}" data-wishlist-id="{{ $prod['id'] ?? '' }}">
                  <span class="material-symbols-outlined text-[18px]">favorite_border</span>
                </button>
              </div>

              <!-- Product Image -->
              <a href="{{ url('/product-details/' . ($prod['slug'] ?? 'sony-fx30')) }}" class="w-full aspect-square bg-surface-base rounded-2xl p-3 flex items-center justify-center mb-3 overflow-hidden border border-surface-border block">
                <img src="{{ $prod['images'][0] ?? 'https://via.placeholder.com/300' }}" alt="{{ $prod['name'] }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" />
              </a>

              <!-- Brand & Stock Indicator -->
              <div class="flex items-center justify-between text-[10px] mb-1">
                <span class="text-primary font-bold uppercase">{{ $prod['brand'] ?? 'Official' }}</span>
                <span class="text-stock-available font-bold flex items-center gap-0.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-stock-available"></span> In Stock
                </span>
              </div>

              <!-- Product Title -->
              <h3 class="text-xs sm:text-sm font-bold text-text-display line-clamp-2 leading-snug mb-1 group-hover:text-primary transition-colors">
                <a href="{{ url('/product-details/' . ($prod['slug'] ?? 'sony-fx30')) }}">{{ $prod['name'] }}</a>
              </h3>

              <!-- Rating -->
              <div class="flex items-center gap-1 text-[11px] text-amber-500 mb-2">
                <span>★★★★★</span>
                <span class="text-text-muted font-medium text-[10px]">({{ $prod['reviews_count'] ?? 12 }})</span>
              </div>
            </div>

            <!-- Price Block & Action Button -->
            <div class="pt-3 border-t border-surface-border">
              <div class="flex items-baseline gap-1.5 flex-wrap mb-2.5">
                <span class="text-sm sm:text-base font-extrabold text-primary">৳ {{ number_format($prod['price']) }} TK</span>
                @if(!empty($prod['old_price']))
                  <span class="text-[11px] text-text-muted line-through">৳ {{ number_format($prod['old_price']) }}</span>
                @endif
              </div>

              <div class="flex gap-1.5">
                <button onclick="addToCart('{{ $prod['name'] }}', {{ $prod['price'] }}, '{{ $prod['images'][0] ?? '' }}')" class="flex-1 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1 shadow-sm active:scale-95">
                  <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                  <span>Add to Cart</span>
                </button>
                <a href="{{ url('/product-details/' . ($prod['slug'] ?? 'sony-fx30')) }}" class="p-2 bg-surface-container hover:bg-surface-container-low text-on-surface rounded-xl border border-surface-border transition-colors flex items-center justify-center" title="Details">
                  <span class="material-symbols-outlined text-[18px]">visibility</span>
                </a>
              </div>
            </div>

          </div>
        @endforeach

      </div>

      <!-- Pagination -->
      <div class="bg-surface-card rounded-2xl p-4 border border-surface-border shadow-xs flex items-center justify-between text-xs font-bold">
        <button class="px-4 py-2 rounded-xl bg-surface-container text-text-muted hover:bg-primary hover:text-white transition-colors">
          Previous
        </button>
        <div class="flex items-center gap-1.5">
          <span class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center">1</span>
          <span class="w-8 h-8 rounded-xl bg-surface-container text-on-surface hover:bg-primary hover:text-white flex items-center justify-center cursor-pointer">2</span>
          <span class="w-8 h-8 rounded-xl bg-surface-container text-on-surface hover:bg-primary hover:text-white flex items-center justify-center cursor-pointer">3</span>
        </div>
        <button class="px-4 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-primary hover:text-white transition-colors">
          Next
        </button>
      </div>

    </div>

  </div>

</div>

<!-- Mobile Filter Slide Drawer -->
<div id="mobile-filter-drawer" class="fixed inset-0 z-[105] bg-black/60 backdrop-blur-xs hidden">
  <div class="fixed top-0 right-0 bottom-0 w-80 max-w-full bg-surface-card p-5 overflow-y-auto space-y-6 shadow-2xl">
    <div class="flex items-center justify-between pb-3 border-b border-surface-border">
      <h3 class="text-sm font-bold text-text-display">Filter Products</h3>
      <button onclick="toggleMobileFilter()" class="text-text-muted hover:text-primary">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>
    
    <div>
      <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2">Brands</h4>
      <div class="space-y-2 text-xs">
        @foreach(['Sony', 'DJI', 'Hollyland', 'Canon', 'FUJIFILM', 'Datavideo'] as $b)
          <label class="flex items-center gap-2 text-text-muted cursor-pointer">
            <input type="checkbox" class="rounded text-primary" />
            <span>{{ $b }}</span>
          </label>
        @endforeach
      </div>
    </div>

    <button onclick="toggleMobileFilter(); showToast('Filters applied!', 'success');" class="w-full bg-primary text-white text-xs font-bold py-3 rounded-xl">
      Apply Filters
    </button>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // GA4 / GTM DataLayer Category View Item List
  @php
    $dlCatItems = [];
    if (!empty($products) && is_array($products)) {
      $catItemCounter = 1;
      foreach (array_slice(array_values($products), 0, 24) as $idx => $p) {
        $dlCatItems[] = [
          'item_id' => (string)($p['id'] ?? ($p['sku'] ?? '')),
          'item_name' => $p['name'] ?? '',
          'affiliation' => $siteName ?? 'CAMERA BD',
          'currency' => 'BDT',
          'index' => $catItemCounter++,
          'item_brand' => $p['brand'] ?? 'CAMERA BD',
          'item_category' => $p['category'] ?? ($currentCategory['name'] ?? 'Gear'),
          'price' => (float)($p['price'] ?? 0),
          'quantity' => 1
        ];
      }
    }
  @endphp

  @if(!empty($dlCatItems))
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.pushToDataLayer === 'function') {
      window.pushToDataLayer('view_item_list', {
        item_list_id: 'category_{{ $currentCategory["slug"] ?? "catalog" }}',
        item_list_name: '{{ addslashes($currentCategory["name"] ?? "Category") }}',
        items: {!! json_encode($dlCatItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
      });
    }
  });
  @endif
  function updatePriceLabel(val) {
    document.getElementById('price-range-label').textContent = `Max: ৳ ${parseInt(val).toLocaleString()}`;
  }

  function resetFilters() {
    document.querySelectorAll('.filter-checkbox').forEach(cb => cb.checked = false);
    showToast('Filters reset', 'info');
  }

  function toggleMobileFilter() {
    const drawer = document.getElementById('mobile-filter-drawer');
    if (drawer) {
      drawer.classList.toggle('hidden');
    }
  }

  function sortProducts(criteria) {
    showToast(`Sorted by ${criteria}`, 'info');
  }
</script>
@endpush
