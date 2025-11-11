<?php

use App\Http\Controllers\Auth\AuthController;
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


// Route::get('/auth/microsoft/redirect', function () {
//     return Socialite::driver('microsoft')
//         ->scopes(config('services.microsoft.scopes', []))
//         ->redirect();
// })->name('microsoft.login');

// Route::get('/auth/microsoft/callback', function () {
//     $user = Socialite::driver('microsoft')->user();

//     // Aquí puedes manejar la lógica de inicio de sesión o registro
//     // dd($user);
// });

Route::get('/resultados',[ResultsController::class, 'index']);