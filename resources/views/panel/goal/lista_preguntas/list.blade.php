@extends('layouts.panel')
@push('css-header')
<!-- Page Specific Css (Datatables.css) -->
<link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">

<link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css"/>
<link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css"/>
<link href="{{ asset('css/tokenfield-typeahead.min.css')}}" type="text/css" rel="stylesheet">
    <!-- Tokenfield CSS -->
<link href="{{ asset('css/bootstrap-tokenfield.min.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
<style>
 @media only screen and (min-width: 580px) {
   .modal-lg {
       max-width: 80% !important;
   }
 }
 .file-footer-buttons > .btn {
   padding: 0.625rem 1rem;
   min-width: 0!important;
   margin-top: 1rem;
 }
</style>@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    @if(\Session::has('success'))
    <div class="alert alert-success">
        {{\Session::get('success')}}
    </div>
    @endif
    <div class="invisible" id="status_crud"></div>
    {{Breadcrumbs::render('visit report')}}
    <div class="ms-panel">
      <div class="ms-panel-header ms-panel-custome">
        <h6>Question and answer list</h6>
        <button class="btn btn-pill btn-primary btn-sm create" data-tipo="problem">Create problem</button>
      </div>
        <div class="ms-panel-body">
          <div class="container d-flex justify-content-center">
          </div>
            <table id="list-orden" class="table thead-primary w-100">
              <thead>
                <tr>
                    <th>Description problem</th>
                    <th width="80">Actions</th>
                    <th>Description consequences</th>
                    <th width="80">Actions</th>
                    <th>Description solution</th>
                    <th width="80">Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
        </div>
    </div>
  </div>
</div>
 <!--Modal Eliminar -->
<x-components.delete-modal/>
{{-- crud preguntas --}}
<x-components.goal.question.create/>
<x-components.goal.question.edit/>
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
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/taginput_custom.js') }}"></script>
<script>
   var table = $('#list-orden').DataTable( {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('datatable.goal.preguntas') }}",
        order: [[ 0, "desc" ]],
        columns: [
          { data: "descripcion_problema", name: "descripcion_problema" },
          { data: "acciones_problema", name: "acciones_problema" },
          { data: "descripcion_consecuencia", name: "descripcion_consecuencia" },
          { data: "acciones_consecuencia", name: "acciones_consecuencia" },
          { data: "descripcion_solucion", name: "descripcion_solucion" },
          { data: "acciones_solucion", name: "acciones_solucion" },
        ],
        pageLength: 100
    });

</script>
<script src="{{ asset('js/goal/lista_preguntas/create.js') }}"></script>
<script src="{{ asset('js/goal/lista_preguntas/edit.js') }}"></script>
<script src="{{ asset('js/goal/lista_preguntas/delete.js') }}"></script>
<script src="{{ asset('js/upload_image.js') }}"></script>
@endpush
