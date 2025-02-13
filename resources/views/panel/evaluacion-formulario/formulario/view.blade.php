@extends('layouts.panel')
@push('css-header')
    <style>
        .centrar-bottons {
            width: 100px;
            display: flex;
            justify-content: center;
        }

    </style>
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
            <form action="" method="post">
                <div id="contenedor" class="row justify-content-md-center">

                </div>
               
            </form>
        </div>
    </div>
@endsection
@push('datatable')
    <script src="{{ asset('js/forms/previewForm.js') }}"></script>

    <script>
       var preview=new View();
       preview.inizialize();
    </script>
@endpush
