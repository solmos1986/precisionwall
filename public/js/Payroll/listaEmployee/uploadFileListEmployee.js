$('#upload_list_employee').click(function () {
    var data = new FormData();
    var new_docs = $('#doc_list_employee')[0].files[0];
    data.append("doc_list_employee", new_docs);
    limpiar_constante();
    //animacion
    animacion_load();
    disabledButtons(true);
    $('#upload_list_employee').html('<i class="fa fa-upload"></i>Loading...');
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/upload-list-employee`,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        async: true,
        success: function (response) {
            disabledButtons(false);
            $('#upload_list_employee').html('<i class="fa fa-upload"></i>Import Timerline');
            if (response.status == 'ok') {
                $('#list_employeeId').val(response.data.listEmployeeId);

                $('#employee_descripcion').val(`list employee ${moment().format('DD/MM/YYYY HH:mm:ss')}`);
                $('#employee_fechaRegistro').val(moment().format('DD/MM/YYYY HH:mm:ss'));
                $('#save_import_list_employee').modal('show');
            } else {
                Swal.fire({
                    width: 950,
                    title: 'An error has occurred!',
                    text: 'Please follow the format, as in the image.',
                    imageUrl: `${base_url}/img/ejemplo_list_employeee.jpg`,
                    imageWidth: 900,
                    imageHeight: 450,
                    imageAlt: 'Error image',
                })
            }

        }
    });
});

$('#doc_list_employee').change(function () {
    var filename = $(this).val().split('\\').pop();
    $('#label_list_employee').text(filename);
});


$('#save_list_employee').click(function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/store-list-employee/${$('#list_employeeId').val()}`,
        data: {
            listEmployeeId: $('#list_employeeId').val(),
            descripcion: $('#employee_descripcion').val(),
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
                $('#save_import_list_employee').modal('hide');
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