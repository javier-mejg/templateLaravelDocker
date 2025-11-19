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
        $data=array();

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',

            ])->post($url, [
                        'correo' => $correo,
                    ]);

                $status = $response->status();
                $data = $response->json();
                $intento = 0;
                return view('resultados', compact('data', 'titulo','status', 'intento'));
           
        } catch (\Exception $e) {
            return ['error' => 'No se puede enviar la información'];
        }
    }
}

