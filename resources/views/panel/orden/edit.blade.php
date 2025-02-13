@extends('layouts.panel')
@push('css-header')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/table-responsive.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="div-error container-fluid" id="validate" style="display: none;">
                <ul class="alert alert-danger ul-error">
                </ul>
            </div>
            {{ Breadcrumbs::render('edit order', $orden->num) }}
            <div class="ms-panel">

                <div class="ms-panel-header ms-panel-custome">
                    <h6>Order #{{ $orden->num }}</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_order" method="post" enctype="multipart/form-data"
                        action="{{ route('update.orden', ['id' => $id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_mail" class="is_mail">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="cc" class="cc">
                        <input type="hidden" name="title_m" class="title_m">
                        <input type="hidden" name="body_m" class="body_m">
                        <div class="form-group">
                            <label for="generate">assign to project:</label>
                            @if (Auth::user()->verificarRol([1]))
                                <select class="form-control form-control-sm" id="proyect" name="proyect"
                                    style="width:100%" required>
                                    <option value="{{ $orden->proyecto_id }}" selected>{{ $orden->Nombre }}</option>
                                </select>
                            @else
                                <select class="form-control form-control-sm" id="proyect" name="proyect"
                                    style="width:100%" required disabled>
                                    <option value="{{ $orden->proyecto_id }}" selected>{{ $orden->Nombre }}</option>
                                </select>
                            @endif

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="job_name" class="col-sm-3 col-form-label col-form-label-sm">Job Name</label>
                                    <div class="col-sm-9">
                                        @if (Auth::user()->verificarRol([1]))
                                            <input type="text" class="form-control form-control-sm" id="job_name"
                                                name="job_name" placeholder="Job Name" value="{{ $orden->job_name }}"
                                                required>
                                        @else
                                            <input type="text" class="form-control form-control-sm" id="job_name"
                                                name="job_name" placeholder="Job Name" value="{{ $orden->job_name }}"
                                                required readonly>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="sub_contractor"
                                        class="col-sm-3 col-form-label col-form-label-sm">Sub‐Contractor</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="sub_contractor"
                                            value="{{ $orden->empresa }}" name="sub_contractor"
                                            placeholder="Sub-Contractor" readonly required autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="sub_empleoye_id" class="col-sm-3 col-form-label col-form-label-sm">Name Sub
                                        C. Employee</label>
                                    <div class="col-sm-9">
                                        @if (Auth::user()->verificarRol([1]))
                                            <select name="sub_empleoye_id" id="sub_empleoye_id"
                                                class="form-control form-control-sm" style="width:100%" required>
                                                <option value="{{ $orden->sub_empleoye_id }}">
                                                    {{ $orden->sub_employe }}
                                                </option>
                                            </select>
                                        @else
                                            <select name="sub_empleoye_id" id="sub_empleoye_id"
                                                class="form-control form-control-sm" style="width:100%" required disabled>
                                                <option value="{{ $orden->sub_empleoye_id }}">
                                                    {{ $orden->sub_employe }}
                                                </option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Order
                                        Date</label>
                                    <div class="col-sm-9">
                                        @if (Auth::user()->verificarRol([1]))
                                            <input type="text" class="form-control form-control-sm datepicker"
                                                id="date_order" name="date_order" placeholder="Date of Work"
                                                value="{{ $orden->date_order }}" autocomplete="off">
                                        @else
                                            <input type="text" class="form-control form-control-sm" id="date_order"
                                                name="date_order" placeholder="Date of Work"
                                                value="{{ $orden->date_order }}" readonly autocomplete="off">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Date
                                        Schedule</label>
                                    <div class="col-sm-9">
                                        @if (Auth::user()->verificarRol([1]))
                                            <input type="text" class="form-control form-control-sm datepicker"
                                                id="date_work" name="date_work" placeholder="Date of Work"
                                                value="{{ $orden->date_work }}" autocomplete="off">
                                        @else
                                            <input type="text" class="form-control form-control-sm" id="date_work"
                                                name="date_work" placeholder="Date of Work"
                                                value="{{ $orden->date_work }}" readonly>
                                        @endif

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="created_by" class="col-sm-3 col-form-label col-form-label-sm">Name
                                        by</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="created_by"
                                            name="created_by" placeholder="Name by"
                                            value="{{ $orden->creator }}"disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover thead-light" id="table-material">
                            <thead>
                                <tr>
                                    <th scope="col" width="250px">Material</th>
                                    <th scope="col">Unity</th>
                                    <th scope="col">Quantity Ordered</th>
                                    <th scope="col">Q. to the job site</th>
                                    <th scope="col">Quantity Installed </th>
                                    <th scope="col">Date Installed</th>
                                    <th scope="col">Q.Remaining WC</th>
                                    <th scope="col">Remaining WC stored at</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($materiales as $val)
                                    <tr>
                                        <td data-label="Material:">
                                            <input type="text" name="material_id[]"
                                                class="form-control form-control-sm"
                                                value="{{ $val->Mat_ID }}" hidden {{ Auth::user()->verificarRol([1]) ? 'disabled' : '' }}>
                                            <select class="form-control form-control-sm select_material"
                                                name="material_id[]">
                                                <option value="{{ $val->Mat_ID }}" selected>{{ $val->Denominacion }}
                                                </option>
                                            </select>
                                        </td>
                                        <td data-label="Unity:">
                                            <input type="text" name="pre_unit[]"
                                                class="form-control form-control-sm pre_unit"
                                                value="{{ $val->Unidad_Medida }}" readonly>
                                        </td>
                                        <td data-label="Quantity Ordered:">
                                            <input type="number" name="q_ordered[]" step="1.0" min="0"
                                                class="form-control form-control-sm" value="{{ $val->q_ordered }}"
                                                {{ !Auth::user()->verificarRol([1]) ? 'readonly' : '' }}>
                                        </td>
                                        <td data-label="Q. to the job site:">
                                            <input type="number" name="q_job_site[]" step="1.0" min="0"
                                                class="form-control form-control-sm" value="{{ $val->q_job_site }}">
                                        </td>
                                        <td data-label="Quantity Installed:">
                                            <input type="number" name="q_installed[]" step="1.0" min="0"
                                                class="form-control form-control-sm" value="{{ $val->q_installed }}">
                                        </td>
                                        <td data-label="Date Installed:">
                                            <input type="text" name="d_installed[]"
                                                class="form-control form-control-sm datepicke"
                                                value="{{ $val->d_installed ? date('m/d/Y', strtotime($val->d_installed)) : null }}">
                                        </td>
                                        <td data-label="Q.Remaining WC:">
                                            <input type="number" name="q_remaining_wc[]" step="1.0" min="0"
                                                class="form-control form-control-sm"
                                                value="{{ $val->q_remaining_wc }}">
                                        </td>
                                        <td data-label="Remaining WC stored at:">
                                            <input type="text" name="remaining_wc_stored[]"
                                                class="form-control form-control-sm"
                                                value="{{ $val->remaining_wc_stored }}">
                                        </td>
                                        <td data-label="*">
                                            <button class="ms-btn-icon btn-danger btn-sm remove_material"  {{ !Auth::user()->verificarRol([1]) ? 'disabled' : '' }}>
                                                <i class="fas fa-trash-alt mr-0"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="none_tr_mat">
                                        <td scope="row" colspan="9" class="text-center text-bold">I don't add
                                            anything</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="p-0">
                                        <button type="button" class="btn btn-sm btn-block btn-success mt-0 añadir-material" {{ !Auth::user()->verificarRol([1]) ? 'disabled' : '' }} >Add material
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="fecha_firm_installer"
                                        class="col-sm-4 col-form-label col-form-label-sm">Date
                                        Signature</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fecha_firm_installer" id="fecha_firm_installer"
                                            class="form-control form-control-sm datepicker"
                                            value="{{ $orden->fecha_firm_installer }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="fecha_firm_foreman" class="col-sm-4 col-form-label col-form-label-sm">Date
                                        Signature</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fecha_firm_foreman" id="fecha_firm_foreman"
                                            class="form-control form-control-sm datepicker"
                                            value="{{ $orden->fecha_firm_foreman }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p>Images Startup</p>
                                    <div class="file-loading">
                                        <input id="images_inicio" name="images_inicio[]" type="file" accept="image/*"
                                            multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p>Images Final</p>
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
                                    <img class="img-thumbnail" id="img_signature_insta"
                                        src="{{ $orden->firma_installer ? asset('signatures/install/' . $orden->firma_installer) : asset('signatures/no-signature.jpg') }}">
                                    <p>Installer Signature</p>
                                    <div class="btn btn-pill btn-primary mt-0 btn-sm signature"
                                        data-title="Installer Signature" data-id_img="img_signature_insta"
                                        data-id_img_input="input_signature_insta">Add Signature</div>
                                    <input type="hidden" id="input_signature_insta" name="input_signature_insta">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <img class="img-thumbnail" id="img_signature_fore"
                                        src="{{ $orden->firma_foreman ? asset('signatures/empleoye/' . $orden->firma_foreman) : asset('signatures/no-signature.jpg') }}">
                                    <p>Foreman Signature</p>
                                    <div class="btn btn-pill btn-primary mt-0 btn-sm signature"
                                        data-title="Foreman Signature" data-id_img="img_signature_fore"
                                        data-id_img_input="input_signature_fore">Add Signature</div>
                                    <input type="hidden" id="input_signature_fore" name="input_signature_fore">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="ms-panel-footer">
                    <button class="btn btn-primary d-block" type="submit" id="enviar">Save and Continue</button>
                </div>
            </div>
        </div>
    </div>
    <x-components.firma-modal />
    <x-components.mail-modal title="order" />
@endsection
@push('javascript-form')
    <script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script>
        var n_orden = "{{ $orden->num }}";
        var admin = "{{ Auth::user()->verificarRol([1]) }}";
        $(document).ready(function() {
            get_empleoyes($("#sub_contractor").val());
            load_select_material();
            fileinput_images("{{ $id }}", 'images_inicio', 'inicio', 'orden');
            fileinput_images("{{ $id }}", 'images_final', 'final', 'orden');
        });
    </script>
    <script src="{{ asset('js/orden.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/firmas_orden.js') }}"></script>
@endpush
