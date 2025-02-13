<div id="filter_download_excel" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row pb-2">
                <div class="col-md-12">
                    <div class="modal-body">
                        <form id="form_filtro_download">
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">No SOV code:</label>
                                <div class="col-sm-9">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="checkbox" name="no_sov_code" id="no_sov_code">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">No Price:</label>
                                <div class="col-sm-9">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="checkbox" name="no_precio" id="no_precio">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" id="filter_download_sov">Donwload</button>
                        {{-- <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
