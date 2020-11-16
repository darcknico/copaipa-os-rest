<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    //Route::post('register', 'AuthController@register');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    //Route::post('recovery', 'AuthController@recovery');
    //Route::post('password', 'AuthController@password');
});
Route::post('mercadopago/webhook','MercadoPagoController@webhook')->name('mercadopago.webhook');
Route::post('mercadopago/ipn','MercadoPagoController@webhook')->name('mercadopago.ipn');

Route::group(['middleware' => 'jwt'], function(){

    Route::post('mercadopago','MercadoPagoController@crearPreferencia');
    Route::get('mercadopago/{id_pago_mercado_pago}','MercadoPagoController@actualizar')->where('id_pago_mercado_pago','[0-9]+');
    Route::delete('mercadopago/{id_pago_mercado_pago}','MercadoPagoController@eliminar')->where('id_pago_mercado_pago','[0-9]+');

	Route::get('aportes', 'AporteController@index');
    Route::get('deudas', 'DeudaController@index');
    Route::get('deudas/ultimo', 'DeudaController@ultimo');
	Route::post('deudas/obtener', 'DeudaController@show');

	Route::get('recibos', 'ReciboController@index');
	Route::post('recibos/{id_recibo}/reporte', 'ReciboController@reporte')->where('id_recibo','[0-9]+');

	Route::prefix('novedades')->group(function(){
        Route::get('', 'NovedadController@index');
    });

});

/*
Route::get('redis',function(){

    $isDatabaseReady = App\Helpers\ConnectionChecker::isDatabaseReady();
    $isRedisReady = App\Helpers\ConnectionChecker::isRedisReady();
    return response()->json([
        'isDatabaseReady' => $isDatabaseReady,
        'isRedisReady' => $isRedisReady,
    ]);
});
*/