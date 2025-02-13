@extends('layouts.panel')
@push('css-header')
    <style>
        @media only screen and (min-width: 580px) {
            .modal-lg {
                max-width: 80% !important;
            }
        }

        .file-footer-buttons>.btn {
            padding: 0.625rem 1rem;
            min-width: 0 !important;
            margin-top: 1rem;
        }
    </style>
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="row">

        <div class="col-md-12">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    {{ \Session::get('success') }}
                </div>
            @endif

            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Submittals</h6>
                </div>
                <div class="ms-panel-body">
                    <p class="ms-directions mb-1">REPORTS:</p>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <button type="button" id="descarga_excel" class="btn btn-primary has-icon btn-sm mt-1">
                                <i class="far fa-file-excel"></i>
                                Export Excel
                            </button>
                            <form action="" method="POST" id="download_excel" hidden> @csrf</form>
                        </div>
                    </div>
                    <form id="form_estadisticas" action="">
                        <p class="ms-directions mb-1">MORE SEARCH OPTIONS:</p>
                        <div class="row pb-2">
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order"
                                        class="col-sm-5 col-form-label col-form-label-sm">Project:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control form-control-sm" id="multiselect_project"
                                            name="multiselect_project[]" multiple="multiple" required style="width:100%">
                                            @foreach ($proyectos as $proyecto)
                                                <option value="{{ $proyecto->Pro_ID }}"
                                                    {{ $proyecto_id == $proyecto->Pro_ID ? 'selected' : '' }}>
                                                    {{ $proyecto->Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order" class="col-sm-5 col-form-label col-form-label-sm">Project
                                        Status:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control form-control-sm" id="status_proyecto"
                                            name="status_proyecto[]" multiple="multiple" style="width:100%">
                                            @foreach ($status_proyecto as $estado)
                                                <option value="{{ $estado->Estatus_ID }}">
                                                    {{ $estado->Nombre_Estatus }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order" class="col-sm-5 col-form-label col-form-label-sm">From
                                        vendor:</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control form-control-sm datepicke"
                                            id="date_from_vendor" name="date_from_vendor" placeholder="Date from vendor"
                                            value="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order" class="col-sm-5 col-form-label col-form-label-sm">To
                                        vendor:</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control form-control-sm datepicke"
                                            id="date_to_vendor" name="date_to_vendor" placeholder="Date to vendor"
                                            value="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col md 3">
                                <div class="form-group row mb-1">
                                    <label for="date_order" class="col-sm-5 col-form-label col-form-label-sm">Category
                                        submittals:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control form-control-sm" id="status_submittals"
                                            name="status_submittals[]" multiple="multiple" style="width:100%">
                                            @foreach ($status_materiales as $status_material)
                                                <option value="{{ $status_material->Cat_ID }}">
                                                    {{ $status_material->Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order" class="col-sm-5 col-form-label col-form-label-sm">From
                                        GC:</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control form-control-sm datepicke"
                                            id="date_from_gc" name="date_from_gc" placeholder="Date from GC" value=""
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order" class="col-sm-5 col-form-label col-form-label-sm">To
                                        GC:</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control form-control-sm datepicke"
                                            id="date_to_gc" name="date_to_gc" placeholder="Date to GC" value=""
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-pill btn-primary d-block mt-0" style="padding: 0.2rem 0.5rem;"
                                        type="button" id="buscar"><i class="fa fa-search"></i> Search</button>
                                    <button class="btn btn-pill btn-warning d-block mt-0" style="padding: 0.2rem 0.5rem;"
                                        type="button" id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div></div>
                                <button type="button" id="add_submittals"
                                    class="btn btn-primary has-icon btn-sm d-inline m-0 mb-1">
                                    Add submittals
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="list-proyectos" class="table table-striped thead-primary">
                                    <thead>
                                        <tr>
                                            <th width="50">Code</th>
                                            <th width="150">Project</th>
                                            <th width="150">Denomination</th>
                                            <th>Unit</th>
                                            <th width="150">Status</th>
                                            <th width="150">Vendor</th>
                                            <th width="100">Date request from Vendor</th>
                                            <th width="100">Date received to Vendor</th>
                                            <th width="120">Note Vendor</th>
                                            <th width="100">Date set to GC</th>
                                            <th width="100">Date received from GC</th>
                                            <th width="120">Note GC</th>
                                            <th width="70">Total Q. Neeeded</th>
                                            <th width="70">Quantity Ordered</th>
                                            <th width="50">Unit Price</th>
                                            <th width="100">Price</th>
                                            <th width="120">Aux 1</th>
                                            <th width="120">Aux 2</th>
                                            <th width="120">Aux 3</th>
                                            <th width="120">Note</th>
                                            <th width="120">Apply to</th>
                                            <th width="50">Acctions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--Modal uploadModal -->
@endsection
@push('javascript-form')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>

    <script>
        //sort por foramto de fecha
        var dataTable = $("#list-proyectos").DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                scrollY: "700px",
                scrollX: true,
                scrollCollapse: true,
                ajax: `${base_url}/submittals/data-table?proyectos=${$('#multiselect_project').val()}&status_submittals=${$('#status_submittals').val()}&status_proyecto=${$('#status_proyecto').val()}&date_from_vendor=${$('#date_from_vendor').val()}&date_to_vendor=${$('#date_to_vendor').val()}&date_from_gc=${$('#date_from_gc').val()}&date_to_gc=${$('#date_to_gc').val()}`,
                language: {
                    searchPlaceholder: "Criterion"
                },
                order: [

                ],
                columns: [{
                        data: 'Codigo',
                        name: 'Codigo',
                        render: function(data, type, row) {
                            return `
                            <input type = "text"
                                class = "form-control form-control-sm editar"
                                name = "description"
                                placeholder = ""
                                value = "${data==null ? '' : data}"
                                autocomplete = "off"
                                data-id="${row.Mat_ID}"
                            >
                        `;
                        }
                    },
                    {
                        data: 'nombre_proyecto',
                        name: 'nombre_proyecto',
                        render: function(data, type, row) {
                            return `
                            <select style="width:auto" class="projects" name="projectos">
                                <option seleted value="${row.Pro_ID}" >${data}</option>
                            </select>
                        `;
                        }
                    },
                    {

                        data: "Denominacion",
                        name: "Denominacion",
                        render: function(data, type, row) {
                            return `
                            <input type = "text"
                                class = "form-control form-control-sm editar"
                                name = "description"
                                placeholder = ""
                                value = "${data==null ? '' : data}"
                                autocomplete = "off"
                                data-id="${row.Mat_ID}"
                            >
                        `;
                        }
                    },
                    {

                        data: "Unidad_Medida",
                        name: "Unidad_Medida",
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: "nombre_categoria",
                        name: "nombre_categoria",

                        render: function(data, type, row) {
                            return `
                            <select style="width:100%" class="tipo_materiales" name="tipo_materiales">
                                <option seleted value="${row.Cat_ID}" >${data}</option>
                            </select>
                        `;
                        }
                    },
                    {
                        data: 'nombre_proveedor',
                        name: 'nombre_proveedor',
                        render: function(data, type, row) {
                            return `
                            <select class="proveedor" name="proveedor">
                                <option seleted value="${row.Ven_ID}" >${data}</option>
                            </select>
                        `;
                        }
                    },
                    {
                        data: 'Fecha_from_vendor',
                        name: 'Fecha_from_vendor',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm datepicker editar"
                                    type="date"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Fecha_to_vendor',
                        name: 'Fecha_to_vendor',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm datepicker editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'note_vendor',
                        name: 'note_vendor',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Fecha_to_gc',
                        name: 'Fecha_to_gc',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm datepicker editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Fecha_from_gc',
                        name: 'Fecha_from_gc',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm datepicker editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'note_gc',
                        name: 'note_gc',
                        render: function(data, type, row) {

                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Cantidad',
                        name: 'Cantidad',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'cantidad_ordenada',
                        name: 'cantidad_ordenada'
                    },
                    {
                        data: 'Precio_Unitario',
                        name: 'Precio_Unitario',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Precio',
                        name: 'Precio',
                        render: function(data, type, row) {
                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Aux1',
                        name: 'Aux1',
                        render: function(data, type, row) {

                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Aux2',
                        name: 'Aux2',
                        render: function(data, type, row) {

                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Aux3',
                        name: 'Aux3',
                        render: function(data, type, row) {

                            return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                        `;
                        }
                    },
                    {
                        data: 'Nombre_Generico',
                        name: 'Nombre_Generico',
                        render: function(data, type, row) {
                            return `
                            <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    type="date"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                            `;
                        }
                    },
                    {
                        data: 'Area_donde_va',
                        name: 'Area_donde_va',
                        render: function(data, type, row) {
                            return `
                            <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    type="date"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Mat_ID}"
                                >
                            `;
                        }
                    },
                    {
                        data: 'Mat_ID',
                        name: 'Acctions',
                        render: function(data, type, row) {
                            return `
                            <i class="far fa-trash-alt ms-text-danger delete cursor-pointer" data-id="${row.Mat_ID}" title="Delete"></i>
                        `;
                        }
                    }
                ],
                initComplete: function(settings, json) {

                },
                pageLength: 100,
            })
            .on("draw.dt", function(e, dt, type, indexes) {
                InizializeSelects()
                InizializeDatepicker()
            });

        function InizializeDatepicker() {
            $('.datepicker').datepicker({
                todayHighlight: true,
                dateFormat: "mm/dd/yy"
            });
        }

        function InizializeSelects() {
            $(document).ready(function() {
                $('.projects').select2({
                    theme: "bootstrap4",
                    dropdownAutoWidth: true,
                    width: 'auto',
                    ajax: {
                        url: `${base_url}/goal-structure/select-proyecto`,
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
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        error_status(jqXHR)
                    },
                    fail: function() {
                        fail()
                    }
                }).on('select2:select', function(e) {
                    console.log($(this).parent().parent().children(':first-child').find('input').val(e
                        .params.data.Codigo))
                    console.log(e.params.data)

                });
                $('.tipo_materiales').select2({
                    theme: "bootstrap4",
                    dropdownAutoWidth: true,
                    width: 'auto',
                    ajax: {
                        url: `${base_url}/submittals/category-submittals`,
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
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        error_status(jqXHR)
                    },
                    fail: function() {
                        fail()
                    }
                }).on('select2:select', function(e) {
                    console.log(e.params.data)
                });
                $('.proveedor').select2({
                    theme: "bootstrap4",
                    dropdownAutoWidth: true,
                    width: 'auto',
                    ajax: {
                        url: `${base_url}/submittals/proveedor-submittals`,
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
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        error_status(jqXHR)
                    },
                    fail: function() {
                        fail()
                    }
                }).on('select2:select', function(e) {
                    console.log(e.params.data)
                });
            })
        }
    </script>
    <script>
        $(document).on("keyup", ".editar", function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                let form = {
                    Mat_ID: '',
                    Pro_ID: '',
                    Ven_ID: '',
                    Cat_ID: '',
                    Denominacion: '',
                    Nombre_Generico: '',
                    Area_donde_va: '',
                    Unidad_Medida: '',
                    Cantidad: '',
                    Precio_Unitario: '',
                    Precio: '',
                    Aux1: '',
                    Aux2: '',
                    Aux3: '',
                    Fecha_Registro: '',
                    Fecha_Envio: '',
                    Fecha_Recibido: '',
                    Fecha_from_vendor: '',
                    Fecha_to_vendor: '',
                    note_vendor: '',
                    Fecha_from_gc: '',
                    Fecha_to_gc: '',
                    note_gc: ''
                }
                let inputs = $(this).parent().parent().children().each((i, ele) => {

                    switch (i) {
                        case 0:
                            //console.log($(ele).find('input').val())
                            form.Nombre = $(ele).find('input').val();
                            break;
                        case 1:
                            form.Pro_ID = $(ele).find('select').find(":selected").val();
                            break;
                        case 2:
                            form.Denominacion = $(ele).find('input').val();
                            break;
                        case 3:
                            form.Unidad_Medida = $(ele).find('input').val();
                            break;
                        case 4:
                            form.Cat_ID = $(ele).find('select').find(":selected").val();
                            break;
                        case 5:
                            form.Ven_ID = $(ele).find('select').find(":selected").val();
                            break;
                        case 6:
                            console.log($(ele).find('input').val())
                            form.Fecha_from_vendor = $(ele).find('input').val();
                            break;
                        case 7:
                            form.Fecha_to_vendor = $(ele).find('input').val();
                            break;
                        case 8:
                            form.note_vendor = $(ele).find('input').val();
                            break;
                        case 9:
                            form.Fecha_to_gc = $(ele).find('input').val();
                            break;
                        case 10:
                            form.Fecha_from_gc = $(ele).find('input').val();
                            break;
                        case 11:
                            form.note_gc = $(ele).find('input').val();
                            break;
                        case 12:
                            form.Cantidad = $(ele).find('input').val();
                            break;

                        case 14:
                            form.Precio_Unitario = $(ele).find('input').val();
                            break;
                        case 15:
                            form.Precio = $(ele).find('input').val();
                            break;
                        case 16:
                            form.Aux1 = $(ele).find('input').val();
                            break;
                        case 17:
                            form.Aux2 = $(ele).find('input').val();
                            break;
                        case 18:
                            form.Aux3 = $(ele).find('input').val();
                            break;
                        case 19:
                            form.Nombre_Generico = $(ele).find('input').val();
                            break;
                        case 20:
                            form.Area_donde_va = $(ele).find('input').val();
                            break;
                        default:
                            break;
                    }
                })
                console.log(form)
                $.ajax({
                    type: 'PUT',
                    url: `${base_url}/submittals/update/${$(this).data('id')}`,
                    dataType: 'json',
                    data: form,
                    async: true,
                    success: function(response) {
                        console.log(response)
                        if (response.status == 'ok') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(".delete_temporal").parent().parent().remove();
                            dataTable.ajax.url(
                                `${base_url}/submittals/data-table?proyectos=${$('#multiselect_project').val()}&status_submittals=${$('#status_submittals').val()}&status_proyecto=${$('#status_proyecto').val()}&date_from_vendor=${$('#date_from_vendor').val()}&date_to_vendor=${$('#date_to_vendor').val()}&date_from_gc=${$('#date_from_gc').val()}&date_to_gc=${$('#date_to_gc').val()}`
                            ).draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                'An error occurred',
                                'error'
                            );
                        }
                    }
                });
            }
        });
        //nuevo
        $(document).on("keyup", ".store_temporal", function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                store_submittals(e, this)
            }
        });
        $(document).on("click", ".store_temporal_btn", function(e) {
            store_submittals(e, this)
        });

        function store_submittals(e, input) {
            let form = {
                Mat_ID: '',
                Pro_ID: '',
                Ven_ID: '',
                Cat_ID: '',
                Denominacion: '',
                Nombre_Generico: '',
                Area_donde_va: '',
                Unidad_Medida: '',
                Cantidad: '',
                Precio_Unitario: '',
                Precio: '',
                Aux1: '',
                Aux2: '',
                Aux3: '',
                Fecha_Registro: '',
                Fecha_Envio: '',
                Fecha_Recibido: '',
                Fecha_from_vendor: '',
                Fecha_to_vendor: '',
                note_vendor: '',
                Fecha_from_gc: '',
                Fecha_to_gc: '',
                note_gc: ''
            }
            let inputs = $(input).parent().parent().children().each((i, ele) => {

                switch (i) {
                    case 0:
                        //console.log($(ele).find('input').val())
                        form.Nombre = $(ele).find('input').val();
                        break;
                    case 1:
                        form.Pro_ID = $(ele).find('select').find(":selected").val();
                        break;
                    case 2:
                        form.Denominacion = $(ele).find('input').val();
                        break;
                    case 3:
                        form.Unidad_Medida = $(ele).find('input').val();
                        break;
                    case 4:
                        form.Cat_ID = $(ele).find('select').find(":selected").val();
                        break;
                    case 5:
                        form.Ven_ID = $(ele).find('select').find(":selected").val();
                        break;
                    case 6:
                        console.log($(ele).find('input').val())
                        form.Fecha_from_vendor = $(ele).find('input').val();
                        break;
                    case 7:
                        form.Fecha_to_vendor = $(ele).find('input').val();
                        break;
                    case 8:
                        form.note_vendor = $(ele).find('input').val();
                        break;
                    case 9:
                        form.Fecha_to_gc = $(ele).find('input').val();
                        break;
                    case 10:
                        form.Fecha_from_gc = $(ele).find('input').val();
                        break;
                    case 11:
                        form.note_gc = $(ele).find('input').val();
                        break;
                    case 12:
                        form.Cantidad = $(ele).find('input').val();
                        break;

                    case 14:
                        form.Precio_Unitario = $(ele).find('input').val();
                        break;
                    case 15:
                        form.Precio = $(ele).find('input').val();
                        break;
                    case 16:
                        form.Aux1 = $(ele).find('input').val();
                        break;
                    case 17:
                        form.Aux2 = $(ele).find('input').val();
                        break;
                    case 18:
                        form.Aux3 = $(ele).find('input').val();
                        break;
                    case 19:
                        form.Nombre_Generico = $(ele).find('input').val();
                        break;
                    case 20:
                        form.Area_donde_va = $(ele).find('input').val();
                        break;
                    default:
                        break;
                }
            })
            console.log(form)
            $.ajax({
                type: 'POST',
                url: `${base_url}/submittals/store`,
                dataType: 'json',
                data: form,
                async: true,
                success: function(response) {
                    if (response.status == 'ok') {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $(".delete_temporal").parent().parent().remove();
                        dataTable.ajax.url(
                            `${base_url}/submittals/data-table?proyectos=${$('#multiselect_project').val()}&status_submittals=${$('#status_submittals').val()}&status_proyecto=${$('#status_proyecto').val()}&date_from_vendor=${$('#date_from_vendor').val()}&date_to_vendor=${$('#date_to_vendor').val()}&date_from_gc=${$('#date_from_gc').val()}&date_to_gc=${$('#date_to_gc').val()}`
                        ).draw();
                    } else {
                        Swal.fire(
                            'Error!',
                            'An error occurred',
                            'error'
                        );
                    }
                }
            });
        }
        $(document).on("click", "#add_submittals", function(e) {
            $("#list-proyectos").append(`
                 <tr>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "code"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id="0"
                        >
                    </td>
                    <td>
                        <select style="width:auto" class="projects" name="projectos">
                        </select>
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <select style="width:100%" class="tipo_materiales" name="tipo_materiales">
                        </select>
 
                    </td>
                    <td>
                        <select class="proveedor" name="proveedor">
                        </select>
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm datepicker store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm datepicker store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm datepicker store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm datepicker store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                       
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <input type = "text"
                            class = "form-control form-control-sm store_temporal"
                            name = "project"
                            placeholder = ""
                            value = ""
                            autocomplete = "off"
                            data-id=""
                        >
                    </td>
                    <td>
                        <i class="far fa-check-circle ms-text-primary store_temporal_btn cursor-pointer" title="Save"></i>
                        <i class="far fa-trash-alt ms-text-danger delete_temporal cursor-pointer" title="Delete"></i>
                    </td>
                 </tr>
            `);
            InizializeSelects()
            InizializeDatepicker()
        });

        $(document).on("click", ".delete_temporal", function(e) {
            $(this).parent().parent().remove()
        });

        $(document).on("click", ".delete", function(e) {
            let id = $(this).data('id');
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
                        url: `${base_url}/submittals/delete/${id}`,
                        dataType: 'json',
                        async: true,
                        success: function(response) {
                            if (response.status == 'ok') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                dataTable.ajax.url(
                                    `${base_url}/submittals/data-table?proyectos=${$('#multiselect_project').val()}&status_submittals=${$('#status_submittals').val()}&status_proyecto=${$('#status_proyecto').val()}&date_from_vendor=${$('#date_from_vendor').val()}&date_to_vendor=${$('#date_to_vendor').val()}&date_from_gc=${$('#date_from_gc').val()}&date_to_gc=${$('#date_to_gc').val()}`
                                ).draw();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        })
    </script>
    <script src="{{ asset('js/submittals/list.js') }}"></script>

    <script>
        $("#filtro_proyectos").select2({
            multiple: true,
        });
    </script>
@endpush
