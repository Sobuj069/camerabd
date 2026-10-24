<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BranchProduct;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReturnItem;
use App\Models\ReturnPurchase;
use App\Models\Customer;
use App\Models\ReturnTbl;
use App\Models\Platform;
use App\Models\WebsiteOrder;
use App\Models\WebsiteOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['total_pur_due'] = Purchase::where('branch_id', $filterBranchId)->sum('total_due');
                $total_pur = Purchase::where('branch_id', $filterBranchId)->sum('total_amount');
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $data['total_product'] = Product::whereIn('id', $productIds)->count();
                $data['total_sale_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId), 'total_due');
                $total_inv = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId), 'total_amount');
                $data['total_customer'] = Customer::where('branch_id', $filterBranchId)->count();
                $data['total_return_amount'] = ReturnTbl::where('branch_id', $filterBranchId)->sum('total_return');
                $data['inv_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId)->where('date', date('Y-m-d')), 'total_due');
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId), 'pur_subtotal');
                $returns = ReturnItem::where('branch_id', $filterBranchId)->get();
                $discount = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId), 'discount_amount');
                $returnDis = ReturnTbl::where('branch_id', $filterBranchId)->sum('discount_amount');
                $returnAmu = ReturnTbl::where('branch_id', $filterBranchId)->sum('total_return');
                $data['total_invoice_count'] = Invoice::where('branch_id', $filterBranchId)->count();
            } else {
                $data['total_pur_due'] = Purchase::sum('total_due');
                $total_pur = Purchase::sum('total_amount');
                $data['total_pur_amount'] = $total_pur;
                $data['total_product'] = Product::count();
                $data['total_sale_due'] = Invoice::getFakeSum(Invoice::query(), 'total_due');
                $total_inv = Invoice::getFakeSum(Invoice::query(), 'total_amount');
                $data['total_customer'] = Customer::count();
                $data['total_return_amount'] = ReturnTbl::sum('total_return');
                $data['inv_due'] = Invoice::getFakeSum(Invoice::where('date', date('Y-m-d')), 'total_due');
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::query(), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::query(), 'pur_subtotal');
                $returns = ReturnItem::get();
                $returnAmu = ReturnTbl::sum('total_return');
                $discount = Invoice::getFakeSum(Invoice::query(), 'discount_amount');
                $returnDis = ReturnTbl::sum('discount_amount');
                $data['total_invoice_count'] = Invoice::count();
            }
        } else {
            $data['total_pur_due'] = Purchase::where('branch_id', $userBranchId)->sum('total_due');
            $total_pur = Purchase::where('branch_id', $userBranchId)->sum('total_amount');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['total_product'] = Product::whereIn('id', $productIds)->count();
            $data['total_sale_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId), 'total_due');
            $total_inv = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId), 'total_amount');
            $data['total_customer'] = Customer::where('branch_id', $userBranchId)->count();
            $data['total_return_amount'] = ReturnTbl::where('branch_id', $userBranchId)->sum('total_return');
            $data['inv_due'] = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId)->where('date', date('Y-m-d')), 'total_due');
            $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId), 'inv_subtotal');
            $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId), 'pur_subtotal');
            $returns = ReturnItem::where('branch_id', $userBranchId)->get();
            $discount = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId), 'discount_amount');
            $returnDis = ReturnTbl::where('branch_id', $userBranchId)->sum('discount_amount');
            $returnAmu = ReturnTbl::where('branch_id', $userBranchId)->sum('total_return');
            $data['total_invoice_count'] = Invoice::where('branch_id', $userBranchId)->count();
        }

        $data['total_pur_amount'] = $total_pur;
        $data['total_sale'] = $total_inv;

        // Manual vs Online Lifetime Queries
        $manualLifetimeQuery = Invoice::query()->where(function($q) {
            $q->whereNull('sale_type')->orWhereNotIn('sale_type', ['Online', 'WooCommerce']);
        })->whereNull('woocommerce_order_id')->where('unique_id', 'not like', 'WC-%');

        $onlineLifetimeQuery = Invoice::query()->where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($userBranchId == 1 && $filterBranchId) {
            $manualLifetimeQuery->where('branch_id', $filterBranchId);
            $onlineLifetimeQuery->where(function($q) use ($filterBranchId) {
                $q->where('branch_id', $filterBranchId)->orWhere('sale_type', 'WooCommerce')->orWhereNotNull('woocommerce_order_id');
            });
        } elseif ($userBranchId != 1) {
            $manualLifetimeQuery->where('branch_id', $userBranchId);
            $onlineLifetimeQuery->where(function($q) use ($userBranchId) {
                $q->where('branch_id', $userBranchId)->orWhere('sale_type', 'WooCommerce')->orWhereNotNull('woocommerce_order_id');
            });
        }

        $data['total_manual_sale'] = Invoice::getFakeSum(clone $manualLifetimeQuery, 'total_amount');
        $data['total_manual_due'] = Invoice::getFakeSum(clone $manualLifetimeQuery, 'total_due');
        $data['total_manual_count'] = (clone $manualLifetimeQuery)->count();

        $data['total_online_sale'] = Invoice::getFakeSum(clone $onlineLifetimeQuery, 'total_amount');
        $data['total_online_due'] = Invoice::getFakeSum(clone $onlineLifetimeQuery, 'total_due');
        $data['total_online_count'] = (clone $onlineLifetimeQuery)->count();

        $dis = $discount - $returnDis;
        $totalProfit = $saleCost - $purchaseCost;
        $profit = $totalProfit - $dis;
        $sale_amu = $total_inv - $profit;
        $branchIdForStock = ($userBranchId == 1) ? $filterBranchId : $userBranchId;
        if ($branchIdForStock) {
            $productIds = BranchProduct::where('branch_id', $branchIdForStock)->pluck('product_id');
            $stockProducts = Product::whereIn('id', $productIds)->where('is_service', 0)->with('unit')->get();
        } else {
            $stockProducts = Product::where('is_service', 0)->with('unit')->get();
        }

        $avail_stock = 0;
        $avail_stock_sale = 0;
        foreach ($stockProducts as $product) {
            $fakeStockQty = product_fake_stock_val($product, $branchIdForStock);
            $factor = ($product->unit && $product->unit->related_value) ? $product->unit->related_value : 1;

            $avgPurchasePrice = \App\Models\PurchaseItem::where('product_id', $product->id)
                ->when($branchIdForStock, function($q) use ($branchIdForStock) {
                    return $q->where('branch_id', $branchIdForStock);
                })
                ->avg('rate');

            $purchasePrice = ($avgPurchasePrice !== null && $avgPurchasePrice > 0) ? (float)$avgPurchasePrice : (float)$product->purchase_price;
            $sellingPrice = (float) $product->selling_price;

            $avail_stock += $fakeStockQty * ($purchasePrice / $factor);
            $avail_stock_sale += $fakeStockQty * ($sellingPrice / $factor);
        }

        // When no sales have occurred yet, sync stock purchase valuation to total purchase amount
        if (($total_inv ?? 0) <= 0 && ($total_pur ?? 0) > 0) {
            $avail_stock = $total_pur;
        }

        $data['totalPrice'] = $avail_stock;
        $data['available_stock_sale_val'] = $avail_stock_sale;
        $data['total_manual_profit'] = $this->calculateManualProfit(null, $branchIdForStock);
        $data['total_online_profit'] = $this->calculateOnlineProfit(null, $branchIdForStock);
        $data['lifetime_bank_sales'] = $this->getBankSalesBreakdown(null, $branchIdForStock);
        $data['lifetime_online_profit_overview'] = $this->getOnlineProfitOverview(null, $branchIdForStock);
        $data['order_counts_chart'] = $this->getOrderCountsData('Last-30-days', null, null, $branchIdForStock);
        $data['earning_reports_chart'] = $this->getEarningReportsData('year', 'all', $branchIdForStock);
        $data['hourly_orders_chart'] = $this->getHourlyOrdersData('web', $branchIdForStock);
        $data['manual_sales_chart'] = $this->getManualSalesData('Last-30-days', null, null, $branchIdForStock);
        $data['online_platforms_chart'] = $this->getOnlinePlatformsData('Last-30-days', null, null, $branchIdForStock);
        $data['website_orders_chart'] = $this->getWebOrderReportData('web_orders', 'Last-30-days', null, null, $branchIdForStock, 'website');
        $data['woocommerce_orders_chart'] = $this->getWebOrderReportData('web_orders', 'Last-30-days', null, null, $branchIdForStock, 'woocommerce');
        $data['web_order_report_chart'] = $data['website_orders_chart'];
        $data['orders_by_source_chart'] = $this->getOrdersBySourceData('all', 'Today', null, null, $branchIdForStock);
        $data['web_orders_count'] = WebsiteOrder::count();
        $items = $this->getDashboardData('Today');
        $branch_id = session('branch_id');

        return view('backend.pages.dashboard.index', $data, ['data' => $items, 'filter' => 'Today']);
    }

    public function filterData(Request $request)
    {
        $filter = $request->input('filter', 'Today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $channel = $request->input('channel'); // 'manual', 'online', or null

        $data = $this->getDashboardData($filter, $startDate, $endDate, $channel);

        return response()->json($data);
    }

    public function getOrderCountsChart(Request $request)
    {
        $filter = $request->input('filter', 'Last-30-days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $data = $this->getOrderCountsData($filter, $startDate, $endDate);
        return response()->json($data);
    }

    public function getEarningReportsChart(Request $request)
    {
        $period = $request->input('period', 'year');
        $status = $request->input('status', 'all');
        $data = $this->getEarningReportsData($period, $status);
        return response()->json($data);
    }

    public function getHourlyOrdersChart(Request $request)
    {
        $channel = $request->input('channel', 'web');
        $data = $this->getHourlyOrdersData($channel);
        return response()->json($data);
    }

    public function getManualSalesChart(Request $request)
    {
        $filter = $request->input('filter', 'Last-30-days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $data = $this->getManualSalesData($filter, $startDate, $endDate);
        return response()->json($data);
    }

    public function getOnlinePlatformsChart(Request $request)
    {
        $filter = $request->input('filter', 'Last-30-days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $data = $this->getOnlinePlatformsData($filter, $startDate, $endDate);
        return response()->json($data);
    }

    public function getWebOrderReportChart(Request $request)
    {
        $type = $request->input('type', 'web_orders');
        $filter = $request->input('filter', 'Last-30-days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $source = $request->input('source', 'website'); // 'website', 'woocommerce', or 'all'
        $data = $this->getWebOrderReportData($type, $filter, $startDate, $endDate, null, $source);
        return response()->json($data);
    }

    public function getOrdersBySourceChart(Request $request)
    {
        $status = $request->input('status', 'all');
        $filter = $request->input('filter', 'Today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $data = $this->getOrdersBySourceData($status, $filter, $startDate, $endDate);
        return response()->json($data);
    }

    public function parseDateRange($filter = 'Last-30-days', $startDate = null, $endDate = null)
    {
        if ($filter === 'custom' && $startDate && $endDate) {
            return ['start' => Carbon::parse($startDate), 'end' => Carbon::parse($endDate)];
        }
        if ($filter === 'single_date' && $startDate) {
            return ['start' => Carbon::parse($startDate), 'end' => Carbon::parse($startDate)];
        }
        switch ($filter) {
            case 'Today': case '1D':
                return ['start' => Carbon::today(), 'end' => Carbon::today()];
            case 'Yesterday':
                return ['start' => Carbon::yesterday(), 'end' => Carbon::yesterday()];
            case 'Last-7-days': case '7days': case '7D':
                return ['start' => Carbon::today()->subDays(6), 'end' => Carbon::today()];
            case 'Last-30-days': case '30days': case '30D':
                return ['start' => Carbon::today()->subDays(29), 'end' => Carbon::today()];
            case 'This-week':
                return ['start' => Carbon::now()->startOfWeek(), 'end' => Carbon::now()->endOfWeek()];
            case 'This-month':
                return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()];
            case 'Last-month':
                return ['start' => Carbon::now()->subMonth()->startOfMonth(), 'end' => Carbon::now()->subMonth()->endOfMonth()];
            case 'This-year':
                return ['start' => Carbon::now()->startOfYear(), 'end' => Carbon::now()->endOfYear()];
            case 'Lifetime': case 'All':
                $firstInv = Invoice::orderBy('date', 'asc')->first();
                return ['start' => $firstInv && $firstInv->date ? Carbon::parse($firstInv->date) : Carbon::today()->subMonths(12), 'end' => Carbon::today()];
            default:
                return ['start' => Carbon::today()->subDays(29), 'end' => Carbon::today()];
        }
    }

    public function getManualSalesData($filter = 'Last-30-days', $startDate = null, $endDate = null, $branchId = null)
    {
        $dates = $this->parseDateRange($filter, $startDate, $endDate);
        $sdate = $dates['start'];
        $edate = $dates['end'];

        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        $query = Invoice::where(function($q) {
            $q->whereNull('sale_type')->orWhereNotIn('sale_type', ['Online', 'WooCommerce']);
        })->whereNull('woocommerce_order_id')
          ->where('unique_id', 'not like', 'WC-%');

        if ($sdate && $edate) {
            $query->whereBetween('date', [$sdate->toDateString(), $edate->toDateString()]);
        }

        if ($activeBranchId) {
            $query->where('branch_id', $activeBranchId);
        }

        $invoices = $query->get(['id', 'total_amount', 'total_paid', 'total_due']);
        $invIds = $invoices->pluck('id');

        $totalOrders = $invoices->count();
        $totalQty = (float) InvoiceItem::whereIn('invoice_id', $invIds)->sum('main_qty');

        $paidCount = $invoices->where('total_due', '<=', 0)->count();
        $partialCount = $invoices->filter(fn($i) => $i->total_paid > 0 && $i->total_due > 0)->count();
        $dueCount = $invoices->filter(fn($i) => $i->total_paid <= 0 && $i->total_due > 0)->count();

        return [
            'total_orders' => $totalOrders,
            'total_qty' => $totalQty,
            'paid' => $paidCount,
            'partial' => $partialCount,
            'due' => $dueCount,
            'labels' => ['Full Paid Invoices', 'Partial Paid Invoices', 'Due Invoices'],
            'data' => [$paidCount, $partialCount, $dueCount],
            'colors' => ['#10b981', '#f59e0b', '#f43f5e'],
            'filter_label' => $filter === 'custom' ? ($sdate->format('M d') . ' - ' . $edate->format('M d, Y')) : str_replace('-', ' ', $filter)
        ];
    }

    public function getOnlinePlatformsData($filter = 'Last-30-days', $startDate = null, $endDate = null, $branchId = null)
    {
        $dates = $this->parseDateRange($filter, $startDate, $endDate);
        $sdate = $dates['start'];
        $edate = $dates['end'];

        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        // Fetch Invoices
        $query = Invoice::where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce', 'Website'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('source_link', 'Website')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($sdate && $edate) {
            $query->whereBetween('date', [$sdate->toDateString(), $edate->toDateString()]);
        }

        if ($activeBranchId) {
            $query->where(function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
        }

        $invoices = $query->get(['id', 'sale_type', 'source_link', 'woocommerce_order_id', 'unique_id', 'platform_id', 'order_status', 'consignment_id', 'total_amount']);
        $allInvIds = $invoices->pluck('id');
        $invTotalQty = (float) InvoiceItem::whereIn('invoice_id', $allInvIds)->sum('main_qty');

        // Fetch WebsiteOrder records
        $webOrdersQuery = WebsiteOrder::query();
        if ($sdate && $edate) {
            $webOrdersQuery->whereBetween('created_at', [$sdate->copy()->startOfDay(), $edate->copy()->endOfDay()]);
        }
        if ($activeBranchId) {
            $webOrdersQuery->where(function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
            });
        }
        $webOrders = $webOrdersQuery->with('items')->get();
        $webOrderLinkedInvIds = $webOrders->pluck('invoice_id')->filter()->toArray();
        $unconvertedWebOrders = $webOrders->filter(fn($w) => empty($w->invoice_id) || !$invoices->contains('id', $w->invoice_id));

        $wooInvs = $invoices->filter(fn($i) => $i->sale_type === 'WooCommerce' || !empty($i->woocommerce_order_id) || str_starts_with($i->unique_id ?? '', 'WC-'));
        $wooCount = $wooInvs->count();
        $wooQty = (float) InvoiceItem::whereIn('invoice_id', $wooInvs->pluck('id'))->sum('main_qty');

        $webInvs = $invoices->filter(fn($i) => !$wooInvs->contains('id', $i->id) && in_array($i->id, $webOrderLinkedInvIds));
        $webInvsQty = (float) InvoiceItem::whereIn('invoice_id', $webInvs->pluck('id'))->sum('main_qty');
        $unconvertedWebQty = (float) $unconvertedWebOrders->sum(fn($w) => $w->items ? $w->items->sum('quantity') : 1);

        $webOrdersCount = $webOrders->count();
        $webOrdersQty = $webInvsQty + $unconvertedWebQty;

        $totalOrders = $invoices->count() + $unconvertedWebOrders->count();
        $totalQty = $invTotalQty + $unconvertedWebQty;

        $labels = [];
        $data = [];
        $qtys = [];
        $palette = ['#8b5cf6', '#3b82f6', '#10b981', '#a855f7', '#f59e0b', '#ec4899', '#06b6d4', '#64748b'];

        if ($wooCount > 0) {
            $labels[] = 'WordPress (WooCommerce)';
            $data[] = $wooCount;
            $qtys[] = $wooQty;
        }

        if ($webOrdersCount > 0) {
            $labels[] = 'Website (E-Commerce)';
            $data[] = $webOrdersCount;
            $qtys[] = $webOrdersQty;
        }

        $platforms = Platform::orderBy('name', 'asc')->get();
        foreach ($platforms as $p) {
            $pInvs = $invoices->filter(fn($i) => $i->platform_id == $p->id && !$wooInvs->contains('id', $i->id) && !$webInvs->contains('id', $i->id));
            if ($pInvs->count() > 0) {
                $labels[] = $p->name;
                $data[] = $pInvs->count();
                $qtys[] = (float) InvoiceItem::whereIn('invoice_id', $pInvs->pluck('id'))->sum('main_qty');
            }
        }

        $noPlatformInvs = $invoices->filter(fn($i) => empty($i->platform_id) && !$wooInvs->contains('id', $i->id) && !$webInvs->contains('id', $i->id) && in_array($i->sale_type, ['Online', null, '']));
        if ($noPlatformInvs->count() > 0 || empty($labels)) {
            $labels[] = 'Direct / General Online';
            $data[] = $noPlatformInvs->count();
            $qtys[] = (float) InvoiceItem::whereIn('invoice_id', $noPlatformInvs->pluck('id'))->sum('main_qty');
        }

        $stProcessing = $invoices->filter(fn($i) => empty($i->order_status) || in_array(strtolower($i->order_status), ['processing', 'pending']))->count()
            + $unconvertedWebOrders->filter(fn($w) => empty($w->order_status) || in_array(strtolower($w->order_status), ['processing', 'pending', 'new', 'unpaid']))->count();

        $stRts = $invoices->filter(fn($i) => in_array(strtolower($i->order_status ?? ''), ['rts', 'approved', 'confirmed']))->count()
            + $unconvertedWebOrders->filter(fn($w) => in_array(strtolower($w->order_status ?? ''), ['rts', 'approved', 'confirmed']))->count();

        $stOnHold = $invoices->filter(fn($i) => in_array(strtolower($i->order_status ?? ''), ['on hold', 'on_hold', 'on-hold', 'hold', 'good but no response', 'no response', 'advance payment']))->count()
            + $unconvertedWebOrders->filter(fn($w) => in_array(strtolower($w->order_status ?? ''), ['on hold', 'on_hold', 'on-hold', 'hold', 'good but no response', 'no response', 'advance payment']))->count();

        $stInCourier = $invoices->filter(fn($i) => !empty($i->consignment_id) || in_array(strtolower($i->order_status ?? ''), ['in courier', 'in_courier', 'shipped']))->count();

        $stDelivered = $invoices->filter(fn($i) => in_array(strtolower($i->order_status ?? ''), ['delivered', 'completed']))->count()
            + $unconvertedWebOrders->filter(fn($w) => in_array(strtolower($w->order_status ?? ''), ['delivered', 'completed']))->count();

        $stCancelled = $invoices->filter(fn($i) => in_array(strtolower($i->order_status ?? ''), ['cancelled', 'failed', 'returned', 'canceled']))->count()
            + $unconvertedWebOrders->filter(fn($w) => in_array(strtolower($w->order_status ?? ''), ['cancelled', 'failed', 'returned', 'canceled']))->count();

        $platformBadges = [];
        foreach ($labels as $idx => $lbl) {
            $platformBadges[] = [
                'label' => $lbl,
                'count' => $data[$idx] ?? 0,
                'qty' => $qtys[$idx] ?? 0,
                'color' => $palette[$idx % count($palette)]
            ];
        }

        return [
            'total_orders' => $totalOrders,
            'total_qty' => $totalQty,
            'woo_orders' => $wooCount,
            'web_orders' => $webOrdersCount,
            'labels' => $labels,
            'data' => $data,
            'qtys' => $qtys,
            'colors' => array_slice($palette, 0, count($labels)),
            'platform_badges' => $platformBadges,
            'status_counts' => [
                'processing' => $stProcessing,
                'rts' => $stRts,
                'on_hold' => $stOnHold,
                'in_courier' => $stInCourier,
                'delivered' => $stDelivered,
                'cancelled' => $stCancelled,
            ],
            'filter_label' => $filter === 'custom' ? ($sdate->format('M d') . ' - ' . $edate->format('M d, Y')) : str_replace('-', ' ', $filter)
        ];
    }

    public function getWebOrderReportData($type = 'web_orders', $filter = 'Last-30-days', $startDate = null, $endDate = null, $branchId = null, $source = 'website')
    {
        $dates = $this->parseDateRange($filter, $startDate, $endDate);
        $sdate = $dates['start'];
        $edate = $dates['end'];

        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        $mergedOrders = collect();

        // 1. Fetch Website Orders (Pure Website Checkout Orders)
        if ($source === 'website') {
            $webOrdersQuery = WebsiteOrder::query();
            if ($sdate && $edate) {
                $webOrdersQuery->whereBetween('created_at', [$sdate->copy()->startOfDay(), $edate->copy()->endOfDay()]);
            }
            if ($activeBranchId) {
                $webOrdersQuery->where(function($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
                });
            }
            $webOrders = $webOrdersQuery->with('invoice')->get();

            foreach ($webOrders as $wo) {
                $status = $wo->order_status;
                if ($wo->invoice && !empty($wo->invoice->order_status)) {
                    $status = $wo->invoice->order_status;
                }
                $mergedOrders->push([
                    'id' => 'wo_' . $wo->id,
                    'order_status' => $status,
                    'total_amount' => $wo->total_amount,
                ]);
            }
        }

        // 2. Fetch WooCommerce Invoices
        if ($source === 'woocommerce') {
            $wooQuery = Invoice::where(function($q) {
                $q->whereNotNull('woocommerce_order_id')
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhere('unique_id', 'like', 'WC-%');
            });

            if ($sdate && $edate) {
                $wooQuery->whereBetween('date', [$sdate->toDateString(), $edate->toDateString()]);
            }

            if ($activeBranchId) {
                $wooQuery->where(function($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)
                      ->orWhere('sale_type', 'WooCommerce')
                      ->orWhereNotNull('woocommerce_order_id');
                });
            }

            $wooInvoices = $wooQuery->get(['id', 'order_status', 'total_amount']);

            foreach ($wooInvoices as $inv) {
                $mergedOrders->push([
                    'id' => 'inv_' . $inv->id,
                    'order_status' => $inv->order_status,
                    'total_amount' => $inv->total_amount,
                ]);
            }
        }

        if ($source === 'all') {
            $webOrdersQuery = WebsiteOrder::query();
            if ($sdate && $edate) {
                $webOrdersQuery->whereBetween('created_at', [$sdate->copy()->startOfDay(), $edate->copy()->endOfDay()]);
            }
            if ($activeBranchId) {
                $webOrdersQuery->where(function($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)->orWhereNull('branch_id');
                });
            }
            $webOrders = $webOrdersQuery->with('invoice')->get();
            $linkedIds = [];
            foreach ($webOrders as $wo) {
                $status = $wo->order_status;
                if ($wo->invoice && !empty($wo->invoice->order_status)) {
                    $status = $wo->invoice->order_status;
                    $linkedIds[] = $wo->invoice->id;
                }
                $mergedOrders->push([
                    'id' => 'wo_' . $wo->id,
                    'order_status' => $status,
                    'total_amount' => $wo->total_amount,
                ]);
            }

            $wooQuery = Invoice::where(function($q) {
                $q->whereNotNull('woocommerce_order_id')
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhere('unique_id', 'like', 'WC-%');
            });
            if ($sdate && $edate) {
                $wooQuery->whereBetween('date', [$sdate->toDateString(), $edate->toDateString()]);
            }
            if ($activeBranchId) {
                $wooQuery->where(function($q) use ($activeBranchId) {
                    $q->where('branch_id', $activeBranchId)
                      ->orWhere('sale_type', 'WooCommerce')
                      ->orWhereNotNull('woocommerce_order_id');
                });
            }
            $wooInvoices = $wooQuery->get(['id', 'order_status', 'total_amount']);
            foreach ($wooInvoices as $inv) {
                if (!in_array($inv->id, $linkedIds)) {
                    $mergedOrders->push([
                        'id' => 'inv_' . $inv->id,
                        'order_status' => $inv->order_status,
                        'total_amount' => $inv->total_amount,
                    ]);
                }
            }
        }

        $totalOrders = $mergedOrders->count();

        if ($type === 'incomplete_orders') {
            $incompleteInvs = $mergedOrders->filter(fn($i) => in_array(strtolower(trim($i['order_status'] ?? '')), ['pending', 'in review', 'incomplete', 'unpaid']));
            $incompleteCount = $incompleteInvs->count();
            return [
                'type' => 'incomplete_orders',
                'total_orders' => $incompleteCount,
                'labels' => ['Incomplete / Pending Checkout', 'Follow Up Required'],
                'data' => [$incompleteCount, 0],
                'colors' => ['#f59e0b', '#ec4899'],
                'breakdown' => [
                    ['label' => 'Incomplete / Pending Checkout', 'count' => $incompleteCount, 'color' => '#f59e0b'],
                    ['label' => 'Follow Up Required', 'count' => 0, 'color' => '#ec4899']
                ],
                'filter_label' => $filter === 'custom' ? ($sdate->format('M d') . ' - ' . $edate->format('M d, Y')) : str_replace('-', ' ', $filter)
            ];
        }

        $categories = [
            'Processing'            => ['color' => '#ec4899', 'keys' => ['processing', 'pending', 'new', 'unpaid']],
            'Good But No Response'  => ['color' => '#3b82f6', 'keys' => ['good but no response', 'good_no_response']],
            'No Response'           => ['color' => '#f97316', 'keys' => ['no response', 'no_response']],
            'Advance Payment'       => ['color' => '#6366f1', 'keys' => ['advance payment', 'advance_payment']],
            'On Hold'               => ['color' => '#84cc16', 'keys' => ['on hold', 'on_hold', 'on-hold', 'hold']],
            'Approved'              => ['color' => '#a855f7', 'keys' => ['approved', 'rts', 'completed', 'confirmed', 'delivered', 'in courier', 'in_courier', 'shipping', 'dispatched']],
            'Cancel'                => ['color' => '#10b981', 'keys' => ['cancelled', 'cancel', 'failed', 'returned', 'canceled', 'return']],
        ];

        $labels = [];
        $data = [];
        $colors = [];
        $breakdown = [];
        $countedIds = [];

        foreach ($categories as $catName => $meta) {
            $matching = $mergedOrders->filter(function($ord) use ($meta, $countedIds) {
                if (in_array($ord['id'], $countedIds)) return false;
                $st = strtolower(trim($ord['order_status'] ?? ''));
                return in_array($st, $meta['keys']);
            });

            $count = $matching->count();
            foreach ($matching as $m) {
                $countedIds[] = $m['id'];
            }

            $labels[] = $catName;
            $data[] = $count;
            $colors[] = $meta['color'];
            $breakdown[] = [
                'label' => $catName,
                'count' => $count,
                'color' => $meta['color']
            ];
        }

        $unclassified = $mergedOrders->filter(fn($i) => !in_array($i['id'], $countedIds) && empty(trim($i['order_status'] ?? '')))->count();
        if ($unclassified > 0) {
            $data[0] += $unclassified;
            $breakdown[0]['count'] += $unclassified;
        }

        return [
            'type' => 'web_orders',
            'total_orders' => $totalOrders,
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
            'breakdown' => $breakdown,
            'filter_label' => $filter === 'custom' ? ($sdate->format('M d') . ' - ' . $edate->format('M d, Y')) : str_replace('-', ' ', $filter)
        ];
    }
    public function getOrdersBySourceData($status = 'all', $filter = 'Today', $startDate = null, $endDate = null, $branchId = null)
    {
        $dates = $this->parseDateRange($filter, $startDate, $endDate);
        $sdate = $dates['start'];
        $edate = $dates['end'];

        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        $query = Invoice::where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce', 'Website'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('source_link', 'Website')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($sdate && $edate) {
            $query->whereBetween('date', [$sdate->toDateString(), $edate->toDateString()]);
        }

        if ($status && $status !== 'all') {
            $stLower = strtolower($status);
            if ($stLower === 'approved') {
                $query->whereIn('order_status', ['Approved', 'approved', 'RTS', 'rts', 'completed']);
            } elseif ($stLower === 'processing') {
                $query->where(function($q) {
                    $q->whereNull('order_status')->orWhereIn('order_status', ['Processing', 'processing', 'Pending', 'pending']);
                });
            } elseif ($stLower === 'in_courier' || $stLower === 'in courier') {
                $query->where(function($q) {
                    $q->whereNotNull('consignment_id')->orWhereIn('order_status', ['In Courier', 'in_courier', 'shipped']);
                });
            } elseif ($stLower === 'delivered') {
                $query->whereIn('order_status', ['Delivered', 'delivered']);
            } elseif ($stLower === 'cancelled') {
                $query->whereIn('order_status', ['Cancelled', 'cancelled', 'failed', 'returned']);
            } else {
                $query->where('order_status', $status);
            }
        }

        if ($activeBranchId) {
            $query->where(function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
        }

        $invoices = $query->with('platform')->get(['id', 'source_link', 'platform_id', 'sale_type', 'woocommerce_order_id', 'unique_id', 'total_amount']);
        $totalOrders = $invoices->count();
        $totalValue = (float) $invoices->sum('total_amount');

        $webOrderLinkedInvIds = WebsiteOrder::pluck('invoice_id')->filter()->toArray();

        $palette = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#a855f7', '#64748b'];
        $sourceMap = [];

        foreach ($invoices as $inv) {
            $isWoo = ($inv->sale_type === 'WooCommerce' || !empty($inv->woocommerce_order_id) || str_starts_with($inv->unique_id ?? '', 'WC-'));
            $isWeb = (!$isWoo && ($inv->source_link === 'Website' || $inv->sale_type === 'Website' || in_array($inv->id, $webOrderLinkedInvIds)));

            if ($isWoo) {
                $src = 'WORDPRESS (WOOCOMMERCE)';
            } elseif ($isWeb) {
                $src = 'WEBSITE (E-COMMERCE)';
            } elseif (!empty($inv->platform?->name)) {
                $src = strtoupper(trim($inv->platform->name));
            } elseif (!empty($inv->source_link)) {
                $src = strtoupper(trim($inv->source_link));
            } else {
                $src = 'DIRECT / GENERAL ONLINE';
            }

            if (!isset($sourceMap[$src])) {
                $sourceMap[$src] = [
                    'name' => $src,
                    'count' => 0,
                    'value' => 0,
                    'inv_ids' => []
                ];
            }
            $sourceMap[$src]['count']++;
            $sourceMap[$src]['value'] += (float)$inv->total_amount;
            $sourceMap[$src]['inv_ids'][] = $inv->id;
        }

        if (empty($sourceMap)) {
            $sourceMap['WORDPRESS (WOOCOMMERCE)'] = ['name' => 'WORDPRESS (WOOCOMMERCE)', 'count' => 0, 'value' => 0, 'inv_ids' => []];
        }

        $labels = [];
        $counts = [];
        $values = [];
        $colors = [];
        $tableRows = [];
        $colorIdx = 0;

        foreach ($sourceMap as $srcKey => $srcData) {
            $c = $palette[$colorIdx % count($palette)];
            $colorIdx++;

            $labels[] = $srcData['name'];
            $counts[] = $srcData['count'];
            $values[] = $srcData['value'];
            $colors[] = $c;

            $qty = (float) InvoiceItem::whereIn('invoice_id', $srcData['inv_ids'])->sum('main_qty');
            $share = $totalOrders > 0 ? round(($srcData['count'] / $totalOrders) * 100, 1) : 0;

            $tableRows[] = [
                'name' => $srcData['name'],
                'count' => $srcData['count'],
                'qty' => $qty,
                'value' => $srcData['value'],
                'value_formatted' => number_format($srcData['value'], 2),
                'share' => $share,
                'color' => $c
            ];
        }

        return [
            'total_orders' => $totalOrders,
            'total_value' => $totalValue,
            'total_value_formatted' => number_format($totalValue, 2),
            'labels' => $labels,
            'counts' => $counts,
            'values' => $values,
            'colors' => $colors,
            'table_rows' => $tableRows,
            'filter_label' => $filter === 'custom' ? ($sdate->format('M d') . ' - ' . $edate->format('M d, Y')) : str_replace('-', ' ', $filter)
        ];
    }

    public function getOrderCountsData($filter = 'Last-30-days', $startDate = null, $endDate = null, $branchId = null)
    {
        $sdate = null;
        $edate = null;
        
        if ($filter === 'custom' && $startDate && $endDate) {
            $sdate = Carbon::parse($startDate);
            $edate = Carbon::parse($endDate);
        } elseif ($filter === 'single_date' && $startDate) {
            $sdate = Carbon::parse($startDate);
            $edate = Carbon::parse($startDate);
        } else {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today();
                    $edate = Carbon::today();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday();
                    $edate = Carbon::yesterday();
                    break;
                case 'Last-7-days':
                case '7days':
                    $sdate = Carbon::today()->subDays(6);
                    $edate = Carbon::today();
                    break;
                case 'Last-30-days':
                case '30days':
                    $sdate = Carbon::today()->subDays(29);
                    $edate = Carbon::today();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth();
                    $edate = Carbon::now()->endOfMonth();
                    break;
                case 'Last-month':
                    $sdate = Carbon::now()->subMonth()->startOfMonth();
                    $edate = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear();
                    $edate = Carbon::now()->endOfYear();
                    break;
                case 'Lifetime':
                    $firstInv = Invoice::orderBy('date', 'asc')->first();
                    $sdate = $firstInv && $firstInv->date ? Carbon::parse($firstInv->date) : Carbon::today()->subMonths(6);
                    $edate = Carbon::today();
                    break;
                default:
                    $sdate = Carbon::today()->subDays(29);
                    $edate = Carbon::today();
                    break;
            }
        }

        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        $ordersQuery = Invoice::where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        })->whereBetween('date', [$sdate->toDateString(), $edate->toDateString()]);

        if ($activeBranchId) {
            $ordersQuery->where(function($q) use ($activeBranchId) {
                $q->where('branch_id', $activeBranchId)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
        }

        $orders = $ordersQuery->get(['id', 'date', 'created_at', 'consignment_id', 'order_status']);

        $labels = [];
        $createdData = [];
        $sentToCourierData = [];

        $period = $sdate->daysUntil($edate);
        foreach ($period as $day) {
            $dateStr = $day->toDateString();
            $dayLabel = $day->format('M d');
            $dayOrders = $orders->where('date', $dateStr);

            $createdCount = $dayOrders->count();
            $courierCount = $dayOrders->filter(function($inv) {
                return !empty($inv->consignment_id) || in_array(strtolower($inv->order_status ?? ''), ['in courier', 'in_courier', 'shipped', 'delivered', 'partial_delivered']);
            })->count();

            $labels[] = $dayLabel;
            $createdData[] = $createdCount;
            $sentToCourierData[] = $courierCount;
        }

        return [
            'labels'           => $labels,
            'created_data'     => $createdData,
            'courier_data'     => $sentToCourierData,
            'total_created'    => array_sum($createdData),
            'total_courier'    => array_sum($sentToCourierData),
            'filter_label'     => $filter === 'custom' ? ($sdate->format('M d') . ' - ' . $edate->format('M d, Y')) : str_replace('-', ' ', $filter),
        ];
    }

    public function getEarningReportsData($period = 'year', $status = 'all', $branchId = null)
    {
        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        $monthsCount = 6;
        if ($period === 'year' || $period === '12months') {
            $monthsCount = 6; // last 6 months for clean monthly comparison bar display like screenshot Apr 26 to Sep 26
        } elseif ($period === '6months') {
            $monthsCount = 6;
        } elseif ($period === '3months') {
            $monthsCount = 3;
        } elseif ($period === '12months_full') {
            $monthsCount = 12;
        }

        $labels = [];
        $ordersData = [];
        $revenueData = [];
        $profitData = [];
        $expenseData = [];

        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $mStart = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $mEnd = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();
            $mLabel = Carbon::now()->subMonths($i)->format('M y');

            $labels[] = $mLabel;

            $invQ = Invoice::query()->whereBetween('date', [$mStart, $mEnd]);
            if ($activeBranchId) {
                $invQ->where('branch_id', $activeBranchId);
            }

            if ($status === 'approved' || $status === 'rts') {
                $invQ->whereIn('order_status', ['RTS', 'rts', 'Approved', 'approved']);
            } elseif ($status === 'delivered') {
                $invQ->whereIn('order_status', ['Delivered', 'delivered', 'completed']);
            } elseif ($status === 'processing') {
                $invQ->where(function($q) {
                    $q->whereNull('order_status')->orWhereIn('order_status', ['Processing', 'processing', 'Pending', 'pending']);
                });
            }

            $invoices = $invQ->get();
            $mCount = $invoices->count();
            $mRevenue = Invoice::getFakeSum(clone $invQ, 'total_amount');

            $expQ = Expense::query()->whereBetween('date', [$mStart, $mEnd]);
            if ($activeBranchId) {
                $expQ->where('branch_id', $activeBranchId);
            }
            $mExpense = (float) $expQ->sum('amount');

            $mProfit = (float) $this->calculateProfit([$mStart, $mEnd]);

            $ordersData[] = $mCount;
            $revenueData[] = (float)$mRevenue;
            $profitData[] = $mProfit;
            $expenseData[] = $mExpense;
        }

        $totalOrders = array_sum($ordersData);
        $totalRevenue = array_sum($revenueData);
        $totalProfit = array_sum($profitData);
        $totalExpense = array_sum($expenseData);

        $rangeLabel = Carbon::now()->subMonths($monthsCount - 1)->format('M y') . ' to ' . Carbon::now()->format('M y');

        return [
            'labels'         => $labels,
            'orders'         => $ordersData,
            'revenue'        => $revenueData,
            'profit'         => $profitData,
            'expense'        => $expenseData,
            'total_orders'   => $totalOrders,
            'total_revenue'  => $totalRevenue,
            'total_profit'   => $totalProfit,
            'total_expense'  => $totalExpense,
            'range_label'    => $rangeLabel,
            'selected_period'=> $period,
            'selected_status'=> $status,
        ];
    }

    public function getHourlyOrdersData($channel = 'web', $branchId = null)
    {
        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = $branchId ?: (($userBranchId == 1) ? $filterBranchId : $userBranchId);

        $todayStr = Carbon::today()->toDateString();
        $yesterdayStr = Carbon::yesterday()->toDateString();

        $todayQ = Invoice::query()->where('date', $todayStr);
        $yesterdayQ = Invoice::query()->where('date', $yesterdayStr);

        if ($channel === 'web' || $channel === 'online') {
            $channelFilter = function($q) {
                $q->whereIn('sale_type', ['Online', 'WooCommerce'])
                  ->orWhereNotNull('woocommerce_order_id')
                  ->orWhere('unique_id', 'like', 'WC-%');
            };
            $todayQ->where($channelFilter);
            $yesterdayQ->where($channelFilter);
        } elseif ($channel === 'manual' || $channel === 'pos') {
            $channelFilter = function($q) {
                $q->where(function($sq) {
                    $sq->whereNull('sale_type')->orWhereNotIn('sale_type', ['Online', 'WooCommerce']);
                })->whereNull('woocommerce_order_id')->where('unique_id', 'not like', 'WC-%');
            };
            $todayQ->where($channelFilter);
            $yesterdayQ->where($channelFilter);
        }

        if ($activeBranchId) {
            $todayQ->where('branch_id', $activeBranchId);
            $yesterdayQ->where('branch_id', $activeBranchId);
        }

        $todayOrders = $todayQ->get(['id', 'created_at']);
        $yesterdayOrders = $yesterdayQ->get(['id', 'created_at']);

        $hourlyLabels = [
            '12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM',
            '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM',
            '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM',
            '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM'
        ];

        $hourlyToday = array_fill(0, 24, 0);
        $hourlyYesterday = array_fill(0, 24, 0);

        foreach ($todayOrders as $inv) {
            if ($inv->created_at) {
                $hour = (int) $inv->created_at->format('H');
                $hourlyToday[$hour]++;
            }
        }

        foreach ($yesterdayOrders as $inv) {
            if ($inv->created_at) {
                $hour = (int) $inv->created_at->format('H');
                $hourlyYesterday[$hour]++;
            }
        }

        $currentHourStr = Carbon::now()->format('H:i');

        return [
            'labels'           => $hourlyLabels,
            'today'            => $hourlyToday,
            'yesterday'        => $hourlyYesterday,
            'total_today'      => array_sum($hourlyToday),
            'total_yesterday'  => array_sum($hourlyYesterday),
            'today_date'       => $todayStr,
            'yesterday_date'   => $yesterdayStr,
            'current_time_str' => $currentHourStr,
            'subtitle'         => "{$todayStr} vs {$yesterdayStr} (Until {$currentHourStr})",
            'channel'          => $channel,
        ];
    }

    // ReturnItem মডেলে
    public static function getRealSum($query, $column)
    {
        $items = $query->get();
        $realItems = $items->filter(function($item) {
            return $item->invoice && !$item->invoice->is_fake;
        });
        return $realItems->sum($column);
    }

    private function getDashboardData($filter, $startDate = null, $endDate = null, $channel = null)
    {
        $sdate = null;
        $edate = null;
        $isLifetime = false;

        if ($filter === 'custom' || ($startDate && $endDate)) {
            $sdate = Carbon::parse($startDate)->toDateString();
            $edate = Carbon::parse($endDate)->toDateString();
        } elseif ($filter === 'single_date' || ($startDate && !$endDate)) {
            $sdate = Carbon::parse($startDate)->toDateString();
            $edate = Carbon::parse($startDate)->toDateString();
        } else {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'Last-7-days':
                case '7days':
                    $sdate = Carbon::today()->subDays(6)->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Last-30-days':
                case '30days':
                    $sdate = Carbon::today()->subDays(29)->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'Last-month':
                    $sdate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                case 'Last-year':
                    $sdate = Carbon::now()->subYear()->startOfYear()->toDateString();
                    $edate = Carbon::now()->subYear()->endOfYear()->toDateString();
                    break;
                case 'Lifetime':
                    $isLifetime = true;
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        }

        $dateRange = $isLifetime ? null : [$sdate, $edate];
        $userBranchId = auth()->user()?->branch_id ?? 1;
        $filterBranchId = session('branch_filter_id', $userBranchId);
        $activeBranchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        $baseInv = Invoice::query();
        if ($dateRange) {
            $baseInv->whereBetween('date', $dateRange);
        }

        $totalpurchase = 0;
        $totalExpense = 0;

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $baseInv->where('branch_id', $filterBranchId);
                $purQ = Purchase::where('branch_id', $filterBranchId);
                $expQ = Expense::where('branch_id', $filterBranchId);
            } else {
                $purQ = Purchase::query();
                $expQ = Expense::query();
            }
        } else {
            $baseInv->where('branch_id', $userBranchId);
            $purQ = Purchase::where('branch_id', $userBranchId);
            $expQ = Expense::where('branch_id', $userBranchId);
        }

        if ($dateRange) {
            $purQ->whereBetween('date', $dateRange);
            $expQ->whereBetween('date', $dateRange);
        }
        $totalpurchase = $purQ->sum('total_amount');
        $totalExpense = $expQ->sum('amount');

        $manualInv = (clone $baseInv)->where(function($q) {
            $q->whereNull('sale_type')->orWhereNotIn('sale_type', ['Online', 'WooCommerce']);
        })->whereNull('woocommerce_order_id')->where('unique_id', 'not like', 'WC-%');

        $onlineInv = (clone $baseInv)->where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        $total_sale_today = Invoice::getFakeSum(clone $baseInv, 'total_amount');
        $manual_sale_today = Invoice::getFakeSum(clone $manualInv, 'total_amount');
        $online_sale_today = Invoice::getFakeSum(clone $onlineInv, 'total_amount');
        $manual_count_today = (clone $manualInv)->count();
        $online_count_today = (clone $onlineInv)->count();
        $manual_due_today = Invoice::getFakeSum(clone $manualInv, 'total_due');
        $online_due_today = Invoice::getFakeSum(clone $onlineInv, 'total_due');

        $manualProfit = $this->calculateManualProfit($dateRange, $activeBranchId);
        $manualProfitLifetime = $this->calculateManualProfit(null, $activeBranchId);
        $onlineProfit = $this->calculateOnlineProfit($dateRange, $activeBranchId);
        $onlineProfitLifetime = $this->calculateOnlineProfit(null, $activeBranchId);
        $onlineProfitOverview = $this->getOnlineProfitOverview($dateRange, $activeBranchId);
        $onlineProfitOverviewLifetime = $this->getOnlineProfitOverview(null, $activeBranchId);
        $bankSales = $this->getBankSalesBreakdown($dateRange, $activeBranchId);
        $bankSalesLifetime = $this->getBankSalesBreakdown(null, $activeBranchId);

        // Status counts for online orders
        $statusProcessingCount = (clone $onlineInv)->where(function($q) {
            $q->whereNull('order_status')->orWhereIn('order_status', ['Processing', 'processing', 'Pending', 'pending']);
        })->count();

        $statusRtsCount = (clone $onlineInv)->where(function($q) {
            $q->whereIn('order_status', ['RTS', 'rts', 'Approved', 'approved']);
        })->count();

        $statusInCourierCount = (clone $onlineInv)->where(function($q) {
            $q->whereNotNull('consignment_id')->orWhereIn('order_status', ['In Courier', 'in_courier', 'shipped', 'Shipped']);
        })->count();

        $statusDeliveredCount = (clone $onlineInv)->where(function($q) {
            $q->whereIn('order_status', ['Delivered', 'delivered', 'completed']);
        })->count();

        return [
            'sale'                            => $total_sale_today,
            'manual_sale'                     => $manual_sale_today,
            'online_sale'                     => $online_sale_today,
            'manual_count'                    => $manual_count_today,
            'online_count'                    => $online_count_today,
            'manual_due'                      => $manual_due_today,
            'online_due'                      => $online_due_today,
            'purchase'                        => $totalpurchase,
            'expense'                         => $totalExpense,
            'profit'                          => $dateRange ? $this->calculateProfit($dateRange) : $this->calculateProfit(['2000-01-01', Carbon::today()->toDateString()]),
            'manual_profit'                   => $manualProfit,
            'manual_profit_lifetime'          => $manualProfitLifetime,
            'online_profit'                   => $onlineProfit,
            'online_profit_lifetime'          => $onlineProfitLifetime,
            'online_profit_overview'          => $onlineProfitOverview,
            'online_profit_overview_lifetime' => $onlineProfitOverviewLifetime,
            'bank_sales'                      => $bankSales,
            'bank_sales_lifetime'             => $bankSalesLifetime,
            'status_processing'               => $statusProcessingCount,
            'status_rts'                      => $statusRtsCount,
            'status_in_courier'               => $statusInCourierCount,
            'status_delivered'                => $statusDeliveredCount,
            'start_date'                      => $sdate,
            'end_date'                        => $edate,
            'is_lifetime'                     => $isLifetime,
        ];
    }

    public function calculateManualProfit($dateRange = null, $branchId = null)
    {
        $manualInvoices = Invoice::where(function($q) {
            $q->whereNull('sale_type')->orWhereNotIn('sale_type', ['Online', 'WooCommerce']);
        })->whereNull('woocommerce_order_id')->where('unique_id', 'not like', 'WC-%');

        if ($branchId) {
            $manualInvoices->where('branch_id', $branchId);
        }
        if ($dateRange) {
            $manualInvoices->whereBetween('date', $dateRange);
        }

        $manualInvIds = $manualInvoices->pluck('id');
        $saleCost = (float) InvoiceItem::whereIn('invoice_id', $manualInvIds)->sum('inv_subtotal');
        $purchaseCost = (float) InvoiceItem::whereIn('invoice_id', $manualInvIds)->sum('pur_subtotal');
        $discount = (float) Invoice::whereIn('id', $manualInvIds)->sum('discount_amount');
        $returnDis = (float) ReturnTbl::whereIn('invoice_id', $manualInvIds)->sum('discount_amount');

        return ($saleCost - $purchaseCost) - ($discount - $returnDis);
    }

    public function calculateOnlineProfit($dateRange = null, $branchId = null, $statusFilter = null)
    {
        $onlineInvoices = Invoice::where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($branchId) {
            $onlineInvoices->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
        }
        if ($dateRange) {
            $onlineInvoices->whereBetween('date', $dateRange);
        }
        if ($statusFilter === 'delivered') {
            $onlineInvoices->whereIn('order_status', ['Delivered', 'delivered', 'completed']);
        } elseif ($statusFilter === 'in_transit') {
            $onlineInvoices->whereNotIn('order_status', ['Delivered', 'delivered', 'completed', 'Cancelled', 'cancelled', 'failed', 'returned']);
        }

        $onlineInvIds = $onlineInvoices->pluck('id');
        $saleCost = (float) InvoiceItem::whereIn('invoice_id', $onlineInvIds)->sum('inv_subtotal');
        $purchaseCost = (float) InvoiceItem::whereIn('invoice_id', $onlineInvIds)->sum('pur_subtotal');
        $discount = (float) Invoice::whereIn('id', $onlineInvIds)->sum('discount_amount');
        $returnDis = (float) ReturnTbl::whereIn('invoice_id', $onlineInvIds)->sum('discount_amount');

        return ($saleCost - $purchaseCost) - ($discount - $returnDis);
    }

    public function getOnlineProfitOverview($dateRange = null, $branchId = null)
    {
        $baseQuery = Invoice::where(function($q) {
            $q->whereIn('sale_type', ['Online', 'WooCommerce'])
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($branchId) {
            $baseQuery->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhere('sale_type', 'WooCommerce')
                  ->orWhereNotNull('woocommerce_order_id');
            });
        }
        if ($dateRange) {
            $baseQuery->whereBetween('date', $dateRange);
        }

        $invoices = $baseQuery->get(['id', 'order_status', 'total_amount', 'discount_amount', 'consignment_id']);
        $allIds = $invoices->pluck('id');

        $totalSales = (float) $invoices->sum('total_amount');
        $saleCost = (float) InvoiceItem::whereIn('invoice_id', $allIds)->sum('inv_subtotal');
        $purchaseCost = (float) InvoiceItem::whereIn('invoice_id', $allIds)->sum('pur_subtotal');
        $discount = (float) $invoices->sum('discount_amount');
        $returnDis = (float) ReturnTbl::whereIn('invoice_id', $allIds)->sum('discount_amount');

        $totalProfit = ($saleCost - $purchaseCost) - ($discount - $returnDis);
        $marginPercent = $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 1) : 0;

        // Delivered
        $deliveredInvs = $invoices->filter(fn($i) => in_array(strtolower($i->order_status ?? ''), ['delivered', 'completed']));
        $delIds = $deliveredInvs->pluck('id');
        $delSaleCost = (float) InvoiceItem::whereIn('invoice_id', $delIds)->sum('inv_subtotal');
        $delPurchaseCost = (float) InvoiceItem::whereIn('invoice_id', $delIds)->sum('pur_subtotal');
        $delDiscount = (float) $deliveredInvs->sum('discount_amount');
        $delReturnDis = (float) ReturnTbl::whereIn('invoice_id', $delIds)->sum('discount_amount');
        $deliveredProfit = ($delSaleCost - $delPurchaseCost) - ($delDiscount - $delReturnDis);
        $delSales = (float) $deliveredInvs->sum('total_amount');

        // In-Transit / Processing
        $transitInvs = $invoices->filter(fn($i) => !in_array(strtolower($i->order_status ?? ''), ['delivered', 'completed', 'cancelled', 'failed', 'returned']));
        $transIds = $transitInvs->pluck('id');
        $transSaleCost = (float) InvoiceItem::whereIn('invoice_id', $transIds)->sum('inv_subtotal');
        $transPurchaseCost = (float) InvoiceItem::whereIn('invoice_id', $transIds)->sum('pur_subtotal');
        $transDiscount = (float) $transitInvs->sum('discount_amount');
        $transReturnDis = (float) ReturnTbl::whereIn('invoice_id', $transIds)->sum('discount_amount');
        $transitProfit = ($transSaleCost - $transPurchaseCost) - ($transDiscount - $transReturnDis);
        $transSales = (float) $transitInvs->sum('total_amount');

        return [
            'total_profit'     => $totalProfit,
            'total_sales'      => $totalSales,
            'total_cost'       => $purchaseCost,
            'margin_percent'   => $marginPercent,
            'delivered_profit' => $deliveredProfit,
            'delivered_count'  => $deliveredInvs->count(),
            'delivered_sales'  => $delSales,
            'transit_profit'   => $transitProfit,
            'transit_count'    => $transitInvs->count(),
            'transit_sales'    => $transSales,
        ];
    }

    public function getBankSalesBreakdown($dateRange = null, $branchId = null)
    {
        $query = \App\Models\BankTransaction::whereNotNull('invoice_id')
            ->where('trans_type', 'deposit')
            ->with('bank_account');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($dateRange) {
            $query->whereBetween('date', $dateRange);
        }

        $transactions = $query->get();
        $allAccounts = \App\Models\BankAccount::where('status', 1)->get();

        $breakdown = [];
        $totalBankSales = (float) $transactions->sum('amount');

        foreach ($allAccounts as $acc) {
            $accTx = $transactions->where('bank_id', $acc->id);
            $collected = (float) $accTx->sum('amount');
            $count = $accTx->count();
            $percent = $totalBankSales > 0 ? round(($collected / $totalBankSales) * 100, 1) : 0;

            $breakdown[] = [
                'id' => $acc->id,
                'name' => $acc->bank_name,
                'account_number' => $acc->account_number,
                'amount' => $collected,
                'count' => $count,
                'percentage' => $percent,
            ];
        }

        return [
            'total' => $totalBankSales,
            'accounts' => $breakdown
        ];
    }

    private function calculateProfit($dateRange)
    {
        $userBranchId = auth()->user()?->branch_id;
        $filterBranchId = session('branch_filter_id', $userBranchId);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'pur_subtotal');
                $discount = Invoice::getFakeSum(Invoice::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange), 'discount_amount');
                $returnDis = ReturnTbl::where('branch_id', $filterBranchId)->whereBetween('date', $dateRange)->sum('discount_amount');
            } else {
                $saleCost = InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', $dateRange), 'inv_subtotal');
                $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::whereBetween('date', $dateRange), 'pur_subtotal');
                $discount = Invoice::getFakeSum(Invoice::query()->whereBetween('date', $dateRange), 'discount_amount');
                $returnDis = ReturnTbl::whereBetween('date', $dateRange)->sum('discount_amount');
            }
        } else {
            $saleCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'inv_subtotal');
            $purchaseCost = InvoiceItem::getFakeSum(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'pur_subtotal');
            $discount = Invoice::getFakeSum(Invoice::where('branch_id', $userBranchId)->whereBetween('date', $dateRange), 'discount_amount');
            $returnDis = ReturnTbl::where('branch_id', $userBranchId)->whereBetween('date', $dateRange)->sum('discount_amount');
        }

        $dis = $discount - $returnDis;
        $totalProfit = $saleCost - $purchaseCost;
        $profit = $totalProfit - $dis;
        return $profit;
    }



    public function downloadPDF(Request $request)
    {
        $note = $request->input('note');

        $pdf = Pdf::loadView('pdf.note', compact('note'));
        return $pdf->download('note.pdf');
    }

    /**
     * Product sales and profit summary data for date filter
     */
    public function productSalesSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filter = $request->input('filter');

        if ($filter) {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        } else {
            $sdate = $startDate ? Carbon::parse($startDate)->toDateString() : Carbon::today()->toDateString();
            $edate = $endDate ? Carbon::parse($endDate)->toDateString() : Carbon::today()->toDateString();
        }

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $itemsQuery = InvoiceItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $returnsQuery = ReturnItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        if ($branchId) {
            $itemsQuery->where('branch_id', $branchId);
            $returnsQuery->where('branch_id', $branchId);
        }

        $allInvoiceItems = InvoiceItem::filterByFakeSale($itemsQuery);
        $allReturnItems = ReturnItem::filterByFakeSale($returnsQuery);

        $salesGrouped = $allInvoiceItems->groupBy('product_id');
        $returnsGrouped = $allReturnItems->groupBy('product_id');
        $allProductIds = $salesGrouped->keys()->concat($returnsGrouped->keys())->unique();

        $summary = [];
        $grandTotalQty = 0;
        $grandTotalReturnQty = 0;
        $grandTotalReturnAmount = 0;
        $grandTotalSaleAmount = 0;
        $grandTotalCostAmount = 0;
        $grandTotalProfit = 0;

        foreach ($allProductIds as $productId) {
            $items = $salesGrouped->get($productId, collect());
            $retItems = $returnsGrouped->get($productId, collect());

            $product = $items->first()?->product ?? $retItems->first()?->product;
            if (!$product) continue;

            $totalQty = (float)$items->sum('main_qty');
            $returnQty = (float)$retItems->sum('main_qty');
            $totalSale = (float)$items->sum('inv_subtotal');
            $returnSale = (float)$retItems->sum('subtotal');
            $totalCost = (float)$items->sum('pur_subtotal');
            $returnCost = (float)$retItems->sum('pur_subtotal');

            $netSale = $totalSale - $returnSale;
            $netCost = $totalCost - $returnCost;
            $totalProfit = $netSale - $netCost;

            $summary[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'return_qty' => $returnQty,
                'return_amount' => $returnSale,
                'total_qty' => $totalQty,
                'total_sale' => $totalSale,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ];

            $grandTotalQty += $totalQty;
            $grandTotalReturnQty += $returnQty;
            $grandTotalReturnAmount += $returnSale;
            $grandTotalSaleAmount += $totalSale;
            $grandTotalCostAmount += $totalCost;
            $grandTotalProfit += $totalProfit;
        }

        usort($summary, function($a, $b) {
            return $b['total_sale'] <=> $a['total_sale'];
        });

        return response()->json([
            'success' => true,
            'sdate' => $sdate,
            'edate' => $edate,
            'summary' => $summary,
            'grand_qty' => $grandTotalQty,
            'grand_return_qty' => $grandTotalReturnQty,
            'grand_return_amount' => $grandTotalReturnAmount,
            'grand_sale' => $grandTotalSaleAmount,
            'grand_cost' => $grandTotalCostAmount,
            'grand_profit' => $grandTotalProfit,
        ]);
    }

    /**
     * Printable view for product sales and profit summary
     */
    public function printProductSalesSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filter = $request->input('filter');

        if ($filter) {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        } else {
            $sdate = $startDate ? Carbon::parse($startDate)->toDateString() : Carbon::today()->toDateString();
            $edate = $endDate ? Carbon::parse($endDate)->toDateString() : Carbon::today()->toDateString();
        }

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $itemsQuery = InvoiceItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $returnsQuery = ReturnItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        if ($branchId) {
            $itemsQuery->where('branch_id', $branchId);
            $returnsQuery->where('branch_id', $branchId);
        }

        $allInvoiceItems = InvoiceItem::filterByFakeSale($itemsQuery);
        $allReturnItems = ReturnItem::filterByFakeSale($returnsQuery);

        $salesGrouped = $allInvoiceItems->groupBy('product_id');
        $returnsGrouped = $allReturnItems->groupBy('product_id');
        $allProductIds = $salesGrouped->keys()->concat($returnsGrouped->keys())->unique();

        $summary = [];
        $grandTotalQty = 0;
        $grandTotalReturnQty = 0;
        $grandTotalReturnAmount = 0;
        $grandTotalSaleAmount = 0;
        $grandTotalCostAmount = 0;
        $grandTotalProfit = 0;

        foreach ($allProductIds as $productId) {
            $items = $salesGrouped->get($productId, collect());
            $retItems = $returnsGrouped->get($productId, collect());

            $product = $items->first()?->product ?? $retItems->first()?->product;
            if (!$product) continue;

            $totalQty = (float)$items->sum('main_qty');
            $returnQty = (float)$retItems->sum('main_qty');
            $totalSale = (float)$items->sum('inv_subtotal');
            $returnSale = (float)$retItems->sum('subtotal');
            $totalCost = (float)$items->sum('pur_subtotal');
            $returnCost = (float)$retItems->sum('pur_subtotal');

            $netSale = $totalSale - $returnSale;
            $netCost = $totalCost - $returnCost;
            $totalProfit = $netSale - $netCost;

            $summary[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'return_qty' => $returnQty,
                'return_amount' => $returnSale,
                'total_qty' => $totalQty,
                'total_sale' => $totalSale,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ];

            $grandTotalQty += $totalQty;
            $grandTotalReturnQty += $returnQty;
            $grandTotalReturnAmount += $returnSale;
            $grandTotalSaleAmount += $totalSale;
            $grandTotalCostAmount += $totalCost;
            $grandTotalProfit += $totalProfit;
        }

        usort($summary, function($a, $b) {
            return $b['total_sale'] <=> $a['total_sale'];
        });

        $branch = \App\Models\Branch::find($branchId);

        return view('backend.pages.dashboard.summary_print', compact(
            'summary', 'sdate', 'edate', 
            'grandTotalQty', 'grandTotalReturnQty', 'grandTotalReturnAmount',
            'grandTotalSaleAmount', 'grandTotalCostAmount', 'grandTotalProfit', 'branch'
        ));
    }

    /**
     * Download or stream PDF for product sales and profit summary
     */
    public function downloadProductSalesSummaryPDF(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filter = $request->input('filter');

        if ($filter) {
            switch ($filter) {
                case 'Today':
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
                case 'Yesterday':
                    $sdate = Carbon::yesterday()->toDateString();
                    $edate = Carbon::yesterday()->toDateString();
                    break;
                case 'This-week':
                    $sdate = Carbon::now()->startOfWeek()->toDateString();
                    $edate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'This-month':
                    $sdate = Carbon::now()->startOfMonth()->toDateString();
                    $edate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'This-year':
                    $sdate = Carbon::now()->startOfYear()->toDateString();
                    $edate = Carbon::now()->endOfYear()->toDateString();
                    break;
                default:
                    $sdate = Carbon::today()->toDateString();
                    $edate = Carbon::today()->toDateString();
                    break;
            }
        } else {
            $sdate = $startDate ? Carbon::parse($startDate)->toDateString() : Carbon::today()->toDateString();
            $edate = $endDate ? Carbon::parse($endDate)->toDateString() : Carbon::today()->toDateString();
        }

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        $itemsQuery = InvoiceItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        $returnsQuery = ReturnItem::with(['product'])
            ->whereBetween('date', [$sdate, $edate]);

        if ($branchId) {
            $itemsQuery->where('branch_id', $branchId);
            $returnsQuery->where('branch_id', $branchId);
        }

        $allInvoiceItems = InvoiceItem::filterByFakeSale($itemsQuery);
        $allReturnItems = ReturnItem::filterByFakeSale($returnsQuery);

        $salesGrouped = $allInvoiceItems->groupBy('product_id');
        $returnsGrouped = $allReturnItems->groupBy('product_id');
        $allProductIds = $salesGrouped->keys()->concat($returnsGrouped->keys())->unique();

        $summary = [];
        $grandTotalQty = 0;
        $grandTotalReturnQty = 0;
        $grandTotalReturnAmount = 0;
        $grandTotalSaleAmount = 0;
        $grandTotalCostAmount = 0;
        $grandTotalProfit = 0;

        foreach ($allProductIds as $productId) {
            $items = $salesGrouped->get($productId, collect());
            $retItems = $returnsGrouped->get($productId, collect());

            $product = $items->first()?->product ?? $retItems->first()?->product;
            if (!$product) continue;

            $totalQty = (float)$items->sum('main_qty');
            $returnQty = (float)$retItems->sum('main_qty');
            $totalSale = (float)$items->sum('inv_subtotal');
            $returnSale = (float)$retItems->sum('subtotal');
            $totalCost = (float)$items->sum('pur_subtotal');
            $returnCost = (float)$retItems->sum('pur_subtotal');

            $netSale = $totalSale - $returnSale;
            $netCost = $totalCost - $returnCost;
            $totalProfit = $netSale - $netCost;

            $summary[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'code' => $product->barcode ?: $product->id,
                'return_qty' => $returnQty,
                'return_amount' => $returnSale,
                'total_qty' => $totalQty,
                'total_sale' => $totalSale,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ];

            $grandTotalQty += $totalQty;
            $grandTotalReturnQty += $returnQty;
            $grandTotalReturnAmount += $returnSale;
            $grandTotalSaleAmount += $totalSale;
            $grandTotalCostAmount += $totalCost;
            $grandTotalProfit += $totalProfit;
        }

        usort($summary, function($a, $b) {
            return $b['total_sale'] <=> $a['total_sale'];
        });

        $branch = \App\Models\Branch::find($branchId);

        $pdf = Pdf::loadView('backend.pages.dashboard.summary_pdf', compact(
            'summary', 'sdate', 'edate', 
            'grandTotalQty', 'grandTotalReturnQty', 'grandTotalReturnAmount',
            'grandTotalSaleAmount', 'grandTotalCostAmount', 'grandTotalProfit', 'branch'
        ))->setPaper('a4', 'portrait');

        $fileName = 'sales_summary_' . $sdate . '_to_' . $edate . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }
}
