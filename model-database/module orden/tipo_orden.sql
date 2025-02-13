create table tipo_orden(
    id int(11) auto_increment primary key,
    sub_contractor_id int(11) not null,
    sub_empleoye_id int(11) not null,
    proyecto_id int(11) not null,
    estado_orden varchar(50) not null,
    num int(11) not null,
    nombre_trabajo varchar(300) not null,
    estado varchar(50) not null,
    fecha_order DATETIME,
    fecha_trabajo DATETIME ,
    fecha_entrega DATETIME,
    fecha_foreman DATETIME,
    firma_installer text,
    firma_foreman text,
    eliminado tinyint(1),
    creado_por int(11) not null
);
create table tipo_orden_materiales(
    id int(11) auto_increment primary key,
    material_id int(11) not null,
    tipo_orden_id int(11) not null,
    cant_ordenada int(11) not null,
    cant_sitio_trab int(11) ,
    entregado varchar(50) not null,
    foreign key (tipo_orden_id) references tipo_orden(id)
);
create table tipo_orden_materiales_recojer_equipo(
    id int(11) auto_increment primary key,
    tipo_orden_materiales_id int(11) not null,
    estatus varchar(50) not null,
    fecha DATETIME,
    cant_dia int(11),
    foreign key (tipo_orden_materiales_id) references tipo_orden_materiales(id)
);
create table tipo_orden_materiales_material(
    id int(11) auto_increment primary key,
    tipo_orden_materiales_id int(11) not null,
    cant_instalada int(11),
    fecha_instalada DATETIME,
    cant_restante int(11),
    cant_almacenada varchar(300),
    foreign key (tipo_orden_materiales_id) references tipo_orden_materiales(id)
);
create table tipo_orden_materiales_movimiento(
    id int(11) auto_increment primary key,
    tipo_orden_materiales_id int(11) not null,
    cantidad int(11),
    fecha DATETIME,
    estatus varchar(50) not null,
    vendedor_id int(11),
    foreign key (tipo_orden_materiales_id) references tipo_orden_materiales(id)

);
create table tipo_orden_imagen(
    id int(11) auto_increment primary key,
    tipo_orden_id  int(11) not null,
    imagen text not null,
    tipo text not null,
    caption text not null,
    size text not null,
    foreign key (tipo_orden_id) references tipo_orden(id)
);


INSERT INTO roles_app(nombre) VALUES('order - delivery');
INSERT INTO roles_app(nombre) VALUES('order - wharehouse');

1
ALTER TABLE tipo_movimiento_pedido RENAME tipo_movimiento_material_pedido;