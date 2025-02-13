//inicianlizado
get_select();
var dias;
//select2
function get_select() {
    $('#event').select2({
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
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    }).on('select2:select', function (e) {
        dias = e.params.data.duracion_day;
        $('#fecha_inicio').prop('disabled', false);
        $('#fecha_inicio').val('');
        $('#fecha_fin').val('');
        $('#duracion_evento').val(e.params.data.duracion_day);
        $('#report_alert').val(e.params.data.report_alert);
        $('#tipo_evento').val(e.params.data.tipo_evento);
    });
}
//despues de escribir ajustando fecha
$('#fecha_inicio').on("keyup change", function () {
    //restauracion de fecha estandar
    var fecha = moment($('#fecha_inicio').val(), 'MM/DD/YYYY').add(dias, 'd');
    $('#fecha_fin').val(fecha.format('MM/DD/YYYY'));
})
//multiselect
$(document).ready(function () {
    $('#company').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked) {
            getAllUser()
        }
    });
    $('#cargo').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked, select) {
            getAllUser()
        },
        onSelectAll: function (option, checked, select) {
            getAllUser()
        },
        onDeselectAll: function (option, checked, select) {
            getAllUser()
        }
    });
    $('#personal').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked, select) {
            if (checked) {
                verificarEventoUser(option.val());
            }
        },
        onSelectAll: function (option) {
            console.log($('#personal').val())
        },
    });
    $('#evento').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked, select) {
            getAllUser();
        },
        onSelectAll: function (option) {
            getAllUser();
        },
        onDeselectAll: function (option, checked, select) {
            getAllUser();
        }
    });
})
///
function getAllUser() {
    let cargo = [];
    $('#cargo').children(':selected').each((idx, el) => {
        cargo.push(
            el.value
        );
    });
    let company = [];
    $('#company').children(':selected').each((idx, el) => {
        company.push(
            el.value
        );
    });
    let evento = [];
    $('#evento').children(':selected').each((idx, el) => {
        evento.push(
            el.value
        );
    });
    $.ajax({
        url: `${base_url}/get_user-evento?cargo=${cargo}&company=${company}&evento=${evento}`,
        dataType: "json",
        async: false,
        success: function (response) {
            //elimina todo elvalue de select
            $("#personal").empty();
            //recorre la respuesta
            $.each(response, function (i, item) {
                if ($("#personal option[value='" + item.Empleado_ID + "']").length == 0) {
                    $('#personal').append(`<option value="${item.Empleado_ID}">${item.Nick_Name == null ? '' : item.Nick_Name + ' |'} ${item.nombre_empleado} ${item.Numero == '' ? '' : '   #' + item.Numero}</option>`);
                }
            });
            //reinicia el select
            $('#personal').multiselect('rebuild');

        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    });
}
//save pdf
$('#docs').change(function () {
    var filename = $(this).val().split('\\').pop();
    var idname = $(this).attr('id');
    $('#docs_name').text(filename);
});

function verificarEventoUser(user_id) {
    $('#users tbody').html("");
    $('#user_name').html("");
    let eventos = [];
    $('#evento').children(':selected').each((idx, el) => {
        eventos.push(
            el.value
        );
    });
    $.ajax({
        type: 'GET',
        url: `${base_url}/movimiento-evento/${user_id}?evento=${eventos}`,
        dataType: 'json',
        success: function (response) {
            var trHTML = '';
            $.each(response.eventos, function (i, item) {
                trHTML +=
                    `<tr>
                    <td>${item.nombre}</td>
                    <td>${item.note == null ? '' : item.note}</td>
                    <td>${item.start_date}</td>
                    <td>${item.exp_date}</td>
                    <td>${item.duracion_day}</td>
                </tr>`;
            });
            $('#user_name').text(`${response.user}- ${response.nombre}`);
            $('#users tbody').append(trHTML);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
}