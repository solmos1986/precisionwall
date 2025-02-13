/* create */
$(document).on('click', '.view_materiales', function () {
    const proyecto_id = $(this).data('proyecto_id');
    $('#fromVisitCheckMaterial').trigger('reset'),
        $.ajax({
            type: "GET",
            url: `${base_url}/goal-project/show-order/${proyecto_id}`,
            dataType: "json",
            success: function (response) {
                materiales(response.data.materiales)
                /* dato proyecto*/
                $('#title_modal_check_list').text(`Project - ${response.data.proyecto.Nombre}`);
                $('#orden_proyecto_id').val(response.data.proyecto.Pro_ID);
                $('#fecha_registro').val(moment().format('DD/MM/YYYY HH:mm'));

                $('#user').val(`${response.data.user.nombre_completo}`);
                $('#nombre_proyecto').val(`${response.data.proyecto.Nombre}`);
                $('#modalCreateViewMateriales').modal('show');
            }
        });
    $('#modalCreateViewMateriales').modal('show');
});

function materiales(materiales) {
    var listaHTML = ``;
    $('#materiales').html('');
    materiales.forEach(material => {
        listaHTML += `
        <tr>
            <td scope="col">
                <input type="checkbox" class="check" name="material_id[]" value="${material.Mat_ID}" style="transform: scale(1.5);">
            </td>
            <td class="ms-table-f-w">${material.Denominacion}</td>
            <td>${material.Unidad_Medida}</td>
            <td>${material.cantidad_sugerida}</td>
            <td>
                <input type="number" class="form-control w-30 form-control-sm quantity" data-input="quantity" id="quantity" name="quantity[]" placeholder="Quantity" value="" autocomplete="off">
            </td>
            <td></td>
        </tr>
        `;
    });
    $('#materiales').append(listaHTML);
}

$(document).on('click', '#save_visit_check_material', function () {

    var values = $("input[name^='quantity']").map(function (idx, ele) {
        return $(ele).val();
     }).get();
     console.log(values)
     var material_id = $("input[name^='material_id']").map(function (idx, ele) {
        return $(ele).val();
     }).get();
     console.log(material_id)
    $.ajax({
        type: "POST",
        url: `${base_url}/goal-project/store-order`,
        dataType: "json",
        data: $('#fromVisitCheckMaterial').serialize(),
        success: function (response) {
            if (response.status == 'ok') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalCreateViewMateriales').modal('hide');
                tableTable.draw();
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

$(document).on('input', '.quantity', function (event) {
    console.log('input', $(this).val())
    console.log($(this).parent().parent().children())
    var value = $(this).val();
    $(this).parent().parent().children().each(function (index, data) {
        if (value > 0) {
            if (index == 0) {
                console.log($(data).find('input').prop("checked", true))
            }
        } else {
            console.log($(data).find('input').prop("checked", false))
        }
    });

});

$(document).on('change', '#view_pdf_all', function (event) {
    if (this.checked) {
        $('.check').prop("checked", true)
    } else {
        $('.check').prop("checked", false)
    }
});