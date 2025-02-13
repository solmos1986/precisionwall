var id_materiales = []

$(document).on('click', '.movimiento', function () {
    $('#materiales_movimiento tbody').html("");
    $('#movimiento tbody').html("");
    $('#formModalMovimientos #form_movimiento_proveedor').trigger('reset');
    id_materiales = [];
    $('#formModalMovimientos').modal("show");
    $.ajax({
        type: "POST",
        url: `${base_url}/tipo-order-admin/material/movimientos/show`,
        dataType: "json",
        data: {
            tipo: 'movimiento',
            order_id: $(this).data('orden_id'),
            proveedor_id: $(this).data('proveedor_id'),
            sub_orden_id: $(this).data('sub_orden_id'),
        },
        success: function (response) {
            var trHTML = '';
            $.each(response.materiales, function (i, material) {
                id_materiales.push(material.id);
                trHTML +=
                    `<tr> 
                        <td>${material.Denominacion}</td>
                        <td>${material.Unidad_Medida}</td>
                        <td>${material.cant_ordenada}</td>
                        <td>${material.cantidad}</td>
                    </tr>`;
            });
            var trMovimientos = '';
            $.each(response.movimientos, function (i, movimiento) {
                trMovimientos +=
                    `<tr> 
                        <td>${movimiento.fecha_registro}</td>
                        <td>${movimiento.nota}</td>
                        <td>${movimiento.fecha_espera == null ? '' : movimiento.fecha_espera}</td>
                    </tr>`;
            });
            $('#materiales_movimiento tbody').append(trHTML);
            $('#movimiento tbody').append(trMovimientos);
            $('#proveedor_vendedor').val(response.movimientos[0].nombre_proveedor);
            $('#movimiento_order_num').val(response.orden.num);
            $('#movimiento_nombre_proyecto').val(response.orden.nombre_trabajo);
            $('#movimiento_orden_fecha_entrega').val(response.orden.fecha_entrega);
            $('#movimiento_lugar_entrega').val(response.sub_orden.lugar_entrega);
            $(`#proveedor_status option[value="${response.sub_orden.estatus_id}"]`).attr("selected", true);
            $('#proveedor_vendedor').val(response.sub_orden.nombre_vendedor);
            $('#tipo_orden_materiales_vendedor_id').val(response.sub_orden.id);

        },
    });

})
$(document).ready(function () {
    $("#enviar_movimiento").click(function (e) {
        var $form = $("#form_movimiento_proveedor");
        $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data: {
                tipo_orden_materiales_vendedor_id: $('#tipo_orden_materiales_vendedor_id').val(),
                material_vendedor: $('#proveedor_vendedor').val(),
                sub_orden_status: $('#proveedor_status').val(),
                movimiento_nota: $('#movimiento_nota').val(),
                movimiento_cantidad_entregada: $('#movimiento_cantidad_entregada').val(),
                movimiento_fecha_espera: $('#movimiento_fecha_espera').val(),
                fecha: moment().format('MM/DD/YYYY HH:mm:ss')
            },
            dataType: "json",
            success: function (data) {
                console.log(data)
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
                        $('#formModalMovimientos').modal("hide");
                    });
                    table.draw()
                }
            },
        });
    });
});

$(document).on('click', '.envios', function () {
    $('#materiales_envios_notas tbody').html("");
    $('#form_envio_delivery_movimiento').trigger('reset');
    $('#formModalEnvio').modal("show");
    $.ajax({
        type: "GET",
        url: `${base_url}/tipo-order-admin/material/view/delivery/${$(this).data('actividades_id')}`,
        dataType: "json",
        success: function (response) {
            console.log(response)
            $('#nombre_proyecto').val(response.actividad.proyecto);
            $('#address').val(response.actividad.address);
            $('#nombre_estatus').val(response.actividad.nombre_estatus);
            $('#fecha_actividad').val(response.actividad.fecha_actividad);
            $('#fecha_entrega').val(response.actividad.fecha_entrega);
            $('#nickname').val(response.actividad.nickname);
            $('#tipo_orden_materiales_actividad_id').val(response.actividad.id);
            var trMovimientos = '';
            $.each(response.movimientos, function (i, movimiento) {
                trMovimientos +=
                    `<tr> 
                        <td>${movimiento.fecha_registro}</td>
                        <td>${movimiento.nota}</td>
                    </tr>`;
            });
            $('#materiales_envios_notas tbody').append(trMovimientos);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
        }
    });
});
$(document).on('click', '#enviar_moviento_delivery', function () {
    var form = $('#form_envio_delivery_movimiento');
    $('#fecha').val(moment().format('MM/DD/YYYY HH:mm:ss'));
    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: form.serialize(),
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status == 'errors') {
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
            if (response.status == 'ok') {
                Swal.fire('Saved!', '', 'success').then((result) => {
                    $('#formModalEnvio').modal("hide");
                });
                table.draw()
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
        }
    });
});