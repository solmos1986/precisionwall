$(document).on("click", ".create_sub_orden", function () {
    $('#materiales tbody').html("");
    $("#formModalCreateMovimiento").removeAttr("tabindex");
    $('#formModalCreateSubOrden #form_create_sub_orden').trigger('reset');
    $.ajax({
        type: "GET",
        url: `${base_url}/sub-order/create/${$(this).data('orden_id')}`,
        dataType: "json",
        success: function (data) {
            if (data.status == 'error') {
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1000
                });
                $('#materiales tbody').html("");
                $('#formModalCreateSubOrden').modal('hide');
            } else {
                $('#formModalCreateSubOrden').modal('show');
                $('#orden_id').val(data.orden.id);
                $('#sub_orden_pco').val(data.orden.pco);
                select2(data.orden.proyecto_id)
                var trHTML = ``;
                $.each(data.materiales, function (i, material) {
                    trHTML +=
                        `<tr> 
                        <input type="text" name="tipo_orden_materiales_movimiento_vendedor_id[]" value="${material.id}" hidden>
                            <td>${material.Denominacion}</td>      
                            <td>${material.Unidad_Medida}</td>
                            <td>${material.cant_ordenada}</td>
                            <td>
                                <input type="checkbox" name="orden_materiales_id[]" value="${material.id}">
                            </td>
                        </tr>`;
                });
                $('#materiales tbody').append(trHTML);
            }
        },
    });
});

$(document).on("click", ".save_sub_orden", function () {
    $('#fecha_registro').val(moment().format('MM/DD/YYYY HH:mm:ss'));
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/store/${$('#orden_id').val()}`,
        data: $('#form_create_sub_orden').serialize(),
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
                    $('#formModalCreateSubOrden').modal("hide");
                });
                sub_order.draw();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
        }

    });
});
function select2(orden_id) {
    $(`#lugar_entrega`).select2({
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
