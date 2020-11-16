<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MercadoPagoController extends Controller
{
    //?collection_id=25898698&collection_status=approved&external_reference=2082_2013_11&payment_type=credit_card&merchant_order_id=1337765396&preference_id=188740775-40899cce-2fca-4b4d-9284-f8b7bd23ba66&site_id=MLA&processing_mode=aggregator&merchant_account_id=null
    public function success(Request $request)
    {
    	$external_reference = $request->query('external_reference');
    	
    	return view('mercadopago.success');
    }
    public function pending(Request $request)
    {
    	return view('mercadopago.pending');
    }
    public function failure(Request $request)
    {
    	return view('mercadopago.failure');
    }
}
