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

    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.20.1/dist/bootstrap-table.min.css">
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css"
        crossorigin="anonymous">
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
                    <h6 class="pt-1">PROJECT NOTES</h6>
                    @if (Auth::user()->verificarRol([1]))
                        <button class="btn btn-pill btn-primary btn-sm" id="create_nota">
                            Create note</button>
                    @endif
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <div class="form-group">
                                <label for="buton">From Date:</label>
                                <input type="text" name="from_date" id="from_date"
                                    class="form-control form-control-sm datepicke" placeholder="From Date"
                                    value="{{ date('m/d/Y') }}" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="form-group">
                                <label for="buton">To Date:</label>
                                <input type="text" name="to_date" id="to_date"
                                    class="form-control form-control-sm datepicke" placeholder="To Date"
                                    value="{{ date('m/d/Y') }}" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="buton">Search Note:</label>
                                <input type="text" name="buscar_nota" id="buscar_nota"
                                    class="form-control form-control-sm" placeholder="Search Note" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="list-notas" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Code</th>
                                            <th>Code Project</th>
                                            <th>Project</th>
                                            <th>Note</th>
                                            <th>Files</th>
                                            <th>Actions</th>
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
    <x-components.proyecto.modal-nota title="Info" />
    <x-components.proyecto.modal-file title="Info" />
@endsection

@push('javascript-form')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <!--script src="https://unpkg.com/bootstrap-table@1.20.1/dist/bootstrap-table.min.js"></script-->

    <!-- the main fileinput plugin script JS file -->
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/buffer.min.js"
        type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/filetype.min.js"
        type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>

    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

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
    {{-- sistema movimientos --}}
    <script src="{{ asset('js/proyecto/notas/list.js') }}"></script>
    <script src="{{ asset('js/proyecto/notas/modal_nota/create.js') }}"></script>
    <script src="{{ asset('js/proyecto/notas/modal_nota/store.js') }}"></script>
    <script src="{{ asset('js/proyecto/notas/modal_nota/edit.js') }}"></script>
    <script src="{{ asset('js/proyecto/notas/modal_nota/update.js') }}"></script>
    <script src="{{ asset('js/proyecto/notas/modal_nota/delete.js') }}"></script>
    <script src="{{ asset('js/proyecto/notas/input_files.js') }}"></script>
@endpush
