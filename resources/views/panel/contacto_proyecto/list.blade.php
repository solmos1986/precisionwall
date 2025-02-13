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
            {{Breadcrumbs::render('contacto proyectos')}}
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Contacto Proyectos</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list-proyecto" class="table table-striped thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Direction</th>
                                    <th>N° Contactos</th>
                                    <th>Contacts</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
        var table = $('#list-proyecto').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('listar.proyecto_contacto') }}",
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "Pro_ID",
                    name: "Pro_ID"
                },
                {
                    data: "Nombre",
                    name: "Nombre"
                },
                {
                    data: "Codigo",
                    name: "Codigo"
                },
                {
                    data: "direccion",
                    name: "direccion"
                },
                {
                    data: "num_contac",
                    name: "num_contac"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100,
        });

    </script>
@endpush
