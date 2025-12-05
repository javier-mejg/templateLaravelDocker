<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: ghostwhite;">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $titulo }} | Propedéutico Medicina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ url('img/logos/ico.png') }}">
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('css/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('css/style.css') . '?' . date('msh') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('css/loading.css') }}" rel="stylesheet" type="text/css" />
    <!-- Alertify JS -->
    <link href="{{ url('alertify/css/alertify.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('css/linea-dentada.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .container-fluid {
            padding-bottom: 20px;
        }

        .ajs-message.ajs-custom {
            color: white;
            background-color: #ff5900;
            border-color: #ff5900;
        }

        /* Estilo para todos los inputs y textareas */
        input[type="text"],
        input[type="number"],
        input[type="tel"],
        input[type="email"],
        select,
        .span_primary,
        textarea {
            border: .5px solid #ff5900;

        }
    </style>
    @yield('header')
</head>

<body style="background: ghostwhite;">

    <!-- SIDEBAR VERTICAL -->
    <div id="sidebar" class="bg-primary responsive-sidebar">


        <!-- LOGO -->
        <div class="text-center py-4 logo-box">
            <img src="{{ url('img/logos/anahuac-blanco.png') }}" alt="logo" style="max-width: 150px;">
        </div>

        <!-- Usuario -->
        <div class="px-3 pb-4 user-box">
            <div class="dropdown">
                <a class="d-flex align-items-center text-white dropdown-toggle" data-toggle="dropdown" href="#"
                    role="button">
                    <img src="{{ url('img/user/leo_user.png') }}" class="rounded-circle bg-white mr-2"
                        style="width: 40px; height: 40px;">
                    <span class="user-name">{{ isset($data['info']['nombre']) ? $data['info']['nombre'] : Crypt::decryptString(Auth::user()->name)}}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right mt-2">
                    <form method="GET" action="{{ route('logout') }}" onsubmit="cerrar_sesion(event)">
                        @csrf
                        <button type="submit" class="dropdown-item button">
                            <i class="dripicons-exit text-muted mr-2"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>


    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content escala">
        <!-- Loader de página -->
        <div class="div-loader" id="loader">
            <div class="loader"></div>
        </div>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ url('js/jquery.min.js') }}"></script>
    <script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('alertify/alertify.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $("#loader").show();
        });

        window.addEventListener('load', function () {
            $("#loader").hide();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function cerrar_sesion(event) {
            
        }

        alertify.defaults.transition = "slide";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";
    </script>

    @yield('footer')

</body>