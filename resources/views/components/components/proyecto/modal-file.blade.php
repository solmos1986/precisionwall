<div id="ModalNotaImages" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_nota_images" action="" method="POST">
                    @csrf
                    <p class="ms-directions text-center mb-1">FILES</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <div class="file-loading">
                                    <input id="modal_nota_files" accept=".heic" name="modal_nota_files[]" type="file" multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="save_nota">Save</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
