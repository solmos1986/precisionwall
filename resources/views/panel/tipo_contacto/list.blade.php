@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="invisible" id="status_crud"></div>
            {{Breadcrumbs::render('tipo contacto')}}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome row">
                    <h6>Types contacts</h6>

                    <button type="button" name="create_razon" id="create_tipo" class="btn btn-pill btn-primary btn-sm">Add
                        new type contact</button>
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list_tipo_proyecto" class="table table-striped thead-primary w-100">
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
                    <span id="form_result"></span>
                    <form id="sample_form">
                        <div class="form-group">
                            <label>Type name : </label>
                            <input class="form-control" type="text" name="name" id="tipo">
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

    <!--Modal Eliminar -->
    <x-components.delete-modal />

@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
    <script>
        var table = $('#list_tipo_proyecto').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('get_list') }}",
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "id_tipo_contacto",
                    name: "id"
                },
                {
                    data: "nombre",
                    name: "name"
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
            pageLength: 100
        });

        $('#create_tipo').click(function() {
            $('#formModal .modal-title').text('Add new type contact');
            $('#formModal #action').val('Add');
            $('#formModal #form_result').html('');
            $('#formModal #sample_form').trigger("reset");
            $('#formModal').modal('show');
        });

        $(document).on('click', '#save_button', function() {
            var action_url = '';
            if ($('#formModal #action').val() == 'Add') {
                action_url = "{{ route('post_list') }}";
            }
            if ($('#formModal #action').val() == 'Edit') {
                action_url = `{{ url('update-contact') }}/${$("#formModal #hidden_id").val()}`;
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $("#formModal #sample_form").serialize(),
                dataType: "json",
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += `<p>${data.errors[count]}</p>`;
                        }
                        html += '</div>';
                        $('#formModal #form_result').html(html);
                    }
                    if (data.success) {
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
            var id = $(this).attr('id');
            $('#formModal #form_result').html('');
            $.ajax({
                url: `{{ url('show-contact') }}/${id}`,
                dataType: "json",
                success: function(data) {
                    $('#tipo').val(data.result.nombre);
                    $('#descripcion').val(data.result.descripcion);
                    $('#hidden_id').val(id);
                    $('#formModal .modal-title').text('Edit Razon Trabajo');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).data('id');
            $("#deleteModal #delete_button").data('id', id);
            $("#deleteModal").modal("show");
        });

        $(document).on('click', '#delete_button', function() {
            var id_i = $(this).data('id');
            $.ajax({
                type: "DELETE",
                url: `{{ url('delete-contact') }}/${$(this).data('id')}`,
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
