//añadir surface
$(document).on("click", ".view_material", function () {
    const superficie_id = $(this).data('surface_id');
    $('#modalViewMateriales').removeAttr("tabindex");
    $.ajax({
        type: "GET",
        url: `${base_url}/goal-structure/list-materiales/${superficie_id}`,
        dataType: "json",
        success: function (response) {
            //materiales(response.data.materiales)
            /* dato proyecto*/
            $('#modalViewMateriales').modal('show');
            $('#title_modal').text(`${response.data.superficie.codigo}  |  ${response.data.superficie.nombre}`);
            /* $('#fecha_registro').val(moment().format('DD/MM/YYYY HH:mm')); */

            $('#code_proyecto').val(`${response.data.superficie.Codigo}`);
            $('#proyecto').val(`${response.data.superficie.Nombre}`);
            $('#proyecto_id').val(`${response.data.superficie.Pro_ID}`);
            //select_material();
            $('#superficie_id').val(response.data.superficie_id);
            lista(response.data.materiales);
            select_material(response.data.superficie.Pro_ID);
            $('#modalViewMateriales').modal('show');
        }
    });
});
function lista(materiales) {
    $('#materiales').html('');
    materialesHTML = ``;
    materiales.forEach(material => {
        materialesHTML += `
        <tr>
            <td >${material.nombre_categoria}</td>
            <td style="width:40%">
                <select class="form-control form-control-sm materiales w-100" data-tipo="material_id" name="material_id[]">
                <option value="${material.material_id}" seleted>${material.Denominacion}</option>
                </select>
            </td>
            <td >${material.Unidad_Medida}</td>
            <th >
                <input type="text" class="form-control w-30 form-control-sm quantity" data-input="quantity" 
                    id="quantity" name="quantity[]" placeholder="Quantity" value="${material.cantidad}"
                    autocomplete="off" >
            </td>
            <td>
            </td>
            <td>
                <i class="far fa-trash-alt ms-text-danger delete_material cursor-pointer" data-visit_report_material_id="${material.id}" title="Delete Material"></i>
            </td>
        </tr>
        `
    });
    $('#materiales').append(materialesHTML);
}
function select_material(proyecto_id = 1) {
    $(document).ready(function () {
        $(".materiales")
            .select2({
                dropdownAutoWidth: true,
                theme: "bootstrap4",
                //disabled: true,
                ajax: {
                    url: `${base_url}/goal-structure/select-materiales/${proyecto_id}/materiales`,
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
                    .parents("tr").children().each(function (index, data) {
                        if (index == 0) {
                            $(data).text(e.params.data["tipo_nombre"])
                        }
                        if (index == 2) {
                            $(data).text(e.params.data["Unidad_Medida"])
                        }
                        if (index == 5) {
                            //$(data).find('input').val(e.params.data["Pro_ID"])
                        }
                        console.log($(data))
                    });
            });
    });
}
$(document).on("click", ".add_material", function () {
    const proyecto_id = $('#proyecto_id').val();
    var task = `
        <tr>
            <td></td>
            <td style="width:40%">
                <select class="form-control form-control-sm materiales w-100" data-tipo="material_id" name="material_id[]"></select>
            </td>
            <td></td>
            <th>
                <input type="text" class="form-control w-30 form-control-sm quantity" data-input="quantity" 
                    id="quantity" name="quantity[]" placeholder="Quantity" value=""
                    autocomplete="off" >
            </td>
            <td></td>
            <td>
                <i class="far fa-trash-alt ms-text-danger new_delete_material cursor-pointer" data-visit_report_material_id="" title="Delete Material"></i>
            </td>
        </tr>
        `;
    $("#materiales").append(task);
    select_material(proyecto_id);
});

$(document).on("click", "#save_material_visit_report", function () {
    $.ajax({
        type: "POST",
        url: `${base_url}/goal-structure/materiales`,
        dataType: "json",
        data:$('#form_materiales').serialize(),
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalViewMateriales').modal('hide');
            } else {
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
        }
    });
});

$(document).on("click", ".delete_material", function () {
    const visit_report_material_id = $(this).data('visit_report_material_id');
    var input= $(this);
    $.ajax({
        type: "DELETE",
        url: `${base_url}/goal-structure/materiales/${visit_report_material_id}`,
        dataType: "json",
        data:$('#form_materiales').serialize(),
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                input.parent().parent().remove();
            } 
        }
    });
});
$(document).on("click", ".new_delete_material", function (event) {
    $(this).parent().parent().remove();
    $(this).prop('disabled', false)
});
