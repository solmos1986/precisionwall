<div id="formModalEditSubOrden" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit order</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit_sub_orden" action="">
                    <input type="text" name="edit_orden_proyecto_id" id="edit_orden_proyecto_id" value="" hidden>
                    <input type="text" name="edit_orden_id" id="edit_orden_id" value="" hidden>
                    <input type="text" name="edit_pedido_id" id="edit_pedido_id" value="" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Num
                                    order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="edit_num_orden_vendor" name="edit_num_orden_vendor"
                                        placeholder="Date of Work" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Name
                                    order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="edit_name_orden_vendor" name="edit_name_orden_vendor"
                                        placeholder="Date of Work" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                <div class="col-sm-9">
                                    <select name="edit_proveedor_status" id="edit_proveedor_status"
                                        class="form-control form-control-sm" style="width:100%" required>
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
                                        id="edit_date_vendor" name="edit_date_vendor" placeholder="Date of Work"
                                        value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p><strong>SELECTED MATERIALS:</strong></p>
                            <table class="table thead-primary table-bordered w-100" id="edit_materiales">
                                <thead>
                                    <tr>
                                        <th>Material/Equipment</th>
                                        <th>Unit</th>
                                        <th>Q. Required</th>
                                        <th>Q. To Ordered</th>
                                        <th>Q. To Order</th>
                                        <th>Q. at Warehouse</th>
                                        <th>Q. at Project</th>
                                        <th width="20">Q. at Vendor</th>
                                        <th>Total Quantity Ordered</th>
                                        <th>Q. used</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div><br>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Vendor:</label>
                                <div class="col-sm-9">
                                    <select name="edit_from_vendedor" id="edit_from_vendedor"
                                        class="form-control form-control-sm" style="width:100%" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">TO:</label>
                                <div class="col-sm-9">
                                    <select name="edit_to_vendor" id="edit_to_vendor"
                                        class="form-control form-control-sm" style="width:100%" required>
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
                                    <input type="text" class="form-control form-control-sm" id="edit_pco_vendor"
                                        name="edit_pco_vendor" placeholder="pco" value="" readonly>
                                    <input type="text" class="form-control form-control-sm" id="edit_pco_corr"
                                        name="edit_pco_corr" placeholder="pco" value="" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-4 col-form-label col-form-label-sm">
                                    Requested delivery date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="edit_fecha_entrega_vendor" name="edit_fecha_entrega_vendor"
                                        placeholder="Date of Work" value="">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">
                                    Tracking date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="edit_fecha_segimiento_vendor" name="edit_fecha_segimiento_vendor"
                                        placeholder="Date of Work" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="edit_orden_delivery">
                        </div>
                        <div class="col-md-12">
                            <br>
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-2 col-form-label col-form-label-sm">Note</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="edit_nota_vendor" id="edit_nota_vendor"
                                        cols="3"
                                        rows="3">Please deliver to the job site tomorrow morning early.</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" name="refresh" id="view_edit_materiales"
                                class="btn btn-primary btn-sm mt-0"><i class="fas fa-eye-slash"></i> View
                                Materials</button>
                            <br>
                            <br>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm update_sub_orden">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="ocultar_edit_materiales" class="hide">
                            <p><strong>LIST MATERIALS:</strong></p>
                            <div class="container d-flex justify-content-center">
                                <div class="row">
                                    <div class="col-md mb-3">
                                        <input type="text" name="material" id="edit_material"
                                            class="form-control form-control-sm" placeholder="Material"
                                            autocomplete="off" />
                                    </div>
                                    <div class="col-md mb-3">
                                        <input type="text" name="proyecto" id="edit_proyecto"
                                            class="form-control form-control-sm" placeholder="Proyect"
                                            autocomplete="off" />
                                    </div>
                                    <div class="col-md mb-3">
                                        <button type="button" name="refresh" id="edit_refresh"
                                            class="btn btn-primary btn-sm mt-0"><i
                                                class="fas fa-retweet"></i></button>
                                    </div>
                                </div>
                            </div><br>
                            <div class="table-responsive">
                                <table id="edit-list-materiales" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th>Unit</th>
                                            <th>Project</th>
                                            <th>Total Q. Ordered</th>
                                            <th>Quantity</th>
                                            <th>Q. store in</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>