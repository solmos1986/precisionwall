<div id="formModalEditEvaluacion" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit_evaluacion_form" action="" method="POST">
                    @csrf
                    <input type="text" name="edit_evaluacion_id" id="edit_evaluacion_id" hidden>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select foreman: </label>
                                <select class="form-control form-control-sm" id="edit_foreman" name="edit_foreman"
                                    style="width:100%" required></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generate">Select staff:</label>
                                <select class="form-control-sm" id="edit_select_personal" style="100%" name="edit_select_personal[]"
                                multiple="multiple" required>
                            </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Note: </label>
                                <input type="text" class="form-control form-control-sm" name="edit_note" id="edit_note" style="width:100%"
                                  autocomplete="off"  required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select form: </label>
                                <select class="form-control form-control-sm" id="edit_formulario" name="edit_formulario"
                                    style="width:100%" required></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="generate"> Evaluation date:</label>
                                <input type="text" class="form-control form-control-sm datepicker" id="edit_fecha_asignacion" name="edit_fecha_asignacion"
                                    style="width:100%" required autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_edit_evaluacion">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
