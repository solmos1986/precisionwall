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
            data: function(params) {
                return {
                    searchTerm: params.term, // search term
                };
            },
            processResults: function(response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    })
    .on("select2:select", function(e) {
        $('option', $('#multiselect_project')).each(function(element) {
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
        success: function(response) {
            //elimina todo elvalue de select
            $("#multiselect_project").empty();
            //recorre la respuesta
            $.each(response, function(i, item) {
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
        onChange: function(option, checked) {
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
                data: function(params) {
                    return {
                        searchTerm: params.term, // search term
                        status: $('#status').val(),
                        cargo: $('#cargo').val(),
                    };
                },
                processResults: function(response) {
                    return {
                        results: response,
                    };
                },
                cache: true,
            },
        })
        .on("select2:select", function(e) {
            $('option', $('#multiselect_project')).each(function(element) {
                $(this).removeAttr('selected').prop('selected', false);
            });
            $('#multiselect_project').multiselect('refresh');
            //lista_proyectos()
        });
}
$("#cargo").change(function() {
    $(`#filtro`).val(null).trigger('change');
    select2Search();
});
/*buscar */
$("#buscar").click(function() {
    lista_proyectos();
});
/*limpiar */
$("#limpiar").click(function() {
    $(`#select2_company`).val(null).trigger('change');
    $(`#filtro`).val(null).trigger('change');
    $('#from_date').val('');
    $('#to_date').val('');
    $('option', $('#multiselect_project')).each(function(element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    $('#multiselect_project').multiselect('refresh');
});
$("#status").change(function() {
    mutiselect_select_proyecto($('#status').val() == '' ? 'all' : $('#status').val())
});

/* cargar tabla */
function lista_proyectos() {
    $('#proyectos').html('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/list-jobs`,
        data: $('#form_estadisticas').serialize(),
        dataType: 'json',
        success: function (response) {
            $('#table').html('');
            var table = table_proyectos(response)
            $('#table').append(table);
        },
    });
};

function inicio_ready_graficos() {
    $('#spinner').show();
    $('#div_chart').hide();
    $('#spinner h5').text('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/statistics/proyect-manager-result`,
        data: {
            tipo: 'inicio'
        },
        dataType: 'json',
        success: function(response) {
            $('#spinner').hide();
            $('#div_chart').show();
            chartResponse = response;
            const datos = new Charbarras(response);
            $(".detail").prop('checked', false)
            myBarChart.destroy();
            myBarChart = new Chart(ctx, {
                type: 'bar',
                options: {
                    plugins: {
                        legend: {
                            display: true
                        },
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top',
                            borderWidth: 2,
                            font: {
                                weinht: 'bold'
                            },
                            formatter: function(value, context) {
                                switch (context.dataIndex) {
                                    case 3:
                                        return `${context.chart.data.datasets[0].data[5]}%`;
                                        break;
                                    case 4:
                                        return `${context.chart.data.datasets[0].data[6]}%`;
                                        break;

                                    default:
                                        return `${context.chart.data.datasets[0].data[context.dataIndex]}`;
                                        break;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {},
                        },
                    }
                },
                plugins: [ChartDataLabels],
                data: {
                    labels: ['Hours Est', 'Hours Used', 'Hours Left', '% Completed', '% Hours used'],
                    datasets: [
                        datos.mostrar_resumen()
                    ]
                }
            });
        },
        error: function() {
            $('#spinner h5').text('Error');
        }
    });
}
var datos_buenos;

function resumen(proyectos, cargo, personal_id, status) {
    $('#spinner').show();
    $('#div_chart').hide();
    $('#spinner h5').text('');
    $(".view_extras").show();
    $(".good").prop('checked', false)
    $(".warning").prop('checked', false)
    $.ajax({
        type: 'POST',
        url: `${base_url}/statistics/proyect-manager-result`,
        data: {
            proyectos: proyectos,
            cargo: cargo,
            personal_id: personal_id,
            status: status,
            tipo: 'resumen',
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val()
        },
        dataType: 'json',
        success: function(response) {
            $('#spinner').hide();
            $('#div_chart').show();
            chartResponse = response;
            localStorage.setItem('chartResponse', JSON.stringify(response));
            const datos = new Charbarras(response);
            $(".detail").prop('checked', false)
            myBarChart.destroy();
            myBarChart = new Chart(ctx, {
                type: 'bar',
                options: {
                    plugins: {
                        legend: {
                            display: true
                        },
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top',
                            borderWidth: 2,
                            font: {
                                weinht: 'bold'
                            },
                            formatter: function(value, context) {
                                switch (context.dataIndex) {
                                    case 3:
                                        return `${context.chart.data.datasets[0].data[5]}%`;
                                        break;
                                    case 4:
                                        return `${context.chart.data.datasets[0].data[6]}%`;
                                        break;

                                    default:
                                        return `${context.chart.data.datasets[0].data[context.dataIndex]}`;
                                        break;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {},
                        },
                    }
                },
                plugins: [ChartDataLabels],
                data: {
                    labels: ['Hours Est', 'Hours Used', 'Hours Left', '% Completed', '% Hours used'],
                    datasets: [
                        datos.mostrar_resumen()
                    ]
                }
            });
        },
        error: function() {
            $('#spinner h5').text('Error');
        }
    });
}

function inicio_ready_tabla() {
    $.ajax({
        type: 'POST',
        url: `${base_url}/statistics/proyect-manager`,
        data: {
            proyectos: 'all',
            view_floor: true,
            view_area: true,
            view_task: true
        },
        dataType: 'json',
        success: function(response) {
            switch (response.tipo) {
                case "proyectos":
                    var table = table_proyectos(response.proyectos, response.view_floor, response.view_areas, response.view_task)
                    $('#table').append(table);
                    break;
                default:
                    break;
            }
        },
    });
}

function ready_tabla(datos, tipo) {
    var analizando;
    switch (tipo) {
        case 'empresa':
            analizando = {
                status: $('#status').val(),
                select2_company: datos,
            }
            break;

        default:
            break;
    }
    $.ajax({
        type: 'POST',
        url: `${base_url}/statistics/proyect-manager`,
        data: analizando,
        dataType: "json",
        delay: 250,
        success: function(response) {
            switch (response.tipo) {
                case "proyectos":
                    var table = table_proyectos(response.proyectos, response.view_floor, response.view_areas, response.view_task)
                    $('#table').append(table);
                    break;
                default:
                    break;
            }
        },
    });
}
multiselect_project();
$('#spinner').hide();
select2Search();
$(document).ready(function() {
    //lista_proyectos()
    mutiselect_select_proyecto(1);
    $( "#buscar" ).trigger( "click" );
});
