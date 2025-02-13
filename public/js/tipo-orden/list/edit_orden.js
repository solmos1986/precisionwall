
$(document).on("click", ".edit_orden", function () {
    $('#fromEditOrden').trigger('reset');
    $('#modalEditOrden').removeAttr("tabindex");
    $('#edit_orden_materiales tbody').html('');
    $.ajax({
        type: "GET",
        url: `${base_url}/order/edit/${$(this).data('id')}`,
        dataType: "json",
        success: function (data) {
            if (data.status == 'errors') {
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: data.message,
                });
                $('#segimiento_materiales tbody').html("");
            } else {
                $('#modalEditOrden').modal('show');
                console.log(data)
                //data modal orden
                $('#edit_orden_id').val(data.orden.id);
                $('#edit_job_name').val(data.orden.nombre_trabajo);
                $('#new_name_orden_vendor').val(data.orden.nombre_trabajo);
                $('#edit_orden_nota').val(data.orden.nota);
                $('#edit_created_by').val(data.orden.creado_por);
                var option = new Option(data.orden.nombre_proyecto, data.orden.Pro_ID, true, true);
                $('#edit_proyect').append(option).trigger('change');
                $('#edit_orden_status').val(data.orden.estatus_id);

                data.materiales.forEach(material => {
                    if (material.tipo_orden_material_id == 1) {
                        var edit_td_material = `
                            <tr>
                                <td data-label="Material:">
                                    <input type="text" name="edit_tipo[]" class="form-control form-control-sm tipo" value="${material.Nombre}" readonly>
                                </td>
                                <td data-label="Unity:">
                                    <select class="form-control form-control-sm edit_select_material" data-tipo="material" name="edit_material_id[]">
                                    <option value="${material.material_id}">${material.Denominacion}</option>
                                    </select>
                                    <input type="text" name="edit_orden_materiales_id[]" value="${material.id}" class="form-control form-control-sm pre_unit" hidden>
                                </td>
                                <td data-label="Unity:">
                                    <input type="text" name="edit_materiales_unidad[]" value="${material.Unidad_Medida}" class="form-control form-control-sm pre_unit" readonly>
                                </td>
                                <td data-label="Nota:">
                                    <input type="text" name="edit_nota[]" value="${material.nota_material==null ? '' :material.nota_material}" autocomplete="off" class="form-control form-control-sm">
                                </td>
                                <td data-label="Quantity Ordered:">
                                    <input type="number" name="edit_cantidad[]" step="1.0" min="0" value="${material.cant_registrada}" value="0" class="form-control form-control-sm">
                                </td>
                                <td data-label="*">
                                <button class="ms-btn-icon btn-danger btn-sm remove_edit_material" type="button"><i class="fas fa-trash-alt mr-0"></i></button>
                                </td>
                            </tr>
                        `;
                        $('#edit_orden_materiales tbody').append(edit_td_material);
                        edit_load_select_material();
                    }/*  else {
                        var edit_td_equipo = `
                        <tr>
                            <td data-label="Material:">
                                <input type="text" name="edit_tipo[]" class="form-control form-control-sm tipo" value="${material.nombre}" readonly>
                            </td>
                            <td data-label="Material:">
                                <select class="form-control form-control-sm edit_select_equipo" data-tipo="equipo" name="edit_material_id[]">
                                <option value="${material.material_id}">${material.Denominacion} selected </option>                             
                                </select>
                                <input type="text" name="edit_orden_materiales_id[]" value="${material.id}" class="form-control form-control-sm pre_unit" hidden>
                            </td>
                            <td data-label="Unity:">
                                <input type="text" name="edit_materiales_unidad[]" value="${material.Unidad_Medida}" class="form-control form-control-sm pre_unit" readonly>
                            </td>
                            <td data-label="Nota:">
                                <input type="text" name="edit_nota[]" value="${material.nota_material==null ? '' :material.nota_material}" autocomplete="off" class="form-control form-control-sm">
                            </td>
                            <td data-label="Quantity Ordered:">
                                <input type="number" name="edit_cantidad[]" step="1.0" min="0" value="${material.cant_registrada}" class="form-control form-control-sm">
                            </td>
                            <td data-label="*">
                            <button class="ms-btn-icon btn-danger btn-sm remove_edit_material" type="button" disabled><i class="fas fa-trash-alt mr-0"></i></button>
                        </td>
                        </tr>
                          `;
                        edit_load_select_equipos();

                        $('#edit_orden_materiales').append(edit_td_equipo);
                    } */

                });
            }
        },
    });
});

function edit_select2_proyectos() {
    $("#edit_proyect")
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/get_proyects`,
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
        })
        .on("select2:select", function (e) {
            $("#new_job_name").val(e.params.data["text"]).prop("disabled", false);
            $(".add-material").prop("disabled", false);
            $(".add-equipo").prop("disabled", false);
        });
}
function edit_load_select_material() {

    $(".edit_select_material")
        .select2({
            theme: "bootstrap4",
            disabled: false,
            ajax: {
                url: `${base_url}/tipo-material/${$("#edit_proyect").val()}/materiales`,
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
        })
        .on("select2:select", function (e) {
            $(this)
                .parents("tr")
                .find(".pre_unit")
                .val(e.params.data["Unidad_Medida"]);
            $(this)
                .parents("tr")
                .find(".tipo")
                .val(e.params.data["tipo_nombre"]);
        });
}
function edit_load_select_equipos() {
    $(".edit_select_equipo")
        .select2({
            theme: "bootstrap4",
            disabled: false,
            ajax: {
                url: `${base_url}/tipo-material/${$("#edit_proyect").val()}/equipos`,
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
        })
        .on("select2:select", function (e) {
            $(this)
                .parents("tr")
                .find(".pre_unit")
                .val(e.params.data["Unidad_Medida"]);
        });
}

$(document).on("click", ".save_update_orden", function () {
    console.log('pasando')
    $.ajax({
        type: "PUT",
        url: `${base_url}/order/update/${$('#edit_orden_id').val()}`,
        dataType: "json",
        data: $('#fromEditOrden').serialize(),
        success: function (data) {
            if (data.status == 'errors') {
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: data.message,
                });
            } else {
                if (data.status == 'ok') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $('#modalEditOrden').modal("hide");
                    table.draw();
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Successfully modified',
                        html: data.message,
                    });
                    $('#modalEditOrden').modal("hide");
                    table.draw();
                }
            }
        },
    });
});

edit_select2_proyectos();

$(document).ready(function () {
});
$(document).on("click", ".remove_edit_material", function () {
    console.log('eliminar')
    $(this).parents("tr").remove();
    //$(".add-new").removeAttr("disabled");
    if ($('#edit_orden_materiales tbody tr').length == 0) {
        $("#edit_orden_materiales").append(`<tr id="none_tr_mat">
            <td scope="row" colspan="9" class="text-center text-bold">I don't add anything</td>
        </tr>`);
    }
});
$(document).on("click", ".add-edit-material", function () {
    console.log('first')
    $("#none_tr_mat").remove();
    $("#edit_orden_materiales tbody").append(edit_td_material);
    edit_load_select_material();
});
edit_td_material = `
<tr>
    <td data-label="Material:">
        <input type="text" name="edit_tipo[]" class="form-control form-control-sm tipo" value="material" readonly>
    </td>
    <td data-label="Unity:">
        <select class="form-control form-control-sm edit_select_material" data-tipo="material" name="edit_material_id[]">
        </select>
    </td>
    <td data-label="Unity:">
        <input type="text" name="edit_materiales_unidad[]" value="" class="form-control form-control-sm pre_unit" readonly>
    </td>
    <td data-label="Nota:">
        <input type="text" name="edit_nota[]" value="" autocomplete="off" class="form-control form-control-sm">
    </td>
    <td data-label="Quantity Ordered:">
        <input type="number" name="edit_cantidad[]" step="1.0" min="0" value="" value="0" class="form-control form-control-sm">
    </td>
    <td data-label="*">
    <button class="ms-btn-icon btn-danger btn-sm remove_edit_material" type="button" disabled><i class="fas fa-trash-alt mr-0"></i></button>
    </td>
</tr>
`; 