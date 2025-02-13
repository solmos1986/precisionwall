$(document).on('click', '.delete ', function () {
    switch ($(this).data('tipo')) {
        case 'problema':
            $('#delete_button').data('tipo', 'problem');
            $('#delete_button').data('id', $(this).data('id'));
            $(".modal-title").text("Delete problem");
            $("#deleteModal").modal("show");
            break;
        case 'consecuencia':
            $('#delete_button').data('tipo', 'consecuencia');
            $('#delete_button').data('id', $(this).data('id'));
            $(".modal-title").text("Delete consequense");
            $('.store').data('id_problem', $(this).data('id_problem'));
            $("#deleteModal").modal("show");
            break;
        case 'solucion':
            $('#delete_button').data('tipo', 'solucion');
            $('#delete_button').data('id', $(this).data('id'));
            $(".modal-title").text("Delete solution");
            $('.store').data('id_consequence', $(this).data('id_consequence'));
            $("#deleteModal").modal("show");
            break;
        default:
            break;
    }
});
$("#delete_button").click(function () {
    switch ($(this).data('tipo')) {
        case 'problem':
            eliminar($(this).data('tipo'))
            break;
        case 'consecuencia':
            eliminar($(this).data('tipo'), $(this).data('id'))
            break;
        case 'solucion':
            eliminar($(this).data('tipo'), $(this).data('id'))
            break;
        default:
            break;
    }
});
/* funcion eliminar */
function eliminar(tipo, id = false) {
    $.ajax({
        type: "DELETE",
        url: `${base_url}/goal-question/delete/${id}`,
        dataType: "json",
        data: {
            tipo: tipo
        },
        success: function (data) {
            $("#deleteModal").modal("hide");
            table.draw();
        },
        error: function(){
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'error',
                showConfirmButton: false,
                timer: 1500
            });
            $("#deleteModal").modal("hide");
          }
    });
}
