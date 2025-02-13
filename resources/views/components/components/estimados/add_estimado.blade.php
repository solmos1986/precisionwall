<div id="ModalAddEstimado" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl"" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_superficie">
                    Add estimate to existing project
                </h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <input type="text" class="form-control form-control-sm" name="superficie_id" id="superficie_id"
                    hidden="">
                <div class="ms-panel" style="margin-bottom: 10px;">
                    <div class="ms-panel-body p-2">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 id="proyecto_add_estimado"></h6>
                                <br>
                                <table id="list-add-import" class="table table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>Name area</th>
                                            <th>Cost code</th>
                                            <th>Name Task</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="content_add_estimado">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="save_add_estimados" class="btn btn-success btn-sm border border-light"
                    data-dismiss="modal">Process changes</button>
            </div>
        </div>
    </div>
</div>
