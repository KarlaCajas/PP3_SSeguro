<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PlainTextToken;
use App\Models\User;

/**
 * ADVERTENCIA: Este middleware usa tokens en texto plano
 * Esto es INSEGURO y solo debe usarse para desarrollo/educación
 */
class AuthenticatePlainTextToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return response()->json([
                'message' => 'Token de acceso requerido',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Buscar el token en la base de datos (texto plano - INSEGURO)
        $plainTextToken = PlainTextToken::where('token', $token)->first();

        if (!$plainTextToken) {
            return response()->json([
                'message' => 'Token de acceso inválido',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Verificar si el token ha expirado
        if ($plainTextToken->isExpired()) {
            return response()->json([
                'message' => 'Token de acceso expirado',
                'error' => 'Token expired'
            ], 401);
        }

        // Cargar el usuario
        $user = $plainTextToken->user;

        if (!$user || !$user->is_active) {
            return response()->json([
                'message' => 'Usuario inactivo o no encontrado',
                'error' => 'User inactive'
            ], 401);
        }

        // Autenticar al usuario
        auth()->login($user);
        
        // Marcar el token como usado
        $plainTextToken->markAsUsed();

        // Agregar el token al request para uso posterior
        $request->attributes->set('plain_text_token', $plainTextToken);

        return $next($request);
    }

    /**
     * Extraer el token del request
     */
    private function getTokenFromRequest(Request $request): ?string
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        return substr($authHeader, 7); // Remover "Bearer "
    }
}
