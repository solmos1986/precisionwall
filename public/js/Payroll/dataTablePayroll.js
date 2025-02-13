var datatable_payroll_data = $('#datatable_payroll_data').DataTable({pageLength: 100,}).clear();

function load_payroll_data(id,fecha) {
    datatable_payroll_data.destroy();
    datatable_payroll_data = $('#datatable_payroll_data').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollY: "950px",
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        ajax: `${base_url}/payroll/datatable-data/${id}?fecha=${fecha}`,
        columns: [
            {
                data: 'NickName',
                name: "NickName",
                width: 200,
                targets: 0,
                render: function (data, type, full, meta) {
                    let render = ``;
                    if (full.NickName) {
                        render = `
                        <select class="${full.id}_empleado" style="width:100%">
                            <option>${full.NickName}</option>
                        </select>`;
                    } else {
                        render = `
                        <select class="${full.id}_empleado" style="width:100%">
                            <option style="background-color:green" value="Error Project">Error Project</option>
                        </select>`;
                    }
                    return render;
                }
            },
            {
                data: 'nombreProyecto',
                name: "nombreProyecto",
                width: 200,
                targets: 0,
                render: function (data, type, full, meta) {
                    let render = ``;
                    if (full.nombreProyecto) {
                        render = `
                        <select class="${full.id}_project" style="width:100%">
                            <option>${full.nombreProyecto}</option>
                        </select>`;
                    } else {
                        render = `
                        <select class="${full.id}_project" style="width:100%"'>
                            <option style="background-color:green" value="Error Project">Error Project</option>
                        </select>`;
                    }
                    return render;
                }
            },
            {
                data: 'nombreEdificio',
                name: "nombreEdificio",
                width: 200,
                targets: 0,
                render: function (data, type, full, meta) {
                    //console.log(full.Pro_ID)
                    let render = ``;
                    if (full.nombreEdificio) {
                        render = `
                        <select class="${full.id}_edificio" style="width:100%">
                            <option>${full.nombreEdificio}</option>
                        </select>`;
                    } else {
                        render = `
                        <select class="${full.id}_edificio" style="width:100%">
                            <option style="background-color:green" >Error Project</option>
                        </select>`;
                        console.log('on close')
                    }
                    return render;
                }
            },
            {
                data: 'nombreFloor',
                name: "nombreFloor",
                width: 200,
                targets: 0,
                render: function (data, type, full, meta) {
                    //console.log(full.Pro_ID)
                    let render = ``;
                    if (full.nombreFloor) {
                        render = `
                        <select class="${full.id}_floor" style="width:100%">
                            <option>${full.nombreFloor}</option>
                        </select>`;
                    } else {
                        render = `
                        <select class="${full.id}_floor" style="width:100%">
                            <option style="background-color:green" value="Error Project">Error Project</option>
                        </select>`;
                    }
                    return render;
                }
            },
            {
                data: 'nombreArea',
                name: "nombreArea",
                width: 200,
                targets: 0,
                render: function (data, type, full, meta) {
                    //console.log(full.Pro_ID)
                    let render = ``;
                    if (full.nombreArea) {
                        render = `
                        <select class="${full.id}_area" style="width:100%">
                            <option>${full.nombreArea}</option>
                        </select>`;
                    } else {
                        render = `
                        <select class="${full.id}_area"  style="width:100%">
                            <option style="background-color:green" value="Error Project">Error Project</option>
                        </select>`;
                    }
                    return render;
                }
            },
            {
                data: 'costCode',
                name: "costCode",
                width: 200,
                targets: 0,
                render: function (data, type, full, meta) {
                    let render = ``;
                    if (full.costCode) {
                        render = `
                        <select class="${full.id}_task" style="width:100%" >
                            <option>${full.costCode}</option>
                        </select>`;
                    } else {
                        render = `
                        <select class="${full.id}_task"  style="width:100%">
                            <option style="background-color:green" value="Error Project">Error Project</option>
                        </select>`;
                    }
                    return render;
                }
            },
            {
                data: 'cat',
                name: "cat"
            },
            {
                data: 'horas',
                name: 'horas',
            },
            {
                data: 'hr_type',
                name: "hr_type",
            },
            {
                data: 'PayId',
                name: "PayId"
            },
            {
                data: 'work_date',
                name: "work_date"
            },
            {
                data: 'cert_class',
                name: "cert_class"
            },
            {
                data: 'reimbId',
                name: "reimbId"
            },
            {
                data: 'unit',
                name: "unit"
            },
            {
                data: 'um',
                name: "um"
            },
            {
                data: 'rate',
                name: "rate"
            },
            {
                data: 'amount',
                name: "amount"
            },
            {
                data: 'acciones',
                name: 'acciones',
            }
        ],
        pageLength: 100,
        order: [

        ],
        createdRow: function (row, data, dataIndex) {
            if (data.Pro_ID == 0 || data.Edificio_ID == 0 || data.Floor_ID == 0 || data.Area_ID == 0 || data.Task_ID == 0) {
                //console.log(data.id)
                return $(row).css("background-color", "#FF8E8E");
            }
        },
        
    });

}
$("#datatable_payroll_data").on("draw.dt", function () {

    $("#datatable_payroll_data").Tabledit({
        editButton: false,
        deleteButton: false,
        restoreButton: false,
        url: "{{ route('update.proyectos') }}",
        dataType: "json",
        columns: {
            identifier: [18, "id"],
            editable: [
                [6, "hr_type", , 'text']
            ],
        },
        pageLength: 100,
        onSuccess: function (data, textStatus, jqXHR) {
            if (data.action == "delete") {
                $("#" + data.id).remove();
                $("#list-proyectos").DataTable().ajax.reload();
            }
        },
        onDraw: function () {
            var data_table = datatable_payroll_data
                .rows()
                .data().toArray();
            //console.log(data_table)
            data_table.forEach(data => {
                select2empleados(data.id);
                select2project(data.id);
                select2Edificio(data.id, data.Pro_ID);
                select2Floor(data.id, data.Edificio_ID);
                select2Area(data.id, data.Floor_ID);
                select2Task(data.id, data.Area_ID);

                $('.select2-selection__rendered').hover(function () {
                    $(this).removeAttr('title');
                });
            });
            $('table tr td:nth-child(3) input, table tr td:nth-child(4) input')
                .each(function () {
                    $(this).datepicker({
                        todayHighlight: true,
                        dateFormat: "mm/dd/yy"
                    });
                });
            $('.change_asistente_proyecto').select2();
        },
    });
});

function select2empleados(id) {
    $(`.${id}_empleado`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/payroll/select-empleado`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                    payroll_id: id // search term 
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
            var data = e.params.data;
            editarEmpleado(data.payroll_id, data.id);
        })
}
function select2project(id) {
    $(`.${id}_project`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/payroll/select-project`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                    payroll_id: id // search term 
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
            var data = e.params.data;
            editarProyecto(data.payroll_id, data.id);
        })
}
function select2Edificio(id, proyecto_id) {
    $(`.${id}_edificio`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/payroll/select-edificio`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term 
                    pro_id: proyecto_id,
                    payroll_id: id
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
            var data = e.params.data;
            editarEdificio(data.payroll_id, data.id)
        });
}
function select2Floor(id, edificio_id) {
    $(`.${id}_floor`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/payroll/select-floor`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                    edificio_id: edificio_id,
                    payroll_id: id  // search term 
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true,
        },
    })
        .on("select2:select", function (e) {
            var data = e.params.data;
            editarFloor(data.payroll_id, data.id)
        })
}
function select2Area(id, floor_id) {
    $(`.${id}_area`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/payroll/select-area`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                    floor_id: floor_id,
                    payroll_id: id  // search term 
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
            console.log(e);
            var data = e.params.data;
            editarArea(data.payroll_id, data.id)
        })
}
function select2Task(id, area_id) {
    $(`.${id}_task`).select2({
        theme: "bootstrap4",
        ajax: {
            url: `${base_url}/payroll/select-task`,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term,
                    area_id: area_id,
                    payroll_id: id  // search term 
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
            console.log(e);
            var data = e.params.data;
            editarTask(data.payroll_id, data.id)
        });
}

function editarEmpleado(payroll_id, id) {
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/update-payroll-empleado`,
        data: {
            payroll_id: payroll_id,
            empleado_id: id
        },
        dataType: 'json',
        success: function (response) {
            datatable_payroll_data.ajax.url(
                `${base_url}/payroll/datatable-data/${$('#payroll_id_eject').val()}`
            ).load();

        }
    });
}
function editarProyecto(payroll_id, id) {
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/update-payroll-project`,
        data: {
            payroll_id: payroll_id,
            proyecto_id: id
        },
        dataType: 'json',
        success: function (response) {
            datatable_payroll_data.ajax.url(
                `${base_url}/payroll/datatable-data/${$('#payroll_id_eject').val()}`
            ).load();
        },
    });
}
function editarEdificio(payroll_id, id) {
    //console.log('valor a cambiar', payroll_id, id)
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/update-payroll-edificio`,
        data: {
            payroll_id: payroll_id,
            edificio_id: id
        },
        dataType: 'json',
        success: function (response) {
            datatable_payroll_data.ajax.url(
                `${base_url}/payroll/datatable-data/${$('#payroll_id_eject').val()}`
            ).load();

        }
    });
}
function editarFloor(payroll_id, id) {
    //console.log('valor a cambiar', payroll_id, id)
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/update-payroll-floor`,
        data: {
            payroll_id: payroll_id,
            floor_id: id
        },
        dataType: 'json',
        success: function (response) {
            datatable_payroll_data.ajax.url(
                `${base_url}/payroll/datatable-data/${$('#payroll_id_eject').val()}`
            ).load();

        }
    });
}
function editarArea(payroll_id, id) {
    console.log('valor a cambiar', payroll_id, id)
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/update-payroll-area`,
        data: {
            payroll_id: payroll_id,
            area_id: id
        },
        dataType: 'json',
        success: function (response) {
            datatable_payroll_data.ajax.url(
                `${base_url}/payroll/datatable-data/${$('#payroll_id_eject').val()}`
            ).load();

        }
    });
}
function editarTask(payroll_id, id) {
    console.log('valor a cambiar', payroll_id, id)
    $.ajax({
        type: 'POST',
        url: `${base_url}/payroll/update-payroll-task`,
        data: {
            payroll_id: payroll_id,
            task_id: id
        },
        dataType: 'json',
        success: function (response) {
            datatable_payroll_data.ajax.url(
                `${base_url}/payroll/datatable-data/${$('#payroll_id_eject').val()}`
            ).load();
        }
    });
}


/*Export payroll */
$(document).on('click', '.download_payroll_data', function () {
    descarga($(this).data('id'));
});
function descarga(id) {
    $('#descargar_payroll').attr("action", `${base_url}/payroll/export-txt?id=${id}`);
    $("#descargar_payroll").submit();
}