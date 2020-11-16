<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreferenciaRequest;
use Illuminate\Http\Request;
use App\Models\PagoMercadoPago;
use App\Views\Deuda;
use App\Events\PagoMercadoPagoModificado;
use App\Events\PagoMercadoPagoCreado;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreferenciaPagoCreado;

use DB;

class MercadoPagoController extends Controller
{

	/*
		{
		    "id": 12345,
		    "live_mode": true,
		    "type": "payment",
		    "date_created": "2015-03-25T10:04:58.396-04:00",
		    "application_id": 123123123,
		    "user_id": 44444,
		    "version": 1,
		    "api_version": "v1",
		    "action": "payment.created",
		    "data": {
		        "id": "999999999"
		    }
		}
	*/
    public function webhook(Request $request)
    {
    	$mp_id = $request->input('id');
    	$mp_live_mode = $request->input('live_mode');
    	$mp_type = $request->input('type');
    	$mp_date_created = $request->input('date_created');
    	$mp_application_id = $request->input('application_id');
    	$mp_user_id = $request->input('user_id');
    	$mp_version = $request->input('version');
    	$mp_api_version = $request->input('api_version');
    	$mp_action = $request->input('action');

    	$response = $this->checkPaymentRequest($request);
    	return response()->json($response,200);
    }

    public function crearPreferencia(PreferenciaRequest $request)
	{

	    $id_afiliado = auth()->id();
	    $anio = $request->input('anio');
	    $mes = $request->input('mes');
	    $email = $request->input('email');
	    /*
	    $id_afiliado = 2082;
		$anio = 2013;
		$mes = 11;
		$email = 'nicolasrl2005@gmail.com';
		*/
		$deuda = Deuda::where('id_afiliado',$id_afiliado)
			->where('anio',$anio)
			->where('mes',$mes)
			->first();

		if(!$deuda){
			return response()->json([
				'message' => 'No existe la deuda',
			],403);
		}
		/*
		$ultimo = DB::select('
			SELECT ROUND(importe + os_interes_mora_cuota(mes, anio, nro_afiliado) - cobrado) AS a_pagar, importe, cobrado, mes, anio, nro_afiliado AS id_afiliado, tipo
			FROM view_os_deudas
			WHERE nro_afiliado = ? and mes = ? and anio = ?
			ORDER BY anio ASC , mes ASC
			LIMIT 1',[
            $id_afiliado,
            $mes,
            $anio,
        ]);
		$registro = $registro[0]??null;
		if(is_null($registro)){
			return response()->json([
				'message' => 'La deuda a generar no tiene un monto a pagar',
			],403);
		}
		
		if($ultimo){
			$ultimo = $ultimo[0];
			if($ultimo->mes == $deuda->mes and $ultimo->anio == $deuda->anio){

			} else {
				return response()->json([
					'message' => 'La deuda a generar la preferencia debe ser la ultima que tenga saldo a pagar',
				],403);
			}
		}
		*/

		$orden = [
			//'id' => $id_afiliado.'_'.$anio.'_'.$mes,
			'email' => $email,
			'title' => 'Cuota '.$mes.' '.$anio,
			'monto' => $deuda->a_pagar,
			'deuda' => $deuda,
			'anio' => $anio,
			'mes' => $mes,
		];

	    $preferencia = $this->generatePaymentGateway($orden);

	    $to = [
		    [
		        'email' => $email, 
		        'name' => '',
		    ]
		];
	    Mail::to($to)
		    ->send(new PreferenciaPagoCreado($orden,$preferencia['url']));

	    return response()->json($preferencia);
	}

	public function actualizar(Request $request){
		$id_pago_mercado_pago = $request->route('id_pago_mercado_pago');

		$pago = PagoMercadoPago::find($id_pago_mercado_pago);
		if($pago and $pago->payment_id){
			$pago = $this->checkPaymentStatus($pago->payment_id);
		} else {
			$pago = $this->checkPreferenceStatus($pago);
		}

		return response()->json($pago);
	}

	public function eliminar(Request $request){
		$id_pago_mercado_pago = $request->route('id_pago_mercado_pago');

		$pago = PagoMercadoPago::find($id_pago_mercado_pago);
		if($pago and $pago->payment_status != 'approved'){
			$pago->delete();
		}

		return response()->json($pago);
	}

	protected function generatePaymentGateway($orden)
	{
	    $method = new \App\PaymentMethods\MercadoPago;

	    return $method->setupPaymentAndGetRedirectURL($orden);
	}

	protected function checkPaymentRequest($request)
	{
		$method = new \App\PaymentMethods\MercadoPago;

	    return $method->checkPaymentRequest($request);
	}

	protected function checkPaymentStatus($payment_id)
	{
		$method = new \App\PaymentMethods\MercadoPago;

	    return $method->checkPaymentStatus($payment_id);
	}

	protected function checkPreferenceStatus($pago)
	{
		$method = new \App\PaymentMethods\MercadoPago;

	    return $method->checkPreferenceStatus($pago);
	}
}
