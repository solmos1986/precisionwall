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
    <link rel="stylesheet" href="{{ asset('css/table-responsive.css') }}">
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
                    <h6>configure project daily report</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_comfoguracion" method="post" enctype="multipart/form-data" action="">
                        @csrf
                        <input type="hidden" name="ticket_id" value="">
                        <input type="hidden" name="is_mail" class="is_mail">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="cc" class="cc">
                        <input type="hidden" name="title_m" class="title_m">
                        <input type="hidden" name="body_m" class="body_m">
                        @if (Auth::user()->verificarRol([1]))
                            <div class="form-group">
                                <label for="generate">Project:</label>
                                <select class="form-control form-control-sm" id="proyect_id" style="width:100%" disabled>
                                    <option value="{{ $proyecto->Pro_ID }}">{{ $proyecto->Nombre }}</option>
                                </select>
                            </div>
                        @endif
                        <input type="hidden" id="empleado_id" name="empleado_id" value="{{ auth()->user()->Empleado_ID }}">
                        <p class="ms-directions">Extra Work Orden</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="general" class="col-sm-3 col-form-label col-form-label-sm">General
                                        Contractor</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="general"
                                            placeholder="General Contractor" value="{{ $proyecto->empresa }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ptw" class="col-sm-3 col-form-label col-form-label-sm">PRECISION WALL
                                        TECH
                                        PROJECT</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="ptw"
                                            placeholder="PRECISION WALL TECH PROJECT" value="{{ $proyecto->Codigo }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="project" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="project"
                                            placeholder="Project Name" value="{{ $proyecto->Nombre }}" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="address"
                                            placeholder="Project Address" value="{{ $address }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Date of
                                        Work</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm datepicker"
                                            id="date_work" name="date_work" value="{{ date('m/d/Y') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="foreman_name" class="col-sm-3 col-form-label col-form-label-sm">Foreman
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="foreman_name"
                                            name="foreman_name" placeholder="Foreman Name" value="{{ $foreman_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="pco" class="col-sm-3 col-form-label col-form-label-sm">PCO#</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" name="pco"
                                            id="pco">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-sm btn-success mt-0 mb-3" type="button"
                                    id="open_modal_table_option">List Options</button>
                            </div>
                        </div>
                        <div id="lista">
                           
                        </div>
                    </form>
                </div>
                <div class="ms-panel-footer">
                    <button class="btn btn-success d-block" type="button" id="enviar">Save and Continue</button>
                </div>
                <option value=""></option>
            </div>
        </div>
    </div>

    <x-components.config-daily-report.list-options />
    <x-components.config-daily-report.option />
@endsection
@push('javascript-form')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/configDailyReport/modal_list_option.js') }}"></script>
    <script src="{{ asset('js/configDailyReport/store.js') }}"></script>
@endpush
