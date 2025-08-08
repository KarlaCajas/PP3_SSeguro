<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Pagos
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Encabezado --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Pagos</h1>
            <p class="mt-2 text-gray-600">Historial completo de todos tus pagos registrados</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('client.dashboard') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Dashboard
            </a>
            <a href="{{ route('client.facturas-pendientes') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Registrar Nuevo Pago
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 bg-white shadow rounded-lg p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Filtrar por estado:</span>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('client.pagos', ['estado' => 'todos']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $estado === 'todos' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Todos
                </a>
                <a href="{{ route('client.pagos', ['estado' => 'pendiente']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Pendientes
                </a>
                <a href="{{ route('client.pagos', ['estado' => 'aprobado']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $estado === 'aprobado' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Aprobados
                </a>
                <a href="{{ route('client.pagos', ['estado' => 'rechazado']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $estado === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Rechazados
                </a>
            </div>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="bg-white shadow-xl rounded-lg">
        @if($pagos->count() > 0)
            {{-- Estadísticas --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-blue-600">{{ $pagos->total() }}</span> pagos encontrados
                        </div>
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-medium text-gray-800">${{ number_format($pagos->sum('monto'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de pagos --}}
            <div class="divide-y divide-gray-200">
                @foreach($pagos as $pago)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            {{-- Información del pago --}}
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-3">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            Pago para {{ $pago->invoice->invoice_number }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            Registrado el {{ $pago->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    {{-- Estado del pago --}}
                                    <div>
                                        @if($pago->estado === 'aprobado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $pago->getEstadoNombre() }}
                                            </span>
                                        @elseif($pago->estado === 'rechazado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $pago->getEstadoNombre() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $pago->getEstadoNombre() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Información detallada --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Monto pagado</p>
                                        <p class="text-lg font-medium text-gray-900">${{ number_format($pago->monto, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Tipo de pago</p>
                                        <p class="text-sm font-medium text-gray-700">{{ $pago->getTipoPagoNombre() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Número de transacción</p>
                                        <p class="text-sm font-medium text-gray-700 font-mono">{{ $pago->numero_transaccion }}</p>
                                    </div>
                                    @if($pago->validated_at)
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                {{ $pago->estado === 'aprobado' ? 'Aprobado' : 'Procesado' }} el
                                            </p>
                                            <p class="text-sm text-gray-700">{{ $pago->validated_at->format('d/m/Y H:i') }}</p>
                                            @if($pago->validadoPor)
                                                <p class="text-xs text-gray-500">por {{ $pago->validadoPor->name }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Observaciones --}}
                                @if($pago->observacion)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-500">Observaciones:</p>
                                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">{{ $pago->observacion }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Acciones --}}
                            <div class="flex flex-col space-y-2 ml-6">
                                <a href="{{ route('client.pago.show', $pago) }}" 
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

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pagos->appends(request()->query())->links() }}
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay pagos registrados</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($estado === 'todos')
                        Aún no has registrado ningún pago.
                    @else
                        No tienes pagos con el estado seleccionado.
                    @endif
                </p>
                <div class="mt-6">
                    @if($estado !== 'todos')
                        <a href="{{ route('client.pagos') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                            Ver Todos los Pagos
                        </a>
                    @endif
                    <a href="{{ route('client.facturas-pendientes') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Registrar Nuevo Pago
                    </a>
            </div>
        </div>
    @endif
</div>
</div>
</div>
</x-app-layout>