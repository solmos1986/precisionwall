<div id="formModalViewEmailSubOrden" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View email vendor</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_create_view_sub_orden" action="">
                    <input type="text" name="orden_id" id="orden_id" value="" hidden>
                    <input type="text" name="fecha_registro" id="fecha_registro" value="" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div id="summernote">
                                <body style="margin: 0px;" id="sub_orden">
                                   
                                </body>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>