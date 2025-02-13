@extends('layouts.panel')
@push('css-header')
    {{-- implementacion para modal lg --}}
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
        .table i {
            margin-right: 0px;
            font-size: 18px;
        }
    </style>

    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
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
            <div class="invisible" id="statubs_crud"></div>
            {{ Breadcrumbs::render('lista empleados') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6 class="pt-1">Employee list</h6>
                    @if (Auth::user()->verificarRol([1, 5, 10]))
                        <a class="btn btn-pill btn-primary btn-sm" href="{{ route('create.cardex') }}">
                            Create employee</a>
                    @endif
                </div>
                <div class="ms-panel-body">

                    @if (Auth::user()->verificarRol([1, 5, 10]))
                        <p class="ms-directions mb-0">OTHERS OPTIONS</p>
                        <div class="row">
                            <div class="col-md-12">

                                <button class="btn  btn-primary btn-sm m-1" id="crear_evento">
                                    Add event to one or multiple employees</button>
                                <button class="btn  btn-primary btn-sm m-1" style="padding: 0.4rem 1rem" id="view_report">
                                    <i class="far fa-file-pdf"></i>
                                    Full Detail</button>
                                <label class="ms-checkbox-wrap">
                                    <input type="checkbox" value="true" name="images" id="images">
                                    <i class="ms-checkbox-check"></i>
                                </label>
                                <span>With images</span>
                                <button class="btn  btn-primary btn-sm m-1" style="padding: 0.4rem 1rem"
                                    id="skill_report_pdf">
                                    <i class="far fa-file-pdf"></i>
                                    Skill Reports Pdf</button>
                                <button class="btn btn-primary btn-sm m-1" style="padding: 0.4rem 1rem"
                                    id="skill_report_excel">
                                    <i class="far fa-file-excel"></i>
                                    Skill Reports Excel</button>
                                <form id="descargar_excel" method="POST" action="" hidden>
                                    @csrf
                                </form>
                                {{-- <a class="btn  btn-primary btn-sm m-1" href="{{ route('cardex.otros') }}">
                                    Other options</a> --}}
                                {{-- <a class="btn  btn-primary btn-sm m-1"
                                    href="{{ route('cardex.list.evento') }}">List
                                    event</a> --}}
                            </div>
                        </div>
                    @endif
                    <br>
                    <p class="ms-directions mb-0">MORE SEARCH OPTIONS</p>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="generate">Company:</label>
                                <select class="form-control form-control-sm " id="compañia" name="compañia[]"
                                    multiple="multiple" required>
                                    @foreach ($company as $empresa)
                                        <option value="{{ $empresa->Emp_ID }}">
                                            {{ $empresa->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="buton">Type:</label>
                                <select class="multiselect-all " id="tipo_personal" name="tipo_personal[]"
                                    multiple="multiple" required>
                                    @foreach ($tipos_personal as $tipo)
                                        <option value="{{ $tipo->id }}">
                                            {{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="buton">Position:</label>
                                <select class="multiselect-all " id="cargos" name="cargos[]" multiple="multiple"
                                    required>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}">
                                            {{ $cargo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="buton">NickName:</label>
                                <select class="multiselect-all " id="personas" name="personas[]" multiple="multiple"
                                    required>
                                    @foreach ($personal as $value)
                                        <option value="{{ $value->Empleado_ID }}">
                                            {{ $value->Nick_Name == null ? '' : $value->Nick_Name . ' | ' }}
                                            {{ $value->Nombre }} {{ $value->Numero == null ? '' : ' #' . $value->Numero }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="buton">Events:</label>
                                <select class="multiselect-all " id="eventos" name="eventos[]" multiple="multiple"
                                    required>
                                    @foreach ($eventos as $evento)
                                        <option value="{{ $evento->cod_evento }}">
                                            {{ $evento->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                    <br>
                    <div class="table-responsive">
                        <table id="list_personal" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Num.</th>
                                    <th>Full name</th>
                                    <th>Nickname</th>
                                    <th>Type of employee</th>
                                    <th>Position</th>
                                    <th>Skill</th>
                                    <th>Email</th>
                                    <th>Company</th>
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
    <!--Modal Eliminar -->
    <x-components.delete-modal />
    <!--crear eventos -->
    <x-components.movimiento.all-personal :eventos="$eventos" :company="$company" :cargos="$cargos" />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/fr.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    {{-- modal report --}}
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#list_personal').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `${base_url}/list-cardex-data-table?companies=${$('#compañia').val()}&tipos=${$('#tipo_personal').val()}&cargos=${$('#cargos').val()}&personas=${$('#personas').val()}&eventos=${$('#eventos').val()}&images=${$('#images').is(':checked')}`,
            order: [

            ],
            columns: [{
                    data: "Numero",
                    name: "Numero"
                },
                {
                    data: "Nombre",
                    name: "Nombre",
                },
                {
                    data: "Nick_Name",
                    name: "Nick_Name"
                },
                {
                    data: "nombre_tipo",
                    name: "nombre_tipo"
                },
                {
                    data: "Cargo",
                    name: "Cargo"
                },
                {
                    data: "eventos",
                    name: "eventos"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "nombre_empresa",
                    name: "nombre_empresa"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100,
        });

        $('#compañia').multiselect({
            buttonClass: 'form-control form-control-sm',
            buttonWidth: '100%',
            includeSelectAllOption: true,
            selectAllText: 'select all',
            selectAllValue: 'multiselect-all',
            enableCaseInsensitiveFiltering: true,
            enableFiltering: true,
            maxHeight: 400,
            onChange: function(option, checked) {

            },
            onFilter: function() {
                alert("Test")
            }
        });
        $('#tipo_personal').multiselect({
            buttonClass: 'form-control form-control-sm',
            buttonWidth: '100%',
            includeSelectAllOption: true,
            selectAllText: 'select all',
            selectAllValue: 'multiselect-all',
            enableCaseInsensitiveFiltering: true,
            enableFiltering: true,
            maxHeight: 400,
            onChange: function(option, checked) {

            }
        });
        $('#cargos').multiselect({
            buttonClass: 'form-control form-control-sm',
            buttonWidth: '100%',
            includeSelectAllOption: true,
            selectAllText: 'select all',
            selectAllValue: 'multiselect-all',
            enableCaseInsensitiveFiltering: true,
            enableFiltering: true,
            maxHeight: 400,
            onChange: function(option, checked) {

            }
        });
        $('#personas').multiselect({
            buttonClass: 'form-control form-control-sm',
            buttonWidth: '100%',
            includeSelectAllOption: true,
            selectAllText: 'select all',
            selectAllValue: 'multiselect-all',
            enableCaseInsensitiveFiltering: true,
            enableFiltering: true,
            maxHeight: 400,
            onChange: function(option, checked) {

            }
        });
        $('#eventos').multiselect({
            buttonClass: 'form-control form-control-sm',
            buttonWidth: '100%',
            includeSelectAllOption: true,
            selectAllText: 'select all',
            selectAllValue: 'multiselect-all',
            enableCaseInsensitiveFiltering: true,
            enableFiltering: true,
            maxHeight: 400,
            onChange: function(option, checked) {

            }
        });
        $(document).on('click', '#buscar', function() {
            table.ajax.url(
                `${base_url}/list-cardex-data-table?companies=${$('#compañia').val()}&tipos=${$('#tipo_personal').val()}&cargos=${$('#cargos').val()}&personas=${$('#personas').val()}&eventos=${$('#eventos').val()}&images=${$('#images').is(':checked')}`
            ).load();
        });
        $(document).on('click', '#limpiar', function() {
            //companies
            $('option', $('#compañia')).each(function(element) {
                $(this).removeAttr('selected').prop('selected', false);
            });
            $('#compañia').multiselect('refresh');
            //tipo personal
            $('option', $('#tipo_personal')).each(function(element) {
                $(this).removeAttr('selected').prop('selected', false);
            });
            $('#tipo_personal').multiselect('refresh');
            //cargo
            $('option', $('#cargos')).each(function(element) {
                $(this).removeAttr('selected').prop('selected', false);
            });
            $('#cargos').multiselect('refresh');
            //nick name
            $('option', $('#personas')).each(function(element) {
                $(this).removeAttr('selected').prop('selected', false);
            });
            $('#personas').multiselect('refresh');
            //nick eventos
            $('option', $('#eventos')).each(function(element) {
                $(this).removeAttr('selected').prop('selected', false);
            });

            $('#eventos').multiselect('refresh');
            table.ajax.url(
                `${base_url}/list-cardex-data-table?companies=${$('#compañia').val()}&tipos=${$('#tipo_personal').val()}&cargos=${$('#cargos').val()}&personas=${$('#personas').val()}&eventos=${$('#eventos').val()}&images=${$('#images').is(':checked')}`
            ).load();
        });
    </script>
    {{-- report --}}
    <script src="{{ asset('js/cardex/view_report.js') }}" type="text/javascript"></script>
    {{-- lista --}}
    <script src="{{ asset('js/listCardex.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/cardex/delete.js') }}" type="text/javascript"></script>
    {{-- multiples eventos --}}
    <script src="{{ asset('js/cardex/create_all.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/cardex/store_all.js') }}" type="text/javascript"></script>
@endpush
