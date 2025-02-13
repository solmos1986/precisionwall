
/* proyectos */
function get_edit_select_proyectos() {
    $(`#proyect`)
        .select2({
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/goal/get_proyects`,
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
            $("#job_name").val(e.params.data["text"]).prop("disabled", false);
            $("#sub_contractor").prop("disabled", false);
            $("#edit_address").val(e.params.data["dirrecion"]);
            $("#edit_project").val(e.params.data["text"]);
            $("#edit_codigo").val(e.params.data["Codigo"]);
            $("#edit_general").val(e.params.data["emp"]);
            get_edit_select_where();
            get_edit_select_task(e.params.data["id"])
        });
}
/* where */
function get_edit_select_where() {
    $(`#edit_where_room`)
        .select2({
            width: 'auto',
            dropdownAutoWidth: true,
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/where-goal/where/${$('#edit_Pro_ID').val()}`,
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
            var textarea = $(`#edit_comentarios`);
            text_coment('edit_where_room', e.params.data["text"], textarea);
        });
}
/* problema */
function get_edit_select_problem() {
    $(`#edit_problem`)
        .select2({
            width: 'auto',
            dropdownAutoWidth: true,
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/buscar-goal/problem`,
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
            var textarea = $(`#edit_comentarios`);
            text_coment('edit_problem', e.params.data["text"], textarea);
            //get_edit_select_consecuencia(e.params.data["id"])
        });
}

/* consecuencia */
function get_edit_select_consecuencia(id) {
    $(`#edit_consequences`)
        .select2({
            width: 'auto',
            dropdownAutoWidth: true,
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/buscar-goal/consequence`,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term, // search term
                        id
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
            var textarea = $(`#edit_comentarios`);
            text_coment('edit_consequences', e.params.data["text"], textarea);
        });
}
/* solucion */
function get_edit_select_solucion(id) {
    $(`#edit_solution`)
        .select2({
            width: 'auto',
            dropdownAutoWidth: true,
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/buscar-goal/solution`,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term, // search term
                        id
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
            var textarea = $(`#edit_comentarios`);
            /*  switch (tipo) {
                 case 'edit_problem':
                     console.log('llega hasta aqui');
                     $(`#edit_consequences`).val(null).trigger("change");
                     get_select("edit_consequences", "edit_comentarios", "/buscar-goal/consequence",e.params.data["id"]);
                    
                     break;
                 case 'edit_consequences':
                     $(`#edit_solution`).val(null).trigger("change");
                     get_select("edit_solution", "edit_comentarios", "/buscar-goal/solution",e.params.data["id"]);
                    
                     break;
                 default:
                    
                     break;
             } */
            text_coment('edit_solution', e.params.data["text"], textarea);
        });
}
/* pasando texto  */
function text_coment(tipo, text, textarea) {
    switch (tipo) {
        case 'edit_where_room':
            if (textarea.val() != '') {
                textarea.val(`${textarea.val()}\n \nFloor: ${text}`);
            } else {
                textarea.val(`${textarea.val()}Floor: ${text}`);
            }
            break;
        case 'edit_problem':
            textarea.val(`${textarea.val()} \n Problem:  ${text}`);
            break;
        case 'edit_consequences':
            textarea.val(`${textarea.val()} \n Consequense: ${text}`);
            break;
        case 'edit_solution':
            textarea.val(` ${textarea.val()} \n Solution: ${text}`);
            break;
        default:
            break;
    }
}
function load_select_class() {
    $(".select_class").select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_class_workers`,
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
    });
}
//crud
$("#enviar").click(function (e) {
    $(this).prop("disabled", false);
    e.preventDefault();
    const values = enviar_actividades($("#edit_Pro_ID").val())
    registrar(values);
    var id_proyect = $("select[name=Pro_ID] option").filter(":selected").val();
    if (id_proyect == undefined) {
        id_proyect = $("#Pro_ID").val();
    }
    //send_form();
});

$(document).on("click", ".send_mail", function () {
    var $button = $(this);
    $(".is_mail").val($button.data("part"));
    $(".to").val($("#to").val());
    $(".cc").val($("#cc").val());
    $(".title_m").val($("#title_m").val());
    $(".body_m").val($("#body_m").val());
    send_form();
});

function send_form() {
    let $form = $("#from_goal");
    $.ajax({
        type: "POST",
        url: $form.attr("action"),
        data: $form.serialize(),
        dataType: "json",
        success: function (data) {
            if (data.errors.length > 0) {
                $alert = "complete the following fields to continue:\n";
                data.errors.forEach(function (error) {
                    $alert += `* ${error}\n`;
                });
                alert($alert);
                $("#enviar").prop("disabled", false);
                $("#send_mail").prop("disabled", false);
                $("#send_mail").text("Send Mail");
            } else {
                $form.submit();
            }
        },
    });
}

function get_edit_select_task(pro_id) {
    $(".task").select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/goal/task/${pro_id}`,
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
    }).on("select2:select", function (e) {
        console.log($(this).parent().prev().find('input'))
        $(this).parent().prev().find('.cost_code').val(e.params.data["ActTas"].trim())
    });
}

get_edit_select_where();
get_edit_select_proyectos();
get_edit_select_problem();
get_edit_select_consecuencia(null);
get_edit_select_solucion(null);
get_edit_select_task($('#edit_Pro_ID').val());

$(document).ready(function () {
    VerificarTarea($('#edit_Pro_ID').val());
    console.log('ready',$('#edit_Pro_ID').val())
});
