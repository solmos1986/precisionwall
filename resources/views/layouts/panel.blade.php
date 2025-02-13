<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Panel</title>
    <!-- Iconic Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('vendors/iconic-fonts/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendors/iconic-fonts/flat-icons/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/iconic-fonts/cryptocoins/cryptocoins.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/iconic-fonts/cryptocoins/cryptocoins-colors.css') }}">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- jQuery UI -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/datapicker.css') }}">
    @stack('css-header')
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

        .ui-state-highlight {
            margin-bottom: 0;
        }

    </style>
    <!-- medboard styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- Favicon -->
    <!--<link rel="icon" type="image/png" sizes="32x32" href="favicon.ico"> -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- crear orden --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css"
    integrity="sha512-LT9fy1J8pE4Cy6ijbg96UkExgOjCqcxAC7xsnv+mLJxSvftGVmmc236jlPTZXPcBRQcVOWoK1IJhb1dAjtb4lQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="ms-body ms-aside-left-open ms-primary-theme ms-has-quickbar">

    <!-- Preloader -->
    <div id="preloader-wrap">
        <div class="spinner spinner-8">
            <div class="ms-circle1 ms-child"></div>
            <div class="ms-circle2 ms-child"></div>
            <div class="ms-circle3 ms-child"></div>
            <div class="ms-circle4 ms-child"></div>
            <div class="ms-circle5 ms-child"></div>
            <div class="ms-circle6 ms-child"></div>
            <div class="ms-circle7 ms-child"></div>
            <div class="ms-circle8 ms-child"></div>
            <div class="ms-circle9 ms-child"></div>
            <div class="ms-circle10 ms-child"></div>
            <div class="ms-circle11 ms-child"></div>
            <div class="ms-circle12 ms-child"></div>
        </div>
    </div>
    <!-- Overlays -->
    <div class="ms-aside-overlay ms-overlay-left ms-toggler" data-target="#ms-side-nav" data-toggle="slideLeft"></div>
    <div class="ms-aside-overlay ms-overlay-right ms-toggler" data-target="#ms-recent-activity"
        data-toggle="slideRight"></div>
    <!-- Sidebar Navigation Left -->
    @component('layouts.parts.sidebar')
    @endcomponent

    <!-- Main Content -->
    <main class="body-content">
        <!-- Navigation Bar -->
        @component('layouts.parts.navigation-bar')
        @endcomponent
        <!-- Body Content Wrapper -->
        <div class="ms-content-wrapper">
            @yield('content')
        </div>
    </main>

    <!-- Global Required Scripts Start -->
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/perfect-scrollbar.js') }}"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!--script src="{{ asset('assets/js/toastr.min.js') }}"> </script-->
    <script>
        var base_url = "{{ url('/') }}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('datatable')
    
    <!-- medboard core JavaScript -->
    <script src="{{ asset('assets/js/framework.js') }}"></script>
    <script src="{{ asset('js/notificacion.js') }}"></script>
    <script src="{{ asset('js/datepicker.js') }}"></script>


    {{-- crear orden desde menu --}}
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <x-components.tipo-orden.list.create-order />
    <script>
    $(function(){
            $(".TodayTime").datetimepicker({  
                defaultDate: $('#TodayTime').val(),
                format: 'HH:mm:ss',
                timeFormat: 'HH:mm:ss',  
                pickDate: false,
                pickSeconds: false,
                pick12HourFormat: false,
                onSelect:function(datetimeText, datepickerInstance){
                    if (!datepickerInstance.timeDefined) {
                        $(".TodayTime").datetimepicker('hide')
                    }
                }
            })
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"
    integrity="sha512-s5u/JBtkPg+Ff2WEr49/cJsod95UgLHbC00N/GglqdQuLnYhALncz8ZHiW/LxDRGduijLKzeYb7Aal9h3codZA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/tipo-orden/list/create_orden.js') }}"></script>
    @stack('javascript-form')
    <script src="{{ asset('js/errors_ajax.js') }}"></script>
</body>

</html>
