<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::post('/transfer', [TransactionController::class, 'transfer']);
    Route::get('/transactions', [TransactionController::class, 'history']);
});

Route::get('/setup', function () {
    Artisan::call('db:seed', ['--force' => true]);
    return response()->json(['message' => 'Banco populado com sucesso! Pode testar o login.']);
});