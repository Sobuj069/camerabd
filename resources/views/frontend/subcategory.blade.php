@extends('frontend.layouts.master')

@section('title', ($currentSubcategory['name'] ?? 'Subcategory') . ' - ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-16 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-5 flex-wrap">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <a href="{{ route('frontend.category', ['slug' => $currentCategory['slug'] ?? 'cameras']) }}" class="hover:text-primary transition-colors">{{ $currentCategory['name'] ?? 'Category' }}</a>
    <span>/</span>
    <span class="text-on-surface font-semibold">{{ $currentSubcategory['name'] ?? 'Subcategory' }}</span>
  </nav>

  <!-- Subcategory Header Banner -->
  <div class="bg-surface-card rounded-3xl p-6 lg:p-8 border border-surface-border shadow-xs mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div class="max-w-2xl">
      <div class="flex items-center gap-2 mb-2">
        <span class="bg-primary/10 text-primary text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-full tracking-wider">
          {{ $currentCategory['name'] ?? 'Category' }}
        </span>
        <span class="text-text-muted text-xs font-bold">•</span>
        <span class="text-xs font-bold text-stock-available flex items-center gap-1">
          <span class="w-2 h-2 rounded-full bg-stock-available animate-pulse"></span> Official Warranty Channel
        </span>
      </div>

      <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-text-display mb-2">
        {{ $currentSubcategory['name'] ?? 'Super 35 & Cinema Cameras' }}
      </h1>
      
      <p class="text-xs md:text-sm text-text-muted leading-relaxed">
        {{ $currentSubcategory['description'] ?? 'Curated selection of professional cinema cameras, video systems, and high-performance production gear with 1-3 years official warranty.' }}
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="flex items-center gap-3 shrink-0">
      <div class="bg-surface-container-low p-4 rounded-2xl border border-surface-border text-center min-w-[110px]">
        <span class="text-xl font-extrabold text-primary block">{{ count($products ?? []) }}</span>
        <span class="text-[11px] text-text-muted font-bold">Gear Models</span>
      </div>
      <div class="bg-surface-container-low p-4 rounded-2xl border border-surface-border text-center min-w-[110px]">
        <span class="text-xl font-extrabold text-stock-available block">100%</span>
        <span class="text-[11px] text-text-muted font-bold">Genuine</span>
      </div>
    </div>
  </div>

  <!-- Main 2-Column Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- LEFT COLUMN: Filters Sidebar (3 Cols) -->
    <aside class="hidden lg:block lg:col-span-3 bg-surface-card rounded-3xl p-5 border border-surface-border shadow-xs sticky top-28 space-y-6">
      
      <div class="flex items-center justify-between pb-3 border-b border-surface-border">
        <h3 class="text-sm font-bold text-text-display flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary text-[18px]">tune</span>
          <span>Filter Selection</span>
        </h3>
        <button onclick="resetSubFilters()" class="text-[11px] text-rec-red-base hover:underline font-bold">Reset</button>
      </div>

      <!-- Sibling Subcategories in Same Category -->
      <div>
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Related Subcategories</h4>
        <div class="space-y-1.5 text-xs">
          @foreach(($currentCategory['subcategories'] ?? []) as $sub)
            <a href="{{ route('frontend.subcategory', ['category_slug' => $currentCategory['slug'], 'subcategory_slug' => $sub['slug']]) }}" class="flex items-center justify-between p-2 rounded-xl {{ ($sub['slug'] === ($currentSubcategory['slug'] ?? '')) ? 'bg-primary text-white font-bold' : 'hover:bg-surface-container text-text-muted hover:text-on-surface' }} transition-colors">
              <span>{{ $sub['name'] }}</span>
              <span class="text-[10px] opacity-80">{{ $sub['count'] ?? 6 }}</span>
            </a>
          @endforeach
        </div>
      </div>

      <!-- Brands -->
      <div class="pt-4 border-t border-surface-border">
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Filter by Brand</h4>
        <div class="space-y-2 text-xs">
          @foreach(['Sony Cinema' => 14, 'Canon Pro' => 8, 'DJI' => 10, 'Hollyland' => 12, 'Blackmagic' => 4, 'Datavideo' => 6] as $br => $num)
            <label class="flex items-center justify-between text-text-muted hover:text-on-surface cursor-pointer">
              <span class="flex items-center gap-2">
                <input type="checkbox" class="rounded text-primary sub-filter-cb" />
                <span>{{ $br }}</span>
              </span>
              <span class="text-[10px] text-text-muted bg-surface-container px-1.5 py-0.5 rounded font-bold">{{ $num }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <!-- Price Slider -->
      <div class="pt-4 border-t border-surface-border">
        <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Max Budget (BDT)</h4>
        <input type="range" min="5000" max="1500000" step="10000" value="950000" class="w-full accent-primary cursor-pointer" oninput="document.getElementById('sub-price-val').textContent = '৳ ' + parseInt(this.value).toLocaleString()" />
        <div class="flex items-center justify-between text-xs font-bold text-on-surface mt-2">
          <span>৳ ৫,০০০</span>
          <span id="sub-price-val" class="text-primary font-extrabold">৳ ৯,৫০,০০০</span>
        </div>
      </div>

      <!-- EMI Facility -->
      <div class="pt-4 border-t border-surface-border p-3 bg-surface-container-low rounded-2xl">
        <div class="flex items-center gap-2 text-primary font-bold text-xs mb-1">
          <span class="material-symbols-outlined text-[18px]">credit_card</span>
          <span>0% EMI Available</span>
        </div>
        <p class="text-[11px] text-text-muted leading-relaxed">
          Up to 36 Months 0% EMI with 36 Partner Banks.
        </p>
      </div>

    </aside>

    <!-- RIGHT COLUMN: Products Grid (9 Cols) -->
    <div class="lg:col-span-9 space-y-6">
      
      <!-- Top Control Bar -->
      <div class="bg-surface-card rounded-2xl p-4 border border-surface-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <button onclick="toggleMobileFilter()" class="lg:hidden bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">tune</span> Filter
          </button>
          <span class="text-xs text-text-muted font-medium">
            Showing <strong class="text-on-surface font-bold">{{ count($products ?? []) }}</strong> items in {{ $currentSubcategory['name'] ?? 'this category' }}
          </span>
        </div>

        <div class="flex items-center gap-2 self-end sm:self-auto text-xs">
          <label class="text-text-muted font-bold">Sort:</label>
          <select class="bg-surface-container-low rounded-xl px-3 py-2 border border-surface-border text-on-surface text-xs font-bold focus:outline-none focus:border-primary cursor-pointer">
            <option>Featured Products</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
            <option>Best Customer Rating</option>
          </select>
        </div>
      </div>

      <!-- Product Cards Grid -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-5">
        @foreach(($products ?? []) as $prod)
          <div class="bg-surface-card rounded-3xl p-3.5 sm:p-4 border border-surface-border flex flex-col justify-between hover:shadow-lg hover:border-primary/40 transition-all duration-300 group">
            <div>
              <div class="flex items-center justify-between min-h-[24px] mb-2">
                @if(!empty($prod['discount']))
                  <span class="bg-rec-red-base text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                    Save {{ $prod['discount'] }}%
                  </span>
                @else
                  <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">
                    Genuine
                  </span>
                @endif

                <button onclick="toggleWishlist(this, '{{ addslashes($prod['name']) }}', {{ (float)$prod['price'] }}, '{{ addslashes($prod['images'][0] ?? '') }}', '{{ $prod['slug'] ?? ($prod['id'] ?? '') }}', '{{ $prod['id'] ?? '' }}', '{{ addslashes($prod['brand'] ?? 'CAMERA BD') }}')" class="wishlist-btn text-text-muted hover:text-red-500 transition-colors cursor-pointer" title="Wishlist" data-wishlist-product="{{ addslashes($prod['name']) }}" data-wishlist-id="{{ $prod['id'] ?? '' }}">
                  <span class="material-symbols-outlined text-[18px]">favorite_border</span>
                </button>
              </div>

              <a href="{{ url('/product-details/' . ($prod['slug'] ?? 'sony-fx30')) }}" class="w-full aspect-square bg-surface-base rounded-2xl p-3 flex items-center justify-center mb-3 overflow-hidden border border-surface-border block">
                <img src="{{ $prod['images'][0] ?? 'https://via.placeholder.com/300' }}" alt="{{ $prod['name'] }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async" />
              </a>

              <div class="flex items-center justify-between text-[10px] mb-1">
                <span class="text-primary font-bold uppercase">{{ $prod['brand'] ?? 'Brand' }}</span>
                <span class="text-stock-available font-bold">In Stock</span>
              </div>

              <h3 class="text-xs sm:text-sm font-bold text-text-display line-clamp-2 leading-snug mb-1 group-hover:text-primary transition-colors">
                <a href="{{ url('/product-details/' . ($prod['slug'] ?? 'sony-fx30')) }}">{{ $prod['name'] }}</a>
              </h3>

              <div class="flex items-center gap-1 text-[11px] text-amber-500 mb-2">
                <span>★★★★★</span>
                <span class="text-text-muted font-medium text-[10px]">({{ $prod['reviews_count'] ?? 10 }})</span>
              </div>
            </div>

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

    </div>

  </div>

  <!-- Buyer's Guidance & FAQ Section -->
  <div class="mt-12 bg-surface-card rounded-3xl p-6 md:p-8 border border-surface-border shadow-xs">
    <h3 class="text-base font-bold text-text-display mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-primary text-[20px]">help_outline</span>
      <span>Frequently Asked Questions & Official Warranty Guide</span>
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
      <div class="p-4 bg-surface-container-low rounded-2xl border border-surface-border">
        <h4 class="font-bold text-on-surface mb-1">Q: How do I claim warranty for my gear?</h4>
        <p class="text-text-muted leading-relaxed">
          Bring your official {{ $siteName ?? 'CAMERA BD' }} invoice & warranty card to our store for instant diagnostic & repair assistance.
        </p>
      </div>
      <div class="p-4 bg-surface-container-low rounded-2xl border border-surface-border">
        <h4 class="font-bold text-on-surface mb-1">Q: Can I test the equipment before purchasing?</h4>
        <p class="text-text-muted leading-relaxed">
          Yes, both our outlets have live camera rigs, lighting zones, and audio calibration setups where you can hands-on test any cinema body or mic before taking delivery.
        </p>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // GA4 / GTM DataLayer Subcategory View Item List
  @php
    $dlSubcatItems = [];
    if (!empty($products) && is_array($products)) {
      $subcatItemCounter = 1;
      foreach (array_slice(array_values($products), 0, 24) as $idx => $p) {
        $dlSubcatItems[] = [
          'item_id' => (string)($p['id'] ?? ($p['sku'] ?? '')),
          'item_name' => $p['name'] ?? '',
          'affiliation' => $siteName ?? 'CAMERA BD',
          'currency' => 'BDT',
          'index' => $subcatItemCounter++,
          'item_brand' => $p['brand'] ?? 'CAMERA BD',
          'item_category' => $currentCategory['name'] ?? 'Gear',
          'item_category2' => $currentSubcategory['name'] ?? '',
          'price' => (float)($p['price'] ?? 0),
          'quantity' => 1
        ];
      }
    }
  @endphp

  @if(!empty($dlSubcatItems))
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.pushToDataLayer === 'function') {
      window.pushToDataLayer('view_item_list', {
        item_list_id: 'subcategory_{{ $currentSubcategory["slug"] ?? "subcat" }}',
        item_list_name: '{{ addslashes($currentSubcategory["name"] ?? ($currentCategory["name"] ?? "Subcategory")) }}',
        items: {!! json_encode($dlSubcatItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
      });
    }
  });
  @endif
  function resetSubFilters() {
    document.querySelectorAll('.sub-filter-cb').forEach(cb => cb.checked = false);
    showToast('Filters reset', 'info');
  }
</script>
@endpush
