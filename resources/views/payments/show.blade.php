<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Pago #{{ $payment->id }}
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Encabezado --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle del Pago</h1>
            <p class="mt-2 text-gray-600">Revisa la información del pago antes de aprobar o rechazar</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('payments.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Información del Pago --}}
        <div class="bg-white shadow-xl rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-medium text-gray-900">Información del Pago</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">ID de Pago</label>
                        <p class="text-sm text-gray-900">#{{ $payment->id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Estado</label>
                        <p class="text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($payment->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                @elseif($payment->estado === 'aprobado') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $payment->getEstadoNombre() }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Monto</label>
                        <p class="text-lg font-semibold text-gray-900">${{ number_format($payment->monto, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tipo de Pago</label>
                        <p class="text-sm text-gray-900">{{ $payment->getTipoPagoNombre() }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Número de Transacción</label>
                    <p class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $payment->numero_transaccion }}</p>
                </div>

                @if($payment->observacion)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Observaciones</label>
                        <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded border">{{ $payment->observacion }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Fecha de Pago</label>
                        <p class="text-sm text-gray-900">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($payment->validated_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Validación</label>
                            <p class="text-sm text-gray-900">{{ $payment->validated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Información de la Factura --}}
        <div class="bg-white shadow-xl rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-medium text-gray-900">Información de la Factura</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Número de Factura</label>
                        <p class="text-sm text-gray-900 font-semibold">{{ $payment->invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total de Factura</label>
                        <p class="text-sm text-gray-900 font-semibold">${{ number_format($payment->invoice->total, 2) }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Cliente</label>
                    <div class="mt-1">
                        <p class="text-sm font-medium text-gray-900">{{ $payment->pagadoPor->name }}</p>
                        <p class="text-sm text-gray-600">{{ $payment->pagadoPor->email }}</p>
                    </div>
                </div>

                @if($payment->validadoPor)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Validado por</label>
                        <p class="text-sm text-gray-900">{{ $payment->validadoPor->name }}</p>
                    </div>
                @endif

                {{-- Resumen de pagos de la factura --}}
                @php
                    $totalPagado = $payment->invoice->getTotalPagosAprobados();
                    $saldoPendiente = $payment->invoice->total - $totalPagado;
                    if ($payment->estado === 'aprobado') {
                        $totalPagado += $payment->monto;
                        $saldoPendiente -= $payment->monto;
                    }
                @endphp

                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Resumen de Pagos</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Factura:</span>
                            <span class="text-gray-900">${{ number_format($payment->invoice->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Pagado:</span>
                            <span class="text-green-600">${{ number_format($totalPagado, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium border-t pt-2">
                            <span class="text-gray-900">Saldo Pendiente:</span>
                            <span class="text-orange-600">${{ number_format(max($saldoPendiente, 0), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    @if($payment->isPendiente())
        <div class="mt-8 bg-white shadow-xl rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-medium text-gray-900">Acciones de Validación</h2>
            </div>
            <div class="px-6 py-4">
                <div class="flex justify-center space-x-4">
                    {{-- Botón Aprobar --}}
                    <form action="{{ route('payments.aprobar', $payment) }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de que deseas aprobar este pago?')">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Aprobar Pago
                        </button>
                    </form>

                    {{-- Botón Rechazar --}}
                    <form action="{{ route('payments.rechazar', $payment) }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de que deseas rechazar este pago?')">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Rechazar Pago
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Items de la Factura --}}
    @if($payment->invoice->items->count() > 0)
        <div class="mt-8 bg-white shadow-xl rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-medium text-gray-900">Items de la Factura</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Unit.
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payment->invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    ${{ number_format($item->total_price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
</div>
</x-app-layout>
