<?php

namespace App\Events;

use App\Models\PagoMercadoPago;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PagoMercadoPagoModificado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_pago_mercadopago;
    public $pago;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PagoMercadoPago $pago,)
    {
        $this->id_pago_mercadopago = $pago->id;
        $this->pago = $pago;
    }

}
