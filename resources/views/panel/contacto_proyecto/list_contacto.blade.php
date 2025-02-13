@extends('layouts.panel')
@push('css-header')
<!-- Page Specific Css (Datatables.css) -->
 <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
 <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
 <link rel="stylesheet" href="{{asset('css/select2.min.css')}}" />
 <link rel="stylesheet" href="{{asset('css/select2-bootstrap4.min.css')}}" />
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="invisible" id="status_crud"></div>
    {{Breadcrumbs::render('proyectos',$proyecto->Pro_ID)}}
    <div class="ms-panel">
        <div class="ms-panel-header ms-panel-custome">
          <h6>Contacts - Proyect: {{ $proyecto->Nombre }} - ID: {{ $proyecto->Pro_ID }}</h6>
          <button type="button" id="create_contacto" class="btn btn-pill btn-primary btn-sm">Add Contacto</button>
        </div>
        <div class="ms-panel-body">
          <div class="table-responsive">
            <table id="list-contactos" class="table table-striped thead-primary w-100">
              <thead>
                <tr>
                    <th>#</th>
                    <th>Company</th>                  
                    <th>Nick_Name</th>                  
                    <th>Name</th>
                    <th>User</th>                  
                    <th>Email</th>                  
                    <th>Type</th>                  
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
<div id="formModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span id="form_result"></span>
        <form id="sample_form">
          <div class="form-group">
            <label>Select Personnel: </label>
            <select id="empleoye" class="form-control form-control-sm" required></select>
            <input type="hidden" name="empleado_id" id="empleado_id">
          </div>
          <div class="form-group">
            <label>Contact Type: </label>
            <select name="tipo" id="tipo" class="form-control form-control-sm" required>
              @foreach($tipo_contacto as $tipo)
              <option value="{{$tipo->id_tipo_contacto}}">{{$tipo->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Email Contact: </label>
            <input type="text" name="email_p" id="email_p" class="form-control form-control-sm" required>
          </div>
            <input type="hidden" name="action" id="action" value="Add" />
            <input type="hidden" name="hidden_id" id="hidden_id" />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="save_button">Save</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
 <!--Modal Eliminar -->
<x-components.delete-modal/>

@endsection
@push('datatable')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script>
  var table = $('#list-contactos').DataTable( {
      processing: true,
      serverSide: true,
      responsive: true,
      searchDelay: 1000,
      order: [[ 1, "asc" ],[ 2, "asc" ]],
      ajax: "{{ route('listar.contactos',['id' => $id]) }}",
      columns: [
        { data: "Empleado_ID", name: "Empleado_ID"},
        { data: "empresa", name: "empresa" },
        { data: "Nick_Name", name: "Nick_Name" },
        { data: "personal_nombre", name: "personal_nombre" },
        { data: "Usuario", name: "Usuario" },
        { data: "email", name: "email" },
        { data: "nombre_tipo", name: "tipo" },
        { data: 'acciones', name: 'acciones', orderable: false }
      ],
  });

  $('#create_contacto').click(function(){
    $('#formModal .modal-title').text('Create Contact');
    $('#formModal #action').val('Add');
    $('#formModal #form_result').html('');
    $('#formModal #sample_form').trigger("reset");
    $('#formModal').modal('show');
  });

  $(document).on('click','#save_button', function () {
    var action_url = '';
    if($('#formModal #action').val() == 'Add'){
      action_url = "{{ route('store.contacto',['id' => $id]) }}";
    }
    if($('#formModal #action').val() == 'Edit'){
      action_url = "{{ route('update.contacto',['id' => $id]) }}";
    }
    $.ajax({
      url: action_url,
      method:"POST",
      data:$("#formModal #sample_form").serialize(),
      dataType:"json",
      success:function(data)
      {
        var html = '';
        if(data.errors){
          html = '<div class="alert alert-danger">';
          for(var count = 0; count < data.errors.length; count++){
            html += `<p>${data.errors[count]}</p>`;
          }
          html += '</div>';
          $('#formModal #form_result').html(html);
        }
        if(data.success){
          html = `<div class="alert alert-success">${data.success}</div>`;
          $('#formModal #sample_form').trigger("reset");
          table.draw();
          $('#status_crud').html(html);
          $('#formModal').modal('hide');
          $('#status_crud').addClass('visible').removeClass('invisible');
        }
      }
    });
  });

  $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    $('#formModal #form_result').html('');
    $.ajax({
      url : `{{ url('edit') }}/${id}/contactos`,
      dataType:"json",
      success:function(data)
      {
        var $newOption = $("<option selected='selected'></option>").val(data.result.Empleado_ID).text(data.result.nombre_completo)
        $("#empleoye").append($newOption).trigger('change');
        $('#empleado_id').val(data.result.Empleado_ID);
        $('#tipo').val(data.result.id_tipo_contacto);
        $('#email_p').val(data.result.email);
        $('#hidden_id').val(id);
        $('#formModal .modal-title').text('Edit Razon Trabajo');
        $('#action').val('Edit');
        $('#formModal').modal('show');
      }
    })
  });

  $(document).on('click', '.delete', function () {
    var id = $(this).data('id');
    $("#deleteModal #delete_button").data('id',id);
    $("#deleteModal").modal("show"); 
  });

  $(document).on('click', '#delete_button', function () {
    var id_i = $(this).data('id');
    $.ajax({
      type: "DELETE",
      url: `{{ url('destroy') }}/${$(this).data('id')}/contactos`,
      dataType: "json",
      success: function (data) {
        var html = '';
        if(data.success){
          html = `<div class="alert alert-success">${data.success}</div>`;
          table.draw();
          $('#status_crud').html(html);
          $('#status_crud').addClass('visible').removeClass('invisible');
          $('#deleteModal').modal('hide');
        }
      }
    });
  });
  $('#empleoye').select2({
        theme: "bootstrap4",
        dropdownParent: $('#formModal'),
        ajax: { 
            url: `{{ route('get_empleoyes.contacto',['id' => $proyecto->Emp_ID]) }}`,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        $("#email_p").val(e.params.data['email']);
        $("#empleado_id").val(e.params.data['id']);
    });
</script>
@endpush