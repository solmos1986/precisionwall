$("#view_pdf").on('click', function (evt) {
    let data_table = table.rows().data().toArray();
    var options = {
        url: `${base_url}/report-ticket-pdf?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&descripcion=${$('#descripcion').val()}`,
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
$(document).on('click', '#descarga_excel', function () {
    console.log( 'aki')
    $('#download_excel').attr("action", `${base_url}/report-ticket-excel?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&descripcion=${$('#descripcion').val()}`);
    $("#download_excel").submit();

});