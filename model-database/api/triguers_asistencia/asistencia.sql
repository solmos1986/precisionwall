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

/* actividades empleados */
DROP TRIGGER IF EXISTS tr_actividad_personal_new;
DELIMITER //
create trigger tr_actividad_personal_new after insert on actividad_personal 
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "actividad_personal", NEW.Actividad_ID, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_actividad_personal_edit;
DELIMITER //
create trigger tr_actividad_personal_edit after update on actividad_personal
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"actividad_personal", NEW.Actividad_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_actividad_personal_delete;
DELIMITER //
create trigger tr_actividad_personal_delete after delete on actividad_personal
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"actividad_personal", OLD.Actividad_ID,"eliminado",now());
//
/* actividades  */
DROP TRIGGER IF EXISTS tr_actividades_new;
DELIMITER //
create trigger tr_actividades_new after insert on actividades
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "actividades", NEW.Actividad_ID, "nuevo", now());
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
/* registro diario actividad  */
DROP TRIGGER IF EXISTS tr_registro_diario_actividad_new;
DELIMITER //
create trigger tr_registro_diario_actividad_new after insert on registro_diario_actividad
  for each row
    insert into appsync(usuario,tabla,id,evento,fecha) values (USER(), "registro_diario_actividad", NEW.RDA_ID, "nuevo", now());
//

DROP TRIGGER IF EXISTS tr_registro_diario_actividad_edit;
DELIMITER //
create trigger tr_registro_diario_actividad_edit after update on registro_diario_actividad
  for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"registro_diario_actividad", NEW.RDA_ID,"editado",now());
//

DROP TRIGGER IF EXISTS tr_registro_diario_actividad_delete;
DELIMITER //
create trigger tr_registro_diario_actividad_delete after delete on registro_diario_actividad
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"registro_diario_actividad", OLD.RDA_ID,"eliminado",now());
//