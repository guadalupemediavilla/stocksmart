<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteController;
use App\Http\Controllers\PrecioController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/productos/create', [ProductoController::class, 'create']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::get('/productos/{id}', [ProductoController::class, 'show']);
    Route::get('/productos/{id}/edit', [ProductoController::class, 'edit']);
    Route::put('/productos/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

    Route::post('/productos/{id_producto}/variantes', [VarianteController::class, 'store']);
    Route::delete('/variantes/{id}', [VarianteController::class, 'destroy']);
    Route::post('/variantes/{id_variante}/actualizar', [VarianteController::class, 'actualizar']);
    Route::get('/variantes/{id}/edit', [VarianteController::class, 'edit']);
Route::put('/variantes/{id}', [VarianteController::class, 'update']);

    Route::post('/variantes/{id_variante}/precios', [PrecioController::class, 'store']);
    Route::post('/variantes/{id_variante}/stocks', [StockController::class, 'store']);

    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::get('/categorias/create', [CategoriaController::class, 'create']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::get('/categorias/{id}/edit', [CategoriaController::class, 'edit']);
    Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/perfil', [HomeController::class, 'perfil']);
    Route::post('/categorias/ajax', [CategoriaController::class, 'storeAjax']);

});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/empleados', [EmpleadoController::class, 'index']);
    Route::get('/empleados/create', [EmpleadoController::class, 'create']);
    Route::post('/empleados', [EmpleadoController::class, 'store']);
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy']);
    Route::get('/empleados/{id}/edit', [EmpleadoController::class, 'edit']);
Route::put('/empleados/{id}', [EmpleadoController::class, 'update']);
Route::get('/dashboard', [DashboardController::class, 'index']);
});