<div id="modalSuperficie" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable {{-- modal-xl --}}" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_superficie"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="superficie">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <input type="text" class="form-control form-control-sm" name="superficie_id" id="superficie_id"
                        hidden>
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-header " style="padding: 0.5rem">
                            <h6 style="font-size:14px">surface
                            </h6>
                        </div>
                        <div class="ms-panel-body p-2">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="codigo_surface"
                                            class="col-sm-4 col-form-label col-form-label-sm">Code:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="codigo_surface"
                                                name="codigo_surface" placeholder="Code" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_surface"
                                            class="col-sm-4 col-form-label col-form-label-sm">Surface name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="nombre_surface"
                                                name="nombre_surface" placeholder="Surface name" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-4 col-form-label col-form-label-sm">Miscellaneous:</label>
                                        <div class="col-sm-8">
                                            <label class="ms-checkbox-wrap">
                                                <input type="checkbox" id="miscellaneous" name="miscellaneous" value="y">
                                                <i class="ms-checkbox-check"></i>
                                              </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm " id="save_superficie"
                        type="button">Save</button>
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
