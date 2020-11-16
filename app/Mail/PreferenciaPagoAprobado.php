<?php

namespace App\Mail;

use App\Models\PagoMercadoPago;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Akaunting\Money\Money;

class PreferenciaPagoAprobado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $monto;
    public $anio;
    public $mes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PagoMercadoPago $pago)
    {
        $this->monto = Money::ARS($pago->importe_pagado*100);
        $this->anio = $pago->anio;
        $this->mes = $pago->mes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.preferencia.aprobado')
            ->from('copaipa.salta@gmail.com','OSCOPAIPA')
            ->subject("Pago cuota Obra Social COPAIPA")
            ->replyTo("no-replay@prueba.com","No Responder");
    }
}
