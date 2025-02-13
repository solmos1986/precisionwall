<?php

//actividades
Breadcrumbs::for('activities', function ($trail) {
    $trail->push('Activities', route('listar.actividades'));
});

Breadcrumbs::for('tickets', function ($trail) {
    $trail->push('My tickets', route('listar.mis.tickets'));
});

Breadcrumbs::for('actividad', function ($trail, $idActividad) {
    $trail->parent('activities');
    $trail->push("Activity - ID $idActividad", route('listar.tickets', $idActividad));
});

Breadcrumbs::for('new ticket', function ($trail, $idActividad, $idTicket) {
    $trail->parent('actividad', $idActividad);
    $trail->push("New Ticket", route('crear.ticket', $idTicket));
});
Breadcrumbs::for('edit ticket', function ($trail, $idActividad, $idTicket, $numTicket) {
    $trail->parent('actividad', $idActividad);
    $trail->push("Edit Ticket #$numTicket", route('crear.ticket', $idTicket));
});

//visit report
Breadcrumbs::for('visit report', function ($trail) {
    $trail->push('List visit report', route('list.goal'));
});

Breadcrumbs::for('new report', function ($trail) {
    $trail->parent('visit report');
    $trail->push('Create visit report', route('create.goal'));
});

Breadcrumbs::for('edit report', function ($trail, $idReport) {
    $trail->parent('visit report');
    $trail->push("Edit visit report #$idReport", route('edit.goal', $idReport));
});

//order WC INSTALLATION
Breadcrumbs::for('list order', function ($trail) {
    $trail->push('List order and report ', route('listar.ordenes'));
});

Breadcrumbs::for('new order', function ($trail) {
    $trail->parent('list order');
    $trail->push('Create order', route('crear.orden'));
});
Breadcrumbs::for('edit order', function ($trail, $numOrder) {
    $trail->parent('visit report');
    $trail->push("Edit order #$numOrder", route('edit.orden', $numOrder));
});

//working razon
Breadcrumbs::for('working reason', function ($trail) {
    $trail->push('Working reason', route('listar.razon'));
});

//tipo trabajo
Breadcrumbs::for('tipo trabajo', function ($trail) {
    $trail->push('Types of work', route('listar.tipo_trabajo'));
});

//tipo contacto
Breadcrumbs::for('tipo contacto', function ($trail) {
    $trail->push('Type contacts', route('list.tipo.contacto'));
});

//contacto proYecto
Breadcrumbs::for('contacto proyectos', function ($trail) {
    $trail->push('Contact proyects', route('listar.proyecto_contacto'));
});

Breadcrumbs::for('proyectos', function ($trail, $idProyecto) {
    $trail->parent('contacto proyectos');
    $trail->push("Proyectos #$idProyecto", route('listar.contactos', $idProyecto));
});

//user
Breadcrumbs::for('user roles', function ($trail) {
    $trail->push('Users roles', route('listar.usuarios'));
});

//recursos humanos
Breadcrumbs::for('lista empleados', function ($trail) {
    $trail->push('Employee List', route('list.cardex'));
});

Breadcrumbs::for('nuevo empleado', function ($trail) {
    $trail->parent('lista empleados');
    $trail->push('Create Employee', route('create.cardex'));
});

Breadcrumbs::for('edit empleado', function ($trail, $nick_name) {
    $trail->parent('lista empleados');
    $trail->push("Edit Employee: $nick_name", route('edit.cardex', $nick_name));
});

Breadcrumbs::for('lista eventos', function ($trail) {
    $trail->parent('lista empleados');
    $trail->push("Event list", route('cardex.list.evento'));
});

//evaluaciones
Breadcrumbs::for('lista evaluaciones', function ($trail) {
    $trail->push('List of evaluations', route('list.evaluationes'));
});

Breadcrumbs::for('lista formularios', function ($trail) {
    $trail->parent('lista evaluaciones');
    $trail->push('List of form', route('list.form'));
});

Breadcrumbs::for('new formulario', function ($trail) {
    $trail->parent('lista formularios');
    $trail->push('New form', route('create.form'));
});

//reports
Breadcrumbs::for('report', function ($trail) {
    $trail->push('Report', route('reports'));
});
//proyect
Breadcrumbs::for('proyects', function ($trail) {
    $trail->push('Proyects', route('listar.proyectos'));
});

//tipo order
Breadcrumbs::for('list order deliver / pick up', function ($trail) {
    $trail->push('list order deliver / pick up', route('order.list.wharehose.delivery'));
});
//tipo order
Breadcrumbs::for('report_order', function ($trail) {
    $trail->push('Material and Equipment Report', route('orden.report'));
});
