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
            background: rgb(228, 231, 255);
        }
    </style>
    <style>
        tr.selected {}

        .verde {
            background-color: #d1f2eb
        }

        .rojo {
            background-color: #F5B7B1;
        }

        .azul {
            background-color: #a9cce3
        }

        .celeste {
            background-color: #d4e6f1
        }

        .amarillo {
            background-color: #fcf3cf
        }

        .blanco {
            background-color: #ffffff
        }

        .colores {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .check-color {
            position: relative;
            width: 20px;
            height: 20px;
            text-align: center;
            padding: 4px 0;
            margin-right: 5px;
            border: 1px solid #878793;
            color: #878793;
            border-radius: 5px;
            -webkit-transition: 0.3s;
            transition: 0.3s;
            cursor: pointer;
        }

        .check-color-all {
            width: 20px;
            height: 20px;
            text-align: center;
            padding: 4px 0;
            margin-right: 5px;
            border: 1px solid #878793;
            color: #878793;
            border-radius: 5px;
            -webkit-transition: 0.3s;
            transition: 0.3s;
            cursor: pointer;
        }

        .position {
            display: block;
        }

        .proyectos {
            opacity: 1;
            accent-color: #ffffff;
            height: 30px;
            /* not needed */
            width: 30px;
            /* not needed */
        }

        .color-text {
            color: #000;
        }
    </style>
    <style>
        @media only screen and (min-width: 580px) {
            .modal-lg {
                max-width: 80% !important;
            }
        }

        .file-footer-buttons>.btn {
            padding: 0.625rem 1rem;
            min-width: 0 !important;
            margin-top: 1rem;
        }
    </style>
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('themes/explorer-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/tokenfield-typeahead.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-multiselect.min.css') }}" type="text/css" rel="stylesheet">
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

            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6>Category Submittals</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div></div>
                                <button type="button" id="add_submittals"
                                    class="btn btn-primary has-icon btn-sm d-inline m-0 mb-1">
                                    Add category submittals
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="list-proyectos" class="table thead-primary w-100">
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
        </div>
    </div>
    <!--Modal uploadModal -->
@endsection
@push('javascript-form')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js"
        integrity="sha512-R/QOHLpV1Ggq22vfDAWYOaMd5RopHrJNMxi8/lJu8Oihwi4Ho4BRFeiMiCefn9rasajKjnx9/fTQ/xkWnkDACg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.11.5/sorting/datetime-moment.js"></script>

    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js"></script>

    <script>
        //sort por foramto de fecha
        var dataTable = $("#list-proyectos").DataTable({
            processing: true,
            serverSide: true,
            scrollY: true,
            scrollX: true,
            scrollCollapse: true,
            pageLength: 50,
            ajax: {
                url: `${base_url}/category-submittals/data-table`,
                type: 'GET',
                contentType: 'application/json',
                dataType: 'json',
                data: function(d, data, otros) {
                    return d
                },
            },
            language: {
                searchPlaceholder: "Criterion"
            },
            order: [

            ],
            columns: [{
                    data: 'Nombre',
                    name: 'Nombre',
                    render: function(data, type, row) {
                        return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Cat_ID}"
                                >
                        `;
                    }
                },
                {
                    data: 'Descripcion',
                    name: 'Descripcion',
                    render: function(data, type, row) {
                        //console.log(data, type, row)
                        return `
                                <input type = "text"
                                    class = "form-control form-control-sm editar"
                                    name = "description"
                                    placeholder = ""
                                    value = "${data==null ? '' : data}"
                                    autocomplete = "off"
                                    data-id="${row.Cat_ID}"
                                >
                        `;
                    }
                },
                {
                    data: 'Cat_ID',
                    name: 'Cat_ID',
                    render: function(data, type, row) {
                        //console.log(data, type, row)
                        return `
                            <i class="far fa-trash-alt ms-text-danger delete cursor-pointer" data-id="${row.Cat_ID}" title="Delete"></i>
                        `;
                    }
                },
            ]
        });
    </script>
    <script>
        $(document).on("keyup", ".editar", function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                let form = {
                    Nombre: '',
                    Descripcion: ''
                }
                let inputs = $(this).parent().parent().children().each((i, ele) => {
                    switch (i) {
                        case 0:
                            console.log($(ele).find('input').val())
                            form.Nombre = $(ele).find('input').val();
                            break;
                        case 1:
                            form.Descripcion = $(ele).find('input').val();
                            break;

                        default:
                            break;
                    }
                })
                $.ajax({
                    type: 'PUT',
                    url: `${base_url}/category-submittals/update/${$(this).data('id')}`,
                    dataType: 'json',
                    data: form,
                    async: true,
                    success: function(response) {
                        if (response.status == 'ok') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(".delete_temporal").parent().parent().remove();
                            dataTable.ajax.url(`${base_url}/category-submittals/data-table`).draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                'An error occurred',
                                'error'
                            );
                        }
                    }
                });
            }
        });
        //editar 
        $(document).on("keyup", ".store_temporal", function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                store(e, this)
            }
        });
        $(document).on("click", ".store_temporal_btn", function(e) {
            console.log('click')
            store(e, this)
        });

        function store(e, input) {
            let form = {
                Nombre: '',
                Descripcion: ''
            }
            let inputs = $(input).parent().parent().children().each((i, ele) => {
                switch (i) {
                    case 0:
                        console.log($(ele).find('input').val())
                        form.Nombre = $(ele).find('input').val();
                        break;
                    case 1:
                        form.Descripcion = $(ele).find('input').val();
                        break;

                    default:
                        break;
                }
            })
            $.ajax({
                type: 'POST',
                url: `${base_url}/category-submittals/store`,
                dataType: 'json',
                data: form,
                async: true,
                success: function(response) {
                    if (response.status == 'ok') {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $(".delete_temporal").parent().parent().remove();
                        dataTable.ajax.url(`${base_url}/category-submittals/data-table`).draw();
                    } else {
                        Swal.fire(
                            'Error!',
                            'An error occurred',
                            'error'
                        );
                    }
                }
            });
        }
        $(document).on("click", "#add_submittals", function(e) {
            $("#list-proyectos").append(`
                 <tr>
                     <td>
                         <input type = "text"
                             class = "form-control form-control-sm store_temporal"
                             name = "description"
                             placeholder = ""
                             value = ""
                             autocomplete = "off"
                             data-id=""
                         >
                     </td>
                     <td>
                         <input type = "text"
                             class = "form-control form-control-sm store_temporal"
                             name = "description"
                             placeholder = ""
                             value = ""
                             autocomplete = "off"
                             data-id=""
                         >
                     </td>
                     <td>
                         <i class="far fa-check-circle ms-text-primary store_temporal_btn cursor-pointer" title="Save"></i>
                         <i class="far fa-trash-alt ms-text-danger delete_temporal cursor-pointer" title="Delete"></i>
                     </td>
                 </tr>
         `);
            /* dataTable.row.add({
                Cat_ID:1,
                DT_RowIndex:1,
                Descripcion:'dasdas',
                Nombre:'sasda'
            }).draw(); */
        });

        $(document).on("click", ".delete_temporal", function(e) {
            $(this).parent().parent().remove()
        });

        $(document).on("click", ".delete", function(e) {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: `${base_url}/category-submittals/delete/${id}`,
                        dataType: 'json',
                        async: true,
                        success: function(response) {
                            if (response.status == 'ok') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                dataTable.draw();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                            dataTable.draw();
                        }
                    });
                }
            });
        })
    </script>
@endpush
