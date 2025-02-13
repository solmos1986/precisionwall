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
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    {{ \Session::get('success') }}
                </div>
            @endif
            {{ Breadcrumbs::render('actividad', $id) }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Tickets - Proyect: {{ $proyecto->Nombre }} - ID: {{ $id }}</h6>
                    <a class="btn btn-pill btn-primary btn-sm" href="{{ route('crear.ticket', ['id' => $id]) }}">Create Ticket</a>
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list-ticket" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Cod Project</th>
                                    <th>Name Project</th>
                                    <th width="50">Number</th>
                                    <th>PCO#</th>
                                    <th width="120">Date Ticket</th>
                                    <th>Schedule</th>
                                    @if (Auth::user()->verificarRol([1])) <th>User</th> @endif
                                    <th>Client's signature</th>
                                    <th>Foreman's signature</th>
                                    <th>Startup images</th>
                                    <th>Final images</th>
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
    <!--Modal mailModal -->
    <x-components.mail-modal title="tickets" />

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
        var table = $('#list-ticket').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('listar.tickets', ['id' => $id]) }}",
            order: [
                [4, "desc"]
            ],
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
                    data: "horario",
                    name: "horario"
                },
                @if (Auth::user()->verificarRol([1]))  {data: 'username', name: 'username'}, @endif {
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
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
        });
    </script>
    <script src="{{ asset('js/list_ticket.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
@endpush
