// show delete
$(document).on('click', '.delete', function () {
    var id = $(this).data('id')
    $('#deleteModal #delete_button').data('id', id)
    $('#deleteModal').modal('show')
})
$(document).on('click', '#delete_button', function () {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/delete-form-staff/${$(this).data('id')}`,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                alert(data.success)
                table.draw();
                $('#deleteModal').modal('hide')
            }
        },
    })
})
//edit
$(document).on('click', '.resultado', function () {
    /*$('#edit_evaluacion_form').trigger('reset')
    $('#edit_select_personal').multiselect('deselect', idUsers);*/
    var id = $(this).attr('data-id');
    $("#secciones").empty();
    $.ajax({
        url: `${base_url}/show-resultado/${id}`,
        dataType: "json",
        success: function (data) {
            console.log(data);
            $('#nombre').val(data.nombre);
            $('#form').val(data.titulo);
            $('#fecha').val(data.fecha);
            data.secciones.forEach(seccion => {
                var res="";
                seccion.preguntas.forEach(pregunta => {
                    res+=`<p style="margin-bottom: 0.1rem;">${pregunta.pregunta} </p>`;
                });
                $('#secciones').append(
                    `<div class="jumbotron" style="padding: 1rem 1rem">
                        <label><strong>${seccion.subtitulo}</strong></label>
                        <p>${seccion.subtitulo===null ? seccion.subtitulo : ''} </p> 
                        <hr><p>
                         ${res} 
                        </p>
                        <hr>
                        <p> <strong>response average:</strong> ${seccion.promedio}</p>
                    </div>`
                )
            });
            
            $('#resumeEvaluacion .modal-title').text('Summary');
            $('#resumeEvaluacion').modal('show');
            $("#resumeEvaluacion").removeAttr("tabindex");
        }
    })
});