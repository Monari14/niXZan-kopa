<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preco extends Model
{
    protected $fillable = [
        'id_produto',
        'valor',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
