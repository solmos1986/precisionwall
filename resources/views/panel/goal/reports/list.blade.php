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
    <style>
        .anyClass {
            height: 300px;
            overflow-y: scroll;
        }
    </style>

    <style>
        td.details-control {
            background: url('../resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.details td.details-control {
            background: url('../resources/details_close.png') no-repeat center center;
        }
    </style>
    <style>
        .table i {
            margin-right: 0px;
            font-size: 18px;
        }
    </style>

    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.20.1/dist/bootstrap-table.min.css">
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
                    <div class="row w-100">
                        <div class="col-md-10">
                            <h6 class="p-2">Reports </h6>
                        </div>
                    </div>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <form id="form_estadisticas" action="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">From
                                                date:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control form-control-sm datepicke"
                                                    id="from_date" name="from_date" placeholder="From date" value=""
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">To
                                                date:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control form-control-sm datepicke"
                                                    id="to_date" name="to_date" placeholder="To date" value=""
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col md 12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order"
                                                class="col-sm-3 col-form-label col-form-label-sm">Project:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control form-control-sm" id="multiselect_project"
                                                    name="multiselect_project[]" multiple="multiple" required
                                                    style="width:100%">
                                                    @foreach ($proyectos as $proyecto)
                                                        <option value="{{ $proyecto->Pro_ID }}">
                                                            {{ $proyecto->Nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <ul class="{{-- ms-list d-flex --}}invisible" style="display:none" hidden>
                                            <li class="ms-list-item pl-0">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="checkbox" name="view_floor" id="view_floor" value="true"
                                                        checked>
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span>Floors </span>
                                            </li>
                                            <li class="ms-list-item">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="checkbox" name="view_area" id="view_area" value="true"
                                                        checked>
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span> Area </span>
                                            </li>
                                            <li class="ms-list-item">
                                                <label class="ms-checkbox-wrap">
                                                    <input type="checkbox" name="view_task" id="view_task" value="true"
                                                        checked>
                                                    <i class="ms-checkbox-check"></i>
                                                </label>
                                                <span> Task </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order"
                                                class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control form-control-sm" id="status"
                                                    name="status" style="width:100%">
                                                    @foreach ($status as $estado)
                                                        <option value="{{ $estado->Estatus_ID }}"
                                                            {{ $estado->Estatus_ID == 1 ? 'selected' : '' }}>
                                                            {{ $estado->Nombre_Estatus }}
                                                        </option>
                                                    @endforeach
                                                    <option value="">All status
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- busqueda por persona --}}
                                    <div class="col-md-12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order"
                                                class="col-sm-3 col-form-label col-form-label-sm">Position:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control form-control-sm" id="cargo"
                                                    name="cargo" style="width:100%">
                                                    <option value="pm">Project Manager
                                                    </option>
                                                    <option value="super">Superintendet
                                                    </option>
                                                    <option value="APM">Assistant Manager
                                                    </option>
                                                    <option value="foreman">Foreman
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order"
                                                class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control form-control-sm" style="width:100%"
                                                    name="filtro" id="filtro">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-pill btn-primary d-block"
                                                style="padding: 0.2rem 0.5rem;" type="button" id="buscar"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <button class="btn btn-pill btn-warning d-block"
                                                style="padding: 0.2rem 0.5rem" type="button" id="limpiar"><i
                                                    class="fas fa-trash"></i> Clean</button>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="button" id="descarga_excel"
                                            class="btn btn-primary has-icon btn-sm d-inline">
                                            <i class="flaticon-excel"></i>
                                            Download excel
                                        </button>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <div class="">
                                            <div class="btn-group">
                                                <button type="button" id="descarga_pdf"
                                                    class="btn btn-primary has-icon btn-sm d-inline">
                                                    <i class="flaticon-pdf"></i>
                                                    View PDF
                                                </button>
                                                <button type="button" class="btn btn-success descarga_pdf_multiple"
                                                    style="min-width:10px; margin-right:0px;">
                                                    <i class="fa fa-download fa-ms"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <div class="">
                                            <div class="btn-group">
                                                <button type="button" id="descarga_pdf_imagen"
                                                    class="btn btn-primary has-icon btn-sm d-inline">
                                                    <i class="far fa-file-image"></i>
                                                    View PDF with images
                                                </button>
                                                <button type="button" class="btn btn-success descarga_pdf_image_multiple"
                                                    style="min-width:10px; margin-right:0px;">
                                                    <i class="fa fa-download fa-ms"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-9">
                            <table id="list-proyectos" class="table thead-primary w-100">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Project Manager</th>
                                        <th>Foreman</th>
                                        <th><input type="checkbox" id="view_pdf_all" class="check" name="check"
                                                value="" style="transform: scale(1.5);"></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- form para descargar --}}
            <form id="descargar_excel" method="POST" action="">
                @csrf
            </form>

        </div>
    </div>
    <x-components.estimados.final.view_edit_sov title="Info" />
@endsection
@push('javascript-form')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js"
        integrity="sha512-R/QOHLpV1Ggq22vfDAWYOaMd5RopHrJNMxi8/lJu8Oihwi4Ho4BRFeiMiCefn9rasajKjnx9/fTQ/xkWnkDACg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.20.1/dist/bootstrap-table.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>

    <script src="{{ asset('js/goal/reports/filtros.js') }}"></script>
    <script src="{{ asset('js/goal/reports/list.js') }}"></script>
    <script src="{{ asset('js/goal/reports/report_pdf_excel.js') }}"></script>
@endpush
