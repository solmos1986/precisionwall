$(document).on("click", ".create_sub_orden", function () {
    $('#materiales tbody').html("");
    $("#formModalCreateSubOrden").removeAttr("tabindex");
    $('#formModalCreateSubOrden #form_create_sub_orden').trigger('reset');
    var id_materiales = [];
    $('.id_materiales:checked').each(function () {
        id_materiales.push($(this).val());
    });
    $('#new_to_vendor').html('');
    $('#new_from_vendedor').html('');
    $('#new_delivery_status').html('');
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/create/${$(this).data('orden_id')}`,
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
                $('#segimiento_materiales tbody').html("");
            } else {
                $('#formModalCreateSubOrden').modal('show');
                //data modal orden
                $('#new_num_orden_vendor').val(data.orden.num);
                $('#new_name_orden_vendor').val(data.orden.nombre_trabajo);
                $('#new_pco_vendor').val(data.orden.pco);
                $('#new_pco_corr').val(data.orden.po_corr);
                $('#new_orden_proyecto_id').val(data.orden.proyecto_id);
                $('#new_orden_id').val(data.orden.id);
                $('#new_fecha_entrega_vendor').val(moment().format('MM/DD/YYYY HH:mm:ss'));
                $('#new_fecha_segimiento_vendor').val(moment().add(1,'days').format('MM/DD/YYYY HH:mm:ss'));
                /*controller de select  to*/
                var select_to = `<option  value=""></option>`;
                data.to.forEach(almacen => {
                    select_to += `<option data-dirrecion="${almacen.Codigo}, ${almacen.nombre_proyecto}, ${almacen.address}" value="${almacen.id}">${almacen.nombre}</option>`;
                });
                $('#new_to_vendor').append(select_to);
                /*controller de select  from*/
                var select_from = `<option  value=""> </option>`;
                data.from.forEach(almacen => {
                    select_from += `<option  value="${almacen.id}">${almacen.nombre}</option>`;
                });
                $('#new_from_vendedor').append(select_from);
                $('#new_date_vendor').val(moment().format('dddd, MMMM D YYYY, HH:mm:ss'));
                var trHTML = ``;
                $.each(data.materiales, function (i, material) {
                    trHTML +=
                        `<tr> 
                            <td>${material.Denominacion}
                                <input type="text" name="new_materiales_nota[]" value="${material.nota_material}" hidden>
                            </td>
                            <td>${material.Unidad_Medida}</td>
                            <td>${material.cant_registrada}</td>
                            <td>${material.cant_ordenada}</td>
                            <td width="90">
                                <input type="text" name="new_materiales_id[]" value="${material.material_id}" hidden>
                                <input type="text" name="new_tipo_orden_materiales[]" value="${material.id}" hidden>
                                <input name="new_cantidad_ordenada[]" value="${material.cant_registrada}" class="form-control form-control-sm" type="number"}>
                            <td>${material.total_warehouse}</td>
                            <td>${material.total_proyecto}</td>
                            <td>${material.total_proveedor}</td>
                            <td>${material.total_ordenado}</td>   
                            <td>${material.total_usado}</td>
                        </tr>`;
                });
                $('#materiales tbody').append(trHTML);
                //delivery
                data_status = data.status;
                var select_status = ``;
                data.status.forEach(estado => {
                    select_status += `<option value="${estado.id}">${estado.nombre}</option>`;
                });
                $('#new_delivery_status').append(select_status);
                $('#new_delivery_status').val(7);
                $('#new_proveedor_status').val(3);
            }
        },
    });
});
$(document).on("click", ".store_sub_orden", function () {
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/store`,
        dataType: "json",
        data: $('#form_create_sub_orden').serialize(),
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
                $('#formModalCreateSubOrden').modal("hide");
                list_materiales_orden.draw();
                sub_order.draw();
                $(".view_request_materiales").trigger("click");
            }
        },
    });
});
$(document).on("change", "#new_proveedor_status", function () {
    console.log('enviar')
});
function create_new_form_delivery() {
    if ($('#new_from_vendedor').val() == 1) {
        var deliveryHTML = `
        <fieldset class="border p-2 l-5">
        <legend class="w-auto">Send with delivery:</legend>
        <div class="row">
            <div class="col-md-6">
                <label for="sub_contractor"
                    class="col-sm-3 col-form-label col-form-label-sm">Sub empleoye:</label>
                <div class="col-sm-9">
                    <select name="new_delivery_sub_employee"  class="new_delivery_sub_employee" id="new_delivery_sub_employee"
                        class="form-control form-control-sm" style="width:100%" required>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label for="sub_contractor"
                    class="col-sm-3 col-form-label col-form-label-sm">Status delivery:</label>
                <div class="col-sm-9">
                    <select name="new_delivery_status" id="new_delivery_status"
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
                        <textarea class="form-control" name="new_delivery_nota"
                            id="new_delivery_nota" cols="3"
                            rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>`;
        $('#new_orden_delivery').append(deliveryHTML);
        //delivery
        var select_status = ``;
        data_status.forEach(estado => {
            select_status += `<option value="${estado.id}">${estado.nombre}</option>`;
        });
        $('#new_delivery_status').append(select_status);
        $('#new_delivery_status').val(7);
        new_get_empleoyes(1);
    } else {
        $('#new_orden_delivery').html('');
    }
}
//select to vendor dirrecion
$(document).on("change", "#new_to_vendor", function () {
    $('#new_nota_vendor').val(`Please deliver at ${$("#new_to_vendor option:selected").data('dirrecion')}`);
});
function new_get_empleoyes(id) {
    $("#new_delivery_sub_employee").select2({
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
new_get_empleoyes(1);
