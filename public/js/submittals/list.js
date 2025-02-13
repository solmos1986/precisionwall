
function multiselect_status_submittals() {
    $('#status_submittals').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked) {
        }
    });
}
$("#buscar").click(function () {
    buscar_proyecto();
});
function buscar_proyecto() {
    dataTable.ajax.url(`${base_url}/submittals/data-table?proyectos=${$('#multiselect_project').val()}&status_submittals=${$('#status_submittals').val()}&status_proyecto=${$('#status_proyecto').val()}&date_from_vendor=${$('#date_from_vendor').val()}&date_to_vendor=${$('#date_to_vendor').val()}&date_from_gc=${$('#date_from_gc').val()}&date_to_gc=${$('#date_to_gc').val()}`).load();
}

function multiselect_project() {
    $('#multiselect_project').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked) {
            $(`#filtro`).val(null).trigger('change');
            $(`#select2_company`).val(null).trigger('change');
        }
    });
}
function multiselect_status() {
    $('#status_proyecto').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked) {

        }
    });
}

$("#limpiar").click(function () {
    $(`#select2_company`).val(null).trigger('change');
    $(`#filtro`).val(null).trigger('change');
    $('#date_from_vendor').val('');
    $('#date_to_vendor').val('');
    $('#date_from_gc').val('');
    $('#date_to_gc').val('');

    /* project */
    $('option', $('#multiselect_project')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#multiselect_project').multiselect('refresh');

    /* status submittals */
    $('option', $('#status_submittals')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#status_submittals').multiselect('refresh');

    /* status_proyecto */
    $('option', $('#status_proyecto')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#status_proyecto').multiselect('refresh');

    $('#list-proyectos').DataTable().search(this.value).draw();
});

$(document).on('click', '#descarga_excel', function () {
    console.log('aki')
    $('#download_excel').attr("action", `${base_url}/submittals/download-excel?proyectos=${$('#multiselect_project').val()}&status_submittals=${$('#status_submittals').val()}&status_proyecto=${$('#status_proyecto').val()}&date_from_vendor=${$('#date_from_vendor').val()}&date_to_vendor=${$('#date_to_vendor').val()}&date_from_gc=${$('#date_from_gc').val()}&date_to_gc=${$('#date_to_gc').val()}`);
    $("#download_excel").submit();

});

/* inizialize */
multiselect_project();
multiselect_status();
multiselect_status_submittals();