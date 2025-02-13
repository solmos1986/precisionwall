@extends('layouts.panel')
@push('css-header')
<style>
    .centrar-bottons {
        width: 100px;
        display: flex;
        justify-content: center;
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
        <div class="invisible" id="status_crud"></div>
        {{Breadcrumbs::render('new formulario')}}
        <div id="contenedor" class="row justify-content-md-center">
            <!--Form-->
            <div class="col-md-10">
                <div id="titulo" class="ms-panel">
                    <div class="ms-panel-header ms-panel-custome">
                        <h6>Create form</h6>
                    </div>
                    <div class="ms-panel-body">

                        <div class="input-group input-group-lg">
                            <input id="title" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-lg" placeholder="Title">
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <input id="description" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm" placeholder="description">
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-10 question">
                <div class="ms-panel">
                    <div class="ms-panel-header ">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" class="form-control descripcion"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default"
                                        placeholder="SUBTITLE">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group input-group-sm ">
                                    <input type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" placeholder="optional description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ms-panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="jumbotron" style="padding: 1rem 1rem">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    aria-label="Sizing example input"
                                                    aria-describedby="inputGroup-sizing-default" placeholder="question">
                                            </div>
                                        </div>
                                        <div class="col-md-3 pb-3">
                                            <div class="dropdown in-line">
                                                <button class="btn btn-primary btn-sm dropdown-toggle mt-0"
                                                    type="button" id="dropdownMenu2" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Type of question
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                    <button class="dropdown-item button_check_box" type="button"><i
                                                            class="fas fa-check-square"></i>
                                                        Check box</button>
                                                    <button class="dropdown-item button_box" type="button"><i
                                                            class="fas fa-dot-circle"></i>
                                                        Box</button>
                                                    <button class="dropdown-item button_paragraph" type="button"><i
                                                            class="fas fa-align-left"></i>
                                                        Paragraph</button>
                                                    <button class="dropdown-item button_lineal_scale" type="button"><i
                                                            class="fas fa-ellipsis-h"></i>
                                                        Linear scale</button>
                                                    <button class="dropdown-item delete_pregunta" type="button"><i
                                                            class="far fa-window-close"></i>
                                                        Delete all</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <button class="btn btn-primary btn-sm add_options">
                                                    Add options
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" href="#"
                                    class="ms-btn-icon btn-pill btn-danger float-right eliminar_question">
                                    <i class="fas fa-trash-alt"></i></button>
                                <a type="button" href="#controls" class="ms-btn-icon btn-pill btn-success  agregar"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="controls" class="col-md-10">
                <div class="ms-panel">
                    <div class="ms-panel-body">
                        <div class="justify-content-md-center">
                            <div class="text-center">
                                <a id="subtitle" type="button" href="#controls" class="ms-btn-icon btn-pill btn-primary"><i
                                        class="fas fa-pencil-alt"></i></a>
                                <a id="view" type="button" href="{{ route('form.preview') }}" target="_blank"
                                    class="ms-btn-icon btn-pill btn-primary"><i class="fas fa-eye"></i></a>
                                <!--a type="button" id="save" href="#controls" class="ms-btn-icon btn-pill btn-info"><i
                                        class="fa fa-window-restore"></i></a>
                                <a type="button" href="{{ route('list.form') }}" class="ms-btn-icon btn-pill btn-info"><i
                                        class="fas fa-power-off"></i></a-->
                            </div>
                            <div class="col-md-12">
                                <button type="button"  id="save" href="#controls"
                                    class="btn btn-primary btn-sm float-right eliminar_question">
                                    Save</button>
                                <a type="button" href="{{ route('list.form') }}" class="btn btn-primary btn-sm agregar">
                                    Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
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
<script src="{{ asset('js/forms/question.js') }}"></script>
<script src="{{ asset('js/forms/form-evaluacion.js') }}"></script>
<script src="{{ asset('js/forms/getForm.js') }}"></script>
<script>
    var form = new Form();
        //create question
        var type = 'checkBox';
        $(document).on('click', '#subtitle', function() {
            form.newDescription();
        })
        $(document).on('click', '.agregar', function() {
            form.newSeccion(this);
        })
        $(document).on('click', '.delete_pregunta', function() {
            form.deletePregunta(this);
        })
        $(document).on('click', '.eliminar_question', function() {
            form.deleteQuestion(this);
        })

        //options

        $(document).on('click', '.add_options', function() {
            form.newOptions(this, type);
        })
        $(document).on('click', '.delete_option', function() {
            form.deleteOption(this);
        })

        //dropbox
        $(document).on('click', '.button_check_box', function() {
            form.updateCheckBox(this);
            type = 'checkBox';
        })
        $(document).on('click', '.button_box', function() {
            form.updateBox(this);
            type = 'box';
        })
        $(document).on('click', '.button_paragraph', function() {
            form.updateParagraph(this);
        })
        $(document).on('click', '.button_lineal_scale', function() {
            form.updateLinealScale(this);
        })

        //obtener datos
        var getForm = new GetForm();
        $(document).on('click', '#view', function() {
            getForm.getQuestion();
        })

        //subir data
        $('#save').click(function(event) {
            const data=getForm.get_formulario();
            console.log('enviando')
            $.ajax({
                type: "POST",
                url: `${base_url}/store-form`,
                data:{
                    title:data.title,
                    secciones:data.secciones,  
                    description:data.description       
                },
                dataType:'json',
                //contentType: "application/json; charset=utf-8",
                success: function (data) {
                    if (data.errors) {
                        $alert = 'complete the following fields to continue:\n'
                        data.errors.forEach(function(error) {
                            $alert += `* ${error}\n`
                        })
                        alert($alert)
                    }
                    if (data.success) {
                        alert(data.success)
                        location.href=`${base_url}/list-form`;
                    }
                }
               
            });
        });
        
</script>
@endpush