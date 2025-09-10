<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\EntregadorController;

Route::prefix('/v1')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']); // --
        Route::post('/login', [AuthController::class, 'login']); // --
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('/auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']); // --
            Route::get('/user/logado', [AuthController::class, 'userLogado']); //
        });

        Route::prefix('/cliente')->group(function () {
            Route::put('/', [AuthController::class, 'update']); //
            Route::delete('/', [AuthController::class, 'destroy']); //

            Route::prefix('/pedidos')->group(function () {
                Route::get('/meus', [PedidoController::class, 'meusPedidos']); // --
                Route::post('/novo', [PedidoController::class, 'novoPedido']); // --
                Route::post('/confirmar', [PedidoController::class, 'confirmar']); // --
                Route::get('/carrinho', [PedidoController::class, 'getarCarrinho']); // --
                Route::post('/cancelar', [PedidoController::class, 'cancelar']); //
                Route::post('/refazer', [PedidoController::class, 'refazer']); //
                Route::post('/{id}', [PedidoController::class, 'tempoExpiradoParaCancelar']); //
            });
        });

        Route::prefix('/admin')->group(function () {
            Route::prefix('/produtos')->group(function () {
                Route::get('/', [ProdutoController::class, 'index']); // --
                Route::post('/', [ProdutoController::class, 'store']); // --
                Route::get('/{id}', [ProdutoController::class, 'show']); //
                Route::put('/{id}', [ProdutoController::class, 'update']); // --
                Route::delete('/{id}', [ProdutoController::class, 'destroy']); // --
            });
            Route::prefix('/pedidos')->group(function () {
                Route::get('/', [PedidoController::class, 'todosPedidos']); // --
            });
            Route::prefix('/clientes')->group(function () {
                Route::get('/todos', [AuthController::class, 'todosClientes']); // --
            });
            Route::prefix('/entregadores')->group(function () {
                Route::get('/todos', [AuthController::class, 'todosEntregadores']); // --
            });
        });

        Route::prefix('/entregador')->group(function () {
            Route::prefix('/pedidos')->group(function () {
                Route::get('/', [EntregadorController::class, 'pedidosEsperandoRetirada']); // --
                Route::get('/entregas', [EntregadorController::class, 'todasMinhasEntregas']); // --
                Route::post('/aceitar', [EntregadorController::class, 'aceitarEntrega']); // --
                Route::post('/finalizar', [EntregadorController::class, 'finalizarEntrega']); // --
            });
        });
    });
});
