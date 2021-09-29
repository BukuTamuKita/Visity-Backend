<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/user','UserController@index');
Route::get('/host','HostController@index');
Route::get('/appointment','AppointmentController@index');
Route::get('/guest','GuestController@index');
Route::get('/user/{id}','UserController@show');

Route::post('/host/create','HostController@store');


// $router->group(['prefix' => 'auth'], function () use ($router) {
//     $router->post('login', [
//         'as' => 'auth.login', 'uses' => 'AuthController@login'
//     ]);

//     $router->post('logout', [
//         'as' => 'auth.logout', 'uses' => 'AuthController@logout'
//     ]);

//     $router->post('refresh', [
//         'as' => 'auth.refresh', 'uses' => 'AuthController@refresh'
//     ]);

//     $router->post('me', [
//         'as' => 'auth.me', 'uses' => 'AuthController@me'
//     ]);
// });