@if ($paginator->hasPages())
  <div class="bg-surface-card rounded-2xl p-4 border border-surface-border shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold mt-6">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <span class="px-4 py-2 rounded-xl bg-surface-container text-text-muted/60 cursor-not-allowed select-none flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
        <span>Previous</span>
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-primary hover:text-white transition-colors flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
        <span>Previous</span>
      </a>
    @endif

    {{-- Numeric Page Links --}}
    <div class="flex items-center gap-1.5 flex-wrap justify-center">
      @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
          <span class="w-8 h-8 flex items-center justify-center text-text-muted font-bold">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center font-extrabold shadow-sm">
                {{ $page }}
              </span>
            @else
              <a href="{{ $url }}" class="w-8 h-8 rounded-xl bg-surface-container text-on-surface hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
                {{ $page }}
              </a>
            @endif
          @endforeach
        @endif
      @endforeach
    </div>

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-primary hover:text-white transition-colors flex items-center gap-1">
        <span>Next</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      </a>
    @else
      <span class="px-4 py-2 rounded-xl bg-surface-container text-text-muted/60 cursor-not-allowed select-none flex items-center gap-1">
        <span>Next</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      </span>
    @endif
  </div>
@endif
