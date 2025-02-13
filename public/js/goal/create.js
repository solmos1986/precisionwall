
/* proyectos */
function get_new_select_proyectos() {
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
            $("#new_address").val(e.params.data["dirrecion"]);
            $("#new_project").val(e.params.data["text"]);
            $("#new_codigo").val(e.params.data["Codigo"]);
            $("#new_general").val(e.params.data["emp"]);
            $(`#new_where_room`).val(null).trigger("change");
            get_new_select_where(true);
            get_new_select_task(e.params.data["id"])
            $("#table-actividad tbody").html('');
            VerificarTarea(e.params.data["id"]);
            $('.add-actividad').prop('disabled', false);
        });
}
/* where */
function get_new_select_where(text = false) {
    $(`#new_where_room`)
        .select2({
            width: 'auto',
            dropdownAutoWidth: true,
            theme: "bootstrap4",
            ajax: {
                url: `${base_url}/where-goal/where/${$('#proyect').val()}`,
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
            var textarea = $(`#new_comentarios`);
            if (text) {
                text_coment('new_where_room', e.params.data["text"], textarea);
            }
        });
}
/* problema */

function get_new_select_problem() {
    $(`#new_problem`)
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
            var textarea = $(`#new_comentarios`);
            text_coment('new_problem', e.params.data["text"], textarea);
        });
}

/* consecuencia */
function get_new_select_consecuencia(id) {
    $(`#new_consequences`)
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
            var textarea = $(`#new_comentarios`);
            text_coment('new_consequences', e.params.data["text"], textarea);
        });
}
/* solucion */
function get_new_select_solucion(id) {
    $(`#new_solution`)
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
            var textarea = $(`#new_comentarios`);
            text_coment('new_solution', e.params.data["text"], textarea);
        });
}
/* pasando texto  */
function text_coment(tipo, text, textarea) {

    switch (tipo) {
        case 'new_where_room':
            if (textarea.val() != '') {
                textarea.val(`${textarea.val()}\n \nFloor: ${text}`);
            } else {
                textarea.val(`${textarea.val()}Floor: ${text}`);
            }
            break;
        case 'new_problem':
            textarea.val(`${textarea.val()} \n Problem:  ${text}`);
            break;
        case 'new_consequences':
            textarea.val(`${textarea.val()} \n Consequense: ${text}`);
            break;
        case 'new_solution':
            textarea.val(` ${textarea.val()} \n Solution Suggested: ${text}`);
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
    const values = enviar_actividades($("#proyect").val())
    registrar(values);
    var id_proyect = $("select[name=Pro_ID] option").filter(":selected").val();
    if (id_proyect == undefined) {
        id_proyect = $("#Pro_ID").val();
    }
    send_form();
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

/*ejecuciones */
get_new_select_proyectos();
get_new_select_where(false);
get_new_select_problem();
get_new_select_consecuencia(null);
get_new_select_solucion(null);

$(document).ready(function () {
    $('.add-actividad').prop('disabled', true);
});
