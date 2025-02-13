@extends('layouts.panel')
@push('css-header')
<style>
    .centrar-bottons {
        width: 100px;
        display: flex;
        justify-content: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        @if (\Session::has('success'))
        <div class="alert alert-success">
            {{ \Session::get('success') }}
        </div>
        @endif
        <div class="invisible" id="status_crud"></div>
        <form action="{{ route('store.evaluar') }}" id="formulario" method="post">
            @csrf
            <input type="text" id="Empleado_ID" name="Empleado_ID" value="{{$personal->Empleado_ID}}" hidden>
            <input type="text" id="evaluacion_id" name="evaluacion_id" value="{{$personal->evaluacion_id}}" hidden>
            <input type="text" id="formulario_id" name="formulario_id" value="{{$personal->formulario_id}}" hidden>
            <input type="text" id="personal_evaluaciones_id" name="personal_evaluaciones_id" value="{{$personal->personal_evaluaciones_id}}" hidden>
            <div id="contenedor" class="row justify-content-md-center">
                <div class="col-md-10">
                    <div id="titulo" class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>NAME OF AMPLOYEE:</strong> {{$personal->nombre_completo}}</p>

                                    <p><strong>DATE: </strong> {{date('m/d/Y')}}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row justify-content-md-center">
                <button class="btn btn-success" id="guardar_form">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('datatable')
<script src="{{ asset('js/forms/show.js') }}"></script>
<script src="{{ asset('js/forms/save.js') }}"></script>
<script>
    var formularioId={{$formularioId}};
       
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: `${base_url}/show-form/${formularioId}`,
                dataType:'json',
                success: function (data) {
                    var preview=new View();
                    preview.inizialize(data);
                }
            
            });
           
        });
 
        $("#formulario").on('submit', function(evt) {
            evt.preventDefault();
            var save=new Save();
            const formulario=save.inizialize();
            $.ajax({
                type: "POST",
                url: `${base_url}/store-form-staff`,
                data:formulario,
                dataType:'json',
                success: function (data) {
                    if (data.errors) {
                        $alert = 'complete the following fields to continue:\n'
                        data.errors.forEach(function(error) {
                            $alert += `* ${error}\n`
                        })
                        alert($alert)
                    }
                    if (data.success) {
                        alert(data.success);
                        window.location.href = `${base_url}/list-staff/${$('#evaluacion_id').val()}`;
                    }
                }
            
            });
        })


</script>
@endpush