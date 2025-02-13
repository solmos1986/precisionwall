$(document).on("click", "#crear_task", function () {
    limpiar_campos_estandar();
    $('#create_metodo').prop("disabled", true);
    $('#save_standar').removeClass('update_standar');
    $('#save_standar').addClass('save_standar');
    $('#modalEstandar').modal('show');
    $('#estandar_superficie_id').val($(this).data('superficie_id'));
    $('#title_modal_estandar').text(`Create New Task`);
});

$(document).on("click", ".save_standar", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/standar-create`,
        data: $('#form_standar').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'errors') {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalEstandar').modal('hide');
                actualizar_detail(response.data,response.data[0].estimado_superficie_id)
            }
        }
    });
});
$(document).on("click", ".edit_standar", function () {
    $('#save_standar').removeClass('save_standar');
    $('#save_standar').addClass('update_standar');
    $('#create_metodo').prop('disabled', false);
   
    const estandar_id = $(this).data('estandar_id');
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/standar-edit/${estandar_id}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            limpiar_campos_estandar();
            $('#title_modal_estandar').text(`Edit Task`);
            $('#nombre_tarea').val(response.data.nombre);
            $('#sov_id').val(response.data.sov_id);
            $('#cost_code').val(response.data.codigo);
            $('#descripcion').val(response.data.descripcion);
            $('#nombre_sov_id').val(response.data.Nom_Sov);
            $('#estandar_superficie_id').val(response.data.id);
            $('#modalEstandar').modal('show');
        }
    });
});
$(document).on("click", ".update_standar", function () {
    $.ajax({
        type: 'PUT',
        url: `${base_url}/project-files/standar-update/${$('#estandar_superficie_id').val()}`,
        data: $('#form_standar').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'errors') {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalEstandar').modal('hide');
                actualizar_detail(response.data,response.data[0].estimado_superficie_id)
            }
        }
    });
});
$(document).on("click", ".delete_standar", function () {
    const estandar_id = $(this).data('estandar_id');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'DELETE',
                url: `${base_url}/project-files/standar-delete/${estandar_id}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                    actualizar_detail(response.data,response.data[0].estimado_superficie_id);
                }
            });
        }
    })
});


function limpiar_campos_estandar() {
    $('#nombre_tarea').val('');
    $('#estandar_superficie_id').val('');
    $('#sov_id').val('');
    $('#cost_code').val('');
    $('#descripcion').val('');
    $('#nombre_sov_id').val('');
}
