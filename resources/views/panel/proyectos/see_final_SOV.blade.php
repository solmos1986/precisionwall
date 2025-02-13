@extends('layouts.panel')
@push('css-header')
    <style>
        #signature-pad {
            min-height: 200px;
            border: 1px solid #000;
        }

        #signature-pad canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: #fff;
        }

    </style>
    <style>
        .anyClass {
            height: 300px;
            overflow-y: scroll;
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
            padding: 0.3rem;
            vertical-align: text-top;
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
        .tableFixHead {
            overflow: auto;
            height: 750px;
        }

        .tableFixHead thead th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        /* Just common table stuff. Really. */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px 16px;
        }

        th {
            background: #4eb0e9;
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
                            <h6 class="p-2">See Final SOV Structure</h6>
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
                                                <select class="form-control form-control-sm" id="status" name="status"
                                                    style="width:100%">
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
                                                <select class="form-control form-control-sm" id="cargo" name="cargo"
                                                    style="width:100%">
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
                                            <button class="btn btn-pill btn-primary d-block" style="padding: 0.2rem 0.5rem;"
                                                type="button" id="buscar"><i class="fa fa-search"></i> Search</button>
                                            <button class="btn btn-pill btn-warning d-block" style="padding: 0.2rem 0.5rem"
                                                type="button" id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-9">
                            <div class="table-responsive tableFixHead" 
                            style="height: 300px; overflow-y: scroll;" id="table">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- form para descargar --}}
            <form id="descargar_excel" method="POST" action="">
                @csrf
            </form>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <div class="row w-100">
                        <div class="col-md-8">
                            <h6 id="nombre_report_view" class="p-2"></h6>
                        </div>
                        <div class="col-md-4 d-flex flex-row-reverse bd-highlight">
                            <button type="button" id="export_sov" class="btn btn-primary has-icon btn-sm d-inline"
                                data-tipo="" data-id="">
                                <i class="fa fa-download"></i>
                                Donwload Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="ms-panel-body">
                    <div class="col-md-12">
                        <div class="table-responsive tableFixHead">
                            <table id="list-proyectos" class="table table-hover thead-primary w-100">
                                <thead id="load-data-thead" style="text-align:left">
                                </thead>
                                <tbody id="load-data-tbody">
                                    <tr style="background: #dee2e6">
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                        <td> &nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.estimados.final.view_edit_sov title="Info" />
    <x-components.estimados.final.view_filter />
@endsection
@push('javascript-form')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js"
        integrity="sha512-R/QOHLpV1Ggq22vfDAWYOaMd5RopHrJNMxi8/lJu8Oihwi4Ho4BRFeiMiCefn9rasajKjnx9/fTQ/xkWnkDACg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="https://unpkg.com/bootstrap-table@1.20.1/dist/bootstrap-table.min.js"></script>
    <script></script>
    <script src="{{ asset('js/estimados/final_estructure/list.js') }}"></script>
    <script src="{{ asset('js/estimados/final_estructure/filtro.js') }}"></script>
    <script src="{{ asset('js/estimados/final_estructure/view_brake_dow.js') }}"></script>
    <script src="{{ asset('js/estimados/final_estructure/view_sov_completed.js') }}"></script>
@endpush
