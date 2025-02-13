@extends('layouts.admin')
@section('contenido')
    <div class="ms-content-wrapper">

        <div class="row">

            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb pl-0">
                        <li class="breadcrumb-item"><a href="#"><i class="material-icons">home</i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Almacen</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Productos</li>
                    </ol>
                </nav>



                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <h6>productos</h6>
                    </div>

                    <div class="ms-panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped thead-primary w-100">

                                <div class="form-row clearfix visible-xs">

                                    <div class="col-md-3 mb-2">
                                        <label for="validationCustom15"></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="validationCustom15"
                                                placeholder="Buscar producto" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">


                                    </div>
                                    <div class="col-md-3 col-md-push mb-2">

                                        <div class="input-group">

                                            <button type="button" class="btn btn-pill btn-success nuevo">NUEVO</button>
                                        </div>
                                    </div>

                                </div>
                                <thead>
                                    <th>Imagen</th>
                                    <th>ID</th>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>
                                    <th>UnidadMedida</th>
                                    <th>StockMinimo</th>
                                    <th>Accion</th>
                                </thead>
                                @foreach ($productos as $pro)
                                    <tr>
                                        <td>{{ $pro->ImagenProducto }}</< /td>
                                        <td>{{ $pro->idProducto }}</td>
                                        <td>{{ $pro->CodProducto }}</< /td>
                                        <td>{{ $pro->NomProducto }}</< /td>
                                        <td>{{ $pro->UnidadMedida }}</< /td>
                                        <td>{{ $pro->StockMinimo }}</< /td>
                                        <td><a><i class='fas fa-pencil-alt ms-text-primary editar'></i></a> <a
                                                href='a'><i class='far fa-trash-alt ms-text-danger'></i></a>

                                        </td>
                                    </tr>
                                @endforeach

                            </table>

                        </div>

                        <div class="ms-panel-bshadow-none">
                            {{ $productos->render() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE PRODUCTO Modal -->
    <div class="modal fade" id="formProducto" tabindex="-1" role="dialog" aria-hidden="true">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {!! Form::open(['url' => 'almacen/producto', 'method' => 'POST', 'autocomplete' => 'off']) !!}
        {{ Form::token() }}

        <div class="modal-dialog ms-modal-dialog-width">
            <div class="modal-content ms-modal-content-width">
                <div class="modal-header  ms-modal-header-radius-0">
                    <h4 class="modal-title text-white">Productos</h4>
                    <button type="button" class="close  text-white" data-dismiss="modal" aria-hidden="true">x</button>

                </div>
                <div class="modal-body p-0 text-left">
                    <div class="col-xl-12 col-md-12">
                        <div class="ms-panel ms-panel-bshadow-none">
                            <div class="ms-panel-header">
                                <h6>Crear Producto</h6>
                            </div>
                            <div class="ms-panel-body">
                                <form class="needs-validation" novalidate>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom09">Codigo Producto</label>
                                            <div class="input-group">

                                                <input type="text" class="form-control" id="validationCustom09"
                                                    placeholder="Ingrese Codigo" required name="CodProducto">

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom10">Nombre Producto</label>
                                            <div class="input-group">

                                                <input type="text" class="form-control" id="validationCustom10"
                                                    placeholder="Ingrese Nombre" required name="NomProducto">

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom11">Unidad Medida</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect" name="UnidadMedida">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>


                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom12">Stock Minimo</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="validationCustom12"
                                                    placeholder="Stock minimo" required name="StockMinimo">

                                            </div>

                                        </div>

                                    </div>
                                    <div class="form-row">


                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">Categoria</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">SubCategoria</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">Tipo</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">SubTipo</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-row">

                                        <div class="col-md-12 mb-2">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="validatedCustomFile">
                                                <label class="custom-file-label" for="validatedCustomFile">Carga
                                                    imagen producto...</label>
                                                <div class="invalid-feedback">Example invalid custom file feedback
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-8 mb-2">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <button class="btn btn-warning mt-4 d-inline w-20"
                                                type="reset">Cancelar</button>
                                            <button class="btn btn-primary mt-4 d-inline w-20"
                                                type="sumit">Guardar</button>

                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    <div class="modal fade" id="formEditarProducto" tabindex="-1" role="dialog" aria-hidden="true">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {!! Form::open(['url' => 'almacen/producto', 'method' => 'POST', 'autocomplete' => 'off']) !!}
        {{ Form::token() }}

        <div class="modal-dialog ms-modal-dialog-width">
            <div class="modal-content ms-modal-content-width">
                <div class="modal-header  ms-modal-header-radius-0">
                    <h4 class="modal-title text-white">Productos</h4>
                    <button type="button" class="close  text-white" data-dismiss="modal" aria-hidden="true">x</button>

                </div>
                <div class="modal-body p-0 text-left">
                    <div class="col-xl-12 col-md-12">
                        <div class="ms-panel ms-panel-bshadow-none">
                            <div class="ms-panel-header">
                                <h6>Editar Producto</h6>
                            </div>
                            <div class="ms-panel-body">
                                <form class="needs-validation" novalidate>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom09">Codigo Producto</label>
                                            <div class="input-group">

                                                <input type="text" class="form-control" id="validationCustom09"
                                                    placeholder="Ingrese Codigo" required name="CodProducto">

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom10">Nombre Producto</label>
                                            <div class="input-group">

                                                <input type="text" class="form-control" id="validationCustom10"
                                                    placeholder="Ingrese Nombre" required name="NomProducto">

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom11">Unidad Medida</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect" name="UnidadMedida">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>


                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-1">
                                            <label for="validationCustom12">Stock Minimo</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="validationCustom12"
                                                    placeholder="Stock minimo" required name="StockMinimo">

                                            </div>

                                        </div>

                                    </div>
                                    <div class="form-row">


                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">Categoria</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">SubCategoria</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">Tipo</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label for="validationCustom13">SubTipo</label>
                                            <div class="input-group">
                                                <select class="form-control" id="exampleSelect">
                                                    <option value="metros">metros</option>
                                                    <option value="pieza">pieza</option>
                                                    <option value="tubo">tubo</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-row">

                                        <div class="col-md-12 mb-2">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="validatedCustomFile">
                                                <label class="custom-file-label" for="validatedCustomFile">Carga
                                                    imagen producto...</label>
                                                <div class="invalid-feedback">Example invalid custom file feedback
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-8 mb-2">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <button class="btn btn-warning mt-4 d-inline w-20"
                                                type="reset">Cancelar</button>
                                            <button class="btn btn-primary mt-4 d-inline w-20"
                                                type="sumit">Guardar</button>

                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
@push('javascript')
    <script type="text/javascript">
        $(document).ready(function() {

            $(window).load(function() {
                $(".cargando").fadeOut(1000);
            });

            //Ocultar mensaje
            setTimeout(function() {
                $("#msj").fadeOut(1000);
            }, 7000);

        });
    </script>
    <script type="text/javascript">
        $(document).on("click", ".editar", function() {
            $('#formEditarProducto').modal('show');
        });
        $(document).on("click", ".nuevo", function() {
            $('#formProducto').modal('show');
        });
    </script>
@endpush
