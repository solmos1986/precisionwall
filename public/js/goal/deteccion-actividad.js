
function enviar_actividades(Pro_ID) {
    var rows = $('#table-actividad tbody').children();
    var formFormulario = [];
    rows.map((i, ele) => {
        const nuevoReg = {
            Actividad_ID: 0,
            RDA_ID: 0,
            Reg_ID: 0,
            estado: 'nuevo',
            Area_ID: 0,
            Detalles: "",
            Edificio_ID: 0,
            Empleado_ID: Empleado_ID,
            Fecha: moment().format('MM/DD/YYYY'),
            Floor_ID: 0,
            Hora: "00:00:00",
            Hora_Ingreso: "00:00:00",
            Hora_Salida: "00:00:00",
            Horas_Contract: 0,
            Horas_TM: 0,
            Nick_Name: 0,
            Pro_ID: Pro_ID,
            Tas_IDT: '',
            Task_ID: 0,
            Verificado_Foreman: 0,
            nombre_area: "",
            nombre_edificio: "",
            nombre_floor: "",
            nombre_proyecto: "",
            nombre_tarea: ""
        };
        const td = $(ele).find('td').children();
        td.map((index, elemento) => {
            switch (index) {
                case 0:
                    console.log('emp id', $(elemento).val(), elemento)
                    nuevoReg.Empleado_ID = $(elemento).val()
                    break;
                case 2:
                    console.log('estado', $(elemento).val(), elemento)
                    nuevoReg.estado = $(elemento).val()
                    break;
                case 3:
                    nuevoReg.Task_ID = $(elemento).val()
                    break;
                case 5:
                    nuevoReg.Hora = $(elemento).val()
                    break;
                case 7:
                    nuevoReg.RDA_ID = $(elemento).val()
                    break;
                case 8:
                    nuevoReg.Reg_ID = $(elemento).val()
                    break;
                default:
                    break;
            }
        });
        formFormulario.push(nuevoReg)
    });
    console.log(formFormulario)
    return formFormulario;
}

function registrar(formulario) {
    $.ajax({
        type: "post",
        url: `${base_url}/register-activities/store-visit-report`,
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
function VerificarTarea(pro_id) {
    $.ajax({
        type: "get",
        url: `${base_url}/goal/task-verificar/${pro_id}`,
        dataType: 'json',
        success: function (response) {
            if (response.status == 'ok') {
                //add 
                if (response.data.actividades.length > 0) {
                    tarea_existente(response.data.actividades, pro_id);
                    const unDiaMas = moment(fechaCreacion).add(1, 'days').format('YYYY-MM-DD');
                    const diaActual = moment().format('YYYY-MM-DD');
                    console.log('fecha registro', fechaCreacion, unDiaMas, moment().format('YYYY-MM-DD'))
                    if (fechaCreacion == diaActual || unDiaMas == diaActual) {
                        console.log('dentro de la fecha');
                    } else {
                        console.log('fuera de la fecha');
                        if (isAdmin == 0) {
                            $('.add-actividad-edit').prop('disabled', true);
                            $('.cost_code').prop('disabled', true);
                            $('.hours_worked').prop('disabled', true);
                            $('.remove_tarea').prop('disabled', true);
                            $('.task').prop('disabled', true);
                        }
                    }

                } else {
                    if (estado == 'nuevo') {
                        tarea_preparada(response.data.tarea)
                    }
                }
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

$(".add-actividad").on('click', function () {
    $("#none_tr_mat").remove();
    tarea_nueva($('#proyect').val())
});

$(".add-actividad-edit").on('click', function () {
    $("#none_tr_mat").remove();
    tarea_nueva($('#edit_Pro_ID').val())
});

$(document).on("click", ".remove_tarea", function () {
    const RDA_ID = $(this).data('id');
    const elementoActual = $(this);
    if (elementoActual.data('delete') == 'temporal') {
        elementoActual.parents("tr").remove();
    }
    else {
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
                    url: `${base_url}/register-activities/delete-registro-diario-actividad/${RDA_ID}`,
                    dataType: 'json',
                    async: true,
                    success: function (response) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        );
                        elementoActual.parents("tr").remove();
                    }
                });
            }
        });
    }
    if ($('#table-class tbody tr').length == 0) {
        $("#table-class tbody").append(`<tr id="none_tr_class">
            <td colspan="11" class="text-center">I don't add anything</td>
        </tr>`);
    }
});

//add tarea automatico
function tarea_preparada(tarea) {
    const td_material_nuevo = `
    <tr>
            <td data-label="Material Description:">
                <input type="text" disabled name="personal_id[]"
                    value="${Empleado_ID}"
                    class="form-control form-control-sm" hidden>
                <input type="text" disabled name="cost_code[]"
                    value="${tarea.ActTas}"
                    class="form-control form-control-sm cost_code">
                <input type="text" name="estado[]"
                    value="nuevo"
                    class="form-control form-control-sm" hidden>
            </td>
            <td data-label="Unit of Measurement:">
                <select class="form-control form-control-sm w-100 task"
                    style="width: 100%" name="task_id[]" style="width:100%">
                    <option value="${tarea.Task_ID}">${tarea.Nombre}</option>
                </select>
            </td>
            <td data-label="QTY:"><input type="number" name="hours_worked[]"
                    value="0"
                    class="form-control form-control-sm hours_worked"></td>
            <td data-label="*">
                <button class="ms-btn-icon btn-danger btn-sm remove_tarea" type="button" data-delete="temporal"><i
                        class="fas fa-trash-alt mr-0"></i></button>
                <input type="text" name="registro_diario_actividad[]"
                    value="0"
                    class="form-control form-control-sm" hidden>
                <input type="text" name="registro_diario[]"
                    value="0"
                    class="form-control form-control-sm" hidden>
            </td>
        </tr>
    `;

    $("#none_tr_mat").remove();
    $("#table-actividad tbody").append(td_material_nuevo);
    get_new_select_task($('#proyect').val())
}

function tarea_existente(actividades, Pro_ID) {
    console.log('entrada', actividades, Pro_ID)
    var td_material_nuevo = ``;
    actividades.map((actividad, i) => {
        td_material_nuevo += `
        <tr>
            <td data-label="Material Description:">
                <input type="text" disabled name="personal_id[]"
                    value="${actividad.Empleado_ID}"
                    class="form-control form-control-sm " hidden>
                <input type="text" disabled name="cost_code[]"
                    value="${actividad.Tas_IDT}"
                    class="form-control form-control-sm cost_code">
                <input type="text" name="estado[]"
                    value="update"
                    class="form-control form-control-sm" hidden>
            </td>
            <td data-label="Unit of Measurement:">
                <select class="form-control form-control-sm w-100 task"
                    style="width: 100%" name="task_id[]" style="width:100%">
                    <option value="${actividad.Task_ID}">
                        ${actividad.nombre_tarea}</option>
                </select>
            </td>
            <td data-label="QTY:"><input type="number" name="hours_worked[]"
                    value="${actividad.Horas_Contract}"
                    class="form-control form-control-sm hours_worked"></td>
            <td data-label="*">
                <button class="ms-btn-icon btn-danger btn-sm remove_tarea" type="button" data-id="${actividad.RDA_ID}"><i
                        class="fas fa-trash-alt mr-0"></i></button>
                <input type="text" name="registro_diario_actividad[]"
                    value="${actividad.RDA_ID}"
                    class="form-control form-control-sm" hidden>
                <input type="text" name="registro_diario[]"
                    value="${actividad.Reg_ID}"
                    class="form-control form-control-sm" hidden>
            </td>
        </tr>
        `;
    });

    $("#none_tr_mat").remove();
    $("#table-actividad tbody").append(td_material_nuevo);
    get_new_select_task(Pro_ID)
}

function tarea_nueva(Pro_ID) {
    var nuevo = `
        <tr>
            <td data-label="Material Description:">
                <input type="text" disabled name="personal_id[]"
                    value="${Empleado_ID}"
                    class="form-control form-control-sm" hidden>
                <input type="text" disabled name="cost_code[]"
                    value=""
                    class="form-control form-control-sm cost_code">
                <input type="text" name="estado[]"
                    value="nuevo" class="form-control form-control-sm" hidden>
            </td>
            <td data-label="Unit of Measurement:">
                <select class="form-control form-control-sm w-100 task"
                    style="width: 100%" name="task_id[]" style="width:100%">

                </select>
            </td>
            <td data-label="QTY:"><input type="number" name="hours_worked[]"
                    value="0"
                    class="form-control form-control-sm hours_worked"></td>
            <td data-label="*">
                <button class="ms-btn-icon btn-danger btn-sm remove_tarea" type="button" data-delete="temporal"><i
                        class="fas fa-trash-alt mr-0"></i></button>
                <input type="text" name="registro_diario_actividad[]"
                    value=""
                    class="form-control form-control-sm" hidden>
                <input type="text" name="registro_diario[]"
                    value=""
                    class="form-control form-control-sm" hidden>
            </td>
        </tr>
        `;
    $("#table-actividad tbody").append(nuevo);
    get_new_select_task(Pro_ID)
}

function get_new_select_task(pro_id) {
    $(".task").select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/goal/task/${pro_id}`,
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
    }).on("select2:select", function (e) {
        // setear cost code
        console.log(e.params.data)
        $('.cost_code').val(e.params.data["ActTas"].trim())
    });
}