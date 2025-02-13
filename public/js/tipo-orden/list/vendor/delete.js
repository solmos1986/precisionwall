/* delete */
$(document).on('click', '.delete_sub_orden', function () {
	var id = $(this).data('pedido_id')
	$('#deleteModalSubOrden #delete_sub_orden').data('id', id)
	$('#deleteModalSubOrden').modal('show')
})
$(document).on('click', '#delete_sub_orden', function () {
	$.ajax({
		type: 'DELETE',
		url: `${base_url}/sub-order/delete/${$(this).data('id')}`,
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
                $('#deleteModalSubOrden').modal('hide');
                list_materiales_orden.draw();
                sub_order.draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: data.message,
                });
                $('#deleteModalSubOrden').modal('hide');
                list_materiales_orden.draw();
                sub_order.draw();
            }
		},
	})
})