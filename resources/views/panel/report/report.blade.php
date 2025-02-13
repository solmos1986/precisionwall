@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
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
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            {{Breadcrumbs::render('report')}}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Reports</h6>
                </div>
                <div class="ms-panel-body">
                    <h6>Report of Attendance</h6>
                    <hr>
                    <div class="ms-panel ms-panel-fh">

                        <div class="ms-panel-body clearfix">
                            <p class="ms-directions">list by:</code></p>
                            <ul class="nav nav-tabs tabs-bordered left-tabs nav-justified" role="tablist"
                                aria-orientation="vertical">
                                <li role="presentation"><a href="#tab1" aria-controls="tab1" class="active show" role="tab"
                                        data-toggle="tab">
                                        Companies </a></li>
                                <!--li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">
                                            Staff </a></li-->

                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active show fade in" id="tab1">
                                    <form id="asistencia" method="POST" action="{{ route('reporte.asistencia') }}">
                                        @csrf
                                        <p>Report of Attendancen by companies</p>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="generate">Start date:</label>
                                                    <input type="text" class="form-control form-control-sm datepicker" id=""
                                                        name="fecha_inicio" style="width:100%" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="generate">End date:</label>
                                                    <input type="text" class="form-control form-control-sm datepicker" id=""
                                                        name="fecha_fin" style="width:100%" required></input>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="generate">Company:</label>
                                                    <select class="form-control form-control-sm" id="company" name="empresa"
                                                        required>
                                                        <option value="0" selected="selected">None selected</option>
                                                        @foreach ($empresas as $empresa)
                                                            <option value="{{ $empresa->Emp_ID }}">
                                                                {{ $empresa->Nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="generate">Type:</label>
                                                    <select class="form-control form-control-sm" id="tipo_empleado"
                                                        name="tipo_empleado[]" multiple="multiple" required>
                                                        @foreach ($tipo_empleado as $tipo)
                                                            <option value="{{ $tipo->Aux5 }}">
                                                                {{ $tipo->Aux5 }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="buton">Nick Name:</label>
                                                    <select class="multiselect-all " id="nick_name" name="nick_name[]"
                                                        multiple="multiple" required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Optional:</label><br>
                                                    <label class="ms-checkbox-wrap ">
                                                        <input type="checkbox" value="true" name="detalle" id="detalle">
                                                        <i class="ms-checkbox-check"></i>
                                                    </label>
                                                    <span> Detail </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary " name="view" value="true"
                                                            id="view_lista_persona_empresa">
                                                            <i class="fas fa-file"></i>
                                                            View pdf</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary " type="submit" id="generar">
                                                            <i class="far fa-file-pdf"></i>
                                                            Donwload pdf</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary " name="view" value="true"
                                                            id="excel_lista_persona_empresa">
                                                            <i class="far fa-file-excel"></i>
                                                            Donwload excel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="tab2">

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <style>
        .multiselect-all label {
            font-weight: bold;
        }

        .multiselect-search {
            color: red;
        }

    </style>
@endsection
@push('javascript-form')
    <script type="text/javascript" src="{{ asset('js/pdfobject.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>
    <script>
        //pdf Preview
        var preview = false;
        var excel = false;
        var pdf = false
        $("#generar").on('click', function(evt) {
            preview = false;
            excel = false;
            pdf = true;
        });
        $("#excel_lista_persona_empresa").on('click', function(evt) {
            excel = true;
            preview = false;
            pdf = false
        });
        $("#view_lista_persona_empresa").on('click', function(evt) {
            preview = true;
            excel = false;
            pdf = false;
            var paramas = getFormData();
            var options = {
                url: `{{ route('view.reporte.asistencia') }}?fecha_inicio=${paramas.fecha_inicio}&fecha_fin=${paramas.fecha_fin}&empresa=${paramas.empresa}&nick_name=${paramas.nick_name}&detalle=${paramas.detalle}`,
                title: 'Preview',
                size: eModal.size.lg,
                buttons: [{
                    text: 'ok',
                    style: 'info',
                    close: true
                }],
            };
            if (validate()) {
                eModal.iframe(options);
            } else {
                alert("fill in the fields")
            }
        });

        //validate
        function validate() {
            var select = $("input").val();
            var empresa = $("#company").val();
            var nick = $("#nick_name").val();
            if ((select.length > 0) && (empresa.length > 0) && (nick.length > 0)) {
                return true;
            } else {
                return false;
            }
        }

        $("#asistencia").on('submit', function(evt) {

            if (preview == true) {
                evt.preventDefault();
            }
            if (excel == true) {
                $(this).attr("action", "{{ route('report.asistencia.excel') }}");

            }
            if (pdf == true) {
                $(this).attr("action", "{{ route('reporte.asistencia') }}");

            }
        });

        function getFormData() {
            var config = {};
            $('input').each(function() {
                config[this.name] = this.value;
            });
            $('#company').each(function() {
                config[this.name] = this.value;
            });
            // Obtenemos los atributos que necesitamos
            let selecteds = [];
            $('#nick_name').children(':selected').each((idx, el) => {
                selecteds.push(
                    el.value
                );
            });
            config['nick_name'] = selecteds;
            if ($('#detalle').prop('checked')) {
                config['detalle'] = true;
            } else {
                config['detalle'] = false;
            }
            //console.log(config)
            return config;
        }


        function ajax_personal() {
            let selecteds = [];
            $('#tipo_empleado').children(':selected').each((idx, el) => {
                selecteds.push(
                    el.value
                );
            });
            $.ajax({
                url: `${base_url}/get_personal/${$("select[name=empresa] option").filter(":selected").val()}?tipo_personal=${selecteds}`,
                dataType: "json",
                async: false,
                success: function(response) {
                    //elimina todo elvalue de select
                    $("#nick_name").empty();
                    //recorre la respuesta
                    $.each(response, function(i, item) {
                        //console.log(i, item)
                        if ($("#nick_name option[value='" + item.Empleado_ID + "']").length == 0) {
                            $('#nick_name').append('<option value="' + item.Empleado_ID + '">' + item
                                .Nick_Name + '</option>');
                        }
                    });
                    //reinicia el select
                    $('#nick_name').multiselect('rebuild');
                },
            });
        }

        function select_personal() {
            $('#nick_name').multiselect({
                buttonClass: 'form-control form-control-sm',
                buttonWidth: '100%',
                includeSelectAllOption: true,
                selectAllText: 'select all',
                selectAllValue: 'multiselect-all',
                enableCaseInsensitiveFiltering: true,
                enableFiltering: true,
                maxHeight: 400,
            });
        }

        $(document).ready(function() {
            $('#company').multiselect({
                buttonClass: 'form-control form-control-sm',
                buttonWidth: '100%',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 400,
                //evento charge solicita datos
                onChange: function(option, checked) {
                    $('#tipo_empleado').multiselect('refresh');
                    $('#tipo_empleado').multiselect('rebuild');
                }
            });
            select_personal();

            $('#tipo_empleado').multiselect({
                buttonClass: 'form-control form-control-sm',
                buttonWidth: '100%',
                enableCaseInsensitiveFiltering: true,
                enableFiltering: true,
                maxHeight: 400,
                onChange: function(option, checked) {
                    ajax_personal();
                }
            });
        });
    </script>
    <script src="{{ asset('js/report.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
@endpush
