@php
  $gtmHeaderScript = function_exists('get_setting') ? get_setting('gtm_header_script') : null;
  $gtmId = function_exists('get_setting') && get_setting('gtm_id') ? get_setting('gtm_id') : env('GTM_ID');
  $approvedOrderDl = session('approved_order_datalayer');
  $approvedOrdersDl = session('approved_orders_datalayer');
@endphp

<!-- DataLayer Core Engine & GTM for Backend Orders Tracking -->
<script>
  window.dataLayer = window.dataLayer || [];

  /**
   * Universal Backend DataLayer Event Dispatcher
   */
  window.pushToDataLayer = function(eventName, ecommerceData, extraParams) {
    try {
      window.dataLayer = window.dataLayer || [];
      if (ecommerceData) {
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
      console.log('[DataLayer Pushed]', eventName, ecommerceData || extraParams || '');
    } catch(err) {
      console.warn('[DataLayer] Error pushing event:', eventName, err);
    }
  };

  window._backendDispatchedOrders = window._backendDispatchedOrders || {};

  /**
   * Helper function to dispatch purchase event with customer data and status on Order Approval (strictly once)
   */
  window.dispatchOrderConfirmDataLayer = function(payload) {
    if (!payload) return;
    var orderKey = String(payload.order_id || payload.transaction_id || 'default');
    var storageKey = 'cb_dispatched_order_' + orderKey;
    if (window._backendDispatchedOrders[orderKey] || localStorage.getItem(storageKey)) {
      console.log('[DataLayer] Order #' + orderKey + ' already dispatched, skipping duplicate.');
      return;
    }
    window._backendDispatchedOrders[orderKey] = true;
    try {
      localStorage.setItem(storageKey, Date.now());
    } catch(e) {}

    try {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ ecommerce: null });

      var fullPayload = Object.assign({}, payload, {
        event: 'purchase',
        status: 'approved',
        order_status: 'Approved'
      });

      // Strictly ONE single event push
      window.dataLayer.push(fullPayload);

      console.log('✅ [DataLayer] Single "purchase" event fired for Approved Order #' + orderKey, fullPayload);
    } catch(err) {
      console.warn('[DataLayer] Error dispatching purchase:', err);
    }
  };

  // Check localStorage for pending order_confirm events across page reloads
  (function() {
    try {
      var storedSingle = localStorage.getItem('cb_approved_order_datalayer');
      if (storedSingle) {
        var parsed = JSON.parse(storedSingle);
        localStorage.removeItem('cb_approved_order_datalayer');
        if (parsed && (!parsed._pushed_at || (Date.now() - parsed._pushed_at < 15 * 60 * 1000))) {
          window.approvedOrderDataLayer = parsed;
          if (typeof window.dispatchOrderConfirmDataLayer === 'function') {
            window.dispatchOrderConfirmDataLayer(parsed);
          }
        }
      }
      var storedMulti = localStorage.getItem('cb_approved_orders_datalayer');
      if (storedMulti) {
        var parsedMulti = JSON.parse(storedMulti);
        localStorage.removeItem('cb_approved_orders_datalayer');
        if (Array.isArray(parsedMulti) && typeof window.dispatchOrderConfirmDataLayer === 'function') {
          parsedMulti.forEach(function(o) {
            window.dispatchOrderConfirmDataLayer(o);
          });
        }
      }
    } catch(e) {}
  })();
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

{{-- Flash Session DataLayer Event on Approved Order --}}
@if(!empty($approvedOrderDl))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var singleOrderPayload = {!! json_encode($approvedOrderDl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
    if (singleOrderPayload && typeof window.dispatchOrderConfirmDataLayer === 'function') {
      window.dispatchOrderConfirmDataLayer(singleOrderPayload);
    }
  });
</script>
@endif

@if(!empty($approvedOrdersDl) && is_array($approvedOrdersDl))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var multipleOrdersPayload = {!! json_encode($approvedOrdersDl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
    if (Array.isArray(multipleOrdersPayload) && typeof window.dispatchOrderConfirmDataLayer === 'function') {
      multipleOrdersPayload.forEach(function(o) {
        window.dispatchOrderConfirmDataLayer(o);
      });
    }
  });
</script>
@endif
