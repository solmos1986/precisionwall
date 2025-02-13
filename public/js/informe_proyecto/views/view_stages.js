$(document).on('change', '.change_reg', function () {
    /*evaluando dato */
    $.ajax({
        type: "POST",
        url: `${base_url}/info-project/update_hrs_cont`,
        data: {
            horas_con: $('#horas_con').val(),
            id: $(this).data('proyecto_id')
        },
        dataType: "json",
        async: false,
        success: function (response) {
            if (response.status = true) {
                var data;
                if ($('.change_reg').val() == 'Select option') {
                    data = {
                        dato: '',
                    }
                } else {
                    data = {
                        dato: $('.change_reg').val(),
                        id: $('.change_reg').data('proyecto_id'),
                    }
                }
                $.ajax({
                    type: "POST",
                    url: `${base_url}/store/stages`,
                    data: data
                    ,
                    dataType: "json",
                    async: false,
                    success: function (response) {
                        $("#modal").modal("show");
                        $('#stage tbody').html("");
                        var trHTML = '';
                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.Nombre + '</td><td>' + item
                                .Fecha_Inicio + '</td><td>' + item.Fecha_Fin +
                                '</td><td>' + item.Horas + '</td><td>' + item.Note +
                                '</td></tr>';
                        });
                        if (response.alert) {
                            alert(response.alert);
                        }
                        $('#stage tbody').append(trHTML);
                        $('.change_reg').prop('selectedIndex',0);
                    }
                });
              
            } else {
                alert('error');
            }
        }
    });


});