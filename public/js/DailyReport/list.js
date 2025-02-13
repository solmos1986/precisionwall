$(document).on('click', '#open_modal_view', function () {
    $('.select_view').data('id', $(this).data('id'));
    $('input[id=admin]').prop('checked', true);

    $('#admin').val($(this).data('admin'))
    let reports = obtener_data_datatable();
  
    //default
    $('#view_daily').prop('href', `${$(this).data('admin')}?reports=${reports}&view=${$(this).data('id')}`)

    $('#cliente').val($(this).data('cliente'))
    $('#Modal_filter_view').modal('show')
});

$(document).on('change', '.select_view', function () {

    let reports = obtener_data_datatable();
    console.log(
        `${$(this).val()}?reports=${reports}?view=${$(this).data('id')}`
    )
    $('#view_daily').prop('href', `${$(this).val()}?reports=${reports}&view=${$(this).data('id')}`);
});

$(document).on('click', '#view_daily', function () {

    $('#Modal_filter_view').modal('hide')
});

function obtener_data_datatable() {
    var rows = table.rows().data().toArray();
    reports = [];
    rows.forEach(report => {
        if (report.id != null) {
            reports.push(report.Actividad_ID);
        }
    });
    return reports;
}