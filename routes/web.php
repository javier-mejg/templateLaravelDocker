<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ResultsController;

Route::get('/', action: function () {
    return view('auth.login');
});

Route::get('/login', 'App\Http\Controllers\Auth\AuthController@login')->name('login');
Route::get('/connect', 'App\Http\Controllers\Auth\AuthController@connect')->name('connect');

// Iniciar Sesión
// Route::get('/auth/microsoft/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');

// Route::get('/auth/microsoft/callback', [AuthController::class, 'callback'])->name('auth.callback');

// Cerrar Sesión

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    // Redirige al logout de Azure AD
    $logoutUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/logout?post_logout_redirect_uri=' . urlencode(url('/'));
    return redirect($logoutUrl);
})->name('logout.microsoft');
Route::group(['middleware' => ['web', 'MsGraphAuthenticated'], 'namespace' => 'App\Http\Controllers'], function () {

    Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout')->name('logout');
    Route::get('/resultados',[ResultsController::class, 'index']);
});