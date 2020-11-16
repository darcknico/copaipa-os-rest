<?php

namespace App\PaymentMethods;

use App\Models\PagoMercadoPago;

use Illuminate\Http\Request;
use MercadoPago\Item;
use MercadoPago\MerchantOrder;
use MercadoPago\Payer;
use MercadoPago\Payment;
use MercadoPago\Preference;
use MercadoPago\SDK;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreferenciaPagoAprobado;
use Carbon\Carbon;
use App\Functions\AuxiliarFunction;

class MercadoPago
{
  public function __construct()
  {
    SDK::setAccessToken(
      config("payment-methods.mercadopago.access_token")
    );
    SDK::setClientId(
      config("payment-methods.mercadopago.client")
    );
    SDK::setClientSecret(
      config("payment-methods.mercadopago.secret")
    );
    
  }

  /*
  id
  preference_id
  payment_id
  payment_status
  */
  public function setupPaymentAndGetRedirectURL($order)
  {
    $pago = PagoMercadoPago::where('id_afiliado',$order['deuda']->id_afiliado)
      ->where('anio',$order['deuda']->anio)
      ->where('mes',$order['deuda']->mes)
      ->first();
    $preference = null;
    if($pago){
      //188740775-7e92fe30-9710-4115-a2c5-bd2b27790e19
      if($pago->preference_id){
        $preference = Preference::find_by_id($pago->preference_id);
      }
      //$preference = Preference::find_by_id('188740775-7e92fe30-9710-4115-a2c5-bd2b27790e19');
    } else {
      
      $pago = new PagoMercadoPago;
      $pago->id_afiliado = $order['deuda']->id_afiliado;
      $pago->anio = $order['deuda']->anio;
      $pago->mes = $order['deuda']->mes;
      $pago->importe_pagado = $order['monto'];
      $pago->fecha_inicio_pago = Carbon::now();
      $pago->save();
    }

    if(!$preference){
      # Create a preference object
      $preference = new Preference();

      # Create an item object
      $item = new Item();
      $item->id = $pago->id;
      $item->title = $order['title'];
      $item->quantity = 1;
      $item->currency_id = 'ARS';
      $item->unit_price = $order['monto'];
      //$item->unit_price = 1;
      $item->picture_url = url('/img/logo.png');

      # Create a payer object
      $payer = new Payer();
      $payer->email = $order['email'];

      # Setting preference properties
      $preference->items = [$item];
      $preference->payer = $payer;

      # Save External Reference
      $preference->external_reference = $pago->id;
      //$preference->external_reference = $order['id'];
      $preference->back_urls = [
        "success" => route('mercadopago.success'),
        "pending" => route('mercadopago.pending'),
        "failure" => route('mercadopago.failure'),
      ];

      $preference->auto_return = "all";
      //$preference->notification_url = "https://f2a9846d358e.ngrok.io/api/mercadopago/ipn";
      $preference->notification_url = route('mercadopago.ipn');

      $preference->save();

      $pago->preference_id = $preference->id;
      $pago->save();
    }
    
    $url = $this->getPreferenceUrl($preference);

    return [
      'url' => $url,
      'pago' => $pago,
    ];
  }

  public function getPreferenceUrl($preference){
    if (AuxiliarFunction::is_true(config('payment-methods.use_sandbox'))) {
      $url = $preference->sandbox_init_point;
    } else {
      $url = $preference->init_point;
    }
    return $url;
  }

  /*
  OBTIENE EL METODO DE PAGO ELEGIDO Y EL ESTADO
  */
  public function checkPaymentRequest($request){
    /*
    WEEBHOOK
    */
    $mp_id = $request->input('id');
    $mp_live_mode = $request->input('live_mode');
    $mp_type = $request->input('type');
    $mp_date_created = $request->input('date_created');
    $mp_application_id = $request->input('application_id');
    $mp_user_id = $request->input('user_id');
    $mp_version = $request->input('version');
    $mp_api_version = $request->input('api_version');
    $mp_action = $request->input('action');
    $mp_data = $request->input('data');

    /*
    IPN
    */
    $ipn_id = $request->query('id');
    $ipn_resource = $request->input('resource');
    $ipn_topic = $request->input('topic');

    $pago = null;
    switch($mp_type) {
        case "payment":
            $response = Payment::find_by_id($mp_data['id']);
            if($response){
              $pago = PagoMercadoPago::find($response->external_reference);
              if($pago){
                if($pago->payment_status != 'approved' and $response->status == 'approved'){
                  $pago->fecha_pago = Carbon::now();
                  $preference = Preference::find_by_id($pago->preference_id);
                  if($preference){
                    $email = $preference->payer->email;
                    $to = [
                    [
                        'email' => $email, 
                        'name' => 'OSCOPAIPA',
                    ]
                    ];
                    Mail::to($to)
                        ->send(new PreferenciaPagoAprobado($pago));
                  }
                }
                $pago->payment_id = $response->id;
                $pago->payment_status = $response->status;
                $pago->save();
              }
            }
            break;
    }
    switch ($ipn_topic) {
      case 'topic':
        $response = MerchantOrder::find_by_id($ipn_id);
        break;
    }
    return $pago;
  }

  /*
  ACTUALIZA EL ESTADO DEL PAGO
  */
  public function checkPaymentStatus($id){
    $response = Payment::find_by_id($id);
    $pago = null;
    if($response){
      $pago = PagoMercadoPago::find($response->external_reference);
      $estado_previo = $pago->payment_status;
      $pago->payment_id = $response->id;
      $pago->payment_status = $response->status;
      $pago->save();

      if($pago and $estado_previo != 'approved' and $pago->payment_status == 'approved'){
          //NOTIFICAR PAGO REALIZADO
          $preference = Preference::find_by_id($pago->preference_id);
          $pago->fecha_pago = Carbon::now();
          $pago->save();

          if($preference){
              $email = $preference->payer->email;
              $to = [
              [
                  'email' => $email, 
                  'name' => 'OSCOPAIPA',
              ]
              ];
              Mail::to($to)
                  ->send(new PreferenciaPagoAprobado($pago));
              $pago->email = $email;
          }
      }
    }
    return $pago;
  }

  public function checkPreferenceStatus(PagoMercadoPago $pago){
    $preference = Preference::find_by_id($pago->preference_id);
    if($preference){
      $pago->preference_url = $this->getPreferenceUrl($preference);
      if($preference->payer){
        $pago->email = $preference->payer->email;
      }
    }
    return $pago;
  }

}