<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para proteger el acceso a Telescope
 * Solo usuarios administradores autenticados y activos pueden acceder
 */
class TelescopeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            abort(403, 'Acceso denegado: Usuario no autenticado');
        }

        $user = auth()->user();

        // Verificar que el usuario esté activo
        if (!$user->is_active) {
            abort(403, 'Acceso denegado: Usuario inactivo');
        }

        // Verificar que el usuario tenga rol de administrador
        if (!$user->hasRole('admin')) {
            abort(403, 'Acceso denegado: Se requieren privilegios de administrador');
        }

        // Registrar el acceso a Telescope en los logs
        logger('Acceso a Telescope', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        return $next($request);
    }
}
