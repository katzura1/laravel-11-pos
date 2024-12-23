<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SubBrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\CashierController;
use App\Http\Controllers\ProductCategoryController;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class,'index'])->name('login');
    Route::post('authenticate', [LoginController::class,'authenticate'])->name('authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::post('logout', [LoginController::class,'logout'])->name('logout');
    Route::get('choose-outlet', [LoginController::class,'chooseOutlet'])->name('choose-outlet');
    Route::post('set-outlet', [LoginController::class,'setOutlet'])->name('set-outlet');
});


Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class,'index'])->name('admin.index');
    Route::get('/get', [AdminController::class,'getAdmins'])->name('admin.getAdmins');
    Route::post('/store', [AdminController::class,'store'])->name('admin.store');
    Route::put('/put', [AdminController::class,'update'])->name('admin.update');

    //outlet mapping
    Route::post('store-outlet', [AdminController::class,'storeOutlet'])->name('admin.storeOutlet');
    //menu mapping
    Route::post('store-menu', [AdminController::class,'storeMenu'])->name('admin.storeMenu');
});

Route::prefix('menu')->middleware(['auth'])->group(function () {
    Route::get('/', [MenuController::class,'index'])->name('outlet.index');
    Route::get('/get', [MenuController::class,'getMenus'])->name('outlet.getMenus');
    Route::get('/get-parents', [MenuController::class,'getParents'])->name('outlet.getParents');
    Route::get('get-menu-parents-with-children', [MenuController::class,'getMenuParentsWithChildren'])->name('outlet.getMenuParentsWithChildren');
    Route::get('/get-user-menus', [MenuController::class,'getUserMenus'])->name('outlet.getUserMenus');
    Route::post('/store', [MenuController::class,'store'])->name('outlet.store');
    Route::put('/put', [MenuController::class,'update'])->name('outlet.update');
    Route::delete('/destroy', [MenuController::class,'destroy'])->name('admin.destroy');
});

Route::prefix('cashier')->middleware(['auth'])->group(function () {
    Route::get('/', [CashierController::class,'index'])->name('cashier.index');
    Route::get('/get', [CashierController::class,'getCashiers'])->name('cashier.getCashiers');
    Route::post('/store', [CashierController::class,'store'])->name('cashier.store');
    Route::put('/put', [CashierController::class,'update'])->name('cashier.update');
    // Route::delete('/admins/{id}', [CashierController::class,'destroy'])->name('admin.destroy');
});

Route::prefix('outlet')->middleware(['auth'])->group(function () {
    Route::get('/', [OutletController::class,'index'])->name('outlet.index');
    Route::get('/get', [OutletController::class,'getOutlets'])->name('outlet.getOutlets');
    Route::post('/store', [OutletController::class,'store'])->name('outlet.store');
    Route::put('/put', [OutletController::class,'update'])->name('outlet.update');
    Route::delete('/destroy', [OutletController::class,'destroy'])->name('outlet.destroy');
});

Route::prefix('supplier')->middleware(['auth'])->group(function () {
    Route::get('/', [SupplierController::class,'index'])->name('supplier.index');
    Route::get('/get', [SupplierController::class,'getSuppliers'])->name('supplier.getSuppliers');
    Route::post('/store', [SupplierController::class,'store'])->name('supplier.store');
    Route::put('/put', [SupplierController::class,'update'])->name('supplier.update');
    Route::delete('/destroy', [SupplierController::class,'destroy'])->name('supplier.destroy');
});

Route::prefix('customer')->middleware(['auth'])->group(function () {
    Route::get('/', [CustomerController::class,'index'])->name('customer.index');
    Route::get('/get', [CustomerController::class,'getCustomers'])->name('customer.getCustomers');
    Route::post('/store', [CustomerController::class,'store'])->name('customer.store');
    Route::put('/put', [CustomerController::class,'update'])->name('customer.update');
    Route::delete('/destroy', [CustomerController::class,'destroy'])->name('customer.destroy');
});

Route::prefix('brand')->middleware(['auth'])->group(function () {
    Route::get('/', [BrandController::class,'index'])->name('brand.index');
    Route::get('/get', [BrandController::class,'getBrands'])->name('brand.getBrands');
    Route::post('/store', [BrandController::class,'store'])->name('brand.store');
    Route::put('/put', [BrandController::class,'update'])->name('brand.update');
    Route::delete('/destroy', [BrandController::class,'destroy'])->name('brand.destroy');
});

Route::prefix('sub-brand')->middleware(['auth'])->group(function () {
    Route::get('/', [SubBrandController::class,'index'])->name('sub-brand.index');
    Route::get('/get', [SubBrandController::class,'getSubBrands'])->name('sub-brand.getSubBrands');
    Route::get('/get-brands', [SubBrandController::class,'getBrands'])->name('sub-brand.getBrands');
    Route::post('/store', [SubBrandController::class,'store'])->name('sub-brand.store');
    Route::put('/put', [SubBrandController::class,'update'])->name('sub-brand.update');
    Route::delete('/destroy', [SubBrandController::class,'destroy'])->name('sub-brand.destroy');
});

Route::prefix('product-category')->middleware(['auth'])->group(function () {
    Route::get('/', [ProductCategoryController::class,'index'])->name('product-category.index');
    Route::get('/get', [ProductCategoryController::class,'getProductCategorys'])->name('product-category.getProductCategorys');
    Route::post('/store', [ProductCategoryController::class,'store'])->name('product-category.store');
    Route::put('/put', [ProductCategoryController::class,'update'])->name('product-category.update');
    Route::delete('/destroy', [ProductCategoryController::class,'destroy'])->name('product-category.destroy');
});

Route::prefix('product')->middleware(['auth'])->group(function () {
    Route::get('/', [ProductController::class,'index'])->name('product.index');
    Route::get('/get', [ProductController::class,'getProducts'])->name('product.getProducts');
    Route::get('get-suppliers', [ProductController::class,'getSuppliers'])->name('product.getSuppliers');
    Route::get('get-sub-brands', [ProductController::class,'getSubBrands'])->name('product.getSubBrands');
    Route::get('get-brands', [ProductController::class,'getBrands'])->name('product.getBrands');
    Route::get('get-product-categories', [ProductController::class,'getProductCategories'])->name('product.getProductCategories');
    Route::post('/store', [ProductController::class,'store'])->name('product.store');
    Route::put('/put', [ProductController::class,'update'])->name('product.update');
    Route::delete('/destroy', [ProductController::class,'destroy'])->name('product.destroy');
});

Route::prefix('stock-in')->middleware(['auth','check.outlet'])->group(function () {
    Route::get('/', [StockInController::class,'index'])->name('stock-in.index');
    Route::get('/get', [StockInController::class,'getStockIns'])->name('stock-in.getStockIns');
    Route::post('/store', [StockInController::class,'store'])->name('stock-in.store');
    Route::put('/put', [StockInController::class,'update'])->name('stock-in.update');
    Route::delete('/destroy', [StockInController::class,'destroy'])->name('stock-in.destroy');
});
