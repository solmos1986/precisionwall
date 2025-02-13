@extends('layouts.panel')
@push('css-header')
<!-- Page Specific Css (Datatables.css) -->
 <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="ms-panel">
        <div class="ms-panel-header ms-panel-custome">
          <h6>Materials</h6>
          <a class="btn btn-pill btn-primary btn-sm" href="{{ route('materiales.create') }}">Add Materials</a>
        </div>
        <div class="ms-panel-body">
          <div class="table-responsive">
            <table id="list-material" class="table table-striped thead-primary w-100">
              <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Price</th>
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
<!-- Large modal -->
<div class="modal fade bs-example-modal-lg" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span></button>
        <h4 class="modal-title" id="myModalLabel">Remove material</h4>
      </div>
      <div class="modal-body">
        <p>You sure want to delete the selected record</p>
      </div>
      <div class="modal-footer">  
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>             
      </div>
    </div>
  </div>
</div>
<!-- Large modal -->
@endsection
@push('datatable')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script>
    $('#list-material').DataTable( {
        processing: true,
        serverSide: true,
        ajax: "{{ route('listar.materiales') }}",
        order: [[ 0, "desc" ]],
        columns: [
            { data: "Mat_ID", name: "Mat_ID"},
            { data: "Denominacion", name: "Denominacion" },
            { data: "Categoria", name: "Categoria" },
            { data: "Unidad_Medida", name: "Unidad_Medida" },
            { data: "Precio_Unitario", name: "Precio_Unitario", "defaultContent": "-"},
            { data: 'acciones', name: 'acciones', orderable: false }
        ],
    });
</script>
@endpush
