<aside id="ms-side-nav" class="side-nav fixed ms-aside-scrollable ms-aside-left">
    <!-- Logo -->
    <div class="logo-sn ms-d-block-lg">
        <a class="pl-0 ml-0 text-center" href="#"> <img src="{{ asset('img/logo.png') }}" alt="logo"> </a>
        <a href="#" class="text-center ms-logo-img-link"> <img
                src="{{ asset('assets/img/User-Account-Person-PNG-File.png') }}" alt="logo"></a>
        <h5 class="text-center text-white mt-2">{{ auth()->user()->Nombre }} {{ auth()->user()->Apellido_Paterno }}
            {{ auth()->user()->Apellido_Materno }}
        </h5>
    </div>
    <!-- Navigation -->
    <ul class="accordion ms-main-aside fs-14" id="side-nav-accordion">
        <!-- Projects -->
        @foreach (Auth::user()->obtenerAccesomodulo() as $modulo)
            <li class="menu-item">
                @if (count($modulo->sub_modulos) > 0)
                    <a href="{{ url('/') . $modulo->url }}" class="has-chevron" data-toggle="collapse"
                        data-target="#modulo{{ $modulo->modulo_id }}" aria-expanded="false" aria-controls="bar_ticket">
                        <span><i class="{{ $modulo->class_icon }}"></i>{{ $modulo->nombre_modulo }}</span>
                    </a>
                    <ul id="modulo{{ $modulo->modulo_id }}" class="collapse"
                        aria-labelledby="modulo{{ $modulo->modulo_id }}" data-parent="#side-nav-accordion">
                        @foreach ($modulo->sub_modulos as $sub_modulo)
                            <li class="menu-item">
                                <a href="{{ $sub_modulo->sub_modulo_id == 11 ? ($sub_modulo->url) : (url('/') . $sub_modulo->url) }}"
                                    {{ $sub_modulo->sub_modulo_id == 11 ? "id=create_orden_menu" : "" }}>
                                    {{ $sub_modulo->nombre_sub_modulo }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ url('/') . $modulo->url }}">
                        <span><i class="{{ $modulo->class_icon }}"></i>{{ $modulo->nombre_modulo }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</aside>
