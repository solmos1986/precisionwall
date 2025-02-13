@extends('layouts.panel')
@push('css-header')
<link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>List of Evaluations Pending</h6>
            </div>
            <div class="ms-panel-body">
                <div class="table-responsive">
                    <table id="list-evaluations" class="table table-striped thead-primary w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Note</th>
                                <th>Date of assignment</th>
                                <th>Assigned users</th>
                                <th>Status</th>
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
            ajax: "{{ route('list.evaluar') }}",
            order: [
                [0, "desc"]
            ],
            columns: [
                {
                    data: "evaluacion_id",
                    name: "#"
                },
                {
                    data: "note",
                    name: "Note"
                },
                {
                    data: "fecha_asignacion",
                    name: "Date of assignment"
                },
                {
                    data: "ver_usuarios",
                    name: "Assigned users"
                },
                {
                    data: "status",
                    name: "Status"
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