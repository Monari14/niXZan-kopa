<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSettings extends Model
{
    protected $fillable = [
        'valor_adicional_pedido',
        'status_aberto',
        'horario_abertura',
        'horario_fechamento',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
