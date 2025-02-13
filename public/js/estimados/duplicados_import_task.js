//impor load database
$(document).on('click', '#import_data_base', function (event) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'yes, continue with the import!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'GET',
                            url: `${base_url}/project-files/import-database/${$('#export_excel').data('imports')}`,
                            dataType: 'json',
                            async: true,
                            success: function (response) {
                                if (response.data.length > 0) {
                                    task_import_duplicados(response.data);
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
                })
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

function task_import_duplicados(repetidos) {
    $('#modalDuplicadosImportDataBse').modal({ backdrop: 'static', keyboard: false });
    $('#modal_duplicados_import_database').text(`Duplicate task in import database`);
    $('#fromUpdateImportDatabase').html('');
    //*capturando selects

    var val_repetidos = ``;
    repetidos.forEach(repetido => {
        //*capturando selects
        var options_nombre = ``;
        var options_horas_estimadas = ``;
        repetido.duplicados.forEach(valores => {
            options_nombre += `
                    <option value="${valores.nombre_descripcion}">(${valores.tipo}) ${valores.nombre_descripcion}</option>`;
            options_horas_estimadas += `
                    <option value="${valores.estimate_hours}" >(${valores.tipo}) ${valores.estimate_hours}</option>`;
        });
        val_repetidos += `
        <div class="ms-panel" style="margin-bottom: 10px;">
            <div class="ms-panel-header" style="padding: 0.5rem">
                <h6 style="font-size:14px"><strong>Area:</strong> ${repetido.area.Nombre}&nbsp; &nbsp;&nbsp;&nbsp; <strong>Cod:</strong> ${repetido.area.Tas_IDT}</h6>
            </div>

            <input type="number" class="form-control form-control-sm" id="task_id"
            name="task_id[]" value="${repetido.area.id}" hidden>

            <input type="text" class="form-control form-control-sm" id="area_code"
            name="area_code[]" value="${repetido.duplicados[0].area}" hidden>

            <input type="number" class="form-control form-control-sm" id="import_id"
            name="import_id[]" value="${repetido.duplicados[0].id}" hidden>

            <input type="text" class="form-control form-control-sm" id="sov_id"
            name="sov_id[]" value="${repetido.duplicados[0].sov_id}" hidden>
            
            <input type="text" class="form-control form-control-sm" id="sov_descripcion"
            name="sov_descripcion[]" value="${repetido.duplicados[0].Nom_Sov}" hidden>

            <div class="ms-panel-body p-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row mb-1">
                            <label for="name"
                                class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                            <div class="col-sm-9">
                                <select class="form-control form-control-sm select_nombre_area" name="nombre_area[]"
                                    id="action">
                                    ${options_nombre}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row mb-1">
                            <label for="duplicado_import_hours_estimadas"
                                class="col-sm-3 col-form-label col-form-label-sm">Hours
                                Estimate:</label>
                            <div class="col-sm-9">
                                <select class="form-control form-control-sm select_nombre_horas" name="horas_estimadas[]"
                                    id="action">
                                    ${options_horas_estimadas}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    });
    $('#fromUpdateImportDatabase').append(val_repetidos);
}
$(document).on('click', '#update_import_database', function (event) {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-import-duplicado-database`,
        data: $('#fromUpdateImportDatabase').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            $('#modalDuplicadosImportDataBse').modal('hide');
        }
    });
});

$(document).on('change', '.select_nombre_area', function (event) {
    $($(this).parent().find('input')).val($(this).find("option:selected").data('tipo'))
});