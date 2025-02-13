$(document).on('click', '.delete_orden', function () {
	var id = $(this).data('id')
	$('#deleteModal #delete_button').data('id', id)
	$('#deleteModal').modal('show')
})
$(document).on('click', '#delete_button', function () {
	$.ajax({
		type: 'DELETE',
		url: `${base_url}/order/delete/${$(this).data('id')}`,
		dataType: 'json',
		success: function (data) {
            if (data.status=='ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#deleteModal').modal('hide');
                table.draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: data.message,
                });
                $('#deleteModal').modal('hide');
            }
		},
	})
})
