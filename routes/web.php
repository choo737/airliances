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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/redirect/{socialservice}', 'SocialAuthFacebookController@redirect');
Route::get('/callback/{socialservice}', 'SocialAuthFacebookController@callback');

Route::get('/bookings/new', 'BookingController@create');
Route::post('/bookings', 'BookingController@store');
Route::get('/mybookings', 'BookingController@index');
Route::get('/home', 'HomeController@index')->name('home');
