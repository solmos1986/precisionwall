//save event
$('.save_movimiento_button').click(function () { ///${$('#edit_movimientos_eventos').val()}
    var data = new FormData();

    $.each($('#new_input_images')[0].files, function (i, value) {
        data.append('new_docs[]', value); // change this to value
    });

    //var new_movimientos_eventos = $('#new_movimientos_eventos').val();
    var new_Empleado_ID = $('#new_Empleado_ID').val();
    var new_event = $("#new_event option:selected").val();
    var new_fecha_inicio = $('#new_fecha_inicio').val();
    var new_fecha_fin = $('#new_fecha_fin').val();
    var new_note = $('#new_note').val();

    data.append('new_Empleado_ID', new_Empleado_ID);
    data.append('new_event', new_event);
    data.append('new_fecha_inicio', new_fecha_inicio);
    data.append('new_fecha_fin', new_fecha_fin);
    data.append('new_note', new_note);
    $.ajax({
        type: 'POST',
        url: `${base_url}/store-movimento-cardex`,
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
                $('#formNewModalEvent #new_event_form').trigger('reset')
                $('#formNewModalEvent').modal('hide');
                table.draw();
                window.location.href = `${base_url}/cardex/${$('#new_Empleado_ID').val()}`;
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
    });
});