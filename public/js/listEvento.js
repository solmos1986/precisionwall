var idUsers;
var idCompany;
var cargo;
//create event
$('#crear_evento').click(function () {
    $('#company').multiselect('refresh');
    $('#cargo').val([]).multiselect('refresh')//deselect
    $('#persona').val([]).multiselect('refresh')//deselect
    $('#formModalEvent .modal-title').text('Create event');
    $('#formModalEvent #new_event_form').trigger('reset');
    $('#company').multiselect('select', 6);
    $('#new_tipo').val(null).trigger('change');
    $('#formModalEvent').modal('show');
    $("#formModalEvent").removeAttr("tabindex");
});

//create type evento
$('#crear_type_evento').click(function () {
    $('#newModalTypeEvent .modal-title').text('Create new event type')
    $('#newModalTypeEvent #new_form_type_event').trigger('reset')
    $('#newModalTypeEvent').modal('show')
})

//select type event
get_select();

function get_select() {
    $(`#new_tipo`)
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/get-type-evento`,
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
        .on("select2:select", function (e) { });
    $(`#edit_tipo`)
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/get-type-evento`,
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
        .on("select2:select", function (e) { });
}

//edit
$(document).on('click', '.edit', function () {
    //limpiando
    $('#edit_cargo').multiselect('deselect', cargo);
    $('#edit_company').multiselect('deselect', idCompany);
    $('#edit_personal').multiselect('deselect', idUsers);
    $('#edit_tipo').val(null).trigger('change');
    var id = $(this).attr('id');
    idUsers = [];
    idCompany = [];
    cargo = [];

    $('#formModalEditEvent #new_event_form').trigger('reset')
    $.ajax({
        url: `${base_url}/edit-event/${id}`,
        dataType: "json",
        success: function (data) {
            //console.log(data);
            $('#cod_evento').val(data.cod_evento);
            var newOption = new Option(data.nombre_tipo, data.tipo_evento_id, true, true);
            $('#edit_tipo').append(newOption).trigger('change');

            idCompany = new Array();
            data.users.forEach(element => {
                idCompany.push(element.Emp_ID)
            });

            cargo = new Array();
            data.users.forEach(element => {
                cargo.push(element.Cargo)
            });

            idUsers = new Array();
            data.users.forEach(element => {
                idUsers.push(element.Empleado_ID)
            });

            $('#edit_company').multiselect('select', idCompany);
            $('#edit_cargo').multiselect('select', cargo);
            // relleno de user
            edit_getUser('no reestablecer');
            //
            $('#name_evento').val(data.nombre);
            $('#descripcion_evento').val(data.descripcion);
            $('#edit_duracion_day').val(data.duracion_day);
            $('#report_alert').val(data.report_alert);
            if (data.access_pers == 'y') {
                $('.editCheckYes').attr('checked', true);
            } else {
                $('.editCheckNo').attr('checked', true);
            }
            $('#note').val(data.note);
            $('#formModalEditEvent .modal-title').text('Edit event');
            $('#action').val('Edit');
            $('#formModalEditEvent').modal('show');
            $("#formModalEditEvent").removeAttr("tabindex");
        }
    })
});

//insert tipe evento
$(document).on('click', '.save_button_type_evento', function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/store-type-evento`,
        data: $('#new_form_type_event').serialize(),
        dataType: 'json',
        success: function (data) {
            //console.log(data)
            if (data.errors) {
                $alert = 'complete the following fields to continue:\n'
                data.errors.forEach(function (error) {
                    $alert += `* ${error}\n`
                })
                alert($alert)
            }
            if (data.success) {
                alert(data.success)
                $('#newModalTypeEvent #new_form_type_event').trigger('reset')
                $('#newModalTypeEvent').modal('hide')
            }
        },
    })
});

//comportamiento se selectmultiple
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
        onChange: function (option, checked, select) {
            if (checked) {
                new_getUser()
            }
        },

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
            new_getUser()
        },
        onSelectAll: function (option, checked, select) {
            new_getUser()
        },
        onDeselectAll: function (option, checked, select) {
            new_getUser()
        }
    });
    $('#persona').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
    });
    $('#edit_company').multiselect({
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
                edit_getUser('reestablecer');
            }
        },
    });
    $('#edit_cargo').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked, select) {
            edit_getUser('reestablecer');
        },
        onSelectAll: function (option, checked, select) {
            edit_getUser('reestablecer')
        },
        onDeselectAll: function (option, checked, select) {
            edit_getUser('reestablecer')
        }
    });
    $('#edit_personal').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
    });
})

//evento selectmultiple para nuevo
function new_getUser() {
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
    //verificar si hay algo selecionado
    if (cargo.length != 0) {
        $.ajax({
            url: `${base_url}/get_user-evento?cargo=${cargo}&company=${company}`,
            dataType: "json",
            async: false,
            success: function (response) {
                //elimina todo elvalue de select
                $("#persona").empty();
                //recorre la respuesta
                $.each(response, function (i, item) {
                    if ($("#persona option[value='" + item.Empleado_ID + "']").length == 0) {
                        $('#persona').append('<option value="' + item.Empleado_ID + '">' + item
                            .Nick_Name + '-' + item.Nombre + '</option>');
                    }
                });
                //reinicia el select
                $('#persona').multiselect('rebuild');
            },
        });
    } else {
        $("#persona").empty();
        $('#persona').multiselect('rebuild');
    }
}

//evento selectmultiple para edit
function edit_getUser(reestablecer) {
    let cargo = [];
    $('#edit_cargo').children(':selected').each((idx, el) => {
        cargo.push(el.value);
    });
    let company = [];
    $('#edit_company').children(':selected').each((idx, el) => {
        company.push(el.value);
    });
    //verificar si hay algo selecionado

    if (cargo.length != 0) {
        $.ajax({
            url: `${base_url}/get_user-evento?cargo=${cargo}&company=${company}`,
            dataType: "json",
            async: false,
            success: function (response) {
                //elimina todo elvalue de select
                $("#edit_personal").empty();
                //recorre la respuesta
                $.each(response, function (i, item) {
                    if ($("#edit_personal option[value='" + item.Empleado_ID + "']").length == 0) {
                        $('#edit_personal').append('<option value="' + item.Empleado_ID + '">' + item
                            .Nick_Name + '-' + item.Nombre + '</option>');
                    }
                });
                if (reestablecer=='reestablecer') {
                    $('#edit_personal').multiselect('rebuild');
                } else {
                     //reinicia el select
                     $('#edit_personal').multiselect('rebuild');
                     $('#edit_personal').multiselect('select', idUsers);
                }
            },
        });
    } else {
        $("#edit_personal").empty();
        $('#edit_personal').multiselect('rebuild');
    }
};

//evento boton guardar
$(document).on('click', '.new_save_button_event', function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/store-evento`,
        data: $('#new_event_form').serialize(),
        dataType: 'json',
        success: function (data) {
            if (data.errors) {
                $alert = 'complete the following fields to continue:\n'
                data.errors.forEach(function (error) {
                    $alert += `* ${error}\n`
                })
                alert($alert)
            }
            if (data.success) {
                alert(data.success);
                table.draw();
                $('#formModalEvent #new_event_form').trigger('reset')
                $('#access').trigger('reset')
                $('#formModalEvent').modal('hide')
            }
        },
    })
});

//evento boton edit 
$(document).on('click', '.edit_save_button_event', function () {
    var cod = $('#cod_evento').val()
    $.ajax({
        type: 'PUT',
        url: `${base_url}/update-event/${$('#cod_evento').val()}`,
        data: $('#edit_event_form').serialize(),
        dataType: 'json',
        success: function (data) {
            if (data.errors) {
                $alert = 'complete the following fields to continue:\n'
                data.errors.forEach(function (error) {
                    $alert += `* ${error}\n`
                })
                alert($alert)
            }
            if (data.success) {
                alert(data.success);
                table.draw();
                $('#formModalEditEvent #edit_event_form').trigger('reset')
                $('#access').trigger('reset')
                $('#formModalEditEvent').modal('hide')
            }
        },
    })
});

// show delete
$(document).on('click', '.delete', function () {
    var id = $(this).data('id')
    $('#deleteModal #delete_button').data('id', id)
    $('#deleteModal').modal('show')
})
//delete event
$(document).on('click', '#delete_button', function () {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/delete-event/${$(this).data('id')}`,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                alert(data.success)
                table.draw();
                $('#deleteModal').modal('hide')
            }else{
                alert(data.error)
                $('#deleteModal').modal('hide')
            }
        },
    })
});

//calculo de fechas
$(".new_date").change(function () {
    var now = moment($('#new_fecha_final').val(), 'MM/DD/YYYY') //todays date
    var end = moment($('#new_fecha_inicio').val(), 'MM/DD/YYYY') // another date
    var duration = moment.duration(now.diff(end));
    var days = duration.asDays();
    console.log(days)
    $('#new_duracion_day').val(parseInt(days));
});
$(".edit_date").change(function () {
    var now = moment($('#edit_fecha_final').val(), 'MM/DD/YYYY') //todays date
    var end = moment($('#edit_fecha_inicio').val(), 'MM/DD/YYYY') // another date
    var duration = moment.duration(now.diff(end));
    var days = duration.asDays();
    $('#edit_duracion_day').val(parseInt(days))
});
