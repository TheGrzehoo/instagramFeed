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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/generateToken', 'InstagramController@generateToken');
Route::get('/getMedia', 'InstagramController@getMedia');
Route::get('/refreshToken', 'InstagramController@refreshToken');
Route::get('/getMediaFromFile', 'InstagramController@getMediaFromFile');
Route::post('/updateClient', 'ClientController@updateClient');
Route::get('/isClientSaved', 'ClientController@isClientSaved');