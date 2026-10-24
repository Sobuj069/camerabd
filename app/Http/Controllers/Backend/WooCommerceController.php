<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\WooCommerceService;
use App\Services\CourierFraudCheckerService;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WooCommerceController extends Controller
{
    protected $wooService;

    public function __construct(WooCommerceService $wooService)
    {
        $this->wooService = $wooService;
    }

    /**
     * Display WooCommerce integration dashboard & settings
     */
    public function index()
    {
        $credentials = $this->wooService->getCredentials();

        $syncedProductsCount = 0;
        if (Schema::hasColumn('products', 'woocommerce_product_id')) {
            $syncedProductsCount = Product::whereNotNull('woocommerce_product_id')->count();
        }

        $syncedOrdersCount = 0;
        if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
            $syncedOrdersCount = Invoice::whereNotNull('woocommerce_order_id')->count();
        }

        $totalProductsCount = Product::count();
        $totalInvoicesCount = Invoice::count();

        return view('backend.woocommerce.index', compact(
            'credentials',
            'syncedProductsCount',
            'syncedOrdersCount',
            'totalProductsCount',
            'totalInvoicesCount'
        ));
    }

    /**
     * Save/Update WooCommerce settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
        ]);

        $this->wooService->updateCredentials([
            'url' => $request->url,
            'consumer_key' => $request->consumer_key,
            'consumer_secret' => $request->consumer_secret,
            'auto_sync' => $request->has('auto_sync'),
            'auto_sync_interval' => $request->input('auto_sync_interval', 2),
            'auto_sync_scope' => $request->input('auto_sync_scope', 'all'),
        ]);

        $notification = [
            'message' => 'WooCommerce API Credentials updated successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    /**
     * AJAX Test Connection
     */
    public function testConnection()
    {
        $result = $this->wooService->testConnection();
        return response()->json($result);
    }

    /**
     * AJAX Action for background automated synchronization
     */
    public function autoSync(Request $request)
    {
        $scope = $request->input('scope', 'all');
        $productLimit = (int)$request->input('product_limit', 100);
        $orderLimit = (int)$request->input('order_limit', 50);

        $result = $this->wooService->syncAll($productLimit, $orderLimit, $scope);

        // Compute updated counts
        $syncedProductsCount = 0;
        if (Schema::hasColumn('products', 'woocommerce_product_id')) {
            $syncedProductsCount = Product::whereNotNull('woocommerce_product_id')->count();
        }

        $syncedOrdersCount = 0;
        if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
            $syncedOrdersCount = Invoice::whereNotNull('woocommerce_order_id')->count();
        }

        $totalProductsCount = Product::count();
        $totalInvoicesCount = Invoice::count();

        $result['syncedProductsCount'] = $syncedProductsCount;
        $result['syncedOrdersCount'] = $syncedOrdersCount;
        $result['totalProductsCount'] = $totalProductsCount;
        $result['totalInvoicesCount'] = $totalInvoicesCount;

        return response()->json($result);
    }

    /**
     * AJAX / Action to Sync Products
     */
    public function syncProducts(Request $request)
    {
        $limit = $request->input('limit', 100);
        $result = $this->wooService->syncProducts($limit);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        $alertType = $result['success'] ? 'success' : 'error';
        return redirect()->back()->with([
            'message' => $result['message'],
            'alert-type' => $alertType
        ]);
    }

    /**
     * AJAX / Action to Sync Orders
     */
    public function syncOrders(Request $request)
    {
        $limit = $request->input('limit', 50);
        $result = $this->wooService->syncOrders($limit);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        $alertType = $result['success'] ? 'success' : 'error';
        return redirect()->back()->with([
            'message' => $result['message'],
            'alert-type' => $alertType
        ]);
    }

    /**
     * Handle incoming WooCommerce Webhook for real-time order creation / update
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        if (empty($payload) || !isset($payload['id'])) {
            return response()->json([
                'status' => 'ignored',
                'message' => 'Empty or non-order webhook payload received'
            ], 200);
        }

        $result = $this->wooService->importSingleOrder($payload);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ], 200);
    }

    /**
     * Display WordPress Order List page
     */
    public function ordersList(Request $request)
    {
        $statusFilter = $request->input('WooCommerceStatus', 'Processing');

        // Base query for WooCommerce orders (includes all WooCommerce orders, both pending transfer and transferred)
        $baseCountQuery = Invoice::query();
        if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
            $baseCountQuery->where(function($q) {
                $q->where('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id')
                  ->orWhere('unique_id', 'like', 'WC-%');
            });
        } else {
            $baseCountQuery->where(function($q) {
                $q->where('sale_type', 'WooCommerce')
                  ->orWhere('unique_id', 'like', 'WC-%');
            });
        }

        // Calculate counts for requested status tabs including Scanner statuses
        $statusCounts = [
            'ALL' => (clone $baseCountQuery)->count(),
            'Processing' => (clone $baseCountQuery)->where(function($q) {
                $q->whereIn('order_status', ['Processing', 'Pending', 'processing', 'pending', 'completed', 'new', 'New', 'on-hold', 'on_hold'])->orWhereNull('order_status');
            })->count(),
            'Shipping' => (clone $baseCountQuery)->whereIn('order_status', ['Shipping', 'shipping', 'In Transit', 'in_transit', 'Dispatched', 'In Courier', 'in_courier'])->count(),
            'Returned' => (clone $baseCountQuery)->whereIn('order_status', ['Returned', 'returned', 'Return', 'return'])->count(),
            'RTS' => (clone $baseCountQuery)->whereIn('order_status', ['RTS', 'rts', 'Ready To Ship', 'ready_to_ship'])->count(),
            'Good But No Response' => (clone $baseCountQuery)->where('order_status', 'Good But No Response')->count(),
            'No Response' => (clone $baseCountQuery)->whereIn('order_status', ['No Response', 'no_response', 'No response', 'no response'])->count(),
            'Advance Payment' => (clone $baseCountQuery)->whereIn('order_status', ['Advance Payment', 'advance_payment'])->count(),
            'On Hold' => (clone $baseCountQuery)->whereIn('order_status', ['On Hold', 'on-hold', 'on_hold'])->count(),
            'Approved' => (clone $baseCountQuery)->whereIn('order_status', ['Approved', 'approved'])->count(),
            'Cancelled' => (clone $baseCountQuery)->whereIn('order_status', ['Cancelled', 'cancelled', 'failed'])->count(),
        ];

        $query = clone $baseCountQuery;
        $query->with(['customer', 'invoiceItems.product']);

        // Apply WooCommerceStatus filter
        if ($statusFilter && $statusFilter !== 'ALL') {
            if (in_array($statusFilter, ['Processing', 'Pending', 'New', 'processing', 'pending'])) {
                $query->where(function($q) {
                    $q->whereIn('order_status', ['Processing', 'Pending', 'processing', 'pending', 'completed', 'new', 'New', 'on-hold', 'on_hold'])->orWhereNull('order_status');
                });
            } elseif ($statusFilter === 'Shipping') {
                $query->whereIn('order_status', ['Shipping', 'shipping', 'In Transit', 'in_transit', 'Dispatched']);
            } elseif ($statusFilter === 'Returned') {
                $query->whereIn('order_status', ['Returned', 'returned', 'Return', 'return']);
            } elseif ($statusFilter === 'RTS') {
                $query->whereIn('order_status', ['RTS', 'rts', 'Ready To Ship', 'ready_to_ship']);
            } elseif ($statusFilter === 'Good But No Response') {
                $query->where('order_status', 'Good But No Response');
            } elseif ($statusFilter === 'No Response') {
                $query->whereIn('order_status', ['No Response', 'no_response', 'No response', 'no response']);
            } elseif ($statusFilter === 'Advance Payment') {
                $query->whereIn('order_status', ['Advance Payment', 'advance_payment']);
            } elseif ($statusFilter === 'On Hold') {
                $query->whereIn('order_status', ['On Hold', 'on-hold', 'on_hold']);
            } elseif ($statusFilter === 'Approved') {
                $query->whereIn('order_status', ['Approved', 'approved']);
            } elseif ($statusFilter === 'Cancelled') {
                $query->whereIn('order_status', ['Cancelled', 'cancelled', 'failed']);
            } else {
                $query->where('order_status', $statusFilter);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('unique_id', 'like', "%{$search}%")
                  ->orWhere('woocommerce_order_id', 'like', "%{$search}%")
                  ->orWhere('consignment_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('invoiceItems', function($iq) use ($search) {
                      $iq->where('new_details', 'like', "%{$search}%")
                        ->orWhere('serial', 'like', "%{$search}%")
                        ->orWhereHas('product', function($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                              ->orWhere('barcode', 'like', "%{$search}%")
                              ->orWhere('sku', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $allMatchingIds = (clone $query)->pluck('id')->toArray();
        $orders = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());

        // Dynamic per-customer delivery stats calculation
        $customerStats = [];
        $fraudService = app(\App\Services\CourierFraudCheckerService::class);

        // Pre-group invoices by customer_id for fast batch lookups
        $customerIds = $orders->pluck('customer_id')->filter()->unique();
        $invoicesByCustomer = Invoice::whereIn('customer_id', $customerIds)
            ->select('id', 'customer_id', 'order_status', 'status')
            ->get()
            ->groupBy('customer_id');

        foreach ($orders as $order) {
            $phone = $order->customer?->phone;
            $cleanPhone = $phone ? $fraudService->normalizePhone($phone) : '';
            
            // Check if fraud data was cached from API lookup
            $cachedFraud = $cleanPhone ? \Illuminate\Support\Facades\Cache::get('fraud_check_' . $cleanPhone) : null;

            if ($cachedFraud && isset($cachedFraud['total_parcels'])) {
                $totalParcels = (int)$cachedFraud['total_parcels'];
                $delivered = (int)$cachedFraud['total_delivered'];
                $cancelled = (int)$cachedFraud['total_cancelled'];
                $rate = (float)$cachedFraud['overall_success_rate'];
                $rating = (int)($cachedFraud['rating_score'] ?? 80);

                $customerStats[$order->id] = [
                    'total' => $totalParcels > 0 ? $totalParcels : 1,
                    'delivered' => $delivered > 0 ? $delivered : ($cancelled > 0 ? 0 : 1),
                    'cancelled' => $cancelled,
                    'rate' => round($rate),
                    'rating' => $rating,
                ];
            } else {
                $custInvoices = $invoicesByCustomer->get($order->customer_id, collect());
                $totalLocal = $custInvoices->count();
                $cancelledLocal = $custInvoices->filter(function($inv) {
                    return in_array($inv->order_status, ['Cancelled', 'cancelled', 'Returned', 'returned', 'failed']) || in_array($inv->status, [2, 3]);
                })->count();
                
                $deliveredLocal = max(0, $totalLocal - $cancelledLocal);

                // If customer is fresh with 1 new order
                if ($totalLocal <= 1 && $cancelledLocal == 0) {
                    $rate = 100;
                    $rating = 80;
                    $totalDisplay = 1;
                    $deliveredDisplay = 1;
                } else {
                    $rate = $totalLocal > 0 ? round(($deliveredLocal / $totalLocal) * 100) : 100;
                    $rating = min(99, max(25, round(($rate * 0.7) + min(25, $deliveredLocal * 4) - ($cancelledLocal * 12))));
                    $totalDisplay = $totalLocal;
                    $deliveredDisplay = $deliveredLocal;
                }

                $customerStats[$order->id] = [
                    'total' => $totalDisplay,
                    'delivered' => $deliveredDisplay,
                    'cancelled' => $cancelledLocal,
                    'rate' => $rate,
                    'rating' => $rating,
                ];
            }
        }

        $credentials = $this->wooService->getCredentials();
        return view('backend.woocommerce.orders', compact('orders', 'statusCounts', 'statusFilter', 'customerStats', 'credentials', 'allMatchingIds'));
    }


    /**
     * Send selected WooCommerce orders to Courier / Online Sale List
     */
    public function sendToOnlineSale(Request $request)
    {
        $courierType = $request->input('courier_type', 'Steadfast');
        $sendDirectly = $request->has('send_to_courier_api');
        $stockMode = $request->input('stock_mode', 'without_manage');

        if ($request->input('select_all_matching') == '1' && $request->filled('all_matching_ids')) {
            $ids = array_values(array_filter(explode(',', (string)$request->input('all_matching_ids'))));
        } else {
            $request->validate([
                'order_ids' => 'required|array',
                'courier_type' => 'required|string',
            ]);
            $ids = $request->order_ids;
        }

        if (empty($ids)) {
            return redirect()->back()->with([
                'error' => 'No orders selected to send.',
                'message' => 'No orders selected to send.',
                'alert-type' => 'error'
            ]);
        }

        $invoices = Invoice::whereIn('id', $ids)->with('invoiceItems.product')->get();

        // 1. Stock Check if Manage Stock mode is selected
        if ($stockMode === 'with_manage') {
            $stockErrors = [];
            foreach ($invoices as $invoice) {
                foreach ($invoice->invoiceItems as $item) {
                    if ($item->product && $item->product->is_service == 0) {
                        $product = $item->product;
                        $requiredQty = (float)($item->main_qty > 0 ? $item->main_qty : 1);

                        // Calculate available stock in POS
                        $purchased  = (float) purchased_qty($product);
                        $invoiced   = (float) invoiced_qty($product);
                        $saleRet    = (float) returned_qty($product);
                        $purRet     = (float) return_pur_qty($product);
                        $damage     = (float) damaged_qty($product);
                        $adjustIn   = (float) adjust_in($product);
                        $adjustOut  = (float) adjust_out($product);

                        $availableStock = $purchased - $invoiced + $adjustIn + $saleRet - $adjustOut - $purRet - $damage;

                        if ($availableStock < $requiredQty) {
                            $stockErrors[] = "Order #{$invoice->invoice_no}: Product '{$product->name}' is OUT OF STOCK (Available: {$availableStock} pcs, Required: {$requiredQty} pcs).";
                        }
                    }
                }
            }

            if (!empty($stockErrors)) {
                $errorMsg = "Manage Stock Failed! Cannot send orders with insufficient stock: " . implode(" | ", $stockErrors);
                return redirect()->back()->with([
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                    'alert-type' => 'error'
                ]);
            }
        }

        // 2. Transfer Orders to Online Sale List
        $wcBatchItems = [];
        foreach ($invoices as $invoice) {
            $invoice->courier_type = $courierType;
            $invoice->sale_type = 'Online';
            $invoice->order_status = 'Approved';
            $invoice->status = 1;
            if (auth()->check()) {
                $invoice->created_by = auth()->id();
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('invoices', 'stock_mode')) {
                $invoice->stock_mode = $stockMode;
            }
            $invoice->save();

            $wcOrderId = !empty($invoice->woocommerce_order_id)
                ? (int)$invoice->woocommerce_order_id
                : (int)preg_replace('/[^0-9]/', '', (string)($invoice->invoice_no ?: $invoice->unique_id));

            if (!empty($wcOrderId)) {
                $wcBatchItems[] = ['id' => $wcOrderId, 'status' => 'approved'];
            }
        }

        // Fast Batch Sync all approved orders to WooCommerce remote store in 1 request
        if (!empty($wcBatchItems)) {
            try {
                $syncRes = $this->wooService->updateOrderStatusBatch($wcBatchItems);
                if (!($syncRes['success'] ?? false)) {
                    \Illuminate\Support\Facades\Log::warning("WooCommerce Remote Batch Sync Failed: " . ($syncRes['message'] ?? 'Unknown'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("WooCommerce Remote Batch Sync Exception: " . $e->getMessage());
            }
        }

        if ($sendDirectly) {
            $invoiceController = app(\App\Http\Controllers\Backend\InvoiceController::class);
            $courierReq = new Request(['invoice_ids' => $ids]);
            return $invoiceController->sendToCourier($courierReq);
        }

        $count = count($ids);
        return redirect()->route('invoice.online.sale', ['status' => 'processing'])->with([
            'message' => __(":count order(s) approved and transferred to Online Sale List successfully!", ['count' => $count]),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Approve single order and transfer to Online Sale List (Processing tab)
     */
    public function approveOrder(Request $request, $id)
    {
        $invoice = Invoice::with('invoiceItems.product')->findOrFail($id);
        $stockMode = $request->input('stock_mode', 'without_manage');
        $courierType = $request->input('courier_type', 'Steadfast');

        // 1. Stock validation if with_manage mode is selected
        if ($stockMode === 'with_manage') {
            $stockErrors = [];
            foreach ($invoice->invoiceItems as $item) {
                if ($item->product && $item->product->is_service == 0) {
                    $product = $item->product;
                    $requiredQty = (float)($item->main_qty > 0 ? $item->main_qty : 1);

                    $purchased  = (float) purchased_qty($product);
                    $invoiced   = (float) invoiced_qty($product);
                    $saleRet    = (float) returned_qty($product);
                    $purRet     = (float) return_pur_qty($product);
                    $damage     = (float) damaged_qty($product);
                    $adjustIn   = (float) adjust_in($product);
                    $adjustOut  = (float) adjust_out($product);

                    $availableStock = $purchased - $invoiced + $adjustIn + $saleRet - $adjustOut - $purRet - $damage;

                    if ($availableStock < $requiredQty) {
                        $stockErrors[] = "Product '{$product->name}' is OUT OF STOCK (Available: {$availableStock} pcs, Required: {$requiredQty} pcs).";
                    }
                }
            }

            if (!empty($stockErrors)) {
                $errorMsg = "Manage Stock Failed! " . implode(" | ", $stockErrors);
                return redirect()->back()->with([
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                    'alert-type' => 'error'
                ]);
            }
        }

        // 2. Update financial totals if provided from the details page
        if ($request->has('delivery_charge')) {
            $invoice->delivery_charge = (float)$request->delivery_charge;
        }
        if ($request->has('discount_amount')) {
            $invoice->discount_amount = (float)$request->discount_amount;
        }
        if ($request->has('total_paid')) {
            $invoice->total_paid = (float)$request->total_paid;
        }
        $itemsSubtotal = $invoice->invoiceItems->sum('subtotal');
        $invoice->total_amount = max(0, $itemsSubtotal + (float)$invoice->delivery_charge - (float)$invoice->discount_amount);
        $invoice->total_due = max(0, $invoice->total_amount - (float)$invoice->total_paid);

        // 3. Transfer order to Online Sale List & set status to Approved
        $invoice->sale_type = 'Online';
        $invoice->order_status = 'Approved';
        $invoice->status = 1;
        $invoice->courier_type = $courierType;
        if (auth()->check()) {
            $invoice->created_by = auth()->id();
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('invoices', 'stock_mode')) {
            $invoice->stock_mode = $stockMode;
        }
        $invoice->save();

        // 4. Sync status back to WooCommerce remote store as 'approved'
        $wcOrderId = !empty($invoice->woocommerce_order_id)
            ? (int)$invoice->woocommerce_order_id
            : (int)preg_replace('/[^0-9]/', '', (string)($invoice->invoice_no ?: $invoice->unique_id));

        if (!empty($wcOrderId)) {
            try {
                $syncRes = $this->wooService->updateOrderStatus($wcOrderId, 'approved');
                if (!($syncRes['success'] ?? false)) {
                    \Illuminate\Support\Facades\Log::warning("WooCommerce Remote Sync Failed for #{$wcOrderId}: " . ($syncRes['message'] ?? 'Unknown'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("WooCommerce Remote Sync Exception for #{$wcOrderId}: " . $e->getMessage());
            }
        }

        return redirect()->route('invoice.online.sale', ['status' => 'processing'])->with([
            'success' => "Order #{$invoice->invoice_no} has been approved and moved to Online Sale List!",
            'message' => "Order #{$invoice->invoice_no} has been approved and moved to Online Sale List!",
            'alert-type' => 'success'
        ]);
    }

    /**
     * Update Order Status from Order Details Action Dropdown
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string',
        ]);

        $order = Invoice::findOrFail($id);
        $newStatus = trim($request->order_status);
        $prevStatus = $order->order_status ?: 'Processing';

        // Synchronize inventory stock and IMEI serials if needed
        if (function_exists('syncInvoiceStockForStatusChange')) {
            syncInvoiceStockForStatusChange($order, $newStatus, $prevStatus);
        }

        $order->order_status = $newStatus;

        if (auth()->check()) {
            $order->created_by = auth()->id();
        }

        if (in_array(strtolower($newStatus), ['returned', 'return'])) {
            $order->status = 2;
        } elseif (in_array(strtolower($newStatus), ['cancelled', 'cancel', 'failed'])) {
            $order->status = 3;
        } else {
            $order->status = 1;
        }

        if (in_array(strtolower(trim($newStatus)), ['in courier', 'in_courier']) && empty($order->consignment_id)) {
            $invoiceController = app(\App\Http\Controllers\Backend\InvoiceController::class);
            $invoiceController->dispatchInvoiceToCourier($order);
        } else {
            $order->save();
        }

        $wcOrderId = !empty($order->woocommerce_order_id)
            ? (int)$order->woocommerce_order_id
            : (int)preg_replace('/[^0-9]/', '', (string)($order->invoice_no ?: $order->unique_id));

        if (!empty($wcOrderId)) {
            $wcStatusMap = [
                'Processing'           => 'processing',
                'Pending'              => 'pending',
                'On Hold'              => 'on-hold',
                'Completed'            => 'completed',
                'Cancelled'            => 'cancelled',
                'Returned'             => 'refunded',
                'Failed'               => 'failed',
                'Approved'             => 'approved',
                'Good But No Response' => 'on-hold',
                'No Response'          => 'on-hold',
                'Advance Payment'      => 'processing',
                'In Courier'           => 'processing',
                'Delivered'            => 'completed',
            ];
            $wcStatus = $wcStatusMap[$newStatus] ?? strtolower($newStatus);
            try {
                $syncRes = $this->wooService->updateOrderStatus($wcOrderId, $wcStatus);
                if (!($syncRes['success'] ?? false)) {
                    \Illuminate\Support\Facades\Log::warning("WooCommerce Remote Sync Failed for #{$wcOrderId}: " . ($syncRes['message'] ?? 'Unknown'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("WooCommerce Remote Sync Exception for #{$wcOrderId}: " . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Order #{$order->invoice_no} status updated to {$newStatus} successfully!",
                'order_status' => $newStatus,
            ]);
        }

        return redirect()->back()->with([
            'message' => "Order #{$order->invoice_no} status updated to '{$newStatus}' successfully!",
            'alert-type' => 'success'
        ]);
    }

    /**
     * Display Web Order Details view page
     */
    public function orderDetails(Request $request, $id)
    {
        $order = Invoice::with(['customer', 'invoiceItems.product'])->findOrFail($id);

        $fraudService = app(CourierFraudCheckerService::class);
        $fraudStats = $order->customer?->phone ? $fraudService->checkFraud($order->customer->phone) : [
            'success' => true,
            'overall_success_rate' => 100,
            'total_parcels' => 0,
            'total_delivered' => 0,
            'total_cancelled' => 0,
            'rating_score' => 76,
            'cards' => [
                'overall' => ['label' => 'Overall', 'rate' => '100%', 'total' => 13, 'success' => 13, 'cancelled' => 0, 'header_bg' => '#a855f7'],
                'pathao' => ['label' => 'Pathao', 'rate' => '100%', 'total' => 5, 'success' => 5, 'cancelled' => 0, 'header_bg' => '#f43f5e'],
                'steadfast' => ['label' => 'Steadfast', 'rate' => '100%', 'total' => 8, 'success' => 8, 'cancelled' => 0, 'header_bg' => '#10b981'],
                'redx' => ['label' => 'RedX', 'rate' => '0', 'total' => 0, 'success' => 0, 'cancelled' => 0, 'header_bg' => '#ef4444'],
                'carrybee' => ['label' => 'CarryBee', 'rate' => '0', 'total' => 0, 'success' => 0, 'cancelled' => 0, 'header_bg' => '#eab308'],
            ]
        ];

        $statusList = [
            'Processing' => 'Processing',
            'Pending' => 'Pending',
            'Good But No Response' => 'Good But No Response',
            'No Response' => 'No Response',
            'Advance Payment' => 'Advance Payment',
            'On Hold' => 'On Hold',
            'Approved' => 'Approved',
            'In Courier' => 'In Courier',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Cancelled',
            'Returned' => 'Returned',
        ];

        $courierList = [
            'Steadfast' => 'Steadfast',
            'Pathao' => 'Pathao',
            'RedX' => 'RedX',
            'CarryBee' => 'CarryBee',
            'Paperfly' => 'Paperfly',
            'Sundarban' => 'Sundarban',
            'SA Paribahan' => 'SA Paribahan',
            'Inside Dhaka' => 'Inside Dhaka',
            'Outside Dhaka' => 'Outside Dhaka',
        ];

        return view('backend.woocommerce.details', compact('order', 'fraudStats', 'statusList', 'courierList'));
    }

    /**
     * Update customer, shipping and order meta
     */
    public function updateOrderDetails(Request $request, $id)
    {
        $order = Invoice::with(['customer', 'invoiceItems'])->findOrFail($id);

        // Update Customer Info
        if ($order->customer) {
            $customer = $order->customer;
            if ($request->filled('customer_name')) {
                $customer->name = $request->customer_name;
            }
            if ($request->filled('customer_phone')) {
                $customer->phone = $request->customer_phone;
            }
            if ($request->has('customer_address')) {
                $customer->address = $request->customer_address;
            }
            $customer->save();
        }

        // Update Order Details
        if ($request->has('courier_type')) {
            $order->courier_type = $request->courier_type;
        }
        if ($request->filled('shipping_note')) {
            $order->note = ($order->note ? ($order->note . "\n") : "") . "[Shipping Note]: " . $request->shipping_note;
        }
        if ($request->has('source')) {
            $order->source_link = $request->source;
        }
        if ($request->has('is_preorder')) {
            $order->sale_type = ($request->is_preorder == '1' || $request->is_preorder == 'true') ? 'PreOrder' : 'WooCommerce';
        }
        if ($request->has('delivery_charge')) {
            $order->delivery_charge = (float)$request->delivery_charge;
        }
        if ($request->has('discount_amount')) {
            $order->discount_amount = (float)$request->discount_amount;
        }
        if ($request->filled('order_status')) {
            $order->order_status = $request->order_status;

            $wcOrderId = !empty($order->woocommerce_order_id)
                ? (int)$order->woocommerce_order_id
                : (int)preg_replace('/[^0-9]/', '', (string)($order->invoice_no ?: $order->unique_id));

            if (!empty($wcOrderId)) {
                $wcStatusMap = [
                    'Processing'           => 'processing',
                    'Pending'              => 'pending',
                    'On Hold'              => 'on-hold',
                    'Completed'            => 'completed',
                    'Cancelled'            => 'cancelled',
                    'Returned'             => 'refunded',
                    'Failed'               => 'failed',
                    'Approved'             => 'approved',
                    'Good But No Response' => 'on-hold',
                    'No Response'          => 'on-hold',
                    'Advance Payment'      => 'processing',
                    'In Courier'           => 'processing',
                    'Delivered'            => 'completed',
                ];
                $wcStatus = $wcStatusMap[$request->order_status] ?? strtolower($request->order_status);
                try {
                    $syncRes = $this->wooService->updateOrderStatus($wcOrderId, $wcStatus);
                    if (!($syncRes['success'] ?? false)) {
                        \Illuminate\Support\Facades\Log::warning("WooCommerce Remote Sync Failed for #{$wcOrderId}: " . ($syncRes['message'] ?? 'Unknown'));
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("WooCommerce Remote Sync Exception for #{$wcOrderId}: " . $e->getMessage());
                }
            }
        }

        // Recalculate totals
        $itemsSubtotal = $order->invoiceItems->sum('subtotal');
        $order->total_amount = max(0, $itemsSubtotal + (float)$order->delivery_charge - (float)$order->discount_amount);
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Web order details updated successfully!',
                'total_amount' => number_format($order->total_amount, 2)
            ]);
        }

        return redirect()->back()->with(['message' => 'Web order details updated successfully!', 'alert-type' => 'success']);
    }

    /**
     * Update quantity or rate of an invoice item
     */
    public function updateOrderItem(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'qty' => 'required|numeric|min:1',
            'rate' => 'nullable|numeric|min:0',
        ]);

        $order = Invoice::with('invoiceItems')->findOrFail($id);
        $item = InvoiceItem::where('invoice_id', $order->id)->where('id', $request->item_id)->firstOrFail();

        $qty = (float)$request->qty;
        $rate = $request->filled('rate') ? (float)$request->rate : (float)$item->rate;
        $subtotal = $qty * $rate;

        $item->main_qty = $qty;
        $item->actual_main = $qty;
        $item->rate = $rate;
        $item->subtotal = $subtotal;
        $item->actual_total = $subtotal;
        $item->inv_subtotal = $subtotal;
        $item->save();

        $order->load('invoiceItems');
        $itemsSubtotal = $order->invoiceItems->sum('subtotal');
        $order->total_amount = max(0, $itemsSubtotal + (float)$order->delivery_charge - (float)$order->discount_amount);
        $order->save();

        return response()->json([
            'success' => true,
            'item_subtotal' => number_format($subtotal, 2, '.', ''),
            'items_subtotal' => number_format($itemsSubtotal, 2, '.', ''),
            'grand_total' => number_format($order->total_amount, 2, '.', ''),
            'message' => 'Product quantity updated successfully!'
        ]);
    }

    /**
     * Add a product to the invoice
     */
    public function addOrderItem(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|numeric|min:1',
        ]);

        $order = Invoice::findOrFail($id);
        $product = Product::findOrFail($request->product_id);

        $qty = (float)($request->qty ?? 1);
        $rate = (float)($product->selling_price ?: $product->purchase_price ?: 0);
        $subtotal = $qty * $rate;

        $existingItem = InvoiceItem::where('invoice_id', $order->id)->where('product_id', $product->id)->first();
        if ($existingItem) {
            $existingItem->main_qty += $qty;
            $existingItem->actual_main += $qty;
            $existingItem->subtotal = $existingItem->main_qty * $existingItem->rate;
            $existingItem->actual_total = $existingItem->subtotal;
            $existingItem->inv_subtotal = $existingItem->subtotal;
            $existingItem->save();
        } else {
            InvoiceItem::create([
                'invoice_id' => $order->id,
                'product_id' => $product->id,
                'rate' => $rate,
                'main_qty' => $qty,
                'subtotal' => $subtotal,
                'actual_main' => $qty,
                'actual_total' => $subtotal,
                'inv_subtotal' => $subtotal,
                'pur_subtotal' => ((float)($product->purchase_price ?: $rate)) * $qty,
                'date' => date('Y-m-d'),
                'status' => 1
            ]);
        }

        $order->load('invoiceItems');
        $itemsSubtotal = $order->invoiceItems->sum('subtotal');
        $order->total_amount = max(0, $itemsSubtotal + (float)$order->delivery_charge - (float)$order->discount_amount);
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to order successfully!'
            ]);
        }

        return redirect()->back()->with(['message' => 'Product added to order successfully!', 'alert-type' => 'success']);
    }

    /**
     * Remove an item from the invoice
     */
    public function removeOrderItem(Request $request, $id, $itemId)
    {
        $order = Invoice::findOrFail($id);
        $item = InvoiceItem::where('invoice_id', $order->id)->where('id', $itemId)->firstOrFail();
        $item->delete();

        $order->load('invoiceItems');
        $itemsSubtotal = $order->invoiceItems->sum('subtotal');
        $order->total_amount = max(0, $itemsSubtotal + (float)$order->delivery_charge - (float)$order->discount_amount);
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'items_subtotal' => number_format($itemsSubtotal, 2, '.', ''),
                'grand_total' => number_format($order->total_amount, 2, '.', ''),
                'message' => 'Product removed from order!'
            ]);
        }

        return redirect()->back()->with(['message' => 'Product removed from order!', 'alert-type' => 'success']);
    }

    /**
     * Add a note to the order
     */
    public function addOrderNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $order = Invoice::findOrFail($id);
        $userNote = trim($request->note);
        $order->note = $order->note ? ($order->note . "\n" . $userNote) : $userNote;
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'note' => $order->note,
                'message' => 'Note added successfully!'
            ]);
        }

        return redirect()->back()->with(['message' => 'Note added successfully!', 'alert-type' => 'success']);
    }

    /**
     * AJAX Product search for live picker
     */
    public function ajaxProductSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $products = Product::where('status', 1)
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('barcode', 'like', "%{$q}%")
                      ->orWhere('woocommerce_product_id', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get();

        $results = $products->map(function($product) {
            $image = $product->images;
            if ($image && !str_starts_with($image, 'http')) {
                $image = asset($image);
            }
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->barcode ?: ($product->woocommerce_product_id ?: $product->id),
                'price' => (float)($product->selling_price ?: $product->purchase_price ?: 0),
                'stock' => (float)($product->main_qty ?? 0),
                'image' => $image ?: asset('backend/images/no_images.png'),
            ];
        });

        return response()->json($results);
    }

    /**
     * Display Logistics Scanner Console view
     */
    public function scannerView(Request $request)
    {
        $mode = $request->input('mode', 'shipping'); // shipping, return, rts
        $today = date('Y-m-d');
        
        $sessionStats = [
            'shipping' => Invoice::whereDate('updated_at', $today)->whereIn('order_status', ['Shipping', 'shipping'])->count(),
            'return'   => Invoice::whereDate('updated_at', $today)->whereIn('order_status', ['Returned', 'returned'])->count(),
            'rts'      => Invoice::whereDate('updated_at', $today)->whereIn('order_status', ['RTS', 'rts'])->count(),
        ];

        return view('backend.woocommerce.scanner', compact('mode', 'sessionStats'));
    }

    /**
     * Process high-speed barcode / invoice scan for status changes
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'mode'    => 'required|string|in:shipping,return,rts',
        ]);

        $barcode = trim($request->barcode);
        $mode = strtolower($request->mode);

        $cleanBarcode = trim(preg_replace('/^#/', '', $barcode));

        $invoice = Invoice::with(['customer', 'invoiceItems.product'])
            ->where(function($q) use ($cleanBarcode) {
                $q->where('invoice_no', $cleanBarcode)
                  ->orWhere('invoice_no', 'WC-' . $cleanBarcode)
                  ->orWhere('woocommerce_order_id', $cleanBarcode)
                  ->orWhere('unique_id', $cleanBarcode)
                  ->orWhere('consignment_id', $cleanBarcode);

                if (strlen($cleanBarcode) >= 10) {
                    $q->orWhereHas('customer', function($cq) use ($cleanBarcode) {
                        $cq->where('phone', 'like', "%{$cleanBarcode}%");
                    });
                }
            })
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'status'  => 'not_found',
                'barcode' => $barcode,
                'message' => "Order #{$barcode} not found in database.",
            ]);
        }

        $targetStatusMap = [
            'shipping' => 'Shipping',
            'return'   => 'Returned',
            'rts'      => 'RTS',
        ];
        $targetStatus = $targetStatusMap[$mode] ?? 'Shipping';
        $prevStatus = $invoice->order_status ?: 'Processing';

        // Check if duplicate / already updated
        if (strcasecmp($prevStatus, $targetStatus) === 0) {
            return response()->json([
                'success'     => true,
                'status'      => 'duplicate',
                'barcode'     => $barcode,
                'invoice_no'  => $invoice->woocommerce_order_id ?: ($invoice->invoice_no ?: $invoice->id),
                'order_id'    => $invoice->id,
                'customer'    => $invoice->customer->name ?? 'Customer',
                'phone'       => $invoice->customer->phone ?? '-',
                'courier'     => $invoice->courier_type ?: 'Steadfast',
                'amount'      => (float)$invoice->total_amount,
                'prev_status' => $prevStatus,
                'new_status'  => $targetStatus,
                'message'     => "Order #{$invoice->invoice_no} is ALREADY in {$targetStatus}!",
                'time'        => date('h:i:s A'),
            ]);
        }

        // Synchronize inventory stock and IMEI serials on status change (e.g. Return restores stock, Shipping deducts)
        syncInvoiceStockForStatusChange($invoice, $targetStatus, $prevStatus);

        // Update invoice status
        $invoice->order_status = $targetStatus;
        if (empty($invoice->sale_type) || $invoice->sale_type === 'WooCommerce') {
            $invoice->sale_type = 'Online';
        }

        if ($targetStatus === 'Returned') {
            $invoice->status = 2;
        } elseif ($targetStatus === 'Shipping') {
            $invoice->status = 1;
        } elseif ($targetStatus === 'RTS') {
            $invoice->status = 1;
        }

        $invoice->save();

        $wcOrderId = !empty($invoice->woocommerce_order_id)
            ? (int)$invoice->woocommerce_order_id
            : (int)preg_replace('/[^0-9]/', '', (string)($invoice->invoice_no ?: $invoice->unique_id));

        if (!empty($wcOrderId)) {
            $wcStatusMap = [
                'Shipping' => 'processing',
                'Returned' => 'refunded',
                'RTS'      => 'processing',
                'Approved' => 'approved',
            ];
            $wcStatus = $wcStatusMap[$targetStatus] ?? strtolower($targetStatus);
            try {
                $syncRes = $this->wooService->updateOrderStatus($wcOrderId, $wcStatus);
                if (!($syncRes['success'] ?? false)) {
                    \Illuminate\Support\Facades\Log::warning("WooCommerce Remote Sync Failed for #{$wcOrderId}: " . ($syncRes['message'] ?? 'Unknown'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("WooCommerce Remote Sync Exception for #{$wcOrderId}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success'     => true,
            'status'      => 'updated',
            'barcode'     => $barcode,
            'invoice_no'  => $invoice->woocommerce_order_id ?: ($invoice->invoice_no ?: $invoice->id),
            'order_id'    => $invoice->id,
            'customer'    => $invoice->customer->name ?? 'Customer',
            'phone'       => $invoice->customer->phone ?? '-',
            'courier'     => $invoice->courier_type ?: 'Steadfast',
            'amount'      => (float)$invoice->total_amount,
            'prev_status' => $prevStatus,
            'new_status'  => $targetStatus,
            'message'     => "Order #{$invoice->invoice_no} successfully updated to {$targetStatus}!",
            'time'        => date('h:i:s A'),
        ]);
    }

    /**
     * Search Orders Console View
     */
    public function searchOrdersView(Request $request)
    {
        $queryText = trim($request->input('q', ''));
        $field = $request->input('field', 'all'); // all, invoice, mobile, name, courier, transaction
        $hasSearched = !empty($queryText);
        $orders = collect();
        $customerStats = [];

        if ($hasSearched) {
            $baseQuery = Invoice::with(['customer', 'invoiceItems.product']);

            // Apply field filter
            switch ($field) {
                case 'invoice':
                    $cleanQ = preg_replace('/^#/', '', $queryText);
                    $baseQuery->where(function($q) use ($cleanQ, $queryText) {
                        $q->where('invoice_no', 'like', "%{$cleanQ}%")
                          ->orWhere('woocommerce_order_id', 'like', "%{$cleanQ}%")
                          ->orWhere('unique_id', 'like', "%{$cleanQ}%");
                        if (is_numeric($cleanQ)) {
                            $q->orWhere('id', (int)$cleanQ);
                        }
                    });
                    break;

                case 'mobile':
                    $cleanPhone = preg_replace('/[^0-9]/', '', $queryText);
                    $baseQuery->whereHas('customer', function($cq) use ($cleanPhone, $queryText) {
                        $cq->where('phone', 'like', "%{$cleanPhone}%")
                          ->orWhere('phone', 'like', "%{$queryText}%");
                    });
                    break;

                case 'name':
                    $baseQuery->whereHas('customer', function($cq) use ($queryText) {
                        $cq->where('name', 'like', "%{$queryText}%");
                    });
                    break;

                case 'courier':
                    $baseQuery->where(function($q) use ($queryText) {
                        $q->where('consignment_id', 'like', "%{$queryText}%")
                          ->orWhere('courier_type', 'like', "%{$queryText}%");
                    });
                    break;

                case 'transaction':
                    $baseQuery->where(function($q) use ($queryText) {
                        $q->where('ref_no', 'like', "%{$queryText}%")
                          ->orWhere('unique_id', 'like', "%{$queryText}%")
                          ->orWhere('description', 'like', "%{$queryText}%");
                    });
                    break;

                case 'all':
                default:
                    $cleanQ = preg_replace('/^#/', '', $queryText);
                    $cleanPhone = preg_replace('/[^0-9]/', '', $queryText);
                    $baseQuery->where(function($q) use ($queryText, $cleanQ, $cleanPhone) {
                        $q->where('invoice_no', 'like', "%{$cleanQ}%")
                          ->orWhere('woocommerce_order_id', 'like', "%{$cleanQ}%")
                          ->orWhere('unique_id', 'like', "%{$cleanQ}%")
                          ->orWhere('consignment_id', 'like', "%{$queryText}%")
                          ->orWhere('ref_no', 'like', "%{$queryText}%")
                          ->orWhere('note', 'like', "%{$queryText}%")
                          ->orWhereHas('customer', function($cq) use ($queryText, $cleanPhone) {
                              $cq->where('name', 'like', "%{$queryText}%");
                              if (strlen($cleanPhone) >= 3) {
                                  $cq->orWhere('phone', 'like', "%{$cleanPhone}%");
                              }
                          })
                          ->orWhereHas('invoiceItems', function($iq) use ($queryText) {
                              $iq->where('new_details', 'like', "%{$queryText}%")
                                ->orWhereHas('product', function($pq) use ($queryText) {
                                    $pq->where('name', 'like', "%{$queryText}%")
                                      ->orWhere('barcode', 'like', "%{$queryText}%");
                                });
                          });
                        if (is_numeric($cleanQ)) {
                            $q->orWhere('id', (int)$cleanQ);
                        }
                    });
                    break;
            }

            $orders = $baseQuery->orderBy('id', 'desc')->paginate(20)->appends($request->all());

            // Compute dynamic customer stats
            $fraudService = app(\App\Services\CourierFraudCheckerService::class);
            $customerIds = $orders->pluck('customer_id')->filter()->unique();
            $invoicesByCustomer = Invoice::whereIn('customer_id', $customerIds)
                ->select('id', 'customer_id', 'order_status', 'status')
                ->get()
                ->groupBy('customer_id');

            foreach ($orders as $order) {
                $custInvoices = $invoicesByCustomer->get($order->customer_id, collect());
                $totalLocal = $custInvoices->count();
                $cancelledLocal = $custInvoices->filter(function($inv) {
                    return in_array($inv->order_status, ['Cancelled', 'cancelled', 'Returned', 'returned', 'failed']) || in_array($inv->status, [2, 3]);
                })->count();
                $deliveredLocal = max(0, $totalLocal - $cancelledLocal);

                if ($totalLocal <= 1 && $cancelledLocal == 0) {
                    $rate = 100;
                    $rating = 80;
                    $totalDisplay = 1;
                    $deliveredDisplay = 1;
                } else {
                    $rate = $totalLocal > 0 ? round(($deliveredLocal / $totalLocal) * 100) : 100;
                    $rating = min(99, max(25, round(($rate * 0.7) + min(25, $deliveredLocal * 4) - ($cancelledLocal * 12))));
                    $totalDisplay = $totalLocal;
                    $deliveredDisplay = $deliveredLocal;
                }

                $customerStats[$order->id] = [
                    'total' => $totalDisplay,
                    'delivered' => $deliveredDisplay,
                    'cancelled' => $cancelledLocal,
                    'rate' => $rate,
                    'rating' => $rating,
                ];
            }
        }

        return view('backend.woocommerce.search', compact('orders', 'queryText', 'field', 'customerStats', 'hasSearched'));
    }

    /**
     * Search Orders Live AJAX API
     */
    public function searchOrdersAjax(Request $request)
    {
        $queryText = trim($request->input('q', ''));
        $field = $request->input('field', 'all');

        if (empty($queryText)) {
            return response()->json(['success' => true, 'total' => 0, 'data' => []]);
        }

        $baseQuery = Invoice::with(['customer', 'invoiceItems.product']);

        switch ($field) {
            case 'invoice':
                $cleanQ = preg_replace('/^#/', '', $queryText);
                $baseQuery->where(function($q) use ($cleanQ) {
                    $q->where('invoice_no', 'like', "%{$cleanQ}%")
                      ->orWhere('woocommerce_order_id', 'like', "%{$cleanQ}%")
                      ->orWhere('unique_id', 'like', "%{$cleanQ}%");
                });
                break;

            case 'mobile':
                $cleanPhone = preg_replace('/[^0-9]/', '', $queryText);
                $baseQuery->whereHas('customer', function($cq) use ($cleanPhone) {
                    $cq->where('phone', 'like', "%{$cleanPhone}%");
                });
                break;

            case 'name':
                $baseQuery->whereHas('customer', function($cq) use ($queryText) {
                    $cq->where('name', 'like', "%{$queryText}%");
                });
                break;

            case 'courier':
                $baseQuery->where('consignment_id', 'like', "%{$queryText}%")
                          ->orWhere('courier_type', 'like', "%{$queryText}%");
                break;

            case 'transaction':
                $baseQuery->where('ref_no', 'like', "%{$queryText}%")
                          ->orWhere('unique_id', 'like', "%{$queryText}%");
                break;

            default:
                $cleanQ = preg_replace('/^#/', '', $queryText);
                $cleanPhone = preg_replace('/[^0-9]/', '', $queryText);
                $baseQuery->where(function($q) use ($queryText, $cleanQ, $cleanPhone) {
                    $q->where('invoice_no', 'like', "%{$cleanQ}%")
                      ->orWhere('woocommerce_order_id', 'like', "%{$cleanQ}%")
                      ->orWhere('consignment_id', 'like', "%{$queryText}%")
                      ->orWhereHas('customer', function($cq) use ($queryText, $cleanPhone) {
                          $cq->where('name', 'like', "%{$queryText}%");
                          if (strlen($cleanPhone) >= 3) {
                              $cq->orWhere('phone', 'like', "%{$cleanPhone}%");
                          }
                      });
                });
                break;
        }

        $results = $baseQuery->orderBy('id', 'desc')->limit(10)->get();

        $formatted = $results->map(function($order) {
            $firstItem = $order->invoiceItems->first();
            $product = $firstItem?->product;
            $productImage = $product?->images ?: 'backend/images/no_images.png';
            if (!str_starts_with($productImage, 'http')) {
                $productImage = asset($productImage);
            }

            return [
                'id' => $order->id,
                'invoice_no' => $order->woocommerce_order_id ?: ($order->invoice_no ?: $order->id),
                'date' => date('d M, h:i A', strtotime($order->created_at ?: $order->date)),
                'customer_name' => $order->customer->name ?? 'Customer',
                'customer_phone' => $order->customer->phone ?? '-',
                'status' => $order->order_status ?: 'Processing',
                'courier' => $order->courier_type ?: 'Steadfast',
                'tracking_id' => $order->consignment_id ?: '-',
                'total_amount' => (float)$order->total_amount,
                'product_name' => $product?->name ?? ($firstItem?->new_details ?? 'Product Item'),
                'product_image' => $productImage,
                'details_url' => route('woocommerce.orders.details', $order->id),
            ];
        });

        return response()->json(['success' => true, 'total' => $results->count(), 'data' => $formatted]);
    }

    /**
     * Bulk Push / Sync Order Statuses from POS to WooCommerce remote store
     */
    public function pushStatusesToWooCommerce(Request $request)
    {
        $rawIds = $request->input('order_ids', $request->input('order_ids[]', []));
        $ids = [];
        if (is_array($rawIds)) {
            $ids = array_values(array_filter($rawIds));
        } elseif (is_string($rawIds) && trim($rawIds) !== '') {
            $decoded = json_decode($rawIds, true);
            $ids = is_array($decoded) ? array_values(array_filter($decoded)) : array_values(array_filter(explode(',', $rawIds)));
        }

        $query = Invoice::query();
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        } else {
            // If no specific IDs selected, sync all recent WooCommerce / Online orders that have WooCommerce ID
            $query->where(function($q) {
                if (Schema::hasColumn('invoices', 'woocommerce_order_id')) {
                    $q->whereNotNull('woocommerce_order_id')
                      ->orWhere('sale_type', 'WooCommerce')
                      ->orWhere('unique_id', 'like', 'WC-%');
                } else {
                    $q->where('sale_type', 'WooCommerce')
                      ->orWhere('unique_id', 'like', 'WC-%');
                }
            });
            $query->orderBy('id', 'desc')->limit(50);
        }

        $invoices = $query->get();
        $batchItems = [];

        foreach ($invoices as $inv) {
            $wcOrderId = !empty($inv->woocommerce_order_id)
                ? (int)$inv->woocommerce_order_id
                : (int)preg_replace('/[^0-9]/', '', (string)($inv->invoice_no ?: $inv->unique_id));

            if ($wcOrderId > 0 && !empty($inv->order_status)) {
                $batchItems[] = [
                    'id' => $wcOrderId,
                    'status' => $inv->order_status
                ];
            }
        }

        if (empty($batchItems)) {
            $msg = 'No WooCommerce orders found to sync.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with(['message' => $msg, 'alert-type' => 'warning']);
        }

        $result = $this->wooService->updateOrderStatusBatch($batchItems);

        if ($request->ajax() || $request->wantsJson()) {
            if (!($result['success'] ?? false)) {
                $creds = $this->wooService->getCredentials();
                $result['fallback_needed'] = true;
                $result['items'] = $batchItems;
                $result['credentials'] = [
                    'url' => $creds['url'],
                    'consumer_key' => $creds['consumer_key'],
                    'consumer_secret' => $creds['consumer_secret'],
                ];
            }
            return response()->json($result);
        }

        $alertType = ($result['success'] ?? false) ? 'success' : 'error';
        return redirect()->back()->with([
            'message' => $result['message'] ?? 'WooCommerce status sync completed.',
            'alert-type' => $alertType
        ]);
    }
}