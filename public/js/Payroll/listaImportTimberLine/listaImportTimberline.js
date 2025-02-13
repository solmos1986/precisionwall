var datatable_timberline = $('#datatable_timberline').DataTable().clear();
$('#open_timerLine_save').click(function () {
    $('#lista_importacion_timberline').modal('show');
    datatable_timberline.destroy();
    datatable_timberline = $('#datatable_timberline').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": `${base_url}/payroll/datatable-timberline`,
        "columns": [
            {
                data: 'descripcion',
                name: "descripcion"
            },
            {
                data: 'fechaRegistro',
                name: "fechaRegistro"
            },
            {
                data: 'acciones',
                name: 'acciones',
            }
        ],
        "order": [

        ]
    });
});