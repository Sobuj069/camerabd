{{-- Mobile Category & Subcategory Slide-Over Drawer --}}

{{-- Dark Backdrop Overlay --}}
<div id="mobile-category-overlay" 
  class="fixed inset-0 z-[110] bg-black/60 backdrop-blur-xs hidden transition-opacity" 
  onclick="closeMobileCategoryDrawer()">
</div>

{{-- Slide-Over Drawer Container --}}
<div id="mobile-category-drawer" 
  class="fixed top-0 bottom-0 left-0 z-[120] w-[330px] max-w-[88vw] bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-in-out font-sans">
  
  {{-- 1. Drawer Header --}}
  <div class="bg-[#006241] text-white px-4 py-3.5 flex items-center justify-between shadow-sm shrink-0">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px] text-white">category</span>
      </div>
      <div>
        <h3 class="text-sm font-bold leading-tight">All Categories</h3>
        <p class="text-[10px] text-emerald-200 leading-none mt-0.5">Browse Pro Cine, Camera & Audio Gear</p>
      </div>
    </div>
    <button type="button" 
      onclick="closeMobileCategoryDrawer()" 
      class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center active:scale-95 transition-all"
      aria-label="Close Categories">
      <span class="material-symbols-outlined text-[20px]">close</span>
    </button>
  </div>

  {{-- 2. Live Category & Subcategory Instant Filter --}}
  <div class="p-3 bg-slate-50 border-b border-slate-200 shrink-0">
    <div class="relative flex items-center">
      <span class="material-symbols-outlined text-[18px] text-slate-400 absolute left-2.5 pointer-events-none">search</span>
      <input type="text" 
        id="mobile-cat-search-input"
        oninput="filterMobileCategories(this)" 
        placeholder="Search category or subcategory..." 
        class="w-full bg-white text-xs text-slate-800 placeholder:text-slate-400 pl-8 pr-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:border-[#006241] focus:ring-1 focus:ring-[#006241] transition-all">
    </div>
  </div>

  {{-- 3. Category Accordion List --}}
  <div class="flex-1 overflow-y-auto mega-scroll-style divide-y divide-slate-100" id="mobile-categories-list">
    @if(isset($navCategories) && count($navCategories) > 0)
      @foreach($navCategories as $cat)
        @php
          $hasSubs = !empty($cat['subcategories']) && count($cat['subcategories']) > 0;
        @endphp

        <div class="mobile-category-item group" data-category-name="{{ strtolower($cat['name']) }}">
          
          {{-- Category Accordion Header --}}
          @if($hasSubs)
            <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 active:bg-emerald-50/50 cursor-pointer transition-colors select-none"
              onclick="toggleMobileCategoryAccordion('{{ $cat['slug'] }}')">
              
              <div class="flex items-center gap-3 min-w-0 flex-1 pr-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#006241] flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[19px]">
                    {{ $cat['icon'] ?? 'category' }}
                  </span>
                </div>
                <div class="min-w-0">
                  <span class="text-xs font-bold text-slate-800 block truncate">{{ $cat['name'] }}</span>
                  <span class="text-[10px] text-slate-400">{{ count($cat['subcategories']) }} subcategories</span>
                </div>
              </div>

              <div class="flex items-center gap-1.5 shrink-0">
                <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-200" id="mobile-chevron-{{ $cat['slug'] }}">
                  <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
              </div>
            </div>

            {{-- Collapsible Subcategories Box --}}
            <div id="mobile-subcat-{{ $cat['slug'] }}" class="hidden bg-slate-50/90 border-t border-b border-slate-200/80 px-3 py-2 space-y-1 animate-fadeIn">
              
              {{-- View All Category Products Direct Link --}}
              <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" 
                class="flex items-center justify-between px-3 py-2 rounded-lg bg-[#006241] text-white text-xs font-bold shadow-2xs hover:bg-[#004d33] transition-colors">
                <span class="flex items-center gap-1.5 truncate">
                  <span class="material-symbols-outlined text-[16px] shrink-0">grid_view</span>
                  <span class="truncate">View All {{ $cat['name'] }}</span>
                </span>
                <span class="material-symbols-outlined text-[15px] shrink-0">arrow_forward</span>
              </a>

              {{-- Subcategory Items Grid/List --}}
              <div class="grid grid-cols-1 gap-0.5 pt-1">
                @foreach($cat['subcategories'] as $sub)
                  <a href="{{ $sub['url'] ?? route('frontend.subcategory', [$cat['slug'], $sub['slug']]) }}" 
                    class="mobile-subcategory-item flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:text-[#006241] hover:bg-white transition-all border border-transparent hover:border-slate-200"
                    data-sub-name="{{ strtolower($sub['name']) }}">
                    <span class="material-symbols-outlined text-[16px] text-emerald-700 shrink-0">
                      {{ $sub['icon'] ?? 'label' }}
                    </span>
                    <span class="truncate">{{ $sub['name'] }}</span>
                  </a>
                @endforeach
              </div>

            </div>
          @else
            {{-- Category without subcategories: Direct Link --}}
            <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" 
              class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 active:bg-emerald-50 transition-colors">
              <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#006241] flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[19px]">
                    {{ $cat['icon'] ?? 'category' }}
                  </span>
                </div>
                <span class="text-xs font-bold text-slate-800 truncate">{{ $cat['name'] }}</span>
              </div>
              <span class="material-symbols-outlined text-[18px] text-slate-400">chevron_right</span>
            </a>
          @endif

        </div>
      @endforeach
    @else
      <div class="p-8 text-center text-slate-400 text-xs">
        <span class="material-symbols-outlined text-[32px] text-slate-300 block mb-1">category</span>
        {{ __('No categories found') }}
      </div>
    @endif
  </div>

  {{-- 4. Drawer Footer: Support Hotline & Deals --}}
  <div class="p-3.5 bg-slate-100 border-t border-slate-200 space-y-2 shrink-0">
    <div class="flex items-center justify-between text-xs">
      <a href="tel:{{ $whatsappDigits ?? '8801917256756' }}" class="flex items-center gap-1.5 text-[#006241] font-bold hover:underline">
        <span class="material-symbols-outlined text-[16px]">phone_in_talk</span>
        <span>{{ $sitePhoneFormatted ?? '+880-1917-256-756' }}</span>
      </a>
      <a href="{{ url('/#hot-deals') }}" class="flex items-center gap-1 text-amber-700 font-bold bg-amber-100 px-2.5 py-1 rounded-md">
        <span class="material-symbols-outlined text-[14px]">local_fire_department</span> Hot Deals
      </a>
    </div>
  </div>

</div>

{{-- Scripts for Mobile Category Drawer --}}
<script>
  function openMobileCategoryDrawer() {
    const drawer = document.getElementById('mobile-category-drawer');
    const overlay = document.getElementById('mobile-category-overlay');
    if (drawer && overlay) {
      drawer.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      const searchInput = document.getElementById('mobile-cat-search-input');
      if (searchInput) {
        setTimeout(() => searchInput.focus(), 250);
      }
    }
  }

  function closeMobileCategoryDrawer() {
    const drawer = document.getElementById('mobile-category-drawer');
    const overlay = document.getElementById('mobile-category-overlay');
    if (drawer && overlay) {
      drawer.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.style.overflow = '';
    }
  }

  function toggleMobileCategoryDrawer() {
    const drawer = document.getElementById('mobile-category-drawer');
    if (drawer && drawer.classList.contains('-translate-x-full')) {
      openMobileCategoryDrawer();
    } else {
      closeMobileCategoryDrawer();
    }
  }

  function toggleMobileCategoryAccordion(slug) {
    const subMenu = document.getElementById('mobile-subcat-' + slug);
    const chevron = document.getElementById('mobile-chevron-' + slug);
    if (subMenu) {
      const isHidden = subMenu.classList.contains('hidden');
      if (isHidden) {
        subMenu.classList.remove('hidden');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
      } else {
        subMenu.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
      }
    }
  }

  function filterMobileCategories(input) {
    const query = input.value.toLowerCase().trim();
    const items = document.querySelectorAll('.mobile-category-item');

    items.forEach(item => {
      const catName = item.getAttribute('data-category-name') || '';
      const subItems = item.querySelectorAll('.mobile-subcategory-item');
      let hasMatchingSub = false;

      subItems.forEach(sub => {
        const subName = sub.getAttribute('data-sub-name') || '';
        if (!query || subName.includes(query)) {
          sub.style.display = '';
          if (query && subName.includes(query)) {
            hasMatchingSub = true;
          }
        } else {
          sub.style.display = 'none';
        }
      });

      if (!query || catName.includes(query) || hasMatchingSub) {
        item.style.display = '';
        if (query && hasMatchingSub) {
          const subContainer = item.querySelector('[id^="mobile-subcat-"]');
          const chevron = item.querySelector('[id^="mobile-chevron-"]');
          if (subContainer) subContainer.classList.remove('hidden');
          if (chevron) chevron.style.transform = 'rotate(180deg)';
        }
      } else {
        item.style.display = 'none';
      }
    });
  }
</script>
