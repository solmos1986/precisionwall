/*validador de  exterior */
var id_materiales = []
var cantidad_escrita = []
$(".edit_movimiento").click(function (e) {
    
    $('#materiales_movimiento tbody').html("");
    $('#movimiento tbody').html("");
    $('#formModalMovimientosProveedor #form_movimiento_proveedor').trigger('reset');

    $("#formModalMovimientosProveedor").removeAttr("tabindex");
    $('#formModalMovimientosProveedor').modal("show");
    id_materiales = [];
    $('.tipo_orden_materiales:checked').each(function () {
        id_materiales.push($(this).val());
    });
    cantidad_escrita=[];
    id_materiales.forEach(value => {
        cantidad_escrita.push($(`.cantidad_asignada${value}`).val())
    });

    $.ajax({
        type: "POST",
        url: `${base_url}/tipo-order-admin/material/show`,
        dataType: "json",
        data: {
            order_id: $('#orden_id').val(),
            id_materiales,
        },
        success: function (response) {
            var trHTML = '';
            $.each(response.materiales, function (i, material) {

                trHTML +=
                    `<tr> 
                        <td>${material.Denominacion}</td>
                        <td>${material.Unidad_Medida}</td>
                        <td>${material.cant_ordenada}</td>
                        <td>${cantidad_escrita[i]}</td>
                    </tr>`;
            });
            var trMovimientos = '';
            $.each(response.movimientos, function (i, movimiento) {
                trMovimientos +=
                    `<tr> 
                        <td>${movimiento.fecha_registro}</td>
                        <td>${movimiento.Denominacion}</td>
                        <td>${movimiento.nota}</td>
                        <td>${movimiento.cantidad}</td>
                        <td>${movimiento.fecha_espera==null ? '' : movimiento.fecha_espera}</td>
                    </tr>`;
            });
            $('#materiales_movimiento tbody').append(trHTML);
            $('#movimiento tbody').append(trMovimientos);
            $('#movimiento_order_num').val(response.orden.num);
            $('#movimiento_nombre_proyecto').val(response.orden.nombre_trabajo);
            $('#movimiento_orden_fecha_entrega').val(response.orden.fecha_entrega);
        },
    });

});
$(document).ready(function () {
    $("#enviar_movimiento").click(function (e) {
        var $form = $("#form_movimiento_proveedor");
        $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data:{
                id_materiales,
                cantidad_escrita,
                material_vendedor:$('#proveedor_vendedor').val(),
                material_status:$('#proveedor_status').val(),
                movimiento_nota:$('#movimiento_nota').val(),
                movimiento_cantidad_entregada:$('#movimiento_cantidad_entregada').val(),
                movimiento_fecha_espera:$('#movimiento_fecha_espera').val(),
                fecha:moment().format('MM/DD/YYYY HH:mm:ss'),
                lugar_entrega:$('#lugar_entrega').val(),
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
                        $('#formModalMovimientosProveedor').modal("hide");
                    });
                }
            },
        });
    });
});
