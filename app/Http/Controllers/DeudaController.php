<?php

namespace App\Http\Controllers;

use App\Views\Deuda;

use Illuminate\Http\Request;
use Carbon\Carbon;

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
		return response()->json([
			'total_count'=>intval($total_count),
			'items'=>$registros,
		],200);
	}

}