<!-- filepath: c:\Users\condo\Documents\Universidad\7mo\SW seguro\sws-projects\ventas-pry-p1\resources\views\products\edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Encabezado --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Editar Producto: {{ $product->name }}</h3>
                        <a href="{{ route('products.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al listado
                        </a>
                    </div>

                    {{-- Información actual --}}
                    <div class="bg-yellow-50 p-4 rounded-lg mb-6 border border-yellow-200">
                        <h4 class="font-semibold text-yellow-800 mb-2">Información actual</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium">ID:</span>
                                <span class="text-yellow-700">{{ $product->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Categoría actual:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $product->category->name }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Ventas registradas:</span>
                                <span class="text-yellow-700">{{ $product->sales->count() }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Creado:</span>
                                <span class="text-yellow-700">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Alerta de ventas --}}
                    @if($product->sales->count() > 0)
                        <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-2">⚠️ Producto con historial de ventas</h4>
                            <p class="text-blue-700 text-sm">
                                Este producto tiene {{ $product->sales->count() }} ventas registradas. 
                                Los cambios en precio afectarán solo las ventas futuras, no las anteriores.
                            </p>
                        </div>
                    @endif

                    {{-- Formulario de edición --}}
                    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nombre del producto --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre del producto
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $product->name) }}" 
                                   required
                                   placeholder="Ej: Coca Cola 350ml, Hamburguesa Clásica..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Categoría --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                Categoría
                            </label>
                            <select name="category_id" 
                                    id="category_id" 
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccionar categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if($categories->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600">
                                    No hay categorías disponibles. 
                                    <a href="{{ route('categories.create') }}" class="text-blue-600 hover:text-blue-800">Crear una categoría primero</a>
                                </p>
                            @endif
                        </div>

                        {{-- Precio --}}
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">
                                Precio (en $)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="price" 
                                       id="price" 
                                       value="{{ old('price', $product->price) }}" 
                                       step="0.01"
                                       min="0"
                                       max="999999.99"
                                       required
                                       placeholder="0.00"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @if($product->price != old('price', $product->price))
                                    <div class="absolute right-3 top-2 text-xs text-gray-500">
                                        Anterior: ${{ number_format($product->price, 2) }}
                                    </div>
                                @endif
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stock --}}
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700">
                                Stock actual (unidades)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="stock" 
                                       id="stock" 
                                       value="{{ old('stock', $product->stock) }}" 
                                       min="0"
                                       required
                                       placeholder="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-1 flex justify-between text-sm">
                                <span class="text-gray-500">Stock actual: {{ $product->stock }} unidades</span>
                                @if($product->stock <= 10 && $product->stock > 0)
                                    <span class="text-yellow-600">⚠️ Stock bajo</span>
                                @elseif($product->stock == 0)
                                    <span class="text-red-600">❌ Sin stock</span>
                                @else
                                    <span class="text-green-600">✅ Stock disponible</span>
                                @endif
                            </div>
                        </div>

                        {{-- Historial de ventas resumen --}}
                        @if($product->sales->count() > 0)
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <h4 class="font-semibold text-gray-800 mb-2">Resumen de ventas</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium">Total vendido:</span>
                                        <span class="text-gray-600">{{ $product->sales->sum('quantity') }} unidades</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Ingresos generados:</span>
                                        <span class="text-gray-600">${{ number_format($product->sales->sum('total_price'), 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Última venta:</span>
                                        <span class="text-gray-600">
                                            {{ $product->sales->sortByDesc('created_at')->first()?->created_at->format('d/m/Y') ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('products.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>