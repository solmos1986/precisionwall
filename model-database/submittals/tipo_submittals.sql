-- Active: 1687829054607@@127.0.0.1@3306@sof77com_pwt

--14060

SELECT
    vendedor.Nombre,
    materiales.*
FROM materiales
    inner JOIN proyectos as vendedor on vendedor.`Pro_ID` = materiales.`Ven_ID` and vendedor.`Emp_ID` = 119;

SELECT materiales.* FROM materiales ORDER BY `Mat_ID` desc;

DESCRIBE materiales SELECT * FROM tipo_orden_materiales;

SELECT * FROM proyectos where `Nombre` LIKE '%McCormick %';

SELECT * FROM empresas WHERE `Emp_ID`=119;

SELECT * FROM pedidos_material ;

DROP TABLE IF EXISTS tipo_movimiento_material_pedido_imagen;

create table
    tipo_movimiento_material_pedido_imagen (
        id int(11) auto_increment primary key,
        imagen Text NOT NULL,
        caption Text NOT NULL,
        size Text NOT NULL,
        tipo_movimiento_material_pedido_id INT NOT NULL
    );

SELECT *
FROM pedidos
    inner JOIN pedidos_material on pedidos_material.`Ped_ID` = pedidos.`Ped_ID`
WHERE pedidos.`Ped_ID` = 18904;

SELECT *
FROM materiales
WHERE
    materiales.`Mat_ID` in (13474, 923, 1028, 1380, 13935);

--DEMO

SELECT
    m.*,
    sum(pm.Cantidad) as Pedidos,
    c.Nombre Categoria,
    v.Nombre as Vendedor
FROM materiales m
    LEFT JOIN categoria_material c ON m.Cat_ID = c.Cat_ID
    LEFT JOIN proyectos v ON v.Pro_ID = m.Ven_ID
    LEFT JOIN pedidos_material pm on pm.Mat_ID = m.Mat_ID
    LEFT JOIN pedidos p on p.Ped_ID = pm.Ped_ID
WHERE p.Pro_ID = 1776
group by m.Mat_ID
ORDER BY m.Cat_ID, m.Denominacion