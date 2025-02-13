var rowUpdate = [];
var dataTable = $('#lista_actividades').DataTable({
    order: [],
    scrollY: "600px",
    scrollX: true,
    scrollCollapse: true,
    language: {
        searchPlaceholder: "Criterion"
    },
    pageLength: 100,
    columns: [{
        data: "Horas_TM",
        name: "Horas_TM",
        render: function (data, type, row, meta, otros) {
            return `
                            <input type="text" name="actividad_id[]" class="actividad_id" id="actividad-id-${meta.row}" data-index="${meta.row}" value="${row.Actividad_ID}"
                                hidden>
                            <input type="text" name="registro_diario_id[]" class="registro_diario_id" id="registro-diario-${meta.row}" data-index="${meta.row}" value="${row.Reg_ID}"
                                hidden>
                            <input type="text" name="registro_actividad_id[]" class="registro_diario_actividad_id" id="registro-diario-actividad-${meta.row}" data-index="${meta.row}" value="${row.RDA_ID}"
                                hidden>
                            <input type="text" name="estado[]" class="estado" id="estado-${meta.row}" data-index="${meta.row}"
                                hidden
                            value="${row.estado}" autocomplete="off" />
                            <label for="buton" id="dia-${meta.row}">${moment(row.Fecha).format('dddd')}</label>
                        `;
        },
        width: 200
    },
    {
        data: "Fecha",
        name: "Fecha",
        render: function (data, type, row, meta) {
            return `
                            <input type="text" name="fecha[]" id="fecha-${meta.row}" data-index="${meta.row}" disabled
                                class="form-control form-control-sm datepicker w-100 fecha_entrega" placeholder="Date"
                                value="${moment(row.Fecha).format('MM/DD/YYYY')}" autocomplete="off" />
                        `;
        },
        width: 200
    },
    {
        data: "Nick_Name",
        name: "Nick_Name",
        render: function (data, type, row, meta) {
            return `
                                <select name="empleado_id[]" id="personal-${meta.row}" class="form-control form-control-sm select_personal w-100"
                                data-index="${meta.row}" disabled >
                                    <option value="${row.Empleado_ID}">${row.Nick_Name}</option>
                                </select> 
                            `;
        },
        width: 200
    },
    {
        data: 'Pro_ID',
        name: 'Pro_ID',
        render: function (data, type, row, meta) {
            return `
                            <select name="proyecto_id[]" class="form-control form-control-sm select_job w-100" id="trabajo-${meta.row}" 
                            data-index="${meta.row}" disabled >
                                <option value="${row.Pro_ID}">${row.nombre_proyecto}</option>
                            </select>
                        `;
        },
        width: 200
    },
    {
        data: 'Hora_Ingreso',
        name: 'Hora_Ingreso',
        render: function (data, type, row, meta) {
            return `
                        <input type="time" step="1" name="check_in[]" data-index="${meta.row}" disabled
                            class="form-control form-control-sm check_in" placeholder="Check in"
                            value="${row.Hora_Ingreso}" autocomplete="off" />
                                
                        `;
        },
        width: 200
    },
    {
        data: 'Hora_Salida',
        name: 'Hora_Salida',
        render: function (data, type, row, meta) {
            return `
                        <input type="time" step="1" name="check_out[]" data-index="${meta.row}" disabled
                            class="form-control form-control-sm check_out" placeholder="Check out"
                            value="${row.Hora_Salida}" autocomplete="off" />
                        `;
        },
        width: 200
    },
    {
        data: 'Edificio_ID',
        name: 'Edificio_ID',
        render: function (data, type, row, meta) {
            return `
                            <select name="edificio_id[]" class="form-control form-control-sm select_edificio w-100" data-proyecto='${row.Pro_ID}' id="edificio-${meta.row}"  data-index="${meta.row}"  disabled>
                                <option value="${row.Edificio_ID}">${row.nombre_edificio}</option>
                            </select>
                        `;
        },
        width: 200
    },
    {
        data: 'Floor_ID',
        name: 'Floor_ID',
        render: function (data, type, row, meta) {
            return `
                            <select name="piso_id[]" class="form-control form-control-sm select_piso w-100" data-edificio='${row.Edificio_ID}' id="piso-${meta.row}"  data-index="${meta.row}" disabled>
                                <option value="${row.Floor_ID}">${row.nombre_floor}</option>
                            </select>
                        `;
        },
        width: 200
    },
    {
        data: 'Area_ID',
        name: 'Area_ID',
        render: function (data, type, row, meta) {
            return `
                            <select name="area_id[]" class="form-control form-control-sm select_area w-100" data-piso='${row.Floor_ID}' id="area-${meta.row}"  data-index="${meta.row}" disabled>
                                <option value="${row.Area_ID}">${row.nombre_area}</option>
                            </select>
                        `;
        },
        width: 200
    },
    {
        data: 'Task_ID',
        name: 'Task_ID',
        render: function (data, type, row, meta) {
            return `
                            <select name="tarea_id[]" class="form-control form-control-sm select_tarea w-100" data-area='${row.Area_ID}' id="tarea-${meta.row}" data-index="${meta.row}" disabled>
                                <option value="${row.Task_ID}">${row.nombre_tarea}</option>
                            </select>
                        `;
        },
        width: 200
    },
    {
        data: 'Horas_Contract',
        name: 'Horas_Contract',
        render: function (data, type, row, meta) {
            return `
                            <input type="text" name="horas_trabajadas[]" data-index="${meta.row}" disabled
                                class="form-control form-control-sm horas_trabajadas" placeholder="Worked"
                                value="${row.Horas_Contract}" autocomplete="off" />
                        `;
        },
        width: 200
    },
    {
        data: 'Detalles',
        name: 'Detalles',
        render: function (data, type, row, meta) {
            return `
                            <input type="text" name="notas[]" data-index="${meta.row}" disabled
                                class="form-control form-control-sm detalle" placeholder="Notes"
                                value="${row.Detalles}" autocomplete="off" />
                        `;
        },
        width: 200
    },
    {
        data: 'Verificado_Foreman',
        name: 'Verificado_Foreman',
        render: function (data, type, row, meta) {
            return `
                            <input name="check_foreman[]" class='Verificado_Foreman' type="checkbox" value="${row.Verificado_Foreman == 1 ? 1 : 0}" ${row.Verificado_Foreman == 1 ? 'checked' : ''}  data-index="${meta.row}" disabled>
                        `;
        },
        width: 200
    },
    {
        data: "Hora",
        name: "Hora",
        orderable: false,
        Searchable: false,
        render: function (data, type, row, meta) {
            var edit = ``;
            if (isAdmin == 1) {
                edit = ` <i class='fas fa-pencil-alt ms-text-success edit cursor-pointer evento' title='Edit' data-evento="no" data-index="${meta.row}" ></i>`;
            } else {
                const hoy = moment();
                const ayer = hoy.subtract(1, 'day');
                //console.log(ayer.format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'), row.Fecha)
                if (ayer.format('YYYY-MM-DD') == row.Fecha || moment().format('YYYY-MM-DD') == row.Fecha) {
                    edit = ` <i class='fas fa-pencil-alt ms-text-success edit cursor-pointer evento' title='Edit' data-evento="no" data-index="${meta.row}" ></i>`;
                } else {
                    console.log('no valido')
                }
            }
            return `
                ${edit}
                <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' title='Delete' data-index="${meta.row}" ></i>
                `;
        }
    },
    ],
}).on("draw", function (e, dt, type, indexes) {
    if (isAdmin == 1) {
        $(`.datepicker`).datepicker({
            todayHighlight: true,
            dateFormat: "mm/dd/yy"
        });
    } else {
        $(`.datepicker`).datepicker({
            todayHighlight: true,
            dateFormat: "mm/dd/yy",
            minDate: -1,
            maxDate: "+0D"
        });
    }
}).on('click', 'tbody tr', (e) => {

});

function get_select_job(index) {
    $(`#trabajo-${index}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/goal/get_proyects`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const index = $(this).data('index');
        get_select_build(index, e.params.data['id']);
        habilitarDeshabilitar(index, false)
        resetEdificio(index);
        detectRow($(this));

        //AUTO SELECION
        $.ajax({
            type: 'GET',
            url: `${base_url}/register-activities/verficar-proyecto/${e.params.data['id']}`,
            dataType: 'json',
            success: function (response) {
                console.log(response.data)
                if (response.data) {
                    var newEdificio = new Option(response.data.nombre_edificio, response.data.Edificio_ID, true, true);
                    $(`#edificio-${index}`).append(newEdificio).trigger('change');
                    get_select_floor(index, response.data.Edificio_ID);

                    var newFloor = new Option(response.data.nombre_floor, response.data.Floor_ID, true, true);
                    $(`#piso-${index}`).append(newFloor).trigger('change');
                    get_select_area(index, response.data.Floor_ID);
                    resetArea(index)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                error_status(jqXHR)
            },
            fail: function () {
                fail()
            }
        })
    });
}

function get_select_empleado(index) {
    $(`#personal-${index}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/register-activities/empleados`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        console.log('select', e.params.data['id'])
    })
}

function get_select_build(index, proyecto_id) {
    $(`#edificio-${index}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/register-activities/edificio/${proyecto_id}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const index = $(this).data('index');
        get_select_floor(index, e.params.data['id']);
        habilitarDeshabilitar(index, false)
        resetArea(index)
    })
}

function get_select_floor(index, edificio_id) {
    $(`#piso-${index}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/register-activities/piso/${edificio_id}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const index = $(this).data('index');
        get_select_area(index, e.params.data['id']);
        habilitarDeshabilitar(index, false)
        resetTarea(index)
    }).on('select2:open', function (e) {

    });
}

function get_select_area(index, piso_id) {
    $(`#area-${index}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/register-activities/area/${piso_id}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const index = $(this).data('index');
        get_select_task(index, e.params.data['id']);
        habilitarDeshabilitar(index, false)
        resetTarea(index)
    }).on('select2:open', function (e) {
        console.log($('.select_job').data('edificio'))
    });
}

function get_select_task(index, area_id) {
    $(`#tarea-${index}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/register-activities/tarea/${area_id}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        console.log(e.params.data['id'])
    }).on('select2:open', function (e) {
        console.log($('.select_job').data('edificio'))
    });
}
$(document).on("click", "#crear_registro", function () {
    //duplicacion
    const tablaActual = dataTable.rows().data();
    const count = tablaActual.length;
    var nuevo = tablaActual[count - 1];

    if (count > 0) {
        nuevo.Actividad_ID = 0;
        nuevo.RDA_ID = 0;
        nuevo.Reg_ID = 0;
        nuevo.estado = 'nuevo';
        nuevo.Horas_Contract = 0;
    }
    else {
        nuevo = {
            Actividad_ID: 0,
            RDA_ID: 0,
            Reg_ID: 0,
            estado: 'nuevo',
            Area_ID: 0,
            Detalles: "",
            Edificio_ID: 0,
            Empleado_ID: $('#tipo_usuario').val() == 0 ? $('#empleado_id').val() : 0,
            Fecha: moment().format('MM/DD/YYYY'),
            Floor_ID: 0,
            Hora: "00:00:00",
            Hora_Ingreso: "00:00:00",
            Hora_Salida: "00:00:00",
            Horas_Contract: 0,
            Horas_TM: 0,
            Nick_Name: $('#tipo_usuario').val() == 0 ? $('#nickname').val() : "",
            Pro_ID: 0,
            Task_ID: 0,
            Verificado_Foreman: 0,
            nombre_area: "",
            nombre_edificio: "",
            nombre_floor: "",
            nombre_proyecto: "",
            nombre_tarea: "",
        };
    }

    var aux = dataTable.row.add(nuevo).draw();
    const data = dataTable.rows().data();

    const elemento_td = dataTable.row(data.length - 1).node();
    detecNewtRow($(elemento_td));

    data.map((valo, index) => {
        get_select_empleado(index);
        get_select_job(index);
        get_select_build(index, data[index].Pro_ID);
        get_select_floor(index, data[index].Edificio_ID);
        get_select_area(index, data[index].Floor_ID);
        get_select_task(index, data[index].Area_ID);
    });
    //
    if ($('#tipo_usuario').val() == 0) {
        $('.select_personal').prop('disabled', true);
    }
});

$('#lista_actividades tbody').on('click', '.delete', function () {
    const primerElemento = $(this).parent().parent().children()[0];
    console.log($(this).parent().parent().children())
    const registro_diario_id = $(primerElemento).find('.registro_diario_actividad_id').val();
    if (registro_diario_id == 0) {
        dataTable
            .row($(this).parents('tr'))
            .remove()
            .draw();
    } else {
        //eliminar desde server
        console.log('eliminar desde server');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: `${base_url}/register-activities/delete-registro-diario-actividad/${registro_diario_id}`,
                    dataType: 'json',
                    async: true,
                    success: function (response) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        );
                        editar()
                    }
                });
            }
        });
    }
});

function resetEdificio(index) {
    $(`#edificio-${index}`).empty().trigger('change')
    $(`#piso-${index}`).empty().trigger('change')
    $(`#area-${index}`).empty().trigger('change')
    $(`#tarea-${index}`).empty().trigger('change')
}

function resetPiso(index) {
    $(`#piso-${index}`).empty().trigger('change')
    $(`#area-${index}`).empty().trigger('change')
    $(`#tarea-${index}`).empty().trigger('change')
}

function resetArea(index) {
    $(`#area-${index}`).empty().trigger('change')
    $(`#tarea-${index}`).empty().trigger('change')
}

function resetTarea(index) {
    $(`#tarea-${index}`).empty().trigger('change')
}

function habilitarDeshabilitar(index, estado) {
    $(`#edificio-${index}`).prop("disabled", estado);
    $(`#piso-${index}`).prop("disabled", estado);
    $(`#area-${index}`).prop("disabled", estado);
    $(`#tarea-${index}`).prop("disabled", estado);
}

$(document).on("click", "#registrar", function () {
    var formulario = [];
    const data = dataTable.rows().data();
    data.map((e, i) => {
        var save = 'no';
        const tr = dataTable.row(i).node();
        const nuevo = {
            Actividad_ID: 0,
            RDA_ID: 0,
            Reg_ID: 0,
            estado: 'nuevo',
            Area_ID: 0,
            Detalles: "",
            Edificio_ID: 0,
            Empleado_ID: 0,
            Fecha: moment().format('MM/DD/YYYY'),
            Floor_ID: 0,
            Hora: "00:00:00",
            Hora_Ingreso: "00:00:00",
            Hora_Salida: "00:00:00",
            Horas_Contract: 0,
            Horas_TM: 0,
            Nick_Name: 0,
            Pro_ID: 0,
            Tas_IDT: '',
            Task_ID: 0,
            Verificado_Foreman: 0,
            nombre_area: "",
            nombre_edificio: "",
            nombre_floor: "",
            nombre_proyecto: "",
            nombre_tarea: ""
        };
        $(tr).children().map((index, ele) => {
            switch (index) {
                case 0:
                    nuevo.Actividad_ID = $(ele).find('.actividad_id').val();
                    nuevo.estado = $(ele).find('.estado').val();
                    nuevo.Reg_ID = $(ele).find('.registro_diario_id').val();
                    nuevo.RDA_ID = $(ele).find('.registro_diario_actividad_id').val();
                    break;
                case 1:
                    nuevo.Fecha = $(ele).find('.fecha_entrega').val();
                    break;
                case 2:
                    nuevo.Empleado_ID = $(ele).find('.select_personal').val();
                    break;
                case 3:
                    nuevo.Pro_ID = $(ele).find('.select_job').val();
                    break;
                case 4:
                    nuevo.Hora_Ingreso = $(ele).find('.check_in').val();
                    break;
                case 5:
                    nuevo.Hora_Salida = $(ele).find('.check_out').val();
                    break;
                case 6:
                    nuevo.Edificio_ID = $(ele).find('.select_edificio').val();
                    break;
                case 7:
                    nuevo.Floor_ID = $(ele).find('.select_piso').val();
                    break;
                case 8:
                    nuevo.Area_ID = $(ele).find('.select_area').val();
                    break;
                case 9:
                    nuevo.Task_ID = $(ele).find('.select_tarea').val();
                    break;
                case 10:
                    nuevo.Hora = $(ele).find('.horas_trabajadas').val();
                    break;
                case 11:
                    nuevo.Detalles = $(ele).find('.detalle').val();
                    break;
                case 12:
                    nuevo.Verificado_Foreman = $(ele).find('.Verificado_Foreman').val();
                    break;
                case 13:
                    save = $(ele).find('.evento').data('evento');
                    break;
                default:
                    break;
            }
        });
        if (save == 'save' || nuevo.estado == 'nuevo') {
            formulario.push(nuevo)
        }
    })
    $.ajax({
        type: "post",
        url: `${base_url}/register-activities/store`,
        dataType: 'json',
        data: {
            actividades: formulario
        },
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                editar();
            } else {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
});


function editar() {
    dataTable
        .clear()
        .draw();
    $.ajax({
        type: 'POST',
        url: `${base_url}/register-activities/show`,
        dataType: 'json',
        data: {
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            nick_name: $('#nick_name').val(),
            job: $('#job').val(),
            no_cost_code: $('#no_cost_code').prop('checked') ? 2 : 1,
            horas_trabajo: $('#horas_trabajo').val(),
            cost_code: $('#cost_code').val()
        },
        success: function (response) {
            if (response.status == 'ok') {
                restauracion(response.data)
                //restauracion(response.data)
            } else {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
}
$(document).ready(function () {
    editar()
});

function restauracion(data) {
    dataTable.rows.add(data).draw();
    //restauracion
    data.map((valo, index) => {
        get_select_empleado(index);
        get_select_job(index);
        get_select_build(index, data[index].Pro_ID);
        get_select_floor(index, data[index].Edificio_ID);
        get_select_area(index, data[index].Floor_ID);
        get_select_task(index, data[index].Area_ID);
    })
}

$(document).on("change", ".datepicker", function () {
    const fecha = $(this).val();
    $(this).parent().prev().find('label').text(moment(fecha).format('dddd'))
});


//search 
$(document).on("click", "#buscar", function () {
    editar()
});

//evento de despliege 
function detectRow(elemento, disabled) {
    const rows = elemento.parent().parent().children()
    rows.map(((i, ele) => {
        $(ele).find('input').prop('disabled', disabled);
        $(ele).find('select').prop('disabled', disabled);
    }))
}

function detecNewtRow(elemento) {
    const rows = elemento.children()
    rows.map(((i, ele) => {
        $(ele).find('input').prop('disabled', false)
        $(ele).find('select').prop('disabled', false)
        if (i == 13) {
            $(ele).find('.evento').remove()
        }

    }))
}
$(document).on("click", ".Verificado_Foreman", function () {
    if ($(this).val() == '0') {
        $(this).val('1');
    } else {
        $(this).val('0');
    }
});

$(document).on("click", ".edit", function () {
    if ($(this).data('evento') == 'save') {
        $(this).data('evento', 'no');
        detectRow($(this), true);
    } else {
        $(this).data('evento', 'save');
        detectRow($(this), false);
    }
    ///////
    if ($('#tipo_usuario').val() == 0) {
        $('.select_personal').prop('disabled', true);
    }
});