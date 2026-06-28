<?php

use App\Http\Controllers\Auth\DemoLoginController;
use App\Http\Controllers\DemoDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DemoDashboardController::class)->name('dashboard');
Route::get('/login', [DemoLoginController::class, 'create'])->name('login');
Route::post('/login', [DemoLoginController::class, 'store'])->name('login.store');
Route::post('/logout', [DemoLoginController::class, 'destroy'])->middleware('auth')->name('logout');
