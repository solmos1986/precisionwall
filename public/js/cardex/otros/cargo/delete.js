$(document).on('click', '.delete_cargo', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure to delete?',
        text: "This process cannot be reversed!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete this!'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminar_cargo(id)
        }
    })
});
function eliminar_cargo(id) {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/cardex-position/delete/${id}`,
        dataType: 'json',
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire(
                    response.message,
                    'It has been safely removed',
                    'success'
                );
                table_position.draw();
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
}