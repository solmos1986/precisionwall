<div class="row">
    <div class="col-md-12">
        <div class="div-error container-fluid" id="validate" style="display: none;">
            <ul class="alert alert-danger ul-error">
            </ul>
        </div>
        {{ Breadcrumbs::render('edit empleado', $personal->Empleado_ID) }}
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>Edit Employee # {{ $personal->Empleado_ID }}</h6>
            </div>
            <div class="ms-panel-body">
                <form id="from_cardex" method="post" enctype="multipart/form-data"
                    action="{{ route('update.cardex', ['id' => $personal->Empleado_ID]) }}">
                    @method('put')
                    @csrf
                    <ul class="nav nav-tabs tabs-bordered d-flex nav-justified mb-4" role="tablist">
                        <li role="presentation"><a href="#tab1" aria-controls="tab1" class="active show"
                                role="tab" data-toggle="tab"> General information </a>
                        </li>
                        @if (Auth::user()->verificarRol([1,10]))
                            <li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab"
                                    data-toggle="tab">
                                    Events
                                </a>
                            </li>
                        @endif
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
                                    style="width:100%" readonly>
                                    <option value="{{ $personal->Emp_ID }}" selected>{{ $personal->nombre_empresa }}
                                    </option>
                                </select>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group row m-1">
                                        <label for="job_name" class="col-sm-4 col-form-label col-form-label-sm">
                                            Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="name"
                                                name="Nombre" placeholder="name" value="{{ $personal->Nombre }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_contractor" class="col-sm-4 col-form-label col-form-label-sm">
                                            Last name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="last_name"
                                                name="Apellido_Paterno" placeholder="last name"
                                                value="{{ $personal->Apellido_Paterno }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Mother's last name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="Mothers_last_name" name="Apellido_Materno"
                                                placeholder="Mothers_last_name"
                                                value="{{ $personal->Apellido_Materno }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Birth date</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="Fecha_Nacimiento" id="Birth_date"
                                                value="{{ $personal->Fecha_Nacimiento }}"
                                                class="form-control form-control-sm datepicker" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" placeholder="example@com" name="email"
                                                id="email" value="{{ $personal->email }}"
                                                class="form-control form-control-sm" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id" class="col-sm-4 col-form-label col-form-label-sm">
                                            Cell phone</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Cell_phone"
                                                name="Celular" placeholder="Cell phone"
                                                value="{{ $personal->Celular }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Nickname</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Nickname"
                                                name="Nick_Name" placeholder="Nickname"
                                                value="{{ $personal->Nick_Name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Telephone</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Telephone"
                                                name="Telephone" placeholder="Telefono"
                                                value="{{ $personal->Telefono }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
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
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            City</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="City"
                                                name="Ciudad" placeholder="City" value="{{ $personal->Ciudad }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Postal Code</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="Postal_Code" name="Zip_Code" placeholder="Postal Code"
                                                value="{{ $personal->Zip_Code }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Street</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Street"
                                                name="Calle" placeholder="Street" value="{{ $personal->Calle }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="Number"
                                                name="Numero" placeholder="Number"
                                                value="{{ $personal->Numero }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-4 col-form-label col-form-label-sm">
                                            Position</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" name="Cargo"
                                                placeholder="Position" value="{{ $personal->Cargo }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Social Security Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Seguro_Social" placeholder="social security number"
                                                value="{{ $personal->Numero_Seguro_Social }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Driver's License Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Licencia_Conducir" placeholder="drivers license number"
                                                value="{{ $personal->Numero_Licencia_Conducir }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Work Permit Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Permiso_Trabajo" placeholder="work permit number"
                                                value="{{ $personal->Numero_Permiso_Trabajo }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Resident Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Numero_Residente" placeholder="resident number"
                                                value="{{ $personal->Numero_Residente }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Work Expiration Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Fecha_Expiracion_Trabajo" placeholder="work expiration date"
                                                value="{{ $personal->Fecha_Expiracion_Trabajo }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Production Index</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Indice_produccion" placeholder="production index"
                                                value="{{ $personal->Indice_produccion }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Bonus number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Nro_Bono" placeholder="bonus number"
                                                value="{{ $personal->Nro_Bono }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Spec_Bon1</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Spec_Bon1" placeholder="Spec_Bon1"
                                                value="{{ $personal->Spec_Bon1 }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Not_Bon</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="Not_Bon"
                                                placeholder="Not_Bon" value="{{ $personal->Not_Bon }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Extra_Mon1</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Extra_Mon1" placeholder="Extra_Mon1"
                                                value="{{ $personal->Extra_Mon1 }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Benefit1</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Benefit1" placeholder="Benefit1"
                                                value="{{ $personal->Benefit1 }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row m-1">
                                        <label for="sub_empleoye_id"
                                            class="col-sm-3 col-form-label col-form-label-sm">
                                            Benefit2</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="Benefit2" placeholder="Benefit2"
                                                value="{{ $personal->Benefit2 }}" readonly>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!--section events-->
                        <div role="tabpanel" class="tab-pane fade" id="tab2">
                            <div class="row">
                                <div class="col-md-12 pb-3">
                                    <a class="btn btn-pill btn-primary btn-sm" href="#" id="crear_evento">
                                        Add new event to employee</a>
                                </div>
                            </div>
                            <div class="accordion has-gap ms-accordion-chevron" id="accordionExample4">
                                @forelse ($movimiento_evento as $val)
                                    <div class="card">
                                        <div class="card-header" data-toggle="collapse" role="button"
                                            data-target="#collapse{{ $loop->index }}" aria-expanded="false"
                                            aria-controls="collapse{{ $loop->index }}">
                                            <span class="has-icon"> <i class="flaticon-sticky-note"></i>
                                                {{ $val->nombre_tipo }} |
                                                {{ $val->nombre }}</span>
                                        </div>
                                        <div id="collapse{{ $loop->index }}" class="collapse"
                                            data-parent="#accordionExample4">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-4 col-form-label col-form-label-sm">
                                                                Event name:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="name_evento" value="{{ $val->nombre }}"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-4 col-form-label col-form-label-sm">
                                                                Description:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="descripcion_evento"
                                                                    value="{{ $val->descripcion }}" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-4 col-form-label col-form-label-sm">
                                                                Note:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="note_evento"
                                                                    value="{{ $val->nota_movimiento }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-4 col-form-label col-form-label-sm">
                                                                Duracion day:</label>
                                                            <div class="col-sm-8">
                                                                <input type="number"
                                                                    class="form-control form-control-sm"
                                                                    name="duracion_evento"
                                                                    value="{{ $val->duracion_day }}" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="generate"
                                                                class="col-sm-4 col-form-label col-form-label-sm">Start
                                                                date:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text"
                                                                    class="form-control form-control-sm datepicker"
                                                                    name="fecha_inicio"
                                                                    value="{{ $val->start_date }}" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="generate"
                                                                class="col-sm-4 col-form-label col-form-label-sm">Expiration
                                                                date:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text"
                                                                    class="form-control form-control-sm datepicker"
                                                                    name="fecha_fin" value="{{ $val->exp_date }}"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-3 col-form-label col-form-label-sm">
                                                                Update this information?:</label>
                                                            <div class="col-sm-9">
                                                                @if ($val->access_pers == 'y')
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="name_evento" value="yes" disabled>
                                                                @else
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="name_evento" value="no" disabled>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-4 col-form-label col-form-label-sm">
                                                                Activate alert | Days of anticipation:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="name_evento"
                                                                    value="{{ $val->report_alert }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <!--div class="col-md-12">
                                                        <div class="form-group row">
                                                            <label for="job_name"
                                                                class="col-sm-2 col-form-label col-form-label-sm">
                                                                Access to register:</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control form-control-sm"
                                                                    id="name" name="name_evento" placeholder="name"
                                                                    value="{{ $val->access_code }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div-->
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if ($val->doc_pdf)
                                                            <a type="button"
                                                                href="{{ url('/docs') }}/{{ $val->doc_pdf }}"
                                                                target="_blank"
                                                                class="btn btn-primary btn-sm m-1 float-left has-icon"><i
                                                                    class="flaticon-pdf"></i> Download pdf</a>
                                                        @endif
                                                        <a class="btn btn-pill btn-danger btn-sm m-1 float-right delete_evento"
                                                            data-id="{{ $val->movimientos_eventos }}" href="#">
                                                            Delete</a>
                                                        <a class="btn btn-pill btn-success btn-sm m-1 float-right edit_evento"
                                                            data-id="{{ $val->movimientos_eventos }}" href="#">
                                                            Edit</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p>There is no registered information.</p>
                                @endforelse
                            </div>
                        </div>

                        <!--section reports-->
                        <div role="tabpanel" class="tab-pane fade" id="tab3">
                            <div id="contenedor" class="row justify-content-md-center">
                                <div class="col-md-10">
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
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @foreach ($movimiento_evento as $val)
                                <div id="contenedor" class="row justify-content-md-center">
                                    <div class="col-md-10">
                                        <div id="titulo" class="ms-panel">
                                            <div class="ms-panel-header ms-panel-custome">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span
                                                            class="badge badge-success">{{ $val->nombre_tipo }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Event name: </strong>{{ $val->nombre }}</p>
                                                        <p><strong>Description: </strong>{{ $val->description }}</p>
                                                        <p><strong>Note: </strong>{{ $val->note }}</p>
                                                        @if ($val->doc_pdf != '')
                                                            <p><strong>Document: </strong><a style=""
                                                                    href="{{ url('/public/docs') }}/{{ $val->doc_pdf }}"><u
                                                                        class="text-primary">Download</u></a></p>
                                                        @endif

                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Duration day: </strong>{{ $val->duracion_day }}
                                                        </p>
                                                        <p><strong>Start date: </strong>{{ $val->start_date }}</p>
                                                        <p><strong>Expiration date: </strong>{{ $val->exp_date }}</p>

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
            @if (Auth::user()->verificarRol([1,10] ))
                <div class="ms-panel-footer">
                    <button class="btn btn-primary" type="submit" id="enviar">Save and Continue</button>
                    <a href="{{ route('list.cardex') }}" class="btn btn-danger" style="color: white"
                        type="submit">Cancel</a>
                </div>
            @endif
        </div>
    </div>
</div>
<x-components.movimiento.edit-movimiento-evento :personal="$personal" />
<x-components.movimiento.new-movimiento-evento :personal="$personal" />
<x-components.delete-modal />
