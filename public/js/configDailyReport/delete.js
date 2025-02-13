$(document).on("click", ".delete_modal_option", function () {
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
            eliminar_evento(id)
        }
    })
});
function eliminar_evento(id) {
    $.ajax({
        type: "DELETE",
        url: `${base_url}/config-daily-report-option/delete/${id}`,
        dataType: "json",
        success: function (response) {
            if (response.status == 'success') {
                Swal.fire(
                    response.message,
                    'It has been safely removed',
                    'success'
                );
                table.draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error server',
                    html: '',
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR);
        },
        fail: function (response) {
            fail()
        }
    });
    
}