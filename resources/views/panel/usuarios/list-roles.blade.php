@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="invisible" id="status_crud"></div>
            {{Breadcrumbs::render('user roles')}}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome row">
                    <h6>User roles</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list-users" class="table table-striped thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Company</th>
                                    <th>Nick Name</th>
                                    <th>Name</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Type</th>
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
    <div id="formModal" class="modal fade" role="dialog" aria-hidden="true">
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
                            <label>Company: </label>
                            <select name="empresa_id" id="empresa_id" class="form-control form-control-sm" required>
                                @foreach ($empresas as $val)
                                    <option value="{{ $val->Emp_ID }}">{{ $val->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Roles: </label>
                            <select name="rol_id[]" id="rol_id" class="form-control form-control-sm" multiple required>
                                <option value="">select an option</option>
                                @foreach ($roles as $val)
                                    <option value="{{ $val->id }}">{{ $val->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name: </label>
                            <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Last name: </label>
                            <input type="text" name="Apellido_Paterno" id="Apellido_Paterno"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Mother's last name: </label>
                            <input type="text" name="Apellido_Materno" id="Apellido_Materno"
                                class="form-control form-control-sm" />
                        </div>

                        <div class="form-group">
                            <label>Nick Name: </label>
                            <input type="text" name="Nick_Name" id="Nick_Name" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Birthdate: </label>
                            <input type="date" name="Fecha_Nacimiento" id="Fecha_Nacimiento"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>email: </label>
                            <input type="email" name="email" id="email" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Telephone: </label>
                            <input type="text" name="Telefono" id="Telefono" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Cell phone: </label>
                            <input type="text" name="Celular" id="Celular" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>User: </label>
                            <input type="text" name="Usuario" id="Usuario" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Password: </label>
                            <input type="text" name="Password" id="Password" class="form-control form-control-sm" />
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
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $('#empresa_id').select2({
            theme: "bootstrap4",
            dropdownParent: $('#formModal')
        });

        $('#rol_id').select2({
            theme: "bootstrap4",
            dropdownParent: $('#formModal')
        });
        var table = $('#list-users').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searchDelay: 1000,
            ajax: "{{ route('listar.usuarios') }}",
            order: [
                [1, "asc"],
                [2, "asc"]
            ],
            columns: [{
                    data: "Empleado_ID",
                    name: "Empleado_ID"
                },
                {
                    data: "empresa",
                    name: "empresa"
                },
                {
                    data: "Nick_Name",
                    name: "Nick_Name"
                },
                {
                    data: "personal_nombre",
                    name: "personal_nombre"
                },
                {
                    data: "Usuario",
                    name: "Usuario"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "permisos",
                    name: "permisos"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100
        });

        $('#create_trabajo').click(function() {
            $('#formModal .modal-title').text('Add type of worker');
            $('#formModal #action').val('Add');
            $('#formModal #form_result').html('');
            $('#formModal #sample_form').trigger("reset");
            $('#formModal').modal('show');
        });

        $(document).on('click', '#save_button', function() {
            var action_url = '';
            if ($('#formModal #action').val() == 'Add') {
                action_url = "{{ route('store.usuarios') }}";
            }
            if ($('#formModal #action').val() == 'Edit') {
                action_url = `{{ url('update') }}/${$("#formModal #hidden_id").val()}/users`;
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
                url: `{{ url('edit') }}/${id}/users`,
                dataType: "json",
                success: function(data) {
                    console.log(data.roles);
                    $('#Nombre').val(data.result.Nombre);
                    $('#Apellido_Paterno').val(data.result.Apellido_Paterno);
                    $('#Apellido_Materno').val(data.result.Apellido_Materno);
                    $('#Nick_Name').val(data.result.Nick_Name);
                    $('#Fecha_Nacimiento').val(data.result.Fecha_Nacimiento);
                    $('#email').val(data.result.email);
                    $('#Telefono').val(data.result.Telefono);
                    $('#Celular').val(data.result.Celular);
                    $('#Usuario').val(data.result.Usuario);
                    $('#Password').val(data.result.Password);
                    $('#rol_id').val(data.roles).trigger('change');
                    $('#empresa_id').val(data.result.empresa_id).trigger('change');

                    $('#hidden_id').val(id);
                    $('#formModal .modal-title').text('Edit User');
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
                url: `{{ url('tipo_trabajo') }}/${$(this).data('id')}/destroy`,
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
