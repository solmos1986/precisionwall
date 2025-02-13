
<div id="formModalCreateTransferencia" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deliveries/Transfers</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_create_transferencia" action="">
                    <input type="text" name="transferencia_orden_proyecto_id" id="transferencia_orden_proyecto_id" value="" hidden>
                    <input type="text" name="transferencia_orden_id" id="transferencia_orden_id" value="" hidden>
                    <input type="text" name="transferencia_pedido_id" id="transferencia_pedido_id" value="" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Num
                                    order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="transferencia_num_orden_vendor" name="transferencia_num_orden_vendor"
                                        placeholder="Number Order" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Name
                                    order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="transferencia_name_orden_vendor" name="transferencia_name_orden_vendor"
                                        placeholder="Date of Work" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                <div class="col-sm-9">
                                    <select name="transferencia_proveedor_status" id="transferencia_proveedor_status"
                                        class="form-control form-control-sm" style="width:100%" required >
                                        @foreach ($status as $estado)
                                        <option value="{{$estado->id}}">{{$estado->nombre}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-5 col-form-label col-form-label-sm">Date order to
                                    vendor:</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="transferencia_date_vendor" name="transferencia_date_vendor" placeholder="Date of Work"
                                        value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p><strong>SELECTED MATERIALS:</strong></p>
                            <table class="table thead-primary table-bordered w-100" id="transferencia_materiales">
                                <thead>
                                    <tr>
                                        <th>Material/Equipment</th>
                                        <th>Unit</th>
                                        <th>Q. Required</th>
                                        <th>Q. Ordered</th>
                                        <th>Q. to Order</th>
                                        <th>Q. at Warehouse</th>
                                        <th>Q. at Project</th>
                                        <th width="20">Q. at Vendor</th>
                                        <th>Total Quantity Ordered</th>
                                        <th>Q. Used</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div><br>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">From:</label>
                                <div class="col-sm-9">
                                    <select name="transferencia_from_vendedor" id="transferencia_from_vendedor"
                                        class="form-control form-control-sm" style="width:100%" required >
                                    </select >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">TO:</label>
                                <div class="col-sm-9">
                                    <select name="transferencia_to_vendor" id="transferencia_to_vendor"
                                        class="form-control form-control-sm" style="width:100%" required >
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">
                                    PCO
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="transferencia_pco_vendor"
                                        name="transferencia_pco_vendor" placeholder="pco" value="" readonly>
                                    <input type="text" class="form-control form-control-sm" id="transferencia_pco_corr"
                                        name="transferencia_pco_corr" placeholder="pco" value="" hidden readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-4 col-form-label col-form-label-sm">
                                    Requested delivery date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="transferencia_fecha_entrega_vendor" name="transferencia_fecha_entrega_vendor"
                                        placeholder="Date of Work" value="" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">
                                    Tracking date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="transferencia_fecha_segimiento_vendor" name="transferencia_fecha_segimiento_vendor"
                                        placeholder="Date of Work" value="" autocomplete="off"> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-2 col-form-label col-form-label-sm">Note</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="transferencia_nota_vendor" id="transferencia_nota_vendor"
                                        cols="1" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="update_transferencia">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>