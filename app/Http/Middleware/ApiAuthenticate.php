<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;


class ApiAuthenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Nunca redirigir → bloquear redirecciones en API
        return null;
    }

    protected function unauthenticated($request, array $guards)
    {
        throw new \Illuminate\Auth\AuthenticationException('Unauthenticated.', $guards);
    }
}
