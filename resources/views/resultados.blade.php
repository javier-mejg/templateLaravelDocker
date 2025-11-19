@extends('layout.main')
@section('header')
    <!-- select2 -->

    <link href="{{url('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endsection

@section('content')
    @if (($status == 200))
        <div class="row justify-content-center">
            <h1>Bienvenido {{ $data['info']['nombre'] }}</h1>
        </div>
        <div class="row justify-content-center mb-4">
            <h2 style="color: gray">Matrícula: {{ $data['info']['matricula'] }}</h2>
        </div>
        <div class="row mb-4 ml-2 justify-content-center">
            <!-- Forma específica de acceder a elementos de un JSON -->
            @foreach ($data['resultados'] as $resultado)

                <!-- <li>
                                                                                                                                                                                            Periodo: {{ $resultado['periodo'] }}
                                                                                                                                                                                        </li>
                                                                                                                                                                                        <li>
                                                                                                                                                                                            Promedio: {{ $resultado['puntaje'] }}
                                                                                                                                                                                        </li>
                                                                                                                                                                                        <li>
                                                                                                                                                                                            Lugar: {{ $resultado['lugar'] }}
                                                                                                                                                                                        </li>
                                                                                                                                                                                        <li>
                                                                                                                                                                                            Decisión: {{ $resultado['decision'] }}
                                                                                                                                                                                        </li>
                                                                                                                                                                                        <br> -->

                <div class="card ml-5 px-0 border-round" style="width: 40rem;">
                    <div class="card-body ">
                        <div class="card border-round align-items-center" style="background: #ff5900">
                            <h3 class="card-title" style="color: white"><strong>Intento #{{ $intento = $intento + 1 }}</strong></h3>
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted">Periodo: {{ $resultado['periodo'] }}</h6> -->
                        <div class="row">
                            <div class="card-body text-center">
                                <h2 class="card-subtitle mb-2 "><strong>Puntaje</strong></h2>
                            </div>
                            <div class="card-body text-center">
                                <h2 class="card-subtitle mb-2 "><strong>Lugar</strong></h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-body">
                                <div id="Counter_2"></div>
                            </div>
                            <div class="card-body">
                                <h2 class="card-subtitle mb-2 "><strong>Lugar</strong></h2>
                            </div>
                        </div>
                        <div class="row">

                            <div class="card " style="width: 14rem;">
                                <div class="card-body">
                                    <div class="row">
                                        <h6 class="card-subtitle mb-2 text-muted">Decisión:</h6>
                                        <h2 class="card-title" {{ ($resultado['decision'] == 'Admitido') ? 'style="color: #ff5900"' : 'style="color: red"' }}>{{ $resultado['decision'] }}</h2>
                                        @if ($resultado['decision'] == 'Admitido')
                                            <!-- <i class="bi bi-check-circle" style="font-size: 2rem; color: green;"></i> -->
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if ($intento > 1)

            @endif
            <div class="card border-round border-dotted ml-2 px-3 p-3" style="width: 40rem; opacity: 0.3">
                <div class="p-3 mx-auto" style="width: 14rem;">
                    <h3 class="card-title">Intento #{{ $intento = $intento + 1 }}</h3>
                    <!-- <div class="card-body">
                                                                    <h6 class="card-subtitle mb-2 text-muted">No te rindas...</h6>
                                                                    <h2 class="card-title"> ¡Todavía puedes intentarlo!</h5> -->
                </div>
            </div>
        </div>


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
        Counter_2 = new JustGage({
            id: "Counter_2",
            value: {{ $resultado['puntaje'] * 10 }},
            min: 0,
            max: 100,
            width: 100,
            height: 100,
            labelMinFontSize: 30,
            donut: true,
            gaugeWidthScale: 0.6,
            counter: true,
            hideInnerShadow: true
        });
    </script>
@endsection