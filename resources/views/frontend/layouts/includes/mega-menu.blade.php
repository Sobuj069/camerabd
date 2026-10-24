{{-- CAMERA BD Top Nav Bar & Desktop Mega Menu --}}
<div class="hidden lg:block bg-[#006241] text-white shadow-md relative z-40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-11">
    
    {{-- Left: "All Categories" Dropdown Trigger & Links --}}
    <div class="flex items-center gap-3 md:gap-5 h-full shrink-0">
      
      {{-- Mega Menu Trigger Container --}}
      <div class="relative h-full flex items-center group/megamenu shrink-0" id="mega-menu-wrapper">
        
        {{-- Trigger Button (Strictly Single-Line) --}}
        <button type="button" 
          id="all-categories-btn"
          class="bg-white text-slate-900 px-3.5 py-1.5 rounded-md font-bold text-xs md:text-sm flex items-center gap-2 shadow-sm hover:bg-slate-50 transition-all select-none cursor-pointer focus:outline-none whitespace-nowrap shrink-0 min-w-max"
          aria-expanded="false"
          onclick="toggleMegaMenuDropdown()">
          <span class="material-symbols-outlined text-[19px] text-slate-800 shrink-0">menu</span>
          <span class="font-bold whitespace-nowrap text-xs md:text-sm">All Categories</span>
          <span class="material-symbols-outlined text-[17px] text-slate-500 transition-transform duration-200 shrink-0" id="mega-arrow-icon">expand_more</span>
        </button>

        {{-- Mega Menu Popup Box --}}
        <div id="mega-menu-dropdown" 
          class="absolute top-full left-0 mt-1 w-[920px] lg:w-[1020px] bg-white rounded-b-2xl shadow-[0_20px_60px_rgba(0,0,0,0.18)] border border-slate-200/90 z-50 overflow-hidden hidden group-hover/megamenu:block transition-all duration-150 animate-fadeIn"
          style="max-width: calc(100vw - 2rem);">
          
          <div class="flex flex-row min-h-[460px] max-h-[540px]">
            
            {{-- 1. LEFT PANEL: Categories List --}}
            <div class="w-[260px] lg:w-[280px] shrink-0 border-r border-slate-150 bg-[#fbfbfb] py-2 overflow-y-auto mega-scroll-style">
              @if(isset($navCategories) && count($navCategories) > 0)
                @foreach($navCategories as $index => $cat)
                  <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" 
                    class="mega-cat-item flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-white hover:text-[#006241] cursor-pointer transition-all border-l-4 border-transparent {{ $index === 0 ? 'active-category-tab bg-white text-[#006241] border-[#006241] font-bold shadow-2xs' : '' }}"
                    data-cat-slug="{{ $cat['slug'] }}"
                    onmouseenter="switchCategoryTab('{{ $cat['slug'] }}', this)">
                    
                    <div class="flex items-center gap-2.5 truncate pr-2">
                      <span class="material-symbols-outlined text-[19px] shrink-0 {{ $index === 0 ? 'text-[#006241]' : 'text-emerald-700' }}">
                        {{ $cat['icon'] ?? 'category' }}
                      </span>
                      <span class="truncate">{{ $cat['name'] }}</span>
                    </div>

                    @if(!empty($cat['subcategories']) && count($cat['subcategories']) > 0)
                      <span class="material-symbols-outlined text-[16px] text-slate-400 shrink-0">chevron_right</span>
                    @endif
                  </a>
                @endforeach
              @else
                <div class="p-4 text-xs text-slate-400 text-center">
                  {{ __('No categories found') }}
                </div>
              @endif
            </div>

            {{-- 2. RIGHT PANEL: Subcategories Display Area --}}
            <div class="flex-1 p-6 bg-white overflow-y-auto mega-scroll-style">
              @if(isset($navCategories) && count($navCategories) > 0)
                @foreach($navCategories as $index => $cat)
                  <div class="mega-pane {{ $index === 0 ? 'block' : 'hidden' }}" id="pane-{{ $cat['slug'] }}">
                    
                    {{-- Category Header --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                      <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#006241]">{{ $cat['icon'] ?? 'category' }}</span>
                        <span>{{ $cat['name'] }}</span>
                      </h3>
                      <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" 
                        class="text-xs text-[#006241] hover:text-[#004d33] font-bold flex items-center gap-0.5 group/viewall transition-colors bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-full">
                        <span>View All {{ $cat['name'] }}</span>
                        <span class="material-symbols-outlined text-[14px] group-hover/viewall:translate-x-0.5 transition-transform">arrow_forward</span>
                      </a>
                    </div>

                    {{-- Subcategories Grid --}}
                    @if(!empty($cat['subcategories']) && count($cat['subcategories']) > 0)
                      <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3.5">
                        @foreach($cat['subcategories'] as $sub)
                          <a href="{{ $sub['url'] ?? route('frontend.subcategory', [$cat['slug'], $sub['slug']]) }}" 
                            class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-emerald-50/60 group/sub transition-all border border-slate-100 hover:border-emerald-200">
                            
                            {{-- Icon Container --}}
                            <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover/sub:bg-emerald-100 flex items-center justify-center text-slate-600 group-hover/sub:text-[#006241] transition-colors shrink-0">
                              <span class="material-symbols-outlined text-[18px]">
                                {{ $sub['icon'] ?? 'label' }}
                              </span>
                            </div>

                            {{-- Subcategory Title --}}
                            <div class="flex-1 min-w-0">
                              <span class="text-xs font-medium text-slate-700 group-hover/sub:text-[#006241] group-hover/sub:font-semibold truncate block transition-colors">
                                {{ $sub['name'] }}
                              </span>
                            </div>
                          </a>
                        @endforeach
                      </div>
                    @else
                      {{-- Clean view when no subcategories added in database yet --}}
                      <div class="flex flex-col items-center justify-center text-center py-12 px-4 bg-slate-50/80 rounded-2xl border border-dashed border-slate-200">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#006241] flex items-center justify-center mb-3">
                          <span class="material-symbols-outlined text-[30px]">{{ $cat['icon'] ?? 'category' }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">{{ $cat['name'] }}</h4>
                        <p class="text-xs text-slate-500 max-w-sm mb-4">Explore all {{ $cat['name'] }} gear and accessories with official warranty and express delivery.</p>
                        <a href="{{ $cat['view_all_url'] ?? route('frontend.category', $cat['slug']) }}" 
                          class="inline-flex items-center gap-2 bg-[#006241] hover:bg-[#004d33] text-white text-xs font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm active:scale-95">
                          <span>Browse {{ $cat['name'] }} Products</span>
                          <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                      </div>
                    @endif

                  </div>
                @endforeach
              @endif
            </div>

          </div>
        </div>

      </div>

      {{-- Top Nav Horizontal Links --}}
      <nav class="hidden md:flex items-center gap-1 text-xs font-medium shrink-0">
        <a class="px-3 py-1 text-white/90 hover:text-white hover:bg-white/10 rounded-md transition-colors font-semibold" 
          href="{{ route('frontend.categories') }}">
          Shop
        </a>
        @if(isset($navCategories) && count($navCategories) > 0)
          @foreach(array_slice($navCategories, 0, 5) as $hCat)
            <a class="px-3 py-1 text-white/90 hover:text-white hover:bg-white/10 rounded-md transition-colors whitespace-nowrap" 
              href="{{ $hCat['view_all_url'] ?? route('frontend.category', $hCat['slug']) }}">
              {{ $hCat['name'] }}
            </a>
          @endforeach
        @endif
        <a class="px-3 py-1 text-amber-300 hover:text-amber-200 hover:bg-white/10 rounded-md font-bold transition-colors flex items-center gap-1 whitespace-nowrap" 
          href="{{ url('/#hot-deals') }}">
          <span class="material-symbols-outlined text-[14px]">local_fire_department</span> Hot Deals
        </a>
      </nav>

    </div>

    {{-- Right: Promotion / Help badge --}}
    <div class="flex items-center gap-3 text-xs shrink-0">
      <a href="tel:{{ $whatsappDigits ?? '8801917256756' }}" class="hidden sm:flex items-center gap-1 text-white/90 hover:text-white font-semibold whitespace-nowrap">
        <span class="material-symbols-outlined text-[16px] text-amber-300">phone_in_talk</span>
        <span>{{ $sitePhoneFormatted ?? '+880-1917-256-756' }}</span>
      </a>
      <span class="text-white/30 hidden sm:inline">|</span>
      <span class="text-emerald-200 font-medium text-[11px] flex items-center gap-1 whitespace-nowrap">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-ping"></span>
        <span>Official Warranty</span>
      </span>
    </div>

  </div>
</div>

<script>
  function switchCategoryTab(slug, elem) {
    document.querySelectorAll('.mega-cat-item').forEach(function(item) {
      item.classList.remove('active-category-tab', 'bg-white', 'text-[#006241]', 'border-[#006241]', 'font-bold', 'shadow-2xs');
      item.classList.add('border-transparent');
      const icon = item.querySelector('.material-symbols-outlined');
      if (icon) {
        icon.classList.remove('text-[#006241]');
        icon.classList.add('text-emerald-700');
      }
    });

    if (elem) {
      elem.classList.add('active-category-tab', 'bg-white', 'text-[#006241]', 'border-[#006241]', 'font-bold', 'shadow-2xs');
      elem.classList.remove('border-transparent');
      const activeIcon = elem.querySelector('.material-symbols-outlined');
      if (activeIcon) {
        activeIcon.classList.add('text-[#006241]');
        activeIcon.classList.remove('text-emerald-700');
      }
    }

    document.querySelectorAll('.mega-pane').forEach(function(pane) {
      pane.classList.add('hidden');
      pane.classList.remove('block');
    });

    const activePane = document.getElementById('pane-' + slug);
    if (activePane) {
      activePane.classList.remove('hidden');
      activePane.classList.add('block');
    }
  }

  function toggleMegaMenuDropdown() {
    const dropdown = document.getElementById('mega-menu-dropdown');
    const arrow = document.getElementById('mega-arrow-icon');
    if (dropdown) {
      if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
      } else {
        dropdown.classList.add('hidden');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
      }
    }
  }

  document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('mega-menu-wrapper');
    const dropdown = document.getElementById('mega-menu-dropdown');
    const arrow = document.getElementById('mega-arrow-icon');
    if (wrapper && !wrapper.contains(e.target) && dropdown && !dropdown.classList.contains('hidden')) {
      dropdown.classList.add('hidden');
      if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
  });
</script>
