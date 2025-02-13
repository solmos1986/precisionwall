<div id="modalEstandar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" id="form_standar">
                @csrf
                <div class="modal-header color-modal">
                    <h5 class="modal-title" id="title_modal_estandar"></h5>
                    <button type="button" class="close" style="color:black" aria-label="Close"
                        data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-body p-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea"
                                            class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="nombre_tarea"
                                                name="nombre_tarea" placeholder="Nombre" autocomplete="off">
                                            <input type="text" class="form-control form-control-sm"
                                                id="estandar_superficie_id" name="estandar_superficie_id" hidden>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="cost_code" class="col-sm-3 col-form-label col-form-label-sm">
                                            Cost Code:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="cost_code"
                                                name="cost_code" placeholder="Cost Code" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="descripcion" class="col-sm-3 col-form-label col-form-label-sm">
                                            Description:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="descripcion"
                                                name="descripcion" placeholder="Description" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="sov_id" class="col-sm-3 col-form-label col-form-label-sm">
                                            Sum SOV Id:</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control form-control-sm" id="sov_id"
                                                name="sov_id" placeholder="id_sov" autocomplete="off">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-sm" id="nombre_sov_id"
                                                name="nombre_sov_id" placeholder="Sum SOV Id" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm " id="save_standar" type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
