$(document).on("click", ".view_date_proyecto", function () {
    $.ajax({
        type: "GET",
        url: `${base_url}/info-project/get-date-project/${$(this).data('proyecto_id')}`,
        dataType: "json",
        success: function (response) {
            $('#historial_fecha').html('');
            var tarjeta = ``;
            response.forEach(fecha_proyecto => {
                tarjeta += `
                    <li class="ms-list-item" style="padding: 0.5rem;">
                        <a href="#" class="media clearfix">
                            <div class="media-body">
                                <!--div class="d-flex justify-content-between">
                                    <h4 class="ms-feed-user mb-0">${calculo_fechas(fecha_proyecto.Fecha_Fin,fecha_proyecto.Fecha_Inicio)}</h4>
                                </div-->
                                <span class="my-2 d-block"> <i class="material-icons">date_range</i> Modified the ${moment(fecha_proyecto.fecha_proyecto_movimiento).format('MMMM dddd D, YYYY h:mm:ss')}</span>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="d-block">From ${fecha_proyecto.Fecha_Inicio} to ${fecha_proyecto.Fecha_Fin} </p>
                                        <p><strong>Note: </strong>${fecha_proyecto.nota}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            });
            $('#historial_fecha').append(tarjeta);
            $('#modalViewFechaProyecto').modal('show');
        }
    });
});
function calculo_fechas(fecha_fin, fecha_inicio) {
    var fin = moment(fecha_fin);
    var inicio = moment(fecha_inicio);
    var dias = fin.diff(inicio, "days");
    if (Math.sign(dias)==1) {
        return `${dias} days are added`;
    } else {
        const negativo=(dias*2)+dias
        return `was reduced dias ${dias} days`;
    }
}