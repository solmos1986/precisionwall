DROP TABLE IF EXISTS appsync;
CREATE TABLE appsync (
appsync_id int auto_increment primary key not null,
usuario varchar(50) not null,
tabla varchar(50) not null,
id int not null,
evento varchar (50) not null,
fecha TIMESTAMP not null
);

DROP TRIGGER IF EXISTS tr_ticket_new;
DELIMITER //
create trigger tr_ticket_new after insert on ticket
   for each row
 IF NEW.estado='creado' THEN
      insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"ticket", NEW.ticket_id,"nuevo",now());
        END IF;
//

DROP TRIGGER IF EXISTS tr_ticket_edit;
DELIMITER //
create trigger tr_ticket_edit after update on ticket
   for each row
    IF NEW.delete=1 THEN
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"ticket", NEW.ticket_id,"eliminado",now());
    ELSE
     IF NEW.estado='creado' && NEW.estado!=null THEN
      insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"ticket", NEW.ticket_id,"nuevo",now());
        ELSE
          insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"ticket", NEW.ticket_id,"editado",now());
        END IF;
    END IF;
//
/*actividades*/
DROP TRIGGER IF EXISTS tr_actividades_new;
DELIMITER //
create trigger tr_actividades_new after insert on actividades
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"actividades", NEW.Actividad_ID,"nuevo",now());
//

DROP TRIGGER IF EXISTS tr_actividades_edit;
DELIMITER //
create trigger tr_actividades_edit after update on actividades
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"actividades", NEW.Actividad_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_actividades_delete;
DELIMITER //
create trigger tr_actividades_delete after delete on actividades
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"actividades", OLD.Actividad_ID,"eliminado",now());
//
/*test*/
INSERT INTO `actividades`( `Pro_ID`, `Tipo_Actividad_ID`, `Descripcion`, `Fecha`, `Hora`, `Aux1`, `Aux2`, `Aux3`, `Estatus`, `Color`, `Aux4`) VALUES ('8','1','test','2021-08-07','00:00:00','1','2','3','4','5','6')

/*ticket_material*/
DROP TRIGGER IF EXISTS tr_ticket_material_new;
DELIMITER //
create trigger tr_ticket_material_new after insert on ticket_material
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_material", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_ticket_material_edit;
DELIMITER //
create trigger tr_ticket_material_edit after update on ticket_material
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_material", NEW.id, "editado", now());
//

DROP TRIGGER IF EXISTS tr_ticket_material_delete;
DELIMITER //
create trigger tr_ticket_material_delete after delete on ticket_material
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_material", OLD.id, "eliminado", now());
//

/*ticket_trabajadores*/
DROP TRIGGER IF EXISTS tr_ticket_trabajadores_new;
DELIMITER //
create trigger tr_ticket_trabajadores_new after insert on ticket_trabajadores
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_trabajadores", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_ticket_trabajadores_edit;
DELIMITER //
create trigger tr_ticket_trabajadores_edit after update on ticket_trabajadores
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_trabajadores", NEW.id, "editado", now());
//

DROP TRIGGER IF EXISTS tr_ticket_trabajadores_delete;
DELIMITER //
create trigger tr_ticket_trabajadores_delete after delete on ticket_trabajadores
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_trabajadores", OLD.id, "eliminado", now());
//

/*ticket_imagen*/
DROP TRIGGER IF EXISTS tr_ticket_imagen_new;
DELIMITER //
create trigger tr_ticket_imagen_new after insert on ticket_imagen
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_imagen", NEW.t_imagen_id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_ticket_imagen_edit;
DELIMITER //
create trigger tr_ticket_imagen_edit after update on ticket_imagen
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_imagen", NEW.t_imagen_id, "editado", now());
//

DROP TRIGGER IF EXISTS tr_ticket_imagen_delete;
DELIMITER //
create trigger tr_ticket_imagen_delete after delete on ticket_imagen
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_imagen", OLD.t_imagen_id, "eliminado", now());
//

/*personal*/
DROP TRIGGER IF EXISTS tr_personal_new;
DELIMITER //
create trigger tr_personal_new after insert on personal
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "personal", NEW.Empleado_ID, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_personal_edit;
DELIMITER //
create trigger tr_personal_edit after update on personal
  for each row
    IF NEW.status=0 THEN
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"personal", NEW.Empleado_ID,"eliminado",now());
    ELSE
     IF NEW.status=1 THEN
      insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"personal", NEW.Empleado_ID,"editado",now());
        END IF;
    END IF;
//

DROP TRIGGER IF EXISTS tr_personal_delete;
DELIMITER //
create trigger tr_personal_delete after delete on personal
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"personal", OLD.Empleado_ID,"eliminado",now());
//

/*areas*/
DROP TRIGGER IF EXISTS tr_area_control_new;
DELIMITER //
create trigger tr_area_control_new after insert on area_control
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "area_control", NEW.Area_ID, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_area_control_edit;
DELIMITER //
create trigger tr_area_control_edit after update on area_control
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"area_control", NEW.Area_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_area_control_delete;
DELIMITER //
create trigger tr_area_control_delete after delete on area_control
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"area_control", OLD.Area_ID,"eliminado",now());
//

/*razon trabajo*/
DROP TRIGGER IF EXISTS tr_razon_trabajo_new;
DELIMITER //
create trigger tr_razon_trabajo_new after insert on razontrabajo
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "razon_trabajo", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_area_control_edit;
DELIMITER //
create trigger tr_area_control_edit after update on razontrabajo
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"razon_trabajo", NEW.id,"editado",now());
//

DROP TRIGGER IF EXISTS tr_area_control_delete;
DELIMITER //
create trigger tr_area_control_delete after delete on razontrabajo
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"razon_trabajo", OLD.id,"eliminado",now());
//
/*materiales*/
DROP TRIGGER IF EXISTS tr_materiales_new;
DELIMITER //
create trigger tr_materiales_new after insert on materiales
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "materiales", NEW.Mat_ID, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_materiales_edit;
DELIMITER //
create trigger tr_materiales_edit after update on materiales
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"materiales", NEW.Mat_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_materiales_delete;
DELIMITER //
create trigger tr_materiales_delete after delete on materiales
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"materiales", OLD.Mat_ID,"eliminado",now());
//

/*tipo_trabajo*/
DROP TRIGGER IF EXISTS tr_tipo_trabajo_new;
DELIMITER //
create trigger tr_tipo_trabajo_new after insert on tipo_trabajo
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "tipo_trabajo", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_tipo_trabajo_edit;
DELIMITER //
create trigger tr_tipo_trabajo_edit after update on tipo_trabajo
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"tipo_trabajo", NEW.id,"editado",now());
//

DROP TRIGGER IF EXISTS tr_tipo_trabajo_delete;
DELIMITER //
create trigger tr_tipo_trabajo_delete after delete on tipo_trabajo
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"tipo_trabajo", OLD.id,"eliminado",now());
//

/*mail_contact*/
DROP TRIGGER IF EXISTS tr_contacto_proyecto_new;
DELIMITER //
create trigger tr_contacto_proyecto_new after insert on contacto_proyecto
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "contacto_proyecto", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_contacto_proyecto_edit;
DELIMITER //
create trigger tr_contacto_proyecto_edit after update on contacto_proyecto
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"contacto_proyecto", NEW.id,"editado",now());
//

DROP TRIGGER IF EXISTS tr_contacto_proyecto_delete;
DELIMITER //
create trigger tr_contacto_proyecto_delete after delete on contacto_proyecto
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"contacto_proyecto", OLD.id,"eliminado",now());
//

/*mail_ticket*/
DROP TRIGGER IF EXISTS tr_mail_ticket_new;
DELIMITER //
create trigger tr_mail_ticket_new after insert on proyectos
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "mail_ticket", NEW.Pro_ID, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_mail_ticket_edit;
DELIMITER //
create trigger tr_mail_ticket_edit after update on proyectos
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"mail_ticket", NEW.Pro_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_mail_ticket_delete;
DELIMITER //
create trigger tr_mail_ticket_delete after delete on proyectos
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"mail_ticket", OLD.Pro_ID,"eliminado",now());
//

/*ticket_material*/
DROP TRIGGER IF EXISTS tr_ticket_material_new;
DELIMITER //
create trigger tr_ticket_material_new after insert on ticket_material
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "ticket_material", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_ticket_material_edit;
DELIMITER //
create trigger tr_ticket_material_edit after update on ticket_material
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"ticket_material", NEW.id,"editado",now());
//

DROP TRIGGER IF EXISTS tr_ticket_material_delete;
DELIMITER //
create trigger tr_ticket_material_delete after delete on ticket_material
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"ticket_material", OLD.id,"eliminado",now());
//
/*tabajadores_material*/
DROP TRIGGER IF EXISTS tr_trabajadores_material_new;
DELIMITER //
create trigger tr_ticket_trabajadores_material_new after insert on ticket_trabajadores
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "trabajadores_material", NEW.id, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_ticket_trabajadores_material_edit;
DELIMITER //
create trigger tr_ticket_trabajadores_material_edit after update on ticket_trabajadores
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"trabajadores_material", NEW.id,"editado",now());
//

DROP TRIGGER IF EXISTS tr_ticket_trabajadores_material_delete;
DELIMITER //
create trigger tr_ticket_trabajadores_material_delete after delete on ticket_trabajadores
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"trabajadores_material", OLD.id,"eliminado",now());


/*registro diario*/
DROP TRIGGER IF EXISTS tr_registro_diario_new;
DELIMITER //
create trigger tr_registro_diario_new after insert on registro_diario 
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "registro_diario", NEW.Reg_ID, "nuevo", now());
//


DROP TRIGGER IF EXISTS tr_registro_diario_edit;
DELIMITER //
create trigger tr_registro_diario_edit after update on registro_diario
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"registro_diario", NEW.Reg_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_registro_diario_delete;
DELIMITER //
create trigger tr_registro_diario_delete after delete on registro_diario
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"registro_diario", OLD.Reg_ID,"eliminado",now());
//


