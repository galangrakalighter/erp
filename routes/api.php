<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SPKController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/quotations/{cabang}', [QuotationController::class, 'getDataQuotation']);
Route::get('/inventories/{cabang}', [QuotationController::class, 'getInventories']);
Route::post('/warehouse/spk/{id}', [SPKController::class, 'sendWarehouse']);
Route::post('/production/spk/{id}', [SPKController::class, 'sendProduction']);
Route::get('/users/production', [UserController::class, 'getUserProduction']);