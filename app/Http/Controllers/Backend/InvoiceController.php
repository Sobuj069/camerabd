<?php

namespace App\Http\Controllers\Backend;

use AdnSms\AdnSms;
use App\Http\Controllers\Controller;
use App\Models\ActualPayment;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\BranchProduct;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Customer;
use App\Models\DamageItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleSerialNumber;
use App\Models\SerialNumber;
use App\Models\Transaction;
use App\Models\TransferItem;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $data['invoice_no'] = $request->invoice_no;
        $data['customer_id'] = $request->customer_id;
        $data['supplier_id'] = $request->supplier_id;
        $data['category_id'] = $request->category_id;
        $data['sub_category_id'] = $request->sub_category_id;
        $data['imei'] = $request->imei;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['bank_id'] = $request->bank_id;
        $data['product_id'] = $request->product_id;
        $sdate = Carbon::createFromDate($request->startDate)->toDateString();
        $edate = Carbon::createFromDate($request->endDate)->toDateString();
        $data['bankAccount'] = BankAccount::Where('status', 1)->orderBy('id', 'asc')->get();
        $data['allCategory'] = Category::orderBy('name', 'asc')->get();
        $data['allSubCategory'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        if (in_array(auth()->user()->id, [1, 2])) {
            $query = Invoice::with('customer', 'user');
        } else {
            $query = Invoice::with('customer', 'user')->where('branch_id', auth()->user()->branch_id);
        }
        $query->where(function ($q) {
            $q->whereNotIn('sale_type', ['Online', 'WooCommerce'])->orWhereNull('sale_type');
        });
        $data['allCustomer'] = Customer::get();
        $data['allSupplier'] = \App\Models\Supplier::get();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
                $query->where('branch_id', $filterBranchId);
            } else {
                $data['allProduct'] = Product::orderBy('id', 'desc')->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $query->where('branch_id', $userBranchId);
        }

        // Bank filter
        if ($request->bank_id) {
            $bankId = $request->bank_id;
            $query->whereHas('transactions', function ($q) use ($bankId) {
                $q->where('bank_id', $bankId);
            });
        }
        if ($request->startDate != null && $request->endDate != null && $request->customer_id != null) {
            $query->whereBetween('date', [$sdate, $edate])->where('customer_id', $request->customer_id);
        }
        if ($request->startDate != null && $request->endDate != null) {
            $query->whereBetween('date', [$sdate, $edate]);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->invoice_no) {
            $query->where('invoice_no', $request->invoice_no);
        }

        if ($request->product_id) {
            $keyword = $request->product_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($keyword) {
                $q->where('id', $keyword);
            });
        }

        if ($request->category_id) {
            $categoryId = $request->category_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($request->sub_category_id) {
            $subCategoryId = $request->sub_category_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($subCategoryId) {
                $q->where('sub_category_id', $subCategoryId);
            });
        }

        if ($request->supplier_id) {
            $supplierId = $request->supplier_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            });
        }

        if ($request->imei) {
            $imei = $request->imei;
            $query->whereHas('invoiceItems', function ($q) use ($imei) {
                $q->where('imei', 'LIKE', "%$imei%");
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();
        $filtered = Invoice::filterByFakeSale($invoices);

        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 20;
        $currentPageItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $data['invoices'] = new \Illuminate\Pagination\LengthAwarePaginator($currentPageItems, count($filtered), $perPage);
        $data['invoices']->setPath($request->url());

        $data['invoices']->appends([
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'customer_id' => $request->customer_id,
            'supplier_id' => $request->supplier_id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'imei' => $request->imei,
            'invoice_no' => $request->invoice_no,
            'product_id' => $request->product_id,
        ]);
        $invoicessss = Invoice::orderBy('created_at', 'desc')->get();  // Get all invoices
        $latestInvoice = $invoicessss->first();


        return view('backend.pages.invoice.index', $data, compact('latestInvoice'));
    }

    /**
     * Build base query for Online Sale orders with filters applied (except status tab)
     */
    protected function buildOnlineSaleBaseQuery(Request $request)
    {
        $query = Invoice::with(['customer', 'user', 'returns', 'platform', 'bankTransactions'])
            ->where('sale_type', 'Online');

        // Date range filter
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $sdate = Carbon::createFromDate($request->startDate)->toDateString();
            $edate = Carbon::createFromDate($request->endDate)->toDateString();
            $query->whereBetween('date', [$sdate, $edate]);
        }

        // Global Search Filter (Search across Invoice No, Customer Name/Phone/Email, Product Barcode/SKU, Consignment ID, Courier, Status, Notes)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                  ->orWhere('unique_id', 'like', '%' . $search . '%')
                  ->orWhere('woocommerce_order_id', 'like', '%' . $search . '%')
                  ->orWhere('consignment_id', 'like', '%' . $search . '%')
                  ->orWhere('courier_type', 'like', '%' . $search . '%')
                  ->orWhere('order_status', 'like', '%' . $search . '%')
                  ->orWhere('note', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('phone', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('invoiceItems', function ($iq) use ($search) {
                      $iq->where('new_details', 'like', '%' . $search . '%')
                         ->orWhere('imei', 'like', '%' . $search . '%')
                         ->orWhereHas('product', function ($pq) use ($search) {
                             $pq->where('name', 'like', '%' . $search . '%')
                                ->orWhere('barcode', 'like', '%' . $search . '%');
                         });
                  });
            });
        }

        // Invoice No filter
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'like', '%' . trim($request->invoice_no) . '%');
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Product filter
        if ($request->filled('product_id')) {
            $prodId = $request->product_id;
            $query->whereHas('invoiceItems.product', function ($q) use ($prodId) {
                $q->where('id', $prodId);
            });
        }

        // Platform / WooCommerce filter
        if ($request->filled('platform_id')) {
            if ($request->platform_id === 'woocommerce') {
                $query->where(function($q) {
                    $q->whereNotNull('woocommerce_order_id')
                      ->orWhereHas('platform', function($pq) {
                          $pq->where('name', 'like', '%woocommerce%')->orWhere('slug', 'like', '%woocommerce%');
                      });
                });
            } else {
                $query->where('platform_id', $request->platform_id);
            }
        }

        return $query;
    }

    /**
     * Build filtered query for Online Sale orders with active status tab applied
     */
    protected function buildOnlineSaleFilteredQuery(Request $request)
    {
        $query = $this->buildOnlineSaleBaseQuery($request);
        $activeStatus = $request->input('filter_status', $request->query('status', $request->input('status', 'all')));

        if ($activeStatus === 'woocommerce') {
            $query->where(function($q) {
                $q->whereNotNull('woocommerce_order_id')
                  ->orWhereHas('platform', function($pq) {
                      $pq->where('name', 'like', '%woocommerce%')->orWhere('slug', 'like', '%woocommerce%');
                  });
            });
        } elseif ($activeStatus === 'processing') {
            $query->where(function($q) {
                $q->where(function($q1) {
                    $q1->whereIn('order_status', ['Processing', 'processing', 'Approved', 'approved'])
                       ->orWhere(function($q2) {
                           $q2->whereIn('order_status', ['Pending', 'pending'])->where(function($q3) {
                               $q3->whereNull('consignment_id')->orWhere('consignment_id', '');
                           });
                       })
                       ->orWhereNull('order_status')
                       ->orWhere('order_status', '');
                });
            });
        } elseif ($activeStatus === 'rts') {
            $query->whereIn('order_status', ['RTS', 'rts']);
        } elseif ($activeStatus === 'in_review') {
            $query->whereIn('order_status', ['In Review', 'in_review', 'Review', 'review', 'in_review_approval']);
        } elseif ($activeStatus === 'pending' || $activeStatus === 'pending_courier') {
            $query->whereIn('order_status', ['Pending', 'pending', 'order_placed', 'pending_pickup'])
                  ->whereNotNull('consignment_id')->where('consignment_id', '!=', '')
                  ->whereNotIn('order_status', ['RTS', 'rts', 'Approved', 'approved', 'Processing', 'processing']);
        } elseif ($activeStatus === 'in_transit') {
            $query->whereIn('order_status', ['In Transit', 'in_transit', 'Transit', 'transit', 'Picked Up', 'picked_up', 'Shipped', 'shipped', 'out_for_delivery']);
        } elseif ($activeStatus === 'in_courier' || $activeStatus === 'all_in_courier') {
            $query->where(function($q) {
                $q->whereIn('order_status', [
                    'In Courier', 'in_courier',
                    'In Transit', 'in_transit', 'Transit', 'transit', 'Picked Up', 'picked_up', 'Shipped', 'shipped', 'out_for_delivery',
                    'In Review', 'in_review', 'Review', 'review', 'in_review_approval'
                ])->orWhere(function($subQ) {
                    $subQ->whereIn('order_status', ['Pending', 'pending', 'order_placed', 'pending_pickup'])
                         ->whereNotNull('consignment_id')->where('consignment_id', '!=', '');
                });
            })->whereNotIn('order_status', ['RTS', 'rts', 'Approved', 'approved', 'Processing', 'processing']);
        } elseif ($activeStatus === 'on_hold') {
            $query->whereIn('order_status', ['On Hold', 'on_hold', 'on-hold', 'Hold', 'hold']);
        } elseif ($activeStatus === 'delivered') {
            $query->whereIn('order_status', ['Delivered', 'delivered', 'completed']);
        } elseif ($activeStatus === 'pending_return') {
            $query->whereIn('order_status', [
                'Pending Return', 'pending_return', 'return_pending',
                'Cancelled', 'cancelled', 'failed', 'pickup_cancel', 'pickup_cancelled', 'canceled',
                'Partial', 'partial_delivery', 'partially_delivered', 'partial_delivered'
            ]);
        } elseif ($activeStatus === 'returned') {
            $query->whereIn('order_status', ['Returned', 'returned', 'Return', 'return', 'returned_by_merchant', 'returned_approval']);
        } elseif ($activeStatus === 'partial') {
            $query->whereIn('order_status', ['Partial', 'partial_delivery', 'partially_delivered', 'partial_delivered']);
        } elseif ($activeStatus === 'cancelled') {
            $query->whereIn('order_status', ['Cancelled', 'cancelled', 'failed', 'pickup_cancel', 'pickup_cancelled', 'canceled']);
        }

        return $query;
    }

    public function onlineSale(Request $request)
    {
        $data['invoice_no'] = $request->invoice_no;
        $data['customer_id'] = $request->customer_id;
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        $data['product_id'] = $request->product_id;
        $data['status'] = $request->query('status', 'all');
        $data['platform_id'] = $request->platform_id;

        // Auto clear due for any Online invoices that are Delivered but still have pending due
        $deliveredDueInvoices = Invoice::where('sale_type', 'Online')
            ->whereIn('order_status', ['Delivered', 'delivered'])
            ->where('total_due', '>', 0)
            ->where('status', '!=', 2)
            ->get();

        foreach ($deliveredDueInvoices as $delInv) {
            autoPayInvoiceDue($delInv);
        }

        $baseStatusQuery = $this->buildOnlineSaleBaseQuery($request);

        // Calculate Status Tab Badge Counts matching smart.ecomdrivebd.com
        $statusCounts = [
            'all' => (clone $baseStatusQuery)->count(),
            'woocommerce' => (clone $baseStatusQuery)->where(function($q) {
                $q->whereNotNull('woocommerce_order_id')
                  ->orWhereHas('platform', function($pq) {
                      $pq->where('name', 'like', '%woocommerce%')->orWhere('slug', 'like', '%woocommerce%');
                  });
            })->count(),
            'processing' => (clone $baseStatusQuery)->where(function($q) {
                $q->where(function($q1) {
                    $q1->whereIn('order_status', ['Processing', 'processing', 'Approved', 'approved'])
                       ->orWhere(function($q2) {
                           $q2->whereIn('order_status', ['Pending', 'pending'])->where(function($q3) {
                               $q3->whereNull('consignment_id')->orWhere('consignment_id', '');
                           });
                       })
                       ->orWhereNull('order_status')
                       ->orWhere('order_status', '');
                });
            })->count(),
            'rts' => (clone $baseStatusQuery)->whereIn('order_status', ['RTS', 'rts'])->count(),
            'in_review' => (clone $baseStatusQuery)->whereIn('order_status', ['In Review', 'in_review', 'Review', 'review', 'in_review_approval'])->count(),
            'pending' => (clone $baseStatusQuery)->whereIn('order_status', ['Pending', 'pending', 'order_placed', 'pending_pickup'])
                ->whereNotNull('consignment_id')->where('consignment_id', '!=', '')
                ->whereNotIn('order_status', ['RTS', 'rts', 'Approved', 'approved', 'Processing', 'processing'])->count(),
            'in_transit' => (clone $baseStatusQuery)->whereIn('order_status', ['In Transit', 'in_transit', 'Transit', 'transit', 'Picked Up', 'picked_up', 'Shipped', 'shipped', 'out_for_delivery'])->count(),
            'in_courier' => (clone $baseStatusQuery)->where(function($q) {
                $q->whereIn('order_status', [
                    'In Courier', 'in_courier',
                    'In Transit', 'in_transit', 'Transit', 'transit', 'Picked Up', 'picked_up', 'Shipped', 'shipped', 'out_for_delivery',
                    'In Review', 'in_review', 'Review', 'review', 'in_review_approval'
                ])->orWhere(function($subQ) {
                    $subQ->whereIn('order_status', ['Pending', 'pending', 'order_placed', 'pending_pickup'])
                         ->whereNotNull('consignment_id')->where('consignment_id', '!=', '');
                });
            })->whereNotIn('order_status', ['RTS', 'rts', 'Approved', 'approved', 'Processing', 'processing'])->count(),
            'on_hold' => (clone $baseStatusQuery)->whereIn('order_status', ['On Hold', 'on_hold', 'on-hold', 'Hold', 'hold'])->count(),
            'delivered' => (clone $baseStatusQuery)->whereIn('order_status', ['Delivered', 'delivered', 'completed'])->count(),
            'pending_return' => (clone $baseStatusQuery)->whereIn('order_status', [
                'Pending Return', 'pending_return', 'return_pending',
                'Cancelled', 'cancelled', 'failed', 'pickup_cancel', 'pickup_cancelled', 'canceled',
                'Partial', 'partial_delivery', 'partially_delivered', 'partial_delivered'
            ])->count(),
            'returned' => (clone $baseStatusQuery)->whereIn('order_status', ['Returned', 'returned', 'Return', 'return', 'returned_by_merchant', 'returned_approval'])->count(),
            'partial' => (clone $baseStatusQuery)->whereIn('order_status', ['Partial', 'partial_delivery', 'partially_delivered', 'partial_delivered'])->count(),
            'cancelled' => (clone $baseStatusQuery)->whereIn('order_status', ['Cancelled', 'cancelled', 'failed', 'pickup_cancel', 'pickup_cancelled', 'canceled'])->count(),
        ];

        $statusCounts['pending_courier'] = $statusCounts['pending'];
        $statusCounts['all_in_courier'] = $statusCounts['in_courier'];
        $query = $this->buildOnlineSaleFilteredQuery($request);

        // Pre-calculate total due and count for all matching records across all pages
        $totalDueAllMatching = (float) (clone $query)->where('total_due', '>', 0)->where('status', '!=', 2)->sum('total_due');
        $totalDueMatchingCount = (clone $query)->where('total_due', '>', 0)->where('status', '!=', 2)->count();

        $invoices = $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->all());
        $data['invoices'] = $invoices;
        $data['totalMatchingCount'] = $invoices->total();
        $data['totalDueAllMatching'] = $totalDueAllMatching;
        $data['totalDueMatchingCount'] = $totalDueMatchingCount;
        $data['bankAccounts'] = BankAccount::where('status', 1)->orderBy('id', 'asc')->get();
        $data['platforms'] = \App\Models\Platform::where('status', 1)->get();
        $data['statusCounts'] = $statusCounts;
        $data['activeStatus'] = $data['status'];

        return view('backend.pages.invoice.online-index', $data, compact('invoices', 'statusCounts'));
    }

    public function updateOrderStatus(Request $request, $id = null)
    {
        try {
            $rawIds = $request->input('invoice_ids');

            $ids = [];
            if (!empty($id)) {
                $ids = [$id];
            } else if ($request->input('select_all_pages') == 1 || $request->input('select_all_pages') === 'true' || $request->input('select_all_pages') === '1') {
                $ids = $this->buildOnlineSaleFilteredQuery($request)->pluck('id')->toArray();
            } elseif (is_array($rawIds)) {
                $ids = $rawIds;
            } elseif (is_string($rawIds)) {
                $ids = json_decode($rawIds, true) ?? array_filter(explode(',', $rawIds));
            } elseif ($request->filled('invoice_id')) {
                $ids = [$request->input('invoice_id')];
            }

            if (empty($ids)) {
                $msg = __('Please select at least one invoice to update status.');
                if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->isJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                session()->flash('error', $msg);
                return redirect()->back();
            }

            $validStatusesMap = [
                'processing' => 'Processing',
                'rts' => 'RTS',
                'in review' => 'In Review',
                'in_review' => 'In Review',
                'pending' => 'Pending',
                'in transit' => 'In Transit',
                'in_transit' => 'In Transit',
                'on hold' => 'On Hold',
                'on_hold' => 'On Hold',
                'no response' => 'No Response',
                'no_response' => 'No Response',
                'in courier' => 'In Courier',
                'in_courier' => 'In Courier',
                'delivered' => 'Delivered',
                'pending return' => 'Pending Return',
                'pending_return' => 'Pending Return',
                'returned' => 'Returned',
                'partial' => 'Partial',
                'cancelled' => 'Cancelled',
            ];

            $rawStatus = $request->input('order_status', $request->input('status', 'Processing'));
            $statusKey = strtolower(trim((string)$rawStatus));
            $status = $validStatusesMap[$statusKey] ?? (function_exists('mapCourierStatus') ? mapCourierStatus($statusKey) : ucfirst($statusKey));

            $invoices = Invoice::with('customer')->whereIn('id', $ids)->get();
            $count = 0;
            $errors = [];

            foreach ($invoices as $inv) {
                $oldStatus = $inv->order_status;

                // Lock Delivered Invoices: Status cannot be changed once Delivered!
                if (in_array(strtolower(trim($oldStatus ?? '')), ['delivered'])) {
                    $errors[] = "Invoice #{$inv->invoice_no} is already Delivered and its status cannot be changed.";
                    continue;
                }

                // Synchronize inventory stock and IMEI serials on status change
                if (function_exists('syncInvoiceStockForStatusChange')) {
                    syncInvoiceStockForStatusChange($inv, $status, $oldStatus);
                }

                // Note update if provided
                if ($request->filled('note')) {
                    $trimmedNote = trim($request->input('note'));
                    $dateStr = date('d M Y, h:i A');
                    $existingNote = !empty($inv->note) ? $inv->note . "\n" : "";
                    $inv->note = $existingNote . "[{$status} - {$dateStr}]: {$trimmedNote}";
                }

                // If status is updated to 'In Courier', dispatch to courier API if not already sent
                if (in_array(strtolower(trim($status)), ['in courier', 'in_courier'])) {
                    if (empty($inv->consignment_id)) {
                        $dispatchResult = $this->dispatchInvoiceToCourier($inv);
                        if ($dispatchResult['success']) {
                            logActivity('Send to Courier', "Invoice #{$inv->invoice_no} dispatched to courier (Consignment: {$inv->consignment_id})", $inv);
                        } else {
                            $errors[] = $dispatchResult['message'];
                        }
                    }
                    $inv->order_status = 'In Courier';
                    $inv->save();
                    $count++;
                    logActivity('Update Order Status', "Invoice #{$inv->invoice_no} status changed from '{$oldStatus}' to 'In Courier'", $inv);
                } else {
                    $inv->order_status = $status;
                    $inv->save();
                    $count++;
                    logActivity('Update Order Status', "Invoice #{$inv->invoice_no} status changed from '{$oldStatus}' to '{$status}'", $inv);
                }

                if (in_array(strtolower(trim($status)), ['delivered'])) {
                    if ($inv->total_due > 0 && $inv->status != 2) {
                        if (function_exists('autoPayInvoiceDue')) {
                            autoPayInvoiceDue($inv);
                        }
                    }
                }
            }

            if ($count > 0) {
                $msg = __(":count invoice(s) status updated to :status!", ['count' => $count, 'status' => $status]);
                if (!empty($errors)) {
                    $msg .= ' ' . implode(' | ', $errors);
                    session()->flash('info', $msg);
                } else {
                    session()->flash('success', $msg);
                }

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->isJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $msg,
                        'count' => $count,
                        'status' => $status,
                    ]);
                }
            } else {
                $errMsg = implode(' | ', $errors) ?: __('No invoices were updated.');
                session()->flash('error', $errMsg);

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->isJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errMsg,
                    ], 422);
                }
            }

            return redirect()->back();

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error in updateOrderStatus: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $errMsg = "স্ট্যাটাস পরিবর্তন ব্যর্থ হয়েছে: " . $e->getMessage();
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                ], 500);
            }
            return redirect()->back()->with('error', $errMsg);
        }
    }

    public function clearSelectedDues(Request $request)
    {
        $rawIds = $request->input('invoice_ids');
        $bankId = $request->input('bank_id');
        $paymentDate = $request->input('payment_date') ?? date('Y-m-d');

        $ids = [];
        if ($request->input('select_all_pages') == 1 || $request->input('select_all_pages') === 'true' || $request->input('select_all_pages') === '1') {
            $ids = $this->buildOnlineSaleFilteredQuery($request)->pluck('id')->toArray();
        } elseif (is_array($rawIds)) {
            $ids = $rawIds;
        } elseif (is_string($rawIds)) {
            $ids = json_decode($rawIds, true) ?? array_filter(explode(',', $rawIds));
        }

        if (empty($ids)) {
            session()->flash('error', __('Please select at least one invoice.'));
            return redirect()->back();
        }

        $invoices = Invoice::whereIn('id', $ids)
            ->where('total_due', '>', 0)
            ->where('status', '!=', 2)
            ->get();

        $clearedCount = 0;
        $totalPaidAmount = 0;

        foreach ($invoices as $inv) {
            $dueAmount = (float) $inv->total_due;
            if (autoPayInvoiceDue($inv, $bankId, $paymentDate)) {
                $clearedCount++;
                $totalPaidAmount += $dueAmount;
            }
        }

        if ($clearedCount > 0) {
            $bankName = BankAccount::find($bankId)?->bank_name ?? __('Bank Account');
            session()->flash('success', __(":count selected invoice due(s) cleared into :bank! Total Paid: TK :amount", [
                'count' => $clearedCount,
                'bank' => $bankName,
                'amount' => number_format($totalPaidAmount, 2)
            ]));
        } else {
            session()->flash('info', __('No pending due found for the selected invoice(s).'));
        }

        return redirect()->back();
    }

    public function create(Request $request)
    {
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = \App\Models\Quotation::with('quotationItems.product', 'customer')->find($request->quotation_id);
        }
        $preOrder = null;
        if ($request->has('pre_order_id')) {
            $preOrder = \App\Models\PreOrder::with('items.product', 'customer')->find($request->pre_order_id);

            if ($preOrder && $request->filled('convert_mode')) {
                if ($preOrder->status != 'pending') {
                    session()->flash('error', __('This Pre-Order has already been processed or cancelled.'));
                    return redirect()->route('pre-orders.index');
                }

                $insufficientItems = [];
                foreach ($preOrder->items as $item) {
                    $product = $item->product;
                    if (!$product || $product->is_service == 1) {
                        continue;
                    }

                    $availableStock = 0;
                    if ($item->product_variation_id) {
                        $availableStock = (float) variation_stock($item->product_variation_id);
                    } else {
                        $availableStock = (float) product_fake_stock_val($product);
                    }

                    if ($availableStock < $item->quantity) {
                        $varName = '';
                        if ($item->product_variation_id) {
                            $var = \App\Models\ProductVariation::with('size', 'color')->find($item->product_variation_id);
                            if ($var) {
                                $varName = ' (' . trim(($var->size->size ?? '') . ' ' . ($var->color->color ?? '')) . ')';
                            }
                        }
                        $insufficientItems[] = [
                            'name'      => $product->name . $varName,
                            'required'  => $item->quantity,
                            'available' => max(0, (int)$availableStock),
                        ];
                    }
                }

                if (!empty($insufficientItems)) {
                    $errMsg = __('Stock is INSUFFICIENT to convert this Pre-Order into a Sale. Please Restock first!') . "<br><ul class='mt-2 mb-0 pl-3'>";
                    foreach ($insufficientItems as $err) {
                        $errMsg .= "<li><b>" . e($err['name']) . "</b> — Required: " . $err['required'] . ", Available Stock: " . $err['available'] . "</li>";
                    }
                    $errMsg .= "</ul>";

                    session()->flash('error', $errMsg);
                    return redirect()->route('pre-orders.index');
                }
            }
        }
        $data['preOrder'] = $preOrder;
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $query = Customer::orderBy('id', 'asc');

        $activeBranchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;
        $productId = BranchProduct::where('branch_id', $activeBranchId)->pluck('product_id');
        $categoryId = BranchCategory::where('branch_id', $activeBranchId)->pluck('category_id');

        $data['products'] = Product::whereIn('id', $productId)
            ->where('status', 1)
            ->where('is_service', 0)
            ->with('unit.related_unit')
            ->when(env('APP_IMEI') != 'yes', function ($query) {
                return $query->where(function($q) {
                    $q->where('imei', '!=', 1)->orWhereNull('imei');
                });
            })
            ->orderBy('name', 'ASC')
            ->paginate(12);

        $data['categories'] = Category::whereIn('id', $categoryId)->orderBy('name', 'ASC')->get();

        $query->where(function ($q) use ($activeBranchId) {
            $q->where('branch_id', $activeBranchId)
                ->orWhereNull('branch_id');
        });

        // $token = env('PATHAO_SECRET_TOKEN');
        // $cityList = getCityList($token);
        // $data['citits'] =  $cityList['data']['data'];
        // $data['shops'] = getStoreList($token);

        // dd($data['citits']);
        // $data['products'] = Product::with('unit.related_unit')->orderBy('name', 'ASC')->paginate(8);

        // dd(ProductVariation::where('product_id',$data['products']->id)   ->get());
        // $data['categories'] = Category::orderBy('name', 'ASC')->get();
        $data['customers'] = Customer::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->with('vehicles', 'discountGroup')
            ->get();
        $data['discountGroups'] = \App\Models\DiscountGroup::where('status', 1)->orderBy('name', 'asc')->get();

        $data['allBranch'] = Branch::get();
        $data['bank_accounts'] = BankAccount::where('status', 1)->get();
        $data['platforms'] = \App\Models\Platform::where('status', 1)->orderBy('name', 'asc')->get();

        $posMode = $request->query('mode', 'manual');
        $isOnlinePos = ($posMode === 'online');
        $data['posMode'] = $posMode;
        $data['isOnlinePos'] = $isOnlinePos;

        $steadfastApiKey = trim(get_setting('steadfast_api_key') ?: env('STEADFAST_API_KEY'));
        $steadfastSecretKey = trim(get_setting('steadfast_secret_key') ?: env('STEADFAST_SECRET_KEY'));
        $hasSteadfast = !empty($steadfastApiKey) || !empty($steadfastSecretKey);

        $pathaoSecretToken = get_setting('pathao_secret_token') ?: env('PATHAO_SECRET_TOKEN');
        $pathaoClientId = get_setting('pathao_client_id') ?: env('PATHAO_CLIENT_ID');
        $pathaoClientSecret = get_setting('pathao_client_secret') ?: env('PATHAO_CLIENT_SECRET');
        $hasPathao = !empty($pathaoSecretToken) || (!empty($pathaoClientId) && !empty($pathaoClientSecret));

        $availableCouriers = [];
        if ($hasSteadfast) {
            $availableCouriers[] = 'Stead Fast';
        }
        if ($hasPathao) {
            $availableCouriers[] = 'Pathao';
        }

        $data['hasSteadfast'] = $hasSteadfast;
        $data['hasPathao'] = $hasPathao;
        $data['availableCouriers'] = $availableCouriers;

        return view('backend.pages.invoice.sc-create', $data, compact('filterBranchId', 'userBranchId', 'quotation', 'isOnlinePos', 'posMode', 'availableCouriers', 'hasSteadfast', 'hasPathao'));
    }

    public function store(Request $request)
    {
        // Safety guard: if this form was submitted from Pre-Order EDIT mode (no convert_mode), block sale creation
        if ($request->filled('pre_order_id') && !$request->filled('convert_mode')) {
            return redirect()->route('pre-orders.index')
                ->with('error', __('Cannot create a Sale from Pre-Order edit page. Use the Update Pre-Order button.'));
        }

        // Capture convert_mode flag and pre_order_id before main logic
        $convertMode   = $request->filled('convert_mode') && $request->filled('pre_order_id');
        $preOrderId    = $request->pre_order_id;

        // dd($request->all());
        $this->validateImeiSelections($request);

        $branchId = $request->branch_id;
        $last_invoice = Invoice::where('branch_id', $branchId)
            ->orderBy('id', 'desc')
            ->select('invoice_no')
            ->first();
        if ($last_invoice == null) {
            $invoice_no = "INV-0000001";
        } else {
            $numberPart = (int) substr($last_invoice->invoice_no, strlen("INV-"));
            $newNumber = str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            $invoice_no = time();
        }

        $invoice = new Invoice();
        $invoice->date = $request->date;
        $invoice->unique_id = uniqid();
        $invoice->invoice_no = $invoice_no;
        $invoice->customer_id = $request->customer_id;
        $invoice->estimated_amount = $request->estimated_amount;
        $invoice->discount = ($request->discount_amount == null) ? '0.00' : $request->discount_amount;
        $invoice->discount_amount = $request->discount;

        $invoice->vat = ($request->vat_amount == null) ? '0.00' : $request->vat_amount;
        $invoice->vat_amount = $request->vat;

        $invoice->total_amount = $request->payable_amount;
        $invoice->delivery_charge = $request->delivery_charge ?? 0;
        $invoice->courier_type = $request->courier_type;
        $previousDue = (float)($request->previous_due ?? 0);
        if ($previousDue <= 0 && $request->customer_id && $request->customer_id != 1) {
            $priorInvDue = (float) Invoice::where('customer_id', $request->customer_id)->sum('total_due');
            $custObj = Customer::find($request->customer_id);
            $openBal = $custObj ? open_balance_customer($request->customer_id, $custObj->due_amount) : 0;
            $previousDue = $priorInvDue + $openBal;
        }
        $invoice->previous_due = $previousDue;
        $invoice->note = $request->note;
        $invoice->ref_no = $request->ref_no;
        $invoice->change_amount = $request->balance;
        $invoice->created_by = auth()->user()->id;

        $saleType = $request->sale_type;
        if ($request->input('mode') === 'online' || $request->input('pos_mode') === 'online') {
            $saleType = 'Online';
        }
        if (empty($saleType)) {
            $saleType = 'Outlet';
        }
        $invoice->sale_type = $saleType;

        if ($request->has('quotation_id')) {
            $invoice->quotation_id = $request->quotation_id;
        }
        if ($request->filled('vehicle_reg_no')) {
            $invoice->vehicle_reg_no = $request->vehicle_reg_no;
        }

        if ($request->customer_id != 1) {
            $point = $request->payable_amount / 100;
            $invoice->inv_point = $point;
            $customer = Customer::where('id', $request->customer_id)->first();
            if ($customer->id != 1) {
                $invoice->total_point = $customer->total_point + $point;
                Customer::where("id", $request->customer_id)->update([
                    'total_point' => $customer->total_point + $point,
                ]);
            }
        }
        if ($request->pay_point != null) {
            $invoice->pay_point = $request->pay_point;
            $customer = Customer::where('id', $request->customer_id)->first();
            Customer::where("id", $request->customer_id)->update([
                'total_point' => $customer->total_point - $request->pay_point,
            ]);
            $invoice->total_point = $customer->total_point - $request->pay_point;
        }

        if (auth()->user()->branch_id == 1) {
            $invoice->branch_id = $request->branch_id;
        } else {
            $invoice->branch_id = auth()->user()->branch_id;
        }

        $customer_info = Customer::where('id', $request->customer_id)->first();
        $courierType = strtolower(trim($request->courier_type ?? ''));
        $isCourierOrder = ($invoice->sale_type === 'Online' && !empty($request->courier_type));

        if ($isCourierOrder) {
            // Prevent courier orders for Walk-in Customer (ID == 1)
            if ($request->customer_id == 1 || ($customer_info && $customer_info->id == 1) || strtolower(trim($customer_info?->name ?? '')) == 'walk-in customer') {
                session()->flash('error', __('Courier orders cannot be placed for Walk-in Customer. Please select or create a registered customer with a valid name, phone, and delivery address.'));
                return redirect()->back()->withInput();
            }

            // Validate phone number
            $cleanPhone = preg_replace('/[^0-9]/', '', $customer_info?->phone ?? '');
            if (empty($cleanPhone) || strlen($cleanPhone) < 11) {
                session()->flash('error', __('Courier order requires a registered customer with a valid 11-digit mobile number.'));
                return redirect()->back()->withInput();
            }

            // Validate delivery address
            if (empty(trim($customer_info?->address ?? ''))) {
                session()->flash('error', __('Courier order requires a registered customer with a valid delivery address.'));
                return redirect()->back()->withInput();
            }
        }

        if ($invoice->sale_type === 'Online') {
            $invoice->courier_type = $request->courier_type ?: null;
            $invoice->platform_id = $request->platform_id ?: null;
            $invoice->source_link = $request->source_link ?: null;
            $invoice->consignment_id = '';
            $invoice->order_status = 'Pending';
        } else {
            $invoice->courier_type = null;
            $invoice->platform_id = null;
            $invoice->source_link = null;
            $invoice->consignment_id = '';
            $invoice->order_status = null;
        }

        if (!$request->has('product_id') || !is_array($request->product_id) || count($request->product_id) === 0) {
            session()->flash('error', __('Please select at least one product to create an invoice.'));
            return redirect()->back()->withInput();
        }

        if ((float)($request->payable_amount ?? 0) < 0) {
            session()->flash('error', __('Payable amount cannot be negative.'));
            return redirect()->back()->withInput();
        }

        // ===== Server-side Stock & Variation Stock Validation =====
        if ($request->has('product_id') && is_array($request->product_id)) {
            foreach ($request->product_id as $key => $productId) {
                $product = Product::find($productId);
                if (!$product || $product->is_service == 1) {
                    continue;
                }

                $variationId = $request->variation_id[$key] ?? null;
                $mainQty = (float)($request->main_qty[$key] ?? 1);
                $subQty  = (float)($request->sub_qty[$key] ?? 0);
                $relatedValue = ($product->unit && $product->unit->related_value) ? (float)$product->unit->related_value : 1;
                $saleQty = ($product->unit && $product->unit->related_unit != null) ? ($mainQty * $relatedValue) + $subQty : $mainQty;

                if ($saleQty <= 0) {
                    session()->flash('error', __('Quantity for product ":prod" must be greater than zero.', ['prod' => $product->name]));
                    return redirect()->back()->withInput();
                }

                if ($variationId) {
                    $varStock = variation_stock($variationId);
                    if ($varStock < $saleQty) {
                        $variation = \App\Models\ProductVariation::with('size', 'color')->find($variationId);
                        $varName = trim(($variation?->size?->size ?? '') . ' ' . ($variation?->color?->color ?? ''));
                        session()->flash('error', __('Stock Out Error: Variation ":var" of product ":prod" is out of stock! Available: :avail, Requested: :req.', [
                            'var' => $varName ?: "#{$variationId}",
                            'prod' => $product->name,
                            'avail' => $varStock,
                            'req' => $saleQty
                        ]));
                        return redirect()->back()->withInput();
                    }
                } else {
                    $prodStock = (float) product_fake_stock_val($product);
                    if ($prodStock < $saleQty) {
                        session()->flash('error', __('Stock Out Error: Product ":prod" is out of stock! Available: :avail, Requested: :req.', [
                            'prod' => $product->name,
                            'avail' => product_stock($product),
                            'req' => $saleQty
                        ]));
                        return redirect()->back()->withInput();
                    }
                }
            }
        }

        DB::transaction(function () use ($request, $invoice) {
            if ($invoice->save()) {
                if ($invoice->quotation_id) {
                    $quotation = \App\Models\Quotation::find($invoice->quotation_id);
                    if ($quotation) {
                        $quotation->status = 1;
                        $quotation->save();
                    }
                }

                foreach ($request->product_id as $key => $product_id) {
                    $invoice_item = new InvoiceItem();
                    $find_unit_id = Product::where('id', $product_id)->first();
                    $selectedImeis = [];
                    if ($find_unit_id && (int) $find_unit_id->imei === 1) {
                        $selectedImeis = $this->parseImeis($request->imei[$key] ?? null);
                        $invoice_item->imei = implode("\n", $selectedImeis);
                    } else {
                        $invoice_item->imei = $request->imei[$key] ?? null;
                    }

                    $invoice_item->branch_id = auth()->user()->branch_id == 1
                        ? $request->branch_id
                        : auth()->user()->branch_id;

                    $invoice_item->invoice_id = $invoice->id;
                    $invoice_item->product_id = $product_id;
                    $invoice_item->rate = $request->rate[$key];
                    $invoice_item->product_discount = $request->product_discount[$key];

                    // Variation handle
                    $variation_id = $request->variation_id[$key] ?? null;
                    $invoice_item->product_variation_id = $variation_id;

                    // Warranty handle
                    $wVal = $request->warranty_value[$key] ?? null;
                    $wUnit = $request->warranty_unit[$key] ?? null;
                    if (empty($wVal) && $find_unit_id) {
                        $find_unit_id->loadMissing('warranty');
                        if ($find_unit_id->warranty) {
                            $wVal = $find_unit_id->warranty->duration;
                            $wUnit = $find_unit_id->warranty->period;
                        } elseif (!empty($find_unit_id->warranty_value)) {
                            $wVal = $find_unit_id->warranty_value;
                            $wUnit = $find_unit_id->warranty_unit ?: 'Month';
                        }
                    }
                    $invoice_item->warranty_value = $wVal;
                    $invoice_item->warranty_unit = $wUnit;

                    if ($find_unit_id->is_service == 0) {
                        if ((int) $find_unit_id->imei === 1) {
                            $qty = count($selectedImeis);
                            $invoice_item->main_qty = $qty;
                            $invoice_item->sub_qty = 0;
                            $invoice_item->actual_main = $qty;
                            $invoice_item->actual_sub = 0;
                            $saleQty = $qty;

                            $rate = (float) ($request->rate[$key] ?? 0);
                            $discount = (float) ($request->product_discount[$key] ?? 0);
                            $lineSubtotal = ($rate * $qty) - $discount;

                            $invoice_item->subtotal = $lineSubtotal;
                            $invoice_item->actual_total = $lineSubtotal;
                            $invoice_item->inv_subtotal = $lineSubtotal;
                        } elseif ($find_unit_id->unit->related_unit == null) {
                            $invoice_item->main_qty = $request->main_qty[$key];
                            $invoice_item->actual_main = $request->main_qty[$key];
                            $saleQty = $request->main_qty[$key];

                            $invoice_item->subtotal = $request->sub_total[$key];
                            $invoice_item->actual_total = $request->sub_total[$key];
                            $invoice_item->inv_subtotal = $request->sub_total[$key];
                        } else {
                            $invoice_item->main_qty = $request->main_qty[$key];
                            $invoice_item->sub_qty = $request->sub_qty[$key];
                            $invoice_item->actual_main = $request->main_qty[$key];
                            $invoice_item->actual_sub = $request->sub_qty[$key];
                            $main = $request->main_qty[$key] * $find_unit_id->unit->related_value;
                            $sub = $request->sub_qty[$key] ?? 0;
                            $saleQty = $main + $sub;

                            $invoice_item->subtotal = $request->sub_total[$key];
                            $invoice_item->actual_total = $request->sub_total[$key];
                            $invoice_item->inv_subtotal = $request->sub_total[$key];
                        }
                        $invoice_item->pur_subtotal = calculateUnitPriceUsingFIFO($product_id, $saleQty, $variation_id);
                    } else {
                        $invoice_item->main_qty = $request->main_qty[$key];
                        $invoice_item->subtotal = $request->sub_total[$key];
                        $invoice_item->inv_subtotal = $request->sub_total[$key];
                        $invoice_item->pur_subtotal = ($find_unit_id->purchase_price ?? 0.00) * $request->main_qty[$key];
                    }
                    $invoice_item->date = $request->date;
                    $invoice_item->save();

                    // Mark IMEI as sold
                    if ($invoice_item->imei) {
                        $imeis = explode(',', $invoice_item->imei);
                        SerialNumber::where('product_id', $product_id)
                                    ->whereIn('serial', array_map('trim', $imeis))
                                    ->update(['status' => 0]);
                    }
                }


                //create purchase log call createPurchaseLog function
                $id = $invoice->id;
                $type = 'Invoice';

                // Transaction
                $transaction = new Transaction();
                $transaction->transaction_type = $type;
                if (auth()->user()->branch_id == 1) {
                    $transaction->branch_id = $request->branch_id;
                } else {
                    $transaction->branch_id = auth()->user()->branch_id;
                }
                $transaction->date = $request->date;
                $transaction->invoice_id = $id;
                $transaction->customer_id = $request->customer_id;
                $transaction->debit = $request->payable_amount;
                $transaction->credit = NULL;
                $transaction->created_by = auth()->user()->id;
                $transaction->save();
                $this->createInvoiceInv($request, $id);

                if ($request->is_installment == 1) {
                    $installment = \App\Models\Installment::create([
                        'invoice_id' => $invoice->id,
                        'customer_id' => $invoice->customer_id,
                        'branch_id' => $invoice->branch_id,
                        'advance_pay' => $request->inst_advance_pay ?? 0.00,
                        'remaining_amount' => $request->inst_remaining ?? 0.00,
                        'total_installments' => $request->inst_total_installments ?? 1,
                        'interval_days' => $request->inst_interval_days ?? 30,
                        'interest_percentage' => $request->inst_interest_percent ?? 0.00,
                        'interest_amount' => $request->inst_interest_amount ?? 0.00,
                        'per_installment_amount' => $request->inst_per_installment ?? 0.00,
                        'total_with_interest' => $request->inst_total_with_interest ?? 0.00,
                        'first_due_date' => $request->inst_first_due_date ?? $request->date,
                        'last_due_date' => Carbon::parse($request->inst_first_due_date ?? $request->date)->addDays((($request->inst_total_installments ?? 1) - 1) * ($request->inst_interval_days ?? 30))->toDateString(),
                        'status' => 'pending',
                        'created_by' => auth()->user()->id,
                    ]);

                    $totalInstallments = (int) ($request->inst_total_installments ?? 1);
                    $intervalDays = (int) ($request->inst_interval_days ?? 30);
                    $totalWithInterest = (double) ($request->inst_total_with_interest ?? 0.00);
                    $firstDueDate = Carbon::parse($request->inst_first_due_date ?? $request->date);
                    
                    $perInstallment = round($totalWithInterest / $totalInstallments, 2);
                    $sumSchedules = 0;

                    for ($i = 1; $i <= $totalInstallments; $i++) {
                        $dueDate = $firstDueDate->copy()->addDays(($i - 1) * $intervalDays)->toDateString();
                        
                        if ($i === $totalInstallments) {
                            $amount = $totalWithInterest - $sumSchedules;
                        } else {
                            $amount = $perInstallment;
                            $sumSchedules += $amount;
                        }

                        \App\Models\InstallmentSchedule::create([
                            'installment_id' => $installment->id,
                            'installment_no' => $i,
                            'due_date' => $dueDate,
                            'amount' => $amount,
                            'paid_amount' => 0.00,
                            'status' => 'pending',
                        ]);
                    }
                }
            }
        });
        // invoiceSMS($request->customer_id, $invoice->id);
        $invoice->load('customer', 'invoiceItems.product');
        session()->flash('success', __('Invoice Created Successfully'));
        logActivity('Create Invoice', "Invoice #{$invoice->invoice_no} created for customer: {$invoice->customer?->name}", $invoice);

        // If this was a Pre-Order → Sale conversion, mark pre-order as converted and go to Sale List
        if ($convertMode && $preOrderId) {
            $preOrder = \App\Models\PreOrder::find($preOrderId);
            if ($preOrder && $preOrder->status === 'pending') {
                $preOrder->status = 'converted';
                $preOrder->save();
            }
            session()->flash('success', __('Pre-Order converted to Sale successfully! Invoice #:no created.', ['no' => $invoice->invoice_no]));
            return redirect()->route('invoice.index');
        }

        return redirect()->route('invoice.print', $invoice->id);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['customer', 'user', 'branch', 'installment.schedules'])->findOrFail($id);
        $inv_items = InvoiceItem::where('invoice_id', $id)
            ->with(['product.unit.related_unit', 'product_variation.size', 'product_variation.color'])
            ->get();
        $payments = BankTransaction::where('invoice_id', $id)
            ->with('bank_account')
            ->orderBy('date', 'asc')
            ->get();

        return view('backend.pages.invoice.show', compact('invoice', 'inv_items', 'payments'));
    }

    private function validateImeiSelections(Request $request): void
    {
        $productIds = $request->input('product_id', []);
        $imeiValues = $request->input('imei', []);
        $qtyValues = $request->input('main_qty', []);

        $branchId = auth()->user()->branch_id == 1
            ? (int) ($request->branch_id ?? auth()->user()->branch_id)
            : auth()->user()->branch_id;

        $used = [];

        foreach ($productIds as $key => $productId) {
            $product = Product::find($productId);
            if (!$product || (int) $product->imei !== 1) {
                continue;
            }

            $imeis = $this->parseImeis($imeiValues[$key] ?? null);
            if (count($imeis) === 0) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Please select IMEI for IMEI products.'),
                ]);
            }

            if (count($imeis) !== count(array_unique($imeis))) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Duplicate IMEI selected.'),
                ]);
            }

            $qty = isset($qtyValues[$key]) ? (int) $qtyValues[$key] : 0;
            if ($qty > 0 && $qty !== count($imeis)) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Selected IMEI count must match quantity.'),
                ]);
            }

            foreach ($imeis as $imei) {
                if (isset($used[$imei])) {
                    throw ValidationException::withMessages([
                        "imei.$key" => __('Same IMEI cannot be used multiple times in one sale.'),
                    ]);
                }
                $used[$imei] = true;
            }

            $available = $this->availableImeisForProduct((int) $productId, $branchId);
            $missing = array_values(array_diff($imeis, $available));
            if (count($missing) > 0) {
                throw ValidationException::withMessages([
                    "imei.$key" => __('Some selected IMEI are not available.'),
                ]);
            }
        }
    }

    private function parseImeis(?string $value): array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }

        $value = str_replace("\r", "", $value);
        $parts = preg_split('/\r?\n/', $value) ?: [];

        $parts = array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $parts), static fn ($v) => $v !== ''));
        return $parts;
    }

    private function availableImeisForProduct(int $productId, int $branchId): array
    {
        // 1. Get all Sold IMEIs (Active Invoices)
        $soldImeisRaw = InvoiceItem::where('product_id', $productId)
            ->whereNotNull('imei')
            ->whereHas('invoice', function ($q) {
                $q->where('status', '!=', 2); // Not Cancelled
            })
            ->pluck('imei')
            ->toArray();

        $allSold = [];
        foreach ($soldImeisRaw as $si) {
            $allSold = array_merge($allSold, $this->parseImeis((string) $si));
        }

        // 2. Get all Damaged IMEIs
        $damagedImeisRaw = DamageItem::where('product_id', $productId)
            ->whereNotNull('imei')
            ->pluck('imei')
            ->toArray();
        foreach ($damagedImeisRaw as $di) {
            $allSold = array_merge($allSold, $this->parseImeis((string) $di));
        }

        // 3. Get all Transferred IMEIs (Out)
        $transferredImeisRaw = TransferItem::where('product_id', $productId)
            ->whereNotNull('imei')
            ->where('from_branch_id', $branchId)
            ->pluck('imei')
            ->toArray();
        foreach ($transferredImeisRaw as $ti) {
            $allSold = array_merge($allSold, $this->parseImeis((string) $ti));
        }

        $allSold = array_values(array_unique($allSold));

        // 4. Get all Purchased/Stocked IMEIs for this branch
        $serials = SerialNumber::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->pluck('serial')
            ->map(static fn ($s) => trim((string) $s))
            ->filter()
            ->values();

        if ($serials->isNotEmpty()) {
            $availableSerials = array_values(array_diff($serials->all(), $allSold));
            return $availableSerials;
        }

        // Fallback to PurchaseItems if SerialNumber table is empty
        $purchaseItems = PurchaseItem::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->whereNotNull('imei')
            ->get(['imei']);
        $allImeis = [];

        foreach ($purchaseItems as $pi) {
            $allImeis = array_merge($allImeis, $this->parseImeis((string) $pi->imei));
        }

        $available = array_values(array_diff(array_unique($allImeis), $allSold));
        return $available;
    }

    public function invoiceEdit($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        $invoiceItem = InvoiceItem::where('invoice_id', $id)->get();

        $previousDue = 0;
        $totalCustomerDue = 0;

        if ($invoice && $invoice->customer_id) {
            $previousDue = Invoice::where('customer_id', $invoice->customer_id)
                ->where('id', '!=', $id)
                ->sum('total_due');

            $totalCustomerDue = Invoice::where('customer_id', $invoice->customer_id)
                ->sum('total_due');
        }

        $bank_accounts = BankAccount::where('status', 1)->get();

        $existingBankAllocations = BankTransaction::where('invoice_id', $id)
            ->selectRaw("bank_id, SUM(CASE WHEN trans_type = 'deposit' THEN amount WHEN trans_type = 'withdraw' THEN -amount ELSE 0 END) as net_amount")
            ->groupBy('bank_id')
            ->pluck('net_amount', 'bank_id')
            ->toArray();

        $primaryBankId = 1;
        if (!empty($existingBankAllocations)) {
            $primaryBankId = array_key_first($existingBankAllocations);
        } else {
            $firstBank = $bank_accounts->first();
            if ($firstBank) {
                $primaryBankId = $firstBank->id;
            }
        }

        $hasMultipleBanks = count(array_filter($existingBankAllocations, fn($v) => (float)$v > 0)) > 1;

        return view('backend.pages.invoice.edit', compact(
            'invoice',
            'invoiceItem',
            'previousDue',
            'totalCustomerDue',
            'bank_accounts',
            'existingBankAllocations',
            'primaryBankId',
            'hasMultipleBanks'
        ));
    }
    public function invoiceUpdate($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);
        $editDate = date('Y-m-d');
        $branchId = $invoice->branch_id;

        if (!$request->has('product_id') || !is_array($request->product_id) || count($request->product_id) === 0) {
            session()->flash('error', __('Please select at least one product to update the invoice.'));
            return redirect()->back()->withInput();
        }

        // ===== 1. SERVER-SIDE STOCK VALIDATION ON SALE EDIT =====
        $oldItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
        $oldQtyMap = [];
        $oldItemsMap = [];

        foreach ($oldItems as $oldItem) {
            $product = Product::find($oldItem->product_id);
            $varId = $oldItem->product_variation_id ?? 0;
            $key = $oldItem->product_id . '_' . $varId;

            if ($product && $product->unit && $product->unit->related_unit != null) {
                $oldUnits = ($oldItem->main_qty * $product->unit->related_value) + ($oldItem->sub_qty ?? 0);
            } else {
                $oldUnits = (float) $oldItem->main_qty;
            }
            $oldQtyMap[$key] = ($oldQtyMap[$key] ?? 0) + $oldUnits;

            $varName = '';
            if ($oldItem->product_variation_id) {
                $var = \App\Models\ProductVariation::with('size', 'color')->find($oldItem->product_variation_id);
                if ($var) {
                    $varName = " (" . ($var->size?->size ?? '') . "-" . ($var->color?->color ?? '') . ")";
                }
            }
            $fullName = ($product?->name ?? 'Product') . $varName;

            $oldItemsMap[$key] = [
                'product_id'   => $oldItem->product_id,
                'name'         => $fullName,
                'qty'          => $oldUnits,
                'main_qty'     => $oldItem->main_qty,
                'sub_qty'      => $oldItem->sub_qty ?? 0,
                'rate'         => $oldItem->rate,
                'discount'     => $oldItem->product_discount ?? 0,
                'subtotal'     => $oldItem->subtotal,
            ];
        }

        $newProductIds = $request->input('product_id', []);
        $newQtyMap = [];
        $newItemsMap = [];

        foreach ($newProductIds as $k => $pId) {
            $product = Product::find($pId);
            if (!$product || $product->is_service == 1) {
                continue;
            }

            $varId = $request->variation_id[$k] ?? null;
            $mainQty = (float)($request->main_qty[$k] ?? 1);
            $subQty  = (float)($request->sub_qty[$k] ?? 0);

            if ($product->unit && $product->unit->related_unit != null) {
                $newUnits = ($mainQty * $product->unit->related_value) + $subQty;
            } else {
                $newUnits = $mainQty;
            }

            $mapKey = $pId . '_' . ($varId ?? 0);
            $newQtyMap[$mapKey] = ($newQtyMap[$mapKey] ?? 0) + $newUnits;

            $varName = '';
            if ($varId) {
                $var = \App\Models\ProductVariation::with('size', 'color')->find($varId);
                if ($var) {
                    $varName = " (" . ($var->size?->size ?? '') . "-" . ($var->color?->color ?? '') . ")";
                }
            }
            $fullName = $product->name . $varName;

            $newItemsMap[$mapKey] = [
                'product_id'   => $pId,
                'name'         => $fullName,
                'qty'          => $newUnits,
                'main_qty'     => $mainQty,
                'sub_qty'      => $subQty,
                'rate'         => (float)($request->rate[$k] ?? 0),
                'discount'     => (float)($request->product_discount[$k] ?? 0),
                'subtotal'     => (float)($request->sub_total[$k] ?? 0),
            ];
        }

        // Validate stock for any added or increased quantity
        foreach ($newQtyMap as $key => $reqQty) {
            list($pId, $vId) = explode('_', $key);
            $vId = ($vId == 0) ? null : $vId;
            $product = Product::find($pId);
            if (!$product || $product->is_service == 1) continue;

            $alreadyInInvoice = $oldQtyMap[$key] ?? 0;
            $additionalNeeded = $reqQty - $alreadyInInvoice;

            if ($additionalNeeded > 0) {
                $purQuery = PurchaseItem::where('product_id', $pId)->where('branch_id', $branchId);
                if ($vId) {
                    $purQuery->where('product_variation_id', $vId);
                }
                $availableInStock = (float) $purQuery->sum('stock_qty');

                if ($availableInStock < $additionalNeeded) {
                    $prodName = $newItemsMap[$key]['name'] ?? $product->name;
                    session()->flash('error', __("Insufficient stock for ':prod'. Stock available: :stock, Additional needed: :req", [
                        'prod'  => $prodName,
                        'stock' => $availableInStock,
                        'req'   => $additionalNeeded
                    ]));
                    return redirect()->back()->withInput();
                }
            }
        }

        // Track old total paid before update
        $oldTotalPaid = (float) $invoice->total_paid;

        // Perform Update in DB Transaction
        DB::transaction(function () use ($request, $invoice, $oldItems, $oldItemsMap, $newItemsMap, $editDate, $oldTotalPaid) {
            
            // Update Invoice fields
            $totalAmount = (float)$request->total_amount;
            $paymentType = $request->input('payment_type', 'pos');
            $validBankAmounts = [];

            if ($paymentType === 'checking') {
                $amounts = $request->input('amounts', []);
                if (is_array($amounts)) {
                    foreach ($amounts as $bId => $val) {
                        if ((float)$val > 0) {
                            $validBankAmounts[$bId] = (float)$val;
                        }
                    }
                }
                $totalPaid = array_sum($validBankAmounts);
            } else {
                $totalPaid = (float)$request->input('total_paid', 0);
                $bankId = $request->input('bank_id', 1);
                if ($totalPaid > 0) {
                    $validBankAmounts[$bankId] = $totalPaid;
                }
            }

            $due = max(0, $totalAmount - $totalPaid);
            $change = max(0, $totalPaid - $totalAmount);

            $invoice->date = $request->date;
            $invoice->estimated_amount = $request->estimated_amount;
            $invoice->discount = ($request->discount_amount == null) ? '0.00' : $request->discount_amount;
            $invoice->discount_amount = $request->discount;
            $invoice->total_amount = $totalAmount;
            $invoice->total_paid = $totalPaid;
            $invoice->total_due = $due;
            $invoice->change_amount = $change;
            $invoice->status = ($due > 0) ? 0 : 1;
            $invoice->updated_by = auth()->id();
            $previousDue = (float)($request->previous_due ?? 0);
            if ($previousDue <= 0 && $request->customer_id && $request->customer_id != 1) {
                $priorInvDue = (float) Invoice::where('customer_id', $request->customer_id)->where('id', '<', $invoice->id)->sum('total_due');
                $custObj = Customer::find($request->customer_id);
                $openBal = $custObj ? open_balance_customer($request->customer_id, $custObj->due_amount) : 0;
                $previousDue = $priorInvDue + $openBal;
            }
            $invoice->previous_due = $previousDue;
            if ($request->has('note')) {
                $invoice->note = $request->note;
            }
            if ($request->filled('platform_id')) {
                $invoice->platform_id = $request->platform_id;
            }
            if ($request->filled('source_link')) {
                $invoice->source_link = $request->source_link;
            }
            $invoice->save();

            // STEP 1: Restore stock from old items
            foreach ($oldItems as $oldItem) {
                if ($oldItem->imei) {
                    $oldImeis = $this->parseImeis($oldItem->imei);
                    SerialNumber::where('product_id', $oldItem->product_id)
                                ->whereIn('serial', array_map('trim', $oldImeis))
                                ->update(['status' => 1]);
                }
                
                $product = Product::find($oldItem->product_id);
                if ($product && $product->is_service == 0) {
                    if ($product->unit && $product->unit->related_unit != null) {
                        $oldQty = ($oldItem->main_qty * $product->unit->related_value) + ($oldItem->sub_qty ?? 0);
                    } else {
                        $oldQty = $oldItem->main_qty;
                    }
                    restoreToFIFO($product->id, $oldQty, $oldItem->product_variation_id, $invoice->branch_id);
                }
                
                $oldItem->delete();
            }

            // STEP 2: Save new items and deduct stock
            $productIds = $request->input('product_id', []);
            foreach ($productIds as $key => $product_id) {
                $invoice_item = new InvoiceItem();
                $find_unit_id = Product::where('id', $product_id)->first();
                
                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->product_id = $product_id;
                $invoice_item->rate = $request->rate[$key];
                $invoice_item->product_discount = $request->product_discount[$key] ?? 0.00;
                
                $variation_id = $request->variation_id[$key] ?? null;
                $invoice_item->product_variation_id = $variation_id;

                $wVal = $request->warranty_value[$key] ?? null;
                $wUnit = $request->warranty_unit[$key] ?? null;
                if (empty($wVal) && $find_unit_id) {
                    $find_unit_id->loadMissing('warranty');
                    if ($find_unit_id->warranty) {
                        $wVal = $find_unit_id->warranty->duration;
                        $wUnit = $find_unit_id->warranty->period;
                    } elseif (!empty($find_unit_id->warranty_value)) {
                        $wVal = $find_unit_id->warranty_value;
                        $wUnit = $find_unit_id->warranty_unit ?: 'Month';
                    }
                }
                $invoice_item->warranty_value = $wVal;
                $invoice_item->warranty_unit = $wUnit;
                $invoice_item->branch_id = $invoice->branch_id;

                $selectedImeis = [];
                if ($find_unit_id && (int) $find_unit_id->imei === 1) {
                    $selectedImeis = $this->parseImeis($request->imei[$key] ?? null);
                    $invoice_item->imei = !empty($selectedImeis) ? implode("\n", $selectedImeis) : null;
                } else {
                    $invoice_item->imei = null;
                }

                if ($find_unit_id && $find_unit_id->is_service == 0) {
                    if ((int) $find_unit_id->imei === 1) {
                        $qty = count($selectedImeis);
                        $invoice_item->main_qty = $qty;
                        $invoice_item->sub_qty = 0;
                        $invoice_item->actual_main = $qty;
                        $invoice_item->actual_sub = 0;
                        $saleQty = $qty;

                        $rate = (float) ($request->rate[$key] ?? 0);
                        $discount = (float) ($request->product_discount[$key] ?? 0);
                        $lineSubtotal = ($rate * $qty) - $discount;

                        $invoice_item->subtotal = $lineSubtotal;
                        $invoice_item->actual_total = $lineSubtotal;
                        $invoice_item->inv_subtotal = $lineSubtotal;
                    } elseif ($find_unit_id->unit && $find_unit_id->unit->related_unit == null) {
                        $invoice_item->main_qty = $request->main_qty[$key];
                        $invoice_item->actual_main = $request->main_qty[$key];
                        $saleQty = $request->main_qty[$key];

                        $invoice_item->subtotal = $request->sub_total[$key];
                        $invoice_item->actual_total = $request->sub_total[$key];
                        $invoice_item->inv_subtotal = $request->sub_total[$key];
                    } else {
                        $invoice_item->main_qty = $request->main_qty[$key];
                        $invoice_item->sub_qty = $request->sub_qty[$key];
                        $invoice_item->actual_main = $request->main_qty[$key];
                        $invoice_item->actual_sub = $request->sub_qty[$key];
                        $main = $request->main_qty[$key] * ($find_unit_id->unit ? $find_unit_id->unit->related_value : 1);
                        $sub = $request->sub_qty[$key] ?? 0;
                        $saleQty = $main + $sub;

                        $invoice_item->subtotal = $request->sub_total[$key];
                        $invoice_item->actual_total = $request->sub_total[$key];
                        $invoice_item->inv_subtotal = $request->sub_total[$key];
                    }
                    
                    $invoice_item->pur_subtotal = calculateUnitPriceUsingFIFO($product_id, $saleQty, $variation_id, $invoice->branch_id);
                } else {
                    $invoice_item->main_qty = $request->main_qty[$key];
                    $invoice_item->subtotal = $request->sub_total[$key];
                    $invoice_item->inv_subtotal = $request->sub_total[$key];
                    $invoice_item->pur_subtotal = ($find_unit_id->purchase_price ?? 0.00) * $request->main_qty[$key];
                }
                
                $invoice_item->date = $request->date;
                $invoice_item->save();

                if ($invoice_item->imei) {
                    $imeis = $selectedImeis ?: $this->parseImeis($invoice_item->imei);
                    SerialNumber::where('product_id', $product_id)
                                ->whereIn('serial', array_map('trim', $imeis))
                                ->update(['status' => 0]);
                }
            }

            // ===== STEP 3: CASH & BANK ACCOUNT PAYMENT TRANSACTIONS (SINGLE OR MULTIPLE) =====
            BankTransaction::where('invoice_id', $invoice->id)
                ->whereIn('pay_type', ['invpay', 'invpay_edit', 'rtn_pay_edit'])
                ->delete();

            Transaction::where('invoice_id', $invoice->id)
                ->whereIn('transaction_type', [
                    'Received from Customer',
                    'Received from Customer (Invoice Edit)',
                    'Return Money to Customer (Invoice Edit)'
                ])
                ->delete();

            foreach ($validBankAmounts as $bId => $amt) {
                BankTransaction::create([
                    'trans_type' => 'deposit',
                    'pay_type'   => 'invpay',
                    'branch_id'  => $invoice->branch_id,
                    'date'       => $request->date,
                    'bank_id'    => $bId,
                    'invoice_id' => $invoice->id,
                    'amount'     => $amt,
                    'created_by' => auth()->id(),
                ]);

                Transaction::create([
                    'transaction_type' => 'Received from Customer',
                    'branch_id'        => $invoice->branch_id,
                    'date'             => $request->date,
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $request->customer_id,
                    'debit'            => null,
                    'credit'           => $amt,
                    'bank_id'          => $bId,
                    'created_by'       => auth()->id(),
                ]);
            }

            // ===== STEP 4: CUSTOMER LEDGER DEBIT TRANSACTION =====
            Transaction::where('invoice_id', $invoice->id)
                ->where(function ($q) {
                    $q->where('transaction_type', 'Invoice')
                      ->orWhere('transaction_type', 'like', '%Invoice Product%');
                })
                ->delete();

            Transaction::create([
                'transaction_type' => 'Invoice',
                'branch_id'        => $invoice->branch_id,
                'date'             => $request->date,
                'invoice_id'       => $invoice->id,
                'customer_id'      => $request->customer_id,
                'debit'            => $totalAmount,
                'credit'           => null,
                'created_by'       => auth()->id(),
            ]);

            // ===== STEP 4: PRODUCT EDIT LOGS AUDIT TRAIL =====
            foreach ($newItemsMap as $k => $newItem) {
                if (!isset($oldItemsMap[$k])) {
                    // Added Product
                    \App\Models\InvoiceEditLog::create([
                        'invoice_id'   => $invoice->id,
                        'user_id'      => auth()->id(),
                        'edit_date'    => $editDate,
                        'product_id'   => $newItem['product_id'],
                        'product_name' => $newItem['name'],
                        'action'       => 'Added Product',
                        'details'      => "Added {$newItem['qty']} Qty @ {$newItem['rate']} Tk (Subtotal: {$newItem['subtotal']} Tk) on {$editDate}",
                        'new_value'    => "Qty: {$newItem['qty']}, Subtotal: {$newItem['subtotal']}",
                    ]);
                } else {
                    $oldItem = $oldItemsMap[$k];
                    if ($newItem['qty'] != $oldItem['qty'] || $newItem['rate'] != $oldItem['rate'] || $newItem['subtotal'] != $oldItem['subtotal']) {
                        // Modified Product
                        \App\Models\InvoiceEditLog::create([
                            'invoice_id'   => $invoice->id,
                            'user_id'      => auth()->id(),
                            'edit_date'    => $editDate,
                            'product_id'   => $newItem['product_id'],
                            'product_name' => $newItem['name'],
                            'action'       => 'Modified Product',
                            'details'      => "Updated {$newItem['name']}: Qty ({$oldItem['qty']} → {$newItem['qty']}), Rate ({$oldItem['rate']} → {$newItem['rate']} Tk), Subtotal ({$oldItem['subtotal']} Tk) on {$editDate}",
                            'old_value'    => "Qty: {$oldItem['qty']}, Rate: {$oldItem['rate']}, Subtotal: {$oldItem['subtotal']}",
                            'new_value'    => "Qty: {$newItem['qty']}, Rate: {$newItem['rate']}, Subtotal: {$newItem['subtotal']}",
                        ]);
                    }
                }
            }

            foreach ($oldItemsMap as $k => $oldItem) {
                if (!isset($newItemsMap[$k])) {
                    // Removed Product
                    \App\Models\InvoiceEditLog::create([
                        'invoice_id'   => $invoice->id,
                        'user_id'      => auth()->id(),
                        'edit_date'    => $editDate,
                        'product_id'   => $oldItem['product_id'],
                        'product_name' => $oldItem['name'],
                        'action'       => 'Removed Product',
                        'details'      => "Removed {$oldItem['name']} (Was {$oldItem['qty']} Qty @ {$oldItem['rate']} Tk) on {$editDate}",
                        'old_value'    => "Qty: {$oldItem['qty']}, Subtotal: {$oldItem['subtotal']}",
                    ]);
                }
            }
        });

        session()->flash('success', __('Invoice Updated Successfully'));
        logActivity('Update Invoice', "Invoice #{$invoice->invoice_no} updated", $invoice);
        return redirect()->route('invoice.print', $invoice->id);
    }

    public function invoiceExchange($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        $categories = Category::orderBy('name', 'ASC')->get();
        $customers = Customer::get();
        $bank_accounts = BankAccount::where('status', 1)->get();

        $existingBankAllocations = BankTransaction::where('invoice_id', $id)
            ->selectRaw("bank_id, SUM(CASE WHEN trans_type = 'deposit' THEN amount WHEN trans_type = 'withdraw' THEN -amount ELSE 0 END) as net_amount")
            ->groupBy('bank_id')
            ->pluck('net_amount', 'bank_id')
            ->toArray();

        $primaryBankId = 1;
        if (!empty($existingBankAllocations)) {
            $primaryBankId = array_key_first($existingBankAllocations);
        } else {
            $firstBank = $bank_accounts->first();
            if ($firstBank) {
                $primaryBankId = $firstBank->id;
            }
        }

        $hasMultipleBanks = count(array_filter($existingBankAllocations, fn($v) => (float)$v > 0)) > 1;

        $viewData = compact(
            'invoice',
            'categories',
            'customers',
            'bank_accounts',
            'existingBankAllocations',
            'primaryBankId',
            'hasMultipleBanks'
        );

        if (env('APP_SC') == 'yes') {
            return view('backend.pages.invoice.sc-exchange', $viewData);
        } else {
            return view('backend.pages.invoice.exchange', $viewData);
        }
    }

    public function returnInvoice($id, Request $request)
    {
        $item = InvoiceItem::where('id', $id)->first();
        if (!$item) {
            session()->flash('error', __('Invoice item not found'));
            return redirect()->back();
        }

        DB::transaction(function () use ($request, $item) {
            if ($item->product && $item->product->is_service == 0) {
                $rtnQty = (float)($request->rtn_main ?: $item->main_qty ?: 1);
                $branchId = $item->branch_id ?: ($item->invoice?->branch_id ?? null);
                if (function_exists('restoreToFIFO')) {
                    restoreToFIFO($item->product_id, $rtnQty, $item->product_variation_id, $branchId);
                }
                if (!empty($item->imei)) {
                    $imeis = array_filter(array_map('trim', explode("\n", $item->imei)));
                    if (!empty($imeis)) {
                        \App\Models\SerialNumber::where('product_id', $item->product_id)
                            ->whereIn('serial', $imeis)
                            ->update(['status' => 1]);
                    }
                }
            }

            $item->delete();

            $main_qty = $request->rtn_main;
            $rate = $request->rate;
            $total = $request->totalAmount;
            $esti_amount = $request->estimateAmount;
            $due = $request->dueAmount;
            $paid = $request->paidAmount;

            if ($due == 0.00) {
                $esti_amount = $esti_amount - ($rate * $main_qty);
                $due = $esti_amount - $paid;
            }

            Invoice::where('id', $request->invoice)->update([
                'estimated_amount' => $esti_amount,
                'total_amount' => $esti_amount,
                'total_due' => $due,
                'return_amount' => $request->return_amount,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        });
        $invoice = Invoice::with('customer')->find($request->invoice);
        session()->flash('success', __('Successfully return Product'));
        logActivity('Return Product', "Product returned for Invoice #{$invoice?->invoice_no}", $invoice);
        return redirect()->back();
    }

    public function updateExchangeInvoice(Request $request)
    {
        $invoice = Invoice::findOrFail($request->id);
        $invoice->date = $request->date ?: date('Y-m-d');
        $invoice->estimated_amount = (float)($request->estimated_amount ?? 0);
        $invoice->discount = $request->discount_amount ?? 0.00;
        $invoice->discount_amount = $request->discount ?? 0.00;
        $payableAmount = (float)$request->payable_amount;
        $invoice->total_amount = $payableAmount;

        $paymentType = $request->input('payment_type', 'pos');
        $validBankAmounts = [];

        if ($paymentType === 'checking') {
            $amounts = $request->input('amounts', []);
            if (is_array($amounts)) {
                foreach ($amounts as $bId => $val) {
                    if ((float)$val > 0) {
                        $validBankAmounts[$bId] = (float)$val;
                    }
                }
            }
            $totalPaid = array_sum($validBankAmounts);
        } else {
            $totalPaid = (float)($request->total_paid ?? 0);
            $bankId = $request->input('bank_id', 1);
            if ($totalPaid > 0) {
                $validBankAmounts[$bankId] = $totalPaid;
            }
        }

        $invoice->total_paid = $totalPaid;
        $due = $payableAmount - $totalPaid;
        $invoice->total_due = max(0, $due);
        $invoice->change_amount = max(0, -$due);
        $invoice->status = ($due > 0) ? 0 : 1;

        if (!$request->has('product_id') || !is_array($request->product_id) || count($request->product_id) === 0) {
            session()->flash('error', __('Please select at least one product to exchange.'));
            return redirect()->back()->withInput();
        }

        DB::transaction(function () use ($request, $invoice, $payableAmount, $validBankAmounts) {

            // STEP 1️⃣ — Restore stock from old invoice items
            $oldExchangeItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
            foreach ($oldExchangeItems as $oldItem) {
                if ($oldItem->imei) {
                    $oldImeis = $this->parseImeis($oldItem->imei);
                    SerialNumber::where('product_id', $oldItem->product_id)
                                ->whereIn('serial', array_map('trim', $oldImeis))
                                ->update(['status' => 1]);
                }
            }
            
            foreach ($oldExchangeItems as $item) {
                $product = Product::find($item->product_id);
                $variationId = $item->product_variation_id;
                if ($product && $product->is_service == 0) {
                    // Convert to base qty
                    if ($product->unit && $product->unit->related_unit == null) {
                        $oldQty = $item->main_qty;
                    } else {
                        $oldQty = ($item->main_qty * ($product->unit ? $product->unit->related_value : 1)) + ($item->sub_qty ?? 0);
                    }
                    restoreToFIFO($product->id, $oldQty, $variationId, $invoice->branch_id);
                }
                $item->delete();
            }

            // STEP 2️⃣ — Create updated items and deduct new stock
            $productIds = $request->input('product_id', []);
            foreach ($productIds as $key => $productId) {
                $product = Product::find($productId);
                $variationId = $request->variation[$key] ?? null;

                $invoiceItem = new InvoiceItem();
                $invoiceItem->date = $invoice->date;
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->branch_id = $invoice->branch_id;
                $invoiceItem->product_id = $productId;
                $invoiceItem->product_variation_id = $variationId;
                $invoiceItem->rate = $request->rate[$key];

                if ($product && $product->is_service == 0) {
                    if ($product->unit && $product->unit->related_unit == null) {
                        $invoiceItem->main_qty = $request->main_qty[$key];
                        $invoiceItem->actual_main = $request->main_qty[$key];
                        $saleQty = $request->main_qty[$key];
                    } else {
                        $invoiceItem->main_qty = $request->main_qty[$key];
                        $invoiceItem->sub_qty = $request->sub_qty[$key] ?? 0;
                        $invoiceItem->actual_main = $request->main_qty[$key];
                        $invoiceItem->actual_sub = $request->sub_qty[$key] ?? 0;
                        $saleQty = ($request->main_qty[$key] * ($product->unit ? $product->unit->related_value : 1)) + ($request->sub_qty[$key] ?? 0);
                    }
                    $invoiceItem->pur_subtotal = calculateUnitPriceUsingFIFO($productId, $saleQty, $variationId, $invoice->branch_id);
                } else {
                    $invoiceItem->main_qty = $request->main_qty[$key];
                    $invoiceItem->actual_main = $request->main_qty[$key];
                    $invoiceItem->pur_subtotal = ($product->purchase_price ?? 0.00) * $request->main_qty[$key];
                }

                $selectedImeis = [];
                if ($product && (int) $product->imei === 1) {
                    $selectedImeis = $this->parseImeis($request->imei[$key] ?? null);
                    $invoiceItem->imei = !empty($selectedImeis) ? implode("\n", $selectedImeis) : null;
                } else {
                    $invoiceItem->imei = null;
                }
                $invoiceItem->subtotal = $request->sub_total[$key];
                $invoiceItem->actual_total = $request->sub_total[$key];
                $invoiceItem->inv_subtotal = $request->sub_total[$key];
                $invoiceItem->save();

                // Mark IMEI as sold
                if ($invoiceItem->imei) {
                    $imeis = $selectedImeis ?: $this->parseImeis($invoiceItem->imei);
                    SerialNumber::where('product_id', $productId)
                                ->whereIn('serial', array_map('trim', $imeis))
                                ->update(['status' => 0]);
                }
            }

            // STEP 3️⃣ — Update Transactions and Bank Deposits
            BankTransaction::where('invoice_id', $invoice->id)->delete();
            Transaction::where('invoice_id', $invoice->id)->delete();

            // Customer ledger Debit
            Transaction::create([
                'transaction_type' => 'Invoice',
                'branch_id'        => $invoice->branch_id,
                'date'             => $invoice->date,
                'invoice_id'       => $invoice->id,
                'customer_id'      => $request->customer_id,
                'debit'            => $payableAmount,
                'credit'           => null,
                'created_by'       => auth()->id(),
            ]);

            // Bank Deposits & Customer Payments
            foreach ($validBankAmounts as $bId => $amt) {
                BankTransaction::create([
                    'trans_type' => 'deposit',
                    'pay_type'   => 'invpay',
                    'branch_id'  => $invoice->branch_id,
                    'date'       => $invoice->date,
                    'bank_id'    => $bId,
                    'invoice_id' => $invoice->id,
                    'amount'     => $amt,
                    'created_by' => auth()->id(),
                ]);

                Transaction::create([
                    'transaction_type' => 'Received from Customer',
                    'branch_id'        => $invoice->branch_id,
                    'date'             => $invoice->date,
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $request->customer_id,
                    'debit'            => null,
                    'credit'           => $amt,
                    'bank_id'          => $bId,
                    'created_by'       => auth()->id(),
                ]);
            }

            $invoice->save();
        });

        session()->flash('success', __('Successfully updated exchange invoice.'));
        logActivity('Exchange Invoice', "Invoice #{$invoice->invoice_no} exchanged", $invoice);
        return redirect()->route('invoice.index');
    }


    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoiceItems = InvoiceItem::where('invoice_id', $id)->get();

        if ($invoice->status != 2) {
            foreach ($invoiceItems as $item) {

                $product = Product::with('unit')->find($item->product_id);

                // ✅ Quantity calculation (main + sub)
                if ($product->unit->related_unit == null) {
                    $qty = $item->main_qty;
                } else {
                    $main = $item->main_qty * $product->unit->related_value;
                    $qty = $main + $item->sub_qty;
                }

                // ✅ restore stock to FIFO queue correctly (including branch constraint)
                if ($product->is_service == 0) {
                    restoreToFIFO(
                        $product->id,
                        $qty,
                        $item->product_variation_id,
                        $invoice->branch_id
                    );
                }

                // Restore IMEI status
                if ($item->imei) {
                    $imeis = $this->parseImeis($item->imei);
                    SerialNumber::where('product_id', $item->product_id)
                                ->whereIn('serial', array_map('trim', $imeis))
                                ->update(['status' => 1]);
                }

                $item->delete();
            }
        } else {
            foreach ($invoiceItems as $item) {
                $item->delete();
            }
        }

        // transactions rollback
        $transactions = Transaction::where('invoice_id', $invoice->id)->get();
        foreach ($transactions as $transaction) {
            if ($transaction->actual_pay_id) {
                $actualpay = ActualPayment::find($transaction->actual_pay_id);
                if ($actualpay) {
                    $actualpay->amount -= $transaction->credit;
                    $actualpay->amount <= 0 ? $actualpay->delete() : $actualpay->save();
                }
            }
            $transaction->delete();
        }

        // Capture info before delete
        $invoice->load('customer', 'invoiceItems.product');
        logActivity('Delete Invoice', "Invoice #{$invoice->invoice_no} for {$invoice->customer?->name} deleted", $invoice);

        $invoice->delete();
        session()->flash('success', __('Invoice deleted successfully'));
        return back();
    }

    public function invoicePay($id)
    {

        //get invoice with supplier and user by id
        $invoice = Invoice::with('customer', 'user', 'invoiceItems')
            ->where('id', $id)->first();

        if ($invoice->total_due <= 0) {
            return redirect()->back();
        }
        // return response()->json($invoice);
        //get all payment methods
        $bank_accounts = BankAccount::where('status', 1)->get();
        // return response()->json($bank_accounts);

        return view('backend.pages.invoice.pay', compact('invoice', 'bank_accounts'));
    }

    public function storeInvoiceLog(Request $request)
    {
        if ($request->paid_amount <= 0) {
            session()->flash('error', __('Please Enter Amount'));
            return redirect()->back();
        }

        $id = $request->invoice_id;
        $this->createInvoiceDue($request, $id);

        $invoice = Invoice::find($id);
        session()->flash('success', __('Due Paid successfully'));
        logActivity('Due Payment', "Due paid for invoice #{$id}", $invoice, $request->all());
        return redirect()->route('due.invoice.print', $id);
    }
    public function printInvoice($id)
    {
        $invoice = Invoice::with(['customer', 'user', 'invoiceItems.product'])
            ->where('id', $id)
            ->orWhere('unique_id', $id)
            ->firstOrFail();

        if (env('APP_IMEI') == 'yes') {
            if (get_setting('inv_design') == 'pos') {
                return view('backend.pages.invoice.p-print-imei', compact('invoice'));
            } else {
                return view('backend.pages.invoice.print-imei', compact('invoice'));
            }
        } else {
            if (get_setting('inv_design') == 'pos') {
                return view('backend.pages.invoice.p-print', compact('invoice'));
            } elseif (get_setting('inv_design') == 'a5') {
                return view('backend.pages.invoice.afive-print', compact('invoice'));
            } else {
                return view('backend.pages.invoice.print', compact('invoice'));
            }
        }
    }


    public function dueInvoicePrint($id)
    {
        //get invoice with supplier and user by id
        $invoice = Invoice::with('customer', 'user', 'invoiceItems')
            ->where('id', $id)->first();

        return view('backend.pages.invoice.due-print', compact('invoice'));
    }

    function createInvoiceLog($request, $id)
    {
        //added total_paid and total_due in invoice table
        $invoice = Invoice::find($id);
        if ($request->type == 'Due Paid') {
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = $invoice->total_due - $request->paid_amount;
            $invoice->due_pay = $invoice->due_pay + $request->paid_amount;
        } else {
            $invoice->total_paid = $request->paid_amount;
            $invoice->total_due = $request->due_amount;
            $invoice->due_pay = $request->paid_amount;
        }
        //change invoice status to 1 if paid amount is equal to total amount
        if ($request->due_amount == 0) {
            $invoice->status = 1;
        }

        DB::transaction(function () use ($request, $invoice, $id) {
            if ($invoice->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'deposit';
                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->invoice_id = $id;
                    $bank_transaction->amount = $request->paid_amount - $request->balance;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Received from Customer';
                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->invoice_id = $id;
                    $transaction->customer_id = $request->customer_id;
                    $transaction->debit = NULL;
                    $transaction->credit = $request->paid_amount  - $request->balance;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }

    function createInvoiceInv($request, $id)
    {
        //added total_paid and total_due in invoice table
        $invoice = Invoice::find($id);
        if ($request->type == 'Due Paid') {
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = $invoice->total_due - $request->paid_amount;
            $invoice->due_pay = $invoice->due_pay + $request->due_amount;
        } else {
            $invoice->total_paid = $request->paid_amount;
            $invoice->total_due = $request->due_amount;
        }
        //change invoice status to 1 if paid amount is equal to total amount
        if ($request->due_amount == 0) {
            $invoice->status = 1;
        }

        DB::transaction(function () use ($request, $invoice, $id) {
            if ($invoice->save()) {
                $paymentType = $request->input('payment_type');
                if ($paymentType === 'pos') {
                    if ($request->paid_amount > 0) {
                        //create bank transaction
                        $bank_transaction = new BankTransaction();
                        $bank_transaction->trans_type = 'deposit';
                        $bank_transaction->pay_type = 'invpay';
                        if (auth()->user()->branch_id == 1) {
                            $bank_transaction->branch_id = $request->branch_id;
                        } else {
                            $bank_transaction->branch_id = auth()->user()->branch_id;
                        }
                        if ($request->type == 'Due Paid') {
                            $bank_transaction->date = Carbon::now()->format('Y-m-d');
                        } else {
                            $bank_transaction->date = $request->date;
                        }
                        $bank_transaction->bank_id = $request->bank_id;
                        $bank_transaction->invoice_id = $id;
                        $bank_transaction->amount = $request->paid_amount - $request->balance - $request->pay_point;
                        $bank_transaction->created_by = auth()->user()->id;
                        $bank_transaction->save();

                        // Transaction
                        $transaction = new Transaction();
                        $transaction->transaction_type = 'Received from Customer';
                        if (auth()->user()->branch_id == 1) {
                            $transaction->branch_id = $request->branch_id;
                        } else {
                            $transaction->branch_id = auth()->user()->branch_id;
                        }
                        if ($request->type == 'Due Paid') {
                            $transaction->date = Carbon::now()->format('Y-m-d');
                        } else {
                            $transaction->date = $request->date;
                        }
                        $transaction->bank_id = $request->bank_id;
                        $transaction->invoice_id = $id;
                        $transaction->customer_id = $request->customer_id;
                        $transaction->debit = NULL;
                        $transaction->credit = $request->paid_amount  - $request->balance;
                        $transaction->created_by = auth()->user()->id;
                        $transaction->save();
                    }
                } elseif ($paymentType === 'checking') {
                    $amounts = $request->input('amounts', []);
                    if (is_array($amounts)) {
                        $totalAllocated = array_sum(array_map('floatval', $amounts));
                        $actualPayable = (float)$request->paid_amount - (float)($request->balance ?? 0) - (float)($request->pay_point ?? 0);
                        $ratio = ($totalAllocated > 0 && abs($totalAllocated - $actualPayable) > 0.01) ? ($actualPayable / $totalAllocated) : 1;

                        foreach ($amounts as $bankId => $rawAmount) {
                            $amount = round((float)$rawAmount * $ratio, 2);
                            if ($amount > 0) {
                                // Create bank transaction
                                $bank_transaction = new BankTransaction();
                                $bank_transaction->trans_type = 'deposit';
                                $bank_transaction->pay_type = 'invpay';
                                if (auth()->user()->branch_id == 1) {
                                    $bank_transaction->branch_id = $request->branch_id;
                                } else {
                                    $bank_transaction->branch_id = auth()->user()->branch_id;
                                }
                                if ($request->type == 'Due Paid') {
                                    $bank_transaction->date = Carbon::now()->format('Y-m-d');
                                } else {
                                    $bank_transaction->date = $request->date;
                                }
                                $bank_transaction->bank_id = $bankId;
                                $bank_transaction->invoice_id = $id;
                                $bank_transaction->amount = $amount;
                                $bank_transaction->created_by = auth()->user()->id;
                                $bank_transaction->save();
                            }
                        }

                        foreach ($amounts as $bankId => $rawAmount) {
                            $amount = round((float)$rawAmount * $ratio, 2);
                            if ($amount > 0) {
                                $transaction = new Transaction();
                                $transaction->transaction_type = 'Received from Customer';
                                if (auth()->user()->branch_id == 1) {
                                    $transaction->branch_id = $request->branch_id;
                                } else {
                                    $transaction->branch_id = auth()->user()->branch_id;
                                }
                                if ($request->type == 'Due Paid') {
                                    $transaction->date = Carbon::now()->format('Y-m-d');
                                } else {
                                    $transaction->date = $request->date;
                                }
                                $transaction->bank_id = $bankId;
                                $transaction->invoice_id = $id;
                                $transaction->customer_id = $request->customer_id;
                                $transaction->debit = NULL;
                                $transaction->credit = $amount;
                                $transaction->created_by = auth()->user()->id;
                                $transaction->save();
                            }
                        }
                    }
                }
            }
        });
    }

    function createInvoiceDue($request, $id)
    {
        //added total_paid and total_due in invoice table
        $invoice = Invoice::find($id);
        if ($request->type == 'Due Paid') {
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = $invoice->total_due - $request->paid_amount;
            $invoice->due_pay = $invoice->due_pay + $request->paid_amount;
        } else {
            $invoice->total_paid = $request->paid_amount;
            $invoice->total_due = $request->due_amount;
            $invoice->due_pay = $request->paid_amount;
        }
        //change invoice status to 1 if paid amount is equal to total amount
        if ($invoice->total_due == 0) {
            $invoice->status = 1;
        }

        DB::transaction(function () use ($request, $invoice, $id) {
            if ($invoice->save()) {
                //create bank transaction
                if ($request->paid_amount > 0) {
                    //create bank transaction
                    $bank_transaction = new BankTransaction();
                    $bank_transaction->trans_type = 'deposit';
                    $bank_transaction->pay_type = 'duepay';
                    $bank_transaction->branch_id = $invoice->branch_id;

                    if ($request->type == 'Due Paid') {
                        $bank_transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $bank_transaction->date = $request->date;
                    }
                    $bank_transaction->bank_id = $request->bank_id;
                    $bank_transaction->invoice_id = $id;
                    $bank_transaction->amount = $request->paid_amount - $request->balance;
                    $bank_transaction->created_by = auth()->user()->id;
                    $bank_transaction->save();

                    // Transaction
                    $transaction = new Transaction();
                    $transaction->transaction_type = 'Received from Customer';
                    $transaction->branch_id = $invoice->branch_id;

                    if ($request->type == 'Due Paid') {
                        $transaction->date = Carbon::now()->format('Y-m-d');
                    } else {
                        $transaction->date = $request->date;
                    }
                    $transaction->bank_id = $request->bank_id;
                    $transaction->invoice_id = $id;
                    $transaction->customer_id = $request->customer_id;
                    $transaction->debit = NULL;
                    $transaction->credit = $request->paid_amount  - $request->balance;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });
    }
    public function customerPrintInvoice($id)
    {
        return $this->printInvoice($id);
    }


    public function getOfflineData()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        // Fetch Products for current active branch with stock and units
        $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
        $productsQuery = Product::where('status', 1)->where('is_service', 0)->whereIn('id', $productIds)->with(['unit', 'variations', 'warranty']);
        $products = $productsQuery->get()->map(function($product) {
            $wVal = $product->warranty ? $product->warranty->duration : ($product->warranty_value ?: '');
            $wUnit = $product->warranty ? $product->warranty->period : ($product->warranty_unit ?: 'Month');
            return [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'selling_price' => $product->selling_price,
                'stock_qty' => product_stock($product),
                'unit' => $product->unit,
                'variations' => $product->variations,
                'imei' => $product->imei,
                'is_service' => $product->is_service,
                'warranty_id' => $product->warranty_id,
                'warranty_value' => $wVal,
                'warranty_unit' => $wUnit,
            ];
        });

        // Fetch Customers
        $customers = Customer::select('id', 'name', 'phone')->get();

        return response()->json([
            'products' => $products,
            'customers' => $customers
        ]);
    }

    public function dispatchInvoiceToCourier(Invoice $inv)
    {
        if (!empty($inv->consignment_id)) {
            return [
                'success' => true,
                'already_sent' => true,
                'consignment_id' => $inv->consignment_id,
                'message' => "Invoice #{$inv->invoice_no}: Already sent to courier (Consignment: {$inv->consignment_id})."
            ];
        }

        $customer_info = $inv->customer;
        $custName = trim($customer_info->name ?? '');
        $custPhone = trim($customer_info->phone ?? '');
        $custAddress = trim($customer_info->address ?? '');

        // Fallback to linked WebsiteOrder if customer info is missing or walk-in customer
        if (empty($custAddress) || empty($custPhone) || $inv->customer_id == 1 || strtolower($custName) == 'walk-in customer') {
            $webOrder = null;
            if (!empty($inv->source_link)) {
                $webOrder = \App\Models\WebsiteOrder::where('order_no', $inv->source_link)->first();
            }
            if (!$webOrder && !empty($inv->note)) {
                if (preg_match('/Website Order #([A-Za-z0-9_-]+)/i', $inv->note, $m)) {
                    $webOrder = \App\Models\WebsiteOrder::where('order_no', $m[1])->first();
                }
            }
            if ($webOrder) {
                if (empty($custName) || strtolower($custName) == 'walk-in customer') {
                    $custName = $webOrder->customer_name ?: 'Customer';
                }
                if (empty($custPhone)) {
                    $custPhone = $webOrder->customer_phone ?: $webOrder->phone ?: '';
                }
                if (empty($custAddress)) {
                    $custAddress = $webOrder->customer_address ?: $webOrder->delivery_address ?: '';
                }
            }
        }

        if (empty($custName) || strtolower($custName) == 'walk-in customer') {
            return [
                'success' => false,
                'message' => "Invoice #{$inv->invoice_no}: Cannot send courier order for Walk-in Customer without customer name."
            ];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
        if (strlen($cleanPhone) === 13 && str_starts_with($cleanPhone, '8801')) {
            $cleanPhone = substr($cleanPhone, 2);
        }
        if (empty($cleanPhone) || strlen($cleanPhone) < 11) {
            return [
                'success' => false,
                'message' => "Invoice #{$inv->invoice_no}: Customer phone number must be at least 11 digits (Found: '{$custPhone}')."
            ];
        }

        if (empty($custAddress)) {
            return [
                'success' => false,
                'message' => "Invoice #{$inv->invoice_no}: Missing delivery address."
            ];
        }
        if (strlen($custAddress) < 10) {
            $custAddress = $custAddress . ', Bangladesh';
        }

        $rawCourier = strtolower(trim($inv->courier_type ?? ''));
        if (str_contains($rawCourier, 'pathao')) {
            $courierType = 'pathao';
            $inv->courier_type = 'Pathao';
        } else {
            // Default any other courier_type (Steadfast, empty, 'Courier Delivery (Outside Dhaka)', etc.) to Steadfast
            $courierType = 'steadfast';
            $inv->courier_type = 'Steadfast';
        }

        $codAmount = (int)($inv->total_due > 0 ? $inv->total_due : $inv->total_amount);

        if ($courierType === 'steadfast') {
            $res = placeOrder($inv->invoice_no, $custName, $cleanPhone, $custAddress, $codAmount, $inv->note);
            if (isset($res['status']) && $res['status'] == 200 && !empty($res['consignment'])) {
                $inv->consignment_id = $res['consignment']['consignment_id'] ?? '';
                // Keep the existing order_status (e.g. RTS) unchanged until user manually changes it
                if (empty($inv->order_status)) {
                    $inv->order_status = 'RTS';
                }
                $inv->save();
                logActivity('Send to Courier', "Invoice #{$inv->invoice_no} sent to Steadfast Courier (Consignment: {$inv->consignment_id}, Status: {$inv->order_status})", $inv);
                return [
                    'success' => true,
                    'consignment_id' => $inv->consignment_id,
                    'courier' => 'Steadfast',
                    'status' => $inv->order_status,
                    'message' => "Invoice #{$inv->invoice_no} sent to Steadfast (Consignment: {$inv->consignment_id})"
                ];
            } else {
                $errMsg = '';
                if (!empty($res['errors']) && is_array($res['errors'])) {
                    $flatErrors = [];
                    foreach ($res['errors'] as $field => $errList) {
                        $flatErrors[] = is_array($errList) ? implode(', ', $errList) : $errList;
                    }
                    $errMsg = implode('; ', $flatErrors);
                }
                if (empty($errMsg)) {
                    $errMsg = $res['message'] ?? 'Steadfast API placement failed';
                }
                return [
                    'success' => false,
                    'message' => "Invoice #{$inv->invoice_no} (Steadfast): {$errMsg}"
                ];
            }
        } elseif ($courierType == 'pathao') {
            $token = getPathaoAccessToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => "Invoice #{$inv->invoice_no} (Pathao): Pathao credentials missing or invalid."
                ];
            }

            $pathaoRes = placePathaoOrder(
                1,
                $inv->invoice_no,
                $customer_info->name,
                $customer_info->phone,
                $customer_info->address,
                1, 1, 1, 48, 2,
                $inv->note ?? '',
                1, 0.5,
                $codAmount
            );

            if (isset($pathaoRes['data']['consignment_id'])) {
                $inv->consignment_id = $pathaoRes['data']['consignment_id'];
                // Keep the existing order_status (e.g. RTS) unchanged until user manually changes it
                if (empty($inv->order_status)) {
                    $inv->order_status = 'RTS';
                }
                $inv->save();
                logActivity('Send to Courier', "Invoice #{$inv->invoice_no} sent to Pathao Courier (Consignment: {$inv->consignment_id}, Status: {$inv->order_status})", $inv);
                return [
                    'success' => true,
                    'consignment_id' => $inv->consignment_id,
                    'courier' => 'Pathao',
                    'status' => $inv->order_status,
                    'message' => "Invoice #{$inv->invoice_no} sent to Pathao (Consignment: {$inv->consignment_id})"
                ];
            } elseif (isset($pathaoRes['consignment_id'])) {
                $inv->consignment_id = $pathaoRes['consignment_id'];
                if (empty($inv->order_status)) {
                    $inv->order_status = 'RTS';
                }
                $inv->save();
                logActivity('Send to Courier', "Invoice #{$inv->invoice_no} sent to Pathao Courier (Consignment: {$inv->consignment_id}, Status: {$inv->order_status})", $inv);
                return [
                    'success' => true,
                    'consignment_id' => $inv->consignment_id,
                    'courier' => 'Pathao',
                    'status' => $inv->order_status,
                    'message' => "Invoice #{$inv->invoice_no} sent to Pathao (Consignment: {$inv->consignment_id})"
                ];
            } else {
                $errMsg = $pathaoRes['message'] ?? 'Pathao API Error';
                return [
                    'success' => false,
                    'message' => "Invoice #{$inv->invoice_no} (Pathao): {$errMsg}"
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => "Invoice #{$inv->invoice_no}: Unsupported courier type '{$inv->courier_type}'."
            ];
        }
    }

    public function bulkPrintInvoice(Request $request)
    {
        $rawIds = $request->input('invoice_ids');
        $ids = [];
        if ($request->input('select_all_pages') == 1 || $request->input('select_all_pages') === 'true' || $request->input('select_all_pages') === '1') {
            $ids = $this->buildOnlineSaleFilteredQuery($request)->pluck('id')->toArray();
        } elseif (is_array($rawIds)) {
            $ids = $rawIds;
        } elseif (is_string($rawIds)) {
            $ids = json_decode($rawIds, true) ?? array_filter(explode(',', $rawIds));
        } elseif ($request->filled('invoice_id')) {
            $ids = [$request->input('invoice_id')];
        }

        if (empty($ids)) {
            session()->flash('error', __('Please select at least one invoice to print.'));
            return redirect()->back();
        }

        $invoices = Invoice::with(['customer', 'user', 'invoiceItems.product', 'platform'])
            ->whereIn('id', $ids)
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            $html = '';
            foreach ($invoices as $inv) {
                $html .= view('backend.pages.invoice.shipping-label', ['data' => $inv, 'prefix' => 'printableArea2'])->render();
                $html .= '<div style="page-break-after: always;"></div>';
            }
            return response()->json([
                'success' => true,
                'count' => $invoices->count(),
                'html' => $html
            ]);
        }

        return view('backend.pages.invoice.bulk-print', compact('invoices'));
    }

    public function sendToCourier(Request $request)
    {
        $rawIds = $request->input('invoice_ids');
        $ids = [];
        if ($request->input('select_all_pages') == 1 || $request->input('select_all_pages') === 'true' || $request->input('select_all_pages') === '1') {
            $ids = $this->buildOnlineSaleFilteredQuery($request)->pluck('id')->toArray();
        } elseif (is_array($rawIds)) {
            $ids = $rawIds;
        } elseif (is_string($rawIds)) {
            $ids = json_decode($rawIds, true) ?? array_filter(explode(',', $rawIds));
        } elseif ($request->filled('invoice_id')) {
            $ids = [$request->input('invoice_id')];
        }

        if (empty($ids)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Please select at least one invoice to send to courier.')
                ], 422);
            }
            session()->flash('error', __('Please select at least one invoice to send to courier.'));
            return redirect()->back();
        }

        $invoices = Invoice::with('customer')->whereIn('id', $ids)->get();
        $successCount = 0;
        $errors = [];

        foreach ($invoices as $inv) {
            $dispatchRes = $this->dispatchInvoiceToCourier($inv);
            if ($dispatchRes['success']) {
                $successCount++;
            } else {
                $errors[] = $dispatchRes['message'];
            }
        }

        if ($successCount > 0) {
            $msg = __(":count invoice(s) successfully sent to courier!", ['count' => $successCount]);
            if (!empty($errors)) {
                $msg .= ' ' . implode(' | ', $errors);
                session()->flash('info', $msg);
            } else {
                session()->flash('success', $msg);
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'count' => $successCount,
                    'errors' => $errors
                ]);
            }
        } else {
            $errMsg = __('Courier placement failed: ') . implode(' | ', $errors);
            session()->flash('error', $errMsg);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                    'errors' => $errors
                ], 422);
            }
        }

        return redirect()->back();
    }

    public function syncCourierStatus(Request $request)
    {
        $query = Invoice::where('sale_type', 'Online')
            ->whereNotNull('consignment_id')
            ->where('consignment_id', '!=', '');

        $singleSync = false;
        $rawIds = $request->input('invoice_ids');

        if ($request->filled('invoice_id')) {
            $query->where('id', $request->invoice_id);
            $singleSync = true;
        } elseif (!empty($rawIds)) {
            $ids = is_array($rawIds) ? $rawIds : (json_decode($rawIds, true) ?? array_filter(explode(',', (string)$rawIds)));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        } else {
            // High-speed optimization: Sync all active in-courier shipments (exclude final delivered/returned/cancelled, and exclude RTS since RTS stays in RTS until moved to In Courier)
            $query->whereIn('order_status', [
                'In Courier', 'in_courier',
                'Pending', 'pending',
                'In Review', 'in_review',
                'In Transit', 'in_transit',
                'Shipping', 'shipping',
                'Picked Up', 'picked_up',
                'On Hold', 'on_hold'
            ])->orderBy('id', 'desc');
        }

        $invoices = $query->get();
        $updatedCount = 0;
        $pathaoToken = getPathaoAccessToken();
        $lastSyncedStatus = null;

        foreach ($invoices as $inv) {
            if (in_array(strtolower(trim($inv->order_status ?? '')), ['delivered'])) {
                $lastSyncedStatus = $inv->order_status;
                continue;
            }

            $cType = strtolower(trim($inv->courier_type ?? ''));
            $newStatus = null;

            if (in_array($cType, ['stead fast', 'steadfast', 'stead_fast', 'stead-fast']) || empty($cType)) {
                $stRes = status($inv->consignment_id);
                $stDeliveryStatus = strtolower(trim($stRes['delivery_status'] ?? ''));

                if (!empty($stDeliveryStatus)) {
                    $newStatus = mapCourierStatus($stDeliveryStatus);
                }
            } elseif ($cType == 'pathao') {
                if ($pathaoToken) {
                    $pathaoRes = getOrderSummary($inv->consignment_id, $pathaoToken);
                    $pathaoStatus = strtolower(trim($pathaoRes['data']['order_status'] ?? ''));

                    if (!empty($pathaoStatus)) {
                        $newStatus = mapCourierStatus($pathaoStatus);
                    }
                }
            }

            $curStatusNorm = strtolower(trim(str_replace(['_', '-'], ' ', $inv->order_status ?? '')));
            $isPreDispatch = in_array($curStatusNorm, ['rts', 'approved', 'processing']);

            if (!empty($newStatus)) {
                if ($isPreDispatch && !$singleSync && in_array($newStatus, ['Pending', 'In Review'])) {
                    $newStatus = null;
                } else {
                    $lastSyncedStatus = $newStatus;
                }
            }

            if (!empty($newStatus) && $inv->order_status !== $newStatus) {
                $oldStatus = $inv->order_status;

                // Synchronize inventory stock and IMEI serials on status change
                syncInvoiceStockForStatusChange($inv, $newStatus, $oldStatus);

                $inv->order_status = $newStatus;
                $inv->save();

                if (in_array(strtolower(trim($newStatus)), ['delivered'])) {
                    if ($inv->total_due > 0 && $inv->status != 2) {
                        autoPayInvoiceDue($inv);
                    }
                }

                $updatedCount++;
                logActivity('Sync Courier Status', "Invoice #{$inv->invoice_no} status synced from courier to {$newStatus}", $inv);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $updatedCount,
                'new_status' => $lastSyncedStatus,
                'message' => $singleSync 
                    ? ($lastSyncedStatus ? __("Courier status synced: :status", ['status' => $lastSyncedStatus]) : __("Invoice status verified."))
                    : __(":count invoice courier status(es) successfully synced!", ['count' => $updatedCount])
            ]);
        }

        session()->flash('success', __(":count invoice courier status(es) successfully synced!", ['count' => $updatedCount]));
        return redirect()->back();
    }


    // =====================================================================
    // Scan To Update — barcode scan & update order status
    // =====================================================================

    public function scanToUpdate(Request $request)
    {
        return app(WooCommerceController::class)->scannerView($request);
    }

    public function searchInvoiceForScan(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (empty($q)) {
            return response()->json(['found' => false, 'message' => 'Enter invoice number or barcode']);
        }

        $invoice = Invoice::where('invoice_no', $q)
            ->orWhere('barcode', $q)
            ->orWhere('consignment_id', $q)
            ->with(['customer:id,name,phone', 'invoiceItems.product:id,name'])
            ->first();

        if (!$invoice) {
            return response()->json(['found' => false, 'message' => 'Invoice not found for: ' . $q]);
        }

        return response()->json([
            'found'           => true,
            'id'              => $invoice->id,
            'invoice_no'      => $invoice->invoice_no,
            'order_status'    => $invoice->order_status,
            'consignment_id'  => $invoice->consignment_id,
            'customer_name'   => optional($invoice->customer)->name ?? 'N/A',
            'customer_phone'  => optional($invoice->customer)->phone ?? '',
            'total_amount'    => $invoice->total_amount,
            'total_due'       => $invoice->total_due,
            'items_count'     => $invoice->invoiceItems->count(),
        ]);
    }

    public function processScanToUpdate(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'new_status' => 'required|string|max:100',
        ]);

        $invoice = Invoice::with('customer')->findOrFail($request->invoice_id);
        $oldStatus = $invoice->order_status;
        $newStatus = $request->new_status;

        // Synchronize inventory stock and IMEI serials on status change
        syncInvoiceStockForStatusChange($invoice, $newStatus, $oldStatus);

        if (in_array(strtolower(trim($newStatus)), ['in courier', 'in_courier']) && empty($invoice->consignment_id)) {
            $dispatchRes = $this->dispatchInvoiceToCourier($invoice);
            if (!$dispatchRes['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $dispatchRes['message'],
                ], 422);
            }
        } else {
            $invoice->order_status = $newStatus;
            $invoice->save();
        }

        if (strtolower($newStatus) === 'delivered' && $invoice->total_due > 0 && $invoice->status != 2) {
            autoPayInvoiceDue($invoice);
        }

        logActivity('Scan To Update', "Invoice #{$invoice->invoice_no} status changed from {$oldStatus} to {$invoice->order_status}", $invoice);

        return response()->json([
            'success'      => true,
            'message'      => "Invoice #{$invoice->invoice_no} updated to '{$invoice->order_status}' successfully.",
            'new_status'   => $invoice->order_status,
            'invoice_no'   => $invoice->invoice_no,
        ]);
    }
}

