<!-- ========================================== -->
<!-- LANDING PAGE EXACT FOOTER (UNIVERSAL) -->
<!-- ========================================== -->
<footer class="w-full bg-white text-slate-800 pt-14 pb-8 border-t border-slate-200 mt-12">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    
    <!-- 4 Columns Link Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-10 mb-12">
      <!-- Col 1 & 2: About (Col Span 2) -->
      <div class="md:col-span-2 space-y-4">
        <div class="flex items-center gap-3">
          <img alt="{{ $siteName ?? 'CAMERA BD' }}" class="h-8 w-auto object-contain max-w-[140px]" src="{{ $siteLogo ?? '' }}"/>
          <span class="text-base font-extrabold text-[#090D16] uppercase tracking-wider">{{ $siteName ?? 'CAMERA BD' }}</span>
        </div>
        <p class="text-[13px] leading-[22px] text-slate-500 pr-0 md:pr-6">
          {{ $siteName ?? 'CAMERA BD' }} is Bangladesh's premier importer and authorized distribution house for optical, cinematography, and television broadcast solutions. Catering to national broadcasters, independent filmmakers, and creative pros.
        </p>
        <div class="flex items-center gap-3 pt-2 flex-wrap">
          <div class="px-3 py-1.5 bg-[#f8fafc] rounded-lg border border-slate-200 inline-flex items-center gap-1.5">
            <span class="text-[11px] text-slate-500 font-medium">Heritage:</span>
            <strong class="text-[11px] font-extrabold text-[#090D16] uppercase tracking-wide">AUTHENTIC GEAR</strong>
          </div>
          <div class="px-3 py-1.5 bg-[#f0fdfa] text-[#0d9488] rounded-lg text-[11px] font-bold border border-[#ccfbf1] inline-flex items-center">
            100% Authentic Optical Glass
          </div>
        </div>
      </div>

      <!-- Col 3: Flagship Store -->
      <div>
        <h5 class="text-[15px] font-bold text-[#090D16] mb-4">Flagship Store</h5>
        <ul class="space-y-3 text-[13px] text-slate-500">
          <li class="space-y-1">
            <strong class="text-[#090D16] block font-bold text-[13px]">Jamuna Future Park Outlet</strong>
            <span class="text-[12px] text-slate-500 leading-relaxed block">{{ $siteAddress ?? 'Jamuna Future park , Level-4 , Block-c , Shop No 012B1' }}</span>
            <span class="block text-[#006a61] font-bold text-[13px] tracking-wide">{{ $sitePhoneFormatted ?? '+880-1352-146-344' }}</span>
          </li>
        </ul>
      </div>

      <!-- Col 4: Top Categories -->
      <div>
        <h5 class="text-[15px] font-bold text-[#090D16] mb-4">Top Categories</h5>
        <ul class="space-y-2.5 text-[13px] text-slate-500">
          @if(isset($topCategories) && $topCategories->isNotEmpty())
            @foreach($topCategories->take(6) as $topCat)
              <li>
                <a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => \Illuminate\Support\Str::slug($topCat->name)]) }}">
                  {{ ucwords(strtolower($topCat->name)) }}
                </a>
              </li>
            @endforeach
          @elseif(isset($navCategories) && !empty($navCategories))
            @foreach(array_slice($navCategories, 0, 6) as $navCat)
              <li>
                <a class="hover:text-[#b70011] transition-colors" href="{{ $navCat['view_all_url'] ?? '#' }}">
                  {{ $navCat['name'] ?? '' }}
                </a>
              </li>
            @endforeach
          @else
            <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => 'drone']) }}">Drone & Aerial Systems</a></li>
            <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => 'action-camera']) }}">Action & 360 Cameras</a></li>
            <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => 'digital-camera']) }}">Digital Cinema Cameras</a></li>
            <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => 'mobile-lens']) }}">Mobile Lenses & Filters</a></li>
            <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => 'microphone']) }}">Wireless Microphones</a></li>
            <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.category', ['slug' => 'trypod']) }}">Tripods & Stabilizers</a></li>
          @endif
        </ul>
      </div>

      <!-- Col 5: Customer Care -->
      <div>
        <h5 class="text-[15px] font-bold text-[#090D16] mb-4">Customer Care</h5>
        <ul class="space-y-2.5 text-[13px] text-slate-500">
          <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.warranty-terms') }}">Official Warranty Claim</a></li>
          <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.terms-of-service') }}#emi">EMI Facilities (36 Banks)</a></li>
          <li><a class="hover:text-[#b70011] transition-colors" href="{{ url('/') }}#orders">Track Live Consignment</a></li>
          <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.warranty-terms') }}">Camera Sensor Service Lab</a></li>
          <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.terms-of-service') }}">Studio Consultation</a></li>
          <li><a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.terms-of-service') }}">Exchange & Upgrade Policy</a></li>
        </ul>
      </div>
    </div>

    <!-- Payment Gateways Row (Light Container) -->
    <div class="bg-[#eff4ff] rounded-2xl p-4 lg:p-5 flex flex-col md:flex-row items-center justify-between gap-4 border border-[#dce9ff]">
      <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-[12px] font-semibold text-slate-600">
        <span class="font-bold text-[#090D16]">Accepted Payment Gateways:</span>
        <div class="flex flex-wrap items-center justify-center gap-2">
          <!-- bKash -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="bKash">
            <img src="{{ asset('frontend/images/payments/bkash.svg') }}" alt="bKash" class="h-4.5 max-h-5 w-auto object-contain"/>
          </div>
          <!-- Nagad -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="Nagad">
            <img src="{{ asset('frontend/images/payments/nagad.svg') }}" alt="Nagad" class="h-4.5 max-h-5 w-auto object-contain"/>
          </div>
          <!-- SSLCommerz -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="SSLCommerz">
            <img src="{{ asset('frontend/images/payments/sslcommerz.png') }}" alt="SSLCommerz" class="h-4 max-h-4.5 w-auto object-contain"/>
          </div>
          <!-- Visa -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="VISA">
            <img src="{{ asset('frontend/images/payments/visa.svg') }}" alt="VISA" class="h-3.5 max-h-4 w-auto object-contain"/>
          </div>
          <!-- MasterCard -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="MasterCard">
            <img src="{{ asset('frontend/images/payments/mastercard.svg') }}" alt="MasterCard" class="h-4.5 max-h-5 w-auto object-contain"/>
          </div>
          <!-- Amex -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="American Express">
            <img src="{{ asset('frontend/images/payments/amex.svg') }}" alt="American Express" class="h-4 max-h-4.5 w-auto object-contain"/>
          </div>
          <!-- City Bank EMI -->
          <div class="h-8 px-2.5 bg-white rounded-lg flex items-center justify-center gap-1.5 shadow-xs border border-slate-200 hover:border-slate-400 transition-colors" title="City Bank EMI">
            <img src="{{ asset('frontend/images/payments/citybank.svg') }}" alt="City Bank" class="h-4.5 max-h-5 w-auto object-contain"/>
            <span class="text-[10px] font-bold text-[#090D16] uppercase tracking-wider bg-slate-100 px-1 py-0.2 rounded border border-slate-200">EMI</span>
          </div>
        </div>
      </div>
      <div class="text-[13px] text-slate-500 text-center md:text-right font-medium">
        &copy; {{ date('Y') }} {{ $siteName ?? 'CAMERA BD' }}. All rights reserved.
      </div>
    </div>

    <!-- Bottom Legal Links -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[12px] text-slate-500 text-center sm:text-left">
      <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
        <a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.privacy-policy') }}">Privacy Policy</a>
        <a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.terms-of-service') }}">Terms of Service</a>
        <a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.warranty-terms') }}">Warranty Terms</a>
        <a class="hover:text-[#b70011] transition-colors" href="{{ route('frontend.sitemap') }}">Site Map</a>
      </div>
      <span class="text-[11px] text-slate-400">{{ function_exists('get_setting') && get_setting('com_trade_license') ? get_setting('com_trade_license') : 'Govt. Trade License No: TRAD/DNCC/023910/2021 | BIN: 001239845-0101' }}</span>
    </div>
  </div>
</footer>

<!-- ========================================== -->
<!-- FLOATING WHATSAPP BUTTON (Global on all pages) -->
<!-- ========================================== -->
<div aria-label="WhatsApp Support" class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-50 flex items-center group pointer-events-auto">
  <!-- Interactive Hover Pill on Desktop -->
  <a href="https://wa.me/{{ $whatsappDigits ?? '8801352146344' }}?text={{ urlencode('Hello! I would like to inquire about products from ' . ($siteName ?? 'CAMERA BD')) }}" 
     target="_blank" 
     rel="noopener noreferrer"
     class="hidden sm:flex items-center gap-2 mr-3 px-3.5 py-2 bg-slate-900/90 hover:bg-slate-900 text-white rounded-full shadow-lg backdrop-blur-sm text-xs font-semibold tracking-wide transition-all transform duration-300 opacity-90 group-hover:opacity-100 group-hover:scale-105 border border-slate-700/50">
    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
    <span>Need Help? <strong class="text-emerald-400 font-bold">Chat on WhatsApp</strong></span>
  </a>

  <!-- Floating Circular Action Button -->
  <a href="https://wa.me/{{ $whatsappDigits ?? '8801352146344' }}?text={{ urlencode('Hello! I would like to inquire about products from ' . ($siteName ?? 'CAMERA BD')) }}" 
     target="_blank" 
     rel="noopener noreferrer"
     aria-label="Chat with us on WhatsApp"
     class="relative flex items-center justify-center w-13 h-13 sm:w-14 sm:h-14 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-full shadow-[0_8px_25px_rgba(37,211,102,0.45)] hover:shadow-[0_12px_32px_rgba(37,211,102,0.65)] transform hover:scale-110 active:scale-95 transition-all duration-300">
    
    <!-- Subtle Pulse Ping Glow -->
    <span class="absolute -inset-1 rounded-full bg-[#25D366] opacity-35 animate-ping -z-10"></span>

    <!-- Official WhatsApp SVG Icon -->
    <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.978-.275-.1-.476-.15-.676.15-.2.301-.776.978-.952 1.179-.175.2-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.894-.798-1.498-1.784-1.674-2.085-.175-.301-.019-.464.132-.614.136-.135.301-.351.452-.527.15-.175.2-.301.3-.501.1-.2.05-.376-.025-.526-.075-.15-.676-1.63-1.002-2.233-.25-.6-.5-.526-.676-.526-.175 0-.376-.025-.576-.025-.2 0-.526.075-.802.376-.276.301-1.053 1.028-1.053 2.508 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.141 4.544.718.31 1.279.496 1.716.635.721.23 1.377.197 1.896.12.578-.087 1.78-.727 2.03-1.43.25-.702.25-1.303.175-1.43-.075-.125-.276-.2-.576-.35zM12.04 21.78c-1.74 0-3.41-.46-4.88-1.33l-.35-.21-3.63.95.97-3.54-.23-.37a9.78 9.78 0 0 1-1.5-5.24c0-5.43 4.42-9.85 9.85-9.85 2.63 0 5.1 1.02 6.96 2.88a9.8 9.8 0 0 1 2.88 6.97c0 5.43-4.42 9.84-9.85 9.84zm0-17.84a7.99 7.99 0 0 0-8 8c0 1.58.46 3.1 1.33 4.41l.21.32-.57 2.08 2.13-.56.31.18c1.28.76 2.76 1.17 4.29 1.17 4.41 0 8-3.59 8-8s-3.59-8-8-8z"/>
    </svg>
  </a>
</div>

