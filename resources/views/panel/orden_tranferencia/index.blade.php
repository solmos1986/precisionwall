@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Order Tranfer:</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                            <div class="form-group">
                                <button type="button" id="registrar" class="btn btn-success btn-sm mt-0 has-icon">
                                    <i class="fa fa-save"></i>
                                    Register new
                                </button>
                                <button type="button" id="crear_transferencia"
                                    class="btn btn-primary btn-sm mt-0 has-icon">
                                    <i class="fa fa-plus"></i>
                                    Add Records
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form action="" id="form_actividades" method="post">
                                <table class="table thead-primary no-footer w-100" id="lista_orden_transferencia">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Deliver date</th>
                                            <th>PO</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Materiales</th>
                                            <th>Note</th>
                                            <th>Quantity</th>
                                            <th>Q. warehouse</th>
                                            <th>Q. project</th>
                                            <th>Acctions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('datatable')
    <script>
        var isAdmin = {{ Auth::user()->verificarRol([1, 10]) == 1 ? '1' : '0' }}
    </script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/order_transferencia/index.js') }}"></script>
@endpush
