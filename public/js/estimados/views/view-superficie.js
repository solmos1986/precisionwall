$(document).on("click", "#crear_superficie", function () {
    //limpiar campos
    $('#view_standares').html('')
    limpiar_campos_superficies();
    $('#save_superficie').removeClass('update_superficie');
    $('#save_superficie').addClass('save_superficie');
    $('#create_standar').prop("disabled", true);
    $('#title_modal_superficie').text(`Create surface`);
    $('#modalSuperficie').modal('show');
});
$(document).on("click", ".save_superficie", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/surface-create`,
        data: $('#superficie').serialize(),
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
                $('#superficie_id').val(response.data);
                $('#create_standar').prop("disabled", false);
                $('#modalSuperficie').modal('hide');
                dt.draw();
            }
        }
    });
});

$(document).on("click", ".edit-superficie", function () {
    //limpiar campos
    $('#save_superficie').removeClass('save_superficie');
    $('#save_superficie').addClass('update_superficie');
    limpiar_campos_superficies();
    $('#title_modal_superficie').text(`Edit surface`);
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/surface-edit/${$(this).data('superficie_id')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            $('#superficie_id').val(response.data.id);
            $('#codigo_surface').val(response.data.codigo);
            $('#nombre_surface').val(response.data.nombre);
            if (response.data.miselaneo=='y') {
                $('#miscellaneous').prop('checked',true);
            }else{
                $('#miscellaneous').prop('checked',false);
            }
            $('#modalSuperficie').modal('show');
        }
    });
});
$(document).on("click", ".update_superficie", function () {
    const superficie_id = $('#superficie_id').val();
    $.ajax({
        type: 'PUT',
        url: `${base_url}/project-files/surface-update/${superficie_id}`,
        dataType: 'json',
        data: $('#superficie').serialize(),
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
                $('#modalSuperficie').modal('hide');
                dt.draw();
            }
        }
    });
});
$(document).on("click", ".delete-superficie", function () {
    const superficie_id = $(this).data('superficie_id')
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
                url: `${base_url}/project-files/surface-delete/${superficie_id}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                    dt.draw();
                }
            });
        }
    });
});

function limpiar_campos_superficies() {
    $('#superficie_id').val('');
    $('#codigo_surface').val('');
    $('#nombre_surface').val('');
}