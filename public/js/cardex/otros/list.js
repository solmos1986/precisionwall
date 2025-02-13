
var table_position = $('#list_position').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: {
        url: `${base_url}/cardex-position/data-table`,
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    },
    order: [

    ],
    columns: [{
        data: "nombre",
        name: "nombre"
    },
    {
        data: "descripcion",
        name: "descripcion"
    },
    {
        data: 'acciones',
        name: 'acciones',
        orderable: false
    }],
    pageLength: 100,
});
var table_tipo_personal = $('#list_tipo_personal').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: {
        url: `${base_url}/cardex-type-employee/data-table`,
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    },
    order: [

    ],
    columns: [{
        data: "nombre",
        name: "nombre"
    },
    {
        data: "descripcion",
        name: "descripcion"
    },
    {
        data: 'acciones',
        name: 'acciones',
        orderable: false
    }],
    pageLength: 100,
});