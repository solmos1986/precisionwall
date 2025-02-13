<div id="modalDuplicadosImportDataBse" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="modal_duplicados_import_database"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <form id="fromUpdateImportDatabase" >
                    @csrf
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm " id="update_import_database" type="button">Save</button>
            </div>
        </div>
    </div>
</div>
