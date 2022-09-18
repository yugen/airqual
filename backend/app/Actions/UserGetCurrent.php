<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsController;

class UserGetCurrent
{
    use AsController;

    public function handle(): ?User
    {
        $user = Auth::user();
        return $user;
    }

    public function asController()
    {
        return $this->handle() ?? response('No authenticated user.', 404);
    }

}
