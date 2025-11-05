<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ResultsController extends Controller
{
    public function index()
    {
        $apiKey = "@Qu3r3Dev!T1-Ana";
        $baseUrl = "https://qa-soyleonadmin.anahuac-qro.com";
        $url = "{$baseUrl}/api/propedeutico-med/estudiantes/resultados";
        $correo = "fulano.h@anahuac.mx";

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',

            ])->post($url, [
                        'correo' => $correo,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return view('resultados', compact('data'));
            } else {

                return redirect()->back()->with('error', $response->json());
            }
        } catch (\Exception $e) {
            return ['error' => 'No se puede enviar la información'];
        }
    }
}

