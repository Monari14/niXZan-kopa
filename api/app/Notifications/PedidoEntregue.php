<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PedidoEntregue extends Notification
{
    use Queueable;

    protected $entregador;
    protected $cliente;
    protected $id_pedido;

    public function __construct($entregador, $cliente, $id_pedido)
    {
        $this->entregador = $entregador;
        $this->cliente = $cliente;
        $this->id_pedido = $id_pedido;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Pedido #{$this->id_pedido} entregue!",
        ];
    }
}
