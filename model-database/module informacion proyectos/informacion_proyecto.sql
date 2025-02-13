##test
DROP TABLE IF EXISTS proyecto_info;
create table proyecto_info (
  id int(11) auto_increment primary key,
  fecha_proyecto_movimiento DATETIME not null,  
  proyecto_id int(11) not null,
  #datos de info
  contact_id int DEFAULT '0',
  submittals_id VARCHAR(512) DEFAULT ' ',
  plans_id int DEFAULT '0',
  const_schedule_id int DEFAULT '0',
  field_folder_id int DEFAULT '0',
  brake_down_id int DEFAULT '0',
  badges_id VARCHAR(512) DEFAULT ' ',
  vendor_id VARCHAR(512) DEFAULT ' ',
  special_material_id VARCHAR(512) DEFAULT ' '
);
DROP TABLE IF EXISTS proyecto_date_proyecto;
create table proyecto_date_proyecto (
  id int(11) auto_increment primary key,
  fecha_proyecto_movimiento DATETIME not null,  
  proyecto_id int(11) not null,
  nota TEXT null,
 #datos de proyectos
  Fecha_Inicio DATE null,
  Fecha_Fin DATE null
);

DROP TABLE IF EXISTS proyecto_detail;
create table proyecto_detail (
  id int(11) auto_increment primary key,
  fecha_proyecto_movimiento DATETIME not null,
  proyecto_id int(11) not null,
  report_weekly TEXT null,
  action_for_week TEXT null
);

DROP TABLE IF EXISTS proyecto_info_status;
create table proyecto_info_status (
  id int(11) auto_increment primary key,
  nombre_status VARCHAR(512) DEFAULT ' ',
  status_color VARCHAR(512) DEFAULT ' '
);
#insert 
INSERT INTO proyecto_info_status(nombre_status, status_color) VALUES ('Pending','warning');
INSERT INTO proyecto_info_status(nombre_status, status_color) VALUES ('Incomplete','danger');
INSERT INTO proyecto_info_status(nombre_status, status_color) VALUES ('Completed','success');