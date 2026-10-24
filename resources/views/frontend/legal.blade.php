@extends('frontend.layouts.master')

@section('title', $pageTitle . ' | ' . ($siteName ?? 'CAMERA BD'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-6 lg:py-10">

  <!-- Breadcrumb Navigation -->
  <nav class="flex items-center gap-2 text-xs text-text-muted mb-6">
    <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-[16px]">home</span>
      <span>Home</span>
    </a>
    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
    <span class="text-text-display font-bold">{{ $pageTitle }}</span>
  </nav>

  <!-- Header Banner -->
  <div class="bg-surface-card rounded-2xl p-6 lg:p-8 border border-surface-border shadow-xs mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold uppercase tracking-wider mb-2">
          <span class="material-symbols-outlined text-[16px]">gavel</span>
          <span>Official Store Guidelines</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-text-display tracking-tight">{{ $pageTitle }}</h1>
        <p class="text-sm text-text-muted mt-1">{{ $metaDescription ?? 'Legal transparency, authentic warranties, and trust policies of CAMERA BD.' }}</p>
      </div>
      <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-4 py-2.5 bg-surface-container hover:bg-surface-container-high text-text-display rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 border border-surface-border">
          <span class="material-symbols-outlined text-[16px]">print</span>
          <span>Print Document</span>
        </button>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Sticky Sidebar Menu -->
    <aside class="lg:col-span-4 space-y-6">
      <div class="bg-surface-card rounded-2xl p-5 border border-surface-border shadow-xs sticky top-28">
        <h3 class="text-sm font-bold text-text-display uppercase tracking-wider mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[18px]">policy</span>
          <span>Policy Documents</span>
        </h3>
        
        <nav class="space-y-1.5">
          <!-- Privacy Policy -->
          <a href="{{ route('frontend.privacy-policy') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $pageType === 'privacy' ? 'bg-primary text-white shadow-xs' : 'text-text-muted hover:bg-surface-container hover:text-text-display' }}">
            <div class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-[18px]">shield_person</span>
              <span>Privacy Policy</span>
            </div>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          </a>

          <!-- Terms of Service -->
          <a href="{{ route('frontend.terms-of-service') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $pageType === 'terms' ? 'bg-primary text-white shadow-xs' : 'text-text-muted hover:bg-surface-container hover:text-text-display' }}">
            <div class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-[18px]">description</span>
              <span>Terms of Service</span>
            </div>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          </a>

          <!-- Warranty Terms -->
          <a href="{{ route('frontend.warranty-terms') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $pageType === 'warranty' ? 'bg-primary text-white shadow-xs' : 'text-text-muted hover:bg-surface-container hover:text-text-display' }}">
            <div class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-[18px]">verified_user</span>
              <span>Warranty Terms</span>
            </div>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          </a>

          <!-- Site Map -->
          <a href="{{ route('frontend.sitemap') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $pageType === 'sitemap' ? 'bg-primary text-white shadow-xs' : 'text-text-muted hover:bg-surface-container hover:text-text-display' }}">
            <div class="flex items-center gap-2.5">
              <span class="material-symbols-outlined text-[18px]">alt_route</span>
              <span>Site Map & Catalog</span>
            </div>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          </a>
        </nav>

        <hr class="my-5 border-surface-border"/>

        <!-- Support & Hotline Box -->
        <div class="space-y-3">
          <span class="text-xs font-bold text-text-display uppercase tracking-wider block">Direct Creator Assistance</span>
          <div class="bg-surface-container p-3.5 rounded-xl border border-surface-border text-xs space-y-2">
            <div class="flex items-center gap-2 text-text-display">
              <span class="material-symbols-outlined text-primary text-[18px]">call</span>
              <strong>{{ $sitePhoneFormatted ?? '+880-1352-146-344' }}</strong>
            </div>
            <div class="flex items-center gap-2 text-text-muted">
              <span class="material-symbols-outlined text-primary text-[18px]">mail</span>
              <span>{{ $siteEmail ?? 'camerabd.shop@gmail.com' }}</span>
            </div>
            <div class="flex items-center gap-2 text-text-muted">
              <span class="material-symbols-outlined text-primary text-[18px]">schedule</span>
              <span>Sat – Thu: 10:00 AM – 8:30 PM</span>
            </div>
          </div>
          <a href="https://wa.me/{{ $whatsappDigits ?? '8801917256756' }}" target="_blank" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors shadow-xs">
            <span class="material-symbols-outlined text-[16px]">chat</span>
            <span>Instant WhatsApp Support</span>
          </a>
        </div>

        <div class="mt-4 pt-3 border-t border-surface-border text-[11px] text-text-muted text-center leading-relaxed">
          {{ get_setting('com_trade_license') ?: 'Govt. Trade License No: TRAD/DNCC/023910/2021 | BIN: 001239845-0101' }}
        </div>
      </div>
    </aside>

    <!-- Right Main Document Content Area -->
    <main class="lg:col-span-8">
      <div class="bg-surface-card rounded-2xl p-6 lg:p-10 border border-surface-border shadow-xs prose prose-slate max-w-none text-on-surface">

        @if(!empty($content))
          <!-- Dynamic Content configured in Admin Settings -->
          <div class="policy-dynamic-content space-y-4 text-sm leading-relaxed">
            {!! str_contains($content, '<') ? $content : nl2br(e($content)) !!}
          </div>
        @else
          <!-- Professional Default Content based on Page Type -->
          
          @if($pageType === 'privacy')
            <div class="space-y-6 text-sm leading-relaxed">
              <div class="p-4 bg-surface-container rounded-xl border border-surface-border flex items-start gap-3">
                <span class="material-symbols-outlined text-primary text-2xl shrink-0 mt-0.5">lock</span>
                <div>
                  <h4 class="font-bold text-text-display text-sm mb-1">Your Privacy is Protected</h4>
                  <p class="text-xs text-text-muted">At {{ $siteName ?? 'CAMERA BD' }}, we respect your privacy and are committed to protecting personal and payment details collected across our website, showrooms, and service centers.</p>
                </div>
              </div>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</span>
                  Information We Collect
                </h3>
                <p class="text-text-muted">When you place an order, pre-order gear, register for warranty, or contact customer support, we collect necessary contact information including your full name, mobile number, delivery address, email, and equipment serial numbers.</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">2</span>
                  How Your Data is Used
                </h3>
                <ul class="list-disc list-inside space-y-1 text-text-muted pl-2">
                  <li>Processing orders, nationwide courier shipments (Pathao, Steadfast, RedX), and in-store pick-up verifications.</li>
                  <li>Issuing official brand warranty certificates, sensor cleaning logs, and serial registration.</li>
                  <li>Notifying you regarding dispatch status, order tracking numbers, and critical firmware upgrades.</li>
                  <li>Preventing fraud and securing transactions via SSLCommerz 256-bit encrypted gateway.</li>
                </ul>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">3</span>
                  Payment Security & Confidentiality
                </h3>
                <p class="text-text-muted">We never store your credit/debit card numbers, CVV, or mobile banking PINs. All financial transactions are processed directly through Bangladesh Bank licensed gateways (SSLCommerz, bKash, Nagad, City Bank EMI).</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">4</span>
                  Third-Party Disclosure
                </h3>
                <p class="text-text-muted">We do not sell, trade, or rent your personal identification information. Information is shared strictly with authorized courier partners and manufacturer service labs solely for the purpose of fulfilling your order or warranty claim.</p>
              </section>
            </div>

          @elseif($pageType === 'terms')
            <div class="space-y-6 text-sm leading-relaxed">
              <div class="p-4 bg-surface-container rounded-xl border border-surface-border flex items-start gap-3">
                <span class="material-symbols-outlined text-primary text-2xl shrink-0 mt-0.5">verified</span>
                <div>
                  <h4 class="font-bold text-text-display text-sm mb-1">Official Terms of Purchase</h4>
                  <p class="text-xs text-text-muted">By purchasing equipment from {{ $siteName ?? 'CAMERA BD' }} online or at our flagship showrooms, you agree to the following terms and commercial policies.</p>
                </div>
              </div>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</span>
                  100% Genuine & Authentic Gear Guarantee
                </h3>
                <p class="text-text-muted">Every cinema camera, lens, wireless transmission system, gimbal, and studio light sold by {{ $siteName ?? 'CAMERA BD' }} is guaranteed 100% authentic, brand-new, and sourced through official distribution channels with verified factory serial numbers.</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">2</span>
                  Pricing, Taxes & Invoices
                </h3>
                <p class="text-text-muted">All prices are quoted in Bangladeshi Taka (BDT). Prices include applicable government taxes. In the event of an erroneous price display due to typographical or technical glitch, {{ $siteName ?? 'CAMERA BD' }} reserves the right to contact the buyer before order confirmation.</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">3</span>
                  Cash on Delivery & Courier Inspection
                </h3>
                <p class="text-text-muted">For courier deliveries across Bangladesh, customers are advised to inspect the outer security seal and box condition before signing. In case of damaged or tampered parcel, refuse delivery and immediately notify our hotline (+880-1352-146-344).</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">4</span>
                  0% Bank EMI Facility
                </h3>
                <p class="text-text-muted">Credit card EMI facilities (3 to 36 months) are available through 36 partner banks via SSLCommerz and in-store POS terminals. EMI approval and applicable bank processing fees are governed by the respective issuing bank's cardholder agreement.</p>
              </section>
            </div>

          @elseif($pageType === 'warranty')
            <div class="space-y-6 text-sm leading-relaxed">
              <div class="p-4 bg-broadcast-teal-subtle rounded-xl border border-broadcast-teal/20 flex items-start gap-3">
                <span class="material-symbols-outlined text-broadcast-teal text-2xl shrink-0 mt-0.5">verified_user</span>
                <div>
                  <h4 class="font-bold text-text-display text-sm mb-1">Official Brand Warranty & Lab Care</h4>
                  <p class="text-xs text-text-muted">We provide full official warranty support, genuine factory parts, and certified technician service for all camera bodies, cine optics, and broadcast wireless equipment.</p>
                </div>
              </div>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</span>
                  Warranty Coverage Scope
                </h3>
                <ul class="list-disc list-inside space-y-1 text-text-muted pl-2">
                  <li><strong>Camera Bodies:</strong> 1 to 3 Years Official Service Warranty covering sensor electronics, motherboard, EVF, and shutter mechanism.</li>
                  <li><strong>Lenses & Optics:</strong> 1 Year Official Warranty covering aperture motors, autofocus drive, and optical alignment.</li>
                  <li><strong>Wireless Audio & Video (Hollyland/DJI):</strong> 1 Year Replacement / Service Warranty as per manufacturer terms.</li>
                  <li><strong>Gimbals & Stabilizers:</strong> 1 Year Motor & IMU stabilization electronics warranty.</li>
                </ul>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">2</span>
                  Sensor Lab & Service Facilities
                </h3>
                <p class="text-text-muted">Our Dhaka service facility is equipped with ISO-certified clean benches for sensor cleaning, firmware flashing, and laser optical calibration. Customers purchasing from {{ $siteName ?? 'CAMERA BD' }} receive complimentary sensor health audits.</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">3</span>
                  Warranty Exclusions (Void Conditions)
                </h3>
                <p class="text-text-muted">Warranty does not cover: physical accidental drops, liquid/water ingress, unauthorized modification/third-party opening, broken LCD screens, fungus due to improper moisture storage, or damage caused by faulty third-party battery chargers.</p>
              </section>

              <section class="space-y-3">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">4</span>
                  How to Claim Warranty
                </h3>
                <p class="text-text-muted">To claim warranty, simply bring your gear along with the original purchase invoice to any of our physical flagship showrooms or ship it securely to our Dhaka Central Service Hub via courier with your invoice copy.</p>
              </section>
            </div>

          @elseif($pageType === 'sitemap')
            <div class="space-y-8 text-sm leading-relaxed">
              <div class="p-4 bg-surface-container rounded-xl border border-surface-border">
                <h4 class="font-bold text-text-display text-sm mb-1">Catalog Index & Directory</h4>
                <p class="text-xs text-text-muted">Browse through all categories, equipment lines, brands, and service centers available at {{ $siteName ?? 'CAMERA BD' }}.</p>
              </div>

              <!-- Categories & Subcategories Directory -->
              <section class="space-y-4">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary">category</span>
                  <span>Shop by Department & Category</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  @forelse($categories ?? [] as $cat)
                    <div class="p-4 bg-surface-base rounded-xl border border-surface-border">
                      <a href="{{ route('frontend.category', $cat->slug) }}" class="font-bold text-text-display hover:text-primary transition-colors text-sm flex items-center justify-between mb-2">
                        <span>{{ $cat->name }}</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                      </a>
                      @if(!empty($cat->subcategories) && count($cat->subcategories) > 0)
                        <ul class="space-y-1.5 pl-3 border-l-2 border-primary/20 text-xs text-text-muted">
                          @foreach($cat->subcategories as $sub)
                            <li>
                              <a href="{{ route('frontend.subcategory', ['category_slug' => $cat->slug, 'subcategory_slug' => $sub->slug]) }}" class="hover:text-primary transition-colors">
                                {{ $sub->name }}
                              </a>
                            </li>
                          @endforeach
                        </ul>
                      @endif
                    </div>
                  @empty
                    <p class="text-xs text-text-muted">No categories indexed.</p>
                  @endforelse
                </div>
              </section>

              <!-- Brands Index -->
              <section class="space-y-4">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary">verified</span>
                  <span>Official Authorized Brands</span>
                </h3>
                <div class="flex flex-wrap gap-2">
                  @forelse($brands ?? [] as $brand)
                    <a href="{{ route('frontend.search') }}?q={{ urlencode($brand->name) }}" class="px-3 py-1.5 bg-surface-base hover:bg-primary hover:text-white rounded-lg border border-surface-border text-xs font-bold transition-colors">
                      {{ $brand->name }}
                    </a>
                  @empty
                    <p class="text-xs text-text-muted">No brands indexed.</p>
                  @endforelse
                </div>
              </section>

              <!-- Store Outlets -->
              <section class="space-y-4">
                <h3 class="text-base font-bold text-text-display flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary">storefront</span>
                  <span>Physical Store Locations</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  @forelse($branches ?? [] as $br)
                    <div class="p-4 bg-surface-base rounded-xl border border-surface-border text-xs space-y-1">
                      <strong class="text-text-display text-sm block">{{ $br->name }}</strong>
                      <p class="text-text-muted">{{ $br->address }}</p>
                      @if($br->phone)<p class="text-primary font-bold">{{ $br->phone }}</p>@endif
                      @if($br->schedule)<p class="text-text-muted">{{ $br->schedule }}</p>@endif
                    </div>
                  @empty
                    <div class="p-4 bg-surface-base rounded-xl border border-surface-border text-xs space-y-1">
                      <strong class="text-text-display text-sm block">Jamuna Future Park Outlet</strong>
                      <p class="text-text-muted">{{ $siteAddress ?? 'Jamuna Future park , Level-4 , Block-c , Shop No 012B1' }}</p>
                      <p class="text-primary font-bold">{{ $sitePhoneFormatted ?? '+880-1352-146-344' }}</p>
                    </div>
                  @endforelse
                </div>
              </section>
            </div>
          @endif

        @endif

      </div>
    </main>

  </div>

</div>
@endsection
