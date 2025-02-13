<div id="formNewModalEvent" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="new_event_form" action="{{ route('new.all.cardex') }}" method="POST">
                    @csrf
                    <input type="text" class="form-control form-control-sm datepicker" id="new_Empleado_ID"
                        name="new_Empleado_ID" value="{{ $personal->Empleado_ID }}" hidden>
                    <p class="ms-directions mb-0">EMPLOYEE INFORMATION</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="nombre_empleado" class="col-sm-4 col-form-label-sm">Employe name:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicker" name="name"
                                        disabled id="nombre_empleado" style="width:100%"
                                        value="{{ $personal->Nombre }} {{ $personal->Apellido_Paterno }} {{ $personal->Apellido_Materno }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row mb-1">
                                <label for="numero" class="col-sm-4 col-form-label-sm">Number:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicker" name="numero"
                                        disabled id="numero" style="width:100%"
                                        value="{{ $personal->Numero }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row mb-1">
                                <label for="nickname" class="col-sm-4 col-form-label-sm">Nickname:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicker" name="nickname"
                                        disabled id="nickname" style="width:100%"
                                        value="{{ $personal->Nick_Name }} "
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="ms-directions mb-0">EVENT INFORMATION</p>
                    <div class="row">
                        <div class="col-md-6 mb-0"">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Select event:</label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control-sm" id="new_event"
                                        name="new_event"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-0"">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Start date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicke"
                                        id="new_fecha_inicio" name="new_fecha_inicio" style="width:100%" required
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-0"">
                            <div class="form-group row">
                                <label for="new_tipo_evento" class="col-sm-4 col-form-label col-form-label-sm">
                                    Tipe event:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="new_tipo_evento" name="new_tipo_evento" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-0"">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Expiration date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicker"
                                        id="new_fecha_fin" name="new_fecha_fin" style="width:100%" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-0"">
                            <div class="form-group mb-1">
                                <label>Note: </label>
                                <textarea name="new_note" id="new_note" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-0">
                            <div class="form-group row">
                                <label for="job_name" class="col-sm-4 col-form-label col-form-label-sm">
                                    Duracion day:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control form-control-sm" id="new_duracion_evento"
                                        name="new_duracion_evento" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-0">
                            <div class="form-group row">
                                <label for="job_name" class="col-sm-6 col-form-label col-form-label-sm">
                                    Activate alert | Days of anticipation:</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" id="new_report_alert"
                                        name="new_report_alert" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="ms-directions text-center mb-1">EVENT FILES</p>
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <div class="text-center">
                                <div class="file-loading">
                                    <input id="new_input_images" name="input_images[]" type="file" multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_movimiento_button">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
