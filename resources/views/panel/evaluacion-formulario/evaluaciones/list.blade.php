@extends('layouts.panel')
@push('css-header')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
<link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
<link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        {{Breadcrumbs::render('lista evaluaciones')}}
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>List of Evaluations</h6>
                <a href="#" id="crear_evaluacion" class="btn btn-pill btn-primary btn-sm">New Evaluation</a>
            </div>
            <div class="ms-panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-pill btn-primary btn-sm m-1" href="{{ route('list.form') }}">
                            List form
                        </a>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table id="list-evaluations" class="table table-striped thead-primary w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Foreman</th>
                                <th>Note</th>
                                <th>Date of assignment</th>
                                <th>Assigned users</th>
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
<!--Create evaluacion -->
<x-components.evaluaciones.new :personal="$personal"/>
<x-components.evaluaciones.edit/>
<!--Modal Eliminar -->
<x-components.delete-modal />
@endsection
@push('datatable')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
<script>
    var table = $('#list-evaluations').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('list.evaluationes') }}",
            order: [
                [0, "desc"]
            ],
            columns: [
                {
                    data: "evaluacion_id",
                    name: "#"
                },
                {
                    data: "foreman_id",
                    name: "Foreman"
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
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100,
        });
</script>
<script src="{{ asset('js/listEvaluaciones.js') }}"></script>
@endpush