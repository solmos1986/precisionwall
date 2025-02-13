get_select_company("company", "job_name", "/list-empresas");
get_select();
var dias;
var movimient_event;
//
//fileinput_images();
//enviar form
function get_select_company(tipo, text_area, url) {
    $(`#${tipo}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}${url}`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    })
        .on("select2:select", function (e) {
            //$("#job_name").val(e.params.data["text"]).prop("disabled", false);
            //$("#sub_contractor").prop("disabled", false);
        });
}

//select2
function get_select() {
    $('#new_event').select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get-event`,
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
        dias = e.params.data.duracion_day;
        $('#new_fecha_inicio').prop('disabled', false);
        $('#new_fecha_inicio').val('');
        $('#new_fecha_fin').val('');
        $('#new_duracion_evento').val(e.params.data.duracion_day);
        $('#new_report_alert').val(e.params.data.report_alert);
        $('#new_tipo_evento').val(e.params.data.tipo_evento);
    });
    //edit
    $('#edit_event').select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get-event`,
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
        dias = e.params.data.duracion_day;
        $('#edit_fecha_inicio').prop('disabled', false);
        $('#edit_fecha_inicio').val('');
        $('#edit_fecha_fin').val('');
        $('#edit_duracion_evento').val(e.params.data.duracion_day);
        $('#edit_report_alert').val(e.params.data.report_alert);
        $('#edit_tipo_evento').val(e.params.data.tipo_evento);
    });
}

// modificando fecha en new
$('#new_fecha_inicio').on("keyup change", function () {
    var fecha = moment($('#new_fecha_inicio').val(), 'MM/DD/YYYY').add(dias, 'd');
    $('#new_fecha_fin').val(fecha.format('MM/DD/YYYY'));
})
// modificando fecha en edit
$('#edit_fecha_inicio').on("keyup change", function () {
    var fecha = moment($('#edit_fecha_inicio').val(), 'MM/DD/YYYY').add(dias, 'd');
    $('#edit_fecha_fin').val(fecha.format('MM/DD/YYYY'));
})

//new event doc
$('#new_docs').change(function () {
    var filename = $(this).val().split('\\').pop();
    var idname = $(this).attr('id');
    $('#new_docs_name').text(filename);
});

//new edit doc
$('#edit_docs').change(function () {
    var filename = $(this).val().split('\\').pop();
    var idname = $(this).attr('id');
    $('#edit_docs_name').text(filename);
});
