
// opciones
function edit_option(option, valores) {
    return `
    <div class="col-md-12 m-1 pt-2 pb-2" style="background: rgb(243, 243, 243)">
            <div class="form-group row">
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm"
                        id="opcion" placeholder="sub option" value="${option}">
                </div>
                <div class="col-sm-1">
                    <button type="button" style="width: 30px;height: 30px;"
                        class="ms-btn-icon btn-square btn-sm btn-primary add_valor">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="col-sm-1">
                    <button type="button" style="width: 30px;height: 30px;"
                        class="ms-btn-icon btn-square btn-sm btn-danger delete_option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-12 option_hijo">
                ${valores}
            </div>
        </div>
    `;
}
function edit_valor(valor) {
    return `
    <div class="row m-1">
        <div class="col-sm-1">
            <label for="general" class=" col-form-label col-form-label-sm">-</label>
        </div>
        <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm" id="opcion" placeholder="detail" value="${valor}">
        </div>
        <div class="col-md-1">
            <button type="button" style="width: 30px;height: 30px;" class="ms-btn-icon btn-square btn-sm btn-danger delete_valor">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>`;
}
$(document).on('click', '.editar_modal_option', function () {
    $('#save_option').removeClass('store_option update_option');
    $('#save_option').addClass('update_option');

    let id = $(this).data('id');
    $('#save_option').data('id', id);
    $('#form_option').trigger("reset");
    $('#formModalTable').modal('hide');
    setTimeout(function () {
        $.ajax({
            type: "GET",
            url: `${base_url}/config-daily-report-option/edit/${id}`,
            dataType: 'json',
            async: true,
            success: function (response) {
                $('#opcion').val(response.data.nombre);
                $('#descripcion').val(response.data.descripcion);

                let optionHTML = ``;
                response.data.options.forEach(option => {
                    let valoresHTML = ``;
                    option.valores.forEach(valor => {
                        valoresHTML += edit_valor(valor.valor)
                    });
                    optionHTML += edit_option(option.opcion, valoresHTML)
                });
                $('#option_padre').html('');
                $('#option_padre').append(optionHTML);
                $('#formModalOption').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                error_status(jqXHR)
            },
            fail: function () {
                fail()
            }
        });
    }, 500);
});

$(document).on('click', '.update_option', function () {
    let resultado = rastreo_opciones();
    let id = $(this).data('id')
    $.ajax({
        type: "PUT",
        url: `${base_url}/config-daily-report-option/update/${id}`,
        data: resultado,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'success') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1000
                });
                $('#formModalOption').modal('hide');
                setTimeout(function () {
                    $('#formModalTable').modal('show');
                }, 500);
                table.draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    });
});


