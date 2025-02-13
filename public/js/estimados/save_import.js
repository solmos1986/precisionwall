
$(document).on("click", "#modal_import", function () {
    limpiar_campos_save_import();
    $("#modalSaveImport").removeAttr("tabindex");
    //ajax para validar si exite registro
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                var newOption = new Option(response.Nombre, response.proyecto_id, true, true);
                // Append it to the select
                $('#select2_proyectos').append(newOption).trigger('change');
            } else {
                $(`#select2_proyectos`).val(null).trigger('change');
            }
            $('#description').val(response.descripcion);
        }
    });

    $(`#select2_proyectos`).val(null).trigger('change');
    $('#fecha_registro').val(moment().format('MMMM Do YYYY, h:mm:ss a'));
    $('#modalSaveImport').modal('show');
});
function limpiar_campos_save_import() {
    $('#description').val('');
    $('#fecha_registro').val('');
}
$("#select2_proyectos").select2({
    theme: "bootstrap4",
    ajax: {
        url: `${base_url}/goal/get_proyects`,
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

});
$(document).on("click", "#save_import", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/save_import_project`,
        data: {
            estimado_id: $('#export_excel').data('imports'),
            proyecto_id: $('#select2_proyectos').val(),
            user_name: $('#user_name').val(),
            user_id: $('#user_id').val(),
            description: $('#description').val(),
            fecha_registro: moment().format('MM/DD/YYYY hh:mm:ss'),
        },
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
                $('#modalSaveImport').modal('hide');
            }
        }
    });
});


$(document).on("click", "#add_estimado_data_base", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/add-estimate-project`,
        dataType: 'json',
        data: {
            estimado_id: $('#export_excel').data('imports')
        },
        async: true,
        success: function (response) {
            console.log(response)
            if (response.status == 'ok') {
                let taskHTML = ``;
                response.data.tareas.map(val => {
                    taskHTML += `
                    <tr>
                        <td>${val.Are_IDT}</td>
                        <td>${val.nombre_area}</td>
                        <td>${val.Tas_IDT}</td>
                        <td>${val.Nombre}</td>
                        <td>${val.estado}</td>
                    </tr>
                    `;
                });
                $('#proyecto_add_estimado').html('');
                $('#proyecto_add_estimado').text(`Affected tasks - ${response.data.proyecto.Nombre}`);
                //recrear tabla
                $('#list-add-import').DataTable().destroy();
                $('#list-add-import tbody').empty();
                $('#content_add_estimado').append(taskHTML);
                $('#ModalAddEstimado').modal('show');
                $('#list-add-import').DataTable({
                    pageLength: 50,
                    order: [
                        [4, "asc"]
                    ],
                });


                /*  Swal.fire({
                     title: 'Are you sure?',
                     html: `
                         <h5>Affected tasks</h5>
                         <table id="list-proyectos" class="table">
                         <thead>
                             <tr>
                                 <th>Area</th>
                                 <th>Name area</th>
                                 <th>Cost code</th>
                                 <th>Name Task</th>
                                 <th>Descripcion</th>
                             </tr>
                         </thead>
                         <tbody>
                             ${taskHTML}
                         </tbody>
                     </table>
                     `,
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#3085d6',
                     cancelButtonColor: '#d33',
                     confirmButtonText: 'yes, continue with the import!'
                 }).then((result) => {
                     if (result.isConfirmed) {
                         $.ajax({
                             type: 'POST',
                             url: `${base_url}/project-files/save-estimate-project`,
                             dataType: 'json',
                             async: true,
                             success: function (response) {
                                 if (response.status == 'ok') {
                                     //task_import_duplicados(response.data);
                                 } else {
                                     Swal.fire({
                                         position: 'center',
                                         icon: 'success',
                                         title: response.message,
                                         showConfirmButton: false,
                                         timer: 1500
                                     });
                                 }
                             }
                         });
                     }
                 }) */
            } else {
                Swal.fire({
                    title: 'you need to save the import?',
                    text: "save the import",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#modal_import").trigger("click");
                    }
                });
            }
        }
    });
});

$(document).on("click", "#save_add_estimados", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/save-estimate-project`,
        dataType: 'json',
        data: {
            estimado_id: $('#export_excel').data('imports')
        },
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    });

});

var listAddImport = $('#list-add-import').DataTable();