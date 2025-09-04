<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrinho extends Model
{
    use HasFactory;

    // Nome da tabela no banco
    protected $table = 'itens_carrinho';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'id_user',
        'itens_pedido',
        'total',
    ];

    // Cast para JSON → array automático
    protected $casts = [
        'itens_pedido' => 'array',
    ];

    // Relacionamento com usuário
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
