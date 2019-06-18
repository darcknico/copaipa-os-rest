<?php

namespace App\Views;

use Illuminate\Database\Eloquent\Model;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

class Aporte extends Model
{

    use Eloquence, Mappable;

    protected $table ='viewapp_aportes';
    protected $primaryKey = 'nro_Recibo';
    public $incrementing = false;
    public $timestamps = false;

    protected $hidden = [
        'nro_Recibo',
        'nro_afiliado',
        'cuotasImpactadas',
        'totalImporteAsignado',
    ];

    protected $maps = [
        'id_recibo' => 'nro_Recibo',
        'id_afiliado' => 'nro_afiliado',
        'cuotas_impactadas' => 'cuotasImpactadas',
        'total_importe_asignado' => 'totalImporteAsignado',
    ];

    protected $appends = [
        'id_recibo',
        'id_afiliado',
        'cuotas_impactadas',
        'total_importe_asignado',
    ];

}
