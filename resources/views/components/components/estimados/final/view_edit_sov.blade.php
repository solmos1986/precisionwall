<div id="modalEditSovFinal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title">Edit Task</h5>
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
                            <div class="table-responsive w-100">
                                <table class="table table-hover w-100" id="lista_labor_cost">
                                    <thead>
                                        <tr>
                                            <th scope="col">DESCRIPTION</th>
                                            <th scope="col">ESTIMATE HOURS</th>
                                            <th scope="col">TOTAL PRICE</th>
                                            <th scope="col">CODE SOV</th>
                                            <th scope="col">DESCRIPTION SOV</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" id="CC_budget_QTY"
                                                    name="CC_budget_QTY" min="0" placeholder="DESCRIPTION" autocomplete="off" >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" id="um" name="um"
                                                    placeholder="ESTIMATE HOURS" autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" id="of_coast"
                                                    name="of_coast" min="0" placeholder="TOTAL PRICE" autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" id="pwt_pro_rate"
                                                    name="pwt_pro_rate" placeholder="CODE SOV" autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" id="estimate_hours"
                                                    name="estimate_hours" placeholder="DESCRIPTION SOV" autocomplete="off">
                                            </td>
                                        </tr>
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
