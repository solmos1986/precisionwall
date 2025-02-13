$(document).on("click", ".view_movimiento_material", function () {
    $('#edit_materiales tbody').html("");
    $("#formModalMovimientoMaterial").removeAttr("tabindex");
    $('#formModalMovimientoMaterial #form_movimiento').trigger('reset');
    $('#movimientos_materiales_pedido').html('');
    $('.update_form_movimiento').data('id', $(this).data('id'));
    $.ajax({
        type: "GET",
        url: `${base_url}/order-movimientos-material/data-table/${$(this).data('id')}`,
        dataType: "json",
        success: function (response) {
            console.log(response.data);
            var trHTML = ``;
            response.data.materiales.forEach(movimiento => {
                trHTML += `
                <tr>         
                    <td>${movimiento.fecha}</td>      
                    <td>${movimiento.Denominacion}</td>
                    <td>
                        ${movimiento.Unidad_Medida}
                    </td>
                    <td>
                        <input name="Ped_Mat_ID[]" value="${movimiento.Ped_Mat_ID}" class="form-control form-control-sm" hidden>
                        <input name="nro_movimiento[]" value="${movimiento.nro_movimiento}" class="form-control form-control-sm" hidden>
                        <input name="movimiento_material_recepcion_cantidad[]" value="${movimiento.total}" class="form-control form-control-sm" type="number" max="" min="" ></td>
                        </td>
                    <td>
                        ${movimiento.nombre_status}
                        </td>
                    <td>
                        <input name="movimiento_material_recepcion_nota[]" value="${movimiento.nota == null ? '' : movimiento.nota}" class="form-control form-control-sm"  ></td>
                    <td>${movimiento.nombre_ubicacion}</td>
                    <td>${movimiento.fecha_espera}</td>
                    <td>
                        <i class="far fa-trash-alt ms-text-danger cursor-pointer delete_movimientos_materiales_pedido" data-nro_movimiento="${movimiento.nro_movimiento}" data-pedido_material="${movimiento.Ped_Mat_ID}" title="Delete"></i>
                    </td>
                </tr>
                `;

            });
            $('#movimientos_materiales_pedido').append(trHTML);
            //inputs file
            autoInizializarInputFileTraking(response.data.Ped_ID)
            $('#formModalMovimientoMaterial').modal('show');

        }
    });
});
$(document).on("click", ".update_form_movimiento", function () {
    $.ajax({
        type: "PUT",
        url: `${base_url}/order-movimientos-material/update/${$(this).data('id')}`,
        dataType: "json",
        data: $('#form_movimiento').serialize(),
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

            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                })
                $('#formModalMovimientoMaterial').modal('hide');
                sub_order.draw();
            }
        }
    });
});
$(document).on("click", ".delete_movimientos_materiales_pedido", function () {
    $.ajax({
        type: "DELETE",
        url: `${base_url}/order-movimientos-material/delete/${$(this).data('nro_movimiento')}/pedido/${$(this).data('pedido_material')}`,
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

            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                })
                $('#formModalMovimientoMaterial').modal('hide');
                sub_order.draw();
            }
        }
    });
});