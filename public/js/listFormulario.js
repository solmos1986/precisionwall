//delete event
$(document).on('click', '.delete', function () {
    console.log('delete')
    var id = $(this).data('id')
    $('#deleteModal #delete_button').data('id', id)
    $('#deleteModal').modal('show')
})
$(document).on('click', '#delete_button', function () {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/delete-form/${$(this).data('id')}`,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                alert(data.success)
                table.draw();
                $('#deleteModal').modal('hide')
            }
        },
    })
})