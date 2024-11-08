<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sign-in', [LoginController::class,'index'])->name('login');
Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
