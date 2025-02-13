DROP TABLE IF EXISTS goal_problem;
create table goal_problem(
    id int(11) auto_increment primary key,
    descripcion varchar(1000) not null,
    estado tinyint(1)
);
DROP TABLE IF EXISTS goal_consecuencia;
create table goal_consecuencia(
    id int(11) auto_increment primary key,
    descripcion varchar(1000) not null,
    estado tinyint(1),
    goal_problem_id int(11) null
);
DROP TABLE IF EXISTS goal_solucion;
create table goal_solucion(
    id int(11) auto_increment primary key,
    descripcion varchar(1000) not null,
    estado tinyint(1),
    goal_consecuencia_id int(11) null
);
SELECT *
from goal_problem;
SELECT goal_problem.id as goal_problem_id,
    goal_problem.descripcion as descripcion_problema,
    goal_consecuencia.id as goal_consecuencia_id,
    goal_consecuencia.descripcion as descripcion_consecuencia
FROM goal_problem
    LEFT JOIN goal_consecuencia on goal_consecuencia.goal_problem_id = goal_problem.id;
   
SELECT *
from goal_consecuencia;
SELECT *
from goal_solucion;
SELECT *
from informe_proyecto;
SELECT *
from proyectos where Nombre LIKE '%Eliot%';

/*campos extras para report visit*/
ALTER TABLE informe_proyecto
ADD email_send int;
ALTER TABLE informe_proyecto
ADD descargas int;