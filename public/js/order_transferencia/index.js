function validarCampo(formulario) {
    let bandera = true;
    formulario.map((valor) => {
        if (valor.Cantidad == 0 || valor.Cantidad == null) {
            Swal.fire(
                'Error!',
                'quantities exist at 0',
                'error'
            );
            bandera = false;
            return;
        }
        if (valor.Mat_ID == 0 || valor.Mat_ID == null) {
            Swal.fire(
                'Error!',
                'Select material',
                'error'
            );
            bandera = false;
            return;
        }
        if (valor.To_ID == 0 || valor.To_ID == null) {
            Swal.fire(
                'Error!',
                'Select where (To) ',
                'error'
            );
            bandera = false;
            return;
        }
        if (valor.Ven_ID == 0 || valor.Ven_ID == null) {
            Swal.fire(
                'Error!',
                'Select from where (From)',
                'error'
            );
            bandera = false;
            return;
        }
        if (valor.fecha_entrega == '' || valor.fecha_entrega == null) {
            Swal.fire(
                'Error!',
                'Select date deliver',
                'error'
            );
            bandera = false;
            return;
        }
    })
    return bandera;
}

var dataTable = $('#lista_orden_transferencia').DataTable({
    order: [],
    scrollY: "600px",
    scrollX: true,
    scrollCollapse: true,
    language: {
        searchPlaceholder: "Criterion"
    },
    pageLength: 10,
    columns: [{
        data: "nombre_completo",
        name: "nombre_completo",
        render: function (data, type, row, meta, otros) {
            return `${data}`;
        },
        width: 150
    },
    {
        data: "fecha_entrega",
        name: "fecha_entrega",
        render: function (data, type, row, meta) {
            return `
            <input type="text" 
                class="form-control form-control-sm w-100 creado_por" 
                value="${row.creado_por}" autocomplete="off" hidden />
            <input type="text" 
                class="form-control form-control-sm w-100 PO" 
                value="${row.PO}" autocomplete="off" hidden />
            <input type="text" 
                class="form-control form-control-sm w-100 fecha_order" 
                value="${row.fecha_order}" autocomplete="off" hidden />
            <input type="text" 
                class="form-control form-control-sm w-100 Ped_ID" 
                value="${row.Ped_ID}" autocomplete="off" hidden />
            <input type="text" 
                class="form-control form-control-sm w-100 Pro_ID" 
                value="${row.Pro_ID}" autocomplete="off" hidden />
            <input type="text" 
                class="form-control form-control-sm w-100 estado" placeholder="Date"
                value="${row.estado}" autocomplete="off" hidden />
            <input type="text" data-pedido_id="${row.Ped_ID}"  ${row.estado == 'modificado' ? 'disabled' : ''}
                data-estado="${row.estado}"
                data-ped_mat_id="${row.Ped_Mat_ID}"
                class="form-control form-control-sm datepicker w-100 fecha_entrega" placeholder="Date"
                value="${moment(row.fecha_entrega).format('MM/DD/YYYY')}" autocomplete="off"/>
            `;
        },
        width: 80
    },
    {
        data: "PO",
        name: "PO",
        render: function (data, type, row, meta) {
            return `
                <span class="badge badge-info m-1">
                    <p style="margin-bottom: 0rem; color:#ffffff">${row.PO}</p>
                    ${row.estado_po}
                </span>
                <input type="text" class="form-control form-control-sm w-100 estado_po"
                value="${row.estado_po}" autocomplete="off" hidden />
            `;

        },
        width: 200
    },
    {
        data: "enviar",
        name: "enviar",
        render: function (data, type, row, meta) {
            return `
            <select class="form-control form-control-sm select_enviar w-100" 
                data-pedido_id="${row.Ped_ID}" 
                data-ped_mat_id="${row.Ped_Mat_ID}"
                ${row.estado == 'modificado' ? 'disabled' : ''}
                data-estado="${row.estado}"
            > 
                <option value="${row.Ven_ID}">${row.enviar}</option>
            </select>
            `;
        },
        width: 200
    },
    {
        data: 'recibir',
        name: 'recibir',
        render: function (data, type, row, meta) {
            return `
            <select class="form-control form-control-sm select_recibir w-100" 
                data-pedido_id="${row.Ped_ID}"
                data-ped_mat_id="${row.Ped_Mat_ID}"
                ${row.estado == 'modificado' ? 'disabled' : ''}
                data-estado="${row.estado}"
            >
                <option value="${row.To_ID}">${row.recibir}</option>
            </select>
            `;
        },
        width: 200
    },
    {
        data: 'Denominacion',
        name: 'Denominacion',
        render: function (data, type, row, meta) {
            return `
            <select data-proyecto-id="${row.Pro_ID}" 
                class="form-control select_materiales_${row.Pro_ID} material w-100" 
                data-pedido_id="${row.Ped_ID}"
                data-ped_mat_id="${row.Ped_Mat_ID}"
                ${row.estado == 'modificado' ? 'disabled' : ''}
                data-estado="${row.estado}"
            >
                <option value="${row.Mat_ID}">${row.Denominacion}</option>
            </select>
            <input type="text" class="form-control form-control-sm Ped_Mat_ID w-100 "
                value="${row.Ped_Mat_ID}" autocomplete="off" hidden />
            `;
        },
        width: 200
    },
    {
        data: 'note',
        name: 'note',
        render: function (data, type, row, meta) {
            return `
            <input type="text" data-pedido_id="${row.note}"  ${row.estado == 'modificado' ? 'disabled' : ''}
                data-estado="${row.estado}"
                data-ped_mat_id="${row.Ped_Mat_ID}"
                class="form-control form-control-sm w-100 note" placeholder=""
                value="${(row.note == 'null' || row.note == null) ? '' : row.note}" autocomplete="off"/>
            `;
        },
        width: 200
    },
    {
        data: 'Cantidad',
        name: 'Cantidad',
        render: function (data, type, row, meta) {
            return `
            <input type="number" name="cantidad[]" 
                data-pedido_id="${row.Ped_ID}" 
                data-estado="${row.estado}"
                class="form-control form-control-sm cantidad w-100" 
                placeholder="Quantity"
                data-ped_mat_id="${row.Ped_Mat_ID}"
                ${row.estado == 'modificado' ? 'disabled' : ''}
                value="${row.Cantidad}" autocomplete="off" />
            `;
        },
        width: 50
    },
    {
        data: 'cant_warehouse',
        name: 'cant_warehouse',
        render: function (data, type, row, meta) {
            return `${data == null ? 0 : data}`;
        },
        width: 50
    },
    {
        data: 'cant_proyecto',
        name: 'cant_proyecto',
        render: function (data, type, row, meta) {
            return `${data == null ? 0 : data}`;
        },
        width: 50
    },
    {
        data: 'cant_proyecto',
        name: 'cant_proyecto',
        render: function (data, type, row, meta) {
            let html = ``;
            //order prioridad edit
            if (isAdmin == 1) {
                if (row.estado == 'modificado') {
                    html += `<i class='fas fa-pencil-alt ms-text-success edit cursor-pointer' title='Edit' data-ped_mat_id="${row.Ped_Mat_ID}" data-index="${meta.row}" ></i>`;
                }
            }
            else {
                if (Date.parse(moment(row.fecha_order).format('YYYY-MM-DD')) == Date.parse(moment().format('YYYY-MM-DD'))) {
                    if (row.estado == 'modificado') {
                        html += `<i class='fas fa-pencil-alt ms-text-success edit cursor-pointer' title='Edit' data-ped_mat_id="${row.Ped_Mat_ID}" data-index="${meta.row}" ></i>`;
                    }
                }
            }
            //order prioridad delete
            if (isAdmin == 1) {
                html += `<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' title='Delete' data-index="${meta.row}" data-estado="${row.estado}" ></i>`;
            }
            else {
                if (Date.parse(moment(row.fecha_order).format('YYYY-MM-DD')) == Date.parse(moment().format('YYYY-MM-DD'))) {
                    if (row.estado == 'modificado' || row.estado == 'nuevo') {
                        html += `<i class='far fa-trash-alt ms-text-danger delete cursor-pointer' title='Delete' data-index="${meta.row}" data-estado="${row.estado}" ></i>`;
                    }
                }
            }
            return html;
        },
        width: 10
    },
    ],
    createdRow: function (row, data, index) {
        const estado = $(row).find('.estado').val()
        if (estado == 'nuevo') {
            $(row).css("background-color", "#EAEEFF");
        }

    }
}).on("draw", function (e, dt, type, indexes) {
    $(`.datepicker`).datepicker({
        todayHighlight: true,
        dateFormat: "mm/dd/yy"
    });
    selectEnviar();
    selectRecibir();
    selectproyecto();
    selectMaterial();
}).on('click', 'tbody tr', (e) => {

});


$(document).on("click", "#crear_transferencia", function () {
    const datos = rastreoDatos();
    const ultimo_valor = datos[0];
    const evaluar = {
        ...ultimo_valor,
        estado: 'nuevo',
        note: '',
        estado_po: 'New',
        cant_warehouse: 0,
        cant_proyecto: 0,
        Denominacion: '',
        Cantidad: 0,
        Mat_ID: 0,
        Ped_Mat_ID: 0,
        fecha_entrega: moment().format('MM/DD/YYYY'),
        fecha_order: moment().format('MM/DD/YYYY'),
    }
    const data = auto_complementar(evaluar.Ven_ID, evaluar.To_ID, evaluar.fecha_entrega, evaluar.fecha_order, evaluar.creado_por);
    data.then(e => {
        evaluar.estado_po = e.data.estado_po;
        evaluar.PO = e.data.PO;
        datos.unshift(evaluar);
        dataTable.rows().remove();
        dataTable.rows.add(datos).draw();
        datos.map((data) => {
            //actualizacionSelectMateriales(`.select_materiales_${data.Pro_ID}`, data.Pro_ID);
        });
    })
});

$(document).ready(function () {
    reloadDataTable()
});

function reloadDataTable() {
    $.ajax({
        type: "GET",
        url: `${base_url}/order-transfer/data-table`,
        dataType: 'json',
        success: function (response) {
            dataTable.rows().remove();
            dataTable.rows.add(response.data).draw();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    });
}


function selectproyecto() {
    $(`.select_proyecto`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/order-transfer/proyecto-no-vendor`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const estado = $(this).data('estado');
        const proyecto_id = e.params.data['id'];
        const formulario = rastreoDatos();
        let elemento = $(this).parent().next().next().next().find('select');
        if (estado == 'nuevo') {
            console.log('nuevo')
            actualizacionSelectMateriales(elemento, proyecto_id);
        } else {
            console.log('modificar')
            actualizacionSelectMateriales(elemento, proyecto_id);
            const data = formulario.filter((valor) => valor.estado == 'modificado')
            modificacion(data);
        }
    }).on('select2:open', function (e) {

    });
}

function selectEnviar() {
    $(`.select_enviar`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/order-transfer/proyecto-from`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const elemento = $(this);
        const formulario = rastreoDatos();
        if (elemento.data('estado') == 'nuevo') {
            console.log('nuevo')
        } else {
            console.log('modificar')
            const select = elemento.parent().next().next().find('.material');
            $(select).val('').trigger('change');
        }
    }).on('select2:open', function (e) {

    });
}

function selectRecibir() {
    $(`.select_recibir`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/order-transfer/proyecto-to`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const To_ID = e.params.data['id'];
        const formulario = rastreoDatos();
        const elemento = $(this);
        if (elemento.data('estado') == 'nuevo') {
            console.log('nuevo')
            const fecha_entrega = moment($(this).parent().prev().prev().prev().find('.fecha_entrega').val()).format('YYYY-MM-DD');
            //mostrar por adelantado
            let Ven_ID = $(this).parent().prev().find('.select_enviar').val();
            let creado_por = $(this).parent().prev().prev().prev().find('.creado_por').val();
            const data = auto_complementar(Ven_ID, To_ID, fecha_entrega, creado_por);
            data.then(e => {
                const text = elemento.parent().prev().prev().html('').append(
                    `
                        <span class="badge badge-info m-1">
                            <p style="margin-bottom: 0rem; color:#ffffff">${e.data.PO}</p>
                            ${e.data.estado_po}
                        </span>
                    `
                );
            })

        } else {
            console.log('modificar')
            const ped_mat_id = elemento.data('ped_mat_id');
            //elemento.parent().next().find('.material').val('').trigge('change');
            const select = elemento.parent().next().find('.material');
            $(select).val('').trigger('change');
            //const data = formulario.filter((valor) => valor.estado == 'modificado' && valor.Ped_Mat_ID == ped_mat_id)

        }
    }).on('select2:open', function (e) {

    });
}
function selectMaterial() {
    $('.material').select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/order-transfer/material`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                const Ven_ID = $(this).parent().prev().prev().find('select').val();
                const To_ID = $(this).parent().prev().find('select').val();

                return {
                    Ven_ID: Ven_ID,
                    To_ID: To_ID,
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        let cantWarehouse = $(this).parent().next().next().next().text(e.params.data['cant_warehouse']);
        let cantProject = $(this).parent().next().next().next().next().text(e.params.data['cant_proyecto']);
        const elemento = $(this);
        const formulario = rastreoDatos();
        if (elemento.data('estado') == 'nuevo') {
            console.log('nuevo')
        } else {
            console.log('modificar')
            /* const ped_mat_id = elemento.data('ped_mat_id')
            const data = formulario.filter((valor) => valor.estado == 'modificado' && valor.Ped_Mat_ID == ped_mat_id)
            console.log(data)
            modificacion(data); */
        }
    }).on('select2:open', function (e) {

    });
}

$('#lista_orden_transferencia tbody').on('click', '.delete', function () {
    const elemento = $(this)
    if (elemento.data('estado') == 'nuevo') {
        dataTable
            .row($(this).parents('tr'))
            .remove()
            .draw();
    } else {
        const Ped_Mat_ID = elemento.parent().prev().prev().prev().prev().prev().find('.Ped_Mat_ID').val();
        Swal.fire({
            title: 'Are you sure?',
            text: "You will not be able to reverse this! It will eliminate the movements of the material",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: `${base_url}/order-transfer/delete-material-pedido/${Ped_Mat_ID}`,
                    dataType: 'json',
                    async: true,
                    success: function (response) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        );
                        reloadDataTable()
                    }
                });
            }
        });
    }

});

$('#lista_orden_transferencia tbody').on('click', '.edit', function () {
    const elemento = $(this);
    console.log($(this).data('ped_mat_id'))
    //desbloqueo de campo
    bloqueoCampos(elemento, false)
    elemento.removeClass('fas fa-pencil-alt ms-text-success edit')
    elemento.addClass('far fa-check-circle ms-text-primary update')

});
$('#lista_orden_transferencia tbody').on('click', '.update', function () {
    const elemento = $(this);
    const data = []
    //multiples edit
    const formulario = rastreoDatos();
    $('.update').each(
        function (index, val) {
            const position = $(val).data('index');
            data.push(formulario.find((val, index) => index == position))
        }
    );
    console.log(data)
    modificacion(data);
    //bloqueoCampos(elemento,false)
});
function bloqueoCampos(elemento, estado) {
    elemento.parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().find('.fecha_entrega').prop('disabled', estado);
    elemento.parent().prev().prev().prev().prev().prev().prev().prev().find('.select_enviar').prop('disabled', estado);
    elemento.parent().prev().prev().prev().prev().prev().prev().find('.select_recibir').prop('disabled', estado);
    elemento.parent().prev().prev().prev().prev().prev().find('.material ').prop('disabled', estado);
    elemento.parent().prev().prev().prev().prev().find('.note').prop('disabled', estado);
    elemento.parent().prev().prev().prev().find('.cantidad').prop('disabled', estado);
}
$(document).on("click", "#registrar", function () {
    const formulario = rastreoDatos();
    const data = formulario.filter((valor) => valor.estado == 'nuevo');
    store_nuevos(data);
});

function disabled() {

    $('.fecha').prop('disabled', true);
    $('.select_enviar').prop('disabled', true);
    $('.select_recibir').prop('disabled', true);
    $('.select_materiales').prop('disabled', true);
    $('.cantidad').prop('disabled', true);
}

$(document).on("change", ".fecha_entrega, .cantidad, .note", function () {
    const elemento = $(this);
    const formulario = rastreoDatos();
    if (elemento.data('estado') == 'nuevo') {
        console.log('nuevo')
    } else {
        console.log('modificar')
        /* const ped_mat_id = elemento.data('ped_mat_id')
        const data = formulario.filter((valor) => valor.estado == 'modificado' && valor.Ped_Mat_ID == ped_mat_id)
        console.log(data)
        modificacion(data); */
    }
});

function rastreoDatos() {
    var formulario = [];
    const data = dataTable.rows().data();
    data.map((e, i) => {
        const tr = dataTable.row(i).node();
        const nuevo = {
            estado: 'nuevo',
            estado_po: '',
            creado_por: '',
            nombre_completo: "Mario Olmos ",
            fecha_order: moment().format('YYYY-MM-DD HH:mm:ss'),
            fecha_entrega: moment().format('YYYY-MM-DD'),
            enviar: "Warehouse/Shop -Sundries/Equipment/Others-",
            recibir: "Warehouse/Shop -Sundries/Equipment/Others-",
            Denominacion: "",
            Ped_ID: 0,
            note: '',
            Pro_ID: 0,
            Ven_ID: 0,
            To_ID: 1,
            Cantidad: 0,
            Mat_ID: 0,
            Ped_Mat_ID: 0,
            pertenece: "Test 1.3",
            cant_warehouse: 0,
            cant_proyecto: 0
        };
        $(tr).children().map((index, ele) => {
            // console.log(ele, index)
            switch (index) {
                case 0:
                    nuevo.nombre_completo = $(ele).text();
                    break;
                case 1:
                    nuevo.creado_por = $(ele).find('.creado_por').val();
                    nuevo.fecha_order = $(ele).find('.fecha_order').val();
                    nuevo.Ped_ID = $(ele).find('.Ped_ID').val();
                    nuevo.Pro_ID = $(ele).find('.Pro_ID').val();
                    nuevo.PO = $(ele).find('.PO').val();
                    nuevo.fecha_entrega = moment($(ele).find('.fecha_entrega').val()).format('YYYY-MM-DD');
                    nuevo.estado = $(ele).find('.estado').val();
                    break;
                case 2:
                    nuevo.estado_po = $(ele).find('.estado_po').val();
                    break;
                case 3:
                    nuevo.Ven_ID = $(ele).find('.select_enviar').val();
                    nuevo.enviar = $(ele).find('.select_enviar option:selected').text().trim();
                    break;
                case 4:
                    nuevo.To_ID = $(ele).find('.select_recibir').val();
                    nuevo.recibir = $(ele).find('.select_recibir option:selected').text().trim();
                    break;
                case 5:
                    nuevo.Mat_ID = $(ele).find('select').val();
                    nuevo.Denominacion = $(ele).find('select option:selected').text().trim();
                    nuevo.Ped_Mat_ID = $(ele).find('.Ped_Mat_ID').val();
                    break;
                case 6:
                    nuevo.note = $(ele).find('.note').val();
                    break;
                case 7:
                    nuevo.Cantidad = $(ele).find('.cantidad').val();
                    break;
                case 8:
                    nuevo.cant_warehouse = $(ele).text();
                    break;
                case 9:
                    nuevo.cant_proyecto = $(ele).text();
                    break;

                default:
                    break;
            }
        });
        formulario.push(nuevo);
    });
    return formulario;
}

function store_nuevos(formulario) {
    if (validarCampo(formulario)) {
        $.ajax({
            type: "post",
            url: `${base_url}/order-transfer/store`,
            dataType: 'json',
            data: {
                formulario
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
                    reloadDataTable()
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
}


function modificacion(formulario) {
    if (validarCampo(formulario)) {
        console.log('formaulario valido')
        $.ajax({
            type: "post",
            url: `${base_url}/order-transfer/update`,
            dataType: 'json',
            data: {
                formulario
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
                    reloadDataTable()
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
        console.log('formaulario no valido')
    }
}

function auto_complementar(Ven_ID, To_ID, fecha_entrega, fecha_order, creado_por) {
    return $.ajax({
        type: "post",
        url: `${base_url}/order-transfer/verificar-orden`,
        dataType: 'json',
        data: {
            Ven_ID,
            To_ID,
            fecha_entrega,
            creado_por,
            fecha_order
        },
        success: function (response) {
            return response;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            error_status(jqXHR)
        },
        fail: function () {
            fail()
        }
    })
}