DROP TRIGGER IF EXISTS tr_visit_report_new;
DELIMITER //
create trigger tr_visit_report_new after insert on informe_proyecto
   for each row
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"visit_report", NEW.Informe_ID,"nuevo",now());
//

DROP TRIGGER IF EXISTS tr_visit_report_edit;
DELIMITER //
create trigger tr_visit_report_edit after update on informe_proyecto
 for each row
    IF NEW.delete_informe_proyecto=0 THEN
     insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"visit_report", NEW.Informe_ID,"eliminado",now());
    ELSE
     IF OLD.estado='creado' THEN
      insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"visit_report", NEW.Informe_ID,"editado",now());
       ELSE
       insert into appsync(usuario,tabla,id,evento,fecha) values (USER(),"visit_report", NEW.Informe_ID,"nuevo",now());
        END IF;
    END IF;
//
