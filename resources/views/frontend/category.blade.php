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
      @if(!empty($currentCategory['subcategories']) && count($currentCategory['subcategories']) > 0)
        <div class="flex items-center gap-2 flex-wrap">
          @foreach($currentCategory['subcategories'] as $sub)
            <a href="{{ route('frontend.subcategory', ['category_slug' => $currentCategory['slug'], 'subcategory_slug' => $sub['slug']]) }}" class="bg-surface-container hover:bg-primary hover:text-white text-on-surface text-xs font-bold px-3.5 py-2 rounded-xl transition-all border border-surface-border flex items-center gap-1.5 shadow-xs">
              <span>{{ $sub['name'] }}</span>
              @if(!empty($sub['count']))
                <span class="bg-white/30 text-current text-[10px] px-1.5 py-0.2 rounded-full">{{ $sub['count'] }}</span>
              @endif
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
    
    <!-- LEFT COLUMN: Filter Sidebar (Desktop 3 Cols) -->
    <form id="category-filter-form" method="GET" action="{{ url()->current() }}" class="hidden lg:block lg:col-span-3">
      <input type="hidden" name="sort" id="filter-sort-input" value="{{ $selectedSort ?? request('sort', 'featured') }}">
      
      <aside class="bg-surface-card rounded-3xl p-5 border border-surface-border shadow-xs sticky top-28 space-y-6">
        
        <!-- Filter Header -->
        <div class="flex items-center justify-between pb-3 border-b border-surface-border">
          <h3 class="text-sm font-bold text-text-display flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-[18px]">tune</span>
            <span>Filter Gear</span>
          </h3>
          <a href="{{ url()->current() }}" class="text-[11px] text-rec-red-base hover:underline font-bold">
            Reset All
          </a>
        </div>

        <!-- Filter 1: Subcategories (if available) -->
        @if(!empty($currentCategory['subcategories']) && count($currentCategory['subcategories']) > 0)
        <div>
          <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Subcategories</h4>
          <div class="space-y-1.5 text-xs max-h-48 overflow-y-auto pr-1">
            @foreach($currentCategory['subcategories'] as $sub)
              <a href="{{ route('frontend.subcategory', ['category_slug' => $currentCategory['slug'], 'subcategory_slug' => $sub['slug']]) }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-surface-container text-text-muted hover:text-on-surface transition-colors">
                <span class="truncate">{{ $sub['name'] }}</span>
                @if(!empty($sub['count']))
                  <span class="text-[10px] text-text-muted bg-surface-container-high px-1.5 py-0.5 rounded font-bold shrink-0">{{ $sub['count'] }}</span>
                @endif
              </a>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Filter 2: Brands -->
        @if(!empty($availableBrands) && $availableBrands->isNotEmpty())
        <div class="pt-4 border-t border-surface-border">
          <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider">Brands</h4>
            @if(!empty($selectedBrands))
              <a href="{{ request()->fullUrlWithQuery(['brand' => null, 'page' => 1]) }}" class="text-[10px] text-primary hover:underline font-bold">Clear</a>
            @endif
          </div>
          <div class="space-y-2 text-xs max-h-56 overflow-y-auto pr-1">
            @foreach($availableBrands as $brand)
              <label class="flex items-center justify-between text-text-muted hover:text-on-surface cursor-pointer select-none">
                <span class="flex items-center gap-2 truncate">
                  <input type="checkbox" name="brand[]" value="{{ $brand->id }}" {{ in_array((string)$brand->id, (array)($selectedBrands ?? [])) || in_array($brand->name, (array)($selectedBrands ?? [])) ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-primary focus:ring-primary filter-checkbox" />
                  <span class="truncate">{{ $brand->name }}</span>
                </span>
                <span class="text-[10px] text-text-muted bg-surface-container px-1.5 py-0.5 rounded font-bold shrink-0">{{ $brand->products_count }}</span>
              </label>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Filter 3: Price Range -->
        <div class="pt-4 border-t border-surface-border">
          <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider">Price Range (BDT)</h4>
            @if(request()->filled('max_price'))
              <a href="{{ request()->fullUrlWithQuery(['max_price' => null, 'page' => 1]) }}" class="text-[10px] text-primary hover:underline font-bold">Reset</a>
            @endif
          </div>
          <div class="space-y-3">
            <input type="range" name="max_price" min="{{ $priceMinBound }}" max="{{ $priceMaxBound }}" step="{{ max(500, round(($priceMaxBound - $priceMinBound) / 50)) }}" value="{{ request('max_price', $priceMaxBound) }}" class="w-full accent-primary cursor-pointer" oninput="updatePriceLabel(this.value)" onchange="this.form.submit()" />
            <div class="flex items-center justify-between text-xs font-bold text-on-surface">
              <span>৳ {{ number_format($priceMinBound) }}</span>
              <span id="price-range-label" class="text-primary font-extrabold">Max: ৳ {{ number_format(request('max_price', $priceMaxBound)) }}</span>
            </div>
          </div>
        </div>

        <!-- Filter 4: Stock Status -->
        <div class="pt-4 border-t border-surface-border">
          <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-3">Stock Status</h4>
          <div class="space-y-2 text-xs">
            <label class="flex items-center gap-2 text-text-muted hover:text-on-surface cursor-pointer select-none">
              <input type="radio" name="stock" value="all" {{ (!request()->filled('stock') || request('stock') === 'all') ? 'checked' : '' }} onchange="this.form.submit()" class="text-primary focus:ring-primary" />
              <span>All Items</span>
            </label>
            <label class="flex items-center gap-2 text-text-muted hover:text-on-surface cursor-pointer select-none">
              <input type="radio" name="stock" value="in_stock" {{ request('stock') === 'in_stock' ? 'checked' : '' }} onchange="this.form.submit()" class="text-primary focus:ring-primary" />
              <span class="text-stock-available font-bold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-stock-available"></span> In Stock Only
              </span>
            </label>
            <label class="flex items-center gap-2 text-text-muted hover:text-on-surface cursor-pointer select-none">
              <input type="radio" name="stock" value="pre_order" {{ request('stock') === 'pre_order' ? 'checked' : '' }} onchange="this.form.submit()" class="text-primary focus:ring-primary" />
              <span>Pre-Booking Ready</span>
            </label>
          </div>
        </div>

      </aside>
    </form>

    <!-- RIGHT COLUMN: Products Grid & Sorting (Desktop 9 Cols) -->
    <div class="lg:col-span-9 space-y-6">
      
      <!-- Top Control Bar (Items Count + Sort Dropdown + Mobile Filter Trigger) -->
      <div class="bg-surface-card rounded-2xl p-4 border border-surface-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <div class="flex items-center gap-3">
          <!-- Mobile Filter Toggle Button -->
          <button type="button" onclick="toggleMobileFilter()" class="lg:hidden bg-primary text-white text-xs font-bold px-3.5 py-2 rounded-xl flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-[16px]">tune</span>
            <span>Filters</span>
          </button>
          
          <span class="text-xs text-text-muted font-medium">
            Showing <strong class="text-on-surface font-bold">{{ method_exists($products, 'total') ? $products->total() : count($products) }}</strong> gear items found
          </span>
        </div>

        <!-- Sort By Dropdown -->
        <div class="flex items-center gap-2 self-end sm:self-auto text-xs">
          <label class="text-text-muted font-bold">Sort By:</label>
          <select class="bg-surface-container-low rounded-xl px-3 py-2 border border-surface-border text-on-surface text-xs font-bold focus:outline-none focus:border-primary cursor-pointer" onchange="sortProducts(this.value)">
            <option value="featured" {{ ($selectedSort ?? request('sort', 'featured')) === 'featured' ? 'selected' : '' }}>Featured & Popular</option>
            <option value="price_low" {{ ($selectedSort ?? request('sort')) === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ ($selectedSort ?? request('sort')) === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="newest" {{ ($selectedSort ?? request('sort')) === 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
            <option value="name" {{ ($selectedSort ?? request('sort')) === 'name' ? 'selected' : '' }}>Name: A to Z</option>
          </select>
        </div>

      </div>

      @if(empty($products) || (method_exists($products, 'isEmpty') && $products->isEmpty()) || (is_array($products) && count($products) === 0))
        <div class="bg-surface-card rounded-3xl p-12 text-center border border-surface-border">
          <div class="w-16 h-16 rounded-2xl bg-surface-container mx-auto flex items-center justify-center text-text-muted mb-4">
            <span class="material-symbols-outlined text-[36px]">inventory_2</span>
          </div>
          <h3 class="text-base font-bold text-text-display mb-1">No products found</h3>
          <p class="text-xs text-text-muted mb-5 max-w-md mx-auto">No gear items match your selected filters. Try broadening your criteria or reset filters.</p>
          <a href="{{ url()->current() }}" class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-xs">
            <span class="material-symbols-outlined text-[16px]">restart_alt</span>
            <span>Reset All Filters</span>
          </a>
        </div>
      @else
        <!-- Products Grid (3 Columns on Desktop, 2 on Tablet/Mobile) -->
        <div id="product-list-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-5">
          
          @foreach($products as $prod)
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
                  <img src="{{ $prod['images'][0] ?? 'https://via.placeholder.com/300' }}" alt="{{ $prod['name'] }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async" />
                </a>

                <!-- Brand & Stock Indicator -->
                <div class="flex items-center justify-between text-[10px] mb-1">
                  <span class="text-primary font-bold uppercase truncate max-w-[65%]">{{ $prod['brand'] ?? 'Official' }}</span>
                  <span class="text-stock-available font-bold flex items-center gap-0.5 shrink-0">
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

        <!-- Real Dynamic Pagination Links -->
        @if(method_exists($products, 'links'))
          {{ $products->links('frontend.layouts.includes.pagination') }}
        @endif
      @endif

    </div>

  </div>

</div>

<!-- Mobile Filter Slide Drawer -->
<div id="mobile-filter-drawer" class="fixed inset-0 z-[105] bg-black/60 backdrop-blur-xs hidden">
  <div class="fixed top-0 right-0 bottom-0 w-80 max-w-full bg-surface-card p-5 overflow-y-auto space-y-6 shadow-2xl flex flex-col justify-between">
    <div class="space-y-6">
      <div class="flex items-center justify-between pb-3 border-b border-surface-border">
        <h3 class="text-sm font-bold text-text-display flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary text-[18px]">tune</span>
          <span>Filter Gear</span>
        </h3>
        <button type="button" onclick="toggleMobileFilter()" class="text-text-muted hover:text-primary cursor-pointer">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>

      <form id="mobile-category-filter-form" method="GET" action="{{ url()->current() }}" class="space-y-6">
        <input type="hidden" name="sort" value="{{ $selectedSort ?? request('sort', 'featured') }}">

        @if(!empty($availableBrands) && $availableBrands->isNotEmpty())
        <div>
          <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2">Brands</h4>
          <div class="space-y-2 text-xs max-h-48 overflow-y-auto pr-1">
            @foreach($availableBrands as $brand)
              <label class="flex items-center justify-between text-text-muted cursor-pointer select-none">
                <span class="flex items-center gap-2 truncate">
                  <input type="checkbox" name="brand[]" value="{{ $brand->id }}" {{ in_array((string)$brand->id, (array)($selectedBrands ?? [])) || in_array($brand->name, (array)($selectedBrands ?? [])) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary" />
                  <span class="truncate">{{ $brand->name }}</span>
                </span>
                <span class="text-[10px] text-text-muted bg-surface-container px-1.5 py-0.5 rounded font-bold shrink-0">{{ $brand->products_count }}</span>
              </label>
            @endforeach
          </div>
        </div>
        @endif

        <div class="pt-4 border-t border-surface-border">
          <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2">Max Price</h4>
          <input type="range" name="max_price" min="{{ $priceMinBound }}" max="{{ $priceMaxBound }}" step="{{ max(500, round(($priceMaxBound - $priceMinBound) / 50)) }}" value="{{ request('max_price', $priceMaxBound) }}" class="w-full accent-primary cursor-pointer" oninput="document.getElementById('mobile-price-range-label').textContent = 'Max: ৳ ' + parseInt(this.value).toLocaleString()" />
          <div class="flex items-center justify-between text-xs font-bold text-on-surface mt-1">
            <span>৳ {{ number_format($priceMinBound) }}</span>
            <span id="mobile-price-range-label" class="text-primary font-extrabold">Max: ৳ {{ number_format(request('max_price', $priceMaxBound)) }}</span>
          </div>
        </div>

        <div class="pt-4 border-t border-surface-border">
          <h4 class="text-xs font-extrabold text-on-surface uppercase tracking-wider mb-2">Stock Status</h4>
          <div class="space-y-2 text-xs">
            <label class="flex items-center gap-2 text-text-muted cursor-pointer select-none">
              <input type="radio" name="stock" value="all" {{ (!request()->filled('stock') || request('stock') === 'all') ? 'checked' : '' }} class="text-primary focus:ring-primary" />
              <span>All Items</span>
            </label>
            <label class="flex items-center gap-2 text-text-muted cursor-pointer select-none">
              <input type="radio" name="stock" value="in_stock" {{ request('stock') === 'in_stock' ? 'checked' : '' }} class="text-primary focus:ring-primary" />
              <span class="text-stock-available font-bold">In Stock Only</span>
            </label>
            <label class="flex items-center gap-2 text-text-muted cursor-pointer select-none">
              <input type="radio" name="stock" value="pre_order" {{ request('stock') === 'pre_order' ? 'checked' : '' }} class="text-primary focus:ring-primary" />
              <span>Pre-Booking Ready</span>
            </label>
          </div>
        </div>

        <div class="pt-4 flex gap-2">
          <a href="{{ url()->current() }}" class="flex-1 text-center bg-surface-container text-on-surface text-xs font-bold py-3 rounded-xl hover:bg-surface-border transition-colors">
            Reset
          </a>
          <button type="submit" class="flex-1 bg-primary text-white text-xs font-bold py-3 rounded-xl hover:bg-primary-hover transition-colors shadow-sm cursor-pointer">
            Apply Filters
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // GA4 / GTM DataLayer Category View Item List
  @php
    $dlCatItems = [];
    $rawItems = method_exists($products, 'items') ? $products->items() : (is_array($products) ? $products : []);
    if (!empty($rawItems)) {
      $catItemCounter = 1;
      foreach (array_slice(array_values($rawItems), 0, 24) as $idx => $p) {
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
    const label = document.getElementById('price-range-label');
    if (label) {
      label.textContent = `Max: ৳ ${parseInt(val).toLocaleString()}`;
    }
  }

  function toggleMobileFilter() {
    const drawer = document.getElementById('mobile-filter-drawer');
    if (drawer) {
      drawer.classList.toggle('hidden');
    }
  }

  function sortProducts(criteria) {
    const sortInput = document.getElementById('filter-sort-input');
    if (sortInput) {
      sortInput.value = criteria;
    }
    const form = document.getElementById('category-filter-form');
    if (form) {
      form.submit();
    } else {
      const url = new URL(window.location.href);
      url.searchParams.set('sort', criteria);
      url.searchParams.set('page', '1');
      window.location.href = url.toString();
    }
  }
</script>
@endpush
