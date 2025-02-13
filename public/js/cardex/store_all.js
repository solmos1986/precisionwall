//insert eventos masivos
$(document).on('click', '.save_button', function () {
    var data = new FormData();

    var personal = $('#personal').val();
    var fecha_inicio = $('#fecha_inicio').val();
    var fecha_fin = $('#fecha_fin').val();
    var event = $("#event option:selected").val();
    var note = $('#note').val();

    $.each($('#docs')[0].files, function (i, value) {
        data.append('docs[]', value); // change this to value
    });

    data.append('personal', personal);
    data.append('fecha_inicio', fecha_inicio);
    data.append('fecha_fin', fecha_fin);
    data.append('event', event);
    data.append('note', note);
    $.ajax({
        type: 'POST',
        url: `${base_url}/new-all-cardex`,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                $('#formModalEvent #event_form').trigger('reset')
                $('#access').trigger('reset')
                $('#formModalEvent').modal('hide')
                $('#personal').multiselect('refresh');
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
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
});