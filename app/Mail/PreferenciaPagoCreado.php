<?php

namespace App\Mail;

use App\Models\PagoMercadoPago;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Akaunting\Money\Money;

class PreferenciaPagoCreado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $preferencia_url;
    public $monto;
    public $anio;
    public $mes;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pago, $preferencia_url)
    {
        $this->preferencia_url = $preferencia_url;
        $this->monto = Money::ARS($pago['monto']*100);
        $this->anio = $pago['anio'];
        $this->mes = $pago['mes'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.preferencia.creado')
            ->from('copaipa.salta@gmail.com','OSCOPAIPA')
            ->subject("Pago cuota Obra Social COPAIPA")
            ->replyTo("no-replay@prueba.com","No Responder");
    }
}
