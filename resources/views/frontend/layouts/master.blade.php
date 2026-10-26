<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport"/>
  <title>@yield('title', ($siteName ?? 'CAMERA BD') . ' | Bangladesh Premier Cine & Broadcast Audio Store')</title>
  
  <!-- Dynamic Favicon / System Icon -->
  <link rel="shortcut icon" href="{{ $siteFavicon ?? (function_exists('get_setting') && get_setting('system_icon') ? asset('uploads/logo/' . get_setting('system_icon')) : asset('uploads/logo/' . (get_setting('system_logo') ?? ''))) }}"/>
  <link rel="icon" type="image/png" href="{{ $siteFavicon ?? (function_exists('get_setting') && get_setting('system_icon') ? asset('uploads/logo/' . get_setting('system_icon')) : asset('uploads/logo/' . (get_setting('system_logo') ?? ''))) }}"/>

  @include('frontend.layouts.includes.datalayer-head')
  @include('frontend.layouts.includes.meta-pixel')

  <link rel="dns-prefetch" href="https://fonts.googleapis.com"/>
  <link rel="dns-prefetch" href="https://fonts.gstatic.com"/>
  <link rel="dns-prefetch" href="https://cdn.tailwindcss.com"/>
  <link rel="dns-prefetch" href="https://masudoff.com"/>
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link crossorigin="" href="https://masudoff.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#b70011",
            "primary-container": "#dc2626",
            "on-primary": "#ffffff",
            "on-primary-container": "#fff6f5",
            "primary-fixed": "#ffdad6",
            "primary-fixed-dim": "#ffb4ab",
            "on-primary-fixed": "#410002",
            "on-primary-fixed-variant": "#93000b",
            "rec-red-base": "#DC2626",
            "rec-red-hover": "#B91C1C",
            "rec-red-subtle": "#FEF2F2",
            "broadcast-teal": "#0D9488",
            "broadcast-teal-subtle": "#F0FDFA",
            "secondary": "#006a61",
            "secondary-container": "#86f2e4",
            "secondary-fixed": "#89f5e7",
            "secondary-fixed-dim": "#6bd8cb",
            "on-secondary": "#ffffff",
            "on-secondary-fixed": "#00201d",
            "on-secondary-fixed-variant": "#005049",
            "on-secondary-container": "#006f66",
            "surface": "#f8f9ff",
            "surface-base": "#F8FAFC",
            "surface-card": "#FFFFFF",
            "surface-border": "#E2E8F0",
            "surface-container": "#e5eeff",
            "surface-container-low": "#eff4ff",
            "surface-container-high": "#dce9ff",
            "surface-container-highest": "#d3e4fe",
            "surface-container-lowest": "#ffffff",
            "surface-bright": "#f8f9ff",
            "surface-dim": "#cbdbf5",
            "surface-variant": "#d3e4fe",
            "surface-tint": "#bf0715",
            "on-surface": "#0b1c30",
            "on-surface-variant": "#5c403c",
            "inverse-surface": "#213145",
            "inverse-on-surface": "#eaf1ff",
            "inverse-primary": "#ffb4ab",
            "text-display": "#090D16",
            "text-muted": "#64748B",
            "price-tk": "#0F172A",
            "stock-available": "#16A34A",
            "outline": "#916f6b",
            "outline-variant": "#e6bdb8",
            "tertiary": "#51596f",
            "tertiary-container": "#6a7188",
            "tertiary-fixed": "#dae2fd",
            "tertiary-fixed-dim": "#bec6e0",
            "on-tertiary": "#ffffff",
            "on-tertiary-fixed": "#131b2e",
            "on-tertiary-fixed-variant": "#3f465c",
            "on-tertiary-container": "#f8f7ff",
            "error": "#ba1a1a",
            "error-container": "#ffdad6",
            "on-error": "#ffffff",
            "on-error-container": "#93000a"
          },
          borderRadius: {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "2xl": "1rem",
            "full": "9999px"
          },
          spacing: {
            "space-xs": "0.25rem",
            "space-sm": "0.5rem",
            "space-md": "0.75rem",
            "space-lg": "1.25rem",
            "space-xl": "2rem",
            "gutter": "0.75rem",
            "gutter-desktop": "1.25rem",
            "margin": "1rem",
            "margin-desktop": "2rem"
          },
          fontFamily: {
            "sans": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
            "headline-md": ["Plus Jakarta Sans", "sans-serif"],
            "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
            "display-lg": ["Plus Jakarta Sans", "sans-serif"],
            "body-lg": ["Plus Jakarta Sans", "sans-serif"],
            "body-md": ["Plus Jakarta Sans", "sans-serif"],
            "body-sm": ["Plus Jakarta Sans", "sans-serif"],
            "price-headline": ["Plus Jakarta Sans", "sans-serif"],
            "price-strikethrough": ["Plus Jakarta Sans", "sans-serif"],
            "label-lg": ["Plus Jakarta Sans", "sans-serif"],
            "label-md": ["Plus Jakarta Sans", "sans-serif"],
            "label-sm": ["Plus Jakarta Sans", "sans-serif"]
          },
          fontSize: {
            "display-lg": ["32px", { lineHeight: "38px", letterSpacing: "-0.03em", fontWeight: "800" }],
            "headline-lg": ["22px", { lineHeight: "28px", letterSpacing: "-0.02em", fontWeight: "700" }],
            "headline-md": ["18px", { lineHeight: "24px", letterSpacing: "-0.015em", fontWeight: "700" }],
            "headline-sm": ["15px", { lineHeight: "20px", letterSpacing: "-0.01em", fontWeight: "600" }],
            "body-lg": ["15px", { lineHeight: "22px", fontWeight: "400" }],
            "body-md": ["13px", { lineHeight: "18px", fontWeight: "400" }],
            "body-sm": ["12px", { lineHeight: "16px", fontWeight: "400" }],
            "price-headline": ["16px", { lineHeight: "20px", letterSpacing: "-0.01em", fontWeight: "800" }],
            "price-strikethrough": ["11px", { lineHeight: "14px", fontWeight: "500" }],
            "label-lg": ["13px", { lineHeight: "16px", letterSpacing: "0.01em", fontWeight: "600" }],
            "label-md": ["11px", { lineHeight: "14px", letterSpacing: "0.02em", fontWeight: "600" }],
            "label-sm": ["10px", { lineHeight: "12px", letterSpacing: "0.04em", fontWeight: "700" }]
          }
        }
      }
    };
  </script>

  <style>
    @layer utilities {
      .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }
      .pt-safe { padding-top: env(safe-area-inset-top, 0px); }
    }
    ::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    @keyframes slideInDown {
      from { transform: translateY(-100%); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    .animate-toast { animation: slideInDown 0.3s ease-out forwards; }
  </style>
  @stack('styles')
</head>

<body class="bg-surface-base font-sans text-on-surface antialiased min-h-screen flex flex-col selection:bg-primary selection:text-on-primary">
  @include('frontend.layouts.includes.datalayer-body')

  <!-- Toast Notification Container -->
  <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

  <!-- ========================================== -->
  <!-- 1. MOBILE HEADER (< lg) -->
  <!-- ========================================== -->
  <header class="lg:hidden fixed top-0 w-full z-50 pt-safe bg-surface-card/95 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="h-14 px-4 flex items-center justify-between gap-2 border-b border-surface-border/60">
      <!-- Logo + Brand -->
      <div class="flex items-center gap-2 min-w-0 flex-1">
        <a class="flex items-center shrink-0" href="{{ url('/') }}">
          <img alt="{{ $siteName ?? 'CAMERA BD' }} Logo" class="h-7 w-auto object-contain max-w-[100px]" src="{{ $siteLogo }}"/>
        </a>
        <div class="flex flex-col min-w-0">
          <span class="text-[11px] text-slate-900 uppercase tracking-tight leading-none font-extrabold truncate">{{ $siteName ?? 'CAMERA BD' }}</span>
          <span class="text-[8px] text-[#006241] truncate leading-tight font-bold uppercase tracking-wide">Pro Cine & Audio</span>
        </div>
      </div>
      
      <!-- Right Action Icons -->
      <div class="flex items-center gap-1 shrink-0">
        <a aria-label="Call Hotline" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container transition-colors" href="tel:{{ $whatsappDigits ?? '8801917256756' }}">
          <span class="material-symbols-outlined text-[20px]">support_agent</span>
        </a>
        <button aria-label="Search" id="mobile-search-btn" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container transition-colors">
          <span class="material-symbols-outlined text-[20px]">search</span>
        </button>
        <a aria-label="Wishlist" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container transition-colors relative" href="{{ route('frontend.wishlist') }}">
          <span class="material-symbols-outlined text-[20px]">favorite</span>
          <span id="mobile-wishlist-badge" class="absolute top-1 right-1 w-4 h-4 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none">0</span>
        </a>
        <button aria-label="Shopping Cart" class="relative w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container transition-colors" onclick="toggleCartDrawer()">
          <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
          <span id="mobile-cart-badge" class="absolute top-1 right-1 w-4 h-4 bg-rec-red-base text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none">3</span>
        </button>
      </div>
    </div>

    <!-- Mobile Quick Category Strip -->
    <div class="bg-[#006241] text-white px-3 py-1.5 flex items-center gap-2 shadow-xs">
      <button type="button" onclick="openMobileCategoryDrawer()" class="bg-white text-slate-900 px-2.5 py-1 rounded-md font-bold text-xs flex items-center gap-1 shrink-0 shadow-xs active:scale-95 transition-transform">
        <span class="material-symbols-outlined text-[17px] text-[#006241]">menu</span>
        <span>Categories</span>
        <span class="material-symbols-outlined text-[15px] text-slate-500">expand_more</span>
      </button>
      <div class="flex-1 flex items-center gap-1.5 overflow-x-auto no-scrollbar text-[11px] whitespace-nowrap">
        <a href="{{ url('/#hot-deals') }}" class="bg-amber-400 text-slate-900 font-bold px-2 py-0.5 rounded flex items-center gap-0.5 shrink-0">
          <span class="material-symbols-outlined text-[12px]">local_fire_department</span> Deals
        </a>
        @if(isset($navCategories) && count($navCategories) > 0)
          @foreach(array_slice($navCategories, 0, 7) as $topNavCat)
            <a href="{{ $topNavCat['view_all_url'] ?? route('frontend.category', $topNavCat['slug']) }}" class="bg-white/15 hover:bg-white/25 text-white px-2 py-0.5 rounded shrink-0 transition-colors font-medium">
              {{ $topNavCat['name'] }}
            </a>
          @endforeach
        @endif
        <a href="{{ route('frontend.categories') }}" class="bg-emerald-800 text-emerald-100 font-semibold px-2 py-0.5 rounded shrink-0 transition-colors">All →</a>
      </div>
    </div>
  </header>

  <!-- ========================================== -->
  <!-- 2. DESKTOP HEADER (>= lg) -->
  <!-- ========================================== -->
  <header class="hidden lg:block fixed top-0 left-0 right-0 z-50 bg-surface-card/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <!-- Main Navigation Bar -->
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between gap-6 border-b border-surface-border">
      <!-- Logo -->
      <a class="flex items-center gap-2.5 shrink-0 group" href="{{ url('/') }}">
        <img alt="{{ $siteName ?? 'CAMERA BD' }} Logo" class="h-8 md:h-9 w-auto object-contain max-w-[130px] transition-transform group-hover:scale-105" src="{{ $siteLogo }}"/>
        <div class="flex flex-col">
          <span class="text-xs md:text-sm font-extrabold tracking-tight text-slate-900 uppercase leading-tight">{{ $siteName ?? 'CAMERA BD' }}</span>
          <span class="text-[9px] text-[#006241] font-bold uppercase tracking-wider leading-none">Pro Cine & Broadcast</span>
        </div>
      </a>

      <!-- Desktop Search Bar -->
      <div class="flex-1 max-w-xl relative" id="desktop-search-container">
        <form action="{{ route('frontend.search') }}" method="GET" class="relative flex items-center">
          <input id="desktop-search-input" class="w-full bg-surface-container rounded-full pl-11 pr-24 py-2.5 text-sm text-on-surface border border-surface-border focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-text-muted" placeholder="Search Sony FX30, DJI, Hollyland Mic, Datavideo..." type="search" name="q" value="{{ request('q') ?? '' }}" autocomplete="off"/>
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-text-muted pointer-events-none">search</span>
          <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-primary hover:bg-primary-hover text-white text-xs font-bold px-4 py-1.5 rounded-full transition-colors">
            Search
          </button>
        </form>

        <!-- Live Instant Search Dropdown Box -->
        <div id="desktop-search-results" class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-[0_15px_50px_rgba(0,0,0,0.18)] border border-slate-200 z-50 overflow-hidden hidden max-h-[480px] overflow-y-auto text-left">
          <!-- Populated by JS -->
        </div>
      </div>

      <!-- Quick Action Icons -->
      <div class="flex items-center gap-3 shrink-0">
        <!-- Hotline -->
        <a class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-surface-container text-on-surface transition-all border border-transparent hover:border-surface-border" href="tel:{{ $whatsappDigits ?? '8801917256756' }}">
          <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-[20px]">phone_in_talk</span>
          </div>
          <div class="flex flex-col text-left">
            <span class="text-[10px] text-text-muted font-bold uppercase leading-none">Hotline Support</span>
            <span class="text-xs font-bold text-on-surface mt-0.5">{{ $sitePhoneFormatted ?? '+880-1917-256-756' }}</span>
          </div>
        </a>

        <!-- Wishlist -->
        <a class="relative w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-container text-on-surface hover:text-primary transition-colors border border-surface-border" href="{{ route('frontend.wishlist') }}" title="Wishlist">
          <span class="material-symbols-outlined text-[22px]">favorite_border</span>
          <span id="desktop-wishlist-count" class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none ring-2 ring-surface-card">0</span>
        </a>

        <!-- Cart Trigger Button -->
        <button class="relative flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all active:scale-95" onclick="toggleCartDrawer()">
          <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
          <span>Cart</span>
          <span id="desktop-cart-badge" class="bg-white text-primary text-xs font-bold px-2 py-0.5 rounded-full">3</span>
        </button>
      </div>
    </div>

    <!-- Category Mega Navigation Bar -->
    @include('frontend.layouts.includes.mega-menu')
  </header>

  <!-- Mobile Category & Subcategory Drawer -->
  @include('frontend.layouts.includes.mobile-category-drawer')

  <!-- Mobile Search Modal -->
  <div id="mobile-search-modal" class="fixed inset-0 z-[999] bg-surface-card hidden flex-col">
    <div class="h-16 px-4 border-b border-surface-border flex items-center gap-3">
      <form action="{{ route('frontend.search') }}" method="GET" class="flex-1 relative flex items-center">
        <input id="mobile-search-input" name="q" class="w-full bg-surface-container rounded-xl pl-10 pr-16 py-2.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Search Camera, Drone, Audio..." type="search" autocomplete="off"/>
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-[20px]">search</span>
        <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg">Search</button>
      </form>
      <button id="mobile-search-close" class="p-2 text-text-muted hover:text-primary font-bold">Cancel</button>
    </div>
    
    <!-- Mobile Search Live Results -->
    <div id="mobile-search-results" class="flex-1 p-4 overflow-y-auto hidden">
      <!-- Populated by JS -->
    </div>

    <div id="mobile-search-default" class="p-4 overflow-y-auto">
      <span class="text-xs text-text-muted uppercase font-bold block mb-2">Trending Searches</span>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('frontend.search', ['q' => 'Pocket Camera']) }}" class="bg-surface-container px-3 py-1.5 rounded-lg text-xs hover:bg-primary hover:text-white transition-colors">Pocket Camera</a>
        <a href="{{ route('frontend.search', ['q' => 'Drone']) }}" class="bg-surface-container px-3 py-1.5 rounded-lg text-xs hover:bg-primary hover:text-white transition-colors">Drone</a>
        <a href="{{ route('frontend.search', ['q' => 'Action Camera']) }}" class="bg-surface-container px-3 py-1.5 rounded-lg text-xs hover:bg-primary hover:text-white transition-colors">Action Camera</a>
        <a href="{{ route('frontend.search', ['q' => 'Lighting']) }}" class="bg-surface-container px-3 py-1.5 rounded-lg text-xs hover:bg-primary hover:text-white transition-colors">Lighting</a>
      </div>
    </div>
  </div>

  <!-- Cart Slide-Over Drawer -->
  <div id="cart-drawer-overlay" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-xs hidden transition-opacity" onclick="toggleCartDrawer()"></div>
  <div id="cart-drawer" class="fixed top-0 right-0 bottom-0 z-[101] w-full max-w-md bg-surface-card shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <div class="p-4 border-b border-surface-border flex items-center justify-between bg-surface-container-low">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[24px]">shopping_bag</span>
        <h3 class="font-bold text-text-display">Your Production Bag</h3>
        <span id="cart-drawer-count" class="bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full">3</span>
      </div>
      <button class="w-8 h-8 rounded-full hover:bg-surface-container flex items-center justify-center text-text-muted" onclick="toggleCartDrawer()">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>

    <!-- Items List -->
    <div id="cart-drawer-items" class="flex-1 overflow-y-auto p-4 space-y-3">
      <!-- Dynamically Populated by CameraCart.renderDrawer() -->
    </div>

    <!-- Drawer Footer -->
    <div class="p-4 border-t border-surface-border bg-surface-card space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-sm text-text-muted">Subtotal:</span>
        <span id="cart-drawer-subtotal" class="text-base font-bold text-primary">৳ 0 TK</span>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('frontend.cart') }}" class="flex-1 border border-primary text-primary hover:bg-primary/5 text-xs font-bold py-2.5 rounded-xl text-center transition-colors">
          View Cart Page
        </a>
        <a href="{{ route('frontend.checkout') }}" class="flex-1 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl text-center transition-colors flex items-center justify-center gap-1 shadow-sm">
          <span class="material-symbols-outlined text-[16px]">lock</span> Checkout
        </a>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MAIN CONTENT CONTAINER -->
  <!-- ========================================== -->
  <main class="flex-1 w-full pt-[92px] lg:pt-[132px] pb-24 lg:pb-0 bg-surface-base">
    @yield('content')
  </main>

  <!-- ========================================== -->
  <!-- MOBILE BOTTOM NAVIGATION (Fixed) -->
  <!-- ========================================== -->
  <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-surface-card/95 backdrop-blur-lg border-t border-surface-border px-4 py-2 flex items-center justify-around pb-safe">
    <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 text-primary">
      <span class="material-symbols-outlined text-[22px]">home</span>
      <span class="text-[10px] font-bold">Home</span>
    </a>
    <button type="button" onclick="openMobileCategoryDrawer()" class="flex flex-col items-center gap-0.5 text-text-muted hover:text-primary">
      <span class="material-symbols-outlined text-[22px]">category</span>
      <span class="text-[10px] font-bold">Categories</span>
    </button>
    <a href="{{ route('frontend.wishlist') }}" class="flex flex-col items-center gap-0.5 text-text-muted hover:text-primary relative">
      <span class="material-symbols-outlined text-[22px]">favorite</span>
      <span class="text-[10px] font-bold">Wishlist</span>
      <span id="mobile-bottom-wishlist-badge" class="absolute -top-1 right-3 w-4 h-4 bg-primary text-white text-[8px] font-bold rounded-full flex items-center justify-center leading-none">0</span>
    </a>
    <a href="{{ route('frontend.cart') }}" class="flex flex-col items-center gap-0.5 text-text-muted hover:text-primary relative">
      <span class="material-symbols-outlined text-[22px]">shopping_bag</span>
      <span class="text-[10px] font-bold">Cart</span>
      <span id="mobile-cart-badge" class="absolute -top-1 right-1 w-4 h-4 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center">0</span>
    </a>
    <a href="{{ route('frontend.checkout') }}" class="flex flex-col items-center gap-0.5 text-text-muted hover:text-primary">
      <span class="material-symbols-outlined text-[22px]">shopping_cart_checkout</span>
      <span class="text-[10px] font-bold">Checkout</span>
    </a>
  </div>

  <!-- ========================================== -->
  <!-- FOOTER -->
  <!-- ========================================== -->
  @include('frontend.layouts.includes.footer')

  <!-- Global Scripts -->
  <script>
    function showToast(message, type = 'info') {
      const container = document.getElementById('toast-container');
      if (!container) return;
      const toast = document.createElement('div');
      toast.className = `animate-toast max-w-sm px-4 py-3 rounded-xl shadow-lg flex items-center gap-2.5 text-xs font-bold text-white pointer-events-auto transition-all ${
        type === 'success' ? 'bg-emerald-600' : type === 'error' ? 'bg-red-600' : 'bg-primary'
      }`;
      toast.innerHTML = `<span class="material-symbols-outlined text-[18px]">${type === 'success' ? 'check_circle' : 'info'}</span><span>${message}</span>`;
      container.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }

    // ============================================
    // UNIVERSAL CAMERA CART ENGINE (LocalStorage)
    // ============================================
    window.CameraCart = {
      key: 'cb_cart',
      get: function() {
        try {
          const data = localStorage.getItem(this.key);
          return data ? JSON.parse(data) : [];
        } catch(e) {
          return [];
        }
      },
      save: function(items) {
        try {
          localStorage.setItem(this.key, JSON.stringify(items));
        } catch(e) {}
        this.sync();
      },
      add: function(item, openDrawer = true) {
        let items = this.get();
        const targetName = String(item.name || '').trim().toLowerCase();
        const targetId = item.id ? String(item.id) : null;
        
        const idx = items.findIndex(i => {
          if (targetId && i.id && String(i.id) === targetId) return true;
          return String(i.name || '').trim().toLowerCase() === targetName;
        });

        const qtyToAdd = parseInt(item.qty) || 1;

        if (idx > -1) {
          items[idx].qty = (parseInt(items[idx].qty) || 1) + qtyToAdd;
        } else {
          items.push({
            id: item.id || null,
            name: item.name || 'Cinema Product',
            price: parseFloat(item.price) || 0,
            image: item.image || '',
            barcode: item.barcode || item.sku || '',
            url: item.url || '#',
            qty: qtyToAdd
          });
        }
        this.save(items);
        showToast(`Added "${item.name}" to cart!`, 'success');

        // GA4 / GTM DataLayer AddToCart Event
        if (typeof window.pushToDataLayer === 'function') {
          window.pushToDataLayer('add_to_cart', {
            currency: 'BDT',
            value: (parseFloat(item.price) || 0) * (parseInt(qtyToAdd) || 1),
            items: [{
              item_id: String(item.id || item.barcode || item.sku || item.name),
              item_name: item.name || 'Cinema Product',
              affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
              currency: 'BDT',
              price: parseFloat(item.price) || 0,
              quantity: parseInt(qtyToAdd) || 1
            }]
          });
        }

        // Meta Pixel AddToCart Event (Only if GTM is not active, otherwise GTM handles it via add_to_cart DataLayer)
        try {
          if (!window._hasGtm && typeof fbq === 'function') {
            fbq('track', 'AddToCart', {
              content_name: item.name || 'Cinema Product',
              content_ids: item.id ? [String(item.id)] : [String(item.barcode || item.name)],
              content_type: 'product',
              value: (parseFloat(item.price) || 0) * (parseInt(qtyToAdd) || 1),
              currency: 'BDT'
            });
          }
        } catch(e) {
          console.warn('Meta Pixel AddToCart error:', e);
        }

        if (openDrawer) {
          this.openDrawer();
        }
      },
      updateQty: function(index, delta) {
        let items = this.get();
        if (items[index]) {
          const newQty = (parseInt(items[index].qty) || 1) + delta;
          if (newQty <= 0) {
            this.remove(index);
          } else {
            items[index].qty = newQty;
            this.save(items);
          }
        }
      },
      setQty: function(index, qty) {
        let items = this.get();
        if (items[index]) {
          const newQty = parseInt(qty) || 1;
          if (newQty <= 0) {
            this.remove(index);
          } else {
            items[index].qty = newQty;
            this.save(items);
          }
        }
      },
      remove: function(index) {
        let items = this.get();
        if (items[index]) {
          const removedItem = items[index];
          const removedName = removedItem.name;

          // GA4 / GTM DataLayer RemoveFromCart Event
          if (typeof window.pushToDataLayer === 'function') {
            window.pushToDataLayer('remove_from_cart', {
              currency: 'BDT',
              value: (parseFloat(removedItem.price) || 0) * (parseInt(removedItem.qty) || 1),
              items: [{
                item_id: String(removedItem.id || removedItem.barcode || removedItem.sku || removedItem.name),
                item_name: removedItem.name || 'Cinema Product',
                affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
                currency: 'BDT',
                price: parseFloat(removedItem.price) || 0,
                quantity: parseInt(removedItem.qty) || 1
              }]
            });
          }

          items.splice(index, 1);
          this.save(items);
          showToast(`Removed "${removedName}" from cart`, 'info');
        }
      },
      clear: function() {
        this.save([]);
        showToast('Cart cleared', 'info');
      },
      clearState: function() {
        this.sync();
      },
      getCount: function() {
        const items = this.get();
        return items.reduce((sum, i) => sum + (parseInt(i.qty) || 1), 0);
      },
      getSubtotal: function() {
        const items = this.get();
        return items.reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (parseInt(i.qty) || 1)), 0);
      },
      sync: function() {
        const count = this.getCount();
        const subtotal = this.getSubtotal();

        // Update all badges across page
        document.querySelectorAll('#top-cart-count, #desktop-cart-badge, #mobile-cart-badge, #cart-drawer-count').forEach(el => {
          if (el) el.textContent = count;
        });

        const subtotalFormatted = '৳ ' + Math.round(subtotal).toLocaleString('en-US') + ' TK';
        document.querySelectorAll('#cart-drawer-subtotal, #desktop-cart-total').forEach(el => {
          if (el) el.textContent = subtotalFormatted;
        });

        this.renderDrawer();
        
        window.dispatchEvent(new CustomEvent('cameracart:updated', { 
          detail: { items: this.get(), count: count, subtotal: subtotal } 
        }));
      },
      openDrawer: function() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        if (drawer && overlay) {
          drawer.classList.remove('translate-x-full');
          overlay.classList.remove('hidden');
        }
      },
      closeDrawer: function() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        if (drawer && overlay) {
          drawer.classList.add('translate-x-full');
          overlay.classList.add('hidden');
        }
      },
      renderDrawer: function() {
        const container = document.getElementById('cart-drawer-items');
        if (!container) return;

        const items = this.get();
        if (items.length === 0) {
          container.innerHTML = `
            <div class="py-16 text-center text-text-muted text-xs">
              <span class="material-symbols-outlined text-[42px] text-slate-300 block mb-2">shopping_bag</span>
              <p class="font-bold text-slate-700">Your bag is currently empty</p>
              <p class="text-[11px] text-slate-400 mt-1">Browse our collection and add camera gear!</p>
            </div>
          `;
          return;
        }

        let html = '';
        items.forEach((item, idx) => {
          const imgHtml = item.image 
            ? `<img src="${item.image}" alt="${item.name}" class="w-full h-full object-contain" />`
            : `<span class="material-symbols-outlined text-primary text-[20px]">videocam</span>`;

          html += `
            <div class="flex items-center gap-3 p-3 bg-surface-base rounded-2xl border border-surface-border">
              <div class="w-14 h-14 bg-white rounded-xl p-1 shrink-0 border border-surface-border flex items-center justify-center">
                ${imgHtml}
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-xs text-text-display truncate">${item.name}</h4>
                <div class="text-xs font-bold text-primary mt-0.5">৳ ${Math.round(parseFloat(item.price) || 0).toLocaleString('en-US')} TK</div>
                <div class="flex items-center justify-between mt-1 text-[11px]">
                  <div class="flex items-center border border-surface-border bg-surface-container rounded-md p-0.5">
                    <button onclick="CameraCart.updateQty(${idx}, -1)" class="w-5 h-5 rounded bg-surface-card flex items-center justify-center font-bold hover:bg-primary hover:text-white transition-colors">-</button>
                    <span class="w-6 text-center font-bold">${item.qty || 1}</span>
                    <button onclick="CameraCart.updateQty(${idx}, 1)" class="w-5 h-5 rounded bg-surface-card flex items-center justify-center font-bold hover:bg-primary hover:text-white transition-colors">+</button>
                  </div>
                  <button onclick="CameraCart.remove(${idx})" class="text-rec-red-base hover:underline font-bold text-[10px]">Remove</button>
                </div>
              </div>
            </div>
          `;
        });
        container.innerHTML = html;
      }
    };

    function toggleCartDrawer() {
      const drawer = document.getElementById('cart-drawer');
      if (drawer && drawer.classList.contains('translate-x-full')) {
        CameraCart.openDrawer();
      } else {
        CameraCart.closeDrawer();
      }
    }

    function addToCart(title, price, image, id = null, barcode = '', url = '#') {
      CameraCart.add({ id: id, name: title, price: price, image: image, barcode: barcode, url: url, qty: 1 }, true);
    }

    function buyNow(title, price, image, id = null, barcode = '', url = '#') {
      CameraCart.add({ id: id, name: title, price: price, image: image, barcode: barcode, url: url, qty: 1 }, false);
      window.location.href = "{{ route('frontend.checkout') }}";
    }

    // CameraWishlist Manager
    window.CameraWishlist = {
      key: 'camerawishlist_v1',
      get: function() {
        try {
          const data = localStorage.getItem(this.key);
          return data ? JSON.parse(data) : [];
        } catch (e) {
          return [];
        }
      },
      save: function(items) {
        try {
          localStorage.setItem(this.key, JSON.stringify(items));
        } catch (e) {}
        this.sync();
      },
      has: function(idOrName) {
        const items = this.get();
        return items.some(i => (i.id && i.id == idOrName) || (i.name && i.name.toLowerCase() === String(idOrName).toLowerCase()));
      },
      add: function(item) {
        let items = this.get();
        const exists = items.some(i => (item.id && i.id == item.id) || (i.name && i.name.toLowerCase() === item.name.toLowerCase()));
        if (exists) {
          showToast(`"${item.name}" is already in your Wishlist!`, 'info');
          return false;
        }
        items.unshift({
          id: item.id || null,
          name: item.name || 'Camera Product',
          price: parseFloat(item.price) || 0,
          old_price: parseFloat(item.old_price) || 0,
          image: item.image || '',
          url: item.url || (item.slug ? `/product-details/${item.slug}` : (item.id ? `/product-details/${item.id}` : '#')),
          slug: item.slug || item.id || '',
          brand: item.brand || 'CAMERA BD',
          in_stock: item.in_stock !== false,
          reviews_count: item.reviews_count || 12
        });
        this.save(items);
        showToast(`"${item.name}" added to Wishlist!`, 'success');

        // GA4 / GTM DataLayer AddToWishlist Event
        if (typeof window.pushToDataLayer === 'function') {
          window.pushToDataLayer('add_to_wishlist', {
            currency: 'BDT',
            value: parseFloat(item.price) || 0,
            items: [{
              item_id: String(item.id || item.slug || item.name),
              item_name: item.name || 'Camera Product',
              affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
              currency: 'BDT',
              price: parseFloat(item.price) || 0,
              quantity: 1
            }]
          });
        }
        return true;
      },
      remove: function(identifier) {
        let items = this.get();
        let removedName = '';
        if (typeof identifier === 'number' && identifier < items.length) {
          removedName = items[identifier]?.name || '';
          items.splice(identifier, 1);
        } else {
          const idx = items.findIndex(i => (i.id && i.id == identifier) || (i.name && i.name.toLowerCase() === String(identifier).toLowerCase()));
          if (idx !== -1) {
            removedName = items[idx]?.name || '';
            items.splice(idx, 1);
          }
        }
        this.save(items);
        if (removedName) {
          showToast(`Removed "${removedName}" from Wishlist`, 'info');
        }
      },
      toggle: function(item, btnElement = null) {
        const name = typeof item === 'object' ? item.name : item;
        const isPresent = this.has(item.id || name);
        if (isPresent) {
          this.remove(item.id || name);
          if (btnElement) {
            const icon = btnElement.querySelector('.material-symbols-outlined') || btnElement;
            icon.textContent = 'favorite_border';
            btnElement.classList.remove('text-red-500', 'text-primary');
            btnElement.classList.add('text-text-muted', 'text-gray-300');
          }
        } else {
          const payload = typeof item === 'object' ? item : { name: item };
          this.add(payload);
          if (btnElement) {
            const icon = btnElement.querySelector('.material-symbols-outlined') || btnElement;
            icon.textContent = 'favorite';
            btnElement.classList.add('text-red-500');
            btnElement.classList.remove('text-text-muted', 'text-gray-300');
          }
        }
        this.sync();
      },
      clear: function() {
        this.save([]);
        showToast('Wishlist cleared', 'info');
      },
      addAllToCart: function() {
        const items = this.get();
        if (items.length === 0) {
          showToast('Your wishlist is empty!', 'error');
          return;
        }
        if (window.CameraCart) {
          items.forEach(i => {
            CameraCart.add({
              id: i.id,
              name: i.name,
              price: i.price,
              image: i.image,
              barcode: i.sku || i.barcode || '',
              url: i.url || '#',
              qty: 1
            }, false);
          });
          CameraCart.sync();
          CameraCart.openDrawer();
          showToast(`All ${items.length} items added to Cart!`, 'success');
        }
      },
      getCount: function() {
        return this.get().length;
      },
      sync: function() {
        const count = this.getCount();
        document.querySelectorAll('#top-wishlist-count, #desktop-wishlist-count, #mobile-wishlist-count, #mobile-wishlist-badge, #mobile-bottom-wishlist-badge, #wishlist-header-count, #wishlist-page-count').forEach(el => {
          if (el) el.textContent = count;
        });

        // Highlight active wishlist items
        document.querySelectorAll('[data-wishlist-product], [data-wishlist-id], .wishlist-btn').forEach(btn => {
          const prodName = btn.getAttribute('data-wishlist-product');
          const prodId = btn.getAttribute('data-wishlist-id');
          if (prodName || prodId) {
            const icon = btn.querySelector('.material-symbols-outlined') || btn;
            if (this.has(prodId || prodName)) {
              icon.textContent = 'favorite';
              btn.classList.add('text-red-500');
              btn.classList.remove('text-text-muted', 'text-gray-300');
            } else {
              icon.textContent = 'favorite_border';
              btn.classList.remove('text-red-500');
            }
          }
        });

        window.dispatchEvent(new CustomEvent('camerawishlist:updated', {
          detail: { items: this.get(), count: count }
        }));
      }
    };

    function toggleWishlist(btn, itemOrName, price = 0, image = '', slug = '', id = null, brand = '') {
      let payload = {};
      if (typeof itemOrName === 'object' && itemOrName !== null) {
        payload = itemOrName;
      } else {
        payload = {
          id: id,
          name: itemOrName,
          price: price,
          image: image,
          slug: slug,
          url: slug ? `/product-details/${slug}` : (id ? `/product-details/${id}` : '#'),
          brand: brand
        };
      }
      CameraWishlist.toggle(payload, btn);
    }

    function removeCartItem(btn, price, idx = null) {
      if (idx !== null) {
        CameraCart.remove(idx);
      } else {
        CameraCart.sync();
      }
    }

    // Initialize cart & wishlist on DOM load
    document.addEventListener('DOMContentLoaded', function() {
      CameraCart.sync();
      CameraWishlist.sync();
    });

    // Mobile Search Modal Trigger
    const mSearchBtn = document.getElementById('mobile-search-btn');
    const mSearchModal = document.getElementById('mobile-search-modal');
    const mSearchClose = document.getElementById('mobile-search-close');
    if (mSearchBtn && mSearchModal && mSearchClose) {
      mSearchBtn.addEventListener('click', () => { 
        mSearchModal.classList.remove('hidden'); 
        mSearchModal.classList.add('flex'); 
        const mInput = document.getElementById('mobile-search-input');
        if (mInput) setTimeout(() => mInput.focus(), 50);
      });
      mSearchClose.addEventListener('click', () => { 
        mSearchModal.classList.add('hidden'); 
        mSearchModal.classList.remove('flex'); 
      });
    }

    // Live AJAX Product Search Engine (Master)
    (function initMasterLiveSearch() {
      const liveSearchUrl = "{{ route('frontend.search.live') }}";
      const desktopInput = document.getElementById('desktop-search-input');
      const desktopResults = document.getElementById('desktop-search-results');
      const mobileInput = document.getElementById('mobile-search-input');
      const mobileResults = document.getElementById('mobile-search-results');
      const mobileDefault = document.getElementById('mobile-search-default');

      let debounceTimer = null;

      function renderSearchResults(data, container, isMobile = false) {
        if (!data || !data.results || data.results.length === 0) {
          container.innerHTML = `
            <div class="p-6 text-center text-xs text-slate-500 bg-white rounded-2xl">
              <span class="material-symbols-outlined text-[32px] text-slate-300 mb-1 block">search_off</span>
              <p class="font-bold text-slate-700">No matching products found</p>
              <p class="text-[11px] text-slate-400 mt-0.5">Try searching with a different model, name, or barcode</p>
            </div>
          `;
          container.classList.remove('hidden');
          return;
        }

        let html = `<div class="p-2 space-y-1 bg-white rounded-2xl">`;
        html += `<div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex justify-between border-b border-slate-100 mb-1">
          <span>Matching Products (${data.total} found)</span>
          <span class="text-primary font-bold">Instant Results</span>
        </div>`;

        data.results.forEach(p => {
          const imgHtml = p.has_real_image 
            ? `<img src="${p.image}" alt="${p.name}" class="w-11 h-11 object-contain rounded-lg border border-slate-100 p-0.5 shrink-0 bg-white" />`
            : `<div class="w-11 h-11 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 shrink-0"><span class="material-symbols-outlined text-[22px]">videocam</span></div>`;

          html += `
            <a href="${p.url}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-emerald-50/70 group transition-all border border-transparent hover:border-emerald-100">
              ${imgHtml}
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                  <span class="text-[9px] font-bold text-emerald-800 uppercase bg-emerald-100 px-1.5 py-0.2 rounded">${p.category}</span>
                  ${p.barcode ? `<span class="text-[9px] text-slate-400">#${p.barcode}</span>` : ''}
                </div>
                <h4 class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors truncate mt-0.5">${p.name}</h4>
              </div>
              <div class="text-right shrink-0">
                <span class="text-xs font-extrabold text-primary block">${p.formatted_price}</span>
                ${p.old_price ? `<span class="text-[10px] text-slate-400 line-through block">${p.old_price}</span>` : ''}
              </div>
            </a>
          `;
        });

        html += `
          <div class="pt-2 mt-1 border-t border-slate-100 px-1 pb-1">
            <a href="${data.view_all_url}" class="block text-center bg-[#006241] hover:bg-[#004d33] text-white text-xs font-bold py-2 rounded-xl transition-colors shadow-xs">
              View All ${data.total} Results for "${data.query}" →
            </a>
          </div>
        </div>`;

        container.innerHTML = html;
        container.classList.remove('hidden');
      }

      function fetchSuggestions(query, container, isMobile = false) {
        if (!query || query.trim().length === 0) {
          container.innerHTML = '';
          container.classList.add('hidden');
          if (isMobile && mobileDefault) {
            mobileDefault.classList.remove('hidden');
            mobileResults.classList.add('hidden');
          }
          return;
        }

        fetch(`${liveSearchUrl}?q=${encodeURIComponent(query.trim())}`)
          .then(res => res.json())
          .then(data => {
            if (isMobile) {
              if (mobileDefault) mobileDefault.classList.add('hidden');
              mobileResults.classList.remove('hidden');
            }
            renderSearchResults(data, container, isMobile);
          })
          .catch(err => console.error('Search error:', err));
      }

      if (desktopInput && desktopResults) {
        desktopInput.addEventListener('input', function() {
          const val = this.value;
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            fetchSuggestions(val, desktopResults, false);
          }, 200);
        });

        document.addEventListener('click', function(e) {
          if (!e.target.closest('#desktop-search-container')) {
            desktopResults.classList.add('hidden');
          }
        });
      }

      if (mobileInput && mobileResults) {
        mobileInput.addEventListener('input', function() {
          const val = this.value;
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            fetchSuggestions(val, mobileResults, true);
          }, 200);
        });
      }
    })();
  </script>
  @stack('scripts')
</body>
</html>
