
/* create */
$(document).on("click", ".create_status", function () {
    $("#formModalCreateStatus").removeAttr("tabindex");
    $('#formModalCreateStatus #form_create_status').trigger('reset');
    $('#formModalCreateStatus').modal('show');
});
/* store */
$(document).on("click", ".store_status", function () {
    var id = $(this).data('id')
    $.ajax({
        type: "POST",
        url: `${base_url}/status-orden/store`,
        dataType: "json",
        data:$('#form_create_status').serialize(),
        success: function (data) {
            if (data.status == 'errors') {
                $alert = "";
                data.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
                
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                })
                $('#formModalCreateStatus').modal("hide");
                table.draw();
            }
        },
    });
});
/* edit */
$(document).on("click", ".edit_status", function () {
    var id = $(this).data('id')
    $.ajax({
        type: "GET",
        url: `${base_url}/status-orden/edit/${id}`,
        dataType: "json",
        success: function (data) {
            $('#edit_id').val(data.id);
            $('#edit_code').val(data.codigo);
            $('#edit_name').val(data.nombre);
            $('#edit_color').val(data.color);
            $('#formModalEditStatus').modal('show');
        },
    });
});
/* update */
$(document).on("click", ".update_status", function () {
    $.ajax({
        type: "PUT",
        url: `${base_url}/status-orden/update/${$('#edit_id').val()}`,
        dataType: "json",
        data:$('#form_edit_status').serialize(),
        success: function (data) {
            console.log(data)
            if (data.status == 'errors') {
                $alert = "";
                data.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
                
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                })
                $('#formModalEditStatus').modal("hide");
                table.draw();
            }
        },
    });
});
/* delete */
$(document).on('click', '.delete_status', function () {
	var id = $(this).data('id')
	$('#deleteModal #delete_button').data('id', id)
	$('#deleteModal').modal('show')
})
$(document).on('click', '#delete_button', function () {
	$.ajax({
		type: 'DELETE',
		url: `${base_url}/status-orden/delete/${$(this).data('id')}`,
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
                table.draw();
            }
		},
	})
});