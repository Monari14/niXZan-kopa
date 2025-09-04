<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'id_user',
        'id_entregador',
        'endereco',
        'forma_pagamento',
        'troco',
        'total',
        'itens_pedido',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class, 'id_pedido');
    }
}
