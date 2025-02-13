$(document).on('click', '.upload_image', function () {
    $('#uploadModal .modal-title').text(`Upload images ${$(this).data('image')}`)
    $('#uploadModal').modal('show')
    $('#uploadModal .modal-body').html('')
    $('#uploadModal .modal-body').html(`<div class="file-loading">
          <input id="images" name="images[]" type="file" accept="image/*" multiple>
        </div>`)
    fileinput_images($(this).data('id'), 'images', $(this).data('image'), 'orden')
})

$(document).on('click', '.send-mail', function () {
    var $icon = $(this)
    var $button = $('#send_mail')
    $button.html('Send Mail')
    $button.prop('disabled', false)

    console.log($icon.data('num'))
    $.ajax({
        url: `${base_url}/get_config_mail/${$icon.data('project')}/orden`,
        dataType: 'json',
        async: false,
        success: function (response) {
            $('#mailModal #title_m')
                .attr('name', 'title_m')
                .val(`Job: ${response.config.title_ticket_email}`)
            $("#mailModal").removeAttr("tabindex");
            $('#mailModal #body_m').attr('name', 'body_m').text(`Please find attached order for the project mention above `);
            $('#mailModal #row_id').val($icon.data('id'))
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
            console.log(data)
            $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', data);
            $('#mailModal#cc').attr('name', 'cc').tokenfield('setTokens', []);
        },
    });
    $("#all_email_to").select2({
        theme: "bootstrap4",
        width: '100%',
        ajax: {
            url: `${base_url}/get-all-email/${$icon.data('project')}`,
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
            data.push(e.params.data.email)
            $('#mailModal #to').attr('name', 'to').tokenfield('setTokens', data);
            $("#all_email_to").val('').change();
            $("#mailModal #to").trigger("enterKey");
            $('#mailModal #to').focus();
        });
    let cc=[];
    $("#all_email_cc").select2({
        theme: "bootstrap4",
        width: '100%',
        ajax: {
            url: `${base_url}/get-all-email/${$icon.data('project')}`,
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
            $('#mailModal #cc').attr('name', 'cc').tokenfield('setTokens', cc);
            $("#all_email_cc").val('').change();
            $("#mailModal #cc").trigger("enterKey");
            $('#mailModal #cc').focus();
        });
    $('#mailModal').modal('show')
})

$(document).on('click', '#send_mail', function () {
    var $button = $('#send_mail')
    $button.html('Wait.....')
    $button.prop('disabled', true)
    $.ajax({
        type: 'post',
        url: `${base_url}/send/${$('#mailModal #row_id').val()}/orden`,
        data: $('#mailModal #mail').serialize(),
        dataType: 'json',
        success: function (data) {
            var html = ''

            if (data.errors) {
                html = '<div class="alert alert-danger">'
                data.errors.forEach(function (error) {
                    html += `<p>${error}</p>`
                })
                html += '</div>'
                $('#form_result').html(html)
                $button.html('Send Mail')
            }
            if (data.success) {
                alert(data.success)
                $('#mailModal #mail').trigger('reset')
                $('#mailModal').modal('hide')
                $button.html('Send Mail')
            }
            var pagina_actual = table.page();
            table.page(pagina_actual).draw('page');
        },
        fail: function (xhr, textStatus, errorThrown) {
            alert('ocurrio un error en la peticion por favor actualize la pagina')
            $button.html('Send Mail')
            $button.prop('disabled', false)
        },
    })
})

$(document).on('click', '.delete', function () {
    var id = $(this).data('id')
    $('#deleteModal #delete_button').data('id', id)
    $('#deleteModal').modal('show')
})
$(document).on('click', '#delete_button', function () {
    $.ajax({
        type: 'DELETE',
        url: `${base_url}/orden/${$(this).data('id')}/destroy`,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                alert(data.success)
                table.draw()
                $('#deleteModal').modal('hide')
            }
        },
    })
})

/*multiple mail */
//control de check
var orders = [];

$(document).on('click', '#multiple_email', function () {
    proyecto = [];
    num = [];
    orders = [];//limpiando array antes de inspecionar
    $(".orders").remove();
    $('input[type="checkbox"]').each(function () {
        if (this.checked) {
            num.push($(this).data('num'));
            proyecto.push($(this).data('proyecto'));
            orders.push(this.value);
        }
    })

    if (orders.length == 0) {
        alert('select at least one order')
    } else {
        $.ajax({
            type: "POST",
            url: `${base_url}/post_config_mail/multiples-order`,
            data: { proyecto },
            dataType: "json",
            success: function (response) {
                var emails = [];
                $.each(response.email_contac, function (index, value) {
                    emails.push(value.email);
                });
                emails.push(response.emails.email_contac);
                emails.push(response.emails.Coordinador_Obra_mail);
                emails.push(response.emails.Lead_mail);
                emails.push(response.emails.Pwtsuper_mail);
                data = emails.filter(function (element) {
                    return element !== undefined && element !== null;
                });

                $('#multipleMailModal #to').attr('name', 'to').tokenfield('setTokens', response.emails.Foreman_mail);
                $('#multipleMailModal #cc').attr('name', 'cc').tokenfield('setTokens', data);
                $("#multipleMailModal #body_m").attr('name', 'body_m').text('Please find attached order for the project mention above ');

                num.forEach(element => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-info m-1 orders'
                    button.innerText = `${element}`;
                    $("#data").append(button);
                });
                $("#multipleMailModal #title_m").attr('name', 'title_m').val(`${response.config.title_ticket_email} ${num}`);

            },
            fail: function (xhr, textStatus, errorThrown) {
                alert('ocurrio un error en la peticion por favor actualize la pagina');
                $button.html("Send Mail");
                $button.prop("disabled", false);
            }
        });
        $("#multipleMailModal").modal("show");
    }
});
$(document).on('click', '#send_multiple_mail', function () {
    var $button = $(this);
    var $button = $("#multipleMailModal #send_multiple_mail");
    $button.html("Wait.....");
    $button.prop("disabled", true);
    $.ajax({
        type: "post",
        url: `${base_url}/send/multiple-orden`,
        data: {
            cc: $("#multipleMailModal #mail #cc").val(),
            to: $("#multipleMailModal #mail #to").val(),
            body_m: $("#multipleMailModal #mail #body_m").val(),
            title_m: $("#multipleMailModal #mail #title_m").val(),
            orders: orders
        },
        dataType: "json",
        success: function (data) {
            var html = '';
            if (data.errors) {
                data.errors.forEach(function (error) {
                    alert(error);
                });
                html += '</div>';
                $('#form_result').html(html);
                $button.html("Send Mail");
                $button.prop("disabled", false);
            }
            if (data.success) {
                alert(data.success);
                $('#multipleMailModal #mail').trigger("reset");
                $('#multipleMailModal').modal('hide');
                $button.html("Send Mail");
                $button.prop("disabled", false);
                $('input[type="checkbox"]').each(function () {
                    $(this).prop("checked", false);
                });
                var pagina_actual = table.page();
                table.page(pagina_actual).draw('page');
            }
        },
        fail: function (xhr, textStatus, errorThrown) {
            alert('ocurrio un error en la peticion por favor actualize la pagina');
            $button.html("Send Mail");
            $button.prop("disabled", false);
        }
    });
});