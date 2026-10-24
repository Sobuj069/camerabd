<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Brand;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\CarbonPeriod;
use App\Models\OwnerShip;
use App\Models\DamageItem;
use App\Models\ReturnItem;
use App\Models\BankAccount;
use App\Models\BranchBrand;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\UsedProduct;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\ReturnPurchase;
use App\Models\ReturnPurchaseItem;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use Illuminate\Support\Carbon;
use App\Models\BankTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function stock(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['sub_category_id'] = $request->sub_category_id;
        $data['brand_id'] = $request->brand_id;
        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $query = Product::with(['unit.related_unit', 'subCategory'])->where('is_service', 0);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $brandIds = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $data['produc'] = Product::whereIn('id', $productIds)->with('unit.related_unit')
                    ->where('is_service', 0)
                    ->orderBy('created_at', 'DESC')
                    ->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->get();
                $data['brands'] = Brand::whereIn('id', $brandIds)->orderBy('id', 'desc')->get();
                $query->whereIn('id', $productIds);
            } else {
                $data['produc'] = Product::with('unit.related_unit')
                    ->where('is_service', 0)
                    ->orderBy('created_at', 'DESC')
                    ->get();
                $data['categories'] = Category::orderBy('id', 'desc')->get();
                $data['brands'] = Brand::orderBy('id', 'desc')->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $brandIds = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');
            $data['produc'] = Product::whereIn('id', $productIds)->with('unit.related_unit')
                ->where('is_service', 0)
                ->orderBy('created_at', 'DESC')
                ->get();
            $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->get();
            $data['brands'] = Brand::whereIn('id', $brandIds)->orderBy('id', 'desc')->get();
            $query->whereIn('id', $productIds);
        }

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }
        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->sub_category_id != null) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->brand_id != null) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->search_keyword != null) {
            $keyword = $request->search_keyword;
            $resolved = resolveProductAndVariationFromBarcode($keyword);
            if ($resolved['product_id']) {
                // Variation barcode scanned — filter directly by the resolved product
                $query->where('id', $resolved['product_id']);
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('barcode', 'like', '%' . $keyword . '%');
                });
            }
        }

        $allProductsQuery = clone $query;
        $allProducts = $allProductsQuery->with('product_variations')->get();

        $data['products'] = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(20)->appends($request->all());

        // grand totals
        $grand_purchase   = 0;
        $grand_sale       = 0;
        $grand_sale_ret   = 0;
        $grand_pur_ret    = 0;
        $grand_damage     = 0;
        $grand_stock_qty  = 0;
        $grand_adjust_in_total = 0;
        $grand_adjust_out_total = 0;

        foreach ($allProducts as $product) {
            $purchase = (float) purchased_qty($product);
            $sale     = (float) invoiced_qty($product);
            $sale_ret = (float) returned_qty($product);
            $pur_ret  = (float) return_pur_qty($product);
            $damage   = (float) damaged_qty($product);
            $adjust_in_total = (float) adjust_in($product);
            $adjust_out_total = (float)adjust_out($product);

            $stock = $purchase - $sale + $adjust_in_total + $sale_ret - $adjust_out_total - $pur_ret - $damage;


            $grand_purchase  += $purchase;
            $grand_sale      += $sale;
            $grand_sale_ret  += $sale_ret;
            $grand_pur_ret   += $pur_ret;
            $grand_damage    += $damage;
            $grand_stock_qty += $stock;
            $grand_adjust_in_total += $adjust_in_total;
            $grand_adjust_out_total += $adjust_out_total;
        }





        return view('backend.pages.report.sc_stock', $data, compact(
            'grand_purchase',
            'grand_sale',
            'grand_sale_ret',
            'grand_pur_ret',
            'grand_damage',
            'grand_adjust_in_total',
            'grand_adjust_out_total',
            'grand_stock_qty'
        ));
    }

    public function supplierLedger(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['suppliers'] = Supplier::where('branch_id', $filterBranchId)->orderBy('created_at', 'desc')->get();
            } else {
                $data['suppliers'] = Supplier::orderBy('created_at', 'desc')->get();
            }
        } else {
            $data['suppliers'] = Supplier::where('branch_id', $userBranchId)->orderBy('created_at', 'desc')->get();
        }

        if ($request->all() != NULL) {
            $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;
            $data['oneSupplier'] = Supplier::where('id', $request->supplier_id)->where('status', 1)->first();
            $data['sdate'] = Carbon::createFromDate($request->start_date)->format('Y-m-d');
            $data['edate'] = Carbon::createFromDate($request->end_date)->format('Y-m-d');

            $data['transaction'] = Transaction::with('invoice', 'purchase', 'supplier')
                ->where('supplier_id', $request->supplier_id)
                ->whereBetween('date', [$data['sdate'], $data['edate']])
                ->when($branchId, function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();


            // Calculate Opening Balance before start date
            $before_trans = Transaction::where('supplier_id', $request->supplier_id)
                ->where('date', '<', $data['sdate'])
                ->when($branchId, function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->get();
            $previous_balance = $before_trans->sum('debit') - $before_trans->sum('credit');
            $data['previous_balance'] = $previous_balance;

            $data['closeing_balance'] = $previous_balance + $data['transaction']->sum('debit') - $data['transaction']->sum('credit');
        }
        return view('backend.pages.report.supplier_ledger', $data);
    }

    public function customerLedger(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        if ($branchId) {
            $data['customers'] = Customer::where('branch_id', $branchId)->orderBy('created_at', 'desc')->get();
        } else {
            $data['customers'] = Customer::orderBy('created_at', 'desc')->get();
        }

        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->format('Y-m-d');
            $data['edate'] = Carbon::createFromDate($request->end_date)->format('Y-m-d');

            if ($branchId) {
                $data['oneCustomer'] = Customer::where('branch_id', $branchId)->where('id', $request->customer_id)->where('status', 1)->first();
            } else {
                $data['oneCustomer'] = Customer::where('id', $request->customer_id)->where('status', 1)->first();
            }

            // Get transactions within period (filtered by active branch)
            $transQuery = Transaction::with('invoice', 'purchase', 'customer')
                ->where('customer_id', $request->customer_id)
                ->whereBetween('date', [$data['sdate'], $data['edate']]);
            
            if ($branchId) {
                $transQuery->where('branch_id', $branchId);
            }
            $transactions = Transaction::filterByFakeSale($transQuery, 'asc');
            $data['transaction'] = $transactions->sort(function ($a, $b) {
                $isOpeningA = ($a->transaction_type === 'Opening Due Amount' || $a->transaction_type === 'Opening Balance');
                $isOpeningB = ($b->transaction_type === 'Opening Due Amount' || $b->transaction_type === 'Opening Balance');

                if ($isOpeningA && !$isOpeningB) return -1;
                if (!$isOpeningA && $isOpeningB) return 1;

                if ($a->date === $b->date) {
                    return $a->id <=> $b->id;
                }
                return strcmp($a->date, $b->date);
            })->values();


            // Calculate Opening Balance before start date
            $beforeDebitQuery = Transaction::where('customer_id', $request->customer_id)->where('date', '<', $data['sdate']);
            $beforeCreditQuery = Transaction::where('customer_id', $request->customer_id)->where('date', '<', $data['sdate']);
            if ($branchId) {
                $beforeDebitQuery->where('branch_id', $branchId);
                $beforeCreditQuery->where('branch_id', $branchId);
            }
            $previous_balance = Transaction::getFakeSum($beforeDebitQuery, 'debit') - Transaction::getFakeSum($beforeCreditQuery, 'credit');
            $data['previous_balance'] = $previous_balance;

            // Calculate Closing Balance (Opening Balance + period total)
            $periodDebitQuery = Transaction::where('customer_id', $request->customer_id)->whereBetween('date', [$data['sdate'], $data['edate']]);
            $periodCreditQuery = Transaction::where('customer_id', $request->customer_id)->whereBetween('date', [$data['sdate'], $data['edate']]);
            if ($branchId) {
                $periodDebitQuery->where('branch_id', $branchId);
                $periodCreditQuery->where('branch_id', $branchId);
            }
            
            $data['closeing_balance'] = $previous_balance 
                + Transaction::getFakeSum($periodDebitQuery, 'debit') 
                - Transaction::getFakeSum($periodCreditQuery, 'credit');
        }
        return view('backend.pages.report.customer_ledger', $data);
    }

    public function customerFullReport(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        if ($branchId) {
            $data['customers'] = Customer::where('branch_id', $branchId)->orderBy('name')->get();
        } else {
            $data['customers'] = Customer::orderBy('name')->get();
        }

        $data['customer_id'] = $request->customer_id;
        $data['customer'] = null;
        $data['invoices'] = collect();
        $data['transactions'] = collect();

        if ($request->filled('customer_id')) {
            $customer = Customer::with('vehicles')->find($request->customer_id);
            $data['customer'] = $customer;

            // All invoices for this customer
            $data['invoices'] = Invoice::with('invoiceItems.product')
                ->where('customer_id', $request->customer_id)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderBy('date', 'asc')
                ->get();

            // All transactions for this customer
            $data['transactions'] = Transaction::with('invoice')
                ->where('customer_id', $request->customer_id)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderBy('date', 'asc')
                ->get();

            // Summary totals
            $data['total_invoice_amount'] = $data['invoices']->sum('total_amount');
            $data['total_paid']           = $data['invoices']->sum('total_paid');
            $data['total_due']            = $data['invoices']->sum('total_due');
            $data['opening_due']          = $customer->due_amount ?? 0;
            $data['grand_due']            = $data['total_due'] + $data['opening_due'];
        }

        return view('backend.pages.report.customer_full_report', $data);
    }

    public function customerDue(Request $request)
    {

        $data['customer_id'] = $request->customer_id;
        $data['phone_no'] = $request->phone_no;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $customersQuery = Customer::query();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['custommer'] = Customer::where('branch_id', $filterBranchId)->orderBy('created_at', 'desc')->get();
                $customersQuery->where('branch_id', $filterBranchId);
            } else {
                $data['custommer'] = Customer::orderBy('created_at', 'desc')->get();
            }
        } else {
            $data['custommer'] = Customer::where('branch_id', $userBranchId)->orderBy('created_at', 'desc')->get();
            $customersQuery->where('branch_id', $userBranchId);
        }

        if ($request->customer_id != null) {
            $customersQuery->where('id', $request->customer_id);
        }
        if ($request->phone_no != null) {
            $customersQuery->where('phone', $request->phone_no);
        }
        $filteredCustomers = $customersQuery->get()->filter(function ($customer) {
            $inv_due = Invoice::getFakeSum(Invoice::where('customer_id', $customer->id), 'total_due');
            $open_balance = open_balance_customer($customer->id, $customer->due_amount);
            return ($inv_due + $open_balance) > 0;
        })->values();

        $page = request()->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredCustomers->slice($offset, $perPage),
            $filteredCustomers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        $data['customers'] = $paginated;

        return view('backend.pages.report.customer_due', $data);
    }

    public function supplierDue(Request $request)
    {
        $data['supplier_id'] = $request->supplier_id;
        $data['phone_no'] = $request->phone_no;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $suppliersQuery = Supplier::query();
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['supliers'] = Supplier::where('branch_id', $filterBranchId)->get();
                $suppliersQuery->where('branch_id', $filterBranchId);
            } else {
                $data['supliers'] = Supplier::get();
            }
        } else {
            $data['supliers'] = Supplier::where('branch_id', $userBranchId)->get();
            $suppliersQuery->where('branch_id', $userBranchId);
        }
        if ($request->supplier_id != null) {
            $suppliersQuery->where('id', $request->supplier_id);
        }

        if ($request->phone_no != null) {
            $suppliersQuery->where('phone', $request->phone_no);
        }
        $filteredSuppliers = $suppliersQuery->get()->filter(function ($supplier) {
            $total_payable = \App\Models\Purchase::where('supplier_id', $supplier->id)->sum('total_amount');
            $total_paid = \App\Models\Purchase::where('supplier_id', $supplier->id)->sum('total_paid');
            $opening_balance = $supplier->opening_balance ?? 0;

            $total_due = ($total_payable - $total_paid) + $opening_balance;

            return $total_due > 0;
        })->values();

        $page = request()->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredSuppliers->slice($offset, $perPage),
            $filteredSuppliers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $data['suppliers'] = $paginated;

        return view('backend.pages.report.supplier_due', $data);
    }

    public function daily(Request $request)
    {
        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $ssdate = Carbon::createFromDate('2023-09-01')->toDateString();
            $sdate = Carbon::createFromDate($request->start_date)->toDateString();
            $data['bankAcc'] = BankAccount::where('status', 1)->orderBy('id', 'asc')->first();

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', null);

            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    $data['invoices'] = Invoice::filterByFakeSale(Invoice::with('invoiceItems')->where('branch_id', $filterBranchId)->where('date', $sdate)->get());
                    $data['bankTrans'] = BankTransaction::filterByFakeSale(BankTransaction::where('date', $sdate)->where('branch_id', $filterBranchId)->where('pay_type', 'duepay'));
                    $data['bankTransdue'] = BankTransaction::getFakeSum(BankTransaction::where('date', $sdate)->where('branch_id', $filterBranchId)->where('pay_type', 'duepay'), 'amount');
                    $previous_total_sale = Invoice::getFakeSum(Invoice::whereBetween('date', [$ssdate, $sdate])->where('branch_id', $filterBranchId), 'total_amount');
                    $today_total_sale = Invoice::getFakeSum(Invoice::where('date', $sdate)->where('branch_id', $filterBranchId), 'total_amount');
                    $privious_sale = ($previous_total_sale - $today_total_sale);
                    $today_sale_amount = Invoice::getFakeSum(Invoice::where('date', $sdate)->where('branch_id', $filterBranchId), 'total_amount');
                    $total_expense = Expense::where('date', $sdate)->where('branch_id', $filterBranchId)->get();
                } else {
                    $data['invoices'] = Invoice::filterByFakeSale(Invoice::with('invoiceItems')->where('date', $sdate)->get());
                    $data['bankTrans'] = BankTransaction::filterByFakeSale(BankTransaction::where('date', $sdate)->where('pay_type', 'duepay'));
                    $data['bankTransdue'] = BankTransaction::getFakeSum(BankTransaction::where('date', $sdate)->where('pay_type', 'duepay'), 'amount');
                    $previous_total_sale = Invoice::getFakeSum(Invoice::whereBetween('date', [$ssdate, $sdate]), 'total_amount');
                    $today_total_sale = Invoice::getFakeSum(Invoice::where('date', $sdate), 'total_amount');
                    $privious_sale = ($previous_total_sale - $today_total_sale);
                    $today_sale_amount = Invoice::getFakeSum(Invoice::where('date', $sdate), 'total_amount');
                    $total_expense = Expense::where('date', $sdate)->get();
                }
            } else {
                $invoiceQuery = Invoice::with('invoiceItems')->where('branch_id', $userBranchId)->where('date', $sdate);
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $invoiceQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['invoices'] = Invoice::filterByFakeSale($invoiceQuery->get());

                $bankTransQuery = BankTransaction::where('date', $sdate)->where('branch_id', $userBranchId)->where('pay_type', 'duepay');
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $bankTransQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['bankTrans'] = BankTransaction::filterByFakeSale($bankTransQuery);

                $bankTransDueQuery = BankTransaction::where('date', $sdate)->where('branch_id', $userBranchId)->where('pay_type', 'duepay');
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $bankTransDueQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['bankTransdue'] = BankTransaction::getFakeSum($bankTransDueQuery, 'amount');

                $prevSaleQuery = Invoice::whereBetween('date', [$ssdate, $sdate])->where('branch_id', $userBranchId);
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $prevSaleQuery->where('branch_id', auth()->user()->branch_id);
                }
                $previous_total_sale = Invoice::getFakeSum($prevSaleQuery, 'total_amount');

                $todayTotalSaleQuery = Invoice::where('date', $sdate)->where('branch_id', $userBranchId);
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $todayTotalSaleQuery->where('branch_id', auth()->user()->branch_id);
                }
                $today_total_sale = Invoice::getFakeSum($todayTotalSaleQuery, 'total_amount');

                $privious_sale = ($previous_total_sale - $today_total_sale);

                $todaySaleAmountQuery = Invoice::where('date', $sdate)->where('branch_id', $userBranchId);
                if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                    $todaySaleAmountQuery->where('branch_id', auth()->user()->branch_id);
                }
                $today_sale_amount = Invoice::getFakeSum($todaySaleAmountQuery, 'total_amount');

                $total_expense = Expense::where('date', $sdate)->where('branch_id', $userBranchId)->get();
            }

            return view('backend.pages.report.daily', $data, compact('today_sale_amount', 'total_expense', 'privious_sale', 'previous_total_sale'));
        } else {
            return view('backend.pages.report.daily');
        }
    }




    public function accountLedger(Request $request)
    {
        // dd($request->all());
        $data['accounts'] = \App\Models\BankAccount::all();

        if ($request->filled('bank_id') && $request->filled('start_date') && $request->filled('end_date')) {

            $data['oneAccount'] = BankAccount::find($request->bank_id);
            $bank_name = BankAccount::where('id', $request->bank_id)->first();

            // рждрж╛рж░рж┐ржЦ ржарж┐ржХ ржХрж░рж╛
            $sdate = Carbon::parse($request->start_date)->startOfDay();
            $edate = Carbon::parse($request->end_date)->endOfDay();

            // Start Date ржПрж░ ржЖржЧрзЗрж░ рж╕ржорж╕рзНржд ржЯрзНрж░рж╛ржиржЬрзНржпрж╛ржХрж╢ржи ржерзЗржХрзЗ Opening Balance ржмрзЗрж░ ржХрж░рж╛
            $before_deposit = BankTransaction::where('bank_id', $bank_name->id)
                ->where('trans_type', 'deposit')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $before_withdraw = BankTransaction::where('bank_id', $bank_name->id)
                ->where('trans_type', 'withdraw')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $before_transfer_out = BankTransaction::where('from_bank_id', $bank_name->id)
                ->where('trans_type', 'transfer')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $before_transfer_in = BankTransaction::where('to_bank_id', $bank_name->id)
                ->where('trans_type', 'transfer')
                ->where('date', '<', $sdate)
                ->sum('amount');

            $previous_balance = $bank_name->opening_balance + $before_deposit - $before_withdraw - $before_transfer_out + $before_transfer_in;

            $data['previous_balance_closing'] = $previous_balance;

            // ржлрж┐рж▓рзНржЯрж╛рж░ ржХрж░рж╛ ржЯрзНрж░рж╛ржиржЬрзНржпрж╛ржХрж╢ржи
            $data['bank_transaction'] = BankTransaction::where(function ($q) use ($request) {
                $q->where('bank_id', $request->bank_id)
                    ->orWhere('from_bank_id', $request->bank_id)
                    ->orWhere('to_bank_id', $request->bank_id);
            })
                ->whereBetween('date', [$sdate, $edate])
                ->orderBy('date', 'asc')
                ->get();
            $bank_transaction_closing_form = BankTransaction::where('from_bank_id', $request->bank_id)->where('trans_type', 'transfer')
                ->sum('amount');
            $bank_transaction_closing_to = BankTransaction::where('to_bank_id', $request->bank_id)->where('trans_type', 'transfer')
                ->sum('amount');
            $bank_transaction_closing_depo = BankTransaction::where('bank_id', $request->bank_id)->where('trans_type', 'deposit')
                ->sum('amount');
            $bank_transaction_closing_with = BankTransaction::where('bank_id', $request->bank_id)
                ->where('trans_type', 'withdraw')
                ->sum('amount');
            $balance_check = $bank_transaction_closing_depo - $bank_transaction_closing_with - $bank_transaction_closing_form + $bank_transaction_closing_to + $bank_name->opening_balance;

            $data['balance_closing'] = $balance_check;
        } else {
            $data['bank_transaction'] = collect(); // ржЦрж╛рж▓рж┐ ржХрж╛рж▓рзЗржХрж╢ржи

            $data['balance_closing'] = 0;
        }


        return view('backend.pages.report.account_ledger', $data);
    }




    public function dailyStock(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
        $data['edate'] = Carbon::parse($request->end_date)->toDateString();

        $sdate = $data['sdate'];
        $edate = $data['edate'];

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $purchaseItems = PurchaseItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
                $transferItems = TransferItem::where('from_branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
                $receiveItems = TransferItem::where('status', 1)->where('to_branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
                $invoiceItems  = InvoiceItem::filterByFakeSale(InvoiceItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate]));
                $returnItems   = ReturnItem::filterByFakeSale(ReturnItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate]));
                $damageItems   = DamageItem::where('branch_id', $filterBranchId)->whereBetween('date', [$sdate, $edate])->get();
            } else {
                $purchaseItems = PurchaseItem::whereBetween('date', [$sdate, $edate])->get();
                $transferItems = TransferItem::whereBetween('date', [$sdate, $edate])->get();
                $receiveItems = TransferItem::where('status', 1)->whereBetween('date', [$sdate, $edate])->get();
                $invoiceItems  = InvoiceItem::filterByFakeSale(InvoiceItem::whereBetween('date', [$sdate, $edate]));
                $returnItems   = ReturnItem::filterByFakeSale(ReturnItem::whereBetween('date', [$sdate, $edate]));
                $damageItems   = DamageItem::whereBetween('date', [$sdate, $edate])->get();
            }
        } else {
            $purchaseItems = PurchaseItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
            $transferItems = TransferItem::where('from_branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
            $receiveItems = TransferItem::where('status', 1)->where('to_branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
            $invoiceItems  = InvoiceItem::filterByFakeSale(InvoiceItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate]));
            $returnItems   = ReturnItem::filterByFakeSale(ReturnItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate]));
            $damageItems   = DamageItem::where('branch_id', $userBranchId)->whereBetween('date', [$sdate, $edate])->get();
        }



        $products = [];
        $productQuantities = []; // [product_id => [...qtys...]]

        $initProduct = function ($product_id) use (&$products, &$productQuantities) {
            if (!isset($products[$product_id])) {
                $product = Product::find($product_id);
                if ($product) {
                    $products[$product_id] = $product;
                }
            }
            if (!isset($productQuantities[$product_id])) {
                $productQuantities[$product_id] = [
                    'purchase' => 0,
                    'transfer' => 0,
                    'receive' => 0,
                    'invoice'  => 0,
                    'return'   => 0,
                    'damage'   => 0,
                ];
            }
        };

        // Loop and collect data
        foreach ($purchaseItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['purchase'] += $item->main_qty;
        }

        foreach ($transferItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['transfer'] += $item->main_qty;
        }
        foreach ($receiveItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['receive'] += $item->main_qty;
        }

        foreach ($invoiceItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['invoice'] += $item->main_qty;
        }

        foreach ($returnItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['return'] += $item->main_qty;
        }

        foreach ($damageItems as $item) {
            $initProduct($item->product_id);
            $productQuantities[$item->product_id]['damage'] += $item->main_qty;
        }

        // Pass to view
        $data['products'] = array_values($products);
        $data['productQuantities'] = $productQuantities;

        return view('backend.pages.report.daily_stock', $data);
    }

    public function sale(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        // Default data
        $data['products'] = Product::orderBy('created_at', 'DESC')->get();
        $data['categories'] = Category::orderBy('created_at', 'DESC')->get();
        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            // Branch Logic
            if ($userBranchId == 1) {
                $branchId = $filterBranchId;
            } else {
                $branchId = $userBranchId;
            }

            // Product + Category filter by branch
            if ($branchId) {
                $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $branchId)->pluck('category_id');

                $data['products'] = Product::whereIn('id', $productIds)->latest()->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->latest()->get();
            }

            // Main Query (IMPORTANT FIX)
            $invoiceQuery = Invoice::with(['invoiceItems' => function ($q) use ($request) {

                $q->where('is_return', 0);

                // Product filter
                if ($request->filled('product_id')) {
                    $q->where('product_id', $request->product_id);
                }

                // Category filter
                if ($request->filled('category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('category_id', $request->category_id);
                    });
                }

                // Sub Category filter
                if ($request->filled('sub_category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('sub_category_id', $request->sub_category_id);
                    });
                }
            }, 'invoiceItems.product.unit.related_unit'])
                ->whereBetween('date', [$sdate, $edate]);

            // Add role-based filter to match InvoiceController
            if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                $invoiceQuery->where('branch_id', auth()->user()->branch_id);
            }

            // Branch condition
            if ($branchId) {
                $invoiceQuery->where('branch_id', $branchId);
            }

            // Filter only invoices that have matching items
            $invoiceQuery->whereHas('invoiceItems', function ($q) use ($request) {

                if ($request->filled('product_id')) {
                    $q->where('product_id', $request->product_id);
                }

                if ($request->filled('category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('category_id', $request->category_id);
                    });
                }

                if ($request->filled('sub_category_id')) {
                    $q->whereHas('product', function ($sub) use ($request) {
                        $sub->where('sub_category_id', $request->sub_category_id);
                    });
                }
            });

            $data['invoiceItem'] = Invoice::filterByFakeSale($invoiceQuery);
        }

        return view('backend.pages.report.sale', $data);
    }

    public function itemSale(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $data['products'] = Product::whereIn('id', $productIds)->latest()->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->latest()->get();
            } else {
                $data['products'] = Product::latest()->get();
                $data['categories'] = Category::latest()->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $data['products'] = Product::whereIn('id', $productIds)->latest()->get();
            $data['categories'] = Category::whereIn('id', $categoryIds)->latest()->get();
        }

        if ($request->all() != null) {

            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            $invoiceItemQuery = InvoiceItem::with(['invoice', 'product'])
                ->where('is_return', 0)
                ->whereHas('invoice', function ($q) use ($sdate, $edate, $userBranchId, $filterBranchId) {
                    $q->whereBetween('date', [$sdate, $edate]);

                    // Add role-based filter to match InvoiceController
                    if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                        $q->where('branch_id', auth()->user()->branch_id);
                    }

                    if ($userBranchId == 1) {
                        if ($filterBranchId) {
                            $q->where('branch_id', $filterBranchId);
                        }
                    } else {
                        $q->where('branch_id', $userBranchId);
                    }
                });

            // Product filter
            if (!empty($request->product_id)) {
                $invoiceItemQuery->where('product_id', $request->product_id);
            }

            // Category filter
            if (!empty($request->category_id)) {
                $invoiceItemQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            // Sub Category filter
            if (!empty($request->sub_category_id)) {
                $invoiceItemQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('sub_category_id', $request->sub_category_id);
                });
            }

            $data['invoiceItem'] = InvoiceItem::filterByFakeSale($invoiceItemQuery);
        }

        return view('backend.pages.report.item_sale', $data);
    }

    public function service(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        // Only load service products
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $data['products'] = Product::whereIn('id', $productIds)->where('is_service', 1)->orderBy('name', 'asc')->get();
            } else {
                $data['products'] = Product::where('is_service', 1)->orderBy('name', 'asc')->get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['products'] = Product::whereIn('id', $productIds)->where('is_service', 1)->orderBy('name', 'asc')->get();
        }

        // Initialize variables
        $data['invoiceItem'] = null;

        // If filters applied
        if ($request->all() != null) {
            $data['oneProduct'] = Product::find($request->product_id);
            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            // Build base query on ServiceInvoice
            $invoiceQuery = \App\Models\ServiceInvoice::query()
                ->whereBetween('date', [$sdate, $edate]);

            // Add role-based filter
            if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                $invoiceQuery->where('branch_id', auth()->user()->branch_id);
            }

            // Branch handling for Super Admin
            if ($userBranchId == 1 && $filterBranchId) {
                $invoiceQuery->where('branch_id', $filterBranchId);
            }

            // Filter by product if selected
            if (!empty($request->product_id)) {
                $invoiceQuery->whereHas('items', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }

            // Get filtered data
            $data['invoiceItem'] = $invoiceQuery->orderBy('date', 'desc')->get();
        }

        return view('backend.pages.report.service_report', $data);
    }
    public function productSale(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        // Base product and category lists
        $data['products'] = Product::orderBy('created_at', 'DESC')->get();
        $data['categories'] = Category::orderBy('created_at', 'DESC')->get();

        // If filters applied
        if ($request->all() != null) {

            $data['oneProduct'] = Product::find($request->product_id);
            $data['sdate'] = Carbon::parse($request->start_date)->toDateString();
            $data['edate'] = Carbon::parse($request->end_date)->toDateString();

            $sdate = $data['sdate'];
            $edate = $data['edate'];

            // Build base query
            $invoiceQuery = Invoice::query()
                ->where('status', '!=', 2)
                ->whereBetween('date', [$sdate, $edate]);

            // Add role-based filter to match InvoiceController
            if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
                $invoiceQuery->where('branch_id', auth()->user()->branch_id);
            }

            // Branch handling
            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    // Filtered branch (for super admin)
                    $invoiceQuery->where('branch_id', $filterBranchId);

                    $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                    $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                    $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'DESC')->get();
                    $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('created_at', 'DESC')->get();
                }
            } else {
                // Normal branch user
                $invoiceQuery->where('branch_id', $userBranchId);

                $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
                $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
                $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'DESC')->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->orderBy('created_at', 'DESC')->get();
            }

            // --- SERVICE FILTER SECTION ---
            $invoiceQuery->whereHas('invoiceItems', function ($q) use ($request) {
                // Only include items that are service products
                $q->whereHas('product', function ($p) use ($request) {
                    $p->where('is_service', 0);

                    // Optional: category filter
                    if (!empty($request->category_id)) {
                        $p->where('category_id', $request->category_id);
                    }
                });

                // Optional: product filter
                if (!empty($request->product_id)) {
                    $q->where('product_id', $request->product_id);
                }
            });

            // Get filtered data
            $data['invoiceItem'] = Invoice::filterByFakeSale($invoiceQuery);
        }

        return view('backend.pages.report.product_report', $data);
    }


    public function purchase(Request $request)
    {
        $data['products'] = Product::orderBy('created_at', 'desc')->get();
        if ($request->all() != NULL) {
            // $data['oneProduct'] = Product::where('id', $request->product_id)->first();
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_date)->toDateString();
            $sdate = $data['sdate'];
            $edate = $data['edate'];

            $userBranchId = auth()->user()->branch_id;
            $filterBranchId = session('branch_filter_id', null);

            if ($userBranchId == 1) {
                if ($filterBranchId) {
                    $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                    $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'DESC')->get();
                    $purchaseItem = PurchaseItem::with('purchase', 'product')->where('branch_id', $filterBranchId)
                        ->whereHas('purchase', function ($query) use ($sdate, $edate) {
                            $query->whereBetween('date', [$sdate, $edate]);
                        });
                    if ($request->product_id != NULL) {
                        $purchaseItem = $purchaseItem->where('product_id', $request->product_id);
                    }
                } else {
                    $data['products'] = Product::orderBy('created_at', 'desc')->get();
                    $purchaseItem = PurchaseItem::with('purchase', 'product')
                        ->whereHas('purchase', function ($query) use ($sdate, $edate) {
                            $query->whereBetween('date', [$sdate, $edate]);
                        });
                    if ($request->product_id != NULL) {
                        $purchaseItem = $purchaseItem->where('product_id', $request->product_id);
                    }
                }
            } else {
                $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
                $data['products'] = Product::whereIn('id', $productIds)->orderBy('created_at', 'DESC')->get();
                $purchaseItem = PurchaseItem::with('purchase', 'product')->where('branch_id', $userBranchId)
                    ->whereHas('purchase', function ($query) use ($sdate, $edate) {
                        $query->whereBetween('date', [$sdate, $edate]);
                    });
                if ($request->product_id != NULL) {
                    $purchaseItem = $purchaseItem->where('product_id', $request->product_id);
                }
            }

            $data['purchaseItem'] = $purchaseItem->get();
        }
        return view('backend.pages.report.purchase', $data);
    }

    public function profitLoss(Request $request)
    {
        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_month)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_month)->toDateString();
            $dateRange = CarbonPeriod::create($data['sdate'], $data['edate']);

            $data['groupedDates'] = [];
            foreach ($dateRange as $date) {
                $data['groupedDates'][$date->format('Y-m')][] = $date->toDateString();
            }
            return view('backend.pages.report.profit_loss', $data);
        } else {
            return view('backend.pages.report.profit_loss');
        }
    }

    public function low_stock(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['brand_id'] = $request->brand_id;

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);

        $query = Product::with(['unit.related_unit', 'category', 'brand'])->where('is_service', 0);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $brandIds = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->get();
                $data['brands'] = Brand::whereIn('id', $brandIds)->get();
                $data['categories'] = Category::whereIn('id', $categoryIds)->get();
                $query->whereIn('id', $productIds);
            } else {
                $data['produc'] = Product::where('is_service', 0)->get();
                $data['brands'] = Brand::get();
                $data['categories'] = Category::get();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $brandIds = BranchBrand::where('branch_id', $filterBranchId ?: $userBranchId)->pluck('brand_id');
            $categoryIds = BranchCategory::where('branch_id', $filterBranchId ?: $userBranchId)->pluck('category_id');
            $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->get();
            $data['brands'] = Brand::whereIn('id', $brandIds)->get();
            $data['categories'] = Category::whereIn('id', $categoryIds)->get();
            $query->whereIn('id', $productIds);
        }

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }

        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id != null) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->search_keyword != null) {
            $keyword = $request->search_keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('barcode', 'like', '%' . $keyword . '%');
            });
        }

        $data['products'] = $query->orderBy('main_qty', 'asc')->get();

        return view('backend.pages.report.low_stock_report', $data);
    }

    public function user_sell(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $data['users'] = User::where('branch_id', $filterBranchId)->orderBy('created_at', 'desc')->get();
            } else {
                $data['users'] = User::orderBy('created_at', 'desc')->get();
            }
        } else {
            $data['users'] = User::where('branch_id', $userBranchId)->orderBy('created_at', 'desc')->get();
        }

        if ($request->all() != NULL) {
            $data['sdate'] = Carbon::createFromDate($request->start_date)->toDateString();
            $data['edate'] = Carbon::createFromDate($request->end_date)->toDateString();
            $sdate = $data['sdate'];
            $edate = $data['edate'];
            $data['oneUser'] = $request->user_id;

            $invoiceItem = Invoice::where('created_by', $request->user_id)->whereBetween('date', [$sdate, $edate]);

            $data['invoiceItem'] = Invoice::filterByFakeSale($invoiceItem->get());
        }
        return view('backend.pages.report.user_sell', $data);
    }

    public function usedStock(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['keyword'] = $request->search_keyword;
        $data['category_id'] = $request->category_id;
        $data['brand_id'] = $request->brand_id;
        //get all stock with supplier and user
        $data['products'] = UsedProduct::with('unit.related_unit')
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        $data['totalPrice'] = UsedProduct::all()->sum(function ($product) {
            // dd(product_stock_balance($product));
            return product_stock_balance($product) * $product->purchase_price;
        });

        if ($request->product_id != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('id', $request->product_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'product_id' => $request->product_id,
                ]);
        }

        if ($request->category_id != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('category_id', $request->category_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'category_id' => $request->category_id,
                ]);
        }

        if ($request->brand_id != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('brand_id', $request->brand_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'brand_id' => $request->brand_id,
                ]);
        }

        if ($request->search_keyword != null) {
            $data['products'] = UsedProduct::with('unit.related_unit')->where('name', 'like', '%' . $request->search_keyword . '%')
                ->orWhere('barcode', $request->search_keyword)
                ->orderBy('created_at', 'DESC')
                ->paginate(20)
                ->appends([
                    'search_keyword' => $request->search_keyword,
                ]);
        }
        return view('backend.pages.report.usedStock', $data);
    }

    public function inventoryRegister(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $branchId = ($userBranchId == 1) ? $filterBranchId : $userBranchId;

        // Default to current date if not provided
        $sdate = $request->start_date ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $edate = $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->toDateString();
        
        $group_by = $request->group_by ?? 'date_wise';
        $productId = $request->product_id;
        $categoryId = $request->category_id;
        $brandId = $request->brand_id;

        // Fetch filter dropdown options with minimal column footprint
        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $prods = Product::whereIn('id', BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id'));
                $cats = Category::whereIn('id', BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id'));
                $brnds = Brand::whereIn('id', BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id'));
            } else {
                $prods = Product::query();
                $cats = Category::query();
                $brnds = Brand::query();
            }
        } else {
            $prods = Product::whereIn('id', BranchProduct::where('branch_id', $userBranchId)->pluck('product_id'));
            $cats = Category::whereIn('id', BranchCategory::where('branch_id', $userBranchId)->pluck('category_id'));
            $brnds = Brand::whereIn('id', BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id'));
        }

        $data['allProducts'] = $prods->where('is_service', 0)->select('id', 'name')->orderBy('name', 'asc')->get();
        $data['categories'] = $cats->select('id', 'name')->orderBy('name', 'asc')->get();
        $data['brands'] = $brnds->select('id', 'name')->orderBy('name', 'asc')->get();

        // Build product query for report rows with eager loading of unit
        $productQuery = Product::with('unit:id,name')->select('id', 'name', 'purchase_price', 'unit_id')->where('is_service', 0);
        if ($productId) {
            $productQuery->where('id', $productId);
        }
        if ($categoryId) {
            $productQuery->where('category_id', $categoryId);
        }
        if ($brandId) {
            $productQuery->where('brand_id', $brandId);
        }
        if ($branchId) {
            $productQuery->whereIn('id', BranchProduct::where('branch_id', $branchId)->pluck('product_id'));
        }
        $reportProducts = $productQuery->get();
        $reportProductIds = $reportProducts->pluck('id')->toArray();

        $rows = [];

        if (!empty($reportProductIds)) {
            // 1. Bulk Opening Balances prior to $sdate (Eager Grouped Aggregation)
            $purchasedBefore = PurchaseItem::where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $transfersInBefore = TransferItem::where('status', 1)->where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('to_branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $returnsBefore = ReturnItem::where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $adjustInBefore = \App\Models\AdjustStockItem::where('stock_status', 1)->where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();

            $salesBefore = InvoiceItem::where('is_return', 0)->where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $transfersOutBefore = TransferItem::where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('from_branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $purReturnsBefore = \App\Models\ReturnPurchaseItem::where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $damagesBefore = DamageItem::where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
            $adjustOutBefore = \App\Models\AdjustStockItem::where('stock_status', 0)->where('date', '<', $sdate)->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();

            // Precompute initial opening running quantity for each product
            $runningQty = [];
            foreach ($reportProducts as $product) {
                $pId = $product->id;
                $pInBefore = ($purchasedBefore[$pId] ?? 0) + ($transfersInBefore[$pId] ?? 0) + ($returnsBefore[$pId] ?? 0) + ($adjustInBefore[$pId] ?? 0);
                $pOutBefore = ($salesBefore[$pId] ?? 0) + ($transfersOutBefore[$pId] ?? 0) + ($purReturnsBefore[$pId] ?? 0) + ($damagesBefore[$pId] ?? 0) + ($adjustOutBefore[$pId] ?? 0);
                $runningQty[$pId] = $pInBefore - $pOutBefore;
            }

            if ($group_by === 'date_wise') {
                // Bulk Period Transactions grouped by date & product_id
                $mapDatePeriod = function($query, $hasVal = false) {
                    $res = [];
                    $activeIds = [];
                    foreach ($query->get() as $item) {
                        $res[$item->date][$item->product_id] = [
                            'qty' => (float)$item->qty,
                            'val' => $hasVal ? (float)$item->val : 0,
                        ];
                        $activeIds[$item->product_id] = true;
                    }
                    return [$res, $activeIds];
                };

                list($purchasesMap, $act1) = $mapDatePeriod(PurchaseItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('date', 'product_id'), true);
                list($purReturnsMap, $act2) = $mapDatePeriod(\App\Models\ReturnPurchaseItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('date', 'product_id'), true);
                list($damagesMap, $act3) = $mapDatePeriod(DamageItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('date', 'product_id'), true);
                list($transfersOutMap, $act4) = $mapDatePeriod(TransferItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('from_branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty')->groupBy('date', 'product_id'));
                list($transfersInMap, $act5) = $mapDatePeriod(TransferItem::where('status', 1)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('to_branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty')->groupBy('date', 'product_id'));
                list($salesOutMap, $act6) = $mapDatePeriod(InvoiceItem::where('is_return', 0)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('date', 'product_id'), true);
                list($salesReturnMap, $act7) = $mapDatePeriod(ReturnItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('date', 'product_id'), true);
                list($adjustInMap, $act8) = $mapDatePeriod(\App\Models\AdjustStockItem::where('stock_status', 1)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty')->groupBy('date', 'product_id'));
                list($adjustOutMap, $act9) = $mapDatePeriod(\App\Models\AdjustStockItem::where('stock_status', 0)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('date, product_id, SUM(main_qty) as qty')->groupBy('date', 'product_id'));

                $activeProductMap = $act1 + $act2 + $act3 + $act4 + $act5 + $act6 + $act7 + $act8 + $act9;
                $targetProductIds = $productId ? [$productId] : array_keys($activeProductMap);
                $reportProductsKeyed = $reportProducts->keyBy('id');

                // Generate all dates in range
                $period = new \DatePeriod(
                    new \DateTime($sdate),
                    new \DateInterval('P1D'),
                    (new \DateTime($edate))->modify('+1 day')
                );

                foreach ($period as $dateObj) {
                    $currentDate = $dateObj->format('Y-m-d');

                    foreach ($targetProductIds as $pId) {
                        $product = $reportProductsKeyed[$pId] ?? null;
                        if (!$product) continue;

                        $pPurchase = $purchasesMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pPurReturn = $purReturnsMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pDamage = $damagesMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pTransferOut = $transfersOutMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pTransferIn = $transfersInMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pSalesOut = $salesOutMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pSalesReturn = $salesReturnMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pAdjustIn = $adjustInMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];
                        $pAdjustOut = $adjustOutMap[$currentDate][$pId] ?? ['qty' => 0, 'val' => 0];

                        $hasTransactions = ($pPurchase['qty'] != 0) || ($pPurReturn['qty'] != 0) || ($pDamage['qty'] != 0) ||
                            ($pTransferOut['qty'] != 0) || ($pTransferIn['qty'] != 0) || ($pSalesOut['qty'] != 0) ||
                            ($pSalesReturn['qty'] != 0) || ($pAdjustIn['qty'] != 0) || ($pAdjustOut['qty'] != 0);

                        $opQty = $runningQty[$pId] ?? 0;
                        $price = (float)$product->purchase_price;
                        $opVal = $opQty * $price;

                        $avlQty = $opQty + $pPurchase['qty'] + $pAdjustIn['qty'] - $pDamage['qty'] - $pTransferOut['qty'] - $pAdjustOut['qty'];
                        $avlVal = $avlQty * $price;

                        $endQty = $opQty + $pPurchase['qty'] + $pTransferIn['qty'] + $pAdjustIn['qty'] - $pTransferOut['qty'] - $pSalesOut['qty'] + $pSalesReturn['qty'] - $pPurReturn['qty'] - $pDamage['qty'] - $pAdjustOut['qty'];
                        $endVal = $endQty * $price;

                        // Advance running balance for the next day
                        $runningQty[$pId] = $endQty;

                        if (!$hasTransactions && $productId == null) {
                            continue;
                        }

                        $rows[] = [
                            'date' => $currentDate,
                            'product' => $product,
                            'product_name' => $product->name,
                            'opening_qty' => $opQty,
                            'opening_value' => $opVal,
                            'purchase_qty' => $pPurchase['qty'],
                            'purchase_val' => $pPurchase['val'],
                            'purchase_return_qty' => $pPurReturn['qty'],
                            'purchase_return_val' => $pPurReturn['val'],
                            'damage_qty' => $pDamage['qty'],
                            'damage_val' => $pDamage['val'],
                            'available_qty' => $avlQty,
                            'available_value' => $avlVal,
                            'transfer_out_qty' => $pTransferOut['qty'],
                            'transfer_in_qty' => $pTransferIn['qty'],
                            'sales_out_qty' => $pSalesOut['qty'],
                            'sales_out_val' => $pSalesOut['val'],
                            'sales_return_qty' => $pSalesReturn['qty'],
                            'sales_return_val' => $pSalesReturn['val'],
                            'adjust_in_qty' => $pAdjustIn['qty'],
                            'adjust_out_qty' => $pAdjustOut['qty'],
                            'ending_qty' => $endQty,
                            'ending_value' => $endVal,
                        ];
                    }
                }
            } else {
                // Product-wise bulk period aggregation
                $periodPurchases = PurchaseItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('product_id')->get()->keyBy('product_id');
                $periodPurReturns = \App\Models\ReturnPurchaseItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('product_id')->get()->keyBy('product_id');
                $periodDamages = DamageItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('product_id')->get()->keyBy('product_id');
                $periodTransfersOut = TransferItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('from_branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
                $periodTransfersIn = TransferItem::where('status', 1)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('to_branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
                $periodSalesOut = InvoiceItem::where('is_return', 0)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('product_id')->get()->keyBy('product_id');
                $periodSalesReturn = ReturnItem::whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty, SUM(subtotal) as val')->groupBy('product_id')->get()->keyBy('product_id');
                $periodAdjustIn = \App\Models\AdjustStockItem::where('stock_status', 1)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();
                $periodAdjustOut = \App\Models\AdjustStockItem::where('stock_status', 0)->whereBetween('date', [$sdate, $edate])->whereIn('product_id', $reportProductIds)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->selectRaw('product_id, SUM(main_qty) as qty')->groupBy('product_id')->pluck('qty', 'product_id')->toArray();

                foreach ($reportProducts as $product) {
                    $pId = $product->id;
                    $opQty = $runningQty[$pId];
                    $opVal = $opQty * $product->purchase_price;

                    $pPurchaseQty = (float)($periodPurchases[$pId]->qty ?? 0);
                    $pPurchaseVal = (float)($periodPurchases[$pId]->val ?? 0);
                    $pPurReturnQty = (float)($periodPurReturns[$pId]->qty ?? 0);
                    $pPurReturnVal = (float)($periodPurReturns[$pId]->val ?? 0);
                    $pDamageQty = (float)($periodDamages[$pId]->qty ?? 0);
                    $pDamageVal = (float)($periodDamages[$pId]->val ?? 0);
                    $pTransferOutQty = (float)($periodTransfersOut[$pId] ?? 0);
                    $pTransferInQty = (float)($periodTransfersIn[$pId] ?? 0);
                    $pSalesOutQty = (float)($periodSalesOut[$pId]->qty ?? 0);
                    $pSalesOutVal = (float)($periodSalesOut[$pId]->val ?? 0);
                    $pSalesReturnQty = (float)($periodSalesReturn[$pId]->qty ?? 0);
                    $pSalesReturnVal = (float)($periodSalesReturn[$pId]->val ?? 0);
                    $pAdjustInQty = (float)($periodAdjustIn[$pId] ?? 0);
                    $pAdjustOutQty = (float)($periodAdjustOut[$pId] ?? 0);

                    $avlQty = $opQty + $pPurchaseQty + $pAdjustInQty - $pDamageQty - $pTransferOutQty - $pAdjustOutQty;
                    $avlVal = $avlQty * $product->purchase_price;

                    $endQty = $opQty + $pPurchaseQty + $pTransferInQty + $pAdjustInQty - $pTransferOutQty - $pSalesOutQty + $pSalesReturnQty - $pPurReturnQty - $pDamageQty - $pAdjustOutQty;
                    $endVal = $endQty * $product->purchase_price;

                    $rows[] = [
                        'product' => $product,
                        'product_name' => $product->name,
                        'opening_qty' => $opQty,
                        'opening_value' => $opVal,
                        'purchase_qty' => $pPurchaseQty,
                        'purchase_val' => $pPurchaseVal,
                        'purchase_return_qty' => $pPurReturnQty,
                        'purchase_return_val' => $pPurReturnVal,
                        'damage_qty' => $pDamageQty,
                        'damage_val' => $pDamageVal,
                        'available_qty' => $avlQty,
                        'available_value' => $avlVal,
                        'transfer_out_qty' => $pTransferOutQty,
                        'transfer_in_qty' => $pTransferInQty,
                        'sales_out_qty' => $pSalesOutQty,
                        'sales_out_val' => $pSalesOutVal,
                        'sales_return_qty' => $pSalesReturnQty,
                        'sales_return_val' => $pSalesReturnVal,
                        'adjust_in_qty' => $pAdjustInQty,
                        'adjust_out_qty' => $pAdjustOutQty,
                        'ending_qty' => $endQty,
                        'ending_value' => $endVal,
                    ];
                }
            }
        }

        $data['rows'] = $rows;
        $data['sdate'] = $sdate;
        $data['edate'] = $edate;
        $data['group_by'] = $group_by;
        $data['product_id'] = $productId;
        $data['category_id'] = $categoryId;
        $data['brand_id'] = $brandId;

        return view('backend.pages.report.inventory_register', $data);
    }

    public function woocommerceReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $keyword = $request->input('search');

        $invQuery = Invoice::query()->where(function($q) {
            $q->where('sale_type', 'WooCommerce')
              ->orWhereNotNull('woocommerce_order_id')
              ->orWhere('unique_id', 'like', 'WC-%');
        });

        if ($startDate) {
            $invQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $invQuery->whereDate('created_at', '<=', $endDate);
        }

        $allWooInvoices = $invQuery->with(['invoiceItems.product'])->get();
        $productStats = [];

        foreach ($allWooInvoices as $invoice) {
            $isUntransferred = ($invoice->sale_type === 'WooCommerce');
            $stockMode = $invoice->stock_mode ?? 'without_manage';

            foreach ($invoice->invoiceItems as $item) {
                if (!$item->product_id || !$item->product) continue;

                $pid = $item->product_id;
                if (!isset($productStats[$pid])) {
                    $product = $item->product;
                    $purchased  = (float) purchased_qty($product);
                    $invoiced   = (float) invoiced_qty($product);
                    $saleRet    = (float) returned_qty($product);
                    $purRet     = (float) return_pur_qty($product);
                    $damage     = (float) damaged_qty($product);
                    $adjustIn   = (float) adjust_in($product);
                    $adjustOut  = (float) adjust_out($product);

                    $currentStock = $purchased - $invoiced + $adjustIn + $saleRet - $adjustOut - $purRet - $damage;

                    $productStats[$pid] = [
                        'product' => $product,
                        'current_stock' => $currentStock,
                        'total_orders' => 0,
                        'total_qty' => 0,
                        'with_manage_qty' => 0,
                        'without_manage_qty' => 0,
                        'untransferred_qty' => 0,
                    ];
                }

                $qty = (float)($item->main_qty > 0 ? $item->main_qty : 1);
                $productStats[$pid]['total_orders']++;
                $productStats[$pid]['total_qty'] += $qty;

                if ($isUntransferred) {
                    $productStats[$pid]['untransferred_qty'] += $qty;
                } else {
                    if ($stockMode === 'with_manage') {
                        $productStats[$pid]['with_manage_qty'] += $qty;
                    } else {
                        $productStats[$pid]['without_manage_qty'] += $qty;
                    }
                }
            }
        }

        $allWooInvoiceIds = $allWooInvoices->pluck('id');
        $returnItemsMap = \App\Models\ReturnItem::whereIn('invoice_id', $allWooInvoiceIds)
            ->select('product_id', \DB::raw('SUM(main_qty) as total_return_qty'))
            ->groupBy('product_id')
            ->pluck('total_return_qty', 'product_id')
            ->toArray();

        foreach ($productStats as $pid => &$stat) {
            $stat['return_qty'] = (float)($returnItemsMap[$pid] ?? 0);
        }
        unset($stat);

        if (!empty($keyword)) {
            $productStats = array_filter($productStats, function($stat) use ($keyword) {
                $pname = mb_strtolower($stat['product']->name ?? '');
                $barcode = mb_strtolower($stat['product']->barcode ?? '');
                $search = mb_strtolower($keyword);
                return (str_contains($pname, $search) || str_contains($barcode, $search));
            });
        }

        $totalOrdersCount = count($allWooInvoices);
        $totalSoldQty = array_sum(array_column($productStats, 'total_qty'));
        $totalWithManageQty = array_sum(array_column($productStats, 'with_manage_qty'));
        $totalWithoutManageQty = array_sum(array_column($productStats, 'without_manage_qty'));
        $totalUntransferredQty = array_sum(array_column($productStats, 'untransferred_qty'));
        $totalReturnedQty = array_sum(array_column($productStats, 'return_qty'));

        return view('backend.pages.report.woocommerce', compact(
            'productStats',
            'totalOrdersCount',
            'totalSoldQty',
            'totalWithManageQty',
            'totalWithoutManageQty',
            'totalUntransferredQty',
            'totalReturnedQty',
            'startDate',
            'endDate',
            'keyword'
        ));
    }

    public function supplierReport(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', null);
        $branchId = ($userBranchId == 1 && $filterBranchId) ? $filterBranchId : $userBranchId;

        // Suppliers & products list for filter dropdowns
        if ($branchId) {
            $data['suppliers'] = Supplier::where('branch_id', $branchId)->orderBy('name', 'asc')->get();
            $data['products'] = Product::whereIn('id', BranchProduct::where('branch_id', $branchId)->pluck('product_id'))
                ->where('is_service', 0)
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $data['suppliers'] = Supplier::orderBy('name', 'asc')->get();
            $data['products'] = Product::where('is_service', 0)->orderBy('name', 'asc')->get();
        }

        $data['supplier_id'] = $request->supplier_id;
        $data['product_id'] = $request->product_id;
        $data['sdate'] = $request->filled('start_date') ? Carbon::parse($request->start_date)->toDateString() : null;
        $data['edate'] = $request->filled('end_date') ? Carbon::parse($request->end_date)->toDateString() : null;

        $data['supplier'] = null;
        $data['purchases'] = collect();
        $data['purchaseItems'] = collect();
        $data['productSummary'] = collect();
        $data['returnPurchases'] = collect();
        $data['returnItems'] = collect();
        $data['transactions'] = collect();
        $data['allSuppliersSummary'] = collect();

        $sdate = $data['sdate'];
        $edate = $data['edate'];
        $supplierId = $data['supplier_id'];
        $productId = $data['product_id'];

        if ($request->filled('supplier_id')) {
            $supplier = Supplier::with('branch')->find($supplierId);
            $data['supplier'] = $supplier;

            if ($supplier) {
                // 1. Purchase Invoices
                $purchaseQuery = Purchase::with([
                    'purchaseItems.product.unit.related_unit',
                    'purchaseItems.product_variation',
                    'purchaseItems.size',
                    'purchaseItems.color',
                    'user',
                    'branch'
                ])
                ->where('supplier_id', $supplierId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->when($sdate && $edate, fn($q) => $q->whereBetween('date', [$sdate, $edate]))
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc');

                $data['purchases'] = $purchaseQuery->get();

                // 2. Product-wise Purchase Items (A to Z detailed breakdown)
                $purchaseItemQuery = PurchaseItem::with([
                    'purchase',
                    'product.unit.related_unit',
                    'product.category',
                    'product.brand',
                    'product_variation',
                    'size',
                    'color'
                ])
                ->whereHas('purchase', function ($q) use ($supplierId, $branchId, $sdate, $edate) {
                    $q->where('supplier_id', $supplierId)
                      ->when($branchId, fn($sq) => $sq->where('branch_id', $branchId))
                      ->when($sdate && $edate, fn($sq) => $sq->whereBetween('date', [$sdate, $edate]));
                })
                ->when($productId, fn($q) => $q->where('product_id', $productId))
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc');

                $data['purchaseItems'] = $purchaseItemQuery->get();

                // 3. Product aggregated summary (Grouped by product)
                $data['productSummary'] = $data['purchaseItems']->groupBy('product_id')->map(function ($items) {
                    $firstItem = $items->first();
                    $prod = $firstItem->product;
                    $totalMainQty = $items->sum('main_qty');
                    $totalSubQty = $items->sum('sub_qty');
                    $totalSubtotal = $items->sum('subtotal');
                    $avgRate = $totalMainQty > 0 ? ($totalSubtotal / $totalMainQty) : ($firstItem->rate ?? 0);

                    // Supplier-specific Present Stock Calculations (only from this supplier's purchase items)
                    $supplierStockQty = (float) $items->sum('stock_qty');
                    $supplierStockAmount = (float) $items->sum(function($it) {
                        return (float)$it->stock_qty * (float)$it->rate;
                    });
                    $stockText = $supplierStockQty . ' ' . ($prod?->unit?->name ?? 'pcs');

                    return (object)[
                        'product' => $prod,
                        'total_main_qty' => $totalMainQty,
                        'total_sub_qty' => $totalSubQty,
                        'avg_rate' => $avgRate,
                        'total_amount' => $totalSubtotal,
                        'order_count' => $items->count(),
                        'last_purchase_date' => $items->max('date'),
                        'present_stock_qty' => $supplierStockQty,
                        'present_stock_text' => $stockText,
                        'unit_price' => $avgRate,
                        'present_stock_amount' => $supplierStockAmount,
                    ];
                })->values();

                $data['total_present_stock_qty'] = $data['productSummary']->sum('present_stock_qty');
                $data['total_present_stock_amount'] = $data['productSummary']->sum('present_stock_amount');

                // 4. Return Purchases & Return Items
                $returnPurchaseQuery = ReturnPurchase::with(['returnPurchaseItems.product.unit', 'user', 'branch'])
                    ->where('supplier_id', $supplierId)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($sdate && $edate, fn($q) => $q->whereBetween('date', [$sdate, $edate]))
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc');
                $data['returnPurchases'] = $returnPurchaseQuery->get();

                $returnItemQuery = ReturnPurchaseItem::with(['return', 'product.unit', 'size', 'color'])
                    ->whereHas('return', function ($q) use ($supplierId, $branchId, $sdate, $edate) {
                        $q->where('supplier_id', $supplierId)
                          ->when($branchId, fn($sq) => $sq->where('branch_id', $branchId))
                          ->when($sdate && $edate, fn($sq) => $q->whereBetween('date', [$sdate, $edate]));
                    })
                    ->when($productId, fn($q) => $q->where('product_id', $productId))
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc');
                $data['returnItems'] = $returnItemQuery->get();

                // 5. Transactions / Financial Ledger
                $transQuery = Transaction::with(['purchase'])
                    ->where('supplier_id', $supplierId)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($sdate && $edate, fn($q) => $q->whereBetween('date', [$sdate, $edate]))
                    ->orderBy('date', 'asc')
                    ->orderBy('id', 'asc');
                $data['transactions'] = $transQuery->get();

                // Calculate Opening Balance before start date
                if ($sdate) {
                    $beforeTrans = Transaction::where('supplier_id', $supplierId)
                        ->where('date', '<', $sdate)
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                        ->get();
                    $data['previous_balance'] = $beforeTrans->sum('debit') - $beforeTrans->sum('credit');
                } else {
                    $data['previous_balance'] = 0;
                }

                // Summary Totals
                $data['total_purchase_amount'] = $data['purchases']->sum('total_amount');
                $data['total_paid'] = $data['purchases']->sum('total_paid');
                $data['total_due'] = $data['purchases']->sum('total_due');
                $data['total_return_amount'] = $data['returnPurchases']->sum('total_return');
                $data['total_items_qty'] = $data['purchaseItems']->sum('main_qty');

                $data['opening_due'] = $supplier->due_amount ?? 0;
                $data['opening_advance'] = $supplier->advance_amount ?? 0;
                $openBalance = open_balance_supplier($supplier->id, $supplier->due_amount, $supplier->advance_amount);
                $data['open_balance'] = $openBalance;
                $data['grand_due'] = ($data['total_purchase_amount'] - $data['total_paid']) + $openBalance - $data['total_return_amount'];
            }
        } else {
            // Overview for all suppliers
            $suppliersList = $data['suppliers'];
            $data['allSuppliersSummary'] = $suppliersList->map(function ($sup) use ($branchId, $sdate, $edate) {
                $pQuery = Purchase::where('supplier_id', $sup->id)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($sdate && $edate, fn($q) => $q->whereBetween('date', [$sdate, $edate]));

                $totalPurchases = $pQuery->sum('total_amount');
                $totalPaid = $pQuery->sum('total_paid');
                $totalDue = $pQuery->sum('total_due');
                $purchaseCount = $pQuery->count();

                $totalItemsQty = PurchaseItem::whereHas('purchase', function ($q) use ($sup, $branchId, $sdate, $edate) {
                    $q->where('supplier_id', $sup->id)
                      ->when($branchId, fn($sq) => $sq->where('branch_id', $branchId))
                      ->when($sdate && $edate, fn($sq) => $sq->whereBetween('date', [$sdate, $edate]));
                })->sum('main_qty');

                $openBal = open_balance_supplier($sup->id, $sup->due_amount, $sup->advance_amount);
                $returnAmt = ReturnPurchase::where('supplier_id', $sup->id)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->when($sdate && $edate, fn($q) => $q->whereBetween('date', [$sdate, $edate]))
                    ->sum('total_return');

                return (object)[
                    'supplier' => $sup,
                    'purchase_count' => $purchaseCount,
                    'total_items_qty' => $totalItemsQty,
                    'total_amount' => $totalPurchases,
                    'total_paid' => $totalPaid,
                    'total_due' => $totalDue,
                    'open_balance' => $openBal,
                    'return_amount' => $returnAmt,
                    'net_due' => ($totalPurchases - $totalPaid) + $openBal - $returnAmt,
                ];
            });
        }

        return view('backend.pages.report.supplier_report', $data);
    }
}
