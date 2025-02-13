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
                    <h6>Production analysis</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row mb-0">
                        <div class="col-md-3">
                            <button type="button" id="descarga_all_pdf" class="btn btn-primary has-icon btn-sm mt-1 w-100">
                                <i class="flaticon-pdf"></i>
                                Download PDF
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="descarga_all_excel"
                                class="btn btn-primary has-icon btn-sm mt-1 w-100">
                                <i class="flaticon-excel"></i>
                                Download EXCEL
                            </button>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label col-form-label-sm">Projects</label>
                                <div class="col-sm-9">
                                    <select class="form-control form-control-sm" id="multiselect_project"
                                        name="multiselect_project[]" multiple="multiple" required>
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->Pro_ID }}">
                                                {{ $proyecto->Nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-pill btn-primary d-block mt-0" style="padding: 0.2rem 0.5rem;"
                                    type="button" id="buscar"><i class="fa fa-search"></i> Search</button>
                                <button class="btn btn-pill btn-warning d-block mt-0" style="padding: 0.2rem 0.5rem;"
                                    type="button" id="limpiar"><i class="fas fa-trash"></i> Clean</button>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="list-proyectos" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>*</th>
                                            <th>Name Project</th>
                                            <th>GC - Company</th>
                                            <th>Type</th>
                                            <th>&nbsp;&nbsp;&nbsp;Address&nbsp;&nbsp;&nbsp;</th>
                                            <th>PM</th>
                                            <th>Field Superintendent</th>
                                            <th>Foreman</th>
                                            <th>Lead</th>
                                            <th>Asistant PM</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="descarga_excel" method="POST" action="">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    </div>
    <!--Modal uploadModal -->
@endsection
@push('javascript-form')
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
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

    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>

    <script src="{{ asset('js/informe_proyecto_report/index.js') }}"></script>
@endpush
