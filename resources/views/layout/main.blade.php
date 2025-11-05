<!DOCTYPE html>

<html lang="es_MX" dir="ltr">



<head>

    <meta charset="utf-8" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ isset($act_submenu_route->sub_name) ? $act_submenu_route->sub_name : 'Soy león' }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta content=" Descripción del módulo " name="description" />
    <link href="{{ url('/public/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">

    <link rel="shortcut icon" href="{{ url('/public/img/logos/ico.png') }}">

    <link href="{{ url('/public/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ url('/public/css/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ url('/public/css/icons.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ url('/public/css/style.css') . '?2' . date('msh') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('/public/css/loading.css') }}" rel="stylesheet" type="text/css" />

    @yield('header')
    <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    <style>
        footer {
  text-align: right;
  padding-right: 1rem; /* o el valor que desees */
}
            @media (max-width: 600px) {
                .dropdown-menu-notificaciones {
                    width: 95vw !important;
                    left: -65% !important;
                    transform: translateX(-50%) !important;
                    right: auto !important;
                    min-width: unset !important;
                    border-radius: 12px;
                }
            }

            .dropdown-menu-notificaciones .dropdown-item {
                padding: 12px 16px;
                border-bottom: 1px solid #F2F2F2;
                background: #fff;
                transition: background 0.2s;
            }

            .dropdown-menu-notificaciones .dropdown-item:last-child {
                border-bottom: none;
            }

            .dropdown-menu-notificaciones .dropdown-item:hover {
                background: #FAFAFA;
            }

            @media (max-width: 600px) {
                .navbar-badge {
                    right: 7px !important;
                    top: 7px !important;
                }
            }

        .dropdown-menu-notificaciones .dropdown-item span {
            white-space: normal !important;
            word-break: break-word !important;
        }
    </style>
    @livewireStyles
</head>


@php
$user = auth()->user();
$notificaciones = $user ? $user->unreadNotifications : collect();
$notificacionesLeidas = $user ? $user->readNotifications : collect();
$cantidadNoLeidas = $notificaciones->count();

    @endphp
    <body>
 <div class="min-vh-100" style="flex: 1;">
    <nav class="navbar navbar-expand-lg topbar-left bg-primary p-0">
        <div class="container-fluid d-flex justify-content-between align-items-center" style="min-height: 60px;">
            <div class="topbar-left" style="order:1">
                <a href="#" class="logo">
                    <span>
                        <img src="{{ url('/public/img/logos/anahuac-blanco.png') }}" alt="logo-small" class="logo-sm">
                    </span>
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation"
                style="font-size: 2rem; line-height: inherit; color: #fff; order:0">
                <span class="navbar-toggler-icon"><i class="mdi mdi-menu"></i></span>
            </button>
            <!-- Iconos de campanita y usuario alineados -->
            <ul class="list-unstyled d-flex align-items-center mb-0" style="gap: 0px; order:3; margin-right: 16px; position: relative;">
                    {{-- GLPI & SLA --}}
                    <style>
                        .hover-show {
                            opacity:0;
                            transition: opacity 0.3s ease;
                        }

                        *:hover .hover-show {
                            opacity: 1;
                        }
                    </style>
                    <li>
                        <a class="nav-link waves-effect waves-light" 
                            href="https://atencion.redanahuac.mx/front/ticket.form.php" target="_blank" style="position: relative; color:#fff; display:inline-flex; align-items: center; gap:0.125rem">
                            <i class="mdi mdi-information-outline" style="font-size: 1.7rem;"></i> Abrir ticket <span class="mdi mdi-open-in-new hover-show" style="font-size: 130%; line-height: 100%; position: relative;"></span>
                        </a>
                    </li>
                    <li class="mr-2">
                        <a class="waves-effect waves-light" 
                        href="#" data-toggle="modal" data-target="#slaModal" style="position: relative; color:#fff; display:inline-flex; align-items: center; gap:0.125rem">
                            <i class="mdi mdi-table" style="font-size: 1.7rem;"></i>Tiempos de soporte
                        </a> 
                    </li>
                {{-- Notificaciones --}}
                <li class="dropdown" style="position: relative;">
                    <a id="campanaNotificaciones" class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown"
                        href="#" role="button" aria-haspopup="true" aria-expanded="false" style="position: relative; color:#fff">
                        <i class="mdi mdi-bell-outline" style="font-size: 1.7rem;"></i>
                        @if ($cantidadNoLeidas > 0)
                        <span class="badge badge-danger navbar-badge" style="background-color: #3A164C; position:absolute; top:13px; right:13px; font-size:0.75rem; min-width:18px; min-height:18px; padding:2px 6px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            {{ $cantidadNoLeidas }}
                        </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-0 dropdown-menu-notificaciones" style="width:340px; max-height:665px; overflow-y:auto; top:50px">
                        <div class="dropdown-header text-center font-weight-bold">Notificaciones</div>
                        @if($notificaciones->count())
                        <h6 class="dropdown-header">Notificaciones nuevas</h6>
                        @foreach ($notificaciones as $notificacion)
                        <a href="{{ Route::has($notificacion->data['url']) ? route($notificacion->data['url'], $notificacion->data['parametros'] ?? []) : '#' }}" class="dropdown-item d-flex flex-column">
                            <span class="font-weight-bold" style="font-size: 1rem;"><i class="{{ $notificacion->data['icono'] }}" style="font-size:0.9rem;"></i> {{ $notificacion->data['titulo'] ?? 'Sin título' }}</span>
                            <span style="font-size: .95rem;">{{ $notificacion->data['mensaje'] ?? '' }}</span>
                            <span class="text-right text-secondary" style="font-size: .85rem;">{{ $notificacion->created_at->diffForHumans() }}</span>
                        </a>
                        @endforeach
                        @else
                        <div style="padding: 15px 0; text-align:center; color:#888;">Sin notificaciones nuevas</div>
                        @endif
                        @if($notificacionesLeidas->count())
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Notificaciones leídas</h6>
                        @foreach ($notificacionesLeidas as $notificacion)
                       <a href="{{ Route::has($notificacion->data['url']) ? route($notificacion->data['url'], $notificacion->data['parametros'] ?? []) : '#' }}" class="dropdown-item d-flex flex-column">
                            <span class="font-weight-bold" style="font-size: 1rem;"><i class="{{ $notificacion->data['icono'] }}" style="font-size:0.9rem;"></i> {{ $notificacion->data['titulo'] ?? 'Sin título' }}</span>
                            <span style="font-size: .95rem;">{{ $notificacion->data['mensaje'] ?? '' }}</span>
                            <span class="text-right text-secondary" style="font-size: .85rem;">{{ $notificacion->created_at->diffForHumans() }}</span>
                        </a>
                        @endforeach
                        @endif
                    </div>
                </li>
                {{-- Usuario --}}
                <li class="dropdown" style="position: relative;">
                    <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown"
                        href="#" role="button" aria-haspopup="true" aria-expanded="false"
                        style="display:flex; align-items:center;">
                        @php
                        if (session()->has('photo')) {
                        echo session()->get('photo')['img'];
                        } else {
                        echo '<img src="https://soyleonadmin.anahuacqro.edu.mx/public/img/user/leo_user.png" alt="profile-user"
                            class="rounded-circle bg-white" style="width:38px; height:38px; object-fit:cover;" />';
                        }
                        @endphp
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">
                            {{ isset(Auth::user()->name) ? Crypt::decryptString(Auth::user()->name) . ' ' . Auth::user()->id : '' }}</a>
                        <a class="dropdown-item" href="{{ route('main.menu.index') }}">Pantalla principal</a>
                        <div class="dropdown-divider"></div>
                        @if (isset(Auth::user()->cat_uID) && Auth::user()->cat_uID == '421f7e4b-6010-4404-85ec-66d24bcfed5a')
                        @php 
                            $urlsl = str_replace('admin', '', env('ASSET_URL'));
                            @endphp
                       <a class="dropdown-item" href="/logout-to-perfil">
                        <i class="fas fa-user text-muted mr-2"></i>Perfil profesor
                        </a>
                        <div class="dropdown-divider"></div>
                        @endif
                        <a class="dropdown-item" href="/logout"><i
                                class="dripicons-exit text-muted mr-2"></i> Cerrar sesión</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>              
{{-- Modal con la tabla de SLA --}}
    <div class="modal fade" id="slaModal" tabindex="-1" role="dialog" aria-labelledby="slaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            
            <div class="modal-header">
                <span class="h4 zilla text-white m-0">Matriz de niveles de servicio (SLA)</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <p class="font-16 mb-1">En esta tabla se definen los tiempos de atención y solución de un problema según su severidad. </p>
                <table class="table table-bordered table-sm text-sm">
                    <thead class="bg-warning text-white text-center">
                        <tr>
                        <th>Nivel de severidad</th>
                        <th>Impacto y criterios</th>
                        <th>Tiempo de respuesta</th>
                        <th>Tiempo de restauración/resolución</th>
                        <th>Frecuencia de actualización</th>
                        <th>RCA requerido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>1 (Crítico)</td>
                        <td>
                            Sistema completamente inactivo.<br>
                            Impacto mayor a la organización.<br>
                            Sin alternativas de trabajo.
                        </td>
                        <td>15 min</td>
                        <td>
                            Restaurar en 4 horas máximo.<br>
                            Resolución definitiva con trabajo continuo máximo 5 días hábiles.
                        </td>
                        <td>
                            Cada hora hasta restauración.
                        </td>
                        <td>
                            Obligatorio en 5 días hábiles posteriores.
                        </td>
                        </tr>
                        <tr>
                        <td>2 (Alta)</td>
                        <td>
                            Afectación parcial en funcionalidad crítica.<br>
                            Múltiples usuarios impactados, pero con alternativa parcial.
                        </td>
                        <td>30 min</td>
                        <td>Resolución en 1 día</td>
                        <td>
                            Cada 4 horas hasta restauración.
                        </td>
                        <td>
                            Opcional según análisis de impacto.
                        </td>
                        </tr>
                        <tr>
                        <td>3 (Media)</td>
                        <td>
                            Afectación a funcionalidades no críticas.<br>
                            Operación general no comprometida.
                        </td>
                        <td>4 horas</td>
                        <td>Resolución en 10 días hábiles.</td>
                        <td>Semanal.</td>
                        <td>No aplica.</td>
                        </tr>
                        <tr>
                        <td>4 (Baja)</td>
                        <td>
                            Solicitudes de mejoras, ajustes estéticos o consultas.<br>
                            Sin impacto en la operación diaria.
                        </td>
                        <td>8 horas</td>
                        <td>Resolución en 20 días hábiles.</td>
                        <td>Cada 2 semanas.</td>
                        <td>No aplica.</td>
                        </tr>
                    </tbody>
                    </table>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
            
            </div>
        </div>
    </div>
<input type="hidden" value="{{env('ASSET_URL')}}" id="urlSystem">

@include('general.menu')

@include('general.loading')
<div class="page-wrapper">

    <div class="page-content" style="flex: 1;">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12">

                    <div class="card">

                        <div class="card-body">

                            <div class="row mb-4">

                                <div class="col-lg-4 col-md-6 col-sm-12">

                                    <div class="d-flex ">

                                        <div class="icon-main-div d-flex rounded bg-primary"
                                            style=" border: 2px solid #fff">

                                            <i
                                                class="icon {{ isset($act_submenu_route->sub_icon) ? $act_submenu_route->sub_icon : '' }} icon-main pl-3 pr-3 m-auto text-white"></i>

                                        </div>

                                        <div class="ml-2">

                                            <h1 class="mt-0 mb-0 header-title" style="color:#ff5900">

                                                {{ isset($act_submenu_route->sub_name) ? $act_submenu_route->sub_name : '' }}

                                            </h1>

                                            <p class="text-muted m-0 font-13">

                                                {{ isset($act_submenu_route->menu->men_name) ? $act_submenu_route->menu->men_name : '' }}



                                            </p>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-lg-8 col-md-6">

                                    @yield('filter')

                                </div>

                                <div class="col-12 mt-2" id="msg">
                                @if (session('status'))
                                        <div role="alert" class="alert alert-{{ session('status') }} border-0">
                                            {{ session('message') }}</div>
                                    @endif
                                </div>
                                <!-- <div class="col-12 mt-2" id="msg">
                                    @if (session('status'))
                                        <div role="alert" class="alert alert-{{ session('status') }} border-0">
                                            {{ session('message') }}</div>
                                    @endif
                                             <div role="alert" style="
                                        background-color: #d4edda;
                                        color: #155724;
                                        border: 1px solid #c3e6cb;
                                        padding: 10px;
                                        text-align: center;
                                        border-radius: 8px;
                                        font-family: Arial, sans-serif;
                                        font-size: 15px;
                                        max-width: 950px;
                                        margin: 10px auto;
                                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                    ">
                                        <i class="fas fa-info-circle" style="margin-right: 10px; font-size: 20px;"></i><br>
                                        <strong>Estimada comunidad:</strong><br>
                                        Soy León estará en un proceso de migración hacia la nube de Microsoft para mejorar la estabilidad, robustez y calidad de la experiencia.<br>
                                        A partir del <strong>viernes 4 de julio a las 10:00 p.m.</strong> y hasta el <strong>domingo 6 de julio a las 11:00 p.m.</strong>, la plataforma <strong>no estará disponible</strong>.<br>
                                        Por supuesto, <strong>toda la información será respaldada al 100%</strong>.<br>
                                        Gracias por su comprensión.
                                    </div>
                                </div> -->
                                
                            </div>


                            @yield('content')

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<input type="hidden" id="idValidtae" value="1">
</div>
@include('general.files')
<!-- jQuery  -->

<script src="{{ url('/public/js/jquery.min.js') }}"></script>
<script src="{{ url('public/js/general.js?').date('Ymds') }}"></script>
<script src="{{ url('/public/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('/public/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
@yield('footer')
<footer class="text-muted"> 

    Sistema versión {{ config('version.app_version') }} 

</footer>  
<script>
    $(document).ready(function() {

        var idValidtae=$('#idValidtae').val();
        if(idValidtae==0){
               $('#validateAccountUser').modal({backdrop: 'static', keyboard: false});
            }
        });

//         function validarCuenta(us_uIDAccount){

// const SITEURL = "{{ url('/') }}";
//     Swal.fire({
//         title: "¡Aviso!",
//         text: "¿Deseas validar tu cuenta?",
//         type: "warning",
//         cancelButtonText: 'Cancelar',
//         confirmButtonText: 'Si, validar',
//         showCancelButton: true,
//         confirmButtonColor: '#28a745',
//         cancelButtonColor: '#fc0505',
//     }).then((result) => {
//         if (result.value == true) {
//             $.ajax({
// type: 'POST',
// url: SITEURL+"/validate",
// data: {
//     us_uIDAccount : us_uIDAccount
// },
// beforeSend: function() {
//     $("#loader").show();
// },
// success: function(response) {
//     $("#loader").hide();
//     if (response==200) {
//         location.reload();

//     }else if (response.error) {
//         // Mostrar una alerta de error si es necesario
//         Swal.fire({
//             title: "¡Aviso!",
//             text: 'Acude a las oficinas de Desarrollo de sistemas(Edificio A)',
//             type: "warning",
//             confirmButtonText: 'Entendido'
//         });
//     }
// }
// }
// );

//         } else {
//         }
//     })

// }
function cerrarSesión(){
    Swal.fire({
            title: "¡Aviso!",
            text: 'Acude a las oficinas de Desarrollo de sistemas(Edificio A)',
            type: "warning",
            confirmButtonText: 'Entendido'
        }).then((result) => {
if (result.value == true) {
    // Redirige a una ruta de Laravel después de confirmar
    window.location.href = '{{ route('logout') }}'; // Cambia esto a la ruta que necesites
}
});

}
function checkConnection() {
            let connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            if (!navigator.onLine) {
                Swal.fire({
                    title: "Conexión a Internet nula",
                    text: "Por favor verifica tu conexión.",
                    icon: "error"
                });
            } 
        }

        // Verifica el estado inicial de la conexión
        checkConnection();

        // Escucha los eventos de conexión
        window.addEventListener('online', checkConnection);
        window.addEventListener('offline', () => {
            Swal.fire({
                title: "Conexión a Internet",
                text: "Te has desconectado. Por favor verifica tu conexión.",
                icon: "error"
            });
        });
    
</script>

<script>
        $(document).ready(function() {
            $('#campanaNotificaciones').parent('.dropdown').on('show.bs.dropdown', function() {
                // Solo si hay notificaciones no leídas
                var cantidad = parseInt($('.navbar-badge').text());
                if (cantidad > 0) {
                    $.ajax({
                        url: "{{ route('notifications.marcar_todas_leidas') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('.navbar-badge').fadeOut();
                            }
                        }
                    });
                }
            });
        });

// === Flags y constantes ===
    let modalAbierto = false;
    let sessionExpiredShown = false; // evita alertas duplicadas
    const APP_URL = "{{ env('APP_URL') }}";
    let lastActivity = Date.now();

    // === Alerta centralizada de sesión expirada ===
    function showSessionExpired() {
        // OJO: no bloqueamos por modalAbierto, porque el aviso previo puede seguir abierto
        if (sessionExpiredShown) return;
        sessionExpiredShown = true;

        // Por si quedara el modal de "Inactividad detectada" abierto, lo cerramos
        if (Swal.isVisible()) Swal.close();
        modalAbierto = true;

        Swal.fire({
            title: "Sesión expirada",
            text: "Tu sesión ha expirado por inactividad.",
            type: "warning",
            confirmButtonText: "Volver a iniciar",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            window.location.href = APP_URL + '/principal/menu';
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        let warningTimer;
        let logoutTimer;
        let activityDetected = false;

        function keepSessionAlive() {
            $.get(APP_URL + '/check-session');
        }

        // === Aviso tras X inactividad ===
        function showInactivityWarning() {
            if (sessionExpiredShown || modalAbierto) return;
            modalAbierto = true;

            Swal.fire({
                title: "Inactividad detectada",
                text: "¿Sigues ahí? Tu sesión se cerrará en 3 minuto si no haces nada.",
                type: "warning",
                confirmButtonText: "Seguir aquí",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                // Si hace clic en "Seguir aquí"
                if (result.value === true && !sessionExpiredShown) {
                    keepSessionAlive();
                    modalAbierto = false;
                    resetInactivityTimers();
                }
            });

            // Tras 3 minuto sin respuesta → mostrar "Sesión expirada"
            logoutTimer = setTimeout(() => {
                if (sessionExpiredShown) return;
                if (Swal.isVisible()) Swal.close(); // cerrar aviso si sigue abierto
                modalAbierto = false;               // liberar candado
                showSessionExpired();
            }, 3 * 60 * 1000); // tiempo de inactividad para mostrar la sesión cerrada
        }

        function resetInactivityTimers() {
            clearTimeout(warningTimer);
            clearTimeout(logoutTimer);
            if (!sessionExpiredShown) {
                warningTimer = setTimeout(showInactivityWarning, 5 * 60 * 1000); // tiempo de inactividad
            }
        }

        // Cualquier actividad reinicia timers
        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart', 'touchmove'].forEach(event => {
            document.addEventListener(event, () => {
                if (sessionExpiredShown) return;
                activityDetected = true;
                modalAbierto = false;
                resetInactivityTimers();
            }, { passive: true });
        });

        // Keep-alive cada minuto solo si hubo actividad reciente
        setInterval(() => {
            if (activityDetected && !sessionExpiredShown) {
                keepSessionAlive();
                activityDetected = false;
            }
        }, 60 * 1000);

        // Inicializa timers
        resetInactivityTimers();
    });

    // === Sondeo periódico del estado de sesión ===
    setInterval(function () {
        if (sessionExpiredShown) return;
        $.ajax({
            url: APP_URL + '/check-session',
            method: 'GET',
            success: function (response) {
                // Si tu endpoint devuelve { active: false }
                if (response && response.active === false) {
                    showSessionExpired();
                }
            },
            error: function (xhr) {
                // Códigos típicos de expiración CSRF/unauthorized
                if (xhr.status === 419 || xhr.status === 401 || xhr.status === 403) {
                    showSessionExpired();
                }
            }
        });
    }, 60 * 1000);

    setInterval(() => {
        const now = Date.now();
        const diff = now - lastActivity;
        if (diff > 1000 * 60 * 10) { // 10 minutos sin ejecutar el código (suspensión detectada)
            showSessionExpired();
        }
        lastActivity = now;
    }, 60 * 1000);
    </script>
    @livewireScripts
</body>



</html>
