var id_materiales_sub_orden = [];
var id_materiales = [];
var cantidad_escrita=[];
$(".envio-delivery").click(function (e) {
    id_materiales = [];
    $('.tipo_orden_materiales:checked').each(function () {
        id_materiales.push($(this).val());
    });
    cantidad_escrita=[];
    id_materiales.forEach(value => {
        cantidad_escrita.push($(`.cantidad_asignada${value}`).val())
    });
    id_materiales_sub_orden = [];
    $.ajax({
        type: "POST",
        url: `${base_url}/tipo-order-admin/material/delivery`,
        data: {
            id_materiales,
            cantidad_escrita
        },
        dataType: "json",
        success: function (data) {
            //responseData=data.data;
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
                $('#materiales tbody').html("");
                $('#formModalEnvio').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                var trHTML = '';
                $.each(data.materiales, function (i, material) {
                    id_materiales_sub_orden.push(material.id)
                    trHTML +=
                        `<tr> 
                        <input type="text" name="tipo_orden_materiales_movimiento_vendedor_id[]" value="${material.id}" hidden>
                        <input type="text" name="fecha_movimiento" value="${ moment().format('MM/DD/YYYY HH:mm:ss')}" hidden>
                            <td>${material.Denominacion}</td>      
                            <td>${material.cant_ordenada}</td>
                            <td>${cantidad_escrita[i]}</td>
                        </tr>`;
                });
                $('#materiales tbody').append(trHTML);
            }
        },
    });
});

$("#enviar_orden").click(function (e) {
    e.preventDefault();
    var $form=$('#form_envio_delivey')
    $.ajax({
        type: "POST",
        url: $form.attr("action"),
        data:{
            orden_id:$('#orden_id').val(),
            sub_empleoye_id:$('#sub_empleoye_id').val(),
            estatus_id:$('#estatus_id').val(),
            fecha_envio:$('#fecha_envio').val(),
            nota:$('#nota').val(),
            fecha_actividad:moment().format('MM/DD/YYYY HH:mm:ss'),
            materiales:id_materiales,
            cantidad:cantidad_escrita,
            id_materiales_sub_orden:id_materiales_sub_orden
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 'ok') {
                Swal.fire('Saved!', '', 'success').then((result) => {
                    $('#materiales tbody').html("");
                    $('#formModalEnvio').modal('hide');
                    //window.location.href = `${base_url}/tipo-order-list-admin`
                });
            }
            $alert = "";
            data.message.forEach(function (error) {
                $alert += `* ${error}<br>`;
            });
            Swal.fire({
                icon: 'error',
                title: 'complete the following fields to continue:',
                html: $alert,
            })
         
        },
    });
});