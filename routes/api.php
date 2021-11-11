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

$router->post('send-notif', [
    'as' => 'notification', 'uses' => 'NotificationController@send'
]);

$router->post('save-token', [
    'as' => 'user.saveFCM', 'uses' => 'UserController@saveToken'
]);

$router->post('del-token', [
    'as' => 'user.delFCM', 'uses' => 'UserController@logoutToken'
]);

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
    
        $router->group(['middleware' => 'role:admin'], function () use ($router) {
            $router->get('', [
                'as' => 'user.index', 'uses' => 'UserController@index'
            ]);

            $router->get('{id}', [
                'as' => 'user.show', 'uses' => 'UserController@show'
            ]);

            $router->post('', [
                'as' => 'user.store', 'uses' => 'UserController@store'
            ]);

            $router->put('{id}', [
                'as' => 'user.update', 'uses' => 'UserController@update'
            ]);

            $router->delete('{id}', [
                'as' => 'user.destroy', 'uses' => 'UserController@destroy'
            ]);
        });
    });

    $router->group(['prefix' => 'hosts'], function () use ($router) {
        $router->get('', [
            'as' => 'hosts.index', 'uses' => 'HostController@index'
        ]);
        $router->group(['middleware' => 'role:admin'], function () use ($router) {
          
            $router->get('{id}', [
                'as' => 'hosts.show', 'uses' => 'HostController@show'
            ]);

            $router->get('{id}/appointments', [
                'as' => 'host.getAppointments', 'uses' => 'HostController@getAppointments'
            ]);

        });
    });

    $router->group(['prefix' => 'guests'], function () use ($router) {
        $router->group(['middleware' => 'role:admin'], function () use ($router) {
            $router->get('', [
                'as' => 'guest.index', 'uses' => 'GuestController@index'
            ]);

            $router->get('{id}', [
                'as' => 'guest.show', 'uses' => 'GuestController@show'
            ]);

            $router->post('', [
                'as' => 'guest.store', 'uses' => 'GuestController@store'
            ]);

            $router->get('{id}/appointments', [
                'as' => 'guest.getAppointments', 'uses' => 'GuestController@getAppointments'
            ]);
        });
    });

    $router->group(['prefix' => 'appointments'], function () use ($router) {
        $router->get('', [
            'as' => 'appointment.index', 'uses' => 'AppointmentController@index'
        ]);

        $router->put('{id}', [
            'as' => 'appointment.update', 'uses' => 'AppointmentController@update',
        ]);

      
        $router->group(['middleware' => 'role:admin'], function () use ($router) {
            $router->post('', [
                'as' => 'appointment.store', 'uses' => 'AppointmentController@store',
            ]);


            $router->get('{id}', [
                'as' => 'appointment.show', 'uses' => 'AppointmentController@show',
            ]);


            $router->delete('{id}', [
                'as' => 'appointment.destroy', 'uses' => 'AppointmentController@destroy'
            ]);
        });
    });
    
    $router->group(['prefix' => 'utils'], function () use ($router) {
        $router->post('send_email/{id}',[
            'as' => 'appointment.send_email', 'uses' => 'AppointmentController@sendEmail',   
        ]);
        $router->group(['middleware' => 'role:admin'], function () use ($router) {
            
            $router->get('export_excel', [
                'as' => 'appointment.export', 'uses' => 'AppointmentController@export_excel',
            ]);
            
            $router->post('scan_ktp', [
                'as' => 'scan_ktp', 'uses' => 'AppointmentController@scan_ktp',
            ]);
        });
    });

});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('loginHost', [
        'as' => 'login', 'uses' => 'AuthController@loginHost'
    ]);

    $router->post('loginAdmin', [
        'as' => 'login', 'uses' => 'AuthController@loginAdmin'
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
});
