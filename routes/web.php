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
            Route::get('purchases', function() { return 'Purchase Invoices Page'; })->name('purchases.index');
            Route::get('purchases/create', function() { return 'Create Purchase Page'; })->name('purchases.create');

            // 4. Supplier Ledger
            Route::get('supplier-ledgers', function() { return 'Supplier Ledger Page'; })->name('supplier-ledgers.index');

            // 5. Payment Management
            Route::get('supplier-payments', function() { return 'Payment Management Page'; })->name('supplier-payments.index');

            // 6. Return Management
            Route::get('purchase-returns', function() { return 'Purchase Returns Page'; })->name('purchase-returns.index');

            // 7. Reporting & Statements
            Route::get('supplier-reports', function() { return 'Supplier Reports Page'; })->name('supplier-reports.index');
});

require __DIR__.'/auth.php';
