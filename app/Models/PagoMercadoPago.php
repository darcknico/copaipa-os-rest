<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;
/**
nro_afiliado
anio
mes
importe_pagado
fecha_pago
*/
class PagoMercadoPago extends Model
{
	use Eloquence, Mappable;
    protected $table ='os_app_pagosmercadopago';

    public $timestamps = false;

    protected $casts = [
        'importe_pagado' => 'float',
    ];

    protected $hidden = [
        'nro_afiliado',
    ];

    protected $maps = [
        'id_afiliado' => 'nro_afiliado',
    ];

    protected $appends = [
        'id_afiliado',
    ];
}
