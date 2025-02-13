$(document).on('click', '#create_tipo_empleado', function () {
    $('#ModalTipoPersonal .modal-title').text('Create type of employee');
    $('#ModalTipoPersonal #form_tipo_personal').trigger('reset');

    $('#save_tipo_personal').removeClass('store_tipo_empleado update_tipo_empleado');
    $('#save_tipo_personal').addClass('store_tipo_empleado');
    
    $('#ModalTipoPersonal').modal('show');
});

$(document).on('click', '.store_tipo_empleado', function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/cardex-type-employee/store`,
        data: $('#form_tipo_personal').serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                table_tipo_personal.draw();
                $('#ModalTipoPersonal').modal('hide');
                $('#save_tipo_personal').removeClass('store_tipo_empleado update_tipo_empleado');
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
        error:function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR);
        },
        fail:function (response) {
            fail()
        }
    });

});