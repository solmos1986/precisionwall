$(document).ready(function () {
    $("#notificationDropdown").trigger("click");
});

$(document).on("click", "#notificationDropdown", function () {
    $.ajax({
        type: 'GET',
        url: `${base_url}/action-week/notificaciones`,
        dataType: 'json',
        success: function (response) {
            $('#total').empty();
            $('#total').html(`${response.data.length} New`);
            $('#datos').empty()
            let notificacionHTML = `<li class="ms-scrollable ms-dropdown-list">`;
            response.data.forEach(function (notificacion) {
                notificacionHTML += `
                    <a class="media p-2" href="#" >
                        <div class="media-body">
                            <span>${notificacion.action_for_week}</span>
                            <p class="fs-10 my-1 text-disabled">
                                <i class="material-icons">access_time</i>
                                ${moment(notificacion.fecha_proyecto_movimiento).format('dddd DD, MMMM YYYY')}
                                <label class="ms-checkbox-wrap ml-1">
                                    <input class="completado_notificacion" type="checkbox" value="" data-id="${notificacion.notificacion_acciones_persona_id}" ${notificacion.notificacion_estado == 2 ? 'checked' : ''}>
                                    <i class="ms-checkbox-check"></i>
                                </label>
                                <span>Mark as completed</span>
                            </p>
                        </div>
                    </a>
                `;
            });
            notificacionHTML += `</li>`;
            $('#datos').append(notificacionHTML)
        },
    })
});


$(document).on("click", ".completado_notificacion", function () {
    const notificacion_acciones_persona_id = $(this).data('id')
    $.ajax({
        type: "POST",
        url: `${base_url}/action-week/marcado/${notificacion_acciones_persona_id}`,
        dataType: "json",
        data: {
            completado: $(this).is(':checked') == true ? 1 : 0
        },
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    });
});