@extends('layouts.panel')
@push('css-header')
    <style>
        #signature-pad {
            min-height: 200px;
            border: 1px solid #000;
        }

        #signature-pad canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: #fff;
        }
    </style>
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">

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
            <div class="invisible" id="statubs_crud"></div>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome p-3">
                    <h6 class="mt-2">List Position</h6>
                    @if (Auth::user()->verificarRol([1,5]))
                        <button class="btn btn-pill btn-primary btn-sm m-0" id="create_position">
                            Create Position</button>
                    @endif
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list_position" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome p-3">
                    <h6 class="mt-2">Type of Employee</h6>
                    @if (Auth::user()->verificarRol([1,5]))
                        <button class="btn btn-pill btn-primary btn-sm m-0 mb-2" id="create_tipo_empleado">
                            Create Type Employee</button>
                    @endif
                </div>
                <div class="ms-panel-body">
                    <div class="table-responsive">
                        <table id="list_tipo_personal" class="table thead-primary w-100">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
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
    <!--Modal -->
    <x-components.cardex.otros.modal-cargo />
    <x-components.cardex.otros.modal-tipo-personal />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="{{ asset('js/cardex/otros/list.js') }}"></script>

    <script src="{{ asset('js/cardex/otros/cargo/store.js') }}"></script>
    <script src="{{ asset('js/cardex/otros/cargo/update.js') }}"></script>
    <script src="{{ asset('js/cardex/otros/cargo/delete.js') }}"></script>

    <script src="{{ asset('js/cardex/otros/tipo_personal/store.js') }}"></script>
    <script src="{{ asset('js/cardex/otros/tipo_personal/update.js') }}"></script>
    <script src="{{ asset('js/cardex/otros/tipo_personal/delete.js') }}"></script>
   
@endpush
