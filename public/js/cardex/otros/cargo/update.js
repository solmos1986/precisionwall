$(document).on('click', '.edit_cargo', function () {
    const id= $(this).data('id');
    $('#ModalCargo #form_position').trigger('reset');
    $.ajax({
        type: 'GET',
        url: `${base_url}/cardex-position/edit/${id}`,
        dataType: 'json',
        success: function (response) {
            if (response.status == 'ok') {
                $('#form_position #id_cargo').val(response.data.id);
                $('#form_position #name_cargo').val(response.data.nombre);
                $('#form_position #description_cargo').val(response.data.descripcion);
           
                $('#ModalCargo .modal-title').text('Edit Position');
                $('#save_position').removeClass('store_cargo update_cargo');
                $('#save_position').addClass('update_cargo');
                $('#ModalCargo').modal('show');
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

$(document).on('click', '.update_cargo', function () {
    const id= $('#form_position #id_cargo').val();
    $.ajax({
        type: 'PUT',
        url: `${base_url}/cardex-position/update/${id}`,
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
                $('#ModalCargo #form_position').trigger('reset');
                $('#save_position').removeClass('store_cargo update_cargo');
                $('#ModalCargo').modal('hide');
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