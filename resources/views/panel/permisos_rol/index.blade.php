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
                    <h6>Permissions and roles </h6>
                </div>
                <div class="ms-panel-body">
                    <input type="text" hidden id="tipo_usuario" name="tipo_usuario"
                        value="{{ Auth::user()->verificarRol([1]) }}">
                    <input type="text" hidden id="empleado_id" name="empleado_id"
                        value="{{ auth()->user()->Empleado_ID }}">
                    <input type="text" hidden id="nickname" name="nickname" value="{{ auth()->user()->Nick_Name }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                    <div class="form-group">
                                        <button type="button"
                                            class="btn btn-primary btn-sm mt-0 has-icon nuevo">
                                            <i class="fa fa-plus"></i>
                                            Add Rol
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form action="" id="form_actividades" method="post">
                                <table class="table thead-primary no-footer w-100" id="list_rol">
                                    <thead>
                                        <tr>
                                            <th>Rol</th>
                                            <th>Modulo</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.rol-permiso.modal-rol-permisos title="visit report" />
@endsection

@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('js/permiso-rol/index.js') }}"></script>
@endpush
