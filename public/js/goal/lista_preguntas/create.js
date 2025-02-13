/* create */
$(document).on('click', '.create', function () {
    $('#formModalNewPregunta #form_new').trigger("reset");
    switch ($(this).data('tipo')) {
        case 'problem':
            $('.store').data('tipo', 'problem');
            $(".modal-title").text("Create problem");
            $("#formModalNewPregunta").modal("show");
            break;
        case 'consecuencia':
            $('.store').data('tipo', 'consecuencia');
            $(".modal-title").text("Create consequense");
            $('.store').data('id_problem', $(this).data('id_problem'));
            $("#formModalNewPregunta").modal("show");
            break;
        case 'solucion':
            $('.store').data('tipo', 'solucion');
            $(".modal-title").text("Create solution");
            $('.store').data('id_consequence', $(this).data('id_consequence'));
            $("#formModalNewPregunta").modal("show");
            break;
        default:
            break;
    }
});
/* click store */
$(".store").click(function () {
    switch ($(this).data('tipo')) {
        case 'problem':
            store($(this).data('tipo'))
            break;
        case 'consecuencia':
            store($(this).data('tipo'),$(this).data('id_problem'))
            break;
        case 'solucion':
            store($(this).data('tipo'),$(this).data('id_consequence'))
            break;
        default:
            break;
    }
});
/* funcion guardar */
function store(tipo,id=false) {
    $.ajax({
        type: "POST",
        url: `${base_url}/goal-question/store`,
        dataType: "json",
        data: {
            tipo: tipo,
            descripcion: $('#new_descripcion').val(),
            id       
        },
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
                
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                $("#formModalNewPregunta").modal("hide");
                table.draw();
            }
        },
    });
}
