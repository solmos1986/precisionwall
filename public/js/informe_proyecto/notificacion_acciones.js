let personas = [{
    Empleado_ID: 0,
    Nombre: '',
    notificacion_estado: 0,
    fecha_registro: null,
    verificar_estado: 0
}];

$('#view_acciones').click(function () {
    initialState();
});
function initialState() {
    $('#historial_notificacion_action').html('');
    $.ajax({
        type: "GET",
        url: `${base_url}/action-week/edit/${$('#proyecto_id').val()}`,
        dataType: "json",
        success: function (response) {
            var tarjeta = ``;
            response.data.forEach((action, i) => {
                //personas
                const personaHTML = editPersonalHTML(action.notificacion_personas);
                const estadosHTML = mostrarEstadosHTML(action.notificacion_estado, action.notificacion.notificacion_estado_id)

                tarjeta += `
                <li id="notificacion_acciones${i}">
                    <div class="ms-btn-icon btn-pill icon btn-success " >
                        <i class="flaticon-pencil edit_action_notificacion" data-id="notificacion_acciones${i}" title="Edit record"></i>
                    </div>
                        <span class="my-2 d-block" style="font-size: 13px;"> <i class="material-icons" >date_range</i> Add the
                            ${moment(action.fecha_proyecto_movimiento).format('MMMM dddd DD, YYYY HH:mm:ss ')}
                        </span>
                    <div class="row pt-2" style="background:${action.notificacion.color_estado}">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between mb-1">
                                <p class="d-block mb-0 mt-2"><strong>Action for Week:</strong></p>
                                <select class="form-control form-control-sm estado_notificacion" data-id="${action.notificacion.notificacion_acciones_id}" style="width:30%">
                                    ${estadosHTML}
                                </select>
                            </div>
                            <textarea type="text" class="form-control form-control-sm mensaje" id="report_weekly" name="report_weekly" rows="4" placeholder="Report Weekly"> ${action.action_for_week == null ? '' : action.action_for_week}</textarea>
                            <input class="proyecto_detail_id" value="${action.id}" hidden>
                            <input class="notificacion_acciones_id" value="${action.notificacion.notificacion_acciones_id}" hidden>
                        </div>
                        <div class="col-md-12">
                            </br>
                            <div class="d-flex justify-content-between mb-1">
                                <p class="d-block mb-0 mt-2"><strong>Send message to:</strong></p>
                                <button class="btn btn-sm btn-primary mt-0 add_empleado_notificacion" type="button">Add employee</button>
                            </div>
                            <table class="table table-hover thead-light table_${i}">
                                <thead>
                                    <tr>
                                        <th width="100">Employee</th>
                                        <th width="20">Completed</th>
                                        <th width="20">Registration date</th>
                                        <th width="20">Verified</th>
                                        <th width="20">*</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${personaHTML}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </li>
                `;
            });

            $('#historial_notificacion_action').append(tarjeta);
            disabled_elemento(true)
            response.data.forEach((action, i) => {
                disabled_elemento(`notificacion_acciones${i}`, true)
            });

            select2_empleado()
            $('#modalViewAcciones').modal({
                show: true
            });
        }
    });
}
function mostrarEstadosHTML(notificacion_estado, notificacion_estado_id) {
    let estadoHTML = ``;
    notificacion_estado.forEach((estado, i) => {
        estadoHTML += `
            <option value="${estado.notificacion_acciones_estado_id}" ${notificacion_estado_id == estado.notificacion_acciones_estado_id ? 'selected' : ''}>
                ${estado.notificacion_estado_nombre}
            </option >
    `;
    });
    return estadoHTML;
}
function editPersonalHTML(personas) {
    let etiqueta = ``;
    personas.forEach((persona, index) => {
        etiqueta += `
        <tr>
            <td>
                <select class="form-control form-control-sm select2_personal"
                    name="personal[]" style="width:100%">
                    <option value="${persona.Empleado_ID}">${persona.Nombre}</option>
                </select>
                <input class="estado" type="checkbox" value="edit" hidden >
                <input class="notificacion_acciones_persona_id" type="checkbox" value="${persona.notificacion_acciones_persona_id}" hidden >
            </td>
            <td>
                <label class="ms-checkbox-wrap ms-checkbox-primary">
                    <input class="completado" type="checkbox" value="" ${(persona.notificacion_estado == 1) ? "checked" : ""} >
                        <i class="ms-checkbox-check"></i>
                </label>
            </td>
            <td>
                ${(persona.fecha_registro == null) ? "Not registered" : moment(persona.fecha_registro).format('MM/DD/YYYY HH:mm:ss')}
            </td>
            <td>
                <label class="ms-checkbox-wrap ms-checkbox-primary">
                    <input class="check" type="checkbox" value="" ${(persona.verificar_estado == 1) ? "checked" : ""}>
                        <i class="ms-checkbox-check"></i>
                </label>
            </td>
            <td>
                <button class="ms-btn-icon btn-sm btn-danger delete" data-id="${persona.notificacion_acciones_persona_id}">
                    <i class="fas fa-trash-alt" style="margin-right: 2px" ></i>
                </button>
            </td>
        </tr>
        `;
    });
    return etiqueta;
}

function addPersonalHTML(personas) {
    let etiqueta = ``;
    personas.forEach((persona, index) => {
        etiqueta += `
            <tr>
                <td>
                    <select class="form-control form-control-sm select2_personal"
                        name="personal[]" style="width:100%">
                    </select>
                    <input class="estado" type="checkbox" value="nuevo" hidden >
                    <input class="notificacion_acciones_persona_id" type="checkbox" value="0" hidden >
                </td>
                <td>
                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                        <input class="completado" type="checkbox" value="" ${(persona.notificacion_estado == 1) ? "checked" : ""} >
                        <i class="ms-checkbox-check"></i>
                    </label>
                </td>
                <td>
                    ${(persona.fecha_registro == null) ? "Not registered" : moment(persona.fecha_registro).format('MM/DD/YYYY HH:mm:ss')}
                </td>
                <td>
                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                    <input class="check" type="checkbox" value="" ${(persona.verificar_estado == 1) ? "checked" : ""}>
                        <i class="ms-checkbox-check"></i>
                    </label>
                </td>
                <td>
                    <button class="ms-btn-icon btn-sm btn-danger delete_temporal" data-id="">
                        <i class="fas fa-trash-alt" style="margin-right: 2px" ></i>
                    </button>
                </td>
            </tr>
            `;
    });
    return etiqueta;
}
function select2_empleado() {
    personas.forEach((persona, index) => {
        $(`.select2_personal`).select2({
            theme: "bootstrap4",
            dropdownParent: $('#modalViewAcciones'),
            ajax: {
                url: `${base_url}/action-week/empleados`,
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
    });
}

$(document).on("click", ".add_empleado_notificacion", function () {
    const personaHTML = addPersonalHTML(personas);
    $(this).parent().next().find('tbody').append(personaHTML)
    select2_empleado()
});

$(document).on("click", ".edit_action_notificacion", function () {
    $(this).removeClass('flaticon-pencil edit_action_notificacion');
    $(this).parent().removeClass('btn-success');
    $(this).addClass('far fa-check-circle save_action_notificacion');
    $(this).parent().addClass('btn-primary');
    disabled_elemento($(this).data('id'), false);
});

$(document).on("click", ".save_action_notificacion", function () {
    $(this).removeClass('far fa-check-circle save_action_notificacion');
    $(this).parent().removeClass('btn-primary');
    $(this).addClass('flaticon-pencil edit_action_notificacion');
    $(this).parent().addClass('btn-success');
    disabled_elemento($(this).data('id'), true);
    const data = valores($(this).data('id'));
    $.ajax({
        type: "PUT",
        url: `${base_url}/action-week/update`,
        dataType: "json",
        data: data,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                initialState();
            }
        }
    });
});

function disabled_elemento(elementoPadre, estado) {
    $(`#${elementoPadre} .add_empleado_notificacion`).prop('disabled', estado);
    $(`#${elementoPadre} .select2_personal`).prop('disabled', estado);
    $(`#${elementoPadre} .mensaje`).prop('disabled', estado);
    $(`#${elementoPadre} .completado`).prop('disabled', estado);
    $(`#${elementoPadre} .check`).prop('disabled', estado);
    $(`#${elementoPadre} .delete`).prop('disabled', estado)
}

function valores(elementoPadre) {
    var personas = [];
    $(`#${elementoPadre} .estado`).each((function (index) {
        console.log(index + ": " + $(this).val());
        //prepare
        personas.push({
            estado: $(this).val(),
            Empleado_ID: 0,
            notificacion_estado: 0,
            verificar_estado: 0,
        })
    }))

    $(`#${elementoPadre} .select2_personal`).each((function (index) {
        console.log(index + ": " + $(this).val());
        personas[index].Empleado_ID = $(this).val();
    }))

    $(`#${elementoPadre} .completado`).each((function (index) {
        console.log(index + ": " + $(this).is(':checked'));
        personas[index].notificacion_estado = $(this).is(':checked') == true ? 1 : 0;
    }))

    $(`#${elementoPadre} .check`).each((function (index) {
        console.log(index + ": " + $(this).is(':checked'));
        personas[index].verificar_estado = $(this).is(':checked') == true ? 1 : 0;
    }))

    $(`#${elementoPadre} .proyecto_detail_id`).each((function (index) {
        console.log(index + ": " + $(this).val());
    }))

    $(`#${elementoPadre} .notificacion_acciones_persona_id`).each((function (index) {
        console.log(index + ": " + $(this).val());
        personas[index].notificacion_acciones_persona_id = $(this).val();
    }))

    var rastreo = {
        mensaje: $(`#${elementoPadre} .mensaje`).val(),
        proyecto_detail_id: $(`#${elementoPadre} .proyecto_detail_id`).val(),
        notificacion_acciones_id: $(`#${elementoPadre} .notificacion_acciones_id`).val(),
        personas
    }
    console.log(rastreo)
    return rastreo;
}

$(document).on("click", ".delete_temporal", function () {
    $(this).parent().parent().remove();
});


$(document).on("click", ".delete", function () {
    const notificacion_acciones_persona_id = $(this).data('id');
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
                url: `${base_url}/action-week/delete/${notificacion_acciones_persona_id}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                    initialState();
                }
            });
        }
    });
});

$(document).on('change', '.estado_notificacion', function () {
    const estado_id = $(this).val();
    const notificacion_acciones_id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: `${base_url}/action-week/notificacion-accion/${notificacion_acciones_id}`,
        dataType: 'json',
        data: {
            estado_id
        },
        success: function (response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            initialState();
        }
    });
});