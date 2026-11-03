<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // --- PRODUK ---
    // PENTING: route spesifik 'products/search' HARUS didaftarkan SEBELUM apiResource,
    // agar 'search' tidak salah dianggap sebagai {product} id.
    Route::get('products/search', [ProductController::class, 'search']);
    Route::apiResource('products', ProductController::class)->except(['destroy']);
    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->middleware('role:admin');

    // --- TRANSAKSI ---
    // Route laporan spesifik didaftarkan lebih dulu (3 segmen, tidak bentrok, tapi tetap best practice).
    Route::get('transactions/report/summary', [TransactionController::class, 'summary'])
        ->middleware('role:admin');
    Route::apiResource('transactions', TransactionController::class)
        ->only(['index', 'store', 'show']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::apiResource('products', ProductController::class);
