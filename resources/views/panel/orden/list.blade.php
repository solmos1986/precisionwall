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

        .file-footer-buttons>.btn {
            padding: 0.625rem 1rem;
            min-width: 0 !important;
            margin-top: 1rem;
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
            {{ Breadcrumbs::render('list order') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>order and Report WC Installation</h6>

                    @if (Auth::user()->verificarRol([1]))
                        <a class="btn btn-pill btn-primary btn-sm" href="{{ route('crear.orden') }}">Create order WC
                            Installation</a>
                    @endif
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
                                <button type="button" name="refresh" id="refresh" class="btn btn-primary btn-sm mt-0"><i
                                        class="fas fa-retweet"></i></button>
                            </div>
                            @if (Auth::user()->verificarRol([1]))
                                <div class="col-md mb-3">
                                    <button type="button" id="multiple_email" class="btn btn-primary btn-sm mt-0">
                                        <i class='fas fa-envelope' title='Send Mail'></i>
                                </div>
                            @endif
                           {{--  @if (Auth::user()->verificarRol([1]))
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
                            @endif --}}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="list-orden" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Cod Project</th>
                                    <th>Name Project</th>
                                    <th>Num</th>
                                    <th>Sub contractor</th>
                                    <th>Date Schedule</th>
                                    @if (Auth::user()->verificarRol([1]))
                                        <th>Created by</th>
                                    @endif
                                    <th>Installer signature</th>
                                    <th>Foreman Signature</th>
                                    <th>Startup images</th>
                                    <th>Final images</th>
                                    <th>Select</th>
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
    <!--Modal Eliminar -->
    <x-components.delete-modal />
    <!--Modal uploadModal -->
    <x-components.upload-modal />
    <x-components.multiple-mail />
    <!--Modal uploadModal -->
    <x-components.mail-modal title="order" />
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
            ajax: `{{ route('listar.ordenes') }}?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&descripcion=${$('#descripcion').val()}&uso_descarga=${$('#uso_descarga').val()}`, //filtro inicial
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "Codigo",
                    name: "Codigo"
                },
                {
                    data: "proyecto",
                    name: "proyecto"
                },
                {
                    data: "num",
                    name: "num"
                },
                {
                    data: "empresa",
                    name: "empresa"
                },
                {
                    data: "date_work",
                    name: "date_work"
                },
                @if (Auth::user()->verificarRol([1]))
                    {
                        data: "username",
                        name: "username"
                    },
                @endif {
                    data: "firma_installer",
                    name: "firma_installer"
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
                {
                    data: "check_email",
                    name: "check_email",
                    orderable: false
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100
        });
        $('#from_date, #to_date, #proyecto, #descripcion, #uso_descarga').change(function() {
            if ($("#to_date").val() == "") {
                $("#to_date").val($("#from_date").val());
            }
            table.ajax.url(
                `${base_url}/orders?from_date=${$('#from_date').val()}&to_date=${$('#to_date').val()}&proyecto=${$('#proyecto').val()}&descripcion=${$('#descripcion').val()}&uso_descarga=${$('#uso_descarga').val()}`
            ).load();
            var rows = table.rows().data().toArray();
        });
        $('#refresh').click(function(e) {
            $("#from_date").val("");
            $("#to_date").val("");
            $("#descripcion").val("");
            $("#proyecto").val("");
            $("#uso_descarga").val("");
            table.ajax.url(`${base_url}/orders?`).load();
        });
        $(document).on('click', '.show_orden_wc', function(event) {
            event.preventDefault();
            var rows = table.rows().data().toArray();
            ordenes = [];
            rows.forEach(orden => {
                ordenes.push(orden.id);
            });
            var href = $(this).attr('href');
            var id = $(this).attr('data-id');
            $(this).attr('href', `${href}?orders=${ordenes}&view=${id}`);
            window.location = $(this).attr('href');

        });

        /* detectar evento */
        $(document).on('click', '.load_descargar', function(event) {
            var pagina_actual = table.page();
            table.page(pagina_actual).draw('page');
        });
    </script>
    <script src="{{ asset('js/list_orden.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
@endpush
