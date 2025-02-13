@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">

    <style>
        .table-standar td,
        .table-standar th {
            padding: 1px;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table td,
        .table th {
            padding: 0.5rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
        }

        .no-margin {
            vertical-align: top;
            border-top: 1px solid #ffffff;
        }

        td.details-control {
            background: url('https://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.details td.details-control {
            background: url('https://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
        }
        .table i {
            margin-right: 3px;
            font-size: 18px;
        }

    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome">
                    <h6> Structure of Asemblies:</h6>
                </div>
                <div class="ms-panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" id="list_labor_cost" class="btn btn-primary btn-sm mt-0">
                                List Labor Cost
                            </button>
                        </div>
                        <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                            <button type="button" id="crear_superficie" class="btn btn-primary btn-sm mt-0">
                                Add new surface
                            </button>
                        </div>
                        <div class="col-md-12 mt-2">
                            <table id="lista_superficies" class="table thead-primary w-100 " style="width:100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Cod</th>
                                        <th>Name</th>
                                        <th>Miscelaneos</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-components.estimados.view-superficie title="Info" />
    <x-components.estimados.labor_cost.labor_cost title="Info" />
    <x-components.estimados.labor_cost.view_list_labor_cost title="Info" />
    <x-components.estimados.view-standar title="Info" />
    <x-components.estimados.view-metodo title={{--  --}}"Info" />
    {{--   herramientas  --}}
    <x-components.goal.estructure.materiales title="Info" />
@endsection
@push('datatable')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/tableedit.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        //modal multinivel
        $(document).ready(function() {
            $('.modal').on('hidden.bs.modal', function(event) {
                $(this).removeClass('fv-modal-stack');
                $('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
            });
            $('.modal').on('shown.bs.modal', function(event) {
                // keep track of the number of open modals
                if (typeof($('body').data('fv_open_modals')) == 'undefined') {
                    $('body').data('fv_open_modals', 0);
                }
                // if the z-index of this modal has been set, ignore.

                if ($(this).hasClass('fv-modal-stack')) {
                    return;
                }

                $(this).addClass('fv-modal-stack');

                $('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);

                $(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));

                $('.modal-backdrop').not('.fv-modal-stack')
                    .css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));
                $('.modal-backdrop').not('fv-modal-stack')
                    .addClass('fv-modal-stack');
            });
        });
    </script>
    <script>
        var dt = $('#lista_superficies').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('datatable.surface') }}",
            "columns": [{
                    "class": "details-control",
                    "orderable": false,
                    "data": null,
                    "defaultContent": ""
                },
                {
                    data: 'codigo',
                    name: "codigo"
                },
                {
                    data: 'nombre',
                    name: "nombre"
                },
                {
                    data: 'miselaneo',
                    name: "miselaneo"
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                }
            ],
            "order": [

            ],
            pageLength: 100,
        });

        function format(data, superficie_id) {
            //standares
            var estandares = ``;
            data.forEach(estandar => {
                //metodos
                var metodos = ``;
                estandar.metodos.forEach(metodo => {
                    metodos += `
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${metodo.nombre}</td>
                            <td>${metodo.unidad_medida}</td>
                            <td>${metodo.materal_spread}</td>
                            <td>${metodo.material_cost_unit}</td>
                            <td>${metodo.material_unit_med}</td>
                            <td>${metodo.num_coast}</td>
                            <td>${metodo.rate_hour}</td>
                            <td>${metodo.mark_up== null ? '' : metodo.mark_up}</td>
                            <td>${metodo.defauld =='y' ? `<span class="badge badge-pill badge-primary">Yes</span>` : ` ` }  </td></td>
                            <td>
                                <i class="fas fa-pencil-alt ms-text-warning cursor-pointer edit_metodo" title="Edit Method" id="create_standar" data-metodo_id="${metodo.id}"></i>
                                <i class="far fa-trash-alt ms-text-danger delete_metodo cursor-pointer" data-metodo_id="${metodo.id}" title="Delete Method"></i>
                            </td>
                        </tr>
                        `;
                });
                estandares += `
               <tr>
                    <td>${estandar.nombre}</td>
                    <td>${estandar.codigo}</td>
                    <td>${estandar.Nom_Sov}</td>
                    <td>
                        <i class="fas fa-pencil-alt ms-text-warning cursor-pointer edit_standar" title="Edit Task" data-estandar_id="${estandar.id}"></i>
                        <i class="far fa-trash-alt ms-text-danger cursor-pointer delete-superficie cursor-pointer delete_standar" data-estandar_id="${estandar.id}" title="Delete Task"></i>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm mt-0 create_metodo" data-estandar_id="${estandar.id}">
                            Add Method
                        </button>
                    </td>
                </tr>
                ${metodos}
               `;
            });
            return `
            <div class="row" id="${superficie_id}">
                <div class="col-md-1">
                </div>
                <div class="col-md-11">
                    <div class="row">
                        <div class="col-md-6">
                           
                        </div>
                        <div class="col-md-6 d-flex flex-row-reverse bd-highlight">
                            <button type="button" id="crear_task" class="btn btn-primary btn-sm mt-0" data-superficie_id="${superficie_id}">
                                Add Task
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="" class="table table-hover mt-2">
                            <thead style="background-color:#ffffff, color:black">
                                <tr>
                                    <th>Task</th>
                                    <th>Cost Code</th>
                                    <th>Sov</th>
                                    <th>Method</th>
                                    <th>Unit Med.</th>
                                    <th>M. Spread</th>
                                    <th>M. Cost Unit</th>
                                    <th>M. Unit Med.</th>
                                    <th>Num. Coast</th>
                                    <th>Rate Hours</th>
                                    <th>% Cost</th>
                                    <th>Default</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${estandares}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;
        }
        $(document).ready(function() {
            // Array to track the ids of the details displayed rows
            var detailRows = [];

            $('#lista_superficies tbody').on('click', 'tr td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = dt.row(tr);
                var idx = $.inArray(tr.attr('id'), detailRows);

                if (row.child.isShown()) {
                    tr.removeClass('details');
                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice(idx, 1);
                } else {
                    tr.addClass('details');
                    console.log('aki')
                    /*data tareas*/
                    $.ajax({
                        type: 'GET',
                        url: `${base_url}/project-files/list-standars/${row.data().id}`,
                        dataType: 'json',
                        async: true,
                        success: function(response) {
                            row.child(format(response, row.data().id)).show();
                        }
                    });


                    // Add to the 'open' array
                    if (idx === -1) {
                        detailRows.push(tr.attr('id'));
                    }
                }
            });

            // On each draw, loop over the `detailRows` array and show any child rows
            dt.on('draw', function() {
                $.each(detailRows, function(i, id) {
                    $('#' + id + ' td.details-control').trigger('click');
                });
            });

        });
    </script>
    <script src="{{ asset('js/estimados/list_labor_cost.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-superficie.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-estandar.js') }}"></script>
    <script src="{{ asset('js/estimados/views/view-metodo.js') }}"></script>
    {{-- lista de heramientas --}}
    <script src="{{ asset('js/estimados/views/crear_material.js') }}"></script>
@endpush
