<div id="modalEditOrden" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Request</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="fromEditOrden" method="POST" enctype="multipart/form-data"
                    action="{{ route('order.create') }}">
                    @csrf
                    <input type="hidden" name="edit_orden_id" id="edit_orden_id" value="">
                    <div class="form-group">
                        <label for="generate">Project:</label>
                        <select class="form-control form-control-sm" id="edit_proyect" name="edit_proyect"
                            style="width:50%" required></select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="job_name" class="col-sm-3 col-form-label col-form-label-sm">Project Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="edit_job_name"
                                        name="edit_job_name" placeholder="Job Name" required autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="sub_contractor"
                                    class="col-sm-3 col-form-label col-form-label-sm">Status:</label>
                                <div class="col-sm-9">
                                    <select name="edit_orden_status" id="edit_orden_status"
                                        class="form-control form-control-sm" style="width:100%" required>
                                        @foreach ($status as $estado)
                                        <option value="{{$estado->id}}" >{{$estado->nombre}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">Request Date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm TodayTime" id="edit_date_work"
                                        name="edit_date_work" placeholder="Date of Work"
                                        value="{{ date('m/d/Y H:i:s') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="new_date_order" class="col-sm-3 col-form-label col-form-label-sm">Request to Date:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm datepicke-time" id="
                                    new_date_order" name="new_date_order" placeholder="Date of Work"
                                        value="{{ date('m/d/Y H:i:s') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="edit_created_by" class="col-sm-3 col-form-label col-form-label-sm">Request by:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm" id="edit_created_by"
                                        name="edit_created_by" placeholder="Name by"
                                        value=""
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label for="date_work" class="col-sm-1 col-form-label col-form-label-sm">Note:</label>
                                <div class="col-sm-11">
                                    <textarea class="form-control" name="edit_orden_nota" id="edit_orden_nota" cols="1"
                                        rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-hover thead-light" id="edit_orden_materiales">
                                <thead>
                                    <tr>
                                        <th scope="col" width="125">Type/Status</th>
                                        <th scope="col" width="250">Material/Equipment</th>
                                        <th scope="col" width="80">Unity</th>
                                        <th scope="col" width="300">Spetial Note</th>
                                        <th scope="col"  width="90">Q. Request</th>
                                        <th>Q. Used</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="p-0">
                                            <button class="btn btn-sm btn-block btn-success mt-0 add-edit-material" type="button" >Add material/equipment</button>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td colspan="9" class="p-0">
                                            <button class="btn btn-sm btn-block btn-success mt-0 add-equipo" type="button" disabled>Add
                                                equipment</button>
                                        </td>
                                    </tr> --}}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm save_update_orden " type="button">Save</button>
                <button type="button" class="btn btn-danger btn-sm"  type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>