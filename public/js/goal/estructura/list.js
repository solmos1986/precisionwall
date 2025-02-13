var table_proyecto = $('#lista_proyectos').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    scrollY: "550px",
    scrollX: true,
    scrollCollapse: true,
    ajax: `${base_url}/goal-project/data-table?multiselect_project=${$('#multiselect_project').val()}&status=${$('#status').val()}&from_date=${$('#from_date').val()}&from_date=${$('#to_date').val()}&cargo=${$('#cargo').val()}&filtro=${$('#filtro').val()}`,
    order: [
       
    ],
    columns: [
        {
            class: "details-control",
            orderable:false,
            data: null,
            defaultContent: ""
        },
        {
            data: 'Codigo',
            name: "Codigo"
        },
        {
            data: 'Nombre',
            name: "Nombre"
        },
        {
            data: 'tipo',
            name: "tipo"
        },
        {
            data: 'nombre_empresa',
            name: "nombre_empresa"
        },
        {
            data: 'nombre_project_manager',
            name: "nombre_project_manager"
        },
        {
            data: 'nombre_foreman',
            name: "nombre_foreman"
        },
    ],
    lengthMenu: [
        [13, 26, -1],
        [13, 26, "All"]
    ],
});
$(document).ready(function () {
    // Array to track the ids of the details displayed rows
    var detailRows = [];

    $('#lista_proyectos tbody').on('click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table_proyecto.row(tr);
        var idx = $.inArray(tr.attr('id'), detailRows);

        if (row.child.isShown()) {
            tr.removeClass('details');
            row.child.hide();
            // Remove from the 'open' array
            detailRows.splice(idx, 1);
        } else {
            tr.addClass('details');
            /*data tareas*/
            $.ajax({
                type: 'GET',
                url: `${base_url}/goal-structure/surfaces/${row.data().Pro_ID}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    row.child(format(response.data.superficies, row.data().Pro_ID)).show();
                }
            });
            // Add to the 'open' array
            if (idx === -1) {
                detailRows.push(tr.attr('id'));
            }
        }
    });
    // On each draw, loop over the `detailRows` array and show any child rows
    table_proyecto.on('draw', function () {
        $.each(detailRows, function (i, id) {
            $('#' + id + ' td.details-control').trigger('click');
        });
    });
});

function format(data, proyecto_id) {
    //standares
    var superficies = ``;
    data.forEach(superficie => {
        superficies += `
       <tr>
            <td>
                <input type="text" class="form-control w-30 form-control-sm code" data-input="code" 
                id="code" name="code" placeholder="Code" value="${superficie.codigo}" readonly
                autocomplete="off" >
            </td>
            <td> <input type="text" class="form-control w-30 form-control-sm nombre" data-input="nombre" 
                id="nombre" name="nombre" placeholder="Name" value="${superficie.nombre}" readonly
                autocomplete="off" >
            </td>
            <td> <input type="text" class="form-control w-30 form-control-sm description" data-input="description" 
                id="description" name="description" placeholder="Description" value="${superficie.descripcion}"
                autocomplete="off" readonly>
            </td>
            <td>
                <i class="fas fa-eye ms-text-primary cursor-pointer view_material" title="View Materials" data-proyecto_id="${proyecto_id}" data-surface_id="${superficie.id}"></i>
                <i class="fas fa-pencil-alt ms-text-warning cursor-pointer edit_surface" title="Edit Surface" data-proyecto_id="${proyecto_id}" data-surface_id="${superficie.id}"></i>
                <i class="far fa-trash-alt ms-text-danger delete_surface cursor-pointer" data-proyecto_id="${proyecto_id}" data-surface_id="${superficie.id}" title="Delete Surface"></i>
            </td>
        </tr>
       `;
    });

    return `
    <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-11">
            <div class="row">
                <div class="col-md-6">
                    <h6 for="date_work" class="form-label d-inline p-0">List Surfaces</h6>
                </div>
                <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                    <button type="button" class="btn btn-primary btn-sm m-1 add_surfaces" data-proyecto_id="${proyecto_id}">
                        Add Surface
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="" class="table table-hover mt-2">
                    <thead style="background-color:#ffffff, color:black">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${superficies}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    `;
}
//añadir surface
$(document).on("click", ".add_surfaces", function () {
    $(this).prop('disabled', true)
    const proyecto_id = $(this).data('proyecto_id');
    var task = `
            <tr>
                <td>
                    <input type="text" class="form-control w-30 form-control-sm code" data-input="code" 
                    id="code" name="code" placeholder="Code" value="" 
                    autocomplete="off" >
                </td>
                <td> <input type="text" class="form-control w-30 form-control-sm nombre" data-input="nombre" 
                    id="nombre" name="nombre" placeholder="Name" value="" 
                    autocomplete="off" >
                </td>
                <td> <input type="text" class="form-control w-30 form-control-sm description" data-input="description" 
                    id="description" name="description" placeholder="Description" value=""
                    autocomplete="off" >
                </td>
                <td>
                    <i class="far fa-check-circle ms-text-primary cursor-pointer save_surfaces" title="save Surface" data-proyecto_id="${proyecto_id}"></i>
                    <i class="far fa-trash-alt ms-text-danger new_delete_surface cursor-pointer" data-herramienta_id="15" title="Delete Surface"></i>
                </td>
            </tr>
                `;
    //añadir nuevo campo
    $(this).parent().parent().next().children().find('tbody').append(task);
    select_material(proyecto_id);
    $("#nuevo_quantity").focus();
});
$(document).on("click", ".new_delete_surface", function (event) {
    $(this).parent().parent().remove();
    $('.add_surfaces').prop('disabled', false)
});
$(document).on("click", ".save_surfaces", function (event) {
    var posicion=$(this);
    var proyecto_id=$(this).data('proyecto_id')
    //valores
    var codigo;
    var nombre;
    var descripcion;

    $(this).parent().parent().children().each(function (index, data) {
        switch (index) {
            case 0:
                codigo = $(data).find('input').val();
                break;
            case 1:
                nombre = $(data).find('input').val();
                break;
            case 2:
                descripcion = $(data).find('input').val();
                break;
            default:
                break;
        }
    });

    $.ajax({
        type: 'POST',
        url: `${base_url}/goal-structure/surfaces`,
        dataType: 'json',
        data: {
            codigo: codigo,
            nombre: nombre,
            descripcion: descripcion,
            proyecto_id: proyecto_id
        },
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCreateViewMateriales').modal('hide');
                var antes=posicion.parent().parent().parent().parent().parent().parent().parent().parent();
                var eliminar=posicion.parent().parent().parent().parent().parent().parent().parent().remove();
                //console.log(posicion.parent().parent().parent().parent().parent().parent().parent().prev());
                antes.append(format(response.data.superficies, response.data.proyecto_id));
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
//editar
$(document).on("click", ".edit_surface", function() {
    //var superficie_id=$(this).data('surface_id');
    let tr = $(this).parent().parent().children();
    tr.each(function(index) {
        if ($(this).find('input').val() != undefined) {
            console.log($(this).find('input').prop("readonly", false)) //
        }
    });

    $(this).removeClass('fas fa-pencil-alt ms-text-warning edit_surface');
    $(this).addClass('far fa-check-circle ms-text-primary update_surface');
});
$(document).on("click", ".update_surface", function() {
    var posicion=$(this);
    var proyecto_id=$(this).data('proyecto_id');
    var superficie_id=$(this).data('surface_id');
   
    //valores
    var codigo;
    var nombre;
    var descripcion;

    $(this).parent().parent().children().each(function (index, data) {
        switch (index) {
            case 0:
                codigo = $(data).find('input').val();
                break;
            case 1:
                nombre = $(data).find('input').val();
                break;
            case 2:
                descripcion = $(data).find('input').val();
                break;
            default:
                break;
        }
    });

    $.ajax({
        type: 'PUT',
        url: `${base_url}/goal-structure/surfaces/${superficie_id}`,
        dataType: 'json',
        data: {
            codigo: codigo,
            nombre: nombre,
            descripcion: descripcion,
            proyecto_id: proyecto_id
        },
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCreateViewMateriales').modal('hide');
                var antes=posicion.parent().parent().parent().parent().parent().parent().parent().parent();
                var eliminar=posicion.parent().parent().parent().parent().parent().parent().parent().remove();
                //console.log(posicion.parent().parent().parent().parent().parent().parent().parent().prev());
                antes.append(format(response.data.superficies, response.data.proyecto_id));
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
//eliminar
$(document).on("click", ".delete_surface", function() {
    var posicion=$(this);
    var superficie_id=$(this).data('surface_id');
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/goal-structure/surfaces/${superficie_id}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCreateViewMateriales').modal('hide');
                var antes=posicion.parent().parent().parent().parent().parent().parent().parent().parent();
                var eliminar=posicion.parent().parent().parent().parent().parent().parent().parent().remove();
                //console.log(posicion.parent().parent().parent().parent().parent().parent().parent().prev());
                antes.append(format(response.data.superficies, response.data.proyecto_id));
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
