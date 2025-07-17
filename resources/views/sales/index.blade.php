<!-- filepath: c:\Users\condo\Documents\Universidad\7mo\SW seguro\sws-projects\ventas-pry-p1\resources\views\sales\index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Ventas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Encabezado con botón de crear --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Historial de Ventas</h3>
                        <a href="{{ route('sales.create') }}" 
                           class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Registrar Venta
                        </a>
                    </div>

                    {{-- Mensajes --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    {{-- Resumen rápido --}}
                    @php
                        $allSales = \App\Models\Sale::where('user_id', auth()->id())->get();
                        $todaySales = \App\Models\Sale::where('user_id', auth()->id())
                                                    ->where('created_at', '>=', today())->get();
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-800">Total Ventas</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ $allSales->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800">Ingresos Totales</h4>
                            <p class="text-2xl font-bold text-green-600">${{ number_format($allSales->sum('total_price'), 2) }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h4 class="font-semibold text-yellow-800">Ventas Hoy</h4>
                            <p class="text-2xl font-bold text-yellow-600">{{ $todaySales->count() }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="font-semibold text-purple-800">Promedio Venta</h4>
                            <p class="text-2xl font-bold text-purple-600">
                                ${{ $allSales->count() > 0 ? number_format($allSales->avg('total_price'), 2) : '0.00' }}
                            </p>
                        </div>
                    </div>

                    {{-- Controles de tabla: búsqueda y paginación --}}
                    <x-table-controls 
                        :search="$search" 
                        :per-page="$perPage" 
                        :route="route('sales.index')" 
                        placeholder="Buscar por producto, categoría, precio o cantidad..." 
                    />

                    {{-- Tabla de ventas --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Unitario
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
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
                                @forelse($sales as $sale)
                                    <tr class="{{ $sale->created_at->isToday() ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            #{{ $sale->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $sale->product->name }}
                                            <div class="text-xs text-gray-500">
                                                Categoría: {{ $sale->product->category->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $sale->quantity }} unidades
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($sale->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                            ${{ number_format($sale->total_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $sale->created_at->format('d/m/Y H:i') }}
                                            <div class="text-xs text-gray-500">
                                                {{ $sale->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($sale->created_at->isToday())
                                                <div class="flex space-x-2">
                                                    {{-- Botón Editar (solo del día actual) --}}
                                                    <a href="{{ route('sales.edit', $sale) }}" 
                                                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Editar
                                                    </a>

                                                    {{-- Botón Eliminar (solo del día actual) --}}
                                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" 
                                                          class="inline"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar esta venta? El stock será restaurado.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500 italic">
                                                    Solo editable hoy
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            @if($search)
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                    <p class="text-lg font-semibold">No se encontraron ventas</p>
                                                    <p class="text-sm">No hay ventas que coincidan con "{{ $search }}"</p>
                                                    <a href="{{ route('sales.index') }}" class="mt-3 text-indigo-600 hover:text-indigo-900">
                                                        Limpiar búsqueda
                                                    </a>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                    </svg>
                                                    <p class="text-lg font-semibold">No hay ventas registradas</p>
                                                    <p class="text-sm">Comienza registrando tu primera venta</p>
                                                    <a href="{{ route('sales.create') }}" 
                                                       class="mt-3 bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                                        Registrar Venta
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Información de paginación --}}
                    <x-pagination-info :items="$sales" />

                    {{-- Nota informativa --}}
                    @if($sales->count() > 0)
                        <div class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="text-blue-700 text-sm">
                                <strong>Nota:</strong> Solo puedes editar o eliminar las ventas realizadas el día de hoy. 
                                Las ventas de días anteriores están protegidas para mantener la integridad del historial.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>