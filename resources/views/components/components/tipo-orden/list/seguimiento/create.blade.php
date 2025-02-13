<div id="formModalCreateSeguimiento" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Traking equipment / material</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_create_seguimiento" action="">
                    <input type="text" name="new_segimiento_proyecto_id" id="new_segimiento_proyecto_id" value="" hidden>
                    <input type="text" name="new_segimiento_orden_id" id="new_segimiento_orden_id" value="" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Num order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="new_segimiento_orden_num" name="new_segimiento_orden_num"
                                        placeholder="Date of Work" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Name order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="new_segimiento_nombre" name="new_segimiento_nombre"
                                        placeholder="Date of Work" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                <div class="col-sm-9">
                                    <select name="new_segimiento_vendor" id="new_segimiento_vendor"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        @foreach ($status as $estado)
                                        <option id="edit_exterior_status" value="{{$estado->id}}">{{$estado->nombre}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-5 col-form-label col-form-label-sm">Date order to vendor:</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="new_segimiento_date" name="new_segimiento_date"
                                        placeholder="Date of Work" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p><strong>SELECTED MATERIALS:</strong></p>
                            <table class="table thead-primary table-bordered w-100" id="segimiento_materiales">
                                <thead>
                                    <tr>
                                        <th>Material/Equipment</th>
                                        <th>Unidad</th>
                                        <th>Q. requested</th>
                                        <th>Q. ordered amount</th>
                                        <th>Q. status</th>
                                        <th>Q. warehouse</th>
                                        <th>Q. project</th>
                                        <th>Q. estimated</th>
                                        <th>Q. ordered</th>
                                        <th>Q. received</th>
                                        <th>Q. using</th>
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
                                    <select name="proveedor_vendedor" id="proveedor_vendedor"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        @foreach ($proveedores as $proveedor)
                                        <option value="{{$proveedor->Pro_ID}}">{{$proveedor->Nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">TO:</label>
                                <div class="col-sm-9">
                                    <select name="new_segimiento_to_vendor" id="new_segimiento_to_vendor"
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
                                    <input type="text" class="form-control form-control-sm" id="new_segimiento_pco_vendor"
                                        name="new_segimiento_pco_vendor" placeholder="pco" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-4 col-form-label col-form-label-sm">
                                    Requested delivery date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="new_fecha_entrega_vendor" name="new_fecha_entrega_vendor"
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
                                        id="new_fecha_segimiento_vendor" name="new_fecha_segimiento_vendor"
                                        placeholder="Date of Work" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-2 col-form-label col-form-label-sm">Note</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="new_nota_vendor" id="new_nota_vendor" cols="3"
                                        rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_sub_orden">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>