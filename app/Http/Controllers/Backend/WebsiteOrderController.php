<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\WebsiteOrder;
use App\Models\WebsiteOrderItem;
use App\Services\CourierFraudCheckerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebsiteOrderController extends Controller
{
    protected $fraudChecker;

    public function __construct(CourierFraudCheckerService $fraudChecker)
    {
        $this->fraudChecker = $fraudChecker;
    }

    /**
     * Display a listing of the website orders.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'processing');
        $search = $request->get('search');
        $courierFilter = $request->get('courier_service');

        // Status counts for top carousel pills
        $counts = [
            'processing' => WebsiteOrder::where(function($q) {
                $q->whereIn('status', ['processing', 'pending'])
                  ->orWhereIn(DB::raw('LOWER(order_status)'), ['processing', 'pending']);
            })->count(),
            'good_no_response' => WebsiteOrder::where(function($q) {
                $q->where('status', 'good_no_response')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'good_no_response');
            })->count(),
            'no_response' => WebsiteOrder::where(function($q) {
                $q->where('status', 'no_response')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'no_response');
            })->count(),
            'advance_payment' => WebsiteOrder::where(function($q) {
                $q->where('status', 'advance_payment')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'advance_payment');
            })->count(),
            'on_hold' => WebsiteOrder::where(function($q) {
                $q->where('status', 'on_hold')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'on_hold');
            })->count(),
            'approved' => WebsiteOrder::where(function($q) {
                $q->where('status', 'approved')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'approved');
            })->count(),
            'delivered' => WebsiteOrder::where(function($q) {
                $q->where('status', 'delivered')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'delivered');
            })->count(),
            'cancelled' => WebsiteOrder::where(function($q) {
                $q->where('status', 'cancelled')
                  ->orWhere(DB::raw('LOWER(order_status)'), 'cancelled');
            })->count(),
            'all' => WebsiteOrder::count(),
        ];

        $query = WebsiteOrder::with(['items.product.unit', 'items.variation', 'invoice'])
            ->orderBy('created_at', 'desc');

        // Filter by Status (unless 'all')
        if (!empty($status) && $status !== 'all') {
            if ($status === 'processing') {
                $query->where(function($q) {
                    $q->whereIn('status', ['processing', 'pending'])
                      ->orWhereIn(DB::raw('LOWER(order_status)'), ['processing', 'pending']);
                });
            } else {
                $query->where(function($q) use ($status) {
                    $q->where('status', $status)
                      ->orWhere(DB::raw('LOWER(order_status)'), strtolower($status));
                });
            }
        }

        // Search query
        if (!empty($search)) {
            $searchVal = trim($search);
            $query->where(function ($q) use ($searchVal) {
                $q->where('order_no', 'like', "%{$searchVal}%")
                  ->orWhere('customer_name', 'like', "%{$searchVal}%")
                  ->orWhere('customer_phone', 'like', "%{$searchVal}%")
                  ->orWhere('phone', 'like', "%{$searchVal}%")
                  ->orWhere('customer_address', 'like', "%{$searchVal}%")
                  ->orWhere('delivery_address', 'like', "%{$searchVal}%")
                  ->orWhereHas('items', function ($itemQ) use ($searchVal) {
                      $itemQ->where('product_name', 'like', "%{$searchVal}%")
                            ->orWhere('product_code', 'like', "%{$searchVal}%")
                            ->orWhere('product_barcode', 'like', "%{$searchVal}%");
                  });
            });
        }

        if (!empty($courierFilter)) {
            $query->where('courier_service', $courierFilter);
        }

        $orders = $query->paginate(20)->appends($request->all());

        // Attach courier fraud check stats for each order's customer phone
        $orders->getCollection()->transform(function ($order) {
            try {
                $order->fraud_stats = $this->fraudChecker->checkFraud($order->customer_phone);
            } catch (\Exception $e) {
                $order->fraud_stats = [
                    'overall_success_rate' => 0,
                    'total_parcels' => 0,
                    'total_delivered' => 0,
                    'total_cancelled' => 0,
                    'risk_level' => 'new_customer',
                    'risk_label' => 'New Customer',
                    'badge_class' => 'secondary',
                ];
            }

            // Also check stock availability for each item in the order
            $canApprove = true;
            $stockDetails = [];
            foreach ($order->items as $item) {
                $prod = $item->product;
                if ($prod && $prod->is_service == 0) {
                    $stockQ = PurchaseItem::where('product_id', $item->product_id)
                        ->where('stock_qty', '>', 0);
                    if ($item->product_variation_id) {
                        $stockQ->where('product_variation_id', $item->product_variation_id);
                    }
                    $available = (float) $stockQ->sum('stock_qty');
                    $required = (float) $item->quantity;
                    $itemInStock = $available >= $required;
                    if (!$itemInStock) {
                        $canApprove = false;
                    }
                    $stockDetails[] = [
                        'item_id' => $item->id,
                        'product_name' => $item->product_name,
                        'available' => $available,
                        'required' => $required,
                        'in_stock' => $itemInStock,
                    ];
                } else {
                    $stockDetails[] = [
                        'item_id' => $item->id,
                        'product_name' => $item->product_name,
                        'available' => 9999,
                        'required' => (float) $item->quantity,
                        'in_stock' => true,
                    ];
                }
            }
            $order->can_approve = $canApprove;
            $order->stock_details = $stockDetails;

            return $order;
        });

        return view('backend.pages.website_orders.index', compact('orders', 'counts', 'status', 'search'));
    }

    /**
     * Check real-time stock for an order's items.
     */
    public function checkStock($id)
    {
        $order = WebsiteOrder::with('items.product')->findOrFail($id);
        $itemsStock = [];
        $allInStock = true;

        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->is_service == 0) {
                $stockQ = PurchaseItem::where('product_id', $item->product_id)
                    ->where('stock_qty', '>', 0);
                if ($item->product_variation_id) {
                    $stockQ->where('product_variation_id', $item->product_variation_id);
                }
                $available = (float) $stockQ->sum('stock_qty');
                $required = (float) $item->quantity;
                $inStock = $available >= $required;
                if (!$inStock) {
                    $allInStock = false;
                }
                $itemsStock[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'required' => $required,
                    'available' => $available,
                    'in_stock' => $inStock,
                ];
            } else {
                $itemsStock[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'required' => (float) $item->quantity,
                    'available' => 9999,
                    'in_stock' => true,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'can_approve' => $allInStock,
            'items' => $itemsStock,
        ]);
    }

    /**
     * Update the status of a Website Order.
     * If approved, checks stock first, then deducts via FIFO and creates an Invoice in Online Sale List with status 'processing'.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order = WebsiteOrder::with(['items.product', 'items.variation'])->findOrFail($id);
        $newStatus = strtolower(trim($request->status));
        $userBranchId = auth()->user()->branch_id ?? 1;

        // If order is already approved, allow cancelling it with stock restoration and Meta CAPI cancellation
        if ($order->status === 'approved') {
            if ($newStatus === 'cancelled') {
                if (!empty($order->invoice_id)) {
                    $inv = Invoice::find($order->invoice_id);
                    if ($inv) {
                        if (function_exists('syncInvoiceStockForStatusChange')) {
                            syncInvoiceStockForStatusChange($inv, 'Cancelled', $inv->order_status);
                        }
                        $inv->order_status = 'Cancelled';
                        $inv->save();
                    }
                }
                $order->update([
                    'status' => 'cancelled',
                    'order_status' => 'Cancelled',
                ]);

                // Dispatch Meta Conversions API Cancellation Event
                try {
                    \App\Services\MetaCapiService::sendEvent(
                        'OrderCancelled',
                        [
                            'name' => $order->customer_name,
                            'phone' => $order->phone ?: $order->customer_phone,
                            'city' => $order->city ?: $order->delivery_address,
                            'country' => 'bd',
                        ],
                        [
                            'currency' => 'BDT',
                            'value' => (float) $order->total_amount,
                            'order_id' => (string) $order->order_no,
                            'status' => 'cancelled',
                            'reason' => 'Cancelled by store admin',
                        ],
                        'cancel_' . $order->order_no . '_' . time()
                    );
                    Log::info("Meta CAPI OrderCancelled Dispatched for Order #{$order->order_no}");
                } catch (\Throwable $ce) {
                    Log::warning("Meta CAPI Cancel Event Error: " . $ce->getMessage());
                }

                $cancelMsg = "অর্ডার #{$order->order_no} বাতিল (Cancelled) করা হয়েছে, স্টক রিস্টোর করা হয়েছে এবং মেটাতে ক্যানসেলেশন আপডেট পাঠানো হয়েছে।";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $cancelMsg,
                        'status' => 'cancelled',
                    ]);
                }
                return redirect()->back()->with('success', $cancelMsg);
            }

            $msg = 'Approved অর্ডার কেবল "Cancelled" করা যাবে, অন্য স্ট্যাটাসে পরিবর্তন করা যাবে না।';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        // If transitioning to 'approved'
        if ($newStatus === 'approved') {
            // 1. Stock check: Ensure every physical product has sufficient stock
            $insufficientItems = [];
            foreach ($order->items as $item) {
                $prod = $item->product;
                if ($prod && $prod->is_service == 0) {
                    $stockQuery = PurchaseItem::where('product_id', $item->product_id)
                        ->where('stock_qty', '>', 0);
                    if ($item->product_variation_id) {
                        $stockQuery->where('product_variation_id', $item->product_variation_id);
                    }
                    $available = (float) $stockQuery->sum('stock_qty');
                    $required = (float) $item->quantity;

                    if ($available < $required) {
                        $insufficientItems[] = "{$item->product_name} (প্রয়োজন: {$required}, মজুদ: {$available})";
                    }
                }
            }

            if (!empty($insufficientItems)) {
                $msg = "স্টক পর্যাপ্ত নেই! নিম্নের পণ্যগুলো প্রথমে পারচেজ (Purchase) করুন:\n- " . implode("\n- ", $insufficientItems);
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg,
                        'insufficient_items' => $insufficientItems,
                    ], 422);
                }
                return redirect()->back()->with('error', $msg);
            }

            // 2. Perform DB Transaction: Deduct FIFO stock, create/find Customer, create Invoice & InvoiceItems
            try {
                DB::transaction(function () use ($order, $userBranchId) {
                    // Find or create customer
                    $cleanPhone = preg_replace('/[^\d]/', '', $order->customer_phone);
                    if (strlen($cleanPhone) > 11) {
                        $cleanPhone = substr($cleanPhone, -11);
                    }

                    $customer = Customer::where('phone', $order->customer_phone)
                        ->orWhere('phone', $cleanPhone)
                        ->first();

                    if (!$customer) {
                        $customer = Customer::create([
                            'name' => $order->customer_name,
                            'phone' => $order->customer_phone,
                            'address' => $order->customer_address,
                            'branch_id' => $userBranchId,
                            'status' => 1,
                        ]);
                    }

                    // Generate Invoice Number
                    $last_invoice = Invoice::orderBy('id', 'desc')->first();
                    $nextNumber = 1;
                    if ($last_invoice) {
                        $digits = preg_replace('/\D/', '', $last_invoice->invoice_no);
                        $nextNumber = ((int) $digits) + 1;
                    }
                    $invoice_no = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
                    while (Invoice::where('invoice_no', $invoice_no)->exists()) {
                        $nextNumber++;
                        $invoice_no = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
                    }

                    // Create Invoice in Online Sale List with status 'processing'
                    $invoice = new Invoice();
                    $invoice->date = Carbon::now()->toDateString();
                    $invoice->unique_id = uniqid();
                    $invoice->invoice_no = $invoice_no;
                    $invoice->customer_id = $customer->id;
                    $invoice->branch_id = $userBranchId;
                    $invoice->sale_type = 'Online';
                    $invoice->order_status = 'processing';
                    $rawService = strtolower(trim($order->courier_service ?? $order->delivery_method ?? ''));
                    $invoice->courier_type = str_contains($rawService, 'pathao') ? 'Pathao' : 'Steadfast';
                    $invoice->total_amount = $order->total_amount;
                    $invoice->delivery_charge = $order->shipping_fee ?? 0;
                    $invoice->discount = $order->discount ?? 0;
                    $invoice->discount_amount = $order->discount ?? 0;
                    $invoice->total_paid = 0;
                    $invoice->total_due = $order->total_amount;
                    $invoice->note = $order->note;
                    $invoice->source_link = $order->order_no;
                    $invoice->status = 1;
                    $invoice->created_by = auth()->id() ?? 1;
                    $invoice->save();

                    // Create InvoiceItems and deduct FIFO stock
                    foreach ($order->items as $item) {
                        $invoice_item = new InvoiceItem();
                        $invoice_item->invoice_id = $invoice->id;
                        $invoice_item->product_id = $item->product_id;
                        $invoice_item->product_variation_id = $item->product_variation_id;
                        $invoice_item->branch_id = $userBranchId;
                        $invoice_item->rate = $item->unit_price;
                        $invoice_item->main_qty = $item->quantity;
                        $invoice_item->actual_main = $item->quantity;
                        $invoice_item->subtotal = $item->subtotal;
                        $invoice_item->actual_total = $item->subtotal;
                        $invoice_item->inv_subtotal = $item->subtotal;

                        $prod = $item->product;
                        if ($prod && $prod->is_service == 0) {
                            $purSubtotal = calculateUnitPriceUsingFIFO(
                                $item->product_id,
                                $item->quantity,
                                $item->product_variation_id,
                                $userBranchId
                            );
                            $invoice_item->pur_subtotal = $purSubtotal;
                        } else {
                            $invoice_item->pur_subtotal = 0;
                        }
                        $invoice_item->save();
                    }

                    // Mark order approved and link invoice
                    $order->update([
                        'status' => 'approved',
                        'invoice_id' => $invoice->id,
                    ]);
                });

                // Dispatch Meta Conversions API (Server-Side) Purchase Event ONLY on Approval!
                try {
                    $order->loadMissing('items');
                    \App\Services\MetaCapiService::trackPurchase($order, 'purchase_' . $order->order_no);
                    Log::info("Meta CAPI Purchase Event Dispatched on Order Approval #{$order->order_no}");
                } catch (\Throwable $capiErr) {
                    Log::warning("Meta CAPI Purchase tracking error on approve: " . $capiErr->getMessage());
                }

                // Generate dataLayer payload for order_confirm event
                $orderDataLayer = $this->buildOrderDataLayerPayload($order);
                session()->flash('approved_order_datalayer', $orderDataLayer);

                $successMsg = "অর্ডার {$order->order_no} সফলভাবে অনুমোদিত হয়েছে এবং অনলাইন সেল লিস্টে (Processing) যুক্ত করা হয়েছে।";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMsg,
                        'status' => 'approved',
                        'order_datalayer' => $orderDataLayer,
                    ]);
                }
                return redirect()->back()
                    ->with('success', $successMsg)
                    ->with('approved_order_datalayer', $orderDataLayer);

            } catch (\Exception $e) {
                Log::error("Error approving website order #{$order->id}: " . $e->getMessage());
                $errMsg = "অনুমোদন ব্যর্থ হয়েছে: " . $e->getMessage();
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errMsg], 500);
                }
                return redirect()->back()->with('error', $errMsg);
            }
        }

        // Other status changes (processing, good_no_response, no_response, advance_payment, on_hold, delivered, cancelled)
        $order->update([
            'status' => $newStatus,
            'order_status' => ucfirst($newStatus),
        ]);

        $statusLabels = [
            'processing' => 'Processing',
            'good_no_response' => 'Good But No Response',
            'no_response' => 'No Response',
            'advance_payment' => 'Advance Payment',
            'on_hold' => 'On Hold',
            'approved' => 'Approved',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        $msg = "অর্ডারের স্ট্যাটাস '{$label}' এ পরিবর্তন করা হয়েছে।";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'status' => $newStatus,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Update order note.
     */
    public function updateNote(Request $request, $id)
    {
        $order = WebsiteOrder::findOrFail($id);
        $order->update([
            'note' => $request->input('note'),
            'note_updated_at' => Carbon::now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'নোট সফলভাবে আপডেট করা হয়েছে।',
                'note' => $order->note,
                'updated_time' => Carbon::now()->diffForHumans(),
            ]);
        }

        return redirect()->back()->with('success', 'নোট আপডেট করা হয়েছে।');
    }

    /**
     * Bulk update status or perform bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $orderIds = $request->input('order_ids', []);

        if (empty($orderIds) || !is_array($orderIds)) {
            return response()->json(['success' => false, 'message' => 'কোনো অর্ডার সিলেক্ট করা হয়নি!'], 400);
        }

        $orders = WebsiteOrder::whereIn('id', $orderIds)->get();
        $approvedCount = 0;
        $failedCount = 0;
        $errorMessages = [];
        $approvedOrdersDataLayer = [];

        foreach ($orders as $order) {
            if ($action === 'approve') {
                $subRequest = new Request(['status' => 'approved']);
                $res = $this->updateStatus($subRequest, $order->id);
                $resData = json_decode($res->getContent(), true);
                if (!empty($resData['success'])) {
                    $approvedCount++;
                    if (!empty($resData['order_datalayer'])) {
                        $approvedOrdersDataLayer[] = $resData['order_datalayer'];
                    }
                } else {
                    $failedCount++;
                    $errorMessages[] = "#{$order->order_no}: " . ($resData['message'] ?? 'Failed');
                }
            } elseif (in_array($action, ['processing', 'good_no_response', 'no_response', 'advance_payment', 'on_hold', 'delivered', 'cancelled'])) {
                if ($order->status === 'approved') {
                    $failedCount++;
                    $errorMessages[] = "#{$order->order_no}: Approved অর্ডারের স্ট্যাটাস পরিবর্তন করা যাবে না।";
                    continue;
                }
                $order->update(['status' => $action]);
                $approvedCount++;
            } elseif ($action === 'delete') {
                $order->delete();
                $approvedCount++;
            }
        }

        $msg = "{$approvedCount} টি অর্ডার সফলভাবে আপডেট হয়েছে।";
        if ($failedCount > 0) {
            $msg .= " ({$failedCount} টি অর্ডারে স্টক ঘাটতি বা সমস্যা ছিল)";
        }

        if (!empty($approvedOrdersDataLayer)) {
            session()->flash('approved_orders_datalayer', $approvedOrdersDataLayer);
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'errors' => $errorMessages,
            'approved_orders_datalayer' => $approvedOrdersDataLayer,
        ]);
    }

    /**
     * Dispatch selected orders directly to Courier API (e.g. Steadfast).
     */
    public function dispatchCourier(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        $courierService = $request->input('courier_service', 'Steadfast Courier');
        $sendToCourierApi = $request->boolean('send_to_courier_api', false);

        if (empty($orderIds)) {
            return response()->json(['success' => false, 'message' => 'কোনো অর্ডার সিলেক্ট করা হয়নি!'], 400);
        }

        // First approve them if they are still processing
        $approved = 0;
        $errors = [];

        foreach ($orderIds as $oid) {
            $order = WebsiteOrder::find($oid);
            if (!$order) continue;

            if ($order->status !== 'approved') {
                $subRequest = new Request(['status' => 'approved']);
                $res = $this->updateStatus($subRequest, $order->id);
                $resData = json_decode($res->getContent(), true);
                if (empty($resData['success'])) {
                    $errors[] = "#{$order->order_no}: " . ($resData['message'] ?? 'Stock insufficient');
                    continue;
                }
            }
            $approved++;
        }

        $msg = "{$approved} টি অর্ডার সফলভাবে কুরিয়ার প্রসেসিংয়ে পাঠানো হয়েছে।";
        if (!empty($errors)) {
            $msg .= " (" . count($errors) . " টি অর্ডারে সমস্যা পাওয়া গেছে)";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'errors' => $errors,
        ]);
    }

    /**
     * Approve order endpoint from details page
     */
    public function approveOrder(Request $request, $id)
    {
        $subRequest = new Request(['status' => 'approved']);
        return $this->updateStatus($subRequest, $id);
    }

    /**
     * Display the details page for a website order.
     */
    public function show($id)
    {
        return $this->orderDetails($id);
    }

    /**
     * Order Details View
     */
    public function orderDetails($id)
    {
        $order = WebsiteOrder::with(['items.product.unit', 'items.variation', 'customer', 'branch', 'invoice'])->findOrFail($id);

        $fraudStats = [];
        try {
            $phone = $order->phone ?: $order->customer_phone;
            if ($phone) {
                $fraudStats = $this->fraudChecker->checkFraud($phone);
            }
        } catch (\Exception $e) {
            $fraudStats = [];
        }

        $courierList = [
            'steadfast' => 'Steadfast Courier',
            'pathao' => 'Pathao Courier',
            'redx' => 'RedX Courier',
        ];

        $statusList = [
            'processing' => 'Processing',
            'good_no_response' => 'Good But No Response',
            'no_response' => 'No Response',
            'advance_payment' => 'Advance Payment',
            'on_hold' => 'On Hold',
            'approved' => 'Approved',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        return view('backend.pages.website_orders.details', compact('order', 'fraudStats', 'courierList', 'statusList'));
    }

    /**
     * Print Website Order Invoice
     */
    public function printInvoice($id)
    {
        $order = WebsiteOrder::with(['items.product', 'items.variation', 'customer'])->findOrFail($id);
        return view('backend.pages.website_orders.print', compact('order'));
    }

    /**
     * Update customer, shipping and meta from details page
     */
    public function updateOrderDetails(Request $request, $id)
    {
        $order = WebsiteOrder::findOrFail($id);

        $order->customer_name = $request->input('customer_name', $order->customer_name);
        if ($request->filled('phone')) {
            $order->phone = $request->input('phone');
            $order->customer_phone = $order->phone;
        }
        if ($request->filled('delivery_address')) {
            $order->delivery_address = $request->input('delivery_address');
            $order->customer_address = $order->delivery_address;
        }
        if ($request->filled('courier_type')) {
            $inputCourier = $request->input('courier_type');
            $normCourier = str_contains(strtolower($inputCourier), 'pathao') ? 'Pathao' : 'Steadfast';
            $order->courier_service = ($normCourier === 'Pathao') ? 'Pathao Courier' : 'Steadfast Courier';
            $order->delivery_method = $inputCourier;
            if ($order->invoice) {
                $order->invoice->courier_type = $normCourier;
                $order->invoice->save();
            }
        }
        if ($request->has('order_notes')) {
            $order->order_notes = $request->input('order_notes');
            $order->note = $order->order_notes;
        }
        if ($request->has('delivery_charge')) {
            $order->shipping_cost = (float)$request->input('delivery_charge');
            $order->shipping_fee = $order->shipping_cost;
        }
        if ($request->has('discount_amount')) {
            $order->discount = (float)$request->input('discount_amount');
        }

        $subtotal = $order->items()->sum('total_price') ?: $order->subtotal;
        $order->subtotal = $subtotal;
        $order->total_amount = max(0, $subtotal + $order->shipping_cost - $order->discount);
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Order details updated successfully!']);
        }
        return redirect()->back()->with('success', 'Order details updated successfully!');
    }

    /**
     * Update quantity of an item in the order
     */
    public function updateOrderItem(Request $request, $id)
    {
        $itemId = $request->input('item_id');
        $qty = max(1, (int)$request->input('qty', 1));

        $item = WebsiteOrderItem::where('website_order_id', $id)->where('id', $itemId)->firstOrFail();
        $item->quantity = $qty;
        $item->total_price = $item->unit_price * $qty;
        $item->subtotal = $item->total_price;
        $item->save();

        $order = WebsiteOrder::findOrFail($id);
        $subtotal = $order->items()->sum('total_price');
        $order->subtotal = $subtotal;
        $order->total_amount = max(0, $subtotal + $order->shipping_cost - $order->discount);
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated',
            'item_total' => $item->total_price,
            'subtotal' => $order->subtotal,
            'grand_total' => $order->total_amount,
        ]);
    }

    /**
     * Add product to website order
     */
    public function addOrderItem(Request $request, $id)
    {
        $productId = $request->input('product_id');
        $variationId = $request->input('variation_id');
        $product = Product::findOrFail($productId);

        $price = (float)($product->selling_price ?? 0);
        if ($variationId) {
            $variation = ProductVariation::find($variationId);
            if ($variation && $variation->selling_price > 0) {
                $price = (float)$variation->selling_price;
            }
        }

        $item = WebsiteOrderItem::create([
            'website_order_id' => $id,
            'product_id' => $productId,
            'product_variation_id' => $variationId,
            'product_name' => $product->name,
            'product_barcode' => $product->barcode,
            'product_code' => $product->barcode,
            'product_image' => $product->thumbnail_url,
            'unit_price' => $price,
            'quantity' => 1,
            'total_price' => $price,
            'subtotal' => $price,
        ]);

        $order = WebsiteOrder::findOrFail($id);
        $subtotal = $order->items()->sum('total_price');
        $order->subtotal = $subtotal;
        $order->total_amount = max(0, $subtotal + $order->shipping_cost - $order->discount);
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Product added to order',
            'item' => $item,
            'subtotal' => $order->subtotal,
            'grand_total' => $order->total_amount,
        ]);
    }

    /**
     * Remove item from website order
     */
    public function removeOrderItem(Request $request, $id, $itemId)
    {
        $item = WebsiteOrderItem::where('website_order_id', $id)->where('id', $itemId)->firstOrFail();
        $item->delete();

        $order = WebsiteOrder::findOrFail($id);
        $subtotal = $order->items()->sum('total_price');
        $order->subtotal = $subtotal;
        $order->total_amount = max(0, $subtotal + $order->shipping_cost - $order->discount);
        $order->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from order',
                'subtotal' => $order->subtotal,
                'grand_total' => $order->total_amount,
            ]);
        }
        return redirect()->back()->with('success', 'Item removed from order');
    }

    /**
     * Add note alias
     */
    public function addOrderNote(Request $request, $id)
    {
        return $this->updateNote($request, $id);
    }

    /**
     * Product search for adding to order
     */
    public function ajaxProductSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (empty($q)) {
            return response()->json([]);
        }

        $products = Product::where('status', 1)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        $results = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'price' => (float)$p->selling_price,
                'image' => $p->thumbnail_url ?: asset('backend/images/no_images.png'),
            ];
        });

        return response()->json($results);
    }

    /**
     * Build unified dataLayer payload for approved order
     */
    private function buildOrderDataLayerPayload($order)
    {
        $order->loadMissing(['items.product.brand']);

        $dlItems = [];
        $idx = 1;
        if (!empty($order->items)) {
            foreach ($order->items as $item) {
                $brandName = $item->product?->brand?->name ?? (get_setting('com_name') ?: 'CAMERA BD');
                $dlItems[] = [
                    'item_id' => (string)($item->product_code ?: ($item->product_barcode ?: ($item->product_id ?: ''))),
                    'item_name' => $item->product_name ?: 'Product',
                    'affiliation' => get_setting('com_name') ?: 'CAMERA BD',
                    'currency' => 'BDT',
                    'index' => $idx++,
                    'item_brand' => $brandName,
                    'price' => (float)($item->unit_price ?: 0),
                    'quantity' => (int)($item->quantity ?: 1),
                ];
            }
        }

        $customerPhone = $order->customer_phone ?: $order->phone ?: '';
        $customerName = $order->customer_name ?: '';
        $customerEmail = $order->customer_email ?: $order->email ?: '';
        $customerAddress = $order->customer_address ?: $order->delivery_address ?: '';
        $customerCity = $order->city ?: '';
        $customerDivision = $order->division ?: '';
        $totalAmount = (float)($order->total_amount ?: 0);
        $shippingFee = (float)($order->shipping_fee ?: ($order->shipping_cost ?: 0));

        return [
            'event' => 'order_confirm',
            'order_id' => (string)$order->order_no,
            'transaction_id' => (string)$order->order_no,
            'value' => $totalAmount,
            'currency' => 'BDT',
            'tax' => 0,
            'shipping' => $shippingFee,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_email' => $customerEmail,
            'customer_address' => $customerAddress,
            'customer_city' => $customerCity,
            'customer_division' => $customerDivision,
            'payment_method' => $order->payment_method ?: 'Cash on Delivery',
            'courier_service' => $order->courier_service ?: $order->delivery_method ?: '',
            'items' => $dlItems,
            'ecommerce' => [
                'transaction_id' => (string)$order->order_no,
                'value' => $totalAmount,
                'tax' => 0,
                'shipping' => $shippingFee,
                'currency' => 'BDT',
                'items' => $dlItems,
            ],
            'customer_info' => [
                'name' => $customerName,
                'phone' => $customerPhone,
                'email' => $customerEmail,
                'address' => $customerAddress,
                'city' => $customerCity,
                'division' => $customerDivision,
            ],
            'user_data' => [
                'phone_number' => $customerPhone,
                'email' => $customerEmail,
                'address' => [
                    'first_name' => $customerName,
                    'street' => $customerAddress,
                    'city' => $customerCity,
                    'region' => $customerDivision,
                    'country' => 'BD',
                ],
            ],
        ];
    }
}
