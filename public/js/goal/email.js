
//////////////email/////////////
$(document).on('click', '.send-mail', function () {
    $("#mailModal").removeAttr("tabindex");
    $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', []);
    $('#mailModal #cc').attr('name', 'cc').tokenfield('setTokens', []);
    var $icon = $(this);
    var $button = $("#send_mail");
    $button.html("Send Mail");
    $button.prop("disabled", false);
    $("#send_mail").data('id', $(this).data('id'));
    $.ajax({
        url: `${base_url}/get_config_mail/${$(this).data('id')}/goal/${$(this).data('proyecto')}`,
        dataType: "json",
        async: false,
        success: function (response) {

            $("#mailModal #title_m").attr('name', 'title_m').val(
                `${response.config.title_ticket_email} ${response.subempresa}`
            );
            $("#mailModal #body_m").attr('name', 'body_m').text('Please find attached visit report for the project mentioned above');
            $("#mailModal #row_id").val($(this).data('id'));
            var emails = [];
            $.each(response.email_contac, function (index, value) {
                emails.push(value.email);
            });
            emails.push(response.emails.email_contac);
            emails.push(response.emails.Coordinador_Obra_mail);
            emails.push(response.emails.Lead_mail);
            emails.push(response.emails.Pwtsuper_mail);
            emails.push(response.emails.Foreman_mail);
            data = emails.filter(function (element) {
                return element !== undefined && element !== null;
            });

            $('#to').attr('name', 'to').tokenfield('setTokens', data);
            $('#cc').attr('name', 'cc').tokenfield('setTokens', []);
        }
    });

    $("#all_email_to").select2({
        theme: "bootstrap4",
        width: '100%',
        ajax: {
            url: `${base_url}/get-all-email/${$(this).data('id')}`,
            type: "GET",
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
            console.log(e.params.data)
            data.push(e.params.data.email)
            $('#to').tokenfield('setTokens', data);
            $("#all_email_to").val('').change();
            $("#to").trigger("enterKey");
            $('#to').focus();
        });

    let cc=[];
    $("#all_email_cc").select2({
        theme: "bootstrap4",
        width: '100%',
        ajax: {
            url: `${base_url}/get-all-email/${$(this).data('id')}`,
            type: "GET",
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
            cc.push(e.params.data.email)
            $('#cc').tokenfield('setTokens', cc);
            $("#all_email_cc").val('').change();
            $("#cc").trigger("enterKey");
            $('#cc').focus();
        });
    $("#mailModal").modal("show");
});

$(document).on('click', '#send_mail', function () {
    var $button = $(this);
    var $text = $button.text();
    $button.html("Wait.....", true);
    $button.prop("disabled", true);

    $.ajax({
        type: "post",
        url: `${base_url}/send/${$(this).data('id')}/all/goal`,
        data: $("#mailModal #mail").serialize(),
        dataType: "json",
        success: function (data) {
            var html = '';
            if (data.errors) {
                for (var count = 0; count < data.errors.length; count++) {
                    alert(`${data.errors[count]}`);
                }
                html += '</div>';
                $('#mailModal #form_result').html(html);
                $button.html($text);
                $button.prop("disabled", false);
                button.html("Wait.....", false);
            }
            if (data.success) {
                alert(data.success);
                $('#mailModal #mail').trigger("reset");
                $('#mailModal').modal('hide');
                $button.html($text);
                $button.prop("disabled", false);
                $button.html("Wait.....", false);
                var pagina_actual = table.page();
                table.page(pagina_actual).draw('page');
            }
        },
        fail: function (xhr, textStatus, errorThrown) {
            alert('ocurrio un error en la peticion por favor actualize la pagina');
            $button.html($text);
        }
    });
});