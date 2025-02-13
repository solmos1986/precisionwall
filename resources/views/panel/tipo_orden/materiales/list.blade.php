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
                <h6>LIST MATERIALS</h6>
                <!--button class="btn btn-pill btn-primary btn-sm create_status">Create status</button-->
            </div>
            <div class="ms-panel-body">
                <div class="container d-flex justify-content-center">
                    <div class="row">               
                        <div class="col-md mb-3">
                            <input type="text" name="material" id="material" class="form-control form-control-sm"
                                placeholder="Material" autocomplete="off" />
                        </div>
                        <div class="col-md mb-3">
                            <input type="text" name="proyecto" id="proyecto" class="form-control form-control-sm"
                                placeholder="Proyect" autocomplete="off" />
                        </div>
                        <div class="col-md mb-3">
                            <button type="button" name="refresh" id="refresh" class="btn btn-primary btn-sm mt-0"><i
                                    class="fas fa-retweet"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="list-materiales" class="table thead-primary w-100">
                        <thead>
                            <tr>
                                <th>Denominacion</th>
                                <th>Unit</th>
                                <th>Total Q. Ordered</th>
                                <th>Quantity</th>
                                <th>Q. store in</th>
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
    table_materiales = $('#list-materiales').DataTable( {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `${base_url}/materials/data-table`,
        order: [],
        columns: [
            { data: "Denominacion", name: "Denominacion" },
            { data: "Unidad_Medida", name: "Unidad_Medida" },
            { data: "total_ordenada", name: "total_ordenada" },
            { data: "total", name: "total" },
            { data: "ubicacion_proyecto", name: "ubicacion_proyecto" },
        ],
        columnDefs: [
            {
                width: "250px",
                targets: 0
            },
            {
                width: "50px",
                targets: 1
            },
            {
                width: "50px",
                targets: 2
            },
            {
                width: "50px",
                targets: 3
            },
            {
                width: "200px",
                targets: 4
            },
        ],
        pageLength: 100,
    });
    $('#proyecto, #material').change(function() {
        if ($("#to_date").val()=="") {
            $("#to_date").val($("#from_date").val());
        }
        table_materiales.ajax.url(`${base_url}/materials/data-table?proyecto=${$('#proyecto').val()}&material=${$('#material').val()}`).load();
        var rows = table_materiales.rows().data().toArray();
    });
    $('#refresh').click(function (e) { 
        $("#proyecto").val("");
        $("#material").val("");
        table_materiales.ajax.url(`${base_url}/materials/data-table`).load();
    });
</script>
@endpush