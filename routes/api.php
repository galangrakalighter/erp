<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/quotations/{cabang}', [QuotationController::class, 'getDataQuotation']);
Route::get('/inventories/{cabang}', [QuotationController::class, 'getInventories']);
