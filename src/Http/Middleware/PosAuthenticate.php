<?php

namespace Restotech\Standard\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;

class PosAuthenticate extends Authenticate
{
    protected function redirectTo(Request $request)
    {
        return route('restotech.standard.pos.login');
    }
}
