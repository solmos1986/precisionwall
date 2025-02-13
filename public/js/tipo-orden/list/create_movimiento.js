$(document).on("click", ".create_movimiento", function () {
    $("#formModalCreateMovimiento").removeAttr("tabindex");
    $('#formModalCreateMovimiento').modal('show');
    $('#formModalCreateMovimiento #form_create_movimiento').trigger('reset');
    $("#lugar_entrega_movimiento").val(null).trigger('change');
    $.ajax({
        type: "GET",
        url: `${base_url}/order-movimientos/create/${$(this).data('id')}`,
        dataType: "json",
        success: function (data) {
            console.log(data.id)
            $('#sub_orden_id').val(data.id);
            select2_movimiento(data.proyecto_id);
        }
    });
    
});

$(document).on("click", ".save_movimiento", function () {
    $("#fecha_registro_movimiento").val(moment().format('MM/DD/YYYY HH:mm:ss'));
    $.ajax({
        type: "POST",
        url: `${base_url}/order-movimientos/store/${$('#sub_orden_id').val()}`,
        data: $('#form_create_movimiento').serialize(),
        dataType: "json",
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
            }
            if (data.status == 'ok') {
                Swal.fire('Saved!', '', 'success').then((result) => {
                    $('#formModalCreateMovimiento').modal("hide");
                    materiales_movimiento.draw();
                    materiales.draw();
                });
            }
        },
    });
});

function select2_movimiento(orden_id) {
    $(`#lugar_entrega_movimiento`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/lugar_entrega/${orden_id}`,
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    })
        .on("select2:select", function (e) {
        });
}