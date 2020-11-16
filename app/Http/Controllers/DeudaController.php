<?php

namespace App\Http\Controllers;

use App\Models\PagoMercadoPago;
use App\Views\Deuda;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DeudaController extends Controller
{
	public function index(Request $request){
		$user = $request->user();
		$start = $request->query('start',"");
		$length = $request->query('length',"");
		$registros = Deuda::where('id_afiliado',$user->id)->orderBy('anio','desc')->orderBy('mes','desc');

		if(strlen($start)==0 or strlen($length)==0 ){
			return response()->json($registros->get());
		}

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
		$salida = [];

		foreach ($registros as $registro) {
			$registro->mercadopago = PagoMercadoPago::where('id_afiliado',$user->id)
				->where('anio',$registro->anio)
				->where('mes',$registro->mes)
				->first();
			$salida[] = $registro;
		}
		return response()->json([
			'total_count'=>intval($total_count),
			'items'=>$salida,
		],200);
	}

	public function ultimo(Request $request){
		$user = $request->user();

		$registro = DB::select('
			SELECT 
				ROUND(importe + os_interes_mora_cuota(mes, anio, nro_afiliado) - cobrado) AS a_pagar,
				importe, 
				cobrado, 
				mes, 
				anio, 
				nro_afiliado AS id_afiliado, 
				tipo,
				os_interes_mora_cuota(mes, anio, nro_afiliado) as interes
			FROM view_os_deudas
			WHERE nro_afiliado = ?
			HAVING (importe + os_interes_mora_cuota(mes, anio, nro_afiliado) - cobrado) > 0
			ORDER BY anio ASC , mes ASC',[
            $user->id,
        ]);
        $cantidad = count($registro);
		$registro = $registro[0]??null;
		if($registro){
			$registro->mercadopago = PagoMercadoPago::where('id_afiliado',$user->id)
				->where('anio',$registro->anio)
				->where('mes',$registro->mes)
				->first();
		}
		return response()->json([
			'deuda' => $registro,
			'cantidad' => $cantidad,
		]);
	}

	public function show(Request $request){
		$user = $request->user();

		$anio = $request->input('anio');
		$mes = $request->input('mes');

		$deuda = Deuda::where('id_afiliado',$user->id)
			->where('anio',$anio)
			->where('mes',$mes)
			->first();
		if($deuda){
			$deuda->mercadopago = PagoMercadoPago::where('id_afiliado',$user->id)
				->where('anio',$anio)
				->where('mes',$mes)
				->first();
		}

		return response()->json($deuda);
	}

}