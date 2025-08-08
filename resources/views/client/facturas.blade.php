<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Facturas
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Encabezado --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Facturas</h1>
            <p class="mt-2 text-gray-600">Consulta todas tus facturas y su estado de pago</p>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Pagar Facturas
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 bg-white shadow rounded-lg p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Filtrar por estado:</span>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('client.facturas', ['status' => 'todas']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $status === 'todas' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Todas
                </a>
                <a href="{{ route('client.facturas', ['status' => 'active']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $status === 'active' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Pendientes
                </a>
                <a href="{{ route('client.facturas', ['status' => 'paid']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Pagadas
                </a>
                <a href="{{ route('client.facturas', ['status' => 'cancelled']) }}" 
                   class="px-3 py-1 rounded-full text-sm font-medium {{ $status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Canceladas
                </a>
            </div>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="bg-white shadow-xl rounded-lg">
        @if($facturas->count() > 0)
            {{-- Estadísticas --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-blue-600">{{ $facturas->total() }}</span> facturas encontradas
                        </div>
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-medium text-gray-800">${{ number_format($facturas->sum('total'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de facturas --}}
            <div class="divide-y divide-gray-200">
                @foreach($facturas as $factura)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            {{-- Información de la factura --}}
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $factura->invoice_number }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            Creada el {{ $factura->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    {{-- Estado de la factura --}}
                                    <div>
                                        @if($factura->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Pagada
                                            </span>
                                        @elseif($factura->status === 'cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Cancelada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pendiente
                                            </span>
                                        @endif

                                        @if($factura->tiene_pagos_pendientes)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Pago en revisión
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Información de montos --}}
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Total de la factura</p>
                                        <p class="text-lg font-medium text-gray-900">${{ number_format($factura->total, 2) }}</p>
                                    </div>
                                    @if($factura->status !== 'paid' && $factura->status !== 'cancelled')
                                        <div>
                                            <p class="text-sm text-gray-500">Saldo pendiente</p>
                                            <p class="text-lg font-medium {{ $factura->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                ${{ number_format($factura->saldo_pendiente, 2) }}
                                            </p>
                                        </div>
                                        @if($factura->saldo_pendiente < $factura->total)
                                            <div>
                                                <p class="text-sm text-gray-500">Total pagado</p>
                                                <p class="text-lg font-medium text-green-600">${{ number_format($factura->total - $factura->saldo_pendiente, 2) }}</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('client.factura.show', $factura) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalles
                                </a>
                                
                                @if($factura->saldo_pendiente > 0 && $factura->status !== 'cancelled')
                                    <a href="{{ route('client.facturas-pendientes') }}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Pagar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $facturas->appends(request()->query())->links() }}
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay facturas</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($status === 'todas')
                        No tienes facturas registradas.
                    @else
                        No tienes facturas con el estado seleccionado.
                    @endif
                </p>
                @if($status !== 'todas')
                    <div class="mt-6">
                        <a href="{{ route('client.facturas') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ver Todas las Facturas
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
</div>
</x-app-layout>
