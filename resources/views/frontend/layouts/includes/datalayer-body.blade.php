@php
  $gtmId = function_exists('get_setting') && get_setting('gtm_id') ? get_setting('gtm_id') : env('GTM_ID');
@endphp
@if(!empty($gtmId))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
