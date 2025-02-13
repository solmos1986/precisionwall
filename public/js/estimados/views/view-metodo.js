
$(document).on('click', '.create_metodo', function (event) {
    $('#modalMetodo').modal('show');
    limpiar_campos_metodos();
    $('#title_modal_metodo').text(`Create Method`);
    $('#save_metodo').removeClass('update_metodo');
    $('#save_metodo').addClass('save_metodo');
    $('#metodo_estandar_id').val($(this).data('estandar_id'));
});
$(document).on("click", ".save_metodo", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/method-create`,
        data: $('#form_metodo').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.status == 'errors') {
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
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalMetodo').modal('hide');
                actualizar_detail(response.data,response.data[0].estimado_superficie_id);
            }
        }
    });
});
$(document).on("click", ".edit_metodo", function () {
    //limpiar campos
    $('#save_metodo').removeClass('save_metodo');
    $('#save_metodo').addClass('update_metodo');
    limpiar_campos_metodos();
    $('#title_modal_metodo').text(`Edit Method`);
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/method-edit/${$(this).data('metodo_id')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            $('#nombre_metodo').val(response.data.nombre);
            $('#unidad_medida').val(response.data.unidad_medida);
            $('#materal_spread').val(response.data.materal_spread);
            $('#material_cost_unit').val(response.data.material_cost_unit);
            $('#material_unit_med').val(response.data.material_unit_med);
            $('#num_coast').val(response.data.num_coast);
            $('#rate_hour').val(response.data.rate_hour);
            $('#mark_up').val(response.data.mark_up);
            $('#process option').each(function() {
                if($(this).val() == response.data.procedimiento) {
                    $(this).prop("selected", true);
                }
            });
            if (response.data.defauld=='y') {
                $('#default').prop('checked',true);
            }else{
                $('#default').prop('checked',false);
            }
            
            $('#metodo_estandar_id').val(response.data.estimado_estandar_id);
            $('#metodo_id').val(response.data.id);
            $('#cod_category_labor').val(response.data.cod_category_labor);
            $('#cod_category_material').val(response.data.cod_category_material);
            $('#modalMetodo').modal('show');
        }
    });
});
$(document).on("click", ".update_metodo", function () {
    const metodo_id = $('#metodo_id').val();
    $.ajax({
        type: 'PUT',
        url: `${base_url}/project-files/method-update/${metodo_id}`,
        dataType: 'json',
        data: $('#form_metodo').serialize(),
        async: true,
        success: function (response) {
            if (response.status == 'errors') {
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
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalMetodo').modal('hide');
                actualizar_detail(response.data,response.data[0].estimado_superficie_id);
            }
        }
    });
});
$(document).on("click", ".delete_metodo", function () {
    const metodo_id = $(this).data('metodo_id')
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
                url: `${base_url}/project-files/method-delete/${metodo_id}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                    actualizar_detail(response.data,response.data[0].estimado_superficie_id);
                }
            });
        }
    })
});

function limpiar_campos_metodos() {
    $('#nombre_metodo').val('');
    $('#unidad_medida').val('');
    $('#materal_spread').val('');
    $('#material_cost_unit').val('');
    $('#material_unit_med').val('');
    $('#num_coast').val('');
    $('#rate_hour').val('');
    $('#metodo_estandar_id').val('');
    $('#mark_up').val('');
    $('#mark_up').val('');
    $('#cod_category_labor').val('');
    $('#cod_category_material').val('');
    $('#default').prop('checked',false);
    $('#process option').each(function() {      
        $(this).prop("selected", false);
    });
}

//actualizar detail
function actualizar_detail(data, superficie_id) {
    //standares
    var estandares = ``;
    data.forEach(estandar => {
        //metodos
        var metodos=``;
        estandar.metodos.forEach(metodo => {
                metodos+=`
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>${metodo.nombre}</td>
                    <td>${metodo.unidad_medida}</td>
                    <td>${metodo.materal_spread}</td>
                    <td>${metodo.material_cost_unit}</td>
                    <td>${metodo.material_unit_med}</td>
                    <td>${metodo.num_coast}</td>
                    <td>${metodo.rate_hour}</td>
                    <td>${metodo.mark_up== null ? '' : metodo.mark_up}</td>
                    <td>${metodo.defauld =='y' ? `<span class="badge badge-pill badge-primary">Yes</span>` : ` ` }</td>
                    <td>
                        <i class="fas fa-pencil-alt ms-text-warning cursor-pointer edit_metodo" title="Edit Method" id="create_standar" data-metodo_id="${metodo.id}"></i>
                        <i class="far fa-trash-alt ms-text-danger delete_metodo cursor-pointer" data-metodo_id="${metodo.id}" title="Delete Method"></i>
                    </td>
                </tr>
                `;
        });
        estandares += `
       <tr>
            <td>${estandar.nombre}</td>
            <td>${estandar.codigo}</td>
            <td>${estandar.Nom_Sov}</td>
            <td>
                <i class="fas fa-pencil-alt ms-text-warning cursor-pointer edit_standar" title="Edit Task" data-estandar_id="${estandar.id}"></i>
                <i class="far fa-trash-alt ms-text-danger delete-superficie cursor-pointer delete_standar" data-estandar_id="${estandar.id}" title="Delete Task"></i>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <button type="button" class="btn btn-primary btn-sm mt-0 create_metodo" data-estandar_id="${estandar.id}">
                    Add Method
                </button>
            </td>
        </tr>
        ${metodos}
       `;
    });
    var data =`
    <div class="col-md-1">
    </div>
    <div class="col-md-11">
        <div class="row">
            <div class="col-md-6">
               
            </div>
            <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                <button type="button" id="crear_task" class="btn btn-primary btn-sm mt-0" data-superficie_id="${superficie_id}">
                    Add Task
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="" class="table table-hover mt-2">
                <thead style="background-color:#ffffff, color:black">
                    <tr>
                        <th>Task</th>
                        <th>Cost Code</th>
                        <th>Sov</th>
                        <th>Method</th>
                        <th>Unit Med.</th>
                        <th>M. Spread</th>
                        <th>M. Cost Unit</th>
                        <th>M. Unit Med.</th>
                        <th>Num. Coast</th>
                        <th>Rate Hours</th>
                        <th>% Cost</th>
                        <th>Default</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    ${estandares}
                </tbody>
            </table>
        </div>
    </div>
    `;
    $(`#${superficie_id}`).html('');
    $(`#${superficie_id}`).append(data);
}