<div id="formModalMovimientoMaterial" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Traking Material</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_movimiento">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Traking:</strong></p>
                            <table class="table thead-primary table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Material/Equipment</th>
                                        <th>Unidad</th>
                                        <th width="50">Quantity</th>
                                        <th width="50">Status</th>
                                        <th width="200">Note</th>
                                        <th>Sent a</th>
                                        <th>Tracking date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="movimientos_materiales_pedido">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" id="load_images_traking">
                            <p class="ms-directions text-center mb-1">FILES</p>
                            <div class="file-loading">
                                <input class="recibir" id="recibir-pedido-traking" name="recibir[]" type="file" multiple>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm update_form_movimiento">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
