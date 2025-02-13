@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <!-- Tokenfield CSS -->
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
            <div class="invisible" id="status_crud"></div>
            {{Breadcrumbs::render('lista eventos')}}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6 class="pt-1">Event list</h6>
                    <a class="btn btn-pill btn-primary btn-sm" id="crear_evento" href="#">Create event</a>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-primary btn-sm m-1" id="crear_type_evento" href="#">Add type event </a>
                        </div>
                    </div>
                    <br>
                    <table id="list_personal" class="table thead-primary w-100">
                        <thead>
                            <tr>
                                <th>Event type</th>
                                <th>Name of the event</th>
                                <th>Associate employee</th>
                                <th>Duration in days</th>
                                <th>Note</th>
                                <th>Visible to</th>
                                <th>Update event</th>
                                <th>Days of anticipation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-components.evento.new-evento :typeEventos="$tipo_eventos" :company="$company" :cargo="$cargo" />
    <x-components.evento.edit-evento :typeEventos="$tipo_eventos" :company="$company" :cargo="$cargo" />
    <x-components.tipo-evento.new-tipo-evento />
    <!--Modal Eliminar -->
    <x-components.delete-modal />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('js/plugins/piexif.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/plugins/sortable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/fr.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var table = $('#list_personal').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('cardex.list.evento') }}",
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "nombre_tipo",
                    name: "nombre_tipo"
                },
                {
                    data: "nombre",
                    name: "nombre"
                },
                {
                    data: "ver_usuarios",
                    name: "ver_usuarios",
                    orderable: false
                },
                {
                    data: "duracion_day",
                    name: "duracion_day",
                    orderable: false
                },
                {
                    data: "note",
                    name: "note"
                },
                {
                    data: "users",
                    name: "users",
                    orderable: false
                },
                {
                    data: "access_pers",
                    name: "access_pers",
                    orderable: false
                },
                {
                    data: "report_alert",
                    name: "report_alert",
                    orderable: false
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100,
        });

    </script>
    <script src="{{ asset('js/listEvento.js') }}" type="text/javascript"></script>
@endpush
