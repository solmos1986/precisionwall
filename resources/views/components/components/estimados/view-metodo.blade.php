<div id="modalMetodo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" id="form_metodo">
                @csrf
                <div class="modal-header color-modal">
                    <h5 class="modal-title" id="title_modal_metodo"></h5>
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
                                            <input type="text" class="form-control form-control-sm" id="nombre_metodo"
                                                name="nombre_metodo" placeholder="Nombre" autocomplete="off">
                                            <input type="text" class="form-control form-control-sm"
                                                id="metodo_estandar_id" name="metodo_estandar_id" hidden>
                                            <input type="text" class="form-control form-control-sm" id="metodo_id"
                                                name="metodo_id" hidden>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">Unit
                                            Med.:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="unidad_medida"
                                                name="unidad_medida" placeholder="Unit Med." autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">M.
                                            Spread :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="materal_spread"
                                                name="materal_spread" placeholder="M. Spread " autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">M.
                                            Cost Unit :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                id="material_cost_unit" name="material_cost_unit"
                                                placeholder="M. Cost Unit" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">M.
                                            Unit Med :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                id="material_unit_med" name="material_unit_med"
                                                placeholder="M. Unit Med" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">Num.
                                            Coast :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="num_coast"
                                                name="num_coast" placeholder="Num. Coast" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">COST :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="mark_up"
                                                name="mark_up" placeholder="COST" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea" class="col-sm-3 col-form-label col-form-label-sm">Rate
                                            Hours :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="rate_hour"
                                                name="rate_hour" placeholder="Rate Hours" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="nombre_tarea"
                                            class="col-sm-3 col-form-label col-form-label-sm">Default :</label>
                                        <div class="col-sm-9">
                                            <label class="ms-checkbox-wrap">
                                                <input type="checkbox" id="default" name="default" value="y">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="cod_category_labor" class="col-sm-3 col-form-label col-form-label-sm">Cod category labor :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="cod_category_labor"
                                                name="cod_category_labor" placeholder="Cod category labor" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="cod_category_material" class="col-sm-3 col-form-label col-form-label-sm">Cod Category Material :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="cod_category_material"
                                                name="cod_category_material" placeholder="Cod Category Material" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label col-form-label-sm"
                                            for="process">Process:</label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-sm" name="process" id="process">
                                                <option value="Material and Installation">Material and Installation</option>
                                                <option value="Only Material">Only Material</option>
                                                <option value="Only Installation">Only Installation</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm " id="save_metodo" type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
