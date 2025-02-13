    <div id="formModalOption" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-ms" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_option">
                        <div class="table-responsive">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label for="general"
                                            class="col-sm-3 col-form-label col-form-label-sm">Option</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="opcion"
                                                placeholder="option">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="general"
                                            class="col-md-3 col-form-label col-form-label-sm">Description</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control form-control-sm" id="descripcion"
                                                placeholder="Description">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-primary mt-0 mb-3" id="add_option"
                                                style="min-width: 20px;">Add</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row " id="option_padre">
                                        <div class="col-md-12 m-1 pt-2 pb-2" style="background: rgb(243, 243, 243)">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="opcion" placeholder="sub option">
                                                </div>
                                                <div class="col-sm-1">
                                                    <button type="button" style="width: 30px;height: 30px;"
                                                        class="ms-btn-icon btn-square btn-sm btn-primary add_valor">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="col-sm-1">
                                                    <button type="button" style="width: 30px;height: 30px;"
                                                        class="ms-btn-icon btn-square btn-sm btn-danger delete_option">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-12 option_hijo">
                                                <div class="row m-1">
                                                    <div class="col-sm-1">
                                                        <label for="general"
                                                            class=" col-form-label col-form-label-sm">-</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="opcion" placeholder="detail">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" style="width: 30px;height: 30px;"
                                                            class="ms-btn-icon btn-square btn-sm btn-danger delete_valor">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm store_option" id="save_option">Add</button>
                </div>
            </div>
        </div>
    </div>
