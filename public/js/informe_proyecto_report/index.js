
var dataTable = $('#list-proyectos').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: `${base_url}/project-reports/data-table?proyectos=${$('#multiselect_project').val()}`,
    order: [],
    columns: [{

        data: "Codigo",
        name: "Codigo",
    },
    {

        data: "Pro_ID",
        name: "Pro_ID",
        render: function (data, type, row, meta) {
            return `
                <input type="checkbox" class="proyecto" data-proyecto_id="${row.Pro_ID}" style="transform: scale(1.3);">
            `;
        }
    },
    {

        data: "Nombre",
        name: "Nombre"
    },
    {
        data: "empresa",
        name: "empresa"
    },
    {
        data: 'tipo',
        name: 'tipo',
    },
    {
        data: 'direccion',
        name: 'direccion',
    },
    {
        data: 'Manager',
        name: 'Manager',
    },
    {
        data: 'Cordinador',
        name: 'Cordinador',
    },
    {
        data: 'Foreman',
        name: 'Foreman',
    },
    {
        data: 'lead',
        name: 'lead',
    },
    {
        data: 'asistente_proyecto',
        name: 'asistente_proyecto',
    },
    {
        data: 'actions',
        name: 'actions',
        render: function (data, type, row, meta) {
            return `
                <i class="flaticon-pdf ms-text-primary cursor-pointer descarga_pdf" title="Download PDF" data-proyecto_id="${row.Pro_ID}"></i>
                <i class="flaticon-excel ms-text-primary cursor-pointer descarga_excel" title="Download Excel" data-proyecto_id="${row.Pro_ID}"></i>
            `;
        }
    }]
});

//EVENTOS DE DESCARGA DE DOCUMENTOS

$(document).on('click', '.descarga_pdf', function () {
    const proyectos = $(this).data('proyecto_id')
    var options = {
        url: `${base_url}/project-reports/descarga-pdf?proyectos=${proyectos}`,
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

/* modal view pdf */
$(document).on('click', '.descarga_excel', function () {
    const elemento = $(this);
    const proyectos_id = elemento.data('proyecto_id');
    console.log('excel', $(this).parent());
    $('#descarga_excel').attr("action", `${base_url}/project-reports/descarga-excel?proyectos=${proyectos_id}`);
    $('#descarga_excel').submit();

});

$(document).on('click', '#descarga_all_excel', function () {
    let proyectos = [];
    $('.proyecto').each(function (index, val) {
        if (this.checked) {
            proyectos.push($(val).data('proyecto_id'));
        }
    });
    if (proyectos.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        $('#descarga_excel').attr("action", `${base_url}/project-reports/descarga-excel?proyectos=${proyectos.toString()}`);
        $('#descarga_excel').submit();
    }
});

$(document).on('click', '#descarga_all_pdf', function () {
    let proyectos = [];
    $('.proyecto[type="checkbox"]').each(function (index, val) {
        if (this.checked) {
            proyectos.push($(val).data('proyecto_id'));
        }
    });
    if (proyectos.length == 0) {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'selection one',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        var options = {
            url: `${base_url}/project-reports/descarga-pdf?proyectos=${proyectos.toString()}`,
            title: 'Preview',
            size: eModal.size.lg,
            buttons: [{
                text: 'ok',
                style: 'info',
                close: true
            }],
        };
        eModal.iframe(options);
    }
});

function multiselect_project() {
    $('#multiselect_project').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked) {
            $(`#filtro`).val(null).trigger('change');
            $(`#select2_company`).val(null).trigger('change');
        }
    });
}

/*buscar */
$("#buscar").click(function () {
    dataTable.ajax.url(`${base_url}/project-reports/data-table?proyectos=${$('#multiselect_project').val()}`).load();
});
/*limpiar */
$("#limpiar").click(function () {
    $('option', $('#multiselect_project')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#multiselect_project').multiselect('refresh');
});

multiselect_project();
