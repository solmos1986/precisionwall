#test subir archivos DROP TABLE IF EXISTS import_file;

create table import_file (
    id int(11) auto_increment primary key,
    nombre text not null,
    url text not null,
    #datos de info
    sincronizacion int null,
    estado VARCHAR(512) DEFAULT ' '
);

DROP TABLE IF EXISTS estimado_superficie;

#test subir archivos
create table estimado_superficie (
    id int(11) auto_increment primary key,
    nombre text not null,
    codigo text not null,
    descripcion text not null,
    #datos de info
    estado VARCHAR(512) DEFAULT ' ',
    miselaneo text null
);

DROP TABLE IF EXISTS estimado_estandar;
create table estimado_estandar (
    id int(11) auto_increment primary key,
    nombre text not null,
    codigo text null,
    descripcion text null,
    #datos de info
    estado VARCHAR(512) DEFAULT ' ',
    estimado_superficie_id int(11) not null
);

DROP TABLE IF EXISTS estimado_metodo;

create table estimado_metodo (
    id int(11) auto_increment primary key,
    nombre text not null,
    descripcion text null,
    codigo text null,
    unidad_medida text null,
    rate_hour FLOAT null,
    defauld text null,
    num_coast FLOAT null,
    material_cost_unit FLOAT null,
    material_unit_med text null,
    materal_spread FLOAT null,
    color text null,
    procedimiento text null,
    estimado_estandar_id int(11) not null,
    mark_up FLOAT null
);

DROP TABLE IF EXISTS estimado_use_import;

create table estimado_use_import (
    id int(11) auto_increment primary key,
    area text not null,
    cost_code text null,
    area_description text null,
    cc_descripcion text null,
    cc_butdget_qty DECIMAL (12, 2) null,
    um text null,
    of_coast DECIMAL (12, 2) null,
    pwt_prod_rate DECIMAL (12, 2) null,
    estimate_hours DECIMAL (12, 2) null,
    estimate_labor_cost DECIMAL (12, 2) null,
    material_or_equipment_unit_cost DECIMAL (12, 2) null,
    material_spread_rate_per_unit DECIMAL (12, 2) null,
    mat_qty_or_galon DECIMAL (12, 2) null,
    mat_um text null,
    material_cost DECIMAL (12, 2) null,
    buscontract_cost DECIMAL (12, 2) DEFAULT 0 ,
    equipament_cost DECIMAL (12, 2)  DEFAULT 0,
    other_cost DECIMAL (12, 2)  DEFAULT 0,
    estado text null,
    /*llaves*/
    price_total DECIMAL (12, 2) null,
    price_each DECIMAL (12, 2) null,
    estimado_superficie_id int(11) not null,
    estimado_use_id int(11) null,
    nombre_area text not null,
    mark_up DECIMAL (12, 2) null,
	/*nuevo*/
	default_import text null
    /*modo prueba*/
    precio_segun_avance DECIMAL (12, 2) DEFAULT 0,
);

DROP TABLE IF EXISTS estimado_use_metodo;

create table estimado_use_metodo (
    estimado_use_import_id int(11) not null,
    id int(11) auto_increment primary key,
    estimado_metodo_id int(11) not null,
    estimado_estandar_id int(11) not null,
    estado text null,
    estimado_use_id int(11) not null
);

/*copia*/

DROP TABLE IF EXISTS estimado_use_import_original;

create table estimado_use_import_original (
    id int(11) auto_increment primary key,
    area text not null,
    cost_code text null,
    area_description text null,
    cc_descripcion text null,
    cc_butdget_qty DECIMAL (12, 2) null,
    um text null,
    of_coast DECIMAL (12, 2) null,
    pwt_prod_rate DECIMAL (12, 2) null,
    estimate_hours DECIMAL (12, 2) null,
    estimate_labor_cost DECIMAL (12, 2) null,
    material_or_equipment_unit_cost DECIMAL (12, 2) null,
    material_spread_rate_per_unit DECIMAL (12, 2) null,
    mat_qty_or_galon DECIMAL (12, 2) null,
    mat_um text null,
    material_cost DECIMAL (12, 2) null,
    buscontract_cost DECIMAL (12, 2) null,
    equipament_cost DECIMAL (12, 2) null,
    other_cost DECIMAL (12, 2) null,
    estado text null,
    /*llaves*/
    price_total DECIMAL (12, 2) null,
    price_each DECIMAL (12, 2) null,
    estimado_superficie_id int(11) not null,
    estimado_use_id int(11) null,
    nombre_area text not null,
    mark_up DECIMAL (12, 2) null
);

DROP TABLE IF EXISTS estimado_use_metodo_original;

create table estimado_use_metodo_original (
    estimado_use_import_id int(11) not null,
    id int(11) auto_increment primary key,
    estimado_metodo_id int(11) not null,
    estimado_estandar_id int(11) not null,
    estado text null,
    estimado_use_id int(11) not null
);

DROP TABLE IF EXISTS estimado_use;

create table estimado_use (
    id int(11) auto_increment primary key,
    estimado_use_import_id int(11) not null,
    estimado_use_import_original int(11) not null,
    /*copia original*/
    estimado_id int(11) not null
);

DROP TABLE IF EXISTS estimado;

create table estimado (
    id int(11) auto_increment primary key,
    fecha DATETIME null,
    usuario_id text not null,
    descripcion text not null,
    proyecto_id int(11)  null,
    import_proyecto text  null,
    estado text null,
    /*totales*/
    estimated_hours DECIMAL (12, 2) null,
    estimated_labor_hours DECIMAL (12, 2) null,
    material_cost DECIMAL (12, 2) null,
    /* nuevo */
    total_cost DECIMAL (12, 2) null,
    mark_up DECIMAL (12, 2) null,
    sub_contract DECIMAL (12, 2) null,
    equipo DECIMAL (12, 2) null,
    /**/
    price_total DECIMAL (12, 2) null,
    labor_cost DECIMAL (12, 2) null,
    index_prod DECIMAL (12, 2) null
);

DROP TABLE IF EXISTS estimado_gene_info;

create table estimado_gene_info (
    id int(11) auto_increment primary key,
    labor_cost FLOAT null,
    index_prod FLOAT null,
    estado text null
);

INSERT INTO
    `estimado_gene_info`(`labor_cost`, `index_prod`, `estado`)
VALUES
    ('37.93', '1', 'y');

DROP TABLE IF EXISTS estimado_temporal;

create table estimado_temporal (
    id int(11) auto_increment primary key,
    Name FLOAT null,
    Description FLOAT null,
    Qty FLOAT null,
    Units text null,
    Cost_Each FLOAT null,
    Markup FLOAT null,
    Price_Each FLOAT null,
    Price_Total FLOAT null,
    Color text null,
    cost_code text null,
    area text null,
    superficie text null,
    metodo text null
);

/* pendientes rol*/

INSERT INTO `roles_app`( `nombre`) VALUES ('import brake');

/*!modificaciones*/
ALTER TABLE task
ADD import_id int(11) null;
ALTER TABLE estimado_use_import
ADD precio_segun_avance DECIMAL (12, 2) DEFAULT 0;
ALTER TABLE estimado
ADD import_proyecto text null;
ALTER TABLE estimado_gene_info
ADD descripcion text null;
/*task*/

ALTER TABLE task
ADD sov_id text null;
ALTER TABLE task
ADD sov_descripcion text null;
ALTER TABLE task
ADD precio_segun_avance DECIMAL (12, 2) DEFAULT 0;
ALTER TABLE task
ADD precio_total DECIMAL (12, 2) DEFAULT 0;
ALTER TABLE task
ADD cc_butdget_qty DECIMAL (12, 2) DEFAULT 0;
ALTER TABLE task
ADD um text null;