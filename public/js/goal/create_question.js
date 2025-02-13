/*  mostrar modal */
$(document).on('click', '#create_razon', function() {
    $('#modal_reason #form_result').html('');
    $('#modal_reason #razon').trigger("reset");
    $("#modal_reason").removeAttr("tabindex");
    $("#modal_reason").modal("show");
    $(`#select2_consequences`).val(null).trigger("change");
    $(`#select2_problem`).val(null).trigger("change");
    $('#generar_opciones').html('');

});
/* selecion de tipo */
$(document).on("change", "#new_question_tipo", function () {
    const verificar = $("#new_question_tipo option:selected").val();
    $('#generar_opciones').html('');
    switch (verificar) {
        case 'problem':
            console.log($("#new_question_tipo option:selected").val());
            break;
        case 'consequence':
            console.log($("#new_question_tipo option:selected").val());
            generar_consecuencia()
            get_tipo_select_2('select2_problem','/question-goal/problem');
            break;
        case 'solution':
            console.log($("#new_question_tipo option:selected").val());
            generar_solucion() 
            get_tipo_select_2('select2_consequences','/question-goal/consequence');
            break;
        default:
            break;
    }
});

/* constructor de opciones */
function generar_consecuencia() {
    $('#generar_opciones').append(`
    <div class="form-group row">
        <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Select a problem</label>
        <div class="col-sm-9">
            <select name="select2_problem" id="select2_problem" class="form-control form-control-sm" required>
              
            </select>
        </div>
    </div>
    `);
}
function generar_solucion() {
    $('#generar_opciones').append(`
    <div class="form-group row">
        <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Select a consequences</label>
        <div class="col-sm-9">
            <select name="select2_consequences" id="select2_consequences" class="form-control form-control-sm" required>
             
            </select>
        </div>
    </div>
    `);
}
/* select2 */
function get_tipo_select_2(tipo, url) {
    $(`#${tipo}`)
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}${url}`,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        searchTerm: params.term, // search term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response,
                    };
                },
                cache: true,
            },
        })
        
}
