<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'tipo',
        'preco_base',
        'estoque',
        'img_url',
    ];

    // Caso tenha uma tabela separada de preços:
    public function precos()
    {
        return $this->hasMany(Preco::class);
    }

    // Método para diminuir o estoque do produto
    public function diminuirEstoque(int $quantidade)
    {
        if ($this->estoque >= $quantidade) {
            $this->estoque -= $quantidade;
            $this->save();
            return true;
        }
        return false;
    }
}
