<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','Web\HomeController@home')->name('home');

Route::get('/app/payment/success','Web\MercadoPagoController@success')->name('mercadopago.success');
Route::get('/app/payment/pending','Web\MercadoPagoController@pending')->name('mercadopago.pending');
Route::get('/app/payment/failure','Web\MercadoPagoController@failure')->name('mercadopago.failure');

