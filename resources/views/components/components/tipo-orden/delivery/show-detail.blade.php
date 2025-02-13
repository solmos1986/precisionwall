<style>
    #signature-pad-install {
        min-height: 200px;
        min-width: 300px;
        border: 0px solid #000;
    }

    #signature-pad-install canvas {
        position: relative: 0;
        top: 0;
        width: 80%;
        height: 80%
    }

    #signature-pad-foreman {
        min-height: 200px;
        min-width: 300px;
        border: 0px solid #000;
    }

    #signature-pad-foreman canvas {
        position: relative: 0;
        top: 0;
        width: 80%;
        height: 80%
    }
</style>

<div id="formModalShowOrder" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="show_order_detail" action="">
                    @csrf
                    <input type="text" id="order_id" name="order_id" hidden>
                    <input type="text" id="fecha" name="fecha" hidden>
                    <input type="text" id="tipo_tranferencia_envio_id" name="tipo_tranferencia_envio_id" hidden>
                    <input type="text" class="form-control form-control-sm" id="fecha_foreman" name="fecha_foreman"
                        value="{{ date('Y-m-d H:i:s') }}" hidden>
                    <input type="text" class="form-control form-control-sm" id="signature_install"
                        name="signature_install" hidden>
                    <input type="text" class="form-control form-control-sm" id="signature_foreman"
                        name="signature_foreman" hidden>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="job_name" class="col-sm-3 col-form-label col-form-label-sm">Address</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="address" name="address"
                                        placeholder="Address" required readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="job_name" class="col-sm-3 col-form-label col-form-label-sm">Job Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="job_name"
                                        name="job_name" placeholder="Job Name" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_empleoye_id" class="col-sm-3 col-form-label col-form-label-sm">Name Sub
                                    C. Employee</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="sub_empleoye_id"
                                        name="sub_empleoye_id" placeholder="Job Name" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Date
                                    Schedule</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicker" id="date_work"
                                        name="date_work" placeholder="Date of Work" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Po order</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="pco_pedido"
                                        name="pco_pedido" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status</label>
                                <div class="col-sm-9">
                                        <select name="status" id="status"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        <option value="7">Deliver Requested</option>
                                        <option value="8">In Transit</option>
                                        <option value="6">Fully Delivered</option>
                                        <option value="4">Received</option>
                                        <option value="1">Not Yet Ordered</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <table class="table table-hover thead-light" id="table-material">
                                <thead>
                                    <tr>
                                        <th scope="col" width="250px">Material</th>
                                        <th scope="col">Unity</th>
                                        <th scope="col">Quantity Ordered</th>
                                        <th scope="col">Q. to the job site</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-center">Installer signature</p>
                            <div class="text-center">
                                <div id="signature-pad-install">
                                    <canvas style="border:1px solid #000" id="sign_install">
                                    </canvas>
                                </div>
                                <img id="show_signature-pad-install">
                                <button type="button" class="btn btn-pill btn-primary mt-0 btn-sm"
                                    id="limpiar_install">Clear</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="text-center">Installer foreman</p>
                            <div class="text-center">
                                <div id="signature-pad-foreman">
                                    <canvas style="border:1px solid #000" id="sign_foreman">
                                    </canvas>
                                </div>
                                <img id="show_signature-pad-foreman">
                                <button type="button" class="btn btn-pill btn-primary mt-0 btn-sm"
                                    id="limpiar_foreman">Clear</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_order_detail">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>