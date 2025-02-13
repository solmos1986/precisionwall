//delete event
$(document).on('click', '.delete', function() {
    console.log('delete')
    var id = $(this).data('id')
    $('#deleteModal #delete_button').data('id', id)
    $('#deleteModal').modal('show')
});
$('#crear_type_evento').click(function() {
    $('#newModalTypeEvent .modal-title').text('Create new event type')
    $('#newModalTypeEvent #new_form_type_event').trigger('reset')
    $('#newModalTypeEvent').modal('show')
});

//insert tipe evento
$(document).on('click', '.save_button_type_evento', function() {
    $.ajax({
        type: 'POST',
        url: `${base_url}/store-type-evento`,
        data: $('#new_form_type_event').serialize(),
        dataType: 'json',
        success: function(data) {
            //console.log(data)
            if (data.errors) {
                $alert = 'complete the following fields to continue:\n'
                data.errors.forEach(function(error) {
                    $alert += `* ${error}\n`
                })
                alert($alert)
            }
            if (data.success) {
                alert(data.success)
                table.draw();
                $('#newModalTypeEvent #new_form_type_event').trigger('reset')
                $('#newModalTypeEvent').modal('hide')
            }
        },
    })
});
//delete event
$(document).on('click', '#delete_button', function () {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/type-event/${$(this).data('id')}`,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                alert(data.success)
                table.draw();
                $('#deleteModal').modal('hide')
            }else{
                alert(data.error)
                $('#deleteModal').modal('hide') 
            }
        },
    })
});
$(document).on('click', '.edit_tipo_evento', function() {
    $('#editModalTypeEvent').modal('show');
    var id = $(this).data('id')
    $.ajax({
        url: `${base_url}/type-event/${id}`,
        dataType: "json",
        success: function(data) {
            console.log(data)
            $('#edit_nombre').val(data.nombre);
            $('#edit_descripcion').val(data.descripcion);
            $('#edit_tipo_evento_id').val(data.tipo_evento_id);
            $('#editModalTypeEvent .modal-title').text('Modifying event type');
            $('#editModalTypeEvent').modal('show');
        }
    })
});

$('.save_edit_tipo_evento').click(function() {
    $.ajax({
        type: 'PUT',
        url: `${base_url}/type-event/${$('#edit_tipo_evento_id').val()}`,
        data: $('#edit_form_type_event').serialize(),
        dataType: 'json',
        success: function(data) {
            if (data.errors) {
                $alert = 'complete the following fields to continue:\n'
                data.errors.forEach(function(error) {
                    $alert += `* ${error}\n`
                })
                alert($alert)
            }
            if (data.success) {
                alert(data.success)
                $('#editModalTypeEvent #edit_form_type_event').trigger('reset');
                table.draw();
                $('#editModalTypeEvent').modal('hide');
            }
        }
    });

});