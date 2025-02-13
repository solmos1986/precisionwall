$("#badges").change(function () {
    const color = $("#badges option:selected").data('color')
    $("#badges").attr('class', `form-control form-control-sm text-${color}`);
});
$("#contact").change(function () {
    const color = $("#contact option:selected").data('color')
    $("#contact").attr('class', `form-control form-control-sm text-${color}`);
});
$("#submittals").change(function () {
    const color = $("#submittals option:selected").data('color')
    $("#submittals").attr('class', `form-control form-control-sm text-${color}`);
});
$("#plans").change(function () {
    const color = $("#plans option:selected").data('color')
    $("#plans").attr('class', `form-control form-control-sm text-${color}`);
});
$("#vendor").change(function () {
    const color = $("#vendor option:selected").data('color')
    $("#vendor").attr('class', `form-control form-control-sm text-${color}`);
});
$("#const_schedule").change(function () {
    const color = $("#const_schedule option:selected").data('color')
    $("#const_schedule").attr('class', `form-control form-control-sm text-${color}`);
});
$("#field_folder").change(function () {
    const color = $("#field_folder option:selected").data('color')
    $("#field_folder").attr('class', `form-control form-control-sm text-${color}`);
});
$("#brake_down").change(function () {
    const color = $("#brake_down option:selected").data('color')
    $("#brake_down").attr('class', `form-control form-control-sm text-${color}`);
});
$("#special_material").change(function () {
    const color = $("#special_material option:selected").data('color')
    $("#special_material").attr('class', `form-control form-control-sm text-${color}`);
});

$(document).on("click", "#save_info", function () {
    $.ajax({
        type: "PUT",
        url: `${base_url}/info-project/update-info/${$('#proyecto_id').val()}`,
        data: {
            fecha_registro:moment().format('YYYY/MM/DD HH:mm:ss'),
            contact: $('#contact').val(),
            submittals: $('#submittals').val(),
            plans: $('#plans').val(),
            vendor: $('#vendor').val(),
            const_schedule: $('#const_schedule').val(),
            field_folder: $('#field_folder').val(),
            brake_down: $('#brake_down').val(),
            badges: $('#badges').val(),
            special_material: $('#special_material').val(),
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