var datatable_labort_cost = $('#lista_labor_cost').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": `${base_url}/project-files/datatable-labor-cost`,
    "lengthMenu": [
        [25, 50, -1],
        [25, 50, "All"]
    ],
    "columns": [
        {
            data: 'labor_cost',
            name: "labor_cost"
        },
        {
            data: 'descripcion',
            name: "descripcion"
        },
        {
            data: 'acciones',
            name: 'acciones',
        }
    ],
    "paging": false,
    "searching": false,
    "lengthChange": false,
    "info": false
});

$(document).on("click", "#list_labor_cost", function () {
    $('#modalListLaborCost').modal('show');
});

$(document).on("click", "#create_labor_cost", function () {
    $('#modalListLaborCost').modal('show');
});

$(document).on("click", ".create_labor_cost", function () {
    $('#save_labor_cost').removeClass('update_labor_cost');
    $('#save_labor_cost').addClass('save_labor_cost');
    $('#title_modal_labor_cost').text(`Create Labor Cost`);
    $('#modalLaborCost').modal('show');
    limpiar_imput_labor_cost()
});

$(document).on("click", ".edit_labor_cost", function () {
    $('#save_labor_cost').removeClass('save_labor_cost');
    $('#save_labor_cost').addClass('update_labor_cost');
    $('#title_modal_labor_cost').text(`Edit Labor Cost`);
    $.ajax({
        type: 'GET',
        url: `${base_url}/project-files/edit-labor-cost/${$(this).data('labor_cost_id')}`,
        dataType: 'json',
        async: true,
        success: function (response) {
            limpiar_imput_labor_cost();
            $('#title_modal_labor_cost').text(`Edit Labor Cost`);
            $('#modalLaborCost').modal('show');
            $('#labor_cost').val(response.data.labor_cost);
            $('#labor_cost_id').val(response.data.id);
            $('#labor_cost_descripcion').val(response.data.descripcion);
        }
    }); 
});

$(document).on("click", ".save_labor_cost", function () {
    $.ajax({
        type: 'POST',
        url: `${base_url}/project-files/store-labor-cost`,
        data: $('#form_labor_cost').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            $('#modalLaborCost').modal('hide');
            datatable_labort_cost.draw()
        }
    });
});
$(document).on("click", ".update_labor_cost", function () {
    $.ajax({
        type: 'PUT',
        url: `${base_url}/project-files/update-labor-cost/${$('#labor_cost_id').val()}`,
        data: $('#form_labor_cost').serialize(),
        dataType: 'json',
        async: true,
        success: function (response) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            $('#modalLaborCost').modal('hide');
            datatable_labort_cost.draw()
        }
    });
});
$(document).on("click", ".delete_labor_cost", function () {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'DELETE',
                url: `${base_url}/project-files/delete-labor-cost/${$(this).data('labor_cost_id')}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                    datatable_labort_cost.draw();
                }
            });
        }
    });
});


function limpiar_imput_labor_cost() {
    $('#labor_cost').val('');
    $('#labor_cost_id').val('');
    $('#labor_cost_descripcion').val('');
}