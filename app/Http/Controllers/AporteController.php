<?php

namespace App\Http\Controllers;

use App\Views\Aporte;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AporteController extends Controller
{
	public function index(Request $request){
		$user = $request->user();
		$start = $request->query('start',"");
		$length = $request->query('length',"");
		$registros = Aporte::where('id_afiliado',$user->id)->orderBy('fecha','desc');

		if(strlen($start)==0 or strlen($length)==0 ){
			return response()->json($registros->get());
		}
		$fecha_desde = $request->query('fecha_desde',null);
		$fecha_hasta = $request->query('fecha_hasta',null);
		$registros = $registros
			->when(!is_null($fecha_desde),function($q)use($fecha_desde){
				$q->whereDate('fecha','>=',$fecha_desde);
			})
			->when(!is_null($fecha_hasta),function($q)use($fecha_hasta){
				$q->whereDate('fecha','<=',$fecha_hasta);
			});
		$sql = $registros->toSql();
		$q = clone($registros->getQuery());
		$total_count = $q->count();
		if($length>0){
			$registros = $registros->limit($length);
			if($start>1){
				$registros = $registros->offset($start - 1)->get();
			} else {
				$registros = $registros->get();
			}
		} else {
			$registros = $registros->get();
		}
		return response()->json([
			'total_count'=>intval($total_count),
			'items'=>$registros,
		],200);
	}

}