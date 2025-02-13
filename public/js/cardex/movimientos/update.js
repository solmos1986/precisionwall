
//update evento
$('.update_movimiento_button').click(function () {

    var edit_data = new FormData();
    //files
    $.each($('#update_input_images')[0].files, function (i, value) {
        console.log(value)
        edit_data.append('edit_docs[]', value); // change this to value
    });

    var edit_movimientos_eventos = $('#edit_movimientos_eventos').val();
    var edit_Empleado_ID = $('#edit_Empleado_ID').val();
    var edit_event = $("#edit_event option:selected").val();
    var edit_fecha_inicio = $('#edit_fecha_inicio').val();
    var edit_fecha_fin = $('#edit_fecha_fin').val();
    var edit_note = $('#edit_note').val();

    edit_data.append('edit_movimientos_eventos', edit_movimientos_eventos);

    edit_data.append('edit_Empleado_ID', edit_Empleado_ID);
    edit_data.append('edit_event', edit_event);
    edit_data.append('edit_fecha_inicio', edit_fecha_inicio);
    edit_data.append('edit_fecha_fin', edit_fecha_fin);
    edit_data.append('edit_note', edit_note);
    console.log(edit_note, edit_Empleado_ID, edit_event, edit_fecha_inicio, edit_fecha_fin)
    console.log(edit_data);
    $.ajax({
        type: 'POST',
        url: `${base_url}/update-movimento-cardex/${$('#edit_movimientos_eventos').val()}`,
        data: edit_data,
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
                $('#formEditModalEvent #edit_event_form').trigger('reset')
                $('#formEditModalEvent').modal('hide');
                table.draw();
                window.location.href = `${base_url}/cardex/${$('#edit_Empleado_ID').val()}`;
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