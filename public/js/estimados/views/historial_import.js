var datatable_historial = $('#datatable_historial').DataTable().clear();
$(document).on("click", "#historial_import", function () {
    $('#modalHistorialImport').modal('show');
    datatable_historial.destroy();
    datatable_historial = $('#datatable_historial').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": `${base_url}/project-files/datatable-history/${user_id}`,
        "columns": [
            {
                data: 'export',
                name: "export"
            },
            {
                data: 'fecha',
                name: "fecha"
            },
            {
                data: 'descripcion',
                name: "descripcion"
            },
            {
                data: 'Nombre',
                name: "Nombre"
            },
            {
                data: 'usuario',
                name: "usuario"
            },
            {
                data: 'acciones',
                name: 'acciones',
            }
        ],
        "order": [

        ],
        pageLength: 10,
    });
});

$(document).on("click", ".export_estimado", function () {
    const estimado_id = $(this).data('estimado_id');
    limpiar_constante();
    animacion_load();
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/export-history/${estimado_id}`,
        dataType: 'json',
        contentType: false,
        processData: false,
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales)
            var llaves = [];
            response.data.imports.forEach(campo => {
                llaves.push(campo.id);
            });
            $('#export_excel').data('imports', response.data.totales.id)
            $('#export_excel_sov').data('imports', response.data.totales.id)
            //muestra de constante 

            $('#labor_cost').val(response.data.totales.labor_cost);
            $('#index_prod').val(response.data.totales.index_prod);
        }
    });
    $('#modalHistorialImport').modal('hide');
});
$(document).on("click", ".delete_estimado", function () {
    const estimado_id = $(this).data('estimado_id');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'DELETE',
                url: `${base_url}/project-files/delete-history/${estimado_id}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    datatable_historial.draw();
                }
            });
        }
    })
});