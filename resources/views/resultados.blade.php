
<h1>Bienvenido {{ $data['info']['nombre'] }}: Matrícula: {{ $data['info']['matricula'] }}</h1>
<!-- Forma específica de acceder a elementos de un JSON -->
@foreach ($data['resultados'] as $resultado)
    <li>
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
    <br>
@endforeach