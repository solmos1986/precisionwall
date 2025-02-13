<div id="modalCreateOrden" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="fromCreateOrden" method="POST" enctype="multipart/form-data"
                    action="{{ route('order.store') }}">
                    @csrf
                    <input type="hidden" name="orden_id" value="">
                    <div class="form-group">
                        <label for="generate">Select Project:</label>
                        <select class="form-control form-control-sm" id="new_proyect" name="new_proyect"
                            style="width:50%" required></select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="job_name" class="col-sm-3 col-form-label col-form-label-sm">Project Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="new_job_name"
                                        name="new_job_name" placeholder="Job Name" required autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                <div class="col-sm-9">
                                    <select name="new_orden_status" id="new_orden_status"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="new_date_order" class="col-sm-3 col-form-label col-form-label-sm">Request Date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicke-time" id="
                                    new_date_order" name="new_date_order" placeholder="Date of Work"
                                        value="{{ date('m/d/Y H:i:s') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Request to Date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime" id="new_date_work"
                                        name="new_date_work" placeholder="Date of Work"
                                        value="{{ date('m/d/Y H:i:s') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="created_by" class="col-sm-3 col-form-label col-form-label-sm">Request by:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="created_by"
                                        name="created_by" placeholder="Name by"
                                        value="{{ auth()->user()->Nombre }} {{ auth()->user()->Apellido_Paterno }} {{ auth()->user()->Apellido_Materno }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <ul class="ms-list d-flex">
                                <li class="ms-list-item pl-0">
                                    <label class="ms-switch">
                                        <input class="detail" id="list_recojer" type="checkbox">
                                        <span class="ms-switch-slider round"></span>
                                    </label>
                                    <span>Pick Up Request</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-1 col-form-label col-form-label-sm">Note:</label>
                                <div class="col-sm-11">
                                    <textarea class="form-control" name="new_orden_nota" id="new_orden_nota" cols="1"
                                        rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-hover thead-light" id="table-material">
                                <thead>
                                    <tr>
                                        <th scope="col" width="125">Type/Status</th>
                                        <th scope="col" width="250">Material/Equipment</th>
                                        <th scope="col" width="80">Unit</th>
                                        <th scope="col" width="300">Spetial Note</th>
                                        <th scope="col" width="90">Q. Request</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="none_tr_mat">
                                        <td scope="row" colspan="9" class="text-center text-bold">I don't add anything</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="p-0">
                                            <button class="btn btn-sm btn-block btn-success mt-0 add-material" type="button" disabled>Add
                                                material/equipment</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <button type="button" name="refresh" id="view_create_orden_materiales"
                                class="btn btn-primary btn-sm mt-0"><i class="fas fa-eye-slash"></i> View
                                Materials</button>
                            <br>
                            <br>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_orden " type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm"  type="button" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="ocultar_create_orde_materiales" class="hide">
                            <p><strong>LIST MATERIALS:</strong></p>
                            <div class="container d-flex justify-content-center">
                                <div class="row">
                                    <div class="col-md mb-3">
                                        <input type="text" name="material" id="create_orden_material"
                                            class="form-control form-control-sm" placeholder="Material"
                                            autocomplete="off" />
                                    </div>
                                    <div class="col-md mb-3">
                                        <input type="text" name="proyecto" id="create_orden_proyecto"
                                            class="form-control form-control-sm" placeholder="Proyect"
                                            autocomplete="off" />
                                    </div>
                                    <div class="col-md mb-3">
                                        <button type="button" name="refresh" id="create_orden_refresh"
                                            class="btn btn-primary btn-sm mt-0"><i
                                                class="fas fa-retweet"></i></button>
                                    </div>
                                </div>
                            </div><br>
                            <div class="table-responsive">
                                <table id="create-orden-list-materiales" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th>Unit</th>
                                            <th>Project</th>
                                            <th>Total Q. Ordered</th>
                                            <th>Quantity</th>
                                            <th>Q. store in</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>