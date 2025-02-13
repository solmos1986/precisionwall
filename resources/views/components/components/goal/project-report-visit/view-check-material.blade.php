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
<div id="modalCreateViewMateriales" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header color-modal">
                <h5 class="modal-title" id="title_modal_check_list"></h5>
                <button type="button" class="close" style="color:black" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background: rgb(242, 242, 255);">
                <form id="fromVisitCheckMaterial" method="POST" enctype="multipart/form-data" action="">
                    @csrf
                    <input type="text" class="form-control form-control-sm" name="proyecto_id" id="orden_proyecto_id"
                        hidden>
                    <input type="text" class="form-control form-control-sm" name="fecha_registro" id="fecha_registro"
                        hidden>
                    <div class="ms-panel">
                        <div class="ms-panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="date_work"
                                                    class="col-sm-2 col-form-label col-form-label-sm">User:</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="user" name="user" placeholder="User"
                                                        autocomplete="off" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="date_work"
                                                    class="col-sm-3 col-form-label col-form-label-sm">Name
                                                    Project:</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nombre_proyecto" name="nombre_proyecto"
                                                        placeholder="Name Project" value="" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="date_work"
                                                    class="col-sm-5 col-form-label col-form-label-sm">Request to
                                                    Date:</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm TodayTime"
                                                        id="fecha_envio" name="fecha_envio" placeholder="Date of Work"
                                                        value="" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group row">
                                                <label for="date_work"
                                                    class="col-sm-1 col-form-label col-form-label-sm">Note:</label>
                                                <div class="col-sm-11">
                                                    <textarea class="form-control" name="nota" id="nota" cols="1" rows="1">visit report: </textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>List Tools: </label>
                                    </div>
                                    <div class="table-responsive tableFixHead">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="background: #4eb0e9; color:white">
                                                        <input type="checkbox" id="view_pdf_all" class="check"
                                                            name="check" value=""
                                                            style="transform: scale(1.5);">
                                                    </th>
                                                    <th style="background: #4eb0e9; color:white">Tools/Equipment</th>
                                                    <th style="background: #4eb0e9; color:white">Unit</th>
                                                    <th style="background: #4eb0e9; color:white">Suggested Quantity</th>
                                                    <th style="background: #4eb0e9; color:white">Quantity</th>
                                                    <th style="background: #4eb0e9; color:white">Quantity Received </th>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm " id="save_visit_check_material"
                    type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm border border-light" type="button"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
