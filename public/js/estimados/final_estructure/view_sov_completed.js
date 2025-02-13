
function upload_datatable_Proyecto(edificios) {
    $('#load-data-thead').html('');
    $('#load-data-thead').append(header_sov_proyecto());
    $('#load-data-tbody').html('');
    proyectoHTML = ``;
    edificios.forEach((edificio, index) => {
        efidicioHTML = ``;
        if (edificio.floors) {
            edificio.floors.forEach((floor, index) => {
                floorsHTML = ``;
                if (floor.areas) {
                    areasHTML = ``;
                    floor.areas.forEach((area, index) => {
                        if (area.task) {
                            tareasHTML = ``;
                            area.task.forEach((tarea, index) => {
                                tareasHTML += `
                        <tr style="border-top: 1px solid #ffffff; background-color: ${verificar_procedimiento(superficie.procedimiento, superficie.miselaneo)};" data-estimado_use_metodo_id="${superficie.id}">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${tarea.Tas_IDT}</td>
                            <td>${tarea.Nombre}</td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm cc_butdget_qty"
                                id="cc_butdget_qty" name="cc_butdget_qty" placeholder="cc butdget qty" data-valor="${tarea.cc_butdget_qty == null ? '' : tarea.cc_butdget_qty}" data-task_id="${tarea.Task_ID}" data-input="cc_butdget_qty" 
                                autocomplete="off" value="${tarea.cc_butdget_qty == null ? '' : tarea.cc_butdget_qty}" readonly >
                            </td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm um"
                                id="um" name="um" placeholder="Um" data-valor="${tarea.um == null ? '' : tarea.um}" data-task_id="${tarea.Task_ID}" data-input="um"
                                autocomplete="off" value="${tarea.um == null ? '' : tarea.um}" readonly >
                            </td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm edit_horas_estimadas"
                                id="edit_horas_estimadas" name="edit_horas_estimadas" placeholder="Estimate Hours" data-valor="${tarea.Horas_Estimadas == null ? '' : tarea.Horas_Estimadas}" data-task_id="${tarea.Task_ID}" data-input="horas_estimadas"
                                autocomplete="off" value="${tarea.Horas_Estimadas == null ? '' : tarea.Horas_Estimadas}" readonly >
                            </td>
                            <td>${tarea.horas_usadas == null ? '' : tarea.horas_usadas}</td>
                            <td>
                                ${tarea.Last_Date_Per_Recorded == null ? '' : tarea.Last_Date_Per_Recorded}
                            </td>
                            <td>
                                ${tarea.Usr == null ? '' : tarea.Usr}
                            </td>
                            <td>
                                ${tarea.porcentaje_horas_usadas == null ? '' : tarea.porcentaje_horas_usadas}
                            </td>
                            <td>
                                <input type="text" class="form-control w-100 form-control-sm porcentaje" data-valor="${tarea.Last_Per_Recorded}" data-task_id="${tarea.Task_ID}" data-input="porcentaje" 
                                id="porcentaje" name="porcentaje" placeholder="% Completed" value="${tarea.Last_Per_Recorded == null ? '' : tarea.Last_Per_Recorded}"
                                autocomplete="off" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control w-80 form-control-sm precio_total" data-valor="${tarea.precio_total}" data-task_id="${tarea.Task_ID}" data-input="precio_total" 
                                id="precio_total" name="precio_total" placeholder="Price Total" value="${tarea.precio_total == null ? '' : tarea.precio_total}"
                                autocomplete="off" readonly>
                            </td>
                            <td>
                                ${tarea.precio_segun_avance == null ? '' : tarea.precio_segun_avance}
                            </td>
                            <td>
                                <input type="text" class="form-control w-100 form-control-sm sov_id" data-valor="${tarea.sov_id == null ? '' : tarea.sov_id}" data-task_id="${tarea.Task_ID}" data-input="sov_id"
                                id="sov_id" name="sov_id" placeholder="Code Sov" value="${tarea.sov_id == null ? '' : tarea.sov_id}"
                                autocomplete="off" readonly>
                            </td> 
                            <td>
                                <input type="text" class="form-control form-control-sm sov_descripcion" data-valor="${tarea.sov_descripcion == null ? '' : tarea.sov_descripcion}" data-task_id="${tarea.Task_ID}" data-input="sov_descripcion"
                                id="sov_descripcion" name="sov_descripcion" placeholder="Description SoV" value="${tarea.sov_descripcion == null ? '' : tarea.sov_descripcion}"
                                autocomplete="off" readonly>
                            </td>            
                        </tr>
                        `;
                            });
                            areasHTML += `
                    <tr data-estimado_use_metodo_id="${area.Area_ID}">
                        <td></td>
                        <td></td>
                        <td>${area.Are_IDT}</td>
                        <td colspan="4">${area.Nombre}</td>
                        <td>${area.total_area_horas_estimadas == null ? '' : area.total_area_horas_estimadas}</td>
                        <td>${area.total_area_horas_usadas == null ? '' : area.total_area_horas_usadas}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>${area.total_area_precio_total == null ? '' : area.total_area_precio_total}</td>
                        <td>${area.total_area_precio_segun_avance == null ? '' : area.total_area_precio_segun_avance}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    ${tareasHTML}
                    `;
                        } else {
                            areasHTML += `
                    <tr>
                        <td> </td>
                        <td></td>
                        <td></td>
                        <td colspan='5'>
                        Data no encontrada
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                `;
                        }

                    });
                    floorsHTML += `
                        ${areasHTML}
                    `;
                } else {
                    floorsHTML += `
            <tr>
                <td> </td>
                <td></td>
                <td> </td>
                <td colspan='5'>
                Data no encontrada
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        `;
                }
                //areass
                efidicioHTML += `
            <tr data-estimado_use_metodo_id="${floor.Area_ID}" style="background-color: #d1e1ff">
                <td></td>
                <td colspan="6">${floor.Nombre}</td>
                <td>${floor.total_floor_horas_estimadas == null ? '' : floor.total_floor_horas_estimadas}</td>
                <td>${floor.total_floor_horas_usadas == null ? '' : floor.total_floor_horas_usadas}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>${floor.total_floor_precio_total == null ? '' : floor.total_floor_precio_total}</td>
                <td>${floor.total_floor_precio_segun_avance == null ? '' : floor.total_floor_precio_segun_avance}</td>
                <td></td>
                <td></td>
            </tr>
                ${floorsHTML}
            `;
            });
        } else {
            floorsHTML += `
                <tr>
                    <td> </td>
                    <td></td>
                    <td> </td>
                    <td colspan='5'>
                    Data no encontrada
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            `;
        }
        proyectoHTML += `
        <tr data-estimado_use_metodo_id="${edificio.Edificio_ID}" style="background-color: #f1f3e1">
            <td colspan="7">${edificio.Nombre}</td>
                <td>${edificio.total_edificio_horas_estimadas == null ? '' : edificio.total_edificio_horas_estimadas}</td>
                <td>${edificio.total_edificio_horas_usadas == null ? '' : edificio.total_edificio_horas_usadas}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>${edificio.total_edificio_precio_total == null ? '' : edificio.total_edificio_precio_total}</td>
                <td>${edificio.total_edificio_precio_segun_avance == null ? '' : edificio.total_edificio_precio_segun_avance}</td>
                <td></td>
                <td></td>
        </tr>
            ${efidicioHTML}
        `;
    });

    $('#load-data-tbody').append(proyectoHTML);
}
function upload_datatable_edificio(floors) {
    $('#load-data-thead').html('');
    $('#load-data-thead').append(header_sov_edificio());
    $('#load-data-tbody').html('');
    efidicioHTML = ``;
    floors.forEach((floor, index) => {
        floorsHTML = ``;
        if (floor.areas) {
            areasHTML = ``;
            floor.areas.forEach((area, index) => {
                if (area.task) {
                    tareasHTML = ``;
                    area.task.forEach((tarea, index) => {
                        tareasHTML += `
                        <tr style="border-top: 1px solid #ffffff; background-color: ${verificar_procedimiento(superficie.procedimiento, superficie.miselaneo)};" data-estimado_use_metodo_id="${superficie.id}">
                            <td></td>
                            <td></td>
                            <td>${tarea.Tas_IDT}</td>
                            <td>${tarea.Nombre}</td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm cc_butdget_qty"
                                id="cc_butdget_qty" name="cc_butdget_qty" placeholder="cc butdget qty" data-valor="${tarea.cc_butdget_qty}" data-task_id="${tarea.Task_ID}" data-input="cc_butdget_qty"
                                autocomplete="off"  value="${tarea.cc_butdget_qty == null ? '' : tarea.cc_butdget_qty}" readonly >
                            </td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm um"
                                id="um" name="um" placeholder="Um" data-valor="${tarea.um == null ? '' : tarea.um}" data-task_id="${tarea.Task_ID}" data-input="um"
                                autocomplete="off"  value="${tarea.um == null ? '' : tarea.um}" readonly >
                            </td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm edit_horas_estimadas"
                                id="edit_horas_estimadas" name="edit_horas_estimadas" placeholder="Estimate Hours" data-valor="${tarea.Horas_Estimadas == null ? '' : tarea.Horas_Estimadas}" data-task_id="${tarea.Task_ID}" data-input="horas_estimadas"
                                autocomplete="off"  value="${tarea.Horas_Estimadas == null ? '' : tarea.Horas_Estimadas}" readonly >
                            </td>
                            <td>${tarea.horas_usadas == null ? '' : tarea.horas_usadas}</td>
                            <td>
                                ${tarea.Last_Date_Per_Recorded == null ? '' : tarea.Last_Date_Per_Recorded}
                            </td>
                            <td>
                                ${tarea.Usr == null ? '' : tarea.Usr}
                            </td>
                            <td>
                                ${tarea.porcentaje_horas_usadas == null ? '' : tarea.porcentaje_horas_usadas}
                            </td>
                            <td>
                                <input type="text" class="form-control w-100 form-control-sm porcentaje" data-valor="${tarea.Last_Per_Recorded}" data-task_id="${tarea.Task_ID}" data-input="porcentaje" 
                                id="porcentaje" name="porcentaje" placeholder="% Completed" value="${tarea.Last_Per_Recorded}"
                                autocomplete="off" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control w-80 form-control-sm precio_total" data-valor="${tarea.precio_total}" data-task_id="${tarea.Task_ID}" data-input="precio_total" 
                                id="precio_total" name="precio_total" placeholder="Price Total" value="${tarea.precio_total}"
                                autocomplete="off" readonly>
                            </td>
                            <td>
                                ${tarea.precio_segun_avance == null ? '' : tarea.precio_segun_avance}
                            </td>
                            <td>
                                <input type="text" class="form-control w-100 form-control-sm sov_id" data-valor="${tarea.sov_id}" data-task_id="${tarea.Task_ID}" data-input="sov_id"
                                id="sov_id" name="sov_id" placeholder="Code Sov" value="${tarea.sov_id == null ? '' : tarea.sov_id}"
                                autocomplete="off" readonly>
                            </td> 
                            <td>
                                <input type="text" class="form-control form-control-sm sov_descripcion" data-valor="${tarea.sov_descripcion == null ? '' : tarea.sov_descripcion}" data-task_id="${tarea.Task_ID}" data-input="sov_descripcion"
                                id="sov_descripcion" name="sov_descripcion" placeholder="Description SoV" value="${tarea.sov_descripcion == null ? '' : tarea.sov_descripcion}"
                                autocomplete="off" readonly>
                            </td>            
                        </tr>
                        `;
                    });
                    areasHTML += `
                    <tr data-estimado_use_metodo_id="${area.Area_ID}">
                        <td></td>
                        <td>${area.Are_IDT}</td>
                        <td colspan="4">${area.Nombre}</td>
                        <td>${area.total_area_horas_estimadas == null ? '' : area.total_area_horas_estimadas}</td>
                        <td>${area.total_area_horas_usadas == null ? '' : area.total_area_horas_usadas}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>${area.total_area_precio_total == null ? '' : area.total_area_precio_total}</td>
                        <td>${area.total_area_precio_segun_avance == null ? '' : area.total_area_precio_segun_avance}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    ${tareasHTML}
                    `;
                } else {
                    areasHTML += `
                    <tr>
                        <td> </td>
                        <td></td>
                        <td></td>
                        <td colspan='5'>
                        Data no encontrada
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                `;
                }

            });
            floorsHTML += `
                ${areasHTML}
            `;
        } else {
            floorsHTML += `
            <tr>
                <td> </td>
                <td></td>
                <td> </td>
                <td colspan='5'>
                Data no encontrada
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        `;
        }
        //areass
        efidicioHTML += `
        <tr data-estimado_use_metodo_id="${floor.Area_ID}" style="background-color: #d1e1ff">
            <td colspan="6">${floor.Nombre}</td>
            <td>${floor.total_floor_horas_estimadas == null ? '' : floor.total_floor_horas_estimadas}</td>
            <td>${floor.total_floor_horas_usadas == null ? '' : floor.total_floor_horas_usadas}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>${floor.total_floor_precio_total == null ? '' : floor.total_floor_precio_total}</td>
            <td>${floor.total_floor_precio_segun_avance == null ? '' : floor.total_floor_precio_segun_avance}</td>
            <td></td>
            <td></td>
        </tr>
            ${floorsHTML}
        `;
    });
    $('#load-data-tbody').append(efidicioHTML);
}
function upload_datatable_floor(areas) {
    $('#load-data-thead').html('');
    $('#load-data-thead').append(header_sov_floor());
    $('#load-data-tbody').html('');
    areasHTML = ``;
    areas.forEach((area, index) => {
        if (area.task) {
            tareasHTML = ``;
            area.task.forEach((tarea, index) => {
                tareasHTML += `
                        <tr style="border-top: 1px solid #ffffff; background-color: ${verificar_procedimiento(superficie.procedimiento, superficie.miselaneo)};" data-estimado_use_metodo_id="${superficie.id}">
                            <td></td>
                            <td>${tarea.Tas_IDT}</td>
                            <td>${tarea.Nombre}</td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm cc_butdget_qty"
                                id="cc_butdget_qty" name="cc_butdget_qty" placeholder="cc butdget qty" data-valor="${tarea.cc_butdget_qty == null ? '' : tarea.cc_butdget_qty}" data-task_id="${tarea.Task_ID}" data-input="cc_butdget_qty"
                                autocomplete="off"  value="${tarea.cc_butdget_qty == null ? '' : tarea.cc_butdget_qty}"   readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm um"
                                id="um" name="um" placeholder="Um" data-valor="${tarea.um == null ? '' : tarea.um}" data-task_id="${tarea.Task_ID}" data-input="um"
                                autocomplete="off"  value="${tarea.um == null ? '' : tarea.um}"  readonly >
                            </td>
                            <td>
                                <input type="text" class="form-control w-60 form-control-sm edit_horas_estimadas"
                                id="edit_horas_estimadas" name="edit_horas_estimadas" placeholder="Estimate Hours" data-valor="${tarea.Horas_Estimadas == null ? '' : tarea.Horas_Estimadas}" data-task_id="${tarea.Task_ID}" data-input="horas_estimadas"
                                autocomplete="off"  value="${tarea.Horas_Estimadas == null ? '' : tarea.Horas_Estimadas}"  readonly >
                            </td>
                            <td>${tarea.horas_usadas == null ? '' : tarea.horas_usadas}</td>
                            <td>
                                ${tarea.Last_Date_Per_Recorded == null ? '' : tarea.Last_Date_Per_Recorded}
                            </td>
                            <td>
                                ${tarea.Usr == null ? '' : tarea.Usr}
                            </td>
                            <td>
                            ${tarea.porcentaje_horas_usadas == null ? '' : tarea.porcentaje_horas_usadas}
                        </td>
                            <td>
                                <input type="text" class="form-control w-100 form-control-sm porcentaje" data-valor="${tarea.Last_Per_Recorded}" data-task_id="${tarea.Task_ID}" data-input="porcentaje" 
                                id="porcentaje" name="porcentaje" placeholder="% Completed" value="${tarea.Last_Per_Recorded}" readonly
                                autocomplete="off" >
                            </td>
                            <td>
                                <input type="text" class="form-control w-80 form-control-sm precio_total" data-valor="${tarea.precio_total}" data-task_id="${tarea.Task_ID}" data-input="precio_total" 
                                id="precio_total" name="precio_total" placeholder="Price Total" value="${tarea.precio_total}" readonly
                                autocomplete="off" >
                            </td>
                            <td>
                                ${tarea.precio_segun_avance == null ? '' : tarea.precio_segun_avance}
                            </td>
                            <td>
                                <input type="text" class="form-control w-100 form-control-sm sov_id" data-valor="${tarea.sov_id}" data-task_id="${tarea.Task_ID}" data-input="sov_id"
                                id="sov_id" name="sov_id" placeholder="Code Sov" value="${tarea.sov_id == null ? '' : tarea.sov_id}" readonly
                                autocomplete="off" >
                            </td> 
                            <td>
                                <input type="text" class="form-control form-control-sm sov_descripcion" data-valor="${tarea.sov_descripcion}" data-task_id="${tarea.Task_ID}" data-input="sov_descripcion"
                                id="sov_descripcion" name="sov_descripcion" placeholder="Description SoV" value="${tarea.sov_descripcion == null ? '' : tarea.sov_descripcion}" readonly
                                autocomplete="off" >
                            </td>            
                        </tr>
                        `;
            });
            areasHTML += `
                    <tr data-estimado_use_metodo_id="${area.Area_ID}">
                        <td>${area.Are_IDT}</td>
                        <td colspan="4">${area.Nombre}</td>
                        <td>${area.total_area_horas_estimadas == null ? '' : area.total_area_horas_estimadas}</td>
                        <td>${area.total_area_horas_usadas == null ? '' : area.total_area_horas_usadas}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>${area.total_area_precio_total == null ? '' : area.total_area_precio_total}</td>
                        <td>${area.total_area_precio_segun_avance == null ? '' : area.total_area_precio_segun_avance}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    ${tareasHTML}
                    `;
        } else {
            areasHTML += `
                    <tr>
                        <td></td>
                        <td></td>
                        <td colspan='5'>
                        Data no encontrada
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                `;
        }

    });
    $('#load-data-tbody').append(areasHTML);
}
$(document).on("click", ".view_sov_proyecto", function () {
    const nombre = $(this).data('nombre');
    animacion_load();
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/final-export-sov`,
        dataType: 'json',
        data: {
            'id': $(this).data('proyecto_id'),
            'tipo': 'proyecto'
        },
        async: true,
        success: function (response) {
            $('#nombre_report_view').text('');
            $('#nombre_report_view').text(`Report See Final SOV ${nombre}`);
            upload_datatable_Proyecto(response.data);
            $('#export_sov').data('id', response.data[0].Pro_ID);
            $('#export_sov').data('tipo', 'proyecto');
            $('#export_sov').prop('disabled', false);
            /*guadardo de info del sitio actual*/
        }
    });
});
$(document).on("click", ".view_sov_edificio", function () {
    const nombre = $(this).data('nombre');
    animacion_load();
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/final-export-sov`,
        dataType: 'json',
        data: {
            'id': $(this).data('edificio_id'),
            'tipo': 'edificio'
        },
        async: true,
        success: function (response) {
            $('#nombre_report_view').text('');
            $('#nombre_report_view').text(`Report See Final SOV ${nombre}`);
            upload_datatable_edificio(response.data);
            $('#export_sov').data('id', response.data[0].Edificio_ID);
            $('#export_sov').data('tipo', 'edificio');
            $('#export_sov').prop('disabled', false);
        }
    });
});
$(document).on("click", ".view_sov_floor", function () {
    const nombre = $(this).data('nombre');
    animacion_load();
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/final-export-sov`,
        dataType: 'json',
        data: {
            'id': $(this).data('floor_id'),
            'tipo': 'floor'
        },
        async: true,
        success: function (response) {
            $('#nombre_report_view').text('');
            $('#nombre_report_view').text(`Report See Final SOV ${nombre}`);
            upload_datatable_floor(response.data);
            $('#export_sov').data('id', response.data[0].Floor_ID);
            $('#export_sov').data('tipo', 'floor');
            $('#export_sov').prop('disabled', false);
        }
    });
});
function verificar_procedimiento(procedimiento, miselaneo) {
    var color;
    if (miselaneo == 'y') {
        color = '#f4f5d5';
    }
    else {
        color = '#f1f5fc';
    }
    switch (procedimiento) {
        case 'Only Material':
            color = '#b9f1ff';
            break;
        case 'Only Installation':
            color = '#b9f1ff';
            break;
        default:
            color = '#f1f5fc';
            break;
    }
    return color;
}
function header_sov_proyecto() {
    return header = `
        <tr>
            <th>Building</th>
            <th>Floors</th>
            <th>Area</th>
            <th>Cost Code</th>
            <th>Description</th>
            <th>CC Butget QTY</th>
            <th>Um</th>
            <th>Estimate Hours</th>
            <th>Hrs. Used</th>
            <th>% Date Record</th>
            <th>&nbsp;&nbsp;User&nbsp;&nbsp;</th>
            <th>% Hrs. Used</th>
            <th>% Completed</th>
            <th>Price Total</th>
            <th>To Bill Acording % Completed</th>
            <th>Code SoV</th>
            <th>Description SoV</th>
        </tr>
    `;
}
function header_sov_edificio() {
    return header = `
        <tr>
            <th>Floors</th>
            <th>Area</th>
            <th>Cost Code</th>
            <th>Description</th>
            <th>CC Butget QTY</th>
            <th>Um</th>
            <th>Estimate Hours</th>
            <th>Hrs. Used</th>
            <th>% Date Record</th>
            <th>&nbsp;&nbsp;User&nbsp;&nbsp;</th>
            <th>% Hrs. Used</th>
            <th>% Completed</th>
            <th>Price Total</th>
            <th>To Bill Acording % Completed</th>
            <th>Code SoV</th>
            <th>Description SoV</th>
        </tr>
    `;
}
function header_sov_floor() {
    return header = `
        <tr>
         
            <th>Area</th>
            <th>Cost Code</th>
            <th>Description</th>
            <th>CC Butget QTY</th>
            <th>Um</th>
            <th>Estimate Hours</th>
            <th>Hrs. Used</th>
            <th>% Date Record</th>
            <th>&nbsp;&nbsp;User&nbsp;&nbsp;</th>
            <th>% Hrs. Used</th>
            <th>% Completed</th>
            <th>Price Total</th>
            <th>To Bill Acording % Completed</th>
            <th>Code SoV</th>
            <th>Description SoV</th>
        </tr>
    `;
}
$("#export_sov").on('click', function (evt) {
    $('#filter_download_excel').modal('show');
    $('#form_filtro_download').trigger('reset')
    $('#filter_download_excel .modal-title').text('Filters');
});
/*filter donwload */
$("#filter_download_sov").on('click', function (evt) {
    $('#descargar_excel').attr("action", `${base_url}/project-files/export-excel-completed?id=${$('#export_sov').data('id')}&tipo=${$('#export_sov').data('tipo')}&no_sov_code=${$('#no_sov_code').is(':checked')}&no_precio=${$('#no_precio').is(':checked')}`);
    $("#descargar_excel").submit();
    $('#filter_download_excel').modal('hide');
});
/*edicion de tabla */
$(document).on("click", ".edit_task_final", function () {
    $('#modalEditSovFinal').modal('show');

});
$(document).on("click", ".edit_horas_estimadas", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".porcentaje", function () {
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
$(document).on("click", ".sov_id", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});
$(document).on("click", ".sov_descripcion", function () {
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
$(document).on("click", ".um", function () {
    actualizacion_table();
    $(this).addClass('rest');
    bloquear_input();
    $(this).prop('readonly', false);
});

$(document).on("keydown", ".edit_horas_estimadas, .porcentaje, .precio_total, .sov_id, .sov_descripcion, .cc_butdget_qty, .um", function (event) {
    const input = $(this);
    if (event.which == 13) {
        bloquear_input();
        $.ajax({
            type: 'PUT',
            url: `${base_url}/project-files/final-sov-update`,
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
                const evaluar = $('#export_sov').data('tipo');
                console.log(evaluar)
                if (response.status == 'ok') {
                    switch (evaluar) {
                        case 'proyecto':
                            upload_datatable_Proyecto(response.data);
                            break;
                        case 'edificio':
                            upload_datatable_edificio(response.data);
                            break;
                        case 'floor':
                            upload_datatable_floor(response.data)
                            break;
                        default:
                            break;
                    }
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
//bloqueo general de tabla
function bloquear_input() {
    $('.edit_horas_estimadas, .porcentaje, .precio_total, .sov_id, .sov_descripcion, .cc_butdget_qty, .um').prop('readonly', true);
}

$("#export_sov_multiple").on('click', function (evt) {
    var proyectos_id = [];
    $('.proyectos[type="checkbox"]').each(function () {
        if (this.checked) {
            proyectos_id.push(this.value);
        }
    });
});
function actualizacion_table(input) {
    $('.rest').val($('.rest').data('valor'))
    $('.rest').removeClass('rest');
}

$(document).ready(function () {
    $('#export_sov').prop('disabled', true);
});
//save event
function animacion_load() {
    $('#load-data-tbody').html(`
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
   `);
}