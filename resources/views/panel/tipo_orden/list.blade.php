@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <style>
        @media only screen and (min-width: 720px) {
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
    <style>
        td.details-control {
            cursor: pointer;
            font-size: 22px;
            color: #07be6e;
            cursor: pointer;
        }

        tr.details td.details-control {
            color: rgb(167, 165, 165);
        }
    </style>
    <style>
        td.details-control-sub-orden {
            cursor: pointer;
            font-size: 22px;
            color: #07be6e;
            cursor: pointer;
        }

        tr.details td.details-control-sub-orden {
            color: rgb(167, 165, 165);
        }
    </style>
    <style>
        td.details-control-sub-orden-material {
            cursor: pointer;
            font-size: 22px;
            color: #07be6e;
            cursor: pointer;
        }

        tr.details td.details-control-sub-orden-material {
            color: rgb(167, 165, 165);
        }
    </style>
    <style>
        td.details-control-orden-materiales {
            cursor: pointer;
            font-size: 22px;
            color: #07be6e;
            cursor: pointer;
        }

        tr.details td.details-control-orden-materiales {
            color: rgb(167, 165, 165);
        }
    </style>
    <style>
        legend {
            font-size: 1.0rem;
        }
    </style>
    <style>
        .big-checkbox {
            width: 20px;
            height: 20px;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    {{ \Session::get('success') }}
                </div>
            @endif
            {{ Breadcrumbs::render('list order') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>List Order/Deliveres/Pick-Ups</h6>
                    @if (Auth::user()->verificarRol([1, 3, 10]))
                        <div class="d-flex justify-content-md-around">
                            <div>
                                <button class="btn btn-pill btn-primary btn-sm" id="create_orden">Request
                                    Material/Equipment</button>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="ms-panel-body">
                    <div class="container d-flex justify-content-center">
                        <div class="row">
                            <div class="col-md mb-3">
                                <label>Status filter
                                    <select name="status_order_requerimiento" id="status_order_requerimiento"
                                        placeholder="Status filter" class="form-control form-control-sm" multiple>
                                        @foreach ($status as $val)
                                            <option value="{{ $val->id }}">{{ $val->nombre }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list-orden" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Num</th>
                                    <th>Project</th>
                                    <th wihtd="250">PO's</th>
                                    <th>Status</th>
                                    <th>Date Schedule</th>
                                    @if (Auth::user()->verificarRol([1, 10]))
                                        <th>Created by</th>
                                    @endif
                                    <th width="170">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Modal Eliminar  -->
    <x-components.delete-modal />
    <!--Modal -->
    <x-components.mail-modal title="order" />
    {{-- modals order a vendor --}}
    <x-components.tipo-orden.list.vendor.create :proveedores='$proveedores' :status='$status' :vendors='$vendors' />
    <x-components.tipo-orden.list.vendor.edit :proveedores='$proveedores' :status='$status' :vendors='$vendors' />
    <x-components.tipo-orden.list.vendor.delete />
    {{-- modal de segimiento --}}
    <x-components.tipo-orden.list.seguimiento.create :proveedores='$proveedores' :status='$status' />
    {{-- modal de recepecion --}}
    <x-components.tipo-orden.list.recepcion.create :proveedores='$proveedores' :status='$status' />
    {{-- modal de trasferencia --}}
    <x-components.tipo-orden.list.transferencia.create :proveedores='$proveedores' :status='$status' />
    <x-components.tipo-orden.list.transferencia.edit-deliver :proveedores='$proveedores' :status='$status' />
    {{-- modal de orden --}}
    <x-components.tipo-orden.list.view-email />
    <x-components.tipo-orden.list.edit-order :status='$status' />
    <x-components.tipo-orden.list.list-materiales />
    <x-components.tipo-orden.movimiento-material.view />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>

    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    {{-- imagenes --}}
    <!-- the main fileinput plugin script JS file -->
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/buffer.min.js"
        type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/filetype.min.js"
        type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>


    <script>
        function format(data) {
            return `
    @if (Auth::user()->verificarRol([1, 3, 10]))
        <fieldset class="border p-4  ml-5">
            <legend class="w-auto"><i class="fas fa-eye ms-text-primary cursor-pointer view_request_materiales"
                    title="View"></i>Q. REQUEST MATERIALS:</legend>
            <div class="px-6" id="ocultar_view_request_materiales">
                <table id="list_materiales_orden" class="table thead-primary table-bordered w-100">
                    <thead>
                        <tr>
                            <!--th> </th-->
                            <th width="260">Material/Equipment</th>
                            <th width="20">Unit</th>
                            <th width="20">Q. Required</th>
                            <th width="20">Q. To order</th>
                            <th width="20"></th>
                            <th width="20">Q. at Warehouse</th>
                            <th width="20">Q. at Project</th>
                            <th width="20">Q. at vendor</th>
                            <th width="20">Total Quantity ordered</th>
                            <th width="20">Q. Used</th>
                        </tr>
                    </thead>
                    <tbody>
    
                    </tbody>
                </table>
    
                <button type="button" class="btn btn-primary has-icon ml-2 create_sub_orden" data-vendedor_id="${data.id}"
                    data-orden_id="${data.id}"><i class="fas fa-box-open"></i>Order to Vendor</button>
    
                <button type="button" class="btn btn-primary has-icon ml-2 view_materiales" data-orden_id="${data.id}"
                    data-name_proyecto="${data.proyecto}"><i class="fa fa-list-alt"></i>List Materials</button>
                <button type="button" class="btn btn-primary has-icon create_transferencia" data-vendedor_id="${data.id}"
                    data-orden_id="${data.id}"><i class="fas fa-exchange-alt"></i>Deliveries/Transfers</button>
                <!--button type="button" class="btn btn-primary has-icon " data-vendedor_id="${data.id}" data-orden_id="${data.id}"><i class="fa fa-truck"></i></i>Deliveries/Pick Up Request</button>
                                <button type="button" class="btn btn-primary has-icon create_seguimiento" data-vendedor_id="${data.id}" data-orden_id="${data.id}"><i class="fa fa-archive"></i>Store/Vendor</button-->
            </div>
        </fieldset>
    @endif
    <fieldset class="border p-4  ml-5" >
            <legend class="w-auto">ORDER:</legend>
        <div class="px-6">
            <table id="list_sub_orden" class="table thead-primary table-bordered w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th>PO</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>From</th>
                        <th>To</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
   </fieldset>
    `;
        };

        function format_materiales(data) {
            return `
    <fieldset class="border p-4  ml-5">
        <legend class="w-auto" > TRAKING:</legend>
        <div class="px-6">
            <table id="list_sub_orden_materiales" class="table thead-primary table-bordered w-100 ">
                <thead>
                <tr>
                    <th>PO</th>
                    <th>Received amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Date next control</th>
                    <th>Next control Description</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </fieldset>
    `;
        };

        function format_movimientos_suborden(data) {
            return `
    <fieldset class="border p-4 ml-5">
        <legend class="w-auto" >TRAKING:</legend>
        <div class="px-6">
            <table id="list_sub_orden_movimientos" class="table thead-primary table-bordered w-100 ">
                <thead>
                    <tr>
                        <th>Material/Equipement</th>
                        <th>Status</th>
                        <th>Q. Ordered</th>
                        <th>Q. Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </fieldset>
    `;
        };

        var sub_order;
        var list_materiales_orden;
        var sub_orden_movimiento;
        var materiales;
        var table = $('#list-orden').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `${base_url}/order/list-data-table?status=${$('#status_order_requerimiento').val()}`,
            order: [
                [1, "desc"]
            ],
            columns: [{
                    "class": "details-control success",
                    "orderable": false,
                    "data": null,
                    "defaultContent": '<i class="fas fa-plus-circle"></i>'
                },
                {
                    data: "num",
                    name: "num"
                },
                {
                    data: "proyecto",
                    name: "proyecto"
                },
                {
                    data: "po",
                    name: "po"
                },
                {
                    data: "status",
                    name: "status"
                },
                {
                    data: "date_work",
                    name: "date_work"
                },
                @if (Auth::user()->verificarRol([1, 10]))
                    {
                        data: "username",
                        name: "username"
                    },
                @endif {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            columnDefs: [{
                width: "400px",
                targets: 3
            }, ],
            pageLength: 10,
        });
        var detailRows = [];
        $('#list-orden tbody').on('click', 'tr td.details-control', function() {

            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                table.rows().eq(0).each(function(idx) {
                    var row = table.row(idx);

                    if (row.child.isShown()) {
                        row.child.hide();
                    }
                });

                tr.addClass('details');
                row.child(format(row.data())).show();

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
                //auto detecion de  proyecto en tiempo de ejecucion
                $('#list_proyecto').val(row.data().proyecto);
                $('#new_proyecto').val(row.data().proyecto);
                $('#edit_proyecto').val(row.data().proyecto);
                //sub-ordenes
                sub_order = $('#list_sub_orden').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: `${base_url}/sub-order/list-data-table/${row.data().id}`,
                    order: [
                        [0, "desc"]
                    ],
                    columns: [{
                            "class": "details-control-sub-orden success",
                            "orderable": false,
                            "data": null,
                            "defaultContent": '<i class="fas fa-plus-circle"></i>'
                        },
                        {
                            data: "PO",
                            name: "PO"
                        },
                        {
                            data: "Fecha",
                            name: "Fecha"
                        },
                        {
                            data: "status",
                            name: "status"
                        },
                        {
                            data: "from",
                            name: "from"
                        },
                        {
                            data: "to",
                            name: "to"
                        },
                        {
                            data: 'acciones',
                            name: 'acciones',
                            orderable: false
                        }
                    ],
                    pageLength: 100,
                });
                /* ocultar materiales request */
                if (row.data().po != "") {
                    $(".view_request_materiales").trigger("click");
                }
                // materiales
                list_materiales_orden = $('#list_materiales_orden').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: `${base_url}/order-materiales/list-data-table/${row.data().id}`,
                    order: [
                        [0, "desc"]
                    ],
                    columns: [{
                            data: "Denominacion",
                            name: "Denominacion"
                        },
                        {
                            data: "Unidad_Medida",
                            name: "Unidad_Medida"
                        },
                        {
                            data: "cant_registrada",
                            name: "cant_registrada"
                        },
                        {
                            data: "cant_ordenada",
                            name: "cant_ordenada"
                        },
                        {
                            data: "check",
                            name: "check"
                        },
                        {
                            data: "total_warehouse",
                            name: "total_warehouse"
                        },
                        {
                            data: "total_proyecto",
                            name: "total_proyecto"
                        },
                        {
                            data: "total_proveedor",
                            name: "total_proveedor"
                        },
                        {
                            data: "cantidad_ordenada",
                            name: "cantidad_ordenada"
                        },
                        {
                            data: "cantidad_usada",
                            name: "cantidad_usada"
                        },
                    ],
                    pageLength: 100,
                });
            }

        });
        // On each draw, loop over the `detailRows` array and show any child rows
        table.on('draw', function() {
            $.each(detailRows, function(i, id) {
                $('#' + id + ' td.details-control').trigger('click');
            });
        });

        /*materiales*/
        $(document).on('click', ' #list_materiales_orden  tbody tr.odd  td.details-control-orden-materiales.success',
            function() {
                var tr = $(this).closest('tr');
                var row = list_materiales_orden.row(tr);
                var idx = $.inArray(tr.attr('id'), detailRows);

                if (row.child.isShown()) {
                    tr.removeClass('details');
                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice(idx, 1);
                } else {
                    list_materiales_orden.rows().eq(0).each(function(idx) {
                        var row = list_materiales_orden.row(idx);

                        if (row.child.isShown()) {
                            row.child.hide();
                        }
                    });
                    tr.addClass('details');
                    row.child(format_materiales(row.data())).show();

                    // Add to the 'open' array
                    if (idx === -1) {
                        detailRows.push(tr.attr('id'));
                    }
                    console.log(row.data())
                    materiales = $('#list_sub_orden_materiales').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: `${base_url}/order-materiales/list-data-table/${row.data().id}/materiales/${row.data().tipo_orden_id}`,
                        order: [
                            [0, "desc"]
                        ],
                        columns: [{
                                data: "po",
                                name: "po"
                            },
                            {
                                data: "cantidad",
                                name: "cantidad"
                            },
                            {
                                data: "fecha",
                                name: "fecha"
                            },
                            {
                                data: "nombre",
                                name: "nombre"
                            },
                            {
                                data: "fecha_espera",
                                name: "fecha_espera"
                            },
                            {
                                data: "nota",
                                name: "nota"
                            },
                        ],
                    });
                }
            });
        $(document).on('click', ' #list_materiales_orden  tbody tr.even td.details-control-orden-materiales.success',
            function() {
                //console.log('capturando')
                var tr = $(this).closest('tr');
                var row = list_materiales_orden.row(tr);
                var idx = $.inArray(tr.attr('id'), detailRows);

                if (row.child.isShown()) {
                    tr.removeClass('details');
                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice(idx, 1);
                } else {
                    list_materiales_orden.rows().eq(0).each(function(idx) {
                        var row = list_materiales_orden.row(idx);

                        if (row.child.isShown()) {
                            row.child.hide();
                        }
                    });
                    tr.addClass('details');
                    row.child(format_materiales(row.data())).show();

                    // Add to the 'open' array
                    if (idx === -1) {
                        detailRows.push(tr.attr('id'));
                    }
                    materiales = $('#list_sub_orden_materiales').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: `${base_url}/order-materiales/list-data-table/${row.data().id}/materiales/${row.data().tipo_orden_id}`,
                        order: [
                            [0, "desc"]
                        ],
                        columns: [{
                                data: "po",
                                name: "po"
                            },
                            {
                                data: "cantidad",
                                name: "cantidad"
                            },
                            {
                                data: "fecha",
                                name: "fecha"
                            },
                            {
                                data: "nombre",
                                name: "nombre"
                            },
                            {
                                data: "fecha_espera",
                                name: "fecha_espera"
                            },
                            {
                                data: "nota",
                                name: "nota"
                            },
                        ],
                        pageLength: 100
                    });
                }
            });


        /*sub orden*/
        $(document).on('click', ' #list_sub_orden  tbody tr.odd  td.details-control-sub-orden.success', function() {
            var tr = $(this).closest('tr');
            var row = sub_order.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                sub_order.rows().eq(0).each(function(idx) {
                    var row = sub_order.row(idx);

                    if (row.child.isShown()) {
                        row.child.hide();
                    }
                });
                tr.addClass('details');
                row.child(format_movimientos_suborden(row.data())).show();

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
                sub_orden_movimiento = $('#list_sub_orden_movimientos').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: `${base_url}/order-materiales/list-data-table/${row.data().tipo_orden_id}/materiales/${row.data().Ped_ID}`,
                    order: [
                        [0, "desc"]
                    ],
                    columns: [{
                            data: "Denominacion",
                            name: "Denominacion"
                        },
                        {
                            data: "status",
                            name: "status"
                        },
                        {
                            data: "Cantidad",
                            name: "Cantidad"
                        },
                        {
                            data: "cantidad_recibida",
                            name: "cantidad_recibida"
                        },
                        {
                            data: "acciones",
                            name: "acciones"
                        }
                    ],
                    pageLength: 100
                });
            }
        });
        $(document).on('click', ' #list_sub_orden  tbody tr.even td.details-control-sub-orden.success', function() {
            //console.log('capturando')
            var tr = $(this).closest('tr');
            var row = sub_order.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                tr.removeClass('details');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                sub_order.rows().eq(0).each(function(idx) {
                    var row = sub_order.row(idx);

                    if (row.child.isShown()) {
                        row.child.hide();
                    }
                });
                tr.addClass('details');
                row.child(format_movimientos_suborden(row.data())).show();
                console.log(row.data())
                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
                sub_orden_movimiento = $('#list_sub_orden_movimientos').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: `${base_url}/order-materiales/list-data-table/${row.data().tipo_orden_id}/materiales/${row.data().Ped_ID}`,
                    order: [
                        [0, "desc"]
                    ],
                    columns: [{
                            data: "Denominacion",
                            name: "Denominacion"
                        },
                        {
                            data: "status",
                            name: "status"
                        },
                        {
                            data: "Cantidad",
                            name: "Cantidad"
                        },
                        {
                            data: "cantidad_recibida",
                            name: "cantidad_recibida"
                        },
                        {
                            data: "acciones",
                            name: "acciones"
                        }
                    ],
                    pageLength: 100
                });
            }
        });
    </script>
@endpush
@push('javascript-form')
    <script>
        $(function() {
            $(".TodayTime").datetimepicker({
                defaultDate: $('#TodayTime').val(),
                format: 'HH:mm:ss',
                timeFormat: 'HH:mm:ss',
                pickDate: false,
                pickSeconds: false,
                pick12HourFormat: false,
                onSelect: function(datetimeText, datepickerInstance) {
                    if (!datepickerInstance.timeDefined) {
                        $(".TodayTime").datetimepicker('hide')
                    }
                }
            })
        });
        /* extras */
        $(document).on("click", ".view_request_materiales", function() {
            if ($('#ocultar_view_request_materiales').is(":visible")) {
                $('#ocultar_view_request_materiales').hide()
            } else {
                $('#ocultar_view_request_materiales').show();
            }
            var i = $('.view_request_materiales');
            i.attr('class', i.hasClass('fa-eye-slash') ?
                'fas fa-eye view_request_materiales ms-text-primary cursor-pointer ' :
                'fas fa-eye-slash view_request_materiales ms-text-primary cursor-pointer ');
        });

        $('#ocultar_view_request_materiales').hide();

        //multiselect status
        $(document).ready(function() {

            $('#status_order_requerimiento').multiselect({
                buttonClass: 'form-control form-control-sm',
                buttonWidth: '100%',
                includeSelectAllOption: true,
                selectAllText: 'select all',
                selectAllValue: 'multiselect-all',
                enableCaseInsensitiveFiltering: true,
                enableFiltering: true,
                maxHeight: 400,
                placeholder: "Status filter",
                nonSelectedText: 'Satus filter',
            });
        });

        $('#status_order_requerimiento').change(function() {
            table.ajax.url(
                `${base_url}/order/list-data-table?status=${$('#status_order_requerimiento').val()}`,
            ).load();
        });
    </script>
    {{-- pedido order --}}
    <script src="{{ asset('js/tipo-orden/list/vendor/create.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/vendor/edit.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/vendor/delete.js') }}"></script>
    {{-- segimiento --}}
    <script src="{{ asset('js/tipo-orden/list/seguimiento/create.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/recepcion/create.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/recepcion/input-file.js') }}"></script>
    {{-- transferencia --}}
    <script src="{{ asset('js/tipo-orden/list/transferencia/create.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/transferencia/edit-deliver.js') }}"></script>
    {{-- ordenes --}}
    <script src="{{ asset('js/tipo-orden/list/edit_orden.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/delete_orden.js') }}"></script>

    <script src="{{ asset('js/tipo-orden/list/create_movimiento.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/view_email.js') }}"></script>

    <script src="{{ asset('js/tipo-orden/list/vendor/view_materiales.js') }}"></script>
    <script src="{{ asset('js/tipo-orden/list/view_materiales.js') }}"></script>
    {{-- mini crud movimiento material --}}
    <script src="{{ asset('js/tipo-orden/list/movimiento_material/view.js') }}"></script>
@endpush
