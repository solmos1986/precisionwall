var dataTable = $("#list-notas").DataTable({
    processing: true,
    serverSide: true,
    scrollY: true,
    scrollX: true,
    scrollCollapse: true,
    ajax: `${base_url}/project-notas/data-table/${proyecto}?buscar=${$('#buscar_nota').val()}&from_date=${$("#from_date").val()}&to_date=${$("#to_date").val()}`,
    language: {
        searchPlaceholder: "Criterion"
    },
    columns: [{
        data: "fecha_entrega",
        name: "fecha_entrega",
        width: "15%",
    },
    {
        data: "codigo",
        name: "codigo",
        width: "10%",
    },
    {
        data: "codigo_proyecto",
        name: "codigo_proyecto",
        width: "10%",
    },
    {
        data: "nombre_proyecto",
        name: "nombre_proyecto",
        width: "25%",
    },
    {
        data: 'nota',
        name: 'nota',
        width: "35%",
    },
    {
        data: 'images',
        name: 'images',
        width: "5%",
    },
    {
        data: 'acciones',
        name: 'acciones',
        width: "10%",
    }],
    pageLength: 100
});

$('#proyecto_id').select2({
    theme: "bootstrap4",
    ajax: {
        url: `${base_url}/project-notas/select-project`,
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                searchTerm: params.term // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    }
}).on('select2:select', function (e) {
    $("#empresa").val(`${e.params.data['empresa']}`);
    $("#codigo").val(`${e.params.data['codigo']}`);
    $("#proyecto_manager_id").val(`${e.params.data['proyecto_manager_id']}`);
    $("#proyecto_manager").text(`${e.params.data['proyecto_manager']}`);
    $("#asistente_proyecto_manager_id").val(`${e.params.data['asistente_proyecto_manager'] == null ? '' : e.params.data['asistente_proyecto_manager']}`);
    $("#asistente_proyecto_manager").text(`${e.params.data['asistente_proyecto_manager'] == null ? '' : e.params.data['asistente_proyecto_manager']}`);
    
    $('#foreman_id').val(`${e.params.data['foreman_id'] == null ? '' : e.params.data['foreman_id']}`);
    $('#foreman').text(`${e.params.data['foreman'] == null ? '' : e.params.data['foreman']}`);

    $('#lead_id').val(`${e.params.data['lead_id'] == null ? '' : e.params.data['lead_id']}`);
    $('#lead').text(`${e.params.data['lead'] == null ? '' : e.params.data['lead']}`);
   
});

$('#buscar_nota, #from_date, #to_date').change(function() {
    dataTable.ajax.url(
        `${base_url}/project-notas/data-table?buscar=${$('#buscar_nota').val()}&from_date=${$("#from_date").val()}&to_date=${$("#to_date").val()}`
    ).load();
    var rows = dataTable.rows().data().toArray();
});


$("#buscar_nota").on('keyup', function (e) {
    var keycode = e.keyCode || e.which;
      if (keycode == 13) {
        
      }
  });