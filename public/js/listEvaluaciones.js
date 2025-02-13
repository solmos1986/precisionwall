var idUsers = [];
$('#crear_evaluacion').click(function () {
    //$('#new_personal').multiselect('refresh');
    $('#formModalNewEvaluacion .modal-title').text('Create evaluacion')
    $('#formModalNewEvaluacion #action').val('Add')
    $('#formModalNewEvaluacion #form_result').html('')
    $('#formModalNewEvaluacion #sample_form').trigger('reset')
    $('#formModalNewEvaluacion').modal('show');
    $("#formModalNewEvaluacion").removeAttr("tabindex");
});
//save evaluacion
$(document).on('click', '.save_evaluacion', function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/store-evaluations`,
        data: $('#new_evaluacion_form').serialize(),
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
                $('#formModalNewEvaluacion #new_evaluacion_form').trigger('reset')
                $('#select_foreman').val('').trigger('change');
                $('#select_form').val('').trigger('change');
                $('#select_personal').multiselect('deselect', idUsers);
                //$('#new_select_personal').multiselect('refresh');
                console.log(idUsers)
                $('#formModalNewEvaluacion').modal('hide')
            }
        },
    })
});
$(document).ready(function () {
        select2("select_foreman","get-foreman-evaluations");
        select2("select_form","get-form-evaluations")
        select2("edit_foreman","get-foreman-evaluations")
        select2("edit_formulario","get-form-evaluations")
    //multiselect
    $('#select_personal').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
    });
    $('#edit_select_personal').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
    });
    //ajax_personal();
});
//select2 
function select2(selector,url) {
    $(`#${selector}`)
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/${url}`,
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
function ajax_personal() {
    $.ajax({
        url: `${base_url}/get-personal-evaluacion`,
        dataType: "json",
        async: false,
        success: function (response) {
            //elimina todo elvalue de select
            $("#edit_select_personal").empty();
            //recorre la respuesta
            $.each(response, function (i, item) {
                //console.log(i, item)
                if ($("#edit_select_personal option[value='" + item.Empleado_ID + "']").length == 0) {
                    $('#edit_select_personal').append('<option value="' + item.Empleado_ID + '">' + item
                        .name_personal + '</option>');
                }
            });
            //reinicia el select
            $('#edit_select_personal').multiselect('rebuild');
        },
    });
}
// show delete
$(document).on('click', '.delete', function () {
    var id = $(this).data('id')
    $('#deleteModal #delete_button').data('id', id)
    $('#deleteModal').modal('show')
})
//delete evalucaion
$(document).on('click', '#delete_button', function () {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/delete-evaluations/${$(this).data('id')}`,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                alert(data.success)
                table.draw();
                $('#deleteModal').modal('hide')
            }
        },
    })
})
//edit
$(document).on('click', '.edit', function () {
    $('#edit_evaluacion_form').trigger('reset')
    $('#edit_select_personal').multiselect('deselect', idUsers);
    var id = $(this).attr('data-id');
    

    $.ajax({
        url: `${base_url}/edit-evaluations/${id}`,
        dataType: "json",
        success: function (data) {
            //console.log(data);
            $('#edit_note').val(data.note);
            $('#edit_evaluacion_id').val(data.evaluacion_id);
            
            var option = new Option(data.name_personal, data.foreman_id, true, true);
            $('#edit_foreman').append(option).trigger('change');
            var option1 = new Option(data.titulo, data.formulario_id, true, true);
            $('#edit_formulario').append(option1).trigger('change');
            if (idUsers.length===0) {
                ajax_personal();
            }
            idUsers = new Array();
            data.personal.forEach(element => {
                idUsers.push(element.Empleado_ID)
            });

            $('#edit_select_personal').multiselect('select', idUsers);
            $('#edit_fecha_asignacion').val(data.fecha_asignacion);
            $('#formModalEditEvaluacion .modal-title').text('Edit evaluations');
            $('#formModalEditEvaluacion').modal('show');
            $("#formModalEditEvaluacion").removeAttr("tabindex");
        }
    })
});
//save edit
$(document).on('click', '.save_edit_evaluacion', function() {
    var cod = $('#cod_evento').val()
    $.ajax({
        type: 'PUT',
        url: `${base_url}/update-evaluations/${$('#edit_evaluacion_id').val()}`,
        data: $('#edit_evaluacion_form').serialize(),
        dataType: 'json',
        success: function(data) {
            if (data.errors) {
                $alert = 'complete the following fields to continue:\n'
                data.errors.forEach(function(error) {
                    $alert += `* ${error}\n`
                })
                alert($alert)
            }
            if (data.success) {
                alert(data.success);
                table.draw();
                $('#formModalEditEvaluacion #edit_evaluacion_form').trigger('reset')
                $('#formModalEditEvaluacion').modal('hide')
            }
        },
    })
});