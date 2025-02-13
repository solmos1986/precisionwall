DROP TABLE IF EXISTS tipo_orden_estatus;
create table tipo_orden_estatus(
    id int(11) auto_increment primary key,
    codigo varchar(100) not null,
    nombre varchar(100) not null,
    color varchar(100) null,
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
    eliminado tinyint(1)
);
DROP TABLE IF EXISTS tipo_orden_materiales;
create table tipo_orden_materiales(
    id int(11) auto_increment primary key,
    material_id int(11) not null,
    nota_material varchar(250) null,
    tipo_orden_id int(11) not null,
    tipo_orden_material_id int(11) not null,
    cant_ordenada int(11) not null,
    cant_registrada int(11) not null,
    estado tinyint(1)

);
DROP TABLE IF EXISTS tipo_movimiento_material_pedido;
create table tipo_movimiento_material_pedido(
    id int(11) auto_increment primary key,
    estatus_id int(11) not null,
    Ped_Mat_ID int(11) not null,
    material_id int(11) not null,
    fecha DATETIME,
    fecha_espera DATETIME,
    nota varchar(1000) null,
    estado tinyint(1) not null,
    egreso int(11) null,
    ingreso int(11) null,
    Pro_id_ubicacion int(11) DEFAULT NULL
);
create table tipo_movimiento_pedido(
    id int(11) auto_increment primary key,
    Ped_ID int(11) not null,
    fecha DATETIME,
    fecha_espera DATETIME,
    nota varchar(1000) null
);
DROP TABLE IF EXISTS tipo_tranferencia_envio;
create table tipo_tranferencia_envio(
    id int(11) auto_increment primary key,
    sub_empleoye_id int(11) DEFAULT '' null,
    fecha_actividad DATETIME,
    nota varchar(1000) null,
    estatus_id int(11) not null,
    pedido_id int(11) not null,
    firma_entrega text,
    firma_foreman text,
    fecha_entrega DATETIME,
    fecha_foreman DATETIME,
    estado tinyint(1) not null
);
/*añadir status_id y tipo_orden_id  la tabla pedidos */
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('NYO', 'Not Yet Ordered','primary', '1');
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('NA', 'Not Approved','primary', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('OR', 'Ordered','primary', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('RE', 'Received','primary', '1');
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('PD', 'Parcial Delivered','primary', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('FD', 'Fully Delivered','primary', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('DR', 'Deliver Requested','primary', '1');
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('IT', 'In Transit','primary', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('CU', 'Completed/Used','primary', 1);
INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('SAV', 'Stored at Vendor','primary', 1);


SELECT * FROM tipo_orden_estatus;
SELECT * FROM personal;
SELECT * FROM tipo_orden;
SELECT * FROM tipo_orden_materiales;
SELECT * FROM tipo_orden_material;
SELECT * FROM proyectos;
SELECT * FROM pedidos;

SELECT * FROM tipo_movimiento_pedido;
SELECT * FROM pedidos_material;
SELECT * FROM  tipo_tranferencia_envio;

SELECT * FROM tipo_movimiento_pedido where Ped_Mat_ID=35708;
INSERT INTO roles_app(nombre) VALUES('order - wharehouse');


ALTER TABLE tipo_movimiento_pedido RENAME tipo_movimiento_material_pedido;

INSERT INTO tipo_orden_estatus(codigo, nombre,color, estado)
VALUES ('CR', 'Completed/Received','primary', 1);