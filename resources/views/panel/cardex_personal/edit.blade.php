@extends('layouts.panel')
@push('css-header')
    <link href="{{ asset('css/fileinput.css') }}" media="all" rel="stylesheet" type="text/css" />
    <!-- Tokenfield CSS -->
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">
    <link href="{{ asset('css/bootstrap-tokenfield.min.css') }}" type="text/css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css"
        crossorigin="anonymous">
    {{-- <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput.min.css" media="all"
        rel="stylesheet" type="text/css" /> --}}
    <link href="http://localhost/constructora2/public/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
    @section('content')
        @if (Auth::user()->verificarRol([1,5,10]))
            @include('panel.cardex_personal.view_admin')
        @else
            @include('panel.cardex_personal.view_foreman')
        @endif
    @endsection

    @push('javascript-form')
        <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap.min.js"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('js/datepicker.js') }}"></script>

        <!-- the main fileinput plugin script JS file -->
        <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/buffer.min.js"
            type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/plugins/filetype.min.js"
            type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>

        <script src="{{ asset('js/fileinput.js') }}" type="text/javascript"></script>
        <script src="{{ asset('themes/fas/theme.js') }}" type="text/javascript"></script>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script>
            $("#enviar").click(function(e) {
                e.preventDefault();
                send_form();
            });

            function send_form() {
                let $form = $('#from_cardex');
                $.ajax({
                    type: "POST",
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: "json",
                    success: function(data) {
                        console.log(data.errors.length)
                        if (data.errors.length > 0) {
                            console.log('error')
                            $alert = "complete the following fields to continue:\n";
                            data.errors.forEach(function(error) {
                                $alert += `* ${error}\n`;
                            });
                            alert($alert);
                        } else {
                            $form.submit();
                        }
                    }
                });
            }
            //show report
        </script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#list_personal').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('show.movimiento.datatable', ['id' => $personal->Empleado_ID]) }}",
                order: [
                    [1, "asc"]
                ],
                columns: [{
                        data: "type_event",
                        name: "type_event",
                    },
                    {
                        data: "nombre",
                        name: "nombre"
                    },
                    {
                        data: "note",
                        name: "note",
                    },
                    {
                        data: "start_date",
                        name: "start_date"
                    },
                    {
                        data: "exp_date",
                        name: "exp_date"
                    },
                    {
                        data: "duracion_day",
                        name: "duracion_day"
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false
                    }
                ],
                pageLength: 100,
            });
        </script>
        <script>
            $('#tipo_personal_id').select2();
            $('#cargo_personal_id').select2();
        </script>
        <script src="{{ asset('js/cardex.js') }}"></script>
        {{-- <script src="{{ asset('js/cardex/otros/upload.js') }}"></script> --}}
        {{-- <script src="{{ asset('js/cardex/views/files.js') }}"></script> --}}
        {{-- sistema movimientos--}}
        <script src="{{ asset('js/cardex/movimientos/create.js') }}"></script>
        <script src="{{ asset('js/cardex/movimientos/store.js') }}"></script>
        <script src="{{ asset('js/cardex/movimientos/edit.js') }}"></script>
        <script src="{{ asset('js/cardex/movimientos/update.js') }}"></script>
        <script src="{{ asset('js/cardex/movimientos/delete.js') }}"></script>
    @endpush
