@extends('frontend.layouts.master')

@section('title', 'My Wishlist - ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 pt-4 lg:pt-6 pb-16 w-full">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-6">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[15px]">home</span> Home
    </a>
    <span>/</span>
    <span class="text-on-surface font-semibold">Wishlist</span>
  </nav>

  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-2xl lg:text-3xl font-bold text-text-display">My Production Wishlist</h1>
      <p class="text-xs text-text-muted mt-1">Saved cinema cameras, audio gear, and lighting accessories (<span id="wishlist-page-count" class="font-bold text-primary">0</span> items)</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="button" onclick="CameraWishlist.addAllToCart()" class="bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
        <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
        <span>Add All to Cart</span>
      </button>
      <button type="button" onclick="CameraWishlist.clear()" class="bg-surface-card hover:bg-red-50 text-text-muted hover:text-red-500 border border-surface-border text-xs font-bold py-2.5 px-3.5 rounded-xl transition-all flex items-center gap-1.5 shadow-xs" title="Clear all saved items">
        <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
        <span>Clear</span>
      </button>
      <button type="button" onclick="shareWishlist()" class="bg-surface-card hover:bg-surface-container text-on-surface border border-surface-border text-xs font-bold py-2.5 px-4 rounded-xl transition-all flex items-center gap-1.5 shadow-xs">
        <span class="material-symbols-outlined text-[16px]">share</span>
        <span>Share List</span>
      </button>
    </div>
  </div>

  <!-- Wishlist Grid & Dynamic Container -->
  <div id="wishlist-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
    <!-- Rendered dynamically by renderWishlistPage() via CameraWishlist -->
  </div>

</div>
@endsection

@push('scripts')
<script>
  function renderWishlistPage() {
    const grid = document.getElementById('wishlist-grid');
    if (!grid) return;

    const items = window.CameraWishlist ? CameraWishlist.get() : [];
    const countEl = document.getElementById('wishlist-page-count');
    if (countEl) countEl.textContent = items.length;

    if (items.length === 0) {
      grid.className = 'w-full block';
      grid.innerHTML = `
        <div class="bg-surface-card rounded-3xl p-10 md:p-16 border border-surface-border shadow-xs text-center max-w-xl mx-auto my-6">
          <div class="w-20 h-20 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
            <span class="material-symbols-outlined text-[42px]">favorite_border</span>
          </div>
          <h2 class="text-xl font-bold text-text-display mb-1.5">Your Wishlist is Empty</h2>
          <p class="text-xs text-text-muted leading-relaxed mb-6">
            You haven't saved any camera, lens, or studio production gear to your wishlist yet. Browse our catalog and click the heart icon on any gear you love!
          </p>
          <div class="flex items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-3 rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">
              <span class="material-symbols-outlined text-[18px]">shopping_basket</span>
              <span>Explore Products</span>
            </a>
            <a href="{{ route('frontend.categories') }}" class="inline-flex items-center gap-2 bg-surface-card hover:bg-surface-container text-on-surface border border-surface-border px-5 py-3 rounded-xl font-bold text-xs shadow-xs transition-colors">
              <span>View Categories</span>
            </a>
          </div>
        </div>
      `;
      return;
    }

    grid.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5';
    let html = '';

    items.forEach((item, idx) => {
      const priceNum = parseFloat(item.price) || 0;
      const oldPriceNum = parseFloat(item.old_price) || 0;
      const formattedPrice = priceNum.toLocaleString('en-US');
      const formattedOldPrice = oldPriceNum > priceNum ? oldPriceNum.toLocaleString('en-US') : null;
      const productUrl = item.url || (item.slug ? `{{ url('/product-details') }}/${item.slug}` : (item.id ? `{{ url('/product-details') }}/${item.id}` : '#'));
      const brandName = item.brand || 'Official Gear';
      const reviews = item.reviews_count || 12;

      const imgTag = item.image 
        ? `<img src="${escapeAttr(item.image)}" alt="${escapeAttr(item.name)}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\\'material-symbols-outlined text-slate-300 text-[48px]\\'>photo_camera</span>';" />`
        : `<span class="material-symbols-outlined text-slate-300 text-[48px]">photo_camera</span>`;

      html += `
        <div class="wishlist-card bg-surface-card rounded-3xl p-4 border border-surface-border flex flex-col justify-between hover:shadow-md transition-all duration-300 group" id="wishlist-card-${idx}">
          <div>
            <!-- Image Box with Remove Action -->
            <div class="relative bg-surface-base rounded-2xl p-4 aspect-square flex items-center justify-center mb-3 overflow-hidden border border-surface-border">
              <button type="button" onclick="removeWishlistItemCard(${idx}, '${escapeJsString(item.name)}')" class="absolute top-2.5 right-2.5 z-10 w-8 h-8 rounded-full bg-white/90 hover:bg-red-50 text-text-muted hover:text-red-500 flex items-center justify-center transition-colors shadow-xs cursor-pointer" title="Remove from wishlist">
                <span class="material-symbols-outlined text-[18px]">delete</span>
              </button>
              <a href="${productUrl}" class="w-full h-full flex items-center justify-center">
                ${imgTag}
              </a>
            </div>

            <!-- Brand & Stock Status -->
            <div class="flex items-center justify-between gap-1 mb-1 text-[10px]">
              <span class="text-primary font-bold uppercase truncate">${escapeHtml(brandName)}</span>
              <span class="text-stock-available font-bold flex items-center gap-0.5 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-stock-available"></span> In Stock
              </span>
            </div>

            <!-- Product Title -->
            <h3 class="text-xs md:text-sm font-bold text-text-display line-clamp-2 leading-snug mb-2 group-hover:text-primary transition-colors">
              <a href="${productUrl}">${escapeHtml(item.name)}</a>
            </h3>

            <!-- Rating -->
            <div class="flex items-center gap-1 text-[11px] text-amber-500 mb-3">
              <span>★★★★★</span>
              <span class="text-text-muted font-medium text-[10px]">(${reviews})</span>
            </div>
          </div>

          <!-- Price & Add to Cart -->
          <div class="pt-3 border-t border-surface-border">
            <div class="flex items-baseline gap-2 mb-3">
              <span class="text-base font-extrabold text-primary">৳ ${formattedPrice} TK</span>
              ${formattedOldPrice ? `<span class="text-xs text-text-muted line-through">৳ ${formattedOldPrice}</span>` : ''}
            </div>

            <button type="button" onclick="handleWishlistAddToCart(${idx})" class="w-full bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2.5 rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
              <span>Add to Cart</span>
            </button>
          </div>
        </div>
      `;
    });

    grid.innerHTML = html;
  }

  function removeWishlistItemCard(index, name) {
    const card = document.getElementById(`wishlist-card-${index}`);
    if (card) {
      card.style.opacity = '0';
      card.style.transform = 'scale(0.95)';
      card.style.transition = 'all 0.2s ease-out';
      setTimeout(() => {
        CameraWishlist.remove(index);
      }, 200);
    } else {
      CameraWishlist.remove(index);
    }
  }

  function handleWishlistAddToCart(index) {
    const items = CameraWishlist.get();
    if (items[index]) {
      const item = items[index];
      if (window.CameraCart) {
        CameraCart.add({
          id: item.id,
          name: item.name,
          price: item.price,
          image: item.image,
          barcode: item.barcode || item.sku || '',
          url: item.url || '#',
          qty: 1
        }, true);
      }
    }
  }

  function shareWishlist() {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(window.location.href);
      showToast('Wishlist page link copied to clipboard!', 'success');
    } else {
      showToast('Share link ready: ' + window.location.href, 'info');
    }
  }

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function escapeAttr(text) {
    if (!text) return '';
    return String(text).replace(/"/g, '&quot;');
  }

  function escapeJsString(text) {
    if (!text) return '';
    return String(text).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
  }

  // GA4 / GTM DataLayer View Wishlist Event
  function trackDataLayerViewWishlist() {
    if (typeof window.pushToDataLayer !== 'function') return;
    const items = window.CameraWishlist ? CameraWishlist.get() : [];
    if (!items || items.length === 0) return;

    const dlWishlistItems = items.map(function(item, idx) {
      return {
        item_id: String(item.id || item.slug || item.name),
        item_name: item.name || 'Camera Product',
        affiliation: '{{ addslashes($siteName ?? "CAMERA BD") }}',
        currency: 'BDT',
        price: parseFloat(item.price) || 0,
        quantity: 1,
        index: idx + 1
      };
    });

    window.pushToDataLayer('view_item_list', {
      item_list_id: 'customer_wishlist',
      item_list_name: 'Customer Saved Wishlist',
      items: dlWishlistItems
    });
  }

  // Listen for live wishlist changes
  window.addEventListener('camerawishlist:updated', function() {
    renderWishlistPage();
  });

  document.addEventListener('DOMContentLoaded', function() {
    trackDataLayerViewWishlist();
    renderWishlistPage();
  });
</script>
@endpush
