<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Confirmar Cancelación de Factura') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    ¡Atención! Esta acción no se puede deshacer
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>Está a punto de cancelar la factura <strong>{{ $invoice->invoice_number }}</strong>.</p>
                                    <p class="mt-1">Al cancelar esta factura:</p>
                                    <ul class="list-disc list-inside mt-2">
                                        <li>Se restaurará el stock de todos los productos</li>
                                        <li>La factura quedará marcada como cancelada</li>
                                        <li>No se podrá revertir esta acción</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la factura -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                        <h3 class="text-lg font-semibold mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Información de la Factura
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <p><strong>Número:</strong> <span class="text-blue-600 font-mono">{{ $invoice->invoice_number }}</span></p>
                                <p><strong>Cliente:</strong> <span class="text-gray-700">{{ $invoice->customer_name }}</span></p>
                                <p><strong>Total:</strong> <span class="text-green-600 font-bold">${{ number_format($invoice->total, 2) }}</span></p>
                            </div>
                            <div class="space-y-2">
                                <p><strong>Fecha:</strong> <span class="text-gray-700">{{ $invoice->created_at->format('d/m/Y H:i') }}</span></p>
                                <p><strong>Vendedor:</strong> <span class="text-gray-700">{{ $invoice->user->name }}</span></p>
                                <p><strong>Items:</strong> <span class="text-purple-600 font-medium">{{ $invoice->items->count() }} productos</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Productos que serán restaurados -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold mb-3 flex items-center text-blue-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h1.586a1 1 0 01.707.293l1.414 1.414a1 1 0 00.707.293H15a2 2 0 012 2v2M5 8l2.5 5.5a2 2 0 001.79 1.11L12 15h4.5a2 2 0 001.83-1.22L20 10H8.5"></path>
                            </svg>
                            Stock que será restaurado:
                        </h3>
                        <div class="space-y-2">
                            @foreach($invoice->items as $item)
                                <div class="flex justify-between items-center p-3 bg-white rounded border">
                                    <span class="font-medium">{{ $item->product_name }}</span>
                                    <span class="font-bold text-green-600">+{{ $item->quantity }} unidades</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Formulario de confirmación -->
                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Confirme su contraseña para continuar *
                            </label>
                            <input type="password" name="password" id="password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">
                                Razón de la cancelación *
                            </label>
                            <textarea name="cancellation_reason" id="cancellation_reason" rows="3" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      placeholder="Describa el motivo de la cancelación..." required>{{ old('cancellation_reason') }}</textarea>
                            @error('cancellation_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('invoices.show', $invoice) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                    onclick="return confirm('¿Está seguro de que desea cancelar esta factura? Esta acción no se puede deshacer.')">
                                Confirmar Cancelación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
