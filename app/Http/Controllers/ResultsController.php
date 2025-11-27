<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;


class ResultsController extends Controller
{
    public function index()
    {
        $titulo = 'Resultados';
        $apiKey = "@Qu3r3Dev!T1-Ana";
        $baseUrl = config('app.base_url');
        $url = "{$baseUrl}/api/propedeutico-med/estudiantes/resultados";
        $correo = base64_decode(Auth::user()->email);
        $data = array();

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',

            ])->post($url, [
                        'correo' => $correo,
                    ]);

            $status = $response->status();
            $data = $response->json();
            // Normalización de datos
            if (isset($data['resultados']) && is_array($data['resultados'])) {
                // Ordenar de menor a mayor/ascendente
                usort($data['resultados'], function ($a, $b) {
                    return $a['periodo'] <=> $b['periodo']; // ascendente
                });
                // Cambiar cualquier valor que no sea 'Admitido' a 'No admitido' (útil por las decisiones con 'Segunda oportunidad' y no estandarizados)
                foreach ($data['resultados'] as &$resultado) {
                    if (isset($resultado['decision'])) {
                        if ($resultado['decision'] !== 'Admitido') {
                            $resultado['decision'] = 'No admitido';
                        }
                    }
                }
                unset($resultado);
            }
            $intento = 0;
            return view('resultados', compact('data', 'titulo', 'status', 'intento'));

        } catch (\Exception $e) {
            return ['error' => 'No se puede enviar la información'];
        }
    }
}

