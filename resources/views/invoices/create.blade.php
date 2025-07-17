<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Factura') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
                        @csrf

                        <!-- Información del cliente -->
                        <div class="mb-6 p-4 border rounded-lg">
                            <h3 class="text-lg font-medium mb-4">Información del Cliente</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Cliente Registrado (Opcional)</label>
                                    <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="fillCustomerData()">
                                        <option value="">Seleccionar cliente registrado...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-email="{{ $customer->email }}">
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div></div>
                                
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Nombre del Cliente *</label>
                                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    @error('customer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-gray-700">Email del Cliente</label>
                                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @error('customer_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>                            <!-- Productos -->
                            <div class="mb-6 p-4 border rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Productos a Facturar</h3>
                                
                                <!-- Buscador de productos -->
                                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="font-medium mb-3">Agregar Producto</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Producto</label>
                                            <select id="product-selector" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">🔍 Buscar y seleccionar producto...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            data-name="{{ $product->name }}"
                                                            data-price="{{ $product->price }}" 
                                                            data-stock="{{ $product->stock }}"
                                                            data-category="{{ $product->category->name ?? 'Sin categoría' }}">
                                                        {{ $product->name }} | {{ $product->category->name ?? 'Sin categoría' }} | Stock: {{ $product->stock }} | ${{ number_format($product->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                            <input type="number" id="quantity-input" class="w-full rounded-md border-gray-300 shadow-sm" 
                                                   min="1" value="1" placeholder="1">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">IVA</label>
                                            <select id="tax-selector" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="0">0% (Exento)</option>
                                                <option value="15" selected>15% (Gravado)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <button type="button" onclick="addProductToInvoice()" 
                                                    class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                Agregar
                                            </button>
                                        </div>
                                    </div>
                                    <div id="product-info" class="mt-3 p-3 bg-blue-50 rounded-lg hidden">
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium">Precio Unitario:</span>
                                                <span id="product-price" class="text-blue-600 font-bold"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Stock Disponible:</span>
                                                <span id="product-stock" class="text-green-600 font-bold"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Total:</span>
                                                <span id="product-total" class="text-purple-600 font-bold"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de productos agregados -->
                                <div class="mb-4">
                                    <h4 class="font-medium mb-3">Productos en la Factura</h4>
                                    <div class="bg-white border rounded-lg overflow-hidden">
                                        <table class="min-w-full">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">IVA</th>
                                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">IVA Monto</th>
                                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoice-items" class="divide-y divide-gray-200">
                                                <tr id="no-items-row">
                                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                                        <div class="flex flex-col items-center">
                                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                            </svg>
                                                            <p>No hay productos agregados</p>
                                                            <p class="text-sm">Busca y agrega productos usando el selector de arriba</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                @error('items')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        <!-- Totales -->
                        <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                            <div class="flex justify-end">
                                <div class="w-64">
                                    <div class="flex justify-between py-2">
                                        <span class="font-medium">Subtotal (sin IVA):</span>
                                        <span id="invoice-subtotal">$0.00</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="font-medium">IVA Total:</span>
                                        <span id="invoice-tax">$0.00</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-t">
                                        <span class="font-bold text-lg">Total a Pagar:</span>
                                        <span id="invoice-total" class="font-bold text-lg text-green-600">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('invoices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Factura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let invoiceItems = [];
        let itemIndex = 0;

        // Evento para actualizar información del producto seleccionado
        document.getElementById('product-selector').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const productInfo = document.getElementById('product-info');
            const quantityInput = document.getElementById('quantity-input');
            
            if (option.value) {
                const price = parseFloat(option.dataset.price);
                const stock = parseInt(option.dataset.stock);
                const quantity = parseInt(quantityInput.value) || 1;
                
                document.getElementById('product-price').textContent = `$${price.toFixed(2)}`;
                document.getElementById('product-stock').textContent = stock;
                document.getElementById('product-total').textContent = `$${(price * quantity).toFixed(2)}`;
                
                quantityInput.max = stock;
                if (quantity > stock) {
                    quantityInput.value = stock;
                }
                
                productInfo.classList.remove('hidden');
            } else {
                productInfo.classList.add('hidden');
            }
        });

        // Evento para actualizar total cuando cambia la cantidad
        document.getElementById('quantity-input').addEventListener('input', function() {
            const productSelector = document.getElementById('product-selector');
            const option = productSelector.options[productSelector.selectedIndex];
            
            if (option.value) {
                const price = parseFloat(option.dataset.price);
                const stock = parseInt(option.dataset.stock);
                const quantity = parseInt(this.value) || 1;
                
                if (quantity > stock) {
                    this.value = stock;
                    showMessage(`Cantidad ajustada al stock disponible (${stock})`, 'warning');
                }
                
                const total = price * (parseInt(this.value) || 1);
                document.getElementById('product-total').textContent = `$${total.toFixed(2)}`;
            }
        });

        function fillCustomerData() {
            const select = document.getElementById('customer_id');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                document.getElementById('customer_name').value = option.dataset.name;
                document.getElementById('customer_email').value = option.dataset.email;
            }
        }

        function addProductToInvoice() {
            const productSelector = document.getElementById('product-selector');
            const quantityInput = document.getElementById('quantity-input');
            const taxSelector = document.getElementById('tax-selector');
            const option = productSelector.options[productSelector.selectedIndex];
            
            if (!option.value) {
                showMessage('Por favor selecciona un producto', 'error');
                return;
            }
            
            const quantity = parseInt(quantityInput.value) || 1;
            const taxRate = parseFloat(taxSelector.value);
            const stock = parseInt(option.dataset.stock);
            
            if (quantity <= 0) {
                showMessage('La cantidad debe ser mayor a 0', 'error');
                return;
            }
            
            if (quantity > stock) {
                showMessage(`Stock insuficiente. Disponible: ${stock}`, 'error');
                return;
            }
            
            // Verificar si el producto ya está en la lista con la misma tasa de IVA
            const existingItem = invoiceItems.find(item => item.productId === option.value && item.taxRate === taxRate);
            if (existingItem) {
                const newQuantity = existingItem.quantity + quantity;
                if (newQuantity > stock) {
                    showMessage(`No se puede agregar. Total sería ${newQuantity}, stock disponible: ${stock}`, 'error');
                    return;
                }
                existingItem.quantity = newQuantity;
                existingItem.subtotal = existingItem.price * newQuantity;
                existingItem.taxAmount = existingItem.subtotal * (existingItem.taxRate / 100);
                existingItem.total = existingItem.subtotal + existingItem.taxAmount;
            } else {
                // Calcular valores
                const price = parseFloat(option.dataset.price);
                const subtotal = price * quantity;
                const taxAmount = subtotal * (taxRate / 100);
                const total = subtotal + taxAmount;
                
                // Agregar nuevo item
                invoiceItems.push({
                    index: itemIndex++,
                    productId: option.value,
                    productName: option.dataset.name,
                    price: price,
                    quantity: quantity,
                    taxRate: taxRate,
                    subtotal: subtotal,
                    taxAmount: taxAmount,
                    total: total,
                    stock: stock
                });
            }
            
            // Resetear formulario
            productSelector.value = '';
            quantityInput.value = 1;
            taxSelector.value = 15; // Volver al 15% por defecto
            document.getElementById('product-info').classList.add('hidden');
            
            // Actualizar tabla y totales
            renderInvoiceItems();
            updateInvoiceTotals();
            
            showMessage('Producto agregado exitosamente', 'success');
        }

        function renderInvoiceItems() {
            const tbody = document.getElementById('invoice-items');
            const noItemsRow = document.getElementById('no-items-row');
            
            if (invoiceItems.length === 0) {
                noItemsRow.style.display = 'table-row';
                // Limpiar otros rows
                Array.from(tbody.children).forEach(row => {
                    if (row.id !== 'no-items-row') {
                        row.remove();
                    }
                });
                return;
            }
            
            noItemsRow.style.display = 'none';
            
            // Limpiar tabla excepto el row de "no items"
            Array.from(tbody.children).forEach(row => {
                if (row.id !== 'no-items-row') {
                    row.remove();
                }
            });
            
            invoiceItems.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${item.productName}</div>
                        <div class="text-sm text-gray-500">Stock disponible: ${item.stock}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" value="${item.quantity}" min="1" max="${item.stock}" 
                               class="w-20 text-center border-gray-300 rounded-md"
                               onchange="updateItemQuantity(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900">
                        $${item.price.toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-center font-medium">
                        ${item.taxRate}%
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-700">
                        $${item.subtotal.toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-green-600">
                        $${item.taxAmount.toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">
                        $${item.total.toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="removeInvoiceItem(${index})" 
                                class="text-red-600 hover:text-red-800 font-medium">
                            🗑️ Eliminar
                        </button>
                    </td>
                    <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
                    <input type="hidden" name="items[${index}][product_name]" value="${item.productName}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="items[${index}][tax_rate]" value="${item.taxRate}">
                `;
                tbody.appendChild(row);
            });
        }

        function updateItemQuantity(itemIndex, newQuantity) {
            const quantity = parseInt(newQuantity) || 1;
            const item = invoiceItems[itemIndex];
            
            if (quantity > item.stock) {
                showMessage(`Stock insuficiente. Disponible: ${item.stock}`, 'error');
                renderInvoiceItems(); // Re-renderizar para restaurar valor anterior
                return;
            }
            
            if (quantity <= 0) {
                showMessage('La cantidad debe ser mayor a 0', 'error');
                renderInvoiceItems();
                return;
            }
            
            item.quantity = quantity;
            item.subtotal = item.price * quantity;
            item.taxAmount = item.subtotal * (item.taxRate / 100);
            item.total = item.subtotal + item.taxAmount;
            
            renderInvoiceItems();
            updateInvoiceTotals();
        }

        function removeInvoiceItem(itemIndex) {
            if (confirm('¿Estás seguro de eliminar este producto?')) {
                invoiceItems.splice(itemIndex, 1);
                renderInvoiceItems();
                updateInvoiceTotals();
                showMessage('Producto eliminado', 'info');
            }
        }

        function updateInvoiceTotals() {
            const subtotal = invoiceItems.reduce((sum, item) => sum + item.subtotal, 0);
            const totalTax = invoiceItems.reduce((sum, item) => sum + item.taxAmount, 0);
            const total = subtotal + totalTax;
            
            document.getElementById('invoice-subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('invoice-tax').textContent = `$${totalTax.toFixed(2)}`;
            document.getElementById('invoice-total').textContent = `$${total.toFixed(2)}`;
        }

        function showMessage(message, type = 'info') {
            // Crear elemento de mensaje
            const messageDiv = document.createElement('div');
            const colors = {
                success: 'bg-green-100 border-green-400 text-green-700',
                error: 'bg-red-100 border-red-400 text-red-700',
                warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
                info: 'bg-blue-100 border-blue-400 text-blue-700'
            };
            
            messageDiv.className = `fixed top-4 right-4 p-4 border rounded-lg shadow-lg z-50 ${colors[type]}`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-lg">&times;</button>
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            // Auto eliminar después de 3 segundos
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.remove();
                }
            }, 3000);
        }

        // Validar antes de enviar el formulario
        document.getElementById('invoice-form').addEventListener('submit', function(e) {
            if (invoiceItems.length === 0) {
                e.preventDefault();
                showMessage('Debe agregar al menos un producto a la factura', 'error');
                return;
            }
            
            // Debug: ver qué datos se están enviando
            console.log('Datos de invoiceItems:', invoiceItems);
            const formData = new FormData(this);
            console.log('Datos del formulario:');
            for (let pair of formData.entries()) {
                if (pair[0].includes('items')) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
            }
            
            // Validación final de stock
            let valid = true;
            invoiceItems.forEach(item => {
                if (item.quantity > item.stock) {
                    valid = false;
                    showMessage(`Stock insuficiente para ${item.productName}. Disponible: ${item.stock}`, 'error');
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            renderInvoiceItems();
            updateInvoiceTotals();
        });
    </script>
</x-app-layout>
