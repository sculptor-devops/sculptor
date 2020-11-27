<?php

namespace App\Events;

use Illuminate\Support\Facades\Auth;
use Sculptor\Agent\Facades\Logs;

class AuthLoginEventHandler
{
    public function login()
    {
        $user = Auth::user();

        Logs::security()->info("User {$user->email} logged in");
    }

    public function attempt($credentials)
    {
        //
    }

    public function lockout($lockout)
    {
        Logs::security()->warning("User " . $lockout->request->input('email') . ' lockout');
    }
}
