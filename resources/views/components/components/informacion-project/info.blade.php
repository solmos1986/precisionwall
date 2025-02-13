<style type="text/css">
#modalCreateJobInformacion .modal-dialog.modal-dialog-scrollable.modal-xl .modal-content .modal-body #fromJobInformacion .ms-panel .ms-panel-body.p-2 .row .col-md-12 .form-group.row.mb-1 .col-sm-1.5.col-form-label.col-form-label-sm {
	text-align: right;
}
</style>
<div id="modalCreateJobInformacion" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <form id="fromJobInformacion" method="POST" enctype="multipart/form-data" action="">
                    @csrf
                    <input type="text" class="form-control form-control-sm" name="proyecto_id" id="proyecto_id"
                        hidden>
                    <input type="text" class="form-control form-control-sm" name="fecha_registro" id="fecha_registro"
                        hidden>
                    <div class="ms-panel " style="margin-bottom: 10px;">
                        <div class="ms-panel-header" style="padding: 0.5rem">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 style="font-size:14px; ">Job Information</h6>
                                </div>
                                <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                    <ul class="colores mb-0 mt-1">
                                        <li class="check-color rojo" data-color="rojo" data-proyecto_id=""> </li>
                                        <li class="check-color verde" data-color="verde" data-proyecto_id=""> <i></i>
                                        </li>
                                        <li class="check-color azul" data-color="azul" data-proyecto_id=""> <i></i>
                                        </li>
                                        <li class="check-color celeste" data-color="celeste" data-proyecto_id="">
                                            <i></i>
                                        </li>
                                        <li class="check-color amarillo" data-color="amarillo" data-proyecto_id=""> <i
                                                {{-- class="inline fa fa-times position" --}}></i> </li>
                                        <li class="check-color blanco" data-color="blanco" data-proyecto_id=""><i></i>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="ms-panel-body p-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            GC-Company:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="gc_company"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            Cod:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="job"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            Name:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="name_proyecto">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            Street:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="street"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            City:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="city"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            State:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="state"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-5 col-form-label-sm color-text">
                                            Zip Code:</strong>
                                        <p class="col-sm-7 col-form-label-sm color-text" id="zip_code"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <label for="created_by" class="col-sm-4 col-form-label col-form-label-sm">
                                            <strong>
                                                Type:
                                            </strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm change_tipo_proyecto"
                                                name="tipo_proyecto" id="tipo_proyecto">
                                                @foreach ($tipoProyecto as $tipo)
                                                    <option value="{{ $tipo->Tipo_ID }}">{{ $tipo->Nombre_Tipo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <label for="created_by" class="col-sm-4 col-form-label col-form-label-sm">
                                            <strong>
                                                Status:
                                            </strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm change_status_proyecto"
                                                name="status_proyecto" id="status_proyecto">
                                                @foreach ($statusProyecto as $status)
                                                    <option value="{{ $status->Estatus_ID }}">
                                                        {{ $status->Nombre_Estatus }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-2 col-form-label-sm color-text">
                                            GC-PM:</strong>
                                        <p class="col-sm-10 col-form-label-sm color-text" id="GC_pmr">
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-4 col-form-label-sm color-text">
                                            GC-Superintendent:</strong>
                                        <p class="col-sm-8 col-form-label-sm color-text" id="superintendet">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ms-panel-header" style="padding: 0.5rem">
                            <h6 style="font-size:14px">Contacts</h6>
                        </div>
                        <div class="ms-panel-body p-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            PM:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="pm"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-8 col-form-label-sm color-text">
                                            Field Superintendent:</strong>
                                        <p class="col-sm-4 col-form-label-sm color-text" id="field_superintendent">

                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            Foreman:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="foreman"></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            Lead:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="lead"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-0">
                                        <strong class="col-sm-3 col-form-label-sm color-text">
                                            Asistant Proyect Manager:</strong>
                                        <p class="col-sm-9 col-form-label-sm color-text" id="apm"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-header " style="padding: 0.5rem">
                            <h6 style="font-size:14px">Project Dates</h6>
                        </div>
                        <div class="spinner spinner-3 spinner_graficos" style="margin:40px auto">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                            <h5></h5>
                        </div>
                        <div class="ms-panel-body p-2" id="div_date_proyecto">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="new_date_order"
                                            class="col-sm-5 col-form-label col-form-label-sm ">Start
                                            Date:</label>
                                        <div class="col-sm-7">
                                            <input type="text"
                                                class="form-control form-control-sm datepicke color-modal"
                                                id="fecha_inicio" name="fecha_inicio" placeholder="Date of Work"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="date_work" class="col-sm-5 col-form-label col-form-label-sm">End
                                            Date:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control form-control-sm datepicke"
                                                id="fecha_fin" name="fecha_fin" placeholder="Date of Work"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="date_work" class="col-sm-7 col-form-label col-form-label-sm ">
                                            Hrs. Con:</label>
                                        <div class="col-sm-5">
                                            <input type="number" class="form-control form-control-sm" id="horas_con"
                                                name="horas_con" placeholder="Hrs. Con">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="created_by"
                                            class="col-sm-4 col-form-label col-form-label-sm">Action:</label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm change_reg" name="action"
                                                id="action">
                                                <option>Select option</option>
                                                <option value="Add">Add</option>
                                                <option value="No SDate">No SDate</option>
                                                <option value="No EDate">No EDate</option>
                                                <option value="Ini">Ini</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="date_work" class="col-sm-7 col-form-label col-form-label-sm">
                                            Total Hrs. Contract:</label>
                                        <div class="col-sm-5">
                                            <input type="number" class="form-control form-control-sm"
                                                id="total_horas" name="total_horas"
                                                placeholder="Total hours Contract:">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="created_by" class="col-sm-6 col-form-label col-form-label-sm">
                                            Days Aprox:</label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control form-control-sm"
                                                id="day_aproximado" name="day_aproximado" placeholder="Days Aprox">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="created_by" class="col-sm-6 col-form-label col-form-label-sm">
                                            Q. Employees:</label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control form-control-sm"
                                                id="num_personas" name="num_personas" placeholder="Empleoyes">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label for="created_by" class="col-sm-2 col-form-label col-form-label-sm">
                                            Note:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm" id="nota"
                                                name="nota" placeholder="Note" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success btn-sm"
                                        id="save_date_proyecto">Save</button>
                                    <button class="btn btn-sm btn-primary view_date_proyecto" data-proyecto_id=""
                                        type="button">View
                                        history</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-header " style="padding: 0.5rem">
                            <h6 style="font-size:14px">Info</h6>
                        </div>
                        <div class="ms-panel-body p-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="contact"
                                            class="col-sm-4 col-form-label col-form-label-sm">Contacts:</label>
                                        <div class="col-sm-8">
                                            <select id="contact" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="submittals" class="col-sm-4 col-form-label">Submittals:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="submittals" name="submittals" placeholder="Submittals"
                                                autocomplete="off">
                                            {{-- <select id="submittals" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="plans"
                                            class="col-sm-4 col-form-label col-form-label-sm">Plans:</label>
                                        <div class="col-sm-8">
                                            <select id="plans" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="vendor"
                                            class="col-sm-4 col-form-label col-form-label-sm">Vendor:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="vendor"
                                                name="vendor" placeholder="Vendors" autocomplete="off">
                                            {{-- <select id="vendor" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="const_schedule"
                                            class="col-sm-6 col-form-label col-form-label-sm">Const.
                                            Schedule:</label>
                                        <div class="col-sm-6">
                                            <select id="const_schedule" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="field_folder"
                                            class="col-sm-5 col-form-label col-form-label-sm">Field
                                            Folder:</label>
                                        <div class="col-sm-7">
                                            <select id="field_folder" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="brake_down"
                                            class="col-sm-5 col-form-label col-form-label-sm">Brake
                                            Down:</label>
                                        <div class="col-sm-7">
                                            <select id="brake_down" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row mb-1">
                                        <label for="badges"
                                            class="col-sm-4 col-form-label col-form-label-sm">Badges:</label>
                                        <div class="col-sm-8">
                                            {{-- <select id="badges" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                            <input type="text" class="form-control form-control-sm" id="badges"
                                                name="badges" placeholder="Badges" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="special_material"
                                            class="col-sm-1.5 col-form-label col-form-label-sm">Special Material:
                                        <br />
                                        2nd.TakeOff:<br />
Commitment of Instal:
                                        <br />
                                        Equipment: </label>
                                        <div class="col-sm-10">
                                            <textarea name="special_material" placeholder="Details of materials
 y/n
 Entered y/n
 Status" rows="4" class="form-control form-control-sm" id="special_material" >Special Materials </textarea>
                                            {{-- <select id="special_material" class="form-control form-control-sm">
                                                <option value="0" data-color="dark">
                                                    select option
                                                </option>
                                                @foreach ($statusInfo as $status)
                                                    <option value="{{ $status->id }}"
                                                        data-color="{{ $status->status_color }}">
                                                        {{ $status->nombre_status }}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success btn-sm"
                                        id="save_info">Save</button>
                                    <button class="btn btn-sm btn-primary view_info" data-proyecto_id=""
                                        type="button">View
                                        history</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-header " style="padding: 0.5rem">
                            <h6 style="font-size:14px">Actions</h6 style="font-size:14px">
                        </div>
                        <div class="ms-panel-body p-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="spinner spinner-3 spinner_graficos">
                                        <div class="rect1"></div>
                                        <div class="rect2"></div>
                                        <div class="rect3"></div>
                                        <div class="rect4"></div>
                                        <div class="rect5"></div>
                                        <h5></h5>
                                    </div>
                                    <div id="div_chart">
                                        <canvas id="myChart" style="display: block; box-sizing: border-box; "
                                            height="250vh"></canvas>
                                        <input type="text" class="form-control form-control-sm" name="imagen"
                                            id="imagen" hidden>
                                    </div>
                                </div>
                                <div class="col-md-8" style="height:300px;overflow-y: scroll;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="date_work" class="col-sm-12 col-form-label">Weekly
                                                Report:</label>
                                            <div id="view_weekly">

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="created_by"
                                                class="form-label-sm">Action for the
                                                Week:</label>
                                            <button class="btn btn-sm btn-primary mt-0" id="view_acciones"
                                                type="button">View actions</button>
                                            <div id="view_week">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success btn-sm"
                                        id="save_action">Save</button>
                                    <button class="btn btn-sm btn-primary view_action" data-proyecto_id=""
                                        type="button">View
                                        history</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-success btn-sm " id="update" type="button">Save</button> --}}
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
