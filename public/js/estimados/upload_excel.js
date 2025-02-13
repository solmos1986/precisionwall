//save event
function animacion_load() {
    $('#load-data-tbody').html(`
    <tr  >
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
$('#upload_excel').click(function () {
    var data = new FormData();
    var new_docs = $('#doc_excel')[0].files[0];
    data.append("doc_excel", new_docs);
    limpiar_constante();
    //animacion
    animacion_load();
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/leer_excel`,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        async: true,
        success: function (response) {
            if (response.status == 'ok') {
                upload_datatable(response.data.imports, response.data.totales)
                var llaves = [];
                response.data.imports.forEach(campo => {
                    llaves.push(campo.id);
                });
                $('#export_excel').data('imports', response.data.totales.id)
                $('#export_excel_sov').data('imports', response.data.totales.id)
                //muestra de constante 
                $('#labor_cost').val(response.data.totales.labor_cost);
                $('#index_prod').val(response.data.totales.index_prod);
            } else {
                Swal.fire({
                    width: 950,
                    title: 'An error has occurred!',
                    text: 'Please follow the format, as in the image.',
                    imageUrl: `${base_url}/img/ejemplo.png`,
                    imageWidth: 900,
                    imageHeight: 450,
                    imageAlt: 'Error image',
                })
            }

        }
    });
});
$('#doc_excel').change(function () {
    var filename = $(this).val().split('\\').pop();

    $('#label_doc_excel').text(filename);
});

function cargar_uploads() {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/downloads`,
        dataType: 'json',
        contentType: false,
        processData: false,
        async: true,
        success: function (data) {
            var HTML_res = ``;
            data.forEach(upload => {
                HTML_res += `
                <li class="ms-list-item media">
                  <label class="ms-checkbox-wrap">
                    <input type="radio" name="radioExample" value="">
                    <i class="ms-checkbox-check"></i>
                  </label>
                  <span> </span>
                    <div class="media-body mt-1">
                        <h4>${upload.nombre}</h4>
                        <a href="#"><span class="fs-12">${upload.sincronizacion == 0 ? 'no sync' : 'sync'}</span></a>
                    </div>
                    <button type="button" class="btn btn-success btn-sm descarga" name="button">Download
                    </button>
                </li>`;
            });
            $('#list_uploads').append(HTML_res);
        }
    });
}
function upload_datatable(imports, total) {
    $('#load-data-tbody').html('');
    tbodyHTML = ``;
    imports.forEach((area, index) => {
        detailHTML = ``;
        if (area.superficies) {
            nombre_descripcion = ``;
            //superficies
            area.superficies.forEach(superficie => {
                superficie.tareas.forEach(tarea => {
                    metodosHTML = ``;
                    tarea.metodos.forEach(metodo => {
                        metodosHTML += `
                        <option value="${metodo.id}" ${metodo.id == tarea.estimado_metodo_id ? 'selected' : ''}>${metodo.nombre}</option>`;
                    });
                    detailHTML += `
                        <tr style="border-top: 1px solid #ffffff; background-color: ${verificar_procedimiento(superficie.procedimiento, superficie.miselaneo)};" data-estimado_use_metodo_id="${superficie.id}">
                            <td> </td>
                            <td>
                                <i class="fas fa-pencil-alt ms-text-warning cursor-pointer modal_edit_tarea" title="Edit Fields" data-estimado_use_import_id="${superficie.id}"></i>       
                            </td>
                            <td>${superficie.cost_code}</td>
                            <td colspan='1'>
                            ${superficie.cc_descripcion}
                                <div class="form-group row">
                                    <div class="col-sm-9">
                                        <select data-estimado_use_metodo_id="${superficie.id}" class="form-control form-control-sm metodo" style="width:auto;" required>
                                           ${metodosHTML} 
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>${superficie.cc_butdget_qty}</td>
                            <td>${superficie.um}</td>
                            <td>${superficie.of_coast}</td>
                            <td>${superficie.pwt_prod_rate}</td>
                            <td>${superficie.estimate_hours}</td>
                            <td>${superficie.estimate_labor_cost}</td>
                            <td>${superficie.material_or_equipment_unit_cost}</td>
                            <td>${superficie.material_spread_rate_per_unit}</td>
                            <td>${superficie.mat_qty_or_galon}</td>
                            <td>${superficie.mat_um}</td> 
                            <td>${superficie.material_cost}</td>
                            <td>${superficie.price_total}</td>
                            <td>${superficie.buscontract_cost}</td> 
                            <td>${superficie.equipament_cost}</td>      
                            <td>${superficie.other_cost}</td>             
                        </tr>
                    `;
                });
            });
        } else {
            detailHTML += `
        <tr>
                <td> </td>
                <td></td>
                <td></td>
                <td colspan='1'></td>   
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan='5'>
                Data no encontrada
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        `;
        }
        //areass
        tbodyHTML += `
        <tr data-estimado_use_metodo_id="${area.id}">
            <td >
                ${verificar_copia(area.area) ? `<i class="fa fa-copy  ms-text-primary modal_duplicar cursor-pointer " data-estimado_use_metodo_id="${area.id}" title="Copy"></i>` : ''}
                <i class="far fa-trash-alt ms-text-danger cursor-pointer eliminar" data-estimado_use_import_id="${area.id}" title="Delete"></i>
                <i class="fas fa-pencil-alt ms-text-warning cursor-pointer modal_edit_nombre" title="Edit" data-nombre_area="${area.nombre_area}"></i>       
            </td>
            <td colspan="5">${area.nombre_area}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
            ${detailHTML}
        `;
    });
    var totales = `
    <tr style="background:#fafbe3;">
        <td><strong>TOTAL:</strong></td>
        <td></td>
        <td></td>
        <td>Total cost: $${total.total_cost}</td>
        <td>Mark Up:${(total.mark_up)}%</td>
        <td></td>
        <td></td>
        <td></td>
        <td>${total.estimated_hours}</td>
        <td>${total.estimated_labor_hours}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>${total.material_cost}</td>
        <td>${total.price_total}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    `;
    $('#load-data-tbody').append(totales + tbodyHTML);
}
function cambio_metodo(estimado_use_metodo_id, metodo_id, $campo) {
    $.ajax({
        type: 'PUT',
        url: `${base_url}/project-files/cambio_metodo/${estimado_use_metodo_id}`,
        data: {
            estimado_metodo_id: metodo_id
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales);
        }
    });
}
function eliminar(estimado_use_import_id, $campo) {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/eliminar/${estimado_use_import_id}`,
        data: {
            estimado_use_import_id: estimado_use_import_id,
            estimado_id: $('#export_excel').data('imports')
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales);
        }
    });
}

$(document).on("click", ".eliminar", function () {
    eliminar($(this).data('estimado_use_import_id'), $(this));
});
/*descarga excel */
$("#export_excel").on('click', function (evt) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                $('#descargar_excel').attr("action", `${base_url}/project-files/export-excel?imports=${$('#export_excel').data('imports')}`);
                $("#descargar_excel").submit();
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
/*descarga excel */
$("#export_excel_sov").on('click', function (evt) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                $('#descargar_excel').attr("action", `${base_url}/project-files/export-excel-sov?imports=${$('#export_excel').data('imports')}}`);
                $("#descargar_excel").submit();
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
/*descarga txt */
$("#export_txt").on('click', function (evt) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                $('#descargar_excel').attr("action", `${base_url}/project-files/export-txt?imports=${$('#export_excel').data('imports')}`);
                $("#descargar_excel").submit();
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
/*descarga excel */
$("#export_stp").on('click', function (evt) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                $('#descargar_excel').attr("action", `${base_url}/project-files/export-txt-stp?imports=${$('#export_excel').data('imports')}`);
                $("#descargar_excel").submit();
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
$("#export_completado").on('click', function (evt) {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                $('#descargar_excel').attr("action", `${base_url}/project-files/export-excel-completed?imports=${$('#export_excel').data('imports')}`);
                $("#descargar_excel").submit();
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
function validar_import_project() {
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/get-imports-project/${$('#export_excel').data('imports')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.proyecto_id != 0) {
                return false;
            } else {
                return true
            }
        }
    });
}
$(document).on("click", ".modal_duplicar", function () {
    $('#ModalDuplicar').modal('show');
    $('#title_modal_duplicar').text(`Copy`);
    $('#estimado_use_import_id').val($(this).data('estimado_use_metodo_id'));
    $('#num_copia').val('');
});

$(document).on("click", "#duplicar", function () {
    var numero_copias = $('#num_copia').val();
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/duplicar/${$('#estimado_use_import_id').val()}`,
        data: {
            estimado_id: $('#export_excel').data('imports'),
            estimado_use_import: $('#estimado_use_import_id').val(),
            numero_copias: numero_copias
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales);
        }
    });
});
$(document).on("change", ".metodo", function () {
    //console.log('detect evento metodo')
    cambio_metodo($(this).data('estimado_use_metodo_id'), $(this).val(), $(this));
});

function verificar_copia(area) {
    var verificando = area.split(' ')
    //si es copia devuelve true
    if (verificando.length > 1) {
        return false;
    } else {
        return true;
    }
}
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
$(document).on("change", "#labor_cost", function () {

    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-const`,
        data: {
            labor_cost: $('#labor_cost').val(),
            estimado_id: $('#export_excel').data('imports')
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales)
        }
    });
});
$(document).on("change", "#index_prod", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/update-index-of-prod`,
        data: {
            index_prod: $('#index_prod').val(),
            estimado_id: $('#export_excel').data('imports')
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales)
        }
    });
});

/*Edit tarea */
$(document).on("click", ".modal_edit_tarea", function () {
    limpiar_edit_import();
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/edit-import/${$(this).data('estimado_use_import_id')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            $('#modalEditModalTask').modal('show');
            $('#title_modal_edit_import').text(`Edit ${response.cost_code} ${response.cc_descripcion}`);
            //campos
            $('#estimado_use_import_id').val(response.id)
            $('#CC_budget_QTY').val(response.cc_butdget_qty);
            $('#um').val(response.um);
            $('#of_coast').val(response.of_coast);
            $('#pwt_pro_rate').val(response.pwt_prod_rate);
            $('#estimate_hours').val(response.estimate_hours);
            $('#estimate_labor_hours').val(response.estimate_labor_cost);
            $('#material_or_equipment_unit_cost').val(response.material_or_equipment_unit_cost);
            $('#material_spread_rate_per_unit').val(response.material_spread_rate_per_unit);
            $('#mat_qty_or_galon').val(response.mat_qty_or_galon);
            $('#mat_um').val(response.mat_um);
            $('#material_cost').val(response.material_cost);
            $('#preci_total').val(response.price_total);
            $('#mark_up').val(response.mark_up);
            $('#sub_contrac_cost').val(response.buscontract_cost);
            $('#equipment_cost').val(response.equipament_cost);
            $('#other_cost').val(response.other_cost);
            $('#porcentaje').val(response.porcentaje);
        }
    });
});
/*cualculo automatico */
$("#estimate_hours").keyup(function () {
    $('#estimate_labor_hours').val($(this).val()*$('#labor_cost').val())
});


$(document).on("click", "#update_import", function () {
    $.ajax({
        type: 'PUT',
        url: `${base_url}/project-files/update-import/${$('#estimado_use_import_id').val()}`,
        data: $('#fromUpdateImport').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            upload_datatable(response.data.imports, response.data.totales)
        }
    });
    $('#modalEditModalTask').modal('hide');
});
/* edit name area*/
$(document).on("click", ".modal_edit_nombre", function () {
    limpiar_update_area();
    const nombre_area = $(this).data('nombre_area');
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/get-area`,
        data: {
            estimado_id: $('#export_excel').data('imports'),
            nombre_area: nombre_area
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            $('#ModalEditArea').modal('show');
            $('#title_modal_update_area').text(`Edit Area`);
            //campos
            $('#nombre_area').val(response.nombre_area);
            $('#nombre_area_anterior').val(response.nombre_area);
        }
    });
});
$(document).on("click", "#update_area", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/validate-update-area`,
        data: {
            nombre_area: $('#nombre_area').val(),
            nombre_area_anterior: $('#nombre_area_anterior').val(),
            estimado_id: $('#export_excel').data('imports')
        },
        dataType: 'json',
        async: true,
        success: function (response) {
            if (response.data == 'existe') {
                Swal.fire({
                    title: 'Sure to join areas?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //OK
                        $.ajax({
                            type: 'PUT',
                            url: `${base_url}/project-files/update-area`,
                            data: {
                                nombre_area: $('#nombre_area').val(),
                                nombre_area_anterior: $('#nombre_area_anterior').val(),
                                estimado_id: $('#export_excel').data('imports')
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
                                    $('#ModalEditArea').modal('hide');
                                    upload_datatable(response.data.imports, response.data.totales)
                                }
                            }
                        });
                    }
                });
            } else {
                $.ajax({
                    type: 'PUT',
                    url: `${base_url}/project-files/update-area`,
                    data: {
                        nombre_area: $('#nombre_area').val(),
                        nombre_area_anterior: $('#nombre_area_anterior').val(),
                        estimado_id: $('#export_excel').data('imports')
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
                            $('#ModalEditArea').modal('hide');
                            upload_datatable(response.data.imports, response.data.totales)
                        }
                    }
                });
            }
        }
    });

});
function limpiar_update_area() {
    $('#nombre_area').val('');
}
function limpiar_constante() {
    $('#index_prod').val('');
    $('#labor_cost').val('');
}
function limpiar_edit_import() {
    $('#estimado_use_import_id').val('');
    $('#CC_budget_QTY').val('');
    $('#um').val('');
    $('#of_coast').val('');
    $('#pwt_pro_rate').val('');
    $('#estimate_hours').val('');
    $('#estimate_labor_hours').val('');
    $('#material_or_equipment_unit_cost').val('');
    $('#material_spread_rate_per_unit').val('');
    $('#mat_qty_or_galon').val('');
    $('#mat_um').val('');
    $('#material_cst').val('');
    $('#preci_total').val('');
    $('#mark_up').val('');
    $('#sub_contrac_cost').val('');
    $('#equipment_cost').val('');
    $('#other_cost').val('');
    $('#porcentaje').val('');
}

$(document).on("change", ".select_labor_cost", function () {
    $('#labor_cost').val('');
    $('#labor_cost').val($(this).val());
    $("#labor_cost").trigger("change");
});