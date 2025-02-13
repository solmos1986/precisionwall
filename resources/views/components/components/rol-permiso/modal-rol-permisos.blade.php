<div id="ModalPermisoRol" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label for="nombre" class="col-sm-2 col-form-label col-form-label-sm">
                                Rol:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre" data-id=""
                                    placeholder="Rol" required="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="sub_contractor" class="col-sm-12 col-form-label col-form-label-sm">Sub
                                Permissions to modules and submodules::</label>
                        </div>
                        <div class="row" id="content_modulos">
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="save_button" data-estado="">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
