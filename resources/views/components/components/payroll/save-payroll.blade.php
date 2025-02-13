<div id="save_import_payroll" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title">Save Payroll</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_payroll">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <input type="text" class="form-control form-control-sm" name="payrollId" id="payrollId"
                        hidden="">
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-body p-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="payroll_descripcion"
                                            class="col-sm-4 col-form-label col-form-label-sm">Name:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="payroll_nombre" name="payroll_nombre" placeholder="Name"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="payroll_FechaActualizacion"
                                            class="col-sm-4 col-form-label col-form-label-sm">Description:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="payroll_descripcion" name="payroll_descripcion" placeholder="Description"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="payroll_FechaActualizacion"
                                            class="col-sm-4 col-form-label col-form-label-sm">From date:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="fecha_inicio" name="fecha_inicio" placeholder="To date"
                                                autocomplete="off" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="payroll_FechaActualizacion"
                                            class="col-sm-4 col-form-label col-form-label-sm">To date:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="fecha_fin" name="fecha_fin" placeholder="Decription"
                                                autocomplete="off" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm save_payroll" id="save_payroll">Save and deploy</button>
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
