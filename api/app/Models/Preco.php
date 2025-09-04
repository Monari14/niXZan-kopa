<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Preco extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_produto',
        'valor',
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
