$(document).on("click", ".view_action", function () {
    $.ajax({
        type: "GET",
        url: `${base_url}/info-project/get-project-action/${$(this).data('proyecto_id')}`,
        dataType: "json",
        success: function (response) {
            $('#historial_action').html('');
            var tarjeta = ``;
            response.action.forEach(action => {
                tarjeta += `
                <li>
                    <div class="ms-btn-icon btn-pill icon btn-success " >
                        <i class="flaticon-pencil edit_action_history" title="Edit record"></i>
                    </div>
                    <span class="my-2 d-block" style="font-size: 13px;"> <i class="material-icons"   >date_range</i> Add the
                    ${moment(action.fecha_proyecto_movimiento).format('MMMM dddd D, YYYY h:mm:ss')}
                        <i class="far fa-trash-alt ms-text-danger inline-flex d-flex flex-row-reverse delete_action_history cursor-pointer" style="font-size: 15px;"></i>
                    </span>
                    <p class="d-block mb-0"><strong>Report Weekly:</strong> </p>
                   
                    <textarea type="text" class="form-control form-control-sm" id="report_weekly" name="report_weekly" rows="5" placeholder="Report Weekly" readonly>${action.report_weekly == null ? '' : action.report_weekly}</textarea>
                    <div class="text-center">
                    ${action.imagen ? `<img src="${base_url}/uploads/${action.imagen}">` : ``}
                    </div>
                    <p class="d-block mb-0"><strong>Action for Week:</strong></p>
                    <textarea type="text" class="form-control form-control-sm" id="report_weekly" name="report_weekly" rows="6" placeholder="Report Weekly" readonly> ${action.action_for_week == null ? '' : action.action_for_week}</textarea>
                    <input value="${action.id}" hidden>
                </li>
                `;
            });
            $('#historial_action').append(tarjeta);
            $('#modalViewAction').modal('show');
        }
    });
});
//edit_action_histori

$(document).on("click", ".edit_action_history", function () {
    $(this).removeClass('flaticon-pencil edit_action_history');
    $(this).parent().removeClass('btn-success');
    $(this).addClass('far fa-check-circle save_action_history');
    $(this).parent().addClass('btn-primary');
    $(this).parent().next().next().next().prop('readonly', false);
    $(this).parent().next().next().next().next().next().next().prop('readonly', false);
});
$(document).on("click", ".save_action_history", function () {
    $(this).removeClass('far fa-check-circle save_action_history');
    $(this).parent().removeClass('btn-primary');
    $(this).addClass('flaticon-pencil edit_action_history');
    $(this).parent().addClass('btn-success');

    let report_week = $(this).parent().next().next().next().val();
    let action_for_week = $(this).parent().next().next().next().next().next().next().val();
    let id = $(this).parent().next().next().next().next().next().next().next().val();
    console.log(report_week, action_for_week, id)
    update_actions_history(report_week, action_for_week, id)
    $(this).parent().next().next().next().next().next().next().prop('readonly', true);
    $(this).parent().next().next().next().prop('readonly', true)
});

function update_actions_history(report_week, action_for_week, id) {
    $.ajax({
        type: "PUT",
        url: `${base_url}/info-project/update_action_history/${id}`,
        data: {
            action_for_week,
            report_week
        },
        dataType: "json",
        success: function (response) {
            console.log(response)
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            $('#modalViewAction').modal('hide');
        }
    });
}
/*delete */
$(document).on("click", ".delete_action_history", function () {
    id=$(this).parent().next().next().next().next().next().next().val();
    delete_actions_history(id);
});

function delete_actions_history(id) {
    $.ajax({
        type: "DELETE",
        url: `${base_url}/info-project/update_action_history/${id}`,
       
        dataType: "json",
        success: function (response) {
     
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            $('#modalViewAction').modal('hide');
        }
    });

}