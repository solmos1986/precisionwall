<nav class="navbar ms-navbar">
    <div class="ms-aside-toggler ms-toggler pl-0" data-target="#ms-side-nav" data-toggle="slideLeft">
        <span class="ms-toggler-bar bg-white"></span>
        <span class="ms-toggler-bar bg-white"></span>
        <span class="ms-toggler-bar bg-white"></span>
    </div>
    <div class="logo-sn logo-sm ms-d-block-sm">
        <a class="pl-0 ml-0 text-center navbar-brand mr-0" href="#"><img src="{{ asset('img/logo.png') }}" alt="logo">
        </a>
    </div>
    <ul class="ms-nav-list ms-inline mb-0" id="ms-nav-options">
        <!--notificacion-->
        <li class="ms-nav-item dropdown">
            <a href="#" class="text-disabled ms-has-notification" id="notificationDropdown" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false"><i class="flaticon-bell"></i></a>
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown">
                <li class="dropdown-menu-header">
                    <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Notifications</span></h6>
                    <span id="total" class="badge badge-pill badge-info"></span>
                </li>
                <li class="dropdown-divider"></li>
                <li class="ms-scrollable ms-dropdown-list" id="datos">
                </li>
               
            </ul>
        </li>
        <li class="ms-nav-item ms-nav-user dropdown">
            <a href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img
                    class="ms-user-img ms-img-round float-right"
                    src="{{ asset('assets/img/User-Account-Person-PNG-File.png') }}" alt="people"> </a>
            <ul class="dropdown-menu dropdown-menu-right user-dropdown" aria-labelledby="userDropdown">
                <li class="dropdown-menu-header">
                    <h6 class="dropdown-header ms-inline m-0"><span class="text-disabled">Welcome,
                            {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido_Paterno }}
                            {{ auth()->user()->Apellido_Materno }}</span></h6>
                </li>

                <li class="dropdown-divider"></li>
                <li class="dropdown-menu-footer">
                    <form method="POST" id="logout" action="{{ route('logout') }}">
                        {{ csrf_field() }}
                        <a class="media fs-14 p-2" href="javascript:{}"
                            onclick="document.getElementById('logout').submit();"> <span><i
                                    class="flaticon-shut-down mr-2"></i> Logout</span> </a>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
    <div class="ms-toggler ms-d-block-sm pr-0 ms-nav-toggler" data-toggle="slideDown" data-target="#ms-nav-options">
        <span class="ms-toggler-bar bg-white"></span>
        <span class="ms-toggler-bar bg-white"></span>
        <span class="ms-toggler-bar bg-white"></span>
    </div>
</nav>
