<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Models\WebsiteOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    const GRAPH_API_VERSION = 'v19.0';

    /**
     * Get Meta Pixel ID from settings or fallback.
     */
    public static function getPixelId(): string
    {
        if (function_exists('get_setting')) {
            $id = get_setting('meta_pixel_id');
            if (!empty($id)) {
                return trim($id);
            }
        }
        return env('META_PIXEL_ID', '');
    }

    /**
     * Get Conversions API Access Token.
     */
    public static function getAccessToken(): ?string
    {
        if (function_exists('get_setting')) {
            $token = get_setting('meta_capi_access_token');
            if (!empty($token)) {
                return trim($token);
            }
        }
        return env('META_CAPI_ACCESS_TOKEN', null);
    }

    /**
     * Get Test Event Code (Optional, for Events Manager Test Events tab).
     */
    public static function getTestEventCode(): ?string
    {
        if (function_exists('get_setting')) {
            $code = get_setting('meta_capi_test_event_code');
            if (!empty($code)) {
                return trim($code);
            }
        }
        return env('META_CAPI_TEST_EVENT_CODE', null);
    }

    /**
     * Check if CAPI is enabled and has token.
     */
    public static function isEnabled(): bool
    {
        return !empty(self::getAccessToken());
    }

    /**
     * Send an Event to Meta Conversions API.
     */
    public static function sendEvent(
        string $eventName,
        array $userData = [],
        array $customData = [],
        ?string $eventId = null,
        ?string $sourceUrl = null,
        ?Request $request = null
    ): array {
        $accessToken = self::getAccessToken();
        $pixelId = self::getPixelId();

        if (empty($accessToken) || empty($pixelId)) {
            return [
                'success' => false,
                'message' => 'Meta CAPI Access Token or Pixel ID is not configured.',
            ];
        }

        $req = $request ?: request();
        $eventId = $eventId ?: ('capi_' . $eventName . '_' . uniqid() . '_' . time());
        $sourceUrl = $sourceUrl ?: ($req ? $req->fullUrl() : url('/'));

        // Format normalized user data with SHA-256
        $formattedUserData = self::formatUserData($userData, $req);

        $eventPayload = [
            'event_name' => $eventName,
            'event_time' => time(),
            'event_id' => $eventId,
            'event_source_url' => $sourceUrl,
            'action_source' => 'website',
            'user_data' => $formattedUserData,
            'custom_data' => (object) $customData,
        ];

        $body = [
            'data' => [$eventPayload],
        ];

        $testCode = self::getTestEventCode();
        if (!empty($testCode)) {
            $body['test_event_code'] = $testCode;
        }

        $endpoint = "https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/{$pixelId}/events?access_token={$accessToken}";

        try {
            $http = Http::timeout(6)->asJson();

            $caFile = ini_get('curl.cainfo');
            if (empty($caFile) || !file_exists($caFile)) {
                if (file_exists('G:/laragon/etc/ssl/cacert.pem')) {
                    $http = $http->withOptions(['verify' => 'G:/laragon/etc/ssl/cacert.pem']);
                } elseif (file_exists('C:/laragon/etc/ssl/cacert.pem')) {
                    $http = $http->withOptions(['verify' => 'C:/laragon/etc/ssl/cacert.pem']);
                } elseif (class_exists(\Composer\CaBundle\CaBundle::class)) {
                    $http = $http->withOptions(['verify' => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()]);
                } else {
                    $http = $http->withoutVerifying();
                }
            }

            $response = $http->post($endpoint, $body);

            if ($response->successful()) {
                Log::info("Meta CAPI [{$eventName}] Event Dispatched Successfully", [
                    'event_id' => $eventId,
                    'response' => $response->json(),
                ]);
                return [
                    'success' => true,
                    'event_id' => $eventId,
                    'response' => $response->json(),
                ];
            } else {
                Log::warning("Meta CAPI [{$eventName}] Event Error", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'error' => $response->json(),
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Meta CAPI [{$eventName}] Exception: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send Purchase Event to Conversions API.
     */
    public static function trackPurchase(WebsiteOrder $order, ?string $eventId = null, ?Request $request = null): array
    {
        $req = $request ?: request();
        $eventId = $eventId ?: ('purchase_' . $order->order_no);

        $userData = [
            'name' => $order->customer_name,
            'phone' => $order->phone,
            'email' => $order->email,
            'city' => $order->city,
            'country' => 'bd',
        ];

        $contents = [];
        $contentIds = [];
        if ($order->relationLoaded('items') || $order->items) {
            foreach ($order->items as $item) {
                $pId = (string)($item->product_id ?: ($item->product_barcode ?: $item->product_name));
                $contentIds[] = $pId;
                $contents[] = [
                    'id' => $pId,
                    'quantity' => (int) ($item->quantity ?: 1),
                    'item_price' => (float) ($item->unit_price ?: 0),
                ];
            }
        }

        $customData = [
            'currency' => 'BDT',
            'value' => (float) $order->total_amount,
            'content_type' => 'product',
            'content_name' => 'Website Order #' . $order->order_no,
            'order_id' => (string) $order->order_no,
            'num_items' => count($contents) ?: 1,
            'contents' => $contents,
            'content_ids' => $contentIds ?: [(string) $order->order_no],
        ];

        return self::sendEvent(
            'Purchase',
            $userData,
            $customData,
            $eventId,
            route('frontend.order-confirmation', ['order_number' => $order->order_no]),
            $req
        );
    }

    /**
     * Send InitiateCheckout Event to Conversions API.
     */
    public static function trackInitiateCheckout(array $cartItems, float $subtotal, ?string $eventId = null, ?Request $request = null): array
    {
        $req = $request ?: request();
        $eventId = $eventId ?: ('initiate_checkout_' . session()->getId() . '_' . time());

        $contentIds = [];
        $contents = [];
        $totalQty = 0;

        foreach ($cartItems as $item) {
            $pId = (string)($item['id'] ?? ($item['barcode'] ?? ($item['name'] ?? '')));
            $qty = (int)($item['quantity'] ?? ($item['qty'] ?? 1));
            $price = (float)($item['price'] ?? ($item['unit_price'] ?? 0));
            $totalQty += $qty;

            if ($pId) {
                $contentIds[] = $pId;
                $contents[] = [
                    'id' => $pId,
                    'quantity' => $qty,
                    'item_price' => $price,
                ];
            }
        }

        $customData = [
            'currency' => 'BDT',
            'value' => (float) $subtotal,
            'content_type' => 'product',
            'content_ids' => $contentIds ?: ['CHECKOUT'],
            'contents' => $contents,
            'num_items' => $totalQty ?: 1,
        ];

        return self::sendEvent(
            'InitiateCheckout',
            [],
            $customData,
            $eventId,
            route('frontend.checkout'),
            $req
        );
    }

    /**
     * Normalize and SHA-256 hash user data according to Meta CAPI specification.
     */
    public static function formatUserData(array $raw = [], ?Request $request = null): array
    {
        $req = $request ?: request();
        $userData = [];

        // 1. IP & User Agent (Do NOT hash)
        if ($req) {
            $ip = $req->header('CF-Connecting-IP') ?: ($req->header('X-Forwarded-For') ?: $req->ip());
            if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
                $userData['client_ip_address'] = trim(explode(',', $ip)[0]);
            } else {
                $userData['client_ip_address'] = '103.145.118.1'; // Bangladesh fallback IP for local dev testing
            }

            $ua = $req->userAgent();
            if ($ua) {
                $userData['client_user_agent'] = $ua;
            }

            // Cookies: _fbp and _fbc
            $fbp = $req->cookie('_fbp');
            if ($fbp) {
                $userData['fbp'] = $fbp;
            }

            $fbc = $req->cookie('_fbc');
            if ($fbc) {
                $userData['fbc'] = $fbc;
            } elseif ($req->has('fbclid')) {
                $userData['fbc'] = 'fb.1.' . (time() * 1000) . '.' . $req->input('fbclid');
            }
        }

        // 2. Email (lowercase, trimmed, SHA-256 hashed)
        if (!empty($raw['email'])) {
            $cleanEmail = strtolower(trim($raw['email']));
            $userData['em'] = [hash('sha256', $cleanEmail)];
        }

        // 3. Phone (numeric only, ensure country code 880, SHA-256 hashed)
        if (!empty($raw['phone'])) {
            $digits = preg_replace('/[^0-9]/', '', $raw['phone']);
            if (str_starts_with($digits, '0')) {
                $digits = '88' . $digits;
            } elseif (!str_starts_with($digits, '880') && strlen($digits) === 10) {
                $digits = '880' . $digits;
            }
            $userData['ph'] = [hash('sha256', $digits)];
        }

        // 4. Name (split first/last, lowercase, trimmed, SHA-256 hashed)
        if (!empty($raw['name'])) {
            $parts = explode(' ', trim($raw['name']));
            $fn = strtolower($parts[0] ?? '');
            $ln = strtolower(end($parts) ?: '');

            if ($fn) {
                $userData['fn'] = [hash('sha256', $fn)];
            }
            if ($ln && $ln !== $fn) {
                $userData['ln'] = [hash('sha256', $ln)];
            }
        }

        // 5. City (lowercase, trimmed, SHA-256 hashed)
        if (!empty($raw['city'])) {
            $cleanCity = strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $raw['city'])));
            if ($cleanCity) {
                $userData['ct'] = [hash('sha256', $cleanCity)];
            }
        }

        // 6. Country (lowercase 2-letter ISO, SHA-256 hashed)
        $userData['country'] = [hash('sha256', 'bd')];

        return $userData;
    }
}
