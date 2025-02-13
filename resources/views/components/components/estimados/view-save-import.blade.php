<div id="modalSaveImport" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable {{-- modal-xl --}}" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title">Save import</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="save_import_project">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-body p-2">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="date_order"
                                            class="col-sm-3 col-form-label col-form-label-sm">Project:</label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-sm" id="select2_proyectos"
                                                name="proyecto_id" style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="description"
                                            class="col-sm-3 col-form-label col-form-label-sm">Description:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="description" autocomplete="off"
                                                name="description" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="user_name"
                                            class="col-sm-3 col-form-label col-form-label-sm">User:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="user_name" 
                                            value="{{$userName}}"
                                                name="user_name" readonly>
                                            <input type="text" class="form-control form-control-sm" id="user_id"
                                                name="user_id" value="{{$userId}}" hidden>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_surface"
                                            class="col-sm-3 col-form-label col-form-label-sm">Date:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="fecha_registro"
                                                name="fecha_registro" placeholder="Surface name" autocomplete="off" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm " id="save_import"
                        type="button">Save</button>
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
