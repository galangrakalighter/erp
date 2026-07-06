<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\warehouseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SalesController;
Route::get('/', function () {
    return view('login');
});

Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
Route::get('/purchase-order/{id}/print', [PurchaseOrderController::class,'print'])->name('purchase-order.print');
Route::get('/purchase-order/{id}/quotation', [PurchaseOrderController::class,'quotation'])->name('purchase-order.quotation');
Route::get('/purchase-order/{id}/invoice', [PurchaseOrderController::class,'invoice'])->name('purchase-order.invoice');
Route::get('/purchase-order/{id}/faktur', [PurchaseOrderController::class,'faktur'])->name('purchase-order.faktur');
Route::get('/purchase-order/{id}/surat-jalan', [PurchaseOrderController::class,'suratJalan'])->name('purchase-order.suratJalan');
Route::resource('warehouse', warehouseController::class);
Route::resource('users', UserController::class);
Route::resource('purchase-order', PurchaseOrderController::class);
Route::get('/getBarang/{id}', [PurchaseOrderController::class, 'getBarang'])->name('getBarang');
Route::resource('sales', SalesController::class);