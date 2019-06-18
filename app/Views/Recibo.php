<?php

namespace App\Views;

use Illuminate\Database\Eloquent\Model;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

class Recibo extends Model
{

    use Eloquence, Mappable;

    protected $table ='view_os_recibospdf';
    protected $primaryKey = 'nro_Recibo';
    public $incrementing = false;
    public $timestamps = false;

    protected $hidden = [
        'nro_afiliado',
        'nro_Recibo',
        'recibopdf',
        'Tipo',
    ];

    protected $maps = [
        'id_afiliado' => 'nro_afiliado',
        'id_recibo' => 'nro_Recibo',
        'tipo' => 'Tipo',
    ];

    protected $appends = [
        'id_afiliado',
        'id_recibo',
        'tipo',
    ];

}
