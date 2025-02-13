/* td_material = `
        <tr>
            <td data-label="Material Description:">
                <input type="text" disabled name="personal_id[]" value="" class="form-control form-control-sm" hidden>
                <input type="text" disabled name="cost_code[]" value="" class="form-control form-control-sm" >
                <input type="text" name="estado[]" value="nuevo" class="form-control form-control-sm" hidden>
            </td>
            <td data-label="Unit of Measurement:">
            <select class="form-control form-control-sm w-100 task" style="width: 100%"
                name="task_id[]" style="width:100%">
            </select>
            </td>
            <td data-label="QTY:"><input type="number" name="hours_worked[]" step="1.0" min="0" value="0" class="form-control form-control-sm"></td>
            <td data-label="*"> <div class="ms-btn-icon btn-danger btn-sm remove_material"><i class="fas fa-trash-alt mr-0"></i></div> </td>
        </tr>
    `;

$(".add-actividad").on('click', function () {
    $("#none_tr_mat").remove();
    $("#table-actividad tbody").append(td_material);
    get_new_select_task($('#proyect').val())
});

$(document).on("click", ".remove_class", function () {
    $(this).parents("tr").remove();
    if ($('#table-class tbody tr').length == 0) {
        $("#table-class tbody").append(`<tr id="none_tr_class">
            <td colspan="11" class="text-center">I don't add anything</td>
        </tr>`);
    }
}); */