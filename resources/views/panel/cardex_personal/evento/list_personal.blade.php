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
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Event: {{ $evento->nombre }}</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="invoice-address">
                                <h5 class="ms-feed-user mb-0">Type event:</h5>
                                <p>
                                    {{ $evento->tipo_evento }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-address">
                                <h5 class="ms-feed-user mb-0">Description</h5>
                                <p>
                                    {{ $evento->tipo_evento }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-address">
                                <h5 class="ms-feed-user mb-0">Days of duration:</h5>
                                <p>
                                    {{ $evento->duracion_day }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-address">
                                <h5 class="ms-feed-user mb-0">Note:</h5>
                                <p>
                                    {{ $evento->note }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-address">
                                <h5 class="ms-feed-user mb-0">Update this event:</h5>
                                @if ($evento->access_pers === 'y')
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-success">No</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="invoice-address">
                                <h5 class="ms-feed-user mb-0">Activate alert | Days of anticipation:</h5>
                                <p>
                                    {{ $evento->report_alert }} days
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="invoice-address">
                        <h5><strong>Associate staff:</strong></h5>
                    </div>
                    <table id="list_personal" class="table thead-primary w-100">
                        <thead>
                            <tr>
                                <th>Num</th>
                                <th>Full name</th>
                                <th>Nickname</th>
                                <th>Position</th>
                                <th>Note</th>
                                <th>Start date</th>
                                <th>Expiration date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
            ajax: "{{ route('cardex.list_personal.evento', $evento->cod_evento) }}",
            order: [

            ],
            columns: [{
                    data: "Numero",
                    name: "Numero"
                },
                {
                    data: "nombre_completo",
                    name: "nombre_completo"
                },
                {
                    data: "Nick_Name",
                    name: "Nick_Name"
                },
                {
                    data: "Cargo",
                    name: "Cargo"
                },
                {
                    data: "note",
                    name: "note"
                },
                {
                    data: "start_date",
                    name: "start_date"
                },
                {
                    data: "exp_date",
                    name: "exp_date"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
        });
    </script>
    <script src="{{ asset('js/listEvento.js') }}" type="text/javascript"></script>
@endpush
