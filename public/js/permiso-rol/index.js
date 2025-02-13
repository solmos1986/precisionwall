var table = $('#list_rol').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: `${base_url}/permisos-rol/data-table`,
    order: [
        [0, "desc"]
    ],
    columns: [
        {
            data: "nombre",
            name: "nombre"
        },
        {
            data: "modulos",
            name: "modulos",
            render: function (data, type, row, meta) {
                const modulos = row.modulos.split(",");
                var moduloHTML = ``;
                modulos.map((modulo) => {
                    moduloHTML += `<span class="badge badge-outline-secondary" style='font-size: 85%; margin:0.5px;'>${modulo}</span>`;
                })
                return moduloHTML;
            },
        },
        {
            data: 'id',
            name: 'id',
            render: function (data, type, row, meta) {
                return `
                <i class='fas fa-pencil-alt ms-text-success edit cursor-pointer' data-id="${row.id}" title='Edit'></i>
                <i class='far fa-trash-alt ms-text-danger delete cursor-pointer' data-id="${row.id}" title='Delete'></i>
                            `;
            },
        }
    ],
    pageLength: 100
});


$(document).on('click', '.edit', function () {
    $('#save_button').data('estado', 'editar');
    $('.modal-title').text('Edit');
    $('#nombre').val('');
    const rol_id = $(this).data('id');
    $.ajax({
        type: "get",
        url: `${base_url}/permisos-rol/edit/${rol_id}`,
        dataType: 'json',
        success: function (response) {
            console.log(response)
            if (response.status == 'ok') {
                var elementHTML = ``;
                response.data.modulos.forEach(mod => {
                    elementHTML += generateCard(mod.modulo_id, mod.verificado, mod.nombre_modulo, mod.sub_modulos)
                });
                $('#nombre').val(response.data.rol.nombre);
                $('#nombre').data('id', response.data.rol.id)
                $('#content_modulos').html('');
                $('#content_modulos').append(elementHTML);
                $('#ModalPermisoRol').modal('show');
            } else {

            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
});

$(document).on('click', '.nuevo', function () {
    $('#save_button').data('estado', 'nuevo');
    $('.modal-title').text('Create')
    $('#nombre').val('');
    $.ajax({
        type: "get",
        url: `${base_url}/permisos-rol/create`,
        dataType: 'json',
        success: function (response) {
            $('#modal-title').text('Create')
            if (response.status == 'ok') {
                var elementHTML = ``;
                response.data.modulos.forEach(mod => {
                    elementHTML += generateCard(mod.modulo_id, mod.verificado, mod.nombre_modulo, mod.sub_modulos)
                });
                $('#nombre').data('id', 0)
                $('#content_modulos').html('');
                $('#content_modulos').append(elementHTML);
                $('#ModalPermisoRol').modal('show');
            } else {

            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
});

//generate card modulos
function generateCard(modulo_id, verificado, nombre_modulo, listaSubModulo) {
    var sub_modulo = ``;
    listaSubModulo.forEach(value => {
        sub_modulo += `
        <li style="margin-bottom: 5px;">
            <label class="ms-checkbox-wrap ms-checkbox-primary">
                <input  type="checkbox" data-nombre="${value.nombre_sub_modulo}" value="${value.sub_modulo_id}" ${value.verificado ? 'checked' : ''}>
                <i class="ms-checkbox-check"></i>
            </label>
            <span>${value.nombre_sub_modulo}</span>
        </li>`;
    });
    return `
        <div class="col-md-4">
            <div class="ms-card">
                <div class="ms-card-body">
                    <div class="row ">
                        <div class="col-md-12 modulo">
                            <ul class="ms-list ms-list-display">
                                <li style="margin-bottom: 5px;">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="checkbox" data-nombre="${nombre_modulo}" value="${modulo_id}" class="modulo" ${verificado ? 'checked' : ''}>
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span>${nombre_modulo}</span>
                                </li>
                                <hr>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <ul class="ms-list ms-list-display submodulo">
                                ${sub_modulo}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}
function recurperarInformacion() {
    var resultado = [];
    const modulos = $('#content_modulos').find('div.modulo');
    modulos.map((i, ele) => {
        var modulo_id = $(ele).find('input.modulo').val();
        var nombre_modulo = $(ele).find('input.modulo').data('nombre');
        var verificado = $(ele).find('input.modulo').prop('checked');

        var buscarSubModulo = $(ele).next().children()[0];
        var SubModulo = $(buscarSubModulo).children();
        //submodulos
        var sub_modulos = [];
        SubModulo.map((i, ele) => {
            var ultimoElemento = $(ele).children()[0];
            var data = $(ultimoElemento).children()[0];
            var input = $(data).val()
            var inputNombre = $(data).data('nombre')
            var verificado = $(data).prop('checked')

            const sub_modulo = {
                sub_modulo_id: input,
                nombre_sub_modulo: inputNombre,
                verificado: verificado,
                modulo_id: modulo_id
            }
            sub_modulos.push(sub_modulo)
        })
        //console.log(SubModulo)
        resultado.push(
            {
                modulo_id: modulo_id,
                nombre_modulo: nombre_modulo,
                verificado: verificado,
                sub_modulos: sub_modulos
            }
        )
    });
    return resultado;
}

$(document).on('click', '#save_button', function () {
    const valores = recurperarInformacion();
    if ($(this).data('estado') == 'nuevo') {
        $.ajax({
            type: "POST",
            url: `${base_url}/permisos-rol/store`,
            dataType: 'json',
            data: {
                modulos: valores,
                rol: {
                    nombre_rol: $('#nombre').val(),
                    rol_id: $('#nombre').data('id')
                }
            },
            success: function (response) {
                if (response.status == 'ok') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    table.draw();
                    $('#ModalPermisoRol').modal('hide');
                } else {
                    $alert = "";
                    response.message.forEach(function (error) {
                        $alert += `* ${error}<br>`;
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'complete the following fields to continue:',
                        html: $alert,
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                error_status(jqXHR)
            },
            fail: function () {
                fail()
            }
        })
    }
    else {
        $.ajax({
            type: "PUT",
            url: `${base_url}/permisos-rol/update`,
            dataType: 'json',
            data: {
                modulos: valores,
                rol: {
                    nombre_rol: $('#nombre').val(),
                    rol_id: $('#nombre').data('id')
                }
            },
            success: function (response) {
                if (response.status == 'ok') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    table.draw();
                    $('#ModalPermisoRol').modal('hide');
                } else {
                    $alert = "";
                    response.message.forEach(function (error) {
                        $alert += `* ${error}<br>`;
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'complete the following fields to continue:',
                        html: $alert,
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                error_status(jqXHR)
            },
            fail: function () {
                fail()
            }
        })
    }
});


$(document).on('click', '.delete', function () {
    const rol_id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'DELETE',
                url: `${base_url}/permisos-rol/delete/${rol_id}`,
                dataType: 'json',
                async: true,
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                    table.draw();
                }
            });
        }
    });
});

