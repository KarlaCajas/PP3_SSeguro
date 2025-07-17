<!-- filepath: c:\Users\condo\Documents\Universidad\7mo\SW seguro\sws-projects\ventas-pry-p1\resources\views\categories\edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Encabezado --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Editar Categoría: {{ $category->name }}</h3>
                        <a href="{{ route('categories.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al listado
                        </a>
                    </div>

                    {{-- Información actual --}}
                    <div class="bg-green-50 p-4 rounded-lg mb-6 border border-green-200">
                        <h4 class="font-semibold text-green-800 mb-2">Información actual</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium">ID:</span>
                                <span class="text-green-700">{{ $category->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Productos asociados:</span>
                                <span class="text-green-700">{{ $category->products->count() }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Creada:</span>
                                <span class="text-green-700">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario de edición --}}
                    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nombre de la categoría --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre de la categoría
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   required
                                   placeholder="Ej: Bebidas, Comidas, Postres..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                El nombre debe ser único y descriptivo
                            </p>
                        </div>

                        {{-- Productos asociados --}}
                        @if($category->products->count() > 0)
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <h4 class="font-semibold text-yellow-800 mb-2">Productos asociados ({{ $category->products->count() }})</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                                    @foreach($category->products->take(6) as $product)
                                        <div class="text-yellow-700">• {{ $product->name }}</div>
                                    @endforeach
                                    @if($category->products->count() > 6)
                                        <div class="text-yellow-600 italic">... y {{ $category->products->count() - 6 }} más</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('categories.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>