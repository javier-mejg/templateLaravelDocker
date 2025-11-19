@extends('layout.main')

@section('header')
    <link href="{{ url('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection

@section('content')
    @if ($status == 200)

        @php $intento = 0; @endphp

        <div class="row justify-content-center">
            <h1>Bienvenido {{ $data['info']['nombre'] }}</h1>
        </div>
        <div class="row justify-content-center mb-4">
            <h2 style="color: gray">Matrícula: {{ $data['info']['matricula'] }}</h2>
        </div>

        <div class="row mb-4 ml-2 justify-content-center">

            {{-- 📌 Recorremos los resultados --}}
            @foreach ($data['resultados'] as $resultado)
                @php $intento++; @endphp

                <div class="card ml-5 px-0 border-round" style="width: 40rem;">
                    <div class="card-body ">
                        <div class="row">
                            <div class="card border-round ml-2 px-3 align-items-center" style="background: #ff5900">
                                <h3 class="card-title" style="color: white"><strong>Intento {{ $intento }}</strong></h3>
                            </div>
                        </div>

                        <h6 class="card-subtitle mb-2 text-muted">Periodo: {{ $resultado['periodo'] }}</h6>

                        <div class="row pt-0 pb-0">
                            <div class="card-body text-center">
                                <h2><strong>Puntaje</strong></h2>
                            </div>
                            <div class="card-body text-center">
                                <h2><strong>Lugar</strong></h2>
                            </div>
                        </div>

                        <div class="row ml-2 pb-5">
                            <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                                {{-- 📌 Aquí se imprime la gráfica --}}
                                <div id="GraphPuntuacion{{ $intento }}" style="width: 155px; height: 140px;"></div>
                            </div>
                            <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                                <h1 class="titulo-grande" style="color: #ff5900;">
                                    <strong>#{{ $resultado['lugar'] }}</strong>
                                </h1>
                            </div>
                        </div>

                        <div class="row">
                            <div class="card" style="width: 14rem;">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Decisión:</h6>
                                    <h2 class="card-title"
                                        style="color: {{ $resultado['decision'] == 'Admitido' ? '#ff5900' : 'red' }}">
                                        {{ $resultado['decision'] }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 📌 Guardamos datos para JustGage en JS --}}
                <script>
                    window.graficas = window.graficas || [];
                    window.graficas.push({
                        id: "GraphPuntuacion{{ $intento }}",
                        valor: {{ $resultado['puntaje'] * 10 }}
                                });
                </script>

            @endforeach

        </div>

    @else
        <div class="row justify-content-center">
            <h1>No existe información sobre este usuario</h1>
        </div>
    @endif
@endsection


@section('footer')
    <script src="plugins/justgage/justgage.js"></script>
    <script src="plugins/justgage/raphael-2.1.4.min.js"></script>

    <script>
        // Esperamos a que el DOM exista
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
@endsection