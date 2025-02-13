$(document).on("click", ".create_recepcion", function () {
    $('#new_recepcion_materiales tbody').html("");
    $("#formModalCreateRecepecion").removeAttr("tabindex");
    $('#formModalCreateRecepecion #form_create_recepecion').trigger('reset');
    $.ajax({
        type: "GET",
        url: `${base_url}/sub-order/reception/${$(this).data('pedido_id')}`,
        dataType: "json",
        success: function (data) {
            console.log('SE ACTIVO FORMULARIO',data)
            if (data.status == 'errors') {
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: data.message,
                });
                $('#new_recepcion_materiales tbody').html("");
            } else {
                $('#formModalCreateRecepecion').modal('show');
                //data modal orden 
                $('#new_recepcion_to').val(data.sub_orden.To_ID);
                $('#new_segimiento_vendor_status').val(data.sub_orden.status_id);
                $('#new_recepcion_num_orden').val(data.orden.num);
                $('#new_recepcion_name_orden').val(data.orden.nombre_trabajo);
                $('#new_recepcion_pco_vendor').val(data.sub_orden.PO);
                $('#new_recepcion_orden_proyecto_id').val(data.orden.proyecto_id);
                $('#new_recepcion_orden_id').val(data.orden.id);
                $('#new_recepcion_sub_orden').val(data.sub_orden.Ped_ID);
                $('#new_vendor_recepcion').val(data.sub_orden.nombre_vendedor);
                $('#new_recepcion_from_vendor').val(data.sub_orden.Ven_ID);
                $('#new_fecha_recepcion_vendor').val(moment().format('MM/DD/YYYY HH:mm:ss'));
                $('#new_fecha_recepcion_traking').val(moment().add(1, 'days').format('MM/DD/YYYY HH:mm:ss'));
                $("#new_recepcion_proveedor_vendedor option[value='" + data.sub_orden.Ven_ID + "']").attr('selected', 'selected');
                $('#new_recepcion_date').val(moment().format('dddd, MMMM D YYYY, HH:mm:ss'));
                /*TABLE */
                var select_status = ``;
                data.status.forEach(estado => {
                    select_status += `<option value="${estado.id}" ${estado.id == 4 ? 'selected' : ''}>${estado.nombre}</option>`;
                });
                var select_to = ``;
                data.to.forEach(enviar_a => {
                    select_to += `<option value="${enviar_a.id}"  ${data.sub_orden.To_ID == enviar_a.id ? 'selected' : ''} >${enviar_a.nombre}</option>`;
                });
                var trHTML = ``;
                $.each(data.materiales, function (i, material) {
                    trHTML +=
                        `<tr> 
                            <td>${material.Denominacion} / ${material.Aux1 == 'null' ? '' : material.Aux1} </td>      
                            <td>${material.Unidad_Medida}</td>
                            <td>${material.Cantidad}</td>
                            <td width="90">${material.recibido}</td>
                            <td width="90">
                                <input type="text" name="new_materiales_id[]" value="${material.Mat_ID}" hidden>
                                <input type="text" class="new_recepcion_materiales_pedido" name="new_recepcion_materiales_pedido[]" value="${material.Ped_Mat_ID}" hidden>
                                <input name="new_cantidad_recibida[]" value="0" class="form-control form-control-sm new_cantidad_recibida" type="number" max="" min="">
                            <td>
                                <select name="new_status_material_recepcion[]" id="new_status_material_recepcion" class="form-control form-control-sm" style="width:100%" required>
                                ${select_status}
                                </select>
                            </td>
                            <td>
                                <select name="new_recepcion_to_vendor[]" id="new_recepcion_to_vendor" class="form-control form-control-sm" style="width:100%" required>
                                ${select_to}
                                </select>
                            </td> 
                            <td>
                                <textarea class="form-control" name="new_nota_recepcion[]" id="new_nota_recepcion" cols="1"
                                rows="1"></textarea>
                            </td>   
                        </tr>
                        `;

                });
                $('#new_recepcion_materiales tbody').append(trHTML);
                //carga de imagenes
                autoInizializarInputFile(data.sub_orden.Ped_ID)
            }
        },
    });
});
$(document).on("click", ".store_new_recepcion", function () {
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/reception/store`,
        dataType: "json",
        data: $('#form_create_recepecion').serialize(),
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
                $('#formModalCreateRecepecion').modal("hide");
                sub_order.draw();
                list_materiales_orden.draw();
            }
        },
    });
});
//detect key
$(document).on("keyup", ".new_cantidad_recibida", function () {
    const recibida = $(this).parent().prev().text();
    const recibiendo = $(this).val();
    const ordenada = $(this).parent().prev().prev().text();
    var suma = parseInt(recibida) + parseInt(recibiendo);
    if (suma >= ordenada) {
        $(this).parent().next().find('#new_status_material_recepcion').val(4);
    } else {
        $(this).parent().next().find('#new_status_material_recepcion').val(11);
    }
});      