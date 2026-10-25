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

  /**
   * Helper function to dispatch order_confirm event with all aliases
   */
  window.dispatchOrderConfirmDataLayer = function(payload) {
    if (!payload) return;
    try {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ ecommerce: null });
      // Standard snake_case event
      window.dataLayer.push(Object.assign({}, payload, { event: 'order_confirm' }));
      // Exact user requested alias
      window.dataLayer.push(Object.assign({}, payload, { event: 'order confrom' }));
      window.dataLayer.push(Object.assign({}, payload, { event: 'order_confirmed' }));
      if (typeof window.pushToDataLayer === 'function') {
        window.pushToDataLayer('order_confirm', payload.ecommerce, payload);
      }
      console.log('✅ [DataLayer] order_confirm event successfully dispatched for Order #' + (payload.order_id || payload.transaction_id));
    } catch(err) {
      console.warn('[DataLayer] Error dispatching order_confirm:', err);
    }
  };
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
