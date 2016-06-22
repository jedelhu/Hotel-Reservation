<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::resource('payment', 'PaymentController');

Route::group(['prefix' => 'api'], function() {
    Route::post('searchavailabity', 'RoomCalendarController@searchAvailability');
    Route::post('createreservation', 'ReservationController@createReservation');
    Route::get('reservation/{id}', 'ReservationController@show');

    Route::get('todayreservations', 'ReservationController@getTodayReservation');
    Route::post('checkout', 'ReservationController@getCheckout');



});

Route::group(['prefix' => 'adminapi'], function()
{
    Route::resource('room_type', 'RoomTypeController');
    Route::post('setpriceinrange', 'RoomCalendarController@setPriceInRangeForRoomType');
    Route::get('customer', 'CustomerController@index');
    Route::get('customer', 'CustomerController@index');
    Route::post('order', 'OrderController@store');
    Route::get('getorder', 'OrderController@index');
});