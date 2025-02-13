
function table_proyectos(proyectos) {
    var table = `
    <table class="table thead-primary" >
        <thead>
            <tr>
                <th style="width: 1%;">#</th>
                <th>Codigo</th>
                <th>Project</th>
                <th>Hours Used</th>
                <th with="120" >Status</th>
                <th>Project Manager</th>
                <th>Foreman</th>
            </tr>
        </thead>
        <tbody>`;
    proyectos.forEach(proyecto => {
        table += `
        <tr>
            <td>
            <i class="far fa-eye-slash ms-text-primary cursor-pointer view_detail" title="view leven" data-pro_id="${proyecto.Pro_ID}" data-tipo="empresa"></i>
            </td>
            <td>${proyecto.Codigo}</td>
            <td>${proyecto.Nombre}</td>
            <td>
                ${proyecto.horas_usadas == null ? '' : proyecto.horas_usadas}
            </td>
            <td>${proyecto.Nombre_Estatus == null ? '' : proyecto.Nombre_Estatus}</td>
            <td>${proyecto.nombre_project_manager == null ? '' : proyecto.nombre_project_manager}</td>
            <td>${proyecto.nombre_foreman == null ? '' : proyecto.nombre_foreman}</td>
        </tr>
        ${vista_edificios(proyecto.edificios, proyecto.Pro_ID)}        
    `;
    });
    table += `
        </tbody>
    </table>
    <!--nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav-->`
        ;
    return table;
}
/*
*render de edificios
*/
function vista_edificios(list_edificios, proyecto_id, view = false) {
    var edificios = `
        <tr ${view == false ? `style="display:none"` : ''}>
            <td></td>
            <td colspan="6">
            <div class="row w-100 p-0">
                <div class="col-md-12  d-flex flex-row-reverse bd-highlight">
                    <button type="button" id="export_sov" class="btn btn-primary has-icon btn-sm d-inline m-0 nuevo_edificio" data-proyecto_id="${proyecto_id}">
                        Add Building
                    </button>
                </div>
            </div>
        </div>
            <fieldset class="border p-1 mb-0">
                <legend class="w-auto" style="font-size:0.9rem;"> List Level 0: building </legend>
                    <table class="table  no-footer w-100">
                        <thead>
                            <tr>
                                <th style="width: 1%;"></th>
                                <th>Name</th>
                                <th>Hours Used</th>
                                <th> Description</th>
                                <th style="width: 6%;"> Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
    list_edificios.forEach(edificio => {
        edificios += `
                            <tr >
                                <td>  <i class="far fa-eye-slash ms-text-primary cursor-pointer view_detail" title="view leven" data-tipo="empresa"></i></td>
                                <td>
                                    <input type="text" class="form-control w-10 form-control-sm nombre_edifico" data-valor="${edificio.Nombre == null ? '' : edificio.Nombre}" data-edificio_id="${edificio.Edificio_ID}" data-input="nombre_edifico" 
                                    id="precio_total" name="precio_total" placeholder="Name" value="${edificio.Nombre == null ? '' : edificio.Nombre}" readonly
                                    autocomplete="off" >
                                </td>
                                <td>
                                    ${edificio.horas_usadas == null ? '' : edificio.horas_usadas}
                                </td>
                                <td>
                                    <input type="text" class="form-control w-10 form-control-sm descripcion_edificio" data-valor="${edificio.Descripcion == null ? '' : edificio.Descripcion}" data-edificio_id="${edificio.Edificio_ID}" data-input="descripcion_edificio" 
                                    id="precio_total" name="precio_total" placeholder="Description" value="${edificio.Descripcion == null ? '' : edificio.Descripcion}" readonly
                                    autocomplete="off" >
                                </td>
                                <td>
                                    <i class="far fa-trash-alt ms-text-danger delete_edificio cursor-pointer" data-edificio_id="${edificio.Edificio_ID}" title="Delete"></i>
                                </td>
                            </tr>
                            ${vista_floor(edificio.floors, list_edificios, edificio.Edificio_ID)}
        `;
    });
    edificios += `
                        </tbody>
                    </table>
                </fieldset>
                <br>
            </td>
        </tr>
       `;
    return edificios;
}
/*
*render de floor
*/
function vista_floor(list_floors, list_edificios, edificio_id, view = false) {
    var floors = `
        <tr ${view == false ? `style="display:none"` : ''}>
            <td></td>
            <td colspan="6">
            <div class="row w-100 p-0">
                <div class="col-md-12  d-flex flex-row-reverse bd-highlight">
                    <button type="button" class="btn btn-primary has-icon btn-sm d-inline m-0 nuevo_floor" data-edificio_id="${edificio_id}">
                        Add Floor
                    </button>
                </div>
            </div>
            <fieldset class="border p-1  mb-0">
                <legend class="w-auto" style="font-size:0.9rem;"> List Level 1 floors</legend>                
                    <table class="table  no-footer w-100">
                        <thead>
                            <tr>
                                <th style="width: 1%;"></th>
                                <th>Name</th>
                                <th>Hours Used</th>
                                <th>List Level 0 building:</th>
                                <th style="width: 6%;"> Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
    list_floors.forEach(floor => {
        //edificios
        var option = ``;
        list_edificios.forEach(edificio => {
            option += `
            <option value="${edificio.Edificio_ID}" ${floor.Edificio_ID == edificio.Edificio_ID ? 'selected' : ''}>${edificio.Nombre}</option>
            `;
        });
        floors += `
                            <tr >
                                <td>  <i class="far fa-eye-slash ms-text-primary cursor-pointer view_detail" title="view leven" data-tipo="empresa"></i></td>
                                <td>
                                    <input type="text" class="form-control w-10 form-control-sm nombre_floor" data-valor="${floor.Nombre == null ? '' : floor.Nombre}" data-floor_id="${floor.Floor_ID}" data-input="nombre_floor" 
                                    id="precio_total" name="precio_total" placeholder="Name" value="${floor.Nombre == null ? '' : floor.Nombre}" readonly
                                    autocomplete="off" >
                                </td>
                                <td>
                                    ${floor.horas_usadas == null ? '' : floor.horas_usadas}
                                </td>
                                <td>
                                    <select class="form-control form-control-sm select_floor " name="edificio_id" data-floor_id="${floor.Floor_ID}">
                                        ${option}
                                        </select>
                                </td>
                                <td>
                                    <i class="far fa-trash-alt ms-text-danger delete_floor cursor-pointer" data-floor_id="${floor.Floor_ID}" title="Delete"></i>
                                </td>
                            </tr>
                            ${vista_areas(floor.area_control, list_floors, floor.Floor_ID)}  
        `;
    });
    floors += `
                        </tbody>
                    </table>
                </fieldset>
                <br>
            </td>
        </tr>
       `;
    return floors;
}
/*
*render de areas
*/
function vista_areas(list_areas, list_floor, floor_id, view = false, view_tarea = false) {
    var areas = `
        <tr ${view == false ? `style="display:none"` : ''}>
            <td></td>
            <td colspan="6">
            <div class="row w-100 p-0">
                <div class="col-md-12  d-flex flex-row-reverse bd-highlight">
                    <button type="button" id="export_sov" class="btn btn-primary has-icon btn-sm d-inline m-0 nueva_area" data-floor_id="${floor_id}">
                        Add Area
                    </button>
                </div>
            </div>
            <fieldset class="border p-1 mb-0">
                <legend class="w-auto" style="font-size:0.9rem;">List Level 2: Floors and/or common areas</legend>                
                    <table class="table  no-footer w-100">
                        <thead>
                            <tr>
                                <th style="width: 1%;"></th>
                                <th>Code Area</th>
                                <th>Name</th>
                                <th>Hours Used</th>
                                <th>List Level 1 Floor:</th>
                                <th style="width: 6%;"> Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
    list_areas.forEach(area => {
        //floors
        var option = ``;
        list_floor.forEach(floor => {
            option += `
            <option value="${floor.Floor_ID}" ${area.Floor_ID == floor.Floor_ID ? 'selected' : ''}>${floor.Nombre}</option>
            `;
        });
        areas += `
                            <tr>
                                <td><i class="far fa-eye-slash  ms-text-primary cursor-pointer view_detail view_area" title="view leven" data-id_area="${area.Area_ID}"  data-tipo_view="task"></i></td>
                                <td>
                                    <input type="text" class="form-control w-80 form-control-sm Are_IDT" data-valor="${area.Are_IDT == null ? '' : area.Are_IDT}" data-area_id="${area.Area_ID}" data-input="Are_IDT" 
                                    id="precio_total" name="cod_area" placeholder="Code Area" value="${area.Are_IDT == null ? '' : area.Are_IDT}" readonly
                                    autocomplete="off" >
                                </td>
                                <td>
                                    <input type="text" class="form-control w-80 form-control-sm nombre_area" data-valor="${area.Nombre == null ? '' : area.Nombre}" data-area_id="${area.Area_ID}" data-input="nombre_area" 
                                        id="precio_total" name="nombre_area" placeholder="Name" value="${area.Nombre == null ? '' : area.Nombre}" readonly
                                        autocomplete="off" >
                                </td>
                                <td>
                                    ${area.horas_usadas == null ? '' : area.horas_usadas}
                                </td>
                                <td>
                                    <select class="form-control form-control-sm select_area " name="floor_id" data-area_id="${area.Area_ID}">
                                    ${option}
                                    </select>
                                </td>
                                <td>
                                    <i class="far fa-trash-alt ms-text-danger delete_area cursor-pointer" data-area_id="${area.Area_ID} title="Delete"></i>
                                </td>
                            </tr>
                            ${vista_tareas(area.task, list_areas, area.Area_ID, view_tarea)}
        `;
    });
    areas += `
                        </tbody>
                    </table>
                </fieldset>
                <br>
            </td>
        </tr>
       `;
    return areas;
}
/*
*render de task
*/
function vista_tareas(list_task, list_areas, area_id, view = false) {
    var tareas = `
        <tr ${view == false ? `style="display:none"` : ''} data-view="true" id="${area_id}">
            <td></td>
            <td colspan="6">
                <div class="row w-100 p-0">
                    <div class="col-md-12  d-flex flex-row-reverse bd-highlight">
                        <button type="button" href="#nueva_tarea"  id="export_sov" class="btn btn-primary has-icon btn-sm d-inline m-0 nueva_tarea" data-area_id="${area_id}">
                            Add Task
                        </button>
                    </div>
                </div>
                <fieldset class="border p-1 mb-0">
                    <legend class="w-auto" style="font-size:0.9rem;">List Level 3: Areas or Tasks </legend>                
                        <table class="table wrapper no-footer w-100">
                            <thead>
                                <tr>
                                    <th style="padding-left: 10px; padding-right: 10px; text-wrap: nowrap;">Cost Code</th>
                                    <th style="padding-left: 10px; padding-right: 10px; text-wrap: nowrap;">Mask area</th>
                                    <th style="padding-left: 10px; padding-right: 10px; text-wrap: nowrap;">Ac Area</th>
                                    <th style="padding-left: 10px; padding-right: 10px; text-wrap: nowrap;">Ac task</th>
                                    <th style="padding-left: 50px; padding-right: 50px; text-wrap: nowrap;">Name</th>
                                    <th >Estimated Hours</th>
                                    <th >Estimated Material</th>
                                    <th style="text-wrap: nowrap;">Hours Used</th>
                                    <th style="text-wrap: nowrap;">CC Butget QTY</th>
                                    <th style="text-wrap: nowrap;">% Completed</th>
                                    <th style="text-wrap: nowrap;">Price Total</th>
                                    <th >To Bill Acording % Completed</th>
                                    <th style="padding-left: 20px; padding-right: 20px; text-wrap: nowrap;">List Level 2: Areas</th>
                                    <th style="text-wrap: nowrap;">Actions</th>
                                    <th style="text-wrap: nowrap;">Of coast</th>
                                    <th style="text-wrap: nowrap;">PWT prod rate</th>
                                    <th >Estimate labor const</th>
                                    <th >Material or equipment unit cost</th>
                                    <th >Material spread rate per unit</th>
                                    <th style="padding-right: 40px">Mat qty or gallon unit</th>
                                    <th style="text-wrap: nowrap;">Mat um</th>
                                    <th style="text-wrap: nowrap;">Material cost</th>
                                    <th >Subcontract cost</th>
                                    <th >Equipment cost</th>
                                    <th style="text-wrap: nowrap;">Others cost</th>
                                </tr>
                            </thead>
                            <tbody>`;
    list_task.forEach(task => {
        //*areas
        var option = ``;
        list_areas.forEach(areas => {
            option += `
            <option value="${areas.Area_ID}" ${areas.Area_ID == task.Area_ID ? 'selected' : ''}>${areas.Nombre}</option>
            `;
        });
        tareas += `
                                <tr >
                                    <td>
                                        <input type="text" class="form-control w-80 form-control-sm Tas_IDT" data-valor="${task.Tas_IDT == null ? '' : task.Tas_IDT}" data-task_id="${task.Task_ID}" data-input="Tas_IDT" 
                                        id="precio_total" name="precio_total" placeholder="Cost Code" value="${task.Tas_IDT == null ? '' : task.Tas_IDT}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                    ${task.NumAct == null ? '' : task.NumAct}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm ActAre" data-valor="${task.ActAre == null ? '' : task.ActAre}" data-task_id="${task.Task_ID}" data-input="ActAre" 
                                        id="precio_total" name="precio_total" placeholder="Ac Area" value="${task.ActAre == null ? '' : task.ActAre}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm ActTas" data-valor="${task.ActTas == null ? '' : task.ActTas}" data-task_id="${task.Task_ID}" data-input="ActTas" 
                                        id="precio_total" name="precio_total" placeholder="Ac task" value="${task.ActTas == null ? '' : task.ActTas}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        <textarea rows="2" type="text" class="form-control w-30 form-control-sm nombre_task" data-valor="${task.Nombre == null ? '' : task.Nombre}" data-task_id="${task.Task_ID}" data-input="nombre_task" 
                                        id="precio_total" name="precio_total" placeholder="Name"  readonly
                                        autocomplete="off">${task.Nombre == null ? '' : task.Nombre}</textarea>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm Horas_Estimadas" data-valor="${task.Horas_Estimadas == null ? '' : task.Horas_Estimadas}" data-task_id="${task.Task_ID}" data-input="Horas_Estimadas" 
                                        id="precio_total" name="precio_total" placeholder="Estimated Hours" value="${task.Horas_Estimadas == null ? '' : task.Horas_Estimadas}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm Material_Estimado" data-valor="${task.Material_Estimado == null ? '' : task.Material_Estimado}" data-task_id="${task.Task_ID}" data-input="Material_Estimado" 
                                        id="precio_total" name="precio_total" placeholder="Estimated Material" value="${task.Material_Estimado == null ? '' : task.Material_Estimado}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        ${task.horas_usadas == null ? '' : task.horas_usadas}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm cc_butdget_qty" data-valor="${task.cc_butdget_qty == null ? '' : task.cc_butdget_qty}" data-task_id="${task.Task_ID}" data-input="cc_butdget_qty" 
                                        id="precio_total" name="precio_total" placeholder="CC Butget QTY" value="${task.cc_butdget_qty == null ? '' : task.cc_butdget_qty}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm completado" data-valor="${task.Last_Per_Recorded == null ? '' : task.Last_Per_Recorded}" data-task_id="${task.Task_ID}" data-input="completado" 
                                        id="completado" name="completado" placeholder="%completado" value="${task.Last_Per_Recorded == null ? '' : task.Last_Per_Recorded}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w-30 form-control-sm precio_total" data-valor="${task.precio_total == null ? '' : task.precio_total}" data-task_id="${task.Task_ID}" data-input="precio_total" 
                                        id="precio_total" name="precio_total" placeholder="Price Total" value="${task.precio_total == null ? '' : task.precio_total}" readonly
                                        autocomplete="off" >
                                    </td>
                                    <td>
                                        ${task.precio_segun_avance}
                                    </td>
                                    <td> 
                                        <select class="form-control form-control-sm select_task" name="area_id" data-task_id="${task.Task_ID}">
                                            ${option}
                                        </select>
                                    </td>
                                    <td> 
                                        <i class="far fa-trash-alt ms-text-danger delete_task cursor-pointer" data-task_id="${task.Task_ID}" title="Delete"></i>
                                    </td>
                                    <td>
                                        ${task.of_coast}
                                    </td>
                                    <td>
                                        ${task.pwt_prod_rate}
                                    </td>
                                    <td>
                                        ${task.estimate_labor_cost}
                                    </td>
                                    <td>
                                        ${task.material_or_equipment_unit_cost}
                                    </td>
                                    <td>
                                        ${task.material_spread_rate_per_unit}
                                    </td>
                                    <td>
                                        ${task.material_qty_or_gallons_unit}
                                    </td>
                                    <td>
                                        ${task.mat_um}
                                    </td>
                                    <td>
                                        ${task.material_cost}
                                    </td>
                                    <td>
                                        ${task.subcontract_cost}
                                    </td>
                                    <td>
                                        ${task.equipment_cost}
                                    </td>
                                    <td>
                                        ${task.other_cost}
                                    </td>
                                </tr>
                            `;
    });
    tareas += `
                        </tbody>
                    </table>
                </fieldset>
            <br>
            </td>
        </tr>
       `;
    return tareas;
}
/*
 *animacion ojos visualizacion de datos
 */
$(document).on('click', '.view_detail', function () {
    $(this).toggleClass('fa-eye-slash').toggleClass('fa-eye');

    //console.log($(this).data('id_area'));

    view_detalle_proyectos($(this));
});

function view_detalle_proyectos(posision) {
    var detalle = posision.parent().parent().next();
    const verficar = posision.parent().parent().next().is(":visible");
    if (verficar) {
        //console.log(true);
        eliminar_vista(posision);
        detalle.hide();
    } else {
        //console.log(false);
        guardar_vista(posision);
        detalle.show();
    }
}
function guardar_vista(posision) {
    //console.log('añadiendo')
    var id = posision.data('id_area');
    var tipo = posision.data('tipo_view');
    switch (tipo) {
        case 'task':
            var data = localStorage.getItem('view_task');
            data = JSON.parse(data);
            if (data != null) {
                data.push(id);
            }
            localStorage.setItem('view_task', JSON.stringify(data));
            break;
        default:
            break;
    }
}
function eliminar_vista(posision) {
    //console.log('eliminando')
    var id = posision.data('id_area');
    var tipo = posision.data('tipo_view');
    switch (tipo) {
        case 'task':
            var data = localStorage.getItem('view_task');
            data = JSON.parse(data);
            if (data != null) {
                removeItemFromArr(data, id);
            }
            localStorage.setItem('view_task', JSON.stringify(data));
            break;
        default:
            break;
    }
}
function removeItemFromArr(arr, item) {
    var i = arr.indexOf(item);
    if (i !== -1) {
        arr.splice(i, 1);
    }
}
/**
 **cambio de evento
 */
$(document).on("change", ".select_task", function () {
    const input = $(this);
    var tr = input.parent().parent().parent().parent().parent().parent().parent().parent().children();
    $(tr).each(function (key, value) {
        if (key % 2 != 0) {
            console.log($(value))
        }
    });
    $('#proyectos').html('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-task`,
        data: {
            task_id: $(this).data('task_id'),
            area_id: $(this).val(),
            proyectos: $('#multiselect_project').val(),
            cargo: $('#cargo').val(),
            filtro: $('#filtro').val(),
        },
        dataType: 'json',
        success: function (response) {
            area = input.parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
            floor = input.parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().prev();
            area.remove();
            var reensamblaje = vista_areas(response.data.areas, response.data.floors, response.data.floor_id, true);
            floor.after(reensamblaje);
            // actualizar  vistas
            var data = localStorage.getItem('view_task');
            data = JSON.parse(data)
            data.forEach(id => {
                $(`#${id}`).show();
                $($(`#${id}`).prev().children()[0]).find('i').removeClass('far fa-eye-slash');
                $($(`#${id}`).prev().children()[0]).find('i').addClass('far fa-eye');
            });
            //console.log($(`#${data}`).show());
        },
    });
});
$(document).on("change", ".select_area", function () {
    const input = $(this);
    //console.log($(this).data('area_id'), $(this).val());
    $('#proyectos').html('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-area`,
        data: {
            area_id: $(this).data('area_id'),
            floor_id: $(this).val(),
            proyectos: $('#multiselect_project').val(),
            cargo: $('#cargo').val(),
            filtro: $('#filtro').val(),
        },
        dataType: 'json',
        success: function (response) {
            floor = input.parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
            edificio = input.parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().prev();
            floor.remove();
            var reensamblaje = vista_floor(response.data.floors, response.data.edificios, response.data.edificio_id, true);
            edificio.after(reensamblaje);
        },
    });
});
$(document).on("change", ".select_floor", function () {
    const input = $(this);
    console.log($(this).data('floor_id'), $(this).val());
    $('#proyectos').html('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-floor`,
        data: {
            floor_id: $(this).data('floor_id'),
            edificio_id: $(this).val(),
            proyectos: $('#multiselect_project').val(),
            cargo: $('#cargo').val(),
            filtro: $('#filtro').val(),
        },
        dataType: 'json',
        success: function (response) {
            floor = input.parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
            edificio = input.parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().prev();
            floor.remove();
            var reensamblaje = vista_edificios(response.data.edificios, response.data.proyecto_id, true);
            edificio.after(reensamblaje);
        },
    });
});
/*
! edit task campos de lista
*/
$(document).on("click", ".Tas_IDT", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".ActAre", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);

});
$(document).on("click", ".ActTas", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".nombre_task", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".Horas_Estimadas", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".Material_Estimado", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".cc_butdget_qty", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".precio_total", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".completado", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
//! edit task area  de lista
$(document).on("click", ".Are_IDT", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".nombre_area", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
//!floor
$(document).on("click", ".nombre_floor", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
//!edificio
$(document).on("click", ".nombre_edifico", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".descripcion_edificio", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});


//*update task
$(document).on("keydown", ".Tas_IDT, .ActAre, .ActTas, .nombre_task, .Horas_Estimadas, .Material_Estimado, .cc_butdget_qty, .precio_total, .completado", function (event) {
    const input = $(this);
    if (event.which == 13) {
        bloquear_input();
        $.ajax({
            type: 'PUT',
            url: `${base_url}/project-files/task-update`,
            dataType: 'json',
            data: {
                id: $('#export_sov').data('id'),
                tipo: $('#export_sov').data('tipo'),
                input: $(this).data('input'),
                task_id: $(this).data('task_id'),
                valor: $(this).val(),
            },
            async: true,
            success: function (response) {
                if (response.status == 'ok') {
                    task = input.parent().parent().parent().parent().parent().parent().parent();
                    area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                    task.remove();
                    var reensamblaje = vista_tareas(response.data.task, response.data.areas, response.data.area_id, true);
                    area.after(reensamblaje);
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    actualizacion_table();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        });
    }
});
//*update area
$(document).on("keydown", ".Are_IDT, .nombre_area", function (event) {
    const input = $(this);
    if (event.which == 13) {
        bloquear_input();
        $.ajax({
            type: 'PUT',
            url: `${base_url}/project-files/area-update`,
            dataType: 'json',
            data: {
                id: $('#export_sov').data('id'),
                tipo: $('#export_sov').data('tipo'),
                input: $(this).data('input'),
                area_id: $(this).data('area_id'),
                valor: $(this).val(),
            },
            async: true,
            success: function (response) {
                if (response.status == 'ok') {
                    task = input.parent().parent().parent().parent().parent().parent().parent();
                    area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                    task.remove();
                    var reensamblaje = vista_areas(response.data.areas, response.data.floors, response.data.floor_id, true);
                    area.after(reensamblaje);
                    input.removeClass('reset');
                    bloquear_input();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    actualizacion_table();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        });
    }
});
$(document).on("keydown", ".nombre_floor ", function (event) {
    const input = $(this);
    if (event.which == 13) {
        bloquear_input();
        $.ajax({
            type: 'PUT',
            url: `${base_url}/project-files/floor-update`,
            dataType: 'json',
            data: {
                id: $('#export_sov').data('id'),
                tipo: $('#export_sov').data('tipo'),
                input: $(this).data('input'),
                floor_id: $(this).data('floor_id'),
                valor: $(this).val(),
            },
            async: true,
            success: function (response) {
                if (response.status == 'ok') {
                    task = input.parent().parent().parent().parent().parent().parent().parent();
                    area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                    task.remove();
                    var reensamblaje = vista_floor(response.data.floors, response.data.edificios, response.data.edificio_id, true);
                    area.after(reensamblaje);
                    input.removeClass('reset');
                    bloquear_input();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    actualizacion_table();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        });
    }
});
//*edificio
$(document).on("keydown", ".nombre_edifico, .descripcion_edificio ", function (event) {
    const input = $(this);
    if (event.which == 13) {
        bloquear_input();
        $.ajax({
            type: 'PUT',
            url: `${base_url}/project-files/edificio-update`,
            dataType: 'json',
            data: {
                id: $('#export_sov').data('id'),
                tipo: $('#export_sov').data('tipo'),
                input: $(this).data('input'),
                edificio_id: $(this).data('edificio_id'),
                valor: $(this).val(),
            },
            async: true,
            success: function (response) {
                if (response.status == 'ok') {
                    task = input.parent().parent().parent().parent().parent().parent().parent();
                    area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                    task.remove();
                    var reensamblaje = vista_edificios(response.data.edificios, response.data.proyecto_id, true);
                    area.after(reensamblaje);
                    input.removeClass('reset');
                    bloquear_input();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    actualizacion_table();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        });
    }
});
/*
!delete
*/
//*delete task
$(document).on("click", ".delete_task", function (event) {
    const input = $(this);
    const task_id = $(this).data('task_id');
    Swal.fire({
        title: 'Are you sure to delete this task?',
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
                url: `${base_url}/project-files/delete-task`,
                dataType: 'json',
                data: {
                    task_id: task_id
                },
                dataType: 'json',
                async: true,
                success: function (response) {
                    task = input.parent().parent().parent().parent().parent().parent().parent();
                    area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                    task.remove();
                    var reensamblaje = vista_tareas(response.data.task, response.data.areas, response.data.area_id, true);
                    area.after(reensamblaje);
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
    });

});
$(document).on("click", ".delete_area", function (event) {
    const input = $(this);
    const area_id = $(this).data('area_id');
    Swal.fire({
        title: 'Are you sure to delete this Area?',
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
                url: `${base_url}/project-files/delete-area`,
                dataType: 'json',
                data: {
                    area_id: area_id
                },
                dataType: 'json',
                async: true,
                success: function (response) {
                    if (response.status == 'ok') {
                        task = input.parent().parent().parent().parent().parent().parent().parent();
                        area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                        task.remove();
                        var reensamblaje = vista_areas(response.data.areas, response.data.floors, response.data.floor_id, true);
                        area.after(reensamblaje);
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                }
            });
        }
    });
});
$(document).on("click", ".delete_floor", function (event) {
    const input = $(this);
    const floor_id = $(this).data('floor_id');
    Swal.fire({
        title: 'Are you sure to delete this Floor?',
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
                url: `${base_url}/project-files/delete-floor`,
                dataType: 'json',
                data: {
                    floor_id: floor_id
                },
                dataType: 'json',
                async: true,
                success: function (response) {
                    if (response.status == 'ok') {
                        task = input.parent().parent().parent().parent().parent().parent().parent();
                        area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                        //console.log(task,area)
                        task.remove();
                        var reensamblaje = vista_floor(response.data.floors, response.data.edificios, response.data.edificio_id, true);
                        area.after(reensamblaje);
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            });
        }
    });
});
$(document).on("click", ".delete_edificio", function (event) {
    const input = $(this);
    const edificio_id = $(this).data('edificio_id');
    Swal.fire({
        title: 'Are you sure to delete this Floor?',
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
                url: `${base_url}/project-files/delete-edificio`,
                dataType: 'json',
                data: {
                    edificio_id: edificio_id
                },
                dataType: 'json',
                async: true,
                success: function (response) {
                    if (response.status == 'ok') {
                        task = input.parent().parent().parent().parent().parent().parent().parent();
                        area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                        //console.log(task,area)
                        task.remove();
                        var reensamblaje = vista_edificios(response.data.edificios, response.data.proyecto_id, true);
                        area.after(reensamblaje);
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            });
        }
    });

});

//bloqueo general de tabla
function bloquear_input() {
    $('.Tas_IDT, .ActAre, .ActTas, .nombre_task, .Horas_Estimadas, .Material_Estimado, .cc_butdget_qty, .precio_total, .completado, .Are_IDT, .nombre_area, .nombre_area, .nombre_floor, .nombre_edifico , .descripcion_edificio').prop('readonly', true);
}
function actualizacion_table() {
    $('.rest').val($('.rest').data('valor'))
    $('.rest').removeClass('rest');
}
/*
!nuevos
*/
//task
$(document).on("click", ".nueva_tarea", function (event) {
    console.log('nueva task')
    const area_id = $(this).data('area_id');
    var task = `
    <tr id="nueva_tarea">
        <td>
            <input type="text" class="form-control w-80 form-control-sm nuevo_cost_code" data-valor="" data-task_id="" data-input="Tas_IDT" 
            id="cost_code" name="nuevo_cost_code" placeholder="Cost Code" value="" 
            autocomplete="off" >
        </td>
        <td>
        
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_ac_area" data-valor="" data-task_id="" data-input="ActAre" 
            id="ac_area" name="nuevo_cost_code" placeholder="Ac Area" value="" 
            autocomplete="off" >
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_act_Tas" data-valor="" data-task_id="" data-input="ActTas" 
            id="act_Tas" name="nuevo_act_Tas" placeholder="Ac task" value="" 
            autocomplete="off" >
        </td>
        <td>
            <textarea rows="2" type="text" class="form-control w-30 form-control-sm nuevo_nombre_task" data-valor="" data-task_id="" data-input="nombre_task"  
            id="nombre_task" name="nuevo_nombre_task" placeholder="Name"
            autocomplete="off"></textarea>
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_Horas_Estimadas" data-valor="}" data-input="Horas_Estimadas" 
            id="Horas_Estimadas" name="nuevo_Horas_Estimadas" placeholder="Estimated Hours" value="" 
            autocomplete="off" >
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_Material_Estimado" data-valor="" data-task_id="" data-input="Material_Estimado" 
            id="Material_Estimado" name="nuevo_Material_Estimado" placeholder="Estimated Material" value="" 
            autocomplete="off" >
        </td>
        <td>
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_cc_butdget_qty" data-valor="" data-task_id="" data-input="cc_butdget_qty" 
            id="cc_butdget_qty" name="nuevo_cc_butdget_qty" placeholder="CC Butget QTY" value="" 
            autocomplete="off" >
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_completado" data-valor="" data-task_id="" data-input="completado" 
            id="completado" name="nuevo_completado" placeholder="%completado" value="0" 
            autocomplete="off" >
        </td>
        <td>
            <input type="text" class="form-control w-30 form-control-sm nuevo_precio_total" data-valor="" data-task_id="" data-input="precio_total" 
            id="precio_total" name="nuevo_precio_total" placeholder="Price Total" value="0" 
            autocomplete="off" >
        </td>
        <td>
            
        </td>
        <td> 
            
        </td>
        <td>
            <i class="far fa-check-circle ms-text-primary delete cursor-pointer save_tarea" data-area_id="${area_id}" title="Save"></i>
            <i class="far fa-trash-alt ms-text-danger nuevo_delete cursor-pointer" data-id="89" title="Delete"></i>
        </td>
    </tr>
    `;
    //añadir nuevo campo
    $(this).parent().parent().next().children().next().find('tbody').append(task);
    $('.nueva_tarea').prop('disabled', true);
    $(".nuevo_cost_code").focus();
});
$(document).on("click", ".save_tarea", function (event) {
    console.log($(this).parent().parent().parent());
    const area_id = $(this).data('area_id');
    const input = $(this);
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/new-task`,
        dataType: 'json',
        data: {
            area_id: area_id,
            nuevo_cost_code: $('.nuevo_cost_code').val(),
            nuevo_ac_area: $('.nuevo_ac_area').val(),
            nuevo_act_Tas: $('.nuevo_act_Tas').val(),
            nuevo_nombre_task: $('.nuevo_nombre_task').val(),
            nuevo_Horas_Estimadas: $('.nuevo_Horas_Estimadas').val(),
            nuevo_Material_Estimado: $('.nuevo_Material_Estimado').val(),
            nuevo_cc_butdget_qty: $('.nuevo_cc_butdget_qty').val(),
            nuevo_completado: $('.nuevo_completado').val(),
            nuevo_precio_total: $('.nuevo_precio_total').val(),
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                $('.nueva_tarea').prop('disabled', false);
                //$(this).parent().parent().remove();
                console.log(response);
                task = input.parent().parent().parent().parent().parent().parent().parent();
                area = input.parent().parent().parent().parent().parent().parent().parent().prev();
                console.log(task, area)
                task.remove();
                var reensamblaje = vista_tareas(response.data.task, response.data.areas, response.data.area_id, true);
                area.after(reensamblaje);
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            else {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
        }
    });
});
$(document).on("click", ".nuevo_delete", function (event) {
    $('.nueva_tarea').prop('disabled', false);
    $(this).parent().parent().remove();
});
//area
$(document).on("click", ".nueva_area", function (event) {
    console.log('nueva area')
    const floor_id = $(this).data('floor_id');
    var area = `
    <tr>
        <td>

        </td>
        <td>
            <input type="text" class="form-control w-80 form-control-sm nuevo_code_area"
            id="nuevo_code_area" name="nuevo_code_area" placeholder="Code Area" 
            autocomplete="off" >
        </td>
        <td>
            <input type="text" class="form-control w-80 form-control-sm nuevo_nombre_area" 
                id="nuevo_nombre_area" name="nuevo_nombre_area" placeholder="Name"
                autocomplete="off" >
        </td>
        <td>
           
        </td>
        <td>

        </td>
        <td>
            <i class="far fa-check-circle ms-text-primary delete cursor-pointer save_area" data-floor_id="${floor_id}" title="Save"></i>
            <i class="far fa-trash-alt ms-text-danger nuevo_delete_area cursor-pointer" data-id="89" title="Delete"></i>
        </td>
    </tr>
    `;
    //añadir nuevo campo
    $(this).parent().parent().next().children().next().append(area);
    $('.nueva_area').prop('disabled', true);
    $(".nuevo_code_area").focus();
});
$(document).on("click", ".save_area", function (event) {
    console.log($('.nuevo_code_area').val());
    const floor_id = $(this).data('floor_id');
    const input = $(this);
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/new-area`,
        dataType: 'json',
        data: {
            floor_id: floor_id,
            nuevo_code_area: $('#nuevo_code_area').val(),
            nuevo_nombre_area: $('#nuevo_nombre_area').val(),
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                $('.save_area').prop('disabled', false);
                task = input.parent().parent().parent().parent().parent().parent();
                area = input.parent().parent().parent().parent().parent().parent().prev();
                task.remove();
                var reensamblaje = vista_areas(response.data.areas, response.data.floors, response.data.floor_id, true);
                area.after(reensamblaje);
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
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
                })
            }
        }
    });
});
$(document).on("click", ".nuevo_delete_area", function (event) {
    $('.nueva_area').prop('disabled', false);
    $(this).parent().parent().remove();
});
//floor
$(document).on("click", ".nuevo_floor", function (event) {
    const edificio_id = $(this).data('edificio_id');
    var area = `
    <tr>
        <td>

        </td>
        <td>
            <input type="text" class="form-control w-10 form-control-sm nuevo_nombre_floor"  
            id="nuevo_nombre_floor" name="nuevo_nombre_floor" placeholder="Name" autocomplete="off" >
        </td>
        <td>
           
        </td>
        <td>
        
        </td>
        <td>
            <i class="far fa-check-circle ms-text-primary delete cursor-pointer save_floor" data-edificio_id="${edificio_id}" title="Save"></i>
            <i class="far fa-trash-alt ms-text-danger nuevo_delete_floor cursor-pointer"  title="Delete"></i>
        </td>
    </tr>
    `;
    //añadir nuevo campo
    $(this).parent().parent().next().children().next().append(area);
    $('.nuevo_floor').prop('disabled', true);
    $(".nuevo_nombre_floor").focus();
});
$(document).on("click", ".save_floor", function (event) {
    const edificio_id = $(this).data('edificio_id');
    const input = $(this);
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/new-floor`,
        dataType: 'json',
        data: {
            edificio_id: edificio_id,
            nuevo_nombre_floor: $('#nuevo_nombre_floor').val(),
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                $('.nuevo_floor').prop('disabled', false);
                task = input.parent().parent().parent().parent().parent().parent();
                area = input.parent().parent().parent().parent().parent().parent().prev();
                //console.log(task,area)
                task.remove();
                var reensamblaje = vista_floor(response.data.floors, response.data.edificios, response.data.edificio_id, true);
                area.after(reensamblaje);
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            else {
                $alert = "";
                response.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
        }
    });
});
$(document).on("click", ".nuevo_delete_floor", function (event) {
    $('.nuevo_floor').prop('disabled', false);
    $(this).parent().parent().remove();
});
//edificio
$(document).on("click", ".nuevo_edificio", function (event) {
    const proyecto_id = $(this).data('proyecto_id');
    var area = `
    <tr>
        <td>

        </td>
        <td>
            <input type="text" class="form-control w-10 form-control-sm nuevo_nombre_edificio"  
            id="nuevo_nombre_edificio" name="nuevo_nombre_edificio" placeholder="Name" autocomplete="off" >
        </td>
        <td>
        
        </td>
        <td>
            <input type="text" class="form-control w-10 form-control-sm nuevo_descripcion_edificio"  
            id="nuevo_descripcion_edificio" name="nuevo_descripcion_edificio" placeholder="Description" autocomplete="off" >
        </td>
        <td>
            <i class="far fa-check-circle ms-text-primary delete cursor-pointer save_edificio" data-proyecto_id="${proyecto_id}" title="Save"></i>
            <i class="far fa-trash-alt ms-text-danger nuevo_delete_edificio cursor-pointer"  title="Delete"></i>
        </td>
    </tr>
    `;
    //añadir nuevo campo
    $(this).parent().parent().next().children().next().append(area);
    $('.nuevo_edificio').prop('disabled', true);
    $(".nuevo_nombre_edificio").focus();
});
$(document).on("click", ".save_edificio", function (event) {
    const proyecto_id = $(this).data('proyecto_id');
    const input = $(this);
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/new-edificio`,
        dataType: 'json',
        data: {
            proyecto_id: proyecto_id,
            nuevo_nombre_edificio: $('#nuevo_nombre_edificio').val(),
            nuevo_descripcion_edificio: $('#nuevo_descripcion_edificio').val(),
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                $('.nuevo_edificio').prop('disabled', false);
                task = input.parent().parent().parent().parent().parent().parent();
                area = input.parent().parent().parent().parent().parent().parent().prev();
                //console.log(task,area)
                task.remove();
                var reensamblaje = vista_edificios(response.data.edificios, response.data.proyecto_id, true);
                area.after(reensamblaje);
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
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
                })
            }
        }
    });
});
$(document).on("click", ".nuevo_delete_edificio", function (event) {
    $('.nuevo_edificio').prop('disabled', false);
    console.log('ejecuntando esto')
    $(this).parent().parent().remove();
});

//save event
function animacion_load() {
    $('#table').html(`
    <table class="table thead-primary" >
    <tbody>
    <tr>
        <td rowspan="19" colspan="19">
            <div class="spinner spinner-3">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </td>
    </tr>
    </tbody>
    </table>`);
}