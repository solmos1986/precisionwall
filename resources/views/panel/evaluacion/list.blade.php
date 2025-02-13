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
                    <h6>List of Evaluations</h6>
                    <a href="{{ route('evaluation.new') }}" class="btn btn-pill btn-primary btn-sm">New Evaluation</a>
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list-evaluations" class="table table-striped thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>N° Questions</th>
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
            ajax: "{{ route('list.evaluations') }}",
            order: [
                [0, "desc"]
            ],
            columns: [{
                    data: "evaluation_form_id",
                    name: "evaluation_form_id"
                },
                {
                    data: "titulo",
                    name: "titulo"
                },
                {
                    data: "descripcion",
                    name: "descripcion"
                },
                {
                    data: "questions_count",
                    name: "questions_count"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false
                }
            ],
            pageLength: 100,
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
                url: `{{ url('delete') }}/${$(this).data('id')}/evaluations`,
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
