<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_entregador')->nullable();
            $table->string('endereco');
            $table->string('forma_pagamento');
            $table->decimal('troco', 8, 2)->nullable();
            $table->decimal('total', 8, 2)->default(0);
            $table->json('itens_pedido');
            $table->string('status')->default('preparando');
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_entregador')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
