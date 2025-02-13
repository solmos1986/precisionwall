<div id="formModalListaMateriales" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="nombre_lista_materiales" class="modal-title"></h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="ms-panel-body">
                    <div class="container d-flex justify-content-center">
                        <div class="row">               
                            <div class="col-md mb-3">
                                <input type="text" name="material" id="list_material" class="form-control form-control-sm"
                                    placeholder="Material" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <input type="text" name="proyecto" id="list_proyecto" class="form-control form-control-sm"
                                    placeholder="Proyect" autocomplete="off" />
                            </div>
                            <div class="col-md mb-3">
                                <button type="button" name="refresh" id="list_refresh" class="btn btn-primary btn-sm mt-0"><i
                                        class="fas fa-retweet"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list-materiales" class="table thead-primary w-100">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>