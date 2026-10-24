<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\OthersController;
use App\Http\Controllers\PathaoController;
use App\Http\Controllers\OwnershipController;
use App\Http\Controllers\Backend\PDFController;
use App\Http\Controllers\Backend\SmsController;
use App\Http\Controllers\Backend\SizeController;
use App\Http\Controllers\Backend\UnitController;
use App\Http\Controllers\Backend\UsedController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\PlatformController;
use App\Http\Controllers\Backend\MarketingController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\DamageController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\Backend\AssetController;
use App\Http\Controllers\Backend\InvoiceController;
use App\Http\Controllers\Backend\QuotationController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\ServiceReceiveController;
use App\Http\Controllers\Backend\ServiceInvoiceController;
use App\Http\Controllers\Backend\ServiceCenterController;
use App\Http\Controllers\Backend\WarrantyClaimController;
use App\Http\Controllers\Backend\WarrantyDeliveryController;
use App\Http\Controllers\Backend\WarrantyController;
use App\Http\Controllers\Backend\SupplierClaimController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DiscountGroupController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\VariationController;
use App\Http\Controllers\Backend\ReturnSaleController;
use App\Http\Controllers\Backend\BankAccountController;
use App\Http\Controllers\Backend\StockAdjustController;
use App\Http\Controllers\Backend\StockAuditController;
use App\Http\Controllers\Backend\UsedProductController;
use App\Http\Controllers\Backend\UserProfileController;
use App\Http\Controllers\Backend\StockTransferController;
use App\Http\Controllers\Backend\ReturnPurchaseController;
use App\Http\Controllers\Backend\BankTransactionController;
use App\Http\Controllers\Backend\RolesPermissionController;
use App\Http\Controllers\Backend\ActivityLogController;
use App\Http\Controllers\Backend\UsedProductPurchaseController;
use App\Http\Controllers\Backend\AIStockAuditorController;
use App\Http\Controllers\Backend\AIChatbotController;
use App\Http\Controllers\Backend\PayrollController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\LeaveTypeController;
use App\Http\Controllers\Backend\LeaveApplicationController;
use App\Http\Controllers\Backend\AttendanceController;
use App\Http\Controllers\Backend\SuperAdminSettingsController;
use App\Http\Controllers\Backend\PreOrderController;
use App\Http\Controllers\Backend\WooCommerceController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\WebsiteOrderController;



use App\Http\Controllers\FrontendController;

// Frontend Routes
Route::get('/', [FrontendController::class, 'index'])->name('frontend');
Route::get('/home', [FrontendController::class, 'index'])->name('home');
Route::get('/catalog', [FrontendController::class, 'allCategories'])->name('frontend.categories');
Route::get('/categories', [FrontendController::class, 'allCategories']);
Route::get('/catalog/{slug}', [FrontendController::class, 'category'])->name('frontend.category');
Route::get('/category/{slug}', [FrontendController::class, 'category']);
Route::get('/categories/{slug}', [FrontendController::class, 'category']);
Route::get('/catalog/{category_slug}/{subcategory_slug}', [FrontendController::class, 'subcategory'])->name('frontend.subcategory');
Route::get('/product-details/{slug}', [FrontendController::class, 'productDetails'])->name('frontend.product.details');
Route::get('/product-details-info/{slug}', [FrontendController::class, 'productDetails'])->name('frontend.product-details');
Route::get('/product/{slug}', [FrontendController::class, 'productDetails'])->where('slug', '^(?!(edit|create|search|store|destroy|multiple|update|toggle-|bulk-|save-)).*$');
Route::get('/search', [FrontendController::class, 'search'])->name('frontend.search');
Route::get('/search/live', [FrontendController::class, 'liveSearch'])->name('frontend.search.live');
Route::get('/api/search/live', [FrontendController::class, 'liveSearch'])->name('frontend.api.search.live');
Route::get('/cart', [FrontendController::class, 'cart'])->name('frontend.cart');
Route::get('/checkout', [FrontendController::class, 'checkout'])->name('frontend.checkout');
Route::post('/checkout', [FrontendController::class, 'processCheckout'])->name('frontend.process-checkout');
Route::get('/order-confirmation/{order_number?}', [FrontendController::class, 'orderConfirmation'])->name('frontend.order-confirmation');
Route::get('/wishlist', [FrontendController::class, 'wishlist'])->name('frontend.wishlist');
Route::post('/api/meta-capi/track', [FrontendController::class, 'trackCapiEvent'])->name('meta-capi.track');

// Legal, Policies & Sitemap Routes
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('frontend.privacy-policy');
Route::get('/terms-of-service', [FrontendController::class, 'termsOfService'])->name('frontend.terms-of-service');
Route::get('/terms', [FrontendController::class, 'termsOfService']);
Route::get('/warranty-terms', [FrontendController::class, 'warrantyTerms'])->name('frontend.warranty-terms');
Route::get('/warranty', [FrontendController::class, 'warrantyTerms']);
Route::get('/sitemap', [FrontendController::class, 'siteMap'])->name('frontend.sitemap');
Route::get('/site-map', [FrontendController::class, 'siteMap']);

// CLear
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return view('auth.login');
});

// OTP Verification Routes
Route::get('/verify-otp', [\App\Http\Controllers\Backend\OtpVerificationController::class, 'show'])->name('otp.verify');
Route::post('/verify-otp', [\App\Http\Controllers\Backend\OtpVerificationController::class, 'verify']);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Branch Selection Routes
    Route::get('/select-branch', [App\Http\Controllers\Backend\BranchSelectionController::class, 'index'])->name('branch.select');
    Route::post('/set-branch',  [App\Http\Controllers\Backend\BranchSelectionController::class, 'select'])->name('branch.set');
});

// ইনভয়েস বারকোড জেনারেট করার জন্য আলাদা রাউট
Route::get('/invoice-barcode/{code}', function ($code) {
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
    return response($barcode)->header('Content-Type', 'image/png');
})->name('invoice.barcode');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'permission', 'branch.check'])->group(function () {
    // Route::controller(DashboardController::class)->group(function (){

    //     Route::get('/dashboard', 'index')->name('dashboard');
    //     Route::get('/dashboard/today', 'today')->name('dashboard.dashboard');
    // });
    Route::controller(DashboardController::class)->group(function () {

        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/dashboard/today', 'today')->name('dashboard.dashboard');

        Route::get('/dashboard/filter', 'filterData')->name('dashboard.filter');
        Route::get('/dashboard/chart/order-counts', 'getOrderCountsChart')->name('dashboard.chart.order-counts');
        Route::get('/dashboard/chart/earning-reports', 'getEarningReportsChart')->name('dashboard.chart.earning-reports');
        Route::get('/dashboard/chart/hourly-orders', 'getHourlyOrdersChart')->name('dashboard.chart.hourly-orders');
        Route::get('/dashboard/chart/manual-sales', 'getManualSalesChart')->name('dashboard.chart.manual-sales');
        Route::get('/dashboard/chart/online-platforms', 'getOnlinePlatformsChart')->name('dashboard.chart.online-platforms');
        Route::get('/dashboard/chart/web-order-report', 'getWebOrderReportChart')->name('dashboard.chart.web-order-report');
        Route::get('/dashboard/chart/orders-by-source', 'getOrdersBySourceChart')->name('dashboard.chart.orders-by-source');
        Route::get('/dashboard/product-sales-summary', 'productSalesSummary')->name('dashboard.product-sales-summary');
        Route::get('/dashboard/product-sales-summary/print', 'printProductSalesSummary')->name('dashboard.product-sales-summary.print');
        Route::get('/dashboard/product-sales-summary/pdf', 'downloadProductSalesSummaryPDF')->name('dashboard.product-sales-summary.pdf');

    });
    Route::post('/download-note', [DashboardController::class, 'downloadPDF'])->name('note.download');

    // --------------------- POS ---------------------
    Route::controller(InvoiceController::class)->group(function () {
        Route::post('/invoice/log/store',  'storeInvoiceLog')->name('storeInvoiceLog');
        Route::get('/invoice/pay/{id}', 'invoicePay')->name('invoice.pay');
        Route::get('/invoice/eedit/{id}', 'invoiceEdit')->name('inv.edit');
        Route::post('/invoice/update/{id}', 'invoiceUpdate')->name('invoice.up');
        Route::get('/invoice/exchange/{id}', 'invoiceExchange')->name('invoice.exchange');
        Route::get('/print/invoice/{id}', 'printInvoice')->name('invoice.print');
        Route::match(['get', 'post'], '/print/invoice-bulk', 'bulkPrintInvoice')->name('invoice.bulk-print');
        Route::get('/print/invoice/due/{id}', 'dueInvoicePrint')->name('due.invoice.print');
        Route::post('/invoice/return/{id}', 'returnInvoice')->name('invoice.return');
        Route::post('/invoice/return/amount/{id}', 'returnAmount')->name('invoice.return.amount');
        Route::post('/invoice/exchange/update', 'updateExchangeInvoice')->name('invoice.ex.update');
        Route::get('/invoice/online/sale', 'onlineSale')->name('invoice.online.sale');
        Route::post('/invoice/online/clear-selected-dues', 'clearSelectedDues')->name('invoice.online.clear-selected-dues');
        Route::post('/invoice/online/send-courier', 'sendToCourier')->name('invoice.online.send-courier');
        Route::post('/invoice/online/update-status/{id?}', 'updateOrderStatus')->name('invoice.online.update-status');
        Route::post('/invoice/online/bulk-status', 'updateOrderStatus')->name('invoice.online.bulk-status');
        Route::post('/invoice/online/sync-courier', 'syncCourierStatus')->name('invoice.online.sync-courier');
        Route::get('/invoice/scan-to-update', 'scanToUpdate')->name('invoice.scan.update');
        Route::post('/invoice/scan-to-update/process', 'processScanToUpdate')->name('invoice.scan.process');
        Route::get('/invoice/scan-to-update/search', 'searchInvoiceForScan')->name('invoice.scan.search');
        Route::get('/invoice/offline-data', 'getOfflineData')->name('invoice.offline-data');
        // Route::get('/invoice/due/print/{id}', 'dueInvoicePrint')->name('due.invoice.print');

    });
    Route::resource('invoice', InvoiceController::class);

    // --------------------> Pre-Order System (APP_ONLINE == 'yes') <--------------------
    Route::controller(PreOrderController::class)->group(function () {
        Route::get('/pre-orders', 'index')->name('pre-orders.index');
        Route::get('/pre-order/show/{id}', 'show')->name('pre-orders.show');
        Route::post('/pre-order/store', 'store')->name('pre-orders.store');
        Route::get('/pre-order/edit/{id}', 'edit')->name('pre-orders.edit');
        Route::post('/pre-order/update/{id}', 'update')->name('pre-orders.update');
        Route::post('/pre-order/convert/{id}', 'convertToSale')->name('pre-orders.convert');
        Route::post('/pre-order/cancel/{id}', 'cancel')->name('pre-orders.cancel');
        Route::delete('/pre-order/delete/{id}', 'destroy')->name('pre-orders.destroy');
    });
    Route::get('/print/quotation/{id}', [QuotationController::class, 'printQuotation'])->name('quotation.print');
    Route::resource('quotation', QuotationController::class);

    // --------------------> Purchase <--------------------
    Route::controller(PurchaseController::class)->group(function () {
        Route::post('/purchase/log/store',  'storePurchaseLog')->name('storePurchaseLog');
        Route::get('/purchase/pay/{id}', 'purchasePay')->name('purchase.pay');
        Route::get('/purchase/edit/{id}', 'purchaseEdit')->name('purchase.edit');
        Route::post('/purchase/update/{id}', 'purchaseUpdate')->name('purchase.updat');
        Route::get('/print/purchase/{id}', 'printPurchase')->name('purchase.print');
        Route::get('/print/return/purchase/{id}', 'printReturnPurchase')->name('purchase.return.print');
        Route::get('/imei-print/purchase/{id}', 'imeiPrintPurchase')->name('purchase.imei-print');

        Route::get('/purchase/search', 'search')->name('purchase.search');
    });
    Route::resource('purchase', PurchaseController::class);
    Route::get('/get-purchase-no', [PurchaseController::class, 'getPurchaseNo'])->name('get.purchase.no');


    // ----------------->  used product section <----------------
    Route::resource('usedProduct', UsedProductController::class);
    Route::resource('usedPurchase', UsedProductPurchaseController::class);
    Route::resource('used', UsedController::class);

    // --------------------> customers & discount groups <--------------------
    Route::resource('customer', CustomerController::class)->except(['show', 'edit', 'create']);
    Route::resource('discount-group', DiscountGroupController::class);
    Route::get('/customer/upcoming-wishlist', [CustomerController::class, 'upcomingWishlist'])->name('customer.upcoming-wishlist');
    Route::get('/customer/previous-due', [CustomerController::class, 'getPreviousDue'])->name('customer.previous.due');
    Route::get('/get-suppliers-by-branch', [SupplierController::class, 'getSuppliersByBranch'])->name('get.suppliers.by.branch');
    Route::get('/get-customer-by-branch', [CustomerController::class, 'getCustomersByBranch'])->name('get.customers.by.branch');

    // --------------------> installments --------------------
    Route::controller(\App\Http\Controllers\Backend\InstallmentController::class)->group(function () {
        Route::get('/installments', 'index')->name('installments.index');
        Route::get('/installments/today-due', 'todayDue')->name('installments.today-due');
        Route::get('/installments/today-collection', 'todayCollection')->name('installments.today-collection');
        Route::get('/installments/overdue', 'overdueList')->name('installments.overdue');
        Route::get('/installments/completed', 'completed')->name('installments.completed');
        Route::get('/installments/{id}', 'show')->name('installments.show');
        Route::post('/installments/schedule/{id}/collect', 'collectPayment')->name('installments.collect');
    });


    // --------------------> branch <--------------------
    Route::resource('branch', BranchController::class);

    Route::post('/switch-branch', [BranchController::class, 'switch'])->name('switch.branch');

    // --------------------> transfer <--------------------
    Route::resource('transfer', StockTransferController::class);
    Route::resource('stock-adjust', StockAdjustController::class);

    // --------------------> Stock Audit <--------------------
    Route::controller(StockAuditController::class)->prefix('stock-audit')->name('stock-audit.')->group(function () {
        Route::get('/product-scan', 'productScan')->name('product-scan');
        Route::get('/load-category-products', 'loadCategoryProducts')->name('load-category-products');
        Route::get('/print/{id}', 'print')->name('print');
    });
    Route::resource('stock-audit', StockAuditController::class);

    Route::controller(StockTransferController::class)->group(function () {
        Route::post('transfer/receive/{id}', 'transferReceive')->name('transfer.receive');
        Route::post('transfer/cancel/{id}', 'transferCancel')->name('transfer.cancel');
        Route::get('transfer/product/print/{id}', 'transferPrint')->name('transfer.print');
    });
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('employee/salary/details/{id}', 'salaryDetails')->name('employee.salary.details');
        Route::post('employee/payment/{id}', 'PaymentStore')->name('employee.payment.store');
    });

    Route::get('/customer/points/{id}', function ($id) {
        $customer = Customer::find($id);
        return response()->json(['total_point' => $customer ? $customer->total_point : 0]);
    });

    // Customer Quick History for POS
    Route::get('/customer/quick-history/{id}', [CustomerController::class, 'quickHistory'])->name('customer_quick_history');
    Route::get('/customer/fraud-check/{identifier}', [\App\Http\Controllers\Backend\CustomerFraudCheckController::class, 'check'])->name('customer.fraud-check');

    // --------------------> Shop (Online POS Platforms) <--------------------
    Route::resource('shop', PlatformController::class);

    // --------------------> employee <--------------------
    Route::resource('employee', EmployeeController::class);

    // --------------------> Payroll Module <--------------------
    Route::group(['prefix' => 'payroll', 'as' => 'payroll.'], function () {
        // Department
        Route::resource('department', DepartmentController::class);
        
        // Designation
        Route::resource('designation', DesignationController::class);

        // Leave Type
        Route::resource('leave-type', LeaveTypeController::class);

        // Leave Application
        Route::resource('leave-application', LeaveApplicationController::class);
        Route::post('leave-application/status/{id}', [LeaveApplicationController::class, 'updateStatus'])->name('leave-application.status');

        // Attendance
        Route::resource('attendance', AttendanceController::class);
        
        // Salary Sheet
        Route::controller(PayrollController::class)->prefix('salary-sheet')->as('salary-sheet.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/generate', 'generate')->name('generate');
            Route::post('/pay/{id}', 'pay')->name('pay');
            Route::post('/update/{id}', 'update')->name('update');
        });
    });

    // --------------------> ownership <--------------------
    Route::resource('ownership', OwnershipController::class);

    // --------------------> suppliers <--------------------
    Route::resource('supplier', SupplierController::class)->except(['show', 'edit', 'create']);

    // --------------------> units <--------------------
    Route::resource('unit', UnitController::class)->except(['show', 'edit', 'create']);

    // --------------------> category <--------------------
    Route::post('/category/toggle-top/{id}', [CategoryController::class, 'toggleTop'])->name('category.toggle-top');
    Route::resource('category', CategoryController::class)->except(['show', 'edit', 'create']);

    // --------------------> subcategory <--------------------
    Route::get('/get-subcategories-by-category', [SubCategoryController::class, 'getByCategory'])->name('sub-category.by-category');
    Route::resource('sub-category', SubCategoryController::class)->except(['show', 'edit', 'create']);

    // --------------------> color <--------------------
    Route::resource('color', ColorController::class);
    Route::post('/color/ajax-store', [ColorController::class, 'ajaxStore'])->name('color.ajaxStore');

    // --------------------> color <--------------------
    Route::resource('size', SizeController::class);
    Route::post('/size/ajax-store', [SizeController::class, 'ajaxStore'])->name('size.ajaxStore');

    // --------------------> variation <--------------------
    Route::resource('variation', VariationController::class);

    // --------------------> brands <--------------------
    Route::resource('brand', BrandController::class)->except(['show', 'edit', 'create']);

    // --------------------> platforms <--------------------
    Route::resource('platform', PlatformController::class)->except(['show', 'edit', 'create']);

    // --------------------> marketing & ad costs <--------------------
    Route::controller(MarketingController::class)->group(function () {
        Route::get('ad-cost', 'index')->name('ad-cost.index');
        Route::get('ad-cost/create', 'create')->name('ad-cost.create');
        Route::post('ad-cost', 'store')->name('ad-cost.store');
        Route::delete('ad-cost/{id}', 'destroy')->name('ad-cost.destroy');
        Route::get('roi-tracking', 'roi')->name('roi.tracking');
        Route::get('courier-tracking', 'courierTracking')->name('courier.tracking');
    });

    // --------------------> sms <--------------------
    Route::resource('sms', SmsController::class)->except(['show', 'edit', 'create']);

    Route::get('/generate-variations', [VariationController::class, 'generateVariations']);
    Route::get('/variations', [VariationController::class, 'getVariations']);

    // --------------------> products <--------------------
    // Route::resource('product', ProductController::class);
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product', 'index')->name('product.index');
        Route::get('/product/create', 'create')->name('product.create');
        Route::post('/product/store', 'store')->name('product.store');
        // Route::get('/product/edit/{}','edit/')->name('product.edit/');
        Route::get('/product/edit/{id}', 'edit')->name('product.edit');
        Route::post('/product/update/{id}', 'update')->name('product.update');
        Route::post('/product/destroy/{id}', 'destroy')->name('product.destroy');
        Route::get('/product/search', 'search')->name('product.search');
        Route::get('/multiple/barcode', 'multipleBarcode')->name('multiple.barcode');
        Route::post('/multiple/barcode/print', 'print')->name('multiple.barcode-print')->withoutMiddleware('permission');
        Route::post('/product/update-description/{id}', 'updateDescription')->name('product.update-description');
        Route::post('/product/toggle-homepage-section/{id}', 'toggleHomepageSection')->name('product.toggle-homepage-section');
        Route::post('/product/bulk-assign-homepage-section', 'bulkAssignHomepageSection')->name('product.bulk-assign-homepage-section');
        Route::post('/product/save-flash-deal-settings', 'saveFlashDealSettings')->name('product.save-flash-deal-settings');
        Route::post('/product/save-hollyland-settings', 'saveHollylandSettings')->name('product.save-hollyland-settings');
        Route::post('/product/toggle-website-visibility/{id}', 'toggleWebsiteVisibility')->name('product.toggle-website-visibility');
        Route::post('/product/bulk-toggle-website-visibility', 'bulkToggleWebsiteVisibility')->name('product.bulk-toggle-website-visibility');
    });

    // --------------------> Homepage Sections Management <--------------------
    Route::controller(\App\Http\Controllers\Backend\HomepageSectionController::class)->group(function () {
        Route::post('/homepage-sections/store', 'store')->name('homepage-sections.store');
        Route::post('/homepage-sections/update/{id}', 'update')->name('homepage-sections.update');
        Route::match(['post', 'delete'], '/homepage-sections/destroy/{id}', 'destroy')->name('homepage-sections.destroy');
        Route::post('/homepage-sections/toggle-status/{id}', 'toggleStatus')->name('homepage-sections.toggle-status');
        Route::post('/homepage-sections/reorder', 'reorder')->name('homepage-sections.reorder');
    });

    // --------------------> Service controller <--------------------
    Route::resource('service', ServiceController::class);
    Route::resource('service-receive', ServiceReceiveController::class);
    Route::resource('service-invoice', ServiceInvoiceController::class);

    // --------------------> Warranty Management <--------------------
    Route::resource('warranty', WarrantyController::class);
    Route::resource('service-center', ServiceCenterController::class);
    Route::get('warranty-claim/ajax-search', [WarrantyClaimController::class, 'ajaxSearch'])->name('warranty-claim.ajax-search');
    Route::resource('warranty-claim', WarrantyClaimController::class);
    Route::get('warranty-claim/check/{id}', [WarrantyClaimController::class, 'check'])->name('warranty-claim.check');
    Route::post('warranty-claim/check/{id}', [WarrantyClaimController::class, 'updateChecking'])->name('warranty-claim.update-check');
    
    Route::resource('warranty-delivery', WarrantyDeliveryController::class);
    Route::resource('supplier-claim', SupplierClaimController::class);

    // --------------------> Bank Account <--------------------
    Route::resource('bank-account', BankAccountController::class);

    // --------------------> Bank Transaction <--------------------
    Route::controller(BankTransactionController::class)->group(function () {
        // --------------------> Deposit <--------------------
        Route::get('/deposit', 'deposit')->name('deposit-create');
        Route::post('/deposit', 'depositStore')->name('deposit-store');

        // --------------------> Withdraw <--------------------
        Route::get('/withdraw', 'withdraw')->name('withdraw-create');
        Route::post('/withdraw', 'withdrawStore')->name('withdraw-store');

        // --------------------> Bank Transfer <--------------------
        Route::get('/bank-transfer-index', 'bankTransferIndex')->name('bank-transfer-index');
        Route::post('/bank-transfer', 'bankTransferStore')->name('bank-transfer-store');

        // --------------------> Transaction <--------------------
        Route::get('/transaction-history', 'transactionHistory')->name('transaction-history');
    });

    // --------------------> Payment <--------------------
    Route::controller(PaymentController::class)->prefix('payment')->name('payment.')->group(function () {
        // --------------------> Deposit <--------------------
        Route::get('/customer', 'payCustomer')->name('pay-customer');
        Route::post('/customer/store', 'payCustomerStore')->name('pay-customer-store');

        // --------------------> Deposit <--------------------
        Route::get('/supplier', 'paySupplier')->name('pay-supplier');
        Route::post('/supplier/store', 'paySupplierStore')->name('pay-supplier-store');

        // --------------------> destroy <--------------------
        Route::match(['get', 'post', 'delete'], '/customer/destroy/{id}', 'customerDestroy')->name('customer-destroy');
        Route::match(['get', 'post', 'delete'], '/supplier/destroy/{id}', 'supplierDestroy')->name('supplier-destroy');
    });

    // --------------------> expense <--------------------
    Route::resource('expense', ExpenseController::class);
    Route::controller(ExpenseController::class)->prefix('expense')->name('expense.')->group(function () {
        // --------------------> category <--------------------
        Route::get('/category/index', 'categoryIndex')->name('category-index');
        Route::post('/category/store', 'categoryStore')->name('category-store');
        Route::post('/category/update/{id}', 'categoryUpdate')->name('category-update');
        Route::delete('/category/destroy/{id}', 'categoryDestroy')->name('category-destroy');
    });

    // --------------------> asset <--------------------
    Route::resource('asset', AssetController::class)->except(['show', 'edit', 'update']);

    // --------------------> Reports <--------------------
    Route::controller(ReportController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('/stock', 'stock')->name('stock');
        Route::get('used/stock', 'usedStock')->name('used-stock');
        Route::get('/daily', 'daily')->name('daily');
        Route::get('/sale', 'sale')->name('sale');
        Route::get('/item-sale', 'itemSale')->name('item-sale');
        Route::get('/service', 'service')->name('service');
        Route::get('/product-sale', 'productSale')->name('product.sale');
        Route::get('/purchase', 'purchase')->name('purchase');
        Route::get('/supplier/ledger', 'supplierLedger')->name('supplier-ledger');
        Route::get('/customer/ledger', 'customerLedger')->name('customer-ledger');
        Route::get('/ownership/ledger', 'ownerShipLedger')->name('ownership-ledger');
        Route::get('/account/ledger', 'accountLedger')->name('account-ledger');
        Route::get('/profit-loss', 'profitLoss')->name('profit-loss');
        Route::get('/supplier/due', 'supplierDue')->name('supplier-due');
        Route::get('/supplier/report', 'supplierReport')->name('supplier-report');
        Route::get('/customer/due', 'customerDue')->name('customer-due');
        Route::get('/customer/full-report', 'customerFullReport')->name('customer.full-report');
        Route::get('/low-stock', 'low_stock')->name('low.stock');
        Route::get('/user/sell', 'user_sell')->name('user.sell');
        Route::get('/daily/stock', 'dailyStock')->name('daily.stock');
        Route::get('/inventory/register', 'inventoryRegister')->name('inventory.register');
        Route::get('/woocommerce', 'woocommerceReport')->name('woocommerce');
        Route::get('/shop', 'shopReport')->name('shop');
        Route::get('/shop/print', 'shopReportPrint')->name('shop.print');
    });
    //------------------------- pdf ------------------
    Route::controller(PDFController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('/stock/pdf', 'stock_pdf')->name('stock.pdf');
        Route::get('/daily/pdf', 'daily_pdf')->name('daily.pdf');
        Route::get('/user/sell/pdf', 'pdf')->name('user.sell.pdf');
    });

    // ----------------------- AI Stock Auditor ---------------------
    Route::controller(AIStockAuditorController::class)->prefix('ai-auditor')->name('ai-auditor.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/audit/{id}', 'audit')->name('audit');
        Route::get('/fix/{id}', 'fix')->name('fix');
    });

    // ----------------------- AI Chatbot ---------------------
    Route::controller(AIChatbotController::class)->prefix('ai-chatbot')->name('ai-chatbot.')->group(function () {
        Route::post('/chat', 'chat')->name('chat');
        Route::post('/clear', 'clearHistory')->name('clear');
    });

    // ----------------------- BotMan Chatbot ---------------------
    Route::match(['get', 'post'], '/botman', [\App\Http\Controllers\Backend\BotManController::class, 'handle'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/botman/chat', function () {
        return view('backend.layouts.includes.chatbot-frame');
    });

    // ----------------------- Activity Log ---------------------
    Route::group(['prefix' => 'activity-log', 'as' => 'activity-log.'], function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{id}/details', [ActivityLogController::class, 'getDetails'])->name('details');
        Route::delete('/clear', [ActivityLogController::class, 'destroyAll'])->name('clear');
    });

    // --------------------> users <--------------------
    Route::resource('user', UserController::class);

    // Managers Route ARE HERE...
    Route::controller(BusinessSettingsController::class)->prefix('setting')->name('setting.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/update', 'update')->name('update');
    });

    // Super Admin Settings
    Route::controller(SuperAdminSettingsController::class)->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/settings', 'index')->name('settings');
        Route::get('/settings/env', fn() => redirect()->route('super-admin.settings'))->name('settings.env.get');
        Route::post('/settings/env', 'updateEnv')->name('settings.env');
        Route::post('/settings/sidebar', 'updateSidebar')->name('settings.sidebar');
    });

    // --------------------> roles & permission <--------------------
    Route::controller(RolesPermissionController::class)->group(function () {
        Route::get('roles-permission', 'index')->name('roles-permission.index');
        Route::get('roles-permission/create', 'create')->name('roles-permission.create');
        Route::post('roles-permission', 'store')->name('roles-permission.store');
        Route::get('roles-permission/{id}/edit', 'edit')->name('roles-permission.edit');
        Route::put('roles-permission/{id}', 'update')->name('roles-permission.update');
    });

    // --------------------> others <--------------------
    Route::controller(OthersController::class)->group(function () {
        Route::get('/status-update', 'statusUpdate')->name('status.update');
        Route::get('/get/product-by-supplier/{supplier_id}', 'getProductBySupplier')->name('get.productBySupplier');
        Route::get('/get/product-search', 'productSearch')->name('product-search');
        Route::get('/get/sc-product-search', 'scProductSearch')->name('sc-product-search');
        Route::get('/get/used/product-search', 'usedProductSearch')->name('used-product-search');
        Route::get('/get/search-product-id/{my_id}', 'productSearchDetails')->name('search-product-id');
        Route::get('/get/sc-search-product-id/{my_id}', 'scproductSearchDetails')->name('sc-search-product-id');
        Route::get('/get/used/search-product-id/{my_id}', 'usedProductSearchDetails')->name('used-search-product-id');
        Route::get('/get/trans/search-product-id/{product_id}/{branch_id}', 'transProductSearchDetails')->name('trans-search-product-id');
        Route::get('/get/product-barcode/{code}', 'productBarcode')->name('product-barcode');
        Route::get('/get/product/unit/{my_id}', 'productUnit')->name('product-unit');
        Route::get('/get/pos-products', 'posProducts')->name('posProducts');
        Route::get('/get/pos/product-id/{my_id}', 'productPosDetails')->name('pos-product-id');
        Route::get('/get/pos/sc-product-id/{my_id}', 'productScPosDetails')->name('sc-pos-product-id');
        Route::get('/get/serial-search', 'serialSearch')->name('serial-search');
        Route::get('/get/search-serial-id/{my_id}', 'serialSearchDetails')->name('search-serial-id');
        Route::post('/get/check-imei-duplicates', 'checkImeiDuplicates')->name('check-imei-duplicates');
        Route::get('/get/invoice-item-candidate-imeis', 'getInvoiceItemCandidateImeis')->name('get-invoice-item-candidate-imeis');
        //transfer
        Route::get('/get/to/account', 'getToAccount')->name('get-to-account');
        Route::get('get/account/balance', 'getAccountBalance')->name('get-account-balance');

        Route::get('/customer/previous/due', 'findCustomerDue')->name('customer.previous.due');
        Route::get('/customer/vehicles', 'getCustomerVehicles')->name('customer.vehicles');
        // payment
        Route::get('customer/account/balance/{my_id}', 'getCustomerAccountBalance')->name('customer-account-balance');
        Route::get('supplier/account/balance/{my_id}', 'getSupplierAccountBalance')->name('supplier-account-balance');
        // download.backup
        Route::get('/download/backup', 'downloadBackup')->name('status.download.backup');
    });
    Route::controller(UserProfileController::class)->group(function () {
        Route::get('/user/profile', 'index')->name('profile');
        Route::post('/user/profile/update', 'update')->name('profile.update');
        Route::post('/user/profile/change/password', 'changePassword')->name('profile.password');
    });

    Route::resource('pathao', PathaoController::class);
    Route::post('/pathao/order', [PathaoController::class, 'placeOrder'])->name('pathao.order');
    Route::get('/pathao/order/status/{id}', [PathaoController::class, 'checkOrderStatus']);
    Route::get('/fetch-zones', [PathaoController::class, 'getZonesByCity'])->name('pathao.zones');
    Route::get('/fetch-areas', [PathaoController::class, 'getAreasByCity'])->name('pathao.areas');

    // ----------------------- Banners & Sliders ---------------------
    Route::resource('banner', BannerController::class);
    Route::post('/banner/toggle-status/{id}', [BannerController::class, 'toggleStatus'])->name('banner.toggle-status');

    Route::controller(ReturnSaleController::class)->group(function () {
        Route::get('return/sale', 'index')->name('return.sale');
        Route::get('return/sale/{id}', 'create')->name('return.create');
        Route::post('return/sale/submit', 'insert')->name('return.insert');
        Route::get('return/sale/delete/{id}', 'delete')->name('return.delete');
    });

    Route::controller(ReturnPurchaseController::class)->group(function () {
        Route::get('return/purchase/delete/{id}', 'destroy')->name('return.purchase.delete');
    });

    Route::resource('rtnPurchase', ReturnPurchaseController::class);
    // ----------------------- damage ---------------------
    Route::controller(DamageController::class)->group(function () {
        Route::get('damage', 'index')->name('damage.index');
        Route::get('damage/create', 'create')->name('damage.create');
        Route::post('damage/create/submit', 'insert')->name('damage.insert');
        Route::post('damage/delete/{id}', 'softDelete')->name('damage.delete');
    });

    // WooCommerce Integration Routes
    Route::controller(WooCommerceController::class)->group(function () {
        Route::get('/woocommerce', 'index')->name('woocommerce.index');
        Route::get('/woocommerce/orders', 'ordersList')->name('woocommerce.orders.list');
        Route::post('/woocommerce/orders/update-status/{id}', 'updateOrderStatus')->name('woocommerce.orders.updateStatus');
        Route::post('/woocommerce/settings', 'saveSettings')->name('woocommerce.settings.save');
        Route::post('/woocommerce/test-connection', 'testConnection')->name('woocommerce.test');
        Route::post('/woocommerce/sync-products', 'syncProducts')->name('woocommerce.sync.products');
        Route::post('/woocommerce/sync-orders', 'syncOrders')->name('woocommerce.sync.orders');
        Route::post('/woocommerce/auto-sync', 'autoSync')->name('woocommerce.auto.sync');
        Route::post('/woocommerce/orders/send-to-online', 'sendToOnlineSale')->name('woocommerce.orders.sendToOnline');
        Route::post('/woocommerce/orders/approve/{id}', 'approveOrder')->name('woocommerce.orders.approve');
        Route::get('/woocommerce/orders/details/{id}', 'orderDetails')->name('woocommerce.orders.details');
        Route::post('/woocommerce/orders/details/{id}', 'updateOrderDetails')->name('woocommerce.orders.update');
        Route::post('/woocommerce/orders/details-update/{id}', 'updateOrderDetails')->name('woocommerce.orders.updateDetails');
        Route::post('/woocommerce/orders/update-item/{id}', 'updateOrderItem')->name('woocommerce.orders.updateItem');
        Route::post('/woocommerce/orders/add-item/{id}', 'addOrderItem')->name('woocommerce.orders.addItem');
        Route::match(['post', 'delete'], '/woocommerce/orders/{id}/item/{itemId}', 'removeOrderItem')->name('woocommerce.orders.removeItem');
        Route::match(['post', 'delete'], '/woocommerce/orders/{id}/remove-item/{itemId}', 'removeOrderItem');
        Route::post('/woocommerce/orders/add-note/{id}', 'addOrderNote')->name('woocommerce.orders.addNote');
        Route::get('/woocommerce/products/search', 'ajaxProductSearch')->name('woocommerce.ajax.productSearch');
        Route::get('/woocommerce/products/search-query', 'ajaxProductSearch')->name('woocommerce.products.ajaxSearch');
        Route::get('/woocommerce/scanner', 'scannerView')->name('woocommerce.scanner');
        Route::post('/woocommerce/scanner/process', 'processScan')->name('woocommerce.scanner.process');
        Route::get('/woocommerce/orders/search', 'searchOrdersView')->name('woocommerce.search');
        Route::get('/woocommerce/orders/search-list', 'searchOrdersView')->name('woocommerce.orders.search');
        Route::get('/woocommerce/orders/search-ajax', 'searchOrdersAjax')->name('woocommerce.search.ajax');
        Route::get('/woocommerce/orders/search-ajax-list', 'searchOrdersAjax')->name('woocommerce.orders.searchAjax');
        Route::post('/woocommerce/orders/push-statuses-to-woo', 'pushStatusesToWooCommerce')->name('woocommerce.orders.pushStatuses');
    });

    // Website Orders Management
    Route::controller(WebsiteOrderController::class)->group(function () {
        Route::get('/website-orders', 'index')->name('website-orders.index');
        Route::get('/website-orders/details/{id}', 'orderDetails')->name('website-orders.details');
        Route::post('/website-orders/details/{id}', 'updateOrderDetails')->name('website-orders.updateDetails');
        Route::post('/website-orders/approve/{id}', 'approveOrder')->name('website-orders.approve');
        Route::post('/website-orders/update-item/{id}', 'updateOrderItem')->name('website-orders.updateItem');
        Route::post('/website-orders/add-item/{id}', 'addOrderItem')->name('website-orders.addItem');
        Route::match(['post', 'delete'], '/website-orders/{id}/item/{itemId}', 'removeOrderItem')->name('website-orders.removeItem');
        Route::post('/website-orders/add-note/{id}', 'addOrderNote')->name('website-orders.addNote');
        Route::get('/website-orders/products/search', 'ajaxProductSearch')->name('website-orders.ajax.productSearch');
        Route::get('/website-orders/{id}', 'show')->name('website-orders.show');
        Route::post('/website-orders/update-status/{id}', 'updateStatus')->name('website-orders.update-status');
        Route::post('/website-orders/{id}/status', 'updateStatus')->name('website-orders.quick-status');
        Route::post('/website-orders/{id}/note', 'updateNote')->name('website-orders.quick-note');
        Route::post('/website-orders/bulk-action', 'bulkAction')->name('website-orders.bulk-action');
        Route::post('/website-orders/dispatch-courier', 'dispatchCourier')->name('website-orders.dispatch-courier');
        Route::delete('/website-orders/{id}', 'destroy')->name('website-orders.destroy');
        Route::get('/website-orders/print/{id}', 'printInvoice')->name('website-orders.print');
        Route::post('/website-orders/convert/{id}', 'convertToInvoice')->name('website-orders.convert');
        Route::post('/website-orders/send-to-online', 'sendToOnlineSale')->name('website-orders.send-to-online');
        Route::post('/website-orders/bulk-convert', 'bulkConvertToInvoice')->name('website-orders.bulk-convert');
    });

});

Route::get('/customer/invoice/copy/{id}', [InvoiceController::class,'customerPrintInvoice'])->name('printNewInvoice');

Route::get('lang/{lang}', [App\Http\Controllers\LanguageController::class, 'switchLang']);

// WooCommerce Real-time Webhook endpoint (Web route fallback with aliases)
Route::match(['get', 'post', 'head'], '/woocommerce/webhook', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::match(['get', 'post', 'head'], '/woocommerce/webl', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::match(['get', 'post', 'head'], '/woocommerce/webhooks', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


