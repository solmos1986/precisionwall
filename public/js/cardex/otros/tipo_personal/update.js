$(document).on('click', '.edit_tipe_employee', function () {
    $('#ModalTipoPersonal #form_tipo_personal').trigger('reset');
    const id= $(this).data('id');
    $.ajax({
        type: 'GET',
        url: `${base_url}/cardex-type-employee/edit/${id}`,
        dataType: 'json',
        success: function (response) {
            if (response.status == 'ok') {
                $('#form_tipo_personal #id_tipo_personal').val(response.data.id);
                $('#form_tipo_personal #name_tipo_personal').val(response.data.nombre);
                $('#form_tipo_personal #description_tipo_personal').val(response.data.descripcion);
           
                $('#ModalTipoPersonal .modal-title').text('Edit Type of Employee');

                $('#save_tipo_personal').removeClass('store_tipo_empleado update_tipo_empleado');
                $('#save_tipo_personal').addClass('update_tipo_empleado');
                $('#ModalTipoPersonal').modal('show');
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

$(document).on('click', '.update_tipo_empleado', function () {

    console.log('update' );
    const id= $('#form_tipo_personal #id_tipo_personal').val();
    $.ajax({
        type: 'PUT',
        url: `${base_url}/cardex-type-employee/update/${id}`,
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
                $('#ModalTipoPersonal #form_tipo_personal').trigger('reset');
                $('#save_tipo_personal').removeClass('store_tipo_empleado update_tipo_empleado');
                $('#ModalTipoPersonal').modal('hide');
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