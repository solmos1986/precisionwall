DROP TABLE IF EXISTS tipo_orden_estatus;
create table tipo_orden_estatus(
    id int(11) auto_increment primary key,
    codigo varchar(100) not null,
    nombre varchar(100) not null,
    estado tinyint(1) not null
);
DROP TABLE IF EXISTS tipo_orden_material;
create table tipo_orden_material(
    id int(11) auto_increment primary key,
    nombre varchar(100) not null,
    estado tinyint(1) not null
);
DROP TABLE IF EXISTS tipo_orden;
create table tipo_orden(
    id int(11) auto_increment primary key,
    proyecto_id int(11) not null,
    estatus_id int(11) not null,
    num int(11) not null,
    nota varchar(1000) null,
    nombre_trabajo varchar(300) not null,
    estado varchar(50) not null,
    fecha_order DATETIME,
    fecha_entrega DATETIME,
    creado_por int(11) not null,
    eliminado tinyint(1),
    foreign key (estatus_id) references tipo_orden_estatus(id)
);
DROP TABLE IF EXISTS tipo_orden_materiales;
create table tipo_orden_materiales(
    id int(11) auto_increment primary key,
    material_id int(11) not null,
    tipo_orden_id int(11) not null,
    tipo_orden_material_id int(11) not null,
    cant_ordenada int(11) not null,
    estado tinyint(1),
    foreign key (tipo_orden_id) references tipo_orden(id),
    foreign key (tipo_orden_material_id) references tipo_orden_material(id)
);
DROP TABLE IF EXISTS tipo_orden_materiales_solicitud;
create table tipo_orden_materiales_solicitud(
    id int(11) auto_increment primary key,
    tipo_orden_materiales_id int(11) not null,
    solicitud_orden_vendedor_id int(11) null,
    cantidad int(11) null,
    estado tinyint(1) not null,
    foreign key (tipo_orden_materiales_id) references tipo_orden_materiales(id)
);
DROP TABLE IF EXISTS tipo_solicitud_orden_vendedor;
create table tipo_solicitud_orden_vendedor(
    id int(11) auto_increment primary key,
    vendedor_id int(11) not null,
    proyecto_id int(11) not null,
    estatus_id int(11) not null,
    nota varchar(1000) null,
    fecha_registro DATETIME,
    estado tinyint(1) not null,
    pco varchar(100) not null,
    foreign key (estatus_id) references tipo_orden_estatus(id)
);
DROP TABLE IF EXISTS tipo_almacen;
create table tipo_almacen(
    id int(11) auto_increment primary key,
    proyecto_id int(11) not null,
    fecha_registro DATETIME,
    estado tinyint(1) not null
);
DROP TABLE IF EXISTS tipo_almacen_materiales;
create table tipo_almacen_materiales(
    id int(11) auto_increment primary key,
    material_id int(11) not null,
    almacen_id int(11) not null,
    estado tinyint(1) not null,
    foreign key (almacen_id) references tipo_almacen(id)
);
DROP TABLE IF EXISTS tipo_movimiento_materiales_solicitud;
create table tipo_movimiento_materiales_solicitud(
    id int(11) auto_increment primary key,
    estatus_id int(11) not null,
    tipo_orden_materiales_solicitud_id int(11) not null,
    almacen_id int(11) null,
    fecha DATETIME,
    fecha_espera DATETIME,
    nota varchar(1000) null,
    estado tinyint(1) not null,
    cantidad int(11) null,
    foreign key (almacen_id) references tipo_almacen(id),
    foreign key (tipo_orden_materiales_solicitud_id) references tipo_orden_materiales_solicitud(id),
    foreign key (estatus_id) references tipo_orden_estatus(id)
);

DROP TABLE IF EXISTS tipo_almacen_vendor;
create table tipo_almacen_vendor(
    id int(11) auto_increment primary key,
    almacen_id int(11) not null,
    proyecto_id int(11) not null,
    cantidad int(11) not null,
    cantidad_usanda int(11) not null,
    estado tinyint(1) not null,
    foreign key (almacen_id) references tipo_almacen(id)
);
DROP TABLE IF EXISTS tipo_tranferencia_envio;
create table tipo_tranferencia_envio(
    id int(11) auto_increment primary key,
    sub_empleoye_id int(11) not null,
    fecha_actividad DATETIME,
    nota varchar(1000) null,
    estatus_id int(11) not null,
    almacen_id int(11) not null,
    firma_entrega text,
    firma_foreman text,
    fecha_entrega DATETIME,
    fecha_foreman DATETIME,
    estado tinyint(1) not null,
    foreign key (estatus_id) references tipo_orden_estatus(id),
    foreign key (almacen_id) references tipo_almacen(id)
);
DROP TABLE IF EXISTS tipo_transferencia_almacen;
create table tipo_transferencia_almacen(
    id int(11) auto_increment primary key,
    almacen_id int(11) not null,
    tipo_orden_materiales_solicitud_id int(11) not null,
    tranferencia_envio_id int(11) not null,
    estado tinyint(1) not null,
    foreign key (almacen_id) references tipo_almacen(id),
    foreign key (tipo_orden_materiales_solicitud_id) references tipo_orden_materiales_solicitud(id),
    foreign key (tranferencia_envio_id) references tipo_tranferencia_envio(id)
);


DROP TABLE IF EXISTS tipo_tranferencia_envio;
DROP TABLE IF EXISTS tipo_transferencia_almacen;
DROP TABLE IF EXISTS tipo_almacen_vendor;
DROP TABLE IF EXISTS tipo_almacen;
DROP TABLE IF EXISTS tipo_movimiento_materiales_solicitud;
DROP TABLE IF EXISTS tipo_solicitud_orden_vendedor;
DROP TABLE IF EXISTS tipo_orden_materiales_solicitud;
DROP TABLE IF EXISTS tipo_orden_materiales;
DROP TABLE IF EXISTS tipo_orden;
DROP TABLE IF EXISTS tipo_orden_material;
DROP TABLE IF EXISTS tipo_orden_estatus;

/**/
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('NYO', 'Not Yet Ordered', '1');
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('NA', 'Not Approved', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('OR', 'Ordered', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('RE', 'Received', '1');
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('PD', 'Parcial Delivered', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('FD', 'Fully Delivered', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('DR', 'Deliver Requested', '1');
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('IT', 'In Transit', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('CU', 'Completed/Used', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre, estado)
VALUES ('SAV', 'Stored at Vendor', 1);
/**/
INSERT INTO tipo_orden_material(nombre, estado)
VALUES ('material', 1);
INSERT INTO tipo_orden_material(nombre, estado)
VALUES ('equipment', 1);
/*Almacenes*/
INSERT INTO tipo_almacen (proyecto_id, nombre, fecha_registro,estado)
VALUES ('1403', 'Almacen Thurgood Marshall Space Realig','2020-01-01 00:00:00', '1');
INSERT INTO tipo_almacen (proyecto_id, nombre, fecha_registro,estado)
VALUES ('1230', 'Almacen hurgood Marshall Space Realig','2020-01-01 00:00:00', '1');
INSERT INTO tipo_almacen (proyecto_id, nombre, fecha_registro,estado)
VALUES ('1237', 'Almacen JPMC Bank Rockville Pike','2020-01-01 00:00:00', '1');

/*almacen proveedor*/

INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('37', '4',0,0, '1');

INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES('37', '3',0,0, '1');
INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('38', '4',0,0, '1');

INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('38', '3',0,0, '1');

INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('39', '4',0,0, '1');
INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('38', '1403',0,0, '1');
INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('37', '1230',0,0, '1');

INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('37', '1',0,0, '1');

INSERT INTO tipo_almacen_vendor (almacen_id,proyecto_id, cantidad,cantidad_usanda,estado)
VALUES ('38', '1',0,0, '1');


