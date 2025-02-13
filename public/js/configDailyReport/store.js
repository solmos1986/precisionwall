$(document).on('click', '#open_modal_option', function () {
    $('#form_option').trigger("reset");
    $('#formModalTable').modal('hide');
    $('#save_option').removeClass('store_option update_option');
    $('#save_option').addClass('store_option');
    setTimeout(function () {
        $('#formModalOption').modal('show');
    }, 500);
});

$("#enviar").click(function (e) {
    $(this).prop("disabled", true);
    e.preventDefault();
    let rastreo = rastrear_resultado();
    let resultado = {
        proyect_id: $('#proyect_id').val(),
        options: rastreo
    }
    store(resultado);
});

function rastrear_resultado() {
    let option = [];
    $('#lista').children().each(function (i, data) {
        $(data).find('.row').next().find('.col-md-12').find('.ms-list-display').children().each(function (j, value) {
            if ($(value).find('label').find('input').prop('checked')) {
                option.push($(value).find('label').find('input').val())
            }
        });
    });
    return option;
}
function store(data) {
    if (data.options.length <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'complete the following fields to continue:',
            html: 'select an option',
        })
        $('#enviar').prop("disabled", false);
    } else {
        $.ajax({
            type: "POST",
            url: `${base_url}/config-daily-report-detail/store`,
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.status == 'success') {
                    window.location.href = `${base_url}/info-project`;
                }
            }
        });
    }
    
}

// opciones
function nuevo_option(valor) {
    return `
    <div class="col-md-12 m-1 pt-2 pb-2" style="background: rgb(243, 243, 243)">
            <div class="form-group row">
                <div class="col-sm-9">
                    <input type="text" class="form-control form-control-sm"
                        id="opcion" placeholder="sub option">
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
                <div class="row m-1">
                    <div class="col-sm-1">
                        <label for="general"
                            class=" col-form-label col-form-label-sm">-</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm"
                            id="opcion" placeholder="detail">
                    </div>
                    <div class="col-md-1">
                        <button type="button" style="width: 30px;height: 30px;"
                            class="ms-btn-icon btn-square btn-sm btn-danger delete_valor">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}
function nuevo_valor(params) {
    return `
    <div class="row m-1">
        <div class="col-sm-1">
            <label for="general" class=" col-form-label col-form-label-sm">-</label>
        </div>
        <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm" id="opcion" placeholder="detail">
        </div>
        <div class="col-md-1">
            <button type="button" style="width: 30px;height: 30px;" class="ms-btn-icon btn-square btn-sm btn-danger delete_valor">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>`;
}

$(document).on('click', '#add_option', function () {
    let resultado = nuevo_option('');
    $('#option_padre').append(resultado);
});

$(document).on('click', '.delete_option', function () {
    $(this).parent().parent().parent().remove();
});

$(document).on('click', '.add_valor', function () {
    let valor = nuevo_valor(5);
    $(this).parent().parent().next().append(valor)
});

$(document).on('click', '.delete_valor', function () {
    $(this).parent().parent().remove();
});
function rastreo_opciones() {
    let option = []
    $('#option_padre').children().each(function (i, data) {
        let valores = [];
        $(data).find('.col-md-12').children().each(function (j, valor) {
            valores.push({
                valor: $(valor).find('.col-sm-9').find('input').val()
            })
        })
        option.push({
            option: $(data).find('.form-group').find('.col-sm-9').find('input').val(),
            valores: valores
        });
    });

    let data = {
        nombre: $('#formModalOption #opcion').val(),
        descripcion: $('#formModalOption #descripcion').val(),
        options: option
    }
    return data;
}
$(document).on('click', '.store_option', function () {
    let resultado = rastreo_opciones();
    $.ajax({
        type: "POST",
        url: `${base_url}/config-daily-report-option/store`,
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