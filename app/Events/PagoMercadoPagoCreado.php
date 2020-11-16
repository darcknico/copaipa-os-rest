<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PagoMercadoPagoCreado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_pago_mercadopago;
    public $id_deuda;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id_pago_mercadopago,$id_deuda)
    {
        $this->id_pago_mercadopago = $id_pago_mercadopago;
        $this->id_deuda = $id_deuda;
    }

}
