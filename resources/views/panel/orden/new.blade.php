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
            {{Breadcrumbs::render('new order')}}
            <div class="ms-panel">

                <div class="ms-panel-header ms-panel-custome">
                    <h6>Order #{{ $n_orden }}</h6>
                </div>
                <div class="ms-panel-body">
                    <form id="from_order" method="post" enctype="multipart/form-data" action="{{ route('store.orden') }}">
                        @csrf
                        <input type="hidden" name="orden_id" value="{{ $orden }}">
                        <input type="hidden" name="is_mail" class="is_mail">
                        <input type="hidden" name="to" class="to">
                        <input type="hidden" name="cc" class="cc">
                        <input type="hidden" name="title_m" class="title_m">
                        <input type="hidden" name="body_m" class="body_m">
                        <div class="form-group">
                            <label for="generate">assign to project:</label>
                            <select class="form-control form-control-sm" id="proyect" name="proyect" style="width:100%"
                                required></select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="job_name" class="col-sm-3 col-form-label col-form-label-sm">Job Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="job_name"
                                            name="job_name" placeholder="Job Name" disabled="disabled" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="sub_contractor"
                                        class="col-sm-3 col-form-label col-form-label-sm">Sub-Contractor</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="sub_contractor"
                                            name="sub_contractor" placeholder="Sub-Contractor" readonly required autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="sub_empleoye_id" class="col-sm-3 col-form-label col-form-label-sm w-100">Name Sub
                                        C. Employee</label>
                                    <div class="col-sm-9">
                                        <select name="sub_empleoye_id" id="sub_empleoye_id"
                                            class="form-control form-control-sm" disabled="disabled" required></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Order
                                        Date</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_order"
                                            name="date_order" placeholder="Date of Work" value="{{ date('m/d/Y') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Date
                                        Schedule</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_work"
                                            name="date_work" placeholder="Date of Work" value="{{ date('m/d/Y') }}" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="created_by" class="col-sm-3 col-form-label col-form-label-sm">Name
                                        by</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-sm" id="created_by"
                                            name="created_by" placeholder="Name by"
                                            value="{{ auth()->user()->Nombre }} {{ auth()->user()->Apellido_Paterno }} {{ auth()->user()->Apellido_Materno }}"
                                            disabled="disabled" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-sm table-hover thead-light" id="table-material">
                            <thead>
                                <tr>
                                    <th scope="col" width="250">Material</th>
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
                                <tr id="none_tr_mat">
                                    <td scope="row" colspan="9" class="text-center text-bold">I don't add anything</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="p-0">
                                        <button class="btn btn-sm btn-block btn-success mt-0 añadir-material" type="button"
                                            disabled >Add material</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="fecha_firm_installer" class="col-sm-4 col-form-label col-form-label-sm">Date
                                        Signature</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fecha_firm_installer" id="fecha_firm_installer"
                                            class="form-control form-control-sm datepicker" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="fecha_firm_foreman" class="col-sm-4 col-form-label col-form-label-sm">Date
                                        Signature</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="fecha_firm_foreman" id="fecha_firm_foreman"
                                            class="form-control form-control-sm datepicker" autocomplete="off">
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
                                    <img class="img-thumbnail" id="img_signature_insta">
                                    <p>Installer Signature</p>
                                    <div class="btn btn-pill btn-success mt-0 btn-sm signature"
                                        data-title="Installer Signature" data-id_img="img_signature_insta"
                                        data-id_img_input="input_signature_insta">Add Signature</div>
                                    <input type="hidden" id="input_signature_insta" name="input_signature_insta">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <img class="img-thumbnail" id="img_signature_fore">
                                    <p>Foreman Signature</p>
                                    <div class="btn btn-pill btn-success mt-0 btn-sm signature"
                                        data-title="Foreman Signature" data-id_img="img_signature_fore"
                                        data-id_img_input="input_signature_fore">Add Signature</div>
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
        var n_orden = "{{ $n_orden }}";
        var admin = "{{ Auth::user()->verificarRol([1]) }}";

        $(document).ready(function() {
            fileinput_images("{{ $orden }}", 'images_inicio', 'inicio', 'orden');
            fileinput_images("{{ $orden }}", 'images_final', 'final', 'orden');
        });

    </script>
    <script src="{{ asset('js/orden.js') }}"></script>
    <script src="{{ asset('js/upload_image.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>
    <script src="{{ asset('js/taginput_custom.js') }}"></script>
    <script src="{{ asset('js/firmas_orden.js') }}"></script>
@endpush
