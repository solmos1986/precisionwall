<div id="modalListLaborCost" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title">List Labor Cost</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="superficie">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <div class="ms-panel" style="margin-bottom: 10px;">          
                        <div class="ms-panel-body p-2">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                    <button type="button" class="btn btn-primary btn-sm mt-0 create_labor_cost">
                                        Add Labort Cost
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive w-100">
                                <table class="table table-hover w-100" id="lista_labor_cost">
                                    <thead>
                                        <tr>
                                            <th scope="col">Labor Cost</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
