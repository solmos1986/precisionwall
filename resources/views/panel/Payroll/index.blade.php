@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
        rel="stylesheet" />
    <style>
        .table td,
        .table th {
            padding: 0.2rem;
            vertical-align: inherit;
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

        .table-cell-edit {
            background-color: rgb(250, 65, 65);
        }

        .redClass {
            background-color: rgb(250, 65, 65);
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6 class="p-2">Payroll/Compare info:</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0">IMPORT FILE TIMBERLINE</p>
                                    <form id="descargar_excel" method="POST" action="">
                                        @csrf
                                    </form>
                                    <form id="descargar_payroll" action="{{ route('payroll.upload.timerline') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-file">
                                                    <input type="file" name="doc_timerLine" class="custom-file-input"
                                                        id="doc_timerLine">
                                                    <label class="custom-file-label" id="label_upload_timerLine"
                                                        for="doc_timerLine">Choose
                                                        file...</label>
                                                    <div class="invalid-feedback">error</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <button type="button" id="upload_timerLine"
                                                    class="btn btn-primary btn-sm m-1 has-icon">
                                                    <i class="fa fa-upload"></i>
                                                    Import Timberline
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0">IMPORT FILE EMPLOYEE</p>
                                    <form id="form_upload_excel" action="{{ route('upload.excel') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-file">
                                                    <input type="file" name="doc_list_employee" class="custom-file-input"
                                                        id="doc_list_employee">
                                                    <label class="custom-file-label" id="label_list_employee"
                                                        for="label_list_employee">Choose
                                                        file...</label>
                                                    <div class="invalid-feedback">error</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <button type="button" id="upload_list_employee"
                                                    class="btn btn-primary btn-sm m-1 has-icon">
                                                    <i class="fa fa-upload"></i>
                                                    Import list employee
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <form id="descargar_excel" method="POST" action="">
                                    @csrf
                                </form>
                            </div>
                        </div>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <div class="col-md-12">
                            <hr class="mt-0">
                            <p class="mb-0"><strong>FIELD TIME MANAGER</strong></p>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="NombretimberLine" class="mb-0">TimberLine</label>
                                                <input type="text" id="timberLineId" hidden>
                                                <input type="text" class="form-control" id="NombretimberLine"
                                                    placeholder="TimberLine" readonly>
                                                <button type="button" id="open_timerLine_save"
                                                    class="btn btn-primary btn-sm m-1 has-icon">
                                                    <i class="fa fa-folder-open"></i>
                                                    Import Timberline saves
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombreListEmployee" class="mb-0">List employee</label>
                                                <input type="text" id="ListEmployeeId" hidden>
                                                <input type="text" class="form-control" id="nombreListEmployee"
                                                    placeholder="List employee" readonly>
                                                <button type="button" id="open_list_employee_save"
                                                    class="btn btn-primary btn-sm m-1 has-icon">
                                                    <i class="fa fa-folder-open"></i>
                                                    Import list employee saves
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6 m-0">
                                            <div class="form-group m-0">
                                                <label for="NombretimberLine" class="mb-0">From date</label>
                                                <input type="text" class="form-control datepicke" id="from_date"
                                                    name="from_date" placeholder="From date">
                                            </div>
                                        </div>
                                        <div class="col-md-6 m-0">
                                            <div class="form-group m-0">
                                                <label for="NombretimberLine" class="mb-0">To date</label>
                                                <input type="text" class="form-control datepicke" id="to_date"
                                                    name="to_date" placeholder="To date">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="button" id="open_save_payroll"
                                                class="btn btn-success btn-sm m-1 has-icon">
                                                <i class="fas fa-redo"></i>
                                                Compare
                                            </button>
                                            <button type="button" id="reset"
                                                class="btn btn-warning btn-sm m-1 has-icon">
                                                <i class="fa fa-eraser"></i>
                                                Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <p class="mb-0"><strong>DAILY EMPLOYEE TIME</strong></p>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="import_payroll" style="p-1"
                                        class="btn btn-primary btn-sm m-1 has-icon">
                                        <i class="fa fa-folder-open"></i>
                                        Import Payroll
                                    </button>
                                </div>

                                <div class="col-md-12 text-center">
                                    <p class="mb-0 text-center">Days</p>
                                    <div id="lista_fecha">

                                    </div>
                                </div>
                            </div>
                            <p class="ms-directions mb-0">MORE SEARCH OPTIONS</p>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group m-1">
                                        <label for="NombretimberLine" class="mb-0">View</label>
                                    </div>
                                    <label class="ms-checkbox-wrap">
                                        <input type="checkbox" value="true" name="error" id="error">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span>Show errors</span>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label for="NombretimberLine" class="mb-0">NickName</label>
                                        <input type="text" class="form-control datepicke hasDatepicker" id="nick_name"
                                            name="nick_name" placeholder="Nick Name">
                                    </div>
                                </div>
                                <div class="col-md-2">

                                </div>
                                <div class="col-md-2">

                                </div>
                                <div class="col-md-2">

                                </div>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-pill btn-primary d-block btn-sm mt-0"
                                        style="padding: 0.4rem 1rem" id="buscar">
                                        <i class="fa fa-search"></i>
                                        Search</button>
                                    <button class="btn btn-pill btn-warning d-block btn-sm mt-0"
                                        style="padding: 0.4rem 1rem" id="limpiar">
                                        <i class="fas fa-trash"></i>
                                        Clean</button>
                                </div>
                            </div>
                            <br>
                            <p class="mb-0 text-center payroll_fecha_eject" style="background: #c6c7c8"></p>
                            <div class="table-responsive">
                                <input type="text" id="payroll_id_eject" hidden>
                                <input type="text" id="payroll_id_fecha_eject" hidden>
                                <table class="table table-hover thead-primary" id="datatable_payroll_data">
                                    <thead>
                                        <tr>
                                            <th width="150">EMPLOYEE</th>
                                            <th width="150">JOB</th>
                                            <th width="150">BUILDING</th>
                                            <th width="150">FLOOR</th>
                                            <th width="150">AREA</th>
                                            <th>COST CODE</th>
                                            <th>CAT</th>
                                            <th>HOURS</th>
                                            <th>HR TYPE</th>
                                            <th>PAY ID</th>
                                            <th>WORD DATE</th>
                                            <th>CERT CLASS</th>
                                            <th>Reimb ID</th>
                                            <th>Units</th>
                                            <th>MAT QTY OR GALLONS / UNIT</th>
                                            <th>UM</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.payroll.timberline-list title="Info" />
    <x-components.payroll.save-timberline title="Info" />

    <x-components.payroll.list-employee-list title="Info" />
    <x-components.payroll.save-list-employee title="Info" />

    <x-components.payroll.listpayroll title="Info" />
    <x-components.payroll.save-payroll title="Info" />

    <x-components.estimados.view-duplicados title="Info" />
    <x-components.estimados.historial_imports title="Info" />
    <x-components.estimados.duplicar_area title="Info" />
    <x-components.estimados.view-superficie title="Info" />
    <x-components.estimados.view-standar title="Info" />
    <x-components.estimados.edit-import title="Info" />
    <x-components.estimados.update-area title="Info" />
@endsection
@push('datatable')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js">
        < script type = "text/javascript"
        src = "https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" >
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.3/jquery.scrollTo.min.js"></script>
    <script src="{{ asset('js/errors_ajax.js') }}"></script>
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
    </script>
    <script src="{{ asset('js/estimados/save_import.js') }}"></script>
    <script src="{{ asset('js/estimados/upload_excel.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-superficie.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    {{-- NUEVO --}}
    <script src="{{ asset('js/Payroll/listaImportTimberLine/uploadFileTimberLine.js') }}"></script>
    <script src="{{ asset('js/Payroll/listaImportTimberLine/listaImportTimberline.js') }}"></script>

    <script src="{{ asset('js/Payroll/listaEmployee/listaImportListEmployee.js') }}"></script>
    <script src="{{ asset('js/Payroll/listaEmployee/uploadFileListEmployee.js') }}"></script>

    <script src="{{ asset('js/Payroll/listPayRoll/listaImportPayRoll.js') }}"></script>
    <script src="{{ asset('js/Payroll/listPayRoll/uploadpayRoll.js') }}"></script>

    <script src="{{ asset('js/Payroll/payroll.js') }}"></script>
    <script src="{{ asset('js/Payroll/dataTablePayroll.js') }}"></script>
@endpush
