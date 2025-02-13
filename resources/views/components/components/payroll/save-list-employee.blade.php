<div id="save_import_list_employee" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title">Save list employee</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="form_save_list_employee">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <input type="text" class="form-control form-control-sm" name="list_employeeId" id="list_employeeId"
                        hidden="">
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-body p-2">
                            <input type="hidden" name="_token" value="AIXk8JJCgCphUPOmFs1GpCWEYzaljnmajMEnR6jL">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="descripcion"
                                            class="col-sm-4 col-form-label col-form-label-sm">Description:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="employee_descripcion" name="descripcion" placeholder="Description"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row mb-1">
                                        <label for="fechaRegistro"
                                            class="col-sm-4 col-form-label col-form-label-sm">Registration date:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm"
                                                id="employee_fechaRegistro" name="fechaRegistro" placeholder="Registration date" readonly
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm save_list_employee" id="save_list_employee">Save</button>
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
