$(document).on("click", "#save_date_proyecto", function () {
    $.ajax({
        type: "PUT",
        url: `${base_url}/info-project/update-date-project/${$('#proyecto_id').val()}`,
        data:{
            fecha_inicio:$('#fecha_inicio').val(),
            fecha_fin:$('#fecha_fin').val(),
            horas_con:$('#horas_con').val(),
            nota:$('#nota').val(),
            fecha_registro:moment().format('YYYY/MM/DD HH:mm:ss')
        },
        dataType: "json",
        success: function (response) {
            if (response.status == 'errors') {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
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