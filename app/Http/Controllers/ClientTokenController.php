<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ClientTokenController extends Controller
{
    /**
     * Mostrar tokens del cliente autenticado
     */
    public function index()
    {
        $user = auth()->user();
        
        // Obtener tokens de Sanctum (hasheados)
        $sanctumTokens = $user->tokens;
        
        // Obtener tokens en texto plano (INSEGURO)
        $plainTextTokens = $user->getActivePlainTextTokens();

        return view('client.tokens', compact('sanctumTokens', 'plainTextTokens'));
    }

    /**
     * Crear un nuevo token para el cliente autenticado
     */
    public function store(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        // Crear token de Sanctum (hasheado)
        $sanctumToken = $user->createToken($request->token_name)->plainTextToken;
        
        // Crear token en texto plano (INSEGURO)
        $plainTextToken = $user->createPlainTextToken($request->token_name);

        session()->flash('nuevo_token', $sanctumToken);
        session()->flash('nuevo_plain_token', $plainTextToken->token);

        return redirect()->route('client.tokens')
            ->with('success', 'Nuevo token creado exitosamente');
    }

    /**
     * Eliminar un token específico
     */
    public function destroy($tokenId, $type = 'sanctum')
    {
        $user = auth()->user();

        if ($type === 'plaintext') {
            $token = $user->plainTextTokens()->findOrFail($tokenId);
            $token->delete();
        } else {
            $token = $user->tokens()->findOrFail($tokenId);
            $token->delete();
        }

        return redirect()->route('client.tokens')
            ->with('success', 'Token eliminado exitosamente');
    }
}