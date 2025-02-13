/* modal view pdf */
$(document).on("click", "#view_report_daily", function () {
    const id = $(this).data('id');
    $('#form_filtro').trigger('reset')
    $('#filtro_proyectos option').remove();
    $('#filtro_proyectos').val(null).trigger('change');
    $('#filter_from_date').val($('#from_date').val());
    $('#filter_to_date').val($('#to_date').val());
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
    if (proyectos_id.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        $.ajax({
            type: "GET",
            url: `${base_url}/info-project/filter-pdf-daily?projects=${proyectos_id}`,
            dataType: "json",
            success: function (response) {
                $('#filter_report_daily').modal('show');
                $('#filter_report_daily .modal-title').text('Filters');
                
                var values = [];
                response.data.proyectos.forEach(proyecto => {
                    values.push(new Option(proyecto.Nombre, proyecto.Pro_ID, true, true));
                });
                $('#filtro_proyectos').append(values).trigger('change');
            }
        });

        //preview_pdf(proyectos_id)
    }

});

$("#view_daily").on('click', function (evt) {
    $('#filter_report_daily').modal('hide');
    setTimeout(function () {
        var options = {
            url: `${base_url}/info-project/view-pdf-daily?proyectos=${$('#filtro_proyectos').val()}&fecha_inicio=${$('#filter_from_date').val()}&fecha_fin=${$('#filter_to_date').val()}&personal=${$('#filter_tipo').is(':checked')}`,
            title: 'Preview',
            size: eModal.size.lg,
            buttons: [{
                text: 'ok',
                style: 'info',
                close: true
            }],
        };
        eModal.iframe(options);
    }, 500);
});