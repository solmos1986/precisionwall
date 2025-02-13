td_material = `
        <tr>
            <td data-label="Material Description:"><select class="form-control form-control-sm select_material" name="material_id[]"></select></td>
            <td data-label="Unit of Measurement:"><input type="text" name="pre_unit[]" class="form-control form-control-sm pre_unit" readonly></td>
            <td data-label="QTY:"><input type="number" name="n_material[]" step="1.0" min="0" value="0" class="form-control form-control-sm"></td>
            <td data-label="*"> <div class="ms-btn-icon btn-info btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div> </td>
        </tr>
    `;
td_class = `
        <tr>
            <td data-label="N° workers:"><input type="number" name="n_workers[]" step="1.0" min="0" value="0" class="form-control form-control-sm n_workers"></td>
            <td data-label="CLASS:"><select class="form-control form-control-sm select_class" name="class_id[]"></select></td>
            <td data-label="Reg Hours:"><input type="number" min="0" name="reg_hours[]" class="form-control form-control-sm reg_hours" value="0"></td>
            <td data-label="Total Reg Hours:"><input type="number" min="0" name="total_reg_hours[]" class="form-control form-control-sm total_reg_hours" value="0" readonly></td>
            <td data-label="Premium Hours:"><input type="number" min="0" name="premium_hours[]" class="form-control form-control-sm premium_hours" value="0"></td>
            <td data-label="Total Premium Hours:"><input type="number" min="0" name="total_premium_hours[]" class="form-control form-control-sm total_premium_hours" value="0" readonly></td>
            <td data-label="Out Hours:"><input type="number" min="0" name="out_hours[]" class="form-control form-control-sm out_hours" value="0"></td>
            <td data-label="Total Out Hours:"><input type="number" min="0" name="total_out_hours[]" class="form-control form-control-sm total_out_hours" value="0" readonly></td>
            <td data-label="Prepaid Hrs:"><input type="number" min="0" name="prepaid_hours[]" class="form-control form-control-sm prepaid_hours" value="0"></td>
            <td data-label="T. Prepaid Hrs:"><input type="number" min="0" name="total_prepaid_hours[]" class="form-control form-control-sm total_prepaid_hours" value="0" readonly></td>
            <td data-label="*"> <div class="ms-btn-icon btn-info btn-sm remove_class"><i class="fas fa-trash-alt mr-0"></i></div> </td>
        </tr>
    `;

$('#mailModal').on('hidden.bs.modal', function(e) {
    $("#enviar").prop("disabled", false);
});


$("#enviar").click(function(e) {
    $(this).prop("disabled", true);
    e.preventDefault();
    $(".is_mail").val(false);
    send_form();
});

$(document).on('click', '#send_mail', function() {
    $(".to").val($("#to").val());
    $(".cc").val($("#cc").val());
    $(".title_m").val($("#title_m").val());
    $(".body_m").val($("#body_m").val());
    var $button = $("#send_mail");
    $button.html("Wait.....");
    $button.prop("disabled", true);
    send_form();
});

$(".add-material").on('click', function() {
    $("#none_tr_mat").remove();
    $("#table-material tbody").append(td_material);
    load_select_material();
});
$(document).on("click", ".remove_material", function() {
    $(this).parents("tr").remove();
    if ($('#table-material tbody tr').length == 0) {
        $("#table-material tbody").append(`<tr id="none_tr_mat">
            <td colspan="4" class="text-center">I don't add anything</td>
        </tr>`);
    }
});

$(".add-class").on('click', function() {
    $("#none_tr_class").remove();
    $("#table-class tbody").append(td_class);
    load_select_class();
});

$(document).on("click", ".remove_class", function() {
    $(this).parents("tr").remove();
    if ($('#table-class tbody tr').length == 0) {
        $("#table-class tbody").append(`<tr id="none_tr_class">
            <td colspan="11" class="text-center">I don't add anything</td>
        </tr>`);
    }
});

$('#table-class tbody').on('keyup change', function() {
    calc();
}).trigger("change").trigger("keyup");


$('#empleoye').select2({
    theme: "bootstrap4",
    ajax: {
        url: `${base_url}/get_empleoyes`,
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                searchTerm: params.term // search term
            };
        },
        processResults: function(response) {
            return {
                results: response
            };
        },
        cache: true
    }
}).on('select2:select', function(e) {
    $("#empleado_id").val(`${e.params.data['id']}`);
    $("#foreman_name").val(`${e.params.data['text']}`);
});

$(document).on('click', '#create_razon', function() {
    $('#modal_reason #form_result').html('');
    $('#modal_reason #razon').trigger("reset");
    $("#modal_reason").modal("show");
});
$(document).on('click', '#guardar_pregunta', function() {
    $.ajax({
        url: `${base_url}/Razontrabajo`,
        method: "POST",
        data: $("#modal_reason #razon").serialize(),
        dataType: "json",
        success: function(data) {
            var html = '';
            if (data.errors) {
                html = '<div class="alert alert-danger">';
                for (var count = 0; count < data.errors.length; count++) {
                    html += `<p>${data.errors[count]}</p>`;
                }
                html += '</div>';
                $('#modal_reason #form_result').html(html);
            }
            if (data.success) {
                alert(data.success);
                $('#modal_reason #razon').trigger("reset");
                $('#modal_reason').modal('hide');
            }
        }
    });
});

$('#create_trabajo').click(function() {
    $('#profesionModal .form_result').html('');
    $('#profesionModal #profesion').trigger("reset");
    $('#profesionModal').modal('show');
});
$(document).on('click', '#guardar_profesion', function() {
    $.ajax({
        url: `${base_url}/Tipo`,
        method: "POST",
        data: $("#profesionModal #profesion").serialize(),
        dataType: "json",
        success: function(data) {
            var html = '';
            if (data.errors) {
                html = '<div class="alert alert-danger">';
                for (var count = 0; count < data.errors.length; count++) {
                    html += `<p>${data.errors[count]}</p>`;
                }
                html += '</div>';
                $('#profesionModal .form_result').html(html);
            }
            if (data.success) {
                $('#profesionModal #profesion').trigger("reset");
                $('#profesionModal').modal('hide');
                alert(data.success);
            }
        }
    });
});

$('#modal_material').click(function() {
    $('#materialModal .form_result').html('');
    $('#materialModal #material').trigger("reset");
    $('#materialModal').modal('show');
});
$(document).on('click', '#guardar_material', function() {
    $.ajax({
        url: `${base_url}/store/${Pro_ID}/material`,
        method: "POST",
        data: $("#materialModal #material").serialize(),
        dataType: "json",
        success: function(data) {
            var html = '';
            if (data.errors) {
                html = '<div class="alert alert-danger">';
                for (var count = 0; count < data.errors.length; count++) {
                    html += `<p>${data.errors[count]}</p>`;
                }
                html += '</div>';
                $('#materialModal .form_result').html(html);
            }
            if (data.success) {
                $('#materialModal #material').trigger("reset");
                $('#materialModal').modal('hide');
                alert(data.success);
            }
        }
    });
});

//funciones
function load_select_material() {
    $('.select_material').select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_materiales/${Pro_ID}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function(response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        $(this).parents('tr').find('.pre_unit').val(e.params.data['Unidad_Medida']);
    });
}

function load_select_class() {
    $('.select_class').select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_class_workers`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function(response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    });
}

function calc() {
    $('#table-class tbody tr').each(function() {
        var html = $(this).html();
        if (html != '') {
            let n_workers = Number($(this).find('.n_workers').val()) || 0;
            let reg_hours = Number($(this).find('.reg_hours').val()) || 0;
            let premium_hours = Number($(this).find('.premium_hours').val()) || 0;
            let out_hours = Number($(this).find('.out_hours').val()) || 0;
            let prepaid_hours = Number($(this).find('.prepaid_hours').val()) || 0;

            $(this).find('.total_reg_hours').val(n_workers * reg_hours);
            $(this).find('.total_premium_hours').val(n_workers * premium_hours);
            $(this).find('.total_out_hours').val(n_workers * out_hours);
            $(this).find('.total_prepaid_hours').val(n_workers * prepaid_hours);
        }
    });
}

function send_form() {
    let $form = $('#from_ticket');
    $.ajax({
        type: "POST",
        url: $form.attr('action'),
        data: $form.serialize(),
        dataType: "json",
        success: function(data) {
            if (data.errors.length > 0) {
                $alert = "complete the following fields to continue:\n";
                data.errors.forEach(function(error) {
                    $alert += `* ${error}\n`;
                });
                alert($alert);
                $('#enviar').prop("disabled", false);
                $('#send_mail').prop('disabled', false);
                $('#send_mail').text('Send Mail');
            } else {
                $form.submit();
            }
        }
    });
}