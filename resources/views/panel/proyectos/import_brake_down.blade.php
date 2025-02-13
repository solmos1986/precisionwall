@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
        rel="stylesheet" />
    <style>
        .table-standar td,
        .table-standar th {
            padding: 1px;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table td,
        .table th {
            padding: 0.2rem;
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
    </style>
    <style>
        .tableFixHead {
            overflow: auto;
            height: 750px;
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
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <div class="row w-100">
                        <div class="col-md-3">
                            <h6 class="p-2">Estimated import:</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label for="labor_cost" class="col-sm-4 col-form-label col-form-label-sm">Labor
                                    Cost:</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control form-control-sm" id="labor_cost"
                                        name="labor_cost" autocomplete="off">
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control form-control-sm select_labor_cost" name="action"
                                        id="action">
                                        @foreach ($labor_cost as $item)
                                            <option value="{{ $item->labor_cost }}">
                                                {{ $item->descripcion }} ({{ $item->labor_cost }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label for="exampleSelect" class="col-sm-4 col-form-label col-form-label-sm">index of
                                    Prod.</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="index_prod"
                                        name="index_prod" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="historial_import" class="btn btn-primary has-icon btn-sm d-inline">
                                <i class="flaticon-layers"></i>
                                Imports saves
                            </button>
                            <button type="button" id="modal_import" class="btn btn-success has-icon btn-sm d-inline m-1">
                                <i class="flaticon-tick-inside-circle"></i>
                                save
                            </button>
                        </div>
                    </div>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>IMPORT FILE</p>
                                    <form id="form_upload_excel" action="{{ route('upload.excel') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-file">
                                                    <input type="file" name="doc_excel" class="custom-file-input"
                                                        id="doc_excel">
                                                    <label class="custom-file-label" id="label_doc_excel"
                                                        for="doc_excel">Choose
                                                        file...</label>
                                                    <div class="invalid-feedback">error</div>
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="col-md-12">
                                                <button type="button" id="upload_excel"
                                                    class="btn btn-primary btn-sm mt-0">
                                                    Import excel
                                                </button>
                                                <button type="button" id="export_excel" class="btn btn-primary btn-sm mt-0"
                                                    data-imports="">
                                                    Export excel
                                                </button>
                                                <button type="button" id="export_excel_sov"
                                                    class="btn btn-primary btn-sm mt-0" data-imports="">
                                                    Export sov
                                                </button>
                                                <button type="button" id="export_txt" class="btn btn-primary btn-sm mt-0">
                                                    Export for Timberline txt
                                                </button>
                                                <button type="button" id="export_stp" class="btn btn-primary btn-sm mt-0">
                                                    Export for STP txt
                                                </button>
                                                {{-- <button type="button" id="export_completado" class="btn btn-primary btn-sm mt-0">
                                                    Export Completed
                                                </button> --}}
                                                <button type="button" id="import_data_base"
                                                    class="btn btn-primary btn-sm mt-0">
                                                    Import load DataBase
                                                </button>
                                                <button type="button" id="add_estimado_data_base"
                                                    class="btn btn-primary btn-sm mt-0">
                                                    Add estimate to existing project
                                                </button>
                                            </div>
                                            <br>
                                            <br>
                                        </div>
                                    </form>
                                    <form id="descargar_excel" method="POST" action="">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive tableFixHead">
                                <table id="list-proyectos" class="table table-hover thead-primary w-100">
                                    <thead id="load-data-thead" style="text-align:left">
                                        <tr>
                                            <th style="background: #4eb0e9;">
                                                &nbsp;&nbsp;&nbsp;&nbsp;ACTIONS&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th style="background: #4eb0e9;">AREA</th>
                                            {{-- <th>Area Description</th> --}}
                                            <th style="background: #4eb0e9;">COST CODE</th>
                                            <th style="background: #4eb0e9;">
                                                &nbsp;&nbsp;&nbsp;DESCRIPTION&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th style="background: #4eb0e9;">CC&nbsp;BUTGET&nbsp;QTY</th>
                                            <th style="background: #4eb0e9;">&nbsp;UM&nbsp;</th>
                                            <th style="background: #4eb0e9;">OF COATS</th>
                                            <th style="background: #4eb0e9;">PWT PROD RATE</th>
                                            <th style="background: #4eb0e9;">ESTIMATED HOURS</th>
                                            <th style="background: #4eb0e9;">ESTIMATED LABOR COST </th>
                                            <th style="background: #4eb0e9;">MATERIAL OR EQUIPMENT UNIT COST</th>
                                            <th style="background: #4eb0e9;">MATERIAL SPREAD RATE PER UNIT</th>
                                            <th style="background: #4eb0e9;">MAT QTY OR GALLONS / UNIT</th>
                                            <th style="background: #4eb0e9;">MAT UM</th>
                                            <th style="background: #4eb0e9;">MATERIAL COST</th>
                                            <th style="background: #4eb0e9;">PRICE TOTAL</th>
                                            <th style="background: #4eb0e9;">SUBCONTRACT COST</th>
                                            <th style="background: #4eb0e9;">EQUIPMENT COST</th>
                                            <th style="background: #4eb0e9;">OTHER COST</th>
                                        </tr>
                                    </thead>
                                    <tbody id="load-data-tbody">
                                        <tr style="background: #dee2e6">
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                            <td> &nbsp;</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.estimados.view-duplicados title="Info" />
    <x-components.estimados.historial_imports title="Info" />
    <x-components.estimados.view-save-import :user-id="$user_id" :user-name="$user_name" />
    <x-components.estimados.duplicar_area title="Info" />
    <x-components.estimados.view-superficie title="Info" />
    <x-components.estimados.view-standar title="Info" />
    <x-components.estimados.edit-import title="Info" />
    <x-components.estimados.update-area title="Info" />
    <x-components.estimados.add_estimado title="Info" />
@endsection
@push('datatable')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js">
    </script>
    <script>
        /*  var table_import = $('#list-proyectos').DataTable({
                        "paging": false,
                        "searching": false,
                        "lengthChange": false,
                        "info": false
                    }) */
    </script>
    <script>
        //modal multinivel
        $(document).ready(function() {
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

        //const
        var user_id = {{ $user_id }}
    </script>
    <script src="{{ asset('js/estimados/save_import.js') }}"></script>
    <script src="{{ asset('js/estimados/upload_excel.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-superficie.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-estandar.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-metodo.js') }}"></script>
    <script src="{{ asset('js/estimados/views/historial_import.js') }}"></script>
    <script src="{{ asset('js/estimados/duplicados_import_task.js') }}"></script>
@endpush
