DROP TABLE IF EXISTS proyectos_nota;

create table
    proyectos_nota (
        id int(11) auto_increment primary key,
        codigo VARCHAR(50) null,
        fecha_registro DATETIME null,
        fecha_entrega DATETIME null,
        titulo VARCHAR(350) null,
        nota TEXT null,
        creado_por int(11) null,
        proyecto_id int(11) null,
        project_manager_id int(11) null,
        asistente_project_manager_id int(11) null,
        foreman_id int(11) null,
        lead_id int(11) null
    );

DROP TABLE IF EXISTS proyecto_nota_imagenes;

create table
    proyecto_nota_imagenes (
        id int(11) auto_increment primary key,
        tipo TEXT null,
        nombre TEXT null,
        proyectos_nota_id int(11) not null,
        caption TEXT null,
        size TEXT null
    );