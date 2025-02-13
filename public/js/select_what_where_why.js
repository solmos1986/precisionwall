get_select("where");
get_select("why");
get_select("what");

function get_select(select) {
    var $textarea = $("#descripcion");
    $(`#${select}`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/get_razon/${select}/${Pro_ID}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function(response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        if (select == "where") {
            $textarea.val(`${$textarea.val()} ${e.params.data['text']}`);
            $(`#${select}`).val(null).trigger("change");

        } else {
            (select == "what") ? $textarea.val(`${$textarea.val()} \r\n${e.params.data['descripcion']}`): $textarea.val(`${$textarea.val()} ${e.params.data['descripcion']}`);
            $(`#${select}`).val(null).trigger("change");

        }

    });
}