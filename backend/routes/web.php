<?php

use App\Actions\UserAuthenticateProvided;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\AuthRedirectRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Redirect to OAuth Provider
 */
Route::get('/auth/redirect', function (AuthRedirectRequest $request) {
    return Socialite::driver($request->provider)->redirect();
});

/**
 * Redirect to OAuth callback
 */
Route::get('/auth/callback/{provider}', function ($provider) {
    (new UserAuthenticateProvided)->handle($provider);
    return redirect('/');
});

Route::get('/auth/logout', function () {
    Auth::logout();
    return response()->redirectTo('/');
});
