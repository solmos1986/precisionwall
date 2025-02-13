let dataTable_historial_acciones = $("#table_historial").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: `${base_url}/action-week/historial/0`,
    language: {
        searchPlaceholder: "Criterion"
    },
    order: [[1, 'DESC']],
    columns: [{
        data: "action_for_week",
        name: "action_for_week",
    },
    {
        data: "fecha_proyecto_movimiento",
        name: "fecha_proyecto_movimiento",
        render: function (data, type, row, meta) {
            return `${moment(data).format('MM/DD/YYYY HH:mm:ss')}`;
        }
    },
    {
        data: "notificacion_estado_nombre",
        name: "notificacion_estado_nombre"
    },
    {
        data: 'Nombre',
        name: 'Nombre'
    },
    {
        data: 'notificacion_estado',
        name: 'notificacion_estado',
        orderable: false,
        Searchable: false,
        render: function (data, type, row, meta) {
            return `
            ${data == 1 ? '<span class="badge badge-pill badge-success">Yes</span>' : '<span class="badge badge-pill badge-danger">No</span>'}
          `;
        }
    },
    {
        data: 'fecha_registro',
        name: 'fecha_registro',
        render: function (data, type, row, meta) {
            return `${data == null ? 'no record' : (moment(data).format('MM/DD/YYYY HH:mm:ss'))}`;
        }
    },
    {
        data: 'verificar_estado',
        name: 'verificar_estado',
        orderable: false,
        Searchable: false,
        render: function (data, type, row, meta) {
            return `
            ${data == 1 ? '<span class="badge badge-pill badge-success">Yes</span>' : '<span class="badge badge-pill badge-danger">No</span>'}
          `;
        }
    }
    ],
    pageLength: 10
});

$(document).on("click", ".acciones_historial", function () {
    const proyecto_id = $('#proyecto_id').val();
    dataTable_historial_acciones.ajax.url(`${base_url}/action-week/historial/${proyecto_id}`).draw();
    $('#modalViewAccionesHistory').modal({
        show: true
    });
});