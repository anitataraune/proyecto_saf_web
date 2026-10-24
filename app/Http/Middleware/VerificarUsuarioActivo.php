<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarUsuarioActivo
{
    /**
     * Bloquea las peticiones de usuarios desactivados aunque conserven un token válido.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && ! $usuario->activo) {
            $usuario->currentAccessToken()?->delete();

            return response()->json([
                'mensaje' => 'Este usuario se encuentra desactivado.',
            ], 403);
        }

        return $next($request);
    }
}
