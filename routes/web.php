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
});

require __DIR__.'/auth.php';
