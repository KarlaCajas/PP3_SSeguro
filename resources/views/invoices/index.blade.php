<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Facturas') }}
            </h2>
            <a href="{{ route('invoices.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nueva Factura
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif


                    <!-- Filtros -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <form method="GET" class="flex gap-4 items-end">
                            {{-- Mantener parámetros de búsqueda --}}
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            @if($perPage != 15)
                                <input type="hidden" name="per_page" value="{{ $perPage }}">
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input type="date" name="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="mt-6 flex space-x-2">
                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Filtrar
                                </button>
                                @if(request('status') || request('date'))
                                    <a href="{{ route('invoices.index') }}{{ $search ? '?search=' . $search : '' }}{{ $search && $perPage != 15 ? '&per_page=' . $perPage : (!$search && $perPage != 15 ? '?per_page=' . $perPage : '') }}" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Limpiar Filtros
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    
                    {{-- Controles de tabla: búsqueda y paginación --}}
                    <x-table-controls 
                        :search="$search" 
                        :per-page="$perPage" 
                        :route="route('invoices.index')" 
                        placeholder="Buscar por número, cliente, vendedor o total..." 
                    />
                    
                    <!-- Lista de facturas -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Número
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vendedor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $invoice->invoice_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $invoice->customer_name }}
                                            @if($invoice->customer_email)
                                                <br><small class="text-gray-500">{{ $invoice->customer_email }}</small>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $invoice->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($invoice->total, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($invoice->status === 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activa
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Cancelada
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $invoice->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('invoices.show', $invoice) }}" method="GET" class="inline">
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Ver
                                                </button>
                                            </form>
                                            @if($invoice->isActive() && $invoice->created_at->isToday())
                                                <form action="{{ route('invoices.edit', $invoice) }}" method="GET" class="inline">
                                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Editar
                                                    </button>
                                                </form>
                                            @endif
                                            @if($invoice->canBeCancelledBy(auth()->user()) && $invoice->isActive())
                                                <form action="{{ route('invoices.confirm-delete', $invoice) }}" method="GET" class="inline">
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            @if($search)
                                                No se encontraron facturas que coincidan con "{{ $search }}"
                                                <div class="mt-2">
                                                    <a href="{{ route('invoices.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Limpiar búsqueda
                                                    </a>
                                                </div>
                                            @else
                                                No hay facturas registradas
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Información de paginación --}}
                    <x-pagination-info :items="$invoices" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
