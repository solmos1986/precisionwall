<div id="ModalDuplicar" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_superficie">Copy</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <input type="text" class="form-control form-control-sm" name="superficie_id" id="superficie_id"
                    hidden="">
                <div class="ms-panel" style="margin-bottom: 10px;">
                    <div class="ms-panel-body p-2">
                        <form action="" id="superficie">
                            <input type="hidden" name="_token" value="TDwPWqkv0AIZFmB4Z5Snmtwc6g5kcLVca6pfv6oP">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label for="codigo_surface"
                                            class="col-sm-3 col-form-label col-form-label-sm">Quantity:</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control form-control-sm" id="num_copia"
                                                name="num_copia" min="0" oninput="this.value = 
                                                !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" placeholder="Quantity" autocomplete="off">
                                                <input type="text" class="form-control form-control-sm" id="estimado_use_import_id"
                                                name="estimado_use_import_id" placeholder="Quantity" autocomplete="off"  hidden>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="duplicar" class="btn btn-success btn-sm border border-light"
                    data-dismiss="modal">Copy</button>
            </div>
        </div>
    </div>
</div>
