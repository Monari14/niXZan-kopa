<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;

Route::prefix('/v1')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']); //
        Route::post('/login', [AuthController::class, 'login']); //
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('/auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']); //
        });

        Route::prefix('/cliente')->group(function () {
            Route::get('/logado', [AuthController::class, 'userLogado']); //
            Route::put('/', [AuthController::class, 'update']); //
            Route::delete('/', [AuthController::class, 'destroy']); //

            Route::prefix('/pedidos')->group(function () {
                Route::get('/meus', [PedidoController::class, 'meusPedidos']); //
                Route::post('/novo', [PedidoController::class, 'novoPedido']); //
                Route::post('/confirmar', [PedidoController::class, 'confirmar']); //
                Route::get('/carrinho', [PedidoController::class, 'carrinho']); //
                Route::post('/cancelar', [PedidoController::class, 'cancelar']); //
                Route::post('/refazer', [PedidoController::class, 'refazer']); //
            });
        });
    });
});
