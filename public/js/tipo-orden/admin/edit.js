
td_material = `
<tr>
    <td data-label="Material:">
        <input type="text" name="tipo[]" class="form-control form-control-sm" value="material" readonly>
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
        <input type="text" name="tipo[]" class="form-control form-control-sm" value="equipment" readonly>
    </td>
    <td data-label="Material:">
        <select class="form-control form-control-sm select_material" data-tipo="equipo" name="material_id[]"></select>
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
    $('.movimiento_fecha').val(moment().format('DD/MM/YYYY HH:mm:ss'));

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
    $(".select_material")
        .select2({
            theme: "bootstrap4",
            disabled: false,
            ajax: {
                url: `${base_url}/tipo-material/${$("#proyect").val()}/materiales`,
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
/*enviar delivery */
var responseData;
$("#enviar").click(function (e) {
    e.preventDefault();
    Swal.fire({
        icon: 'warning',
        title: 'Do you want to send order?',
        //showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Save',
        //denyButtonText: `Send order`,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#28a745",
        //denyButtonColor: "#7066e0",
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            var $form = $("#from_order");
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
                        Swal.fire('Saved!', '', 'success').then((result) => {
                            window.location.href = `${base_url}/tipo-order-list-admin`
                        });
                    }
                },
            });
        } else if (result.isDenied) {
            var $form = $("#from_order");
            $.ajax({
                type: "POST",
                url: $form.attr("action"),
                data: $form.serialize(),
                dataType: "json",
                success: function (data) {
                    responseData=data.data;
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
                        $('#materiales tbody').html("");
                        $('#formModalEnvio').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        var trHTML = '';
                        $.each(data.data, function (i, item) {
                            console.log(item)
                            trHTML +=
                                `<tr> 
                                <input type="text" name="tipo_orden_materiales_movimiento_vendedor_id[]" value="${item.id}" hidden>
                                <input type="text" name="fecha_movimiento" value="${ moment().format('MM/DD/YYYY HH:mm:ss')}" hidden>
                                    <td>${item.nombre}</td>
                                    <td>0</td>
                                    <td>${item.Denominacion}</td>
                                    <td>${item.cantidad}</td>
                                </tr>`;
                        });
                        $('#materiales tbody').append(trHTML);
                    }
                },
            });
        }
    })
});

$(".cantidad_asignada").keyup(function () {
    var data = $(this).data('id');
    var valor_final = $(`.movimiento_ordenada_${data}`).val();
    var valor_entregado = $(`.movimiento_entregado_${data}`).val();

    if (valor_entregado > 0) {
        console.log('el valor es mayor a 0')


        if (this.value == '' || this.value == 0) {
            deseleccionar(data);
            $(`.movimiento_status_${data} option[value=2]`).attr({ selected: true });
        }
        var suma = (parseInt(valor_entregado) + parseInt(this.value))
        if (suma >= parseInt(valor_final)) {
            deseleccionar(data);
            $(`.movimiento_status_${data} option[value=3]`).attr({ selected: true });
        } else {
            deseleccionar(data);
            $(`.movimiento_status_${data} option[value=1]`).attr({ selected: true });
        }
    } else {
        if (this.value == '' || this.value == 0) {
            console.log('vacio o 0')
            deseleccionar(data);
            $(`.movimiento_status_${data} option[value=2]`).attr({ selected: true });
        }
        if (this.value > 0) {
            deseleccionar(data);
            $(`.movimiento_status_${data} option[value=1]`).attr({ selected: true });
        }
        if (parseInt(this.value) >= parseInt(valor_final)) {
            deseleccionar(data);
            $(`.movimiento_status_${data} option[value=3]`).attr({ selected: true });
        }
    }

});

function deseleccionar(data) {
    $(`.movimiento_status_${data} option`).each(function () {
        $(this).attr({ selected: false })
    });
}

/*$("#enviar_orden").click(function (e) {
    e.preventDefault();
    var $form=$('#form_envio_delivey')
    $.ajax({
        type: "POST",
        url: $form.attr("action"),
        data:{
            sub_empleoye_id:$('#sub_empleoye_id').val(),
            estatus_id:$('#estatus_id').val(),
            fecha_envio:$('#fecha_envio').val(),
            nota:$('#nota').val(),
            fecha_actividad:moment().format('MM/DD/YYYY HH:mm:ss'),
            tipo_orden_materiales_movimiento_vendedor_id:responseData
        },
        dataType: "json",
        success: function (data) {
            if (data.status == 'ok') {
                Swal.fire('Saved!', '', 'success').then((result) => {
                    window.location.href = `${base_url}/tipo-order-list-admin`
                });
            }
            $alert = "";
            data.message.forEach(function (error) {
                $alert += `* ${error}<br>`;
            });
            Swal.fire({
                icon: 'error',
                title: 'complete the following fields to continue:',
                html: $alert,
            })
         
        },
    });
});
*/
