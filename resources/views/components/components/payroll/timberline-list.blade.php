<div id="lista_importacion_timberline" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title">List of saved imports</h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" id="save_import_project">
                @csrf
                <div class="modal-body" style="background: rgb(242, 242, 255);">
                    <div class="ms-panel" style="margin-bottom: 10px;">
                        <div class="ms-panel-body p-2">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-hover w-100" id="datatable_timberline">
                                    <thead>
                                        <tr>
                                            <th scope="col">Description</th>
                                            <th scope="col">Fecha create</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
