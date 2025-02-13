<div id="ModalEditArea" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_update_area">Copy</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <input type="text" class="form-control form-control-sm" name="superficie_id" id="superficie_id"
                    hidden="">
                <div class="ms-panel" style="margin-bottom: 10px;">
                    <div class="ms-panel-body p-2">
                        <form action="" id="from_update_area">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_area"
                                            class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="nombre_area"
                                                name="nombre_area"  placeholder="Name" autocomplete="off">
                                                <input type="text" class="form-control form-control-sm" id="nombre_area_anterior"
                                                name="nombre_area_anterior" autocomplete="off" hidden>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="update_area" class="btn btn-success btn-sm border border-light" >save</button>
            </div>
        </div>
    </div>
</div>
