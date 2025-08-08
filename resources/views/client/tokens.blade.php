<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <div class="px-6 py-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        🔑 Tokens de Acceso API
                    </h2>
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Usa estos tokens para acceder a la API de registro de pagos. 
                            <strong>Importante:</strong> Guarda el token en un lugar seguro, solo se muestra una vez.
                        </p>
                    </div>

                    <!-- Mostrar nuevo token generado -->
                    @if(session('nuevo_token'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 mb-2">✅ Nuevo Token Generado</h3>
                            <p class="text-sm text-green-700 mb-3">
                                Copia y guarda este token ahora. No se volverá a mostrar:
                            </p>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       id="nuevo-token" 
                                       value="{{ session('nuevo_token') }}" 
                                       readonly 
                                       class="flex-1 px-3 py-2 bg-white border border-green-300 rounded-md text-sm font-mono text-green-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <button onclick="copiarToken('nuevo-token')" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                    📋 Copiar
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Token Sanctum desde session flash del admin -->
                    @if(session('api_token'))
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-2">🆕 Token Sanctum Creado (Hasheado - Seguro)</h3>
                            <p class="text-sm text-blue-700 mb-3">
                                El administrador ha generado un nuevo token Sanctum para ti:
                            </p>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       id="admin-token" 
                                       value="{{ session('api_token') }}" 
                                       readonly 
                                       class="flex-1 px-3 py-2 bg-white border border-blue-300 rounded-md text-sm font-mono text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button onclick="copiarToken('admin-token')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    📋 Copiar
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Token en texto plano desde session flash del admin -->
                    @if(session('plain_text_token'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-800 mb-2">⚠️ Token en Texto Plano (INSEGURO)</h3>
                            <p class="text-sm text-red-700 mb-3">
                                <strong>ADVERTENCIA:</strong> Este token se almacena en texto plano y es inseguro. Solo para desarrollo/educación:
                            </p>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       id="plain-token" 
                                       value="{{ session('plain_text_token') }}" 
                                       readonly 
                                       class="flex-1 px-3 py-2 bg-white border border-red-300 rounded-md text-sm font-mono text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <button onclick="copiarToken('plain-token')" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                                    📋 Copiar
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Nuevo token en texto plano generado -->
                    @if(session('nuevo_plain_token'))
                        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-orange-800 mb-2">⚠️ Nuevo Token en Texto Plano (INSEGURO)</h3>
                            <p class="text-sm text-orange-700 mb-3">
                                <strong>ADVERTENCIA:</strong> Este token se almacena en texto plano. Cópialo ahora:
                            </p>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       id="nuevo-plain-token" 
                                       value="{{ session('nuevo_plain_token') }}" 
                                       readonly 
                                       class="flex-1 px-3 py-2 bg-white border border-orange-300 rounded-md text-sm font-mono text-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <button onclick="copiarToken('nuevo-plain-token')" 
                                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                                    📋 Copiar
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Formulario para crear nuevo token -->
                    <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">➕ Crear Nuevo Token</h3>
                        <form action="{{ route('client.tokens.store') }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <input type="text" 
                                   name="token_name" 
                                   placeholder="Nombre del token (ej: Mi Token API)" 
                                   required 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                🔑 Crear Token
                            </button>
                        </form>
                    </div>

                    <!-- Información de tokens Sanctum existentes (hasheados) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">📊 Tokens Sanctum (Seguros - Hasheados)</h3>
                        
                        @if(isset($sanctumTokens) && $sanctumTokens->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nombre
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Creado
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Último uso
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sanctumTokens as $token)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $token->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $token->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $token->last_used_at ? $token->last_used_at->format('d/m/Y H:i') : 'Nunca usado' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form action="{{ route('client.tokens.destroy', [$token->id, 'sanctum']) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('¿Estás seguro de eliminar este token?')"
                                                            class="text-red-600 hover:text-red-900">
                                                        🗑️ Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No tienes tokens Sanctum activos.</p>
                        @endif
                    </div>

                    <!-- Información de tokens en texto plano existentes (INSEGUROS) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-3">⚠️ Tokens en Texto Plano (INSEGUROS)</h3>
                        
                        @if(isset($plainTextTokens) && $plainTextTokens->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-red-200">
                                    <thead class="bg-red-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">
                                                Nombre
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">
                                                Token (Texto Plano)
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">
                                                Creado
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">
                                                Último uso
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-red-100">
                                        @foreach($plainTextTokens as $token)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $token->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <input type="text" 
                                                           id="token-{{ $token->id }}" 
                                                           value="{{ $token->token }}" 
                                                           readonly 
                                                           class="flex-1 px-2 py-1 bg-red-50 border border-red-300 rounded text-xs font-mono text-red-800">
                                                    <button onclick="copiarToken('token-{{ $token->id }}')" 
                                                            class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                                        📋
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $token->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $token->last_used_at ? $token->last_used_at->format('d/m/Y H:i') : 'Nunca usado' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form action="{{ route('client.tokens.destroy', [$token->id, 'plaintext']) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('¿Estás seguro de eliminar este token?')"
                                                            class="text-red-600 hover:text-red-900">
                                                        🗑️ Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No tienes tokens en texto plano activos.</p>
                        @endif
                    </div>

                    <!-- Información de uso de la API -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">📚 Cómo usar la API</h4>
                        <div class="text-sm text-gray-600 space-y-2">
                            <p><strong>URLs Base:</strong></p>
                            <ul class="ml-4 space-y-1">
                                <li>• <strong>V1 (Sanctum - Seguro):</strong> <code class="bg-gray-200 px-1 rounded">{{ url('/api/v1') }}</code></li>
                                <li>• <strong>V2 (Texto Plano - INSEGURO):</strong> <code class="bg-red-200 px-1 rounded">{{ url('/api/v2') }}</code></li>
                            </ul>
                            <p><strong>Header requerido:</strong> <code class="bg-gray-200 px-1 rounded">Authorization: Bearer TU_TOKEN</code></p>
                            <p><strong>Endpoints disponibles:</strong></p>
                            <ul class="ml-4 space-y-1">
                                <li>• <code class="bg-gray-200 px-1 rounded">GET /facturas-pendientes</code> - Ver facturas pendientes</li>
                                <li>• <code class="bg-gray-200 px-1 rounded">POST /pagos</code> - Registrar un pago</li>
                                <li>• <code class="bg-gray-200 px-1 rounded">GET /mis-pagos</code> - Ver historial de pagos</li>
                                <li>• <code class="bg-gray-200 px-1 rounded">GET /user</code> - Ver información del usuario</li>
                                <li>• <code class="bg-gray-200 px-1 rounded">GET /facturas</code> - Ver todas las facturas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copiarToken(inputId) {
            const input = document.getElementById(inputId);
            input.select();
            input.setSelectionRange(0, 99999); // Para dispositivos móviles
            
            try {
                document.execCommand('copy');
                // Mostrar confirmación
                const button = input.nextElementSibling;
                const originalText = button.innerHTML;
                button.innerHTML = '✅ Copiado';
                button.classList.add('bg-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600');
                }, 2000);
            } catch (err) {
                alert('Error al copiar el token');
            }
        }
    </script>
</x-app-layout>
