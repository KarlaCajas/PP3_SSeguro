<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historial de Pagos
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Encabezado --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Historial de Pagos</h1>
            <p class="mt-2 text-gray-600">Consulta todos los pagos procesados</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('payments.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pagos Pendientes
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('payments.historial') }}" class="flex items-center space-x-4">
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="estado" id="estado" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="todos" {{ $estado === 'todos' ? 'selected' : '' }}>Todos</option>
                        <option value="aprobado" {{ $estado === 'aprobado' ? 'selected' : '' }}>Aprobados</option>
                        <option value="rechazado" {{ $estado === 'rechazado' ? 'selected' : '' }}>Rechazados</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="bg-white shadow-xl rounded-lg">
        @if($pagos->count() > 0)
            {{-- Estadísticas --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-blue-600">{{ $pagos->total() }}</span> pagos en total
                        </div>
                        @php
                            $aprobados = $pagos->where('estado', 'aprobado');
                            $rechazados = $pagos->where('estado', 'rechazado');
                        @endphp
                        @if($estado === 'todos' || $estado === 'aprobado')
                            <div class="text-sm text-gray-600">
                                Aprobados: <span class="font-medium text-green-600">{{ $aprobados->count() }}</span>
                                <span class="text-green-600">(${{{ number_format($aprobados->sum('monto'), 2) }}})</span>
                            </div>
                        @endif
                        @if($estado === 'todos' || $estado === 'rechazado')
                            <div class="text-sm text-gray-600">
                                Rechazados: <span class="font-medium text-red-600">{{ $rechazados->count() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabla de pagos --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Factura
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pago
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validado por
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pagos as $pago)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pago->invoice->invoice_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Total: ${{ number_format($pago->invoice->total, 2) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pago->pagadoPor->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $pago->pagadoPor->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900">
                                            ${{ number_format($pago->monto, 2) }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $pago->getTipoPagoNombre() }}</div>
                                        @if($pago->numero_transaccion)
                                            <div class="text-xs text-gray-400 mt-1">
                                                Ref: {{ $pago->numero_transaccion }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($pago->estado === 'aprobado') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $pago->getEstadoNombre() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $pago->validadoPor?->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-900">
                                            {{ $pago->validated_at?->format('d/m/Y') ?? '-' }}
                                        </div>
                                        @if($pago->validated_at)
                                            <div class="text-xs text-gray-500">
                                                {{ $pago->validated_at->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($pagos->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pagos->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay pagos procesados</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($estado === 'todos')
                        No hay pagos procesados aún.
                    @elseif($estado === 'aprobado')
                        No hay pagos aprobados.
                    @else
                        No hay pagos rechazados.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('payments.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Pagos Pendientes
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</div>
</x-app-layout>
