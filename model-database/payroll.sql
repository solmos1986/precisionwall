-- Active: 1687829054607@@127.0.0.1@3306@sof77com_pwt

-----GUARDAR LISTA DE TIMERLINE STP

DROP TABLE IF EXISTS temp_timerline;

create table
    temp_timerline (
        id int(11) auto_increment primary key,
        descripcion TEXT NOT NULL,
        fechaRegistro DATETIME NOT NULL,
        estado VARCHAR(50) NOT NULL DEFAULT 'pendiente'
    );

DROP TABLE IF EXISTS temp_timerline_data;

create table
    temp_timerline_data (
        id int(11) auto_increment primary key,
        codigoProyecto TEXT NULL,
        nombreProyecto Text NOT NULL,
        Pro_ID INT NOT NULL,
        codigoEdificio Text NOT NULL,
        nombreEdificio Text NOT NULL,
        Edificio_ID INT NOT NULL,
        codigoFloor Text NOT NULL,
        nombreFloor Text NOT NULL,
        Floor_ID INT NOT NULL,
        codigoArea Text NOT NULL,
        nombreArea Text NOT NULL,
        Area_ID INT NOT NULL,
        costCode Text NOT NULL,
        nombreTrabajo Text NOT NULL,
        Task_ID INT NOT NULL,
        hours Text NOT NULL,
        temp_timerline_id INT NOT NULL
    );

-----GUARDAR LISTA DE EMPLEADOS

DROP TABLE IF EXISTS temp_list_employee;

create table
    temp_list_employee (
        id int(11) auto_increment primary key,
        descripcion TEXT NOT NULL,
        fechaRegistro DATETIME NOT NULL,
        estado VARCHAR(50) NOT NULL DEFAULT 'pendiente'
    );

DROP TABLE IF EXISTS temp_list_employee_data;

create table
    temp_list_employee_data (
        id int(11) auto_increment primary key,
        numero TEXT NULL,
        NickName Text NOT NULL,
        cargo Text NOT NULL,
        tipoPersona Text NOT NULL,
        telefono Text NOT NULL,
        email Text NOT NULL,
        temp_list_employee_id INT NOT NULL
    );

---- MESA DE TRABAJO

DROP TABLE IF EXISTS temp_payroll_job;

create table
    temp_payroll_job (
        id int(11) auto_increment primary key,
        nombre TEXT NOT NULL,
        descripcion TEXT NOT NULL,
        fecha_inicio DATE NOT NULL,
        fecha_fin DATE NOT NULL,
        estado VARCHAR(50) NOT NULL DEFAULT 'pendiente'
    );

DROP TABLE IF EXISTS temp_payroll_job_data;

create table
    temp_payroll_job_data (
        id int(11) auto_increment primary key,
        empleadoId INT NOT NULL,
        NickName TEXT NOT NULL,
        codigoProyecto Text NOT NULL,
        nombreProyecto Text NOT NULL,
        Pro_ID INT NOT NULL,
        nombreEdificio Text NOT NULL,
        codigoEdificio Text NOT NULL,
        Edificio_ID INT NOT NULL,
        nombreFloor Text NOT NULL,
        codigoFloor Text NOT NULL,
        Floor_ID INT NOT NULL,
        nombreArea Text NOT NULL,
        codigoArea Text NOT NULL,
        Area_ID INT NOT NULL,
        nombreTrabajo Text NOT NULL,
        costCode Text NOT NULL,
        Task_ID INT NOT NULL,
        cat Text NOT NULL,
        horas DECIMAL(8, 2) NOT NULL,
        hr_type Text NOT NULL,
        PayId Text NOT NULL,
        work_date DATETIME NOT NULL,
        cert_class Text NOT NULL,
        reimbId Text NOT NULL,
        unit Text NOT NULL,
        um Text NOT NULL,
        rate Text NOT NULL,
        amount Text NOT NULL,
        temp_payroll_job_id INT NOT NULL
    );

SELECT *FROM temp_list_employee;

SELECT *FROM temp_list_employee_data;

SELECt * FROM proyectos as p where p.`Codigo`='001.23.1';

--prueba

SELECT* FROM temp_timerline;

SELECT* FROM temp_timerline_data WHERE id='9355';

DELETE from temp_timerline_data WHERE temp_timerline_id= 1;

SELECT* FROM proyectos as p where p.`Codigo` LIKE '%001.23.1%' ;

SELECT* FROM temp_payroll_job where id= 2;

SELECT *
FROM temp_payroll_job_data
WHERE
    temp_payroll_job_data.temp_payroll_job_id = 2;

SELECT p.`Nombre`, e.*
from edificios as e
    inner join proyectos as p on e.`Pro_ID` = p.`Pro_ID`
WHERE e.`Pro_ID` = 1589;

SELECT p.`Nombre`, f.*
from floor as f
    inner join proyectos as p on f.`Pro_ID` = p.`Pro_ID`
WHERE p.`Pro_ID` = 1589;

SELECT
    p.`Codigo`,
    p.`Nombre` as nombreProyecto,
    e.`Edi_IDT`,
    e.`Nombre` as nombreEdificio,
    f.`Flo_IDT`,
    f.`Nombre` as nombreFloor,
    ac.Are_IDT,
    ac.Nombre as nombreArea,
    t.`Tas_IDT`,
    t.`NumAct`,
    t.`Nombre`
from area_control as ac
    inner join proyectos as p on ac.`Pro_ID` = p.`Pro_ID`
    inner join edificios as e on e.`Pro_ID` = p.`Pro_ID`
    inner join floor as f on f.`Pro_ID` = p.`Pro_ID`
    inner join task as t on t.`Pro_ID` = p.`Pro_ID`
WHERE
    p.`Pro_ID` = 1739
    and f.`Flo_IDT` = '01'
    and ac.`Are_IDT` = '10th'
    and t.`Tas_IDT` = '21.120';

SELECT * FROM task as t ORDER BY t.date;

SELECT *
FROM actividad_personal as ap
    INNER JOIN actividades as ac ON ac.Actividad_ID = ap.Actividad_ID
WHERE
    ac.`Fecha` BETWEEN '2023-02-10' AND '2023-02-25';

SELECT * FROM task where task.`Nombre`;

SELECT
    rd.`Empleado_ID`,
    rd.`Fecha`,
    rda.`Horas_Contract` as horas_actividad,
    t.`Tas_IDT` as code_cost,
    t.`Nombre`,
    arc.`Nombre`,
    f.`Nombre`,
    e.`Nombre`,
    p.`Nombre`
FROM actividades as a
    INNER JOIN registro_diario as rd ON rd.`Actividad_Id` = a.`Actividad_ID`
    INNER JOIN registro_diario_actividad as rda ON rd.`Reg_ID` = rda.`Reg_ID`
    INNER JOIN task as t ON t.`Task_ID` = rda.`Task_ID`
    INNER JOIN area_control as arc ON arc.`Area_ID` = t.`Area_ID`
    INNER JOIN floor as f ON f.`Floor_ID` = arc.`Floor_ID`
    INNER JOIN edificios as e ON e.`Edificio_ID` = f.`Edificio_ID`
    INNER JOIN proyectos as p ON p.`Pro_ID` = e.`Pro_ID`
WHERE a.`Fecha` = '2023-03-02'
ORDER BY a.`Fecha` DESC;

SELECT a.`Fecha`
FROM actividades as a
    INNER JOIN registro_diario as rd ON a.`Actividad_ID` = rd.`Actividad_ID`
WHERE a.`Fecha` = '2023-03-02';

SELECT
    rd.`Empleado_ID`,
    pe.`Nick_Name`,
    rd.`Fecha`,
    rda.`Horas_Contract` as horas_actividad,
    rda.`Task_ID`,
    t.`Tas_IDT` as code_cost,
    t.`Nombre`,
    arc.`Nombre`,
    f.`Nombre`,
    e.`Nombre`,
    p.`Nombre`
FROM registro_diario as rd
    INNER JOIN registro_diario_actividad as rda ON rd.`Reg_ID` = rda.`Reg_ID`
    LEFT JOIN task as t ON t.`Task_ID` = rda.`Task_ID`
    LEFT JOIN area_control as arc ON arc.`Area_ID` = t.`Area_ID`
    LEFT JOIN floor as f ON f.`Floor_ID` = arc.`Floor_ID`
    LEFT JOIN edificios as e ON e.`Edificio_ID` = f.`Edificio_ID`
    LEFT JOIN proyectos as p ON p.`Pro_ID` = e.`Pro_ID`
    LEFT JOIN personal as pe ON rd.`Empleado_ID` = pe.`Empleado_ID`
WHERE rd.`Fecha` = '2023-03-15'
ORDER BY rd.Empleado_ID ASC;

SELECT
    rd.`Empleado_ID`,
    rd.`Fecha`,
    rd.`Pro_ID`,
    rda.`Task_ID`
FROM registro_diario as rd
    INNER JOIN registro_diario_actividad as rda ON rd.`Reg_ID` = rda.`Reg_ID`
WHERE rd.`Fecha` = '2023-03-15'
ORDER BY rd.Empleado_ID ASC;

;

SELECT* FROM temp_payroll_job_data;

select * from `temp_payroll_job` where '2023-03-01' >= DATE(temp_payroll_job.fecha_inicio) and DATE(temp_payroll_job.fecha_fin) >= '2023-03-01';
select * from `temp_payroll_job` where `2023-03-01` >= DATE(temp_payroll_job.fecha_inicio) and DATE(temp_payroll_job.fecha_fin) >= 2023-03-01)