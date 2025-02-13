$(document).on('click', '#create_position', function () {
    $('#ModalCargo .modal-title').text('Create Position');
    $('#ModalCargo #form_position').trigger('reset');

    $('#save_position').removeClass('store_cargo update_cargo');
    $('#save_position').addClass('store_cargo');

    $('#ModalCargo').modal('show');
});

$(document).on('click', '.store_cargo', function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/cardex-position/store`,
        data: $('#form_position').serialize(),
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
                table_position.draw();
                $('#save_tipo_personal').removeClass('store_tipo_empleado update_tipo_empleado');
                $("#ModalCargo").modal("hide");
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