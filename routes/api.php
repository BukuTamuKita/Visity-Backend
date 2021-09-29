<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group([

//     // 'middleware' => 'api',
//     'prefix' => 'auth'

// ], function ($router) {

//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('me', 'AuthController@me');

// });

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('loginHost', [
        'as' => 'auth.login', 'uses' => 'AuthController@loginHost'
    ]);

    $router->post('loginAdmin', [
        'as' => 'auth.loginAdmin', 'uses' => 'AuthController@loginAdmin'
    ]);

    $router->post('logout', [
        'as' => 'auth.logout', 'uses' => 'AuthController@logout'
    ]);

    $router->post('refresh', [
        'as' => 'auth.refresh', 'uses' => 'AuthController@refresh'
    ]);

    $router->post('me', [
        'as' => 'auth.me', 'uses' => 'AuthController@me'
    ]);

    $router->post('register', [
        'as' => 'auth.register', 'uses' => 'AuthController@register'
    ]);
});

