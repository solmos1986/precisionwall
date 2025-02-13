/* variales */
var company;
var ready = [];
/* empresas */
$("#select2_company").select2({
    theme: "bootstrap4",
    ajax: {
        url: `${base_url}/statistics/company`,
        type: "post",
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                searchTerm: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response,
            };
        },
        cache: true,
    },
})
    .on("select2:select", function (e) {
        $('option', $('#multiselect_project')).each(function (element) {
            $(this).removeAttr('selected').prop('selected', false);
        });
        $('#multiselect_project').multiselect('refresh');
    });
/* proyectos */
function mutiselect_select_proyecto(status) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/statistics/project/${status}`,
        dataType: "json",
        success: function (response) {
            //elimina todo elvalue de select
            $("#multiselect_project").empty();
            //recorre la respuesta
            $.each(response, function (i, item) {
                //console.log(i, item)         
                $('#multiselect_project').append('<option value="' + item.Pro_ID + '">' + item
                    .Nombre + '</option>');
            });
            //reinicia el select
            $('#multiselect_project').multiselect('rebuild');
        },
    });
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
        maxHeight: 400,
        onChange: function (option, checked) {
            $(`#filtro`).val(null).trigger('change');
            $(`#select2_company`).val(null).trigger('change');
        }
    });
}
/* personal Filtros*/
function select2Search() {
    $("#filtro").select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/statistics/search`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                    status: $('#status').val(),
                    cargo: $('#cargo').val(),
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    })
        .on("select2:select", function (e) {
            $('option', $('#multiselect_project')).each(function (element) {
                $(this).removeAttr('selected').prop('selected', false);
            });
            $('#multiselect_project').multiselect('refresh');
            //lista_proyectos()
        });
}
$("#cargo").change(function () {
    $(`#filtro`).val(null).trigger('change');
    select2Search();
});
/*buscar */
$("#buscar").click(function () {
    //$('#list-proyectos').DataTable().search('').draw();
    table_proyecto.ajax.url(
        `${base_url}/goal-project/data-table?multiselect_project=${$('#multiselect_project').val()}&status=${$('#status').val()}&from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&cargo=${$('#cargo').val()}&filtro=${$('#filtro').val()}`
    ).load();
});
/*limpiar */
$("#limpiar").click(function () {
    $(`#select2_company`).val(null).trigger('change');
    $(`#filtro`).val(null).trigger('change');
    $('#from_date').val('');
    $('#to_date').val('');
    $('option', $('#multiselect_project')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#multiselect_project').multiselect('refresh');
    //lipiar datatavle
    $(".check").prop("checked", false);
    $('#lista_proyectos').DataTable().search('').draw();
});
$("#status").change(function () {
    mutiselect_select_proyecto($('#status').val() == '' ? 'all' : $('#status').val())
});

/* cargar tabla */

multiselect_project();
$('#spinner').hide();
select2Search();
$(document).ready(function () {
    //lista_proyectos()
    mutiselect_select_proyecto(1);
    //$("#buscar").trigger("click");
});
