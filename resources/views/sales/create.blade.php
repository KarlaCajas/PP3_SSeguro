<!-- filepath: c:\Users\condo\Documents\Universidad\7mo\SW seguro\sws-projects\ventas-pry-p1\resources\views\sales\create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Encabezado --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Nueva Venta</h3>
                        <a href="{{ route('sales.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al historial
                        </a>
                    </div>

                    {{-- Información del cajero --}}
                    <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-200">
                        <h4 class="font-semibold text-blue-800 mb-2">Información de la venta</h4>
                        <p class="text-blue-700">Cajero: {{ auth()->user()->name }}</p>
                        <p class="text-blue-700">Fecha: {{ now()->format('d/m/Y H:i') }}</p>
                    </div>

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" id="saleForm">
                        @csrf

                        {{-- Selección de producto --}}
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700">
                                Producto
                            </label>
                            <select name="product_id" 
                                    id="product_id" 
                                    required
                                    onchange="updateProductInfo()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccionar producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-price="{{ $product->price }}"
                                            data-stock="{{ $product->stock }}"
                                            data-category="{{ $product->category->name }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} - ${{ number_format($product->price, 2) }} (Stock: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if($products->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600">
                                    No hay productos con stock disponible.
                                </p>
                            @endif
                        </div>

                        {{-- Información del producto seleccionado --}}
                        <div id="productInfo" class="hidden bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-semibold text-gray-800 mb-2">Información del producto</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Categoría:</span>
                                    <span id="productCategory" class="text-gray-600"></span>
                                </div>
                                <div>
                                    <span class="font-medium">Precio unitario:</span>
                                    <span id="productPrice" class="text-gray-600 font-semibold"></span>
                                </div>
                                <div>
                                    <span class="font-medium">Stock disponible:</span>
                                    <span id="productStock" class="text-gray-600"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Cantidad --}}
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                Cantidad
                            </label>
                            <input type="number" 
                                   name="quantity" 
                                   id="quantity" 
                                   value="{{ old('quantity', 1) }}" 
                                   min="1"
                                   required
                                   onchange="calculateTotal()"
                                   oninput="calculateTotal()"
                                   placeholder="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500" id="stockWarning"></p>
                        </div>

                        {{-- Resumen de la venta --}}
                        <div id="saleTotal" class="hidden bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">Resumen de la venta</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Precio unitario:</span>
                                    <span id="displayUnitPrice" class="font-semibold"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Cantidad:</span>
                                    <span id="displayQuantity" class="font-semibold"></span>
                                </div>
                                <hr class="my-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="displayTotal" class="text-green-600"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('sales.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    id="submitBtn"
                                    disabled
                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                Registrar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript para funcionalidad dinámica --}}
    <script>
        let selectedProduct = null;
        
        /**
         * Actualizar información del producto seleccionado
         */
        function updateProductInfo() {
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productInfo = document.getElementById('productInfo');
            const quantityInput = document.getElementById('quantity');
            
            if (selectedOption.value) {
                selectedProduct = {
                    price: parseFloat(selectedOption.dataset.price),
                    stock: parseInt(selectedOption.dataset.stock),
                    category: selectedOption.dataset.category
                };
                
                // Mostrar información del producto
                document.getElementById('productCategory').textContent = selectedProduct.category;
                document.getElementById('productPrice').textContent = '$' + selectedProduct.price.toFixed(2);
                document.getElementById('productStock').textContent = selectedProduct.stock + ' unidades';
                
                // Establecer máximo de cantidad
                quantityInput.max = selectedProduct.stock;
                quantityInput.value = Math.min(quantityInput.value || 1, selectedProduct.stock);
                
                productInfo.classList.remove('hidden');
                calculateTotal();
            } else {
                selectedProduct = null;
                productInfo.classList.add('hidden');
                document.getElementById('saleTotal').classList.add('hidden');
                document.getElementById('submitBtn').disabled = true;
            }
        }
        
        /**
         * Calcular total de la venta
         */
        function calculateTotal() {
            const quantityInput = document.getElementById('quantity');
            const quantity = parseInt(quantityInput.value) || 0;
            const stockWarning = document.getElementById('stockWarning');
            const saleTotal = document.getElementById('saleTotal');
            const submitBtn = document.getElementById('submitBtn');
            
            if (!selectedProduct || quantity <= 0) {
                saleTotal.classList.add('hidden');
                submitBtn.disabled = true;
                return;
            }
            
            // Verificar stock
            if (quantity > selectedProduct.stock) {
                stockWarning.textContent = `Solo hay ${selectedProduct.stock} unidades disponibles`;
                stockWarning.className = 'mt-1 text-sm text-red-600';
                quantityInput.value = selectedProduct.stock;
                return calculateTotal();
            } else if (quantity === selectedProduct.stock) {
                stockWarning.textContent = 'Se agotará el stock con esta venta';
                stockWarning.className = 'mt-1 text-sm text-yellow-600';
            } else {
                stockWarning.textContent = `Quedarán ${selectedProduct.stock - quantity} unidades`;
                stockWarning.className = 'mt-1 text-sm text-gray-500';
            }
            
            // Calcular total
            const total = quantity * selectedProduct.price;
            
            // Mostrar resumen
            document.getElementById('displayUnitPrice').textContent = '$' + selectedProduct.price.toFixed(2);
            document.getElementById('displayQuantity').textContent = quantity + ' unidades';
            document.getElementById('displayTotal').textContent = '$' + total.toFixed(2);
            
            saleTotal.classList.remove('hidden');
            submitBtn.disabled = false;
        }
        
        // Inicializar si hay un producto seleccionado (para casos de old input)
        document.addEventListener('DOMContentLoaded', function() {
            updateProductInfo();
        });
    </script>
</x-app-layout>