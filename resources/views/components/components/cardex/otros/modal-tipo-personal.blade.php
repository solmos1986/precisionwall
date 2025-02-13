<div id="ModalTipoPersonal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-gl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_tipo_personal" action="" method="POST">
                    @csrf
                    <input type="text" class="form-control form-control-sm" name="id_tipo_personal" id="id_tipo_personal" hidden>
                    <div class="form-group">
                        <label for="generate">Name:</label>
                        <input type="text" class="form-control form-control-sm" name="name_tipo_personal" id="name_tipo_personal" style="width:100%"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Description: </label>
                        <input name="description_tipo_personal" id="description_tipo_personal" type="text" style="width:100%" class="form-control"
                            placeholder="optional">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="save_tipo_personal" >Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
