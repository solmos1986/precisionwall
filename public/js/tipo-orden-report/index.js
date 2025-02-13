function select2_status() {
    $("#status")
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/order-report/status`,
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
            proyectos(e.params.data["id"]);
        });
}

function proyectos() {
    $.ajax({
        type: "POST",
        url: `${base_url}/order-report/projects`,
        data: {
            status: $('#status_proyectos').val()
        },
        dataType: "json",
        async: false,
        success: function (response) {
            console.log(response)
            //elimina todo elvalue de select
            $("#proyects").empty();
            //recorre la respuesta
            $.each(response, function (i, item) {
                //console.log(i, item)
                $('#proyects').append('<option value="' + item.Pro_ID + '">' + item
                    .nombre + '</option>');
            });
            //reinicia el select
            $('#proyects').multiselect('rebuild');
            multi_select_proyectos();
        },
    });
}
function materiales(proyectos_id, tipo_material) {
    $.ajax({
        type: "POST",
        url: `${base_url}/order-report/materials`,
        data: {
            proyectos: proyectos_id,
            tipo_material: tipo_material
        },
        dataType: "json",
        async: false,
        success: function (response) {
            console.log(response)
            //elimina todo elvalue de select
            $("#materiales").empty();
            //recorre la respuesta
            $.each(response, function (i, item) {
                $('#materiales').append('<option value="' + item.material_id + '">' + item
                    .denominacion + '</option>');
            });
            //reinicia el select
            $('#materiales').multiselect('rebuild');
            multi_select_proyectos();
        },
    });
}

function multi_select_status() {
    $('#status_proyectos').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        //evento charge solicita datos
        onChange: function (option, checked) {
            proyectos()
        }
    })
        .multiselect('selectAll', true);
}

function multi_select_status_orden() {
    $('#multi_select_status_orden').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        //evento charge solicita datos
        onChange: function (option, checked) {
            //verificar_tipo();
        },

    })
        .multiselect('selectAll', true);
}

function multi_select_proyectos() {
    $('#proyects').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        //evento charge solicita datos
        onChange: function (option, checked) {
            console.log(option)
            verificar_tipo();
        }
    });
}
function multi_select_materiales() {
    $('#materiales').multiselect({
        buttonClass: 'form-control form-control-sm',
        buttonWidth: '100%',
        includeSelectAllOption: true,
        selectAllText: 'select all',
        selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: 400,
        //evento charge solicita datos
        onChange: function (option, checked) {

        }
    });
}

/*preview document */
$("#view_pdf").on('click', function (evt) {
    if ($("#proyects").val().length > 0) {
        var options = {
            url: `${base_url}/order-report/view-pdf?fecha_inicio=${$("#fecha_inicio").val()}&fecha_fin=${$("#fecha_fin").val()}&status=${$("#status_proyectos").val()}&status_orden=${$("#multi_select_status_orden").val()}&proyectos=${$("#proyects").val()}&materiales=${$("#materiales").val()}&detalle=${$("#detalle").val()}&view=${$(".view").val()}`,
            title: 'Preview',
            size: eModal.size.lg,
            buttons: [{
                text: 'ok',
                style: 'info',
                close: true
            }],
        };
        eModal.iframe(options);
    } else {
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Select project',
            showConfirmButton: false,
            timer: 1000
        });
    }
});
/*descarga document */
$("#download_pdf").on('click', function (evt) {
    $('#descargar_pdf').attr("action", `${base_url}/order-report/download-pdf?fecha_inicio=${$("#fecha_inicio").val()}&fecha_fin=${$("#fecha_fin").val()}&status=${$("#status").val()}&proyectos=${$("#proyects").val()}&materiales=${$("#materiales").val()}&detalle=${$("#detalle").val()}&view=${$(".view").val()}`);
    $("#descargar_pdf").submit();
});
/*descarga excel */
$("#excel_pdf").on('click', function (evt) {
    $('#descargar_pdf').attr("action", `${base_url}/order-report/excel-pdf?fecha_inicio=${$("#fecha_inicio").val()}&fecha_fin=${$("#fecha_fin").val()}&status=${$("#status").val()}&proyectos=${$("#proyects").val()}&materiales=${$("#materiales").val()}&detalle=${$("#detalle").val()}&view=${$(".view").val()}`);
    $("#descargar_pdf").submit();
});

/* carga de tipo */
$(".tipo").on('change', function (evt) {
    verificar_tipo()
});
function verificar_tipo() {
    console.log($('#proyects').val())
    if ($('input:radio[name=tipo]:checked').val() == 'material') {
        materiales($('#proyects').val(), 'material');
    } else {
        materiales($('#proyects').val(), 'equipo');
    }
}
/* carga de detalle */
$("#detalle").on('change', function (evt) {
    verificar_detalle()
});
function verificar_detalle() {
    if ($('input:checkbox[name=detalle]:checked').val() == 'false') {
        $("#detalle").val('true')
    } else {
        $("#detalle").val('false')
    }
}

/* carga de view */
$(".view").on('change', function (evt) {
    verificar_view()
});
function verificar_view() {
    if ($('input:radio[name=view]:checked').val() == 'view_material') {
        $(".view").val('view_proyecto');
    } else {
        $(".view").val('view_material');
    }
}

/* preconstruccion */
multi_select_proyectos();
multi_select_materiales();
multi_select_status();
multi_select_status_orden()

/* inizializando */
materiales($('#proyects').val(), 'material');
proyectos($('#proyects').val());
select2_status();