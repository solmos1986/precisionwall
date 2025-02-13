$(document).on("click", ".asignar_deliver", function () {
    $('#materiales tbody').html("");
    $("#formModalAsignarDeliver").removeAttr("tabindex");
    $('#formModalAsignarDeliver #form_create_asignar').trigger('reset');
    $('#formModalAsignarDeliver').modal('show');
    $('#asignar_from_vendedor').html('');
    $('#asignar_orden_delivery').html('');
    $('#asignar_to_vendor').html('');
    $('#asignar_materiales tbody').html('');
    $('#asignar_delivery_status').html('');
    $('#asignar_delivery_sub_employee').val(null).trigger('change');

    $.ajax({
        type: "GET",
        url: `${base_url}/sub-order/trasferencia/${$(this).data('pedido_id')}`,
        dataType: "json",
        success: function (data) {
            console.log(data)
            $('#tipo_asignar_envio_id').val(data.delivery.id);
            $('#update_asignar').data('pedido_id',data.sub_orden.Ped_ID);
            $('#asignar_orden_proyecto_id').val(data.sub_orden.proyecto_id);
            $('#asignar_orden_id').val(data.sub_orden.tipo_orden_id);
            $('#asignar_pedido_id').val(data.sub_orden.Ped_ID);
            $('#asignar_orden_proyecto_id').val(data.sub_orden.proyecto_id);
            $('#asignar_pco_corr').val(data.sub_orden.PO_Corr);

            $('#asignar_num_orden_vendor').val(data.sub_orden.num);
            $('#asignar_name_orden_vendor').val(data.sub_orden.nombre_trabajo);
            $('#asignar_proveedor_status').val(data.sub_orden.status_id);
            $('#asignar_date_vendor').val(moment(data.sub_orden.Fecha).format('dddd, MMMM D YYYY, HH:mm:ss'));
            var select_from = ``;
            data.from.forEach(almacen => {
                select_from += `<option data-dirrecion="${almacen.Codigo}, ${almacen.nombre_proyecto}, ${almacen.address}"  value="${almacen.id}">${almacen.nombre}</option>`;
            });
            $('#asignar_to_vendor').append(select_from);
            $('#asignar_to_vendor').val(data.sub_orden.To_ID);
            //create_form_delivery();
            var select_to = ``;
            data.to.forEach(almacen => {
                select_to += `<option value="${almacen.id}">${almacen.nombre}</option>`;
            });
            $('#asignar_from_vendedor').append(select_to);
            $('#asignar_from_vendedor').val(data.sub_orden.Ven_ID);
            $('#asignar_pco_vendor').val(data.sub_orden.PO);
            $('#asignar_fecha_entrega_vendor').val(data.ultimo_movimiento.fecha);
            $('#asignar_fecha_segimiento_vendor').val(data.ultimo_movimiento.fecha_espera);
            $('#asignar_nota_vendor').val(data.sub_orden.Note);
            /* materiales */
            var trHTML = ``;
            $.each(data.materiales, function (i, material) {
                trHTML +=
                    `<tr> 
                        <td>${material.Denominacion}
                            <input type="text" name="asignar_materiales_nota[]" value="${material.nota_material}" hidden>
                        </td>      
                        <td>${material.Unidad_Medida}</td>
                        <td>${material.cant_registrada}</td>
                        <td>${material.cant_ordenada}</td>
                        <td width="90">
                            <input type="text" name="asignar_materiales_pedido[]" value="${material.Ped_Mat_ID}" hidden>
                            <input type="text" name="asignar_materiales_id[]" value="${material.material_id}" hidden>
                            <input type="text" name="asignar_tipo_orden_materiales[]" value="${material.id}" hidden>
                            <input name="asignar_cantidad_ordenada[]" value="${material.Cantidad}" class="form-control form-control-sm" type="number" max="" min="" readonly>
                        <td>${material.total_warehouse}</td>
                        <td>${material.total_proyecto}</td>
                        <td>${material.total_proveedor}</td>
                        <td>${material.total_ordenado}</td>   
                        <td>${material.total_usado}</td>
                    </tr>`;
            });
            data_status = data.status;
            /* verificar delivery */
            transferecia_get_empleoyes(1);
            var select_status = ``;
            data_status.forEach(estado => {
                select_status += `<option value="${estado.id}">${estado.nombre}</option>`;
            });
            $('#asignar_delivery_status').append(select_status);
            $('#asignar_delivery_status').val(data.delivery.estatus_id);
            console.log(data.delivery.nombre_delivery, data.delivery.sub_empleoye_id)
            if (data.delivery.sub_empleoye_id!=0) {
                var option = new Option(data.delivery.nombre_delivery, data.delivery.sub_empleoye_id, true, true);
                $('#asignar_delivery_sub_employee').append(option).trigger('change');
            }
            $('#asignar_delivery_nota').val(data.delivery.nota);

            $('#asignar_materiales tbody').append(trHTML);
            $('#formModalAsignarDeliver').modal('show');
        },
    });
});
function transferecia_get_empleoyes(id) {
    $("#asignar_delivery_sub_employee").select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/sub-order/get_deliverys/${id}/orden`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    });
}

/*update asignar */
$(document).on("click", "#update_asignar", function () {
    $.ajax({
        type: "PUT",
        url: `${base_url}/sub-order/trasferencia/${$(this).data('pedido_id')}`,
        dataType: "json",
        data: $('#form_create_asignar').serialize(),
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
                $('#formModalAsignarDeliver').modal("hide");
                sub_order.draw();
            }
        },
    });
});
function select_status() {
    $('#status').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
    });
}

$('#from_date, #to_date, #status').change(function() {
    sub_order.ajax.url(
        `${base_url}/order-delivery/datatable-deliver?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&status=${$('#status').val()}`
    ).load();
});

$('#buscar').click(function() {
    sub_order.ajax.url(
        `${base_url}/order-delivery/datatable-deliver?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&status=${$('#status').val()}`
    ).load();
});

//inizialize
select_status();