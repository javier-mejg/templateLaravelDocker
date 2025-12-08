@extends('layout.main')

@section('header')
    <link href="{{ url('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection

@section('content')
    @if ($status == 200)
        <div class="row justify-content-center">
            <h1>Bienvenido a tus resultados, {{ $data['info']['nombre'] }} [{{ $data['info']['matricula'] }}]</h1>
        </div>

        @php $intento = 0; @endphp

        <div class="row mb-4 justify-content-center">

            {{-- Recorremos los resultados --}}
            @foreach ($data['resultados'] as $resultado)
                @php $intento++; @endphp

                <div class="form-group col-md-6 mb-4 px-3">
                    <div class="card px-0 border-round h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="card border-round px-3 align-items-center" style="background: #ff5900">
                                    <h3 class="card-title text-white">
                                        <strong>Intento {{ $intento }}</strong>
                                    </h3>
                                </div>
                            </div>

                            <h6 class="card-subtitle mb-2 text-muted">
                                Periodo: {{ $resultado['periodo'] }}
                            </h6>

                            <div class="row">
                                <div class="col text-center">
                                    <h2><strong>Puntaje</strong></h2>
                                </div>
                                <div class="col text-center">
                                    <h2><strong>Lugar</strong></h2>
                                </div>
                            </div>

                            <div class="row pb-4">
                                <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                                    <div id="GraphPuntuacion{{ $intento }}" style="width: 155px; height: 140px;"></div>
                                </div>
                                <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                                    <h1 class="titulo-grande" style="color: #ff5900;">
                                        <strong>#{{ $resultado['lugar'] }}</strong>
                                    </h1>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-center">
                                    <h2 class="card-title">
                                        @if ($resultado['decision'] == 'Admitido')
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger"></i>
                                        @endif
                                        {{ $resultado['decision'] }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos para JustGage --}}
                <script>
                    window.graficas = window.graficas || [];
                    window.graficas.push({
                        id: "GraphPuntuacion{{ $intento }}",
                        valor: {{ $resultado['puntaje'] * 10 }}
                                                                                                                                                                                                                                                                                                                                                        });
                </script>
            @endforeach

            {{-- Card placeholder si falta intento --}}
            @if (($intento < 2) && ($resultado['decision'] != 'Admitido'))
                <div class="form-group col-md-6 mb-4 px-3">
                    <div class="card px-0 border-round border-dotted bg-transparent h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="card border-round px-3 align-items-center" style="background: #ff5900">
                                    <h3 class="card-title text-white">
                                        <strong>Intento {{ $intento + 1 }}</strong>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- MENSAJES FINALES --}}
        <div class="row justify-content-center mb-4">

            @if ($resultado['decision'] == 'Admitido')
                <div class="form-group col-12 mb-4 px-3">
                    <div class="card px-0 border-round success h-100">
                        <div class="card-body">
                            <div class="row align-items-center">

                                {{-- TEXTO --}}
                                <div class="col-12 col-md-8">
                                    <h2 class="ml-2 text-size-title"><strong>¡Felicidades!</strong></h2>
                                    <h4 class="ml-2 text-size-subtitle">Tienes todo lo necesario para ser un león.</h4>
                                </div>

                                {{-- IMAGEN --}}
                                <div class="col-12 col-md-4 text-center text-md-right mt-3 mt-md-0">
                                    <img class="size img-fluid" src="{{ url('img/leo/leo_success.png') }}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif


            @if ($resultado['decision'] == 'No admitido' && $intento < 2)
                <div class="form-group col-12 mb-4 px-3">
                    <div class="card px-0 border-round retry h-100">
                        <div class="card-body">
                            <div class="row align-items-center">

                                {{-- TEXTO --}}
                                <div class="col-12 col-md-8">
                                    <h2 class="ml-2 text-size-title"><strong>¡No te rindas!</strong></h2>
                                    <h4 class="ml-2 text-size-subtitle">Todavía tienes oportunidad para tomar el curso propedéutico
                                        una vez más.</h4>
                                    {{-- Botón que abre el modal --}}
                                    <button type="button" class="btn ml-2 btn-primary btn-abrir-modal-redactar">
                                        Redactar correo
                                    </button>
                                </div>


                                {{-- IMAGEN (opcional) --}}
                                <div class="col-12 col-md-4 text-center text-md-right mt-3 mt-md-0">
                                    <img class="size img-fluid" src="{{ url('img/leo/leo_retry.png') }}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif


            @if ($resultado['decision'] == 'No admitido' && $intento > 1)
                <div class="form-group col-12 mb-4 px-3">
                    <div class="card px-0 border-round fail h-100">
                        <div class="card-body">
                            <div class="row align-items-center">

                                {{-- TEXTO --}}
                                <div class="col-12 col-md-8">
                                    <h2 class="ml-2 text-size-title"><strong>Se acabaron tus oportunidades...</strong></h2>
                                    <h4 class="ml-2 text-size-subtitle">Bien intentado, pero lamentablemente se acabaron tus
                                        intentos.</h4>
                                    <h3 class="ml-2 ">¿Tienes alguna duda o no estás de acuerdo con tus resulados?
                                        Envía un correo a {{ $correo_apelaciones }}</h4>
                                        {{-- Botón que abre el modal --}}
                                        <div class="row justify-content-center mb-4">
                                            <button type="button" class="btn btn-primary btn-abrir-modal-redactar">
                                                Redactar correo
                                            </button>
                                        </div>

                                </div>

                                {{-- IMAGEN (opcional) --}}
                                <div class="col-12 col-md-4 text-center text-md-right mt-3 mt-md-0">
                                    <img class="size img-fluid" src="{{ url('img/leo/leo_fail.png') }}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif


        </div>

    @else
        <div class="row justify-content-center">
            <div class="form-group col-12 mb-4 px-3">
                <div class="card px-0 border-round fail h-100">
                    <div class="card-body">
                        <div class="row align-items-center">

                            {{-- TEXTO --}}
                            <div class="col-12 col-md-8">
                                <h2 class="ml-2 text-size-title"><strong>Lo sentimos...</strong></h2>
                                <h4 class="ml-2 text-size-subtitle">No encontramos información para tu usuario, contacta a
                                    tatiana.rivera@anahuac.mx
                                    para validar la información.</h4>
                            </div>

                            {{-- IMAGEN (opcional) --}}
                            <div class="col-12 col-md-4 text-center text-md-right mt-3 mt-md-0">
                                <img class="size img-fluid" src="{{ url('img/leo/leo_fail.png') }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif
    <x-modal-email :correoApelaciones="$correo_apelaciones" :periodos="$periodos" :action="route('resultados.correo')"
        :nombreUsuario="$data['info']['nombre'] ?? null" :idUsuario="$data['info']['matricula']"
        :correoUsuario="$data['resultados'][0]['correo'] ?? null" />

    


@endsection


@section('footer')
    <script src="plugins/justgage/justgage.js"></script>
    <script src="plugins/justgage/raphael-2.1.4.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (!window.graficas) return;

            window.graficas.forEach(g => {
                new JustGage({
                    id: g.id,
                    value: g.valor,
                    min: 0,
                    max: 100,
                    width: 140,
                    height: 140,
                    donut: true,
                    gaugeWidthScale: 0.4,
                    counter: true,
                    hideInnerShadow: true
                });
            });
        });
    </script>
    {{-- JS del componente --}}
    <script>
        (function () {
            const buttonsAbrirModal = document.querySelectorAll('.btn-abrir-modal-redactar');
            const modalOverlay = document.getElementById('modal-redactar-overlay');
            const btnCancelar = document.getElementById('btn-cancelar-modal');
            const form = document.getElementById('form-redactar-correo');
            const selectPeriodo = document.getElementById('select-periodo');
            const textareaComentarios = document.getElementById('textarea-comentarios');

            const loader = document.getElementById('loader');

            const urlEnvio = "{{ route('resultados.correo') }}";
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Mostrar modal desde cualquier botón
            buttonsAbrirModal.forEach(btn => {
                btn.addEventListener('click', function () {
                    modalOverlay.style.display = 'flex';
                });
            });

            // Cancelar modal
            btnCancelar.addEventListener('click', function () {
                modalOverlay.style.display = 'none';
            });


            // Envío del formulario
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const periodo = selectPeriodo.value.trim();
                const comentarios = textareaComentarios.value.trim();

                if (!periodo) {
                    Swal.fire({
                        title: 'Atención',
                        text: 'Debes seleccionar un periodo.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (!comentarios) {
                    Swal.fire({
                        title: 'Atención',
                        text: 'Debes escribir comentarios.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Mostrar loader
                if (loader) {
                    loader.style.display = 'flex';
                }

                try {
                    const formData = new FormData(form);

                    const resp = await fetch(urlEnvio, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await resp.json();

                    // Ocultar loader
                    if (loader) {
                        loader.style.display = 'none';
                    }

                    // Cerrar modal
                    modalOverlay.style.display = 'none';

                    // Limpiar formulario
                    selectPeriodo.value = '';
                    textareaComentarios.value = '';

                    if (data.status === 'success') {
                        Swal.fire({
                            title: '¡Correo enviado!',
                            text: data.mensaje || 'El correo se envió correctamente.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'No se pudo enviar el correo',
                            text: data.mensaje || 'Ocurrió un problema al enviar el correo.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }

                } catch (error) {
                    console.error(error);

                    // Ocultar loader
                    if (loader) {
                        loader.style.display = 'none';
                    }

                    // Cerrar modal por si quedó abierto
                    modalOverlay.style.display = 'none';

                    Swal.fire({
                        title: 'Lo sentimos',
                        text: 'Ocurrió un error inesperado al enviar el correo. Intenta nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });


        })();
    </script>

@endsection