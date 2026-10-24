@extends('frontend.layouts.master')

@section('title', (!empty($q) ? 'Search results for "' . $q . '"' : 'Search Products') . ' - ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-16 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-5 flex-wrap">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <a href="{{ route('frontend.categories') }}" class="hover:text-primary transition-colors">Shop</a>
    <span>/</span>
    <span class="text-on-surface font-semibold">Search Results</span>
  </nav>

  <!-- Search Header Banner -->
  <div class="bg-surface-card rounded-3xl p-6 lg:p-8 border border-surface-border shadow-xs mb-8 relative overflow-hidden">
    <div class="relative z-10 max-w-3xl">
      <div class="flex items-center gap-2 flex-wrap mb-2">
        <span class="bg-primary/10 text-primary text-[11px] font-extrabold uppercase px-3 py-1 rounded-full tracking-wider inline-flex items-center gap-1">
          <span class="material-symbols-outlined text-[14px]">search</span>
          <span>Live Catalog Search</span>
        </span>
        @if(!empty($categorySlug) && $categorySlug !== 'all')
          <span class="bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full capitalize">
            Category: {{ str_replace('-', ' ', $categorySlug) }}
          </span>
        @endif
      </div>

      <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-text-display mb-2 flex items-center gap-2 flex-wrap">
        @if(!empty($q))
          <span>Results for <span class="text-primary font-black">"{{ $q }}"</span></span>
        @else
          <span>All Available Products</span>
        @endif
        <span class="text-sm md:text-base font-bold text-text-muted bg-surface-container px-3 py-1 rounded-full border border-surface-border">
          {{ $totalCount }} {{ $totalCount === 1 ? 'item' : 'items' }} found
        </span>
      </h1>

      <p class="text-xs md:text-sm text-text-muted leading-relaxed mb-4">
        Showing authentic cinema cameras, wireless audio, broadcast equipment, and studio accessories available with official warranty across Bangladesh.
      </p>

      <!-- Active Search Form Inside Header for Easy Refinement -->
      <form action="{{ route('frontend.search') }}" method="GET" class="flex items-center gap-2 max-w-xl bg-surface-container-low rounded-2xl p-1.5 border border-surface-border focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all shadow-xs">
        <div class="relative pl-3">
          <select name="category" class="bg-transparent text-xs font-bold text-slate-700 outline-none cursor-pointer pr-6 py-2 appearance-none">
            <option value="all">All Categories</option>
            @if(isset($navCategories) && count($navCategories) > 0)
              @foreach($navCategories as $catItem)
                <option value="{{ $catItem['slug'] }}" {{ $categorySlug === $catItem['slug'] ? 'selected' : '' }}>
                  {{ $catItem['name'] }}
                </option>
              @endforeach
            @endif
          </select>
          <span class="material-symbols-outlined text-[14px] text-slate-400 absolute right-1 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
        </div>
        <div class="h-4 w-px bg-slate-300"></div>
        <div class="flex-1 flex items-center px-2">
          <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search by model, brand, barcode..." class="w-full bg-transparent text-xs text-on-surface placeholder:text-slate-400 focus:outline-none" />
        </div>
        <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors shrink-0 shadow-xs flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px]">search</span>
          <span>Search</span>
        </button>
      </form>
    </div>

    <div class="hidden lg:block absolute right-8 top-1/2 -translate-y-1/2 w-64 h-64 opacity-15 pointer-events-none text-right">
      <span class="material-symbols-outlined text-[180px] text-primary">manage_search</span>
    </div>
  </div>

  <!-- Main 2-Column Search Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- LEFT COLUMN: Filter Sidebar -->
    <aside class="hidden lg:block lg:col-span-3 bg-surface-card rounded-3xl p-5 border border-surface-border shadow-xs sticky top-28 space-y-6">
      <div class="flex items-center justify-between pb-3 border-b border-surface-border">
        <h3 class="text-sm font-bold text-text-display flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary text-[18px]">filter_list</span>
          <span>Categories</span>
        </h3>
        @if(!empty($q) || $categorySlug !== 'all')
          <a href="{{ route('frontend.search') }}" class="text-[11px] text-rec-red-base hover:underline font-bold">
            Clear
          </a>
        @endif
      </div>

      <!-- Categories List -->
      <div class="space-y-1 text-xs">
        <a href="{{ route('frontend.search', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
           class="flex items-center justify-between p-2 rounded-xl transition-colors {{ $categorySlug === 'all' ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-surface-container' }}">
          <span>All Categories</span>
          <span class="text-[10px] {{ $categorySlug === 'all' ? 'bg-white/20 text-white' : 'bg-surface-container text-slate-500' }} px-2 py-0.5 rounded-full font-bold">All</span>
        </a>

        @if(isset($navCategories) && count($navCategories) > 0)
          @foreach($navCategories as $cat)
            <a href="{{ route('frontend.search', array_merge(request()->except(['category', 'page']), ['category' => $cat['slug']])) }}"
               class="flex items-center justify-between p-2 rounded-xl transition-colors {{ $categorySlug === $cat['slug'] ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-surface-container' }}">
              <div class="flex items-center gap-2 truncate">
                <span class="material-symbols-outlined text-[16px] {{ $categorySlug === $cat['slug'] ? 'text-white' : 'text-emerald-700' }}">{{ $cat['icon'] ?? 'category' }}</span>
                <span class="truncate">{{ $cat['name'] }}</span>
              </div>
            </a>
          @endforeach
        @endif
      </div>

      <!-- Helpline Card -->
      <div class="p-4 rounded-2xl bg-surface-container-low border border-surface-border space-y-2 text-xs">
        <span class="font-bold text-slate-800 flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary text-[16px]">support_agent</span>
          <span>Need Custom Gear?</span>
        </span>
        <p class="text-slate-500 text-[11px] leading-relaxed">Call our camera specialists directly for stock availability and package quotes.</p>
        <a href="tel:{{ $whatsappDigits ?? '8801917256756' }}" class="block text-center bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 rounded-xl shadow-xs transition-colors">
          {{ $sitePhoneFormatted ?? '+880-1917-256-756' }}
        </a>
      </div>
    </aside>

    <!-- RIGHT COLUMN: Search Results & Sorting -->
    <section class="lg:col-span-9 space-y-6">
      
      <!-- Top Action Bar (Sorting & View Info) -->
      <div class="flex flex-wrap items-center justify-between gap-3 bg-surface-card p-3.5 rounded-2xl border border-surface-border shadow-xs">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
          <span>Showing</span>
          <span class="text-primary">{{ $products->count() }}</span>
          <span>of</span>
          <span class="text-primary">{{ $totalCount }}</span>
          <span>results</span>
        </div>

        <div class="flex items-center gap-3">
          <span class="text-xs text-slate-500 font-medium">Sort By:</span>
          <div class="relative">
            <select onchange="window.location.href = this.value" class="bg-surface-container text-xs font-bold text-slate-800 rounded-xl pl-3 pr-8 py-2 border border-surface-border outline-none cursor-pointer appearance-none">
              <option value="{{ route('frontend.search', array_merge(request()->except(['sort', 'page']), ['sort' => 'latest'])) }}" {{ $sort === 'latest' ? 'selected' : '' }}>Latest Added</option>
              <option value="{{ route('frontend.search', array_merge(request()->except(['sort', 'page']), ['sort' => 'price_low'])) }}" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
              <option value="{{ route('frontend.search', array_merge(request()->except(['sort', 'page']), ['sort' => 'price_high'])) }}" {{ $sort === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
              <option value="{{ route('frontend.search', array_merge(request()->except(['sort', 'page']), ['sort' => 'name'])) }}" {{ $sort === 'name' ? 'selected' : '' }}>Alphabetical (A-Z)</option>
            </select>
            <span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
          @foreach($products as $p)
            @php
              $pPrice = (float)($p->selling_price ?? 0);
              $pDisPrice = (float)($p->dis_selling_price ?? 0);
              $pDiscount = $p->discount ? round(($p->discount / ($pDisPrice ?: $pPrice)) * 100) : 0;
            @endphp
            <div class="bg-surface-card rounded-2xl p-3 border border-surface-border shadow-xs hover:shadow-md hover:border-primary/40 transition-all flex flex-col justify-between group relative">
              
              <!-- Top Badges -->
              <div class="flex items-center justify-between gap-1 mb-2 z-10">
                @if($pDiscount > 0)
                  <span class="bg-rec-red-base text-white text-[10px] font-extrabold px-2 py-0.5 rounded-md shadow-2xs">
                    -{{ $pDiscount }}%
                  </span>
                @else
                  <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                    In Stock
                  </span>
                @endif
                <button type="button" class="wishlist-btn w-7 h-7 rounded-full bg-surface-container hover:bg-rec-red-subtle text-text-muted hover:text-rec-red-base flex items-center justify-center transition-colors cursor-pointer" onclick="toggleWishlist(this, '{{ addslashes($p->name) }}', {{ (float)($p->dis_selling_price ?: $p->selling_price) }}, '{{ addslashes($p->featured_image ?? '') }}', '{{ $p->slug ?? $p->id }}', '{{ $p->id }}', '{{ addslashes($p->brand?->name ?? 'CAMERA BD') }}')" data-wishlist-product="{{ addslashes($p->name) }}" data-wishlist-id="{{ $p->id }}">
                  <span class="material-symbols-outlined text-[15px]">favorite_border</span>
                </button>
              </div>

              <!-- Product Image -->
              <a href="{{ route('frontend.product.details', $p->id) }}" class="relative block w-full aspect-square bg-surface-container rounded-xl overflow-hidden p-2 group-hover:scale-102 transition-transform flex items-center justify-center">
                @if($p->has_real_image)
                  <img src="{{ $p->featured_image }}" alt="{{ $p->name }}" class="w-full h-full object-contain mix-blend-multiply" loading="lazy" />
                @else
                  <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 group-hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[44px]">videocam</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mt-1">Official Gear</span>
                  </div>
                @endif
              </a>

              <!-- Product Details -->
              <div class="mt-3 flex-1 flex flex-col justify-between">
                <div>
                  <div class="flex items-center justify-between text-[11px] text-text-muted mb-1">
                    <span class="truncate font-medium text-emerald-700 font-bold">{{ $p->category?->name ?? 'Cinema Gear' }}</span>
                    @if(!empty($p->barcode))
                      <span class="text-[10px] text-slate-400">#{{ $p->barcode }}</span>
                    @endif
                  </div>

                  <a href="{{ route('frontend.product.details', $p->id) }}" class="block font-bold text-xs md:text-sm text-text-display group-hover:text-primary transition-colors line-clamp-2 leading-snug" title="{{ $p->name }}">
                    {{ $p->name }}
                  </a>
                </div>

                <!-- Price & Action -->
                <div class="pt-3 mt-2 border-t border-surface-border/60">
                  <div class="flex items-baseline gap-1.5 mb-2">
                    <span class="text-sm md:text-base font-extrabold text-primary">
                      ৳ {{ number_format($pPrice, 0) }}
                    </span>
                    @if($pDisPrice > $pPrice)
                      <span class="text-[11px] text-slate-400 line-through">
                        ৳ {{ number_format($pDisPrice, 0) }}
                      </span>
                    @endif
                  </div>

                  <div class="grid grid-cols-2 gap-1.5">
                    <a href="{{ route('frontend.product.details', $p->id) }}" class="border border-surface-border hover:border-primary text-slate-700 hover:text-primary text-[11px] font-bold py-1.5 rounded-xl text-center transition-colors">
                      Details
                    </a>
                    <button type="button" class="bg-primary hover:bg-primary-hover text-white text-[11px] font-bold py-1.5 rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1 active:scale-95" onclick="addToCart('{{ addslashes($p->name) }}', {{ $pPrice }}, '{{ $p->featured_image }}')">
                      <span class="material-symbols-outlined text-[14px]">add_shopping_cart</span>
                      <span>Buy</span>
                    </button>
                  </div>
                </div>

              </div>
            </div>
          @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-6">
          {{ $products->links() }}
        </div>

      @else
        <!-- No Products Found State -->
        <div class="bg-surface-card rounded-3xl p-10 text-center border border-surface-border shadow-xs space-y-5">
          <div class="w-20 h-20 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto shadow-inner">
            <span class="material-symbols-outlined text-[44px]">search_off</span>
          </div>

          <div class="max-w-md mx-auto space-y-2">
            <h3 class="text-xl font-bold text-text-display">No Gear Found</h3>
            <p class="text-xs text-text-muted leading-relaxed">
              We couldn't find any products matching <strong class="text-slate-800">"{{ $q }}"</strong>.
            </p>
          </div>

          <div class="p-4 bg-surface-container rounded-2xl max-w-lg mx-auto text-left text-xs space-y-2">
            <span class="font-bold text-slate-800 block">💡 Helpful Search Tips:</span>
            <ul class="list-disc list-inside text-slate-600 space-y-1 text-[11px]">
              <li>Check your spelling for typos (e.g. "DJI", "Sony", "Hollyland", "Action").</li>
              <li>Try using fewer or more general keywords (e.g. "pocket", "camera", "drone", "mic").</li>
              <li>Search by product model number or barcode directly.</li>
              <li>Browse our full department catalog below.</li>
            </ul>
          </div>

          <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('frontend.search') }}" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-sm transition-colors">
              Browse All Products
            </a>
            <a href="{{ url('/') }}" class="bg-surface-container hover:bg-surface-container-high text-slate-700 text-xs font-bold px-6 py-2.5 rounded-xl transition-colors">
              Return to Homepage
            </a>
          </div>
        </div>
      @endif

    </section>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // GA4 / GTM DataLayer Search & View Item List Event
  @php
    $dlSearchItems = [];
    if (isset($products) && $products->isNotEmpty()) {
      foreach ($products as $idx => $p) {
        $dlSearchItems[] = [
          'item_id' => (string)($p->id ?? ($p->barcode ?? '')),
          'item_name' => $p->name ?? '',
          'affiliation' => $siteName ?? 'CAMERA BD',
          'currency' => 'BDT',
          'index' => $idx + 1,
          'item_brand' => $p->brand?->name ?? 'CAMERA BD',
          'item_category' => $p->category?->name ?? 'Gear',
          'price' => (float)($p->selling_price ?? 0),
          'quantity' => 1
        ];
      }
    }
  @endphp

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.pushToDataLayer === 'function') {
      @if(!empty($q))
      window.pushToDataLayer('search', null, {
        search_term: '{{ addslashes($q) }}'
      });
      @endif

      @if(!empty($dlSearchItems))
      window.pushToDataLayer('view_item_list', {
        item_list_id: 'search_results',
        item_list_name: 'Search Results: {{ addslashes($q ?? "Catalog") }}',
        items: {!! json_encode($dlSearchItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
      });
      @endif
    }
  });
</script>
@endpush
