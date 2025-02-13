@extends('layouts.panel')
@push('css-header')
    <style>
        .anyClass {
            height: 720px;
            /* overflow-y: scroll; */
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
            padding: 0.4rem;
            vertical-align: text-top;
            border-top: 1px solid #dee2e6;
        }

        .table i {
            margin-right: 3px;
            font-size: 18px;
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
        .wrapper {
            overflow-x: auto;
        }

        .wrapper table {
            white-space: nowrap;
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
                    <h6 class="p-2">See Jobs Structure</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="form_estadisticas" action="">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order" class="col-sm-4 col-form-label">From
                                        date:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm datepicke" id="from_date"
                                            name="from_date" placeholder="From date" value="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order" class="col-sm-4 col-form-label col-form-label-sm">To
                                        date:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm datepicke" id="to_date"
                                            name="to_date" placeholder="To date" value="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col md 3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order"
                                        class="col-sm-3 col-form-label col-form-label-sm">Project:</label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-sm" id="multiselect_project"
                                            name="multiselect_project[]" multiple="multiple" required style="width:100%">
                                            @foreach ($proyectos as $proyecto)
                                                <option value="{{ $proyecto->Pro_ID }}">
                                                    {{ $proyecto->Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Company:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="select2_company"
                                            name="select2_company" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Status:</label>
                                    <div class="col-sm-8">
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

                            <div class="col-md-3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Position:</label>
                                    <div class="col-sm-8">
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
                            <div class="col-md-3">
                                <div class="form-group row  mb-1">
                                    <label for="date_order"
                                        class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                                    <div class="col-sm-9">
                                        <select class="form-control form-control-sm" style="width:100%" name="filtro"
                                            id="filtro">
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
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"> List Projects</h6>
                            <div class="row">
                                <div class="table-responsive anyClass" id="table">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
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
    <script src="{{ asset('js/estimados/estructure_project/filtro.js') }}"></script>
    <script src="{{ asset('js/estimados/estructure_project/table.js') }}"></script>
@endpush
