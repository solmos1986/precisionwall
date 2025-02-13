$(document).on("click", ".create_seguimiento", function () {
    $('#segimiento_materiales tbody').html("");
    $("#formModalCreateSeguimiento").removeAttr("tabindex");
    $('#formModalCreateSeguimiento #form_create_seguimiento').trigger('reset');
    $('#new_seguimiento_fecha_entrega').val(moment().format('dddd, MMMM D YYYY, HH:mm:ss'));
    var id_materiales = [];
    $('.id_materiales:checked').each(function () {
        id_materiales.push($(this).val());
    });
    $('#new_segimiento_to_vendor').html('');
    console.log(moment($('#new_seguimiento_fecha_entrega').val()).format('MM/DD/YYYY HH:mm:ss'))

    $('#formModalCreateSeguimiento').modal('show');
    /* $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/segimiento/${$(this).data('orden_id')}`,
        data:{
            id_materiales
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 'errors') {
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: data.message,
                })
                $('#segimiento_materiales tbody').html("");
            } else {
                $('#formModalCreateSeguimiento').modal('show');
                $('#new_segimiento_orden_num').val(data.orden.num);
                $('#new_segimiento_nombre').val(data.orden.nombre_trabajo);
                $('#new_segimiento_pco_vendor').val(data.orden.pco);
                $('#new_segimiento_proyecto_id').val(data.orden.proyecto_id);
                $('#new_segimiento_orden_id').val(data.orden.id);
               
                var select=``;
                data.to.forEach(almacen => {
                    select+=`<option value="${almacen.id}">${almacen.nombre}</option>`;
                });
                $('#new_segimiento_to_vendor').append(select);
                $('#new_segimiento_date').val(moment().format('dddd, MMMM D YYYY, HH:mm:ss'));
                var trHTML = ``;
                $.each(data.materiales, function (i, material) {
                    trHTML +=
                        `<tr> 
                           
                            <td>${material.Denominacion}</td>      
                            <td>${material.Unidad_Medida}</td>
                            <td>${material.cant_ordenada}</td>
                            <td width="90">
                                <input type="text" name="new_materiales_id[]" value="${material.material_id}" hidden>
                                <input type="text" name="new_tipo_orden_materiales[]" value="${material.id}" hidden>
                                <input name="new_cantidad_ordenada[]" value="0" class="form-control form-control-sm" type="number" max="" min="" ${material.status_orden==false ? '' : 'readonly'}>
                            <td>${material.status_orden==false ? "<h5><span class='badge badge-danger'>no ordenado</span></h5>" : "<h5><span class='badge badge-success'>ordenado</span></h5>"}</td>      
                            <td>${material.total_warehouse}</td>
                            <td>${material.total_proyecto}</td>
                            <td>${material.total_estimado}</td>
                            <td>${material.total_ordenado}</td>
                            <td>${material.total_recibido}</td>      
                            <td>${material.total_usado}</td>
                        </tr>`;
                });
                $('#segimiento_materiales tbody').append(trHTML);
            }
        },
    }); */
});