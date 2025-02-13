DROP TABLE IF EXISTS report_daily_project;

create table
    report_daily_project (
        Pro_ID int not null,
        report_daily_opcion_id int not null,
        used text null
    );

DROP TABLE IF EXISTS report_daily;

create table
    report_daily (
        id int(11) auto_increment primary key,
        nombre text null,
        descripcion text null,
        estado text null
    );

DROP TABLE IF EXISTS report_daily_opcion;

create table
    report_daily_opcion (
        id int(11) auto_increment primary key,
        opcion text null,
        report_daily_id int not null
    );

DROP TABLE IF EXISTS report_daily_valor;

create table
    report_daily_valor (
        id int(11) auto_increment primary key,
        descripcion text null,
        valor text null,
        report_daily_opcion_id int not null
    );

DROP TABLE IF EXISTS report_daily_detalle;

create table
    report_daily_detalle (
        id int(11) auto_increment primary key,
        actividad_id int not null,
        fecha date not null,
        detalle text not null,
        question text null,
        empleado_id int not null,
        estado text not null
    );
DROP TABLE IF EXISTS report_daily_detalle_image;
create table
    report_daily_detalle_image (
        id int(11) auto_increment primary key,
        caption text not null,
        size int not null,
        imagen text not null,
        referencia  text not null,
        report_daily_detalle_id int not null,
        estado VARCHAR(15) DEFAULT '' 
    );
