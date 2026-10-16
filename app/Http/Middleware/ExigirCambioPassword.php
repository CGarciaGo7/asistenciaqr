<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExigirCambioPassword
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $usuario = $request->user();

        if (
            $usuario &&
            $usuario->debe_cambiar_password &&
            ! $request->routeIs(
                'password.first-change.edit',
                'password.first-change.update',
                'logout'
            )
        ) {
            return redirect()->route('password.first-change.edit');
        }

        return $next($request);
    }
}
