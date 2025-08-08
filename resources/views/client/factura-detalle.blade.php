<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Factura - {{ $invoice->invoice_number }}
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{-- Encabezado --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle de Factura</h1>
            <p class="mt-2 text-gray-600">{{ $invoice->invoice_number }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('client.facturas') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Facturas
            </a>
            @if($saldoPendiente > 0 && $invoice->status !== 'cancelled')
                <a href="{{ route('client.facturas-pendientes') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Pagar Factura
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Información de la factura --}}
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-900">Información de la Factura</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Número de Factura</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Estado</label>
                        <div class="mt-1">
                            @if($invoice->status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Pagada
                                </span>
                            @elseif($invoice->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Cancelada
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendiente
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Fecha de Creación</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Cliente</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $invoice->customer_name }}</p>
                    </div>
                </div>

                {{-- Información de montos --}}
                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Resumen de Pagos</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Subtotal:</span>
                            <span class="text-sm font-medium text-gray-900">${{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">IVA:</span>
                            <span class="text-sm font-medium text-gray-900">${{ number_format($invoice->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium text-gray-900">Total:</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($invoice->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total Pagado:</span>
                            <span class="text-sm font-medium text-green-600">${{ number_format($totalPagado, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium text-gray-900">Saldo Pendiente:</span>
                            <span class="text-sm font-bold {{ $saldoPendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ${{ number_format($saldoPendiente, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de pagos --}}
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-900">Historial de Pagos</h2>
            </div>
            <div class="p-6">
                @if($invoice->payments->count() > 0)
                    <div class="space-y-4">
                        @foreach($invoice->payments as $pago)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        ${{ number_format($pago->monto, 2) }}
                                    </span>
                                    @if($pago->estado === 'aprobado')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aprobado
                                        </span>
                                    @elseif($pago->estado === 'rechazado')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rechazado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Tipo:</span> {{ $pago->getTipoPagoNombre() }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Ref:</span> {{ $pago->numero_transaccion }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Fecha:</span> {{ $pago->created_at->format('d/m/Y') }}
                                    </div>
                                    @if($pago->validated_at)
                                        <div>
                                            <span class="font-medium">Validado:</span> {{ $pago->validated_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                                @if($pago->observacion)
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">Observación:</span> {{ $pago->observacion }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Sin pagos registrados</h3>
                        <p class="mt-1 text-sm text-gray-500">Esta factura aún no tiene pagos asociados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Detalles de los productos --}}
    <div class="mt-8 bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h2 class="text-lg font-semibold text-gray-900">Productos Facturados</h2>
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
                            Subtotal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                @if($item->product)
                                    <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</x-app-layout>
