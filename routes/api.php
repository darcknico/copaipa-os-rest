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

Route::group(['middleware' => 'jwt'], function(){
	Route::get('aportes', 'AporteController@index');
	Route::get('deudas', 'DeudaController@index');
	Route::get('recibos', 'ReciboController@index');
	Route::post('recibos/{id_recibo}/reporte', 'ReciboController@reporte')->where('id_recibo','[0-9]+');

	Route::prefix('novedades')->group(function(){
		Route::get('', 'NovedadController@index');
	});

});