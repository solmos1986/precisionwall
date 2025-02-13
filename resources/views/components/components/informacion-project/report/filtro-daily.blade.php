<div id="filter_report_daily" class="modal fade" role="dialog" aria-hidden="true">
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
                        <form id="form_filtro">
                            <div class="form-group row">
                                <label for="date_order"
                                    class="col-sm-12 col-form-label col-form-label-sm">Projects:</label>
                                <div class="col-sm-12">
                                    <select name="filtro_proyectos[]" id="filtro_proyectos"
                                        class="form-control form-control-sm" multiple>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">From
                                    date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicke"
                                        id="filter_from_date" name="filter_from_date" placeholder="From date"
                                        value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">To
                                    date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicke"
                                        id="filter_to_date" name="filter_to_date" placeholder="To date" value=""
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="date_order" class="col-sm-3 col-form-label col-form-label-sm">Per
                                    Employee:</label>
                                <div class="col-sm-9">
                                    <label class="ms-checkbox-wrap ms-checkbox-primary">
                                        <input type="checkbox" name="filter_tipo" value="personal" id="filter_tipo">
                                        <i class="ms-checkbox-check"></i>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" id="view_daily">View report</button>
                        {{-- <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
