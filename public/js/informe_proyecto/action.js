$(document).on("click", "#save_action", function () {
    console.log($('#imagen').val())
    $.ajax({
        type: "PUT",
        url: `${base_url}/info-project/update_action/${$('#proyecto_id').val()}`,
        data:{
            report_weekly:$('#report_weekly').val(),
            action_for_week:$('#action_for_week').val(),
            fecha_registro:moment().format('YYYY/MM/DD HH:mm:ss'),
            imagen:$('#imagen').val(),          
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