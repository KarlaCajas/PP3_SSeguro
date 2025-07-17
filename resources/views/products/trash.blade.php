<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Papelera de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header con navegación -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Productos Eliminados ({{ $deletedProducts->total() }})
                            </h3>
                        </div>
                        <a href="{{ route('products.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Productos
                        </a>
                    </div>

                    {{-- Controles de tabla: búsqueda y paginación --}}
                    <x-table-controls 
                        :search="$search" 
                        :per-page="$perPage" 
                        :route="route('products.trash')" 
                        placeholder="Buscar por nombre, categoría, precio o razón..." 
                    />

                    @if($deletedProducts->count() > 0)
                        <!-- Tabla de productos eliminados -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Categoría
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Eliminado por
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Razón
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha eliminación
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($deletedProducts as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $product->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $product->category ? $product->category->name : 'Sin categoría' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($product->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $product->stock }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $product->deletedBy ? $product->deletedBy->name : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate" title="{{ $product->deletion_reason }}">
                                                    {{ $product->deletion_reason ?? 'Sin razón especificada' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $product->deleted_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <!-- Botón Restaurar -->
                                                    <button type="button" 
                                                            onclick="openRestoreModal({{ $product->id }}, '{{ $product->name }}')"
                                                            class="text-green-600 hover:text-green-900">
                                                        Restaurar
                                                    </button>
                                                    
                                                    <!-- Botón Eliminar Permanentemente -->
                                                    <button type="button" 
                                                            onclick="openForceDeleteModal({{ $product->id }}, '{{ $product->name }}')"
                                                            class="text-red-600 hover:text-red-900">
                                                        Eliminar Permanente
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Información de paginación --}}
                        <x-pagination-info :items="$deletedProducts" />
                    @else
                        <!-- Estado vacío -->
                        <div class="text-center py-12">
                            @if($search)
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron productos eliminados</h3>
                                <p class="mt-1 text-sm text-gray-500">No hay productos eliminados que coincidan con "{{ $search }}"</p>
                                <div class="mt-4">
                                    <a href="{{ route('products.trash') }}" class="text-indigo-600 hover:text-indigo-900">
                                        Limpiar búsqueda
                                    </a>
                                </div>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos eliminados</h3>
                                <p class="mt-1 text-sm text-gray-500">Todos los productos están activos.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para restaurar producto -->
    <div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Restaurar Producto</h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que deseas restaurar el producto <span id="restoreProductName" class="font-medium"></span>?
                    </p>
                    <p class="text-sm text-red-600 mt-2">
                        Para confirmar, ingresa tu contraseña actual.
                    </p>
                </div>
                <form id="restoreForm" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <input type="password" 
                               name="current_password" 
                               placeholder="Tu contraseña actual"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" 
                                onclick="closeRestoreModal()"
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Restaurar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar permanentemente -->
    <div id="forceDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 text-red-600">Eliminar Permanentemente</h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que deseas eliminar permanentemente el producto <span id="forceDeleteProductName" class="font-medium"></span>?
                    </p>
                    <p class="text-sm text-red-600 mt-2 font-medium">
                        Esta acción NO se puede deshacer.
                    </p>
                    <p class="text-sm text-red-600 mt-1">
                        Para confirmar, ingresa tu contraseña actual.
                    </p>
                </div>
                <form id="forceDeleteForm" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <input type="password" 
                               name="current_password" 
                               placeholder="Tu contraseña actual"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
                               required>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" 
                                onclick="closeForceDeleteModal()"
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Eliminar Permanente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRestoreModal(productId, productName) {
            document.getElementById('restoreProductName').textContent = productName;
            document.getElementById('restoreForm').action = `/products/${productId}/restore`;
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
            document.getElementById('restoreForm').reset();
        }

        function openForceDeleteModal(productId, productName) {
            document.getElementById('forceDeleteProductName').textContent = productName;
            document.getElementById('forceDeleteForm').action = `/products/${productId}/force-delete`;
            document.getElementById('forceDeleteModal').classList.remove('hidden');
        }

        function closeForceDeleteModal() {
            document.getElementById('forceDeleteModal').classList.add('hidden');
            document.getElementById('forceDeleteForm').reset();
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const restoreModal = document.getElementById('restoreModal');
            const forceDeleteModal = document.getElementById('forceDeleteModal');
            
            if (event.target == restoreModal) {
                closeRestoreModal();
            }
            if (event.target == forceDeleteModal) {
                closeForceDeleteModal();
            }
        }
    </script>
</x-app-layout>
