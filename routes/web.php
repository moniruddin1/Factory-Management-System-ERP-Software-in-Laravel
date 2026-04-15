<?php

use App\Http\Controllers\ProfileController;
//use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\CategoryController; // এই লাইনটি চেক করুন
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Setup\AuditLogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SupplierLedgerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\Inventory\ProductionController;


// পাবলিক ইনভয়েস দেখার রাউট
Route::get('/qrinvoice/{invoice_no}', [PurchaseController::class, 'qrInvoicePreview'])
    ->name('qrinvoice.preview')
    ->middleware('signed'); // এটি ইউআরএল টেম্পারিং প্রতিরোধ করবে

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

Route::resource('roles', RoleController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::resource('variations', VariationController::class);
Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');


    // Master Modules

    Route::resource('categories', CategoryController::class);
    // Master Setup: Units Route
        Route::resource('units', UnitController::class)->except(['create', 'show', 'edit']);
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
            // Variations (আপনার আগের কোড অনুযায়ী)
            Route::resource('variations', VariationController::class);
            Route::resource('products', ProductController::class)->except(['create', 'show', 'edit']);



            // Supplier Management Routes
            Route::resource('suppliers', App\Http\Controllers\SupplierController::class);


// 2. Product Mapping
Route::get('supplier-products', [App\Http\Controllers\SupplierProductController::class, 'index'])->name('supplier-products.index');
Route::post('supplier-products/{supplier}', [App\Http\Controllers\SupplierProductController::class, 'store'])->name('supplier-products.store');



            // 3. Purchase Invoices
            Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
            Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
            Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
            Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
            Route::get('get-supplier-products/{supplier_id}', [PurchaseController::class, 'getSupplierProducts'])->name('get-supplier-products');
Route::resource('supplier-payments', SupplierPaymentController::class);

            Route::prefix('supplier-ledgers')->group(function () {
                Route::get('/', [SupplierLedgerController::class, 'index'])->name('supplier-ledgers.index');
                Route::get('/{id}/show', [SupplierLedgerController::class, 'show'])->name('supplier-ledgers.show');
            });


            Route::prefix('purchase-returns')->group(function () {
                Route::get('/', [PurchaseReturnController::class, 'index'])->name('purchase-returns.index');
                Route::get('/create', [PurchaseReturnController::class, 'create'])->name('purchase-returns.create');
                Route::post('/store', [PurchaseReturnController::class, 'store'])->name('purchase-returns.store');

                // নির্দিষ্ট পারচেজ আইডির আইটেমগুলো পাওয়ার জন্য AJAX রাউট
                Route::get('/get-purchase-items/{id}', [PurchaseReturnController::class, 'getPurchaseItems']);
            });

            Route::get('/{id}/show', [PurchaseReturnController::class, 'show'])->name('purchase-returns.show');

            Route::prefix('supplier-reports')->name('supplier-reports.')->group(function () {
                Route::get('/', [App\Http\Controllers\SupplierReportController::class, 'index'])->name('index');
                Route::get('/due-balances', [App\Http\Controllers\SupplierReportController::class, 'dueReport'])->name('due-report');
            Route::get('/purchase-summary', [App\Http\Controllers\SupplierReportController::class, 'purchaseReport'])->name('purchase-report');

            });
            Route::get('/supplier-reports/payment-summary', [App\Http\Controllers\SupplierReportController::class, 'paymentReport'])->name('supplier-reports.payment-report');

// Master Setup Routes
Route::middleware(['auth'])->group(function () {
    // Location Setup
    Route::resource('locations', LocationController::class);

    // Staff Setup
    Route::resource('staffs', StaffController::class);
});

// Inventory Routes
Route::get('/inventory/stock', [\App\Http\Controllers\InventoryController::class, 'stockReport'])->name('inventory.stock');
Route::get('/inventory/ledger', [\App\Http\Controllers\InventoryController::class, 'ledger'])->name('inventory.ledger');
// Material Issue Routes
Route::get('/inventory/issue', [\App\Http\Controllers\InventoryController::class, 'issueCreate'])->name('inventory.issue.create');
Route::post('/inventory/issue', [\App\Http\Controllers\InventoryController::class, 'issueStore'])->name('inventory.issue.store');
Route::get('/inventory/get-stock-details', [\App\Http\Controllers\InventoryController::class, 'getStockDetails'])->name('inventory.get_stock_details'); // AJAX এর জন্য
Route::get('/inventory/issues', [\App\Http\Controllers\InventoryController::class, 'issueIndex'])->name('inventory.issue.index');
Route::get('/inventory/issues/{id}', [\App\Http\Controllers\InventoryController::class, 'issueShow'])->name('inventory.issue.show');


// Inventory & Mfg Routes
Route::resource('boms', BomController::class);

// Production Routes
Route::get('productions/get-bom/{id}', [\App\Http\Controllers\Inventory\ProductionController::class, 'getBomDetails'])->name('productions.get-bom');

Route::get('productions/get-issue/{id}', [\App\Http\Controllers\Inventory\ProductionController::class, 'getIssueDetails'])->name('productions.get-issue');
// আগের get-bom রাউটটি তো আছেই:
Route::get('productions/get-bom/{id}', [\App\Http\Controllers\Inventory\ProductionController::class, 'getBomDetails'])->name('productions.get-bom');

// ১. কাস্টম রাউটটি সবসময় resource এর উপরে থাকবে
Route::get('productions/analytics', [ProductionController::class, 'analytics'])->name('productions.analytics');

// ২. AJAX রাউটগুলো
Route::get('productions/get-bom/{id}', [ProductionController::class, 'getBomDetails'])->name('productions.get-bom');
Route::get('productions/get-issue/{id}', [ProductionController::class, 'getIssueDetails'])->name('productions.get-issue');

// ৩. রিসোর্স রাউটটি সবার শেষে একবারই থাকবে
Route::resource('productions', ProductionController::class);

Route::get('/inventory/ready-products', [\App\Http\Controllers\InventoryController::class, 'readyProducts'])->name('inventory.ready_products');
Route::post('/inventory/transfer-to-store', [\App\Http\Controllers\InventoryController::class, 'transferToStore'])->name('inventory.transfer_to_store');
Route::get('/inventory/store-stock', [\App\Http\Controllers\InventoryController::class, 'storeStock'])->name('inventory.store_stock');
// এই দুইটা ঠিক করা হয়েছে
Route::get('/inventory/production-stock', [\App\Http\Controllers\InventoryController::class, 'productionInventory'])->name('inventory.production_stock');
Route::get('/inventory/production-stock/{product_id}', [\App\Http\Controllers\InventoryController::class, 'productionItemDetails'])->name('inventory.production_item_details');





});

require __DIR__.'/auth.php';
