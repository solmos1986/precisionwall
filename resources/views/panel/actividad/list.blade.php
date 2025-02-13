@extends('layouts.panel')
@push('css-header')
    <!-- Page Specific Css (Datatables.css) -->
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            {{Breadcrumbs::render('activities')}}
            <div class="ms-panel">
                <div class="ms-panel-header">
                    <h6>Activities</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="container d-flex justify-content-center">
                        <div class="row">
                            <div class="col-md mb-3">
                                <input type="date" name="from_date" id="from_date" class="form-control form-control-sm"
                                    placeholder="From Date" value="{{ date('Y-m-d') }}" />
                            </div>
                            <div class="col-md mb-3">
                                <button type="button" name="refresh" id="refresh" class="btn btn-primary btn-sm mt-0"><i
                                        class="fas fa-retweet"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list-proyect" class="table table-striped thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>Direction</th>
                                    <th>Contact</th>
                                    <th>Foreman</th>
                                    <th>Lead</th>
                                    <th>Pwt super</th>
                                    <th>Employees</th>
                                    <th>Tickets</th>
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
        var table = $('#list-proyect').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `{{ url('activities') }}?from_date=${$('#from_date').val()}`,
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "Fecha",
                    name: "Fecha"
                },
                {
                    data: "empresa",
                    name: "empresa"
                },
                {
                    data: "Nombre",
                    name: "Nombre"
                },
                {
                    data: "direccion",
                    name: "direccion"
                },
                {
                    data: "Coordinador_Obra",
                    name: "Coordinador_Obra"
                },
                {
                    data: "Foreman",
                    name: "Foreman"
                },
                {
                    data: "Lead",
                    name: "Lead"
                },
                {
                    data: "Pwtsuper",
                    name: "Pwtsuper"
                },
                {
                    data: "empleados",
                    name: "empleados"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100
        });
        $('#from_date').change(function() {

            table.ajax.url(`{{ url('activities') }}?from_date=${$('#from_date').val()}`).load();
        });
        $('#refresh').click(function(e) {
            e.preventDefault();
            $("#from_date").val("{{ date('Y-m-d') }}");
            table.ajax.url(`{{ url('activities') }}?from_date=${$('#from_date').val()}`).load();
        });

    </script>
    <!--script src="{{ asset('js/toas_alerta.js') }}"></script-->
@endpush
