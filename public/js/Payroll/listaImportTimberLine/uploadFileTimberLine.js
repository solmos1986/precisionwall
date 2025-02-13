$('#upload_timerLine').click(function () {
    var data = new FormData();
    var new_docs = $('#doc_timerLine')[0].files[0];
    data.append("doc_timerLine", new_docs);
    disabledButtons(true);
    $('#upload_timerLine').html('<i class="fa fa-upload"></i>Loading...');
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/upload-timberline`,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        async: true,
        success: function (response) {
            disabledButtons(false);
            $('#upload_timerLine').html('<i class="fa fa-upload"></i>Import Timerline');
            if (response.status == 'ok') {
                $('#timerlineId').val(response.data.timberLineId);
                $('#descripcion').val(`timberLine ${moment().format('DD/MM/YYYY HH:mm:ss')}`);
                $('#fechaRegistro').val(moment().format('DD/MM/YYYY HH:mm:ss'));
                $('#save_import_timberline').modal('show');
            } else {
                Swal.fire({
                    width: 950,
                    title: 'An error has occurred!',
                    text: 'Please follow the format, as in the image.',
                    imageUrl: `${base_url}/img/ejemplo_timerline.jpg`,
                    imageWidth: 900,
                    imageHeight: 450,
                    imageAlt: 'Error image',
                })
            }
        }
    });
});

$('#doc_timerLine').change(function () {
    var filename = $(this).val().split('\\').pop();
    $('#label_upload_timerLine').text(filename);
});

function disabledButtons(estado) {
    $('#upload_timerLine').prop('disabled', estado);
    $('#open_timerLine_save').prop('disabled', estado);
    $('#upload_list_employee').prop('disabled', estado);
    $('#open_list_employee_save').prop('disabled', estado);
}

$('#save_timberLine').click(function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/store-timberline/${$('#timerlineId').val()}`,
        data: {
            timerlineId: $('#timerlineId').val(),
            descripcion: $('#descripcion').val(),
            fechaRegistro: moment().format('YYYY-MM-DD HH:mm:ss')
        },
        dataType: 'json',
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#save_import_timberline').modal('hide');
            } else {
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
        }
    });
});