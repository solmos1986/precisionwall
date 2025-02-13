
function table_proyectos(proyectos) {
    var table = `
    <table class="table thead-primary" >
        <thead>
            <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Project</th>
                <th with="120" >Status</th>
                <th>Project Manager</th>
                <th>Foreman</th>
                <th>Actions</th>
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
            <td>${proyecto.Nombre_Estatus==null ? '' : proyecto.Nombre_Estatus}</td>
            <td>${proyecto.nombre_project_manager==null ? '' : proyecto.nombre_project_manager}</td>
            <td>${proyecto.nombre_foreman==null ? '' : proyecto.nombre_foreman}</td>
            <td>
                <a href="#list-proyectos">
                <i class="fas fa-pencil-alt ms-text-warning view_sov_proyecto" data-proyecto_id="${proyecto.Pro_ID}" data-nombre="${proyecto.Nombre}" title="Edit records" ></i>
                </a>
            </td>
        </tr>
        ${vista_edificios(proyecto.edificios, proyecto.Nombre)}        
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
function vista_edificios(list_edificios, nombre_proyecto) {
    var edificios = `
        <tr style="display:none">
            <td></td>
            <td colspan="6">
            <fieldset class="border p-1 ml-5 mb-0">
                <legend class="w-auto" style="font-size:0.9rem;"> List Level 0: building</legend>                
                    <table class="table  no-footer w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%;"></th>
                                <th>Name</th>
                                <th> Description</th>
                                <th style="width: 15%;"> Actions</th>
                                
                            </tr>
                        </thead>
                        <tbody>`;
    list_edificios.forEach(edificio => {
        edificios += `
                            <tr >
                                <td>  <i class="far fa-eye-slash ms-text-primary cursor-pointer view_detail" title="view leven" data-tipo="empresa"></i></td>
                                <td> ${edificio.Nombre}</td>
                                <td> ${edificio.Descripcion == null ? '' : edificio.Descripcion }</td>
                                <td> 
                                    <a href="#list-proyectos"><i class="fas fa-pencil-alt ms-text-warning view_sov_edificio" data-edificio_id="${edificio.Edificio_ID}" data-nombre="${nombre_proyecto} / ${edificio.Nombre}" title="Edit records"></i></a>
                                    <!--i class="far fa-trash-alt ms-text-danger delete cursor-pointer" data-id="89" title="Delete"></i-->
                                </td>
                            </tr>
                            ${vista_floor(edificio.floors, nombre_proyecto, edificio.Nombre)}
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
function vista_floor(list_floors, nombre_proyecto, nombre_edificio) {
    var floors = `
        <tr style="display:none">
            <td></td>
            <td colspan="6">
            <fieldset class="border p-1 ml-5 mb-0">
                <legend class="w-auto" style="font-size:0.9rem;"> List Level 1 floors</legend>                
                    <table class="table  no-footer w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%;"></th>
                                <th>Name</th>
                                <th style="width: 15%;"> Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
    list_floors.forEach(floor => {
        floors += `
                            <tr >
                                <td>  <!--i class="far fa-eye-slash ms-text-primary cursor-pointer view_detail" title="view leven" data-tipo="empresa"></i--></td>
                                <td> ${floor.Nombre}</td>
                                <td> 
                                    <a href="#"><i class="fas fa-pencil-alt ms-text-warning view_sov_floor" data-floor_id="${floor.Floor_ID}" data-nombre="${nombre_proyecto} / ${nombre_edificio} / ${floor.Nombre}" title="Edit records"></i></a>
                                    <!--i class="far fa-trash-alt ms-text-danger delete cursor-pointer" data-id="89" title="Delete"></i-->
                                </td>
                            </tr>
                            <!--vista_areas-->
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
function vista_areas(list_areas, list_floor) {
    var areas = `
        <tr style="display:none">
            <td></td>
            <td colspan="6">
            <fieldset class="border p-1 ml-5 mb-0">
                <legend class="w-auto" style="font-size:0.9rem;">List Level 2: Floors and/or common areas</legend>                
                    <table class="table  no-footer w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%;"></th>
                                <th>Code Area</th>
                                <th>Name</th>
                                <th></th>
                                <th style="width: 15%;"> </th>
                                
                            </tr>
                        </thead>
                        <tbody>`;
    list_areas.forEach(area => {
        var option = ``;
        list_floor.forEach(floor => {
            option += `
            <option value="${floor.Floor_ID}" ${area.Floor_ID == floor.Floor_ID ? 'selected' : ''}>${floor.Nombre}</option>
            `;
        });
        areas += `
                            <tr >
                                <td>  <i class="far fa-eye-slash ms-text-primary cursor-pointer view_detail" title="view leven" data-tipo="empresa"></i></td>
                                <td> ${area.Are_IDT}</td>
                                <td> ${area.Nombre}</td>
                                <td> 
                                    
                                </td>
                                <td> 
                                    <!--a href="#"><i class="fas fa-pencil-alt ms-text-warning"></i></a>
                                    <i class="far fa-trash-alt ms-text-danger delete cursor-pointer" data-id="89" title="Delete"></i-->
                                </td>
                            </tr>
                            ${vista_tareas(area.task)}
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
function vista_tareas(list_task) {
    var tareas = `
        <tr style="display:none">
            <td></td>
            <td colspan="6">
                <fieldset class="border p-1 ml-5 mb-0">
                    <legend class="w-auto" style="font-size:0.9rem;">List Level 3: Areas or Tasks </legend>                
                        <table class="table  no-footer w-100">
                            <thead>
                                <tr>
                                    <th style="width: 5%;"></th>
                                    <th>Cost Code</th>
                                    <th>Name</th>
                                    <th style="width: 15%;"> Actions</th>
                                    
                                </tr>
                            </thead>
                            <tbody>`;
    list_task.forEach(task => {
        tareas += `
                                <tr >
                                    <td></td>
                                    <td> ${task.Tas_IDT}</td>
                                    <td> ${task.Nombre}</td>
                                    <td> 
                                        <a href="#"><i class="fas fa-pencil-alt ms-text-warning"></i></a>
                                        <i class="far fa-trash-alt ms-text-danger delete cursor-pointer" data-id="89" title="Delete"></i>
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
    view_detalle_proyectos($(this));
});

function view_detalle_proyectos(posision) {
    var detalle = posision.parent().parent().next();
    const verficar = posision.parent().parent().next().is(":visible");
    if (verficar) {
        console.log(true)
        detalle.hide();
    } else {
        console.log(false)
        detalle.show();
    }
}
/**
 **cambio de evento
 */
$(document).on("change", ".select_area", function () {
    console.log($(this).data('area_id'), $(this).val());
    $('#proyectos').html('');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-area`,
        data: {
            area_id: $(this).data('area_id'),
            floor_id: $(this).val(),
            proyectos: $('#multiselect_project').val()
        },
        dataType: 'json',
        success: function (response) {
            $('#table').html('');
            var table = table_proyectos(response)
            $('#table').append(table);
        },
    });
});

