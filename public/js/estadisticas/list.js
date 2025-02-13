
function table_tareas(tareas, area_id, floor_id, proyecto_id) {
    var table = `
    <fieldset class="border p-1 ml-5 mb-0">
    <legend class="w-auto" style="font-size:0.9rem;">Task:</legend>
    <table class="table thead-primary" >
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>`;
    tareas.forEach(tarea => {
        table += `
        <tr>
            <td> <i class="far fa-chart-bar ms-text-primary cursor-pointer loadChart" title="view statistics" data-pro_id="${proyecto_id}" data-floor_id="${floor_id}" data-area_id="${area_id}" data-task_id="${tarea.Task_ID}" data-tipo="task"></i></td>
            <td>${tarea.Nombre}</td>
        </tr>                   
    `;
    });
    table += `
        </tbody>
    </table>
    </fieldset>`;
    return table;
}
function table_areas(areas, floor_id, proyecto_id, view_task) {
    var table = `
    <fieldset class="border p-1 ml-5 mb-0">
    <legend class="w-auto" style="font-size:0.9rem;">Areas:</legend>
    <table class="table table-hover thead-primary" >
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>`;
    areas.forEach(area => {
        /* validar tareas */
        var tareas = ``;
        if (view_task) {
            tareas = `
                <tr>
                    <td colspan="4">
                        ${table_tareas(area.tareas, area.Area_ID, floor_id, proyecto_id)}
                    </td>
                </tr>  
                `;
        }
        table += `
        <tr>
            <td> <i class="far fa-chart-bar ms-text-primary cursor-pointer loadChart"  data-pro_id="${proyecto_id}" data-floor_id="${floor_id}" data-area_id="${area.Area_ID}" data-tipo="areas" title="view statistics"></i></td>
            <td>${area.Nombre}</td>
        </tr>   
        ${tareas == '' ? '' : tareas}                    
    `;
    });
    table += `
        </tbody>
    </table>
    </fieldset>`;
    return table;
}
function table_floor(floors, proyecto_id, view_areas, view_task) {
    var table = `
    <fieldset class="border p-1 ml-5 mb-0">
    <legend class="w-auto" style="font-size:0.9rem;">Floors:</legend>
    <table class="table thead-primary" >
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
        </fieldset>`;
    floors.forEach(floor => {
        /* validar areas */
        var areas = ``;
        if (view_areas) {
            areas = `
                <tr>
                    <td  colspan="6">
                        ${table_areas(floor.area_control, floor.Floor_ID, proyecto_id, view_task)}
                    </td>
                </tr>  
                `;
        }
        table += `
        <tr>
            <td> <i class="far fa-chart-bar ms-text-primary cursor-pointer loadChart" title="view statistics"  data-pro_id="${proyecto_id}" data-floor_id="${floor.Floor_ID}" data-tipo="floor"></i></td>
            <td>${floor.Nombre}</td>
        </tr> 
        ${areas == '' ? '' : areas}                    
    `;
    });
    table += `
        </tbody>
    </table>
    </fieldset>`;
    return table
}
function table_proyectos(proyectos, view_floor, view_areas, view_task) {
    var table = `
    <table class="table table-hover thead-primary w-100" >
        <thead>
            <tr>
                <th style="background: #4eb0e9;" width="80">#</th>
                <th style="background: #4eb0e9;">Codigo</th>
                <th style="background: #4eb0e9;">Project</th>
                <th style="background: #4eb0e9;">GC</th>
                <th style="background: #4eb0e9;" width="120" >Status</th>
                <th style="background: #4eb0e9;">Project Manager</th>
                <th style="background: #4eb0e9;">Foreman</th>
                <th style="background: #4eb0e9;">Superintendent</th>
            </tr>
        </thead>
        <tbody id="contenido_tbody">`;
    proyectos.forEach(proyecto => {
        /* validar floor */
        var floor = ``;
        if (view_floor) {
            floor = `
                <tr id="${proyecto.Pro_ID}" style="display:none">
                    <td  colspan="8">
                        ${table_floor(proyecto.floors, proyecto.Pro_ID, view_areas, view_task)}
                    </td>
                </tr>  
                `;
        }
        table += `
        <tr>
            <td>
            <i class="far fa-chart-bar ms-text-primary cursor-pointer p-0 loadChart" title="view statistics" data-pro_id="${proyecto.Pro_ID}" data-tipo="proyecto" ></i>
            <i class="far fa-eye-slash ms-text-primary cursor-pointer p-0 view_detail" title="view leven" data-pro_id="${proyecto.Pro_ID}" data-tipo="empresa"></i>
            </td>
            <td>${proyecto.Codigo}</td>
            <td>${proyecto.Nombre}</td>
            <td>${proyecto.NombreEmpresa}</td>
            <td>${proyecto.nombre_estatus}</td>
            <td>${proyecto.nombre_project_manager}</td>
            <td>${proyecto.nombre_foreman}</td>
            <td>${proyecto.nombre_super}</td>
        </tr>
           ${floor == '' ? '' : floor}             
    `;
    });
    table += `
        </tbody>
    </table>`;
    return table;
}
function table_empresas(empresas) {
    var table = `
    <div class="text-center">
        <strong>List Company:</strong>
    </div>
    <table class="table table-bordered thead-primary" >
        <thead>
            <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Company</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="contenido_tbody">`;
    empresas.forEach(empresa => {
        table += `
        <tr>
            <td>
                <i class="far fa-chart-bar ms-text-primary cursor-pointer loadChart" title="view statistics" data-emp_id="${empresa.Emp_ID}" data-estatus_id="${empresa.Estatus_ID}"  data-tipo="empresa"></i>
            </td>
            <td>${empresa.Codigo}</td>
            <td>${empresa.Nombre}</td>
            <td>${empresa.Nombre_Estatus === undefined ? 'All status' : empresa.Nombre_Estatus}</td>
        </tr>                   
    `;
    });
    table += `
        </tbody>
    </table>`;
    return table;
}


/////////////////////
/**Funciones chart */
/////////////////////

var chartResponse;
var char_response_buenos;
var char_response_malos;

function loadChart(task_id, area_id, pro_id, emp_id, floor_id, tipo, estatus_id) {
    $("#proyect_manager").val()
    $('#spinner').show();
    $('#div_chart').hide();
    $('#spinner h5').text('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/statistics/proyect-manager-result`,
        data: {
            task_id: task_id,
            area_id: area_id,
            pro_id: pro_id,
            emp_id: emp_id,
            floor_id: floor_id,
            tipo: tipo,
            status: estatus_id
        },
        tryCount: 0,
        retryLimit: 3,
        dataType: 'json',
        success: function (response) {
            $('#spinner').hide();
            $('#div_chart').show();
            chartResponse = response;
            localStorage.setItem('chartResponse', JSON.stringify(response));
            const datos = new Charbarras(chartResponse);
            $(".detail").prop('checked', false);
            $(".good").prop('checked', false)
            $(".warning").prop('checked', false)
            switch (response.tipo) {
                case 'task':
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
                                    formatter: function (value, context) {

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
                                    ticks: {
                                    },
                                },
                            }
                        },
                        plugins: [ChartDataLabels],
                        data: {
                            labels: ['Hours Est', 'Hours Used', 'Hours Left', '% Completed', '% Hours used'],
                            datasets: [
                                datos.mostrar_tareas()
                            ]
                        }
                    });
                    break;
                case 'areas':
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
                                    formatter: function (value, context) {

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
                                    ticks: {
                                    },
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
                    break;
                case 'floor':
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
                                    formatter: function (value, context) {

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
                                    ticks: {
                                    },
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
                    break;
                case 'proyecto':
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
                                    formatter: function (value, context) {

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
                                    ticks: {
                                    },
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
                    break;
                case 'empresa':
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
                                    formatter: function (value, context) {
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
                                    ticks: {
                                    },
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
                    myBarChart.getDatasetMeta(5).hidden = true;
                    myBarChart.getDatasetMeta(6).hidden = true;
                    myBarChart.update();
                    break;
                default:
                    break;
            }

        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('error textStatus', textStatus)
            console.log('error status', xhr.status)
            if (textStatus == 'error') {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    $.ajax(this);
                    return;
                }else{
                    $('#spinner h5').text('Error');
                }
                return;
                console.log('return' )
            }
            if (xhr.status == 500) {
                //handle error
            } else {
                //handle error
            }
          
        }
    });
}
$(document).on('click', '.loadChart', function () {
    loadChart($(this).data('task_id'),
        $(this).data('area_id'),
        $(this).data('pro_id'),
        $(this).data('emp_id'),
        $(this).data('floor_id'),
        $(this).data('tipo'),
        $(this).data('estatus_id'));
    $(".view_extras").hide();
});

$(".detail").change(function () {
    if (this.checked) {
        var guardado = localStorage.getItem('chartResponse');
        let MemoryChart = JSON.parse(guardado);//desmontaje
        const datos = new Charbarras(MemoryChart);
        switch (MemoryChart.tipo) {
            case 'task':
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
                                formatter: function (value, context) {

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
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data: {
                        labels: ['Hours Est', 'Hours Used', 'Hours Left', '% Completed', '% Hours used'],
                        datasets: [
                            datos.mostrar_tareas()
                        ]
                    }
                });
                break;
            case 'areas':
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
                                formatter: function (value, context) {

                                    switch (context.datasetIndex) {
                                        case 3:
                                            /*   console.log(
                                                  context.chart.data.datasets[0].data[context.dataIndex],
                                                  context.chart.data.datasets[3].data[context.dataIndex]
                                              ); */
                                            var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        case 4:
                                            var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        default:

                                            break;
                                    }
                                    //return value;
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data: datos.destructure('tarea')
                });
                break;
            case 'floor':
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
                                formatter: function (value, context) {

                                    switch (context.datasetIndex) {
                                        case 3:
                                            var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        case 4:
                                            var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        default:

                                            break;
                                    }
                                    //return value;
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data:
                        datos.destructure('areas_control')
                });
                break;
            case 'proyecto':
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
                                formatter: function (value, context) {

                                    switch (context.datasetIndex) {
                                        case 3:
                                            var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        case 4:
                                            var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        default:

                                            break;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data:
                        datos.destructure('floor')
                });
                break;
            case 'inicio':
                console.log('inicio detectado')
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
                                formatter: function (value, context) {

                                    switch (context.datasetIndex) {
                                        case 3:
                                            var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        case 4:
                                            var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        default:

                                            break;
                                    }
                                    //return value;
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data:
                        datos.destructure('proyecto')
                });
                break;
            case 'empresa':
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
                                formatter: function (value, context) {

                                    switch (context.datasetIndex) {
                                        case 3:
                                            var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        case 4:
                                            var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        default:

                                            break;
                                    }
                                    //return value;
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data:
                        datos.destructure('proyecto')
                });
                break;
            case 'resumen':
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
                                formatter: function (value, context) {
                                    switch (context.datasetIndex) {
                                        case 3:
                                            var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        case 4:
                                            var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                            porcentaje = Math.round(porcentaje * 100);
                                            if (Number.isNaN(porcentaje)) {
                                                return `0%`;
                                            } else {
                                                return `${porcentaje}%`;
                                            }
                                            break;
                                        default:

                                            break;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data:
                        datos.destructure('proyecto')
                });
                break;
        }
    } else {
        var guardado = localStorage.getItem('chartResponse');
        let MemoryChart = JSON.parse(guardado);//desmontaje
        const datos = new Charbarras(MemoryChart);
        switch (MemoryChart.tipo) {
            case 'task':
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
                                formatter: function (value, context) {

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
                                ticks: {
                                },
                            },
                        }
                    },
                    plugins: [ChartDataLabels],
                    data: {
                        labels: ['Hours Est', 'Hours Used', 'Hours Left', '% Completed', '% Hours used'],
                        datasets: [
                            datos.mostrar_tareas()
                        ]
                    }
                });
                break;
            case 'areas':
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
                                formatter: function (value, context) {

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
                                ticks: {
                                },
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
                break;
            case 'floor':
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
                                formatter: function (value, context) {
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
                                ticks: {
                                },
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
                break;
            case 'proyecto':
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
                                formatter: function (value, context) {

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
                                ticks: {
                                },
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
                break;
            case 'empresa':
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
                                formatter: function (value, context) {
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
                                ticks: {
                                },
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
                break;
            case 'inicio':
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
                                formatter: function (value, context) {
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
                                ticks: {
                                },
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
                break;
            case 'resumen':
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
                                formatter: function (value, context) {
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
                                ticks: {
                                },
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
                break;
            default:
                break;

        }
    }
});
/* mostrar niveles en proyectos  */
$(document).on('click', '.view_detail', function () {
    $(this).toggleClass('fa-eye-slash').toggleClass('fa-eye');
    view_detalle_proyectos($(this).data('pro_id'));
});

function view_detalle_proyectos(pro_id) {
    const verficar = $(`#${pro_id}`).is(":visible");
    if (verficar) {
        $(`#${pro_id}`).hide();
    } else {
        $(`#${pro_id}`).show();
    }
}
/*ubicar proyectos buenos */
$(".warning").change(function () {
    if (this.checked) {
        var guardado = localStorage.getItem('chartResponse');
        console.log(JSON.parse(guardado));
        const proyectos_malos = new Charbarras(JSON.parse(guardado));
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
                        formatter: function (value, context) {
                            switch (context.datasetIndex) {
                                case 3:
                                    var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                    porcentaje = Math.round(porcentaje * 100);
                                    return `${porcentaje}%`;
                                    break;
                                case 4:
                                    var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                    porcentaje = Math.round(porcentaje * 100);
                                    return `${porcentaje}%`;
                                    break;
                                default:

                                    break;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                        },
                    },
                }
            },
            plugins: [ChartDataLabels],
            data:
                proyectos_malos.verficar_proyectos_malos()
        });
    } else {
        $('.detail').trigger('change');
    }
});

$(".good").change(function () {
    var guardado = localStorage.getItem('chartResponse');
    if (this.checked) {
        const proyectos_buenos = new Charbarras(JSON.parse(guardado));
        //ejecucion
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
                        formatter: function (value, context) {
                            switch (context.datasetIndex) {
                                case 3:
                                    var porcentaje = parseInt(context.chart.data.datasets[3].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                    porcentaje = Math.round(porcentaje * 100);
                                    return `${porcentaje}%`;
                                    break;
                                case 4:
                                    var porcentaje = parseInt(context.chart.data.datasets[4].data[context.dataIndex]) / parseInt(context.chart.data.datasets[0].data[context.dataIndex]);
                                    porcentaje = Math.round(porcentaje * 100);
                                    return `${porcentaje}%`;
                                    break;
                                default:

                                    break;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                        },
                    },
                }
            },
            plugins: [ChartDataLabels],
            data:
                proyectos_buenos.verficar_proyectos_buenos()
        });
    } else {
        $('.detail').trigger('change');
    }
});

