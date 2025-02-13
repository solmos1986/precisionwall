@extends('layouts.panel')
@push('css-header')
<link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
<link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
<link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/tokenfield-typeahead.min.css')}}" type="text/css" rel="stylesheet">
<link href="{{ asset('css/bootstrap-tokenfield.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css"
    integrity="sha512-LT9fy1J8pE4Cy6ijbg96UkExgOjCqcxAC7xsnv+mLJxSvftGVmmc236jlPTZXPcBRQcVOWoK1IJhb1dAjtb4lQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        @if(\Session::has('success'))
        <div class="alert alert-success">
            {{\Session::get('success')}}
        </div>
        @endif
        {{Breadcrumbs::render('list order')}}
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>LIST STATUS</h6>
                <button class="btn btn-pill btn-primary btn-sm create_status">Create status</button>
            </div>
            <div class="ms-panel-body">
                <div class="table-responsive">
                    <table id="list-status" class="table thead-primary w-100">
                        <thead>
                            <tr>
                                <th>Status id</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th width="170">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<x-components.tipo-orden-status.edit />
<x-components.tipo-orden-status.create />
<!--Modal Eliminar -->
<x-components.delete-modal />
<!--Modal -->

@endsection
@push('datatable')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/locales/es.js') }}" type="text/javascript"></script>
<script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>
<script src="{{ asset('themes/explorer-fas/theme.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-tokenfield.min.js') }}" charset="UTF-8"></script>
<script type="text/javascript" src="{{ asset('js/typeahead.bundle.min.js') }}" charset="UTF-8"></script>
<script src="{{ asset('js/taginput_custom.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <button type="button"></button>
<script>
   
  var table = $('#list-status').DataTable( {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('status.orden.list.datatable.status') }}",
        order: [[ 1, "desc" ]],
        columns: [
            { data: "id", name: "id" },
            { data: "codigo", name: "codigo" },
            { data: "nombre", name: "nombre" },
            { data: "color", name: "color" },
            { data: 'acciones', name: 'acciones', orderable: false }
        ],
        pageLength: 100,
    });
    
    </script>
    <script type="text/javascript" src="{{ asset('js/tipo-orden-status/crud.js') }}"></script>
@endpush