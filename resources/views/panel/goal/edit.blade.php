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
            {{ Breadcrumbs::render('edit report', $informe_proyect->Codigo) }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Edit visit report {{ $informe_proyect->Codigo }}</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_goal" method="POST" enctype="multipart/form-data"
                        action="{{ route('update.goal', ['id' => $informe_proyect->Informe_ID]) }}">
                        @csrf
                        <input type="hidden" name="is_mail" class="is_mail">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="cc" class="cc">
                        <input type="hidden" name="title_m" class="title_m">
                        <input type="hidden" name="body_m" class="body_m">
                        <input type="text" id="Informe_ID" name="Informe_ID" value="{{ $id }}" hidden>
                        <div class="form-group">
                            <label for="generate">Project:</label>
                            <input type="text" class="form-control form-control-sm" id="general"
                                placeholder="General Contractor" value="{{ $informe_proyect->nombre_proyecto }}"
                                disabled="disabled">
                            <input type="text" id="edit_Pro_ID" name="edit_Pro_ID"
                                value="{{ $informe_proyect->Pro_ID }}" hidden>
                        </div>
                        <div class="row">

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="general" class="col-sm-3 col-form-label col-form-label-sm">General
                                        Contractor</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="edit_general"
                                            placeholder="General Contractor" value="{{ $informe_proyect->codigo_empresa }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ptw" class="col-sm-3 col-form-label col-form-label-sm">PRECISION WALL
                                        TECH
                                        PROJECT</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="edit_codigo"
                                            placeholder="PRECISION WALL TECH PROJECT"
                                            value="{{ $informe_proyect->codigo_proyecto }}" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="project" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="edit_project"
                                            placeholder="Project Name" value="{{ $informe_proyect->nombre_proyecto }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_address" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="edit_address"
                                            placeholder="Project Address" value="{{ $informe_proyect->dirrecion }}"
                                            disabled="disabled">
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
                                            value="{{ $informe_proyect->nombre_empleado }}" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date_work" class="col-sm-2 col-form-label col-form-label-sm">Date</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm datepicker"
                                            id="date_work" name="date_work" value="{{ $informe_proyect->Fecha }}"
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
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <button type="button"
                                                            class="btn btn-sm btn-success add-actividad-edit">Add line
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
                                <textarea id="edit_comentarios" name="edit_comentarios" id="Quality" rows="10" class="form-control">{{ $informe_proyect->Drywall_comments }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3 p-1">
                                        <label for="what"
                                            class="col-sm-12 col-form-label col-form-label-sm">Where?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="edit_where_room" id="edit_where_room"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-1">
                                        <label for="what"
                                            class="col-sm-12 col-form-label col-form-label-sm">Problems?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="edit_problem" id="edit_problem"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-1">
                                        <label for="what"
                                            class="col-sm-12 col-form-label col-form-label-sm">Consequences?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="edit_consequences" id="edit_consequences"
                                                class="form-control form-control-sm"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-1">
                                        <label for="what" class="col-sm-12 col-form-label col-form-label-sm">Suggested
                                            solution?</label>
                                        <div class="col-sm-12 p-0">
                                            <select name="edit_solution" id="edit_solution"
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
                        <button class="btn btn-success d-block" type="submit" id="enviar">Save and Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div id="modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-dark">
                    <input type="hidden" id="id_signature">
                    <input type="hidden" id="id_signature_input">
                    <div id="signature-pad"><canvas style="border:1px solid #000" id="sign"></canvas></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" id="guardar_firma">Save Signature</button>
                    <button type="button" class="btn btn-success btn-sm" id="limpiar">Clear</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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


    <div id="mailModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
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
@endsection

@push('javascript-form')
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
            fileinput_images("{{ $id }}", 'input_images', 'images', 'goal');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        ////modal

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
        var Empleado_ID = '{{ $informe_proyect->Empleado_ID }}'
        var isAdmin = {{ Auth::user()->verificarRol([1]) == 1 ? Auth::user()->verificarRol([1]) : 0 }}
        var fechaCreacion = '{{ $informe_proyect->fechaCreacion }}'
        var estado = 'editar'
    </script>
    <script src="{{ asset('js/register_actividad/modal_actividad.js') }}"></script>
    <script src="{{ asset('js/goal/edit.js') }}"></script>
    <script src="{{ asset('js/goal/create_question.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
    <script src="{{ asset('js/goal/deteccion-actividad.js') }}"></script>
@endpush
