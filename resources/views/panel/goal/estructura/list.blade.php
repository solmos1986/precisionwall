@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <style>
        .table-standar td,
        .table-standar th {
            padding: 1px;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table td,
        .table th {
            padding: 0.5rem;
            vertical-align: top;
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

        td.details-control {
            background: url('https://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.details td.details-control {
            background: url('https://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
        }
    </style>
    <style>
        .table i {
            margin-right: 0px;
            font-size: 18px;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Strucuture Set up a job:</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row  mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">From
                                    date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicke" id="from_date"
                                        name="from_date" placeholder="From date" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row  mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">To
                                    date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicke" id="to_date"
                                        name="to_date" placeholder="To date" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row  mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Project:</label>
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
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
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
                        <div class="col-md-3">
                            <div class="form-group row  mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Position:</label>
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
                        <div class="col-md-3">
                            <div class="form-group row  mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
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
                        <div class="col-md-12 mt-2">
                            <table id="lista_proyectos" class="table thead-primary w-100">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Code</th>
                                        <th>Project</th>
                                        <th>Type</th>
                                        <th>GC-Company</th>
                                        <th>Project Manager</th>
                                        <th>Foreman</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.goal.estructure.materiales title="Info" />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>



    <script src="{{ asset('js/goal/estructura/list.js') }}"></script>
    <script src="{{ asset('js/goal/estructura/filtros.js') }}"></script>
    <script src="{{ asset('js/goal/estructura/crear_material.js') }}"></script>
@endpush
