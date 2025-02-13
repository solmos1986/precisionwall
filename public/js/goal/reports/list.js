//sort por foramto de fecha
var dataTable = $("#list-proyectos").DataTable({
    createdRow: function (row, data, dataIndex) {
        if (data.color != `null`) {
            $(row).addClass(data.color);
        }
    },
    processing: true,
    serverSide: true,
    scrollY: true,
    scrollX: true,
    scrollCollapse: true,
    ajax: `${base_url}/goal-reports/datatable`,
    language: {
        searchPlaceholder: "Criterion"
    },
    columns: [
        {

            data: "Codigo",
            name: "Codigo",
        },
        {
            data: "Nombre",
            name: "Nombre",
        },
        {
            data: "Nombre_Estatus",
            name: "Nombre_Estatus",
        },
        {
            width: "20%",
            data: "nombre_project_manager",
            name: "nombre_project_manager",
        },
        {
            data: "nombre_foreman",
            name: "nombre_foreman",
        },
        {
            data: 'check',
            name: 'check',
            orderable: false
        },
        {
            width: "10%",
            data: 'acciones',
            name: 'acciones',
            orderable: false
        }
    ],
    pageLength: 100
});

$("#view_pdf_all").on('click', function () {
    if (this.checked) {
        $('.proyectos[type="checkbox"]').each(function () {
            $('.proyectos').prop('checked', true);
        });
    } else {
        $('.proyectos[type="checkbox"]').each(function () {
            $('.proyectos').prop('checked', false);
        });
    }
});
