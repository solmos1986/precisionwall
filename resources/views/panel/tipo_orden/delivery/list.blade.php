@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
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
            {{ Breadcrumbs::render('list order') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>List Order/Deliveres/Pick-Ups</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="container d-flex justify-content-center">
                        <div class="row">
                            <div class="col-md mb-3">
                                <input type="text" name="from_date" id="from_date"
                                    class="form-control form-control-sm datepicke datepicke" placeholder="From Date"
                                    autocomplete="off">
                            </div>
                            <div class="col-md mb-3">
                                <input type="text" name="to_date" id="to_date"
                                    class="form-control form-control-sm datepicke datepicke" placeholder="To Date"
                                    autocomplete="off">
                            </div>
                            <div class="col-md mb-3">
                                <select name="status" id="status" class="form-control form-control-sm select">
                                    <option value="">select an option</option>
                                    @foreach ($status as $val)
                                        <option value="{{ $val->id }}" {{ $val->id==7 ? 'selected' : '' }}>{{ $val->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md mb-3">
                                <button type="button" class="btn btn-primary has-icon btn-sm mt-0" id="buscar"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="ordenes">
                    </div>
                </div>
            </div>
        </div>
        <!--Modal Eliminar -->
        <x-components.delete-modal />
        <!--Modal uploadModal -->
        <x-components.upload-modal />
        <!--Modal uploadModal -->
        <x-components.mail-modal title="order" />
        <!--Modal show view -->
        <x-components.tipo-orden.delivery.show-detail />
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
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
        <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="{{ asset('js/tipo-orden/delivery/list.js') }}"></script>
        <script src="{{ asset('js/datepicker.js') }}"></script>
    @endpush
