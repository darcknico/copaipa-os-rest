<?php

namespace App\Views;

use Illuminate\Database\Eloquent\Model;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

use DB;

class Deuda extends Model
{

    use Eloquence, Mappable;

    protected $table ='view_os_deudas';
    protected $primaryKey = 'nro_afiliado';
    public $incrementing = false;
    public $timestamps = false;

    protected $hidden = [
        'nro_afiliado',
        'Tipo',
    ];

    protected $maps = [
        'id_afiliado' => 'nro_afiliado',
        'tipo' => 'Tipo',
    ];

    protected $appends = [
        'id_afiliado',
        'interes',
        'tipo',
    ];

    protected function getInteresAttribute(){
        $todo = DB::select('SELECT os_interes_mora_cuota(mes, anio, nro_afiliado) AS interes FROM view_os_deudas WHERE nro_afiliado = ? and mes = ? and anio = ?',[
            $this['nro_afiliado'],
            $this['mes'],
            $this['anio'],
        ]);
        return $todo[0]->interes??0;
    }
}
