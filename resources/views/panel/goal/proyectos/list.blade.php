@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
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
        .table i {
            margin-right: 3px;
            font-size: 18px;
        }

    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Set up a job:</h6>
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
                                        <option value="">All status
                                        </option>
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
                        <div class="col-md-12 mt-2">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-pill btn-primary d-block" style="padding: 0.2rem 0.5rem;"
                                    type="button" id="buscar"><i class="fa fa-search"></i> Search</button>
                                <button class="btn btn-pill btn-warning d-block" style="padding: 0.2rem 0.5rem"
                                    type="button" id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <table id="lista_proyectos" class="table thead-primary w-100">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Project</th>
                                        <th>Type</th>
                                        <th>GC-Company</th>
                                        <th>Project Manager</th>
                                        <th>Foreman</th>
                                        <th>Quantity Orders</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.goal.project-report-visit.view-request-materiales title="Info" />
    <x-components.goal.project-report-visit.view-check-material title="Info" />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script>
        var tableTable = $('#lista_proyectos').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax:  `${base_url}/goal-project/data-table?multiselect_project=${$('#multiselect_project').val()}&status=${$('#status').val()}&from_date=${$('#from_date').val()}&from_date=${$('#to_date').val()}&cargo=${$('#cargo').val()}&filtro=${$('#filtro').val()}`,
            columns: [
                {
                    data: 'Codigo',
                    name: "Codigo"
                },
                {
                    data: 'Nombre',
                    name: "Nombre"
                },
                {
                    data: 'tipo',
                    name: "tipo"
                },
                {
                    data: 'nombre_empresa',
                    name: "nombre_empresa"
                },
                {
                    data: 'nombre_project_manager',
                    name: "nombre_project_manager"
                },
                {
                    data: 'nombre_foreman',
                    name: "nombre_foreman"
                },
                {
                    data: 'materiales',
                    name: "materiales"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false,
                }
            ],
            order: [

            ],
            pageLength: 100
        });
    </script>
    <script>
        $(function() {
            $(".TodayTime").datetimepicker({
                defaultDate: $('#TodayTime').val(),
                format: 'HH:mm:ss',
                timeFormat: 'HH:mm:ss',
                pickDate: false,
                pickSeconds: false,
                pick12HourFormat: false,
                onSelect: function(datetimeText, datepickerInstance) {
                    if (!datepickerInstance.timeDefined) {
                        $(".TodayTime").datetimepicker('hide')
                    }
                }
            })
        });
    </script>
    <script src="{{ asset('js/goal/view_material/filtros.js') }}"></script>
    <script src="{{ asset('js/goal/view_material/create.js') }}"></script>
    <script src="{{ asset('js/goal/view_material/check_material.js') }}"></script>
    <script src="{{ asset('js/goal/reports/list.js') }}"></script>
@endpush
