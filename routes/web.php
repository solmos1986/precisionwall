<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routesshow-form-staff
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::get('/', 'LoginController@Showloginform')->name('showloginform');
Route::get('auto-login/{id}', 'LoginController@auto_login')->name('auto.login.foreman');
Route::get('auto-login-submittals', 'LoginController@auto_login_submittals')->name('auto.login.foreman');
Route::post('login', 'LoginController@login')->name('login');
Route::post('logout', 'LoginController@logout')->name('logout');

Route::get('home', 'HomeController@index')->name('home');

/** tickets */
Route::get('activities', 'ActividadController@index')->name('listar.actividades')->middleware('ticket');
Route::get('tickets/{id}', 'TicketController@index')->name('listar.tickets');
Route::get('new/{id}/ticket', 'TicketController@create')->name('crear.ticket');
Route::post('store/{id}/ticket', 'TicketController@store')->name('store.ticket');
Route::put('update/{id}/ticket', 'TicketController@update')->name('update.ticket');
Route::get('show/{id}/ticket', 'TicketController@show')->name('show.ticket');
Route::get('show-btn/ticket', 'TicketController@show_btn')->name('show.ticket.btn');
Route::get('edit/{id}/ticket', 'TicketController@edit')->name('edit.ticket');
Route::get('pdf/{id}/ticket', 'TicketController@pdf')->name('pdf.ticket');
Route::delete('ticket/{id}/destroy', 'TicketController@destroy');
Route::post('update_signature/{id}/ticket', 'TicketController@update_signature')->name('update_signature.ticket');
Route::get('tickets', 'TicketController@index2')->name('listar.mis.tickets');
Route::get('get_mails', 'TicketController@get_mails')->name('get_mails');
Route::get('get_images/{id}/{type}/ticket', 'TicketController@get_images')->name('listar.image');
Route::post('upload_image/{id}/{type}/ticket/{nombre_camp?}', 'TicketController@upload_image')->name('upload.image');
Route::post('delete_image/{id}/{type}/ticket', 'TicketController@delete_image')->name('delete.image');
Route::get('get_config_mail/{id}/ticket', 'TicketController@get_config_mail')->name('get_config_mail');
Route::post('post_config_mail/multiples-ticket', 'TicketController@multiple_post_config_mail')->name('multiple_post_config_mail');
Route::post('get_materiales/{id}', 'TicketController@get_materiales')->name('get_materiales');
Route::post('get_razon/{tipo}/{id}', 'TicketController@get_razon')->name('get_razon');
Route::post('send/{id}/ticket', 'TicketController@sendmailticket')->name('sendmailticket');
Route::post('send/multiple-ticket', 'TicketController@sendMultipleMailTicket')->name('sendmailticket');
Route::post('get_materiales/{id}', 'TicketController@get_materiales')->name('get_materiales');
Route::post('get_razon/{tipo}/{id}', 'TicketController@get_razon')->name('get_razon');
Route::post('get_class_workers', 'TicketController@get_class_workers')->name('get_class_workers');
Route::post('get_empleoyes', 'TicketController@get_empleoyes')->name('get_empleoyes');
Route::get('get-all-email/{id}', 'TicketController@all_email')->name('get_all_email');

//report
Route::get('report-ticket-pdf', 'TicketController@report_ticket')->name('report.ticket');
Route::post('report-ticket-excel', 'TicketController@report_excel')->name('report.excel');

/** contacto de formulariod */
Route::get('proyecto_contacto', 'ContactoProyectoController@index')->name('listar.proyecto_contacto');
Route::get('contactos/{id}/proyecto', 'ContactoProyectoController@indexContacto')->name('listar.contactos');
Route::post('store/{id}/contactos', 'ContactoProyectoController@store')->name('store.contacto');
Route::post('update/{id}/contactos', 'ContactoProyectoController@update')->name('update.contacto');
Route::get('edit/{id}/contactos', 'ContactoProyectoController@edit')->name('edit.contacto');
Route::delete('destroy/{id}/contactos', 'ContactoProyectoController@destroy');
Route::post('get_empleoyes/{id}/contactos', 'ContactoProyectoController@get_empleoyes')->name('get_empleoyes.contacto');

/** razon de trabajo */
Route::resource('Razontrabajo', 'RazontrabajoController');
Route::get('razon', 'RazontrabajoController@index')->name('listar.razon');
Route::get('razoncrear', 'RazontrabajoController@create')->name('razoncrear');
Route::post('editrazon/update', 'RazontrabajoController@update')->name('Razontrabajo.update');
Route::delete('razon/{id}/destroy', 'RazontrabajoController@destroy');

/* tipo de trabajo */
Route::resource('Tipo', 'TipoController');
Route::get('tipo_trabajo', 'TipoController@index')->name('listar.tipo_trabajo');
Route::post('tipo_trabajo/update', 'TipoController@update')->name('Tipo.update');
Route::delete('tipo_trabajo/{id}/destroy', 'TipoController@destroy');

/* orden de trabajo */
Route::get('orders', 'OrdenController@index')->name('listar.ordenes');
Route::get('new/orden', 'OrdenController@create')->name('crear.orden');
Route::post('get_materiales/ordenes', 'OrdenController@get_materiales')->name('get_materiales.ordenes');
Route::post('get_proyects', 'OrdenController@get_proyects')->name('get_proyects');
Route::post('get_empleoyes/{id}/orden', 'OrdenController@get_empleoyes')->name('get_empleoyes.orden');
Route::post('get_empresas', 'OrdenController@get_empresas')->name('get_empresas');
Route::get('get_images/{id}/{type}/orden', 'OrdenController@get_images')->name('listar.image.orden');
Route::post('upload_image/{id}/{type}/orden/{nombre_camp?}', 'OrdenController@upload_image')->name('upload.image.orden');
Route::post('delete_image/{id}/{type}/orden', 'OrdenController@delete_image')->name('delete.image.orden');
Route::get('get_config_mail/{id}/orden', 'OrdenController@get_config_mail');
Route::post('update_signature/{id}/orden', 'OrdenController@update_signature')->name('update_signature.orden');
Route::post('store/orden', 'OrdenController@store')->name('store.orden');
Route::put('update/{id}/orden', 'OrdenController@update')->name('update.orden');
Route::get('show/orden', 'OrdenController@show')->name('show.orden');
Route::get('pdf/{id}/orden', 'OrdenController@pdf')->name('pdf.orden');
Route::get('edit/{id}/orden', 'OrdenController@edit')->name('edit.orden');
Route::post('send/{id}/orden', 'OrdenController@sendmailorden')->name('sendmailticket');
Route::delete('orden/{id}/destroy', 'OrdenController@destroy');
Route::post('post_config_mail/multiples-order', 'OrdenController@multiple_post_config_mail')->name('multiple_post_config_mail_order');
Route::post('send/multiple-orden', 'OrdenController@sendMultipleMailOrder')->name('sendmailorder');

/*Order */
Route::prefix('order')->group(function () {
    Route::get('list-order', 'TipoOrdenController@list_order')->name('order.list');
    Route::get('create', 'TipoOrdenController@create_order')->name('order.create');
    Route::get('pick-up/{id}', 'TipoOrdenController@get_materiales_recojer')->name('order.list.recojer.show');
    Route::post('store', 'TipoOrdenController@store_order')->name('order.store');
    Route::get('edit/{id}', 'TipoOrdenController@edit_orden')->name('order.list.edit.order');
    Route::put('update/{id}', 'TipoOrdenController@update_orden')->name('order.list.update.order');
    Route::delete('delete/{id}', 'TipoOrdenController@delete_orden')->name('order.list.delete.order');
    Route::get('list-data-table', 'TipoOrdenController@datatable_order')->name('order.list.datatable.order');
});
Route::prefix('order-materiales')->group(function () {
    Route::get('list-data-table/{id}', 'TipoOrdenController@datatable_order_materiales')->name('order.list.datatable.materiales');
});
Route::prefix('sub-order')->group(function () {
    Route::post('get_deliverys/{id}/orden', 'TipoOrdenController@get_deliverys')->name('order.select2.deliverys');
    Route::post('create/{id}', 'TipoOrdenPedidoController@show_orden')->name('order.list.show.create');
    Route::post('store', 'TipoOrdenPedidoController@store_sub_orden')->name('order.list.store.order');
    Route::get('edit/{id}', 'TipoOrdenPedidoController@edit_sub_orden')->name('order.list.store.order');
    Route::put('update/{id}', 'TipoOrdenPedidoController@update_sub_orden')->name('order.list.store.order');
    Route::delete('delete/{id}', 'TipoOrdenPedidoController@delete_sub_orden')->name('order.list.store.order');
    /*tranferencia */
    Route::post('trasferencia/create/{id}', 'TipoOrdenEnvioController@create_tranferencia')->name('order.list.transferencia.create');
    Route::post('trasferencia/store', 'TipoOrdenEnvioController@store_tranferencia_envio')->name('order.list.transferencia.store');
    //mostrar deliveres
    Route::get('trasferencia/{id}', 'TipoOrdenEnvioController@asignar_deliver')->name('order.list.transferencia.edit');
    Route::put('trasferencia/{id}', 'TipoOrdenEnvioController@update_deliver')->name('order.list.transferencia.update');
    /*segimiento */
    Route::post('segimiento/{id}', 'TipoOrdenController@show_segimiento_material')->name('order.list.segimiento.show');
    /*recepcion */
    Route::post('reception/store', 'TipoOrdenMovimientoMaterialController@store_recepcion_material')->name('order.list.recepcion.store');
    Route::get('reception/{id}', 'TipoOrdenMovimientoMaterialController@show_recepcion_material')->name('order.list.recepcion.show');
    Route::post('upload-images/{id}', 'TipoOrdenMovimientoMaterialController@upload_image')->name('order.list.recepcion.upload-images');
    Route::get('get-images/{id}', 'TipoOrdenMovimientoMaterialController@get_images')->name('order.list.recepcion.get-images');
    Route::post('delete-images/{id}', 'TipoOrdenMovimientoMaterialController@delete_image')->name('order.list.recepcion.delete-images');
    /* */
    Route::post('view-email', 'TipoOrdenController@view_email')->name('order.sub_order.materiales.suborden.show');
    Route::get('list-data-table/{id}', 'TipoOrdenPedidoController@datatable_order_sub_order')->name('order.list.datatable.sub_order');
});
Route::prefix('order-materiales')->group(function () {
    Route::get('list-data-table/{orden_material_id}/materiales/{order_id}', 'TipoOrdenController@datatable_materiales')->name('order.list.datatable.sub_order.materiales');
});
Route::prefix('order-movimientos')->group(function () {
    Route::get('list-data-table/materiales/{movimiento_id}', 'TipoOrdenController@datatable_materiales_movimientos')->name('order.list.datatable.sub_order.materiales.movimientos');
    Route::get('create/{id}', 'TipoOrdenController@show_material_movimiento')->name('order.sub_order.materiales.movimientos.create');
    Route::post('store/{id}', 'TipoOrdenController@store_material_movimiento')->name('order.sub_order.materiales.movimientos.store');
});
Route::prefix('order-delivery')->group(function () {
    Route::get('list', 'TipoOrdenController@index_delivery')->name('order.list.delivery');
    Route::post('order-list', 'TipoOrdenController@list_delivery')->name('order.list.orden.delivery'); //delivery
    Route::get('show-detail/{id}', 'TipoOrdenController@show_modal_delivery')->name('order.show.detail.delivery'); //delivery
    Route::put('update-detail-express/{id}', 'TipoOrdenController@update_modal_delivery_express')->name('order.update.detail.delivery'); //delivery
    Route::put('update-detail/{id}', 'TipoOrdenController@update_modal_delivery')->name('order.update.detail.delivery'); //delivery
    /* lista de wharehouse */
    Route::get('list-deliver', 'TipoOrdenEnvioController@index')->name('order.list.wharehose.delivery');
    Route::get('datatable-deliver', 'TipoOrdenEnvioController@datatable')->name('order.list.datatable.delivery'); //delivery
});
Route::prefix('status-orden')->group(function () {
    Route::get('/', 'TipoOrdenStatus@index')->name('status.orden.list.status');
    Route::get('list-data-table-status', 'TipoOrdenStatus@datatable_status')->name('status.orden.list.datatable.status');
    Route::post('store', 'TipoOrdenStatus@store')->name('status.orden.store');
    Route::get('edit/{id}', 'TipoOrdenStatus@edit')->name('status.orden.edit');
    Route::put('update/{id}', 'TipoOrdenStatus@update')->name('status.orden.edit');
    Route::delete('delete/{id}', 'TipoOrdenStatus@destroy')->name('status.orden.edit');
});
Route::prefix('materials')->group(function () {
    Route::get('/', 'TipoOrdenMaterial@index')->name('materiales.list');
    Route::get('data-table', 'TipoOrdenMaterial@datatable')->name('materiales.list.datatable');
});
Route::prefix('order-movimientos-material')->group(function () {
    Route::put('update/{id}', 'TipoOrdenMaterial@update')->name('materiales.movimiento.update');
    Route::delete('delete/{id}/pedido/{material_pedido_id}', 'TipoOrdenMaterial@destroy')->name('materiales.movimiento.destroy');
    Route::get('data-table/{id}', 'TipoOrdenMaterial@datatable_movimiento')->name('materiales.movimiento.datatable');
});
Route::prefix('order-report')->group(function () {
    Route::get('/', 'TipoOrdenReport@index')->name('orden.report');
    Route::get('/view-pdf', 'TipoOrdenReport@view_pdf')->name('orden.report.view-pdf');
    Route::post('/download-pdf', 'TipoOrdenReport@download_pdf')->name('orden.report.download-pdf');
    Route::post('/excel-pdf', 'TipoOrdenReport@excel_pdf')->name('orden.report.excel-pdf');
    /*filtros*/
    Route::post('/status', 'TipoOrdenReport@get_status')->name('orden.status.select2');
    Route::post('/projects', 'TipoOrdenReport@get_proyectos')->name('orden.proyectos');
    Route::post('/materials', 'TipoOrdenReport@get_materiales')->name('orden.materiales');
});
Route::post('tipo-material/{id}/materiales', 'TipoOrdenMaterial@select_material')->name('orden.select.material');
Route::post('tipo-material/{id}/equipos', 'TipoOrdenMaterial@select_equipo')->name('orden.select.equipo');

/* permisos y usuarios */
Route::get('users', 'UsuariosController@indexUserRoles')->name('listar.usuarios');
Route::get('edit/{id}/users', 'UsuariosController@editUser');
Route::post('store/users', 'UsuariosController@store')->name('store.usuarios');
Route::post('update/{id}/users', 'UsuariosController@updateUser')->name('update.usuarios');

Route::get('usuarios', 'UsuariosController@index')->name('usuarios');
Route::get('alltickets', 'UsuariosController@alltickets')->name('alltickets');
Route::get('allprojects', 'UsuariosController@allprojects')->name('allprojects');

/** Rutas proyectos */

Route::get('proyectos', 'ProyectoController@index')->name('listar.proyectos');
Route::get('data-table-proyectos', 'ProyectoController@datatable')->name('listar.datatable.proyectos');
Route::post('store/stages', 'ProyectoController@store')->name('store.stages');
Route::post('update/proyectos', 'ProyectoController@update')->name('update.proyectos');
Route::post('update_status/proyectos', 'ProyectoController@update_status')->name('proyectos.update.status');
Route::post('update_asistente/proyectos', 'ProyectoController@update_asistente_proyecto')->name('proyectos.update.asistente');

/*reports*/
Route::get('pdf_ticket', 'ReportsController@report_ticket')->name('report_ticket');
Route::post('buscar_reporte/{type}', 'ReportsController@get_id')->name('post.report');
Route::get('pdf_task', 'ReportsController@report_task')->name('report.task');
Route::get('pdf_proyect', 'ReportsController@report_proyecto')->name('report.proyect');
Route::get('pdf_company', 'ReportsController@report_empresas')->name('report.company');
Route::get('reports', 'ReportsController@index')->name('reports');

/*goal job */
Route::get('list/goals', 'GoalController@index')->name('list.goal');
Route::get('new/goal', 'GoalController@create')->name('create.goal');
Route::get('edit/{id}/goal', 'GoalController@edit')->name('edit.goal');
Route::post('update/{id}/goal', 'GoalController@update')->name('update.goal');
Route::post('store/goal', 'GoalController@store')->name('store.goal');
Route::post('where-goal/{tipo}/{id}', 'GoalController@get_where_goal');
Route::post('question-goal/{tipo}', 'GoalController@goal_question');
Route::post('buscar-goal/{tipo}', 'GoalController@goal_buscar');
Route::get('get_images/{id}/{type}/goal', 'GoalController@get_images');
Route::delete('delete/{id}/goal', 'GoalController@destroy')->name('delete.goal');
Route::post('delete_goal/{id}/{type}/goal', 'GoalController@delete_image');
Route::post('upload_image/{id}/{type}/goal/{nombre_camp?}', 'GoalController@upload_image');
Route::get('show/{id}/goal', 'GoalController@show')->name('show.goal');
Route::get('pdf/{id}/goal', 'GoalController@pdf')->name('pdf.goal');
Route::post('options', 'GoalController@option')->name('option.store');
Route::get('pwt/{id}/goal', 'GoalController@pwt')->name('pwt');
Route::get('get_config_mail/{id}/goal/{proyect?}', 'GoalController@get_config_mail');
Route::post('send/{id}/{part}/goal', 'GoalController@sendmailgoal')->name('sendmailgoal');
Route::post('goal/get_proyects', 'GoalController@get_proyects')->name('get_proyects.goal');
Route::get('show-btn/goals', 'GoalController@show_btn')->name('show.goal.btn');
//preguntas y respuestas
Route::get('list-question-answer', 'GoalController@list_preguntas')->name('list.goal.preguntas');
Route::get('data-table-question-answer', 'GoalController@datatable_preguntas')->name('datatable.goal.preguntas');
Route::prefix('goal-question')->group(function () {
    Route::post('store', 'GoalController@store_option')->name('question.goal.store');
    Route::post('edit/{id}', 'GoalController@edit_option')->name('question.goal.edit');
    Route::put('update/{id}', 'GoalController@update_option')->name('question.goal.update');
    Route::delete('delete/{id}', 'GoalController@delete_option')->name('question.goal.delete');
});
Route::prefix('goal')->group(function () {
    Route::post('/task/{id}', 'GoalController@select_task_by_proyecto')->name('select.task.proyecto');
    Route::get('/task-verificar/{id}', 'GoalController@tarea_verificar')->name('select.task.proyecto-verificar');

});

//report visit
Route::prefix('goal-reports')->group(function () {
    Route::get('/', 'visitReport\ReportsController@index')->name('visit.report.list');
    Route::get('/datatable', 'visitReport\ReportsController@datatable')->name('visit.report.datatable');
    Route::get('/view-pdf', 'visitReport\ReportsController@view_pdf')->name('visit.report.datatable');
    Route::get('/view-pdf-images/{id}', 'visitReport\ReportsController@datatable')->name('visit.report.datatable');
    Route::post('/view-excel', 'visitReport\ReportsController@view_excel')->name('visit.report.view_excel');
    Route::post('/view-all-pdf', 'visitReport\ReportsController@descarga_pdf')->name('visit.report.datatable');
    Route::post('/view-all-pdf-images', 'visitReport\ReportsController@descarga_pdf')->name('visit.report.datatable');
});
//report visit estrucutura
Route::prefix('goal-structure')->group(function () {
    //estrucutura
    Route::get('/', 'visitReport\EstructuraVisitReportController@index')->name('visit.report.estructura.list');
    Route::get('/data-table', 'visitReport\EstructuraVisitReportController@datatable')->name('visit.report.estructura.datatable');
    //superficie
    Route::get('/surfaces/{id}', 'visitReport\EstructuraVisitReportController@list_superficies')->name('visit.report.estructura.superficie.lista');
    Route::post('/surfaces', 'visitReport\EstructuraVisitReportController@save_superficie')->name('visit.report.estructura.superficie.store');
    Route::put('/surfaces/{id}', 'visitReport\EstructuraVisitReportController@update_superficie')->name('visit.report.estructura.update');
    Route::delete('/surfaces/{id}', 'visitReport\EstructuraVisitReportController@delete_superficie')->name('visit.report.estructura.delete');
    //materiales
    Route::get('/list-materiales/{id}', 'visitReport\EstructuraVisitReportController@list_materiales')->name('visit.report.estructura.material.lista');
    Route::post('/materiales', 'visitReport\EstructuraVisitReportController@save_material')->name('visit.report.estructura.material.store');
    Route::put('/materiales/{id}', 'visitReport\EstructuraVisitReportController@update_material')->name('visit.report.material.update');
    Route::delete('/materiales/{id}', 'visitReport\EstructuraVisitReportController@delete_material')->name('visit.report.material.delete');
    //
    Route::post('/create-material-tools', 'visitReport\EstructuraVisitReportController@store')->name('visit.report.estructura.create');
    Route::put('/update-material-tools', 'visitReport\EstructuraVisitReportController@update')->name('visit.report.estructura.update');
    Route::delete('/delete-material-tools', 'visitReport\EstructuraVisitReportController@destroy')->name('visit.report.estructura.delete');
    //select
    Route::post('/select-proyecto', 'visitReport\EstructuraVisitReportController@select_proyectos')->name('visit.report.estructura.proyecto');
    Route::post('/select-materiales/{id}/materiales', 'visitReport\EstructuraVisitReportController@select_material')->name('visit.report.estructura.materiales');
}); //
Route::prefix('goal-project')->group(function () {
    Route::get('/', 'visitReport\ProjectVisitReportController@index')->name('visit.report.proyectos.list');
    Route::get('/data-table', 'visitReport\ProjectVisitReportController@datatable')->name('visit.report.proyectos.datatable');
    //select
    Route::get('/select-proyecto', 'visitReport\ProjectVisitReportController@datatable')->name('visit.report.proyectos.datatable');
    Route::get('/select-materiales', 'visitReport\ProjectVisitReportController@datatable')->name('visit.report.proyectos.datatable');
    //visit report proyect materiales
    Route::get('/report-visit-superficies/{id}', 'visitReport\ProjectVisitReportController@show')->name('visit.report.proyectos.list-material');
    Route::post('/report-visit-superficies', 'visitReport\ProjectVisitReportController@store')->name('visit.report.proyectos.list-material');
    //crear orden
    Route::get('/show-order/{id}', 'visitReport\ProjectVisitReportController@crear_materiales')->name('visit.report.orden.show');
    Route::post('/store-order', 'visitReport\ProjectVisitReportController@save_orden')->name('visit.report.orden.store');
    //lista orden material
    Route::get('/data-table-orden/{id}', 'visitReport\ProjectVisitReportController@datatable_orden')->name('visit.report.orden.datatable');
});

/*tipo contact */
Route::get('list-contacts', 'tipoContactoController@index')->name('list.tipo.contacto');
Route::get('type-contacts', 'tipoContactoController@index')->name('get_list');
Route::post('new-contact', 'tipoContactoController@store')->name('post_list');
Route::post('update-contact/{id}', 'tipoContactoController@update')->name('put_list');
Route::get('show-contact/{id}', 'tipoContactoController@show')->name('show_list');
Route::delete('delete-contact/{id}', 'tipoContactoController@destroy')->name('delete_list');

/*cardex personal */
Route::get('no-auth-cardex', 'cardex\noAuthController@no_auth_index')->name('no.auth.list.cardex');
Route::get('no-auth-datatable', 'cardex\noAuthController@no_auth_datatable')->name('no.auth.list.datable');
//

Route::get('list-cardex', 'CardexController@index')->name('list.cardex');
Route::get('list-cardex-data-table', 'CardexController@index')->name('list.cardex.datatable');
Route::get('notificaciones', 'CardexController@notificacion')->name('notificacion'); //notificaciones
Route::get('create-cardex', 'CardexController@create')->name('create.cardex');
Route::post('new-cardex', 'CardexController@store')->name('new.cardex');
Route::get('cardex/{id}', 'CardexController@edit')->name('edit.cardex');
Route::put('cardex/{id}', 'CardexController@update')->name('update.cardex');
//Route::get('cardex/{id}', 'CardexController@show')->name('show.cardex');//a単adiendo viesta
Route::post('list-empresas', 'CardexController@get_empresas')->name('list.report_empresas');
Route::delete('cardex/{id}/destroy', 'CardexController@destroy')->name('destroy.cardex');
Route::post('store-type-evento', 'TipoEventoController@store')->name('store.tipo_evento'); //create tipo evento
Route::post('get-type-evento', 'TipoEventoController@obtener_tipo_evento')->name('get.tipo_evento'); //select2 tipo evento
Route::post('store-evento', 'EventoController@store')->name('cardex.new.evento'); //create evento
Route::get('list-event', 'EventoController@index')->name('cardex.list.evento'); //list evento
Route::get('edit-event/{id}', 'EventoController@edit')->name('cardex.edit.evento'); //edit event
Route::put('update-event/{id}', 'EventoController@update')->name('cardex.update.evento'); //update event
Route::delete('delete-event/{id}', 'EventoController@destroy')->name('cardex.delete.evento'); //destroy event

Route::post('get-event', 'MovimientoEventoController@get_event')->name('cardex.delete.evento'); //select2 event /tipo evento
Route::post('new-all-cardex', 'MovimientoEventoController@createAll')->name('new.all.cardex'); //movimientos masivos
Route::post('store-movimento-cardex', 'MovimientoEventoController@store')->name('store.movimiento.cardex'); //movimientos store
Route::get('edit-movimento-cardex/{id}', 'MovimientoEventoController@edit')->name('edit.movimiento.cardex'); //movimientos edit
Route::post('update-movimento-cardex/{id}', 'MovimientoEventoController@update')->name('update.movimiento.cardex'); //movimientos update
Route::delete('delete-movimento-cardex/{id}', 'MovimientoEventoController@destroy')->name('delete.movimiento.cardex'); //movimientos delete
Route::get('movimiento-evento/{id}', 'MovimientoEventoController@show')->name('show.movimiento.cardex'); //
Route::get('list-movimiento-evento/{id}', 'MovimientoEventoController@show_data_table')->name('show.movimiento.datatable'); //

Route::get('list-event/{id}', 'EventoController@list_personal')->name('cardex.list_personal.evento'); //list evento personal
Route::get('get_user-evento', 'EventoController@getCargo')->name('cardex.list_user.evento'); //list multiselect

/* otras opciones personal */
Route::get('cardex-other', 'CardexController@otras_opciones')->name('cardex.otros');

Route::post('upload_image/{id}/{type}/cardex/{nombre_camp?}', 'CardexController@upload_image')->name('cardex.store.images');
Route::get('get_images/{id}/{type}/cardex/{nombre_camp?}', 'CardexController@get_images')->name('cardex.show.images');
Route::post('delete_image/{id}/{type}/cardex', 'CardexController@delete_image')->name('cardex.delete.image');

Route::prefix('cardex-position')->group(function () {
    Route::get('data-table', 'cardex\CargoController@index')->name('cardex.cargo.datatable');
    Route::post('store', 'cardex\CargoController@store')->name('cardex.cargo.store');
    Route::get('edit/{id}', 'cardex\CargoController@edit')->name('cardex.cargo.edit');
    Route::put('update/{id}', 'cardex\CargoController@update')->name('cardex.cargo.update');
    Route::delete('delete/{id}', 'cardex\CargoController@destroy')->name('cardex.cargo.delete');
});
Route::prefix('cardex-type-employee')->group(function () {
    Route::get('data-table', 'cardex\TipoPersonalController@index')->name('cardex.tipo.datatable');
    Route::post('store', 'cardex\TipoPersonalController@store')->name('cardex.tipo.store');
    Route::get('edit/{id}', 'cardex\TipoPersonalController@edit')->name('cardex.tipo.edit');
    Route::put('update/{id}', 'cardex\TipoPersonalController@update')->name('cardex.tipo.update');
    Route::delete('delete/{id}', 'cardex\TipoPersonalController@destroy')->name('cardex.tipo.delete');
});
Route::prefix('cardex-report')->group(function () {
    Route::get('/', 'cardex\ReportCardexController@index')->name('cardex.personas.report');
    Route::get('/pdf', 'cardex\ReportCardexController@reportSkillPdf')->name('cardex.personas.reportSkill');
    Route::post('/excel', 'cardex\ReportCardexController@reportSkillExcel')->name('cardex.personas.reportSkillExcel');
});

//tipo evento
Route::get('type-event', 'TipoEventoController@index')->name('cardex.tipo_evento.list');
Route::get('type-event/list', 'TipoEventoController@list_tipo_evento')->name('cardex.tipo_evento.datatable');
Route::get('type-event/{id}', 'TipoEventoController@edit')->name('cardex.tipo_evento.edit');
Route::put('type-event/{id}', 'TipoEventoController@update')->name('cardex.tipo_evento.update');
Route::delete('type-event/{id}', 'TipoEventoController@destroy')->name('cardex.tipo_evento.delete');

/*Reporte de asistencia */
Route::get('get_personal/{id}', 'ReportsController@get_empleados');
Route::post('report-of-attendance', 'ReportsController@reporte_asistencia')->name('reporte.asistencia');
Route::get('report-of-attendance', 'ReportsController@view_reporte_asistencia')->name('view.reporte.asistencia');
Route::post('report-of-attendance-detail', 'ReportsController@reporte_asistencia_detail')->name('reporte.asistencia.detail');
Route::get('report-of-attendance-detail', 'ReportsController@view_reporte_asistencia_detail')->name('view.reporte.asistencia.detail');
Route::post('excel', 'ReportsController@test_excel')->name('report.asistencia.excel');

/* evaluaciones*/
//Route::get('list-evaluations', 'EvaluacionController@index')->name('list.evaluations');
Route::get('new/evaluation', 'EvaluacionController@create')->name('evaluation.new');
Route::post('store/evaluations', 'EvaluacionController@store')->name('store.evaluations');
Route::get('edit/{id}/evaluations', 'EvaluacionController@edit')->name('edit.evaluations');
Route::put('update/{id}/evaluations', 'EvaluacionController@update')->name('update.evaluations');
Route::delete('delete/{id}/evaluations', 'EvaluacionController@destroy')->name('delete.evaluations');

Route::get('list-areas-evaluations', 'HowAreaController@index')->name('list.areas.evaluations');
Route::post('store/areas-evaluations', 'HowAreaController@store')->name('store.areas.evaluations');
Route::get('areas-evaluations/{id}/edit', 'HowAreaController@edit')->name('edit.areas.evaluations');

Route::post('update/areas-evaluations', 'HowAreaController@update')->name('update.areas.evaluations');
Route::delete('areas-evaluations/{id}/destroy', 'HowAreaController@destroy')->name('destroy.areas.evaluations');

/*preguntas*/
Route::get('list-questions', 'QuestionController@index')->name('list.questions');
Route::get('get_questions', 'QuestionController@get_questions');
Route::get('new/questions', 'QuestionController@create')->name('create.questions');
Route::post('store/questions', 'QuestionController@store')->name('store.questions');
Route::get('questions/{id}/edit', 'QuestionController@edit')->name('edit.questions');
Route::post('update/{id}/questions', 'QuestionController@update')->name('update.questions');
Route::delete('questions/{id}/destroy', 'QuestionController@destroy')->name('destroy.questions');

/*Formulario */
Route::get('list-form', 'FormularioController@index')->name('list.form');
Route::post('store-form', 'FormularioController@store')->name('store.form');
Route::get('create-form', 'FormularioController@create')->name('create.form');
Route::get('show-form/{id}', 'FormularioController@show')->name('show.form');
Route::delete('delete-form/{id}', 'FormularioController@destroy')->name('delete.form');
/*a単adiendo verbo update*/
Route::get('list-form/{id}/edit', 'FormularioController@edit')->name('panel.evaluacion-formulario.formulario.edit');
Route::put('list-form', 'FormularioController@update')->name('panel.evaluacion-formulario.formulario.update');

//preview
Route::get('preview-form', function () {
    return view('panel.evaluacion-formulario.formulario.view');
})->name('form.preview');

//list evaluacion
Route::get('list-evaluations', 'EvaluacionesController@index')->name('list.evaluationes');
Route::post('store-evaluations', 'EvaluacionesController@store')->name('store.evaluationes');
Route::get('edit-evaluations/{id}', 'EvaluacionesController@edit')->name('edit.evaluationes');
Route::put('update-evaluations/{id}', 'EvaluacionesController@update')->name('update.evaluationes');
Route::delete('delete-evaluations/{id}', 'EvaluacionesController@destroy')->name('delete.evaluationes');
//detalle evaluacion
Route::get('list-detail-evaluations/{id}', 'Personal_evaluacionesController@index')->name('detail-list.personal_evaluationes');
Route::post('get-foreman-evaluations', 'EvaluacionesController@get_foreman')->name('get_foreman.evaluationes');
Route::post('get-form-evaluations', 'EvaluacionesController@get_formulario')->name('get_foreman.evaluationes');
Route::get('get-personal-evaluacion', 'EvaluacionesController@get_personal')->name('get_personal.evaluationes');
//list evaluacion foreman
Route::get('list-evaluations-pendient', 'Personal_evaluacionesController@list_evaluar')->name('list.evaluar');
Route::get('list-staff/{id}', 'Personal_evaluacionesController@lista_personal')->name('list.personal.evaluar');
Route::get('staff-form/{id}/{eval}', 'Personal_evaluacionesController@show')->name('show.evaluar');
Route::post('store-form-staff', 'Personal_evaluacionesController@store')->name('store.evaluar');
Route::delete('delete-form-staff/{id}', 'Personal_evaluacionesController@destroy')->name('delete.evaluar');
Route::get('show-form-staff/{id}', 'FormularioController@mostrar_form_completado')->name('show.complete.form');
Route::get('show-resultado/{id}', 'Personal_evaluacionesController@view_resultado')->name('show.resultado.form');

//estadisticas test

Route::prefix('statistics')->group(function () {
    Route::get('/', 'EstadisticasController@index')->name('estadisticas.list');
    Route::post('/proyect-manager', 'EstadisticasController@select_proyectos')->name('estadisticas.select2.proyect.manager');
    Route::post('/search', 'EstadisticasController@select2')->name('estadisticas.mostrar.proyect');
    Route::post('/proyect-manager-result', 'EstadisticasController@project_manager')->name('estadisticas.mostrar.proyect');
    /* filtros */
    Route::post('/company', 'EstadisticasController@select2_company')->name('estadisticas.select2.company');
    Route::post('/view-detail/{proyecto_id}', 'EstadisticasController@view_detail_proyecto')->name('estadisticas.view.detail');
    Route::get('/project/{id}', 'EstadisticasController@list_proyectos')->name('estadisticas.proyectos');
});
/* informacion de proyectos */
Route::prefix('info-project')->group(function () {
    Route::get('/', 'InformacionProjectController@index')->name('info.index');
    //datatable
    Route::get('proyect', 'InformacionProjectController@datatable_proyectos')->name('info.datatable.proyectos');
    Route::post('traking', 'InformacionProjectController@datatable_movimientos')->name('info.datatable.proyectos.movimientos');
    //guarda informacon
    Route::post('edit/{id}', 'InformacionProjectController@edit')->name('info.datatable.edit');
    //guardar date proyect
    Route::get('get-date-project/{id}', 'InformacionProjectController@get_date_proyecto')->name('info.show.date.proyecto');
    Route::put('update-date-project/{id}', 'InformacionProjectController@update_date_proyecto')->name('info.update.date.proyecto');
    //guardar  info proyect
    Route::get('get-project-info/{id}', 'InformacionProjectController@get_info')->name('info.show.date.info');
    Route::put('update-info/{id}', 'InformacionProjectController@update_info')->name('info.update.info');
    //guardar actions proyect
    Route::get('get-project-action/{id}', 'InformacionProjectController@get_action')->name('info.show.action');
    Route::put('update_action/{id}', 'InformacionProjectController@update_action')->name('info.update.action');
    Route::put('update_action_history/{id}', 'InformacionProjectController@update_action_history')->name('info.update.action.history');
    Route::DELETE('update_action_history/{id}', 'InformacionProjectController@delete_action_history')->name('info.update.action');
    //load  graficos
    Route::post('load-graficos/{id}', 'InformacionProjectController@get_graficos')->name('info.show.graficos');
    //view pdf
    Route::get('view-pdf', 'InformacionProjectController@view_pdf')->name('info.show.pdf');
    //actualizacion hrs Con
    Route::post('/update_hrs_cont', 'InformacionProjectController@update_hrs_cont');
    //color
    Route::post('/update-color', 'InformacionProjectController@update_color')->name('info.update.color');
    //update tipo status
    Route::put('/update-general', 'InformacionProjectController@update_general')->name('info.update.general');
    /*report Daily */
    Route::get('filter-pdf-daily', 'InformacionProyecto\reportDailyController@proyecto')->name('info.filtro.daily.pdf');
    Route::get('view-pdf-daily', 'InformacionProyecto\reportDailyController@view_report')->name('info.show.daily.pdf');
});

/* upload data */
Route::prefix('project-files')->group(function () {
    Route::get('import', 'ImportFileController@index')->name('list.import');
    Route::get('lis-surface', 'Estimados\SuperficieController@index')->name('list.surface');
    Route::post('leer_excel', 'ImportFileController@leer_excel')->name('upload.excel');
    //superficie
    Route::get('surface-datatable', 'Estimados\SuperficieController@datatable_superficie')->name('datatable.surface');
    Route::post('surface-create', 'Estimados\SuperficieController@store')->name('surface.store');
    Route::get('surface-list-standar/{id}', 'Estimados\SuperficieController@list_superficie_standares')->name('surface.list.standar');
    Route::get('surface-edit/{id}', 'Estimados\SuperficieController@edit')->name('surface.edit');
    Route::put('surface-update/{id}', 'Estimados\SuperficieController@update')->name('surface.update');
    Route::delete('surface-delete/{id}', 'Estimados\SuperficieController@destroy')->name('surface.delete');
    //standar
    Route::get('list-standars/{id}', 'Estimados\EstandarController@list_standar')->name('standar.list');
    Route::get('standar-edit/{id}', 'Estimados\EstandarController@edit')->name('standar.edit');
    Route::post('standar-create', 'Estimados\EstandarController@store')->name('standar.store');
    Route::put('standar-update/{id}', 'Estimados\EstandarController@update')->name('standar.update');
    Route::delete('standar-delete/{id}', 'Estimados\EstandarController@destroy')->name('standar.delete');
    //metodo
    Route::get('list-method/{id}', 'Estimados\MetodoController@datatable')->name('metodo.list');
    Route::post('method-create', 'Estimados\MetodoController@store')->name('metodo.store');
    Route::get('method-edit/{id}', 'Estimados\MetodoController@edit')->name('metodo.edit');
    Route::put('method-update/{id}', 'Estimados\MetodoController@update')->name('metodo.update');
    Route::delete('method-delete/{id}', 'Estimados\MetodoController@destroy')->name('metodo.delete');
    //import excel to datatable
    Route::post('datatable-generate', 'ImportFileController@datatable_import')->name('datatable.generate');
    //cambio en import
    Route::put('cambio_metodo/{id}', 'ImportFileController@update_metodo')->name('metodo.edit');
    //duplicar
    Route::post('duplicar/{id}', 'ImportFileController@duplicar_area')->name('metodo.duplicar');
    //eliminar
    Route::post('eliminar/{id}', 'ImportFileController@eliminar_area')->name('metodo.eliminar');
    //export
    Route::post('export-excel', 'Estimados\SuperficieController@export_excel')->name('export.excel');
    Route::post('export-excel-sov', 'Estimados\SuperficieController@export_excel_sov')->name('export.excel.sov');
    Route::post('export-txt', 'ImportFileController@load_descarga_txt')->name('export.txt');
    Route::post('export-txt-stp', 'ImportFileController@load_descarga_txt_stp')->name('export.txt.stp');
    Route::post('export-excel-completed', 'Estimados\SuperficieController@export_excel_estimado_completado')->name('export.txt.stp');
    //modificacion de constante
    Route::post('update-const', 'ImportFileController@cambio_constante')->name('export.constante.update');
    Route::post('update-index-of-prod', 'ImportFileController@cambio_index_prod')->name('export.constante.update');
    //modificacion area
    Route::post('get-area', 'ImportFileController@obtener_area')->name('export.area.get');
    Route::post('validate-update-area', 'ImportFileController@validate_modificar_area')->name('export.area.update');
    Route::put('update-area', 'ImportFileController@modificar_area')->name('export.area.update');

    // modificar import edit_update
    Route::get('edit-import/{id}', 'ImportFileController@edit_import')->name('import.edit');
    Route::put('update-import/{id}', 'ImportFileController@update_import')->name('import.update');
    //save import
    Route::get('get-imports-project/{id}', 'ImportFileController@get_import_project')->name('import.project.get');
    Route::post('save_import_project', 'ImportFileController@save_import_project')->name('import.project.save');
    //add estimados a database import
    Route::post('add-estimate-project', 'ImportFileStructure@add_estimado_import_project')->name('import.project.get');
    Route::post('save-estimate-project', 'ImportFileStructure@save_estimado_import_project')->name('import.project.save');
    //historial
    Route::get('datatable-history/{id}', 'ImportFileController@datatable_historial_import')->name('import.datatable.history');
    Route::delete('delete-history/{id}', 'ImportFileController@delete_historial_import')->name('import.delete.history');
    Route::get('export-history/{id}', 'ImportFileController@export_historial_import')->name('import.get.history');
    //load data base
    Route::get('import-database/{id}', 'ImportFileStructure@import_database')->name('import.database');
    Route::post('update-import-duplicado-database', 'ImportFileStructure@update_import_database')->name('import.database');
    //list labor cost
    Route::get('datatable-labor-cost', 'Estimados\LaborCostController@index')->name('import.datatable.list_labor_cost');
    Route::post('store-labor-cost', 'Estimados\LaborCostController@store')->name('import.store.list_labor_cost');
    Route::get('edit-labor-cost/{id}', 'Estimados\LaborCostController@edit')->name('import.edit.list_labor_cost');
    Route::put('update-labor-cost/{id}', 'Estimados\LaborCostController@update')->name('import.update.list_labor_cost');
    Route::delete('delete-labor-cost/{id}', 'Estimados\LaborCostController@destroy')->name('import.delete.list_labor_cost');
    //Final Estructure
    Route::get('final-escructure', 'ImportFileStructure@index')->name('import.final.list');
    Route::post('final-brake-down', 'ImportFileStructure@import_sov_proyect')->name('import.final.list.break_down');
    Route::post('final-export-sov', 'ImportFileStructure@import_sov_proyect')->name('import.final.list.sov');
    //modificando parametros
    Route::put('final-sov-update', 'ImportFileStructure@import_sov_task_update')->name('import.final.list.update');
    /*
    !restructuracion de proyectos
     */
    Route::get('estructure-jobs', 'Estructure\ProyectosController@index')->name('estructura.list');
    Route::post('table-estructure-jobs', 'Estructure\ProyectosController@proyectos_detalle')->name('estructura.list.jobs');
    Route::post('list-jobs', 'Estructure\ProyectosController@proyectos_list')->name('estructura.list.jobs');
    //cambio de relaciones
    Route::post('update-floor', 'Estructure\ProyectosController@update_floor')->name('estructura.update.floor');
    Route::post('update-area', 'Estructure\ProyectosController@update_area')->name('estructura.update.area');
    Route::post('update-task', 'Estructure\ProyectosController@update_task')->name('estructura.update.task');
    //update en estructura
    Route::put('task-update', 'Estructure\ProyectosController@import_task_update')->name('import.task.update');
    Route::put('area-update', 'Estructure\ProyectosController@import_area_update')->name('import.area.update');
    Route::put('floor-update', 'Estructure\ProyectosController@import_floor_update')->name('import.floor.update');
    Route::put('edificio-update', 'Estructure\ProyectosController@import_edificio_update')->name('import.edificio.update');
    Route::put('project-update', 'Estructure\ProyectosController@import_project_update')->name('import.project.update');
    //nuevos en estructura
    Route::post('new-task', 'Estructure\ProyectosController@store_task')->name('estructura.store.task');
    Route::post('new-area', 'Estructure\ProyectosController@store_area')->name('estructura.store.area');
    Route::post('new-floor', 'Estructure\ProyectosController@store_floor')->name('estructura.store.floor');
    Route::post('new-edificio', 'Estructure\ProyectosController@store_edificio')->name('estructura.store.edificio');
    //delete en estructura
    Route::delete('delete-task', 'Estructure\ProyectosController@delete_task')->name('estructura.delete.task');
    Route::delete('delete-area', 'Estructure\ProyectosController@delete_area')->name('estructura.delete.area');
    Route::delete('delete-floor', 'Estructure\ProyectosController@delete_floor')->name('estructura.delete.floor');
    Route::delete('delete-edificio', 'Estructure\ProyectosController@delete_edificio')->name('estructura.delete.edificio');
});
/*notas proyecto */
Route::prefix('project-notas')->group(function () {
    Route::post('select-project', 'InformacionProyecto\NotaProyectoController@select_proyectos')->name('notas.proyecto.select2');
    Route::get('/', 'InformacionProyecto\NotaProyectoController@index_admin')->name('notas.proyecto.list');
    Route::get('data-table', 'InformacionProyecto\NotaProyectoController@datatable_admin')->name('notas.proyecto.datatable');
    Route::get('/{id}', 'InformacionProyecto\NotaProyectoController@index_proyecto')->name('notas.proyecto.lis.proyecto');
    Route::get('data-table/{id}', 'InformacionProyecto\NotaProyectoController@datatable_proyecto')->name('notas.proyecto.datatable.proyecto');
    Route::post('store', 'InformacionProyecto\NotaProyectoController@store')->name('notas.proyecto.store');
    Route::get('show/{id}', 'InformacionProyecto\NotaProyectoController@show')->name('notas.proyecto.show');
    Route::get('pdf/{id}', 'InformacionProyecto\NotaProyectoController@pdf')->name('notas.proyecto.pdf');
    Route::get('edit/{id}', 'InformacionProyecto\NotaProyectoController@edit')->name('notas.proyecto.edit');
    Route::post('update/{id}', 'InformacionProyecto\NotaProyectoController@update')->name('notas.proyecto.update');
    Route::delete('delete/{id}', 'InformacionProyecto\NotaProyectoController@destroy')->name('notas.proyecto.delete');

    /*images */
    Route::post('upload_image/{id}', 'InformacionProyecto\NotaProyectoController@upload_image_async')->name('upload.image');
    Route::post('delete_image/{id}', 'InformacionProyecto\NotaProyectoController@delete_image')->name('delete.image');

});
Route::prefix('submittals')->group(function () {
    Route::get('/', 'Submittals\SubmittalsController@index')->name('submittals.list');
    Route::get('/data-table', 'Submittals\SubmittalsController@datatable')->name('submittals.datatable');
    Route::post('/download-excel', 'Submittals\SubmittalsController@report_excel')->name('submittals.excel');
    //crud
    Route::get('/create', 'Submittals\SubmittalsController@create')->name('submittals.create');
    Route::post('/store', 'Submittals\SubmittalsController@store')->name('submittals.store');
    Route::get('/edit/{id}', 'Submittals\SubmittalsController@edit')->name('submittals.edit');
    Route::put('/update/{id}', 'Submittals\SubmittalsController@update')->name('submittals.update');
    Route::delete('/delete/{id}', 'Submittals\SubmittalsController@destroy')->name('submittals.delete');
    Route::post('/category-submittals', 'Submittals\SubmittalsController@select_tipo_materiales')->name('submittals.categoria');
    Route::post('/proveedor-submittals', 'Submittals\SubmittalsController@select_proveedor')->name('submittals.proveedores');

});
Route::prefix('daily-report-detail')->group(function () {
    Route::get('/data-table/{id}', 'DailyReportDetailController@dataTable')->name('daily_report_detail.datatable');
    Route::get('/data-table-project/{id}', 'DailyReportDetailController@dataTableProject')->name('daily_report_detail.datatable.project');
    Route::get('/list/{id}', 'DailyReportDetailController@index')->name('daily_report_detail.index');
    Route::get('/create/{id}', 'DailyReportDetailController@create')->name('daily_report_detail.create');
    Route::post('/store/{id}', 'DailyReportDetailController@store')->name('daily_report_detail.store');
    Route::get('/edit/{id}', 'DailyReportDetailController@edit')->name('daily_report_detail.edit');
    Route::put('/update/{id}', 'DailyReportDetailController@update')->name('daily_report_detail.update');

    //preview pdf
    Route::get('/view-admin', 'DailyReportDetailController@show_admin')->name('daily_report_detail.show_admin');
    Route::get('/view-client', 'DailyReportDetailController@show_cliente')->name('daily_report_detail.show_cliente');
    //images
    Route::post('/image/{id}/upload', 'DailyReportDetailController@upload_image')->name('daily_report_detail.imagen.upload');
    Route::get('/image/{id}/get', 'DailyReportDetailController@get_images')->name('daily_report_detail.imagen.get');
    Route::post('/image/{id}/delete', 'DailyReportDetailController@delete_image')->name('daily_report_detail.imagen.delete');
    // pdf
    Route::get('/pdf-admin/{id}', 'DailyReportDetailController@pdf_admin')->name('daily_report_detail.pdf_admin');
    Route::get('/pdf-client/{id}', 'DailyReportDetailController@pdf_cliente')->name('daily_report_detail.pdf_cliente');
    //send mail
    Route::post('/email/{id}/{tipo}', 'DailyReportDetailController@sendmail')->name('daily_report_detail.email_admin');

});
Route::prefix('config-daily-report-detail')->group(function () {

    Route::get('/datatable-option', 'ConfigReportDailyController@dataTable')->name('config_daily_report_detail.datatable');
    Route::get('/create/{id}', 'ConfigReportDailyController@create')->name('config_daily_report_detail.create');
    Route::post('/store', 'ConfigReportDailyController@store')->name('config_daily_report_detail.store');
    Route::get('/edit/{id}', 'ConfigReportDailyController@edit')->name('config_daily_report_detail.edit');
});

Route::prefix('config-daily-report-option')->group(function () {
    Route::get('/create', 'ConfigDailyReportOptionController@create')->name('config_daily_option.create');
    Route::post('/store', 'ConfigDailyReportOptionController@store')->name('config_daily_option.store');
    Route::get('/edit/{id}', 'ConfigDailyReportOptionController@edit')->name('config_daily_option.store');
    Route::put('/update/{id}', 'ConfigDailyReportOptionController@update')->name('config_daily_option.store');
    Route::delete('/delete/{id}', 'ConfigDailyReportOptionController@destroy')->name('config_daily_option.store');
    Route::post('/show-options', 'ConfigDailyReportOptionController@showOption')->name('daily_report_detail.show');
});

//modulo de payroll/compare
Route::prefix('payroll')->group(function () {
    /*timberline */
    Route::get('/', 'PayRoll\PayRollController@index')->name('payroll.index');
    Route::post('/upload-timberline', 'PayRoll\PayRollController@uploadFileTimerLine')->name('payroll.upload.timerline');
    Route::get('/datatable-timberline', 'PayRoll\PayRollController@datatable_timberline')->name('payroll.datatable');
    Route::post('/store-timberline/{id}', 'PayRoll\PayRollController@store_timber_line')->name('payroll.store.timberline');

    /*list employee */
    Route::post('/upload-list-employee', 'PayRoll\PayRollController@uploadFileListEmployee')->name('payroll.upload.employee');
    Route::get('/datatable-list-employee', 'PayRoll\PayRollController@datatable_list_employee')->name('payroll.datatable');
    Route::post('/store-list-employee/{id}', 'PayRoll\PayRollController@store_list_employee')->name('payroll.store');

    Route::post('/compare-info', 'PayRoll\PayRollController@compare_info')->name('payroll.compare');
    Route::get('/datatable-payroll', 'PayRoll\PayRollController@datatable_data_payroll')->name('payroll.datatable.listEmployee');
    Route::get('/datatable-data/{id}', 'PayRoll\PayRollController@datatable_data')->name('payroll.datatable.data');

    Route::post('/select-empleado', 'PayRoll\PayRollController@select_empleado')->name('payroll.project');
    Route::post('/select-project', 'PayRoll\PayRollController@select_proyectos')->name('payroll.project');
    Route::post('/select-edificio', 'PayRoll\PayRollController@select_edificio')->name('payroll.edificio');
    Route::post('/select-floor', 'PayRoll\PayRollController@select_floor')->name('payroll.floor');
    Route::post('/select-area', 'PayRoll\PayRollController@select_area')->name('payroll.area');
    Route::post('/select-task', 'PayRoll\PayRollController@select_task')->name('payroll.task');
    Route::post('/store-payroll', 'PayRoll\PayRollController@store_payroll')->name('payroll.store.payroll');

    /*Update Payroll */
    Route::post('/update-payroll-empleado', 'PayRoll\PayRollController@update_empleado')->name('payroll.update_proyectos.project');
    Route::post('/update-payroll-project', 'PayRoll\PayRollController@update_proyectos')->name('payroll.update_proyectos.project');
    Route::post('/update-payroll-edificio', 'PayRoll\PayRollController@update_edificio')->name('payroll.update_proyectos.edificio');
    Route::post('/update-payroll-floor', 'PayRoll\PayRollController@update_floor')->name('payroll.update_proyectos.floor');
    Route::post('/update-payroll-area', 'PayRoll\PayRollController@update_area')->name('payroll.update_proyectos.area');
    Route::post('/update-payroll-task', 'PayRoll\PayRollController@update_task')->name('payroll.update_proyectos.task');

    /*descarga payroll */
    Route::post('/export-txt', 'PayRoll\PayRollController@load_descarga_txt')->name('payroll.export.txt');
});
Route::prefix('category-submittals')->group(function () {
    Route::get('/', 'Submittals\TipoCategoriaController@index')->name('category_submittals.index');
    Route::get('/data-table', 'Submittals\TipoCategoriaController@datatable')->name('category_submittals.datatable');
    Route::get('/create', 'Submittals\TipoCategoriaController@create')->name('category_submittals.create');
    Route::post('/store', 'Submittals\TipoCategoriaController@store')->name('category_submittals.store');
    Route::get('/edit/{id}', 'Submittals\TipoCategoriaController@edit')->name('category_submittals.edit');
    Route::put('/update/{id}', 'Submittals\TipoCategoriaController@update')->name('category_submittals.update');
    Route::delete('/delete/{id}', 'Submittals\TipoCategoriaController@destroy')->name('category_submittals.delete');
});
Route::prefix('register-activities')->group(function () {
    Route::get('/', 'RegisterActivitiesController@index')->name('register_activities.index');
    Route::post('/empleados', 'RegisterActivitiesController@empleado')->name('register_activities.empleados');
    Route::post('/projects', 'RegisterActivitiesController@projects')->name('register_activities.projects');
    Route::post('/edificio/{id}', 'RegisterActivitiesController@edificio')->name('register_activities.edificio');
    Route::post('/piso/{id}', 'RegisterActivitiesController@piso')->name('register_activities.piso');
    Route::post('/area/{id}', 'RegisterActivitiesController@area')->name('register_activities.area');
    Route::post('/tarea/{id}', 'RegisterActivitiesController@tarea')->name('register_activities.tarea');
    Route::post('/store-visit-report', 'RegisterActivitiesController@store_visit_report')->name('register_activities.store_visit_report');
    Route::post('/show', 'RegisterActivitiesController@show')->name('register_activities.show');
    Route::put('/update', 'RegisterActivitiesController@update')->name('register_activities.update');
    Route::get('/verficar-proyecto/{id}', 'RegisterActivitiesController@auto_complementar_proyecto')->name('register_activities.verificar-proyecto');
    Route::delete('/delete-registro-diario-actividad/{id}', 'RegisterActivitiesController@delete_registro_diario')->name('register_activities.delete');
});
Route::prefix('permisos-rol')->group(function () {
    Route::get('/', 'RolPermisos@index')->name('permisos-rol.index');
    Route::get('/data-table', 'RolPermisos@dataTable')->name('permisos-rol.dataTable');
    Route::post('/store', 'RolPermisos@store')->name('permisos-rol.store');
    Route::get('/create', 'RolPermisos@create')->name('permisos-rol.create');
    Route::get('/edit/{id}', 'RolPermisos@edit')->name('permisos-rol.edit');
    Route::put('/update', 'RolPermisos@update')->name('permisos-rol.update');
    Route::delete('/delete/{id}', 'RolPermisos@destroy')->name('permisos-rol.delete');
});
Route::prefix('action-week')->group(function () {
    Route::get('/edit/{id}', 'InformacionProyecto\NotificacionAccionController@edit')->name('action-week.edit');
    Route::put('/update', 'InformacionProyecto\NotificacionAccionController@update')->name('action-week.update');
    Route::delete('/delete/{id}', 'InformacionProyecto\NotificacionAccionController@destroy')->name('action-week.delete');
    Route::post('/empleados', 'InformacionProyecto\NotificacionAccionController@empleados')->name('action-week.empleados');
    Route::get('/notificaciones', 'InformacionProyecto\NotificacionAccionController@show')->name('action-week.notificaciones');
    Route::post('/marcado/{id}', 'InformacionProyecto\NotificacionAccionController@marcado')->name('action-week.marcado');
    Route::get('/historial/{id}', 'InformacionProyecto\NotificacionAccionController@historial')->name('action-week.historial');
    Route::post('/notificacion-accion/{id}', 'InformacionProyecto\NotificacionAccionController@cambio_estado')->name('action-week.cambio_estado');
});

Route::prefix('order-transfer')->group(function () {
    Route::get('/', 'Order\OrdenTransferenciaController@index')->name('order_transfer.edit');
    Route::get('/data-table', 'Order\OrdenTransferenciaController@dataTable')->name('order_transfer.datatable');
    Route::post('/proyecto-from', 'Order\OrdenTransferenciaController@proyectos_from')->name('order_transfer.from');
    Route::post('/proyecto-to', 'Order\OrdenTransferenciaController@proyectos_to')->name('order_transfer.to');
    Route::post('/pedidos', 'Order\OrdenTransferenciaController@pedidos')->name('order_transfer.pedidos');
    Route::post('/material', 'Order\OrdenTransferenciaController@material')->name('order_transfer.material');
    Route::post('/store', 'Order\OrdenTransferenciaController@store')->name('order_transfer.store');
    Route::post('/update', 'Order\OrdenTransferenciaController@update')->name('order_transfer.update');
    Route::post('/verificar-orden', 'Order\OrdenTransferenciaController@verificar_orden')->name('order_transfer.verificar');
    Route::delete('/delete-material-pedido/{id}', 'Order\OrdenTransferenciaController@delete_material')->name('order_transfer.delete_material');

});

Route::prefix('project-reports')->group(function () {
    Route::get('/', 'InformacionProjectReporteController@index')->name('info_project_report.index');
    Route::get('/data-table', 'InformacionProjectReporteController@data_table')->name('info_project_report.data_table');
    Route::get('/descarga-pdf', 'InformacionProjectReporteController@export_pdf')->name('info_project_report.descarga_pdf');
    Route::post('/descarga-excel', 'InformacionProjectReporteController@export_excel')->name('info_project_report.descarga_excel');
});
