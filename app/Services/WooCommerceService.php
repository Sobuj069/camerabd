<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Branch;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use App\Models\BranchBrand;
use App\Models\Unit;
use Illuminate\Support\Facades\Http;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class WooCommerceService
{
    /**
     * Get WooCommerce credentials from business_settings or fallback defaults
     */
    public function getCredentials(): array
    {
        $urlSetting = BusinessSetting::where('type', 'woocommerce_url')->first();
        $keySetting = BusinessSetting::where('type', 'woocommerce_consumer_key')->first();
        $secretSetting = BusinessSetting::where('type', 'woocommerce_consumer_secret')->first();
        $autoSyncSetting = BusinessSetting::where('type', 'woocommerce_auto_sync')->first();
        $autoSyncInterval = BusinessSetting::where('type', 'woocommerce_auto_sync_interval')->first();
        $autoSyncScope = BusinessSetting::where('type', 'woocommerce_auto_sync_scope')->first();
        $lastSyncedAt = BusinessSetting::where('type', 'woocommerce_last_synced_at')->first();

        return [
            'url' => rtrim($urlSetting ? $urlSetting->value : 'https://masudoff.com', '/'),
            'consumer_key' => $keySetting ? $keySetting->value : 'ck_8ca0da13e3604b021c8cd3acd0444281ed6e6a4c',
            'consumer_secret' => $secretSetting ? $secretSetting->value : 'cs_7fcc1d353dedd76f1188067ce9ec6e461e097b5a',
            'auto_sync' => $autoSyncSetting ? (bool)$autoSyncSetting->value : true,
            'auto_sync_interval' => $autoSyncInterval ? (int)$autoSyncInterval->value : 2,
            'auto_sync_scope' => $autoSyncScope ? $autoSyncScope->value : 'all',
            'last_synced_at' => $lastSyncedAt ? $lastSyncedAt->value : null,
        ];
    }

    /**
     * Update WooCommerce credentials in business_settings
     */
    public function updateCredentials(array $data): void
    {
        if (isset($data['url'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_url'],
                ['value' => rtrim($data['url'], '/')]
            );
        }
        if (isset($data['consumer_key'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_consumer_key'],
                ['value' => trim($data['consumer_key'])]
            );
        }
        if (isset($data['consumer_secret'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_consumer_secret'],
                ['value' => trim($data['consumer_secret'])]
            );
        }
        if (isset($data['auto_sync'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_auto_sync'],
                ['value' => $data['auto_sync'] ? '1' : '0']
            );
        }
        if (isset($data['auto_sync_interval'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_auto_sync_interval'],
                ['value' => (string)(int)$data['auto_sync_interval']]
            );
        }
        if (isset($data['auto_sync_scope'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_auto_sync_scope'],
                ['value' => trim($data['auto_sync_scope'])]
            );
        }
        if (isset($data['last_synced_at'])) {
            BusinessSetting::updateOrCreate(
                ['type' => 'woocommerce_last_synced_at'],
                ['value' => $data['last_synced_at']]
            );
        }
    }

    /**
     * Run full sync for products, orders or both and track last sync timestamp
     */
    public function syncAll(int $productLimit = 100, int $orderLimit = 50, string $scope = 'all'): array
    {
        $prodResult = null;
        $orderResult = null;
        $messages = [];
        $hasError = false;

        if (in_array($scope, ['all', 'products'])) {
            $prodResult = $this->syncProducts($productLimit);
            if (!empty($prodResult['message'])) {
                $messages[] = $prodResult['message'];
            }
            if (!($prodResult['success'] ?? false)) {
                $hasError = true;
            }
        }

        if (in_array($scope, ['all', 'orders'])) {
            $orderResult = $this->syncOrders($orderLimit);
            if (!empty($orderResult['message'])) {
                $messages[] = $orderResult['message'];
            }
            if (!($orderResult['success'] ?? false)) {
                $hasError = true;
            }
        }

        $now = date('Y-m-d H:i:s');
        BusinessSetting::updateOrCreate(
            ['type' => 'woocommerce_last_synced_at'],
            ['value' => $now]
        );

        return [
            'success' => !$hasError,
            'message' => implode(' | ', $messages) ?: 'WooCommerce sync completed.',
            'products' => $prodResult,
            'orders' => $orderResult,
            'last_synced_at' => $now,
            'last_synced_formatted' => date('d M, Y h:i:s A', strtotime($now)),
        ];
    }

    /**
     * Build an optimized HTTP client for WooCommerce REST API
     * Solves cPanel connection timeouts (cURL error 28), IPv6 resolution hangs,
     * WAF / Wordfence bot-blocking, and SSL certificate verification delays.
     */
    protected function buildHttpClient(int $timeout = 60, int $connectTimeout = 20)
    {
        return Http::timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->withOptions([
                'verify' => false,
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4 to prevent 10s connect timeout on cPanel
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_TCP_NODELAY => true,
                ],
            ])
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
            ]);
    }

    /**
     * Test connection to WooCommerce REST API
     */
    public function testConnection(): array
    {
        $creds = $this->getCredentials();

        try {
            $endpoint = $creds['url'] . '/wp-json/wc/v3/products';
            
            // Try query param authentication first
            $response = $this->buildHttpClient(30, 15)->get($endpoint, [
                'consumer_key' => $creds['consumer_key'],
                'consumer_secret' => $creds['consumer_secret'],
                'per_page' => 1
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to WooCommerce API!',
                    'data' => $response->json()
                ];
            } else {
                // Fallback to Basic Auth if query param auth failed
                $fallbackResponse = $this->buildHttpClient(30, 15)
                    ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                    ->get($endpoint, ['per_page' => 1]);

                if ($fallbackResponse->successful()) {
                    return [
                        'success' => true,
                        'message' => 'Successfully connected to WooCommerce API (Basic Auth)!',
                        'data' => $fallbackResponse->json()
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Connection failed with HTTP status: ' . $response->status() . ' - ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync Products from WooCommerce
     */
    public function syncProducts(int $limit = 100): array
    {
        $creds = $this->getCredentials();
        $endpoint = $creds['url'] . '/wp-json/wc/v3/products';

        try {
            $response = $this->buildHttpClient(60, 25)->get($endpoint, [
                'consumer_key' => $creds['consumer_key'],
                'consumer_secret' => $creds['consumer_secret'],
                'per_page' => min($limit, 100)
            ]);

            if (!$response->successful()) {
                // Fallback with Basic Auth
                $response = $this->buildHttpClient(60, 25)
                    ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                    ->get($endpoint, [
                        'per_page' => min($limit, 100)
                    ]);
            }

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch products from WooCommerce. Status: ' . $response->status()
                ];
            }

            $wcProducts = $response->json();
            if (!is_array($wcProducts)) {
                return [
                    'success' => false,
                    'message' => 'Invalid product payload returned from WooCommerce.'
                ];
            }

            $createdCount = 0;
            $updatedCount = 0;

            foreach ($wcProducts as $wcProduct) {
                $wcId = $wcProduct['id'];
                $sku = !empty($wcProduct['sku']) ? $wcProduct['sku'] : ('WC-' . $wcId);
                $name = $wcProduct['name'];
                $price = (float)($wcProduct['price'] ?? 0);
                $regularPrice = (float)($wcProduct['regular_price'] ?? $price);
                $salePrice = !empty($wcProduct['sale_price']) ? (float)$wcProduct['sale_price'] : $price;
                $stock = isset($wcProduct['stock_quantity']) && is_numeric($wcProduct['stock_quantity']) 
                    ? (float)$wcProduct['stock_quantity'] 
                    : 10;
                $description = strip_tags($wcProduct['description'] ?? '');

                // Image handling
                $imageSrc = null;
                if (!empty($wcProduct['images']) && is_array($wcProduct['images']) && isset($wcProduct['images'][0]['src'])) {
                    $imageSrc = $wcProduct['images'][0]['src'];
                }

                // Category handling
                $categoryId = null;
                if (!empty($wcProduct['categories']) && is_array($wcProduct['categories'])) {
                    $catName = $wcProduct['categories'][0]['name'];
                    $category = Category::firstOrCreate(['name' => $catName]);
                    $categoryId = $category->id;
                }

                // Brand handling
                $brandId = null;
                if (!empty($wcProduct['brands']) && is_array($wcProduct['brands'])) {
                    $brandName = $wcProduct['brands'][0]['name'];
                    $brand = Brand::firstOrCreate(
                        ['name' => $brandName],
                        ['slug' => \Illuminate\Support\Str::slug($brandName)]
                    );
                    $brandId = $brand->id;
                }


                // Check if product exists by woocommerce_product_id or barcode/sku
                $existingProduct = null;
                if (Schema::hasColumn('products', 'woocommerce_product_id')) {
                    $existingProduct = Product::where('woocommerce_product_id', $wcId)->first();
                }
                if (!$existingProduct) {
                    $existingProduct = Product::where('barcode', $sku)->orWhere('name', $name)->first();
                }

                $productData = [
                    'name' => $name,
                    'barcode' => $sku,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'purchase_price' => $regularPrice > 0 ? $regularPrice : $price,
                    'selling_price' => $price > 0 ? $price : $regularPrice,
                    'dis_selling_price' => $salePrice,
                    'main_qty' => $stock,
                    'description' => mb_substr($description, 0, 500, 'UTF-8'),

                    'images' => $imageSrc,
                    'is_service' => 0,
                    'unit_id' => Unit::firstOrCreate(['name' => 'Pcs'])->id,
                    'status' => ($wcProduct['status'] ?? 'publish') === 'publish' ? '1' : '0',
                    'date' => date('Y-m-d'),
                ];



                if (Schema::hasColumn('products', 'woocommerce_product_id')) {
                    $productData['woocommerce_product_id'] = $wcId;
                }

                if ($existingProduct) {
                    $existingProduct->update($productData);
                    $this->attachToBranches($existingProduct);
                    $updatedCount++;
                } else {
                    $newProd = Product::create($productData);
                    $this->attachToBranches($newProd);
                    $createdCount++;
                }

            }

            return [
                'success' => true,
                'message' => "Product Sync Completed! Total: " . count($wcProducts) . " (Created: {$createdCount}, Updated: {$updatedCount})",
                'created' => $createdCount,
                'updated' => $updatedCount,
                'total' => count($wcProducts)
            ];

        } catch (\Exception $e) {
            Log::error('WooCommerce Product Sync Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Product Sync Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync Orders from WooCommerce
     */
    public function syncOrders(int $limit = 50): array
    {
        $creds = $this->getCredentials();
        $endpoint = $creds['url'] . '/wp-json/wc/v3/orders';

        try {
            $response = $this->buildHttpClient(60, 25)->get($endpoint, [
                'consumer_key' => $creds['consumer_key'],
                'consumer_secret' => $creds['consumer_secret'],
                'per_page' => min($limit, 100)
            ]);

            if (!$response->successful()) {
                // Fallback with Basic Auth
                $response = $this->buildHttpClient(60, 25)
                    ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                    ->get($endpoint, [
                        'per_page' => min($limit, 100)
                    ]);
            }

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch orders from WooCommerce. Status: ' . $response->status()
                ];
            }

            $wcOrders = $response->json();
            if (!is_array($wcOrders)) {
                return [
                    'success' => false,
                    'message' => 'Invalid order payload returned from WooCommerce.'
                ];
            }

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($wcOrders as $wcOrder) {
                $importResult = $this->importSingleOrder($wcOrder);
                if ($importResult['imported'] ?? false) {
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }

            return [
                'success' => true,
                'message' => "Order Sync Completed! Imported: {$createdCount}, Already Synced: {$skippedCount}",
                'imported' => $createdCount,
                'skipped' => $skippedCount,
                'total' => count($wcOrders)
            ];

        } catch (\Exception $e) {
            Log::error('WooCommerce Order Sync Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Order Sync Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Import or update a single WooCommerce order (used by Webhooks and Sync)
     */
    public function importSingleOrder(array $wcOrder): array
    {
        $wcOrderId = $wcOrder['id'] ?? null;
        if (!$wcOrderId) {
            return ['success' => false, 'imported' => false, 'message' => 'No order ID provided.'];
        }

        // Check if invoice already imported
        $existingInvoice = null;
        if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
            $existingInvoice = Invoice::where('woocommerce_order_id', $wcOrderId)->first();
        }
        if (!$existingInvoice) {
            $existingInvoice = Invoice::where('unique_id', 'WC-' . $wcOrderId)->first();
        }

        if ($existingInvoice) {
            // Only update status from WooCommerce if the order has not yet been approved or transferred to Online Sale
            $isAlreadyHandled = ($existingInvoice->sale_type === 'Online') || in_array(strtolower((string)$existingInvoice->order_status), ['approved', 'rts', 'in courier', 'delivered', 'returned', 'cancelled']);
            if (!$isAlreadyHandled && !empty($wcOrder['status'])) {
                $existingInvoice->order_status = $wcOrder['status'];
                $existingInvoice->save();
            }
            return [
                'success' => true,
                'imported' => false,
                'message' => 'Order already imported, status synced.',
                'invoice' => $existingInvoice
            ];
        }

        // Process Customer info
        $billing = $wcOrder['billing'] ?? [];
        $firstName = $billing['first_name'] ?? 'WooCommerce';
        $lastName = $billing['last_name'] ?? 'Customer';
        $customerName = trim($firstName . ' ' . $lastName);
        if (empty($customerName)) {
            $customerName = 'WC Customer #' . $wcOrderId;
        }
        $phone = !empty($billing['phone']) ? $billing['phone'] : ('01700' . sprintf('%05d', $wcOrderId));
        $email = $billing['email'] ?? null;
        $address = trim(($billing['address_1'] ?? '') . ' ' . ($billing['city'] ?? ''));

        $customer = Customer::where('phone', $phone)->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => $customerName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'branch_id' => 1,
                'status' => '1',
                'date' => date('Y-m-d')
            ]);
        }

        $totalAmount = (float)($wcOrder['total'] ?? 0);
        $discountAmount = (float)($wcOrder['discount_total'] ?? 0);
        $shippingTotal = (float)($wcOrder['shipping_total'] ?? 0);
        $rawWcStatus = strtolower(trim($wcOrder['status'] ?? 'processing'));
        
        // In POS: New WooCommerce orders must show in 'Processing' tab for fulfillment unless cancelled/returned
        if (in_array($rawWcStatus, ['cancelled', 'failed', 'canceled'])) {
            $orderStatus = 'Cancelled';
        } elseif (in_array($rawWcStatus, ['returned', 'return'])) {
            $orderStatus = 'Returned';
        } else {
            $orderStatus = 'Processing';
        }

        $isPaid = in_array($rawWcStatus, ['completed', 'processing']);

        $wcDateCreated = !empty($wcOrder['date_created']) ? $wcOrder['date_created'] : 'now';
        $orderTimestamp = date('Y-m-d H:i:s', strtotime($wcDateCreated));

        $invoiceData = [
            'unique_id' => 'WC-' . $wcOrderId,
            'invoice_no' => 'WC-' . ($wcOrder['number'] ?? $wcOrderId),
            'customer_id' => $customer->id,
            'date' => date('Y-m-d', strtotime($wcDateCreated)),
            'created_at' => $orderTimestamp,
            'estimated_amount' => $totalAmount,
            'discount_amount' => $discountAmount,
            'delivery_charge' => $shippingTotal,
            'total_amount' => $totalAmount,
            'total_paid' => 0,
            'total_due' => $totalAmount,
            'order_status' => $orderStatus,
            'sale_type' => 'WooCommerce',
            'branch_id' => 1,
            'note' => !empty($wcOrder['customer_note']) ? trim($wcOrder['customer_note']) : null,
            'created_by' => auth()->id() ?: 1,
            'status' => 1
        ];

        if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
            $invoiceData['woocommerce_order_id'] = $wcOrderId;
        }

        $invoice = Invoice::create($invoiceData);

        // Add line items
        $lineItems = $wcOrder['line_items'] ?? [];
        foreach ($lineItems as $item) {
            $lineWcProductId = $item['product_id'] ?? null;
            $lineName = $item['name'] ?? 'Product';
            $qty = (float)($item['quantity'] ?? 1);
            $price = (float)($item['price'] ?? 0);
            $subtotal = (float)($item['subtotal'] ?? ($price * $qty));

            // Match local product
            $localProduct = null;
            if ($lineWcProductId && Schema::hasColumn('products', 'woocommerce_product_id')) {
                $localProduct = Product::where('woocommerce_product_id', $lineWcProductId)->first();
            }
            if (!$localProduct) {
                $localProduct = Product::where('name', $lineName)->first();
            }

            // Create placeholder product if not matched
            if (!$localProduct) {
                $placeholderData = [
                    'name' => $lineName,
                    'barcode' => 'WC-' . ($lineWcProductId ?: rand(1000, 9999)),
                    'selling_price' => $price,
                    'purchase_price' => $price,
                    'main_qty' => 100,
                    'status' => '1',
                    'is_service' => 0,
                    'unit_id' => Unit::firstOrCreate(['name' => 'Pcs'])->id,
                    'date' => date('Y-m-d'),
                ];

                if ($lineWcProductId && Schema::hasColumn('products', 'woocommerce_product_id')) {
                    $placeholderData['woocommerce_product_id'] = $lineWcProductId;
                }
                $localProduct = Product::create($placeholderData);
            }

            if ($localProduct) {
                $this->attachToBranches($localProduct);
            }

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $localProduct->id,
                'rate' => $price,
                'main_qty' => $qty,
                'subtotal' => $subtotal,
                'actual_main' => $qty,
                'actual_total' => $subtotal,
                'inv_subtotal' => $subtotal,
                'pur_subtotal' => ($localProduct->purchase_price ?? $price) * $qty,
                'date' => date('Y-m-d'),
                'status' => 1
            ]);
        }

        return [
            'success' => true,
            'imported' => true,
            'message' => "Order #{$wcOrderId} imported successfully!",
            'invoice' => $invoice
        ];
    }

    /**
     * Associate product, category, and brand with active branches
     */
    public function attachToBranches(Product $product): void
    {
        try {
            $branches = Branch::all();
            $branchIds = $branches->isNotEmpty() ? $branches->pluck('id')->toArray() : [1];

            foreach ($branchIds as $branchId) {
                BranchProduct::firstOrCreate([
                    'branch_id' => $branchId,
                    'product_id' => $product->id,
                ]);

                if ($product->category_id) {
                    BranchCategory::firstOrCreate([
                        'branch_id' => $branchId,
                        'category_id' => $product->category_id,
                    ]);
                }

                if ($product->brand_id) {
                    BranchBrand::firstOrCreate([
                        'branch_id' => $branchId,
                        'brand_id' => $product->brand_id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to attach product to branches: ' . $e->getMessage());
        }
    }

    /**
     * Fetch a single order from WooCommerce remote store via REST API
     */
    public function fetchSingleOrder($wcOrderId): array
    {
        $creds = $this->getCredentials();
        $endpoint = $creds['url'] . '/wp-json/wc/v3/orders/' . $wcOrderId;

        try {
            $response = $this->buildHttpClient(30, 15)
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->get($endpoint);

            if (!$response->successful()) {
                $response = $this->buildHttpClient(30, 15)
                    ->get($endpoint . '?consumer_key=' . urlencode($creds['consumer_key']) . '&consumer_secret=' . urlencode($creds['consumer_secret']));
            }

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::warning("Failed to fetch WooCommerce remote order #{$wcOrderId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update order status on WooCommerce remote store via REST API
     */
    public function updateOrderStatus($wcOrderId, string $status): array
    {
        $creds = $this->getCredentials();
        $endpoint = $creds['url'] . '/wp-json/wc/v3/orders/' . $wcOrderId;

        $normalizedMap = [
            'approved'             => 'approved',
            'processing'           => 'processing',
            'pending'              => 'pending',
            'on-hold'              => 'on-hold',
            'on hold'              => 'on-hold',
            'completed'            => 'completed',
            'cancelled'            => 'cancelled',
            'canceled'             => 'cancelled',
            'refunded'             => 'refunded',
            'returned'             => 'refunded',
            'failed'               => 'failed',
            'good but no response' => 'on-hold',
            'no response'          => 'on-hold',
            'advance payment'      => 'processing',
            'in courier'           => 'processing',
            'shipping'             => 'processing',
            'rts'                  => 'processing',
            'delivered'            => 'completed',
        ];
        $cleanStatus = $normalizedMap[strtolower(trim($status))] ?? strtolower(trim($status));

        try {
            $response = $this->buildHttpClient(30, 15)
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->put($endpoint, [
                    'status' => $cleanStatus
                ]);

            if (!$response->successful()) {
                $response = $this->buildHttpClient(30, 15)
                    ->put($endpoint . '?consumer_key=' . urlencode($creds['consumer_key']) . '&consumer_secret=' . urlencode($creds['consumer_secret']), [
                        'status' => $cleanStatus
                    ]);
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => "WooCommerce remote order #{$wcOrderId} status updated to {$cleanStatus}."
                ];
            }

            $errDetail = $this->extractErrorMessage($response);
            return [
                'success' => false,
                'message' => "Failed to update remote order #{$wcOrderId} (HTTP {$response->status()}: {$errDetail})"
            ];
        } catch (\Exception $e) {
            Log::warning("Failed to update WooCommerce remote order #{$wcOrderId} status: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper to extract human-readable error message from API response
     */
    protected function extractErrorMessage($response): string
    {
        $rawBody = $response->body();
        $json = $response->json();
        if (is_array($json) && !empty($json['message'])) {
            return $json['message'] . (!empty($json['code']) ? " [{$json['code']}]" : "");
        }
        if (preg_match('/<title>(.*?)<\/title>/i', $rawBody, $m)) {
            return strip_tags(trim($m[1]));
        }
        if (preg_match('/<h[1-2][^>]*>(.*?)<\/h[1-2]>/i', $rawBody, $m)) {
            return strip_tags(trim($m[1]));
        }
        $stripped = trim(preg_replace('/\s+/', ' ', strip_tags($rawBody)));
        return $stripped ? mb_substr($stripped, 0, 150) : ("HTTP " . $response->status());
    }

    /**
     * Batch update multiple order statuses on WooCommerce remote store via REST API
     * @param array $items Array of items, each with ['id' => int, 'status' => string]
     */
    public function updateOrderStatusBatch(array $items): array
    {
        if (empty($items)) {
            return ['success' => true, 'updated' => 0, 'message' => 'No orders to update.'];
        }

        $creds = $this->getCredentials();
        $endpoint = $creds['url'] . '/wp-json/wc/v3/orders/batch';

        $normalizedMap = [
            'approved'             => 'approved',
            'processing'           => 'processing',
            'pending'              => 'pending',
            'on-hold'              => 'on-hold',
            'on hold'              => 'on-hold',
            'completed'            => 'completed',
            'cancelled'            => 'cancelled',
            'canceled'             => 'cancelled',
            'refunded'             => 'refunded',
            'returned'             => 'refunded',
            'failed'               => 'failed',
            'good but no response' => 'on-hold',
            'no response'          => 'on-hold',
            'advance payment'      => 'processing',
            'in courier'           => 'processing',
            'shipping'             => 'processing',
            'rts'                  => 'processing',
            'delivered'            => 'completed',
        ];

        // Format batch payload
        $updatePayload = [];
        foreach ($items as $item) {
            $wcId = (int)($item['id'] ?? 0);
            $rawStatus = (string)($item['status'] ?? 'approved');
            $cleanStatus = $normalizedMap[strtolower(trim($rawStatus))] ?? strtolower(trim($rawStatus));

            if ($wcId > 0) {
                $updatePayload[] = [
                    'id' => $wcId,
                    'status' => $cleanStatus
                ];
            }
        }

        if (empty($updatePayload)) {
            return ['success' => true, 'updated' => 0, 'message' => 'No valid order IDs provided.'];
        }

        try {
            // Process in chunks of 50 orders max to respect WooCommerce batch limits
            $chunks = array_chunk($updatePayload, 50);
            $totalUpdated = 0;
            $lastError = '';
            foreach ($chunks as $chunk) {
                $response = $this->buildHttpClient(30, 15)
                    ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                    ->post($endpoint, ['update' => $chunk]);

                if (!$response->successful()) {
                    $response = $this->buildHttpClient(30, 15)
                        ->post($endpoint . '?consumer_key=' . urlencode($creds['consumer_key']) . '&consumer_secret=' . urlencode($creds['consumer_secret']), [
                            'update' => $chunk
                        ]);
                }

                if ($response->successful()) {
                    $resData = $response->json();
                    $totalUpdated += count($resData['update'] ?? $chunk);
                } else {
                    $errDetail = $this->extractErrorMessage($response);
                    $lastError = "HTTP " . $response->status() . " (" . $errDetail . ")";
                    Log::warning("WooCommerce Batch Status Update Partial Failure: " . $lastError . ", attempting individual PUT fallback...");
                    
                    // Fallback to individual updates if batch POST is blocked by server firewall
                    foreach ($chunk as $singleItem) {
                        $singleRes = $this->updateOrderStatus($singleItem['id'], $singleItem['status']);
                        if ($singleRes['success'] ?? false) {
                            $totalUpdated++;
                        } else {
                            $lastError = $singleRes['message'] ?? $lastError;
                        }
                    }
                }
            }

            if ($totalUpdated > 0) {
                return [
                    'success' => true,
                    'updated' => $totalUpdated,
                    'message' => "Successfully updated {$totalUpdated} order(s) on WooCommerce remote store."
                ];
            }

            return [
                'success' => false,
                'updated' => 0,
                'message' => $lastError ? "WooCommerce update failed ({$lastError})" : "No order statuses could be updated on WooCommerce."
            ];
        } catch (\Exception $e) {
            Log::warning("Failed to batch update WooCommerce remote orders: " . $e->getMessage());
            return [
                'success' => false,
                'updated' => 0,
                'message' => $e->getMessage()
            ];
        }
    }
}

