
td_material = `
    <tr>
        <td data-label="Material:">
            <input type="text" name="tipo[]" class="form-control form-control-sm tipo" value="material" readonly>
        </td>
        <td data-label="Unity:">
            <select class="form-control form-control-sm select_material" data-tipo="material" name="material_id[]"></select>
        </td>
        <td data-label="Unity:">
            <input type="text" name="pre_unit[]" class="form-control form-control-sm pre_unit" readonly>
        </td>
        <td data-label="Quantity Ordered:">
            <input type="number" name="q_ordered[]" step="1.0" min="0" value="0" class="form-control form-control-sm">
        </td>
        <td data-label="*">
        <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div>
    </td>
    </tr>
`;
td_equipo = `
    <tr>
        <td data-label="Material:">
            <input type="text" name="tipo[]" class="form-control form-control-sm tipo" value="equipment" readonly>
        </td>
        <td data-label="Material:">
            <select class="form-control form-control-sm select_equipo" data-tipo="equipo" name="material_id[]"></select>
        </td>
        <td data-label="Unity:">
            <input type="text" name="pre_unit[]" class="form-control form-control-sm pre_unit" readonly>
        </td>
        <td data-label="Quantity Ordered:">
            <input type="number" name="q_ordered[]" step="1.0" min="0" value="0" class="form-control form-control-sm" 
    } >
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
$(document).on("click", ".remove_material", function () {
    $(this).parents("tr").remove();
    $(".add-new").removeAttr("disabled");
    if ($('#table-material tbody tr').length == 0) {
        $("#table-material").append(`<tr id="none_tr_mat">
            <td scope="row" colspan="9" class="text-center text-bold">I don't add anything</td>
        </tr>`);
    }
});

$("#proyect")
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
        $("#job_name").val(e.params.data["text"]).prop("disabled", false);
        $("#sub_contractor_id").prop("disabled", false);
        $(".add-material").prop("disabled", false);
        $(".add-equipo").prop("disabled", false);
    });

$("#sub_contractor_id")
    .select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_empresas`,
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
        $("#sub_empleoye_id").prop("disabled", false);
        get_empleoyes(e.params.data["id"]);
    });

function get_empleoyes(id) {
    $("#sub_empleoye_id").select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_empleoyes/${id}/orden`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    });
}

function load_select_material() {
    console.log($("data").val())
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
        });
}
function load_select_equipos() {
    $(".select_equipo")
        .select2({
            theme: "bootstrap4",
            disabled: false,
            ajax: {
                url: `${base_url}/tipo-material/${$("#proyect").val()}/equipos`,
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
$("#enviar").click(function(e) {
    //$(this).prop("disabled", true);
    e.preventDefault();
    //$(".is_mail").val(false);
    send_form();
});
function send_form() {
    let $form = $("#from_order");
    $.ajax({
        type: "POST",
        url: $form.attr("action"),
        data: $form.serialize(),
        dataType: "json",
        success: function(data) {
            console.log(data);
            
            if (data.errors.length > 0) {
                $alert = "complete the following fields to continue:\n";
                data.errors.forEach(function(error) {
                    $alert += `* ${error}\n`;
                });
                alert($alert);
                $("#enviar").prop("disabled", false);
                $("#send_mail").prop("disabled", false);
                $("#send_mail").text("Send Mail");
            } else {
                $form.submit();
            }
        },
    });
}