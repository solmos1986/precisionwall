<div class="row">
    <div class="col-md-12">
        <div class="div-error container-fluid" id="validate" style="display: none;">
            <ul class="alert alert-danger ul-error">
            </ul>
        </div>
        {{ Breadcrumbs::render('edit empleado', $personal->Empleado_ID) }}
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>Edit employee #{{ $personal->Numero }}</h6>
            </div>
            <div class="ms-panel-body">
                <form id="from_cardex" method="POST" enctype="multipart/form-data"
                    action="{{ route('update.cardex', ['id' => $personal->Empleado_ID]) }}">
                    @method('put')
                    @csrf
                    <input type="text" id="Empleado_ID" value="{{ $personal->Empleado_ID }}" hidden>
                    <ul class="nav nav-tabs tabs-bordered d-flex nav-justified mb-4" role="tablist">
                        <li role="presentation"><a href="#tab1" aria-controls="tab1" class="active show"
                                role="tab" data-toggle="tab"> General information </a>
                        </li>
                        <li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">
                                Events
                            </a>
                        </li>
                        <li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">
                                View
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active show fade in" id="tab1">
                            <div class="form-group">
                                <label for="generate">select company:</label>
                                <select class="form-control form-control-sm" id="company" name="Emp_ID"
                                    style="width:100%" required>
                                    <option value="{{ $personal->Emp_ID }}" selected>{{ $personal->nombre_empresa }}
                                    </option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Number"
                                                name="Numero" placeholder="Number" value="{{ $personal->Numero }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="job_name" class="col-sm-4 col-form-label col-form-label-sm">
                                            Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="name"
                                                name="Nombre" placeholder="name" value="{{ $personal->Nombre }}"
                                                required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_contractor" class="col-sm-4 col-form-label col-form-label-sm">
                                            Last name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="last_name"
                                                name="Apellido_Paterno" placeholder="last name"
                                                value="{{ $personal->Apellido_Paterno }}" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Mother's last name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="Mothers_last_name" name="Apellido_Materno"
                                                placeholder="Mothers_last_name"
                                                value="{{ $personal->Apellido_Materno }}" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Birth date</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="Fecha_Nacimiento" id="Birth_date"
                                                value="{{ $personal->Fecha_Nacimiento }}"
                                                class="form-control form-control-sm datepicke" required
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" placeholder="example@com" name="email"
                                                id="email" value="{{ $personal->email }}"
                                                class="form-control form-control-sm" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Cell phone</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="Cell_phone" name="Celular" placeholder="Cell phone"
                                                value="{{ $personal->Celular }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Nickname</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Nickname"
                                                name="Nick_Name" placeholder="Nickname"
                                                value="{{ $personal->Nick_Name }}" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="usuario" class="col-sm-4 col-form-label col-form-label-sm">
                                            <strong>User</strong></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="usuario"
                                                name="Usuario" placeholder="User" value="{{ $personal->Usuario }}"
                                                required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="password" class="col-sm-4 col-form-label col-form-label-sm">
                                            <strong>Password</strong></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="password"
                                                name="Password" placeholder="Password"
                                                value="{{ $personal->Password }}" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Telephone</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Telephone"
                                                name="Telefono" placeholder="Telefono"
                                                value="{{ $personal->Telefono }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Civil status</label>
                                        <div class="col-sm-8">
                                            <select name="Estado" id="Civil_status"
                                                class="form-control form-control-sm"" <option
                                                value="
                                                single">single
                                                </option>
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
                                                name="Ciudad" placeholder="City" value="{{ $personal->Ciudad }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Postal Code</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="Postal_Code" name="Zip_Code" placeholder="Postal Code"
                                                value="{{ $personal->Zip_Code }}" autocomplete="off">
                                        </div>

                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Street</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Street"
                                                name="Calle" placeholder="Street" value="{{ $personal->Calle }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Position</label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" id="cargo_personal_id"
                                                name="cargo_personal_id" style="width:100%" required>
                                                @foreach ($cargos as $cargo)
                                                    <option value="{{ $cargo->id }}"
                                                        {{ $cargo->id == $personal->cargo_personal_id ? 'selected' : '' }}>
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
                                                        value="{{ $personal->P1 }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-5 col-form-label col-form-label-sm">
                                                    Question 2: </label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="Number" name="P2" placeholder="Question 2"
                                                        value="{{ $personal->P2 }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-5 col-form-label col-form-label-sm">
                                                    Question 3: </label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="P3" placeholder="Question 3"
                                                        value="{{ $personal->P3 }}" autocomplete="off">
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
                                                        value="{{ $personal->R1 }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Answer 2:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="Number" name="R2" placeholder="Answer 2"
                                                        value="{{ $personal->R2 }}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="sub_empleoye_id"
                                                    class="col-sm-4 col-form-label col-form-label-sm">
                                                    Answer 3:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="R3" placeholder="Answer 3"
                                                        value="{{ $personal->R3 }}" autocomplete="off">
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
                                                value="{{ $personal->Numero_Seguro_Social }}" required
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-5 col-form-label col-form-label-sm">
                                            Driver's License Number</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Licencia_Conducir" placeholder="drivers license number"
                                                value="{{ $personal->Numero_Licencia_Conducir }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-5 col-form-label col-form-label-sm">
                                            Work Permit Number</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Permiso_Trabajo" placeholder="work permit number"
                                                value="{{ $personal->Numero_Permiso_Trabajo }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-5 col-form-label col-form-label-sm">
                                            Resident Number</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Residente" placeholder="resident number"
                                                value="{{ $personal->Numero_Residente }}" autocomplete="off">
                                        </div>
                                    </div>
                                    @if (Auth::user()->verificarRol([1, 9, 10]))
                                        <div class="form-group row mb-1">
                                            <label for="Fecha_Contratacion"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Contract date</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm datepicke"
                                                    id="Fecha_Contratacion" name="Fecha_Contratacion"
                                                    placeholder="Contract date"
                                                    value="{{ $personal->Fecha_Contratacion }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Hire Date</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm datepicke"
                                                    name="Fecha_Expiracion_Trabajo" placeholder="Hire Date"
                                                    value="{{ $personal->Fecha_Expiracion_Trabajo }}"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Production Index</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Indice_produccion" placeholder="production index"
                                                    value="{{ $personal->Indice_produccion }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Bonus number</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Nro_Bono" placeholder="bonus number"
                                                    value="{{ $personal->Nro_Bono }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Note for Bonus</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Not_Bon" placeholder="Not_Bon"
                                                    value="{{ $personal->Not_Bon }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Spec_Bon1</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Spec_Bon1" placeholder="Spec_Bon1"
                                                    value="{{ $personal->Spec_Bon1 }}" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Extra_Mon1</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Extra_Mon1" placeholder="Extra_Mon1"
                                                    value="{{ $personal->Extra_Mon1 }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Benefit A</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Benefit1" placeholder="Benefit A"
                                                    value="{{ $personal->Benefit1 }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="Extra_Mon2" class="col-sm-5 col-form-label col-form-label-sm">
                                                Extra_Mon2</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Extra_Mon2" placeholder="Extra_Mon2" id="Extra_Mon2"
                                                    value="{{ $personal->Extra_Mon2 }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="sub_empleoye_id"
                                                class="col-sm-5 col-form-label col-form-label-sm">
                                                Benefit B</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="Benefit2" placeholder="Benefit B"
                                                    value="{{ $personal->Benefit2 }}" autocomplete="off">
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
                                                placeholder="Aux 1" value="{{ $personal->Aux1 }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-2 col-form-label col-form-label-sm">
                                            Aux 2</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm" name="Aux2"
                                                placeholder=" Aux 2" value="{{ $personal->Aux2 }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-2 col-form-label col-form-label-sm">
                                            Aux 3</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm" name="Aux3"
                                                placeholder="Aux 3" value="{{ $personal->Aux3 }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-2 col-form-label col-form-label-sm">
                                            Aux 4</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm" name="Aux4"
                                                placeholder="Aux 4" value="{{ $personal->Aux4 }}"
                                                autocomplete="off">
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
                                                    <option value="{{ $tipo_usuario->id }}"
                                                        {{ $tipo_usuario->id == $personal->tipo_personal_id ? 'selected' : '' }}>
                                                        {{ $tipo_usuario->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!--section events-->
                        <div role="tabpanel" class="tab-pane fade" id="tab2">
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-between">
                                    <p></p>
                                    <a class="btn btn-primary btn-sm mt-0" href="#" id="crear_evento">
                                        Create event</a>
                                </div>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table id="list_personal" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            <th>Type event</th>
                                            <th>Event name</th>
                                            <th>Note</th>
                                            <th>Start date</th>
                                            <th>Exp date</th>
                                            <th>Duracion day</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>

                        <!--section reports-->
                        <div role="tabpanel" class="tab-pane fade" id="tab3">
                            <div id="contenedor" class="row justify-content-md-center">
                                <div class="col-md-12">
                                    <div id="titulo" class="ms-panel">
                                        <div class="ms-panel-header ms-panel-custome">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p><strong>NAME OF EMPLOYEE: </strong>{{ $personal->Nombre }}</p>
                                                    <p><strong>LASTNAME:
                                                        </strong>{{ $personal->Apellido_Paterno }}&nbsp;{{ $personal->Apellido_Materno }}
                                                    </p>
                                                    <p><strong>EMAIL: </strong>{{ $personal->email }}</p>
                                                    <p><strong>CELL PHONE: </strong>{{ $personal->Celular }}</p>
                                                    <p><strong>POSTION: </strong>{{ $personal->cargo_personal_nombre }}
                                                    </p>
                                                    <p><strong>TYPE OF EMPLOYEE:
                                                        </strong>{{ $personal->tipo_personal_nombre }}</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @foreach ($movimiento_evento as $val)
                                <div id="contenedor" class="row justify-content-md-center">
                                    <div class="col-md-12">
                                        <div id="titulo" class="ms-panel">
                                            <div class="ms-panel-header ms-panel">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span
                                                            class="badge badge-success">{{ $val->nombre_tipo }}</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <p><strong>Event name: </strong>{{ $val->nombre }}</p>
                                                                <p><strong>Description:
                                                                    </strong>{{ $val->description }}</p>
                                                                <p><strong>Note: </strong>{{ $val->note }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <p><strong>Duration day:
                                                                    </strong>{{ $val->duracion_day }}
                                                                </p>
                                                                <p><strong>Start date: </strong>{{ $val->start_date }}
                                                                </p>
                                                                <p><strong>Expiration date:
                                                                    </strong>{{ $val->exp_date }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    @foreach ($val->docs as $doc)
                                                                        <div class="col-md-3">
                                                                            @if ($doc->ext == 'jpg' || $doc->ext == 'png')
                                                                                <a target="_blank"
                                                                                    rel="noopener noreferrer"
                                                                                    href="{{ url('/') }}/docs/{{ $doc->imagen }}">
                                                                                    <img class="img-thumbnail"
                                                                                        title="download"
                                                                                        src="{{ url('/') }}/docs/{{ $doc->imagen }}"
                                                                                        alt="...">
                                                                                </a>
                                                                            @else
                                                                                <div class="img-thumbnail">
                                                                                    <a href="{{ url('/') }}/docs/{{ $doc->imagen }}"
                                                                                        target="_blank"
                                                                                        rel="noopener noreferrer">
                                                                                        <img class="img-thumbnail"
                                                                                            title="download"
                                                                                            src="{{ url('/') }}/docs/document.png"
                                                                                            alt="...">
                                                                                    </a>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                </form>
            </div>
            <div class="ms-panel-footer m-0">
                <button class="btn btn-success" type="submit" id="enviar">Save and Continue</button>
                <a href="{{ route('list.cardex') }}" class="btn btn-danger" style="color: white"
                    type="submit">Cancel</a>
            </div>
        </div>
    </div>
</div>
<x-components.movimiento.edit-movimiento-evento :personal="$personal" />
<x-components.movimiento.new-movimiento-evento :personal="$personal" />
<x-components.delete-modal />
