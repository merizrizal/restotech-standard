<?php

namespace Restotech\Standard\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;

class BackOfficeAuthenticate extends Authenticate
{
    protected function redirectTo(Request $request)
    {
        return route('restotech.standard.back_office.login');
    }
}
