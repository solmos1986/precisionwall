
var ctx = $('#myChart');
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {

        }
    }
});
$(document).on("click", ".view_proyecto", function () {

    $('#fromJobInformacion').trigger('reset');
    $('#new_date_work').val(moment().format('MM/DD/YYYY HH:mm:ss'));
    $.ajax({
        type: "POST",
        url: `${base_url}/info-project/edit/${$(this).data('id')}`,
        data: {
            proyecto_id: $(this).data('id')
        },
        dataType: "json",
        success: function (response) {
            /* dato proyecto*/
            limpiar_values_text();
            $('#fecha_inicio').val(response.proyecto.Fecha_Inicio).attr('readonly', false);
            $('#fecha_fin').val(response.proyecto.Fecha_Fin).attr('readonly', false);
            $('#horas_con').val(response.proyecto.Horas);

            $('.view_date_proyecto').data('proyecto_id', response.proyecto.Pro_ID);
            $('.view_action').data('proyecto_id', response.proyecto.Pro_ID);
            $('.view_info').data('proyecto_id', response.proyecto.Pro_ID);
            $('.change_reg').data('proyecto_id', response.proyecto.Pro_ID);
            $('.check-color').data('proyecto_id', response.proyecto.Pro_ID);
            cambio_color(response.proyecto.color)

            $('#proyecto_id').val(response.proyecto.Pro_ID).attr('readonly', false);
            $('#company').text(`Company: ${response.proyecto.nombre_empresa} - Project ${response.Codigo} ${response.Nombre}`).attr('readonly', false);
            $('#gc_company').text(`${response.proyecto.nombre_empresa}`).attr('readonly', false);
            $('#job').text(`${response.proyecto.Codigo}`).attr('readonly', false);
            $('#name_proyecto').text(`${response.proyecto.Nombre}`).attr('readonly', false);

            //title modal
            $('#title_modal').text(`${response.proyecto.Nombre}`);
            $('#street').text(`${response.proyecto.Calle}`).attr('readonly', false);
            $('#city').text(`${response.proyecto.Ciudad}`).attr('readonly', false);
            $('#state').text(`${response.proyecto.Estado}`).attr('readonly', false);
            $('#zip_code').text(`${response.proyecto.Zip_Code}`).attr('readonly', false);
            $('#proyecto_status').text(`${response.proyecto.nombre_status}`).attr('readonly', false);
            $('#proyecto_type').text(`${response.proyecto.nombre_tipo}`).attr('readonly', false);
            $('#status_proyecto').val(response.proyecto.Estatus_ID);
            $('#tipo_proyecto').val(response.proyecto.Tipo_ID);
            console.log(response.proyecto.Tipo_ID, response.proyecto.Estatus_ID)
            $('#nota').val(response.proyecto.nota).attr('readonly', false);

            $('#pm').text(response.proyecto.Manager).attr('readonly', false);
            $('#lead').text(response.proyecto.lead_proyecto).attr('readonly', false);
            $('#field_superintendent').text(response.proyecto.field_superintendent).attr('readonly', false);
            $('#foreman').text(response.proyecto.Foreman).attr('readonly', false);
            $('#GC_pmr').text(`${response.proyecto.Project_Manager == null ? '' : response.proyecto.Project_Manager} ${response.proyecto.Project_Manager_celular == null ? '' : response.proyecto.Project_Manager_celular} ${response.proyecto.Project_Manager_email == null ? '' : response.proyecto.Project_Manager_email}`).attr('readonly', false);

            $('#superintendet').text(`${response.proyecto.Coordinador_Obra == null ? '' : response.proyecto.Coordinador_Obra} ${response.proyecto.Coordinador_Obra_celular == null ? '' : response.proyecto.Coordinador_Obra_celular} ${response.proyecto.Coordinador_Obra_email == null ? '' : response.proyecto.Coordinador_Obra_email}`).attr('readonly', false);
            /* info */
            if (response.info == null) {
                limpia_values_info();
            } else {
                limpia_values_info();
                $('#contact').val(`${response.info.contact_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.contact_id)}`);
                $('#submittals').val(`${response.info.submittals_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.submittals_id)}`)
                $('#plans').val(`${response.info.plans_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.plans_id)}`)
                $('#vendor').val(`${response.info.vendor_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.vendor_id)}`)
                $('#const_schedule').val(`${response.info.const_schedule_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.const_schedule_id)}`)
                $('#field_folder').val(response.info.field_folder_id).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.field_folder_id)}`)
                $('#brake_down').val(`${response.info.brake_down_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.brake_down_id)}`)
                $('#badges').val(`${response.info.badges_id}`).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.badges_id)}`)
                $('#special_material').val(response.info.special_material_id).addClass(`form-control form-control-sm text-${info_status(response.status, response.info.special_material_id)}`)
            }
            /* action */
            if (response.actions.length == 0) {
                limpiar_values_action();
            } else {
                load_action(response.actions)
            }
            /* carga de graficos */
            $('#div_chart').hide();
            $('#div_date_proyecto').hide();
            $('.spinner_graficos').show();

            carga_graficos(response.proyecto.Pro_ID)
            $('#modalCreateJobInformacion').modal('show');
        }
    });
})
function info_status(status, campo) {
    var color = '';
    status.forEach(estado => {
        if (estado.id == campo) {
            color = estado.status_color;
        }
    });
    return color;
}
function render_char(chartResponse) {
    //localStorage.setItem('chartResponse', JSON.stringify(response));
    const datos = new Charbarras(chartResponse);
    myBarChart.destroy();
    myBarChart = new Chart(ctx, {
        type: 'bar',
        options: {
            plugins: {
                legend: {
                    labels: {
                        padding: 10 //default is 10
                    },
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
            },
            animation: {
                onComplete: function () {
                    $('#imagen').val(myBarChart.toBase64Image())
                    if ($('#report_weekly').val() == '') {
                        const estado = chartResponse.porcentaje_horas_completadas - chartResponse.porcentaje_horas_trabajadas;
                        $('#report_weekly').val(`HE=${chartResponse.horas_estimadas} HU=${chartResponse.horas_trabajadas}  HL=${chartResponse.horas_restantes}  %Com=${chartResponse.porcentaje_horas_completadas}%  %HU=${chartResponse.porcentaje_horas_trabajadas}%  diff  %Com-%HU=${estado}% ${verificarNumNegativo(estado) == true ? 'Gain' : 'Loss'}`)
                    }
                }
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
}
/* inizialize */
var ctx = $('#myChart');

function limpiar_values_text() {
    $('#title_modal').text(``);
    $('#company').text(``).attr('readonly', false);
    $('#gc_company').text(``).attr('readonly', false);
    $('#job').text(``).attr('readonly', false);
    $('#name_proyecto').text(``).attr('readonly', false);
    $('#street').text(``).attr('readonly', false);
    $('#city').text(``).attr('readonly', false);
    $('#state').text(``).attr('readonly', false);
    $('#zip_code').text(``).attr('readonly', false);
    $('#proyecto_status').text(``).attr('readonly', false);
    $('#proyecto_type').text(``).attr('readonly', false);
    $('#day_aproximado').val(``).attr('readonly', false);
    $('#nota').val(``).attr('readonly', false);
    $('#horas_con').val('').attr('readonly', false);;
}
function limpia_values_info() {
    $('#contact').attr('class', `form-control form-control-sm`).val(0).attr('readonly', false);
    $('#submittals').attr('class', `form-control form-control-sm`).val('').attr('readonly', false);
    $('#plans').val(0).attr('class', `form-control form-control-sm`).attr('readonly', false);
    $('#vendor').val('').attr('class', `form-control form-control-sm`).attr('readonly', false);
    $('#const_schedule').attr('class', `form-control form-control-sm`).val(0).attr('readonly', false);
    $('#field_folder').attr('class', `form-control form-control-sm`).val(0).attr('readonly', false);
    $('#brake_down').attr('class', `form-control form-control-sm`).val(0).attr('readonly', false);
    $('#badges').attr('class', `form-control form-control-sm`).val('').attr('readonly', false);
    $('#special_material').attr('class', `form-control form-control-sm`).val('').attr('readonly', false);
}
function limpiar_values_action() {
    $('#view_weekly').html('');
    $('#view_week').html('');
    var weeklyHTML = `
    <div class="form-group row mb-1">
        <div class="col-sm-12">
            <textarea type="text" class="form-control form-control-sm" id="report_weekly" name="report_weekly" rows="4"
                placeholder="Weekly Report"></textarea>
        </div>
    </div>
    `;
    var weekHTML = `
    <div class="form-group row mb-1">
        <div class="col-sm-12">
            <textarea type="text" class="form-control form-control-sm" id="action_for_week" name="action_for_week" rows="4"
                placeholder="Action for the Week"></textarea>
        </div>
    </div>
    `;
    $('#view_weekly').append(weeklyHTML);
    $('#view_week').append(weekHTML);
}
function load_action(acciones) {
    $('#view_weekly').html('');
    $('#view_week').html('');
    var weeklyHTML = ``;
    var weekHTML = ``;
    const fecha_actual = moment().format('YYYY-MM-DD');

    var bandera;
    if (moment(acciones[0].fecha_proyecto_movimiento).format('YYYY-MM-DD') != fecha_actual) {
        bandera = true;
        weeklyHTML += `
        <div class="form-group row mb-1">
            <div class="col-sm-12">
                <textarea type="text" class="form-control form-control-sm" id="report_weekly" name="report_weekly" rows="4"
                    placeholder="Report Weekly"></textarea>
            </div>
        </div>
        `;
        weekHTML += `
        <div class="form-group row mb-1">
            <div class="col-sm-12">
                <textarea type="text" class="form-control form-control-sm" id="action_for_week" name="action_for_week" rows="4"
                    placeholder="Action for Week"></textarea>
            </div>
        </div>
        `;
    }
    acciones.forEach((accion, index) => {
        //console.log(moment(accion.fecha_proyecto_movimiento).format('MM-DD-YYYY'),fecha_actual)
        if (bandera == true) {
            weeklyHTML += `
            <div class="form-group row mb-1">
                <div class="col-sm-12">
                    <textarea type="text" class="form-control form-control-sm" id="report_weekly" name="report_weekly" rows="4"
                        placeholder="Report Weekly" readonly>${accion.report_weekly == null ? '' : accion.report_weekly}</textarea>
                </div>
            </div>
            `;
            weekHTML += `
            <div class="form-group row mb-1">
                <div class="col-sm-12">
                    <textarea type="text" class="form-control form-control-sm" id="action_for_week" name="action_for_week" rows="4"
                        placeholder="Action for Week" readonly>${accion.action_for_week == null ? '' : accion.action_for_week}</textarea>
                </div>
            </div>
            `;
        } else {
            weeklyHTML += `
            <div class="form-group row mb-1">
                <div class="col-sm-12">
                    <textarea type="text" class="form-control form-control-sm" id="report_weekly" name="report_weekly" rows="4"
                        placeholder="Report Weekly" ${index != 0 ? 'readonly' : ''} >${accion.report_weekly == null ? '' : accion.report_weekly}</textarea>
                </div>
            </div>
            `;
            weekHTML += `
            <div class="form-group row mb-1">
                <div class="col-sm-12">
                    <textarea type="text" class="form-control form-control-sm" id="action_for_week" name="action_for_week" rows="4"
                        placeholder="Action for Week" ${index != 0 ? 'readonly' : ''} >${accion.action_for_week == null ? '' : accion.action_for_week}</textarea>
                </div>
            </div>
            `;
        }
    });

    $('#view_weekly').append(weeklyHTML);
    $('#view_week').append(weekHTML);
}
/* carga de  graficos */
function carga_graficos(proyecto_id) {
    var ctx = $('#myChart');
    $.ajax({
        type: "POST",
        url: `${base_url}/info-project/load-graficos/${proyecto_id}`,
        data: {
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val()
        },
        dataType: "json",
        success: function (response) {
            $('.spinner_graficos').hide();
            $('#div_chart').show();
            $('#div_date_proyecto').show();
            $('#total_horas').val(response.data.proyecto.horas_estimadas).attr('readonly', true);
            $('#num_personas').val(response.data.personas).attr('readonly', true);
            $('#day_aproximado').val(response.data.dias).attr('readonly', true);
            render_char(response.data.proyecto)
        }
    });
}


$(".check-color").on('click', function () {
    $.ajax({
        type: "POST",
        url: `${base_url}/info-project/update-color`,
        data: {
            color: $(this).data('color'),
            proyecto_id: [$(this).data('proyecto_id')]
        },
        dataType: "json",
        success: function (response) {
            cambio_color(response.color);
            $(this).find('i').removeClass('inline fa fa-times position');
            dataTable.draw();
        }
    });
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
/*extras */
function verificarNumNegativo(x) {
    let res;
    switch (Math.sign(x)) {
        case 1:
            res = true;
            break;
        case 0:
            res = true;
            break;
        case -1:
            res = false;
            break;

        default:
            res = false;
            break;
    }
    return res;
}
$(".change_tipo_proyecto").change(function () {
    console.log($('#proyecto_id').val());
    select = $(this);
    $.ajax({
        type: "PUT",
        url: `${base_url}/info-project/update-general`,
        data: {
            tipo: "tipo",
            data: select.val(),
            proyecto_id: $('#proyecto_id').val()
        },
        dataType: "json",
        success: function (response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
});
$(".change_status_proyecto").change(function () {
    console.log($('#proyecto_id').val());
    $.ajax({
        type: "PUT",
        url: `${base_url}/info-project/update-general`,
        data: {
            tipo: "status",
            data: $(this).val(),
            proyecto_id: $('#proyecto_id').val()
        },
        dataType: "json",
        success: function (response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
});


//vista de historial
$(document).on("click", ".view_historial_notificacion", function () {
    console.log('open')
    const proyecto_id = $(this).data('id');
    dataTable_historial_acciones.ajax.url(`${base_url}/action-week/historial/${proyecto_id}`).draw();
    $('#modalViewAccionesHistory').modal({
        show: true
    });
});