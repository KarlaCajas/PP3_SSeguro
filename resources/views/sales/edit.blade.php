<!-- filepath: c:\Users\condo\Documents\Universidad\7mo\SW seguro\sws-projects\ventas-pry-p1\resources\views\sales\edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Encabezado --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Editar Venta #{{ $sale->id }}</h3>
                        <a href="{{ route('sales.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al historial
                        </a>
                    </div>

                    {{-- Información de la venta actual --}}
                    <div class="bg-purple-50 p-4 rounded-lg mb-6 border border-purple-200">
                        <h4 class="font-semibold text-purple-800 mb-2">Información actual de la venta</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium">ID:</span>
                                <span class="text-purple-700">#{{ $sale->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Producto:</span>
                                <span class="text-purple-700">{{ $sale->product->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Cantidad:</span>
                                <span class="text-purple-700">{{ $sale->quantity }} unidades</span>
                            </div>
                            <div>
                                <span class="font-medium">Total:</span>
                                <span class="text-purple-700 font-semibold">${{ number_format($sale->total_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-purple-600">
                            Registrada: {{ $sale->created_at->format('d/m/Y H:i') }} ({{ $sale->created_at->diffForHumans() }})
                        </div>
                    </div>

                    {{-- Alerta de restricción temporal --}}
                    <div class="bg-yellow-50 p-4 rounded-lg mb-6 border border-yellow-200">
                        <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Edición limitada</h4>
                        <p class="text-yellow-700 text-sm">
                            Solo puedes editar ventas del día actual. Al modificar esta venta:
                        </p>
                        <ul class="list-disc list-inside text-yellow-700 text-sm mt-2">
                            <li>Se restaurará el stock del producto original</li>
                            <li>Se verificará stock disponible del nuevo producto</li>
                            <li>Se actualizará el precio según el precio actual del producto</li>
                        </ul>
                    </div>

                    {{-- Formulario de edición --}}
                    <form method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-6" id="editSaleForm">
                        @csrf
                        @method('PUT')

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
                                    @php
                                        // Calcular stock disponible (stock actual + cantidad de la venta si es el mismo producto)
                                        $availableStock = $product->stock;
                                        if ($product->id == $sale->product_id) {
                                            $availableStock += $sale->quantity;
                                        }
                                    @endphp
                                    
                                    @if($availableStock > 0)
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->price }}"
                                                data-stock="{{ $availableStock }}"
                                                data-category="{{ $product->category->name }}"
                                                {{ old('product_id', $sale->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - ${{ number_format($product->price, 2) }} 
                                            (Disponible: {{ $availableStock }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Información del producto seleccionado --}}
                        <div id="productInfo" class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-semibold text-gray-800 mb-2">Información del producto</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Categoría:</span>
                                    <span id="productCategory" class="text-gray-600">{{ $sale->product->category->name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Precio unitario:</span>
                                    <span id="productPrice" class="text-gray-600 font-semibold">${{ number_format($sale->product->price, 2) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Stock disponible:</span>
                                    <span id="productStock" class="text-gray-600">{{ $sale->product->stock + $sale->quantity }}</span>
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
                                   value="{{ old('quantity', $sale->quantity) }}" 
                                   min="1"
                                   required
                                   onchange="calculateTotal()"
                                   oninput="calculateTotal()"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500" id="stockWarning"></p>
                        </div>

                        {{-- Comparación de precios --}}
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-2">Comparación de precios</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Precio original de venta:</span>
                                    <span class="text-blue-700">${{ number_format($sale->unit_price, 2) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Precio actual del producto:</span>
                                    <span id="currentPrice" class="text-blue-700">${{ number_format($sale->product->price, 2) }}</span>
                                </div>
                            </div>
                            @if($sale->unit_price != $sale->product->price)
                                <div class="mt-2 text-xs text-blue-600">
                                    <strong>Nota:</strong> El precio ha cambiado desde la venta original. Se usará el precio actual.
                                </div>
                            @endif
                        </div>

                        {{-- Resumen de la venta actualizada --}}
                        <div id="saleTotal" class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">Resumen de la venta actualizada</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Precio unitario:</span>
                                    <span id="displayUnitPrice" class="font-semibold">${{ number_format($sale->product->price, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Cantidad:</span>
                                    <span id="displayQuantity" class="font-semibold">{{ $sale->quantity }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="displayTotal" class="text-green-600">${{ number_format($sale->product->price * $sale->quantity, 2) }}</span>
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
                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Venta
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
        let originalSale = {
            productId: {{ $sale->product_id }},
            quantity: {{ $sale->quantity }},
            unitPrice: {{ $sale->unit_price }},
            totalPrice: {{ $sale->total_price }}
        };
        
        /**
         * Actualizar información del producto seleccionado
         */
        function updateProductInfo() {
            const productSelect = document.getElementById('product_id');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const quantityInput = document.getElementById('quantity');
            
            if (selectedOption.value) {
                selectedProduct = {
                    id: parseInt(selectedOption.value),
                    price: parseFloat(selectedOption.dataset.price),
                    stock: parseInt(selectedOption.dataset.stock),
                    category: selectedOption.dataset.category
                };
                
                // Mostrar información del producto
                document.getElementById('productCategory').textContent = selectedProduct.category;
                document.getElementById('productPrice').textContent = '$' + selectedProduct.price.toFixed(2);
                document.getElementById('productStock').textContent = selectedProduct.stock + ' unidades';
                document.getElementById('currentPrice').textContent = '$' + selectedProduct.price.toFixed(2);
                
                // Establecer máximo de cantidad
                quantityInput.max = selectedProduct.stock;
                
                // Si cambió de producto, ajustar cantidad si es necesario
                if (quantityInput.value > selectedProduct.stock) {
                    quantityInput.value = selectedProduct.stock;
                }
                
                calculateTotal();
            } else {
                selectedProduct = null;
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
            const submitBtn = document.getElementById('submitBtn');
            
            if (!selectedProduct || quantity <= 0) {
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
            
            submitBtn.disabled = false;
        }
        
        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateProductInfo();
            calculateTotal();
        });
    </script>
</x-app-layout>