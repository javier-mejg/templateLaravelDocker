<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ResultsController;

Route::get('/', function () {
    return view('layout.main');
});

Route::get('/auth/microsoft/redirect', function () {
    $response = Socialite::driver('microsoft')->scopes(
        config('services.microsoft.scopes', [])
    )->redirect();

    // Muestra la URL a la que vas a saltar (incluye el redirect_uri que verá Microsoft)
    dd($response->getTargetUrl());
});

Route::get('/resultados',[ResultsController::class, 'index']);