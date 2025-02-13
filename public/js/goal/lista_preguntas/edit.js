$(document).on('click', '.edit', function() {
    $('#formModalEditPregunta #form_edit').trigger("reset");
    console.log($(this).data('tipo'));
    switch ($(this).data('tipo')) {
        case 'problema':
            console.log('problema');
            $('.update').data('tipo', 'problema');
            $('.update').data('id', $(this).data('id'));
            $(".modal-title").text("Edit problem");
            edit('problema',$(this).data('id'));

            break;
        case 'consecuencia':
            console.log('consecuancia');
            $('.update').data('tipo', 'consecuencia');
            $('.update').data('id', $(this).data('id'));
            $(".modal-title").text("Edit consequense");
            edit('consecuencia',$(this).data('id'));
        
            break;
        case 'solucion':
            console.log('solucion');
            $('.update').data('tipo', 'solucion');
            $('.update').data('id', $(this).data('id'));
            $(".modal-title").text("Edit solution");
            edit('solucion',$(this).data('id'));

            break;
        default:
            break;
    }
});
function edit(tipo,id) {
    $.ajax({
        type: "POST",
        url: `${base_url}/goal-question/edit/${id}`,
        dataType: "json",
        data:{
            tipo:tipo
        },
        success: function (data) {
            $('#edit_descripcion').val(data.descripcion);
            console.log(data);
            $("#formModalEditPregunta").modal("show");
        },
    });
}
$(".update").click(function () {
    switch ($(this).data('tipo')) {
        case 'problema':
            update($(this).data('tipo'), $(this).data('id'))
            break;
        case 'consecuencia':
            update($(this).data('tipo'), $(this).data('id'))
            break;
        case 'solucion':
            update($(this).data('tipo'), $(this).data('id'))
            break;
        default:
            break;
    }
});
/* funcion guardar */
function update(tipo,id=false) {
    $.ajax({
        type: "PUT",
        url: `${base_url}/goal-question/update/${id}`,
        dataType: "json",
        data: {
            tipo: tipo,
            descripcion: $('#edit_descripcion').val()   
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
                $("#formModalEditPregunta").modal("hide");
                table.draw();
            }
           
        },
    });
}
