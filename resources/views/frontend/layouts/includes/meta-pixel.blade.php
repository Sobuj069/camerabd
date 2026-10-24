<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
@php
  $metaPixelId = function_exists("get_setting") && get_setting("meta_pixel_id") ? get_setting("meta_pixel_id") : "28625456847108675";
  $isConfirmationPage = request()->routeIs('frontend.order-confirmation') ? 'true' : 'false';
@endphp

// ========================================================
// META PIXEL GUARD: Block ob3_plugin & Premature Purchase Events
// ========================================================
(function() {
  const isOrderConfirmation = {{ $isConfirmationPage }} || (window.location.pathname.indexOf('order-confirmation') !== -1);

  function isBlockedEvent(action, eventName, params, options) {
    const act = String(action || '').toLowerCase();
    if (act !== 'track' && act !== 'trackcustom') return false;

    const evName = String(eventName || '').toLowerCase();

    // 1. HARD BLOCK: Browser NEVER fires Purchase event. Purchase is strictly reserved for Admin Approval via Server CAPI.
    if (evName === 'purchase') {
      console.warn('[Meta Guard] Browser Purchase blocked: Purchase fires strictly on Admin Approval via Server CAPI in Software.');
      return true;
    }

    // 2. HARD BLOCK: Any event with ob3_plugin ID (residual Meta Event Setup Tool / partner plugin)
    const optId = options && typeof options === 'object' ? String(options.eventID || options.eventId || '') : '';
    const paramId = params && typeof params === 'object' ? String(params.eventID || params.eventId || '') : '';
    if (optId.indexOf('ob3_plugin') !== -1 || paramId.indexOf('ob3_plugin') !== -1) {
      console.warn('[Meta Guard] Blocked rogue ob3_plugin event:', eventName);
      return true;
    }

    return false;
  }

  // Intercept fbq calls
  const origFbq = window.fbq;
  const guardedFbq = function() {
    const args = Array.prototype.slice.call(arguments);
    if (isBlockedEvent(args[0], args[1], args[2], args[3])) {
      return; // DROP
    }
    if (origFbq.callMethod) {
      return origFbq.callMethod.apply(origFbq, args);
    }
    return origFbq.apply(this, args);
  };

  for (let k in origFbq) {
    if (Object.prototype.hasOwnProperty.call(origFbq, k)) {
      guardedFbq[k] = origFbq[k];
    }
  }

  let internalCallMethod = origFbq.callMethod;
  Object.defineProperty(guardedFbq, 'callMethod', {
    configurable: true,
    enumerable: true,
    get: function() { return internalCallMethod; },
    set: function(realFn) {
      internalCallMethod = function() {
        const cArgs = Array.prototype.slice.call(arguments);
        if (isBlockedEvent(cArgs[0], cArgs[1], cArgs[2], cArgs[3])) {
          return; // DROP
        }
        return realFn.apply(this, cArgs);
      };
    }
  });

  window.fbq = guardedFbq;

  // Intercept network requests (Beacon, Fetch, Image) targeting facebook.com/tr
  function isBlockedUrl(url, body) {
    if (typeof url !== 'string' || url.indexOf('facebook.com/tr') === -1) return false;
    const bodyStr = typeof body === 'string' ? body : '';

    if (url.indexOf('ob3_plugin') !== -1 || bodyStr.indexOf('ob3_plugin') !== -1) {
      console.warn('[Meta Guard] Network request dropped: contains ob3_plugin');
      return true;
    }

    // Drop any browser Purchase network request - Purchase is strictly triggered via backend CAPI on approval
    if (url.indexOf('ev=Purchase') !== -1 || url.indexOf('ev=purchase') !== -1 ||
        bodyStr.indexOf('"Purchase"') !== -1 || bodyStr.indexOf('ev=Purchase') !== -1) {
      console.warn('[Meta Guard] Network request dropped: Browser Purchase is blocked (Admin CAPI only)');
      return true;
    }
    return false;
  }

  if (navigator.sendBeacon) {
    const rawSendBeacon = navigator.sendBeacon.bind(navigator);
    navigator.sendBeacon = function(url, data) {
      if (isBlockedUrl(url, data)) {
        return true; // Pretend it succeeded
      }
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
        if (isBlockedUrl(srcVal)) {
          return; // DROP
        }
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

fbq('set', 'autoConfig', false, '{{ $metaPixelId }}');
fbq('init', '{{ $metaPixelId }}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
