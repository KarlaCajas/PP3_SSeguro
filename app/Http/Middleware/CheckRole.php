<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar roles de usuario usando Spatie
 */
class CheckRole
{
    /**
     * Manejar una solicitud entrante
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            abort(401, 'No autorizado');
        }

        // Verificar si el usuario tiene al menos uno de los roles requeridos
        if (!auth()->user()->hasAnyRole($roles)) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}