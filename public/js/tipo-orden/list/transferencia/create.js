$(document).on("click", ".create_transferencia", function () {
    $('#transferencia_materiales tbody').html("");
    $("#formModalCreateTransferencia").removeAttr("tabindex");
    $('#formModalCreateTransferencia #form_create_transferencia').trigger('reset');
    var id_materiales = [];
    $('.id_materiales:checked').each(function () {
        id_materiales.push($(this).val());
    });
    $('#new_to_vendor').html('');
    $('#transferencia_from_vendedor').html('');
    $('#transferencia_to_vendor').html('');
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/trasferencia/create/${$(this).data('orden_id')}`,
        dataType: "json",
        data: {
            id_materiales
        },
        success: function (data) {
            if (data.status == 'errors') {
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: data.message,
                });
            } else {
                $('#formModalCreateTransferencia').modal('show');
                var select_to = `<option  value=""> </option>`;
                data.to.forEach(almacen => {
                    select_to += `<option data-dirrecion="${almacen.Codigo}, ${almacen.nombre_proyecto}, ${almacen.address}" value="${almacen.id}">${almacen.nombre}</option>`;
                });
                $('#transferencia_to_vendor').append(select_to);
                //data modal orden
                $('#transferencia_num_orden_vendor').val(data.orden.num);
                $('#transferencia_name_orden_vendor').val(data.orden.nombre_trabajo);
                $('#transferencia_pco_vendor').val(data.orden.pco);
                $('#transferencia_pco_corr').val(data.orden.po_corr);
                $('#transferencia_orden_proyecto_id').val(data.orden.proyecto_id);
                $('#transferencia_orden_id').val(data.orden.id);
                $('#transferencia_fecha_entrega_vendor').val(moment().format('MM/DD/YYYY HH:mm:ss'));
                $('#transferencia_fecha_segimiento_vendor').val(moment().add(1, 'days').format('MM/DD/YYYY HH:mm:ss'));
                /*controller de select  from*/
                var select_from = `<option  value=""> </option>`;
                data.from.forEach(almacen => {
                    select_from += `<option  value="${almacen.id}">${almacen.nombre}</option>`;
                });
                $('#transferencia_from_vendedor').append(select_from);
                $('#transferencia_date_vendor').val(moment().format('dddd, MMMM D YYYY, HH:mm:ss'));
                var trHTML = ``;
                $.each(data.materiales, function (i, material) {
                    trHTML +=
                        `<tr> 
                            <td>${material.Denominacion}
                                <input type="text" name="transferencia_materiales_nota[]" value="${material.nota_material}" hidden>
                            </td>
                            <td>${material.Unidad_Medida}</td>
                            <td>${material.cant_registrada}</td>
                            <td>${material.cant_ordenada}</td>
                            <td width="90">
                                <input type="text" name="transferencia_materiales_id[]" value="${material.material_id}" hidden>
                                <input type="text" name="transferencia_tipo_orden_materiales[]" value="${material.id}" hidden>
                                <input name="transferencia_cantidad_ordenada[]" value="${material.cant_registrada}" class="form-control form-control-sm" type="number"}>
                            <td>${material.total_warehouse}</td>
                            <td>${material.total_proyecto}</td>
                            <td>${material.total_proveedor}</td>
                            <td>${material.total_ordenado}</td>   
                            <td>${material.total_usado}</td>
                        </tr>`;
                });
                $('#transferencia_materiales tbody').append(trHTML);
                //delivery
                data_status = data.status;
                var select_status = ``;
                data.status.forEach(estado => {
                    select_status += `<option value="${estado.id}">${estado.nombre}</option>`;
                });
                $('#transferencia_proveedor_status').val(7);
            }
        }
    });
});

$(document).on("change", "#transferencia_to_vendor", function () {
    $('#transferencia_nota_vendor').val(`Please deliver at ${$("#transferencia_to_vendor option:selected").data('dirrecion')}`);
});

/*store transferencia */
$(document).on("click", "#update_transferencia", function () {
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/trasferencia/store`,
        dataType: "json",
        data: $('#form_create_transferencia').serialize(),
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
                $('#formModalCreateTransferencia').modal("hide");
                sub_order.draw();
                list_materiales_orden.draw();
            }
        }
    })
});