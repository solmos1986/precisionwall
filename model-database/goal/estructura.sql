DROP TABLE IF EXISTS visit_report_material;
create table visit_report_material (
    id int(11) auto_increment primary key,
    material_id int(11) not null,
    nota VARCHAR(512) DEFAULT '',
    cantidad int(11) DEFAULT 0,
    #datos de info
    estado VARCHAR(512) DEFAULT 'nuevo',
    superficie_id int(11) not null
);

DROP TABLE IF EXISTS visit_report_view_material;
create table visit_report_view_material (
    id int(11) auto_increment primary key,
    proyecto_id int(11) not null,
    superficie_id  int(11) not null,
    verificado VARCHAR(10) DEFAULT 'no'
);

DROP TABLE IF EXISTS visit_report_orden;
create table visit_report_orden (
    id int(11) auto_increment primary key,
    creado_por int(11) not null,
    proyecto_id int(11) not null,
    tipo_orden_id  int(11) not null,
    estado VARCHAR(512) DEFAULT 'nuevo'
);

 DROP TABLE IF EXISTS visit_report_superficie;
/*create table visit_report_superficie (
    id int(11) auto_increment primary key,
    nombre text not null,
    codigo text not null,
    descripcion VARCHAR(512) DEFAULT '',
    #datos de info
    estado VARCHAR(512) DEFAULT 'nuevo',
    proyecto_id int(11) not null
); */