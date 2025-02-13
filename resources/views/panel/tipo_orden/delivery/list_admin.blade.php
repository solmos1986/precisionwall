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
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
<link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<style>
    legend {
        font-size: 1.0rem;
    }

</style>
<style>
    .big-checkbox {
        width: 20px;
        height: 20px;
    }
</style>
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        @if(\Session::has('success'))
        <div class="alert alert-success">
            {{\Session::get('success')}}
        </div>
        @endif
        {{-- {{Breadcrumbs::render('Order(Mat. Eqp) /Status of Orders/Deliveres/Pick-ups')}} --}}
        <div class="ms-panel">
            <div class="ms-panel-header ms-panel-custome">
                <h6>Status of Deliveres/Pick-ups</h6>
            </div>
            <div class="ms-panel-body">
                <div class="container d-flex justify-content-center">
                    <div class="row">
                        <div class="col-md mb-3">
                            <input type="text" name="from_date" id="from_date"
                                class="form-control form-control-sm datepicke datepicke" placeholder="From Date"
                                autocomplete="off">
                        </div>
                        <div class="col-md mb-3">
                            <input type="text" name="to_date" id="to_date"
                                class="form-control form-control-sm datepicke datepicke" placeholder="To Date"
                                autocomplete="off">
                        </div>
                        <div class="col-md mb-3">
                            <select name="status" id="status" class="form-control form-control-sm select" multiple>
                                @foreach ($status as $val)
                                    <option value="{{ $val->id }}">{{ $val->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md mb-3">
                            <button type="button" class="btn btn-primary has-icon btn-sm mt-0" id="buscar"><i
                                    class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="list-orden" class="table thead-primary w-100">
                        <thead>
                            <tr>
                                <th>PO's</th>
                                <th>Project</th>
                                <th>Address</th>
                                <th>Name deliver</th>
                                <th>Status Deliver</th>
                                <th>Date </th>
                                <th width="90">Actions</th>
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
<!--Modal -->
<x-components.tipo-orden.list.transferencia.edit-deliver :proveedores='$proveedores' :status='$status' />

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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <button type="button"></button>
<script>
  var sub_order = $('#list-orden').DataTable( {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('order.list.datatable.delivery') }}",
        order: [],
        columns: [
            { data: "PO", name: "PO" },
            { data: "nombre_proyecto", name: "nombre_proyecto" },
            { data: "address", name: "address" },
            { data: "sub_empleoye", name: "sub_empleoye" },
            { data: "envio_status", name: "envio_status" },
            { data: "fecha_actividad", name: "fecha_actividad" },
            { data: 'acciones', name: 'acciones', orderable: false }
        ],
        columnDefs: [{
                width: "100px",
                targets: 0
            },
            {
                targets: 4,
            }
        ],
        pageLength: 100,
    });
    $(function(){
            $(".TodayTime").datetimepicker({  
                defaultDate: $('#TodayTime').val(),
                format: 'HH:mm:ss',
                timeFormat: 'HH:mm:ss',  
                pickDate: false,
                pickSeconds: false,
                pick12HourFormat: false,
                onSelect:function(datetimeText, datepickerInstance){
                    if (!datepickerInstance.timeDefined) {
                        $(".TodayTime").datetimepicker('hide')
                    }
                }
            })
        });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"
    integrity="sha512-s5u/JBtkPg+Ff2WEr49/cJsod95UgLHbC00N/GglqdQuLnYhALncz8ZHiW/LxDRGduijLKzeYb7Aal9h3codZA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
{{-- transferencia --}}
<script src="{{ asset('js/tipo-orden/list/transferencia/edit-deliver.js') }}"></script>
@endpush