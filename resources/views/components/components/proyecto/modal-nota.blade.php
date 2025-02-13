<div id="ModalNota" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_nota" action="" method="POST">
                    @csrf
                    <input type="text" class="form-control form-control-sm" id="nota_id" name="nota_id"
                        autocomplete="off" hidden>
                    <p class="ms-directions mb-0">ASSING NOTE TO:</p>
                    <div class="row pb-2">
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Project:</label>
                                <div class="col-sm-8">
                                    <select class="form-control form-control-sm" id="proyecto_id"
                                        name="proyecto_id"></select>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">General Contractor:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="empresa"
                                        name="empresa" autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Code Project:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="codigo"
                                        name="codigo" autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-4 col-form-label-sm">Date of notification:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm TodayTime"
                                        id="fecha_entrega" name="fecha_entrega" autocomplete="off"
                                        value="{{ date('m/d/Y') }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="generate" class="col-sm-12 col-form-label-sm">Assign to Employee:</label>
                                <div class="col-sm-6">
                                    <ul class="ms-list ms-list-display">
                                        <li class="mb-1">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="checkbox" id="proyecto_manager_id" value="">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <strong>PM: </strong><span id="proyecto_manager"></span>
                                        </li>
                                        <li class="mb-1">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="checkbox" value=""
                                                    id="asistente_proyecto_manager_id">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <strong>Asistant PM: </strong><span id="asistente_proyecto_manager"> </span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="ms-list ms-list-display">
                                        <li class="mb-1">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="checkbox" id="foreman_id" value="">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <strong>Foreman: </strong><span id="foreman"></span>
                                        </li>
                                        <li class="mb-1">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="checkbox" value="" id="lead_id">
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <strong>Lead: </strong><span id="lead"> </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    <p class="ms-directions mb-0">DESCRIPTION:</p>
                    <div class="row mb-1">
                        <label for="generate" class="col-sm-4 col-form-label-sm">Note:</label>
                        <div class="col-md-12">
                            <textarea name="note" id="note" rows="5" class="form-control mb-3"></textarea>
                        </div>
                    </div>
                    <p class="ms-directions text-center mb-1">FILES</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <div class="file-loading">
                                    <input id="nota_files" name="nota_files[]" type="file" multiple>
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
