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
        'imagem',
    ];

    protected $appends = ['img_url'];

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

    public function getImgUrlAttribute()
    {
        if ($this->imagem && file_exists(storage_path('app/public/' . $this->imagem))) {
            return asset('s/' . $this->imagem);
        }
        return asset('i/produto-padrao.png');
    }
}
