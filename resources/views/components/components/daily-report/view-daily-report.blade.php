<div id="Modal_filter_view" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select type of report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row pb-2">
                <div class="col-md-12">
                    <div class="modal-body">
                        <form id="form_filtro">
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">
                                    Admin/Foreman
                                </label>
                                <div class="col-sm-9">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input class="select_view" type="radio" name="tipo" value="personal" id="admin" data-id="" checked>
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">
                                    Client
                                </label>
                                <div class="col-sm-9">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input class="select_view" type="radio" name="tipo" value="personal" id="cliente" data-id="" >
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <a href="" class="btn btn-primary btn-sm" id="view_daily">View report</a>
                            {{-- <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
