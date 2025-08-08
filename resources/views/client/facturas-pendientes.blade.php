<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Facturas Pendientes de Pago
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Encabezado --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Facturas Pendientes de Pago</h1>
            <p class="mt-2 text-gray-600">Registra el pago de tus facturas usando la API REST</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('client.facturas') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Ver Todas las Facturas
            </a>
        </div>
    </div>

    {{-- Información de Tokens API --}}
    <div class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-purple-800">
                    Tokens de Acceso API
                </h3>
                <div class="mt-2 text-sm text-purple-700">
                    @if($tokens->count() > 0)
                        <p class="mb-3">Usa uno de estos tokens para autenticarte en la API:</p>
                        <div class="space-y-2">
                            @foreach($tokens as $token)
                                <div class="flex items-center justify-between p-3 bg-white rounded border">
                                    <div>
                                        <span class="font-mono text-xs text-gray-600">Token #{{ $token->id }}</span>
                                        <div class="text-xs text-gray-500">
                                            Creado: {{ $token->created_at->format('d/m/Y H:i') }}
                                            @if($token->last_used_at)
                                                | Último uso: {{ $token->last_used_at->format('d/m/Y H:i') }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs">
                            <strong>Importante:</strong> Usa el header <code class="bg-purple-100 px-1 rounded">Authorization: Bearer {tu-token}</code> en todas las peticiones API.
                        </p>
                    @else
                        <p>No tienes tokens de acceso API configurados.</p>
                        <p class="mt-1 text-xs">Contacta al administrador para que te genere un token de acceso.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Información de la API --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Información para desarrolladores</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p class="mb-2">Para registrar pagos programáticamente, utiliza la API REST:</p>
                    <div class="bg-blue-100 p-3 rounded border font-mono text-xs">
                        <p><strong>Endpoint:</strong> POST {{ url('/api/pagos') }}</p>
                        <p><strong>Autenticación:</strong> Bearer Token (Laravel Sanctum)</p>
                        <p><strong>Parámetros:</strong> invoice_id, tipo_pago, monto, numero_transaccion, observacion</p>
                    </div>
                    <p class="mt-2">
                        Usa tus tokens de acceso mostrados arriba para autenticarte en la API.
                        Puedes ver ejemplos específicos para cada factura.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Token de API (oculto por defecto) --}}
    <div id="tokenSection" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6 hidden">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Tu Token de API</h3>
                <div class="mt-2">
                    <div class="bg-gray-800 text-green-400 p-3 rounded font-mono text-sm break-all">
                        @if(session('api_token'))
                            {{ session('api_token') }}
                        @else
                            <span class="text-red-400">No hay token disponible. Solicita uno nuevo al administrador.</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-yellow-700">
                        <strong>¡Importante!</strong> Mantén este token seguro y no lo compartas con nadie.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de facturas pendientes --}}
    <div class="bg-white shadow-xl rounded-lg">
        @if($facturas->count() > 0)
            {{-- Estadísticas --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-blue-600">{{ $facturas->count() }}</span> facturas pendientes
                        </div>
                        <div class="text-sm text-gray-600">
                            Total adeudado: <span class="font-medium text-red-600">${{ number_format($facturas->sum('saldo_pendiente'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de facturas --}}
            <div class="divide-y divide-gray-200">
                @foreach($facturas as $factura)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            {{-- Información de la factura --}}
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $factura->invoice_number }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            Creada el {{ $factura->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    @if($factura->tiene_pagos_pendientes)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Pago en revisión
                                        </span>
                                    @endif
                                </div>

                                {{-- Información de montos --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Total de la factura</p>
                                        <p class="text-lg font-medium text-gray-900">${{ number_format($factura->total, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total pagado</p>
                                        <p class="text-lg font-medium text-green-600">${{ number_format($factura->total_pagado, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Saldo pendiente</p>
                                        <p class="text-lg font-medium text-red-600">${{ number_format($factura->saldo_pendiente, 2) }}</p>
                                    </div>
                                </div>

                                {{-- Ejemplo de curl para API --}}
                                <div class="mt-4">
                                    <button onclick="toggleCurlExample({{ $factura->id }})" 
                                            class="text-sm text-blue-600 hover:text-blue-800 underline">
                                        Ver ejemplo de API para esta factura
                                    </button>
                                    <div id="curl-{{ $factura->id }}" class="mt-3 hidden">
                                        <div class="bg-gray-800 text-white p-3 rounded text-xs font-mono overflow-x-auto">
<pre>curl -X POST {{ url('/api/pagos') }} \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer @if($tokens->count() > 0){{ $tokens->first()->plainTextToken ?? 'TU_TOKEN_AQUI' }}@else{{'TU_TOKEN_AQUI'}}@endif" \
  -d '{
    "invoice_id": {{ $factura->id }},
    "tipo_pago": "transferencia",
    "monto": {{ $factura->saldo_pendiente }},
    "numero_transaccion": "TXN123456789",
    "observacion": "Pago completo de la factura"
  }'</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex flex-col space-y-2 ml-6">
                                <a href="{{ route('client.factura.show', $factura) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">¡Todas las facturas están pagadas!</h3>
                <p class="mt-1 text-sm text-gray-500">
                    No tienes facturas pendientes de pago en este momento.
                </p>
                <div class="mt-6">
                    <a href="{{ route('client.facturas') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Todas las Facturas
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleCurlExample(facturaId) {
    document.getElementById('curl-' + facturaId).classList.toggle('hidden');
}
</script>
</div>
</div>
</x-app-layout>
