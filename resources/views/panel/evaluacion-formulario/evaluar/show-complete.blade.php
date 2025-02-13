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
            <div id="contenedor" class="row justify-content-md-center">
                <div class="col-md-10">
                    <div id="titulo" class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>NAME OF AMPLOYEE:</strong>{{$res_formulario->nombre_completo}}</p>

                                    <p><strong>DATE: </strong> {{$res_formulario->fecha_asignacion}}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div id="contenedor" class="row justify-content-md-center">
                <div class="col-md-10">
                    <div id="titulo" class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>{{$res_formulario->titulo}}</h6>
                                </div>
                                <div class="col-md-12">
                                    <p>{{$res_formulario->descripcion}}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @foreach ($res_formulario->secciones as $seccion)
                <div class="col-md-10" data-seccion="Subtitulo prueba 1" data-id="4">
                    <div id="titulo" class="ms-panel">
                        <div class="ms-panel-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><small>{{$seccion->subtitulo}}</small></h6>
                                </div>
                                <div class="col-md-12">
                                    <p>{{$seccion->descripcion}}</p>
                                </div>
                            </div>
                            <hr>
                            @foreach ($seccion->preguntas as $pregunta)
                            @if ($pregunta->tipo==="box")
                            <div class="row box" data-id="2">
                                <div class="col-md-12">
                                    <p style="font-size: 14px">{{$pregunta->pregunta}}</p>
                                    <br>
                                </div>
                                <div class="col-md-12">
                                    <ul class="ms-list ms-list-display">
                                        @foreach ($pregunta->respuestas as $respuesta)
                                        <li style="margin-bottom: 0.5rem;">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="radio" value="1" name="" {{$respuesta->respuesta =='ok' ? 'checked' : '' }} disabled >
                                                <i class="ms-checkbox-check" ></i>
                                            </label>
                                            <span style="font-size: 13px">{{$respuesta->val}}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                            @if ($pregunta->tipo==="check")
                            <div class="row checkbox" data-id="3">
                                <div class="col-md-12">
                                    <p style="font-size: 14px">{{$pregunta->pregunta}}</p>
                                    <br>
                                </div>
                                <div class="col-md-12">
                                    <ul class="ms-list ms-list-display">
                                        @foreach ($pregunta->respuestas as $respuesta)
                                        <li style="margin-bottom: 0.5rem;">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="checkbox" value="5" name="3[]" {{$respuesta->respuesta =='ok' ? 'checked' : '' }} disabled >
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <span style="font-size: 13px">{{$respuesta->val}}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                            @if ($pregunta->tipo==="text")
                            <div class="row text" data-id="4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleTextarea">
                                            <p style="font-size: 14px">{{$pregunta->pregunta}}</p>
                                        </label>
                                        @foreach ($pregunta->respuestas as $respuesta)
                                        <textarea name="9" class="form-control" id="exampleTextarea" rows="3" disabled
                                            required="">{{$respuesta->respuesta }}</textarea>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($pregunta->tipo==="escala")
                            <div class="row scale" data-id="5">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleTextarea">
                                            <p style="font-size: 14px">{{$pregunta->pregunta}}</p>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <p>Poor</p>
                                        </div>
                                        @foreach ($pregunta->respuestas as $respuesta)
                                        <div class="col-md-1">
                                            <label class="ms-checkbox-wrap ms-checkbox-primary">
                                                <input type="radio" value="10" name="" 
                                                disabled {{$respuesta->respuesta =='ok' ? 'checked' : '' }}>
                                                <i class="ms-checkbox-check"></i>
                                            </label>
                                            <span style="font-size: 13px">{{$respuesta->val}}</span>
                                        </div>
                                        @endforeach
                                        <div class="col-md-1">
                                            <p>Excellent</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </form>
    </div>
</div>
@endsection