var datatable_list_employee = $('#datatable_list_employee').DataTable().clear();
$('#open_list_employee_save').click(function () {
    $('#lista_importacion_list_employee').modal('show');
    datatable_list_employee.destroy();
    datatable_list_employee = $('#datatable_list_employee').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": `${base_url}/payroll/datatable-list-employee`,
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

        ],
    });
});