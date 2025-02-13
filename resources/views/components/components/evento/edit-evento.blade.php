<div id="formModalEditEvent" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit_event_form" action="{{ route('new.all.cardex') }}" method="POST">
                    @csrf
                    <input type="text" name="cod_evento" id="cod_evento" hidden>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type event: </label>
                                <select class="form-control form-control-sm" id="edit_tipo" name="tipo_evento"
                                    style="width:100%" required>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generate">Event name:</label>
                                <input type="text" class="form-control form-control-sm" name="name" id="name_evento"
                                    style="width:100%" required autocomplete="none">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Note: </label>
                                <input type="text" class="form-control form-control-sm" name="note" id="note"
                                    style="width:100%" placeholder="optional" autocomplete="none">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description: </label>
                                <input name="description" id="descripcion_evento" type="text" style="width:100%"
                                    class="form-control" placeholder="optional" autocomplete="none">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="generate">Visible to:</label>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="generate">Company:</label>
                                        <select class="form-control-sm" id="edit_company" style="100%"
                                            multiple="multiple" required>
                                            @foreach ($company as $value)
                                                <option value="{{ $value->Emp_ID }}">
                                                    {{ $value->Nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="generate">Position:</label>
                                        <select class="form-control-sm" id="edit_cargo" style="100%"
                                            multiple="multiple" required>
                                            @foreach ($cargo as $rol)
                                            <option value="{{ $rol->Cargo }}">
                                                {{ $rol->Cargo }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="generate">User:</label>
                                        <select class="form-control-sm" id="edit_personal" style="100%" name="access_code[]"
                                            multiple="multiple" required>
                                           
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Calculate days </label>
                                    <input id="edit_fecha_inicio" class="form-control form-control-sm datepicke edit_date"
                                    style="width:100%"  autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                    <input id="edit_fecha_final" class="form-control form-control-sm datepicke edit_date"
                                    style="width:100%"  autocomplete="off" required>
                                </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Days of duration: </label>
                                        <input type="number" min="0" class="form-control form-control-sm" id="edit_duracion_day" name="duracion_day"
                                            style="width:100%" placeholder="number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Update this event: </label>
                            <ul class="ms-list d-flex">
                                <li class="ms-list-item pl-0">
                                    <label class="ms-checkbox-wrap">
                                        <input class="editCheckYes" type="radio" name="access_pers" value="y">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span> Yes </span>
                                </li>
                                <li class="ms-list-item">
                                    <label class="ms-checkbox-wrap">
                                        <input class="editCheckNo" type="radio" name="access_pers" value="n">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                    <span> No </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Activate alert | Days of anticipation</label>
                                <input type="number" min="0" class="form-control form-control-sm" name="day_alert"
                                    id="report_alert" style="width:20%" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm edit_save_button_event">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
