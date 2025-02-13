<div id="modalLaborCost" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable {{-- modal-xl --}}" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_labor_cost"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_labor_cost">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <input type="text" class="form-control form-control-sm" name="labor_cost_id" id="labor_cost_id"
                        hidden>
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-body p-2">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="labor_cost"
                                            class="col-sm-4 col-form-label col-form-label-sm">Labor Cost:</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="labor_cost"
                                                name="labor_cost" placeholder="Code" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="labor_cost_descripcion"
                                            class="col-sm-4 col-form-label col-form-label-sm">Description:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="labor_cost_descripcion"
                                                name="labor_cost_descripcion" placeholder="Surface name" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm " id="save_labor_cost"
                        type="button">Save</button>
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
