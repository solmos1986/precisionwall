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
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/table-responsive.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="div-error container-fluid" id="validate" style="display: none;">
                <ul class="alert alert-danger ul-error">
                </ul>
            </div>
            {{ Breadcrumbs::render('new report') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Create visit report</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_goal" method="POST" enctype="multipart/form-data" action="{{ route('store.goal') }}">
                        @csrf
                        <input type="hidden" name="is_mail" class="is_mail">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="cc" class="cc">
                        <input type="hidden" name="title_m" class="title_m">
                        <input type="hidden" name="body_m" class="body_m">

                        <input type="date" name="Fecha" value="{{ date('Y-m-d') }}" hidden>
                        <input type="text" id="Informe_ID" name="Informe_ID" value="{{ $Informe_ID }}" hidden>
                        <div class="form-group">
                            <label for="generate">Project:</label>
                            <select class="form-control form-control-sm" id="proyect" name="Pro_ID" style="width:100%"
                                required></select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="general" class="col-sm-3 col-form-label col-form-label-sm">General
                                        Contractor</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="new_general"
                                            placeholder="General Contractor" value="" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ptw" class="col-sm-3 col-form-label col-form-label-sm">PRECISION WALL
                                        TECH
                                        PROJECT</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="new_codigo"
                                            placeholder="PRECISION WALL TECH PROJECT" value="" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="project" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="new_project"
                                            placeholder="Project Name" value="" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="new_address" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="new_address"
                                            placeholder="Project Address" value="" disabled="disabled">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="created_by" class="col-sm-2 col-form-label col-form-label-sm">Report
                                        by</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm" id="created_by"
                                            name="created_by" placeholder="Name by"
                                            value="{{ auth()->user()->Nombre }} {{ auth()->user()->Apellido_Paterno }} {{ auth()->user()->Apellido_Materno }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date_work" class="col-sm-2 col-form-label col-form-label-sm">Date</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm datepicker"
                                            id="date_work" name="date_work" value="{{ date('m/d/Y') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="row" id="tareas">
                                    <div class="col-md-12">
                                        <table class="table table-hover thead-light" id="table-actividad">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Cost code</th>
                                                    <th scope="col" width="250px">Task</th>
                                                    <th scope="col">Hours worked</th>
                                                    <th scope="col">*</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="none_tr_mat">
                                                    <td colspan="4" class="text-center">I don't add anything</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <button type="button"
                                                            class="btn btn-sm btn-success add-actividad">Add line
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="ms-directions">OTHERS TRADES WORK:</p>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="" class="col-sm-12 col-form-label col-form-label-sm">Quality of the
                                    substrates:
                                    Drywall / wood / metals / concrete etc.</label>
                                <textarea id="new_comentarios" name="new_comentarios" id="Quality" rows="10" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3 p-1">
                                        <label for="what"
                                            class="col-sm-12 col-form-label col-form-label-sm">Where?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="new_where_room" id="new_where_room"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-1">
                                        <label for="what"
                                            class="col-sm-12 col-form-label col-form-label-sm">Problems?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="new_problem" id="new_problem"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-1">
                                        <label for="what"
                                            class="col-sm-12 col-form-label col-form-label-sm">Consequences?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="new_consequences" id="new_consequences"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-1">
                                        <label for="what" class="col-sm-12 col-form-label col-form-label-sm">Suggested
                                            solution?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="new_solution" id="new_solution"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12 p-0">
                                @if (Auth::user()->verificarRol([1]))
                                    <div class="col-md-3 p-1">
                                        <br>
                                        <div style="" class="btn btn-sm btn-block btn-success mt-0 mb-3"
                                            id="create_razon">
                                            Create cuestion</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <p>Images</p>
                                    <div class="file-loading">
                                        <input id="input_images" name="input_images[]" type="file" accept="image/*"
                                            multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-success d-block send-mail" type="button" id="enviar">Save and
                            Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="modal_reason" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create options</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form id="razon">
                        <div class="form-group row">
                            <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Type Reason</label>
                            <div class="col-sm-9">
                                <select name="new_question_tipo" id="new_question_tipo"
                                    class="form-control form-control-sm" required>
                                    <option value="problem">Problem</option>
                                    <option value="consequence">consequences</option>
                                    <option value="solution">Solution</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Description</label>
                            <div class="col-sm-9">
                                <textarea name="new_question_description" id="new_question_description" cols="30" rows="10"
                                    class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                        <div id="generar_opciones">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" id="guardar_pregunta">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="mailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send visit report to emails</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form id="mail">
                        <input type="hidden" name="goal_id" id="goal_id">
                        <div class="form-group">
                            <label>TO: </label>
                            <input type="text" id="to" name="to" class="form-control tagsinput">
                        </div>
                        <div class="form-group">
                            <label>CC: </label>
                            <input type="text" id="cc" name="cc" class="form-control tagsinput">
                        </div>
                        <div class="form-group">
                            <label>Title Mail: </label>
                            <input type="text" name="title_m" id="title_m" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Mail Body: </label>
                            <textarea name="body_m" id="body_m" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Attached file: </label>
                            <span id="file_pdf"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm send_mail" data-part="all">Send Mail
                        Complete</button>
                    <button type="button" class="btn btn-success btn-sm send_mail" data-part="part">send to GC</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <x-components.register-actividad.modal-actividad />
@endsection
@push('javascript-form')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
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
    <script>
        $(document).ready(function() {
            fileinput_images("{{ $Informe_ID }}", 'input_images', 'images', 'goal');
        });
        ////modal
        $(document).on('click', '#create_razon', function() {
            $('#modal_reason #form_result').html('');
            $('#modal_reason #razon').trigger("reset");
            $("#modal_reason").modal("show");
        });
        $(document).on('click', '#guardar_pregunta', function() {
            $.ajax({
                url: "{{ route('option.store') }}",
                method: "POST",
                data: $("#modal_reason #razon").serialize(),
                dataType: "json",
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += `<p>${data.errors[count]}</p>`;
                        }
                        html += '</div>';
                        $('#modal_reason #form_result').html(html);
                    }
                    if (data.success) {
                        alert(data.success);
                        $('#modal_reason #razon').trigger("reset");
                        $('#modal_reason').modal('hide');
                    }
                }
            });
        });
        var Empleado_ID = '{{ auth()->user()->Empleado_ID }}'
        var fechaCreacion = '{{ date('Y-m-d') }}'
        var isAdmin = {{ Auth::user()->verificarRol([1]) == 1 ? Auth::user()->verificarRol([1]) : 0 }}
        var estado = 'nuevo'
    </script>
    <script src="{{ asset('js/goal/create.js') }}"></script>
    <script src="{{ asset('js/goal/create_question.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/register_actividad/modal_actividad.js') }}"></script>
    <script src="{{ asset('js/register_actividad/dataTable.js') }}"></script>
    <script src="{{ asset('js/goal/deteccion-actividad.js') }}"></script>
@endpush
