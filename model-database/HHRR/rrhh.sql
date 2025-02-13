/*recursos humanos evento movimiento*/

create table
    tipo_evento(
        tipo_evento_id int(11) auto_increment primary key,
        nombre varchar(250) not null,
        descripcion varchar(250) null,
        estado char(1) not null
    );

create table
    evento(
        cod_evento int(11) auto_increment primary key,
        nombre varchar(250) not null,
        descripcion varchar(250),
        duracion_day int(11) not null,
        note varchar(250) null,
        /*access_code varchar(250) not null,*/
        access_pers char(1) not null,
        report_alert int(3) not null,
        estado char(1) not null,
        tipo_evento_id int(11),
        foreign key (tipo_evento_id) references tipo_evento(tipo_evento_id)
    );

create table
    movimientos_eventos(
        movimientos_eventos_id int(11) auto_increment primary key,
        cod_evento int(11),
        Empleado_ID int(11),
        start_date date not null,
        exp_date date not null,
        note varchar(250) null,
        raise_from varchar(250) not null,
        raise_to varchar(250) not null,
        estado char(1) not null,
        doc_pdf text null,
        /*foreign key (cod_pers) references personal(Empleado_ID),*/
        foreign key (cod_evento) references evento(cod_evento)
    );

create table
    personal_eventos(
        cod_evento int(11),
        Empleado_ID int(11),
        foreign key (cod_evento) references evento(cod_evento)
    );

/* fin recursos humanos evento movimiento*/

/*formulario*/

create table
    form_formulario(
        formulario_id int(11) auto_increment primary key,
        titulo varchar(250) not null,
        fecha_creacion date not null,
        Empleado_ID int(11),
        descripcion varchar(250),
        estado char(1) not null
        /*foreign key (Empleado_ID) references personal(Empleado_ID)*/
    );

/* evaluacion*/

create table
    evaluaciones(
        evaluacion_id int(11) auto_increment primary key,
        foreman_id int(11),
        note text null,
        fecha_asignacion date not null,
        estado char(1) not null,
        formulario_id int(11),
        foreign key (formulario_id) references form_formulario(formulario_id)
    );

create table
    personal_evaluaciones(
        personal_evaluaciones_id int(11) auto_increment primary key,
        evaluacion_id int(11),
        Empleado_ID int(11),
        estado_formulario char(1) not null,
        estado char(1) not null,
        /*foreign key (Empleado_ID) references personal(Empleado_ID),*/
        foreign key (evaluacion_id) references evaluaciones(evaluacion_id)
    );

/* end evaluacion*/

create table
    form_seccion(
        form_seccion_id int(11) auto_increment primary key,
        descripcion varchar(250) null,
        subtitulo varchar(250) null,
        formulario_id int(11),
        estado char(1) not null,
        foreign key (formulario_id) references form_formulario(formulario_id)
    );

create table
    form_pregunta(
        form_pregunta_id int(11) auto_increment primary key,
        pregunta varchar(250) not null,
        tipo varchar(250) not null,
        form_seccion_id int(11),
        estado char(1) not null,
        foreign key (form_seccion_id) references form_seccion(form_seccion_id)
    );

create table
    form_respuestas(
        form_respuesta_id int(11) auto_increment primary key,
        val text null,
        valor text null,
        form_pregunta_id int(11),
        estado char(1) not null,
        foreign key (form_pregunta_id) references form_pregunta(form_pregunta_id)
    );

create table
    respuestas_personal_evaluaciones(
        respuestas_personal_evaluaciones_id int(11) auto_increment primary key,
        form_respuesta_id int(11),
        personal_evaluaciones_id int(11),
        respuesta text null,
        estado_evaluacion char(1) not null,
        estado char(1) not null,
        foreign key (form_respuesta_id) references form_respuestas(form_respuesta_id),
        foreign key (personal_evaluaciones_id) references personal_evaluaciones(personal_evaluaciones_id)
    );

/* end formulario*/

create table
    how_areas(
        how_areas_id int(11) auto_increment primary key,
        nombre varchar(250) not null,
        descripcion varchar(250) not null
    );

/*create table evaluation_areas(
 evaluation_areas_id int(11) auto_increment primary key,
 nombre varchar(250) not null,
 descripcion varchar(250) not null,
 how_areas_id int(11),
 foreign key (how_areas_id) references how_areas(how_areas_id)
 );*/


create table
    form_areas(
        evaluation_areas_id int(11),
        evaluation_form_id int(11),
        foreign key (evaluation_areas_id) references evaluation_areas(evaluation_areas_id),
        foreign key (evaluation_form_id) references evaluation_form(evaluation_form_id)
    );

create table
    question(
        question_id int(11) auto_increment primary key,
        nombre varchar(250) not null,
        descripcion varchar(250) not null,
        t_input text
    );

create table
    sub_question(
        sub_question_id int(11) auto_increment primary key,
        titulo varchar(250) not null,
        question_id int(11),
        foreign key (question_id) references question(question_id)
    );

create table
    question_pivot(
        question_pivot_id int(11) auto_increment primary key,
        key_question_pivot varchar(250) not null,
        value_question_pivot varchar(250) not null,
        sub_question_id int(11),
        foreign key (sub_question_id) references sub_question(sub_question_id)
    );

##roles
DROP TABLE IF EXISTS  cargo_personal;
create table
    cargo_personal(
        id int(11) auto_increment primary key,
        nombre varchar(250) not null,
        descripcion varchar(250) null
    );

INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('.','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Accauntan','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Apprentice','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Apprentice (L7)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Apprentice A1','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('apprentice/DC','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Aprentice (L7)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Aprentice / Dc','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Aprentice/DC','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('c5','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('CEO','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('F.','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Field Coordination','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Field Coordinator','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Finisher','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Foreman (L2)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Foreman A (L1)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Foreman A (L1)/Super','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Foreman B (L2)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('G.','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('He did not show up n','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Helper','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Helper / DC','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Helper /DC','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Helpers','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('J.','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Jairo said he is not','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Labor','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Lead  (L4)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Lead (L4)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Lead Foreman (L3)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Lead-Foreman (L3)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Leader','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('N','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('PA','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Painter','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Painter (L5)','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Painter (L5)-','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Painter / DC','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Painter /DC','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Payroll','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('PM','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Sub','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Sub Scuffmaster','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Super','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Superintendent','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('SuperUser','');
INSERT INTO `cargo_personal`(`nombre`, `descripcion`) VALUES ('Warehouse','');



DROP TABLE IF EXISTS   tipo_personal;
create table
    tipo_personal(
        id int(11) auto_increment primary key,
        nombre varchar(250) not null,
        descripcion varchar(250) null
    );

INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('F','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('FS','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('FT','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('FU','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('FX','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('FXz','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('FY','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('SF','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('Sub','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('t','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('t.','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('V','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('w.Adm','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z,Adm','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.Adm','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.Adm  07/28/17',''); 
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.Adm.','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.Admin','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.Arm','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.F','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('z.Vicente','');
INSERT INTO `tipo_personal`(`nombre`, `descripcion`) VALUES ('ZADM','');
# rol nuevo
INSERT INTO `roles_app`( `nombre`) VALUES ('super admin');

DROP TABLE IF EXISTS movimientos_eventos_archivos;
create table
    movimientos_eventos_archivos(
        m_imagen_id int(11) auto_increment primary key,
        tipo text not null,
        imagen text not null,
        movimientos_eventos_id  int(11) not null,
        caption text not null,
        size text not null
    );
