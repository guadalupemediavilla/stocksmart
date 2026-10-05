<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\MovimientoApiController;

Route::post('/login', [AuthApiController::class, 'login']);

Route::get('/productos', [ProductoApiController::class, 'index']);
Route::get('/productos/{id}', [ProductoApiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/productos', [ProductoApiController::class, 'store']);
    Route::put('/productos/{id}', [ProductoApiController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoApiController::class, 'destroy']);

    Route::get('/movimientos', [MovimientoApiController::class, 'index']);
    Route::post('/movimientos', [MovimientoApiController::class, 'store']);
});