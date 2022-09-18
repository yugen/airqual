<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\ActionRequest;
use Laravel\Socialite\Facades\Socialite;
use Lorisleiva\Actions\Concerns\AsController;

class UserAuthenticateProvided
{
    use AsController;

    /**
     * Update or create the user with data given by the OAUTH provider
     */
    public function handle($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();

        $user = User::updateOrCreate([
            'provider' => $provider,
            'provider_id' => $user->getId(),
        ], [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'access_token' => $user->token,
            'refresh_token' => $user->refreshToken,
        ]);

        Auth::login($user);

        return $user;
    }
}
