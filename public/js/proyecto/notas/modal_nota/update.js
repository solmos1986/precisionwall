
//update evento
$(document).on('click', '.update_nota', function () {

    var data = new FormData();
    //files
    $.each($('#nota_files')[0].files, function (i, value) {
        data.append('nota_files[]', value); // change this to value
    });

    var proyecto_id = $('#proyecto_id').val();
    var fecha_entrega = $('#fecha_entrega').val();
    var estado = $('#estado').val();
    var note = $('#note').val();
    var proyecto_manager_id = $('#proyecto_manager_id').val();
    var asistente_proyecto_manager_id = $('#asistente_proyecto_manager_id').val();

    data.append('proyecto_id', proyecto_id);
    data.append('fecha_entrega', fecha_entrega);
    data.append('fecha_registro', moment().format('YYYY-MM-DD HH:mm:ss'));
    data.append('estado', estado);
    data.append('note', note);
    data.append('pm', proyecto_manager_id);
    data.append('apm', asistente_proyecto_manager_id);

    $.ajax({
        type: 'POST',
        url: `${base_url}/project-notas/update/${$('#nota_id').val()}`,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                $('#ModalNota #form_nota').trigger('reset')
                $('#ModalNota').modal('hide');
                dataTable.draw();
            } else {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
});