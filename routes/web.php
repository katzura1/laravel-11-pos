<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\CashierController;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class,'index'])->name('login');
    Route::post('authenticate', [LoginController::class,'authenticate'])->name('authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::post('logout', [LoginController::class,'logout'])->name('logout');
});


Route::prefix('admin')->middleware([])->group(function () {
    Route::get('/', [AdminController::class,'index'])->name('admin.index');
    Route::get('/get', [AdminController::class,'getAdmins'])->name('admin.getAdmins');
    Route::post('/store', [AdminController::class,'store'])->name('admin.store');
    Route::put('/put', [AdminController::class,'update'])->name('admin.update');
    // Route::delete('/admins/{id}', [AdminController::class,'destroy'])->name('admin.destroy');
});

Route::prefix('cashier')->middleware([])->group(function () {
    Route::get('/', [CashierController::class,'index'])->name('cashier.index');
    Route::get('/get', [CashierController::class,'getCashiers'])->name('cashier.getCashiers');
    Route::post('/store', [CashierController::class,'store'])->name('cashier.store');
    Route::put('/put', [CashierController::class,'update'])->name('cashier.update');
    // Route::delete('/admins/{id}', [CashierController::class,'destroy'])->name('admin.destroy');
});

Route::prefix('outlet')->middleware([])->group(function () {
    Route::get('/', [OutletController::class,'index'])->name('outlet.index');
    Route::get('/get', [OutletController::class,'getOutlets'])->name('outlet.getOutlets');
    Route::post('/store', [OutletController::class,'store'])->name('outlet.store');
    Route::put('/put', [OutletController::class,'update'])->name('outlet.update');
    Route::delete('/destroy', [OutletController::class,'destroy'])->name('outlet.destroy');
});
