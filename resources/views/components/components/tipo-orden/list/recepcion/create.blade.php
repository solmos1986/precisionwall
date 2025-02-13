<div id="formModalCreateRecepecion" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receipt equipment / material</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_create_recepecion" action="{{ route('order.list.recepcion.store') }}">
                    <input type="text" name="new_recepcion_sub_orden" id="new_recepcion_sub_orden" value=""
                        hidden>
                    <input type="text" name="new_recepcion_orden_id" id="new_recepcion_orden_id" value=""
                        hidden>
                    <input type="text" name="new_recepcion_to" id="new_recepcion_to" value="" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Num
                                    order:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="new_recepcion_num_orden" name="new_recepcion_num_orden"
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
                                        id="new_recepcion_name_orden" name="new_recepcion_name_orden"
                                        placeholder="Date of Work" value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                <div class="col-sm-9">
                                    <select name="new_segimiento_vendor_status" id="new_segimiento_vendor_status"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        @foreach ($status as $estado)
                                            <option value="{{ $estado->id }}">{{ $estado->nombre }}
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
                                        id="new_recepcion_date" name="new_recepcion_date" placeholder="Date of Work"
                                        value="" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p><strong>SELECTED MATERIALS:</strong></p>
                            <table class="table thead-primary table-bordered w-100" id="new_recepcion_materiales">
                                <thead>
                                    <tr>
                                        <th>Material/Equipment</th>
                                        <th>Unidad</th>
                                        <th width="150">Q. Ordered</th>
                                        <th width="150">Q. Received</th>
                                        <th>Q.Receiving</th>
                                        <th width="220">Status</th>
                                        <th width="220">At</th>
                                        <th width="300">Note</th>
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
                                    <input type="text" class="form-control form-control-sm" id="new_vendor_recepcion"
                                        name="new_vendor_recepcion" placeholder="Date of Work" value="" disabled>
                                    <input type="text" class="form-control form-control-sm"
                                        id="new_recepcion_from_vendor" name="new_recepcion_from_vendor" value=""
                                        hidden>
                                </div>
                            </div>
                        </div>
                        <!--div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">TO:</label>
                                <div class="col-sm-9">
                                    <select name="new_recepcion_to_vendor" id="new_recepcion_to_vendor"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        
                                    </select>
                                </div>
                            </div>
                        </div-->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">
                                    PCO
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm"
                                        id="new_recepcion_pco_vendor" name="new_recepcion_pco_vendor"
                                        placeholder="pco" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-4 col-form-label col-form-label-sm">
                                    Requested delivery date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="new_fecha_recepcion_vendor" name="new_fecha_recepcion_vendor"
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
                                        id="new_fecha_recepcion_traking" name="new_fecha_recepcion_traking"
                                        placeholder="Date of Work" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-2 col-form-label col-form-label-sm">Note</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="new_nota_recepcion_sub_orden" id="new_nota_recepcion_sub_orden" cols="1"
                                        rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="load_images">
                            <p class="ms-directions text-center mb-1">FILES</p>
                            <div class="file-loading">
                                <input class="recibir" id="recibir-pedido" name="recibir[]" type="file" multiple>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm store_new_recepcion">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
