<style>
    .tableFixHead {
        overflow: auto;
        height: 500px;
    }

    .tableFixHead thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    /* Just common table stuff. Really. */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 8px 16px;
    }

    .thead-primary thead th {
        background: #4eb0e9;
    }
</style>
<div id="modalViewMateriales" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <form id="form_materiales" method="POST" enctype="multipart/form-data" action="">
                    @csrf
                    <input type="text" class="form-control form-control-sm" name="view_superficie_id"
                        id="view_superficie_id" hidden>
                    <input type="text" class="form-control form-control-sm" name="proyecto_id" id="proyecto_id"
                        hidden>
                    <div class="ms-panel">
                        <div class="ms-panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Standards:</h6>
                                    <ul class="nav nav-tabs nav-justified has-gap" role="tablist" id="view_standares">
                                        
                                    </ul>
                                    
                                </div>
                                <div class="col-md-12 text-center">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-left">List Tools</h6>
                                        </div>
                                        <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                                            <button type="button" class="btn btn-primary btn-sm mb-1 mt-0 add_material"
                                                data-superficie_id="">
                                                Add Tool
                                            </button>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive tableFixHead">
                                                <table class="table table-hover thead-primary w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Type</th>
                                                            <th style="width:40%">Tool/Equipment</th>
                                                            <th>Unit</th>
                                                            <th>Quantity</th>
                                                            <th>Quantity Received </th>
                                                            <th>Action </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="materiales">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm " id="save_material_visit_report"
                    type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
