<div id="modalEditModalTask" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_edit_import"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <form id="fromUpdateImport">
                    @csrf
                    <input type="number" class="form-control form-control-sm" id="estimado_use_import_id"
                        name="estimado_use_import_id" hidden>
                    <table id="list-proyectos" class="table table-hover thead-primary w-100">
                        <thead id="load-data-thead">
                            <tr>
                                <th>CC&nbsp;BUTGET&nbsp;QTY</th>
                                <th>&nbsp;&nbsp;UM&nbsp;&nbsp;</th>
                                <th>OF&nbsp;COATS</th>
                                <th>PWT&nbsp;&nbsp;PROD RATE</th>
                                <th>ESTIMATED HOURS</th>
                                <th>ESTIMATED LABOR COST </th>
                                <th>MATERIAL OR EQUIPMENT UNIT COST</th>
                                <th>MATERIAL SPREAD RATE PER UNIT</th>
                                <th>MAT QTY OR GALLONS / UNIT</th>
                                <th>MAT&nbsp;UM</th>
                                <th>MATERIAL COST</th>
                                <th>PRICE&nbsp;TOTAL</th>
{{--                                 <th>PERCENTAGE</th> --}}
                                <th>&nbsp;&nbsp;COST&nbsp;&nbsp;</th>
                                <th>SUBCONTRACT COST</th>
                                <th>EQUIPMENT COST</th>
                                <th>OTHER COST</th>
                            </tr>
                        </thead>
                        <tbody id="load-data-tbody">
                            <tr>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="CC_budget_QTY"
                                        name="CC_budget_QTY" min="0" placeholder="CC budget QTY" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" id="um" name="um"
                                        placeholder="UM" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="of_coast"
                                        name="of_coast" min="0" placeholder="Of Coast" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="pwt_pro_rate"
                                        name="pwt_pro_rate" placeholder="PWT ProRate" autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="estimate_hours"
                                        name="estimate_hours" placeholder="Quantity" autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="estimate_labor_hours"
                                        name="estimate_labor_hours" placeholder="Estimate Labor Hours"
                                        autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm"
                                        id="material_or_equipment_unit_cost" name="material_or_equipment_unit_cost"
                                        placeholder="MATERIAL OR EQUIPMENT UNIT COST" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm"
                                        id="material_spread_rate_per_unit" name="material_spread_rate_per_unit"
                                        placeholder="MATERIAL SPREAD RATE PER UNIT" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="mat_qty_or_galon"
                                        placeholder="MAT QTY OR GALLONS / UNIT" name="mat_qty_or_galon"
                                        autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" id="mat_um" name="mat_um"
                                        placeholder="MAT UM" autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="material_cost"
                                        placeholder="MATERIAL COST" name="material_cost" placeholder="MATERIAL COST"
                                        autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="preci_total"
                                        placeholder="PRICE TOTAL" name="preci_total" autocomplete="off">
                                </td>
                              {{--   <td>
                                    <input type="number" class="form-control form-control-sm" id="porcentaje"
                                        placeholder="PERCENTAGE" name="porcentaje" autocomplete="off">
                                </td> --}}
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="mark_up"
                                        placeholder="COST" name="mark_up" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" id="sub_contrac_cost"
                                        placeholder="SUBCONTRACT COST" name="sub_contrac_cost" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="equipment_cost"
                                        name="equipment_cost" placeholder="EQUIPMENT COST" autocomplete="off" >
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" id="other_cost"
                                        name="other_cost" autocomplete="off" placeholder="Other Cost">
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm " id="update_import" type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
