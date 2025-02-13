@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">

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
        table.dataTable tbody tr.selected {
            color: rgb(0, 0, 0);
            background-color: #d2d2d2;
            /* Not working */
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Register activities:</h6>
                </div>
                <div class="ms-panel-body">
                    <input type="text" hidden id="tipo_usuario" name="tipo_usuario"
                        value="{{ Auth::user()->verificarRol([1]) }}">
                    <input type="text" hidden id="empleado_id" name="empleado_id"
                        value="{{ auth()->user()->Empleado_ID }}">
                    <input type="text" hidden id="nickname" name="nickname" value="{{ auth()->user()->Nick_Name }}">
                    <div class="row">
                        @if (Auth::user()->verificarRol([1]))
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">From Date:</label>
                                            <input type="text" name="from_date" id="from_date"
                                                class="form-control form-control-sm datepicke" placeholder="From Date"
                                                value="{{ date('m/d/Y') }}" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">To Date:</label>
                                            <input type="text" name="to_date" id="to_date"
                                                class="form-control form-control-sm datepicke" placeholder="From Date"
                                                value="{{ date('m/d/Y') }}" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">Nick name:</label>
                                            <input type="text" name="nick_name" id="nick_name"
                                                class="form-control form-control-sm" placeholder="Nick name" value=""
                                                autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">Job:</label>
                                            <input type="text" name="job" id="job"
                                                class="form-control form-control-sm" placeholder="Job" value=""
                                                autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">Option:</label>
                                            <br>
                                            <label class="ms-checkbox-wrap">
                                                <input type="checkbox" name="no_cost_code" id="no_cost_code" value="1">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <span> Records with No Cost Code and Hours=8 </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">Hours > 8 or < 0 :</label>
                                                    <input type="text" name="horas_trabajo" id="horas_trabajo"
                                                        class="form-control form-control-sm" placeholder="Hours"
                                                        value="" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">Area cost code:</label>
                                            <input type="text" name="cost_code" id="cost_code"
                                                class="form-control form-control-sm" placeholder="Area cost code:"
                                                value="" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">

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
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                        <div class="form-group">
                                            <button type="button" id="crear_registro"
                                                class="btn btn-primary btn-sm mt-0 has-icon">
                                                <i class="fa fa-plus"></i>
                                                Add Records
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">From Date:</label>
                                            <input type="text" name="from_date" id="from_date"
                                                class="form-control form-control-sm datepicke" placeholder="From Date"
                                                value="{{ date('m/d/Y') }}" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buton">To Date:</label>
                                            <input type="text" name="to_date" id="to_date"
                                                class="form-control form-control-sm datepicke" placeholder="From Date"
                                                value="{{ date('m/d/Y') }}" autocomplete="off" />
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
                            <div class="col-md-12">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                        <div class="form-group">
                                            <button type="button" id="crear_registro"
                                                class="btn btn-primary btn-sm mt-0 has-icon">
                                                <i class="fa fa-plus"></i>
                                                Add Records
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <form action="" id="form_actividades" method="post">
                                <table class="table thead-primary no-footer w-100" id="lista_actividades">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;&nbsp;&nbsp;Day&nbsp;&nbsp;&nbsp;</th>
                                            <th>Line&nbsp;/&nbsp;date&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Employee&nbsp;/&nbsp;Nick&nbsp;name&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Job&nbsp;name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                            <th>check&nbsp;in&nbsp;</th>
                                            <th>check&nbsp;out&nbsp;</th>
                                            <th>Title&nbsp;leven&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Building&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Code&nbsp;=&nbsp;floor&nbsp;or&nbsp;area&nbsp;&nbsp;</th>
                                            <th>Code&nbsp;=&nbsp;area&nbsp;or&nbsp;task</th>
                                            <th>Hours&nbsp;Worked&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Check by foreman&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex justify-content-center">
                                <button type="button" id="registrar" class="btn btn-success btn-sm mt-0 has-icon">
                                    <i class="fa fa-save"></i>
                                    register
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('datatable')
    <script>
        var isAdmin = {{ Auth::user()->verificarRol([1]) == 1 ? '1' : '0' }}
    </script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('js/register_actividad/dataTable.js') }}"></script>
@endpush
