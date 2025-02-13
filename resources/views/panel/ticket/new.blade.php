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
            {{Breadcrumbs::render('new ticket',$proyecto->Actividad_ID,$ticket)}}
            <div class="ms-panel">
                <div class="ms-panel-header">
                    <h6>Ticket #{{ $n_ticket }}</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_ticket" method="post" enctype="multipart/form-data"
                        action="{{ route('store.ticket', ['id' => $id]) }}">
                        @csrf
                        <input type="hidden" name="ticket_id" value="{{ $ticket }}">
                        <input type="hidden" name="is_mail" class="is_mail">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="cc" class="cc">
                        <input type="hidden" name="title_m" class="title_m">
                        <input type="hidden" name="body_m" class="body_m">
                        @if (Auth::user()->verificarRol([1]))
                            <div class="form-group">
                                <label for="generate">Assign ticket to:</label>
                                <select class="form-control form-control-sm" id="empleoye" style="width:100%"
                                    required></select>
                            </div>
                        @endif
                        <input type="hidden" id="empleado_id" name="empleado_id"
                            value="{{ auth()->user()->Empleado_ID }}">
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
                                    <label for="ptw" class="col-sm-3 col-form-label col-form-label-sm">PRECISION WALL TECH
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
                                            placeholder="Project Name" value="{{ $proyecto->Nombre }}"
                                            disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Project
                                        Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="address"
                                            placeholder="Project Address" value="{{ $address }}" disabled="disabled">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Date of
                                        Work</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_work"
                                            name="date_work" value="{{ date('m/d/Y') }}" autocomplete="off">
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
                                        <input type="text" class="form-control form-control-sm" name="pco" id="pco">
                                    </div>
                                </div>
                                <ul class="ms-list d-flex">
                                    <li class="ms-list-item pl-0">
                                        <label class="ms-checkbox-wrap">
                                            <input type="radio" name="horario" value="day" checked="">
                                            <i class="ms-checkbox-check"></i>
                                        </label>
                                        <span> Day </span>
                                    </li>
                                    <li class="ms-list-item">
                                        <label class="ms-checkbox-wrap">
                                            <input type="radio" name="horario" value="night">
                                            <i class="ms-checkbox-check"></i>
                                        </label>
                                        <span> Night </span>
                                    </li>
                                    <li class="ms-list-item">
                                        <label class="ms-checkbox-wrap">
                                            <input type="radio" name="horario" value="overtime">
                                            <i class="ms-checkbox-check"></i>
                                        </label>
                                        <span> Overtime </span>
                                    </li>
                                    <li class="ms-list-item">
                                        <label class="ms-checkbox-wrap">
                                            <input type="radio" name="horario" value="premium time">
                                            <i class="ms-checkbox-check"></i>
                                        </label>
                                        <span> Premium Time </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <p class="ms-directions">DESCRIPCION OF WORK: What? / Where? / Why?</p>
                        <textarea name="descripcion" id="descripcion" rows="5" class="form-control mb-3"></textarea>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group row">
                                    <label for="what" class="col-sm-2 col-form-label col-form-label-sm">What?</label>
                                    <div class="col-sm-10">
                                        <select name="" id="what" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group row">
                                    <label for="where" class="col-sm-2 col-form-label col-form-label-sm">Where?</label>
                                    <div class="col-sm-10">
                                        <select name="" id="where" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group row">
                                    <label for="why" class="col-sm-2 col-form-label col-form-label-sm">Why?</label>
                                    <div class="col-sm-10">
                                        <select name="" id="why" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->verificarRol([1]))
                                <div class="col-md">
                                    <div class="btn btn-sm btn-block btn-success mt-0 mb-3" id="create_razon">create
                                        cuestion</div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-hover thead-light" id="table-material">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="250px">Material Description</th>
                                            <th scope="col">Unit of Measurement</th>
                                            <th scope="col">QTY</th>
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
                                                <div class="btn btn-sm btn-success add-material">Add material</div>
                                                @if (Auth::user()->verificarRol([1]))
                                                    <div class="btn btn-sm btn-warning" id="modal_material">Create
                                                        material</div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <table class="table table-hover thead-light" id="table-class">
                            <thead>
                                <tr>
                                    <th scope="col">N° workers</th>
                                    <th scope="col">Employees</th>
                                    <th scope="col">Regular Hrs</th>
                                    <th scope="col">T. Regular Hrs</th>
                                    <th scope="col">Premium Hrs</th>
                                    <th scope="col">T. Premium Hrs</th>
                                    <th scope="col">Overtime Hrs.</th>
                                    <th scope="col">T.Overtime Hrs.</th>
                                    <th scope="col">Allowance Hrs.</th>
                                    <th scope="col">T. Allowance Hrs.</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="none_tr_class">
                                    <td colspan="11" class="text-center">I don't add anything</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <div class="btn btn-sm btn-success add-class">Add worker</div>
                                        @if (Auth::user()->verificarRol([1]))
                                            <div class="btn btn-sm btn-warning" id="create_trabajo">Create profession
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <p class="ms-directions">Signer verifies PWT, has completed the work stated above under my
                            supervision. Time and material listed above are accurate and approved</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="supername"
                                        class="col-sm-4 col-form-label col-form-label-sm">Superintendent's Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="supername" id="supername"
                                            class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="date_super" class="col-sm-4 col-form-label col-form-label-sm">Finish
                                        Date</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="date_super" id="date_super"
                                            class="form-control form-control-sm datepicker" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p>Previous Images</p>
                                    <div class="file-loading">
                                        <input id="images_inicio" name="images_inicio[]" type="file" accept="image/*"
                                            multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p>Final Images</p>
                                    <div class="file-loading">
                                        <input id="images_final" name="images_final[]" type="file" accept="image/*"
                                            multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <img class="img-thumbnail" id="img_signature_super">
                                    <p>Superintendent's Signature</p>
                                    <div class="btn btn-pill btn-success mt-0 btn-sm signature"
                                        data-title="Surperintendent's Signature" data-id_img="img_signature_super"
                                        data-id_img_input="input_signature_super" data-input_text="supername">Add Signature
                                    </div>
                                    <input type="hidden" id="input_signature_super" name="input_signature_super">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <img class="img-thumbnail" id="img_signature_fore">
                                    <p>Foreman Signature</p>
                                    <div class="btn btn-pill btn-success mt-0 btn-sm signature"
                                        data-title="Foreman Signature" data-id_img="img_signature_fore"
                                        data-id_img_input="input_signature_fore" data-input_text="foreman_name">Add
                                        Signature</div>
                                    <input type="hidden" id="input_signature_fore" name="input_signature_fore">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="ms-panel-footer">
                    <button class="btn btn-success d-block" type="submit" id="enviar">Save and Continue</button>
                </div>
            </div>
        </div>
    </div>

    <x-components.firma-modal />
    @if (Auth::user()->verificarRol([1]))
        <x-components.profesion-modal />
        <x-components.material-modal />
        <x-components.reaseon-modal />
    @endif
    <x-components.mail-modal title="tickets" />
@endsection
@push('javascript-form')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
    <script>
        var Pro_ID = "{{ $proyecto->Pro_ID }}";
        var num_ticket = "{{ $n_ticket }}";
        $(document).ready(function() {
            fileinput_images("{{ $ticket }}", 'images_inicio', 'inicio', 'ticket');
            fileinput_images("{{ $ticket }}", 'images_final', 'final', 'ticket');
        });

    </script>
    <script src="{{ asset('js/ticket.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/firmas_ticket.js') }}"></script>
    <script src="{{ asset('js/select_what_where_why.js') }}"></script>
@endpush
