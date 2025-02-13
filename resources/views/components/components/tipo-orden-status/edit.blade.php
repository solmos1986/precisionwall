<div id="formModalEditStatus" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Status</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" name="edit_id" id="edit_id" value="" hidden>
                <form id="form_edit_status" action="">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Code:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="edit_code"
                                        name="edit_code" placeholder="code" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="edit_name"
                                        name="edit_name" placeholder="name" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Color:</label>
                                <div class="col-sm-9">
                                    <select name="edit_color" id="edit_color" class="form-control form-control-sm"
                                        style="width:100%" required>
                                        <option class="text-light bg-primary" value="primary">primary</option>
                                        <option class="text-light bg-secondary" value="secondary">secondary</option>
                                        <option class="text-light bg-success" value="success">success</option>
                                        <option class="text-light bg-danger" value="danger">danger</option>
                                        <option class="text-light bg-warning" value="warning">warning</option>
                                        <option class="text-light bg-info" value="info">info</option>
                                        <option class="text-light bg-light" value="light">light</option>
                                        <option class="text-light bg-dark" value="dark">dark</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm update_status">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>