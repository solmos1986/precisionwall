/* select all */
$("#view_all").on('click', function () {
    if (this.checked) {
        $('.persona[type="checkbox"]').each(function () {
            $('.persona').prop('checked', true);
        });
    } else {
        $('.persona[type="checkbox"]').each(function () {
            $('.persona').prop('checked', false);
        });
    }
});
/* modal */
$(document).on('click', '#view_report', function () {
    var persona_id = [];
    $('.persona[type="checkbox"]').each(function () {
        if (this.checked) {
            console.log(this.value)
            persona_id.push(this.value);
        }
    });
    console.log($('#images').is(':checked'))

    var options = {
        url: `${base_url}/cardex-report?pdf?companies=${$('#compañia').val()}&tipos=${$('#tipo_personal').val()}&cargos=${$('#cargos').val()}&personas=${$('#personas').val()}&eventos=${$('#eventos').val()}&images=${$('#images').is(':checked')}`,
        title: 'Preview',
        size: eModal.size.lg,
        buttons: [{
            text: 'ok',
            style: 'info',
            close: true
        }],
    };
    eModal.iframe(options);
});

$(document).on('click', '#skill_report_pdf', function () {
    console.log(
        table.page.info().recordsTotal
    );
    var persona_id = [];
    $('.persona[type="checkbox"]').each(function () {
        if (this.checked) {
            console.log(this.value)
            persona_id.push(this.value);
        }
    });
    console.log($('#images').is(':checked'))

    var options = {
        url: `${base_url}/cardex-report/pdf?companies=${$('#compañia').val()}&tipos=${$('#tipo_personal').val()}&cargos=${$('#cargos').val()}&personas=${$('#personas').val()}&eventos=${$('#eventos').val()}&images=${$('#images').is(':checked')}`,
        title: 'Preview',
        size: eModal.size.lg,
        buttons: [{
            text: 'ok',
            style: 'info',
            close: true
        }],
    };
    eModal.iframe(options);
});

$("#skill_report_excel").on('click', function (evt) {

    $('#descargar_excel').attr("action", `${base_url}/cardex-report/excel?companies=${$('#compañia').val()}&tipos=${$('#tipo_personal').val()}&cargos=${$('#cargos').val()}&personas=${$('#personas').val()}&eventos=${$('#eventos').val()}&images=${$('#images').is(':checked')}`);
    $("#descargar_excel").submit();

});

