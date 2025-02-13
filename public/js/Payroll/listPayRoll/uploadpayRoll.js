$(document).on('click', '.export_timberLine', function () {
    $('#timberLineId').val($(this).data('id'));
    $('#NombretimberLine').val($(this).data('descripcion'));
    $('#lista_importacion_timberline').modal('hide');
});

$(document).on('click', '.export_list_employee', function () {
    $('#ListEmployeeId').val($(this).data('id'));
    $('#nombreListEmployee').val($(this).data('descripcion'));
    $('#lista_importacion_list_employee').modal('hide');
});

function validador() {
    let validar = {
        estado: true,
        message: ""
    }
    if ($('#ListEmployeeId').val() == "") {
        validar.estado = false;
        validar.message = "Required employee list";
    }
    if ($('#timberLineId').val() == "") {
        validar.estado = false;
        validar.message = "Required timberLine";
    }
    return validar;
}

function disabledButtons(estado) {
    $('#upload_timerLine').prop('disabled', estado);
    $('#open_timerLine_save').prop('disabled', estado);
    $('#upload_list_employee').prop('disabled', estado);
    $('#open_list_employee_save').prop('disabled', estado);
    $('#import_payroll').prop('disabled', estado);
    $('#otros').prop('disabled', estado);
    $('#doc_list_employee').prop('disabled', estado);
    $('#doc_timerLine').prop('disabled', estado);
    $('#buscar').prop('disabled', estado);
    $('#limpiar').prop('disabled', estado);
    $('#compare').prop('disabled', estado);
}

$('#open_save_payroll').click(function () {

    //validar si la fecha es mayor a  to date
    //verificar el sistema que choque con la fecha

    $('#fecha_inicio').val($('#from_date').val());
    $('#fecha_fin').val($('#to_date').val());
    validaPayRoll()

});
function validaPayRoll() {
    disabledButtons(true);
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/compare-info`,
        data: {
            nombre: $('#nombre').val(),
            descripcion: $('#descripcion').val(),
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            listEmployeeId: $('#ListEmployeeId').val(),
            timberlineId: $('#timberLineId').val()
        },
        dataType: 'json',
        success: function (response) {

            if (response.status == 'ok') {
                disabledButtons(false);
                $('#payrollId').val(response.data.temp_payroll_job);
                if (response.data.estado == 'conflicto') {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Some days already exist, do you want to replace them, it will delete previous version!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete the old version'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#save_import_payroll').modal('show');
                        }
                    });
                } else {
                    $('#save_import_payroll').modal('show');
                }
            } else {
                disabledButtons(false);
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
}
$('#save_payroll').click(function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/store-payroll`,
        data: {
            payrollId: $('#payrollId').val(),
            nombre: $('#payroll_nombre').val(),
            descripcion: $('#payroll_descripcion').val(),
            select_payroll: $('#select_payroll').val()
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
                $('#form_payroll').trigger("reset");
                $('#save_import_payroll').modal('hide');
                Renderfechas(response.data.fechas)
                $('#payroll_id_eject').val(response.data.payrollId);
                $('#payroll_id_fecha_eject').val(response.data.fechas[0]);
                $('.payroll_fecha').each(function (i,e) {
                    if (i==0) {
                        $(e).prop('disabled',true)
                    }
                });
                $('.payroll_fecha_eject').text(`Day ${moment(response.data.fechas[0]).format('DD/MM/YYYY')}`)
                load_payroll_data(response.data.payrollId, response.data.fechas[0]);
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
function Renderfechas(lista_fecha) {
    $('#lista_fecha').html('');
    buttonHTML = ` `;
    lista_fecha.forEach(fecha => {
        buttonHTML += `
        <button type="button" class="btn btn-primary btn-sm m-1 payroll_fecha" data-fecha='${fecha}'>
            ${moment(fecha).format('MM/DD/YYYY')}                                    
        </button>
    `;
    });
    $('#lista_fecha').append(buttonHTML);
}

$(document).on('click', '.payroll_fecha', function () {
    let fecha = $(this).data('fecha');
    $('.payroll_fecha_eject').text(`Day ${moment(fecha).format('MM/DD/YYYY')}`)
    let id = $('#payroll_id_eject').val();

    $('.payroll_fecha').each(function (i,e) {
        $(e).prop('disabled',false)
    });
    $(this).prop('disabled',true);

    load_payroll_data(id, fecha);
});

$(document).on('click', '.export_payroll_data', function () {
    let id = $(this).data('id');
    $('#payroll_id').val(id)
    load_payroll_data(id);
    $('#lista_importacion_payroll').modal('hide');
});

$(document).on('click', '#reset', function () {
    $('#NombretimberLine').val('');
    $('#nombreListEmployee').val('');
    $('#payroll_id').val('');
    $('#date_work').val('');

    datatable_payroll_data = $('#datatable_payroll_data').DataTable().clear();
    datatable_payroll_data.destroy();
    datatable_payroll_data = $('#datatable_payroll_data').DataTable().clear();

});