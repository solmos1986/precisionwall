@extends('layouts.panel')
@push('css-header')
    <!-- Tokenfield CSS -->
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    @section('content')
        <div class="row">
            <div class="col-md-12">
                <div class="div-error container-fluid" id="validate" style="display: none;">
                    <ul class="alert alert-danger ul-error">
                    </ul>
                </div>
                {{ Breadcrumbs::render('nuevo empleado') }}
                <div class="ms-panel">
                    <form id="from_cardex" method="POST" enctype="multipart/form-data" action="{{ route('new.cardex') }}">
                        @csrf
                        <div class="ms-panel-header ms-panel-custome">
                            <h6>CREATE EMPLOYEE</h6>
                        </div>
                        <div class="ms-panel-body">
                            <ul class="nav nav-tabs tabs-bordered d-flex nav-justified mb-4" role="tablist">
                                <li role="presentation"><a href="#tab1" aria-controls="tab1" class="active show"
                                        role="tab" data-toggle="tab"> General information </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active show fade in" id="tab1">

                                    <div class="form-group">
                                        <label for="generate">select company:</label>
                                        <select class="form-control form-control-sm" id="company" name="Emp_ID"
                                            style="width:100%" required></select>
                                    </div>
                                    <p class="ms-directions">EMPLOYEE INFORMATION:</p>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group row mb-1">
                                                <label for="Number" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="Number"
                                                        name="Numero" placeholder="Number or E or n/a" value="{{$numero}}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="job_name" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Name:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="name"
                                                        name="Nombre" placeholder="name" value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_contractor" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Last name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="last_name"
                                                        name="Apellido_Paterno" placeholder="last name" value="" required
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Mother's last name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="Mothers_last_name" name="Apellido_Materno"
                                                        placeholder="Mothers_last_name" value="" required
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Birth date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="Fecha_Nacimiento" id="Birth_date"
                                                        class="form-control form-control-sm datepicke" required
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Email</label>
                                                <div class="col-sm-8">
                                                    <input type="email" placeholder="example@com" name="email"
                                                        id="email" class="form-control form-control-sm"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Cell phone</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="Cell_phone" name="Celular" placeholder="Cell phone"
                                                        value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Nickname</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="Nickname"
                                                        name="Nick_Name" placeholder="Nickname" value="" required
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="usuario"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    <strong>User</strong></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="usuario"
                                                        name="Usuario" placeholder="User"
                                                        value="" required autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="password"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    <strong>Password</strong></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="password"
                                                        name="Password" placeholder="Password"
                                                        value="" required autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Telephone</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="Telephone"
                                                        name="Telefono" placeholder="Telefono" value=""
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Civil status</label>
                                                <div class="col-sm-8">
                                                    <select name="Estado" id="Civil_status"
                                                        class="form-control form-control-sm"" required>
                                                        <option value=" single">single</option>
                                                        <option value="married">married</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    City</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="City"
                                                        name="Ciudad" placeholder="City" value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Postal Code</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="Postal_Code" name="Zip_Code" placeholder="Postal Code"
                                                        value="">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Street</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="Street"
                                                        name="Calle" placeholder="Street" value=""
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="Cargos" class="col-sm-4 col-form-label col-form-label-sm">
                                                    Position</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control form-control-sm" id="cargo_personal_id"
                                                        name="cargo_personal_id" style="width:100%" required>
                                                        @foreach ($cargos as $cargo)
                                                            <option value="{{ $cargo->id }}">
                                                                {{ $cargo->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mb-1">
                                                        <label for="sub_empleoye_id"
                                                            class="col-sm-5 col-form-label col-form-label-sm">
                                                            Question 1: </label>
                                                        <div class="col-sm-7">
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="Street" name="P1" placeholder="Question 1"
                                                                value="Type twice 1:" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mb-1">
                                                        <label for="sub_empleoye_id"
                                                            class="col-sm-5 col-form-label col-form-label-sm">
                                                            Question 2: </label>
                                                        <div class="col-sm-7">
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="Number" name="P2" placeholder="Question 2"
                                                                value="Type twice 2:" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mb-1">
                                                        <label for="sub_empleoye_id"
                                                            class="col-sm-5 col-form-label col-form-label-sm">
                                                            Question 3: </label>
                                                        <div class="col-sm-7">
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="P3" placeholder="Question 3" value="Type twice 3:"
                                                                autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row mb-1">
                                                        <label for="sub_empleoye_id"
                                                            class="col-sm-4 col-form-label col-form-label-sm">
                                                            Answer 1:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="Street" name="R1" placeholder="Answer 1"
                                                                value="11" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mb-1">
                                                        <label for="sub_empleoye_id"
                                                            class="col-sm-4 col-form-label col-form-label-sm">
                                                            Answer 2:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="Number" name="R2" placeholder="Answer 2"
                                                                value="22" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mb-1">
                                                        <label for="sub_empleoye_id"
                                                            class="col-sm-4 col-form-label col-form-label-sm">
                                                            Answer 3:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="R3" placeholder="Answer 3" value="33"
                                                                autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-5 col-form-label col-form-label-sm">
                                                    Social Security Number</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="Numero_Seguro_Social" placeholder="social security number"
                                                        value="" required autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-5 col-form-label col-form-label-sm">
                                                    Driver's License Number</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="Numero_Licencia_Conducir" placeholder="drivers license number"
                                                        value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-5 col-form-label col-form-label-sm">
                                                    Work Permit Number</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="Numero_Permiso_Trabajo" placeholder="work permit number"
                                                        value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-5 col-form-label col-form-label-sm">
                                                    Resident Number</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="Numero_Residente" placeholder="resident number" value=""
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            @if (Auth::user()->verificarRol([1,9,10]))
                                                <div class="form-group row mb-1">
                                                    <label for="Fecha_Contratacion"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Contract date</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm datepicke"
                                                            id="Fecha_Contratacion" name="Fecha_Contratacion"
                                                            placeholder="Contract date" value="" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Hire Date</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm datepicke"
                                                            name="Fecha_Expiracion_Trabajo" placeholder="Hire Date"
                                                            value="" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Production Index</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Indice_produccion" placeholder="production index"
                                                            value="" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Bonus number</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Nro_Bono" placeholder="bonus number" value=""
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Note for Bonus</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Not_Bon" placeholder="Not_Bon" value=""
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Spec_Bon1</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Spec_Bon1" placeholder="Spec_Bon1" value=""
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                               
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Extra_Mon1</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Extra_Mon1" placeholder="Extra_Mon1" value=""
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Benefit A</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Benefit1" placeholder="Benefit A" value=""
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="Extra_Mon2"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Extra_Mon2</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Extra_Mon2" placeholder="Extra_Mon2" id="Extra_Mon2"
                                                            value="" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-1">
                                                    <label for="sub_empleoye_id"
                                                        class="col-sm-5 col-form-label col-form-label-sm">
                                                        Benefit B</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="Benefit2" placeholder="Benefit B" value=""
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                            @endif
                                            <hr>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-2 col-form-label col-form-label-sm">
                                                    Aux 1</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" name="Aux1"
                                                        placeholder="Aux 1" value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-2 col-form-label col-form-label-sm">
                                                    Aux 2</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" name="Aux2"
                                                        placeholder=" Aux 2" value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-2 col-form-label col-form-label-sm">
                                                    Aux 3</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" name="Aux3"
                                                        placeholder="Aux 3" value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-2 col-form-label col-form-label-sm">
                                                    Aux 4</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" name="Aux4"
                                                        placeholder="Aux 4" value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <p class="m-2">
                                                <code>FY=Office.FX=Field Related F=Field worker FS=Field Sub z.Adm=no longer ->:
                                                </code>
                                            </p>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-3 col-form-label col-form-label-sm">
                                                    Type of employee</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-sm" id="tipo_personal_id"
                                                        name="tipo_personal_id" style="width:100%" required>
                                                        @foreach ($tipos_usuarios as $tipo_usuario)
                                                            <option value="{{ $tipo_usuario->id }}">
                                                                {{ $tipo_usuario->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ms-panel-footer m-0">
                            <button class="btn btn-success" type="submit" id="enviar">Save and Continue</button>
                            <a href="{{ route('list.cardex') }}" class="btn btn-danger" style="color: white"
                                type="submit">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
    @push('javascript-form')
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('js/datepicker.js') }}"></script>

        <script>
            $("#enviar").click(function(e) {
                e.preventDefault();
                send_form();
            });

            function send_form() {
                let $form = $('#from_cardex');
                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function(data) {
                        if (data.errors.length > 0) {
                            $alert = "complete the following fields to continue:\n";
                            data.errors.forEach(function(error) {
                                $alert += `* ${error}\n`;
                            });
                            alert($alert);

                        } else {
                            $form.submit();
                        }
                    }
                });
            }
            $('#tipo_personal_id').select2();
            $('#cargo_personal_id').select2();
        </script>
        <script src="{{ asset('js/cardex.js') }}"></script>
    @endpush
