<div id="modal_reason" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span id="form_result"></span>
                <form id="razon">
                    <div class="form-group row">
                        <label for="tipo_r" class="col-sm-3 col-form-label col-form-label-sm">Type Reason</label>
                        <div class="col-sm-9">
                            <select name="tipo_r" id="tipo_r" class="form-control form-control-sm" required>
                                <option value="">select an option</option>
                                <option value="what">WHAT?</option>
                                <option value="why">WHY?</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descripcion_r" class="col-sm-3 col-form-label col-form-label-sm">Description</label>
                        <div class="col-sm-9">
                            <textarea name="descripcion_r" id="descripcion_r"
                                class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-sm-3 col-form-label col-form-label-sm">Description
                            Translation</label>
                        <div class="col-sm-9">
                            <textarea name="descripcion_r_t" id="descripcion_r_t"
                                class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="guardar_pregunta">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
