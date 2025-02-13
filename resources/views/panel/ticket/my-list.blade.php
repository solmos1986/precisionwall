@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <style>
        @media only screen and (min-width: 580px) {
            .modal-lg {
                max-width: 80% !important;
            }
        }

    </style>
    <style>
        .puntos-suspencivos {
            width: 250px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
    </style>
    <style>
        .icon-badge-group {}

        .icon-badge-group .icon-badge-container {
            display: inline-block;
            margin-left: 15px;
        }

        .icon-badge-group .icon-badge-container:first-child {
            margin-left: 0
        }

        .icon-badge-container {
            margin-top: 1px;
            position: relative;
        }

        .icon-badge-icon {
            font-size: 30px;
            position: relative;
        }

        .icon-badge {
            background-color: red;
            font-size: 12px;
            color: white;
            text-align: center;
            width: 19px;
            height: 17px;
            border-radius: 35%;
            position: absolute;
            /* changed */
            top: -7px;
            /* changed */
            left: 8px;
            /* changed */
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
            <div class="invisible" id="status_crud"></div>
            {{ Breadcrumbs::render('tickets') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>My Tickets</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="container d-flex justify-content-center">
                        <div class="row">
                            <div class="col-md mb-3">
                                <input type="text" name="from_date" id="from_date"
                                    class="form-control form-control-sm datepicke" placeholder="From Date"
                                    value="{{ date('m/d/Y') }}" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <input type="text" name="to_date" id="to_date"
                                    class="form-control form-control-sm datepicke" placeholder="To Date"
                                    value="{{ date('m/d/Y') }}" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <input type="text" name="proyecto" id="proyecto" class="form-control form-control-sm"
                                    placeholder="Proyect" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <input type="text" name="descripcion" id="descripcion"
                                    class="form-control form-control-sm" placeholder="Description" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <button type="button" name="refresh" id="refresh" class="btn btn-primary btn-sm mt-0"><i
                                        class="fas fa-retweet"></i></button>
                            </div>
                            @if (Auth::user()->verificarRol([1,10]))
                                <div class="col-md mb-3">
                                    <button type="button" id="multiple_email" class="btn btn-primary btn-sm mt-0">
                                        <i class='fas fa-envelope' title='Send Mail'></i>
                                </div>
                            @endif
                            @if (Auth::user()->verificarRol([1,10]))
                                <div class="col-md mb-3">
                                    <input type="number" style="width: 75%" name="uso_descarga" id="uso_descarga"
                                        class="form-control form-control-sm" placeholder="Download" autocomplete="off" />
                                </div>
                            @else
                                <div class="col-md mb-3">
                                    <input type="number" style="width: 75%" name="uso_descarga" id="uso_descarga"
                                        class="form-control form-control-sm" placeholder="Download" autocomplete="off"
                                        hidden />
                                </div>
                            @endif
                            @if (Auth::user()->verificarRol([1,10]))
                                <div class="col-md mb-3">
                                    <button type="button" id="descarga_excel" class="btn btn-primary btn-sm mt-0">
                                        <i class='fas flaticon-excel' title='Send Mail'></i> Download excel
                                </div>
                                <form action="" method="POST" id="download_excel" hidden> @csrf</form>
                            @endif
                            @if (Auth::user()->verificarRol([1,10]))
                                <div class="col-md mb-3">
                                    <button type="button" id="view_pdf" class="btn btn-primary btn-sm mt-0">
                                        <i class='fas flaticon-pdf' title='Send Mail'></i> View pdf
                                </div>
                                
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list-ticket" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Cod Project</th>
                                    <th>Name Project</th>
                                    <th width="50">Num</th>
                                    <th>PCO#</th>
                                    <th width="150px">Date Ticket</th>
                                    <th width="250px">Description</th>
                                    <th>Schedule</th>
                                    <th>User</th>
                                    <th>Client's signature</th>
                                    <th>Foreman Signature</th>
                                    <th>Startup images</th>
                                    <th>Final images</th>
                                    @if (Auth::user()->verificarRol([1,10]))
                                        <th>select to send email</th>
                                    @endif
                                    <th width="220">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Modal Eliminar -->
    <x-components.delete-modal />

    <!--Modal uploadModal -->
    <x-components.upload-modal />
    <!--Modal mailModal -->
    <x-components.mail-modal title="tickets" />
    <!--Modal multiple mailModal -->
    <x-components.multiple-mail title="tickets" />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/dataRender/ellipsis.js"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>
    <script>
        var table = $('#list-ticket').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `{{ route('listar.mis.tickets') }}?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&uso_descarga=${$('#uso_descarga').val()}`,
            order: [],
            columns: [{
                    data: "Codigo",
                    name: "Codigo"
                },
                {
                    data: "Nombre",
                    name: "Nombre"
                },
                {
                    data: "d_num",
                    name: "d_num"
                },
                {
                    data: "pco",
                    name: "pco"
                },
                {
                    data: "fecha_ticket",
                    name: "fecha_ticket"
                },
                {
                    data: "descripcion",
                    name: "descripcion"
                },
                {
                    data: "horario",
                    name: "horario"
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: "firma_cliente",
                    name: "firma_cliente"
                },
                {
                    data: "firma_foreman",
                    name: "firma_foreman"
                },
                {
                    data: "inicio",
                    name: "inicio",
                    orderable: false
                },
                {
                    data: "final",
                    name: "final",
                    orderable: false
                },
                @if (Auth::user()->verificarRol([1,10]))
                    {
                        data: "check_email",
                        name: 'check_email'
                    },
                @endif {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                },
            ],
            columnDefs: [{
                targets: 5,
                render: $.fn.dataTable.render.ellipsis(100, true)
            }],
            pageLength: 100
        });
        /* filtro de busqueda ticket */
        $('#from_date, #to_date, #proyecto, #descripcion, #uso_descarga').change(function() {
            if ($("#to_date").val() == "") {
                $("#to_date").val($("#from_date").val());
            }
            table.ajax.url(
                `{{ url('tickets') }}?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&descripcion=${$('#descripcion').val()}&uso_descarga=${$('#uso_descarga').val()}`
            ).load();
            var rows = table.rows().data().toArray();
        });
        $('#refresh').click(function(e) {
            $("#from_date").val("");
            $("#to_date").val("");
            $("#descripcion").val("");
            $("#proyecto").val("");
            $("#uso_descarga").val("");
            table.ajax.url("{{ route('listar.mis.tickets') }}").load();
        });
        $(document).on('click', '.show_ticket', function(event) {
            event.preventDefault();
            var rows = table.rows().data().toArray();
            tickets = [];
            rows.forEach(ticket => {
                tickets.push(ticket.ticket_id);
            });
            var href = $(this).attr('href');
            var id = $(this).attr('data-id');
            $(this).attr('href', `${href}?tickets=${tickets}&view=${id}`);
            window.location = $(this).attr('href');

            /*guadardo de info del sitio actual*/
            var data_page = {
                'module': 'my tickets',
                'from_date': $('#from_date').val(),
                'to_date': $('#to_date').val(),
                'proyecto': $('#proyecto').val(),
                'descripcion': $('#descripcion').val(),
                'table_url': table.context[0].ajax
            };
            localStorage.setItem('my_tickets', JSON.stringify(data_page));
        });
        /*eliminacion de data de la pagina*/
        $('a').click(function() {
            localStorage.removeItem('my_tickets');
        });
        /*guardado de data de la pagina*/
        $(document).ready(function() {
            var data = localStorage.getItem('my_tickets');
            if (data) {
                data = JSON.parse(data)
                $('#from_date').val(data.from_date)
                $('#to_date').val(data.to_date)
                $('#proyecto').val(data.proyecto)
                $('#descripcion').val(data.descripcion)
                table.ajax.url(
                    `{{ url('tickets') }}?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&descripcion=${$('#descripcion').val()}&uso_descarga=${$('#uso_descarga').val()}`
                ).load();
            }
        });
        /*detect reload eliminacion de data de la pagina*/
        const pageAccessedByReload = (
            (window.performance.navigation && window.performance.navigation.type === 1) ||
            window.performance
            .getEntriesByType('navigation')
            .map((nav) => nav.type)
            .includes('reload')
        );
        if (pageAccessedByReload) { //return bool
            localStorage.removeItem('my_tickets');
        }
    </script>
    <script>
        /* detectar evento */
        $(document).on('click', '.load_descargar', function(event) {
            var pagina_actual = table.page();
            table.page(pagina_actual).draw('page');
        });
    </script>
    <script src="{{ asset('js/list_ticket.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/ticket/report.js') }}"></script>
@endpush
