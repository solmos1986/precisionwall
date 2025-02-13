<div id="formModalNewEvaluacion" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="new_evaluacion_form" action="" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select foreman: </label>
                                <select class="form-control form-control-sm" id="select_foreman" name="new_foreman"
                                    style="width:100%" required></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generate">Select staff:</label>
                                <select class="form-control-sm" id="select_personal" style="100%"
                                    name="new_select_personal[]" multiple="multiple" required>
                                    @foreach ($personal as $persona)
                                    <option value="{{ $persona->Empleado_ID }}">
                                        {{ $persona->name_personal }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Note: </label>
                                <input type="text" class="form-control form-control-sm" name="note" style="width:100%"
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select form: </label>
                                <select class="form-control form-control-sm" id="select_form" name="new_formulario"
                                    style="width:100%" required></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generate"> Evaluation date:</label>
                                <input type="text" class="form-control form-control-sm datepicker"
                                    name="new_fecha_asignacion" style="width:100%" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generate">Notify with email:</label>
                                <br>
                                <label class="ms-checkbox-wrap ms-checkbox-secondary">
                                    <input type="checkbox" value="ok" name="email" checked autocomplete="off">
                                    <i class="ms-checkbox-check" ></i>
                                  </label>
                                  <span>Send email:</span>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_evaluacion">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>