<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Panel</title>
    <!-- Iconic Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('vendors/iconic-fonts/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- jQuery UI -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/datapicker.css') }}">

    <!-- medboard styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- Favicon -->
    <!--<link rel="icon" type="image/png" sizes="32x32" href="favicon.ico"> -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- crear orden --}}

    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap.min.css">

</head>

<body class="ms-body ms-primary-theme ms-has-quickbar">
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
    <main class="body-content">
        <nav class="navbar ms-navbar">
            <div class="ms-aside-toggler ms-toggler pl-0" data-target="#ms-side-nav" data-toggle="slideLeft">
            </div>
            <div class="logo-sn logo-sm ms-d-block-sm">
                <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="#"><img src="{{ asset('img/logo.png') }}"
                        alt="logo">
                </a>
            </div>
            <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
                <!--notificacion-->
                <li class="ms-nav-item ms-nav-user dropdown">
                    <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"> <img class="ms-user-img ms-img-round float-right"
                            src="{{ asset('assets/img/User-Account-Person-PNG-File.png') }}" alt="people"> </a>
                    <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
                        <li class="dropdown-menu-header">
                            <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">
                                    unauthenticated
                                </span></h6>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="ms-toggler ms-d-block-sm pr-0 ms-nav-toggler" data-toggle="slideDown"
                data-target="#ms-nav-options">
                <span class="ms-toggler-bar bg-white"></span>
                <span class="ms-toggler-bar bg-white"></span>
                <span class="ms-toggler-bar bg-white"></span>
            </div>
        </nav>

        <div class="ms-content-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <div class="invisible" id="statubs_crud"></div>
                    <div class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome">
                            <h6 class="pt-1">Employee list</h6>
                        </div>
                        <div class="ms-panel-body">
                            <div class="table-responsive">
                                <table id="list_personal" class="table thead-primary w-100">
                                    <thead>
                                        <tr>
                                            {{-- <th>Num.</th> --}}
                                            <th>Full name</th>
                                            {{-- <th>Nickname</th> --}}
                                            <th>Type of employee</th>
                                            <th>Position</th>
                                            <th>Events</th>
                                            <th>Email</th>
                                            <th>Company</th>
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
    </main>
    <!-- Global Required Scripts Start -->
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/perfect-scrollbar.js') }}"></script>
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

    <!-- medboard core JavaScript -->
    <script src="{{ asset('assets/js/framework.js') }}"></script>

    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>

    <script>
        /*obtener  url*/
        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };
        var personas = getUrlParameter('personas');
        console.log(personas)
        var table = $('#list_personal').DataTable({
            searching: false,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: `${base_url}/no-auth-datatable?personas=${personas}`,
            order: [
                [0, "desc"]
            ],
            columns: [
                /* {
                                    data: "Numero",
                                    name: "Numero"
                                }, */
                {
                    width: '10%',
                    data: "Nick_Name",
                    name: "Nick_Name",
                },
                /*  {
                     data: "Nick_Name",
                     name: "Nick_Name"
                 }, */
                {
                    width: '10%',
                    data: "nombre_tipo",
                    name: "nombre_tipo"
                },
                {
                    width: '10%',
                    data: "Cargo",
                    name: "Cargo"
                },
                {
                    width: '30%',
                    data: "eventos",
                    name: "eventos"
                },
                {
                    width: '15%',
                    data: "email",
                    name: "email"
                },
                {
                    width: '15%',
                    data: "nombre_empresa",
                    name: "nombre_empresa"
                }
            ],
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"
        integrity="sha512-s5u/JBtkPg+Ff2WEr49/cJsod95UgLHbC00N/GglqdQuLnYhALncz8ZHiW/LxDRGduijLKzeYb7Aal9h3codZA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>
