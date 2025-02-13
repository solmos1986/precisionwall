$(document).on("click", ".view-mail", function () {
    $('#summernote').html();
    $("#formModalViewEmailSubOrden").removeAttr("tabindex");
    $.ajax({
        type: "POST",
        url: `${base_url}/sub-order/view-email`,
        dataType: "json",
        data:{
            pedido_id:$(this).data('pedido_id'),
            orden_id:$(this).data('orden_id')
        },
        success: function (data) {
            $('#formModalViewEmailSubOrden').modal('show');
            var sub_orden = ``;
            var materiales = ``;
                data.materiales.forEach(material => {
                    materiales+=`
                   <div id="div_pedido_lista_items" name="div_pedido_lista_items">
                   ${material.Cantidad}${material.Unidad_Medida},&nbsp; ${material.Denominacion}, ${material.Aux1=='null' ? '' : material.Aux1} &nbsp;<br>                   
                    </div>
                   `;
                });
                sub_orden +=
                    `
                   <p><b><span style="font-size:x-large">Purchase Order</span></b></p>
                                    <b>Date:</b>${data.sub_orden.fecha_registro}<br>
                                    <b>GC-Superintendent:</b> ${data.orden.gc_super} <b>Movil:${data.orden.gc_super_celular} </b><br>
                                    <p><b>Job:</b> ${data.orden.Codigo} ${data.orden.Nombre}<br>
                                        <b>Address:</b> ${data.orden.address} <br>
                                        <b>PWT Contacts:</b> ${data.orden.foreman_Nick_Name} <b>Movil:</b>${data.orden.foreman_celular} <br>
                                        <b> 2nd. Contact: </b>  ${data.orden.lead_Nick_Name==null ? '' :data.orden.lead_Nick_Name}<b>Movil: </b>${data.orden.lead_celular==null ? '' :data.orden.lead_celular}
                                        <b>PO:</b> ${data.sub_orden.PO}
                                    </p>
                                  ${materiales}
                                  <br>
                                  ${data.sub_orden.note}.<br>
                                    <b><br>
                                        <b>Thank you<br>
                                        </b></b>
                   `;
            $('#summernote').summernote('code', sub_orden);
        }
    });
});