$(document).on("click", ".edit_sub_orden", function () {
    $('#edit_materiales tbody').html("");
    $("#formModalEditSubOrden").removeAttr("tabindex");
    $('#formModalEditSubOrden #form_edit_sub_orden').trigger('reset');
    $('#edit_from_vendedor').html('');
    $('#edit_orden_delivery').html('');
    $('#edit_to_vendor').html('');
    $.ajax({
        type: "GET",
        url: `${base_url}/sub-order/edit/${$(this).data('pedido_id')}`,
        dataType: "json",
        success: function (data) {
            $('#edit_orden_proyecto_id').val(data.sub_orden.proyecto_id);
            $('#edit_orden_id').val(data.sub_orden.tipo_orden_id);
            $('#edit_pedido_id').val(data.sub_orden.Ped_ID);
            $('#edit_orden_proyecto_id').val(data.sub_orden.proyecto_id);
            $('#edit_orden_id').val(data.sub_orden.tipo_orden_id);
            $('#edit_pco_corr').val(data.sub_orden.PO_Corr);

            $('#edit_num_orden_vendor').val(data.sub_orden.num);
            $('#edit_name_orden_vendor').val(data.sub_orden.nombre_trabajo);
            $('#edit_proveedor_status').val(data.sub_orden.status_id);
            $('#edit_date_vendor').val(moment(data.sub_orden.Fecha).format('dddd, MMMM D YYYY, HH:mm:ss'));
            var select_from = ``;
            data.from.forEach(almacen => {
                select_from += `<option data-dirrecion="${almacen.Codigo}, ${almacen.nombre_proyecto}, ${almacen.address}"  value="${almacen.id}">${almacen.nombre}</option>`;
            });
            $('#edit_to_vendor').append(select_from);
            $('#edit_to_vendor').val(data.sub_orden.To_ID);
            //create_form_delivery();
            var select_to = ``;
            data.to.forEach(almacen => {
                select_to += `<option value="${almacen.id}">${almacen.nombre}</option>`;
            });
            $('#edit_from_vendedor').append(select_to);
            $('#edit_from_vendedor').val(data.sub_orden.Ven_ID);
            $('#edit_pco_vendor').val(data.sub_orden.PO);
            $('#edit_fecha_entrega_vendor').val(data.ultimo_movimiento.fecha);
            $('#edit_fecha_segimiento_vendor').val(data.ultimo_movimiento.fecha_espera);
            $('#edit_nota_vendor').val(data.sub_orden.Note);
            /* materiales */
            var trHTML = ``;
            $.each(data.materiales, function (i, material) {
                trHTML +=
                    `<tr> 
                        <td>${material.Denominacion}
                            <input type="text" name="edit_materiales_nota[]" value="${material.nota_material}" hidden>
                        </td>      
                        <td>${material.Unidad_Medida}</td>
                        <td>${material.cant_registrada}</td>
                        <td>${material.cant_ordenada}</td>
                        <td width="90">
                            <input type="text" name="edit_materiales_pedido[]" value="${material.Ped_Mat_ID}" hidden>
                            <input type="text" name="edit_materiales_id[]" value="${material.material_id}" hidden>
                            <input type="text" name="edit_tipo_orden_materiales[]" value="${material.id}" hidden>
                            <input name="edit_cantidad_ordenada[]" value="${material.Cantidad}" class="form-control form-control-sm" type="number" max="" min="" >
                        <td>${material.total_warehouse}</td>
                        <td>${material.total_proyecto}</td>
                        <td>${material.total_proveedor}</td>
                        <td>${material.total_ordenado}</td>   
                        <td>${material.total_usado}</td>
                    </tr>`;
            });
            data_status = data.status;
            /* verificar delivery */
            //verificar_delivery(data.delivery)
            $('#edit_materiales tbody').append(trHTML);
            $('#formModalEditSubOrden').modal('show');
        }
    });
});
function verificar_delivery(delivery) {
    if (delivery.delivery) {
        edit_form_delivery(delivery);
    }
    else {
        $('#edit_orden_delivery').html('');
    }
}
//delivery
var data_status;
/* $(document).on("change", "#edit_from_vendedor", function () {
    if (this.value == 1) {
        create_form_delivery();
    } else {
        $('#edit_orden_delivery').html('');
    }
}); */
function create_form_delivery() {
    if ($('#edit_from_vendedor').val() == 1) {
        var deliveryHTML = `
        <fieldset class="border p-2 l-5">
            <legend class="w-auto">Send with delivery:</legend>
            <div class="row">
                <div class="col-md-6">
                    <label for="sub_contractor"
                        class="col-sm-3 col-form-label col-form-label-sm">Sub empleoye:</label>
                    <div class="col-sm-9">
                        <select name="edit_delivery_sub_employee" id="edit_delivery_sub_employee"
                            class="form-control form-control-sm" style="width:100%" required>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="sub_contractor"
                        class="col-sm-3 col-form-label col-form-label-sm">Status delivery:</label>
                    <div class="col-sm-9">
                        <select name="edit_delivery_status" id="edit_delivery_status"
                            class="form-control form-control-sm" style="width:100%" required>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <div class="form-group row">
                        <label for="date_work"
                            class="col-sm-2 col-form-label col-form-label-sm">Note delivery:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="edit_delivery_nota"
                                id="edit_delivery_nota" cols="3"
                                rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>`;
        $('#edit_orden_delivery').append(deliveryHTML);
        //delivery
        var select_status = ``;
        data_status.forEach(estado => {
            select_status += `<option value="${estado.id}">${estado.nombre}</option>`;
        });
        $('#edit_delivery_status').append(select_status);
        $('#edit_delivery_status').val(7);
        edit_get_empleoyes(1);
    } else {
        $('#edit_orden_delivery').html('');
    }
}
function edit_form_delivery(delivery) {
    if ($('#edit_from_vendedor').val() == 1) {
        var deliveryHTML = `
        <fieldset class="border p-2 l-5">
            <legend class="w-auto">Send with delivery:</legend>
            <div class="row">
                <div class="col-md-6">
                    <label for="sub_contractor"
                        class="col-sm-3 col-form-label col-form-label-sm">Sub empleoye:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm"
                            id="edit_delivery_id" name="edit_delivery_id"
                            placeholder="Date of Work" value="${delivery.id}" hidden>
                        <select name="edit_delivery_sub_employee" id="edit_delivery_sub_employee"
                            class="form-control form-control-sm" style="width:100%" required>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="sub_contractor"
                        class="col-sm-3 col-form-label col-form-label-sm">Status delivery:</label>
                    <div class="col-sm-9">
                        <select name="edit_delivery_status" id="edit_delivery_status"
                            class="form-control form-control-sm" style="width:100%" required>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <div class="form-group row">
                        <label for="date_work"
                            class="col-sm-2 col-form-label col-form-label-sm">Note delivery:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="edit_delivery_nota"
                                id="edit_delivery_nota" cols="3"
                                rows="3">${delivery.nota}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>`;
        $('#edit_orden_delivery').append(deliveryHTML);
        //delivery
        var select_status = ``;
        data_status.forEach(estado => {
            select_status += `<option value="${estado.id}">${estado.nombre}</option>`;
        });
        $('#edit_delivery_status').append(select_status);
        $('#edit_delivery_status').val(delivery.estatus_id);
        edit_get_empleoyes(1);
        var option = new Option(delivery.nombre_delivery,delivery.sub_empleoye_id,  true, true);
        $('#edit_delivery_sub_employee').append(option).trigger('change');
    } else {
        $('#edit_orden_delivery').html('');
    }
}
function edit_get_empleoyes(id) {
    $("#edit_delivery_sub_employee").select2({
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
//select to vendor dirrecion
$(document).on("change", "#edit_to_vendor", function () {
    $('#edit_nota_vendor').val(`Please deliver at ${$("#edit_to_vendor option:selected").data('dirrecion')}`);
});
/*update sub orden */
$(document).on("click", ".update_sub_orden", function () {
    $.ajax({
        type: "PUT",
        url: `${base_url}/sub-order/update/${$('#edit_pedido_id').val()}`,
        dataType: "json",
        data: $('#form_edit_sub_orden').serialize(),
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
                $('#formModalEditSubOrden').modal("hide");
                list_materiales_orden.draw();
                sub_order.draw();
            }
        },
    });
});
