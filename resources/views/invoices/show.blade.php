<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Factura') }} {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                @if($invoice->isActive() && $invoice->created_at->isToday())
                    <a href="{{ route('invoices.edit', $invoice) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar
                    </a>
                @endif
                @if($invoice->canBeCancelledBy(auth()->user()) && $invoice->isActive())
                    <a href="{{ route('invoices.confirm-delete', $invoice) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Cancelar Factura
                    </a>
                @endif
                <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Imprimir
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" id="invoice-content">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 print:hidden">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Encabezado de la factura -->
                    <div class="border-b pb-6 mb-6">
                        <div class="flex justify-between items-start">

                            <div class="text-right">
                                <h2 class="text-xl font-bold text-blue-600">BarEspe</h2>
                                <p class="text-gray-600">Sistema de Ventas</p>
                                <div class="text-sm text-gray-600">
                                    <p>
                                        <strong>Fecha:</strong> {{ $invoice->created_at->format('d/m/Y') }}
                                    </p>
                                    <p>
                                        <strong>Hora:</strong> {{ $invoice->created_at->format('H:i:s') }}
                                    </p>
                                    @if($invoice->updated_at != $invoice->created_at)
                                        <p class="text-orange-600">
                                            <strong>Modificada:</strong> {{ $invoice->updated_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">FACTURA</h1>
                                <p class="text-lg mt-2 font-mono">{{ $invoice->invoice_number }}</p>
                                <div class="mt-4 flex items-center space-x-4">
                                    @if($invoice->status === 'active')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ ACTIVA
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ✕ CANCELADA
                                        </span>
                                    @endif
                                    
                                    @if($invoice->created_at->isToday())
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            📅 Hoy
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            📅 {{ $invoice->created_at->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del cliente y vendedor -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Información del Cliente
                            </h3>
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <div class="space-y-2">
                                    <p><strong>Nombre:</strong> <span class="text-blue-600">{{ $invoice->customer_name }}</span></p>
                                    @if($invoice->customer_email)
                                        <p><strong>Email:</strong> <span class="text-gray-600">{{ $invoice->customer_email }}</span></p>
                                    @endif
                                    <p>
                                        <strong>Tipo:</strong> 
                                        @if($invoice->customer)
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Cliente Registrado</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Cliente Ocasional</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Información del Vendedor
                            </h3>
                            <div class="bg-gray-50 p-4 rounded-lg border ">
                                <div class="space-y-2">
                                    <p><strong>Vendedor:</strong> <span class="text-green-600">{{ $invoice->user->name }}</span></p>
                                    <p><strong>Email:</strong> <span class="text-gray-600">{{ $invoice->user->email }}</span></p>
                                    <p>
                                        <strong>Rol:</strong> 
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full capitalize">
                                            {{ $invoice->user->getRoleNames()->first() }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detalles de la factura -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Detalles de la Factura
                        </h3>
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full table-auto border-collapse bg-white">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                            Producto
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                            Cantidad
                                        </th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                            Precio Unit.
                                        </th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($invoice->items as $item)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 text-left border-b-2 border-gray-100">
                                                <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                @if($item->product)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <span class="inline-flex items-center">
                                                            📦 Código: {{ $item->product->id }}
                                                        </span>
                                                        @if($item->product->category)
                                                            <span class="ml-3 inline-flex items-center">
                                                                🏷️ {{ $item->product->category->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center border-b-2 border-gray-100">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    {{ $item->quantity }} {{ $item->quantity == 1 ? 'unidad' : 'unidades' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right border-b-2 border-gray-100">
                                                <span class="font-medium text-gray-900">${{ number_format($item->unit_price, 2) }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-right border-b-2 border-gray-100">
                                                <span class="font-bold text-lg text-green-600">${{ number_format($item->total_price, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($invoice->items->count() === 0)
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                    </svg>
                                                    <p class="text-lg font-medium">No hay productos en esta factura</p>
                                                    <p class="text-sm text-gray-400 mt-1">Esta factura está vacía</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totales -->
                    <div class="flex justify-end mb-6">
                        <div class="w-64">
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <h4 class="text-lg font-semibold mb-4 text-gray-900">Resumen de la Factura</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between py-1 text-sm">
                                        <span class="text-gray-600">Productos:</span>
                                        <span class="font-medium">{{ $invoice->items->count() }} {{ $invoice->items->count() == 1 ? 'producto' : 'productos' }}</span>
                                    </div>
                                    <div class="flex justify-between py-1 text-sm">
                                        <span class="text-gray-600">Cantidad total:</span>
                                        <span class="font-medium">{{ $invoice->items->sum('quantity') }} unidades</span>
                                    </div>
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-700 font-medium">Subtotal:</span>
                                            <span class="font-semibold">${{ number_format($invoice->subtotal, 2) }}</span>
                                        </div>
                                        @if($invoice->tax > 0)
                                            <div class="flex justify-between py-2">
                                                <span class="text-gray-700 font-medium">Impuestos:</span>
                                                <span class="font-semibold">${{ number_format($invoice->tax, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between py-2 border-t border-gray-300">
                                            <span class="text-lg font-bold text-gray-900">Total a Pagar:</span>
                                            <span class="text-lg font-bold text-green-600">${{ number_format($invoice->total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de cancelación -->
                    @if($invoice->isCancelled())
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold mb-3 text-red-600 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Información de Cancelación
                            </h3>
                            <div class="bg-red-50 p-6 rounded-lg border border-red-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-sm text-red-600 font-medium">Cancelada por:</p>
                                        <p class="text-red-800 font-semibold">{{ $invoice->cancelledBy->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-red-600 font-medium">Fecha de cancelación:</p>
                                        <p class="text-red-800 font-semibold">{{ $invoice->cancelled_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-red-600 font-medium">Razón:</p>
                                        <p class="text-red-800 font-semibold">{{ $invoice->cancellation_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Pie de página -->
                    <div class="border-t pt-6 mt-8 text-center">
                        <div class="text-sm text-gray-500 space-y-2">
                            <p class="font-medium">Esta factura fue generada electrónicamente por el Sistema de Ventas BarEspe</p>
                            <p>Impreso el: {{ now()->format('d/m/Y H:i:s') }}</p>
                            @if($invoice->isActive())
                                <div class="mt-3 p-3 bg-green-50 rounded-lg inline-block">
                                    <p class="text-green-700 font-medium text-xs">
                                        ✓ Documento válido y verificado
                                    </p>
                                </div>
                            @else
                                <div class="mt-3 p-3 bg-red-50 rounded-lg inline-block">
                                    <p class="text-red-700 font-medium text-xs">
                                        ✕ Documento cancelado - Sin validez comercial
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Botones de acción (no se imprimen) -->
                <div class="p-6 border-t print:hidden">
                    <div class="flex justify-between">
                        <a href="{{ route('invoices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .print\:hidden {
                display: none !important;
            }
            
            body {
                font-size: 12pt;
                color: black !important;
            }
            
            .bg-gray-50, .bg-gradient-to-r {
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact;
            }
            
            .bg-blue-100 {
                background-color: #dbeafe !important;
                -webkit-print-color-adjust: exact;
            }
            
            .bg-green-100 {
                background-color: #dcfce7 !important;
                -webkit-print-color-adjust: exact;
            }
            
            .bg-red-100 {
                background-color: #fee2e2 !important;
                -webkit-print-color-adjust: exact;
            }
            
            .text-blue-600, .text-green-600, .text-red-600 {
                color: black !important;
                font-weight: bold;
            }
            
            .shadow-sm {
                box-shadow: none !important;
            }
            
            .border, .border-b, .border-t {
                border-color: #000 !important;
            }
            
            /* Asegurar que las tablas se impriman correctamente */
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            /* Reducir espaciado para impresión */
            .py-12 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
        }
        
        /* Hover effects para pantalla */
        @media screen {
            .hover\:bg-gray-50:hover {
                background-color: #f9fafb;
            }
        }
    </style>
</x-app-layout>
