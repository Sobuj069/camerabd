<!DOCTYPE html>
<html class="light" lang="bn">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport"/>
  <meta content="{{ $siteName ?? 'CAMERA BD' }} | Premier Pro Camera & Broadcast Solutions Bangladesh" name="description"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $siteName ?? 'CAMERA BD' }} | Premier Pro Camera & Broadcast Solutions Bangladesh</title>
  
  <!-- Dynamic Favicon / System Icon -->
  <link rel="shortcut icon" href="{{ $siteFavicon ?? (function_exists('get_setting') && get_setting('system_icon') ? asset('uploads/logo/' . get_setting('system_icon')) : asset('uploads/logo/' . (get_setting('system_logo') ?? ''))) }}"/>
  <link rel="icon" type="image/png" href="{{ $siteFavicon ?? (function_exists('get_setting') && get_setting('system_icon') ? asset('uploads/logo/' . get_setting('system_icon')) : asset('uploads/logo/' . (get_setting('system_logo') ?? ''))) }}"/>

  @include('frontend.layouts.includes.datalayer-head')
  @include('frontend.layouts.includes.meta-pixel')

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  
  <!-- Material Symbols Outlined -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  
  <!-- Tailwind CSS CDN -->
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
    @layer base {
      html, body {
        width: 100%;
        margin: 0;
        padding: 0;
        scroll-behavior: smooth;
      }
      body {
        overscroll-behavior-y: none;
      }
      .pb-safe {
        padding-bottom: env(safe-area-inset-bottom, 0px);
      }
      .pt-safe {
        padding-top: env(safe-area-inset-top, 0px);
      }
    }
    ::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .line-clamp-1 {
      display: -webkit-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    @keyframes slideInDown {
      from { transform: translateY(-100%); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    .animate-toast {
      animation: slideInDown 0.3s ease-out forwards;
    }
  </style>
</head>

<body class="bg-surface-base font-body-md text-on-surface antialiased min-h-screen flex flex-col selection:bg-primary selection:text-on-primary">
  @include('frontend.layouts.includes.datalayer-body')

  <!-- Toast Notification Container -->
  <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

  <!-- ========================================== -->
  <!-- 1. MOBILE HEADER (Visible on < lg screens) -->
  <!-- ========================================== -->
  <header class="lg:hidden fixed top-0 w-full z-50 pt-safe bg-surface-card/95 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="h-14 px-4 flex items-center justify-between gap-2 border-b border-surface-border/60">
      <!-- Logo + Tag -->
      <div class="flex items-center gap-2 min-w-0 flex-1">
        <a class="flex items-center shrink-0" href="{{ url('/') }}">
          <img alt="{{ $siteName ?? 'CAMERA BD' }} Logo" class="h-8 w-auto object-contain max-w-[120px]" src="{{ $siteLogo }}"/>
        </a>
        <div class="flex flex-col min-w-0">
          <span class="font-label-sm text-[10px] text-rec-red-base uppercase tracking-wider leading-none font-bold truncate">{{ $siteName ?? 'CAMERA BD' }}</span>
          <span class="font-headline-sm text-xs text-on-surface truncate leading-tight font-bold">Pro Cine & Audio</span>
        </div>
      </div>
      
      <!-- Right Action Icons -->
      <div class="flex items-center gap-1 shrink-0">
        <!-- Hotline call -->
        <a aria-label="Call Hotline" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-broadcast-teal hover:bg-surface-container-low transition-colors" href="tel:{{ $whatsappDigits ?? '8801917256756' }}">
          <span class="material-symbols-outlined text-[20px]">support_agent</span>
        </a>
        <!-- Search Trigger -->
        <button aria-label="Search Gear" id="mobile-search-btn" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container-low transition-colors">
          <span class="material-symbols-outlined text-[20px]">search</span>
        </button>
        <!-- Wishlist -->
        <a aria-label="Wishlist" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container-low transition-colors relative" href="{{ route('frontend.wishlist') }}">
          <span class="material-symbols-outlined text-[20px]">favorite</span>
          <span id="mobile-wishlist-count" class="absolute top-1 right-1 w-4 h-4 bg-broadcast-teal text-white font-label-sm text-[9px] font-bold rounded-full flex items-center justify-center leading-none ring-2 ring-surface-card">0</span>
        </a>
        <!-- Shopping Cart -->
        <button aria-label="Shopping Cart" class="relative w-9 h-9 flex items-center justify-center rounded-full text-on-surface hover:text-primary hover:bg-surface-container-low transition-colors" onclick="toggleCartDrawer()">
          <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
          <span id="mobile-cart-badge" class="absolute top-1 right-1 w-4 h-4 bg-primary text-on-primary font-label-sm text-[9px] font-bold rounded-full flex items-center justify-center leading-none ring-2 ring-surface-card">3</span>
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
  <!-- 2. DESKTOP HEADER (Visible on >= lg screens) -->
  <!-- ========================================== -->
  <header class="hidden lg:block fixed top-0 left-0 right-0 z-50 bg-surface-card/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <!-- Main Desktop Header Row -->
    <div class="h-20 max-w-7xl mx-auto px-6 flex items-center justify-between gap-6">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="flex items-center gap-2.5 flex-shrink-0">
        <img alt="{{ $siteName ?? 'CAMERA BD' }} Logo" class="h-8 md:h-9 w-auto object-contain max-w-[130px]" src="{{ $siteLogo }}"/>
        <div class="flex flex-col">
          <span class="text-xs md:text-sm font-extrabold tracking-tight text-slate-900 uppercase leading-tight">{{ $siteName ?? 'CAMERA BD' }}</span>
          <span class="text-[9px] text-[#006241] font-bold uppercase tracking-wider leading-none">Pro Cine & Broadcast</span>
        </div>
      </a>

      <!-- Desktop Search Bar with Category Dropdown & Live AJAX Suggestions -->
      <div class="flex-1 max-w-2xl relative" id="desktop-search-container">
        <form action="{{ route('frontend.search') }}" method="GET" class="flex items-center bg-surface-container-low rounded-xl p-1 border border-surface-border focus-within:border-primary transition-colors relative" id="desktop-search-form">
          <div class="relative group">
            <select name="category" id="desktop-search-category" class="bg-transparent pl-3 pr-7 py-2 text-on-surface font-label-md text-label-md cursor-pointer outline-none appearance-none font-semibold">
              <option value="all">All Gear</option>
              @if(isset($navCategories) && count($navCategories) > 0)
                @foreach($navCategories as $scat)
                  <option value="{{ $scat['slug'] }}" {{ request('category') === $scat['slug'] ? 'selected' : '' }}>{{ $scat['name'] }}</option>
                @endforeach
              @endif
            </select>
            <span class="material-symbols-outlined text-[16px] text-text-muted absolute right-1.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
          </div>
          <div class="h-5 w-px bg-outline-variant opacity-40"></div>
          <div class="flex-1 flex items-center px-3 relative">
            <span class="material-symbols-outlined text-text-muted text-[18px] mr-2">search</span>
            <input id="desktop-search-input" name="q" value="{{ request('q') ?? '' }}" class="w-full bg-transparent text-body-md font-body-md text-on-surface placeholder:text-text-muted focus:outline-none" placeholder="Search cameras, lenses, switchers, barcodes (e.g. H33, DJI)..." type="text" autocomplete="off"/>
            <button type="button" id="desktop-search-clear" class="hidden text-slate-400 hover:text-slate-600 mr-1 cursor-pointer"><span class="material-symbols-outlined text-[16px]">close</span></button>
          </div>
          <button type="submit" class="bg-primary hover:bg-rec-red-hover text-on-primary font-label-md text-label-md px-5 py-2.5 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm font-bold shrink-0">
            <span>Search</span>
          </button>
        </form>

        <!-- Live Instant Search Dropdown Box -->
        <div id="desktop-search-results" class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-[0_15px_50px_rgba(0,0,0,0.18)] border border-slate-200 z-50 overflow-hidden hidden max-h-[480px] overflow-y-auto">
          <!-- Populated by JS -->
        </div>
      </div>

      <!-- Right Header Actions (Hotline, Wishlist, Cart) -->
      <div class="flex items-center gap-5 flex-shrink-0">
        <!-- Helpline & WhatsApp -->
        <a class="flex items-center gap-2 group" href="https://wa.me/{{ $whatsappDigits ?? '8801917256756' }}" target="_blank">
          <div class="w-9 h-9 rounded-full bg-broadcast-teal-subtle flex items-center justify-center text-broadcast-teal group-hover:scale-105 transition-transform">
            <span class="material-symbols-outlined text-[20px]">support_agent</span>
          </div>
          <div class="flex flex-col text-left">
            <span class="font-label-sm text-label-sm text-text-muted">Helpline & WhatsApp</span>
            <span class="font-label-md text-label-md text-price-tk font-bold">{{ $sitePhoneFormatted ?? '+880-1917-256-756' }}</span>
          </div>
        </a>

        <!-- Wishlist / Cart Icons -->
        <div class="flex items-center gap-2">
          <a class="relative p-2 text-on-surface-variant hover:text-primary transition-colors flex flex-col items-center" href="{{ route('frontend.wishlist') }}" title="Wishlist">
            <span class="material-symbols-outlined text-[24px]">favorite_border</span>
            <span id="desktop-wishlist-count" class="absolute top-1 right-1.5 bg-primary text-on-primary text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center">0</span>
            <span class="text-[10px] font-medium mt-0.5">Wishlist</span>
          </a>
          <button class="relative p-2 text-on-surface-variant hover:text-primary transition-colors flex flex-col items-center" onclick="toggleCartDrawer()" title="View Cart">
            <span class="material-symbols-outlined text-[24px]">shopping_bag</span>
            <span id="desktop-cart-badge" class="absolute top-1 right-1.5 bg-primary text-on-primary text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center">3</span>
            <span id="desktop-cart-total" class="text-[10px] font-bold mt-0.5 text-price-tk">৳ ৩,২৪,০০০</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Category Mega Navigation Bar -->
    @include('frontend.layouts.includes.mega-menu')
  </header>

  <!-- Mobile Category & Subcategory Drawer -->
  @include('frontend.layouts.includes.mobile-category-drawer')

  <!-- ========================================== -->
  <!-- MOBILE SEARCH OVERLAY MODAL -->
  <!-- ========================================== -->
  <div id="mobile-search-modal" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden flex-col">
    <div class="bg-surface-card p-4 pt-safe flex items-center gap-3 shadow-lg">
      <form action="{{ route('frontend.search') }}" method="GET" class="flex-1 flex items-center bg-surface-container-low rounded-xl px-3 py-2 border border-surface-border focus-within:border-primary">
        <span class="material-symbols-outlined text-text-muted text-[20px] mr-2">search</span>
        <input id="mobile-search-input" name="q" class="w-full bg-transparent text-body-md font-body-md text-on-surface placeholder:text-text-muted focus:outline-none" placeholder="Search cameras, lenses, mics, gimbals..." type="text" autocomplete="off" autofocus/>
        <button type="submit" class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg ml-1 shrink-0">Search</button>
      </form>
      <button id="mobile-search-close" class="p-2 text-on-surface-variant hover:text-primary font-bold">Cancel</button>
    </div>
    
    <!-- Live Results Container for Mobile -->
    <div id="mobile-search-results" class="flex-1 p-4 overflow-y-auto hidden">
      <!-- Populated by JS -->
    </div>

    <!-- Default Trending Section (when not searching) -->
    <div id="mobile-search-default" class="flex-1 p-4 overflow-y-auto" onclick="closeMobileSearch()">
      <div class="bg-surface-card rounded-2xl p-4 shadow-sm" onclick="event.stopPropagation()">
        <span class="text-label-sm text-text-muted uppercase font-bold block mb-2">Trending Searches</span>
        <div class="flex flex-wrap gap-2">
          <a href="{{ route('frontend.search', ['q' => 'Pocket Camera']) }}" class="bg-surface-container-low px-3 py-1.5 rounded-lg text-body-sm hover:bg-primary hover:text-white transition-colors">Pocket Camera</a>
          <a href="{{ route('frontend.search', ['q' => 'DJI']) }}" class="bg-surface-container-low px-3 py-1.5 rounded-lg text-body-sm hover:bg-primary hover:text-white transition-colors">DJI Gimbals & Drones</a>
          <a href="{{ route('frontend.search', ['q' => 'Action Camera']) }}" class="bg-surface-container-low px-3 py-1.5 rounded-lg text-body-sm hover:bg-primary hover:text-white transition-colors">Action Camera</a>
          <a href="{{ route('frontend.search', ['q' => 'Wireless Mic']) }}" class="bg-surface-container-low px-3 py-1.5 rounded-lg text-body-sm hover:bg-primary hover:text-white transition-colors">Wireless Mic</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- CART SLIDE-OVER DRAWER -->
  <!-- ========================================== -->
  <div id="cart-drawer-overlay" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-xs hidden transition-opacity" onclick="toggleCartDrawer()"></div>
  <div id="cart-drawer" class="fixed top-0 right-0 bottom-0 z-[101] w-full max-w-md bg-surface-card shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <div class="p-4 border-b border-surface-border flex items-center justify-between bg-surface-container-low">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[24px]">shopping_bag</span>
        <h3 class="font-headline-sm text-headline-sm text-text-display font-bold">Your Production Bag</h3>
        <span id="cart-drawer-count" class="bg-primary text-white text-[11px] font-bold px-2 py-0.5 rounded-full">3</span>
      </div>
      <button class="w-8 h-8 rounded-full hover:bg-surface-container flex items-center justify-center text-text-muted" onclick="toggleCartDrawer()">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>

    <!-- Cart Items List (Dynamic) -->
    <div id="cart-drawer-items" class="flex-1 overflow-y-auto p-4 space-y-3">
      <!-- Dynamically Populated by CameraCart.renderDrawer() -->
    </div>

    <!-- Cart Footer & Checkout -->
    <div class="p-4 border-t border-surface-border bg-surface-card space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-body-md text-text-muted">Subtotal:</span>
        <span id="cart-drawer-subtotal" class="font-headline-md text-headline-md text-price-tk font-bold">৳ 0 TK</span>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('frontend.cart') }}" class="flex-1 border border-primary text-primary hover:bg-primary/5 font-bold text-xs py-3 rounded-xl text-center transition-colors">
          View Cart Page
        </a>
        <a href="{{ route('frontend.checkout') }}" class="flex-1 bg-primary hover:bg-rec-red-hover text-white font-bold text-xs py-3 rounded-xl text-center transition-all flex items-center justify-center gap-1 shadow-sm">
          <span class="material-symbols-outlined text-[16px]">lock</span> Checkout
        </a>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MAIN CONTENT CONTAINER -->
  <!-- ========================================== -->
  <main class="flex-1 w-full pt-[92px] lg:pt-[132px] pb-24 lg:pb-0 bg-surface-base">

    <!-- ========================================== -->
    <!-- SECTION 1: HERO SECTION (DYNAMIC BANNERS & SLIDER) -->
    <!-- ========================================== -->
    <section class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-2 w-full">
      
      <!-- MOBILE HERO (Visible on < lg) -->
      <div class="lg:hidden flex flex-col gap-3">
        <!-- Main Featured Hero Slider / Banner (Mobile) -->
        <div class="relative w-full rounded-2xl overflow-hidden shadow-sm aspect-[2/1] bg-surface-card border border-surface-border group" id="mobile-hero-slider">
          @if(isset($heroMainBanners) && $heroMainBanners->count() > 0)
            <div class="relative w-full h-full overflow-hidden">
              @foreach($heroMainBanners as $idx => $mBanner)
                <a href="{{ $mBanner->link ?: '#' }}" 
                   class="mobile-hero-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                   data-slide-index="{{ $idx }}">
                  <img alt="{{ $mBanner->title ?? ($siteName ?? 'CAMERA BD') }}" class="w-full h-full object-cover" src="{{ $mBanner->image_url }}"/>
                </a>
              @endforeach
            </div>

            @if($heroMainBanners->count() > 1)
              <!-- Mobile Dot Indicators -->
              <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 bg-black/40 backdrop-blur-xs px-2.5 py-1 rounded-full">
                @foreach($heroMainBanners as $idx => $mBanner)
                  <button type="button" onclick="goToHeroSlide({{ $idx }})" 
                          class="hero-dot w-2 h-2 rounded-full transition-all {{ $idx === 0 ? 'bg-white w-4' : 'bg-white/50' }}" 
                          aria-label="Slide {{ $idx + 1 }}"></button>
                @endforeach
              </div>
            @endif
          @else
            <!-- Fallback Hero Banner -->
            <a href="{{ url('/product-details/dji-osmo-pocket-4p') }}" class="block w-full h-full">
              <img alt="DJI Osmo Pocket 4P" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida/AEtjO1Vqtovg_xZlDnZaVLXZXohSSKxOzwNsqMMehT2gASqjyOfoGneJMZFqoH9IPecShYtpwgk9u7piXxWXoxpDO3um1RY2TiwiV07r6eOYuayiVh2wtWy2mFCC7cRlyTcyGO5Oow-9tFRAkoIMevpq-fd1cgrFpY2jHKfVeiNgh_iTom9zUqW3Ma_1mf2SEbP2PjUTxpPZkHYWb_ZrFZhOob0QRha2GFauumTzaWXmI3sUn8DY_-RRBUeP5Kg"/>
            </a>
          @endif
        </div>

        <!-- Secondary Hero Quick Tiles (2 Columns) -->
        <div class="grid grid-cols-2 gap-2.5">
          <!-- Card 1: Side Top Promo -->
          @if(isset($heroSideTopBanner) && $heroSideTopBanner)
            <a class="block rounded-xl overflow-hidden bg-surface-card shadow-sm border border-surface-border aspect-[16/9] group active:scale-[0.99] transition-transform" href="{{ $heroSideTopBanner->link ?: '#' }}">
              <img alt="{{ $heroSideTopBanner->title ?? (($siteName ?? 'CAMERA BD') . ' Promo') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ $heroSideTopBanner->image_url }}"/>
            </a>
          @else
            <a class="block rounded-xl overflow-hidden bg-surface-card shadow-sm border border-surface-border aspect-[16/9] group active:scale-[0.99] transition-transform" href="{{ route('frontend.category', ['slug' => 'drone']) }}">
              <img alt="DJI Neo 2 Drone" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida/AEtjO1VhOgpQCvjwP81MCDPHJoV-TCBpVyatc_-EgTdYpU1gErRH_l_jm3yo999fSrNbB71ww4lj1OO7VCT2_6YVzNGYCIhfP53eAwmaELrh5oG5d6IDeulyBqrmcb1_FbCW1DD-0CulNGjDfY4q-YgQA4kRw7onQ9_o4Q2vrVcGlcYyW5nHaPfXyAhtBJbexteyxElnOs_0FS5tROKeEz8tFh-dWLAE52k2g5mJpaUorGGuCV9sVBx_9sv-dOE"/>
            </a>
          @endif

          <!-- Card 2: Side Bottom Promo -->
          @if(isset($heroSideBottomBanner) && $heroSideBottomBanner)
            <a class="block rounded-xl overflow-hidden bg-surface-card shadow-sm border border-surface-border aspect-[16/9] group active:scale-[0.99] transition-transform" href="{{ $heroSideBottomBanner->link ?: '#' }}">
              <img alt="{{ $heroSideBottomBanner->title ?? (($siteName ?? 'CAMERA BD') . ' Promo') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ $heroSideBottomBanner->image_url }}"/>
            </a>
          @else
            <a class="block rounded-xl overflow-hidden bg-surface-card shadow-sm border border-surface-border aspect-[16/9] group active:scale-[0.99] transition-transform" href="{{ route('frontend.category', ['slug' => 'digital-camera']) }}">
              <img alt="Nikon Cinema Series" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida/AEtjO1Uo-ckvULlFV2gVtf9Pp7azws98fqwuvjcqQw3boocOesHEduRc9_3Dh1hXNM3kSacsR3e5Dv9QVTd43GszBoTaQeY911zs_dpSq0GPIO6ELFZslqUonTjzdo7jBxBUDwaP9x3niWmwC18RUiavJ635vJfgHEPBY5u9RS0z315h3uIirGohyxMIPY_ULhnG4GD0tF12d8d1h0VNSrLlz-F7GnHY6BS-r3uOR3Q7w7Vq4Xll1UhToPrl92M"/>
            </a>
          @endif
        </div>
      </div>

      <!-- DESKTOP HERO (Visible on >= lg) -->
      <div class="hidden lg:grid grid-cols-12 gap-5 items-stretch">
        <!-- Main Billboard Slider: 8 Columns -->
        <div class="col-span-8 relative bg-surface-card rounded-2xl shadow-sm overflow-hidden border border-surface-border group aspect-[16/8.5]" id="desktop-hero-slider">
          @if(isset($heroMainBanners) && $heroMainBanners->count() > 0)
            <div class="relative w-full h-full overflow-hidden">
              @foreach($heroMainBanners as $idx => $mBanner)
                <a href="{{ $mBanner->link ?: '#' }}" 
                   class="desktop-hero-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                   data-slide-index="{{ $idx }}">
                  <img alt="{{ $mBanner->title ?? ($siteName ?? 'CAMERA BD') }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 ease-out" src="{{ $mBanner->image_url }}"/>
                </a>
              @endforeach
            </div>

            @if($heroMainBanners->count() > 1)
              <!-- Left / Right Navigation Arrows -->
              <button type="button" onclick="prevHeroSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-md">
                <span class="material-symbols-outlined text-[24px]">chevron_left</span>
              </button>
              <button type="button" onclick="nextHeroSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-md">
                <span class="material-symbols-outlined text-[24px]">chevron_right</span>
              </button>

              <!-- Dot Indicators -->
              <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2 bg-black/40 backdrop-blur-xs px-3 py-1.5 rounded-full">
                @foreach($heroMainBanners as $idx => $mBanner)
                  <button type="button" onclick="goToHeroSlide({{ $idx }})" 
                          class="desktop-hero-dot h-2 rounded-full transition-all duration-300 {{ $idx === 0 ? 'bg-white w-6' : 'bg-white/50 w-2 hover:bg-white/80' }}" 
                          aria-label="Slide {{ $idx + 1 }}"></button>
                @endforeach
              </div>
            @endif
          @else
            <!-- Fallback Billboard -->
            <a href="{{ url('/product-details/dji-osmo-pocket-4p') }}" class="block w-full h-full">
              <img alt="DJI Osmo Pocket 4P Dual-Lens Camera" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 ease-out" src="https://lh3.googleusercontent.com/aida/AEtjO1Vqtovg_xZlDnZaVLXZXohSSKxOzwNsqMMehT2gASqjyOfoGneJMZFqoH9IPecShYtpwgk9u7piXxWXoxpDO3um1RY2TiwiV07r6eOYuayiVh2wtWy2mFCC7cRlyTcyGO5Oow-9tFRAkoIMevpq-fd1cgrFpY2jHKfVeiNgh_iTom9zUqW3Ma_1mf2SEbP2PjUTxpPZkHYWb_ZrFZhOob0QRha2GFauumTzaWXmI3sUn8DY_-RRBUeP5Kg"/>
            </a>
          @endif
        </div>

        <!-- Secondary Stacked Cards (4 Columns) -->
        <div class="col-span-4 flex flex-col gap-5 justify-between">
          <!-- Card 1: Side Top -->
          @if(isset($heroSideTopBanner) && $heroSideTopBanner)
            <a href="{{ $heroSideTopBanner->link ?: '#' }}" class="flex-1 block bg-surface-card rounded-2xl shadow-sm overflow-hidden border border-surface-border group aspect-[16/7.5]">
              <img alt="{{ $heroSideTopBanner->title ?? ($siteName ?? 'CAMERA BD') }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500" src="{{ $heroSideTopBanner->image_url }}"/>
            </a>
          @else
            <a href="{{ route('frontend.category', ['slug' => 'drone']) }}" class="flex-1 block bg-surface-card rounded-2xl shadow-sm overflow-hidden border border-surface-border group aspect-[16/7.5]">
              <img alt="DJI Neo 2 Drone" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500" src="https://lh3.googleusercontent.com/aida/AEtjO1VhOgpQCvjwP81MCDPHJoV-TCBpVyatc_-EgTdYpU1gErRH_l_jm3yo999fSrNbB71ww4lj1OO7VCT2_6YVzNGYCIhfP53eAwmaELrh5oG5d6IDeulyBqrmcb1_FbCW1DD-0CulNGjDfY4q-YgQA4kRw7onQ9_o4Q2vrVcGlcYyW5nHaPfXyAhtBJbexteyxElnOs_0FS5tROKeEz8tFh-dWLAE52k2g5mJpaUorGGuCV9sVBx_9sv-dOE"/>
            </a>
          @endif

          <!-- Card 2: Side Bottom -->
          @if(isset($heroSideBottomBanner) && $heroSideBottomBanner)
            <a href="{{ $heroSideBottomBanner->link ?: '#' }}" class="flex-1 block bg-surface-card rounded-2xl shadow-sm overflow-hidden border border-surface-border group aspect-[16/7.5]">
              <img alt="{{ $heroSideBottomBanner->title ?? ($siteName ?? 'CAMERA BD') }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500" src="{{ $heroSideBottomBanner->image_url }}"/>
            </a>
          @else
            <a href="{{ route('frontend.category', ['slug' => 'digital-camera']) }}" class="flex-1 block bg-surface-card rounded-2xl shadow-sm overflow-hidden border border-surface-border group aspect-[16/7.5]">
              <img alt="Nikon Z30 Cinema Body" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500" src="https://lh3.googleusercontent.com/aida/AEtjO1Uo-ckvULlFV2gVtf9Pp7azws98fqwuvjcqQw3boocOesHEduRc9_3Dh1hXNM3kSacsR3e5Dv9QVTd43GszBoTaQeY911zs_dpSq0GPIO6ELFZslqUonTjzdo7jBxBUDwaP9x3niWmwC18RUiavJ635vJfgHEPBY5u9RS0z315h3uIirGohyxMIPY_ULhnG4GD0tF12d8d1h0VNSrLlz-F7GnHY6BS-r3uOR3Q7w7Vq4Xll1UhToPrl92M"/>
            </a>
          @endif
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 2: BROWSE BY CATEGORY -->
    <!-- ========================================== -->
    <section class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 w-full">
      <div class="flex items-center justify-between mb-3 lg:mb-4">
        <div>
          <h2 class="text-xl lg:text-2xl font-bold text-[#102d28] font-headline-lg">Browse by Category</h2>
        </div>
        
        <div class="flex items-center gap-2">
          <a class="text-xs sm:text-sm font-bold text-secondary hover:text-primary flex items-center gap-1 mr-1 group" href="{{ route('frontend.categories') }}">
            <span>All Categories</span>
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
          </a>
          <!-- Desktop Slider Navigation Arrows -->
          <button id="cat-slider-prev" aria-label="Previous Category" class="hidden md:flex w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-white border border-[#d0ece7] text-[#102d28] hover:bg-secondary hover:text-white hover:border-secondary shadow-xs items-center justify-center transition-all duration-200 active:scale-90">
            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
          </button>
          <button id="cat-slider-next" aria-label="Next Category" class="hidden md:flex w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-white border border-[#d0ece7] text-[#102d28] hover:bg-secondary hover:text-white hover:border-secondary shadow-xs items-center justify-center transition-all duration-200 active:scale-90">
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
          </button>
        </div>
      </div>

      <!-- Mobile 3-Column Grid Layout (< md) -->
      <div class="grid grid-cols-3 gap-2 sm:gap-3 md:hidden">
        @if(isset($navCategories) && count($navCategories) > 0)
          @foreach($navCategories as $cat)
            <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" class="bg-white rounded-2xl border border-[#d0ece7]/80 p-2 sm:p-2.5 flex flex-col items-center justify-between text-center group hover:border-secondary hover:shadow-md transition-all duration-200 shadow-xs">
              <div class="w-full aspect-square rounded-xl bg-gradient-to-b from-[#f2f9f8] to-[#e6f4f1] flex items-center justify-center p-2 mb-1.5 overflow-hidden group-hover:bg-[#d8efe9] transition-colors relative">
                @if(!empty($cat['image']))
                  <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}" class="w-full h-full object-contain filter drop-shadow-sm group-hover:scale-110 transition-transform duration-300" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling ? this.nextElementSibling.classList.remove('hidden') : null;"/>
                  <div class="w-full h-full hidden items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[26px]">{{ $cat['icon'] ?? 'category' }}</span>
                  </div>
                @else
                  <div class="w-full h-full flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[26px]">{{ $cat['icon'] ?? 'category' }}</span>
                  </div>
                @endif
              </div>
              <span class="text-[11px] sm:text-xs font-semibold text-[#18332f] text-center tracking-tight leading-tight group-hover:text-primary transition-colors line-clamp-2">{{ $cat['name'] }}</span>
            </a>
          @endforeach
        @endif
      </div>

      <!-- Desktop Carousel Track (>= md) -->
      <div class="hidden md:block relative w-full overflow-hidden">
        <div id="category-slider-track" class="flex items-stretch gap-3 md:gap-4 overflow-x-auto scroll-smooth no-scrollbar py-2 px-0.5">
          @if(isset($navCategories) && count($navCategories) > 0)
            @foreach($navCategories as $cat)
              <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" class="w-[125px] sm:w-[135px] md:w-[150px] lg:w-[160px] shrink-0 flex-none bg-white rounded-2xl border border-[#d0ece7] p-3 md:p-3.5 flex flex-col items-center justify-between text-center group hover:border-secondary hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 select-none">
                <div class="w-full aspect-square rounded-xl bg-gradient-to-b from-[#f2f9f8] to-[#e6f4f1] flex items-center justify-center p-2 mb-2 overflow-hidden group-hover:bg-[#d8efe9] transition-colors relative">
                  @if(!empty($cat['image']))
                    <img src="{{ $cat['image'] }}" alt="{{ $cat['name'] }}" class="w-full h-full object-contain filter drop-shadow-sm group-hover:scale-110 transition-transform duration-300" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling ? this.nextElementSibling.style.display='flex' : null;"/>
                    <div class="w-full h-full hidden items-center justify-center text-secondary group-hover:scale-110 transition-transform duration-300">
                      <span class="material-symbols-outlined text-[36px]">{{ $cat['icon'] ?? 'category' }}</span>
                    </div>
                  @else
                    <div class="w-full h-full flex items-center justify-center text-secondary group-hover:scale-110 transition-transform duration-300">
                      <span class="material-symbols-outlined text-[36px]">{{ $cat['icon'] ?? 'category' }}</span>
                    </div>
                  @endif
                </div>
                <span class="text-xs md:text-sm font-semibold text-[#18332f] text-center tracking-tight leading-snug group-hover:text-primary transition-colors line-clamp-2">{{ $cat['name'] }}</span>
              </a>
            @endforeach
          @endif
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 4: LIMITED-TIME FLASH DEALS -->
    <!-- ========================================== -->
    <section id="hot-deals" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
      <div class="bg-surface-card rounded-2xl p-4 lg:p-8 shadow-sm border border-surface-border">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4 lg:mb-6 pb-4 border-b border-surface-border">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-sm">
              <span class="material-symbols-outlined text-[22px]">timer</span>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="text-label-sm font-label-sm uppercase tracking-wider text-primary font-bold">{{ $flashDealBadge ?? 'Limited Studio Drops' }}</span>
                <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
              </div>
              <h2 class="text-headline-md lg:text-headline-lg font-headline-lg text-text-display font-bold">{{ $flashDealTitle ?? 'Broadcast & Studio Flash' }}</h2>
            </div>
          </div>

          <!-- Synchronized Countdown Timer -->
          <div class="flex items-center gap-2 bg-surface-container-low px-4 py-2 rounded-xl self-start md:self-auto" id="flash-deal-timer-wrap" data-end-date="{{ $flashDealEndDate ? date('Y-m-d H:i:s', strtotime($flashDealEndDate)) : date('Y-m-d H:i:s', strtotime('+7 days 23:59:59')) }}">
            <span class="text-label-sm font-label-sm text-text-muted uppercase font-bold mr-1">Offer Ends In:</span>
            <div class="flex items-center gap-1 font-headline-sm font-bold text-text-display">
              <span class="bg-surface-card px-2 py-1 rounded shadow-xs cd-days hidden">00</span>
              <span class="cd-days-sep hidden text-text-muted">:</span>
              <span class="bg-surface-card px-2 py-1 rounded shadow-xs cd-hours">00</span>
              <span>:</span>
              <span class="bg-surface-card px-2 py-1 rounded shadow-xs cd-mins">00</span>
              <span>:</span>
              <span class="bg-surface-card px-2 py-1 rounded shadow-xs text-primary cd-secs">00</span>
            </div>
          </div>
        </div>

        <!-- Deals Scroll Track on Mobile / 4-Col Grid on Desktop -->
        <div class="flex lg:grid lg:grid-cols-4 gap-4 overflow-x-auto lg:overflow-x-visible snap-x snap-mandatory pb-2 no-scrollbar">
          @forelse($flashDealProducts as $deal)
            @php
              $currentPrice = (float)($deal->selling_price ?? 0);
              $origPrice = (float)($deal->dis_selling_price > 0 ? $deal->dis_selling_price : ($deal->purchase_price > $currentPrice ? $deal->purchase_price : 0));
              $hasDiscount = $deal->discount > 0 || ($origPrice > $currentPrice);
              if ($deal->discount > 0) {
                $discountTag = $deal->discount_type == 'fixed' ? 'Save ৳' . number_format($deal->discount) : 'Save ' . round($deal->discount) . '%';
              } elseif ($origPrice > $currentPrice) {
                $pct = round((($origPrice - $currentPrice) / $origPrice) * 100);
                $discountTag = $pct > 0 ? "Save {$pct}%" : "Flash Deal";
              } else {
                $discountTag = "Flash Deal";
              }
            @endphp
            <div class="min-w-[240px] max-w-[240px] lg:min-w-0 lg:max-w-none shrink-0 snap-start bg-surface-base lg:bg-surface-base rounded-xl p-4 flex flex-col justify-between group hover:shadow-md transition-all duration-200 border border-surface-border">
              <div>
                <div class="relative bg-surface-card rounded-lg overflow-hidden p-3 aspect-square flex items-center justify-center mb-3">
                  <span class="absolute top-2 left-2 z-10 bg-primary text-on-primary text-label-sm font-label-sm px-2 py-0.5 rounded font-bold shadow-xs">{{ $discountTag }}</span>
                  <button aria-label="Add to Wishlist" class="wishlist-btn absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-surface-card/90 shadow-sm flex items-center justify-center text-text-muted hover:text-primary transition-colors cursor-pointer" onclick="toggleWishlist(this, '{{ addslashes($deal->name) }}', {{ $currentPrice }}, '{{ addslashes($deal->thumbnail_url ?? '') }}', '{{ $deal->slug ?? $deal->id }}', '{{ $deal->id }}', '{{ addslashes($deal->brand?->name ?? 'CAMERA BD') }}')" data-wishlist-product="{{ addslashes($deal->name) }}" data-wishlist-id="{{ $deal->id }}">
                    <span class="material-symbols-outlined text-[18px]">favorite_border</span>
                  </button>
                  <a href="{{ route('frontend.product.details', $deal->id) }}" class="w-full h-full flex items-center justify-center">
                    @if($deal->has_real_image)
                      <img alt="{{ $deal->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300 relative z-0" src="{{ $deal->thumbnail_url }}" loading="lazy" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50 p-2\'><span class=\'material-symbols-outlined text-[36px] text-slate-300\'>videocam</span><span class=\'text-[10px] font-bold text-slate-400 mt-1\'>Broadcast Gear</span></div>';"/>
                    @else
                      <div class="w-full h-full flex items-center justify-center flex-col text-slate-400 bg-gradient-to-br from-slate-50 to-slate-100/90 rounded-lg p-3 text-center border border-slate-100 group-hover:bg-slate-100 transition-colors">
                        <div class="w-12 h-12 rounded-full bg-white shadow-xs flex items-center justify-center text-primary mb-1.5 group-hover:scale-110 transition-transform">
                          <span class="material-symbols-outlined text-[24px]">videocam</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider line-clamp-1">{{ $deal->category?->name ?? 'Broadcast Gear' }}</span>
                      </div>
                    @endif
                  </a>
                </div>
                <div class="flex items-center gap-1 text-amber-500 mb-1">
                  <span class="material-symbols-outlined text-[15px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="text-label-md font-label-md text-text-display font-bold">5.0</span>
                  <span class="text-body-sm font-body-sm text-text-muted">({{ $deal->brand?->name ?? 'Verified' }})</span>
                </div>
                <h3 class="text-headline-sm font-headline-sm text-text-display line-clamp-2 mb-2 group-hover:text-primary transition-colors font-bold">
                  <a href="{{ route('frontend.product.details', $deal->id) }}">{{ $deal->name }}</a>
                </h3>
              </div>
              <div class="mt-2">
                <div class="flex items-baseline gap-2">
                  <span class="text-price-headline font-price-headline text-price-tk font-bold">৳ {{ number_format($currentPrice) }} TK</span>
                </div>
                @if($origPrice > $currentPrice)
                  <span class="text-price-strikethrough font-price-strikethrough text-text-muted line-through">৳ {{ number_format($origPrice) }} TK</span>
                @else
                  <span class="text-[11px] text-stock-available font-bold block">● In Stock</span>
                @endif
                <button onclick="addToCart('{{ addslashes($deal->name) }}', {{ $currentPrice }}, '{{ $deal->has_real_image ? $deal->thumbnail_url : '' }}')" class="w-full mt-3 bg-primary hover:bg-rec-red-hover text-on-primary text-label-md font-label-md py-2.5 rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-sm font-bold active:scale-95">
                  <span class="material-symbols-outlined text-[16px]">shopping_cart</span>
                  <span>Add to Cart</span>
                </button>
              </div>
            </div>
          @empty
            <div class="col-span-4 p-8 text-center text-text-muted bg-surface-base rounded-xl">
              {{ __('No flash deals active right now.') }}
            </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 5: POPULAR CINEMA & CREATOR GEAR -->
    <!-- ========================================== -->
    <!-- ========================================== -->
    <!-- SECTION 5: POPULAR CINEMA & CREATOR GEAR -->
    <!-- ========================================== -->
    <section id="popular-cinema" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
      @php
        $popularCats = $popularProducts->pluck('category.name')->filter()->unique();
      @endphp
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-3 mb-6">
        <div>
          <div class="flex items-center gap-1.5 mb-0.5">
            <span class="w-2 h-2 rounded-full bg-broadcast-teal"></span>
            <span class="font-label-sm text-label-sm uppercase tracking-wider text-broadcast-teal font-bold">{{ $popularBadge ?? 'Essentials' }}</span>
          </div>
          <h2 class="text-headline-md lg:text-headline-lg font-headline-lg text-text-display font-bold">{{ $popularTitle ?? 'Popular Cinema & Creator Gear' }}</h2>
        </div>

        <!-- Dynamic Filter Tabs -->
        <div class="flex items-center gap-1 bg-surface-card p-1 rounded-xl shadow-xs overflow-x-auto border border-surface-border">
          <button onclick="filterCategory('all', this)" class="cinema-tab px-3.5 py-1.5 rounded-lg text-label-md font-label-md bg-primary text-on-primary font-bold shadow-xs">All Gear</button>
          @foreach($popularCats as $pCat)
            <button onclick="filterCategory('{{ \Illuminate\Support\Str::slug($pCat) }}', this)" class="cinema-tab px-3.5 py-1.5 rounded-lg text-label-md font-label-md text-text-muted hover:text-text-display transition-colors whitespace-nowrap">{{ $pCat }}</button>
          @endforeach
        </div>
      </div>

      <!-- 2 Columns Mobile / 4 Columns Desktop Grid -->
      <div id="popular-products-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-5">
        @forelse($popularProducts as $popular)
          @php
            $catSlug = \Illuminate\Support\Str::slug($popular->category?->name ?? 'gear');
            $pPrice = (float)($popular->selling_price ?? 0);
            $pOrig = (float)($popular->dis_selling_price > 0 ? $popular->dis_selling_price : ($popular->purchase_price > $pPrice ? $popular->purchase_price : 0));
          @endphp
          <div data-category="{{ $catSlug }}" class="product-card bg-surface-card rounded-2xl p-3 lg:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group border border-surface-border">
            <div>
              <div class="relative bg-surface-base rounded-xl overflow-hidden p-3 lg:p-4 aspect-square flex items-center justify-center mb-2 lg:mb-3">
                <span class="absolute top-1.5 left-1.5 lg:top-2 lg:left-2 z-10 bg-broadcast-teal text-on-primary text-label-sm font-label-sm px-1.5 lg:px-2 py-0.5 rounded font-bold shadow-xs">
                  {{ $popular->category?->name ?? 'Cinema Gear' }}
                </span>
                <button aria-label="Save to Wishlist" class="wishlist-btn absolute top-1.5 right-1.5 lg:top-2 lg:right-2 z-10 w-7 h-7 lg:w-8 lg:h-8 rounded-full bg-surface-card shadow-xs flex items-center justify-center text-text-muted hover:text-primary transition-colors cursor-pointer" onclick="toggleWishlist(this, '{{ addslashes($popular->name) }}', {{ (float)($popular->dis_selling_price ?: $popular->selling_price) }}, '{{ addslashes($popular->thumbnail_url ?? '') }}', '{{ $popular->slug ?? $popular->id }}', '{{ $popular->id }}', '{{ addslashes($popular->brand?->name ?? 'CAMERA BD') }}')" data-wishlist-product="{{ addslashes($popular->name) }}" data-wishlist-id="{{ $popular->id }}">
                  <span class="material-symbols-outlined text-[16px] lg:text-[18px]">favorite_border</span>
                </button>
                <a href="{{ route('frontend.product.details', $popular->id) }}" class="w-full h-full flex items-center justify-center">
                  @if($popular->has_real_image)
                    <img alt="{{ $popular->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300 relative z-0" src="{{ $popular->thumbnail_url }}" loading="lazy" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50 p-2\'><span class=\'material-symbols-outlined text-[36px] text-slate-300\'>photo_camera</span><span class=\'text-[10px] font-bold text-slate-400 mt-1\'>Camera Gear</span></div>';"/>
                  @else
                    <div class="w-full h-full flex items-center justify-center flex-col text-slate-400 bg-gradient-to-br from-slate-50 to-slate-100/90 rounded-lg p-3 text-center border border-slate-100 group-hover:bg-slate-100 transition-colors">
                      <div class="w-12 h-12 rounded-full bg-white shadow-xs flex items-center justify-center text-broadcast-teal mb-1.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[24px]">photo_camera</span>
                      </div>
                      <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider line-clamp-1">{{ $popular->brand?->name ?? 'Pro Gear' }}</span>
                    </div>
                  @endif
                </a>
              </div>
              <div class="flex items-center justify-between text-body-sm font-body-sm text-text-muted mb-1">
                <span class="text-broadcast-teal font-semibold text-[11px]">{{ $popular->brand?->name ?? 'Camera BD' }}</span>
                <span class="flex items-center gap-1 text-[11px]"><span class="w-1.5 h-1.5 rounded-full bg-stock-available"></span> In Stock</span>
              </div>
              <h3 class="text-headline-sm font-headline-sm text-text-display line-clamp-1 mb-1 group-hover:text-primary transition-colors font-bold">
                <a href="{{ route('frontend.product.details', $popular->id) }}">{{ $popular->name }}</a>
              </h3>
              <div class="flex items-baseline gap-1.5 mb-2">
                <span class="text-price-headline font-price-headline text-price-tk font-bold">৳ {{ number_format($pPrice) }} TK</span>
                @if($pOrig > $pPrice)
                  <span class="text-price-strikethrough font-price-strikethrough text-text-muted line-through">৳ {{ number_format($pOrig) }}</span>
                @endif
              </div>
            </div>
            <button onclick="addToCart('{{ addslashes($popular->name) }}', {{ $pPrice }}, '{{ $popular->has_real_image ? $popular->thumbnail_url : '' }}')" class="w-full bg-on-surface hover:bg-black text-surface font-headline-sm text-label-sm py-2 rounded-xl transition-colors flex items-center justify-center gap-1 font-bold">
              <span class="material-symbols-outlined text-[15px]">shopping_bag</span>
              <span>Add to Cart</span>
            </button>
          </div>
        @empty
          <div class="col-span-4 p-8 text-center text-text-muted bg-surface-card rounded-2xl border border-surface-border">
            {{ __('No popular products found.') }}
          </div>
        @endforelse
      </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 6: BRAND SPOTLIGHT: HOLLYLAND AUDIO SHOWCASE -->
    <!-- ========================================== -->
    @php
      $hlSectionBadge = get_setting('hollyland_section_badge') ?: 'FEATURED';
      $hlSectionTitle = get_setting('hollyland_section_title') ?: 'Hollyland Microphone';
      $hlSectionTag = get_setting('hollyland_section_tag') ?: 'SHOWCASE';
      $hlSectionViewAll = get_setting('hollyland_section_view_all_link') ?: route('frontend.subcategory', ['category_slug' => 'audio', 'subcategory_slug' => 'wireless-mics']);

      $hlBannerBrand = get_setting('hollyland_banner_brand') ?: 'HOLLYLAND';
      $hlBannerTitle = get_setting('hollyland_banner_title') ?: 'PURE SOUND. ZERO LIMITS.';
      $hlBannerSubtitle = get_setting('hollyland_banner_subtitle') ?: 'Wireless Audio for Modern Creators';
      $hlBannerFeatures = get_setting('hollyland_banner_features') ?: 'Wireless • Clear • Reliable • Portable';
      $hlFeatureList = array_values(array_filter(array_map('trim', preg_split('/[•\n\r|]+/', $hlBannerFeatures))));
      $hlBannerBottom = get_setting('hollyland_banner_bottom_text') ?: 'Capture What Matters';
      $hlBannerLink = get_setting('hollyland_banner_link') ?: route('frontend.subcategory', ['category_slug' => 'audio', 'subcategory_slug' => 'wireless-mics']);
      
      $hlBannerImg = get_setting('hollyland_banner_image');
      if ($hlBannerImg) {
          if (str_starts_with($hlBannerImg, 'http://') || str_starts_with($hlBannerImg, 'https://')) {
              $hlBannerSrc = $hlBannerImg;
          } else {
              $hlBannerSrc = asset($hlBannerImg);
          }
      } else {
          $hlBannerSrc = 'https://lh3.googleusercontent.com/aida/AEtjO1X-nc7VZD9tYHp6qutS95cjA-Xk5pOOzJwdae3Vi5yg75ZaanA4-bPFLwz-qoo-JoQi5TkDwO71GgUsiHVOVo5x8y535Jg4E3WEA_VzQl9ZAdT5TVIR5gEoikhv-Z2gFvZvSkGbsxQIifYqIgqsF8YhYzvl6tPc6smePHKqrg_ApXKYK0N-E5a5oijUQjPNmBCEz6Eh9i35JKinBfJI9ZCuzzIm1KilnSKr1gZmzV4bCV9tLBN_Y3w1T5o';
      }
    @endphp
    <section id="hollyland-audio" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
      <div class="bg-white rounded-3xl border border-[#c6eae1] p-4 sm:p-5 lg:p-6 shadow-xs">
        
        <!-- Top Header Strip -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-1">
          <!-- Left Header Title & Badges -->
          <div class="flex items-center gap-2.5 flex-wrap">
            <span class="bg-[#dcf3ed] text-[#006a61] text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded tracking-wider border border-[#bce8dd]">{{ $hlSectionBadge }}</span>
            <div class="h-4 w-[2px] bg-[#006a61] rounded-full"></div>
            <h2 class="text-lg lg:text-xl font-bold text-[#102d28] font-headline-lg">{{ $hlSectionTitle }}</h2>
            <span class="flex items-center gap-1 text-[11px] font-bold text-[#006a61] uppercase tracking-wider bg-[#f0faf7] px-2 py-0.5 rounded border border-[#d2efe7]">
              <span class="material-symbols-outlined text-[14px]">grid_view</span> {{ $hlSectionTag }}
            </span>
          </div>

          <!-- Right Header Actions (Showing count, prev/next arrows, View all button) -->
          <div class="flex items-center gap-2 self-end sm:self-auto">
            <span class="text-xs text-gray-500 font-medium mr-1 hidden sm:inline-block" id="hollyland-showing-count">Showing {{ count($hollylandProducts) }} featured products</span>
            <button id="hollyland-prev-btn" aria-label="Previous Products" class="w-8 h-8 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 flex items-center justify-center transition-all shadow-xs active:scale-90">
              <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <button id="hollyland-next-btn" aria-label="Next Products" class="w-8 h-8 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 flex items-center justify-center transition-all shadow-xs active:scale-90">
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
            <a href="{{ $hlSectionViewAll }}" class="border border-[#006a61] text-[#006a61] hover:bg-[#006a61] hover:text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 transition-all shadow-xs ml-1">
              <span class="material-symbols-outlined text-[14px]">open_in_new</span>
              <span>View all</span>
            </a>
          </div>
        </div>

        <!-- Main Content: Left Banner + Right Product Cards -->
        <div class="flex flex-col lg:flex-row gap-4 items-stretch">
          
          <!-- Left Vertical Poster Banner (~270px on Desktop) -->
          <a href="{{ $hlBannerLink }}" class="w-full lg:w-[270px] xl:w-[290px] shrink-0 rounded-2xl overflow-hidden shadow-sm border border-gray-800 bg-[#0d1217] relative group flex flex-col justify-between p-6 text-white min-h-[380px] lg:min-h-[440px] block">
            <!-- Background graphics/glow -->
            <div class="absolute inset-0 bg-gradient-to-b from-[#111922] via-[#0d1217] to-[#080b0e] z-0"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-[#006a61]/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Content: Hollyland Logo & Title -->
            <div class="relative z-10 text-center flex flex-col items-center">
              <div class="flex items-center gap-1.5 mb-3">
                <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center border border-white/20">
                  <span class="material-symbols-outlined text-white text-[16px]">graphic_eq</span>
                </div>
                <span class="text-xs font-extrabold tracking-widest text-white uppercase">{{ $hlBannerBrand }}</span>
              </div>
              <h3 class="text-lg xl:text-xl font-extrabold text-white leading-tight tracking-tight uppercase mb-1">
                {{ $hlBannerTitle }}
              </h3>
              <p class="text-[11px] text-gray-400 font-medium mb-2">
                {{ $hlBannerSubtitle }}
              </p>
              @if(!empty($hlFeatureList))
                <div class="flex items-center justify-center flex-wrap gap-1 text-[9px] text-gray-400 font-medium tracking-wide">
                  @foreach($hlFeatureList as $fIdx => $fText)
                    @if($fIdx > 0)
                      <span>•</span>
                    @endif
                    <span>{{ $fText }}</span>
                  @endforeach
                </div>
              @endif
            </div>

            <!-- Center Product Visual -->
            <div class="relative z-10 my-auto py-3 flex items-center justify-center">
              <img src="{{ $hlBannerSrc }}" alt="{{ $hlBannerBrand }}" class="max-h-[170px] w-auto object-contain filter drop-shadow-[0_10px_20px_rgba(0,0,0,0.6)] group-hover:scale-105 transition-transform duration-500" />
            </div>

            <!-- Bottom Script: Capture What Matters -->
            <div class="relative z-10 text-center">
              <span class="text-[#d4af37] italic text-base font-serif tracking-wide block drop-shadow-sm font-light">
                {{ $hlBannerBottom }}
              </span>
            </div>
          </a>

          <!-- Right Product Cards Track (4 in View on Desktop, Horizontal Scroll on Mobile/Tablet) -->
          <div class="flex-1 min-w-0 overflow-hidden relative">
            <div id="hollyland-track" class="flex gap-3.5 overflow-x-auto snap-x snap-mandatory scroll-smooth no-scrollbar py-0.5 h-full items-stretch">
              @forelse($hollylandProducts as $hlProduct)
                @php
                  $hlPrice = (float)($hlProduct->selling_price ?? 0);
                  $hlOrig = (float)($hlProduct->dis_selling_price > 0 ? $hlProduct->dis_selling_price : ($hlProduct->purchase_price > $hlPrice ? $hlProduct->purchase_price : 0));
                  $hlSave = $hlOrig > $hlPrice ? round((($hlOrig - $hlPrice) / $hlOrig) * 100) : 0;
                @endphp
                <div class="w-[200px] sm:w-[210px] md:w-[220px] lg:w-[calc(25%-10.5px)] shrink-0 flex-none snap-start bg-white rounded-2xl border border-[#d8ece6] p-3.5 flex flex-col justify-between hover:shadow-md hover:border-[#006a61]/50 transition-all duration-200 group">
                  <div>
                    <!-- Top Row: Blank/Badge + Wishlist -->
                    <div class="flex items-center justify-between min-h-[22px] mb-1">
                      @if($hlSave > 0)
                        <span class="bg-[#00897b] text-white text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded shadow-xs">HOT</span>
                      @elseif($hlProduct->brand?->name)
                        <span class="bg-[#e6f7f2] text-[#006a61] text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded">{{ $hlProduct->brand->name }}</span>
                      @else
                        <div></div>
                      @endif
                      <button aria-label="Add to Wishlist" class="wishlist-btn text-gray-300 hover:text-red-500 transition-colors cursor-pointer" onclick="toggleWishlist(this, '{{ addslashes($hlProduct->name) }}', {{ (float)($hlProduct->dis_selling_price ?: $hlProduct->selling_price) }}, '{{ addslashes($hlProduct->thumbnail_url ?? '') }}', '{{ $hlProduct->slug ?? $hlProduct->id }}', '{{ $hlProduct->id }}', 'Hollyland')" data-wishlist-product="{{ addslashes($hlProduct->name) }}" data-wishlist-id="{{ $hlProduct->id }}">
                        <span class="material-symbols-outlined text-[18px]">favorite_border</span>
                      </button>
                    </div>

                    <!-- Product Image -->
                    <div class="w-full aspect-square flex items-center justify-center p-2 mb-1.5 overflow-hidden">
                      <a href="{{ route('frontend.product.details', $hlProduct->id) }}" class="w-full h-full flex items-center justify-center">
                        @if($hlProduct->has_real_image)
                          <img src="{{ $hlProduct->thumbnail_url }}" alt="{{ $hlProduct->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center text-[#006a61] bg-[#f0faf7] p-2 rounded-lg\'><span class=\'material-symbols-outlined text-[32px]\'>mic</span><span class=\'text-[10px] font-bold mt-1\'>Hollyland</span></div>';"/>
                        @else
                          <div class="w-full h-full flex items-center justify-center flex-col text-[#006a61] bg-gradient-to-br from-[#f0faf7] to-[#e0f4ee] rounded-xl p-3 text-center border border-[#d2efe7] group-hover:bg-[#e6f7f2] transition-colors">
                            <div class="w-10 h-10 rounded-full bg-white shadow-xs flex items-center justify-center text-[#006a61] mb-1 group-hover:scale-110 transition-transform">
                              <span class="material-symbols-outlined text-[22px]">mic</span>
                            </div>
                            <span class="text-[10px] text-[#006a61] font-bold uppercase tracking-wider line-clamp-1">{{ $hlProduct->category?->name ?? 'Microphone' }}</span>
                          </div>
                        @endif
                      </a>
                    </div>

                    <!-- Carousel dot indicator -->
                    <div class="flex items-center justify-center gap-1 mb-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-[#006a61]"></span>
                      <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                    </div>

                    <!-- Title -->
                    <h4 class="text-xs sm:text-sm font-semibold text-[#18332f] line-clamp-2 leading-snug mb-1 group-hover:text-primary transition-colors font-bold">
                      <a href="{{ route('frontend.product.details', $hlProduct->id) }}">{{ $hlProduct->name }}</a>
                    </h4>

                    <!-- Rating -->
                    <div class="flex items-center gap-1 text-[11px] mb-2">
                      <span class="text-amber-400 font-bold tracking-tighter">★★★★★</span>
                      <span class="text-gray-500 font-medium">5.0</span>
                    </div>
                  </div>

                  <!-- Price & Discount Tag -->
                  <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-baseline gap-1.5 flex-wrap">
                      <span class="text-sm sm:text-base font-bold text-[#102d28]">৳ {{ number_format($hlPrice) }} TK</span>
                      @if($hlOrig > $hlPrice)
                        <span class="text-xs text-gray-400 line-through">৳ {{ number_format($hlOrig) }}</span>
                      @endif
                    </div>
                    @if($hlSave > 0)
                      <div class="mt-1">
                        <span class="inline-block bg-[#e6f7f2] text-[#006a61] text-[10px] font-bold px-1.5 py-0.5 rounded">
                          Save {{ $hlSave }}%
                        </span>
                      </div>
                    @endif
                  </div>
                </div>
              @empty
                <div class="w-full p-8 text-center text-gray-500">
                  {{ __('No audio products available in this showcase.') }}
                </div>
              @endforelse
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 7: STUDIO ESSENTIALS & POWER RIGS -->
    <!-- ========================================== -->
    <section id="studio-gear" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
      <div class="flex items-center justify-between mb-4 lg:mb-6">
        <div>
          <div class="flex items-center gap-1.5 mb-0.5">
            <span class="w-2 h-2 rounded-full bg-secondary"></span>
            <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold">{{ $essentialBadge ?? 'Top Rated Rigging' }}</span>
          </div>
          <h2 class="text-headline-md lg:text-headline-lg font-headline-lg text-text-display font-bold">{{ $essentialTitle ?? 'Studio Essentials & Power' }}</h2>
        </div>
        <a class="text-label-sm lg:text-label-lg font-label-lg text-broadcast-teal lg:text-primary hover:text-rec-red-hover flex items-center gap-1 font-bold" href="{{ route('frontend.category', ['slug' => 'switchers']) }}">
          <span>All Rig Gear</span>
          <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
      </div>

      <!-- 2 Cols Mobile / 4 Cols Desktop Grid -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-5">
        @forelse($essentialProducts as $essential)
          @php
            $ePrice = (float)($essential->selling_price ?? 0);
            $eOrig = (float)($essential->dis_selling_price > 0 ? $essential->dis_selling_price : ($essential->purchase_price > $ePrice ? $essential->purchase_price : 0));
            $eSave = $eOrig > $ePrice ? round((($eOrig - $ePrice) / $eOrig) * 100) : 0;
          @endphp
          <div class="bg-surface-card rounded-2xl p-3 lg:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group border border-surface-border">
            <div>
              <div class="relative bg-surface-base rounded-xl overflow-hidden p-3 lg:p-4 aspect-square flex items-center justify-center mb-2 lg:mb-3">
                @if($eSave > 0)
                  <span class="absolute top-1.5 left-1.5 lg:top-2 lg:left-2 z-10 bg-rec-red-base text-on-primary text-label-sm font-label-sm px-1.5 lg:px-2 py-0.5 rounded font-bold shadow-xs">-{{ $eSave }}% Off</span>
                @elseif($essential->category?->name)
                  <span class="absolute top-1.5 left-1.5 lg:top-2 lg:left-2 z-10 bg-broadcast-teal text-white text-label-sm font-label-sm px-1.5 lg:px-2 py-0.5 rounded font-bold shadow-xs">{{ $essential->category->name }}</span>
                @endif
                <a href="{{ route('frontend.product.details', $essential->id) }}" class="w-full h-full flex items-center justify-center">
                  @if($essential->has_real_image)
                    <img alt="{{ $essential->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300 relative z-0" src="{{ $essential->thumbnail_url }}" loading="lazy" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50 p-2\'><span class=\'material-symbols-outlined text-[36px] text-slate-300\'>power</span><span class=\'text-[10px] font-bold text-slate-400 mt-1\'>Studio Gear</span></div>';"/>
                  @else
                    <div class="w-full h-full flex items-center justify-center flex-col text-slate-400 bg-gradient-to-br from-slate-50 to-slate-100/90 rounded-lg p-3 text-center border border-slate-100 group-hover:bg-slate-100 transition-colors">
                      <div class="w-12 h-12 rounded-full bg-white shadow-xs flex items-center justify-center text-amber-500 mb-1.5 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[24px]">power</span>
                      </div>
                      <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider line-clamp-1">{{ $essential->category?->name ?? 'Studio Rig' }}</span>
                    </div>
                  @endif
                </a>
              </div>
              <div class="text-label-sm font-label-sm text-text-muted mb-0.5">{{ $essential->brand?->name ?? ($essential->category?->name ?? 'Pro Accessory') }}</div>
              <h3 class="text-headline-sm font-headline-sm text-text-display line-clamp-2 mb-1 group-hover:text-primary transition-colors font-bold">
                <a href="{{ route('frontend.product.details', $essential->id) }}">{{ $essential->name }}</a>
              </h3>
              <div class="flex items-baseline gap-1.5 mb-2">
                <span class="text-price-headline font-price-headline text-price-tk font-bold">৳ {{ number_format($ePrice) }} TK</span>
                @if($eOrig > $ePrice)
                  <span class="text-price-strikethrough font-price-strikethrough text-text-muted line-through">৳ {{ number_format($eOrig) }}</span>
                @else
                  <span class="font-label-sm text-[10px] text-stock-available font-bold block">● In Stock</span>
                @endif
              </div>
            </div>
            <button onclick="addToCart('{{ addslashes($essential->name) }}', {{ $ePrice }}, '{{ $essential->has_real_image ? $essential->thumbnail_url : '' }}')" class="w-full bg-surface-container hover:bg-primary hover:text-on-primary text-on-surface font-headline-sm text-label-sm py-2 rounded-xl transition-colors flex items-center justify-center gap-1 font-bold">
              <span class="material-symbols-outlined text-[15px]">add_shopping_cart</span>
              <span>Quick Add</span>
            </button>
          </div>
        @empty
          <div class="col-span-4 p-8 text-center text-text-muted bg-surface-card rounded-2xl border border-surface-border">
            {{ __('No studio essential products found.') }}
          </div>
        @endforelse
      </div>
    </section>

    <!-- ========================================== -->
    <!-- DYNAMIC USER CREATED HOMEPAGE SECTIONS -->
    <!-- ========================================== -->
    @if(isset($customHomepageSections) && count($customHomepageSections) > 0)
      @foreach($customHomepageSections as $customSec)
        @php
          $secProducts = $customSec->products ?? collect();
        @endphp
        @if(count($secProducts) > 0)
          <section id="custom-sec-{{ $customSec->id }}" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
            <div class="flex items-center justify-between mb-4 lg:mb-6">
              <div>
                <div class="flex items-center gap-1.5 mb-0.5">
                  <span class="w-2 h-2 rounded-full" style="background-color: {{ $customSec->accent_color ?: '#006a61' }};"></span>
                  <span class="font-label-sm text-label-sm uppercase tracking-wider font-bold" style="color: {{ $customSec->accent_color ?: '#006a61' }};">{{ $customSec->badge ?: 'Featured Collection' }}</span>
                </div>
                <h2 class="text-headline-md lg:text-headline-lg font-headline-lg text-text-display font-bold">{{ $customSec->name }}</h2>
                @if($customSec->subtitle)
                  <p class="text-xs text-text-muted mt-0.5">{{ $customSec->subtitle }}</p>
                @endif
              </div>
              <a class="text-label-sm lg:text-label-lg font-label-lg hover:text-rec-red-hover flex items-center gap-1 font-bold" style="color: {{ $customSec->accent_color ?: '#006a61' }};" href="{{ route('frontend.search', ['q' => $customSec->name]) }}">
                <span>View All</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
              </a>
            </div>

            <!-- 2 Cols Mobile / 4 Cols Desktop Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-5">
              @foreach($secProducts as $secProd)
                @php
                  $sPrice = (float)($secProd->selling_price ?? 0);
                  $sOrig = (float)($secProd->dis_selling_price > 0 ? $secProd->dis_selling_price : ($secProd->purchase_price > $sPrice ? $secProd->purchase_price : 0));
                  $sSave = $sOrig > $sPrice ? round((($sOrig - $sPrice) / $sOrig) * 100) : 0;
                @endphp
                <div class="bg-surface-card rounded-2xl p-3 lg:p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group border border-surface-border">
                  <div>
                    <div class="relative bg-surface-base rounded-xl overflow-hidden p-3 lg:p-4 aspect-square flex items-center justify-center mb-2 lg:mb-3">
                      @if($sSave > 0)
                        <span class="absolute top-1.5 left-1.5 lg:top-2 lg:left-2 z-10 bg-rec-red-base text-on-primary text-label-sm font-label-sm px-1.5 lg:px-2 py-0.5 rounded font-bold shadow-xs">-{{ $sSave }}% Off</span>
                      @elseif($secProd->category?->name)
                        <span class="absolute top-1.5 left-1.5 lg:top-2 lg:left-2 z-10 text-white text-label-sm font-label-sm px-1.5 lg:px-2 py-0.5 rounded font-bold shadow-xs" style="background-color: {{ $customSec->accent_color ?: '#006a61' }};">{{ $secProd->category->name }}</span>
                      @endif
                      <a href="{{ route('frontend.product.details', $secProd->id) }}" class="w-full h-full flex items-center justify-center">
                        @if($secProd->has_real_image)
                          <img alt="{{ $secProd->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300 relative z-0" src="{{ $secProd->thumbnail_url }}" loading="lazy" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50 p-2\'><span class=\'material-symbols-outlined text-[36px] text-slate-300\'>{{ $customSec->icon ?: 'inventory_2' }}</span><span class=\'text-[10px] font-bold text-slate-400 mt-1\'>{{ addslashes($customSec->name) }}</span></div>';"/>
                        @else
                          <div class="w-full h-full flex items-center justify-center flex-col text-slate-400 bg-gradient-to-br from-slate-50 to-slate-100/90 rounded-lg p-3 text-center border border-slate-100 group-hover:bg-slate-100 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-white shadow-xs flex items-center justify-center text-secondary mb-1.5 group-hover:scale-110 transition-transform">
                              <span class="material-symbols-outlined text-[24px]">{{ $customSec->icon ?: 'inventory_2' }}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider line-clamp-1">{{ $secProd->category?->name ?? $customSec->name }}</span>
                          </div>
                        @endif
                      </a>
                    </div>
                    <div class="text-label-sm font-label-sm text-text-muted mb-0.5">{{ $secProd->brand?->name ?? ($secProd->category?->name ?? 'Pro Gear') }}</div>
                    <h3 class="text-headline-sm font-headline-sm text-text-display line-clamp-2 mb-1 group-hover:text-primary transition-colors font-bold">
                      <a href="{{ route('frontend.product.details', $secProd->id) }}">{{ $secProd->name }}</a>
                    </h3>
                    <div class="flex items-baseline gap-1.5 mb-2">
                      <span class="text-price-headline font-price-headline text-price-tk font-bold">৳ {{ number_format($sPrice) }} TK</span>
                      @if($sOrig > $sPrice)
                        <span class="text-price-strikethrough font-price-strikethrough text-text-muted line-through">৳ {{ number_format($sOrig) }}</span>
                      @else
                        <span class="font-label-sm text-[10px] text-stock-available font-bold block">● In Stock</span>
                      @endif
                    </div>
                  </div>
                  <button onclick="addToCart('{{ addslashes($secProd->name) }}', {{ $sPrice }}, '{{ $secProd->has_real_image ? $secProd->thumbnail_url : '' }}')" class="w-full bg-surface-container hover:bg-primary hover:text-on-primary text-on-surface font-headline-sm text-label-sm py-2 rounded-xl transition-colors flex items-center justify-center gap-1 font-bold">
                    <span class="material-symbols-outlined text-[15px]">add_shopping_cart</span>
                    <span>Quick Add</span>
                  </button>
                </div>
              @endforeach
            </div>
          </section>
        @endif
      @endforeach
    @endif

    <!-- ========================================== -->
    <!-- SECTION 8: BRAND PARTNERS & LABELS -->
    <!-- ========================================== -->
    <!-- SECTION 8: TRUSTED PARTNERS & LABELS -->
    <!-- ========================================== -->
    <section id="brands" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
      <div class="bg-[#f0f8f5] rounded-3xl p-6 lg:p-8 border border-[#d4ebe4] shadow-xs">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-6 lg:mb-8">
          <h2 class="text-2xl lg:text-3xl font-bold text-[#102d28] font-headline-lg tracking-tight">Trusted Partners & Labels</h2>
          <p class="text-xs md:text-sm text-[#4a6b63] mt-1.5 font-medium">Tap any logo to jump into the shop filtered for that brand and explore the latest arrivals.</p>
        </div>
        
        <!-- Dynamic Brand Logo Cards Grid (6x3 on Desktop) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 lg:gap-3.5">
          @forelse($brands as $brandItem)
            <a href="{{ route('frontend.search', ['brand' => $brandItem->slug ?: $brandItem->id]) }}" title="{{ $brandItem->name }}" class="bg-white rounded-2xl border border-[#d9eee8] hover:border-[#006241] p-3.5 lg:p-4 flex items-center justify-center min-h-[75px] lg:min-h-[82px] shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 group">
              <div class="w-full flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                @if(!empty($brandItem->image_url))
                  <img src="{{ $brandItem->image_url }}" alt="{{ $brandItem->name }}" class="max-h-[38px] lg:max-h-[44px] max-w-[130px] w-auto object-contain" loading="lazy" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-xs sm:text-sm font-extrabold text-[#111827] uppercase tracking-wider\'>{{ addslashes($brandItem->name) }}</span>';"/>
                @else
                  <div class="flex items-center justify-center gap-1.5 px-2 py-1 text-center">
                    <span class="material-symbols-outlined text-[#4a6b63] text-[18px] opacity-70">sell</span>
                    <span class="text-xs sm:text-sm font-extrabold text-[#102d28] uppercase tracking-wider line-clamp-1 group-hover:text-[#006241] transition-colors">{{ $brandItem->name }}</span>
                  </div>
                @endif
              </div>
            </a>
          @empty
            <div class="col-span-full py-8 text-center text-text-muted">
              {{ __('No brands available.') }}
            </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- SECTION 9: PHYSICAL FLAGSHIP STORES IN DHAKA (Temporarily Hidden) -->
    <!-- ========================================== -->
    {{--
    <section id="flagship-stores" class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 w-full">
      <div class="flex items-center gap-1.5 mb-4">
        <span class="material-symbols-outlined text-[20px] text-rec-red-base">location_on</span>
        <h2 class="text-headline-md lg:text-headline-lg font-headline-lg text-text-display font-bold">Physical Flagship Stores in Dhaka</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
        @forelse($physicalStores as $store)
          @php
            $iconBgClass = ($loop->iteration % 2 == 1) 
              ? 'bg-primary/10 text-primary' 
              : 'bg-broadcast-teal-subtle text-broadcast-teal';
            $storeIcon = $store->icon ?: (($loop->iteration % 2 == 1) ? 'storefront' : 'store');
            $mapUrl = $store->map_link ?: ('https://maps.google.com/?q=' . urlencode($store->name . ' ' . $store->address));
          @endphp
          <div class="bg-surface-card rounded-2xl p-4 lg:p-6 shadow-sm flex flex-col justify-between border border-surface-border">
            <div class="flex items-start justify-between gap-4 mb-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl {{ $iconBgClass }} flex items-center justify-center flex-shrink-0">
                  <span class="material-symbols-outlined text-[26px]">{{ $storeIcon }}</span>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h4 class="text-headline-sm font-headline-sm text-text-display font-bold">{{ $store->name }}</h4>
                    @if(!empty($store->badge))
                      <span class="bg-emerald-50 text-stock-available text-label-sm font-label-sm px-2 py-0.5 rounded-full font-bold">{{ $store->badge }}</span>
                    @endif
                  </div>
                  @if(!empty($store->address))
                    <p class="text-body-sm font-body-sm text-text-muted">{{ $store->address }}</p>
                  @endif
                </div>
              </div>
            </div>
            <div class="space-y-1.5 mb-4 text-body-sm font-body-sm text-text-muted">
              @if(!empty($store->schedule))
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-[16px]">schedule</span>
                  <span>{{ $store->schedule }}</span>
                </div>
              @endif
              @if(!empty($store->phone))
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-[16px]">call</span>
                  <strong class="text-text-display">{{ $store->phone }}</strong>
                </div>
              @endif
              @if(!empty($store->highlights))
                <div class="flex items-center gap-2 text-broadcast-teal font-semibold">
                  <span class="material-symbols-outlined text-[16px]">verified</span>
                  <span>{{ $store->highlights }}</span>
                </div>
              @endif
            </div>
            <div class="flex items-center gap-3 pt-2">
              @if(!empty($store->clean_phone_url))
                <a class="flex-1 bg-surface-container-high hover:bg-surface-container text-text-display text-label-md font-label-md py-2.5 rounded-xl transition-colors flex items-center justify-center gap-1.5 font-bold" href="tel:{{ $store->clean_phone_url }}">
                  <span class="material-symbols-outlined text-[16px] text-primary">call</span>
                  <span>Call Branch</span>
                </a>
              @endif
              <a class="flex-1 bg-text-display hover:bg-black text-on-primary text-label-md font-label-md py-2.5 rounded-xl transition-colors flex items-center justify-center gap-1.5 font-bold" href="{{ $mapUrl }}" target="_blank">
                <span class="material-symbols-outlined text-[16px]">directions</span>
                <span>Directions</span>
              </a>
            </div>
          </div>
        @empty
          <!-- Fallback if no branches configured -->
          <div class="col-span-full py-8 text-center text-text-muted">
            <span class="material-symbols-outlined text-4xl mb-2 text-text-subtle">storefront</span>
            <p>Our store locations will be announced soon.</p>
          </div>
        @endforelse
      </div>
    </section>
    --}}

    <!-- ========================================== -->
    <!-- SECTION 10: VIP CREATOR NEWSLETTER -->
    <!-- ========================================== -->
    <section class="max-w-7xl mx-auto px-4 lg:px-6 pt-8 lg:pt-10 pb-6 w-full">
      <div class="bg-surface-container-high rounded-2xl p-5 lg:p-8 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-6 border border-surface-border">
        <div class="max-w-xl">
          <div class="flex items-center gap-2 mb-2">
            <span class="bg-primary text-on-primary text-label-sm font-label-sm px-2.5 py-0.5 rounded-full uppercase tracking-wider font-bold">VIP Creator Club</span>
            <span class="text-broadcast-teal font-label-md text-label-md font-bold">Exclusive Studio Offers</span>
          </div>
          <h3 class="font-headline-lg text-headline-lg text-text-display font-extrabold mb-1">Join {{ $siteName ?? 'CAMERA BD' }} VIP Creator Network</h3>
          <p class="font-body-md text-body-md text-text-muted">Instant price drops, firmware release alerts, and flat ৳ ১,৫০০ discount on your first professional purchase.</p>
        </div>
        <div class="flex w-full lg:w-auto items-center gap-2">
          <input id="newsletter-email" class="bg-surface-card text-on-surface px-4 py-3 rounded-xl text-body-md font-body-md focus:outline-none w-full lg:w-80 shadow-inner border border-surface-border" placeholder="Enter your email address..." type="email"/>
          <button onclick="handleSubscribe()" class="bg-rec-red-base hover:bg-rec-red-hover text-on-primary px-6 py-3 rounded-xl font-label-lg text-label-lg font-bold transition-all flex items-center justify-center gap-1.5 whitespace-nowrap shadow-sm active:scale-95">
            <span>Subscribe</span>
            <span class="material-symbols-outlined text-[18px]">send</span>
          </button>
        </div>
      </div>
    </section>

  </main>

  <!-- ========================================== -->
  <!-- FOOTER -->
  <!-- ========================================== -->
  @include('frontend.layouts.includes.footer')

  <!-- ========================================== -->
  <!-- MOBILE BOTTOM FIXED APP NAVIGATION BAR -->
  <!-- ========================================== -->
  <nav class="lg:hidden fixed bottom-0 w-full z-50 pb-safe bg-surface-card/95 backdrop-blur-xl shadow-[0_-2px_12px_rgba(15,23,42,0.06)] border-t border-surface-border">
    <div class="flex justify-around items-center h-16 px-1" id="mobile-nav-bar">
      <!-- Home -->
      <a aria-current="page" onclick="setActiveNav(this)" class="mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 transition-colors text-primary font-bold" href="#">
        <span class="material-symbols-outlined text-[22px]">home</span>
        <span class="font-label-sm text-[10px]">Home</span>
      </a>
      <!-- Categories Drawer Trigger -->
      <button type="button" onclick="openMobileCategoryDrawer(); setActiveNav(this);" class="mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors">
        <span class="material-symbols-outlined text-[22px]">category</span>
        <span class="font-label-sm text-[10px]">Categories</span>
      </button>
      <!-- Deals -->
      <a onclick="setActiveNav(this)" class="mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors" href="#hot-deals">
        <span class="material-symbols-outlined text-[22px]">local_fire_department</span>
        <span class="font-label-sm text-[10px]">Deals</span>
      </a>
      <!-- Orders -->
      <a onclick="setActiveNav(this)" class="mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors" href="#orders" onclick="showToast('Loading your order history...', 'info')">
        <span class="material-symbols-outlined text-[22px]">package_2</span>
        <span class="font-label-sm text-[10px]">Orders</span>
      </a>
      <!-- Account -->
      @auth
        <a onclick="setActiveNav(this)" class="mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors" href="{{ route('dashboard') }}">
          <span class="material-symbols-outlined text-[22px]">dashboard</span>
          <span class="font-label-sm text-[10px]">Panel</span>
        </a>
      @else
        <a onclick="setActiveNav(this)" class="mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors" href="{{ route('login') }}">
          <span class="material-symbols-outlined text-[22px]">account_circle</span>
          <span class="font-label-sm text-[10px]">Account</span>
        </a>
      @endauth
    </div>
  </nav>

  <!-- ========================================== -->
  <!-- JAVASCRIPT: CORE INTERACTIONS & STATE -->
  <!-- ========================================== -->
  <script>
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
        showToast(`Added "${item.name}" to bag!`, 'success');

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

        // Meta Pixel AddToCart Event
        try {
          if (typeof fbq === 'function') {
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

        // Update counters
        document.querySelectorAll('#top-cart-count, #desktop-cart-badge, #mobile-cart-badge, #cart-drawer-count').forEach(el => {
          if (el) el.textContent = count;
        });

        const subtotalFormatted = '৳ ' + Math.round(subtotal).toLocaleString('en-US') + ' TK';
        document.querySelectorAll('#cart-drawer-subtotal, #desktop-cart-total').forEach(el => {
          if (el) el.textContent = subtotalFormatted;
        });

        this.renderDrawer();
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
        document.querySelectorAll('#top-wishlist-count, #desktop-wishlist-count, #mobile-wishlist-count, #mobile-wishlist-badge, #wishlist-header-count, #wishlist-page-count').forEach(el => {
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

    function toggleCartDrawer() {
      const drawer = document.getElementById('cart-drawer');
      if (drawer && drawer.classList.contains('translate-x-full')) {
        CameraCart.openDrawer();
      } else {
        CameraCart.closeDrawer();
      }
    }

    function addToCart(productName, price, imageUrl, id = null, barcode = '', url = '#') {
      CameraCart.add({ id: id, name: productName, price: price, image: imageUrl, barcode: barcode, url: url, qty: 1 }, true);
    }

    function buyNow(productName, price, imageUrl, id = null, barcode = '', url = '#') {
      CameraCart.add({ id: id, name: productName, price: price, image: imageUrl, barcode: barcode, url: url, qty: 1 }, false);
      window.location.href = "{{ route('frontend.checkout') }}";
    }

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

    // Initialize cart & wishlist on load
    document.addEventListener('DOMContentLoaded', function() {
      CameraCart.sync();
      CameraWishlist.sync();
    });

    // Mobile Search Modal Handlers
    const searchModal = document.getElementById('mobile-search-modal');
    const searchBtn = document.getElementById('mobile-search-btn');
    const searchClose = document.getElementById('mobile-search-close');

    if (searchBtn && searchModal) {
      searchBtn.addEventListener('click', () => {
        searchModal.classList.remove('hidden');
        searchModal.classList.add('flex');
        const input = document.getElementById('mobile-search-input');
        if (input) {
          setTimeout(() => input.focus(), 50);
        }
      });
    }

    function closeMobileSearch() {
      if (searchModal) {
        searchModal.classList.add('hidden');
        searchModal.classList.remove('flex');
      }
    }

    if (searchClose) {
      searchClose.addEventListener('click', closeMobileSearch);
    }

    // Live AJAX Product Search Engine
    (function initLiveSearchEngine() {
      const liveSearchUrl = "{{ route('frontend.search.live') }}";
      
      // Desktop elements
      const desktopInput = document.getElementById('desktop-search-input');
      const desktopCategory = document.getElementById('desktop-search-category');
      const desktopResults = document.getElementById('desktop-search-results');
      const desktopClear = document.getElementById('desktop-search-clear');
      
      // Mobile elements
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

      function fetchSuggestions(query, category, container, isMobile = false) {
        if (!query || query.trim().length === 0) {
          container.innerHTML = '';
          container.classList.add('hidden');
          if (isMobile && mobileDefault) {
            mobileDefault.classList.remove('hidden');
            mobileResults.classList.add('hidden');
          }
          return;
        }

        fetch(`${liveSearchUrl}?q=${encodeURIComponent(query.trim())}&category=${encodeURIComponent(category || 'all')}`)
          .then(res => res.json())
          .then(data => {
            if (isMobile) {
              if (mobileDefault) mobileDefault.classList.add('hidden');
              mobileResults.classList.remove('hidden');
            }
            renderSearchResults(data, container, isMobile);
          })
          .catch(err => {
            console.error('Search error:', err);
          });
      }

      // Desktop Search Input Listener
      if (desktopInput && desktopResults) {
        desktopInput.addEventListener('input', function() {
          const val = this.value;
          if (desktopClear) {
            desktopClear.classList.toggle('hidden', val.length === 0);
          }
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            const cat = desktopCategory ? desktopCategory.value : 'all';
            fetchSuggestions(val, cat, desktopResults, false);
          }, 200);
        });

        if (desktopClear) {
          desktopClear.addEventListener('click', function() {
            desktopInput.value = '';
            desktopClear.classList.add('hidden');
            desktopResults.innerHTML = '';
            desktopResults.classList.add('hidden');
            desktopInput.focus();
          });
        }

        if (desktopCategory) {
          desktopCategory.addEventListener('change', function() {
            if (desktopInput.value.trim().length > 0) {
              fetchSuggestions(desktopInput.value, this.value, desktopResults, false);
            }
          });
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
          if (!e.target.closest('#desktop-search-container')) {
            desktopResults.classList.add('hidden');
          }
        });
      }

      // Mobile Search Input Listener
      if (mobileInput && mobileResults) {
        mobileInput.addEventListener('input', function() {
          const val = this.value;
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            fetchSuggestions(val, 'all', mobileResults, true);
          }, 200);
        });
      }
    })();

    // Popular Cinema Filter Tabs
    function filterCategory(category, activeBtn) {
      const tabs = document.querySelectorAll('.cinema-tab');
      tabs.forEach(t => {
        t.className = 'cinema-tab px-3.5 py-1.5 rounded-lg text-label-md font-label-md text-text-muted hover:text-text-display transition-colors';
      });
      activeBtn.className = 'cinema-tab px-3.5 py-1.5 rounded-lg text-label-md font-label-md bg-primary text-on-primary font-bold shadow-xs';

      const cards = document.querySelectorAll('.product-card');
      cards.forEach(card => {
        if (category === 'all' || card.getAttribute('data-category') === category) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Mobile Bottom Nav Active Switcher
    function setActiveNav(element) {
      document.querySelectorAll('.mobile-nav-item').forEach(item => {
        item.className = 'mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors';
      });
      element.className = 'mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 transition-colors text-primary font-bold';
    }

    // Newsletter Subscription
    function handleSubscribe() {
      const input = document.getElementById('newsletter-email');
      if (input && input.value.includes('@')) {
        showToast('Welcome to {{ $siteName ?? "CAMERA BD" }} VIP! Flat ৳ 1,500 coupon sent to your inbox.', 'success');
        input.value = '';
      } else {
        showToast('Please enter a valid email address.', 'error');
      }
    }

    // Synchronized Live Countdown Timer based on Dynamic End Date
    (function initCountdown() {
      const timerWrap = document.getElementById('flash-deal-timer-wrap');
      if (!timerWrap) return;

      const rawEndDate = timerWrap.getAttribute('data-end-date');
      let targetTime = new Date(rawEndDate ? rawEndDate.replace(' ', 'T') : '').getTime();
      if (isNaN(targetTime)) {
        targetTime = Date.now() + 7 * 24 * 3600 * 1000;
      }

      const daysEls = document.querySelectorAll('.cd-days');
      const daysSepEls = document.querySelectorAll('.cd-days-sep');
      const hoursEls = document.querySelectorAll('.cd-hours');
      const minsEls = document.querySelectorAll('.cd-mins');
      const secsEls = document.querySelectorAll('.cd-secs');

      function updateCountdown() {
        const now = Date.now();
        const diff = targetTime - now;

        if (diff <= 0) {
          hoursEls.forEach(el => el.textContent = '00');
          minsEls.forEach(el => el.textContent = '00');
          secsEls.forEach(el => el.textContent = '00');
          daysEls.forEach(el => el.classList.add('hidden'));
          daysSepEls.forEach(el => el.classList.add('hidden'));
          return;
        }

        const totalSecs = Math.floor(diff / 1000);
        const days = Math.floor(totalSecs / 86400);
        const hours = Math.floor((totalSecs % 86400) / 3600);
        const mins = Math.floor((totalSecs % 3600) / 60);
        const secs = totalSecs % 60;

        if (days > 0) {
          daysEls.forEach(el => {
            el.textContent = String(days).padStart(2, '0') + 'd';
            el.classList.remove('hidden');
          });
          daysSepEls.forEach(el => el.classList.remove('hidden'));
        } else {
          daysEls.forEach(el => el.classList.add('hidden'));
          daysSepEls.forEach(el => el.classList.add('hidden'));
        }

        const hStr = String(hours).padStart(2, '0');
        const mStr = String(mins).padStart(2, '0');
        const sStr = String(secs).padStart(2, '0');

        hoursEls.forEach(el => el.textContent = hStr);
        minsEls.forEach(el => el.textContent = mStr);
        secsEls.forEach(el => el.textContent = sStr);
      }

      updateCountdown();
      setInterval(updateCountdown, 1000);
    })();

    // Auto-Sliding Category Carousel
    (function initCategorySlider() {
      const track = document.getElementById('category-slider-track');
      const prevBtn = document.getElementById('cat-slider-prev');
      const nextBtn = document.getElementById('cat-slider-next');
      if (!track) return;

      let isPaused = false;
      let autoSlideTimer = null;

      const getScrollStep = () => {
        const firstCard = track.querySelector('a');
        if (firstCard) {
          return firstCard.offsetWidth + 16; // width + gap
        }
        return 160;
      };

      const slideNext = () => {
        const maxScroll = track.scrollWidth - track.clientWidth;
        if (track.scrollLeft >= maxScroll - 15) {
          track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
        }
      };

      const slidePrev = () => {
        if (track.scrollLeft <= 15) {
          track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
        }
      };

      const startAutoSlide = () => {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
        autoSlideTimer = setInterval(() => {
          if (!isPaused) {
            slideNext();
          }
        }, 2500);
      };

      track.addEventListener('mouseenter', () => { isPaused = true; });
      track.addEventListener('mouseleave', () => { isPaused = false; });
      track.addEventListener('touchstart', () => { isPaused = true; }, { passive: true });
      track.addEventListener('touchend', () => { 
        setTimeout(() => { isPaused = false; }, 2000); 
      }, { passive: true });

      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          slideNext();
        });
      }
      if (prevBtn) {
        prevBtn.addEventListener('click', () => {
          slidePrev();
        });
      }

      startAutoSlide();
    })();

    // Hollyland Showcase Slider Controls
    (function initHollylandSlider() {
      const hlTrack = document.getElementById('hollyland-track');
      const hlPrev = document.getElementById('hollyland-prev-btn');
      const hlNext = document.getElementById('hollyland-next-btn');
      const hlCount = document.getElementById('hollyland-showing-count');
      if (!hlTrack) return;

      const getStep = () => {
        const card = hlTrack.querySelector('div.snap-start');
        return card ? (card.offsetWidth + 14) * 2 : 440;
      };

      if (hlNext) {
        hlNext.addEventListener('click', () => {
          hlTrack.scrollBy({ left: getStep(), behavior: 'smooth' });
        });
      }

      if (hlPrev) {
        hlPrev.addEventListener('click', () => {
          hlTrack.scrollBy({ left: -getStep(), behavior: 'smooth' });
        });
      }
    })();

    // Hero Dynamic Banner Auto Slider (Desktop & Mobile)
    (function initHeroSlider() {
      let currentHeroSlideIndex = 0;
      const desktopHeroSlides = document.querySelectorAll('.desktop-hero-slide');
      const mobileHeroSlides = document.querySelectorAll('.mobile-hero-slide');
      const totalHeroSlides = Math.max(desktopHeroSlides.length, mobileHeroSlides.length);
      let heroSliderTimer = null;

      window.goToHeroSlide = function(index) {
        if (totalHeroSlides <= 1) return;
        currentHeroSlideIndex = (index + totalHeroSlides) % totalHeroSlides;

        // Desktop Slides & Dots
        desktopHeroSlides.forEach((slide, i) => {
          if (i === currentHeroSlideIndex) {
            slide.classList.remove('opacity-0', 'z-0', 'pointer-events-none');
            slide.classList.add('opacity-100', 'z-10');
          } else {
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'z-0', 'pointer-events-none');
          }
        });

        document.querySelectorAll('.desktop-hero-dot').forEach((dot, i) => {
          if (i === currentHeroSlideIndex) {
            dot.classList.add('bg-white', 'w-6');
            dot.classList.remove('bg-white/50', 'w-2');
          } else {
            dot.classList.remove('bg-white', 'w-6');
            dot.classList.add('bg-white/50', 'w-2');
          }
        });

        // Mobile Slides & Dots
        mobileHeroSlides.forEach((slide, i) => {
          if (i === currentHeroSlideIndex) {
            slide.classList.remove('opacity-0', 'z-0', 'pointer-events-none');
            slide.classList.add('opacity-100', 'z-10');
          } else {
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'z-0', 'pointer-events-none');
          }
        });

        document.querySelectorAll('.hero-dot').forEach((dot, i) => {
          if (i === currentHeroSlideIndex) {
            dot.classList.add('bg-white', 'w-4');
            dot.classList.remove('bg-white/50', 'w-2');
          } else {
            dot.classList.remove('bg-white', 'w-4');
            dot.classList.add('bg-white/50', 'w-2');
          }
        });

        resetHeroTimer();
      };

      window.nextHeroSlide = function() {
        goToHeroSlide(currentHeroSlideIndex + 1);
      };

      window.prevHeroSlide = function() {
        goToHeroSlide(currentHeroSlideIndex - 1);
      };

      function resetHeroTimer() {
        if (heroSliderTimer) clearInterval(heroSliderTimer);
        if (totalHeroSlides > 1) {
          heroSliderTimer = setInterval(window.nextHeroSlide, 5000);
        }
      }

      if (totalHeroSlides > 1) {
        resetHeroTimer();
        const dSlider = document.getElementById('desktop-hero-slider');
        if (dSlider) {
          dSlider.addEventListener('mouseenter', () => clearInterval(heroSliderTimer));
          dSlider.addEventListener('mouseleave', resetHeroTimer);
        }
      }
    })();
  </script>
  <!-- GA4 / GTM DataLayer Home View Item List -->
  @php
    $dlHomeItems = [];
    if (isset($flashDealProducts) && $flashDealProducts->isNotEmpty()) {
      foreach ($flashDealProducts->take(12) as $idx => $p) {
        $dlHomeItems[] = [
          'item_id' => (string)($p->id ?? ($p->barcode ?? '')),
          'item_name' => $p->name ?? '',
          'affiliation' => $siteName ?? 'CAMERA BD',
          'currency' => 'BDT',
          'discount' => (float)($p->discount ?? 0),
          'index' => $idx + 1,
          'item_brand' => $p->brand?->name ?? 'CAMERA BD',
          'item_category' => $p->category?->name ?? 'Gear',
          'price' => (float)($p->selling_price ?? 0),
          'quantity' => 1
        ];
      }
    }
  @endphp
  @if(!empty($dlHomeItems))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof window.pushToDataLayer === 'function') {
        window.pushToDataLayer('view_item_list', {
          item_list_id: 'home_flash_deals',
          item_list_name: 'Featured & Flash Deals',
          items: {!! json_encode($dlHomeItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
        });
      }
    });
  </script>
  @endif
</body>
</html>
