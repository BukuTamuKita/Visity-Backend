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

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users', 'middleware' => 'role:admin'], function () use ($router) {
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

    $router->group(['prefix' => 'departments', 'middleware' => 'role:admin'], function () use ($router) {
        $router->get('subjects', [
            'as' => 'department.getAllSubjects', 'uses' => 'DepartmentController@getAllSubjects'
        ]);

        $router->get('students', [
            'as' => 'department.getAllStudents', 'uses' => 'DepartmentController@getAllStudents'
        ]);

        $router->get('', [
            'as' => 'department.index', 'uses' => 'DepartmentController@index'
        ]);

        $router->get('{id}', [
            'as' => 'department.show', 'uses' => 'DepartmentController@show'
        ]);

        $router->get('{id}/students', [
            'as' => 'department.getStudents', 'uses' => 'DepartmentController@getStudents'
        ]);

        $router->get('{id}/lecturers', [
            'as' => 'department.getLecturers', 'uses' => 'DepartmentController@getLecturers'
        ]);

        $router->get('{id}/subjects', [
            'as' => 'department.getSubjects', 'uses' => 'DepartmentController@getSubjects'
        ]);
    });

    $router->group(['prefix' => 'classrooms', 'middleware' => 'role:admin'], function () use ($router) {
        $router->get('', [
            'as' => 'classroom.index', 'uses' => 'ClassroomController@index'
        ]);

        $router->get('{id}', [
            'as' => 'classroom.show', 'uses' => 'ClassroomController@show'
        ]);

        $router->get('{id}/students', [
            'as' => 'classroom.getStudents', 'uses' => 'ClassroomController@getStudents'
        ]);

        $router->get('{id}/subjects', [
            'as' => 'classroom.getSubjects', 'uses' => 'ClassroomController@getSubjects'
        ]);
    });

    $router->group(['prefix' => 'schedules'], function () use ($router) {
        $router->get('summarize', [
            'as' => 'schedule.summarize', 'uses' => 'ScheduleController@summarize'
        ]);

        $router->get('', [
            'as' => 'schedule.index', 'uses' => 'ScheduleController@index'
        ]);

        $router->get('{id}', [
            'as' => 'schedule.show', 'uses' => 'ScheduleController@show',
            'middleware' => 'role:lecturer-admin'
        ]);

        $router->get('{id}/attendances', [
            'as' => 'schedule.getAttendances', 'uses' => 'ScheduleController@getAttendances'
        ]);

        $router->put('{id}/attendances', [
            'as' => 'schedule.editAttendances', 'uses' => 'ScheduleController@editAttendances',
            'middleware' => 'role:admin'
        ]);

        $router->delete('{id}/attendances', [
            'as' => 'schedule.removeAttendances', 'uses' => 'ScheduleController@removeAttendances',
            'middleware' => 'role:lecturer-admin'
        ]);

        $router->post('{id}/attend', [
            'as' => 'schedule.attend', 'uses' => 'ScheduleController@attend',
            'middleware' => 'role:student-admin'
        ]);

        $router->post('{id}/open', [
            'as' => 'schedule.open', 'uses' => 'ScheduleController@open',
            'middleware' => 'role:lecturer-admin'
        ]);

        $router->post('{id}/close', [
            'as' => 'schedule.close', 'uses' => 'ScheduleController@close',
            'middleware' => 'role:lecturer-admin'
        ]);
    });

    // TODO: Open for public for the time being
    $router->group(['prefix' => 'subjects'], function () use ($router) {
        $router->get('', [
            'as' => 'subject.index', 'uses' => 'SubjectController@index'
        ]);

        $router->get('{id}', [
            'as' => 'subject.show', 'uses' => 'SubjectController@show'
        ]);

        $router->get('{id}/students', [
            'as' => 'subject.getStudents', 'uses' => 'SubjectController@getStudents'
        ]);

        $router->get('{id}/schedules', [
            'as' => 'subject.getSchedules', 'uses' => 'SubjectController@getSchedules'
        ]);

        $router->get('{id}/attendances', [
            'as' => 'subject.getAttendances', 'uses' => 'SubjectController@getAttendances'
        ]);
    });

    $router->group(['prefix' => 'academic-calendars'], function () use ($router) {
        $router->group(['prefix' => 'types'], function () use ($router) {
            $router->get('', [
                'as' => 'academicCalendarType.index', 'uses' => 'AcademicCalendarTypeController@index'
            ]);

            $router->get('{id}', [
                'as' => 'academicCalendarType.show', 'uses' => 'AcademicCalendarTypeController@show'
            ]);
        });

        $router->get('', [
            'as' => 'academicCalendar.index', 'uses' => 'AcademicCalendarController@index'
        ]);

        $router->get('{id}', [
            'as' => 'academicCalendar.show', 'uses' => 'AcademicCalendarController@show'
        ]);
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

