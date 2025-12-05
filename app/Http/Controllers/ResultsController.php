<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\RedactarCorreoMailable;
use Auth;


class ResultsController extends Controller
{
    public function index()
    {
        $correo_apelaciones = "javier_mejia@anahuac.mx"; // propedeutico.qro@anahuac.mx
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

                $periodos = array_unique(array_column($data['resultados'], 'periodo'));
            }
            $intento = 0;
            return view('resultados', compact('data', 'titulo', 'status', 'intento', 'correo_apelaciones', 'periodos'));

        } catch (\Exception $e) {
            return ['error' => 'No se puede enviar la información'];
        }
    }
    public function enviarCorreo(Request $request)
    {
        // Validación
        $data = $request->validate([
            'correo_apelaciones' => 'required|email',
            'periodo' => 'required|string',
            'comentarios' => 'required|string',
        ]);

        try {
            Mail::to($data['correo_apelaciones'])
                ->send(new RedactarCorreoMailable($data['periodo'], $data['comentarios']));

            return response()->json([
                'ok' => true,
                'mensaje' => 'El correo se envió correctamente.',
            ]);
        } catch (\Throwable $th) {
            // 👇 LOG para ver el error en laravel.log
            \Log::error('Error enviando correo', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            // 👇 TEMPORAL para que tú lo veas directo en el navegador (local)
            return response()->json([
                'ok' => false,
                'mensaje' => $th->getMessage(), // <- en vez del texto genérico
            ], 500);
        }
    }

}

