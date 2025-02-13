var list_table_materiales= $('#list-materiales').DataTable().clear();

$(document).on("click", ".view_materiales", function () {
    $('#formModalListaMateriales').modal('show');
    $('#nombre_lista_materiales').text('LIST MATERIALS');
    list_table_materiales.destroy();
    list_table_materiales = $('#list-materiales').DataTable( {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `${base_url}/materials/data-table?proyecto=${$('#list_proyecto').val()}&material=${$('#list_material').val()}`,
        order: [],
        columns: [
            { data: "Denominacion", name: "Denominacion" },
            { data: "Unidad_Medida", name: "Unidad_Medida" },
            { data: "proyecto_nombre", name: "proyecto_nombre" },
            { data: "total_ordenada", name: "total_ordenada" },
            { data: "total", name: "total" },
            { data: "ubicacion_proyecto", name: "ubicacion_proyecto" },
        ],
        columnDefs: [
            {
                width: "100px",
                targets: 3
            },
        ],
    });
});


$('#list_proyecto, #list_material').change(function() {
    list_table_materiales.ajax.url(`${base_url}/materials/data-table?proyecto=${$('#list_proyecto').val()}&material=${$('#list_material').val()}`).load();
    var rows = list_table_materiales.rows().data().toArray();
});
$('#list_refresh').click(function (e) { 
    $("#list_proyecto").val("");
    $("#list_material").val("");
    list_table_materiales.ajax.url(`${base_url}/materials/data-table`).load();
});
