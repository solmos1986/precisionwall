@extends('layouts.panel')
@push('css-header')
<!-- Page Specific Css (Datatables.css) -->
 <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
 <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">

@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="ms-panel">
        <div class="ms-panel-header">
          <h6>Reports</h6>
        </div>
        <div class="ms-panel-body">
        <div class="ms-panel-body">
            <form id="from_ticket" >
                @csrf
               
                <p class="ms-directions">Report</p>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                          <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm">search by date</label>
                          <div class="col-sm-12">
                            <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="date_work" placeholder="Date of Work" value="">
                            <label for="date_work" class="col-sm-3 col-form-label col-form-label-sm" alig>to</label>
                            <input type="date" class="form-control form-control-sm" id="date_work" name="date_work" placeholder="Date of Work" value="">                            </div>
                          </div>
                        <div class="form-group row">
                            <label for="what" class="col-sm-2 col-form-label col-form-label-sm">search per name</label>
                            <div class="col-sm-10">
                              <select name="" id="buscar" class="form-control form-control-sm"></select>
                            </div>
                        </div>
                        <!--tipo-->
                        <ul class="ms-list d-flex">
                            <li class="ms-list-item pl-0">
                              <label class="ms-checkbox-wrap">
                                <input id="ticket" type="radio" name="horario" value="ticket" >
                                <i class="ms-checkbox-check"></i>
                              </label>
                              <span> Ticket </span>
                            </li>
                            <li class="ms-list-item">
                              <label class="ms-checkbox-wrap">
                                <input id="actividad" type="radio" name="horario" value="actividad" >
                                <i class="ms-checkbox-check"></i>
                              </label>
                              <span> Task </span>
                            </li>
                            <li class="ms-list-item">
                              <label class="ms-checkbox-wrap">
                                <input id="proyecto" type="radio" name="horario" value="proyecto" >
                                <i class="ms-checkbox-check"></i>
                              </label>
                              <span> Proyect </span>
                            </li>
                            <li class="ms-list-item">
                                <label class="ms-checkbox-wrap">
                                  <input id="empresas" type="radio" name="horario" value="empresas" >
                                  <i class="ms-checkbox-check"></i>
                                </label>
                                <span> Company </span>
                              </li>
                          </ul>
                    </div>
                </div>
              </form>
              <button class="btn btn-primary d-block" id="create">create report</button>
                <br>
                <p class="ms-directions">PREVIEW:</p>
                <div class="embed-responsive embed-responsive-16by9">
                  <object id="pdf" class="embed-responsive-item" data="http://localhost:8000/pdf_ticket"
                      type="application/pdf" internalinstanceid="9" title="">
                      <p>
                        Your browser isn't supporting embedded pdf files. You can download the file
                        <a href="example.pdf">here</a>
                      </p>
                  </object>
                </div>
            

        </div>
        </div>
    </div>
  </div>

</div>

@endsection
@push('javascript-form')
<script src="{{asset('js/select2.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script src="{{ asset('assets/js/moment.js') }}"> </script>
<script>
    var fecha_inicio;
    var fecha_fin;
    var id='';
    var general='';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $( "#create" ).click(function() {
      var ticker= $("#ticket").val();
      if($("#ticket").is(':checked')) {  
            alert("Está activado ticket");  
            $('#pdf').attr('data', `http://localhost:8000/pdf_ticket`);
        } else {  
            if ($("#actividad").is(':checked')) {
              alert("Está activado actividad");  
              $('#pdf').attr('data', `{{ url('report.task')}}`);
            } else{
              if ($("#proyecto").is(':checked')) {
                alert("Está activado proyecto");  
              } else{
                if ($("#empresas").is(':checked')) {                
                  fecha_inicio=$('#fecha_inicio').val();
                  alert(fecha_inicio);  
                  $('#pdf').attr('data',`{{ url('report_ticket')}}`);
                }else{
                  alert("nada activado"); 
                }
              }
            }
        }
    });
    /*$('#buscar').select2({
                    ajax:{
                      url:"{{ url('get.ticket')}}/ff"
                      type: "get",
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
                  });*/
    
</script>
@endpush