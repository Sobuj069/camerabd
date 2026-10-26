@php
  $gtmHeaderScript = function_exists('get_setting') ? get_setting('gtm_header_script') : null;
  $gtmId = function_exists('get_setting') && get_setting('gtm_id') ? get_setting('gtm_id') : env('GTM_ID');
  $ga4Id = function_exists('get_setting') && get_setting('ga4_measurement_id') ? get_setting('ga4_measurement_id') : env('GA4_MEASUREMENT_ID');
@endphp

<!-- Meta Pixel & Network Guard: Blocks Premature Purchase and Rogue Button Clicks before Admin Approval -->
<script>
(function() {
  window._isOrderApproved = {{ (!empty($isApproved) && $isApproved) ? 'true' : 'false' }};

  function wrapFbq(fbqInstance) {
    if (!fbqInstance || fbqInstance._guarded) return fbqInstance;
    const origFbq = fbqInstance;
    const guardedFbq = function() {
      const args = Array.prototype.slice.call(arguments);
      const act = String(args[0] || '').toLowerCase();
      const evName = String(args[1] || '').toLowerCase();

      // Block premature Purchase event before approval
      if ((act === 'track' || act === 'trackcustom') && evName === 'purchase') {
        if (!window._isOrderApproved) {
          console.warn('[Meta Guard] Blocked premature browser Purchase event before order approval.');
          return;
        }
      }

      // Block rogue automatic button clicks / subscribedbutton events before approval
      if (evName === 'subscribedbuttonclick' && !window._isOrderApproved) {
        console.warn('[Meta Guard] Blocked automatic button click event before order approval.');
        return;
      }

      return origFbq.apply(window, args);
    };

    for (let k in origFbq) {
      if (Object.prototype.hasOwnProperty.call(origFbq, k)) guardedFbq[k] = origFbq[k];
    }
    guardedFbq._guarded = true;
    return guardedFbq;
  }

  // Pre-seed window.fbq with the guard before any script or GTM can load it
  let _fbq = wrapFbq(function() {
    (_fbq.q = _fbq.q || []).push(arguments);
  });
  _fbq.q = [];
  _fbq.loaded = false;
  _fbq.version = '2.0';
  window.fbq = _fbq;

  try {
    Object.defineProperty(window, 'fbq', {
      configurable: true,
      enumerable: true,
      get: function() { return _fbq; },
      set: function(val) { _fbq = wrapFbq(val); }
    });
  } catch(e) {}

  // Intercept Network Beacons / Fetch / Images targeting facebook.com/tr
  function isBlockedUrl(url, body) {
    if (typeof url !== 'string' || url.indexOf('facebook.com/tr') === -1) return false;
    if (!window._isOrderApproved) {
      const bodyStr = typeof body === 'string' ? body : '';
      if (url.indexOf('ev=Purchase') !== -1 || url.indexOf('ev=purchase') !== -1 ||
          bodyStr.indexOf('"Purchase"') !== -1 || bodyStr.indexOf('ev=Purchase') !== -1 ||
          url.indexOf('ev=SubscribedButtonClick') !== -1 || bodyStr.indexOf('ev=SubscribedButtonClick') !== -1) {
        console.warn('[Meta Guard] Dropped premature network beacon before order approval.');
        return true;
      }
    }
    return false;
  }

  if (navigator.sendBeacon) {
    const rawSendBeacon = navigator.sendBeacon.bind(navigator);
    navigator.sendBeacon = function(url, data) {
      if (isBlockedUrl(url, data)) return true;
      return rawSendBeacon(url, data);
    };
  }

  const imgDesc = Object.getOwnPropertyDescriptor(HTMLImageElement.prototype, 'src');
  if (imgDesc && imgDesc.set) {
    Object.defineProperty(HTMLImageElement.prototype, 'src', {
      configurable: true,
      enumerable: true,
      get: imgDesc.get,
      set: function(srcVal) {
        if (isBlockedUrl(srcVal)) return;
        return imgDesc.set.call(this, srcVal);
      }
    });
  }

  if (window.fetch) {
    const rawFetch = window.fetch;
    window.fetch = function(resource, init) {
      const url = typeof resource === 'string' ? resource : (resource ? resource.url : '');
      const body = init && init.body ? init.body : null;
      if (isBlockedUrl(url, body)) {
        return Promise.resolve(new Response(null, { status: 200 }));
      }
      return rawFetch.apply(this, arguments);
    };
  }
})();
</script>

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

  // Cross-tab check for approved order dataLayer dispatch
  (function() {
    try {
      // Skip on order-confirmation page to prevent duplicate events
      if (window.location.pathname.indexOf('order-confirmation') !== -1) {
        return;
      }
      var stored = localStorage.getItem('cb_approved_order_datalayer');
      if (stored) {
        var p = JSON.parse(stored);
        if (p && !p._gtm_dispatched && (!p._pushed_at || (Date.now() - p._pushed_at < 15 * 60 * 1000))) {
          // Immediately mark and clear to prevent duplicate firing on page reloads
          localStorage.removeItem('cb_approved_order_datalayer');
          var orderKey = String(p.order_id || p.transaction_id || 'default');
          var storageKey = 'cb_dispatched_order_' + orderKey;
          if (localStorage.getItem(storageKey)) {
            return;
          }
          try {
            localStorage.setItem(storageKey, Date.now());
          } catch(e) {}

          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({ ecommerce: null });
          var ev = Object.assign({}, p, {
            event: 'purchase',
            status: 'approved',
            order_status: 'Approved'
          });
          window.dataLayer.push(ev);
          console.log('%c🚀 [DataLayer] Frontend GTM Dispatched single "purchase" for Approved Order #' + (p.order_id || p.transaction_id), 'background: #047857; color: #fff; font-weight: bold; padding: 4px 8px; border-radius: 4px;', ev);
        }
      }
    } catch(e) {}
  })();

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

@if(!empty($gtmHeaderScript))
<!-- Dynamic Google Tag Manager Header Script -->
{!! $gtmHeaderScript !!}
<!-- End Dynamic Google Tag Manager Header Script -->
@elseif(!empty($gtmId))
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
