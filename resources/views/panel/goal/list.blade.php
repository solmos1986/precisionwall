@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">

    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <!-- Tokenfield CSS -->
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
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

    <style>
        .table-standar td,
        .table-standar th {
            padding: 1px;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table td,
        .table th {
            padding: 0.5rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
        }

        .no-margin {
            vertical-align: top;
            border-top: 1px solid #ffffff;
        }

        td.details-control {
            background: url('https://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.details td.details-control {
            background: url('https://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
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
            {{ Breadcrumbs::render('visit report') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>List visit report</h6>
                    <a class="btn btn-pill btn-primary btn-sm" href="{{ route('create.goal') }}">Add field visit
                        report</a>
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
                                <input type="text" name="codigo" id="codigo" class="form-control form-control-sm"
                                    placeholder="Code visit report" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <input type="text" name="comentario" id="comentario" class="form-control form-control-sm"
                                    placeholder="Search in comments" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <button type="button" name="refresh" id="refresh" class="btn btn-primary btn-sm mt-0"><i
                                        class="fas fa-retweet"></i></button>
                            </div>
                            
                            @if (Auth::user()->verificarRol([1]))
                                <!--div class="col-md mb-3">
                        <button type="button" id="multiple_email" class="btn btn-primary btn-sm mt-0">
                          <i class='fas fa-envelope' title='Send Mail'></i>
                      </div-->
                            @endif
                        </div>
                    </div>
                    <table id="list-orden" class="table thead-primary w-100">
                        <thead>
                            <tr>
                                <th>Cod </th>
                                <th>Code Project</th>
                                <th>creation date</th>
                                <th>Name Project</th>
                                {{-- <th>status</th> --}}
                                <th>Start date of Project</th>
                                <th>Images</th>
                                <th>Name Company</th>
                                <th>Created by</th>
                                <th width="170">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--Modal Eliminar -->
    <x-components.delete-modal />
    {{-- Modal mail --}}
    <x-components.mail-modal title="visit report" />
    <!--Modal uploadModal -->
    <x-components.upload-modal />
@endsection

@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script>
        var table = $('#list-orden').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `{{ route('list.goal') }}?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}`,
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "Codigo",
                    name: "Codigo"
                },
                {
                    data: "codigo_proyecto",
                    name: "codigo_proyecto"
                },
                {
                    data: "fecha",
                    name: "fecha"
                },
                {
                    data: "nombre_proyecto",
                    name: "nombre_proyecto"
                },
                /*          { data: "estado", name: "estado" }, */
                {
                    data: "fecha_inicio",
                    name: "fecha_inicio"
                },
                {
                    data: "images",
                    name: "inicio",
                    orderable: false
                },
                {
                    data: "nombre_empresa",
                    name: "nombre_empresa"
                },
                {
                    data: "username",
                    name: "username"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100
        });

        /* filtro de busqueda goal */
        $('#from_date, #to_date, #proyecto, #codigo, #comentario').change(function() {
            if ($("#to_date").val() == "") {
                $("#to_date").val($("#from_date").val());
            }
            table.ajax.url(
                `{{ route('list.goal') }}?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&codigo=${$('#codigo').val()}&comentario=${$('#comentario').val()}`
            ).load();
            console.log($('#codigo').val());
            var rows = table.rows().data().toArray();
        });
        $('#refresh').click(function(e) {
            $("#from_date").val("");
            $("#to_date").val("");
            $("#codigo").val("");
            $("#proyecto").val("");
            table.ajax.url("{{ route('list.goal') }}").load();
        });
        /* navegacion entre vistas  */
        $(document).on('click', '.show_report', function(event) {
            event.preventDefault();
            var rows = table.rows().data().toArray();
            goals = [];
            rows.forEach(goal => {
                goals.push(goal.informe_id);
            });
            var href = $(this).attr('href');
            var id = $(this).attr('data-id');
            console.log(rows);
            $(this).attr('href', `${href}?goals=${goals}&view=${id}`);
            window.location = $(this).attr('href');
        });
    </script>

    <script>
        /* detectar evento */
        $(document).on('click', '.load_descargar', function(event) {
            var pagina_actual = table.page();
            table.page(pagina_actual).draw('page');
        });
    </script>
    <script src="{{ asset('js/goal/email.js') }}"></script>
    <script src="{{ asset('js/goal/list_goal.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
@endpush
