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
        enableCaseInsensitiveFiltering: true,
        maxHeight: 400,
        onChange: function (option, checked) {
            $(`#filtro`).val(null).trigger('change');
            $(`#select2_company`).val(null).trigger('change');
        }
    });
}
function multiselect_status() {
    $('#status').multiselect({
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
    buscar_proyecto();
});
/*limpiar */
$("#limpiar").click(function () {
    $(`#select2_company`).val(null).trigger('change');
    $(`#filtro`).val(null).trigger('change');
    $('#from_date').val('');
    $('#to_date').val('');
    /* project */
    $('option', $('#multiselect_project')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#multiselect_project').multiselect('refresh');
    /* status */
    $('option', $('#status')).each(function (element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#status').multiselect('refresh');
    $('#list-proyectos').DataTable().search(this.value).draw();
});
$("#status").change(function () {
    mutiselect_select_proyecto($('#status').val() == '3,4,2,5,6,1' ? 'all' : $('#status').val())
});

/* buscar proyecto */
function buscar_proyecto() {
    dataTable.ajax.url(`${base_url}/info-project/proyect?proyectos=${$('#multiselect_project').val()}&filtro=${$('#filtro').val()}&cargo=${$('#cargo').val()}&from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&status=${$('#status').val()}`).load();
}
/* modal view pdf */
$("#view_pdf").on('click', function (evt) {
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
        var options = {
            url: `${base_url}/info-project/view-pdf?proyectos=${proyectos_id}`,
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

$("#view_pdf_all").on('click', function () {
    if (this.checked) {
        $('.proyectos[type="checkbox"]').each(function () {
            $('.proyectos').prop('checked', true);
        });
    } else {
        $('.proyectos[type="checkbox"]').each(function () {
            $('.proyectos').prop('checked', false);
        });
    }
});
$(".check-color-all").on('click', function () {
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
            type: "POST",
            url: `${base_url}/info-project/update-color`,
            data: {
                color: $(this).data('color'),
                proyecto_id: proyectos_id
            },
            dataType: "json",
            success: function (response) {
                dataTable.draw();
                $("#view_pdf_all").prop('checked', false)
            }
        });
    }
});
function cambio_color(color) {
    $('.color-modal').removeClass('rojo');
    $('.color-modal').removeClass('verde');
    $('.color-modal').removeClass('amarillo');
    $('.color-modal').removeClass('celeste');
    $('.color-modal').removeClass('azul');
    $('.color-modal').removeClass('blanco');
    $('.color-modal').addClass(color);
}
/* inizialize */
multiselect_project();
multiselect_status();
select2Search();


