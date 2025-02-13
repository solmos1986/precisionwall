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
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
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
            <div class="div-error container-fluid" id="validate" style="display: none;">
                <ul class="alert alert-danger ul-error">
                </ul>
            </div>
            <div class="ms-panel">
                <div class="ms-panel-header">
                    <h6>Daily Report {{ $proyecto->actividad_fecha }}</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_ticket" method="post" enctype="multipart/form-data" action="">
                        <div class="row">
                            @csrf
                            <input type="hidden" name="ticket_id" value="">
                            <input type="hidden" name="is_mail" class="is_mail">
                            <input type="hidden" name="to" class="to">
                            <input type="hidden" name="cc" class="cc">
                            <input type="hidden" name="title_m" class="title_m">
                            <input type="hidden" name="body_m" class="body_m">
                            <div class="col-md-6">
                                @if (Auth::user()->verificarRol([1]))
                                    <div class="form-group row m-1">
                                        <label for="generate"
                                            class="col-sm-3 col-form-label col-form-label-sm">Project:</label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-sm" id="proyect_id" style="width:100%"
                                                disabled>
                                                <option value="{{ $proyecto->Pro_ID }}">{{ $proyecto->Nombre }}</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <input type="hidden" id="empleado_id" name="empleado_id"
                                    value="{{ auth()->user()->Empleado_ID }}">
                                <div class="form-group row m-1">
                                    <label for="general" class="col-sm-3 col-form-label col-form-label-sm">G.
                                        Contractor</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="general"
                                            placeholder="General Contractor" value="{{ $proyecto->empresa }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row m-1">
                                    <label for="ptw" class="col-sm-3 col-form-label col-form-label-sm">PWT </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="ptw"
                                            placeholder="PRECISION WALL TECH PROJECT" value="{{ $proyecto->Codigo }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row m-1">
                                    <label for="project" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="project"
                                            placeholder="Project Name" value="{{ $proyecto->Nombre }}" disabled="disabled">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row m-1">
                                    <label for="report_by" class="col-sm-3 col-form-label col-form-label-sm">Report By
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm " id="report_by"
                                            name="report_by"
                                            value="{{ trim($daily_report_detail->Nombre . $daily_report_detail->Apellido_Paterno . $daily_report_detail->Apellido_Materno) }}"
                                            readonly autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row m-1">
                                    <label for="foreman_name" class="col-sm-3 col-form-label col-form-label-sm">Foreman
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="foreman_name"
                                            readonly name="foreman_name" placeholder="Foreman Name"
                                            value="{{ $foreman_name }}">
                                    </div>
                                </div>
                                <div class="form-group row m-1">
                                    <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="address"
                                            placeholder="Project Address" value="{{ $address }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="ms-directions">Worked On</p>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover thead-light " id="resumen">
                                    <thead>
                                        <tr>
                                            <th width="250px">Area of Work</th>
                                            <th>Cod Code Task</th>
                                            <th>Nro. Employee</th>
                                            <th>H. Worker</th>
                                            <th>Completed</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row" id="ociones">
                            <div class="col-md-12">
                                <p class="ms-directions mb-0">Detail:</p>
                                <textarea class="form-control" id="detalle" rows="12">{{ $daily_report_detail->detalle }}</textarea>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            @foreach ($campos_images as $report)
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <p>{{ $report }}</p>
                                        <div class="file-loading">
                                            <input class="images" name="images[]" type="file"
                                                data-referencia="{{ $report }}" accept="image/*" multiple>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
                <div class="ms-panel-footer">
                    <button class="btn btn-success d-block" type="submit" id="enviar">Save and Continue</button>
                </div>
            </div>
        </div>
    </div>

    <x-components.firma-modal />
    @if (Auth::user()->verificarRol([1]))
        <x-components.profesion-modal />
        <x-components.material-modal />
        <x-components.reaseon-modal />
    @endif
    <x-components.mail-modal title="tickets" />
@endsection
@push('javascript-form')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <!-- the main fileinput plugin script JS file -->
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/buffer.min.js"
        type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/filetype.min.js"
        type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>

    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script>
        var auth = {{ Auth::user()->verificarRol([1]) == 1 ? '1' : '0' }}
        var Actividad_ID = "{{ $proyecto->Actividad_ID }}";
        var detalle_id = "{{ $daily_report_detail->id }}";

        $(document).ready(function() {
            fileinput_images(detalle_id, 'images_inicio', 'inicio', 'ticket');
        });
    </script>
    <script>
        var table = $('#resumen').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `${base_url}/daily-report-detail/data-table-project/${Actividad_ID}`,
            order: [],
            paging: false,
            searching: false,
            info: false,
            columns: [{
                    data: "nombre_area",
                    name: "nombre_area"
                },
                {
                    data: "nombre_tarea",
                    name: "nombre_tarea"
                },
                {
                    data: "cantidad_personas",
                    name: "cantidad_personas"
                },
                {
                    data: "Horas_Contract_total",
                    name: "Horas_Contract_total"
                },

                {
                    data: "porcentaje",
                    name: "porcentaje"
                },
                {
                    data: "note",
                    name: "note"
                }
            ],
        });
    </script>
    <script src="{{ asset('js/DailyReport/store.js') }}"></script>
    <script src="{{ asset('js/DailyReport/datatable.js') }}"></script>
@endpush
