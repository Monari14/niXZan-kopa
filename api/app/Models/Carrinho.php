<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrinho extends Model
{

    protected $table = 'itens_carrinho';

    protected $fillable = [
        'id_user',
        'itens_pedido',
        'copao',
        'total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
