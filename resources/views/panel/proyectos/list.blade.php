@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            {{ Breadcrumbs::render('proyects') }}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>List of Projects/:</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="container d-flex justify-content-center">
                        <div class="row">
                            <div class="col-md mb-4">
                                <label for="gc">GC-Company:</label>
                                <select name="gc" id="gc" class="form-control form-control-sm select">
                                    <option value="">select an option</option>
                                    @foreach ($empresas as $val)
                                        <option value="{{ $val->Nombre }}">{{ $val->Nombre }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md mb-4">
                                <label for="status">Status:</label>
                                <select name="status" id="status" class="form-control form-control-sm select">
                                    <option value="">select an option</option>
                                    @foreach ($status as $val)
                                        <option value="{{ $val->Nombre_Estatus }}" {{-- {{$val->Nombre_Estatus=='In Progress' ? 'selected' : ''}} --}}>{{ $val->Nombre_Estatus }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list-proyectos" class="table table-striped thead-primary">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th width="150">Name Project</th>
                                    <th width="100">Start Date</th>
                                    <th width="100">End Date</th>
                                    <th width="70">Hrs.Con</th>
                                    <th width="150">Action</th>
                                    <th width="150">GC - Company</th>
                                    <th width="70">Status</th>
                                    <th width="70">Type</th>
                                    <th width="200">Address</th>
                                    <th width="120">GC Project Manager</th>
                                    <th width="100">GC Superintendent</th>
                                    <th width="150">Project Manager PWT</th>
                                    <th width="170">Project Coordinator PWT</th>
                                    <th width="100">Foreman PWT</th>
                                    <th width="100">PWT Asistant Proyect</th>
                                    <th width="150">Name Project</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">List of Stages:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="stage" class="table table-sm">
                        <thead>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Hours</th>
                            <th>Note</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            $('.select').select2();
            var dataTable = $("#list-proyectos").DataTable({
                processing: true,
                serverSide: true,
                order: [
                  
                ],
                scrollY: "350px",
                scrollX: true,
                scrollCollapse: true,
                ajax: "{{ route('listar.datatable.proyectos') }}",
                language: {
                    searchPlaceholder: "Criterion"
                },
                columns: [{
                        data: "Codigo",
                        name: "Codigo",
                    },
                    {
                        data: "Nombre",
                        name: "Nombre"
                    },
                    {
                        data: 'Fecha_Inicio',
                        name: 'Fecha_Inicio',
                        type: 'date', 
                    },
                    {
                        data: 'Fecha_Fin',
                        name: 'Fecha_Fin',
                        type: 'date', 
                    },
                    {
                        data: 'Horas',
                        name: 'Horas',
                    },
                    {
                        data: "campo",
                        name: "campo",
                        orderable: false,
                        Searchable: false 
                    },
                    {
                        data: "empresa",
                        name: "empresa"
                    },
                    {
                        data: "estatus",
                        name: "estatus",
                        orderable: false,
                
                    },
                    {
                        data: 'tipo',
                        name: 'tipo',
                    },
                    {
                        data: 'direccion',
                        name: 'direccion',
                    },
                    {
                        data: 'Project_Manager',
                        name: 'Project_Manager',
                    },
                    {
                        data: 'Coordinador_Obra',
                        name: 'Coordinador_Obra',
                    },
                    {
                        data: 'Manager',
                        name: 'Manager',
                    },
                    {
                        data: 'Cordinador',
                        name: 'Cordinador',
                    },
                    {
                        data: 'Foreman',
                        name: 'Foreman',
                    },
                    {
                        data: "Asistant_Proyect_ID",
                        name: "Asistant_Proyect_ID"
                    },
                    {
                        data: "Nombre",
                        name: "Nombre"
                    },
                ],
                pageLength: 100,
            });

            $("#list-proyectos").on("draw.dt", function() {
                $("#list-proyectos").Tabledit({
                    editButton: false,
                    deleteButton: false,
                    restoreButton: false,
                    url: "{{ route('update.proyectos') }}",
                    dataType: "json",
                    columns: {
                        identifier: [0, "Codigo"],
                        editable: [
                            [2, "Fecha_Inicio", , 'text'],
                            [3, "Fecha_Fin", , 'text'],
                            [4, "Horas", , 'number'],
                        ],
                    },

                    onSuccess: function(data, textStatus, jqXHR) {
                        if (data.action == "delete") {
                            $("#" + data.id).remove();
                            $("#list-proyectos").DataTable().ajax.reload();
                        }
                    },
                    onDraw: function() {
                        console.log('onDraw()');
                        $('table tr td:nth-child(3) input, table tr td:nth-child(4) input')
                            .each(function() {
                                $(this).datepicker({
                                    todayHighlight: true,
                                    dateFormat: "mm/dd/yy"
                                });
                            });
                        $('.change_asistente_proyecto').select2();
                    },
                });
            });
            $(document).on('change', '.change_reg', function() {
                var select = $(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('store.stages') }}",
                    data: {
                        dato: select.val(),
                        id: select.find(':selected').data('id'),
                    },
                    dataType: "json",
                    async: false,
                    success: function(response) {
                        $("#modal").modal("show");
                        $('#stage tbody').html("");
                        var trHTML = '';
                        $.each(response.data, function(i, item) {
                            trHTML += '<tr><td>' + item.Nombre + '</td><td>' + item
                                .Fecha_Inicio + '</td><td>' + item.Fecha_Fin +
                                '</td><td>' + item.Horas + '</td><td>' + item.Note +
                                '</td></tr>';
                        });
                        if (response.alert) {
                            alert(response.alert);
                        }
                        $('#stage tbody').append(trHTML);
                    }
                });
            });

            $('#gc, #status').change(function() {
                dataTable.ajax.url(
                    `{{ url('data-table-proyectos') }}?gc=${$('#gc').val()}&status=${$('#status').val()}`
                ).load();
            });

            //cambio de estatus en el datatable 
            $(document).on('change', '.change_status', function() {
                var select = $(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('proyectos.update.status') }}",
                    data: {
                        dato: select.val(),
                        id: select.find(':selected').data('id'),
                    },
                    dataType: "json",
                    async: false,
                    success: function(response) {
                        if (response.alert) {
                            alert(response.alert);
                        }
                    }
                });
            });
            //cambio de asistente proyecto en el datatable 
            $(document).on('change', '.change_asistente_proyecto', function() {
                var select = $(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('proyectos.update.asistente') }}",
                    data: {
                        dato: select.val(),
                        id: select.find(':selected').data('id'),
                    },
                    dataType: "json",
                    async: false,
                    success: function(response) {
                        if (response.alert) {
                            alert(response.alert);
                        }
                    }
                });
            });
        });
    </script>
@endpush
