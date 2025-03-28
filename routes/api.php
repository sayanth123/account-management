<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/{account_number}', [AccountController::class, 'show']);
    Route::put('/accounts/{account_number}', [AccountController::class, 'update']);
    Route::delete('/accounts/{account_number}', [AccountController::class, 'destroy']);

    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
});
