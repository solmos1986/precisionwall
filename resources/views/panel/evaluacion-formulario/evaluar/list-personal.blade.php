@extends('layouts.panel')
@push('css-header')
<link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
<style>
    @media only screen and (min-width: 580px) {
        .modal-lg {
            max-width: 80% !important;
        }
    }

    .file-footer-buttons>.btn {
        padding: 0.625rem 1rem;
        min-width: 0 !important;
        margin-top: 1rem;
    }
</style>@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        @if (\Session::has('success'))
        <div class="alert alert-success">
            {{ \Session::get('success') }}
        </div>
        @endif
        <div class="invisible" id="status_crud"></div>
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6># {{ $evaluacion->note }}</h6>
            </div>
            <div class="ms-panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="invoice-address">
                            <h5><strong>From title:</strong></h5>
                            <p>
                                {{ $evaluacion->titulo }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="invoice-address">
                            <h5><strong>Date of assignment:</strong></h5>
                            <p>
                                {{ $evaluacion->fecha_asignacion }}
                            </p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="invoice-address">
                    <h5><strong>Staff to evaluate:</strong></h5>
                </div>
                <table id="list_personal" class="table thead-primary w-100">
                    <thead>
                        <tr>
                            <th>Staff id</th>
                            <th>Full name</th>
                            <th>Nickname</th>
                            <th>Position</th>
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

@endsection
@push('datatable')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
<script>
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var table = $('#list_personal').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('list.personal.evaluar', $evaluacion->evaluacion_id ) }}/",
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "Empleado_ID",
                    name: "Staff id"
                },
                {
                    data: "nombre_completo",
                    name: "Full name"
                },
                {
                    data: "Nick_Name",
                    name: "Nickname"
                },
                {
                    data: "Cargo",
                    name: "Position"
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
        });

</script>

@endpush