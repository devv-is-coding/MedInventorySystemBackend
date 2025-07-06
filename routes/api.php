<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionTypeController;

Route::post('/login', [AuthController::class, 'login']);
    // Medicine routes
        Route::apiResource('medicines', MedicineController::class);

        // Transaction routes
        Route::apiResource('stock-transactions', StockTransactionController::class);

Route::group(['middleware'=>['auth:sunctum']], 
function () {
    Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Medicine routes
        Route::apiResource('medicines', MedicineController::class);

        // Transaction routes
        Route::apiResource('stock-transactions', StockTransactionController::class);
        Route::get('transaction-types', [TransactionTypeController::class, 'index']);

        // Report routes
        Route::get('reports/daily', [ReportController::class, 'dailyReport']);
        Route::get('reports/monthly', [ReportController::class, 'monthlyReport']);
        Route::post('reports/month-close', [ReportController::class, 'performMonthClose']);
});
