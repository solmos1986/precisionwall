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
//auth
Route::post('/login', 'Api\AuthController@login')->name('api.login');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->post('/logout', 'Api\AuthController@logout');

//ticket
Route::get('/ticket', 'Api\TicketController@index')->name('api.listar.tickets');
Route::get('/ticket/classWorker', 'Api\TicketController@get_class_workers')->name('api.listar.classWorker');
Route::get('/ticket/personal', 'Api\TicketController@allPersonal')->name('api.listar.personal');
Route::get('/ticket/{tipo}', 'Api\TicketController@question')->name('api.listar.question');
Route::get('/ticket/materiales/{id}', 'Api\TicketController@materiales')->name('api.listar.materiales');
Route::post('/ticket/list-activitis', 'Api\ActivityController@index');
Route::post('/ticket/list-ticket/{id}', 'Api\TicketController@index');
Route::post('/ticket/{id}/store', 'Api\TicketController@store');
Route::get('/ticket/edit/{id}', 'Api\TicketController@edit');
Route::post('/ticket/update/{id}', 'Api\TicketController@update');
Route::delete('/ticket/delete/{id}', 'Api\TicketController@destroy');
//asistencia

//recursos app base de datos
Route::get('/db-app', 'Api\AppDatabase@createDataBase');

Route::get('/materiales', 'Api\AppDatabase@get_materiales');
Route::get('/report-personal', 'Api\AppDatabase@get_report_personal');
Route::get('/area-control', 'Api\AppDatabase@area_control');
Route::get('/task', 'Api\AppDatabase@area_task');

Route::post('/db-app', 'Api\Sincronizacion@index'); // syncronizacion

Route::get('/images', 'Api\AppDatabase@loadImages');

Route::get('/ticket/send_ticket/{id}', 'Api\TicketController@get_mail');
Route::post('/ticket/{id}/send', 'Api\TicketController@sendmailticket');
Route::get('/get_images/{id}/{type}/ticket', 'Api\TicketController@get_images');
Route::post('/upload_image/{id}/{type}/ticket/{nombre_camp?}', 'Api\TicketController@upload_image');

Route::get('/test', 'Api\AppDatabase@test');

/*Asistencia */
Route::post('/asistencia/list-activitis', 'Api\ActivityController@actividadesAsistencia');
Route::post('/asistencia/personal', 'Api\AsistenciaController@personalActividad');
Route::post('/asistencia/loginCheck', 'Api\AsistenciaController@loginCheckInOut');
Route::post('/asistencia/check', 'Api\AsistenciaController@SaveRegistroDiarioCheck');

Route::post('/asistencia/report-personal', 'Api\AsistenciaController@get_new_report_personal');
Route::post('/asistencia/area-control', 'Api\AsistenciaController@area_control');
Route::post('/asistencia/task', 'Api\AsistenciaController@area_task');
Route::post('/asistencia/save-report-personal', 'Api\AsistenciaController@store_report_personal');
/* visit report */
Route::prefix('visit-report')->group(function () {
    Route::post('/', 'Api\VisitReportController@list_visit_report');
    Route::get('get-projects', 'Api\VisitReportController@get_proyecto');
    /* preguntas */
    Route::post('where', 'Api\VisitReportController@get_areas');
    /*cometario */
    Route::get('problem', 'Api\VisitReportController@get_problema');
    Route::post('consequense', 'Api\VisitReportController@get_consecuencia');
    Route::post('solution', 'Api\VisitReportController@get_solucion');
    /* crud */
    Route::post('/store', 'Api\VisitReportController@save_visit_report');
    Route::get('/images/{id}', 'Api\VisitReportController@get_images');
    Route::post('store', 'Api\VisitReportController@save_visit_report');
    Route::get('edit/{id}', 'Api\VisitReportController@get_visit_report');
    Route::post('update/{id}', 'Api\VisitReportController@update_visit_report');
    Route::delete('delete/{id}', 'Api\VisitReportController@delete_visit_report');
    Route::get('get-config-mail/{id}/{proyect}', 'Api\VisitReportController@get_config_mail');
    Route::post('send/{id}/{part}/goal', 'Api\VisitReportController@sendmailgoal');
});
