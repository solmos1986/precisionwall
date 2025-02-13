<div id="modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-dark">
                <input type="hidden" id="id_signature">
                <input type="hidden" id="id_signature_input">
                <input type="hidden" id="id_text">
                <div id="signature-pad"><canvas style="border:1px solid #000" id="sign"></canvas></div>
            </div>
            @if (Route::is('crear.ticket') || Route::is('edit.ticket'))
                <div class="modal-body">
                    <input type="text" class="form-control form-control-sm" id="name_signature"
                        placeholder="Please enter your name">
                </div>
            @endif

            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="guardar_firma">Save Signature</button>
                <button type="button" class="btn btn-info btn-sm" id="limpiar">Clear</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
