var datatable_payroll = $('#datatable_list_payroll').DataTable().clear();
$('#import_payroll').click(function () {
    $('#lista_importacion_payroll').modal('show');
    datatable_payroll.destroy();
    datatable_payroll = $('#datatable_list_payroll').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": `${base_url}/payroll/datatable-payroll`,
        "columns": [
            {
                data: 'nombre',
                name: "nombre"
            },
            {
                data: 'descripcion',
                name: "descripcion"
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