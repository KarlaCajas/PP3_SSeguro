<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para redireccionar clientes al dashboard apropiado
 */
class RedirectClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y tiene rol de cliente
        if (auth()->check() && auth()->user()->hasRole('cliente')) {
            // Si está accediendo al dashboard principal, redirigir al dashboard de cliente
            if ($request->is('dashboard')) {
                return redirect()->route('client.dashboard');
            }
        }

        return $next($request);
    }
}
