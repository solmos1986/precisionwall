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
            background: rgb(228, 231, 255);
        }
    </style>
    <style>
        tr.selected {}

        .verde {
            background-color: #d1f2eb
        }

        .rojo {
            background-color: #F5B7B1;
        }

        .azul {
            background-color: #a9cce3
        }

        .celeste {
            background-color: #d4e6f1
        }

        .amarillo {
            background-color: #fcf3cf
        }

        .blanco {
            background-color: #ffffff
        }

        .colores {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .check-color {
            position: relative;
            width: 20px;
            height: 20px;
            text-align: center;
            padding: 4px 0;
            margin-right: 5px;
            border: 1px solid #878793;
            color: #878793;
            border-radius: 5px;
            -webkit-transition: 0.3s;
            transition: 0.3s;
            cursor: pointer;
        }

        .check-color-all {
            width: 20px;
            height: 20px;
            text-align: center;
            padding: 4px 0;
            margin-right: 5px;
            border: 1px solid #878793;
            color: #878793;
            border-radius: 5px;
            -webkit-transition: 0.3s;
            transition: 0.3s;
            cursor: pointer;
        }

        .position {
            display: block;
        }

        .proyectos {
            opacity: 1;
            accent-color: #ffffff;
            height: 30px;
            /* not needed */
            width: 30px;
            /* not needed */
        }

        .color-text {
            color: #000;
        }
    </style>
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

        @media only screen and (min-width: 480px) {
            .modal-md {
                max-width: 50% !important;
            }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
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
                    <h6>Job information</h6>
                </div>
                <div class="ms-panel-body">
                    <p class="ms-directions mb-1">REPORTS:</p>
                    <div class="row pb-2">
                        <div class="col-md-4">
                            <button type="button" id="view_pdf" class="btn btn-primary has-icon btn-sm mt-1">
                                <i class="flaticon-pdf"></i>
                                Job Status - Dash Board PDF
                            </button>
                            <button type="button" id="view_report_daily" class="btn btn-primary has-icon btn-sm mt-1">
                                <i class="flaticon-pdf"></i>
                                Daily Report PDF
                            </button>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-4 col-form-label col-form-label-sm">From
                                    date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicke" id="from_date"
                                        name="from_date" placeholder="From date" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-4 col-form-label col-form-label-sm">To
                                    date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicke" id="to_date"
                                        name="to_date" placeholder="To date" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>
                    <form id="form_estadisticas" action="">
                        <p class="ms-directions mb-1">MORE SEARCH OPTIONS:</p>
                        <div class="row pb-2">

                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Company:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="select2_company"
                                            name="select2_company" style="width:100%">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Status:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" id="status" name="status[]"
                                            multiple="multiple" style="width:100%">
                                            @foreach ($status_proyecto as $estado)
                                                <option value="{{ $estado->Estatus_ID }}">
                                                    {{ $estado->Nombre_Estatus }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col md 3">
                                <div class="form-group row mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Project:</label>
                                    <div class="col-sm-8">
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

                            {{-- busqueda por persona --}}
                            <div class="col-md-3">
                                <div class="form-group row mb-1">
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
                                <div class="form-group row mb-1">
                                    <label for="date_order"
                                        class="col-sm-4 col-form-label col-form-label-sm">Name:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm" style="width:100%" name="filtro"
                                            id="filtro">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">

                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-pill btn-primary d-block mt-0" style="padding: 0.2rem 0.5rem;"
                                        type="button" id="buscar"><i class="fa fa-search"></i> Search</button>
                                    <button class="btn btn-pill btn-warning d-block mt-0" style="padding: 0.2rem 0.5rem;"
                                        type="button" id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row pb-2">
                        <div class="col-md-6 d-flex justify-content-start">

                        </div>
                        <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                            <ul class="colores mb-0 mt-1">
                                <li class="check-color-all rojo" data-color="rojo" data-proyecto_id=""> </li>
                                <li class="check-color-all verde" data-color="verde" data-proyecto_id=""> <i></i>
                                </li>
                                <li class="check-color-all azul" data-color="azul" data-proyecto_id=""> <i></i>
                                </li>
                                <li class="check-color-all celeste" data-color="celeste" data-proyecto_id="">
                                    <i></i>
                                </li>
                                <li class="check-color-all amarillo" data-color="amarillo" data-proyecto_id="">
                                    <i {{-- class="inline fa fa-times position" --}}></i>
                                </li>
                                <li class="check-color-all blanco" data-color="blanco" data-proyecto_id="">
                                    <i></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="list-proyectos" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Name Project</th>
                                            <th>GC - Company</th>
                                            <th>Start Date</th>
                                            <th>End Date:</th>
                                            <th>Type</th>
                                            <th>&nbsp;&nbsp;&nbsp;Address&nbsp;&nbsp;&nbsp;</th>
                                            <th><input type="checkbox" id="view_pdf_all" class="check" name="check"
                                                    value="" style="transform: scale(1.5);"></th>
                                            <th>Action</th>
                                            <th>PM</th>
                                            <th>Field Superintendent</th>
                                            <th>Foreman</th>
                                            <th>Lead</th>
                                            <th>Asistant PM</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--Modal uploadModal -->
    <x-components.informacion-project.info :statusInfo='$status_info' :statusProyecto='$status_proyecto' :tipoProyecto='$tipo_proyecto' />
    <x-components.informacion-project.view-info title="Info" />
    <x-components.informacion-project.view-fecha-proyecto title="Info" />
    <x-components.informacion-project.view-action title="Info" />
    <x-components.informacion-project.view-stages title="Info" />
    <x-components.informacion-project.report.filtro-daily title="Info" />
    <x-components.informacion-project.view-acciones title="Info" />
    <x-components.informacion-project.view-acciones-history title="Info" />
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
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.11.5/sorting/datetime-moment.js"></script>

    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>

    <script>
        //sort por foramto de fecha
        $.fn.dataTable.moment('MM/DD/YYYY');
        var dataTable = $("#list-proyectos").DataTable({
            createdRow: function(row, data, dataIndex) {
                if (data.color != `null`) {
                    $(row).addClass(data.color);
                }
            },
            processing: true,
            serverSide: true,
            scrollY: true,
            scrollX: true,
            scrollCollapse: true,
            ajax: `${base_url}/info-project/proyect?proyectos=${$('#multiselect_project').val()}&filtro=${$('#filtro').val()}&cargo=${$('#cargo').val()}&from_date=${moment().format('MM/DD/YYYY')}&to_date=${moment().format('MM/DD/YYYY')}&status=${$('#status').val()}`,
            language: {
                searchPlaceholder: "Criterion"
            },
            columns: [{

                    data: "Codigo",
                    name: "Codigo",
                },
                {

                    data: "Nombre",
                    name: "Nombre"
                },
                {
                    data: "empresa",
                    name: "empresa"
                },

                {
                    data: 'Fecha_Inicio',
                    name: 'Fecha_Inicio',
                    type: 'date',
                    format: 'MM/DD/YYYY',

                },
                {
                    data: 'Fecha_Fin',
                    name: 'Fecha_Fin',
                    type: 'date',
                },
                {
                    data: 'tipo',
                    name: 'tipo',
                },
                {
                    data: 'direccion',
                    name: 'direccion',
                },
                {
                    data: 'check_pdf',
                    name: 'check_pdf',
                    orderable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                },
                {
                    data: 'Manager',
                    name: 'Manager',
                },
                {
                    data: 'Cordinador',
                    name: 'Cordinador',
                },
                {
                    data: 'Foreman',
                    name: 'Foreman',
                },
                {
                    data: 'lead',
                    name: 'lead',
                },
                {
                    data: 'asistente_proyecto',
                    name: 'asistente_proyecto',
                },
            ],
            pageLength: 100
        });

        $('#list-proyectos tbody').on('click', 'tr', function() {
            /*  if ($(this).hasClass('selected')) {
                 $(this).removeClass('selected');
             } else { */
            dataTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        });

        $('#list-proyectos thead').on('click', ' th', function() {
            console.log('data')
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#view_info').click(function() {
                $('#modalViewInfo').modal({
                    show: true
                })
            });
            $('.modal').on('hidden.bs.modal', function(event) {
                $(this).removeClass('fv-modal-stack');
                $('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
            });
            $('.modal').on('shown.bs.modal', function(event) {
                // keep track of the number of open modals
                if (typeof($('body').data('fv_open_modals')) == 'undefined') {
                    $('body').data('fv_open_modals', 0);
                }
                // if the z-index of this modal has been set, ignore.

                if ($(this).hasClass('fv-modal-stack')) {
                    return;
                }

                $(this).addClass('fv-modal-stack');

                $('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);

                $(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));

                $('.modal-backdrop').not('.fv-modal-stack')
                    .css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));
                $('.modal-backdrop').not('fv-modal-stack')
                    .addClass('fv-modal-stack');
            });
        });
    </script>
    <script src="{{ asset('js/estadisticas/chart.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/index.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/filtros.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/info.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/fecha_proyecto.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/action.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/views/view_info.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/views/view_fecha_proyecto.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/views/view_action.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/views/view_stages.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/notificacion_acciones.js') }}"></script>
    <script src="{{ asset('js/informe_proyecto/notificacion_history.js') }}"></script>
    {{-- view daily --}}
    <script>
        $("#filtro_proyectos").select2({
            multiple: true,
        });
    </script>
    <script src="{{ asset('js/informe_proyecto/report/view_filter_daily.js') }}"></script>
@endpush
