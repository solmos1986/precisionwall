@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
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
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            {{ Breadcrumbs::render('report_order') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Material and Equipment Report</h6>
                </div>
                <div class="ms-panel-body">

                    <div class="ms-panel ms-panel-fh">
                        <div class="ms-panel-body clearfix">
                            <form id="descargar_pdf" method="POST" action="">
                                @csrf
                                <p class="ms-directions mb-0">SEARCH </p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="generate">Start date:</label>
                                            <input type="text" class="form-control form-control-sm datepicke"
                                                value="{{ date('m/d/Y') }}" id="fecha_inicio" name="fecha_inicio"
                                                style="width:100%" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="generate">End date:</label>
                                            <input type="text" class="form-control form-control-sm datepicke"
                                                id="fecha_fin" value="{{ date('m/d/Y') }}" name="fecha_fin"
                                                style="width:100%" required required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="generate">Project:</label>
                                            <select class="form-control form-control-sm " id="proyects" name="proyects[]"
                                                multiple="multiple" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="generate">Status project:</label>
                                            <select class="form-control form-control-sm" id="status_proyectos"
                                                name="status_proyectos" required multiple>
                                                @foreach ($status_proyectos as $status)
                                                    <option value="{{ $status->Estatus_ID }}">{{ $status->Nombre_Estatus }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <p class="ms-directions mb-0">SEARCH OTHERS</p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">Material/Equipment:</label>
                                            <select class="multiselect-all " id="materiales" name="materiales[]"
                                                multiple="multiple" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="generate">Status order:</label>
                                            <select class="form-control form-control-sm" id="multi_select_status_orden"
                                                name="multi_select_status_orden" required multiple>
                                                @foreach ($status_orden as $orden)
                                                    <option value="{{ $orden->id }}">{{ $orden->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Type:</label>
                                        <br>
                                        <ul class="ms-list d-flex">
                                            <li class="ms-list-item pl-0">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="radio" class="tipo" name="tipo" value="material"
                                                        checked>
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span> material </span>
                                            </li>
                                            <li class="ms-list-item">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="radio" class="tipo" name="tipo" value="equipo">
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span> Equipment </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Optional:</label><br>
                                            <label class="ms-checkbox-wrap ">
                                                <input type="checkbox" value="false" name="detalle" id="detalle">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <span> Detail </span>
                                        </div>
                                        <ul class="ms-list d-flex" style="visibility: hidden">
                                            <li class="ms-list-item pl-0">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="radio" class="view" name="view" id="view"
                                                        value="view_proyecto" checked>
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span> View per Projects </span>
                                            </li>
                                            <li class="ms-list-item pl-0">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="radio" class="view" name="view" id="view"
                                                        value="view_proyecto">
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span> View per Material </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button class="btn btn-primary " name="view_pdf" value="true"
                                                type="button" id="view_pdf">
                                                <i class="fas fa-file"></i>
                                                View pdf</button>
                                            <button class="btn btn-primary " type="button" id="download_pdf"
                                                type="button">
                                                <i class="far fa-file-pdf"></i>
                                                Donwload pdf</button>
                                            <button class="btn btn-primary " name="view" value="true"
                                                type="button" id="excel_pdf">
                                                <i class="far fa-file-excel"></i>
                                                Donwload excel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .multiselect-all label {
            font-weight: bold;
        }

        .multiselect-search {
            color: red;
        }
    </style>
@endsection
@push('javascript-form')
    <script type="text/javascript" src="{{ asset('js/pdfobject.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/tipo-orden-report/index.js') }}"></script>
@endpush
