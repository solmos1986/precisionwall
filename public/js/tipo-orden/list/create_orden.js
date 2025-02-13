
td_material = `
<tr>
    <td data-label="Material:">
        <input type="text" name="new_tipo[]" class="form-control form-control-sm tipo" value="material" readonly>
    </td>
    <td data-label="Unity:">
        <select class="form-control form-control-sm select_material" data-tipo="material" name="new_material_id[]"></select>
    </td>
    <td data-label="Unity:">
        <input type="text" name="new_unidad[]" class="form-control form-control-sm pre_unit" readonly>
    </td>
    <td data-label="Nota:">
        <input type="text" name="new_nota[]" autocomplete="off" class="form-control form-control-sm">
    </td>
    <td data-label="Quantity Ordered:">
        <input type="number" name="new_cantidad[]" step="1.0" min="0" value="0" class="form-control form-control-sm">
    </td>
    <td data-label="*">
    <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div>
</td>
</tr>
`;
td_equipo = `
<tr>
    <td data-label="Material:">
        <input type="text" name="new_tipo[]" class="form-control form-control-sm tipo" value="equipment" readonly>
    </td>
    <td data-label="Material:">
        <select class="form-control form-control-sm select_equipo" data-tipo="equipo" name="new_material_id[]"></select>
    </td>
    <td data-label="Unity:">
        <input type="text" name="new_unidad[]" class="form-control form-control-sm pre_unit" readonly>
    </td>
    <td data-label="Nota:">
        <input type="text" name="new_nota[]" autocomplete="off" class="form-control form-control-sm">
    </td>
    <td data-label="Quantity Ordered:">
        <input type="number" name="new_cantidad[]" step="1.0" min="0" value="0" class="form-control form-control-sm" 
    </td>
    <td data-label="*">
    <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div>
</td>
</tr>
`;

$(document).ready(function () {
    $(".add-material").click(function (e) {
        e.preventDefault();
        $("#none_tr_mat").remove();
        $("#table-material tbody").append(td_material);
        load_select_material();
    });
    $(".add-equipo").click(function (e) {
        e.preventDefault();
        $("#none_tr_mat").remove();
        $("#table-material tbody").append(td_equipo);
        load_select_equipos();
    });
});

$(document).on("click", "#create_orden_menu, #create_orden", function () {
    $('#fromCreateOrden').trigger('reset');
    $("#new_proyect").val(null).trigger('change')
    $("#modalCreateOrden").removeAttr("tabindex");
    $("#table-material tbody").html('');
    $("#new_orden_status").html('');
    $(".add-material").prop("disabled", true);
    $("#list_recojer").prop("disabled", true);
    $('#new_date_work').val(moment().format('MM/DD/YYYY HH:mm:ss'));
    $.ajax({
        type: "GET",
        url: `${base_url}/order/create`,
        dataType: "json",
        success: function (data) {
            var select_to = ``;
            data.forEach(status => {
                select_to += `<option value="${status.id}">${status.nombre}</option>`;
            });
            $('#new_orden_status').append(select_to);
        }
    });
    $('#modalCreateOrden').modal('show');
});

$(document).on("click", ".remove_material", function () {
    $(this).parents("tr").remove();
    $(".add-new").removeAttr("disabled");
    if ($('#table-material tbody tr').length == 0) {
        $("#table-material").append(`<tr id="none_tr_mat">
            <td scope="row" colspan="9" class="text-center text-bold">I don't add anything</td>
        </tr>`);
    }
});

function select2_proyectos() {
    $("#new_proyect")
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
            $("#list_recojer").prop("disabled", false);
            $('#create_orden_proyecto').val(e.params.data["text"]);
            $("#create_orden_proyecto").trigger("change");
            $("#table-material tbody").html('');
        });

}
function load_select_material() {
    $(".select_material")
        .select2({
            theme: "bootstrap4",
            disabled: false,
            ajax: {
                url: `${base_url}/tipo-material/${$("#new_proyect").val()}/materiales`,
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
function load_select_equipos() {
    $(".select_equipo")
        .select2({
            theme: "bootstrap4",
            disabled: false,
            ajax: {
                url: `${base_url}/tipo-material/${$("#new_proyect").val()}/equipos`,
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

$(document).on("click", ".save_orden", function () {
    let $form = $("#fromCreateOrden");
    $.ajax({
        type: "POST",
        url: $form.attr("action"),
        data: $form.serialize(),
        dataType: "json",
        success: function (data) {
            if (data.status == 'errors') {
                $alert = "";
                data.message.forEach(function (error) {
                    $alert += `* ${error}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'complete the following fields to continue:',
                    html: $alert,
                })
            }
            if (data.status == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCreateOrden').modal("hide");
                table.draw();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest.status)

        }
    });
});

select2_proyectos();

/* extra view material */
$(document).on("click", "#view_create_orden_materiales", function () {
    if ($('#ocultar_create_orde_materiales').is(":visible")) {
        $('#ocultar_create_orde_materiales').hide()
    } else {
        $('#ocultar_create_orde_materiales').show();
    }
    var i = $(this).find('i');
    console.log(i.hasClass('fa-eye-slash'));
    i.attr('class', i.hasClass('fa-eye-slash') ? 'fas fa-eye' : 'fas fa-eye-slash');
});

$('#ocultar_create_orde_materiales').hide();
/* evetos de recojer equipo */
$(document).on("change", "#list_recojer", function () {
    if (this.checked) {
        $("#new_orden_status").val(12);
        $(".add-material").prop("disabled", false);
        $('#table-material tbody').html('');
        $.ajax({
            type: "GET",
            url: `${base_url}/order/pick-up/${$('#new_proyect').val()}`,
            dataType: "json",
            success: function (data) {
                equipo_recojer = ``;
                data.forEach(material => {
                    equipo_recojer += `
                    <tr>
                        <td data-label="Material:">
                            <input type="text" name="new_tipo[]" class="form-control form-control-sm tipo" value="${material.Nombre}" readonly>
                        </td>
                        <td data-label="Unity:">
                            <select class="form-control form-control-sm select_material" data-tipo="material" name="new_material_id[]">
                            <option value="${material.Mat_ID}">${material.Denominacion}</option>
                            </select>
                        </td>
                        <td data-label="Unity:">
                            <input type="text" name="new_unidad[]" class="form-control form-control-sm pre_unit" value="${material.Unidad_Medida}" readonly>
                        </td>
                        <td data-label="Nota:">
                            <input type="text" name="new_nota[]" autocomplete="off" class="form-control form-control-sm" readonly>
                        </td>
                        <td data-label="Quantity Ordered:">
                            <input type="number" name="new_cantidad[]" class="form-control form-control-sm" value="${material.total}">
                        </td>
                        <td data-label="*">
                        <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div>
                        </td>
                    </tr>
                    `;
                });
                $('#table-material tbody').append(equipo_recojer);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });
    }
    else{
        $('#table-material tbody').html('');
        $(".add-material").prop("disabled", false);
        $("#new_orden_status").val(1);
    }
});

td_material = `
<tr>
    <td data-label="Material:">
        <input type="text" name="new_tipo[]" class="form-control form-control-sm tipo" value="material" readonly>
    </td>
    <td data-label="Unity:">
        <select class="form-control form-control-sm select_material" data-tipo="material" name="new_material_id[]"></select>
    </td>
    <td data-label="Unity:">
        <input type="text" name="new_unidad[]" class="form-control form-control-sm pre_unit" readonly>
    </td>
    <td data-label="Nota:">
        <input type="text" name="new_nota[]" autocomplete="off" class="form-control form-control-sm">
    </td>
    <td data-label="Quantity Ordered:">
        <input type="number" name="new_cantidad[]" step="1.0" min="0" value="0" class="form-control form-control-sm">
    </td>
    <td data-label="*">
    <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div>
    </td>
</tr>
`;