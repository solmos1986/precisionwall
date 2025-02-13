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

    </style>
    <style>
        .tableFixHead {
            overflow: auto;
            height: 550px;
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
                    <h6>Summary of Jobs</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <form id="form_estadisticas" action="">
                                <div class="row">
                                    {{-- <div class="col-md-12">
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
                                    </div> --}}

                                    <div class="col-md-12">
                                        <div class="form-group row  mb-1">
                                            <label for="date_order"
                                                class="col-sm-3 col-form-label col-form-label-sm">Company:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control form-control-sm" id="select2_company"
                                                    name="select2_company" style="width:100%">
                                                </select>
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
                                                <select class="form-control form-control-sm" id="status" name="status[]"
                                                    multiple style="width:100%">
                                                    @foreach ($status as $estado)
                                                        <option value="{{ $estado->Estatus_ID }}"
                                                            {{ $estado->Estatus_ID == 1 ? 'selected' : '' }}>
                                                            {{ $estado->Nombre_Estatus }}
                                                        </option>
                                                    @endforeach
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
                                        <div class="form-group row mb-1">
                                            <label for="date_order"
                                                class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control form-control-sm" style="width:100%"
                                                    name="filtro" id="filtro">
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary text-center" style="padding: 0.2rem 0.5rem;"
                                            type="button" id="graficar_table">
                                            <i class="fa fa-chart-bar cursor-pointer p-0" title="view all statistics"></i>
                                            View all list</button>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-pill btn-primary d-block" style="padding: 0.2rem 0.1rem;"
                                        type="button" id="buscar"><i class="fa fa-search"></i> Search</button>
                                    <button class="btn btn-pill btn-warning d-block" style="padding: 0.2rem 0.1rem"
                                        type="button" id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="spinner spinner-3" id="spinner_table">
                                <div class="rect1"></div>
                                <div class="rect2"></div>
                                <div class="rect3"></div>
                                <div class="rect4"></div>
                                <div class="rect5"></div>
                                <h5></h5>
                            </div>
                            <div class="table-responsive tableFixHead" id="table">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Graphics View</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="spinner spinner-3" id="spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                        <h5></h5>
                    </div>
                    <div id="div_chart">
                        <canvas id="myChart"></canvas>
                    </div>
                    <div class="col-md-12">
                        <ul class="ms-list d-flex">
                            <li class="ms-list-item pl-0">
                                <label class="ms-switch">
                                    <input class="detail" type="checkbox">
                                    <span class="ms-switch-slider round"></span>
                                </label>
                                <span>Detail</span>
                            </li>
                            <li class="ms-list-item pl-0 view_extras">
                                <label class="ms-switch">
                                    <input class="good" type="checkbox">
                                    <span class="ms-switch-slider round"></span>
                                </label>
                                <span>Show project good</span>
                            </li>
                            <li class="ms-list-item pl-0 view_extras">
                                <label class="ms-switch">
                                    <input class="warning" type="checkbox">
                                    <span class="ms-switch-slider round"></span>
                                </label>
                                <span>Show project warning</span>
                            </li>
                        </ul>
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
    <script>
        var ctx = $('#myChart');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {

                }
            }
        });
    </script>
    <script src="{{ asset('js/estadisticas/filtros.js') }}"></script>
    <script src="{{ asset('js/estadisticas/porcentajes.js') }}"></script>
    <script src="{{ asset('js/estadisticas/chart.js') }}"></script>
    <script src="{{ asset('js/estadisticas/list.js') }}"></script>
@endpush
