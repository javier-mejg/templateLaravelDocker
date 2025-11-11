<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Dcblogdev\MsGraph\Facades\MsGraph;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function connect(Request $request)
    {
        // Verifica si el parámetro 'redirect_to' está presente en la URL y almacénalo en la sesión
      if (request()->has('redirect_to')) {
        session(['redirect_to' => request()->query('redirect_to')]);
    }
        return MsGraph::connect();
    }

    public function logout()
    {
        session()->forget('info');
        session()->flush();
        return MsGraph::disconnect('/');
    }
}
