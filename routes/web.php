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

Route::get('/generateToken', 'InstagramController@generateToken');
Route::get('/clientCodeHandler', 'InstagramController@clientCodeHandle');
Route::get('/getMedia', 'InstagramController@getMedia');
Route::get('/refreshToken', 'InstagramController@refreshToken');
Route::get('/getMediaFromFile', 'InstagramController@getMediaFromFile');
Route::post('/saveUserData', 'InstagramController@saveUserData');