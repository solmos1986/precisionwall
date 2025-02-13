td_material = `
    <tr>
        <td data-label="Material:">
            <select class="form-control form-control-sm select_material" name="material_id[]"></select>
        </td>
        <td data-label="Unity:">
            <input type="text" name="pre_unit[]" class="form-control form-control-sm pre_unit" readonly>
        </td>
        <td data-label="Quantity Ordered:">
            <input type="number" name="q_ordered[]" step="1.0" min="0" value="0" class="form-control form-control-sm" ${admin == 0 ? "readonly" : ""
    }>
        </td>
        <td data-label="Q. to the job site:">
            <input type="number" name="q_job_site[]" step="1.0" min="0" value="0" class="form-control form-control-sm">
        </td>
        <td data-label="Quantity Installed:">
            <input type="number" name="q_installed[]" step="1.0" min="0" value="0" class="form-control form-control-sm">
        </td>
        <td data-label="Date Installed:">
            <input type="text" name="d_installed[]" class="form-control form-control-sm datepicke">
        </td>
        <td data-label="Q.Remaining WC:">
            <input type="number" step="1.0" min="0" value="0" name="q_remaining_wc[]" class="form-control form-control-sm">
        </td>
        <td data-label="Remaining WC stored at:">
            <input type="text" name="remaining_wc_stored[]" class="form-control form-control-sm">
        </td>
        <td data-label="*">
            <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div>
        </td>
    </tr>
`;

$("#mailModal").on("hidden.bs.modal", function (e) {
    $("#enviar").prop("disabled", false);
});
/**
 * $("#enviar").click(function(e) {
    $(this).prop("disabled", true);
    e.preventDefault();
    if (confirm("Do you want to send this form by mail ???")) {
        $(".is_mail").val(true);
        $("#mailModal").modal("show");
        $.ajax({
            url: `${base_url}/get_config_mail/${$("select[name=proyect] option")
        .filter(":selected")
        .val()}/orden`,
            dataType: "json",
            async: false,
            success: function(response) {
                $("#mailModal #title_m").val(
                    `${response.config.title_ticket_email} - ORDER #${n_orden}`
                );
                $("#mailModal #body_m").text(response.config.body_ticket_email);
                $("#mailModal #row_id").val(n_orden);
                var emails = [];
                emails.push(response.emails.Coordinador_Obra_mail);
                emails.push(response.emails.Lead_mail);
                emails.push(response.emails.Pwtsuper_mail);
                $("#mailModal #to").tokenfield(
                    "setTokens",
                    response.emails.Foreman_mail
                );
                $.each(emails, function(index, value) {
                    if (value) {
                        $("#mailModal #cc").tokenfield("setTokens", value);
                    }
                });
                $.each(response.email_contac, function(index, value) {
                    if (value.email) {
                        $("#mailModal #cc").tokenfield("setTokens", value.email);
                    }
                });
            },
        });
    } else {
        $(".is_mail").val(false);
        send_form();
    }
});
 */

$("#enviar").click(function (e) {
    $(this).prop("disabled", true);
    e.preventDefault();
    $(".is_mail").val(false);
    send_form();
});

$(document).on("click", "#send_mail", function () {
    $(".to").val($("#to").val());
    $(".cc").val($("#cc").val());
    $(".title_m").val($("#title_m").val());
    $(".body_m").val($("#body_m").val());
    var $button = $("#send_mail");
    $button.html("Wait.....");
    $button.prop("disabled", true);
    send_form();
});

function send_form() {
    let $form = $("#from_order");
    $.ajax({
        type: "POST",
        url: $form.attr("action"),
        data: $form.serialize(),
        dataType: "json",
        success: function (data) {
            if (data.errors.length > 0) {
                $alert = "complete the following fields to continue:\n";
                data.errors.forEach(function (error) {
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
$(document).on("click", ".añadir-material", function () {
    console.log('data load')

    $("#none_tr_mat").remove();
    $("#table-material").append(td_material);
    //add campo
    load_select_material();
    reload();
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
        $("#sub_contractor").val(e.params.data["emp"]);
        get_empleoyes(e.params.data["emp_id"]);
        $("#sub_empleoye_id").prop("disabled", false);
        $(".añadir-material").prop("disabled", false);
    });
/* 
$("#sub_contractor")
    .select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_empresas`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term, // search term
                };
            },
            processResults: function(response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
    })
    .on("select2:select", function(e) {
        $("#sub_empleoye_id").prop("disabled", false);
        get_empleoyes(e.params.data["id"]);
    });
 */
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
    $(".select_material")
        .select2({
            theme: "bootstrap4",
            disabled: admin == 0 ? "readonly" : false,
            ajax: {
                url: `${base_url}/get_materiales/${$("#proyect").val()}`,
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

function reload() {

    var dp = $(".datepicke").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $("#datepicker-2").datepicker();
    $(".ui-datepicker-month", dp).hide();
    $("<span>", {
        class: "ui-datepicker-month-btn btn"
    }).html($(".ui-datepicker-month option:selected", dp).text()).insertAfter($(".ui-datepicker-month", dp));
    $(".ui-datepicker-year", dp).hide();
    $("<span>", {
        class: "ui-datepicker-year-btn btn"
    }).html($(".ui-datepicker-year option:selected", dp).text()).insertAfter($(".ui-datepicker-year", dp));

    function selectMonth(ev) {
        var mObj = $(ev.target);
        $(".ui-datepicker-month", dp).val(mObj.data("value")).trigger("change");
        $(".ui-datepicker-month-btn", dp).html(mObj.text().trim());
        mObj.closest(".ui-datepicker").find(".ui-datepicker-calendar").show();
        mObj.closest(".ui-datepicker-month-calendar").remove();
    }

    function showMonths(trg) {
        $(".ui-datepicker-calendar", trg).hide();
        var mCal = $("<table>", {
            class: "ui-datepicker-month-calendar"
        }).insertAfter($(".ui-datepicker-calendar", trg));
        var row, cell;
        $(".ui-datepicker-month option").each(function (i, o) {
            if (i % 4 == 0) {
                row = $("<tr>").appendTo(mCal);
            }
            cell = $("<td>").appendTo(row);
            $("<span>", {
                class: "ui-widget-header circle btn"
            })
                .data("value", $(o).val())
                .html($(o).text().trim())
                .click(selectMonth)
                .appendTo(cell);
            if ($(o).is(":selected")) {
                $("span", cell).addClass("selected");
            }
        });
    }

    $(".ui-datepicker-month-btn").click(function () {
        console.log("Show Months");
        showMonths(dp);
    })
}

