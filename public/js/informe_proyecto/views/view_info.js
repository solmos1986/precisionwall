$(document).on("click", ".view_info", function () {
    $.ajax({
        type: "GET",
        url: `${base_url}/info-project/get-project-info/${$(this).data('proyecto_id')}`,
        dataType: "json",
        success: function (response) {
            var info = ``;
            response.info.forEach(fecha_proyecto => {
                $('#historial_info').html('');
                info += `
                <li class="ms-list-item">
                <a href="#" class="media clearfix">
                    <div class="media-body">
                        <span class="my-2 d-block"> <i class="material-icons">date_range</i> Modified the
                        ${moment(fecha_proyecto.fecha_proyecto_movimiento).format('MMMM dddd D, YYYY h:mm:ss')}
                        </span>
                        <div class="row">
                            ${ añadir_campo(response.status,fecha_proyecto.contact_id,'Contact:')}
                            ${ añadir_campo(response.status,fecha_proyecto.submittals_id,'Submittals:')}
                            ${ añadir_campo(response.status,fecha_proyecto.plans_id,'Plans:')}
                            ${ añadir_campo(response.status,fecha_proyecto.vendor_id,'Vendor:')}
                            ${ añadir_campo(response.status,fecha_proyecto.const_schedule_id,'Const. Schedule:')}
                            ${ añadir_campo(response.status,fecha_proyecto.field_folder_id,'Field Folder:')}
                            ${ añadir_campo(response.status,fecha_proyecto.brake_down_id,'Brake down:')}
                            ${ añadir_campo(response.status,fecha_proyecto.badges_id,'Badges:')}
                            ${ añadir_campo(response.status,fecha_proyecto.special_material_id,'Special Material:')}
                        </div>
                    </div>
                </a>
            </li>
            `;
            });
            $('#historial_info').append(info);
            $('#modalViewInfo').modal('show');
        }
    });
});
function añadir_campo(status,data_id,nombre) {
    if (data_id==0) {
        return ``;
    } else {
        return `
        <div class="col-md-6">
            <p class="d-block" style="color:#878793"> <span
                    class="ms-feed-user mb-0">
                    ${nombre}</span>${view_info_status(status,data_id)}</p>
        </div>
        `;
    }
    
}
function view_info_status(status, campo) {
    var nombre = '';
    status.forEach(estado => {
        if (estado.id == campo) {
            nombre = estado.nombre_status;
        }
    });
    return nombre;
}
function calculo_fechas(fecha_fin, fecha_inicio) {
    var fin = moment(fecha_fin);
    var inicio = moment(fecha_inicio);
    var dias = fin.diff(inicio, "days");
    if (Math.sign(dias) == 1) {
        return `${dias} days are added`;
    } else {
        const negativo = (dias * 2) + dias
        return `was reduced dias ${dias} days`;
    }
}