@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="invisible" id="status_crud"></div>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>List of Areas de Evaluations</h6>
                    <button id="create_area" class="btn btn-pill btn-primary btn-sm">New Area</button>
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list-evaluations" class="table table-striped thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Modal Eliminar -->
    <x-components.delete-modal />
    <div id="formModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="sample_form">
                        <div class="form-group">
                            <label>Name: </label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>Description: </label>
                            <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                        </div>
                        <input type="hidden" name="action" id="action" value="Add" />
                        <input type="hidden" name="hidden_id" id="hidden_id" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm" id="save_button">Save</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script>
        var table = $('#list-evaluations').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('list.areas.evaluations') }}",
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "how_areas_id",
                    name: "how_areas_id"
                },
                {
                    data: "nombre",
                    name: "nombre"
                },
                {
                    data: "descripcion",
                    name: "descripcion"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
        });
        $('#create_area').click(function() {
            $('#formModal .modal-title').text('Add Evaluation Area');
            $('#formModal #action').val('Add');
            $('#formModal #sample_form').trigger("reset");
            $('#formModal').modal('show');
        });

        $(document).on('click', '#save_button', function() {
            var action_url = '';
            if ($('#formModal #action').val() == 'Add') {
                action_url = "{{ route('store.areas.evaluations') }}";
            }
            if ($('#formModal #action').val() == 'Edit') {
                action_url = "{{ route('update.areas.evaluations') }}";
            }
            $.ajax({
                url: action_url,
                method: "POST",
                data: $("#formModal #sample_form").serialize(),
                dataType: "json",
                success: function(data) {
                    if (data.errors) {
                        var $alert = "complete the following fields to continue:\n";
                        data.errors.forEach(function(error) {
                            $alert += `* ${error}\n`;
                        });
                        alert($alert);
                    }
                    if (data.success) {
                        console.log(data.success);
                        html = `<div class="alert alert-success">${data.success}</div>`;
                        $('#formModal #sample_form').trigger("reset");
                        table.draw();
                        $('#status_crud').html(html);
                        $('#formModal').modal('hide');
                        $('#status_crud').addClass('visible').removeClass('invisible');
                    }
                }
            });
        });

        $(document).on('click', '.edit', function() {
            var id = $(this).data('id');
            console.log(id);
            $.ajax({
                url: `{{ url('areas-evaluations') }}/${id}/edit`,
                dataType: "json",
                success: function(data) {
                    $('#nombre').val(data.result.nombre);
                    $('#descripcion').val(data.result.descripcion);
                    $('#hidden_id').val(id);
                    $('#formModal .modal-title').text('Edit Evaluation Area');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        });

        $(document).on('click', '.delete', function() {
            let id = $(this).data('id');
            $("#deleteModal #delete_button").data('id', id);
            $("#deleteModal").modal("show");
        });

        $(document).on('click', '#delete_button', function() {
            let id = $(this).data('id');
            $.ajax({
                type: "DELETE",
                url: `{{ url('areas-evaluations') }}/${id}/destroy`,
                dataType: "json",
                success: function(data) {
                    var html = '';
                    if (data.success) {
                        html = `<div class="alert alert-success">${data.success}</div>`;
                        table.draw();
                        $('#status_crud').html(html);
                        $('#status_crud').addClass('visible').removeClass('invisible');
                        $('#deleteModal').modal('hide');
                    }
                }
            });
        });

    </script>
@endpush
