<div id="formModalEvent" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="event_form" action="{{ route('new.all.cardex') }}" method="POST">
                    @csrf
                    <p class="ms-directions mb-0">SELECT EMPLOYEES</p>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="generate">Company:</label>
                                <select class="form-control-sm" id="company" style="100%" multiple="multiple"
                                    required>
                                    @foreach ($company as $value)
                                        <option value="{{ $value->Emp_ID }}">
                                            {{ $value->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="generate">Position:</label>
                                <select class="form-control-sm" id="cargo" style="100%" multiple="multiple"
                                    required>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}">
                                            {{ $cargo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="generate">Employee events:</label>
                                <select class="form-control-sm" id="evento" style="100%" name="evento[]"
                                    multiple="multiple" required>
                                    @foreach ($eventos as $evento)
                                        <option value="{{ $evento->cod_evento }}">
                                            {{ $evento->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="generate"><strong>Employees:</strong></label>
                                <select class="form-control-sm" id="personal" style="100%" name="personal[]"
                                    multiple="multiple" required>

                                </select>
                            </div>
                        </div>
                    </div>
                    <p class="ms-directions mb-0">EVENT INFORMATION</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Select event:</label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control-sm" id="event" name="event"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Start date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicke"
                                        id="fecha_inicio" name="fecha_inicio" style="width:100%" autocomplete="off"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Tipo event:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-smtipo_evento"
                                        id="tipo_evento" name="tipo_evento" style="width:100%" autocomplete="off"
                                        required disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Expiration date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm datepicker" id="fecha_fin"
                                        name="fecha_fin" style="width:100%" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row mb-1">
                                <label for="note" class="col-sm-12 col-form-label-sm">Note:</label>
                                <div class="col-sm-12">
                                    <textarea name="note" id="note" class="form-control form-control-sm"" placeholder="optional"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="job_name" class="col-sm-4 col-form-label col-form-label-sm">
                                    Duracion day:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control form-control-sm" id="duracion_evento"
                                        name="edit_duracion_evento" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="job_name" class="col-sm-6 col-form-label col-form-label-sm">
                                    Activate alert | Days of anticipation:</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control form-control-sm" id="report_alert"
                                        name="edit_report_alert" value="" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <div class="file-loading">
                                    <input id="docs" name="docs[]" type="file" multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_button">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    <style>
        .anyClass {
            height: 300px;
            overflow-y: scroll;
        }
    </style>
