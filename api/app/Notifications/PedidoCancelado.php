<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PedidoCancelado extends Notification
{
    use Queueable;

    protected $cliente;
    protected $id_pedido;

    public function __construct($cliente, $id_pedido)
    {
        $this->cliente = $cliente;
        $this->id_pedido = $id_pedido;
    }

    public function via($notifiable)
    {
        return ['notifications'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Pedido #{$this->id_pedido} cancelado!.",
        ];
    }
}
