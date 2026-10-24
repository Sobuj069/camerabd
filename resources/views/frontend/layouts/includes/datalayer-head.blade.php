@php
  $gtmId = function_exists('get_setting') && get_setting('gtm_id') ? get_setting('gtm_id') : env('GTM_ID');
  $ga4Id = function_exists('get_setting') && get_setting('ga4_measurement_id') ? get_setting('ga4_measurement_id') : env('GA4_MEASUREMENT_ID');
@endphp

<!-- DataLayer Core Engine & GA4 Ecommerce Architecture -->
<script>
  window.dataLayer = window.dataLayer || [];

  /**
   * Universal DataLayer Event Dispatcher
   * Compliant with Google Analytics 4 (GA4) & Google Tag Manager (GTM) Enhanced Ecommerce Spec
   */
  window.pushToDataLayer = function(eventName, ecommerceData, extraParams) {
    try {
      window.dataLayer = window.dataLayer || [];
      if (ecommerceData) {
        // Clear previous ecommerce object to prevent parameter leakage (per Google recommendation)
        window.dataLayer.push({ ecommerce: null });
        var payload = Object.assign({
          event: eventName,
          ecommerce: ecommerceData
        }, extraParams || {});
        window.dataLayer.push(payload);
      } else {
        var payload = Object.assign({
          event: eventName
        }, extraParams || {});
        window.dataLayer.push(payload);
      }
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('[DataLayer Pushed]', eventName, ecommerceData || extraParams || '');
      }
    } catch(err) {
      console.warn('[DataLayer] Error pushing event:', eventName, err);
    }
  };

  // Base Page View Context Push
  window.dataLayer.push({
    event: 'page_data',
    page_title: document.title,
    page_location: window.location.href,
    page_path: window.location.pathname,
    site_currency: 'BDT',
    user_data: {
      user_id: '{{ auth()->id() ?? "" }}',
      user_type: '{{ auth()->check() ? "registered" : "guest" }}'
    }
  });
</script>

@if(!empty($gtmId))
<!-- Google Tag Manager Container -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
<!-- End Google Tag Manager Container -->
@endif

@if(!empty($ga4Id) && empty($gtmId))
<!-- Google tag (gtag.js) for Direct GA4 measurement -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $ga4Id }}');
</script>
@endif
