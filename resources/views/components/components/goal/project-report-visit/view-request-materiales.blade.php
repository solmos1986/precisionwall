<div id="modalCreateViewProjectMateriales" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <form id="fromVisitReportSuperficie" method="POST" enctype="multipart/form-data" action="">
                    @csrf
                    <input type="text" class="form-control form-control-sm" name="proyecto_id" id="proyecto_id"
                        hidden>
                    <div class="ms-panel">
                        <div class="ms-panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>List Surfaces: </label>
                                        <select name="superficie_id[]" id="superficie_id"
                                            class="form-control form-control-sm" multiple disabled >
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>List Order: </label>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover thead-primary w-100" id="orden_material">
                                            <thead>
                                                <tr>
                                                    <th>Project</th>
                                                    <th>Num Order</th>
                                                    <th>Status Order</th>
                                                    <th>Date Order</th>
                                                    <th>Note</th>
                                                    <th>Created by</th>
                                                   {{--  <th>Actions</th> --}}
                                                </tr>
                                            </thead>
                                           
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
               {{--  <button type="button" class="btn btn-success btn-sm " id="save_visit_report_superficie"
                    type="button">Save</button> --}}
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
